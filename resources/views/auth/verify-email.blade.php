<x-guest-layout>
    <div class="container">
        <div class="row justify-content-center min-vh-100 align-items-center">
            <div class="col-md-5 col-lg-4">
                <div class="card shadow-sm border-0">
                    <div class="card-body p-4 p-md-5">
                        <div class="text-center mb-4">
                            <div class="mb-2">
                                <i class="bi bi-envelope-check text-primary" style="font-size: 2rem;"></i>
                            </div>
                            <h4 class="fw-bold mb-1">DukaFlow</h4>
                            <p class="text-muted small mb-0">Verify your email</p>
                        </div>

                        <p class="text-muted small mb-3">
                            Thanks for signing up! Before getting started, please verify your email address
                            by clicking the link we just emailed you.
                        </p>
                        <p class="text-muted small mb-4">
                            If you didn’t receive the email, we can send another one.
                        </p>

                        @if (session('status') == 'verification-link-sent')
                            <div class="alert alert-success small py-2" role="alert">
                                A new verification link has been sent to your email address.
                            </div>
                        @endif

                        <div class="d-grid gap-2 mb-3">
                            <form method="POST" action="{{ route('verification.send') }}" id="resendForm">
                                @csrf
                                <button type="submit" class="btn btn-primary w-100">
                                    Resend Verification Email
                                </button>
                            </form>

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="btn btn-outline-secondary w-100">
                                    Log out
                                </button>
                            </form>
                        </div>

                        <p class="text-center text-muted small mb-0">
                            Signed in as<br>
                            <strong>{{ auth()->user()->email ?? '' }}</strong>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('resendForm')?.addEventListener('submit', function () {
            if (typeof showPageLoader === 'function') {
                showPageLoader('Sending email…', 'Check your inbox shortly');
            }
        });
    </script>
</x-guest-layout>