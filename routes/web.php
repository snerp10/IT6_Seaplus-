<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\PaymentController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);

Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');
    
    // Products
    Route::resource('products', ProductController::class)->only(['index', 'show']);
    
    // Cart
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/add', [CartController::class, 'addToCart'])->name('cart.add');
    Route::delete('/cart/remove/{id}', [CartController::class, 'removeFromCart'])->name('cart.remove');
    Route::put('/cart/update/{id}', [CartController::class, 'updateCart'])->name('cart.update');
    
    // Orders
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/create', [OrderController::class, 'create'])->name('orders.create');
    Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');
    Route::get('/orders/{order}/edit', [OrderController::class, 'edit'])->name('orders.edit');
    Route::put('/orders/{order}', [OrderController::class, 'update'])->name('orders.update');
    Route::delete('/orders/{order}', [OrderController::class, 'destroy'])->name('orders.destroy');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::get('/orders/{order}/track', [OrderController::class, 'track'])->name('orders.track');
    Route::get('/orders/{order}/payment', [PaymentController::class, 'create'])->name('orders.payment');
    Route::post('/payments', [PaymentController::class, 'store'])->name('payments.store');

    Route::post('/orders/{order}/payment', [OrderController::class, 'processPayment'])->name('orders.payment');
    
    // GCash Payment Routes
    Route::get('/payments/gcash/redirect', [PaymentController::class, 'gcashRedirect'])->name('payments.gcash.redirect');
    Route::get('/payments/gcash/callback', [PaymentController::class, 'gcashCallback'])->name('payments.gcash.callback');
    Route::post('/payments/gcash/verify', [PaymentController::class, 'gcashVerify'])->name('payments.gcash.verify');
    
    // Customer Profile
    Route::get('/profile', [CustomerController::class, 'show'])->name('customer.profile');
    Route::put('/profile', [CustomerController::class, 'update'])->name('customer.update');
});
