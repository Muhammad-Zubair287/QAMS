@extends('layouts.student')
@section('title', 'Quizzes')

@section('content')
<div class="row g-3 mb-4">
    <div class="col-md-4 col-lg-3">
        <a href="{{ route('student.quizzes.index') }}" class="text-decoration-none">
            <div class="card qams-card h-100 {{ $selectedSubjectId === 0 ? 'border border-primary' : '' }}">
                <div class="card-body">
                    <div class="small text-muted">All Subjects</div>
                    <div class="fw-bold text-primary">All Quizzes</div>
                </div>
            </div>
        </a>
    </div>
    @foreach($enrolledSubjects as $subject)
        <div class="col-md-4 col-lg-3">
            <a href="{{ route('student.quizzes.index', ['subject_id' => $subject['id']]) }}" class="text-decoration-none">
                <div class="card qams-card h-100 {{ $selectedSubjectId === $subject['id'] ? 'border border-primary' : '' }}">
                    <div class="card-body">
                        <div class="small text-muted">{{ $subject['quiz_count'] }} Quiz(s)</div>
                        <div class="fw-bold text-primary">{{ $subject['name'] }}</div>
                        <div class="small text-success">{{ $subject['attempted_count'] }} attempted</div>
                    </div>
                </div>
            </a>
        </div>
    @endforeach
</div>

<div class="card qams-card">
    <div class="card-header bg-white border-0 py-3">
        <h6 class="page-title">
            <i class="bi bi-ui-checks-grid me-2"></i>
            @if($selectedSubjectId === 0)
                Available Quizzes ({{ $quizzes->count() }})
            @else
                Subject Quizzes ({{ $quizzes->count() }})
            @endif
        </h6>
    </div>
    <div class="card-body p-0">
        @if($quizzes->isEmpty())
            <div class="empty-state">
                <div class="icon"><i class="bi bi-inboxes"></i></div>
                <div>No quizzes available for your enrolled subjects.</div>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                    <tr>
                        <th class="ps-4">Quiz</th>
                        <th>Subject</th>
                        <th>Teacher</th>
                        <th>Deadline</th>
                        <th>Status</th>
                        <th class="text-end pe-4">Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($quizzes as $quiz)
                        @php $attempt = $quiz->attempts->first(); @endphp
                        <tr>
                            <td class="ps-4 fw-semibold">{{ $quiz->title }}</td>
                            <td>{{ $quiz->subject?->name ?? '—' }}</td>
                            <td>{{ $quiz->teacher?->name ?? '—' }}</td>
                            <td>{{ $quiz->deadline_at?->format('d M Y h:i A') ?? '—' }}</td>
                            <td>
                                @if($attempt)
                                    <span class="badge bg-success status-badge">Attempted</span>
                                @elseif($currentTime->gt($quiz->deadline_at))
                                    <span class="badge bg-danger status-badge">Closed</span>
                                @else
                                    <span class="badge bg-warning text-dark status-badge">Open</span>
                                @endif
                            </td>
                            <td class="text-end pe-4">
                                @if(!$attempt && $currentTime->lte($quiz->deadline_at))
                                    <a href="{{ route('student.quizzes.attempt', $quiz->id) }}" class="btn btn-primary btn-sm">Attempt</a>
                                @elseif($attempt)
                                    <span class="badge bg-info-subtle text-info">{{ $attempt->score }}/{{ $attempt->total_marks }}</span>
                                @else
                                    <span class="text-muted small">Deadline passed</span>
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
