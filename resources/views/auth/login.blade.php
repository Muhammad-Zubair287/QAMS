{{-- Login Page — QAMS Prototype --}}
@extends('layouts.app')
@section('title', 'Login')

@section('content')
<div class="row justify-content-center mt-5">
    <div class="col-md-5 col-lg-4">

        {{-- ── Header ─────────────────────────────────────────────────── --}}
        <div class="text-center mb-4">
            <i class="bi bi-mortarboard-fill" style="font-size:3.5rem; color:#1e3a5f;"></i>
            <h2 class="fw-bold mt-2" style="color:#1e3a5f;">QAMS</h2>
            <p class="text-muted mb-0">Quiz &amp; Assignment Management System</p>
        </div>

        <div class="card qams-card">
            <div class="card-body p-4">
                <h5 class="fw-semibold text-center mb-4">
                    <i class="bi bi-box-arrow-in-right me-2" style="color:#1e3a5f;"></i>Sign In
                </h5>

                {{-- ── Login Form ───────────────────────────────────────── --}}
                <form method="POST" action="{{ route('login.submit') }}" novalidate>
                    @csrf

                    {{-- Username --}}
                    <div class="mb-3">
                        <label for="user_name" class="form-label fw-semibold">Username</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light">
                                <i class="bi bi-person-fill"></i>
                            </span>
                            <input
                                type="text"
                                id="user_name"
                                name="user_name"
                                value="{{ old('user_name') }}"
                                placeholder="Enter your username"
                                maxlength="30"
                                class="form-control @error('user_name') is-invalid @enderror"
                                required
                                autofocus
                            />
                            @error('user_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- Password --}}
                    <div class="mb-4">
                        <label for="password" class="form-label fw-semibold">Password</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light">
                                <i class="bi bi-lock-fill"></i>
                            </span>
                            <input
                                type="password"
                                id="password"
                                name="password"
                                placeholder="Enter your password"
                                class="form-control @error('password') is-invalid @enderror"
                                required
                            />
                            <button class="btn btn-outline-secondary" type="button" id="togglePwd">
                                <i class="bi bi-eye" id="eyeIcon"></i>
                            </button>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- Submit --}}
                    <div class="d-grid">
                        <button type="submit" class="btn btn-qams btn-lg">
                            <i class="bi bi-box-arrow-in-right me-2"></i>Login
                        </button>
                    </div>
                </form>
                {{-- ── End Form ─────────────────────────────────────────── --}}
            </div>
        </div>

        <p class="text-center mt-3 text-muted small">
            Don't have an account?
            <a href="{{ route('register') }}" class="fw-semibold text-decoration-none">Register here</a>
        </p>

    </div>
</div>
@endsection

@push('scripts')
<script>
    document.getElementById('togglePwd').addEventListener('click', function () {
        const pwd  = document.getElementById('password');
        const icon = document.getElementById('eyeIcon');
        const isHidden = pwd.type === 'password';
        pwd.type = isHidden ? 'text' : 'password';
        icon.classList.toggle('bi-eye',      !isHidden);
        icon.classList.toggle('bi-eye-slash', isHidden);
    });
</script>
@endpush
