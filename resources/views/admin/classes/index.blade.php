{{-- Admin: Classes List — QAMS --}}
@extends('layouts.admin')
@section('title', 'Classes')

@push('styles')
<style>
    .table th { font-size:.78rem; text-transform:uppercase; letter-spacing:.6px; color:#9ca3af; font-weight:600; }
    .action-btn { width:32px;height:32px;border-radius:8px;border:1px solid #e5e9f0;display:inline-flex;align-items:center;justify-content:center;font-size:.85rem;cursor:pointer;text-decoration:none;transition:all .15s;background:#fff;color:#374151; }
    .action-btn.edit-btn:hover   { background:#eff6ff;border-color:#93c5fd;color:#2563eb; }
    .action-btn.delete-btn:hover { background:#fff1f2;border-color:#fca5a5;color:#dc2626; }
    .subject-chip { display:inline-block;background:#f0f4ff;color:#3b5bdb;border-radius:20px;padding:2px 10px;font-size:.75rem;font-weight:600;margin:2px; }
</style>
@endpush

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-1" style="color:#1e3a5f;font-size:1.5rem"><i class="bi bi-building me-2"></i>Classes</h2>
        <p class="text-muted small mb-0">Manage school classes and sections.</p>
    </div>
    <a href="{{ route('admin.classes.create') }}" class="btn btn-qams px-4">
        <i class="bi bi-plus-circle-fill me-2"></i>Add Class
    </a>
</div>

<div class="card qams-card">
    <div class="card-header bg-white border-0 py-3 px-4">
        <h6 class="fw-bold mb-0" style="color:#1e3a5f"><i class="bi bi-table me-2"></i>All Classes
            <span class="badge bg-secondary ms-2 fw-normal">{{ $classes->count() }}</span>
        </h6>
    </div>
    <div class="card-body p-0">
        @if($classes->isEmpty())
        <div class="text-center py-5">
            <i class="bi bi-building display-2 text-muted"></i>
            <p class="text-muted mt-3">No classes yet. <a href="{{ route('admin.classes.create') }}">Add the first one.</a></p>
        </div>
        @else
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4" style="width:50px">#</th>
                        <th>Class Name</th>
                        <th>Section</th>
                        <th>Subjects</th>
                        <th class="text-center pe-4" style="width:100px">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($classes as $i => $class)
                    <tr>
                        <td class="ps-4 text-muted small">{{ $i + 1 }}</td>
                        <td class="fw-semibold">{{ $class->name }}</td>
                        <td>{{ $class->section ?? '—' }}</td>
                        <td>
                            @if($class->subjects_count > 0)
                                <span class="subject-chip"><i class="bi bi-book me-1"></i>{{ $class->subjects_count }} subject(s)</span>
                                <a href="{{ route('admin.subjects.index') }}?class={{ $class->id }}" class="text-muted small ms-1">view</a>
                            @else
                                <span class="text-muted small">No subjects yet</span>
                            @endif
                        </td>
                        <td class="text-center pe-4">
                            <div class="d-flex justify-content-center gap-1">
                                <a href="{{ route('admin.classes.edit', $class->id) }}" class="action-btn edit-btn" title="Edit">
                                    <i class="bi bi-pencil-fill"></i>
                                </a>
                                <form method="POST" action="{{ route('admin.classes.destroy', $class->id) }}" style="display:inline"
                                      onsubmit="return confirm('Delete class {{ addslashes($class->full_name) }} and ALL its subjects?')">
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
