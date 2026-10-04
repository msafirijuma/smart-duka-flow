<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') — DukaFlow</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <script>
        (function () {
            const theme = localStorage.getItem('dukaflow-theme') || 'light';
            document.documentElement.setAttribute('data-bs-theme', theme);
        })();
    </script>
    <style>
        body { background: #f1f5f9; }
        .admin-sidebar {
            width: 240px; min-height: 100vh; background: #0f172a;
            position: fixed; left: 0; top: 0;
        }
        .admin-sidebar .nav-link {
            color: #94a3b8; border-radius: 8px; margin: 2px 8px; padding: 8px 12px;
        }
        .admin-sidebar .nav-link:hover, .admin-sidebar .nav-link.active {
            background: #1e293b; color: #fff;
        }
        .admin-main { margin-left: 240px; padding: 1.5rem; }
        .admin-brand { color: #fff; font-weight: 700; padding: 1.25rem; }
    </style>
</head>
<body>
    <aside class="admin-sidebar">
        <div class="admin-brand">
            <i class="bi bi-shop"></i> DukaFlow Admin
        </div>
        <nav class="nav flex-column px-1">
            <a href="{{ route('admin.dashboard') }}"
               class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="bi bi-speedometer2 me-2"></i> Dashboard
            </a>
            <a href="{{ route('admin.shops.index') }}"
               class="nav-link {{ request()->routeIs('admin.shops.*') ? 'active' : '' }}">
                <i class="bi bi-building me-2"></i> Shops
            </a>
            <a href="{{ route('admin.users.index') }}"
               class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                <i class="bi bi-people me-2"></i> Users
            </a>
            <a href="{{ route('admin.plans.index') }}"
                class="nav-link {{ request()->routeIs('admin.plans.*') ? 'active' : '' }}">
                 <i class="bi bi-credit-card me-2"></i> Plans
            </a>
            <hr class="border-secondary mx-3">
            <a href="{{ route('dashboard') }}" class="nav-link">
                <i class="bi bi-arrow-left me-2"></i> Back to DukaFlow
            </a>
        </nav>
    </aside>

    <main class="admin-main">
        @yield('content')
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/sweetalert2.all.min.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            @if(session('success'))
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: @json(session('success')),
                    showConfirmButton: false,
                    timer: 4000,
                    timerProgressBar: true
                });
            @endif

            @if(session('error'))
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'error',
                    title: @json(session('error')),
                    showConfirmButton: false,
                    timer: 4000,
                    timerProgressBar: true
                });
            @endif
        });
    </script>

    <script>
        (function () {
            const THEME_KEY = 'dukaflow-theme';

            function getTheme() {
                return localStorage.getItem(THEME_KEY) || 'light';
            }

            function setTheme(theme) {
                document.documentElement.setAttribute('data-bs-theme', theme);
                localStorage.setItem(THEME_KEY, theme);
                updateIcon(theme);
            }

            function updateIcon(theme) {
                const icon = document.getElementById('themeIcon');
                if (!icon) return;
                // Dark mode active → show sun (click to go light)
                // Light mode → show moon
                icon.className = theme === 'dark' ? 'bi bi-sun' : 'bi bi-moon-stars';
            }

            function toggleTheme() {
                const next = getTheme() === 'dark' ? 'light' : 'dark';
                setTheme(next);
            }

            document.addEventListener('DOMContentLoaded', function () {
                updateIcon(getTheme());
                document.getElementById('themeToggle')?.addEventListener('click', toggleTheme);
            });
        })();
    </script>
</body>
</html>