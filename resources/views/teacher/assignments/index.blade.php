@extends('layouts.teacher')
@section('title', 'Assignments')

@section('content')
<div class="card qams-card mb-4">
    <div class="card-header bg-white border-0 py-3">
        <h5 class="fw-bold text-primary mb-0"><i class="bi bi-plus-circle me-2"></i>Upload Assignment</h5>
    </div>
    <div class="card-body p-4">
        <form method="POST" action="{{ route('teacher.assignments.store') }}">
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
                <div class="col-md-4">
                    <label class="form-label">Deadline</label>
                    <input type="datetime-local" name="deadline_at" class="form-control" required>
                </div>
                <div class="col-12">
                    <label class="form-label">Description</label>
                    <textarea name="description" rows="3" class="form-control" maxlength="4000"></textarea>
                </div>
                <div class="col-12">
                    <button class="btn btn-primary px-4" type="submit">
                        <i class="bi bi-upload me-1"></i>Save Assignment
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card qams-card">
    <div class="card-header bg-white border-0 py-3">
        <h6 class="mb-0 fw-bold text-primary">Your Assignments ({{ $assignments->count() }})</h6>
    </div>
    <div class="card-body p-0">
        @if($assignments->isEmpty())
            <p class="text-muted text-center py-5 mb-0">No assignments uploaded yet.</p>
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                    <tr>
                        <th class="ps-4">Title</th>
                        <th>Subject</th>
                        <th>Deadline</th>
                        <th>Submissions</th>
                        <th class="text-end pe-4">Extend Deadline</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($assignments as $assignment)
                        <tr>
                            <td class="ps-4 fw-semibold">{{ $assignment->title }}</td>
                            <td>{{ $assignment->subject?->name ?? '—' }}</td>
                            <td>{{ $assignment->deadline_at?->format('d M Y h:i A') }}</td>
                            <td><span class="badge bg-info-subtle text-info">{{ $assignment->submissions_count }}</span></td>
                            <td class="pe-4">
                                <form method="POST" action="{{ route('teacher.assignments.extend-deadline', $assignment->id) }}" class="d-flex gap-2 justify-content-end">
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
</div>
@endsection
