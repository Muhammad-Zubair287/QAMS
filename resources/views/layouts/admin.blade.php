<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />

    <title>QAMS Admin — @yield('title', 'Admin Panel')</title>

    {{-- Bootstrap 5 CSS --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
    {{-- Bootstrap Icons --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet" />

    <style>
        /* ══════════════════════════════════════════════════════
           QAMS Admin Panel — Global Styles
           ══════════════════════════════════════════════════════ */
        :root {
            --sidebar-width:      260px;
            --sidebar-collapsed:  70px;
            --qams-navy:          #1e3a5f;
            --qams-navy-dark:     #152d4a;
            --qams-navy-light:    #2c5282;
            --qams-accent:        #4f8ef7;
            --qams-bg:            #f0f4f8;
            --qams-card-shadow:   0 4px 20px rgba(0,0,0,0.08);
            --sidebar-text:       rgba(255,255,255,0.82);
            --sidebar-text-hover: #ffffff;
            --topbar-height:      64px;
        }

        * { box-sizing: border-box; }

        body {
            background-color: var(--qams-bg);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            overflow-x: hidden;
        }

        /* ── Sidebar ──────────────────────────────────────────── */
        #sidebar {
            position: fixed;
            top: 0; left: 0;
            height: 100vh;
            width: var(--sidebar-width);
            background: linear-gradient(180deg, var(--qams-navy) 0%, var(--qams-navy-dark) 100%);
            z-index: 1040;
            display: flex;
            flex-direction: column;
            transition: width 0.28s cubic-bezier(.4,0,.2,1);
            overflow: hidden;
        }

        #sidebar.collapsed { width: var(--sidebar-collapsed); }

        /* Brand / Logo row */
        .sidebar-brand {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 0 18px;
            height: var(--topbar-height);
            border-bottom: 1px solid rgba(255,255,255,0.1);
            flex-shrink: 0;
        }
        .sidebar-brand .brand-icon {
            width: 36px; height: 36px;
            background: var(--qams-accent);
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
            font-size: 18px; color: #fff;
        }
        .sidebar-brand .brand-text {
            font-size: 1.25rem; font-weight: 700;
            color: #fff; letter-spacing: 1.5px;
            white-space: nowrap;
            transition: opacity 0.2s;
        }
        #sidebar.collapsed .brand-text { opacity: 0; pointer-events: none; }

        /* Nav items */
        .sidebar-nav {
            flex: 1;
            overflow-y: auto;
            overflow-x: hidden;
            padding: 12px 0;
        }
        .sidebar-nav::-webkit-scrollbar { width: 4px; }
        .sidebar-nav::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.2); border-radius: 4px; }

        .nav-section-label {
            font-size: 0.65rem; font-weight: 700;
            text-transform: uppercase; letter-spacing: 1.5px;
            color: rgba(255,255,255,0.35);
            padding: 12px 20px 4px;
            white-space: nowrap;
            transition: opacity 0.2s;
        }
        #sidebar.collapsed .nav-section-label { opacity: 0; }

        .nav-item-link {
            display: flex;
            align-items: center;
            gap: 13px;
            padding: 11px 18px;
            color: var(--sidebar-text);
            text-decoration: none;
            border-radius: 10px;
            margin: 2px 10px;
            transition: background 0.18s, color 0.18s;
            white-space: nowrap;
            position: relative;
        }
        .nav-item-link:hover {
            background: rgba(255,255,255,0.1);
            color: var(--sidebar-text-hover);
        }
        .nav-item-link.active {
            background: var(--qams-accent);
            color: #fff;
            box-shadow: 0 4px 12px rgba(79,142,247,0.4);
        }
        .nav-item-link .nav-icon {
            font-size: 1.15rem;
            flex-shrink: 0;
            width: 22px;
            text-align: center;
        }
        .nav-item-link .nav-label {
            font-size: 0.92rem;
            font-weight: 500;
            transition: opacity 0.2s;
        }
        #sidebar.collapsed .nav-label { opacity: 0; pointer-events: none; }

        /* Tooltip when collapsed */
        #sidebar.collapsed .nav-item-link::after {
            content: attr(data-tooltip);
            position: absolute;
            left: calc(var(--sidebar-collapsed) + 8px);
            background: var(--qams-navy);
            color: #fff;
            padding: 5px 10px;
            border-radius: 6px;
            font-size: 0.8rem;
            white-space: nowrap;
            opacity: 0;
            pointer-events: none;
            z-index: 9999;
            transition: opacity 0.15s;
        }
        #sidebar.collapsed .nav-item-link:hover::after { opacity: 1; }

        /* Sidebar footer — admin info */
        .sidebar-footer {
            padding: 14px 16px;
            border-top: 1px solid rgba(255,255,255,0.1);
            display: flex;
            align-items: center;
            gap: 10px;
            flex-shrink: 0;
        }
        .sidebar-footer .avatar {
            width: 36px; height: 36px;
            border-radius: 50%;
            background: #6f42c1;
            display: flex; align-items: center; justify-content: center;
            color: #fff; font-weight: 700; font-size: 14px;
            flex-shrink: 0;
        }
        .sidebar-footer .user-info { overflow: hidden; transition: opacity 0.2s; }
        .sidebar-footer .user-info .uname {
            font-size: 0.88rem; font-weight: 600; color: #fff;
            white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
        }
        .sidebar-footer .user-info .urole {
            font-size: 0.72rem; color: rgba(255,255,255,0.5);
        }
        #sidebar.collapsed .sidebar-footer .user-info { opacity: 0; pointer-events: none; }

        /* ── Top Bar ──────────────────────────────────────────── */
        #topbar {
            position: fixed;
            top: 0;
            left: var(--sidebar-width);
            right: 0;
            height: var(--topbar-height);
            background: #fff;
            border-bottom: 1px solid #e5e9f0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 24px;
            z-index: 1030;
            transition: left 0.28s cubic-bezier(.4,0,.2,1);
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }
        #topbar.collapsed { left: var(--sidebar-collapsed); }

        .topbar-left { display: flex; align-items: center; gap: 14px; }
        .topbar-right { display: flex; align-items: center; gap: 16px; }

        #toggleBtn {
            background: none; border: none; cursor: pointer;
            color: #6b7280; font-size: 1.3rem;
            padding: 5px 8px; border-radius: 8px;
            transition: background 0.15s, color 0.15s;
        }
        #toggleBtn:hover { background: #f3f4f6; color: var(--qams-navy); }

        .page-breadcrumb { font-size: 0.85rem; color: #9ca3af; }
        .page-breadcrumb strong { color: var(--qams-navy); }

        .topbar-badge {
            background: var(--qams-accent);
            color: #fff; font-size: 0.72rem; font-weight: 700;
            padding: 2px 8px; border-radius: 20px;
            letter-spacing: 0.3px;
        }

        .topbar-logout {
            background: none; border: 1px solid #e5e9f0;
            color: #374151; font-size: 0.85rem; font-weight: 500;
            padding: 6px 14px; border-radius: 8px; cursor: pointer;
            text-decoration: none;
            transition: background 0.15s, border-color 0.15s;
            display: flex; align-items: center; gap: 6px;
        }
        .topbar-logout:hover { background: #fff1f2; border-color: #fca5a5; color: #dc2626; }

        /* ── Main Content Area ────────────────────────────────── */
        #mainContent {
            margin-left: var(--sidebar-width);
            padding-top: var(--topbar-height);
            min-height: 100vh;
            transition: margin-left 0.28s cubic-bezier(.4,0,.2,1);
        }
        #mainContent.collapsed { margin-left: var(--sidebar-collapsed); }

        .content-wrapper { padding: 28px 28px 40px; }

        /* ── Cards ──────────────────────────────────────────────*/
        .qams-card {
            border: none;
            border-radius: 14px;
            box-shadow: var(--qams-card-shadow);
        }

        /* ── Primary Button ── */
        .btn-qams {
            background: var(--qams-navy);
            border-color: var(--qams-navy);
            color: #fff; font-weight: 600; letter-spacing: 0.3px;
        }
        .btn-qams:hover { background: var(--qams-navy-light); border-color: var(--qams-navy-light); color: #fff; }

        /* ── Role Badges ── */
        .badge-admin   { background-color: #6f42c1 !important; }
        .badge-teacher { background-color: #0d6efd !important; }
        .badge-student { background-color: #198754 !important; }

        /* ── Alert overrides ── */
        .alert { border-radius: 10px; border: none; }

        /* ── Responsive: mobile sidebar overlay ── */
        @media (max-width: 767.98px) {
            #sidebar { width: 0; overflow: hidden; }
            #sidebar.mobile-open { width: var(--sidebar-width); box-shadow: 4px 0 20px rgba(0,0,0,0.25); }
            #topbar { left: 0 !important; }
            #mainContent { margin-left: 0 !important; }
            #sidebarOverlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.4); z-index: 1035; }
            #sidebarOverlay.show { display: block; }
        }
    </style>

    @stack('styles')
</head>
<body>

{{-- ── Sidebar ─────────────────────────────────────────────────────────── --}}
<aside id="sidebar">

    {{-- Brand --}}
    <div class="sidebar-brand">
        <div class="brand-icon"><i class="bi bi-mortarboard-fill"></i></div>
        <span class="brand-text">QAMS</span>
    </div>

    {{-- Navigation --}}
    <nav class="sidebar-nav">

        <div class="nav-section-label">Main</div>

        <a href="{{ route('admin.dashboard') }}"
           class="nav-item-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"
           data-tooltip="Dashboard">
            <i class="bi bi-speedometer2 nav-icon"></i>
            <span class="nav-label">Dashboard</span>
        </a>

        <div class="nav-section-label">Academic</div>

        <a href="{{ route('admin.classes.index') }}"
           class="nav-item-link {{ request()->routeIs('admin.classes.*') ? 'active' : '' }}"
           data-tooltip="Classes">
            <i class="bi bi-building nav-icon"></i>
            <span class="nav-label">Classes</span>
        </a>

        <a href="{{ route('admin.subjects.index') }}"
           class="nav-item-link {{ request()->routeIs('admin.subjects.*') ? 'active' : '' }}"
           data-tooltip="Subjects">
            <i class="bi bi-book-fill nav-icon"></i>
            <span class="nav-label">Subjects</span>
        </a>

        <div class="nav-section-label">People</div>

        <a href="{{ route('admin.students.index') }}"
           class="nav-item-link {{ request()->routeIs('admin.students.*') ? 'active' : '' }}"
           data-tooltip="Students">
            <i class="bi bi-person-video3 nav-icon"></i>
            <span class="nav-label">Students</span>
        </a>

        <a href="{{ route('admin.teachers.index') }}"
           class="nav-item-link {{ request()->routeIs('admin.teachers.*') ? 'active' : '' }}"
           data-tooltip="Teachers">
            <i class="bi bi-person-workspace nav-icon"></i>
            <span class="nav-label">Teachers</span>
        </a>

        <div class="nav-section-label">System</div>

        <a href="{{ route('admin.users.create') }}"
           class="nav-item-link {{ request()->routeIs('admin.users.create') ? 'active' : '' }}"
           data-tooltip="Add User">
            <i class="bi bi-person-plus-fill nav-icon"></i>
            <span class="nav-label">Add User</span>
        </a>

          <a href="{{ route('admin.dashboard', ['status'=>'blocked']) }}"
              class="nav-item-link {{ request()->input('status') === 'blocked' ? 'active' : '' }}"
              data-tooltip="Blocked Accounts">
            <i class="bi bi-person-slash nav-icon"></i>
            <span class="nav-label">Blocked Accounts</span>
        </a>

        <a href="{{ route('admin.reports.index') }}"
           class="nav-item-link {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}"
           data-tooltip="Reports">
            <i class="bi bi-bar-chart-fill nav-icon"></i>
            <span class="nav-label">Reports</span>
        </a>

    </nav>

    {{-- Footer: logged-in admin info --}}
    <div class="sidebar-footer">
        @php /** @var \App\Models\User $adminUser */ $adminUser = auth()->user(); @endphp
        <div class="avatar">{{ strtoupper(substr($adminUser->name, 0, 1)) }}</div>
        <div class="user-info">
            <div class="uname">{{ $adminUser->name }}</div>
            <div class="urole">Administrator</div>
        </div>
    </div>
</aside>

{{-- Mobile overlay --}}
<div id="sidebarOverlay" onclick="toggleSidebar()"></div>

{{-- ── Top Bar ──────────────────────────────────────────────────────────── --}}
<header id="topbar">
    <div class="topbar-left">
        <button id="toggleBtn" onclick="toggleSidebar()" title="Toggle Sidebar">
            <i class="bi bi-list"></i>
        </button>
        <div class="page-breadcrumb">
            Admin Panel / <strong>@yield('title', 'Dashboard')</strong>
        </div>
    </div>
    <div class="topbar-right">
        <span class="topbar-badge">Admin</span>
        <span class="text-muted small d-none d-md-inline">
            <i class="bi bi-person-circle me-1"></i>{{ $adminUser->name }}
        </span>
        {{-- Logout --}}
        <form method="POST" action="{{ route('logout') }}" style="margin:0">
            @csrf
            <button type="submit" class="topbar-logout">
                <i class="bi bi-box-arrow-right"></i>
                <span class="d-none d-sm-inline">Logout</span>
            </button>
        </form>
    </div>
</header>

{{-- ── Main Content ─────────────────────────────────────────────────────── --}}
<main id="mainContent">
    <div class="content-wrapper">

        {{-- Flash success --}}
        @if(session('success'))
        <div class="alert alert-success alert-dismissible d-flex align-items-center gap-2 mb-4" role="alert">
            <i class="bi bi-check-circle-fill fs-5"></i>
            <div>{{ session('success') }}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        {{-- Flash errors --}}
        @if($errors->any())
        <div class="alert alert-danger alert-dismissible d-flex align-items-start gap-2 mb-4" role="alert">
            <i class="bi bi-exclamation-triangle-fill fs-5 mt-1"></i>
            <div>
                @foreach($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        @yield('content')

    </div>
</main>

{{-- Bootstrap 5 JS --}}
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
    /* ── Sidebar toggle logic ── */
    const sidebar     = document.getElementById('sidebar');
    const topbar      = document.getElementById('topbar');
    const mainContent = document.getElementById('mainContent');
    const overlay     = document.getElementById('sidebarOverlay');
    const isMobile    = () => window.innerWidth < 768;

    // Restore collapsed state from localStorage
    if (!isMobile() && localStorage.getItem('sidebarCollapsed') === 'true') {
        sidebar.classList.add('collapsed');
        topbar.classList.add('collapsed');
        mainContent.classList.add('collapsed');
    }

    function toggleSidebar() {
        if (isMobile()) {
            sidebar.classList.toggle('mobile-open');
            overlay.classList.toggle('show');
        } else {
            const collapsed = sidebar.classList.toggle('collapsed');
            topbar.classList.toggle('collapsed', collapsed);
            mainContent.classList.toggle('collapsed', collapsed);
            localStorage.setItem('sidebarCollapsed', collapsed);
        }
    }
</script>

@stack('scripts')

</body>
</html>
