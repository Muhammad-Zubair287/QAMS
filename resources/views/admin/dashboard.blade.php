{{-- Admin Dashboard  QAMS --}}
@extends('layouts.admin')
@section('title', 'Dashboard')

@push('styles')
<style>
    .stat-card { border-radius:14px; border:none; box-shadow:0 4px 20px rgba(0,0,0,.07); transition:transform .18s,box-shadow .18s; overflow:hidden; position:relative; }
    .stat-card:hover { transform:translateY(-3px); box-shadow:0 8px 28px rgba(0,0,0,.12); }
    .stat-card .card-body { padding:24px 20px; }
    .stat-card .stat-value { font-size:2rem; font-weight:800; line-height:1; }
    .stat-card .stat-label { font-size:.82rem; color:#6b7280; font-weight:500; margin-top:4px; }
    .stat-card .stat-icon { position:absolute; right:16px; top:16px; font-size:2.2rem; opacity:.12; }
    .stat-total   { border-left:5px solid #1e3a5f; } .stat-total   .stat-value { color:#1e3a5f; }
    .stat-teacher { border-left:5px solid #0d6efd; } .stat-teacher .stat-value { color:#0d6efd; }
    .stat-student { border-left:5px solid #198754; } .stat-student .stat-value { color:#198754; }
    .stat-blocked { border-left:5px solid #dc3545; } .stat-blocked .stat-value { color:#dc3545; }
    .user-avatar { width:40px; height:40px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-weight:700; font-size:15px; color:#fff; flex-shrink:0; }
    .avatar-admin { background:#6f42c1; } .avatar-teacher { background:#0d6efd; } .avatar-student { background:#198754; }
    .users-table th { font-size:.78rem; text-transform:uppercase; letter-spacing:.6px; color:#9ca3af; font-weight:600; }
    .users-table td { vertical-align:middle; }
    .action-btn { width:32px; height:32px; border-radius:8px; border:1px solid #e5e9f0; display:inline-flex; align-items:center; justify-content:center; font-size:.85rem; cursor:pointer; text-decoration:none; transition:all .15s; background:#fff; color:#374151; }
    .action-btn:hover { transform:scale(1.08); }
    .action-btn.edit-btn:hover    { background:#eff6ff; border-color:#93c5fd; color:#2563eb; }
    .action-btn.block-btn:hover   { background:#fff7ed; border-color:#fdba74; color:#ea580c; }
    .action-btn.unblock-btn:hover { background:#f0fdf4; border-color:#86efac; color:#16a34a; }
    .action-btn.delete-btn:hover  { background:#fff1f2; border-color:#fca5a5; color:#dc2626; }
    .filter-tab { padding:6px 16px; border-radius:20px; font-size:.82rem; font-weight:600; text-decoration:none; color:#6b7280; background:#f3f4f6; transition:all .15s; }
    .filter-tab:hover, .filter-tab.active { background:#1e3a5f; color:#fff; }
    .page-header-title { font-size:1.5rem; font-weight:800; color:#1e3a5f; }
</style>
@endpush

@section('content')

{{-- Page Header --}}
<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <div>
        <h2 class="page-header-title mb-1"><i class="bi bi-speedometer2 me-2"></i>Admin Dashboard</h2>
        <p class="text-muted mb-0 small">Welcome back, <strong>{{ auth()->user()->name }}</strong>  manage your system users below.</p>
    </div>
    <a href="{{ route('admin.users.create') }}" class="btn btn-qams px-4">
        <i class="bi bi-person-plus-fill me-2"></i>Add New User
    </a>
</div>

{{-- Stats Row --}}
@php
    $totalUsers    = $allUsers->count();
    $totalTeachers = $allUsers->where('role','teacher')->count();
    $totalStudents = $allUsers->where('role','student')->count();
    $totalBlocked  = $allUsers->where('active','no')->count();
@endphp
<div class="row g-3 mb-4">
    <div class="col-6 col-xl-3">
        <div class="card stat-card stat-total h-100"><div class="card-body">
            <div class="stat-value">{{ $totalUsers }}</div>
            <div class="stat-label"><i class="bi bi-people-fill me-1"></i>Total Users</div>
            <i class="bi bi-people stat-icon"></i>
        </div></div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="card stat-card stat-teacher h-100"><div class="card-body">
            <div class="stat-value">{{ $totalTeachers }}</div>
            <div class="stat-label"><i class="bi bi-person-workspace me-1"></i>Teachers</div>
            <i class="bi bi-person-workspace stat-icon"></i>
        </div></div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="card stat-card stat-student h-100"><div class="card-body">
            <div class="stat-value">{{ $totalStudents }}</div>
            <div class="stat-label"><i class="bi bi-person-video3 me-1"></i>Students</div>
            <i class="bi bi-person-video3 stat-icon"></i>
        </div></div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="card stat-card stat-blocked h-100"><div class="card-body">
            <div class="stat-value">{{ $totalBlocked }}</div>
            <div class="stat-label"><i class="bi bi-person-slash me-1"></i>Blocked</div>
            <i class="bi bi-person-slash stat-icon"></i>
        </div></div>
    </div>
</div>

{{-- Filters + Search --}}
<div class="card qams-card mb-4">
    <div class="card-body p-3">
        <div class="d-flex flex-wrap align-items-center gap-3">
            <div class="d-flex gap-2 flex-wrap">
                <a href="{{ route('admin.dashboard') }}"
                   class="filter-tab {{ !$roleFilter && !$statusFilter ? 'active' : '' }}">All</a>
                <a href="{{ route('admin.dashboard', ['role'=>'teacher']) }}"
                   class="filter-tab {{ $roleFilter==='teacher' ? 'active' : '' }}">
                    <i class="bi bi-person-workspace me-1"></i>Teachers</a>
                <a href="{{ route('admin.dashboard', ['role'=>'student']) }}"
                   class="filter-tab {{ $roleFilter==='student' ? 'active' : '' }}">
                    <i class="bi bi-person-video3 me-1"></i>Students</a>
                <a href="{{ route('admin.dashboard', ['status'=>'blocked']) }}"
                   class="filter-tab {{ $statusFilter==='blocked' ? 'active' : '' }}">
                    <i class="bi bi-person-slash me-1"></i>Blocked</a>
            </div>
            <div class="flex-grow-1"></div>
            <form method="GET" action="{{ route('admin.dashboard') }}" class="d-flex gap-2" style="min-width:260px;max-width:380px;width:100%">
                @if($roleFilter)   <input type="hidden" name="role"   value="{{ $roleFilter }}">   @endif
                @if($statusFilter) <input type="hidden" name="status" value="{{ $statusFilter }}"> @endif
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" name="search" class="form-control border-start-0 ps-0"
                           placeholder="Search by name..." value="{{ $search ?? '' }}" />
                    <button class="btn btn-qams btn-sm px-3" type="submit">Search</button>
                </div>
                @if($search)
                <a href="{{ route('admin.dashboard', array_filter(['role'=>$roleFilter,'status'=>$statusFilter])) }}"
                   class="btn btn-outline-secondary btn-sm px-3" title="Clear"><i class="bi bi-x-lg"></i></a>
                @endif
            </form>
        </div>
        @if($search)
        <p class="text-muted small mb-0 mt-2">
            <i class="bi bi-info-circle me-1"></i>Found <strong>{{ $users->count() }}</strong> result(s) for "<strong>{{ $search }}</strong>"
        </p>
        @endif
    </div>
</div>

{{-- Users Table --}}
<div class="card qams-card">
    <div class="card-header bg-white border-0 py-3 px-4">
        <div class="d-flex align-items-center justify-content-between">
            <h6 class="fw-bold mb-0" style="color:#1e3a5f;">
                <i class="bi bi-table me-2"></i>
                @if($roleFilter==='teacher') Teachers
                @elseif($roleFilter==='student') Students
                @elseif($statusFilter==='blocked') Blocked Users
                @else All Registered Users
                @endif
                <span class="badge bg-secondary ms-2 fw-normal">{{ $users->count() }}</span>
            </h6>
            <small class="text-muted">Showing {{ $users->count() }} of {{ $totalUsers }} total</small>
        </div>
    </div>
    <div class="card-body p-0">
        @if($users->isEmpty())
        <div class="text-center py-5">
            <i class="bi bi-person-x display-2 text-muted"></i>
            <p class="text-muted mt-3 mb-0">
                @if($search) No users found matching "<strong>{{ $search }}</strong>".
                @elseif($roleFilter) No {{ $roleFilter }}s registered yet.
                @elseif($statusFilter==='blocked') No blocked users. Great!
                @else No users registered yet.
                @endif
            </p>
            @if($search||$roleFilter||$statusFilter)
            <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary btn-sm mt-3">
                <i class="bi bi-arrow-left me-1"></i>View All Users
            </a>
            @endif
        </div>
        @else
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 users-table">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4" style="width:50px">#</th>
                        <th>Name</th>
                        <th>Username</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Registered</th>
                        <th class="text-center pe-4" style="width:130px">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $index => $user)
                    @php
                        $avatarClass  = 'avatar-' . $user->role;
                        $isCurrentUser = (int) auth()->id() === (int) $user->id;
                        $blockAction  = $user->active === 'yes' ? 'block' : 'unblock';
                        $blockIcon    = $user->active === 'yes' ? 'bi-shield-slash' : 'bi-shield-check';
                        $blockTitle   = $user->active === 'yes' ? 'Block User' : 'Unblock User';
                    @endphp
                    <tr class="{{ $user->active==='no' ? 'table-danger bg-opacity-10' : '' }}">
                        <td class="ps-4 text-muted small">{{ $index + 1 }}</td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="user-avatar {{ $avatarClass }}">{{ strtoupper(substr($user->name,0,1)) }}</div>
                                <div>
                                    <div class="fw-semibold" style="font-size:.92rem">{{ $user->name }}</div>
                                    @if($isCurrentUser)<small class="text-muted">(You)</small>@endif
                                </div>
                            </div>
                        </td>
                        <td class="text-muted small"><i class="bi bi-at"></i>{{ $user->user_name }}</td>
                        <td>
                            <span class="badge @if($user->role==='admin') badge-admin @elseif($user->role==='teacher') badge-teacher @else badge-student @endif">
                                {{ ucfirst($user->role) }}
                            </span>
                        </td>
                        <td>
                            @if($user->active==='yes')
                                <span class="badge bg-success-subtle text-success border border-success-subtle px-2">
                                    <i class="bi bi-check-circle me-1"></i>Active
                                </span>
                            @else
                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2">
                                    <i class="bi bi-x-circle me-1"></i>Blocked
                                </span>
                            @endif
                        </td>
                        <td class="text-muted small">{{ $user->created_at ? $user->created_at->format('d M Y') : '-' }}</td>
                        <td class="text-center pe-4">
                            <div class="d-flex justify-content-center gap-1">
                                <a href="{{ route('admin.users.edit', $user->id) }}" class="action-btn edit-btn" title="Edit User">
                                    <i class="bi bi-pencil-fill"></i>
                                </a>
                                @if(!$isCurrentUser)
                                <form method="POST" action="{{ route('admin.users.toggle-block', $user->id) }}" style="display:inline"
                                      onsubmit="return confirm('Are you sure you want to {{ $blockAction }} {{ $user->name }}?')">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="action-btn {{ $user->active==='yes' ? 'block-btn' : 'unblock-btn' }}" title="{{ $blockTitle }}">
                                        <i class="bi {{ $blockIcon }}"></i>
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('admin.users.destroy', $user->id) }}" style="display:inline"
                                      onsubmit="return confirm('PERMANENTLY delete {{ $user->name }}? This cannot be undone.')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="action-btn delete-btn" title="Delete User">
                                        <i class="bi bi-trash3-fill"></i>
                                    </button>
                                </form>
                                @else
                                <span class="action-btn" style="opacity:.3;cursor:not-allowed" title="Cannot block yourself"><i class="bi bi-shield-slash"></i></span>
                                <span class="action-btn" style="opacity:.3;cursor:not-allowed" title="Cannot delete yourself"><i class="bi bi-trash3-fill"></i></span>
                                @endif
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
