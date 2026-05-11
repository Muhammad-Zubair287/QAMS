@extends('layouts.teacher')
@section('title', 'Performance Reports')

@section('content')
<div class="row g-3 mb-4">
    <div class="col-md-4 col-lg-3">
        <a href="{{ route('teacher.performance.index') }}" class="text-decoration-none">
            <div class="card qams-card h-100 {{ $selectedSubjectId === 0 ? 'border border-primary' : '' }}">
                <div class="card-body">
                    <div class="small text-muted">All Subjects</div>
                    <div class="fw-bold text-primary">Overall Performance</div>
                </div>
            </div>
        </a>
    </div>
    @foreach($subjectCards as $subjectCard)
        <div class="col-md-4 col-lg-3">
            <a href="{{ route('teacher.performance.index', ['subject_id' => $subjectCard['id']]) }}" class="text-decoration-none">
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
        <h5 class="fw-bold text-primary mb-2"><i class="bi bi-graph-up-arrow me-2"></i>Performance Reports</h5>
        <p class="text-muted mb-0">Track average quiz and assignment performance for your assigned subjects only.</p>
    </div>
</div>

<div class="card qams-card">
    <div class="card-header bg-white border-0 py-3">
        <h6 class="mb-0 fw-bold text-primary">Quiz Performance</h6>
    </div>
    <div class="card-body p-0">
        @if($quizPerformance->isEmpty())
            <p class="text-muted text-center py-5 mb-0">No quiz performance data available.</p>
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                    <tr>
                        <th class="ps-4">Subject</th>
                        <th>Attempts</th>
                        <th>Average Score</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($quizPerformance as $row)
                        <tr>
                            <td class="ps-4 fw-semibold">{{ $row->subject_name }}</td>
                            <td>{{ $row->attempts_count }}</td>
                            <td><span class="badge bg-info-subtle text-info">{{ number_format((float) $row->avg_quiz_score, 2) }}</span></td>
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
        <h6 class="mb-0 fw-bold text-primary">Assignment Performance</h6>
    </div>
    <div class="card-body p-0">
        @if($assignmentPerformance->isEmpty())
            <p class="text-muted text-center py-5 mb-0">No assignment performance data available.</p>
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                    <tr>
                        <th class="ps-4">Subject</th>
                        <th>Submissions</th>
                        <th>Average Grade</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($assignmentPerformance as $row)
                        <tr>
                            <td class="ps-4 fw-semibold">{{ $row->subject_name }}</td>
                            <td>{{ $row->submissions_count }}</td>
                            <td><span class="badge bg-success-subtle text-success">{{ number_format((float) $row->avg_assignment_score, 2) }}</span></td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
@endsection
