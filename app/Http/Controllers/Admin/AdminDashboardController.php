<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Product;
use App\Models\Customer;
use App\Models\Delivery;
use App\Models\Notification;
use App\Models\Employee;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdminDashboardController extends Controller
{
    public function index()
    {
        // Get basic stats
        $totalOrders = Order::count();
        $totalCustomers = Customer::count();
        $totalProducts = Product::count();
        $totalEmployees = Employee::count();
        
        // Get sales analytics from view
        $salesAnalytics = DB::table('vw_sales_analytics')
            ->select(DB::raw('sale_date, SUM(revenue) as daily_revenue, SUM(order_count) as daily_orders'))
            ->where('sale_date', '>=', Carbon::now()->subDays(30))
            ->groupBy('sale_date')
            ->orderBy('sale_date')
            ->get();
            
        // Get inventory analytics from view
        $inventoryAnalytics = DB::table('vw_inventory_analytics')
            ->where('stock_status', 'Low')
            ->limit(5)
            ->get();
            
        // Get sales by category
        $salesByCategory = DB::table('vw_sales_analytics')
            ->select('product_category', DB::raw('SUM(revenue) as category_revenue'))
            ->where('sale_date', '>=', Carbon::now()->subDays(30))
            ->groupBy('product_category')
            ->orderBy('category_revenue', 'desc')
            ->get();
            
        // Get monthly revenue trend
        $monthlyRevenue = DB::table('vw_sales_analytics')
            ->select(DB::raw('DATE_FORMAT(sale_date, "%Y-%m") as month, SUM(revenue) as monthly_revenue'))
            ->where('sale_date', '>=', Carbon::now()->subMonths(6))
            ->groupBy(DB::raw('DATE_FORMAT(sale_date, "%Y-%m")'))
            ->orderBy('month')
            ->get();
            
        // Get recent orders
        $recentOrders = Order::with(['customer'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
            
        // Get pending deliveries
        $pendingDeliveries = Delivery::where('delivery_status', 'Pending')
            ->orWhere('delivery_status', 'Scheduled')
            ->count();
            
        // Get today's revenue
        $todayRevenue = DB::table('vw_sales_analytics')
            ->where('sale_date', Carbon::today()->toDateString())
            ->sum('revenue');
            
        // Get latest notifications from triggers
        $notifications = Notification::orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
        
        // Format data for charts
        $chartData = [
            'dates' => $salesAnalytics->pluck('sale_date'),
            'revenues' => $salesAnalytics->pluck('daily_revenue'),
            'orders' => $salesAnalytics->pluck('daily_orders'),
            'categories' => $salesByCategory->pluck('product_category'),
            'categoryRevenues' => $salesByCategory->pluck('category_revenue'),
            'months' => $monthlyRevenue->pluck('month'),
            'monthlyRevenues' => $monthlyRevenue->pluck('monthly_revenue'),
        ];
        
        // Weekly growth calculation
        $currentWeekRevenue = Order::where('created_at', '>=', Carbon::now()->startOfWeek())
            ->where('order_status', 'Completed')
            ->sum('total_amount');
            
        $lastWeekRevenue = Order::where('created_at', '>=', Carbon::now()->subWeek()->startOfWeek())
            ->where('created_at', '<', Carbon::now()->startOfWeek())
            ->where('order_status', 'Completed')
            ->sum('total_amount');
            
        $weeklyGrowth = $lastWeekRevenue > 0 
            ? (($currentWeekRevenue - $lastWeekRevenue) / $lastWeekRevenue) * 100 
            : 100;

        // Return data to view
        return view('admin.dashboard.index', [
            'totalOrders' => $totalOrders,
            'totalCustomers' => $totalCustomers,
            'totalProducts' => $totalProducts,
            'totalEmployees' => $totalEmployees,
            'pendingDeliveries' => $pendingDeliveries,
            'todayRevenue' => $todayRevenue,
            'weeklyGrowth' => $weeklyGrowth,
            'recentOrders' => $recentOrders,
            'lowStockItems' => $inventoryAnalytics,
            'salesByCategory' => $salesByCategory,
            'notifications' => $notifications,
            'chartData' => $chartData,
        ]);
    }
    
    public function markNotificationAsRead($id)
    {
        $notification = Notification::findOrFail($id);
        $notification->is_read = true;
        $notification->save();
        
        return back()->with('success', 'Notification marked as read');
    }
}
