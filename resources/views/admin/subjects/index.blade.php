{{-- Admin: Subjects List — QAMS --}}
@extends('layouts.admin')
@section('title', 'Subjects')

@push('styles')
<style>
    .table th { font-size:.78rem;text-transform:uppercase;letter-spacing:.6px;color:#9ca3af;font-weight:600; }
    .action-btn { width:32px;height:32px;border-radius:8px;border:1px solid #e5e9f0;display:inline-flex;align-items:center;justify-content:center;font-size:.85rem;cursor:pointer;text-decoration:none;transition:all .15s;background:#fff;color:#374151; }
    .action-btn.edit-btn:hover   { background:#eff6ff;border-color:#93c5fd;color:#2563eb; }
    .action-btn.delete-btn:hover { background:#fff1f2;border-color:#fca5a5;color:#dc2626; }
    .class-chip { display:inline-block;background:#f0f9ff;color:#0369a1;border-radius:20px;padding:2px 10px;font-size:.75rem;font-weight:600; }
</style>
@endpush

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-1" style="color:#1e3a5f;font-size:1.5rem"><i class="bi bi-book-fill me-2"></i>Subjects</h2>
        <p class="text-muted small mb-0">Manage subjects assigned to each class.</p>
    </div>
    <a href="{{ route('admin.subjects.create') }}" class="btn btn-qams px-4">
        <i class="bi bi-plus-circle-fill me-2"></i>Add Subject
    </a>
</div>

<div class="card qams-card">
    <div class="card-header bg-white border-0 py-3 px-4">
        <h6 class="fw-bold mb-0" style="color:#1e3a5f">
            <i class="bi bi-table me-2"></i>All Subjects
            <span class="badge bg-secondary ms-2 fw-normal">{{ $subjects->count() }}</span>
        </h6>
    </div>
    <div class="card-body p-0">
        @if($subjects->isEmpty())
        <div class="text-center py-5">
            <i class="bi bi-book display-2 text-muted"></i>
            <p class="text-muted mt-3">No subjects yet.
                @if(\App\Models\SchoolClass::count() === 0)
                    <a href="{{ route('admin.classes.create') }}">Create a class first</a>, then add subjects.
                @else
                    <a href="{{ route('admin.subjects.create') }}">Add the first subject.</a>
                @endif
            </p>
        </div>
        @else
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4" style="width:50px">#</th>
                        <th>Subject Name</th>
                        <th>Class</th>
                        <th>Teachers</th>
                        <th>Students</th>
                        <th class="text-center pe-4" style="width:100px">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($subjects as $i => $subject)
                    <tr>
                        <td class="ps-4 text-muted small">{{ $i + 1 }}</td>
                        <td class="fw-semibold"><i class="bi bi-book-half me-2 text-primary"></i>{{ $subject->name }}</td>
                        <td>
                            @if($subject->schoolClass)
                                <span class="class-chip">{{ $subject->schoolClass->full_name }}</span>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-primary-subtle text-primary">{{ $subject->teachers_count }} teacher(s)</span>
                        </td>
                        <td>
                            <span class="badge bg-success-subtle text-success">{{ $subject->students_count }} student(s)</span>
                        </td>
                        <td class="text-center pe-4">
                            <div class="d-flex justify-content-center gap-1">
                                <a href="{{ route('admin.subjects.edit', $subject->id) }}" class="action-btn edit-btn" title="Edit">
                                    <i class="bi bi-pencil-fill"></i>
                                </a>
                                <form method="POST" action="{{ route('admin.subjects.destroy', $subject->id) }}" style="display:inline"
                                      onsubmit="return confirm('Delete subject {{ addslashes($subject->name) }}?')">
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
