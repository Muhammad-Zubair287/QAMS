<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SchoolClass;
use App\Models\StudentDetail;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

/**
 * StudentController — QAMS Admin
 * ─────────────────────────────────────────────────────────────────────────
 * OOP: Encapsulation — all student registration and management in one place.
 * OOP: Composition  — StudentDetail is composed with User on creation.
 *
 * Handles:
 *   → List all students
 *   → Register a new student (creates User + StudentDetail + enrolls subjects)
 *   → Edit student info
 *   → Delete student
 *   → Block / Unblock (via toggleBlock in DashboardController)
 */
class StudentController extends Controller
{
    /** GET /admin/students */
    public function index(Request $request): View
    {
        $search = $request->input('search');

        $query = User::where('role', 'student')
            ->with(['studentDetail.schoolClass', 'enrolledSubjects'])
            ->select(['id', 'name', 'user_name', 'active', 'created_at', 'role']);

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', '%' . $search . '%')
                  ->orWhereHas('studentDetail', function ($q2) use ($search) {
                      $q2->where('admission_number', 'LIKE', '%' . $search . '%');
                  });
            });
        }

        $students = $query->orderBy('created_at', 'desc')->get();

        return view('admin.students.index', compact('students', 'search'));
    }

    /** GET /admin/students/create */
    public function create(): View
    {
        $classes  = SchoolClass::with('subjects')->orderBy('name')->get();
        $subjects = Subject::with('schoolClass')->orderBy('name')->get();
        return view('admin.students.create', compact('classes', 'subjects'));
    }

    /** POST /admin/students */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name'             => 'required|string|max:30',
            'user_name'        => 'required|string|max:30|unique:users,user_name',
            'password'         => 'required|string|min:6|confirmed',
            'admission_number' => 'required|string|max:50|unique:student_details,admission_number',
            'father_name'      => 'required|string|max:60',
            'class_id'         => 'required|exists:classes,id',
            'subjects'         => 'nullable|array',
            'subjects.*'       => 'exists:subjects,id',
            'picture'          => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ], [
            'admission_number.required' => 'Admission number is required.',
            'admission_number.unique'   => 'This admission number is already registered.',
            'father_name.required'      => 'Father\'s name is required.',
            'class_id.required'         => 'Please select a class.',
            'picture.image'             => 'Profile picture must be an image.',
            'picture.max'               => 'Picture size must not exceed 2MB.',
        ]);

        try {
            // 1. Handle picture upload
            $picturePath = null;
            if ($request->hasFile('picture')) {
                $picturePath = $request->file('picture')->store('pictures', 'public');
            }

            // 2. Create the user account (Composition)
            $user = User::create([
                'name'      => $request->input('name'),
                'user_name' => $request->input('user_name'),
                'password'  => Hash::make($request->input('password')),
                'role'      => 'student',
                'active'    => 'yes',
            ]);

            // 3. Create the student detail record (Association)
            StudentDetail::create([
                'user_id'          => $user->id,
                'admission_number' => $request->input('admission_number'),
                'father_name'      => $request->input('father_name'),
                'picture'          => $picturePath,
                'class_id'         => $request->input('class_id'),
            ]);

            // 4. Enroll in subjects (Aggregation — many-to-many)
            if ($request->filled('subjects')) {
                $user->enrolledSubjects()->attach($request->input('subjects'));
            }

            Log::info('Admin registered new student', ['user_id' => $user->id]);

        } catch (\Exception $e) {
            Log::error('Student registration failed', ['error' => $e->getMessage()]);
            return back()->withInput()->withErrors(['error' => 'Registration failed: ' . $e->getMessage()]);
        }

        return redirect()->route('admin.students.index')
            ->with('success', 'Student "' . $user->name . '" registered successfully.');
    }

    /** GET /admin/students/{id}/edit */
    public function edit(int $id): View|RedirectResponse
    {
        $student = User::with([
            'studentDetail.schoolClass',
            'enrolledSubjects',
        ])->where('role', 'student')->find($id);

        if (!$student) {
            return redirect()->route('admin.students.index')
                ->withErrors(['error' => 'Student not found.']);
        }

        $classes  = SchoolClass::with('subjects')->orderBy('name')->get();
        $subjects = Subject::with('schoolClass')->orderBy('name')->get();
        $enrolledIds = $student->enrolledSubjects->pluck('id')->toArray();

        return view('admin.students.edit', compact('student', 'classes', 'subjects', 'enrolledIds'));
    }

    /** PUT /admin/students/{id} */
    public function update(Request $request, int $id): RedirectResponse
    {
        $student = User::where('role', 'student')->find($id);
        if (!$student) {
            return redirect()->route('admin.students.index')
                ->withErrors(['error' => 'Student not found.']);
        }

        $request->validate([
            'name'             => 'required|string|max:30',
            'user_name'        => 'required|string|max:30|unique:users,user_name,' . $id,
            'password'         => 'nullable|string|min:6|confirmed',
            'admission_number' => 'required|string|max:50|unique:student_details,admission_number,' . optional($student->studentDetail)->id,
            'father_name'      => 'required|string|max:60',
            'class_id'         => 'required|exists:classes,id',
            'subjects'         => 'nullable|array',
            'subjects.*'       => 'exists:subjects,id',
            'picture'          => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
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
            $student->update($userData);

            // Handle new picture
            $picturePath = optional($student->studentDetail)->picture;
            if ($request->hasFile('picture')) {
                // Delete old picture if exists
                if ($picturePath) {
                    Storage::disk('public')->delete($picturePath);
                }
                $picturePath = $request->file('picture')->store('pictures', 'public');
            }

            // Update or create student detail
            StudentDetail::updateOrCreate(
                ['user_id' => $student->id],
                [
                    'admission_number' => $request->input('admission_number'),
                    'father_name'      => $request->input('father_name'),
                    'picture'          => $picturePath,
                    'class_id'         => $request->input('class_id'),
                ]
            );

            // Sync subjects (re-enroll)
            $student->enrolledSubjects()->sync($request->input('subjects', []));

            Log::info('Admin updated student', ['user_id' => $id]);

        } catch (\Exception $e) {
            Log::error('Student update failed', ['error' => $e->getMessage(), 'user_id' => $id]);
            return back()->withInput()->withErrors(['error' => 'Update failed: ' . $e->getMessage()]);
        }

        return redirect()->route('admin.students.index')
            ->with('success', 'Student "' . $student->name . '" updated successfully.');
    }

    /** DELETE /admin/students/{id} */
    public function destroy(int $id): RedirectResponse
    {
        $student = User::where('role', 'student')->find($id);
        if (!$student) {
            return redirect()->route('admin.students.index')
                ->withErrors(['error' => 'Student not found.']);
        }

        // Delete picture file if exists
        $detail = $student->studentDetail;
        if ($detail && $detail->picture) {
            Storage::disk('public')->delete($detail->picture);
        }

        $name = $student->name;
        $student->delete(); // Cascades to student_details + pivot tables

        Log::info('Admin deleted student', ['user_id' => $id, 'name' => $name]);

        return redirect()->route('admin.students.index')
            ->with('success', 'Student "' . $name . '" deleted successfully.');
    }
}
