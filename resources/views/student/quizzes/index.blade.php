@extends('layouts.student')
@section('title', 'Quizzes')

@section('content')
<div class="card qams-card">
    <div class="card-header bg-white border-0 py-3">
        <h6 class="page-title"><i class="bi bi-ui-checks-grid me-2"></i>Available Quizzes ({{ $quizzes->count() }})</h6>
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
                                @elseif(now()->gt($quiz->deadline_at))
                                    <span class="badge bg-danger status-badge">Closed</span>
                                @else
                                    <span class="badge bg-warning text-dark status-badge">Open</span>
                                @endif
                            </td>
                            <td class="text-end pe-4">
                                @if(!$attempt && now()->lte($quiz->deadline_at))
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
