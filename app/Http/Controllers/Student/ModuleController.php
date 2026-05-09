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
use Illuminate\Support\Facades\Storage;
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

    public function quizzes(): View
    {
        $student = $this->getStudent();
        $subjectIds = $student->enrolledSubjects->pluck('id');

        $quizzes = Quiz::whereIn('subject_id', $subjectIds)
            ->select(['id', 'subject_id', 'teacher_id', 'title', 'duration_minutes', 'start_at', 'deadline_at'])
            ->with(['subject:id,name,class_id', 'teacher:id,name'])
            ->with(['attempts' => function ($query) use ($student): void {
                $query->where('student_id', $student->id)->select(['id', 'quiz_id', 'student_id', 'score', 'total_marks', 'published_at']);
            }])
            ->orderBy('deadline_at')
            ->get();

        return view('student.quizzes.index', compact('student', 'quizzes'));
    }

    public function attemptQuiz(Quiz $quiz): View|RedirectResponse
    {
        $student = $this->getStudent();

        if (!$this->isEnrolled($student, (int) $quiz->subject_id)) {
            return redirect()->route('student.quizzes.index')->withErrors(['error' => 'You can only attempt quizzes for your enrolled subjects.']);
        }

        if (Carbon::now()->gt($quiz->deadline_at)) {
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

        if (Carbon::now()->gt($quiz->deadline_at)) {
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
            'started_at' => Carbon::now(),
            'submitted_at' => Carbon::now(),
        ]);

        return redirect()->route('student.results.index')->with('success', 'Quiz submitted and auto-marked successfully.');
    }

    public function assignments(): View
    {
        $student = $this->getStudent();
        $subjectIds = $student->enrolledSubjects->pluck('id');

        $assignments = Assignment::whereIn('subject_id', $subjectIds)
            ->select(['id', 'subject_id', 'teacher_id', 'title', 'description', 'deadline_at'])
            ->with(['subject:id,name,class_id', 'teacher:id,name'])
            ->with(['submissions' => function ($query) use ($student): void {
                $query->where('student_id', $student->id)
                    ->select(['id', 'assignment_id', 'student_id', 'submitted_at', 'score', 'published_at']);
            }])
            ->orderBy('deadline_at')
            ->get();

        return view('student.assignments.index', compact('student', 'assignments'));
    }

    public function submitAssignment(Request $request, Assignment $assignment): RedirectResponse
    {
        $student = $this->getStudent();

        if (!$this->isEnrolled($student, (int) $assignment->subject_id)) {
            return back()->withErrors(['error' => 'You can only submit assignments for your enrolled subjects.']);
        }

        if (Carbon::now()->gt($assignment->deadline_at)) {
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
            'submitted_at' => Carbon::now(),
        ]);

        return back()->with('success', 'Assignment submitted successfully.');
    }

    public function results(): View
    {
        $student = $this->getStudent();
        $subjectIds = $student->enrolledSubjects->pluck('id');

        $overdueAssignments = Assignment::whereIn('subject_id', $subjectIds)
            ->where('deadline_at', '<', Carbon::now())
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
                'graded_at' => Carbon::now(),
                'published_at' => Carbon::now(),
            ]);
        }

        $quizAttempts = QuizAttempt::where('student_id', $student->id)
            ->whereHas('quiz', function ($query) use ($subjectIds): void {
                $query->whereIn('subject_id', $subjectIds);
            })
            ->select(['id', 'quiz_id', 'score', 'total_marks', 'submitted_at', 'published_at'])
            ->with(['quiz:id,title,subject_id', 'quiz.subject:id,name,class_id'])
            ->orderByDesc('submitted_at')
            ->get();

        $assignmentSubmissions = AssignmentSubmission::where('student_id', $student->id)
            ->whereHas('assignment', function ($query) use ($subjectIds): void {
                $query->whereIn('subject_id', $subjectIds);
            })
            ->select(['id', 'assignment_id', 'score', 'feedback', 'solution_file', 'submitted_at', 'published_at'])
            ->with(['assignment:id,title,subject_id', 'assignment.subject:id,name,class_id'])
            ->orderByDesc(DB::raw('COALESCE(submitted_at, created_at)'))
            ->get();

        return view('student.results.index', compact('student', 'quizAttempts', 'assignmentSubmissions'));
    }
}
