{{-- Admin: Edit Student — QAMS --}}
@extends('layouts.admin')
@section('title', 'Edit Student')

@push('styles')
<style>
    .section-title { font-size:.8rem;text-transform:uppercase;letter-spacing:.7px;color:#9ca3af;font-weight:700;margin-bottom:1rem;padding-bottom:.5rem;border-bottom:2px solid #f1f5f9; }
    .subject-check-grid { display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:.5rem; }
    .subject-check-label { display:flex;align-items:center;gap:.6rem;padding:.5rem .75rem;border:1px solid #e5e9f0;border-radius:8px;cursor:pointer;font-size:.875rem;transition:all .15s; }
    .subject-check-label:hover { border-color:#93c5fd;background:#f0f6ff; }
    .current-pic { width:120px;height:120px;border-radius:50%;object-fit:cover;border:3px solid #e5e9f0; }
    .picture-box { width:120px;height:120px;border:2px dashed #cbd5e1;border-radius:50%;display:flex;align-items:center;justify-content:center;cursor:pointer;flex-direction:column;gap:4px;color:#9ca3af;font-size:.78rem;transition:all .15s;overflow:hidden; }
    .picture-box:hover { border-color:#93c5fd;background:#f0f6ff; }
</style>
@endpush

@section('content')

<div class="d-flex align-items-center gap-3 mb-4">
    <a href="{{ route('admin.students.index') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left"></i></a>
    <div>
        <h2 class="fw-bold mb-0" style="font-size:1.4rem;color:#1e3a5f"><i class="bi bi-person-gear me-2"></i>Edit Student</h2>
        <p class="text-muted small mb-0">Updating: <strong>{{ $student->name }}</strong></p>
    </div>
</div>

@php $detail = $student->studentDetail; @endphp

<form method="POST" action="{{ route('admin.students.update', $student->id) }}" enctype="multipart/form-data" novalidate>
@csrf @method('PUT')

<div class="row g-4">

    <div class="col-lg-8">

        {{-- Account --}}
        <div class="card qams-card mb-4">
            <div class="card-body p-4">
                <p class="section-title"><i class="bi bi-shield-lock me-2"></i>Account Information</p>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Full Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" value="{{ old('name', $student->name) }}"
                               class="form-control @error('name') is-invalid @enderror" required />
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Username <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light">@</span>
                            <input type="text" name="user_name" value="{{ old('user_name', $student->user_name) }}"
                                   class="form-control @error('user_name') is-invalid @enderror" required />
                        </div>
                        @error('user_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">New Password <span class="text-muted fw-normal">(leave blank to keep)</span></label>
                        <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="Enter to change" />
                        @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Confirm New Password</label>
                        <input type="password" name="password_confirmation" class="form-control" placeholder="Repeat if changing" />
                    </div>
                </div>
            </div>
        </div>

        {{-- Profile --}}
        <div class="card qams-card mb-4">
            <div class="card-body p-4">
                <p class="section-title"><i class="bi bi-person-badge me-2"></i>Student Profile</p>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Admission Number <span class="text-danger">*</span></label>
                        <input type="text" name="admission_number" value="{{ old('admission_number', $detail->admission_number ?? '') }}"
                               class="form-control @error('admission_number') is-invalid @enderror" maxlength="50" required />
                        @error('admission_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Father's Name <span class="text-danger">*</span></label>
                        <input type="text" name="father_name" value="{{ old('father_name', $detail->father_name ?? '') }}"
                               class="form-control @error('father_name') is-invalid @enderror" maxlength="60" required />
                        @error('father_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-12">
                        <label class="form-label fw-semibold">Class</label>
                        <select name="class_id" class="form-select @error('class_id') is-invalid @enderror">
                            <option value="">— No class —</option>
                            @foreach($classes as $class)
                            <option value="{{ $class->id }}" {{ old('class_id', $detail->class_id ?? '') == $class->id ? 'selected' : '' }}>
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
                <p class="section-title"><i class="bi bi-book me-2"></i>Enrolled Subjects</p>
                @if($subjects->isEmpty())
                    <p class="text-muted small">No subjects available yet.</p>
                @else
                <div class="subject-check-grid">
                    @foreach($subjects as $subject)
                    <label class="subject-check-label">
                        <input type="checkbox" name="subjects[]" value="{{ $subject->id }}"
                               {{ in_array($subject->id, old('subjects', $enrolledIds)) ? 'checked' : '' }}
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

    {{-- Picture --}}
    <div class="col-lg-4">
        <div class="card qams-card">
            <div class="card-body p-4 text-center">
                <p class="section-title mb-3"><i class="bi bi-image me-2"></i>Profile Picture</p>
                <div class="d-flex justify-content-center mb-3">
                    <label for="picture" style="cursor:pointer">
                        @if($detail && $detail->picture)
                            <img id="pic-display" src="{{ Storage::url($detail->picture) }}" alt="" class="current-pic" />
                        @else
                            <div class="picture-box" id="picture-box">
                                <i class="bi bi-person-bounding-box" style="font-size:2.5rem;color:#cbd5e1"></i>
                                <span>Click to change</span>
                            </div>
                            <img id="pic-display" src="" alt="" class="current-pic" style="display:none" />
                        @endif
                    </label>
                </div>
                <input type="file" id="picture" name="picture" class="d-none" accept="image/*">
                @error('picture')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                <p class="text-muted small mt-2">Leave blank to keep existing. JPG, PNG — max 2MB</p>
            </div>
        </div>
    </div>

</div>

<div class="d-flex gap-3 mt-4">
    <button type="submit" class="btn btn-qams px-5">
        <i class="bi bi-save2-fill me-2"></i>Save Changes
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
        const display = document.getElementById('pic-display');
        const box = document.getElementById('picture-box');
        display.src = e.target.result;
        display.style.display = 'block';
        if (box) box.style.display = 'none';
    };
    reader.readAsDataURL(file);
});
</script>
@endpush

@endsection
