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
use Illuminate\Support\Facades\File;

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
        $generatedAt = session('otp_generated_at');
    
        if (!$otp || !$email || !$generatedAt) {
            return redirect()->route('auth.login')->withErrors(['otp' => 'OTP session expired.']);
        }
    
        if (time() - $generatedAt > (5 * 60)) {
            return back()->withErrors(['otp' => 'OTP has expired.']);
        }
    
        if ($request->otp == $otp) {
            // OTP valid
            if (session()->has('email_change_user_id')) {
                $user = User::find(session('email_change_user_id'));
                if ($user) {
                    $user->email = $email;
                    $user->save();
                }
    
                session()->forget(['otp', 'otp_email', 'otp_generated_at', 'email_change_user_id']);
    
                return redirect()->route('auth.profile')->with('status', 'Email changed successfully!');
            } else {
                // Verifikasi awal akun
                $user = User::where('email', $email)->first();
                if ($user) {
                    $user->is_verified = true;
                    $user->save();
                }
    
                session()->forget(['otp', 'otp_email', 'otp_generated_at']);
    
                return redirect()->route('auth.login')->with('status', 'Account verified successfully!');
            }
        }
    
        return back()->withErrors(['otp' => 'Invalid OTP code.']);
    }
    
    

    public function resendOtp(Request $request)
    {
        // Ambil email dari session
        $email = session('otp_email');
    
        // Jika tidak ada email di session, arahkan kembali ke login
        if (!$email) {
            return redirect()->route('auth.login')->withErrors(['otp' => 'Session expired. Please register or login again.']);
        }
    
        // Cari user berdasarkan email
        $user = User::where('email', $email)->first();
    
        if (!$user) {
            return redirect()->route('auth.login')->withErrors(['otp' => 'User not found.']);
        }
    
        // Generate OTP baru
        $otp = rand(100000, 999999);
    
        // Kirim OTP via email
        Mail::send('auth.otp-email', ['otp' => $otp], function ($message) use ($user) {
            $message->to($user->email)
                    ->subject('Your New OTP Code');
        });
    
        // Simpan OTP dan waktu pembuatan ke session
        session([
            'otp' => $otp,
            'otp_generated_at' => time(),
        ]);
    
        return back()->with('status', 'A new OTP has been sent to your email.');
    }
    
    public function showLoginForm()
    {
        return view('Auth.login');
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
        return view('Auth.forgot-password');
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
        return view('Auth.reset-password', ['token' => $token]);
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
    public function showEditProfile()
    {
        $files = File::allFiles(public_path('img/avatars'));

        $fileNames = array_map(function ($file) {
            return $file->getFilename();
        }, $files);

        $selectedAvatar = session('selectedAvatar', '1.png');

        return view('Auth.edit-profile', compact('fileNames', 'selectedAvatar'));
    }

    public function updateProfile(Request $request)
    {
        $selectedAvatar = $request->input('avatar');

        session(['selectedAvatar' => $selectedAvatar]);

        return redirect()->route('auth.profile');
    }

    public function updateProfileData(Request $request)
    {
        $user = Auth::user();

        // Validasi data input untuk profil selain email
        $request->validate([
            'name' => 'required|string|max:255',
            'phone_number' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:255',
        ]);

        // Update data profil selain email
        $user->name = $request->name;
        $user->phone_number = $request->phone_number;
        $user->address = $request->address;

        // Simpan perubahan profil tanpa email
        $user->save();

        return redirect()->route('auth.profile')->with('status', 'Profile updated successfully!');
    }

    public function showChangeEmailForm()
    {
        return view('Auth.change-email');
    }


    public function changeEmail(Request $request)
    {
        $request->validate([
            'current_email' => 'required|email',
            'new_email' => 'required|email|unique:users,email',
        ]);
    
        $user = auth()->user();
    
        if ($request->current_email !== $user->email) {
            return back()->withErrors(['current_email' => 'Current email does not match.']);
        }
    
        $otp = rand(100000, 999999);
    
        // Simpan data sementara di session
        session([
            'otp' => $otp,
            'otp_generated_at' => time(),
            'otp_email' => $request->new_email,
            'email_change_user_id' => $user->id, // untuk memastikan hanya dia yang bisa ganti
        ]);
    
        // Kirim OTP ke email baru
        Mail::send('auth.otp-email', ['otp' => $otp], function ($message) use ($request) {
            $message->to($request->new_email)
                ->subject('Your OTP Code for Email Change');
        });
    
        return redirect()->route('auth.verifyOtp')->with('status', 'OTP sent to your new email address.');
    }    
}
