<x-guest-layout>
    <div class="container">
        <div class="row justify-content-center min-vh-100 align-items-center">
            <div class="col-md-5 col-lg-4">
                <div class="card shadow-sm border-0">
                    <div class="card-body p-4 p-md-5">
                        <div class="text-center mb-4">
                            <div class="mb-2">
                                <i class="bi bi-shield-lock text-primary" style="font-size: 2rem;"></i>
                            </div>
                            <h4 class="fw-bold mb-1">DukaFlow</h4>
                            <p class="text-muted small mb-0">Confirm your password</p>
                        </div>

                        <p class="text-muted small mb-4">
                            This is a secure area. Please confirm your password before continuing.
                        </p>

                        <form method="POST" action="{{ route('password.confirm') }}" id="confirmForm" novalidate>
                            @csrf

                            <div class="mb-4">
                                <label for="password" class="form-label">Password</label>
                                <div class="password-wrap">
                                    <input id="password" type="password" name="password"
                                           class="form-control @error('password') is-invalid @enderror"
                                           required autofocus autocomplete="current-password"
                                           placeholder="Enter your password">
                                    <button type="button" class="password-toggle" data-target="password" aria-label="Show password">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="d-grid mb-3">
                                <button type="submit" class="btn btn-primary">Confirm</button>
                            </div>

                            <div class="text-center">
                                <a href="{{ route('dashboard') }}" class="small text-decoration-none">
                                    ← Back to Dashboard
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('confirmForm')?.addEventListener('submit', function (e) {
            const passwordEl = document.getElementById('password');
            const password = passwordEl?.value || '';

            if (!password) {
                e.preventDefault();
                showError('Password is required.', passwordEl);
                return;
            }

            if (typeof showPageLoader === 'function') {
                showPageLoader('Confirming…', 'Please wait');
            }
        });
    </script>
</x-guest-layout>