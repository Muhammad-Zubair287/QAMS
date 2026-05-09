{{-- Admin: Reports — QAMS --}}
@extends('layouts.admin')
@section('title', 'Reports')

@push('styles')
<style>
    .stat-card { border-radius:16px;border:none;background:linear-gradient(135deg,var(--c1),var(--c2));color:#fff;padding:1.5rem; }
    .stat-card .icon-wrap { width:52px;height:52px;border-radius:14px;background:rgba(255,255,255,.2);display:flex;align-items:center;justify-content:center;font-size:1.5rem; }
    .stat-label { font-size:.8rem;opacity:.85;font-weight:600;text-transform:uppercase;letter-spacing:.5px; }
    .stat-value { font-size:2.2rem;font-weight:800;line-height:1; }
    .table th { font-size:.78rem;text-transform:uppercase;letter-spacing:.6px;color:#9ca3af;font-weight:600; }
    .section-head { font-size:1rem;font-weight:700;color:#1e3a5f;margin-bottom:1rem;display:flex;align-items:center;gap:.5rem; }
    @media print { .btn, .sidebar, .topbar { display:none!important; } }
</style>
@endpush

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-1" style="color:#1e3a5f;font-size:1.5rem"><i class="bi bi-bar-chart-fill me-2"></i>Reports</h2>
        <p class="text-muted small mb-0">Overview of all QAMS data.</p>
    </div>
    <button onclick="window.print()" class="btn btn-outline-secondary px-4">
        <i class="bi bi-printer-fill me-2"></i>Print Report
    </button>
</div>

{{-- Summary Cards --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-lg-3">
        <div class="stat-card" style="--c1:#1e3a5f;--c2:#2563eb">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <div class="icon-wrap"><i class="bi bi-mortarboard-fill"></i></div>
            </div>
            <div class="stat-value">{{ $summary['total_students'] }}</div>
            <div class="stat-label mt-1">Students</div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-card" style="--c1:#065f46;--c2:#059669">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <div class="icon-wrap"><i class="bi bi-person-workspace"></i></div>
            </div>
            <div class="stat-value">{{ $summary['total_teachers'] }}</div>
            <div class="stat-label mt-1">Teachers</div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-card" style="--c1:#7c3aed;--c2:#a78bfa">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <div class="icon-wrap"><i class="bi bi-building"></i></div>
            </div>
            <div class="stat-value">{{ $summary['total_classes'] }}</div>
            <div class="stat-label mt-1">Classes</div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-card" style="--c1:#b45309;--c2:#f59e0b">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <div class="icon-wrap"><i class="bi bi-book-fill"></i></div>
            </div>
            <div class="stat-value">{{ $summary['total_subjects'] }}</div>
            <div class="stat-label mt-1">Subjects</div>
        </div>
    </div>
</div>

{{-- Extra stats row --}}
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card qams-card text-center py-3">
            <div class="small text-muted mb-1">Blocked Accounts</div>
            <div class="fw-bold" style="font-size:1.6rem;color:#dc2626">{{ $summary['blocked_users'] }}</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card qams-card text-center py-3">
            <div class="small text-muted mb-1">Students with Class</div>
            <div class="fw-bold" style="font-size:1.6rem;color:#2563eb">{{ $summary['students_with_class'] }}</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card qams-card text-center py-3">
            <div class="small text-muted mb-1">Teachers with Subjects</div>
            <div class="fw-bold" style="font-size:1.6rem;color:#059669">{{ $summary['teachers_with_subjects'] }}</div>
        </div>
    </div>
</div>

{{-- Students Table --}}
<div class="card qams-card mb-4">
    <div class="card-header bg-white border-0 py-3 px-4">
        <h6 class="section-head mb-0"><i class="bi bi-mortarboard-fill text-primary"></i>Students ({{ $students->count() }})</h6>
    </div>
    <div class="card-body p-0">
        @if($students->isEmpty())
        <p class="text-muted text-center py-4 mb-0">No students registered.</p>
        @else
        <div class="table-responsive">
            <table class="table table-sm table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">#</th>
                        <th>Name</th>
                        <th>Admission No.</th>
                        <th>Father</th>
                        <th>Class</th>
                        <th>Subjects</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($students as $i => $student)
                    @php $detail = $student->studentDetail; @endphp
                    <tr>
                        <td class="ps-4 text-muted small">{{ $i + 1 }}</td>
                        <td class="fw-semibold">{{ $student->name }}</td>
                        <td class="small">{{ $detail->admission_number ?? '—' }}</td>
                        <td class="small">{{ $detail->father_name ?? '—' }}</td>
                        <td class="small">{{ $detail && $detail->schoolClass ? $detail->schoolClass->full_name : '—' }}</td>
                        <td><span class="badge bg-info-subtle text-info">{{ $student->enrolledSubjects->count() }}</span></td>
                        <td>
                            @if($student->is_blocked)
                                <span class="badge bg-danger-subtle text-danger">Blocked</span>
                            @else
                                <span class="badge bg-success-subtle text-success">Active</span>
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

{{-- Teachers Table --}}
<div class="card qams-card mb-4">
    <div class="card-header bg-white border-0 py-3 px-4">
        <h6 class="section-head mb-0"><i class="bi bi-person-workspace text-success"></i>Teachers ({{ $teachers->count() }})</h6>
    </div>
    <div class="card-body p-0">
        @if($teachers->isEmpty())
        <p class="text-muted text-center py-4 mb-0">No teachers registered.</p>
        @else
        <div class="table-responsive">
            <table class="table table-sm table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">#</th>
                        <th>Name</th>
                        <th>Username</th>
                        <th>Education</th>
                        <th>Subjects Assigned</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($teachers as $i => $teacher)
                    @php $detail = $teacher->teacherDetail; @endphp
                    <tr>
                        <td class="ps-4 text-muted small">{{ $i + 1 }}</td>
                        <td class="fw-semibold">{{ $teacher->name }}</td>
                        <td class="small text-muted">{{ $teacher->user_name }}</td>
                        <td class="small">{{ $detail ? Str::limit($detail->education, 50) : '—' }}</td>
                        <td>
                            @foreach($teacher->teachingSubjects as $subj)
                                <span style="font-size:.72rem;background:#fdf4ff;color:#7e22ce;border-radius:20px;padding:1px 8px;font-weight:600;margin:1px;display:inline-block">{{ $subj->name }}</span>
                            @endforeach
                            @if($teacher->teachingSubjects->isEmpty()) <span class="text-muted small">None</span> @endif
                        </td>
                        <td>
                            @if($teacher->is_blocked)
                                <span class="badge bg-danger-subtle text-danger">Blocked</span>
                            @else
                                <span class="badge bg-success-subtle text-success">Active</span>
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

{{-- Classes & Subjects Table --}}
<div class="card qams-card mb-4">
    <div class="card-header bg-white border-0 py-3 px-4">
        <h6 class="section-head mb-0"><i class="bi bi-building text-purple" style="color:#7c3aed"></i>Classes &amp; Subjects ({{ $classes->count() }} classes)</h6>
    </div>
    <div class="card-body p-0">
        @if($classes->isEmpty())
        <p class="text-muted text-center py-4 mb-0">No classes created yet.</p>
        @else
        <div class="table-responsive">
            <table class="table table-sm table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">#</th>
                        <th>Class</th>
                        <th>Subjects</th>
                        <th>Students Enrolled</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($classes as $i => $class)
                    <tr>
                        <td class="ps-4 text-muted small">{{ $i + 1 }}</td>
                        <td class="fw-semibold">{{ $class->full_name }}</td>
                        <td>
                            @foreach($class->subjects as $subj)
                                <span style="font-size:.72rem;background:#f0f4ff;color:#3b5bdb;border-radius:20px;padding:1px 8px;font-weight:600;margin:1px;display:inline-block">{{ $subj->name }}</span>
                            @endforeach
                            @if($class->subjects->isEmpty()) <span class="text-muted small">No subjects</span> @endif
                        </td>
                        <td>
                            <span class="badge bg-secondary-subtle text-secondary">{{ $class->studentDetails->count() }} student(s)</span>
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
