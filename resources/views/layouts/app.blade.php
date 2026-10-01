<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'DukaFlow')</title>

    <!-- Bootstrap 5 CSS -->
    <!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"> -->
    
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <!-- FortAwesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

    <style>
        :root {
            --sidebar-width: 260px;
            --sidebar-bg: #1e293b;
            --sidebar-text: #cbd5e1;
            --sidebar-active: #3b82f6;
        }

        body {
            font-family: system-ui, -apple-system, "Segoe UI", Roboto, sans-serif;
            background-color: #f1f5f9;
            min-height: 100vh;
        }

        #sidebar {
            width: var(--sidebar-width);
            background: var(--sidebar-bg);
            color: var(--sidebar-text);
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            z-index: 1000;
            transition: all 0.3s;
            overflow-y: auto;
        }

        #sidebar .brand {
            padding: 1.25rem 1.5rem;
            font-size: 1.35rem;
            font-weight: 700;
            color: #fff;
            border-bottom: 1px solid #334155;
            display: flex;
            align-items: center;
            gap: 0.6rem;
        }

        #sidebar .brand i {
            color: #3b82f6;
        }

        #sidebar .nav-link {
            color: var(--sidebar-text);
            padding: 0.7rem 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-size: 0.95rem;
            transition: all 0.2s;
        }

        #sidebar .nav-link:hover,
        #sidebar .nav-link.active {
            background: rgba(59, 130, 246, 0.15);
            color: #fff;
        }

        #sidebar .nav-link.active {
            border-left: 3px solid var(--sidebar-active);
        }

        #sidebar .nav-link i {
            font-size: 1.15rem;
            width: 22px;
            text-align: center;
        }

        #sidebar .section-title {
            padding: 1.2rem 1.5rem 0.4rem;
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #64748b;
        }

        /* ========== OVERLAY ========== */
        .sidebar-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.55);
            z-index: 1045;
        }

        .sidebar-overlay.show {
            display: block;
        }

        #main-content {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            transition: all 0.3s;
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

        .dropdown-menu {
            z-index: 1040;
        }

        .shop-badge {
            background: #eff6ff;
            color: #1d4ed8;
            padding: 0.35rem 0.85rem;
            border-radius: 50rem;
            font-size: 0.875rem;
            font-weight: 500;
        }

        .content-wrapper {
            padding: 1.5rem;
            flex: 1;
        }

        .app-footer {
            background: #fff;
            border-top: 1px solid #e2e8f0;
            padding: 0.85rem 1.5rem;
            font-size: 0.85rem;
            color: #64748b;
        }

        @media (max-width: 991.98px) {
            #sidebar {
                margin-left: calc(var(--sidebar-width) * -1);
                z-index: 1050;
            }
            #sidebar.show {
                margin-left: 0;
            }
            #main-content {
                margin-left: 0;
            }
        }
    </style>

    <!-- Local CSS -->
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dataTables.bootstrap5.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/select2-bootstrap-5.min.css') }}">
    
    @stack('styles')
    <!-- @yield('styles') -->

</head>
<body>

    <!-- Overlay (mobile) -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- Main Content -->
    <div id="main-content">
    <!-- Sidebar -->
    @include('layouts.partials.sidebar')

        <!-- Header -->
        @include('layouts.partials.header')

        <!-- Page Content -->
        <div class="content-wrapper">

            <!-- @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif -->

            @yield('content')
        </div>

        <!-- Footer -->
        @include('layouts.partials.footer')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Jquery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <!-- Local JS -->
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('js/dataTables.bootstrap5.min.js') }}"></script>
    <script src="{{ asset('js/sweetalert2.all.min.js') }}"></script>

    <!-- Datatable -->
    <script>
        $(document).ready(function() {
            $('#employeeTable, #departmentTable, #leaveTable, #payrollTable, #performanceTable, #holidayTable', '#announcementTable').DataTable({
                "language": {
                    "search": "Search:",
                    "lengthMenu": "Show _MENU_ entries",
                    "info": "Showing _START_ to _END_ of _TOTAL_ entries",
                    responsive: true,
                    scrollX: true,        // horizontal scroll 
                    autoWidth: false,
                    "paginate": {
                        "first": "First",
                        "last": "Last",
                        "next": "Next",
                        "previous": "Prev"
                    }
                }
            });
        });
    </script>

    <!-- SweetAlert2 -->
    <script>
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 4000,
            timerProgressBar: false,
            didOpen: (toast) => {
                    toast.onmouseenter = Swal.stopTimer;
                    toast.onmouseleave = Swal.resumeTimer;
            }
        });

        // success message
        @if(session('success'))
            Toast.fire({
                icon: 'success',
                title: "{{ session('success') }}"
            });
        @endif
        
        @if(session('error'))
            Toast.fire({
                icon: 'error',
                title: 'Error!',
                text: "{{ session('error') }}",
                confirmButtonText: 'OK'
            });
        @endif
    </script>

    <!-- Overlays & Offset-->
    <script>
        const sidebar = document.getElementById('sidebar');
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

    <script>
        window.addEventListener('pageshow', function (event) {
            // Back button pressed
            var historyTraversal = event.persisted || 
                                (typeof window.performance != 'undefined' && 
                                    window.performance.navigation.type === 2);
                        
            if (historyTraversal) {
                // Close any frozen SweetAlert spinner immediately
                if (typeof Swal !== 'undefined') {
                    Swal.close();
                }

                var badge = document.querySelector('.notification-badge'); 
                if (badge) {
                    badge.style.display = 'none';
                }

                window.location.reload();
                
            }
        });
        
    </script>

    @stack('scripts')
    
</body>
</html>