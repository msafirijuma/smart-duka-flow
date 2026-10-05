<x-guest-layout>
    <div class="container">
        <div class="row justify-content-center min-vh-100 align-items-center">
            <div class="col-md-5">
                <div class="card shadow-sm border-0">
                    <div class="card-body p-4 p-md-5">
                        <div class="text-center mb-4">
                            <h4 class="fw-bold mb-1">DukaFlow</h4>
                            <p class="text-muted small">Sign in to your account</p>
                        </div>

                        <form method="POST" action="{{ route('login') }}" id="loginForm" novalidate>
                            @csrf

                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input id="email" type="email" name="email"
                                       class="form-control @error('email') is-invalid @enderror"
                                       value="{{ old('email') }}"
                                       required autofocus autocomplete="username"
                                       placeholder="you@example.com">
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <div class="password-wrap">
                                    <input id="password" type="password" name="password"
                                        class="form-control @error('password') is-invalid @enderror"
                                        required minlength="8" autocomplete="current-password"
                                        placeholder="Minimum 8 characters">
                                    <button title="Show/hide password" type="button" class="password-toggle" data-target="password" aria-label="Show password">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </div>
                            </div>

                            <input type="checkbox"
                                name="remember"
                                id="remember"
                                class="form-check-input"
                                value="1"
                                {{ old('remember') ? 'checked' : '' }}>
                            <label class="form-check-label" for="remember">
                                Remember me
                            </label>

                            <div class="d-grid mb-3">
                                <button type="submit" class="btn btn-primary">Log in</button>
                            </div>

                            <div class="text-center">
                                @if (Route::has('password.request'))
                                    <a href="{{ route('password.request') }}" class="small text-danger text-decoration-none d-block mb-2">
                                        Forgot your password?
                                    </a>
                                @endif
                                <span class="small">Don't have an account?</span>
                                <a href="{{ route('register') }}" class="small text-decoration-none">
                                     Register
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>