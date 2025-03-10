<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Product;
use App\Models\Delivery;
use App\Models\Payment;
use App\Models\OrderDetail;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        // Get authenticated user and ensure we have a valid customer ID
        $user = Auth::user();
        if (!$user || !$user->customer || !$user->customer->cus_id) {
            // Log error and redirect to login if customer relationship is not found
            \Log::error('User without valid customer relationship attempted to access dashboard', ['user_id' => $user->id ?? 'unknown']);
            return redirect()->route('login');
        }
        
        $customerId = $user->customer->cus_id;
        
        // Get recent orders with necessary relationships - explicitly filter by customer ID
        $recentOrders = Order::where('cus_id', $customerId)
            ->with(['orderDetails.product', 'delivery', 'payments'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
            
        // Get order statistics with proper financial calculations
        $orderStats = [
            'total_orders' => Order::where('cus_id', $customerId)->count(),
            'completed_orders' => Order::where('cus_id', $customerId)
                ->where('order_status', 'Completed')
                ->count(),
            'processing_orders' => Order::where('cus_id', $customerId)
                ->whereIn('order_status', ['Pending', 'Processing'])
                ->count(),
            'total_spent' => Order::where('cus_id', $customerId)
                ->where('order_status', 'Completed')
                ->sum('total_amount')
        ];
        
        // Get upcoming deliveries
        $upcomingDeliveries = Delivery::whereHas('order', function($query) use ($customerId) {
            $query->where('cus_id', $customerId);
        })
        ->whereIn('delivery_status', ['Pending', 'Scheduled', 'Out for Delivery'])
        ->with('order') // Eager load order relationship
        ->orderBy('delivery_date')
        ->limit(3)
        ->get();
        
        // Get pending payments with proper sorting and relationship loading
        $pendingPayments = Payment::whereHas('order', function($query) use ($customerId) {
            $query->where('cus_id', $customerId);
        })
        ->where('pay_status', '!=', 'Paid')
        ->with('order') // Eager load order relationship
        ->orderBy('pay_date')
        ->limit(3)
        ->get();
        
        // Get product categories for quick navigation
        $productCategories = Product::select('category')
            ->distinct()
            ->orderBy('category')
            ->get()
            ->pluck('category');
            
        // Get personalized product recommendations based on order history
        $recommendedProducts = $this->getRecommendedProducts($customerId);
        
        return view('customer.dashboard.index', compact(
            'recentOrders',
            'orderStats',
            'upcomingDeliveries',
            'pendingPayments',
            'productCategories',
            'recommendedProducts'
        ));
    }
    
    /**
     * Get personalized product recommendations for the customer
     */
    private function getRecommendedProducts($customerId)
    {
        // Get products from customer's order history
        $orderHistoryProducts = OrderDetail::whereHas('order', function($query) use ($customerId) {
            $query->where('cus_id', $customerId);
        })->pluck('prod_id');
        
        // If customer has order history, recommend similar products in the same category
        if($orderHistoryProducts->count() > 0) {
            $categories = Product::whereIn('prod_id', $orderHistoryProducts)
                ->pluck('category')
                ->unique();
                
            return Product::whereIn('category', $categories)
                ->where('status', 'Active')
                ->whereNotIn('prod_id', $orderHistoryProducts)
                ->with('pricing')
                ->inRandomOrder()
                ->limit(3)
                ->get();
        }
        
        // Otherwise, return bestselling products
        return Product::whereHas('orderDetails')
            ->where('status', 'Active')
            ->with('pricing')
            ->withCount('orderDetails')
            ->orderBy('order_details_count', 'desc')
            ->limit(3)
            ->get();
    }
}
