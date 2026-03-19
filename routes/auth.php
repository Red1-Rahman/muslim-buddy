<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Auth\Events\PasswordReset;
use App\Models\User;
use Laravel\Socialite\Facades\Socialite;

Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::post('/login', function (Illuminate\Http\Request $request) {
    $credentials = $request->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);

    if (Auth::attempt($credentials, $request->boolean('remember'))) {
        $request->session()->regenerate();
        return redirect()->intended('/dashboard');
    }

    return back()->withErrors([
        'email' => 'The provided credentials do not match our records.',
    ])->onlyInput('email');
})->name('login.post');

Route::get('/register', function () {
    return view('auth.register');
})->name('register');

Route::post('/register', function (Illuminate\Http\Request $request) {
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users',
        'password' => 'required|string|min:8|confirmed',
    ]);

    $user = \App\Models\User::create([
        'name' => $validated['name'],
        'email' => $validated['email'],
        'password' => Hash::make($validated['password']),
    ]);

    Auth::login($user);

    return redirect('/dashboard');
})->name('register.post');

Route::post('/logout', function (Illuminate\Http\Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect('/');
})->name('logout');

Route::get('/auth/google/redirect', function () {
    if (!config('services.google.client_id') || !config('services.google.client_secret') || !config('services.google.redirect')) {
        return redirect()->route('login')->withErrors([
            'email' => 'Google login is not configured yet.',
        ]);
    }

    return Socialite::driver('google')
        ->scopes(['openid', 'profile', 'email'])
        ->redirect();
})->middleware('guest')->name('auth.google.redirect');

Route::get('/auth/google/callback', function (Illuminate\Http\Request $request) {
    try {
        $googleUser = Socialite::driver('google')->user();
    } catch (\Throwable $e) {
        return redirect()->route('login')->withErrors([
            'email' => 'Google sign-in failed. Please try again.',
        ]);
    }

    if (!$googleUser->getEmail()) {
        return redirect()->route('login')->withErrors([
            'email' => 'Google account did not provide an email address.',
        ]);
    }

    $user = User::where('email', $googleUser->getEmail())
        ->orWhere('google_id', $googleUser->getId())
        ->first();

    if (!$user) {
        $user = User::create([
            'name' => $googleUser->getName() ?: $googleUser->getNickname() ?: 'Google User',
            'email' => $googleUser->getEmail(),
            'google_id' => $googleUser->getId(),
            'email_verified_at' => now(),
            'avatar' => $googleUser->getAvatar(),
            'password' => Hash::make(Str::random(40)),
        ]);
    } else {
        $updates = [];

        if (!$user->google_id) {
            $updates['google_id'] = $googleUser->getId();
        }

        if (!$user->email_verified_at) {
            $updates['email_verified_at'] = now();
        }

        if (!$user->avatar && $googleUser->getAvatar()) {
            $updates['avatar'] = $googleUser->getAvatar();
        }

        if (!empty($updates)) {
            $user->update($updates);
        }
    }

    Auth::login($user, true);
    $request->session()->regenerate();

    return redirect()->intended('/dashboard');
})->middleware('guest')->name('auth.google.callback');

// Password Reset Routes
Route::get('/forgot-password', function () {
    return view('auth.forgot-password');
})->middleware('guest')->name('password.request');

Route::post('/forgot-password', function (Illuminate\Http\Request $request) {
    $request->validate(['email' => 'required|email']);

    $status = Password::sendResetLink(
        $request->only('email')
    );

    return $status === Password::RESET_LINK_SENT
                ? back()->with(['status' => __($status)])
                : back()->withErrors(['email' => __($status)]);
})->middleware('guest')->name('password.email');

Route::get('/reset-password/{token}', function (string $token) {
    return view('auth.reset-password', ['token' => $token]);
})->middleware('guest')->name('password.reset');

Route::post('/reset-password', function (Illuminate\Http\Request $request) {
    $request->validate([
        'token' => 'required',
        'email' => 'required|email',
        'password' => 'required|min:8|confirmed',
    ]);

    $status = Password::reset(
        $request->only('email', 'password', 'password_confirmation', 'token'),
        function (User $user, string $password) {
            $user->forceFill([
                'password' => Hash::make($password)
            ])->setRememberToken(Str::random(60));

            $user->save();

            event(new PasswordReset($user));
        }
    );

    return $status === Password::PASSWORD_RESET
                ? redirect()->route('login')->with('status', __($status))
                : back()->withErrors(['email' => [__($status)]]);
})->middleware('guest')->name('password.store');
