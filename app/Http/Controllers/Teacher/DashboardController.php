<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Show teacher dashboard with assigned-subject insights.
     */
    public function index(): View|RedirectResponse
    {
        try {
            /** @var \App\Models\User|null $currentUser */
            $currentUser = Auth::user();

            if ($currentUser === null) {
                return redirect()->route('login');
            }

            $teacher = User::where('id', $currentUser->id)
                ->where('role', 'teacher')
                ->select(['id', 'name', 'user_name', 'role', 'active'])
                ->with([
                    'teacherDetail:user_id,job_history,education,picture',
                    'teachingSubjects' => function ($query): void {
                        $query->select(['subjects.id', 'subjects.name', 'subjects.class_id'])
                            ->with('schoolClass:id,name,section')
                            ->withCount('students');
                    },
                ])
                ->first();

            if ($teacher === null) {
                return redirect()->route('login')->withErrors(['error' => 'Teacher account not found.']);
            }

            $subjects = $teacher->teachingSubjects->sortBy('name')->values();
            $subjectIds = $subjects->pluck('id');

            $totalEnrolledStudents = $subjectIds->isEmpty()
                ? 0
                : DB::table('student_subjects')
                    ->whereIn('subject_id', $subjectIds)
                    ->distinct()
                    ->count('student_id');

            $classesCount = $subjects->pluck('class_id')->filter()->unique()->count();
            $teacherDetail = $teacher->teacherDetail;

            $profileCompletion = collect([
                !empty($teacherDetail?->education),
                !empty($teacherDetail?->job_history),
                !empty($teacherDetail?->picture),
            ])->filter()->count();

            $summary = [
                'assigned_subjects' => $subjects->count(),
                'covered_classes' => $classesCount,
                'enrolled_students' => $totalEnrolledStudents,
                'profile_completion' => (int) round(($profileCompletion / 3) * 100),
            ];

            return view('teacher.dashboard', compact('teacher', 'subjects', 'summary'));
        } catch (\Throwable $exception) {
            Log::error('Teacher dashboard load failed', [
                'user_id' => Auth::id(),
                'error' => $exception->getMessage(),
            ]);

            return redirect()->route('login')
                ->withErrors(['error' => 'Unable to load teacher dashboard right now. Please try again.']);
        }
    }
}
