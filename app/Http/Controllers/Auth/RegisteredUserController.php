<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
        'name' => ['required', 'string', 'max:255', 'min:2'],
        'email' => [
            'required',
            'string',
            'lowercase',
            'email:rfc,dns', // stronger email check
            'max:255',
            'unique:'.User::class,
        ],
        'password' => [
            'required',
            'confirmed',
            Password::min(8)
                ->mixedCase()   // upper + lower
                ->numbers()     // at least 1 digit
                ->symbols(),    // at least 1 symbol
        ],
    ], [
        'name.required' => 'Please enter your full name.',
        'name.min' => 'Name must be at least 2 characters.',
        'email.required' => 'Email is required.',
        'email.email' => 'Please enter a valid email address.',
        'email.unique' => 'This email is already registered.',
        'password.required' => 'Password is required.',
        'password.confirmed' => 'Password confirmation does not match.',
        'password.min' => 'Password must be at least 8 characters.',
    ]);

    $user = User::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => Hash::make($request->password),
    ]);

    event(new Registered($user));

    Auth::login($user);

    return redirect(route('dashboard', absolute: false));
    }
}
