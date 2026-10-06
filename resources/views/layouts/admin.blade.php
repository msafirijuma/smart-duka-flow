<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') — {{ setting('platform_name', 'DukaFlow') }}</title>
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
            --sidebar-width: 260px;
        }

        #main-content {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            transition: all 0.3s;
        }

        .admin-main { 
            flex: 1;
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

        .brand-logo {
            max-height: 40px;
            width: auto;
            object-fit: contain;
        }

        .admin-sidebar {
            padding: 0.85rem;
            width: 260px; 
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            z-index: 1000;
            background: #1e293b !important;
            overflow-y: auto;
        }

        .admin-sidebar .nav {
            padding-block: 1rem;
        }

        .admin-sidebar .section-title {
            padding: 1.2rem 1.5rem 0.4rem;
            font-size: 0.7rem;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #64748b;
        }

        .admin-sidebar .nav-link {
            background-color: #1e293b; 
            color: #e2e8f0 !important; 
            font-weight: 500 !important;
            font-size: 0.95rem;
            gap: 0.75rem;
            padding: 0.6rem 1.2rem !important;
            transition: all 0.2s ease !important;
            display: flex !important;
            align-items: center !important;
        }

        .admin-sidebar .nav-link.active {
            background-color: #f1f5f9 !important; 
            background: rgba(59, 130, 246, 0.15) !important;
            color: #fff !important;            
            border-left: 3px solid #0d6efd;
        }

         .admin-sidebar .nav-link:hover {
            color: #f1f5f9 !important; 
            background-color: #0d6efd !important;            
            padding-left: 1.5rem !important; 
        }

        .top-navbar {
            background: #fff;
            padding: 1.2rem 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 900;
        }

        .shop-badge {
            background: #eff6ff;
            color: #1d4ed8;
            padding: 0.35rem 0.85rem;
            border-radius: 50rem;
            font-size: 0.875rem;
            font-weight: 500;
        }

        .app-footer {
            background: #fff;
            border-top: 1px solid #e2e8f0;
            padding: 0.85rem 1.5rem;
            font-size: 0.85rem;
            color: #64748b;
        }

        /* Dark mode */
        [data-bs-theme="dark"] body {
            background-color: #0f172a !important;
            color: #e2e8f0;
        }

        [data-bs-theme="dark"] .top-navbar {
            background-color: #172943 !important;
        }

        [data-bs-theme="dark"] .dropdown>#userDropdown {
            color: #fff !important;
        }

        [data-bs-theme="dark"] .bi-sun {
            border-color: #334155;
            color: #e2e8f0;
        }

        [data-bs-theme="dark"] .app-footer {
            background-color: #172943;
            border-color: #334155;
        }

        @media (max-width: 991.98px) {
            .admin-sidebar {
                margin-left: calc(var(--sidebar-width) * -1);
                z-index: 1050;
            }
            .admin-sidebar.show {
                margin-left: 0;
            }
            #main-content {
                margin-left: 0;
            }
        }
    </style>
</head>
<body>
    <!-- Overlay (mobile) -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- Main Content -->
    <div id="main-content">
        <!-- Sidebar -->
        @include('layouts.partials.admin-sidebar')

        <!-- Header -->
        @include('layouts.partials.admin-header')

        <!-- Page Content -->
        <div class="admin-main mt-4">

            @yield('content')
        </div>

        <!-- Footer -->
        @include('layouts.partials.footer')
    </div>

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

    <!-- Overlays & Offset-->
    <script>
        const sidebar = document.getElementById('adminSidebar');
        const overlay = document.getElementById('sidebarOverlay');
        const toggleBtn = document.getElementById('sidebarToggle');
        const closeBtn = document.getElementById('sidebarClose');

        function openSidebar() {
            sidebar.classList.add('show');
            overlay.classList.add('show');
            document.body.style.overflow = 'hidden';
        }

        function closeSidebar() {
            sidebar.classList.remove('show');
            overlay.classList.remove('show');
            document.body.style.overflow = '';
        }

        toggleBtn?.addEventListener('click', () => {
            sidebar.classList.contains('show') ? closeSidebar() : openSidebar();
        });

        closeBtn?.addEventListener('click', closeSidebar);
        overlay?.addEventListener('click', closeSidebar);

        document.querySelectorAll('.app-sidebar-container a').forEach(link => {
            link.addEventListener('click', () => {
                if (window.innerWidth < 992) closeSidebar();
            });
        });
    </script>

    <!-- Dark mode -->
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