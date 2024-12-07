<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\forgetPasswordMail;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PasswordResetLinkController extends Controller
{
    /**
     * Display the password reset link request view.
     */
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Handle an incoming password reset link request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        try {
            $request->validate([
                'email' => ['required', 'email'],
            ]);
            $checkEmail = User::where('email', $request->email)->first();
            if ($checkEmail) {
                $checkForgotPassword = DB::table('password_resets')->where('email', $request->email)->first();
                if ($checkForgotPassword) {
                    DB::table('password_resets')->where('email', $request->email)->delete();
                }
                $token = Str::random(64);
                DB::table('password_resets')->insert([
                    'email' => $request->email,
                    'token' => $token,
                    'created_at' => Carbon::now()
                ]);
                Mail::to($request->email)->send(new forgetPasswordMail($token));
                $messages = "Reset Password Mail Sent Successfully";

                return redirect()->route('login')->with('success', $messages);
            } else {
                $messages = "Email Not Found";
                return redirect()->route('login')->with('error', $messages);
            }
        }catch (\Exception $e) {
            $messages = "Something went wrong.";
            return redirect()->route('login')->with('error', $messages);
        }

    }
}
