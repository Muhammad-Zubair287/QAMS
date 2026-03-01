{{-- Registration Page — QAMS Prototype --}}
@extends('layouts.app')
@section('title', 'Register')

@section('content')
<div class="row justify-content-center mt-4">
    <div class="col-md-6 col-lg-5">

        {{-- ── Header ─────────────────────────────────────────────────── --}}
        <div class="text-center mb-4">
            <i class="bi bi-mortarboard-fill" style="font-size:3.5rem; color:#1e3a5f;"></i>
            <h2 class="fw-bold mt-2" style="color:#1e3a5f;">QAMS</h2>
            <p class="text-muted mb-0">Quiz &amp; Assignment Management System</p>
        </div>

        <div class="card qams-card">
            <div class="card-body p-4">
                <h5 class="fw-semibold text-center mb-4">
                    <i class="bi bi-person-plus-fill me-2" style="color:#1e3a5f;"></i>Create Account
                </h5>

                {{-- ── Registration Form ───────────────────────────────── --}}
                {{-- action: POST /register | @csrf prevents cross-site attacks --}}
                <form method="POST" action="{{ route('register.submit') }}" novalidate>
                    @csrf

                    {{-- Full Name --}}
                    <div class="mb-3">
                        <label for="name" class="form-label fw-semibold">
                            Full Name <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-light">
                                <i class="bi bi-person"></i>
                            </span>
                            <input
                                type="text"
                                id="name"
                                name="name"
                                {{-- old('name') re-fills the field if validation fails --}}
                                value="{{ old('name') }}"
                                maxlength="30"
                                placeholder="Enter your full name"
                                class="form-control @error('name') is-invalid @enderror"
                                required
                                autofocus
                            />
                            {{-- Show error message under the field --}}
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- Username --}}
                    <div class="mb-3">
                        <label for="user_name" class="form-label fw-semibold">
                            Username <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-light">
                                <i class="bi bi-at"></i>
                            </span>
                            <input
                                type="text"
                                id="user_name"
                                name="user_name"
                                value="{{ old('user_name') }}"
                                maxlength="30"
                                placeholder="Choose a unique username"
                                class="form-control @error('user_name') is-invalid @enderror"
                                required
                            />
                            @error('user_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-text">Max 30 characters. Must be unique.</div>
                    </div>

                    {{-- Role --}}
                    <div class="mb-3">
                        <label for="role" class="form-label fw-semibold">
                            Role <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-light">
                                <i class="bi bi-shield-fill"></i>
                            </span>
                            <select
                                id="role"
                                name="role"
                                class="form-select @error('role') is-invalid @enderror"
                                required
                            >
                                <option value="" disabled {{ old('role') ? '' : 'selected' }}>
                                    -- Select Role --
                                </option>
                                {{-- old('role') keeps the selection after validation fails --}}
                                <option value="admin"   {{ old('role') === 'admin'   ? 'selected' : '' }}>Admin</option>
                                <option value="teacher" {{ old('role') === 'teacher' ? 'selected' : '' }}>Teacher</option>
                                <option value="student" {{ old('role') === 'student' ? 'selected' : '' }}>Student</option>
                            </select>
                            @error('role')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- Password --}}
                    <div class="mb-3">
                        <label for="password" class="form-label fw-semibold">
                            Password <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-light">
                                <i class="bi bi-lock-fill"></i>
                            </span>
                            <input
                                type="password"
                                id="password"
                                name="password"
                                placeholder="Minimum 6 characters"
                                class="form-control @error('password') is-invalid @enderror"
                                required
                            />
                            {{-- Toggle password visibility button --}}
                            <button class="btn btn-outline-secondary" type="button" id="togglePwd">
                                <i class="bi bi-eye" id="eyeIcon1"></i>
                            </button>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- Confirm Password --}}
                    <div class="mb-4">
                        <label for="password_confirmation" class="form-label fw-semibold">
                            Confirm Password <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-light">
                                <i class="bi bi-lock"></i>
                            </span>
                            {{-- name must be "password_confirmation" for Laravel's 'confirmed' rule --}}
                            <input
                                type="password"
                                id="password_confirmation"
                                name="password_confirmation"
                                placeholder="Re-enter password"
                                class="form-control"
                                required
                            />
                        </div>
                    </div>

                    {{-- Submit Button --}}
                    <div class="d-grid">
                        <button type="submit" class="btn btn-qams btn-lg">
                            <i class="bi bi-person-check-fill me-2"></i>Create Account
                        </button>
                    </div>
                </form>
                {{-- ── End Form ─────────────────────────────────────────── --}}
            </div>
        </div>

        {{-- Link to login --}}
        <p class="text-center mt-3 text-muted small">
            Already have an account?
            <a href="{{ route('login') }}" class="fw-semibold text-decoration-none">Sign In</a>
        </p>

    </div>
</div>
@endsection

@push('scripts')
<script>
    // Toggle password visibility
    document.getElementById('togglePwd').addEventListener('click', function () {
        const pwd     = document.getElementById('password');
        const icon    = document.getElementById('eyeIcon1');
        const isHidden = pwd.type === 'password';

        pwd.type = isHidden ? 'text' : 'password';
        icon.classList.toggle('bi-eye',       !isHidden);
        icon.classList.toggle('bi-eye-slash',  isHidden);
    });
</script>
@endpush
