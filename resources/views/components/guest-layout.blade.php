<!DOCTYPE html>
<!-- <html lang="{{ str_replace('_', '-', app()->getLocale()) }}"> -->
<html lang="en" data-bs-theme="light">
<head>
    <meta charset="utf-8">
    <html lang="en" data-bs-theme="light">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'DukaFlow') }}</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <script>
        (function () {
            const theme = localStorage.getItem('dukaflow-theme') || 'light';
            document.documentElement.setAttribute('data-bs-theme', theme);
        })();
    </script>

    <style>
        body {
            background-color: #f1f5f9;
            font-family: system-ui, -apple-system, "Segoe UI", Roboto, sans-serif;
        }
        .page-loader {
            position: fixed;
            inset: 0;
            z-index: 9999;
            background: rgba(241, 245, 249, 0.85);
            backdrop-filter: blur(6px);
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .page-loader.d-none { 
            display: none !important; 
        }
        .page-loader-inner {
            text-align: center;
            background: #fff;
            padding: 2rem 2.5rem;
            border-radius: 1rem;
            box-shadow: 0 10px 40px rgba(0,0,0,.08);
            min-width: 260px;
        }
    </style>
</head>
<body>
    {{ $slot }}

    {{-- Full-page loader overlay --}}
    <div id="pageLoader" class="page-loader d-none">
        <div class="page-loader-inner">
            <div class="spinner-border text-primary" role="status" style="width: 2.5rem; height: 2.5rem;">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-3 mb-0 fw-semibold" id="pageLoaderText">Please wait…</p>
            <p class="text-muted small mb-0 mt-1" id="pageLoaderSub">This will only take a moment</p>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    {{-- SweetAlert: asset yako AU CDN fallback --}}
    <script src="{{ asset('js/sweetalert2.all.min.js') }}"></script>
    <script>
        // Fallback if asset is not found
        if (typeof Swal === 'undefined') {
            document.write('<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"><\/script>');
        }
    </script>

    <script>
        // ===== Helpers =====
        function isValidEmail(email) {
            return /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/i.test(String(email).trim());
        }

        function isStrongPassword(password) {
            // min 8, upper, lower, number, symbol
            return /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z0-9]).{8,}$/.test(password);
        }

        function showError(message, focusEl) {
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'error',
                title: message,
                showConfirmButton: false,
                timer: 4000,
                timerProgressBar: false
            });
            if (focusEl) {
                focusEl.focus();
                focusEl.classList.add('is-invalid');
            }
        }

        function showSuccess(message) {
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: message,
                showConfirmButton: false,
                timer: 4000,
                timerProgressBar: false
            });
        }

        // Clear invalid state on input
        document.addEventListener('input', function (e) {
            if (e.target.classList.contains('is-invalid')) {
                e.target.classList.remove('is-invalid');
            }
        });

        // ===== Server-side flash / validation errors =====
        document.addEventListener('DOMContentLoaded', function () {
        @if(session('success'))
            showSuccess(@json(session('success')));
        @endif

        @if(session('status'))
            showSuccess(@json(session('status')));
        @endif

        @if(session('error'))
            showError(@json(session('error')));
        @endif

        @if($errors->any())
            showError(@json($errors->first()));
        @endif

        // ===== LOGIN form =====
        const loginForm = document.getElementById('loginForm');
        if (loginForm) {
            loginForm.addEventListener('submit', function (e) {
            const emailEl = document.getElementById('email');
            const passwordEl = document.getElementById('password');
            const email = (emailEl?.value || '').trim();
            const password = passwordEl?.value || '';

            if (!email) {
                e.preventDefault();
                showError('Email is required.', emailEl);
                return;
            }
            if (!isValidEmail(email)) {
                e.preventDefault();
                showError('Please enter a valid email address.', emailEl);
                return;
            }
            if (!password) {
                e.preventDefault();
                showError('Password is required.', passwordEl);
                return;
            }
            if (password.length < 8) {
                e.preventDefault();
                showError('Password must be at least 8 characters.', passwordEl);
                return;
            }

            // Valid:
            showPageLoader('Signing you in…', 'Almost there');
            const btn = loginForm.querySelector('button[type="submit"]');
            if (btn) {
                btn.disabled = true;
                btn.innerHTML = 'Signing in…';
                }
            });
        }

        // ===== REGISTER form =====
        const registerForm = document.getElementById('registerForm');
        if (registerForm) {
            registerForm.addEventListener('submit', function (e) {
                const nameEl = document.getElementById('name');
                const emailEl = document.getElementById('email');
                const passwordEl = document.getElementById('password');
                const confirmEl = document.getElementById('password_confirmation');

                const name = (nameEl?.value || '').trim();
                const email = (emailEl?.value || '').trim();
                const password = passwordEl?.value || '';
                const confirm = confirmEl?.value || '';

                if (!name || name.length < 2) {
                    e.preventDefault();
                    showError('Please enter your full name (at least 2 characters).', nameEl);
                    return;
                }
                if (!email) {
                    e.preventDefault();
                    showError('Email is required.', emailEl);
                    return;
                }
                if (!isValidEmail(email)) {
                    e.preventDefault();
                    showError('Please enter a valid email address.', emailEl);
                    return;
                }
                if (!password) {
                    e.preventDefault();
                    showError('Password is required.', passwordEl);
                    return;
                }
                if (!isStrongPassword(password)) {
                    e.preventDefault();
                    showError('Password must be 8+ chars with uppercase, lowercase, number and symbol.', passwordEl);
                    return;
                }
                if (password !== confirm) {
                    e.preventDefault();
                    showError('Password confirmation does not match.', confirmEl);
                    return;
                }
        
                // Valid → show loader, allow submit
                showPageLoader('Creating your account…', 'Please wait while we set up your workspace');
                const btn = registerForm.querySelector('button[type="submit"]');
                if (btn) {
                    btn.disabled = true;
                    btn.innerHTML = 'Creating…';
                    }
                });
            }
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

    <script>
        function showPageLoader(title, subtitle) {
            const el = document.getElementById('pageLoader');
            if (!el) return;
            document.getElementById('pageLoaderText').textContent = title || 'Please wait…';
            document.getElementById('pageLoaderSub').textContent = subtitle || 'This will only take a moment';
            el.classList.remove('d-none');
        }

        function hidePageLoader() {
            document.getElementById('pageLoader')?.classList.add('d-none');
        }
    </script>
</body>
</html>