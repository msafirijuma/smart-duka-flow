<x-guest-layout>
    <div class="container">
        <div class="row justify-content-center min-vh-100 align-items-center">
            <div class="col-md-5 col-lg-4">
                <div class="card shadow-sm border-0">
                    <div class="card-body p-4 p-md-5">
                        <div class="text-center mb-4">
                            <h4 class="fw-bold mb-1">DukaFlow</h4>
                            <p class="text-muted small">Set a new password</p>
                        </div>

                        <form method="POST" action="{{ route('password.store') }}" id="resetForm" novalidate>
                            @csrf

                            <input type="hidden" name="token" value="{{ $request->route('token') }}">

                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input id="email" type="email" name="email"
                                       class="form-control @error('email') is-invalid @enderror"
                                       value="{{ old('email', $request->email) }}"
                                       required autofocus autocomplete="username">
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label">New Password</label>
                                <div class="password-wrap">
                                    <input id="password" type="password" name="password"
                                           class="form-control @error('password') is-invalid @enderror"
                                           required autocomplete="new-password"
                                           placeholder="8+ upper, lower, number, symbol">
                                    <button type="button" class="password-toggle" data-target="password" aria-label="Show password">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </div>
                                <small class="text-muted">Min 8 characters: upper, lower, number &amp; symbol</small>
                            </div>

                            <div class="mb-4">
                                <label for="password_confirmation" class="form-label">Confirm Password</label>
                                <div class="password-wrap">
                                    <input id="password_confirmation" type="password"
                                           name="password_confirmation"
                                           class="form-control" required autocomplete="new-password">
                                    <button type="button" class="password-toggle" data-target="password_confirmation" aria-label="Show password">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">Reset Password</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('resetForm')?.addEventListener('submit', function (e) {
            const emailEl = document.getElementById('email');
            const passwordEl = document.getElementById('password');
            const confirmEl = document.getElementById('password_confirmation');

            const email = (emailEl?.value || '').trim();
            const password = passwordEl?.value || '';
            const confirm = confirmEl?.value || '';

            if (!email || (typeof isValidEmail === 'function' && !isValidEmail(email))) {
                e.preventDefault();
                showError('Please enter a valid email address.', emailEl);
                return;
            }
            if (!password) {
                e.preventDefault();
                showError('Password is required.', passwordEl);
                return;
            }
            if (typeof isStrongPassword === 'function' && !isStrongPassword(password)) {
                e.preventDefault();
                showError('Password must be 8+ chars with uppercase, lowercase, number and symbol.', passwordEl);
                return;
            }
            if (password !== confirm) {
                e.preventDefault();
                showError('Password confirmation does not match.', confirmEl);
                return;
            }

            if (typeof showPageLoader === 'function') {
                showPageLoader('Updating password…', 'Please wait');
            }
        });
    </script>
</x-guest-layout>