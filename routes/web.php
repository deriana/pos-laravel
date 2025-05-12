<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CategoriesController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\ProductsController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\SaleController;
use App\Http\Middleware\CheckAuthenticated;
use App\Http\Middleware\CheckVerified;
use Illuminate\Support\Facades\Route;

Route::middleware([CheckAuthenticated::class, CheckVerified::class])->group(function () {
    Route::get('/', function () {
        return view('Dashboard.index');
    });

    Route::resource('/products', ProductsController::class);

    Route::fallback(function () {
        return view('error.not-found');
    });

    Route::get('storage/images/{filename}', [ImageController::class, 'showImage']);
    Route::get('storage/qr/{filename}', [ImageController::class, 'showQrCode']);
    Route::resource('/categories', CategoriesController::class);
    Route::get('/edit-profile', [AuthController::class, 'showEditProfile'])->name('auth.profile');
    Route::post('/edit-profile', [AuthController::class, 'updateProfile'])->name('auth.updateProfile');
    Route::put('/edit-profile', [AuthController::class, 'updateProfileData'])->name('auth.updateProfile');
    Route::get('/change-email', [AuthController::class, 'showChangeEmailForm'])->name('auth.changeEmail');
    Route::post('/change-email', [AuthController::class, 'changeEmail'])->name('auth.changeEmailPost');
    Route::resource('/purchases', PurchaseController::class);
    Route::resource('/sales', SaleController::class);
    Route::get('sales/{id}/receipt', [SaleController::class, 'showReceipt'])->name('sales.receipt');
    Route::get('/sales/receipt-view/{id}', [SaleController::class, 'viewReceipt'])->name('sales.receipt.view');
    Route::post('/sale/{id}/pay-debt', [SaleController::class, 'payDebt'])->name('sale.pay.debt');
    Route::get('purchases/{id}/receipt', [PurchaseController::class, 'showReceipt'])->name('purchases.receipt');
    Route::get('/purchases/receipt-view/{id}', [PurchaseController::class, 'viewReceipt'])->name('purchases.receipt.view');
    Route::post('/purchase/{id}/pay-debt', [PurchaseController::class, 'payDebt'])->name('purchase.pay.debt');
    Route::post('/checkout', [CartController::class, 'store'])->name('cart.store');
});


// Auth Route
Route::middleware('guest')->group(function () {
    // Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('auth.register');
    // Route::post('/register', [AuthController::class, 'register']);
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('auth.login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::get('/verify-otp', [AuthController::class, 'showOtpForm'])->name('auth.verifyOtp');
Route::post('/verify-otp', [AuthController::class, 'verifyOtp']);
Route::get('/forgot-password', [AuthController::class, 'showForgotPasswordForm'])->name('password.request');
Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->name('password.email');
Route::get('/reset-password/{token}', [AuthController::class, 'showResetPasswordForm'])->name('password.reset');
Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::post('/resend-otp', [AuthController::class, 'resendOtp']);