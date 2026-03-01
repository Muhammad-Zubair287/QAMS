{{-- Admin: Edit Class — QAMS --}}
@extends('layouts.admin')
@section('title', 'Edit Class')

@section('content')

<div class="d-flex align-items-center gap-3 mb-4">
    <a href="{{ route('admin.classes.index') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left"></i></a>
    <div>
        <h2 class="fw-bold mb-0" style="font-size:1.4rem;color:#1e3a5f"><i class="bi bi-pencil-square me-2"></i>Edit Class</h2>
        <p class="text-muted small mb-0">Editing: <strong>{{ $class->full_name }}</strong></p>
    </div>
</div>

<div class="row g-4 justify-content-center">
    <div class="col-lg-6">
        <div class="card qams-card">
            <div class="card-body p-4">
                <form method="POST" action="{{ route('admin.classes.update', $class->id) }}" novalidate>
                    @csrf @method('PUT')

                    <div class="mb-4">
                        <label for="name" class="form-label fw-semibold">Class Name <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="bi bi-building"></i></span>
                            <input type="text" id="name" name="name" value="{{ old('name', $class->name) }}"
                                   class="form-control @error('name') is-invalid @enderror"
                                   maxlength="50" required />
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="section" class="form-label fw-semibold">Section <span class="text-muted fw-normal">(optional)</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="bi bi-grid-3x3-gap"></i></span>
                            <input type="text" id="section" name="section" value="{{ old('section', $class->section) }}"
                                   class="form-control @error('section') is-invalid @enderror"
                                   maxlength="10" />
                            @error('section')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="d-flex gap-3">
                        <button type="submit" class="btn btn-qams flex-grow-1">
                            <i class="bi bi-save2-fill me-2"></i>Save Changes
                        </button>
                        <a href="{{ route('admin.classes.index') }}" class="btn btn-outline-secondary px-4">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Subjects in this class --}}
    <div class="col-lg-6">
        <div class="card qams-card">
            <div class="card-header bg-white border-0 py-3 px-4">
                <div class="d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold mb-0" style="color:#1e3a5f"><i class="bi bi-book me-2"></i>Subjects in this class</h6>
                    <a href="{{ route('admin.subjects.create') }}" class="btn btn-sm btn-qams">
                        <i class="bi bi-plus-circle me-1"></i>Add Subject
                    </a>
                </div>
            </div>
            <div class="card-body p-0">
                @if($class->subjects->isEmpty())
                <p class="text-muted text-center py-4 mb-0">No subjects yet.</p>
                @else
                <ul class="list-group list-group-flush">
                    @foreach($class->subjects as $subject)
                    <li class="list-group-item d-flex justify-content-between align-items-center px-4">
                        <span><i class="bi bi-book-half me-2 text-primary"></i>{{ $subject->name }}</span>
                        <div class="d-flex gap-1">
                            <a href="{{ route('admin.subjects.edit', $subject->id) }}"
                               class="btn btn-sm btn-outline-primary py-0 px-2">Edit</a>
                            <form method="POST" action="{{ route('admin.subjects.destroy', $subject->id) }}"
                                  onsubmit="return confirm('Delete subject {{ addslashes($subject->name) }}?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger py-0 px-2">Del</button>
                            </form>
                        </div>
                    </li>
                    @endforeach
                </ul>
                @endif
            </div>
        </div>
    </div>
</div>

@endsection
