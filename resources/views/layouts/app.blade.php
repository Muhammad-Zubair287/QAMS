<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    {{-- CSRF token (needed by Bootstrap JS and meta-tags for AJAX) --}}
    <meta name="csrf-token" content="{{ csrf_token() }}" />

    <title>QAMS — @yield('title', 'Quiz & Assignment Management System')</title>

    {{-- Bootstrap 5 CSS (CDN — no npm needed for prototype) --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
    {{-- Bootstrap Icons --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet" />

    <style>
        /* ── QAMS Custom CSS Variables (easy to change globally) ── */
        :root {
            --qams-navy:    #1e3a5f;
            --qams-green:   #2ecc71;
            --qams-red:     #e74c3c;
            --qams-bg:      #f0f4f8;
            --qams-card-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        }

        body {
            background-color: var(--qams-bg);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        main { flex: 1; }

        /* ── Navbar ── */
        .qams-navbar {
            background: linear-gradient(135deg, var(--qams-navy), #2c5282);
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.2);
        }
        .qams-navbar .navbar-brand { font-weight: 700; letter-spacing: 1px; font-size: 1.3rem; }

        /* ── Cards ── */
        .qams-card {
            border: none;
            border-radius: 14px;
            box-shadow: var(--qams-card-shadow);
        }

        /* ── Primary Button ── */
        .btn-qams {
            background: var(--qams-navy);
            border-color: var(--qams-navy);
            color: #fff;
            font-weight: 600;
            letter-spacing: 0.3px;
        }
        .btn-qams:hover {
            background: #2c5282;
            border-color: #2c5282;
            color: #fff;
        }

        /* ── Role Badge Colors ── */
        .badge-admin   { background-color: #6f42c1 !important; }
        .badge-teacher { background-color: #0d6efd !important; }
        .badge-student { background-color: #198754 !important; }

        /* ── Footer ── */
        footer {
            background: #fff;
            border-top: 1px solid #dee2e6;
        }
    </style>

    {{-- Child pages can add their own CSS here --}}
    @stack('styles')
</head>
<body>

    {{-- ── Navbar (only shown when logged in) ────────────────────────── --}}
    @auth
    <nav class="navbar navbar-expand-lg navbar-dark qams-navbar">
        <div class="container">
            {{-- Logo --}}
            <a class="navbar-brand text-white" href="#">
                <i class="bi bi-mortarboard-fill me-2"></i>QAMS
            </a>

            {{-- Right side: user info + logout --}}
            <div class="ms-auto d-flex align-items-center gap-3">
                <span class="text-white small">
                    <i class="bi bi-person-circle me-1"></i>
                    <strong>{{ auth()->user()->name }}</strong>
                    {{-- Role badge with different color per role --}}
                    <span class="badge ms-1
                        @if(auth()->user()->isAdmin()) badge-admin
                        @elseif(auth()->user()->isTeacher()) badge-teacher
                        @else badge-student
                        @endif">
                        {{ ucfirst(auth()->user()->role) }}
                    </span>
                </span>

                {{-- Logout button — uses POST to prevent CSRF --}}
                <form method="POST" action="{{ route('logout') }}" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-outline-light btn-sm">
                        <i class="bi bi-box-arrow-right me-1"></i>Logout
                    </button>
                </form>
            </div>
        </div>
    </nav>
    @endauth

    {{-- ── Flash Messages ─────────────────────────────────────────────── --}}
    <div class="container mt-3">

        {{-- Success message (green) --}}
        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        {{-- Error messages (red) —— checks multiple possible error bag keys --}}
        @if($errors->has('login_error') || $errors->has('register_error') || $errors->has('error'))
        <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            {{ $errors->first('login_error')
                ?? $errors->first('register_error')
                ?? $errors->first('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

    </div>

    {{-- ── Main Content ─────────────────────────────────────────────────── --}}
    <main class="container py-4">
        @yield('content')
    </main>

    {{-- ── Footer ──────────────────────────────────────────────────────── --}}
    <footer class="text-center text-muted py-3 mt-auto">
        <small>&copy; {{ date('Y') }} QAMS — Quiz &amp; Assignment Management System</small>
    </footer>

    {{-- Bootstrap 5 JS Bundle (includes Popper) --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    {{-- Child pages can add their own scripts here --}}
    @stack('scripts')
</body>
</html>
