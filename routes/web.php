<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\DeliveryController;

// 🏠 Root Route
Route::get('/', function () {
    return view('welcome');
});

// 🔐 Authentication Routes
Route::get('/login', function () {
    return view('auth.login');
})->name('login');
Route::post('/login', [AuthController::class, 'login']);

Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);

Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');

    // Products (read-only)
    Route::resource('products', ProductController::class)->only(['index', 'show']);

    // Orders Routes (using resource for standard CRUD)
    Route::resource('orders', OrderController::class);

    // Additional custom Order routes
    Route::get('/orders/{order}/track', [OrderController::class, 'track'])->name('orders.track');
    Route::get('/orders/{order}/payment', [OrderController::class, 'create'])->name('orders.payment');
    Route::post('/orders/{order}/payment', [OrderController::class, 'processPayment'])->name('orders.processPayment');

    // Delivery Routes
    // Gumamit tayo ng mas malinaw na URL para sa delivery editing/updating
    Route::get('/orders/{order}/delivery/edit', [DeliveryController::class, 'edit'])->name('delivery.edit');
    Route::patch('/orders/{order}/delivery', [DeliveryController::class, 'update'])->name('delivery.update');

    // Payment Routes
    Route::get('/orders/{order}/payment', [PaymentController::class, 'create'])->name('orders.payment');
    Route::post('/payments', [PaymentController::class, 'store'])->name('payments.store');
    Route::post('/orders/{order}/payment', [PaymentController::class, 'processPayment'])->name('orders.processPayment');

    // GCash Payment Routes
    Route::get('/payments/gcash/redirect', [PaymentController::class, 'gcashRedirect'])->name('payments.gcash.redirect');
    Route::get('/payments/gcash/callback', [PaymentController::class, 'gcashCallback'])->name('payments.gcash.callback');
    Route::post('/payments/gcash/verify', [PaymentController::class, 'gcashVerify'])->name('payments.gcash.verify');

    // Customer Profile
    Route::get('/profile', [CustomerController::class, 'show'])->name('customer.profile');
    Route::put('/profile', [CustomerController::class, 'update'])->name('customer.update');
});
