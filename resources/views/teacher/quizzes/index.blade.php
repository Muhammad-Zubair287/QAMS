@extends('layouts.teacher')
@section('title', 'Quizzes')

@section('content')
<div class="card qams-card mb-4">
    <div class="card-header bg-white border-0 py-3">
        <h5 class="fw-bold text-primary mb-0"><i class="bi bi-plus-circle me-2"></i>Conduct New Quiz</h5>
    </div>
    <div class="card-body p-4">
        <form method="POST" action="{{ route('teacher.quizzes.store') }}">
            @csrf
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Subject</label>
                    <select name="subject_id" class="form-select" required>
                        <option value="">Select subject</option>
                        @foreach($subjects as $subject)
                            <option value="{{ $subject->id }}">{{ $subject->name }} ({{ $subject->schoolClass?->full_name ?? 'No class' }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Title</label>
                    <input type="text" name="title" class="form-control" maxlength="120" required>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Duration (mins)</label>
                    <input type="number" min="1" max="300" name="duration_minutes" class="form-control" value="30" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Start At (Optional)</label>
                    <input type="datetime-local" name="start_at" class="form-control">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Deadline</label>
                    <input type="datetime-local" name="deadline_at" class="form-control" required>
                </div>
                <div class="col-12">
                    <label class="form-label">Instructions (Optional)</label>
                    <textarea name="instructions" class="form-control" rows="2" maxlength="3000"></textarea>
                </div>
                <div class="col-12">
                    <button class="btn btn-primary px-4" type="submit">
                        <i class="bi bi-play-circle me-1"></i>Create Quiz
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card qams-card">
    <div class="card-header bg-white border-0 py-3">
        <h6 class="mb-0 fw-bold text-primary">Your Quizzes ({{ $quizzes->count() }})</h6>
    </div>
    <div class="card-body p-0">
        @if($quizzes->isEmpty())
            <p class="text-muted text-center py-5 mb-0">No quizzes created yet.</p>
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Title</th>
                            <th>Subject</th>
                            <th>Duration</th>
                            <th>Deadline</th>
                            <th>Attempts</th>
                            <th class="text-end pe-4">Extend Deadline</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($quizzes as $quiz)
                        <tr>
                            <td class="ps-4 fw-semibold">{{ $quiz->title }}</td>
                            <td>{{ $quiz->subject?->name ?? '—' }}</td>
                            <td>{{ $quiz->duration_minutes }} min</td>
                            <td>{{ $quiz->deadline_at?->format('d M Y h:i A') }}</td>
                            <td><span class="badge bg-info-subtle text-info">{{ $quiz->attempts_count }}</span></td>
                            <td class="pe-4">
                                <form method="POST" action="{{ route('teacher.quizzes.extend-deadline', $quiz->id) }}" class="d-flex gap-2 justify-content-end">
                                    @csrf
                                    @method('PATCH')
                                    <input type="datetime-local" name="deadline_at" class="form-control form-control-sm" required>
                                    <button class="btn btn-outline-primary btn-sm">Update</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
    <div class="card-footer bg-white border-0">
        <div class="small text-muted mb-0">
            Quizzes are restricted to your assigned subjects only.
        </div>
    </div>
</div>
@endsection
