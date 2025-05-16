<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CategoriesController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\InventoryReportController;
use App\Http\Controllers\ProductsController;
use App\Http\Controllers\ProfitReportController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\PurchaseReportController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\SalesReportController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\CheckAuthenticated;
use App\Http\Middleware\CheckRole;
use App\Http\Middleware\CheckVerified;
use Illuminate\Support\Facades\Route;

Route::middleware([CheckAuthenticated::class])->group(function () {
    Route::get('/', [DashboardController::class, 'showDashboard'])->name('dashboard');

    Route::fallback(function () {
        return view('errors.not-found');
    });

    Route::get('storage/images/{filename}', [ImageController::class, 'showImage']);
    Route::get('storage/qr/{filename}', [ImageController::class, 'showQrCode']);
    Route::resource('/categories', CategoriesController::class);
    Route::get('/edit-profile', [AuthController::class, 'showEditProfile'])->name('auth.profile');
    Route::post('/edit-profile', [AuthController::class, 'updateProfile'])->name('auth.updateProfile');
    Route::put('/edit-profile', [AuthController::class, 'updateProfileData'])->name('auth.updateProfile');
    Route::get('/change-email', [AuthController::class, 'showChangeEmailForm'])->name('auth.changeEmail');

    Route::post('/change-email', [AuthController::class, 'changeEmail'])->name('auth.changeEmailPost');


    // Route::post('/checkout', [CartController::class, 'store'])->name('cart.store');
    Route::resource('/sales', SaleController::class)->except('show');

    Route::get('sales/{id}/receipt', [SaleController::class, 'showReceipt'])->name('sales.receipt');
    Route::get('/sales/receipt-view/{id}', [SaleController::class, 'viewReceipt'])->name('sales.receipt.view');
    Route::post('/sale/{id}/pay-debt', [SaleController::class, 'payDebt'])->name('sale.pay.debt');

    Route::resource('/purchases', PurchaseController::class)->except('show');

    Route::get('purchases/{id}/receipt', [PurchaseController::class, 'showReceipt'])->name('purchases.receipt');
    Route::get('/purchases/receipt-view/{id}', [PurchaseController::class, 'viewReceipt'])->name('purchases.receipt.view');
    Route::post('/purchase/{id}/pay-debt', [PurchaseController::class, 'payDebt'])->name('purchase.pay.debt');
    Route::get('/purchase/debt/{id}/confirm-payment', [PurchaseController::class, 'showDebtPayment'])->name('debt.confirmPayment');
    Route::get('/purchases/confirmation/{id}', [PurchaseController::class, 'showConfirmation'])->name('purchases.confirmation');
    Route::post('/purchases/confirmation/{id}', [PurchaseController::class, 'confirmation'])->name('confirmation.purchase.transaction');

    Route::middleware([CheckRole::class])->group(function () {
        Route::resource('/suppliers', SupplierController::class);
        Route::resource('/customers', CustomerController::class);
        Route::resource('/users', UserController::class);
        Route::resource('/products', ProductsController::class);

        Route::put('/users/{id}/change-password', [UserController::class, 'changePassword'])->name('users.change-password');

        Route::prefix('reports')->name('reports.')->group(function () {
            Route::get('/sales', [SalesReportController::class, 'index'])->name('sales');
            Route::get('/sales/export', [SalesReportController::class, 'export'])->name('sales.export');
            Route::get('/purchases', [PurchaseReportController::class, 'index'])->name('purchases');
            Route::get('/purchases/export', [PurchaseReportController::class, 'export'])->name('purchase.export');
            Route::get('/inventory', [InventoryReportController::class, 'index'])->name('inventory');
            // Route::get('/customers', fn() => view('Reports.customers'))->name('customers');
            // Route::get('/suppliers', fn() => view('Reports.suppliers'))->name('suppliers');
            Route::get('/profit', [ProfitReportController::class, 'index'])->name('profit');
            Route::get('/profit/export', [ProfitReportController::class, 'export'])->name('profit.export');
        });
    });
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
