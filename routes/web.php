<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\StockEntryController;
use App\Http\Controllers\TransactionItemController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CashierController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\FinancialController;

// Public routes
Route::get('/', function () {
    return redirect()->route('login');
})->name('home');

// Auth routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('auth.login');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Protected routes (require authentication)
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // User profile
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');

    Route::get('/kasir', [CashierController::class, 'index'])->name('cashier');
    Route::get('/kasir/find', [CashierController::class, 'find'])->name('cashier.find');
    Route::post('/kasir/checkout', [CashierController::class, 'store'])->name('cashier.store');

    Route::resource('products', ProductController::class);
    Route::resource('transactions', TransactionController::class)->only(['index', 'show']);
    Route::resource('stock-entries', StockEntryController::class);
    Route::resource('transaction-items', TransactionItemController::class)->only(['index', 'show']);
    Route::resource('users', UserController::class);

    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/financial', [FinancialController::class, 'index'])->name('financial.index');
});
