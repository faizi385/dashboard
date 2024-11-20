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
/**
 * Handle an incoming authentication request.
 */
public function store(LoginRequest $request): RedirectResponse
{
    $request->authenticate();

    $request->session()->regenerate();

    $user = Auth::user();

    // Check if the user has the LP role and their status is 'requested'
    if ($user->hasRole('LP')) {
        $lpStatus = DB::table('lps')->where('user_id', $user->id)->value('status');

        if ($lpStatus === 'requested') {
            // Logout the user
            Auth::guard('web')->logout();

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            // Redirect back to the login page with a toaster message
            return redirect()->route('login')->with('error', 'Your account is under review. Please wait for approval.');
        }

        return redirect()->intended('/lp/dashboard');
    } elseif ($user->hasRole('Retailer')) {
        return redirect()->intended('/retailer/dashboard');
    }

    // Default redirect if no specific role is found
    return redirect()->intended(RouteServiceProvider::HOME);
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
