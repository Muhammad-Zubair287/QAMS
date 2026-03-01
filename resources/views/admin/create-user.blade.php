{{-- Admin: Create New User — QAMS --}}
@extends('layouts.admin')
@section('title', 'Add New User')

@push('styles')
<style>
    .form-card { border-radius: 16px; border: none; box-shadow: 0 4px 24px rgba(0,0,0,0.08); }
    .form-label { font-weight: 600; font-size: 0.88rem; color: #374151; }
    .input-group-text { background: #f8fafc; border-color: #e5e9f0; color: #6b7280; }
    .form-control, .form-select {
        border-color: #e5e9f0; font-size: 0.92rem;
        transition: border-color .2s, box-shadow .2s;
    }
    .form-control:focus, .form-select:focus {
        border-color: #4f8ef7; box-shadow: 0 0 0 3px rgba(79,142,247,.12);
    }
    .page-header-title { font-size: 1.4rem; font-weight: 800; color: #1e3a5f; }
    .role-option { border: 2px solid #e5e9f0; border-radius: 10px; padding: 14px; cursor: pointer; transition: all .18s; }
    .role-option:hover { border-color: #4f8ef7; background: #f0f6ff; }
    .role-option input[type=radio]:checked + .role-label { color: #1e3a5f; }
    input[type=radio]:checked ~ * .role-card { border-color: #1e3a5f; background: #eff6ff; }
    .role-card { border: 2px solid #e5e9f0; border-radius: 12px; padding: 16px 12px; cursor: pointer; transition: all .18s; text-align: center; }
    .role-card:hover { border-color: #93c5fd; background: #f0f6ff; }
    .role-card.selected-admin   { border-color: #6f42c1; background: #f5f0ff; }
    .role-card.selected-teacher { border-color: #0d6efd; background: #eff6ff; }
    .role-card.selected-student { border-color: #198754; background: #f0fdf4; }
    .role-icon { font-size: 1.8rem; margin-bottom: 6px; }
    .role-icon-admin   { color: #6f42c1; }
    .role-icon-teacher { color: #0d6efd; }
    .role-icon-student { color: #198754; }
    .password-strength { height: 4px; border-radius: 4px; transition: all .3s; }
</style>
@endpush

@section('content')

{{-- Page Header --}}
<div class="d-flex align-items-center gap-3 mb-4">
    <a href="{{ route('admin.dashboard') }}" class="btn btn-sm btn-outline-secondary" title="Back to Dashboard">
        <i class="bi bi-arrow-left"></i>
    </a>
    <div>
        <h2 class="page-header-title mb-0"><i class="bi bi-person-plus-fill me-2"></i>Add New User</h2>
        <p class="text-muted mb-0 small">Create a new system account for admin, teacher, or student.</p>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-lg-8 col-xl-7">
        <div class="card form-card">
            <div class="card-body p-4 p-lg-5">

                {{-- Form: POST to admin.users.store --}}
                <form method="POST" action="{{ route('admin.users.store') }}" novalidate id="createUserForm">
                    @csrf

                    {{-- ── Full Name ──────────────────────────────── --}}
                    <div class="mb-4">
                        <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-person-fill"></i></span>
                            <input type="text" id="name" name="name"
                                   value="{{ old('name') }}"
                                   maxlength="30"
                                   class="form-control @error('name') is-invalid @enderror"
                                   placeholder="e.g. Ali"
                                   required />
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- ── Username ────────────────────────────────── --}}
                    <div class="mb-4">
                        <label for="user_name" class="form-label">Username <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-at"></i></span>
                            <input type="text" id="user_name" name="user_name"
                                   value="{{ old('user_name') }}"
                                   maxlength="30"
                                   class="form-control @error('user_name') is-invalid @enderror"
                                   placeholder="e.g. MuhamamdAli"
                                   required />
                            @error('user_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <small class="text-muted">Letters, numbers, underscores only. Must be unique.</small>
                    </div>

                    {{-- ── Role (card-style selector) ─────────────── --}}
                    <div class="mb-4">
                        <label class="form-label d-block">Role <span class="text-danger">*</span></label>
                        <div class="row g-3">
                            @foreach(['admin' => ['icon'=>'bi-shield-fill-check','color'=>'#6f42c1','label'=>'Admin','desc'=>'Full system access'],
                                       'teacher' => ['icon'=>'bi-person-workspace','color'=>'#0d6efd','label'=>'Teacher','desc'=>'Manage quizzes & assignments'],
                                       'student' => ['icon'=>'bi-person-video3','color'=>'#198754','label'=>'Student','desc'=>'Take quizzes & submit work']] as $roleVal => $roleData)
                            <div class="col-4">
                                <input type="radio" name="role" id="role_{{ $roleVal }}" value="{{ $roleVal }}"
                                       class="d-none role-radio"
                                       {{ old('role', 'student') === $roleVal ? 'checked' : '' }}>
                                <label for="role_{{ $roleVal }}"
                                       class="role-card w-100 {{ old('role','student') === $roleVal ? 'selected-'.$roleVal : '' }}"
                                       id="role-label-{{ $roleVal }}">
                                    <div class="role-icon role-icon-{{ $roleVal }}">
                                        <i class="bi {{ $roleData['icon'] }}"></i>
                                    </div>
                                    <div class="fw-bold small">{{ $roleData['label'] }}</div>
                                    <div class="text-muted" style="font-size:0.72rem">{{ $roleData['desc'] }}</div>
                                </label>
                            </div>
                            @endforeach
                        </div>
                        @error('role')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- ── Password ─────────────────────────────────── --}}
                    <div class="mb-3">
                        <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                            <input type="password" id="password" name="password"
                                   class="form-control @error('password') is-invalid @enderror"
                                   placeholder="Min. 6 characters"
                                   required />
                            <button type="button" class="btn btn-outline-secondary" id="togglePwd" tabindex="-1">
                                <i class="bi bi-eye-fill" id="togglePwdIcon"></i>
                            </button>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        {{-- Password strength bar --}}
                        <div class="mt-2">
                            <div class="bg-light rounded" style="height:4px">
                                <div class="password-strength rounded" id="strengthBar" style="width:0%;background:#dc3545"></div>
                            </div>
                            <small id="strengthText" class="text-muted"></small>
                        </div>
                    </div>

                    {{-- ── Confirm Password ─────────────────────────── --}}
                    <div class="mb-4">
                        <label for="password_confirmation" class="form-label">Confirm Password <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                            <input type="password" id="password_confirmation" name="password_confirmation"
                                   class="form-control"
                                   placeholder="Repeat the password"
                                   required />
                        </div>
                    </div>

                    {{-- ── Submit / Cancel ──────────────────────────── --}}
                    <div class="d-flex gap-3 pt-2">
                        <button type="submit" class="btn btn-qams px-5 flex-grow-1">
                            <i class="bi bi-person-check-fill me-2"></i>Create User
                        </button>
                        <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary px-4">
                            Cancel
                        </a>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    // ── Role card visual selection ──
    document.querySelectorAll('.role-radio').forEach(radio => {
        radio.addEventListener('change', () => {
            // Remove all selected classes
            document.querySelectorAll('.role-card').forEach(c => {
                c.classList.remove('selected-admin','selected-teacher','selected-student');
            });
            // Add to the clicked one
            const label = document.getElementById('role-label-' + radio.value);
            if (label) label.classList.add('selected-' + radio.value);
        });
    });

    // ── Password toggle ──
    const pwdInput = document.getElementById('password');
    const toggleBtn = document.getElementById('togglePwd');
    const toggleIcon = document.getElementById('togglePwdIcon');

    toggleBtn.addEventListener('click', () => {
        const isText = pwdInput.type === 'text';
        pwdInput.type = isText ? 'password' : 'text';
        toggleIcon.className = isText ? 'bi bi-eye-fill' : 'bi bi-eye-slash-fill';
    });

    // ── Password strength ──
    pwdInput.addEventListener('input', () => {
        const val = pwdInput.value;
        const bar = document.getElementById('strengthBar');
        const txt = document.getElementById('strengthText');

        if (!val) { bar.style.width='0%'; txt.textContent=''; return; }

        let score = 0;
        if (val.length >= 6) score++;
        if (val.length >= 10) score++;
        if (/[A-Z]/.test(val)) score++;
        if (/[0-9]/.test(val)) score++;
        if (/[^A-Za-z0-9]/.test(val)) score++;

        const levels = [
            { w:'20%', color:'#dc3545', label:'Very Weak' },
            { w:'40%', color:'#fd7e14', label:'Weak'      },
            { w:'60%', color:'#ffc107', label:'Fair'      },
            { w:'80%', color:'#20c997', label:'Strong'    },
            { w:'100%',color:'#198754', label:'Very Strong'},
        ];
        const idx = Math.min(score, 4);
        bar.style.width  = levels[idx].w;
        bar.style.background = levels[idx].color;
        txt.textContent  = levels[idx].label;
        txt.style.color  = levels[idx].color;
    });
</script>
@endpush
