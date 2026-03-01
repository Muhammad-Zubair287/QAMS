{{-- Admin: Teachers List — QAMS --}}
@extends('layouts.admin')
@section('title', 'Teachers')

@push('styles')
<style>
    .table th { font-size:.78rem;text-transform:uppercase;letter-spacing:.6px;color:#9ca3af;font-weight:600; }
    .action-btn { width:32px;height:32px;border-radius:8px;border:1px solid #e5e9f0;display:inline-flex;align-items:center;justify-content:center;font-size:.85rem;cursor:pointer;text-decoration:none;transition:all .15s;background:#fff;color:#374151; }
    .action-btn.edit-btn:hover { background:#eff6ff;border-color:#93c5fd;color:#2563eb; }
    .action-btn.delete-btn:hover { background:#fff1f2;border-color:#fca5a5;color:#dc2626; }
    .avatar { width:38px;height:38px;border-radius:50%;object-fit:cover; }
    .avatar-placeholder { width:38px;height:38px;border-radius:50%;background:#e9ecef;display:inline-flex;align-items:center;justify-content:center;font-size:1rem;color:#6c757d; }
    .status-badge { font-size:.72rem;padding:3px 10px;border-radius:20px;font-weight:600; }
    .subject-tag { display:inline-block;background:#fdf4ff;color:#7e22ce;border-radius:20px;padding:1px 8px;font-size:.72rem;font-weight:600;margin:1px; }
</style>
@endpush

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-1" style="color:#1e3a5f;font-size:1.5rem"><i class="bi bi-person-workspace me-2"></i>Teachers</h2>
        <p class="text-muted small mb-0">Manage registered teachers and their subject assignments.</p>
    </div>
    <a href="{{ route('admin.teachers.create') }}" class="btn btn-qams px-4">
        <i class="bi bi-person-plus-fill me-2"></i>Register Teacher
    </a>
</div>

{{-- Search --}}
<div class="card qams-card mb-3">
    <div class="card-body py-2 px-3">
        <form method="GET" action="{{ route('admin.teachers.index') }}" class="d-flex gap-2">
            <input type="text" name="search" value="{{ $search ?? '' }}" class="form-control" placeholder="Search by name or username…">
            <button type="submit" class="btn btn-qams px-4"><i class="bi bi-search"></i></button>
            @if($search)
                <a href="{{ route('admin.teachers.index') }}" class="btn btn-outline-secondary px-3"><i class="bi bi-x-lg"></i></a>
            @endif
        </form>
    </div>
</div>

<div class="card qams-card">
    <div class="card-header bg-white border-0 py-3 px-4">
        <h6 class="fw-bold mb-0" style="color:#1e3a5f">
            <i class="bi bi-table me-2"></i>
            @if($search) Results for "{{ $search }}" @else All Teachers @endif
            <span class="badge bg-secondary ms-2 fw-normal">{{ $teachers->count() }}</span>
        </h6>
    </div>
    <div class="card-body p-0">
        @if($teachers->isEmpty())
        <div class="text-center py-5">
            <i class="bi bi-person-workspace display-2 text-muted"></i>
            <p class="text-muted mt-3">
                @if($search) No teachers found for "{{ $search }}". @else No teachers registered yet. @endif
            </p>
        </div>
        @else
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Teacher</th>
                        <th>Education</th>
                        <th>Assigned Subjects</th>
                        <th>Status</th>
                        <th class="text-center pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($teachers as $teacher)
                    @php $detail = $teacher->teacherDetail; @endphp
                    <tr>
                        <td class="ps-4">
                            <div class="d-flex align-items-center gap-2">
                                @if($detail && $detail->picture)
                                    <img src="{{ Storage::url($detail->picture) }}" alt="" class="avatar">
                                @else
                                    <div class="avatar-placeholder"><i class="bi bi-person-fill"></i></div>
                                @endif
                                <div>
                                    <div class="fw-semibold">{{ $teacher->name }}</div>
                                    <div class="text-muted small">@{{ $teacher->username }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="small text-muted" style="max-width:160px">
                            {{ $detail ? Str::limit($detail->education, 60) : '—' }}
                        </td>
                        <td style="max-width:220px">
                            @php $subjects = $teacher->teachingSubjects; @endphp
                            @if($subjects->count())
                                @foreach($subjects as $subj)
                                    <span class="subject-tag">{{ $subj->name }}</span>
                                @endforeach
                            @else
                                <span class="text-muted small">None assigned</span>
                            @endif
                        </td>
                        <td>
                            @if($teacher->is_blocked)
                                <span class="status-badge bg-danger-subtle text-danger">Blocked</span>
                            @else
                                <span class="status-badge bg-success-subtle text-success">Active</span>
                            @endif
                        </td>
                        <td class="text-center pe-4">
                            <div class="d-flex justify-content-center gap-1">
                                <a href="{{ route('admin.teachers.edit', $teacher->id) }}" class="action-btn edit-btn" title="Edit"><i class="bi bi-pencil-fill"></i></a>
                                <form method="POST" action="{{ route('admin.users.toggle-block', $teacher->id) }}">
                                    @csrf
                                    @php
                                        $blockAction = $teacher->is_blocked ? 'Unblock' : 'Block';
                                        $blockColor  = $teacher->is_blocked ? 'text-success' : 'text-danger';
                                    @endphp
                                    <button class="action-btn {{ $blockColor }}" title="{{ $blockAction }}"
                                        onclick="return confirm('{{ $blockAction }} {{ addslashes($teacher->name) }}?')">
                                        <i class="bi bi-{{ $teacher->is_blocked ? 'unlock' : 'lock' }}-fill"></i>
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('admin.teachers.destroy', $teacher->id) }}"
                                      onsubmit="return confirm('Permanently delete teacher {{ addslashes($teacher->name) }}?')">
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
