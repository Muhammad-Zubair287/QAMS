<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

/**
 * AuthController — QAMS
 * ─────────────────────────────────────────────────────────────────────────
 * OOP Concept: Encapsulation
 *   → All authentication logic (login, register, logout) is encapsulated
 *     inside this single class.
 *
 * Handles:
 *   → Show login page
 *   → Process login form
 *   → Show registration page
 *   → Process registration form
 *   → Logout
 */
class AuthController extends Controller
{
    // ── REGISTRATION ──────────────────────────────────────────────────────

    /**
     * Show the Registration Page.
     * Route: GET /register
     *
     * If already logged in → redirect to dashboard.
     */
    public function showRegister(): View|RedirectResponse
    {
        // If user is already logged in, send them to their dashboard
        if (Auth::check()) {
            /** @var \App\Models\User $currentUser */
            $currentUser = Auth::user();
            return redirect()->route($currentUser->getDashboardRoute());
        }

        // Show the registration form view
        return view('auth.register');
    }

    /**
     * Process the Registration Form.
     * Route: POST /register
     *
     * Steps:
     *  1. Validate input
     *  2. Check username is not taken
     *  3. Hash the password
     *  4. Save new user to DB
     *  5. Redirect to login with success message
     */
    public function register(Request $request): RedirectResponse
    {
        // ── Step 1: Validate all input fields ────────────────────────────
        $request->validate([
            'name'      => 'required|string|max:30',
            // unique:users,user_name → check users table, user_name column
            'user_name' => 'required|string|max:30|unique:users,user_name',
            'password'  => 'required|string|min:6|confirmed', // confirmed = needs password_confirmation field
            'role'      => 'required|in:admin,teacher,student',
        ], [
            // Custom error messages (shown in the form)
            'name.required'      => 'Full name is required.',
            'user_name.required' => 'Username is required.',
            'user_name.unique'   => 'This username is already taken. Please choose another.',
            'password.required'  => 'Password is required.',
            'password.min'       => 'Password must be at least 6 characters.',
            'password.confirmed' => 'Passwords do not match.',
            'role.required'      => 'Please select a role.',
            'role.in'            => 'Invalid role selected.',
        ]);

        // ── Step 2 & 3 & 4: Hash password and create user ────────────────
        try {
            // Hash::make() converts plain text to a secure bcrypt hash
            // Example: "mypass123" → "$2y$12$..."
            $user = User::create([
                'name'      => $request->input('name'),
                'user_name' => $request->input('user_name'),
                'password'  => Hash::make($request->input('password')), // Always hash passwords!
                'role'      => $request->input('role'),
                'active'    => 'yes', // New accounts are active by default
            ]);

            // Log successful registration (never log passwords)
            Log::info('New user registered', [
                'user_id'   => $user->id,
                'user_name' => $user->user_name,
                'role'      => $user->role,
            ]);

        } catch (\Exception $e) {
            // If something goes wrong (e.g., DB connection lost)
            Log::error('Registration failed', ['error' => $e->getMessage()]);

            return back()
                ->withInput($request->except('password', 'password_confirmation'))
                ->withErrors(['register_error' => 'Registration failed. Please try again.']);
        }

        // ── Step 5: Redirect to login with success message ────────────────
        return redirect()
            ->route('login')
            ->with('success', 'Account created successfully! You can now log in.');
    }

    // ── LOGIN ─────────────────────────────────────────────────────────────

    /**
     * Show the Login Page.
     * Route: GET /login
     */
    public function showLogin(): View|RedirectResponse
    {
        // If already logged in, go to dashboard
        if (Auth::check()) {
            /** @var \App\Models\User $currentUser */
            $currentUser = Auth::user();
            return redirect()->route($currentUser->getDashboardRoute());
        }

        return view('auth.login');
    }

    /**
     * Process the Login Form.
     * Route: POST /login
     *
     * Steps:
     *  1. Validate input
     *  2. Find user by username
     *  3. Check if account is blocked
     *  4. Verify password with Hash::check()
     *  5. Log the user in and redirect to their dashboard
     */
    public function login(Request $request): RedirectResponse
    {
        // ── Step 1: Validate ──────────────────────────────────────────────
        $request->validate([
            'user_name' => 'required|string',
            'password'  => 'required|string',
        ], [
            'user_name.required' => 'Username is required.',
            'password.required'  => 'Password is required.',
        ]);

        // ── Step 2: Find the user ─────────────────────────────────────────
        $user = User::where('user_name', $request->input('user_name'))
            ->select(['id', 'name', 'user_name', 'password', 'role', 'active'])
            ->first();

        // User not found
        if (!$user) {
            Log::info('Login failed - username not found', ['user_name' => $request->input('user_name')]);
            return back()
                ->withInput($request->only('user_name'))
                ->withErrors(['login_error' => 'Invalid username or password.']);
        }

        // ── Step 3: Check if blocked ──────────────────────────────────────
        if (!$user->isActive()) {
            Log::info('Login blocked - account is blocked', ['user_id' => $user->id]);
            return back()
                ->withInput($request->only('user_name'))
                ->withErrors(['login_error' => 'Your account has been blocked. Please contact the administrator.']);
        }

        // ── Step 4: Verify password ───────────────────────────────────────
        // Hash::check() compares plain text with the stored hash
        if (!Hash::check($request->input('password'), $user->password)) {
            Log::info('Login failed - wrong password', ['user_id' => $user->id]);
            return back()
                ->withInput($request->only('user_name'))
                ->withErrors(['login_error' => 'Invalid username or password.']);
        }

        // ── Step 5: Log the user in ───────────────────────────────────────
        Auth::login($user); // Store user info in session
        $request->session()->regenerate(); // Prevent session fixation attacks

        Log::info('User logged in successfully', ['user_id' => $user->id, 'role' => $user->role]);

        // Redirect to the correct dashboard based on role (Polymorphism)
        // $user is typed as App\Models\User above, so getDashboardRoute() is available
        return redirect()
            ->route($user->getDashboardRoute())
            ->with('success', 'Welcome back, ' . $user->name . '!');
    }

    // ── LOGOUT ────────────────────────────────────────────────────────────

    /**
     * Log the user out.
     * Route: POST /logout
     */
    public function logout(Request $request): RedirectResponse
    {
        $userId = Auth::id(); // Capture before logout

        Auth::logout(); // Clear the authentication

        // Invalidate the session and regenerate CSRF token
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        Log::info('User logged out', ['user_id' => $userId]);

        return redirect()->route('login')->with('success', 'You have been logged out successfully.');
    }
}
