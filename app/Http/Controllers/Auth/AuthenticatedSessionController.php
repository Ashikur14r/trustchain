<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $user = Auth::user();

        // Demo accounts always use password-only sign-in, including databases
        // that still have the old seeded two-factor values.
        if (in_array($user->email, ['seller@trustchain.com', 'buyer@trustchain.com'], true)) {
            $user->forceFill([
                'two_factor_secret' => null,
                'two_factor_enabled' => false,
            ])->save();
        }

        if ($user->two_factor_enabled) {
            Auth::logout();
            $request->session()->put('auth.pending_user_id', $user->id);
            $request->session()->put('auth.pending_remember', $request->boolean('remember'));
            $request->session()->regenerate();

            return redirect()->route('login.2fa');
        }

        $request->session()->regenerate();

        return redirect()->intended(route('dashboard', absolute: false));
    }

    public function showTwoFactorChallenge(Request $request): \Illuminate\View\View|RedirectResponse
    {
        if (! $request->session()->has('auth.pending_user_id')) {
            return redirect()->route('login');
        }

        return view('auth.two-factor-challenge');
    }

    public function verifyTwoFactorChallenge(Request $request): RedirectResponse
    {
        $request->validate(['code' => ['required', 'digits:6']]);

        if (! $request->session()->has('auth.pending_user_id')) {
            return redirect()->route('login');
        }

        $key = 'login-2fa:'.$request->session()->get('auth.pending_user_id').'|'.$request->ip();
        if (RateLimiter::tooManyAttempts($key, 5)) {
            throw ValidationException::withMessages(['code' => 'Too many attempts. Please wait before trying again.']);
        }

        $user = \App\Models\User::find($request->session()->get('auth.pending_user_id'));
        if (! $user || ! $user->two_factor_enabled || ! \App\Services\GoogleAuthenticatorService::verifyCode($user->two_factor_secret, $request->input('code'))) {
            RateLimiter::hit($key, 60);
            throw ValidationException::withMessages(['code' => 'The authenticator code is invalid or has expired.']);
        }

        RateLimiter::clear($key);
        $remember = $request->session()->pull('auth.pending_remember', false);
        $request->session()->forget('auth.pending_user_id');
        Auth::login($user, $remember);
        $request->session()->regenerate();

        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
