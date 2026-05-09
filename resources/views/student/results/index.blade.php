@extends('layouts.student')
@section('title', 'Results & Reports')

@section('content')
<div class="row g-3 mb-4">
    <div class="col-md-6">
        <div class="card qams-card">
            <div class="card-body text-center">
                <div class="small text-muted">Quiz Attempts</div>
                <div class="h4 fw-bold text-primary mb-0">{{ $quizAttempts->count() }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card qams-card">
            <div class="card-body text-center">
                <div class="small text-muted">Assignment Submissions</div>
                <div class="h4 fw-bold text-success mb-0">{{ $assignmentSubmissions->count() }}</div>
            </div>
        </div>
    </div>
</div>

<div class="card qams-card mb-4">
    <div class="card-header bg-white border-0 py-3">
        <h6 class="mb-0 fw-bold text-primary">Quiz Results</h6>
    </div>
    <div class="card-body p-0">
        @if($quizAttempts->isEmpty())
            <p class="text-muted text-center py-4 mb-0">No quiz attempts found.</p>
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                    <tr>
                        <th class="ps-4">Quiz</th>
                        <th>Subject</th>
                        <th>Score</th>
                        <th>Submitted</th>
                        <th>Status</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($quizAttempts as $attempt)
                        <tr>
                            <td class="ps-4">{{ $attempt->quiz?->title ?? '—' }}</td>
                            <td>{{ $attempt->quiz?->subject?->name ?? '—' }}</td>
                            <td><span class="badge bg-info-subtle text-info">{{ $attempt->score }}/{{ $attempt->total_marks }}</span></td>
                            <td>{{ $attempt->submitted_at?->format('d M Y h:i A') ?? '—' }}</td>
                            <td>
                                @if($attempt->published_at)
                                    <span class="badge bg-success">Published</span>
                                @else
                                    <span class="badge bg-warning text-dark">Awaiting Publish</span>
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

<div class="card qams-card">
    <div class="card-header bg-white border-0 py-3">
        <h6 class="mb-0 fw-bold text-primary">Assignment Results</h6>
    </div>
    <div class="card-body p-0">
        @if($assignmentSubmissions->isEmpty())
            <p class="text-muted text-center py-4 mb-0">No assignment submission records found.</p>
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                    <tr>
                        <th class="ps-4">Assignment</th>
                        <th>Subject</th>
                        <th>Score</th>
                        <th>Feedback</th>
                        <th>Status</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($assignmentSubmissions as $submission)
                        <tr>
                            <td class="ps-4">{{ $submission->assignment?->title ?? '—' }}</td>
                            <td>{{ $submission->assignment?->subject?->name ?? '—' }}</td>
                            <td>
                                @if($submission->score !== null)
                                    <span class="badge bg-success-subtle text-success">{{ $submission->score }}</span>
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary">Pending</span>
                                @endif
                            </td>
                            <td>{{ $submission->feedback ?? '—' }}</td>
                            <td>
                                @if($submission->published_at)
                                    <span class="badge bg-success">Published</span>
                                @else
                                    <span class="badge bg-warning text-dark">Awaiting Publish</span>
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
@endsection
