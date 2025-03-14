<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\DeliveryController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\SalesReportController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminProductController;
use App\Http\Controllers\Admin\AdminOrderController;
use App\Http\Controllers\Admin\AdminEmployeeController;
use App\Http\Controllers\Admin\AdminCustomerController;
use App\Http\Controllers\Admin\AdminDeliveryController;
use App\Http\Controllers\Admin\AdminInventoryController;
use App\Http\Controllers\Admin\AdminPaymentController;
use App\Http\Controllers\Admin\AdminSalesReportController;
use App\Http\Controllers\Admin\AdminSupplierController;


// Public Routes
Route::get('/', function () {
    return view('welcome');
});

// Static Pages
Route::get('/about', function () {
    return view('pages.about');
})->name('about');

Route::get('/contact', function () {
    return view('pages.contact');
})->name('contact');

Route::get('/terms', function () {
    return view('auth.terms');
})->name('terms');

Route::get('/privacy', function () {
    return view('auth.privacy');
})->name('privacy');

// Guest Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
    Route::get('/search', [ProductController::class, 'search'])->name('search');
});

// Authenticated Routes
Route::middleware(['auth'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Customer Routes
    Route::prefix('customer')->name('customer.')->middleware(['Customer'])->group(function () {
        // Dashboard - make sure it's accessible at /dashboard
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');
        
        // Products (read-only)
        Route::get('/search', [ProductController::class, 'search'])->name('products.search');
        Route::resource('products', ProductController::class)->only(['index', 'show']);
        
        // Orders
        Route::delete('orders/{order}', [OrderController::class, 'destroy'])->name('orders.destroy');
        Route::resource('orders', OrderController::class);
        
        // Payments
        Route::get('/orders/{order}/payment', [PaymentController::class, 'create'])->name('orders.payment');
        Route::post('/payments', [PaymentController::class, 'store'])->name('payments.store');
        Route::get('/invoices/{order}', [PaymentController::class, 'showInvoice'])->name('invoices.show');
        Route::get('/invoices/{order}/download', [PaymentController::class, 'downloadInvoice'])->name('invoices.download');
        
        // Profile - Fix these routes to be consistent
        Route::get('/profile', [CustomerController::class, 'show'])->name('profile');
        Route::put('/profile', [CustomerController::class, 'update'])->name('profile.update');
        
        // Delivery Routes
        Route::get('/orders/{order}/delivery/edit', [DeliveryController::class, 'edit'])->name('delivery.edit');
        Route::patch('/orders/{order}/delivery', [DeliveryController::class, 'update'])->name('delivery.update');

        // Additional custom Order routes
        Route::post('/orders/{order}/payment', [OrderController::class, 'processPayment'])->name('orders.processPayment');
    });

    // Admin Routes
    Route::middleware(['Admin'])->prefix('admin')->name('admin.')->group(function () {
        // Admin Dashboard
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
        Route::post('/dashboard/notifications/{id}/mark-read', [AdminDashboardController::class, 'markNotificationAsRead'])->name('dashboard.notifications.mark-read');

        // Manage Products
        Route::resource('products', AdminProductController::class);

        // Manage Orders - IMPORTANT: Define specific routes BEFORE the resource route
        Route::post('orders/calculate', [AdminOrderController::class, 'calculate'])->name('orders.calculate');
        Route::get('orders/dashboard', [AdminOrderController::class, 'dashboard'])->name('orders.dashboard');
        Route::get('orders/export', [AdminOrderController::class, 'export'])->name('orders.export');
        Route::post('orders/{order}/status', [AdminOrderController::class, 'updateStatus'])->name('orders.update-status');
        Route::post('orders/{order}/payment', [AdminOrderController::class, 'addPayment'])->name('orders.add-payment');
        
        // Now define the resource route WITHOUT conflicting with the above specific routes
        Route::resource('orders', AdminOrderController::class)->except(['store']);
        // Define store separately to avoid conflicts with calculate route
        Route::post('orders', [AdminOrderController::class, 'store'])->name('orders.store');

        // Manage Employees
        
        Route::resource('employees', AdminEmployeeController::class);

        // Manage Customers
        Route::get('customers/export', [AdminCustomerController::class, 'export'])->name('customers.export');
        Route::resource('customers', AdminCustomerController::class);

        // Manage Deliveries
        Route::get('/deliveries/export', [AdminDeliveryController::class, 'export'])->name('deliveries.export');
        Route::get('/deliveries/monitoring', [AdminDeliveryController::class, 'monitoring'])->name('deliveries.monitoring');
        Route::put('/deliveries/{delivery}/quick-update', [AdminDeliveryController::class, 'quickUpdate'])->name('deliveries.quick_update');
        Route::resource('deliveries', AdminDeliveryController::class);
        

        // Manage Inventory
        Route::get('inventories/lowStockAlerts', [AdminInventoryController::class, 'lowStockAlerts'])->name('inventories.low_stock_alerts');
        Route::get('inventories/export', [AdminInventoryController::class, 'export'])->name('inventories.export');
        Route::get('inventories/stockHistory', [AdminInventoryController::class, 'stockHistory'])->name('inventories.stock_history');

        Route::resource('inventories', AdminInventoryController::class);
        
        // Manage Payments
        Route::post('payments/create-from-order/{order}', [AdminPaymentController::class, 'createFromOrder'])->name('payments.create_from_order');
        Route::get('payments/reports', [AdminPaymentController::class, 'reports'])->name('payments.reports');
        Route::get('payments/export', [AdminPaymentController::class, 'export'])->name('payments.export');
        Route::post('payments/{payment}/change-status', [AdminPaymentController::class, 'changeStatus'])->name('payments.change-status');
        Route::resource('payments', AdminPaymentController::class);


        // Manage Sales Reports
        Route::get('sales_reports/export', [AdminSalesReportController::class, 'export'])->name('sales_reports.export');
        Route::get('sales_reports/saved', [AdminSalesReportController::class, 'savedReports'])->name('sales_reports.saved');
        Route::resource('sales_reports', AdminSalesReportController::class);

        // Manage Suppliers
        Route::get('suppliers/export', [AdminSupplierController::class, 'export'])->name('suppliers.export');
        
        Route::resource('suppliers', AdminSupplierController::class);
    });
});

// Add this outside any middleware groups for testing
Route::get('/test-dashboard', [App\Http\Controllers\DashboardController::class, 'index']);

// GCash verification route
Route::post('/payments/verify-gcash', [PaymentController::class, 'verifyGcashPayment'])
    ->name('payments.verify-gcash');

