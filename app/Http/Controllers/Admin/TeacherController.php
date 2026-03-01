<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use App\Models\TeacherDetail;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

/**
 * TeacherController — QAMS Admin
 * ─────────────────────────────────────────────────────────────────────────
 * OOP: Encapsulation — all teacher registration and management in one place.
 * OOP: Aggregation  — assigns subjects to teachers (many-to-many).
 *
 * Handles:
 *   → List all teachers
 *   → Register a new teacher (creates User + TeacherDetail + assigns subjects)
 *   → Edit teacher info
 *   → Assign/update subjects to teacher
 *   → Delete teacher
 */
class TeacherController extends Controller
{
    /** GET /admin/teachers */
    public function index(Request $request): View
    {
        $search = $request->input('search');

        $query = User::where('role', 'teacher')
            ->with(['teacherDetail', 'teachingSubjects.schoolClass'])
            ->select(['id', 'name', 'user_name', 'active', 'created_at', 'role']);

        if (!empty($search)) {
            $query->where('name', 'LIKE', '%' . $search . '%');
        }

        $teachers = $query->orderBy('created_at', 'desc')->get();

        return view('admin.teachers.index', compact('teachers', 'search'));
    }

    /** GET /admin/teachers/create */
    public function create(): View
    {
        $subjects = Subject::with('schoolClass')->orderBy('name')->get();
        return view('admin.teachers.create', compact('subjects'));
    }

    /** POST /admin/teachers */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name'        => 'required|string|max:30',
            'user_name'   => 'required|string|max:30|unique:users,user_name',
            'password'    => 'required|string|min:6|confirmed',
            'job_history' => 'nullable|string|max:2000',
            'education'   => 'nullable|string|max:2000',
            'subjects'    => 'nullable|array',
            'subjects.*'  => 'exists:subjects,id',
            'picture'     => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        try {
            // 1. Handle picture upload
            $picturePath = null;
            if ($request->hasFile('picture')) {
                $picturePath = $request->file('picture')->store('pictures', 'public');
            }

            // 2. Create user account
            $user = User::create([
                'name'      => $request->input('name'),
                'user_name' => $request->input('user_name'),
                'password'  => Hash::make($request->input('password')),
                'role'      => 'teacher',
                'active'    => 'yes',
            ]);

            // 3. Create teacher detail (Association)
            TeacherDetail::create([
                'user_id'     => $user->id,
                'job_history' => $request->input('job_history'),
                'education'   => $request->input('education'),
                'picture'     => $picturePath,
            ]);

            // 4. Assign subjects (Aggregation — many-to-many)
            if ($request->filled('subjects')) {
                $user->teachingSubjects()->attach($request->input('subjects'));
            }

            Log::info('Admin registered new teacher', ['user_id' => $user->id]);

        } catch (\Exception $e) {
            Log::error('Teacher registration failed', ['error' => $e->getMessage()]);
            return back()->withInput()->withErrors(['error' => 'Registration failed: ' . $e->getMessage()]);
        }

        return redirect()->route('admin.teachers.index')
            ->with('success', 'Teacher "' . $user->name . '" registered successfully.');
    }

    /** GET /admin/teachers/{id}/edit */
    public function edit(int $id): View|RedirectResponse
    {
        $teacher = User::with([
            'teacherDetail',
            'teachingSubjects',
        ])->where('role', 'teacher')->find($id);

        if (!$teacher) {
            return redirect()->route('admin.teachers.index')
                ->withErrors(['error' => 'Teacher not found.']);
        }

        $subjects       = Subject::with('schoolClass')->orderBy('name')->get();
        $assignedIds    = $teacher->teachingSubjects->pluck('id')->toArray();

        return view('admin.teachers.edit', compact('teacher', 'subjects', 'assignedIds'));
    }

    /** PUT /admin/teachers/{id} */
    public function update(Request $request, int $id): RedirectResponse
    {
        $teacher = User::where('role', 'teacher')->find($id);
        if (!$teacher) {
            return redirect()->route('admin.teachers.index')
                ->withErrors(['error' => 'Teacher not found.']);
        }

        $request->validate([
            'name'        => 'required|string|max:30',
            'user_name'   => 'required|string|max:30|unique:users,user_name,' . $id,
            'password'    => 'nullable|string|min:6|confirmed',
            'job_history' => 'nullable|string|max:2000',
            'education'   => 'nullable|string|max:2000',
            'subjects'    => 'nullable|array',
            'subjects.*'  => 'exists:subjects,id',
            'picture'     => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        try {
            // Update user account
            $userData = [
                'name'      => $request->input('name'),
                'user_name' => $request->input('user_name'),
            ];
            if ($request->filled('password')) {
                $userData['password'] = Hash::make($request->input('password'));
            }
            $teacher->update($userData);

            // Handle picture update
            $picturePath = optional($teacher->teacherDetail)->picture;
            if ($request->hasFile('picture')) {
                if ($picturePath) {
                    Storage::disk('public')->delete($picturePath);
                }
                $picturePath = $request->file('picture')->store('pictures', 'public');
            }

            // Update or create teacher detail
            TeacherDetail::updateOrCreate(
                ['user_id' => $teacher->id],
                [
                    'job_history' => $request->input('job_history'),
                    'education'   => $request->input('education'),
                    'picture'     => $picturePath,
                ]
            );

            // Re-sync assigned subjects
            $teacher->teachingSubjects()->sync($request->input('subjects', []));

            Log::info('Admin updated teacher', ['user_id' => $id]);

        } catch (\Exception $e) {
            Log::error('Teacher update failed', ['error' => $e->getMessage(), 'user_id' => $id]);
            return back()->withInput()->withErrors(['error' => 'Update failed: ' . $e->getMessage()]);
        }

        return redirect()->route('admin.teachers.index')
            ->with('success', 'Teacher "' . $teacher->name . '" updated successfully.');
    }

    /** DELETE /admin/teachers/{id} */
    public function destroy(int $id): RedirectResponse
    {
        $teacher = User::where('role', 'teacher')->find($id);
        if (!$teacher) {
            return redirect()->route('admin.teachers.index')
                ->withErrors(['error' => 'Teacher not found.']);
        }

        $detail = $teacher->teacherDetail;
        if ($detail && $detail->picture) {
            Storage::disk('public')->delete($detail->picture);
        }

        $name = $teacher->name;
        $teacher->delete();

        Log::info('Admin deleted teacher', ['user_id' => $id, 'name' => $name]);

        return redirect()->route('admin.teachers.index')
            ->with('success', 'Teacher "' . $name . '" deleted successfully.');
    }
}
