<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name'))</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root {
            --deped-primary: #1e5aa8;
            --deped-primary-light: #2563eb;
            --deped-accent: #93c5fd;
            --sidebar-width: 260px;
            --header-height: 64px;
        }
        body {
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            min-height: 100vh;
            background: linear-gradient(135deg, #f8f4f0 0%, #f0eef8 50%, #e8f0fe 100%);
            background-attachment: fixed;
        }
        /* Dashboard shell: single rounded card containing sidebar + main */
        .dashboard-shell {
            display: flex;
            min-height: 100vh;
            max-width: 1600px;
            margin: 0 auto;
            background: #fff;
            border-radius: 24px;
            box-shadow: 0 4px 24px rgba(0,0,0,0.06), 0 0 1px rgba(0,0,0,0.04);
            overflow: hidden;
        }
        @media (max-width: 991.98px) {
            .dashboard-shell { border-radius: 0; }
        }
        /* Sidebar */
        .sidebar {
            width: var(--sidebar-width);
            min-width: var(--sidebar-width);
            background: #fff;
            display: flex;
            flex-direction: column;
            border-right: 1px solid #eee;
        }
        .sidebar-brand {
            padding: 1.5rem 1.25rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            text-decoration: none;
            color: #1a1a1a;
        }
        .sidebar-brand img { max-height: 40px; width: auto; }
        .sidebar-brand span { font-weight: 700; font-size: 1.1rem; }
        .sidebar-nav {
            flex: 1;
            padding: 0.75rem;
            display: flex;
            flex-direction: column;
            gap: 0.25rem;
        }
        .sidebar-nav a {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.65rem 1rem;
            border-radius: 12px;
            color: #64748b;
            text-decoration: none;
            font-size: 0.9375rem;
            transition: background 0.2s, color 0.2s;
        }
        .sidebar-nav a i { font-size: 1.2rem; opacity: 0.9; }
        .sidebar-nav a:hover { background: #f1f5f9; color: #334155; }
        .sidebar-nav a.active {
            background: var(--deped-primary);
            color: #fff;
            box-shadow: 0 2px 8px rgba(30, 90, 168, 0.35);
        }
        .sidebar-nav a.active i { opacity: 1; }
        .sidebar-footer {
            padding: 1rem 1.25rem;
            border-top: 1px solid #eee;
        }
        .sidebar-footer .btn-logout {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            width: 100%;
            padding: 0.65rem 1rem;
            border: none;
            border-radius: 12px;
            background: transparent;
            color: #64748b;
            font-size: 0.9375rem;
            text-align: left;
            cursor: pointer;
            transition: background 0.2s, color 0.2s;
        }
        .sidebar-footer .btn-logout:hover { background: #fef2f2; color: #dc2626; }
        .sidebar-footer .btn-logout i { font-size: 1.2rem; }
        /* Main content area */
        .main-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            min-width: 0;
        }
        /* Top header bar */
        .top-header {
            height: var(--header-height);
            padding: 0 1.5rem;
            display: flex;
            align-items: center;
            gap: 1rem;
            border-bottom: 1px solid #eee;
            background: #fff;
            flex-shrink: 0;
        }
        .top-header .header-actions {
            display: flex;
            align-items: center;
        }
        .top-header .user-dropdown {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.35rem 0.5rem 0.35rem 0.75rem;
            border-radius: 12px;
            cursor: pointer;
            border: 1px solid transparent;
            transition: background 0.2s, border-color 0.2s;
        }
        .top-header .user-dropdown:hover { background: #f8fafc; border-color: #e2e8f0; }
        .top-header .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 12px;
            background: linear-gradient(135deg, var(--deped-primary), var(--deped-primary-light));
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 0.95rem;
        }
        .top-header .user-info { text-align: left; }
        .top-header .user-info .user-name { font-weight: 600; font-size: 0.9rem; color: #1e293b; }
        .top-header .user-info .user-email { font-size: 0.75rem; color: #64748b; }
        /* Content area */
        .content-area {
            flex: 1;
            padding: 1.5rem;
            overflow-auto;
        }
        /* Guest layout (login/register) */
        body.guest-layout { background: linear-gradient(135deg, #f8f4f0 0%, #e8f0fe 100%); }
        .guest-layout .dashboard-shell { display: none; }
        .guest-layout main { padding: 2rem 1rem; }
        /* Cards & buttons (keep existing) */
        .card { border: none; border-radius: 16px; box-shadow: 0 1px 3px rgba(0,0,0,0.06); transition: box-shadow 0.2s ease; }
        .btn-deped { background: var(--deped-primary); color: #fff; border: none; border-radius: 12px; transition: background 0.2s ease; }
        .btn-deped:hover { background: var(--deped-primary-light); color: #fff; }
        .btn-deped:focus-visible, .form-control:focus, .form-select:focus { box-shadow: 0 0 0 0.2rem rgba(30, 90, 168, 0.35); outline: none; }
        .table { border-radius: 12px; overflow: hidden; }
        .table th { background-color: #f8fafc; font-weight: 600; }
        .personnel-row { cursor: pointer; }
        .personnel-row:hover { background-color: #f0f9ff !important; }
        .page-title { font-size: 1.35rem; font-weight: 600; color: #1a1a1a; }
        .alert-success .bi, .alert-danger .bi { margin-right: 0.5rem; }
        .btn { border-radius: 12px; }
        .form-control, .form-select { border-radius: 12px; }
        /* Mobile: collapse sidebar */
        @media (max-width: 991.98px) {
            .sidebar { position: fixed; left: 0; top: 0; bottom: 0; z-index: 1030; transform: translateX(-100%); transition: transform 0.25s ease; }
            .sidebar.show { transform: translateX(0); }
            .sidebar-backdrop { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.3); z-index: 1029; }
            .sidebar-backdrop.show { display: block; }
            .top-header .menu-toggle {
                display: flex;
                align-items: center;
                justify-content: center;
                width: 40px;
                height: 40px;
                border-radius: 12px;
                border: none;
                background: #f1f5f9;
                color: #334155;
            }
        }
        @media (min-width: 992px) {
            .top-header .menu-toggle { display: none; }
        }
    </style>
    @stack('styles')
</head>
<body class="{{ auth()->check() ? '' : 'guest-layout' }}">
    @auth
    <div class="dashboard-shell">
        <aside class="sidebar" id="sidebar">
            <a class="sidebar-brand" href="{{ route('dashboard') }}">
                <img src="{{ asset('images/deped-maasin-logo.png') }}" alt="{{ config('app.name') }}">
                <span>Training Tracker</span>
            </a>
            <nav class="sidebar-nav">
                <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <i class="bi bi-grid-1x2"></i>
                    <span>Dashboard</span>
                </a>
                @if(auth()->user()->isAdmin())
                <a href="{{ route('personnel.index') }}" class="{{ request()->routeIs('personnel.*') ? 'active' : '' }}">
                    <i class="bi bi-people"></i>
                    <span>Personnel</span>
                </a>
                <a href="{{ route('trainings.manage') }}" class="{{ request()->routeIs('trainings.manage') ? 'active' : '' }}">
                    <i class="bi bi-journal-check"></i>
                    <span>Trainings</span>
                </a>
                @endif
            </nav>
            <div class="sidebar-footer">
                <form action="{{ route('logout') }}" method="POST" class="d-inline w-100">
                    @csrf
                    <button type="submit" class="btn-logout">
                        <i class="bi bi-box-arrow-left"></i>
                        <span>Log out</span>
                    </button>
                </form>
            </div>
        </aside>

        <div class="main-content">
            <header class="top-header">
                <button type="button" class="menu-toggle d-lg-none" id="sidebarToggle" aria-label="Toggle menu">
                    <i class="bi bi-list"></i>
                </button>
                <div class="header-actions ms-auto">
                    <div class="dropdown">
                        <button class="user-dropdown dropdown-toggle border-0 bg-transparent" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <div class="user-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 2)) }}</div>
                            <div class="user-info">
                                <div class="user-name">{{ auth()->user()->name }}</div>
                                <div class="user-email">{{ auth()->user()->email }}</div>
                            </div>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 rounded-3 py-2">
                            <li><a class="dropdown-item rounded-2" href="{{ route('dashboard') }}"><i class="bi bi-grid me-2"></i> Dashboard</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="dropdown-item rounded-2 text-danger"><i class="bi bi-box-arrow-left me-2"></i> Log out</button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            </header>

            <div class="content-area">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show d-flex align-items-center rounded-3" role="alert">
                        <i class="bi bi-check-circle-fill"></i>
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center rounded-3" role="alert">
                        <i class="bi bi-exclamation-triangle-fill"></i>
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
                @yield('content')
            </div>
        </div>
    </div>
    <div class="sidebar-backdrop" id="sidebarBackdrop" aria-hidden="true"></div>

    <script>
    (function() {
        var sidebar = document.getElementById('sidebar');
        var toggle = document.getElementById('sidebarToggle');
        var backdrop = document.getElementById('sidebarBackdrop');
        if (toggle) {
            toggle.addEventListener('click', function() {
                sidebar.classList.add('show');
                if (backdrop) backdrop.classList.add('show');
            });
        }
        if (backdrop) {
            backdrop.addEventListener('click', function() {
                sidebar.classList.remove('show');
                backdrop.classList.remove('show');
            });
        }
    })();
    </script>
    @endauth

    @guest
    <main class="py-4">
        <div class="container">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show d-flex align-items-center rounded-3" role="alert">
                    <i class="bi bi-check-circle-fill"></i>
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center rounded-3" role="alert">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @yield('content')
        </div>
    </main>
    @endguest

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
