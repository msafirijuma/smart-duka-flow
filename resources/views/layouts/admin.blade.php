<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') — DukaFlow</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <script>
        (function () {
            const theme = localStorage.getItem('dukaflow-theme') || 'light';
            document.documentElement.setAttribute('data-bs-theme', theme);
        })();
    </script>
    <style>
        body { 
            background: #f1f5f9; 
        }

        .admin-sidebar {
            width: 260px; 
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            z-index: 1000;
            background: #1e293b !important;
            overflow-y: auto;
        }

        .admin-sidebar .section-title {
            padding: 1.2rem 1.5rem 0.4rem;
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #64748b;
        }

        .admin-sidebar .nav-link {
            background-color: #1e293b; 
            color: #e2e8f0 !important; 
            font-weight: 500 !important;
            padding: 0.6rem 1.2rem !important;
            transition: all 0.2s ease !important;
            display: flex !important;
            align-items: center !important;
        }

        .admin-sidebar .nav-link:hover {
            background-color: #1e293b; 
            color: #fff;
            color: #0d6efd !important;            
            padding-left: 1.5rem !important; 
        }
        
        .admin-sidebar .nav-link.active {
            background-color: #1e293b; 
            color: #fff;
            /* background-color: #f1f5f9 !important;  */
            color: #0d6efd !important;            
            padding-left: 1.5rem !important; 
        }

        .admin-main { 
            flex: 1;
            margin-left: 260px; /* same width to sidebar */
            padding: 1.5rem;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .admin-brand {
            padding: 1.25rem 1.5rem;
            font-size: 1.35rem;
            font-weight: 700;
            color: #fff;
            border-bottom: 1px solid #334155;
            display: flex;
            align-items: center;
            gap: 0.6rem;
        }

        .top-navbar {
            background: #fff;
            border-bottom: 1px solid #e2e8f0;
            padding: 0.75rem 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 900;
        }

        /* Dark mode */
        [data-bs-theme="dark"] body {
            background-color: #0f172a !important;
            color: #e2e8f0;
        }
    </style>
</head>
<body>
    <aside class="admin-sidebar">
        <div class="admin-brand">
            <i class="bi bi-shop"></i> DukaFlow
        </div>
        
        <nav class="nav flex-column px-1">
            <a href="{{ route('admin.dashboard') }}"
               class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="bi bi-speedometer2 me-3"></i> Dashboard
            </a>

            <div class="section-title">MANAGEMENT</div>
            <a href="{{ route('admin.shops.index') }}"
               class="nav-link {{ request()->routeIs('admin.shops.*') ? 'active' : '' }}">
                <i class="bi bi-building me-3"></i> Shops
            </a>
            <a href="{{ route('admin.users.index') }}"
               class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                <i class="bi bi-people me-3"></i> Users
            </a>
            <a href="{{ route('admin.plans.index') }}"
                class="nav-link {{ request()->routeIs('admin.plans.*') ? 'active' : '' }}">
                 <i class="bi bi-credit-card me-3"></i> Plans
            </a>

            <!-- System -->
             <div class="section-title">SYSTEM</div>
            <a href="{{ route('admin.activity.index') }}"
                class="nav-link {{ request()->routeIs('admin.activity.*') ? 'active' : '' }}">
                <i class="bi bi-journal-text me-3"></i> Activity logs
            </a>
            <a href="{{ route('admin.settings.edit') }}"
                class="nav-link {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
                <i class="fas fa-cog me-3"></i> Settings
            </a>
            <hr class="border-secondary mx-3">
            <a href="{{ route('dashboard') }}" class="nav-link">
                <i class="bi bi-arrow-left me-3"></i> Back to DukaFlow
            </a>
        </nav>
    </aside>

    <main class="admin-main">
        <!-- Header -->
        @include('layouts.partials.header')

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
                    timer: 3000,
                    timerProgressBar: false
                });
            @endif

            @if(session('error'))
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'error',
                    title: @json(session('error')),
                    showConfirmButton: false,
                    timer: 3000,
                    falserProgressBar: false
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

    @stack('scripts')
</body>
</html>