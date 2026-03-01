{{-- Edit User Page — QAMS Admin --}}
@extends('layouts.admin')
@section('title', 'Edit User')

@section('content')
<div class="d-flex align-items-center gap-3 mb-4">
    <a href="{{ route('admin.dashboard') }}" class="btn btn-sm btn-outline-secondary" title="Back">
        <i class="bi bi-arrow-left"></i>
    </a>
    <div>
        <h2 class="fw-bold mb-0" style="font-size:1.4rem;color:#1e3a5f;">
            <i class="bi bi-pencil-square me-2"></i>Edit User
        </h2>
        <p class="text-muted mb-0 small">Editing: <strong>{{ $user->name }}</strong></p>
    </div>
</div>
<div class="row justify-content-center">
    <div class="col-lg-8 col-xl-7">

        <div class="card qams-card">
            <div class="card-body p-4">

                {{-- ── Edit User Form ───────────────────────────────────── --}}
                {{-- PUT method: HTML forms only support GET/POST, so @method('PUT') spoofs it --}}
                <form method="POST" action="{{ route('admin.users.update', $user->id) }}" novalidate>
                    @csrf
                    @method('PUT')

                    {{-- Full Name --}}
                    <div class="mb-3">
                        <label for="name" class="form-label fw-semibold">Full Name</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="bi bi-person"></i></span>
                            <input
                                type="text"
                                id="name"
                                name="name"
                                value="{{ old('name', $user->name) }}"
                                maxlength="30"
                                class="form-control @error('name') is-invalid @enderror"
                                required
                            />
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- Username --}}
                    <div class="mb-3">
                        <label for="user_name" class="form-label fw-semibold">Username</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="bi bi-at"></i></span>
                            <input
                                type="text"
                                id="user_name"
                                name="user_name"
                                value="{{ old('user_name', $user->user_name) }}"
                                maxlength="30"
                                class="form-control @error('user_name') is-invalid @enderror"
                                required
                            />
                            @error('user_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- Role --}}
                    <div class="mb-3">
                        <label for="role" class="form-label fw-semibold">Role</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="bi bi-shield-fill"></i></span>
                            <select
                                id="role"
                                name="role"
                                class="form-select @error('role') is-invalid @enderror"
                                required
                            >
                                <option value="admin"   {{ old('role', $user->role) === 'admin'   ? 'selected' : '' }}>Admin</option>
                                <option value="teacher" {{ old('role', $user->role) === 'teacher' ? 'selected' : '' }}>Teacher</option>
                                <option value="student" {{ old('role', $user->role) === 'student' ? 'selected' : '' }}>Student</option>
                            </select>
                            @error('role')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- New Password (optional) --}}
                    <div class="mb-3">
                        <label for="password" class="form-label fw-semibold">
                            New Password
                            <small class="text-muted fw-normal">(leave blank to keep current)</small>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="bi bi-lock-fill"></i></span>
                            <input
                                type="password"
                                id="password"
                                name="password"
                                placeholder="Enter new password"
                                class="form-control @error('password') is-invalid @enderror"
                            />
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- Confirm New Password --}}
                    <div class="mb-4">
                        <label for="password_confirmation" class="form-label fw-semibold">Confirm New Password</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="bi bi-lock"></i></span>
                            <input
                                type="password"
                                id="password_confirmation"
                                name="password_confirmation"
                                placeholder="Re-enter new password"
                                class="form-control"
                            />
                        </div>
                    </div>

                    {{-- Action Buttons --}}
                    <div class="d-flex gap-3">
                        <button type="submit" class="btn btn-qams flex-fill">
                            <i class="bi bi-save2-fill me-2"></i>Save Changes
                        </button>
                        <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary flex-fill">
                            <i class="bi bi-x-circle me-2"></i>Cancel
                        </a>
                    </div>

                </form>
            </div>
        </div>

    </div>
</div>
</div>
@endsection
