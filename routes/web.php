<?php

use App\Http\Controllers\AuthController;
use App\Http\Middleware\CheckAuthenticated;
use Illuminate\Support\Facades\Route;

Route::middleware(CheckAuthenticated::class)->group(function () {
    Route::get('/', function () {
        return view('Dashboard.index');
    });
});

// Auth Route
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('auth.register');
Route::post('/register', [AuthController::class, 'register']);
Route::get('/verify-otp', [AuthController::class, 'showOtpForm'])->name('auth.verifyOtp');
Route::post('/verify-otp', [AuthController::class, 'verifyOtp']);
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('auth.login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/forgot-password', [AuthController::class, 'showForgotPasswordForm'])->name('password.request');
Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->name('password.email');
Route::get('/reset-password/{token}', [AuthController::class, 'showResetPasswordForm'])->name('password.reset');
Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');