<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>QAMS Student — @yield('title', 'Student Panel')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet" />
    <style>
        :root {
            --sidebar-width: 250px;
            --qams-navy: #1e3a5f;
            --qams-bg: #f0f4f8;
            --qams-card-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            --topbar-height: 64px;
        }
        body { margin: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: var(--qams-bg); }
        #sidebar {
            position: fixed; top: 0; left: 0; width: var(--sidebar-width); height: 100vh;
            background: linear-gradient(180deg, var(--qams-navy), #18324f); color: #fff; z-index: 1040;
            padding-top: var(--topbar-height);
        }
        .sidebar-brand {
            position: fixed; top: 0; left: 0; width: var(--sidebar-width); height: var(--topbar-height);
            display: flex; align-items: center; gap: 10px; padding: 0 18px; color: #fff; text-decoration: none;
            font-weight: 700; border-bottom: 1px solid rgba(255,255,255,.15); background: rgba(0,0,0,.1);
            z-index: 1041;
        }
        .nav-link.student-nav-link { color: rgba(255,255,255,.85); border-radius: 10px; margin: 4px 10px; padding: 10px 14px; font-weight: 600; }
        .nav-link.student-nav-link:hover, .nav-link.student-nav-link.active { color: #fff; background: rgba(79, 142, 247, .35); }
        #main { margin-left: var(--sidebar-width); min-height: 100vh; }
        .topbar {
            height: var(--topbar-height); background: #fff; border-bottom: 1px solid #e5e7eb; display: flex; align-items: center;
            justify-content: space-between; padding: 0 1rem; position: sticky; top: 0; z-index: 1030;
        }
        .content-wrap { padding: 1.25rem; }
        .qams-card { border: none; border-radius: 14px; box-shadow: var(--qams-card-shadow); }
        @media (max-width: 991px) {
            #sidebar, .sidebar-brand { position: static; width: 100%; height: auto; }
            #sidebar { padding-top: 0; }
            #main { margin-left: 0; }
            .topbar { position: static; }
        }
    </style>
    @stack('styles')
</head>
<body>
<a href="{{ route('student.dashboard') }}" class="sidebar-brand">
    <i class="bi bi-mortarboard"></i><span>QAMS Student</span>
</a>

<aside id="sidebar">
    <ul class="nav flex-column py-3">
        <li class="nav-item"><a class="nav-link student-nav-link {{ request()->routeIs('student.dashboard') ? 'active' : '' }}" href="{{ route('student.dashboard') }}"><i class="bi bi-speedometer2 me-2"></i>Dashboard</a></li>
        <li class="nav-item"><a class="nav-link student-nav-link {{ request()->routeIs('student.quizzes.*') ? 'active' : '' }}" href="{{ route('student.quizzes.index') }}"><i class="bi bi-ui-checks-grid me-2"></i>Quizzes</a></li>
        <li class="nav-item"><a class="nav-link student-nav-link {{ request()->routeIs('student.assignments.*') ? 'active' : '' }}" href="{{ route('student.assignments.index') }}"><i class="bi bi-file-earmark-text me-2"></i>Assignments</a></li>
        <li class="nav-item"><a class="nav-link student-nav-link {{ request()->routeIs('student.results.*') ? 'active' : '' }}" href="{{ route('student.results.index') }}"><i class="bi bi-graph-up-arrow me-2"></i>Results & Reports</a></li>
    </ul>
</aside>

<section id="main">
    <div class="topbar">
        <div class="fw-semibold text-primary">@yield('title', 'Student Panel')</div>
        <div class="d-flex align-items-center gap-3">
            <span class="small text-muted"><i class="bi bi-person-circle me-1"></i>{{ auth()->user()->name }}</span>
            <form method="POST" action="{{ route('logout') }}">@csrf
                <button class="btn btn-outline-secondary btn-sm" type="submit"><i class="bi bi-box-arrow-right me-1"></i>Logout</button>
            </form>
        </div>
    </div>
    <div class="content-wrap">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if($errors->has('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ $errors->first('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @yield('content')
    </div>
</section>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>
