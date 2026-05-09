<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View|RedirectResponse
    {
        /** @var \App\Models\User|null $currentUser */
        $currentUser = Auth::user();

        if ($currentUser === null) {
            return redirect()->route('login');
        }

        $student = User::where('id', $currentUser->id)
            ->where('role', 'student')
            ->select(['id', 'name', 'user_name', 'active'])
            ->with(['studentDetail.schoolClass', 'enrolledSubjects:id,name,class_id'])
            ->first();

        if ($student === null) {
            return redirect()->route('login')->withErrors(['error' => 'Student account not found.']);
        }

        $subjectIds = $student->enrolledSubjects->pluck('id');
        $now = Carbon::now();

        $availableQuizzes = Quiz::whereIn('subject_id', $subjectIds)
            ->where('deadline_at', '>=', $now)
            ->count();

        $pendingAssignments = Assignment::whereIn('subject_id', $subjectIds)
            ->where('deadline_at', '>=', $now)
            ->whereDoesntHave('submissions', function ($query) use ($student): void {
                $query->where('student_id', $student->id);
            })
            ->count();

        $attemptedQuizzes = QuizAttempt::where('student_id', $student->id)->count();
        $submittedAssignments = AssignmentSubmission::where('student_id', $student->id)->count();

        $summary = [
            'enrolled_subjects' => $subjectIds->count(),
            'available_quizzes' => $availableQuizzes,
            'pending_assignments' => $pendingAssignments,
            'attempted_quizzes' => $attemptedQuizzes,
            'submitted_assignments' => $submittedAssignments,
        ];

        return view('student.dashboard', compact('student', 'summary'));
    }
}
