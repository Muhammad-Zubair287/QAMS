<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

/**
 * Admin DashboardController — QAMS
 * ─────────────────────────────────────────────────────────────────────────
 * OOP Concept: Inheritance
 *   → Extends the base Controller class.
 *
 * OOP Concept: Encapsulation
 *   → All admin user-management operations are grouped here.
 *
 * Handles:
 *   → Show admin dashboard (list all users, search by name)
 *   → Show edit user form
 *   → Update user information
 *   → Block / Unblock user accounts
 */
class DashboardController extends Controller
{
    /**
     * Show Admin Dashboard.
     * Route: GET /admin/dashboard
     *
     * Lists all users. If ?search= is provided, filters by name.
     */
    public function index(Request $request): View
    {
        $search       = $request->input('search');
        $roleFilter   = $request->input('role');   // teacher | student | null
        $statusFilter = $request->input('status'); // blocked | null

        // Fetch ALL users for stats cards (unfiltered)
        $allUsers = User::select(['id', 'name', 'user_name', 'role', 'active', 'created_at'])->get();

        // Build filtered query
        $query = User::select(['id', 'name', 'user_name', 'role', 'active', 'created_at']);

        if (!empty($search)) {
            $query->where('name', 'LIKE', '%' . $search . '%');
        }
        if (!empty($roleFilter)) {
            $query->where('role', $roleFilter);
        }
        if ($statusFilter === 'blocked') {
            $query->where('active', 'no');
        }

        $users = $query->orderBy('created_at', 'desc')->get();

        return view('admin.dashboard', compact('users', 'allUsers', 'search', 'roleFilter', 'statusFilter'));
    }

    /**
     * Show Create User Form.
     * Route: GET /admin/users/create
     */
    public function createUser(): View
    {
        return view('admin.create-user');
    }

    /**
     * Store a new user created by admin.
     * Route: POST /admin/users
     */
    public function storeUser(Request $request): RedirectResponse
    {
        $request->validate([
            'name'      => 'required|string|max:30',
            'user_name' => 'required|string|max:30|unique:users,user_name',
            'role'      => 'required|in:admin,teacher,student',
            'password'  => 'required|string|min:6|confirmed',
        ], [
            'name.required'      => 'Full name is required.',
            'user_name.required' => 'Username is required.',
            'user_name.unique'   => 'This username is already taken.',
            'role.required'      => 'Please select a role.',
            'password.required'  => 'Password is required.',
            'password.min'       => 'Password must be at least 6 characters.',
            'password.confirmed' => 'Passwords do not match.',
        ]);

        try {
            $user = User::create([
                'name'      => $request->input('name'),
                'user_name' => $request->input('user_name'),
                'password'  => Hash::make($request->input('password')),
                'role'      => $request->input('role'),
                'active'    => 'yes',
            ]);

            Log::info('Admin created new user', [
                'admin_id'    => Auth::id(),
                'new_user_id' => $user->id,
                'role'        => $user->role,
            ]);
        } catch (\Exception $e) {
            Log::error('Admin user creation failed', ['error' => $e->getMessage()]);
            return back()->withInput()->withErrors(['error' => 'Failed to create user. Please try again.']);
        }

        return redirect()->route('admin.dashboard')
            ->with('success', 'User "' . $user->name . '" created successfully.');
    }

    /**
     * Delete a user permanently.
     * Route: DELETE /admin/users/{id}
     */
    public function destroyUser(int $id): RedirectResponse
    {
        if ((int) Auth::id() === $id) {
            return back()->withErrors(['error' => 'You cannot delete your own account.']);
        }

        $user = User::find($id);

        if (!$user) {
            return redirect()->route('admin.dashboard')
                ->withErrors(['error' => 'User not found.']);
        }

        $name = $user->name;
        $user->delete();

        Log::info('Admin deleted user', [
            'admin_id'        => Auth::id(),
            'deleted_user_id' => $id,
            'deleted_name'    => $name,
        ]);

        return redirect()->route('admin.dashboard')
            ->with('success', 'User "' . $name . '" has been permanently deleted.');
    }

    /**
     * Show Edit User Form.
     * Route: GET /admin/users/{id}/edit
     */
    public function editUser(int $id): View|RedirectResponse
    {
        // Find the user by ID, or return null if not found
        $user = User::select(['id', 'name', 'user_name', 'role', 'active'])->find($id);

        // If user doesn't exist, go back to dashboard with error
        if (!$user) {
            return redirect()->route('admin.dashboard')
                ->withErrors(['error' => 'User not found.']);
        }

        return view('admin.edit-user', compact('user'));
    }

    /**
     * Update User Information.
     * Route: PUT /admin/users/{id}
     */
    public function updateUser(Request $request, int $id): RedirectResponse
    {
        // ── Validate input ────────────────────────────────────────────────
        $request->validate([
            'name'      => 'required|string|max:30',
            // unique but ignore this user's own record (to allow keeping same username)
            'user_name' => 'required|string|max:30|unique:users,user_name,' . $id,
            'role'      => 'required|in:admin,teacher,student',
            // Password is optional on update — only update if provided
            'password'  => 'nullable|string|min:6|confirmed',
        ], [
            'name.required'      => 'Full name is required.',
            'user_name.required' => 'Username is required.',
            'user_name.unique'   => 'This username is already taken.',
            'role.required'      => 'Please select a role.',
            'password.min'       => 'Password must be at least 6 characters.',
            'password.confirmed' => 'Passwords do not match.',
        ]);

        // Find the user
        $user = User::find($id);

        if (!$user) {
            return redirect()->route('admin.dashboard')
                ->withErrors(['error' => 'User not found.']);
        }

        try {
            // Build the data array to update
            $data = [
                'name'      => $request->input('name'),
                'user_name' => $request->input('user_name'),
                'role'      => $request->input('role'),
            ];

            // Only update password if admin entered a new one
            if ($request->filled('password')) {
                $data['password'] = Hash::make($request->input('password'));
            }

            $user->update($data);

            Log::info('Admin updated user', [
                'admin_id'        => Auth::id(),
                'updated_user_id' => $id,
            ]);

        } catch (\Exception $e) {
            Log::error('Admin user update failed', ['error' => $e->getMessage(), 'user_id' => $id]);
            return back()->withErrors(['error' => 'Update failed. Please try again.']);
        }

        return redirect()->route('admin.dashboard')
            ->with('success', 'User "' . $user->name . '" updated successfully.');
    }

    /**
     * Toggle Block / Unblock a User.
     * Route: PATCH /admin/users/{id}/toggle-block
     *
     * If user is active → block them (active = 'no')
     * If user is blocked → unblock them (active = 'yes')
     */
    public function toggleBlock(int $id): RedirectResponse
    {
        // Prevent admin from blocking their own account
        // Cast both to int — Auth::id() can return int|string depending on driver
        if ((int) Auth::id() === $id) {
            return back()->withErrors(['error' => 'You cannot block your own account.']);
        }

        $user = User::find($id);

        if (!$user) {
            return back()->withErrors(['error' => 'User not found.']);
        }

        // Flip the status: yes → no, no → yes
        $newStatus = $user->active === 'yes' ? 'no' : 'yes';
        $user->update(['active' => $newStatus]);

        $action = $newStatus === 'no' ? 'blocked' : 'unblocked';

        Log::info("Admin {$action} user", [
            'admin_id'       => Auth::id(),
            'target_user_id' => $id,
        ]);

        return back()->with('success', 'User "' . $user->name . '" has been ' . $action . '.');
    }
}
