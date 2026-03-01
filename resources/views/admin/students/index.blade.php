{{-- Admin: Students List — QAMS --}}
@extends('layouts.admin')
@section('title', 'Students')

@push('styles')
<style>
    .table th { font-size:.78rem;text-transform:uppercase;letter-spacing:.6px;color:#9ca3af;font-weight:600; }
    .action-btn { width:32px;height:32px;border-radius:8px;border:1px solid #e5e9f0;display:inline-flex;align-items:center;justify-content:center;font-size:.85rem;cursor:pointer;text-decoration:none;transition:all .15s;background:#fff;color:#374151; }
    .action-btn.edit-btn:hover { background:#eff6ff;border-color:#93c5fd;color:#2563eb; }
    .action-btn.delete-btn:hover { background:#fff1f2;border-color:#fca5a5;color:#dc2626; }
    .avatar { width:38px;height:38px;border-radius:50%;object-fit:cover; }
    .avatar-placeholder { width:38px;height:38px;border-radius:50%;background:#e9ecef;display:inline-flex;align-items:center;justify-content:center;font-size:1rem;color:#6c757d; }
    .status-badge { font-size:.72rem;padding:3px 10px;border-radius:20px;font-weight:600; }
</style>
@endpush

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-1" style="color:#1e3a5f;font-size:1.5rem"><i class="bi bi-mortarboard-fill me-2"></i>Students</h2>
        <p class="text-muted small mb-0">Manage registered students.</p>
    </div>
    <a href="{{ route('admin.students.create') }}" class="btn btn-qams px-4">
        <i class="bi bi-person-plus-fill me-2"></i>Register Student
    </a>
</div>

{{-- Search --}}
<div class="card qams-card mb-3">
    <div class="card-body py-2 px-3">
        <form method="GET" action="{{ route('admin.students.index') }}" class="d-flex gap-2">
            <input type="text" name="search" value="{{ $search ?? '' }}" class="form-control" placeholder="Search by name or admission number…">
            <button type="submit" class="btn btn-qams px-4"><i class="bi bi-search"></i></button>
            @if($search)
            <a href="{{ route('admin.students.index') }}" class="btn btn-outline-secondary px-3"><i class="bi bi-x-lg"></i></a>
            @endif
        </form>
    </div>
</div>

<div class="card qams-card">
    <div class="card-header bg-white border-0 py-3 px-4">
        <h6 class="fw-bold mb-0" style="color:#1e3a5f">
            <i class="bi bi-table me-2"></i>
            @if($search) Results for "{{ $search }}" @else All Students @endif
            <span class="badge bg-secondary ms-2 fw-normal">{{ $students->count() }}</span>
        </h6>
    </div>
    <div class="card-body p-0">
        @if($students->isEmpty())
        <div class="text-center py-5">
            <i class="bi bi-mortarboard display-2 text-muted"></i>
            <p class="text-muted mt-3">
                @if($search) No students found for "{{ $search }}". @else No students registered yet. @endif
            </p>
        </div>
        @else
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Student</th>
                        <th>Admission No.</th>
                        <th>Father's Name</th>
                        <th>Class</th>
                        <th>Subjects</th>
                        <th>Status</th>
                        <th class="text-center pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($students as $student)
                    @php $detail = $student->studentDetail; @endphp
                    <tr>
                        <td class="ps-4">
                            <div class="d-flex align-items-center gap-2">
                                @if($detail && $detail->picture)
                                    <img src="{{ Storage::url($detail->picture) }}" alt="" class="avatar">
                                @else
                                    <div class="avatar-placeholder"><i class="bi bi-person-fill"></i></div>
                                @endif
                                <div>
                                    <div class="fw-semibold">{{ $student->name }}</div>
                                    <div class="text-muted small">@{{ $student->username }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="text-muted small fw-semibold">{{ $detail->admission_number ?? '—' }}</td>
                        <td class="small">{{ $detail->father_name ?? '—' }}</td>
                        <td>
                            @if($detail && $detail->schoolClass)
                                <span style="font-size:.78rem;background:#f0f9ff;color:#0369a1;border-radius:20px;padding:2px 10px;font-weight:600;">
                                    {{ $detail->schoolClass->full_name }}
                                </span>
                            @else <span class="text-muted small">—</span> @endif
                        </td>
                        <td>
                            @php $subjects = $student->enrolledSubjects; @endphp
                            @if($subjects->count())
                                <span class="badge bg-info-subtle text-info">{{ $subjects->count() }} subj.</span>
                            @else <span class="text-muted small">—</span> @endif
                        </td>
                        <td>
                            @if($student->is_blocked)
                                <span class="status-badge bg-danger-subtle text-danger">Blocked</span>
                            @else
                                <span class="status-badge bg-success-subtle text-success">Active</span>
                            @endif
                        </td>
                        <td class="text-center pe-4">
                            <div class="d-flex justify-content-center gap-1">
                                <a href="{{ route('admin.students.edit', $student->id) }}" class="action-btn edit-btn" title="Edit"><i class="bi bi-pencil-fill"></i></a>
                                <form method="POST" action="{{ route('admin.users.toggle-block', $student->id) }}">
                                    @csrf
                                    @php
                                        $blockAction = $student->is_blocked ? 'Unblock' : 'Block';
                                        $blockColor  = $student->is_blocked ? 'text-success' : 'text-danger';
                                    @endphp
                                    <button class="action-btn {{ $blockColor }}" title="{{ $blockAction }}"
                                        onclick="return confirm('{{ $blockAction }} {{ addslashes($student->name) }}?')">
                                        <i class="bi bi-{{ $student->is_blocked ? 'unlock' : 'lock' }}-fill"></i>
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('admin.students.destroy', $student->id) }}"
                                      onsubmit="return confirm('Permanently delete student {{ addslashes($student->name) }}?')">
                                    @csrf @method('DELETE')
                                    <button class="action-btn delete-btn" title="Delete"><i class="bi bi-trash3-fill"></i></button>
                                </form>
                            </div>
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
