@extends('layouts.student')
@section('title', 'Assignments')

@section('content')
<div class="row g-3 mb-4">
    <div class="col-md-4 col-lg-3">
        <a href="{{ route('student.assignments.index') }}" class="text-decoration-none">
            <div class="card qams-card h-100 {{ $selectedSubjectId === 0 ? 'border border-primary' : '' }}">
                <div class="card-body">
                    <div class="small text-muted">All Subjects</div>
                    <div class="fw-bold text-primary">All Assignments</div>
                </div>
            </div>
        </a>
    </div>
    @foreach($enrolledSubjects as $subject)
        <div class="col-md-4 col-lg-3">
            <a href="{{ route('student.assignments.index', ['subject_id' => $subject['id']]) }}" class="text-decoration-none">
                <div class="card qams-card h-100 {{ $selectedSubjectId === $subject['id'] ? 'border border-primary' : '' }}">
                    <div class="card-body">
                        <div class="small text-muted">{{ $subject['assignment_count'] }} Assignment(s)</div>
                        <div class="fw-bold text-primary">{{ $subject['name'] }}</div>
                        <div class="small text-success">{{ $subject['submitted_count'] }} submitted</div>
                    </div>
                </div>
            </a>
        </div>
    @endforeach
</div>

<div class="card qams-card">
    <div class="card-header bg-white border-0 py-3">
        <h6 class="page-title">
            <i class="bi bi-file-earmark-text me-2"></i>
            @if($selectedSubjectId === 0)
                Assignments ({{ $assignments->count() }})
            @else
                Subject Assignments ({{ $assignments->count() }})
            @endif
        </h6>
    </div>
    <div class="card-body">
        @if($assignments->isEmpty())
            <div class="empty-state">
                <div class="icon"><i class="bi bi-journal-x"></i></div>
                <div>No assignments available for your enrolled subjects.</div>
            </div>
        @else
            @foreach($assignments as $assignment)
                @php $submission = $assignment->submissions->first(); @endphp
                <div class="border rounded-3 p-3 mb-3">
                    <div class="d-flex flex-column flex-lg-row justify-content-between gap-2">
                        <div>
                            <div class="fw-semibold">{{ $assignment->title }}</div>
                            <div class="small text-muted">Subject: {{ $assignment->subject?->name ?? '—' }} | Teacher: {{ $assignment->teacher?->name ?? '—' }}</div>
                            <div class="small text-muted">Deadline: {{ $assignment->deadline_at?->format('d M Y h:i A') ?? '—' }}</div>
                            @if(!empty($assignment->description))
                                <p class="small mt-2 mb-0">{{ $assignment->description }}</p>
                            @endif
                        </div>
                        <div>
                            @if($submission)
                                <span class="badge bg-success status-badge">Submitted</span>
                            @elseif($currentTime->gt($assignment->deadline_at))
                                <span class="badge bg-danger status-badge">Missed Deadline</span>
                            @else
                                <span class="badge bg-warning text-dark status-badge">Pending</span>
                            @endif
                        </div>
                    </div>

                    @if(!$submission && $currentTime->lte($assignment->deadline_at))
                        <form method="POST" action="{{ route('student.assignments.submit', $assignment->id) }}" enctype="multipart/form-data" class="mt-3">
                            @csrf
                            <div class="row g-2">
                                <div class="col-lg-6">
                                    <label class="form-label small">Solution Text (Optional)</label>
                                    <textarea name="solution_text" rows="2" class="form-control" maxlength="5000"></textarea>
                                </div>
                                <div class="col-lg-4">
                                    <label class="form-label small">Upload File (Optional)</label>
                                    <input type="file" name="solution_file" class="form-control" accept=".pdf,.doc,.docx,.txt,.png,.jpg,.jpeg">
                                </div>
                                <div class="col-lg-2 d-flex align-items-end">
                                    <button class="btn btn-primary w-100" type="submit">Submit</button>
                                </div>
                            </div>
                        </form>
                    @elseif($submission)
                        <div class="small text-success mt-2">Submitted at: {{ $submission->submitted_at?->format('d M Y h:i A') ?? '—' }}</div>
                    @endif
                </div>
            @endforeach
        @endif
    </div>
</div>
@endsection
