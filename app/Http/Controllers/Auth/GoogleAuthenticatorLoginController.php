<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\GoogleAuthenticatorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GoogleAuthenticatorLoginController extends Controller
{
    /**
     * Show the Google Authenticator login form.
     */
    public function showLoginForm()
    {
        // Suggest active users who have Google Authenticator or demo accounts
        $demoUsers = User::whereIn('email', ['seller@trustchain.com', 'buyer@trustchain.com'])->get();
        return view('auth.google_auth_login', compact('demoUsers'));
    }

    /**
     * Verify Google Authenticator 6-digit TOTP code and log in.
     */
    public function verifyLogin(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
            'one_time_password' => ['required', 'string', 'size:6'],
        ]);

        $user = User::where('email', $request->email)->firstOrFail();

        if (! $user->two_factor_enabled || empty($user->two_factor_secret)) {
            return back()->withErrors(['email' => 'Two-factor authentication is not enabled for this account. Sign in with your password first.']);
        }

        $isValid = GoogleAuthenticatorService::verifyCode($user->two_factor_secret, $request->one_time_password);

        if (! $isValid) {
            return back()->withInput()->withErrors([
                'one_time_password' => 'The 6-digit Google Authenticator code is invalid or has expired. Please check your app or use the live demo code.',
            ]);
        }

        // Authenticate user
        Auth::login($user, (bool) $request->boolean('remember'));
        $request->session()->regenerate();

        return redirect()->intended(route('dashboard'))
            ->with('success', 'Logged in successfully via Google Authenticator!');
    }

    /**
     * Show setup page inside user profile / security.
     */
    public function showSetup(Request $request)
    {
        $user = Auth::user();

        if (empty($user->two_factor_secret)) {
            $user->two_factor_secret = GoogleAuthenticatorService::generateSecret();
            $user->forceFill(['two_factor_secret' => $user->two_factor_secret])->save();
        }

        $qrUri = GoogleAuthenticatorService::getQrCodeUri('TrustChain', $user->email, $user->two_factor_secret);
        return view('auth.google_auth_setup', [
            'user' => $user,
            'secret' => $user->two_factor_secret,
            'qrUri' => $qrUri,
        ]);
    }

    /**
     * Enable or confirm Google Authenticator setup.
     */
    public function confirmSetup(Request $request)
    {
        $request->validate([
            'code' => ['required', 'string', 'size:6'],
        ]);

        $user = Auth::user();

        abort_unless($user->two_factor_secret, 403);

        if (! GoogleAuthenticatorService::verifyCode($user->two_factor_secret, $request->code)) {
            return back()->withErrors(['code' => 'Invalid verification code. Please check your Authenticator app.']);
        }

        $user->forceFill(['two_factor_enabled' => true])->save();

        return redirect()->route('profile.edit')->with('status', 'two-factor-enabled');
    }
}
