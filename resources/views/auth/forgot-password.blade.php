<x-guest-layout>
    <div class="container">
        <div class="row justify-content-center min-vh-100 align-items-center">
            <div class="col-md-5 col-lg-4">
                <div class="card shadow-sm border-0">
                    <div class="card-body p-4 p-md-5">
                        <div class="text-center mb-4">
                            <h4 class="fw-bold mb-1">DukaFlow</h4>
                            <p class="text-muted small mb-0">Forgot your password?</p>
                        </div>

                        <p class="text-muted small mb-4">
                            No problem. Enter your email and we’ll send you a reset link.
                        </p>

                        <form method="POST" action="{{ route('password.email') }}" id="forgotForm" novalidate>
                            @csrf

                            <div class="mb-4">
                                <label for="email" class="form-label">Email</label>
                                <input id="email" type="email" name="email"
                                       class="form-control @error('email') is-invalid @enderror"
                                       value="{{ old('email') }}"
                                       required autofocus autocomplete="username"
                                       placeholder="you@example.com">
                            </div>

                            <div class="d-grid mb-3">
                                <button type="submit" class="btn btn-primary">
                                    Email Password Reset Link
                                </button>
                            </div>

                            <div class="text-center">
                                <a href="{{ route('login') }}" class="small text-decoration-none">
                                    ← Back to Login
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('forgotForm')?.addEventListener('submit', function (e) {
            const emailEl = document.getElementById('email');
            const email = (emailEl?.value || '').trim();

            if (!email) {
                e.preventDefault();
                showError('Email is required.', emailEl);
                return;
            }
            if (typeof isValidEmail === 'function' && !isValidEmail(email)) {
                e.preventDefault();
                showError('Please enter a valid email address.', emailEl);
                return;
            }

            if (typeof showPageLoader === 'function') {
                showPageLoader('Sending reset link…', 'Check your inbox shortly');
            }
            const btn = this.querySelector('button[type="submit"]');
            if (btn) {
                btn.disabled = true;
                btn.innerText = 'Sending…';
            }
        });
    </script>
</x-guest-layout>