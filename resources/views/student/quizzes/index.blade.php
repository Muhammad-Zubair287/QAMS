@extends('layouts.student')
@section('title', 'Quizzes')

@section('content')
<div class="card qams-card">
    <div class="card-header bg-white border-0 py-3">
        <h6 class="mb-0 fw-bold text-primary">Available Quizzes ({{ $quizzes->count() }})</h6>
    </div>
    <div class="card-body p-0">
        @if($quizzes->isEmpty())
            <p class="text-muted text-center py-5 mb-0">No quizzes available for your enrolled subjects.</p>
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
                                    <span class="badge bg-success">Attempted</span>
                                @elseif(now()->gt($quiz->deadline_at))
                                    <span class="badge bg-danger">Closed</span>
                                @else
                                    <span class="badge bg-warning text-dark">Open</span>
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
