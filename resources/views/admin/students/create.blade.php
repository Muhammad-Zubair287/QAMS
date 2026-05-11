{{-- Admin: Register Student — QAMS --}}
@php
/** @var \Illuminate\Support\Collection|\App\Models\Subject[] $subjects */
/** @var \Illuminate\Support\Collection|\App\Models\SchoolClass[] $classes */
@endphp
@extends('layouts.admin')
@section('title', 'Register Student')

@push('styles')
<style>
    .section-title { font-size:.8rem;text-transform:uppercase;letter-spacing:.7px;color:#9ca3af;font-weight:700;margin-bottom:1rem;padding-bottom:.5rem;border-bottom:2px solid #f1f5f9; }
    .subject-check-grid { display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:.5rem; }
    .subject-check-label { display:flex;align-items:center;gap:.6rem;padding:.5rem .75rem;border:1px solid #e5e9f0;border-radius:8px;cursor:pointer;font-size:.875rem;transition:all .15s; }
    .subject-check-label:hover { border-color:#93c5fd;background:#f0f6ff; }
    .subject-check-label input:checked ~ span { color:#1d4ed8;font-weight:600; }
    #picture-preview { max-width:140px;max-height:140px;border-radius:50%;object-fit:cover;border:3px solid #e5e9f0; }
    .picture-box { width:140px;height:140px;border:2px dashed #cbd5e1;border-radius:50%;display:flex;align-items:center;justify-content:center;cursor:pointer;flex-direction:column;gap:4px;color:#9ca3af;font-size:.78rem;transition:all .15s;overflow:hidden; }
    .picture-box:hover { border-color:#93c5fd;background:#f0f6ff; }
</style>
@endpush

@section('content')

<div class="d-flex align-items-center gap-3 mb-4">
    <a href="{{ route('admin.students.index') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left"></i></a>
    <div>
        <h2 class="fw-bold mb-0" style="font-size:1.4rem;color:#1e3a5f"><i class="bi bi-person-plus-fill me-2"></i>Register Student</h2>
        <p class="text-muted small mb-0">Create a new student account and profile.</p>
    </div>
</div>

<form method="POST" action="{{ route('admin.students.store') }}" enctype="multipart/form-data" novalidate>
@csrf

<div class="row g-4">

    {{-- Left column --}}
    <div class="col-lg-8">

        {{-- Account Info --}}
        <div class="card qams-card mb-4">
            <div class="card-body p-4">
                <p class="section-title"><i class="bi bi-shield-lock me-2"></i>Account Information</p>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Full Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" value="{{ old('name') }}"
                               class="form-control @error('name') is-invalid @enderror"
                               placeholder="e.g. Zain Ul Abidin" required />
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Username <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light">@</span>
                            <input type="text" name="user_name" value="{{ old('user_name') }}"
                                   class="form-control @error('user_name') is-invalid @enderror"
                                   placeholder="unique login username" required />
                        </div>
                        @error('user_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Password <span class="text-danger">*</span></label>
                        <input type="password" name="password"
                               class="form-control @error('password') is-invalid @enderror"
                               placeholder="Min 6 characters" required />
                        @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Confirm Password <span class="text-danger">*</span></label>
                        <input type="password" name="password_confirmation" class="form-control" placeholder="Repeat password" required />
                    </div>
                </div>
            </div>
        </div>

        {{-- Student Profile --}}
        <div class="card qams-card mb-4">
            <div class="card-body p-4">
                <p class="section-title"><i class="bi bi-person-badge me-2"></i>Student Profile</p>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Admission Number <span class="text-danger">*</span></label>
                        <input type="text" name="admission_number" value="{{ old('admission_number') }}"
                               class="form-control @error('admission_number') is-invalid @enderror"
                               placeholder="e.g. 2024-001" maxlength="50" required />
                        @error('admission_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Father's Name <span class="text-danger">*</span></label>
                        <input type="text" name="father_name" value="{{ old('father_name') }}"
                               class="form-control @error('father_name') is-invalid @enderror"
                               placeholder="e.g. Saaed Ahmed" maxlength="60" required />
                        @error('father_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-12">
                        <label class="form-label fw-semibold">Class</label>
                        <select name="class_id" id="class_id" class="form-select @error('class_id') is-invalid @enderror" required>
                            <option value="">— Assign to class —</option>
                            @foreach($classes as $class)
                            <option value="{{ $class->id }}" {{ old('class_id') == $class->id ? 'selected' : '' }}>
                                {{ $class->full_name }}
                            </option>
                            @endforeach
                        </select>
                        @error('class_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>
        </div>

        {{-- Subjects --}}
        <div class="card qams-card">
            <div class="card-body p-4">
                <p class="section-title"><i class="bi bi-book me-2"></i>Enroll in Subjects</p>
                @if($subjects->isEmpty())
                    <p class="text-muted small">No subjects available yet.</p>
                @else
                <div class="subject-check-grid">
                    @foreach($subjects as $subject)
                    <label class="subject-check-label">
                        <input type="checkbox" name="subjects[]" value="{{ $subject->id }}"
                               {{ in_array($subject->id, old('subjects', [])) ? 'checked' : '' }}
                               class="form-check-input m-0" />
                        <span>
                            {{ $subject->name }}
                            @if($subject->schoolClass)
                            <br><small class="text-muted">{{ $subject->schoolClass->full_name }}</small>
                            @endif
                        </span>
                    </label>
                    @endforeach
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Right column: picture --}}
    <div class="col-lg-4">
        <div class="card qams-card">
            <div class="card-body p-4 text-center">
                <p class="section-title mb-3"><i class="bi bi-image me-2"></i>Profile Picture</p>
                <div class="d-flex justify-content-center mb-3">
                    <label for="picture" style="cursor:pointer">
                        <div class="picture-box" id="picture-box">
                            <i class="bi bi-person-bounding-box" style="font-size:2.5rem;color:#cbd5e1"></i>
                            <span>Click to upload</span>
                        </div>
                        <img id="picture-preview" src="" alt="" style="display:none" />
                    </label>
                </div>
                <input type="file" id="picture" name="picture" class="d-none" accept="image/*">
                @error('picture')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                <p class="text-muted small mt-2">JPG, PNG, GIF — max 2MB</p>
            </div>
        </div>
    </div>

</div>

<div class="d-flex gap-3 mt-4">
    <button type="submit" class="btn btn-qams px-5">
        <i class="bi bi-check-circle-fill me-2"></i>Register Student
    </button>
    <a href="{{ route('admin.students.index') }}" class="btn btn-outline-secondary px-4">Cancel</a>
</div>

</form>

@push('scripts')
<script>
document.getElementById('picture').addEventListener('change', function () {
    const file = this.files[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = (e) => {
        const preview = document.getElementById('picture-preview');
        const box = document.getElementById('picture-box');
        preview.src = e.target.result;
        preview.style.display = 'block';
        box.style.display = 'none';
    };
    reader.readAsDataURL(file);
});
</script>
@endpush

@endsection
