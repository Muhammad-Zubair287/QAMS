{{-- Admin: Edit Subject — QAMS --}}
@php
/** @var \App\Models\Subject $subject */
/** @var \Illuminate\Support\Collection|\App\Models\Classes[] $classes */
@endphp
@extends('layouts.admin')
@section('title', 'Edit Subject')

@section('content')

<div class="d-flex align-items-center gap-3 mb-4">
    <a href="{{ route('admin.subjects.index') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left"></i></a>
    <div>
        <h2 class="fw-bold mb-0" style="font-size:1.4rem;color:#1e3a5f"><i class="bi bi-pencil-square me-2"></i>Edit Subject</h2>
        <p class="text-muted small mb-0">Editing: <strong>{{ $subject->name }}</strong></p>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="card qams-card">
            <div class="card-body p-4">
                <form method="POST" action="{{ route('admin.subjects.update', $subject->id) }}" novalidate>
                    @csrf @method('PUT')

                    <div class="mb-4">
                        <label for="name" class="form-label fw-semibold">Subject Name <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="bi bi-book-half"></i></span>
                            <input type="text" id="name" name="name" value="{{ old('name', $subject->name) }}"
                                   class="form-control @error('name') is-invalid @enderror"
                                   maxlength="100" required />
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="class_id" class="form-label fw-semibold">Class <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="bi bi-building"></i></span>
                            <select id="class_id" name="class_id"
                                    class="form-select @error('class_id') is-invalid @enderror" required>
                                <option value="">— Select class —</option>
                                @foreach($classes as $class)
                                <option value="{{ $class->id }}" {{ old('class_id', $subject->class_id) == $class->id ? 'selected' : '' }}>
                                    {{ $class->full_name }}
                                </option>
                                @endforeach
                            </select>
                            @error('class_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="d-flex gap-3">
                        <button type="submit" class="btn btn-qams flex-grow-1">
                            <i class="bi bi-save2-fill me-2"></i>Save Changes
                        </button>
                        <a href="{{ route('admin.subjects.index') }}" class="btn btn-outline-secondary px-4">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection
