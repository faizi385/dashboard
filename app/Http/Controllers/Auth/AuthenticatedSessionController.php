<?php

namespace App\Http\Controllers\Auth;

use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use App\Providers\RouteServiceProvider;
use App\Http\Requests\Auth\LoginRequest;

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
  // Inside the store method:
public function store(LoginRequest $request): RedirectResponse
{
    // Attempt to authenticate the user
    if (Auth::attempt($request->only('email', 'password'), $request->filled('remember'))) {
        $request->session()->regenerate();

        // Handle different user roles
        $user = Auth::user();
        if ($user->hasRole('LP')) {
            $lpStatus = DB::table('lps')->where('user_id', $user->id)->value('status');
            if ($lpStatus === 'requested') {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                return redirect()->route('login')->with('error', 'Your account is under review. Please wait for approval.');
            }
            return redirect()->intended('/lp/dashboard');
        } elseif ($user->hasRole('Retailer')) {
            return redirect()->intended('/retailer/dashboard');
        }

        return redirect()->intended(RouteServiceProvider::HOME);
    }

    // If authentication fails
    return redirect()->route('login')->with('error', 'These credentials do not match our records.');
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
