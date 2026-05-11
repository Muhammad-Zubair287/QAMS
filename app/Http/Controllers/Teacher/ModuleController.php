<?php

namespace App\Http\Controllers\Teacher;

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
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class ModuleController extends Controller
{
    /**
     * Common teacher payload for module pages.
     */
    private function getTeacherContext(): array
    {
        /** @var \App\Models\User $currentUser */
        $currentUser = Auth::user();

        $teacher = User::where('id', $currentUser->id)
            ->where('role', 'teacher')
            ->select(['id', 'name', 'user_name', 'active'])
            ->with([
                'teacherDetail:user_id,job_history,education,picture',
                'teachingSubjects' => function ($query): void {
                    $query->select(['subjects.id', 'subjects.name', 'subjects.class_id'])
                        ->with('schoolClass:id,name,section')
                        ->withCount('students');
                },
            ])
            ->firstOrFail();

        return [
            'teacher' => $teacher,
            'subjects' => $teacher->teachingSubjects->sortBy('name')->values(),
        ];
    }

    private function isAssignedSubject(User $teacher, int $subjectId): bool
    {
        return $teacher->teachingSubjects->contains('id', $subjectId);
    }

    public function questionBank(): View
    {
        $context = $this->getTeacherContext();
        $subjectIds = $context['subjects']->pluck('id');

        $questions = QuestionBank::where('teacher_id', $context['teacher']->id)
            ->whereIn('subject_id', $subjectIds)
            ->select(['id', 'subject_id', 'question_text', 'correct_option', 'marks', 'created_at'])
            ->with('subject:id,name,class_id')
            ->orderByDesc('created_at')
            ->get();

        return view('teacher.question-bank.index', $context + ['questions' => $questions]);
    }

    public function storeQuestion(Request $request): RedirectResponse
    {
        $context = $this->getTeacherContext();
        $teacher = $context['teacher'];

        $validated = $request->validate([
            'subject_id' => 'required|integer|exists:subjects,id',
            'question_text' => 'required|string|max:2000',
            'option_a' => 'required|string|max:255',
            'option_b' => 'required|string|max:255',
            'option_c' => 'nullable|string|max:255',
            'option_d' => 'nullable|string|max:255',
            'correct_option' => 'required|in:A,B,C,D',
            'marks' => 'required|integer|min:1|max:100',
        ]);

        if (!$this->isAssignedSubject($teacher, (int) $validated['subject_id'])) {
            return back()->withErrors(['error' => 'You can only create questions for your assigned subjects.']);
        }

        QuestionBank::create([
            'teacher_id' => $teacher->id,
            'subject_id' => $validated['subject_id'],
            'question_text' => $validated['question_text'],
            'option_a' => $validated['option_a'],
            'option_b' => $validated['option_b'],
            'option_c' => $validated['option_c'],
            'option_d' => $validated['option_d'],
            'correct_option' => $validated['correct_option'],
            'marks' => $validated['marks'],
        ]);

        return back()->with('success', 'Question added to your question bank.');
    }

    public function quizzes(): View
    {
        $context = $this->getTeacherContext();
        $subjectIds = $context['subjects']->pluck('id');

        $quizzes = Quiz::where('teacher_id', $context['teacher']->id)
            ->whereIn('subject_id', $subjectIds)
            ->select(['id', 'subject_id', 'title', 'duration_minutes', 'start_at', 'deadline_at', 'created_at'])
            ->with('subject:id,name,class_id')
            ->withCount('attempts')
            ->orderByDesc('created_at')
            ->get();

        return view('teacher.quizzes.index', $context + ['quizzes' => $quizzes]);
    }

    public function storeQuiz(Request $request): RedirectResponse
    {
        $context = $this->getTeacherContext();
        $teacher = $context['teacher'];

        $validated = $request->validate([
            'subject_id' => 'required|integer|exists:subjects,id',
            'title' => 'required|string|max:120',
            'instructions' => 'nullable|string|max:3000',
            'duration_minutes' => 'required|integer|min:1|max:300',
            'start_at' => 'nullable|date',
            'deadline_at' => 'required|date|after:now',
        ]);

        if (!$this->isAssignedSubject($teacher, (int) $validated['subject_id'])) {
            return back()->withErrors(['error' => 'You can only conduct quizzes for your assigned subjects.']);
        }

        Quiz::create([
            'teacher_id' => $teacher->id,
            'subject_id' => $validated['subject_id'],
            'title' => $validated['title'],
            'instructions' => $validated['instructions'],
            'duration_minutes' => $validated['duration_minutes'],
            'start_at' => $validated['start_at'],
            'deadline_at' => $validated['deadline_at'],
        ]);

        return back()->with('success', 'Quiz created successfully.');
    }

    public function extendQuizDeadline(Request $request, Quiz $quiz): RedirectResponse
    {
        $context = $this->getTeacherContext();
        $teacher = $context['teacher'];

        if ($quiz->teacher_id !== $teacher->id || !$this->isAssignedSubject($teacher, (int) $quiz->subject_id)) {
            return back()->withErrors(['error' => 'You can only update your own assigned subject quiz.']);
        }

        $validated = $request->validate([
            'deadline_at' => 'required|date|after:now',
        ]);

        $quiz->update([
            'deadline_at' => $validated['deadline_at'],
        ]);

        return back()->with('success', 'Quiz deadline updated.');
    }

    public function assignments(): View
    {
        $context = $this->getTeacherContext();
        $subjectIds = $context['subjects']->pluck('id');

        $assignments = Assignment::where('teacher_id', $context['teacher']->id)
            ->whereIn('subject_id', $subjectIds)
            ->select(['id', 'subject_id', 'title', 'description', 'deadline_at', 'created_at'])
            ->with('subject:id,name,class_id')
            ->withCount('submissions')
            ->orderByDesc('created_at')
            ->get();

        return view('teacher.assignments.index', $context + ['assignments' => $assignments]);
    }

    public function storeAssignment(Request $request): RedirectResponse
    {
        $context = $this->getTeacherContext();
        $teacher = $context['teacher'];

        $validated = $request->validate([
            'subject_id' => 'required|integer|exists:subjects,id',
            'title' => 'required|string|max:120',
            'description' => 'nullable|string|max:4000',
            'deadline_at' => 'required|date|after:now',
        ]);

        if (!$this->isAssignedSubject($teacher, (int) $validated['subject_id'])) {
            return back()->withErrors(['error' => 'You can only upload assignments for your assigned subjects.']);
        }

        Assignment::create([
            'teacher_id' => $teacher->id,
            'subject_id' => $validated['subject_id'],
            'title' => $validated['title'],
            'description' => $validated['description'],
            'deadline_at' => $validated['deadline_at'],
        ]);

        return back()->with('success', 'Assignment uploaded successfully.');
    }

    public function extendAssignmentDeadline(Request $request, Assignment $assignment): RedirectResponse
    {
        $context = $this->getTeacherContext();
        $teacher = $context['teacher'];

        if ($assignment->teacher_id !== $teacher->id || !$this->isAssignedSubject($teacher, (int) $assignment->subject_id)) {
            return back()->withErrors(['error' => 'You can only update your own assigned subject assignment.']);
        }

        $validated = $request->validate([
            'deadline_at' => 'required|date|after:now',
        ]);

        $assignment->update([
            'deadline_at' => $validated['deadline_at'],
        ]);

        return back()->with('success', 'Assignment deadline updated.');
    }

    public function results(Request $request): View
    {
        $context = $this->getTeacherContext();
        $teacher = $context['teacher'];
        $subjectIds = $context['subjects']->pluck('id');
        $selectedSubjectId = (int) $request->integer('subject_id');

        if ($selectedSubjectId !== 0 && !$subjectIds->contains($selectedSubjectId)) {
            $selectedSubjectId = 0;
        }

        $subjectCards = $context['subjects']->map(function ($subject) use ($teacher) {
            $quizAttemptCount = QuizAttempt::whereHas('quiz', function ($query) use ($teacher, $subject): void {
                $query->where('teacher_id', $teacher->id)->where('subject_id', $subject->id);
            })->count();

            $assignmentSubmissionCount = AssignmentSubmission::whereHas('assignment', function ($query) use ($teacher, $subject): void {
                $query->where('teacher_id', $teacher->id)->where('subject_id', $subject->id);
            })->count();

            return [
                'id' => $subject->id,
                'name' => $subject->name,
                'quiz_attempt_count' => $quizAttemptCount,
                'assignment_submission_count' => $assignmentSubmissionCount,
            ];
        });

        $quizAttemptsQuery = QuizAttempt::whereHas('quiz', function ($query) use ($teacher, $subjectIds): void {
            $query->where('teacher_id', $teacher->id)->whereIn('subject_id', $subjectIds);
        })
            ->select(['id', 'quiz_id', 'student_id', 'score', 'total_marks', 'submitted_at', 'published_at'])
            ->with([
                'quiz:id,title,subject_id',
                'quiz.subject:id,name,class_id',
                'student:id,name,user_name',
            ])
            ->orderByDesc('submitted_at');

        if ($selectedSubjectId !== 0) {
            $quizAttemptsQuery->whereHas('quiz', function ($query) use ($selectedSubjectId): void {
                $query->where('subject_id', $selectedSubjectId);
            });
        }

        $quizAttempts = $quizAttemptsQuery->get();

        $assignmentSubmissionsQuery = AssignmentSubmission::whereHas('assignment', function ($query) use ($teacher, $subjectIds): void {
            $query->where('teacher_id', $teacher->id)->whereIn('subject_id', $subjectIds);
        })
            ->select(['id', 'assignment_id', 'student_id', 'score', 'feedback', 'submitted_at', 'graded_at', 'published_at'])
            ->with([
                'assignment:id,title,subject_id',
                'assignment.subject:id,name,class_id',
                'student:id,name,user_name',
            ])
            ->orderByDesc('submitted_at');

        if ($selectedSubjectId !== 0) {
            $assignmentSubmissionsQuery->whereHas('assignment', function ($query) use ($selectedSubjectId): void {
                $query->where('subject_id', $selectedSubjectId);
            });
        }

        $assignmentSubmissions = $assignmentSubmissionsQuery->get();

        return view('teacher.results.index', $context + [
            'quizAttempts' => $quizAttempts,
            'assignmentSubmissions' => $assignmentSubmissions,
            'subjectCards' => $subjectCards,
            'selectedSubjectId' => $selectedSubjectId,
        ]);
    }

    public function publishQuizResults(): RedirectResponse
    {
        $context = $this->getTeacherContext();
        $teacher = $context['teacher'];
        $subjectIds = $context['subjects']->pluck('id');
        $now = Carbon::now();

        $affected = QuizAttempt::whereNull('published_at')
            ->whereHas('quiz', function ($query) use ($teacher, $subjectIds): void {
                $query->where('teacher_id', $teacher->id)->whereIn('subject_id', $subjectIds);
            })
            ->update(['published_at' => $now]);

        return back()->with('success', $affected . ' quiz result(s) published.');
    }

    public function gradeAssignmentSubmission(Request $request, AssignmentSubmission $submission): RedirectResponse
    {
        $context = $this->getTeacherContext();
        $teacher = $context['teacher'];

        $submission->loadMissing('assignment:id,teacher_id,subject_id');

        if ($submission->assignment === null || $submission->assignment->teacher_id !== $teacher->id) {
            return back()->withErrors(['error' => 'You can only grade your own assignment submissions.']);
        }

        $validated = $request->validate([
            'score' => 'required|numeric|min:0|max:100',
            'feedback' => 'nullable|string|max:3000',
            'publish_now' => 'nullable|in:1',
        ]);

        DB::beginTransaction();
        try {
            $submission->update([
                'score' => $validated['score'],
                'feedback' => $validated['feedback'] ?? null,
                'graded_by' => $teacher->id,
                'graded_at' => Carbon::now(),
                'published_at' => isset($validated['publish_now']) ? Carbon::now() : $submission->published_at,
            ]);
            DB::commit();
        } catch (\Throwable $exception) {
            DB::rollBack();
            Log::error('Assignment grading failed', [
                'teacher_id' => $teacher->id,
                'submission_id' => $submission->id,
                'error' => $exception->getMessage(),
            ]);
            return back()->withErrors(['error' => 'Failed to grade assignment submission.']);
        }

        return back()->with('success', 'Assignment submission graded successfully.');
    }

    public function performance(Request $request): View
    {
        $context = $this->getTeacherContext();
        $teacher = $context['teacher'];
        $subjectIds = $context['subjects']->pluck('id');
        $selectedSubjectId = (int) $request->integer('subject_id');

        if ($selectedSubjectId !== 0 && !$subjectIds->contains($selectedSubjectId)) {
            $selectedSubjectId = 0;
        }

        $subjectCards = $context['subjects']->map(function ($subject) use ($teacher) {
            $quizAttemptCount = QuizAttempt::whereHas('quiz', function ($query) use ($teacher, $subject): void {
                $query->where('teacher_id', $teacher->id)->where('subject_id', $subject->id);
            })->count();

            $assignmentSubmissionCount = AssignmentSubmission::whereHas('assignment', function ($query) use ($teacher, $subject): void {
                $query->where('teacher_id', $teacher->id)->where('subject_id', $subject->id);
            })->count();

            return [
                'id' => $subject->id,
                'name' => $subject->name,
                'quiz_attempt_count' => $quizAttemptCount,
                'assignment_submission_count' => $assignmentSubmissionCount,
            ];
        });

        $quizPerformanceQuery = QuizAttempt::query()
            ->join('quizzes', 'quizzes.id', '=', 'quiz_attempts.quiz_id')
            ->join('subjects', 'subjects.id', '=', 'quizzes.subject_id')
            ->where('quizzes.teacher_id', $teacher->id)
            ->whereIn('quizzes.subject_id', $subjectIds)
            ->groupBy('subjects.id', 'subjects.name')
            ->select([
                'subjects.id as subject_id',
                'subjects.name as subject_name',
                DB::raw('COUNT(quiz_attempts.id) as attempts_count'),
                DB::raw('COALESCE(AVG(quiz_attempts.score),0) as avg_quiz_score'),
            ])
            ->orderBy('subjects.name');

        if ($selectedSubjectId !== 0) {
            $quizPerformanceQuery->where('subjects.id', $selectedSubjectId);
        }

        $quizPerformance = $quizPerformanceQuery->get();

        $assignmentPerformanceQuery = AssignmentSubmission::query()
            ->join('assignments', 'assignments.id', '=', 'assignment_submissions.assignment_id')
            ->join('subjects', 'subjects.id', '=', 'assignments.subject_id')
            ->where('assignments.teacher_id', $teacher->id)
            ->whereIn('assignments.subject_id', $subjectIds)
            ->groupBy('subjects.id', 'subjects.name')
            ->select([
                'subjects.id as subject_id',
                'subjects.name as subject_name',
                DB::raw('COUNT(assignment_submissions.id) as submissions_count'),
                DB::raw('COALESCE(AVG(assignment_submissions.score),0) as avg_assignment_score'),
            ])
            ->orderBy('subjects.name');

        if ($selectedSubjectId !== 0) {
            $assignmentPerformanceQuery->where('subjects.id', $selectedSubjectId);
        }

        $assignmentPerformance = $assignmentPerformanceQuery->get();

        return view('teacher.performance.index', $context + [
            'quizPerformance' => $quizPerformance,
            'assignmentPerformance' => $assignmentPerformance,
            'subjectCards' => $subjectCards,
            'selectedSubjectId' => $selectedSubjectId,
        ]);
    }
}
