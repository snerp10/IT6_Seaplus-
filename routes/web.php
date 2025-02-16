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

// Public Routes
Route::get('/', function () {
    return view('welcome');
});

// Guest Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
});

// Authenticated Routes
Route::middleware(['auth'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Customer Routes
    Route::middleware(['Customer'])->prefix('customer')->group(function () {
        // Dashboard
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');
        
        // Products (read-only)
        Route::resource('products', ProductController::class)->only(['index', 'show']);
        
        // Orders
        Route::resource('orders', OrderController::class);
        Route::get('/orders/{order}/track', [OrderController::class, 'track'])->name('orders.track');
        
        // Payments
        Route::get('/orders/{order}/payment', [PaymentController::class, 'create'])->name('orders.payment');
        Route::post('/payments', [PaymentController::class, 'store'])->name('payments.store');
        
        // Profile
        Route::get('/profile', [CustomerController::class, 'show'])->name('customer.profile');
        Route::put('/profile', [CustomerController::class, 'update'])->name('customer.update');
        
        // Delivery Routes
        Route::get('/orders/{order}/delivery/edit', [DeliveryController::class, 'edit'])->name('delivery.edit');
        Route::patch('/orders/{order}/delivery', [DeliveryController::class, 'update'])->name('delivery.update');

        // Additional custom Order routes
        Route::get('/orders/{order}/payment', [OrderController::class, 'create'])->name('orders.payment');
        Route::post('/orders/{order}/payment', [OrderController::class, 'processPayment'])->name('orders.processPayment');

        // GCash Payment Routes
        Route::get('/payments/gcash/redirect', [PaymentController::class, 'gcashRedirect'])->name('payments.gcash.redirect');
        Route::get('/payments/gcash/callback', [PaymentController::class, 'gcashCallback'])->name('payments.gcash.callback');
        Route::post('/payments/gcash/verify', [PaymentController::class, 'gcashVerify'])->name('payments.gcash.verify');
    });

    // Admin Routes
    Route::middleware(['Admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'adminIndex'])->name('dashboard.index');
        Route::resource('products', ProductController::class);
        Route::resource('orders', OrderController::class);
        Route::resource('customers', CustomerController::class);
        Route::resource('payments', PaymentController::class);
        Route::resource('deliveries', DeliveryController::class);
    });
});

