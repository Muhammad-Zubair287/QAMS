@extends('layouts.teacher')
@section('title', 'Results Publish')

@section('content')
<div class="row g-3 mb-4">
    <div class="col-md-4 col-lg-3">
        <a href="{{ route('teacher.results.index') }}" class="text-decoration-none">
            <div class="card qams-card h-100 {{ $selectedSubjectId === 0 ? 'border border-primary' : '' }}">
                <div class="card-body">
                    <div class="small text-muted">All Subjects</div>
                    <div class="fw-bold text-primary">Overall Results</div>
                </div>
            </div>
        </a>
    </div>
    @foreach($subjectCards as $subjectCard)
        <div class="col-md-4 col-lg-3">
            <a href="{{ route('teacher.results.index', ['subject_id' => $subjectCard['id']]) }}" class="text-decoration-none">
                <div class="card qams-card h-100 {{ $selectedSubjectId === $subjectCard['id'] ? 'border border-primary' : '' }}">
                    <div class="card-body">
                        <div class="small text-muted">{{ $subjectCard['quiz_attempt_count'] }} attempts</div>
                        <div class="fw-bold text-primary">{{ $subjectCard['name'] }}</div>
                        <div class="small text-success">{{ $subjectCard['assignment_submission_count'] }} submissions</div>
                    </div>
                </div>
            </a>
        </div>
    @endforeach
</div>

<div class="card qams-card mb-4">
    <div class="card-body p-4">
        <h5 class="fw-bold text-primary mb-2"><i class="bi bi-check2-square me-2"></i>Publish Results</h5>
        <p class="text-muted mb-3">Publish quiz results and grade assignment submissions for your assigned subjects only.</p>
        <form method="POST" action="{{ route('teacher.results.publish-quizzes', ['subject_id' => $selectedSubjectId]) }}">
            @csrf
            <button class="btn btn-primary">
                <i class="bi bi-send-check me-1"></i>Publish All Pending Quiz Results
            </button>
        </form>
    </div>
</div>

<div class="card qams-card">
    <div class="card-header bg-white border-0 py-3">
        <h6 class="mb-0 fw-bold text-primary">Quiz Attempts ({{ $quizAttempts->count() }})</h6>
    </div>
    <div class="card-body p-0">
        @if($quizAttempts->isEmpty())
            <p class="text-muted text-center py-4 mb-0">No quiz attempts found yet.</p>
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Student</th>
                            <th>Quiz</th>
                            <th>Subject</th>
                            <th>Score</th>
                            <th>Submitted</th>
                            <th>Published</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($quizAttempts as $attempt)
                        <tr>
                            <td class="ps-4">{{ $attempt->student?->name ?? '—' }}</td>
                            <td>{{ $attempt->quiz?->title ?? '—' }}</td>
                            <td>{{ $attempt->quiz?->subject?->name ?? '—' }}</td>
                            <td>{{ $attempt->score }}/{{ $attempt->total_marks }}</td>
                            <td>{{ $attempt->submitted_at?->format('d M Y h:i A') ?? '—' }}</td>
                            <td>
                                @if($attempt->published_at)
                                    <span class="badge bg-success">Published</span>
                                @else
                                    <span class="badge bg-warning text-dark">Pending</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>

<div class="card qams-card mt-4">
    <div class="card-header bg-white border-0 py-3">
        <h6 class="mb-0 fw-bold text-primary">Assignment Submissions ({{ $assignmentSubmissions->count() }})</h6>
    </div>
    <div class="card-body p-0">
        @if($assignmentSubmissions->isEmpty())
            <p class="text-muted text-center py-4 mb-0">No assignment submissions found yet.</p>
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                    <tr>
                        <th class="ps-4">Student</th>
                        <th>Assignment</th>
                        <th>Subject</th>
                        <th>Submitted</th>
                        <th>Current Grade</th>
                        <th class="text-end pe-4">Grade / Publish</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($assignmentSubmissions as $submission)
                        <tr>
                            <td class="ps-4">{{ $submission->student?->name ?? '—' }}</td>
                            <td>{{ $submission->assignment?->title ?? '—' }}</td>
                            <td>{{ $submission->assignment?->subject?->name ?? '—' }}</td>
                            <td>{{ $submission->submitted_at?->format('d M Y h:i A') ?? '—' }}</td>
                            <td>
                                @if($submission->score !== null)
                                    <span class="badge bg-success-subtle text-success">{{ $submission->score }}</span>
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary">Not graded</span>
                                @endif
                            </td>
                            <td class="pe-4">
                                <form method="POST" action="{{ route('teacher.results.grade-assignment', ['submission' => $submission->id, 'subject_id' => $selectedSubjectId]) }}" class="d-flex flex-column flex-md-row gap-2 justify-content-end">
                                    @csrf
                                    <input type="number" min="0" max="100" step="0.01" name="score" class="form-control form-control-sm" placeholder="Score" required>
                                    <input type="text" name="feedback" class="form-control form-control-sm" placeholder="Feedback (optional)">
                                    <div class="form-check mt-1">
                                        <input class="form-check-input" type="checkbox" name="publish_now" value="1" id="publish_{{ $submission->id }}">
                                        <label class="form-check-label small" for="publish_{{ $submission->id }}">Publish</label>
                                    </div>
                                    <button class="btn btn-outline-primary btn-sm">Save</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
@endsection
