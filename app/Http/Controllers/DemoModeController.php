<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Transaction;
use App\Models\Device;
use App\Models\Certificate;
use App\Models\Evidence;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class DemoModeController extends Controller
{
    /**
     * Start interactive Demo Mode without needing manual login.
     */
    public function enter(Request $request, string $role = 'seller')
    {
        $email = ($role === 'buyer') ? 'buyer@trustchain.com' : 'seller@trustchain.com';
        $user = User::where('email', $email)->first();

        if (! $user) {
            $user = User::first() ?? User::create([
                'name' => 'Demo ' . ucfirst($role),
                'email' => $email,
                'password' => bcrypt('password'),
            ]);
        }

        // Log in as the requested demo persona
        Auth::login($user);
        $request->session()->regenerate();
        $request->session()->put('demo_mode', true);
        $request->session()->put('demo_role', $role);

        return redirect()->route('dashboard')->with('demo_notice', "You are currently viewing TrustChain in Demo Mode as {$user->name} (" . ucfirst($role) . ").");
    }

    /**
     * Switch persona between Seller and Buyer in Demo Mode.
     */
    public function switchRole(Request $request, string $role)
    {
        return $this->enter($request, $role);
    }

    /**
     * Exit Demo Mode and log out.
     */
    public function exit(Request $request)
    {
        $request->session()->forget(['demo_mode', 'demo_role']);
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('info', 'You have exited Demo Mode.');
    }
}
