<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;
use App\Models\OrderDetail;
use App\Models\Product;
use App\Models\Pricing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
// No Excel import needed

class AdminSalesReportController extends Controller
{
    public function index(Request $request)
    {
        // Get date range from request or default to last 30 days
        $startDate = $request->input('date_from') 
            ? Carbon::parse($request->input('date_from'))
            : Carbon::now()->subDays(30);
            
        $endDate = $request->input('date_to')
            ? Carbon::parse($request->input('date_to'))->endOfDay()
            : Carbon::now()->endOfDay();

        // Get daily sales data
        $dailySales = Order::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(total_amount) as total')
            )
            ->whereBetween('created_at', [$startDate, $endDate])
            ->where('order_status', '!=', 'Cancelled')
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('date')
            ->get();

        // Calculate total sales
        $totalSales = $dailySales->sum('total');

        // Get top products - modified to use order_details and pricing tables
        $topProducts = DB::table('order_details')
            ->join('products', 'order_details.prod_id', '=', 'products.prod_id')
            ->join('orders', 'order_details.order_id', '=', 'orders.order_id')
            ->join(DB::raw('(SELECT prod_id, MAX(price_id) as latest_pricing_id FROM pricing GROUP BY prod_id) as latest_pricing'), function($join) {
                $join->on('products.prod_id', '=', 'latest_pricing.prod_id');
            })
            ->join('pricing', 'latest_pricing.latest_pricing_id', '=', 'pricing.price_id')
            ->select(
                'products.name',
                'products.category',
                DB::raw('SUM(order_details.quantity) as units_sold'),
                DB::raw('SUM(order_details.quantity * pricing.selling_price) as revenue')
            )
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->where('orders.order_status', '!=', 'Cancelled')
            ->groupBy('products.prod_id', 'products.name', 'products.category')
            ->orderByDesc('revenue')
            ->limit(10)
            ->get();

        // Get sales by category - modified to use order_details and pricing tables
        $salesByCategory = DB::table('order_details')
            ->join('products', 'order_details.prod_id', '=', 'products.prod_id')
            ->join('orders', 'order_details.order_id', '=', 'orders.order_id')
            ->join(DB::raw('(SELECT prod_id, MAX(price_id) as latest_pricing_id FROM pricing GROUP BY prod_id) as latest_pricing'), function($join) {
                $join->on('products.prod_id', '=', 'latest_pricing.prod_id');
            })
            ->join('pricing', 'latest_pricing.latest_pricing_id', '=', 'pricing.price_id')
            ->select(
                'products.category',
                DB::raw('SUM(order_details.quantity * pricing.selling_price) as total')
            )
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->where('orders.order_status', '!=', 'Cancelled')
            ->groupBy('products.category')
            ->orderByDesc('total')
            ->get();

        // Get payment methods breakdown
        $paymentMethods = Payment::select(
                'pay_method',
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(amount_paid) as total')
            )
            ->whereBetween('pay_date', [$startDate, $endDate])
            ->where('pay_status', '=', 'Paid')
            ->groupBy('pay_method')
            ->orderByDesc('total')
            ->get();

        return view('admin.sales_reports.index', compact(
            'totalSales', 
            'dailySales', 
            'topProducts', 
            'salesByCategory', 
            'paymentMethods',
            'startDate',
            'endDate'
        ));
    }

    public function export(Request $request)
    {
        // Simple CSV export instead of using Excel package
        $startDate = $request->input('date_from') 
            ? Carbon::parse($request->input('date_from'))
            : Carbon::now()->subDays(30);
            
        $endDate = $request->input('date_to')
            ? Carbon::parse($request->input('date_to'))->endOfDay()
            : Carbon::now()->endOfDay();

        // Get orders data
        $orders = Order::join('payments', 'orders.order_id', '=', 'payments.order_id')
            ->join('customers', 'orders.cust_id', '=', 'customers.cust_id')
            ->select(
                'orders.order_id',
                'orders.created_at',
                'orders.total_amount',
                'orders.order_status',
                'customers.fname',
                'customers.lname',
                'payments.pay_method',
                'payments.pay_status',
                'payments.amount_paid'
            )
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->where('orders.order_status', '!=', 'Cancelled')
            ->orderBy('orders.created_at', 'desc')
            ->get();

        // CSV export logic
        $headers = [
            'Order ID', 'Date', 'Customer', 'Total Amount',
            'Payment Method', 'Payment Status', 'Amount Paid', 'Order Status'
        ];
        
        $callback = function() use($orders, $headers) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $headers);
            
            foreach ($orders as $order) {
                fputcsv($file, [
                    $order->order_id,
                    Carbon::parse($order->created_at)->format('M d, Y h:i A'),
                    $order->fname . ' ' . $order->lname,
                    number_format($order->total_amount, 2),
                    $order->pay_method,
                    $order->pay_status,
                    number_format($order->amount_paid, 2),
                    $order->order_status
                ]);
            }
            
            fclose($file);
        };
        
        $filename = 'sales_report_' . $startDate->format('Y-m-d') . '_to_' . $endDate->format('Y-m-d') . '.csv';
        
        return response()->stream($callback, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    public function create()
    {
        // Not used in this implementation, but required for resource controller
        return redirect()->route('admin.sales_reports.index');
    }

    public function store(Request $request)
    {
        // Not used in this implementation, but required for resource controller
        return redirect()->route('admin.sales_reports.index');
    }

    public function show($id)
    {
        // Not used in this implementation, but required for resource controller
        return redirect()->route('admin.sales_reports.index');
    }

    public function edit($id)
    {
        // Not used in this implementation, but required for resource controller
        return redirect()->route('admin.sales_reports.index');
    }

    public function update(Request $request, $id)
    {
        // Not used in this implementation, but required for resource controller
        return redirect()->route('admin.sales_reports.index');
    }

    public function destroy($id)
    {
        // Not used in this implementation, but required for resource controller
        return redirect()->route('admin.sales_reports.index');
    }
}
