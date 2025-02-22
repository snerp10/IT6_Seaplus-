<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Product;
use App\Models\Delivery;
use App\Models\SalesReport;
use App\Http\Controllers\Controller;

class AdminDashboardController extends Controller
{
    public function index()
    {
        return view('admin.dashboard.index');
    }
    public function adminIndex()
    {
        $data = [
            'totalOrders' => Order::count(),
            'totalProducts' => Product::count(),
            'totalSales' => SalesReport::sum('total_sales'),
            'pendingDeliveries' => Delivery::where('delivery_status')->count(),
        ];

        return view('admin.dashboard.index', $data);
    }
}
