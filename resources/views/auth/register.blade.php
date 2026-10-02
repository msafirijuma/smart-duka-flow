<x-guest-layout>
    <div class="container">
        <div class="row justify-content-center min-vh-100 align-items-center">
            <div class="col-md-5">
                <div class="card shadow-sm border-0">
                    <div class="card-body p-4 p-md-5">
                        <div class="text-center mb-4">
                            <h4 class="fw-bold mb-1">DukaFlow</h4>
                            <p class="text-muted small">Create your account</p>
                        </div>

                        <form method="POST" action="{{ route('register') }}" id="registerForm" novalidate>
                            @csrf

                            <div class="mb-3">
                                <label for="name" class="form-label">Name</label>
                                <input id="name" type="text" name="name"
                                       class="form-control @error('name') is-invalid @enderror"
                                       value="{{ old('name') }}"
                                       required autofocus autocomplete="name" minlength="2" maxlength="255">
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input id="email" type="email" name="email"
                                       class="form-control @error('email') is-invalid @enderror"
                                       value="{{ old('email') }}"
                                       required autocomplete="username"
                                       placeholder="you@example.com">
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
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
                                    <input id="password_confirmation" type="password" name="password_confirmation"
                                        class="form-control" required autocomplete="new-password">
                                    <button type="button" class="password-toggle" data-target="password_confirmation" aria-label="Show password">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="d-grid mb-3">
                                <button type="submit" class="btn btn-primary">Register</button>
                            </div>

                            <div class="text-center">
                                <a href="{{ route('login') }}" class="small text-decoration-none">
                                    <span class="text-dark">Already registered?</span> Login
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>