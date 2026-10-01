<x-guest-layout>
    <div class="container">
        <div class="row justify-content-center min-vh-100 align-items-center">
            <div class="col-md-5">
                <div class="card shadow-sm border-0">
                    <div class="card-body p-4 p-md-5">

                        <div class="text-center mb-4">
                            <h4 class="fw-bold mb-1">{{ config('app.name', 'DukaFlow') }}</h4>
                            <p class="text-muted small">Create your account</p>
                        </div>

                        <form method="POST" action="{{ route('register') }}">
                            @csrf

                            <!-- Name -->
                            <div class="mb-3">
                                <label for="name" class="form-label">Name</label>
                                <input id="name" type="text"
                                       class="form-control @error('name') is-invalid @enderror"
                                       name="name" value="{{ old('name') }}" required autofocus autocomplete="name">
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Email -->
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input id="email" type="email"
                                       class="form-control @error('email') is-invalid @enderror"
                                       name="email" value="{{ old('email') }}" required autocomplete="username">
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Password -->
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input id="password" type="password"
                                       class="form-control @error('password') is-invalid @enderror"
                                       name="password" required autocomplete="new-password">
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Confirm Password -->
                            <div class="mb-4">
                                <label for="password_confirmation" class="form-label">Confirm Password</label>
                                <input id="password_confirmation" type="password"
                                       class="form-control"
                                       name="password_confirmation" required autocomplete="new-password">
                            </div>

                            <div class="d-grid mb-3">
                                <button type="submit" class="btn btn-primary">
                                    Register
                                </button>
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