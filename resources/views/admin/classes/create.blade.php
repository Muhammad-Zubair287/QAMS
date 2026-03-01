{{-- Admin: Add Class — QAMS --}}
@extends('layouts.admin')
@section('title', 'Add Class')

@section('content')

<div class="d-flex align-items-center gap-3 mb-4">
    <a href="{{ route('admin.classes.index') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left"></i></a>
    <div>
        <h2 class="fw-bold mb-0" style="font-size:1.4rem;color:#1e3a5f"><i class="bi bi-building me-2"></i>Add New Class</h2>
        <p class="text-muted small mb-0">Create a new school class / section.</p>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="card qams-card">
            <div class="card-body p-4">
                <form method="POST" action="{{ route('admin.classes.store') }}" novalidate>
                    @csrf

                    <div class="mb-4">
                        <label for="name" class="form-label fw-semibold">Class Name <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="bi bi-building"></i></span>
                            <input type="text" id="name" name="name" value="{{ old('name') }}"
                                   class="form-control @error('name') is-invalid @enderror"
                                   placeholder="e.g. Grade 10, Class 8, Primary 5"
                                   maxlength="50" required />
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="section" class="form-label fw-semibold">Section <span class="text-muted fw-normal">(optional)</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="bi bi-grid-3x3-gap"></i></span>
                            <input type="text" id="section" name="section" value="{{ old('section') }}"
                                   class="form-control @error('section') is-invalid @enderror"
                                   placeholder="e.g. A, B, Science, Arts"
                                   maxlength="10" />
                            @error('section')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <small class="text-muted">Leave blank if no section. Example: "Grade 10 — A"</small>
                    </div>

                    <div class="d-flex gap-3">
                        <button type="submit" class="btn btn-qams flex-grow-1">
                            <i class="bi bi-check-circle-fill me-2"></i>Create Class
                        </button>
                        <a href="{{ route('admin.classes.index') }}" class="btn btn-outline-secondary px-4">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection
