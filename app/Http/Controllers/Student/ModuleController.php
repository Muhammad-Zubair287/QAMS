<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\QuestionBank;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ModuleController extends Controller
{
    private function getStudent(): User
    {
        /** @var \App\Models\User $currentUser */
        $currentUser = Auth::user();

        return User::where('id', $currentUser->id)
            ->where('role', 'student')
            ->select(['id', 'name', 'user_name', 'active'])
            ->with(['studentDetail.schoolClass', 'enrolledSubjects:id,name,class_id'])
            ->firstOrFail();
    }

    private function isEnrolled(User $student, int $subjectId): bool
    {
        return $student->enrolledSubjects->contains('id', $subjectId);
    }

    private function now(): Carbon
    {
        return Carbon::now(config('app.timezone'));
    }

    private function applyAutoZeroForOverdueAssignments(User $student): void
    {
        $subjectIds = $student->enrolledSubjects->pluck('id');

        $overdueAssignments = Assignment::whereIn('subject_id', $subjectIds)
            ->where('deadline_at', '<', $this->now())
            ->whereDoesntHave('submissions', function ($query) use ($student): void {
                $query->where('student_id', $student->id);
            })
            ->select(['id', 'teacher_id'])
            ->get();

        foreach ($overdueAssignments as $assignment) {
            AssignmentSubmission::create([
                'assignment_id' => $assignment->id,
                'student_id' => $student->id,
                'score' => 0,
                'feedback' => 'Auto-assigned zero due to missed deadline.',
                'graded_by' => $assignment->teacher_id,
                'graded_at' => $this->now(),
                'published_at' => $this->now(),
            ]);
        }
    }

    public function quizzes(Request $request): View
    {
        $student = $this->getStudent();
        $subjectIds = $student->enrolledSubjects->pluck('id');
        $selectedSubjectId = (int) $request->integer('subject_id');

        if ($selectedSubjectId !== 0 && !$subjectIds->contains($selectedSubjectId)) {
            $selectedSubjectId = 0;
        }

        $enrolledSubjects = $student->enrolledSubjects->map(function ($subject) use ($student) {
            $quizCount = Quiz::where('subject_id', $subject->id)->count();
            $attemptedCount = QuizAttempt::where('student_id', $student->id)
                ->whereHas('quiz', function ($query) use ($subject): void {
                    $query->where('subject_id', $subject->id);
                })
                ->count();

            return [
                'id' => $subject->id,
                'name' => $subject->name,
                'quiz_count' => $quizCount,
                'attempted_count' => $attemptedCount,
            ];
        });

        $quizzesQuery = Quiz::whereIn('subject_id', $subjectIds)
            ->select(['id', 'subject_id', 'teacher_id', 'title', 'duration_minutes', 'start_at', 'deadline_at'])
            ->with(['subject:id,name,class_id', 'teacher:id,name'])
            ->with(['attempts' => function ($query) use ($student): void {
                $query->where('student_id', $student->id)->select(['id', 'quiz_id', 'student_id', 'score', 'total_marks', 'published_at']);
            }]);

        if ($selectedSubjectId !== 0) {
            $quizzesQuery->where('subject_id', $selectedSubjectId);
        }

        $quizzes = $quizzesQuery->orderBy('deadline_at')->get();

        $currentTime = $this->now();

        return view('student.quizzes.index', compact('student', 'quizzes', 'enrolledSubjects', 'selectedSubjectId', 'currentTime'));
    }

    public function attemptQuiz(Quiz $quiz): View|RedirectResponse
    {
        $student = $this->getStudent();

        if (!$this->isEnrolled($student, (int) $quiz->subject_id)) {
            return redirect()->route('student.quizzes.index')->withErrors(['error' => 'You can only attempt quizzes for your enrolled subjects.']);
        }

        if ($this->now()->gt($quiz->deadline_at)) {
            return redirect()->route('student.quizzes.index')->withErrors(['error' => 'This quiz deadline has passed.']);
        }

        $alreadyAttempted = QuizAttempt::where('quiz_id', $quiz->id)->where('student_id', $student->id)->exists();
        if ($alreadyAttempted) {
            return redirect()->route('student.quizzes.index')->withErrors(['error' => 'You have already attempted this quiz.']);
        }

        $questions = QuestionBank::where('subject_id', $quiz->subject_id)
            ->select(['id', 'question_text', 'option_a', 'option_b', 'option_c', 'option_d', 'marks'])
            ->orderBy('id')
            ->limit(10)
            ->get();

        return view('student.quizzes.attempt', compact('student', 'quiz', 'questions'));
    }

    public function submitQuiz(Request $request, Quiz $quiz): RedirectResponse
    {
        $student = $this->getStudent();

        if (!$this->isEnrolled($student, (int) $quiz->subject_id)) {
            return back()->withErrors(['error' => 'You can only submit quizzes for your enrolled subjects.']);
        }

        if ($this->now()->gt($quiz->deadline_at)) {
            return back()->withErrors(['error' => 'Quiz deadline has passed.']);
        }

        $alreadyAttempted = QuizAttempt::where('quiz_id', $quiz->id)->where('student_id', $student->id)->exists();
        if ($alreadyAttempted) {
            return back()->withErrors(['error' => 'You have already submitted this quiz.']);
        }

        $questions = QuestionBank::where('subject_id', $quiz->subject_id)
            ->select(['id', 'correct_option', 'marks'])
            ->orderBy('id')
            ->limit(10)
            ->get();

        if ($questions->isEmpty()) {
            return back()->withErrors(['error' => 'This quiz currently has no question bank items.']);
        }

        $validated = $request->validate([
            'answers' => 'required|array',
        ]);

        $score = 0.0;
        $total = 0.0;
        foreach ($questions as $question) {
            $total += (float) $question->marks;
            $selected = $validated['answers'][$question->id] ?? null;
            if ($selected !== null && strtoupper((string) $selected) === strtoupper($question->correct_option)) {
                $score += (float) $question->marks;
            }
        }

        QuizAttempt::create([
            'quiz_id' => $quiz->id,
            'student_id' => $student->id,
            'score' => $score,
            'total_marks' => $total,
            'started_at' => $this->now(),
            'submitted_at' => $this->now(),
        ]);

        return redirect()->route('student.results.index')->with('success', 'Quiz submitted and auto-marked successfully.');
    }

    public function assignments(Request $request): View
    {
        $student = $this->getStudent();
        $this->applyAutoZeroForOverdueAssignments($student);
        $subjectIds = $student->enrolledSubjects->pluck('id');
        $selectedSubjectId = (int) $request->integer('subject_id');

        if ($selectedSubjectId !== 0 && !$subjectIds->contains($selectedSubjectId)) {
            $selectedSubjectId = 0;
        }

        $enrolledSubjects = $student->enrolledSubjects->map(function ($subject) use ($student) {
            $assignmentCount = Assignment::where('subject_id', $subject->id)->count();
            $submittedCount = AssignmentSubmission::where('student_id', $student->id)
                ->whereHas('assignment', function ($query) use ($subject): void {
                    $query->where('subject_id', $subject->id);
                })
                ->count();

            return [
                'id' => $subject->id,
                'name' => $subject->name,
                'assignment_count' => $assignmentCount,
                'submitted_count' => $submittedCount,
            ];
        });

        $assignmentsQuery = Assignment::whereIn('subject_id', $subjectIds)
            ->select(['id', 'subject_id', 'teacher_id', 'title', 'description', 'deadline_at'])
            ->with(['subject:id,name,class_id', 'teacher:id,name'])
            ->with(['submissions' => function ($query) use ($student): void {
                $query->where('student_id', $student->id)
                    ->select(['id', 'assignment_id', 'student_id', 'submitted_at', 'score', 'published_at']);
            }]);

        if ($selectedSubjectId !== 0) {
            $assignmentsQuery->where('subject_id', $selectedSubjectId);
        }

        $assignments = $assignmentsQuery->orderBy('deadline_at')->get();

        $currentTime = $this->now();
        return view('student.assignments.index', compact('student', 'assignments', 'currentTime', 'enrolledSubjects', 'selectedSubjectId'));
    }

    public function submitAssignment(Request $request, Assignment $assignment): RedirectResponse
    {
        $student = $this->getStudent();

        if (!$this->isEnrolled($student, (int) $assignment->subject_id)) {
            return back()->withErrors(['error' => 'You can only submit assignments for your enrolled subjects.']);
        }

        if ($this->now()->gt($assignment->deadline_at)) {
            $this->applyAutoZeroForOverdueAssignments($student);
            return back()->withErrors(['error' => 'Assignment deadline has passed.']);
        }

        $existing = AssignmentSubmission::where('assignment_id', $assignment->id)
            ->where('student_id', $student->id)
            ->first();

        if ($existing !== null) {
            return back()->withErrors(['error' => 'You have already submitted this assignment.']);
        }

        $validated = $request->validate([
            'solution_text' => 'nullable|string|max:5000',
            'solution_file' => 'nullable|file|mimes:pdf,doc,docx,txt,png,jpg,jpeg|max:4096',
        ]);

        if (empty($validated['solution_text']) && !$request->hasFile('solution_file')) {
            return back()->withErrors(['error' => 'Add solution text or upload a file before submitting.']);
        }

        $filePath = null;
        if ($request->hasFile('solution_file')) {
            $filePath = $request->file('solution_file')->store('assignment-solutions', 'public');
        }

        AssignmentSubmission::create([
            'assignment_id' => $assignment->id,
            'student_id' => $student->id,
            'solution_text' => $validated['solution_text'] ?? null,
            'solution_file' => $filePath,
            'submitted_at' => $this->now(),
        ]);

        return back()->with('success', 'Assignment submitted successfully.');
    }

    public function results(Request $request): View
    {
        $student = $this->getStudent();
        $this->applyAutoZeroForOverdueAssignments($student);
        $subjectIds = $student->enrolledSubjects->pluck('id');
        $selectedSubjectId = (int) $request->integer('subject_id');

        if ($selectedSubjectId !== 0 && !$subjectIds->contains($selectedSubjectId)) {
            $selectedSubjectId = 0;
        }

        $enrolledSubjects = $student->enrolledSubjects->map(function ($subject) use ($student) {
            $quizAttemptCount = QuizAttempt::where('student_id', $student->id)
                ->whereHas('quiz', function ($query) use ($subject): void {
                    $query->where('subject_id', $subject->id);
                })
                ->count();

            $assignmentCount = AssignmentSubmission::where('student_id', $student->id)
                ->whereHas('assignment', function ($query) use ($subject): void {
                    $query->where('subject_id', $subject->id);
                })
                ->count();

            return [
                'id' => $subject->id,
                'name' => $subject->name,
                'quiz_attempt_count' => $quizAttemptCount,
                'assignment_count' => $assignmentCount,
            ];
        });

        $quizAttemptsQuery = QuizAttempt::where('student_id', $student->id)
            ->whereHas('quiz', function ($query) use ($subjectIds): void {
                $query->whereIn('subject_id', $subjectIds);
            })
            ->select(['id', 'quiz_id', 'score', 'total_marks', 'submitted_at', 'published_at'])
            ->with(['quiz:id,title,subject_id', 'quiz.subject:id,name,class_id'])
            ->orderByDesc('submitted_at');

        if ($selectedSubjectId !== 0) {
            $quizAttemptsQuery->whereHas('quiz', function ($query) use ($selectedSubjectId): void {
                $query->where('subject_id', $selectedSubjectId);
            });
        }

        $quizAttempts = $quizAttemptsQuery->get();

        $assignmentSubmissionsQuery = AssignmentSubmission::where('student_id', $student->id)
            ->whereHas('assignment', function ($query) use ($subjectIds): void {
                $query->whereIn('subject_id', $subjectIds);
            })
            ->select(['id', 'assignment_id', 'score', 'feedback', 'solution_file', 'submitted_at', 'published_at'])
            ->with(['assignment:id,title,subject_id', 'assignment.subject:id,name,class_id'])
            ->orderByDesc(DB::raw('COALESCE(submitted_at, created_at)'));

        if ($selectedSubjectId !== 0) {
            $assignmentSubmissionsQuery->whereHas('assignment', function ($query) use ($selectedSubjectId): void {
                $query->where('subject_id', $selectedSubjectId);
            });
        }

        $assignmentSubmissions = $assignmentSubmissionsQuery->get();

        return view('student.results.index', compact(
            'student',
            'quizAttempts',
            'assignmentSubmissions',
            'enrolledSubjects',
            'selectedSubjectId'
        ));
    }
}
