<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;
use App\Mail\OtpMail;

class AuthController extends Controller
{
    public function showRegisterForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|confirmed|min:8',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'verification_token' => Str::random(60),
            'is_verified' => false, 
        ]);

        $otp = rand(100000, 999999);
        Mail::send('auth.otp-email', ['otp' => $otp], function ($message) use ($user) {
            $message->to($user->email)
                ->subject('Your OTP Code');
        });

        session(['otp' => $otp, 'otp_email' => $user->email]);

        return redirect()->route('auth.verifyOtp');
    }

    public function showOtpForm()
    {
        return view('auth.otp');
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp' => 'required|numeric',
        ]);

        $otp = session('otp');
        $email = session('otp_email');

        if ($request->otp == $otp) {
            $user = User::where('email', $email)->first();
            $user->is_verified = true;
            $user->save();

            return redirect()->route('auth.login')->with('status', 'Account verified successfully!');
        }

        return back()->withErrors(['otp' => 'Invalid OTP code.']);
    }

    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            return redirect('/');
        }

        return back()->withErrors(['email' => 'Invalid credentials.']);
    }

    public function showForgotPasswordForm()
    {
        return view('auth.forgot-password');
    }
    
    public function sendResetLink(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);
    
        $user = User::where('email', $request->email)->first();
    
        $otp = rand(100000, 999999);
        
        session(['reset_otp' => $otp, 'reset_email' => $user->email]);
    
        Mail::send('auth.otp-email', ['otp' => $otp], function ($message) use ($user) {
            $message->to($user->email)
                    ->subject('Your OTP for Password Reset');
        });
    
        return redirect()->route('password.reset', ['token' => $user->verification_token]);
    }
    
    public function showResetPasswordForm($token)
    {
        return view('auth.reset-password', ['token' => $token]);
    }
    
    public function resetPassword(Request $request)
    {
        $request->validate([
            'otp' => 'required|numeric',
            'password' => 'required|string|confirmed|min:8',
        ]);
    
        $otp = session('reset_otp');
        if ($request->otp != $otp) {
            return back()->withErrors(['otp' => 'Invalid OTP code.']);
        }
    
        $email = session('reset_email');
        $user = User::where('email', $email)->first();
    
        $user->password = Hash::make($request->password);
        $user->save();
    
        session()->forget(['reset_otp', 'reset_email']);
    
        return redirect()->route('auth.login')->with('status', 'Password reset successfully.');
    }    

    public function logout()
    {
        Auth::logout();

        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return redirect()->route('auth.login')->with('status', 'You have been logged out successfully.');
    }
}
