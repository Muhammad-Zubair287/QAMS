{{-- Teacher Dashboard — QAMS --}}
@extends('layouts.teacher')
@section('title', 'Teacher Dashboard')

@push('styles')
<style>
    .teacher-hero {
        border: none;
        border-radius: 16px;
        color: #fff;
        background: linear-gradient(135deg, #1e3a5f, #2c5282);
        box-shadow: 0 10px 30px rgba(30, 58, 95, 0.25);
    }
    .stat-card {
        border: none;
        border-radius: 14px;
        box-shadow: var(--qams-card-shadow);
    }
    .stat-label {
        font-size: 0.78rem;
        text-transform: uppercase;
        letter-spacing: 0.4px;
        color: #6b7280;
        margin-bottom: 0.25rem;
    }
    .stat-value {
        font-size: 1.7rem;
        font-weight: 800;
        line-height: 1;
    }
    .subject-chip {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 10px;
        border-radius: 999px;
        font-size: 0.75rem;
        background: #eef2ff;
        color: #1e40af;
        font-weight: 600;
    }
</style>
@endpush

@section('content')
<div class="card teacher-hero mb-4">
    <div class="card-body p-4 p-lg-5">
        <div class="d-flex flex-column flex-lg-row justify-content-between gap-3">
            <div>
                <h2 class="fw-bold mb-2">
                    <i class="bi bi-person-workspace me-2"></i>
                    Welcome, {{ $teacher->name }}
                </h2>
                <p class="mb-2 text-white-50">
                    Manage your assigned subjects, monitor student coverage, and prepare quiz and assignment workflows.
                </p>
                <span class="badge bg-light text-dark">
                    <i class="bi bi-person-badge me-1"></i>{{ $teacher->user_name }}
                </span>
            </div>
            <div class="text-lg-end">
                <div class="small text-white-50">Account Status</div>
                @if($teacher->is_blocked)
                    <span class="badge bg-danger px-3 py-2">Blocked</span>
                @else
                    <span class="badge bg-success px-3 py-2">Active</span>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-6 col-lg-3">
        <div class="card stat-card h-100">
            <div class="card-body">
                <div class="stat-label">Assigned Subjects</div>
                <div class="stat-value text-primary">{{ $summary['assigned_subjects'] }}</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card stat-card h-100">
            <div class="card-body">
                <div class="stat-label">Covered Classes</div>
                <div class="stat-value text-success">{{ $summary['covered_classes'] }}</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card stat-card h-100">
            <div class="card-body">
                <div class="stat-label">Enrolled Students</div>
                <div class="stat-value" style="color:#7c3aed">{{ $summary['enrolled_students'] }}</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card stat-card h-100">
            <div class="card-body">
                <div class="stat-label">Profile Completion</div>
                <div class="stat-value text-warning">{{ $summary['profile_completion'] }}%</div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card qams-card h-100">
            <div class="card-header bg-white border-0 py-3">
                <h5 class="mb-0 fw-bold text-primary">
                    <i class="bi bi-book-half me-2"></i>Assigned Subjects
                </h5>
            </div>
            <div class="card-body p-0">
                @if($subjects->isEmpty())
                    <p class="text-muted text-center py-5 mb-0">No subjects assigned yet. Please ask admin to assign subjects.</p>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                            <tr>
                                <th class="ps-4">#</th>
                                <th>Subject</th>
                                <th>Class</th>
                                <th>Students</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($subjects as $index => $subject)
                                <tr>
                                    <td class="ps-4 text-muted">{{ $index + 1 }}</td>
                                    <td class="fw-semibold">{{ $subject->name }}</td>
                                    <td>{{ $subject->schoolClass?->full_name ?? '—' }}</td>
                                    <td>
                                        <span class="badge bg-info-subtle text-info">{{ $subject->students_count }}</span>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card qams-card mb-4">
            <div class="card-header bg-white border-0 py-3">
                <h6 class="mb-0 fw-bold text-primary"><i class="bi bi-info-circle me-2"></i>Teaching Profile</h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <small class="text-muted d-block mb-1">Education</small>
                    <div class="fw-semibold">{{ $teacher->teacherDetail?->education ?: 'Not added yet' }}</div>
                </div>
                <div class="mb-3">
                    <small class="text-muted d-block mb-1">Job History</small>
                    <div class="fw-semibold">{{ $teacher->teacherDetail?->job_history ?: 'Not added yet' }}</div>
                </div>
                <div>
                    <small class="text-muted d-block mb-2">Classes You Teach</small>
                    @php $classNames = $subjects->map(fn($s) => $s->schoolClass?->full_name)->filter()->unique(); @endphp
                    @forelse($classNames as $className)
                        <span class="subject-chip mb-1">{{ $className }}</span>
                    @empty
                        <div class="text-muted small">No class mapping yet.</div>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="card qams-card">
            <div class="card-header bg-white border-0 py-3">
                <h6 class="mb-0 fw-bold text-primary"><i class="bi bi-link-45deg me-2"></i>Teacher Modules</h6>
            </div>
            <div class="card-body">
                <ul class="list-unstyled mb-0 small">
                    <li class="mb-2">
                        <a class="text-decoration-none" href="{{ route('teacher.question-bank.index') }}">
                            <i class="bi bi-journal-text text-primary me-2"></i>Question Bank
                        </a>
                    </li>
                    <li class="mb-2">
                        <a class="text-decoration-none" href="{{ route('teacher.quizzes.index') }}">
                            <i class="bi bi-ui-checks-grid text-primary me-2"></i>Quizzes
                        </a>
                    </li>
                    <li class="mb-2">
                        <a class="text-decoration-none" href="{{ route('teacher.assignments.index') }}">
                            <i class="bi bi-file-earmark-text text-primary me-2"></i>Assignments
                        </a>
                    </li>
                    <li class="mb-2">
                        <a class="text-decoration-none" href="{{ route('teacher.results.index') }}">
                            <i class="bi bi-check2-square text-primary me-2"></i>Results Publish
                        </a>
                    </li>
                    <li>
                        <a class="text-decoration-none" href="{{ route('teacher.performance.index') }}">
                            <i class="bi bi-graph-up-arrow text-primary me-2"></i>Performance Reports
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
