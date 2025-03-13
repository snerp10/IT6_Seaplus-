<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;
use App\Models\OrderDetail;
use App\Models\Product;
use App\Models\Pricing;
use App\Models\SalesReport;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

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

        // Get daily sales data - only include completed orders
        $dailySales = Order::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(total_amount) as total')
            )
            ->whereBetween('created_at', [$startDate, $endDate])
            ->where('order_status', 'Completed') // Only include completed orders
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('date')
            ->get();

        // Calculate total sales from completed orders
        $totalSales = $dailySales->sum('total');

        // Get top products - modified to use order_details and only consider completed orders
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
            ->where('orders.order_status', 'Completed') // Only include completed orders
            ->groupBy('products.prod_id', 'products.name', 'products.category')
            ->orderByDesc('revenue')
            ->limit(10)
            ->get();

        // Get sales by category - only consider completed orders
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
            ->where('orders.order_status', 'Completed') // Only include completed orders
            ->groupBy('products.category')
            ->orderByDesc('total')
            ->get();

        // Get payment methods breakdown - only include Paid payments
        $paymentMethods = Payment::select(
                'pay_method',
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(amount_paid) as total')
            )
            ->whereBetween('pay_date', [$startDate, $endDate])
            ->where('pay_status', 'Paid') // Only include paid payments
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

        // Get orders data - only include completed orders with paid payments
        $orders = Order::join('payments', 'orders.order_id', '=', 'payments.order_id')
            ->join('customers', 'orders.cus_id', '=', 'customers.cus_id')
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
            ->where('orders.order_status', 'Completed') // Only include completed orders
            ->where('payments.pay_status', 'Paid') // Only include paid payments
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
        // Show form to create a new saved report
        return view('admin.sales_reports.create');
    }

    public function store(Request $request)
    {
        // Validate the incoming request with more specific rules
        $validated = $request->validate([
            'report_type' => 'required|in:daily,weekly,monthly,quarterly,yearly',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'date_from' => 'required|date',
            'date_to' => 'required|date|after_or_equal:date_from',
        ]);

        DB::beginTransaction();
        
        try {
            // Get current authenticated user's employee ID
            $currentUser = Auth::user();
            $employeeId = null;
            
            // First try to get from relationship if exists
            if ($currentUser && $currentUser->employee) {
                $employeeId = $currentUser->employee->emp_id;
            } 
            // If no direct relationship, try to find by email
            else if ($currentUser) {
                $employee = Employee::where('email', $currentUser->email)->first();
                if ($employee) {
                    $employeeId = $employee->emp_id;
                }
            }
            
            // If still no employee ID, use a fallback admin employee
            if (!$employeeId) {
                $adminEmployee = Employee::where('position', 'Admin')->first();
                $employeeId = $adminEmployee ? $adminEmployee->emp_id : 1; // Use ID 1 as last resort
            }
            
            // Calculate date ranges based on report type
            $startDate = Carbon::parse($validated['date_from'])->startOfDay();
            $endDate = Carbon::parse($validated['date_to'])->endOfDay();
            
            // Get the report data based on type
            $reportData = [];
            switch ($validated['report_type']) {
                case 'daily':
                    $reportData = $this->getDailySalesData($startDate, $endDate);
                    break;
                case 'weekly':
                    $reportData = $this->getWeeklySalesData($startDate, $endDate);
                    break;
                case 'monthly':
                    $reportData = $this->getMonthlySalesData($startDate, $endDate);
                    break;
                case 'quarterly':
                    $reportData = $this->getQuarterlySalesData($startDate, $endDate);
                    break;
                case 'yearly':
                    $reportData = $this->getYearlySalesData($startDate, $endDate);
                    break;
            }
            
            // Calculate expenses for this period
            $expenses = $this->calculateExpenses($startDate, $endDate);
            
            // Calculate net profit
            $netProfit = $reportData['totalSales'] - $expenses;
            
            // Create the sales report
            $salesReport = SalesReport::create([
                'name' => $validated['name'],
                'description' => $validated['description'],
                'report_type' => $validated['report_type'],
                'date_from' => $startDate,
                'date_to' => $endDate,
                'total_sales' => $reportData['totalSales'],
                'total_expenses' => $expenses,
                'net_profit' => $netProfit,
                'date_generated' => now(),
                'generated_by' => $employeeId, // Ensure generated_by is always populated
                'parameters' => json_encode([
                    'reportType' => $validated['report_type'],
                    'dateRange' => [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')]
                ]),
            ]);
            
            DB::commit();
            
            return redirect()->route('admin.sales_reports.show', $salesReport->report_id)
                ->with('success', 'Sales report has been created successfully');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error creating sales report: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Calculate expenses for a given date range
     */
    private function calculateExpenses($startDate, $endDate)
    {
        // First try to calculate based on original product costs
        $productCosts = DB::table('order_details')
            ->join('products', 'order_details.prod_id', '=', 'products.prod_id')
            ->join('orders', 'order_details.order_id', '=', 'orders.order_id')
            ->join('pricing', function($join) {
                $join->on('products.prod_id', '=', 'pricing.prod_id')
                    ->whereNull('pricing.end_date');
            })
            ->select(DB::raw('SUM(order_details.quantity * pricing.original_price) as cost'))
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->where('orders.order_status', 'Completed')
            ->first();
        
        // If we have product costs, add operational expenses estimate
        if ($productCosts && $productCosts->cost > 0) {
            // Add 15% operational costs (staff, utilities, etc.)
            return $productCosts->cost * 1.15;
        }
        
        // Fallback to a simple estimate based on total sales
        // Use our current cost structure estimate
        $totalSales = Order::whereBetween('created_at', [$startDate, $endDate])
            ->where('order_status', 'Completed')
            ->sum('total_amount');
            
        return $totalSales * 0.65; // Estimate: 65% of sales goes to expenses
    }

    // Update the show method to use report_id primary key
    public function show($id)
    {
        // Find the saved report
        $report = SalesReport::findOrFail($id);
        
        // Get the date range from the report
        $startDate = $report->date_from ? Carbon::parse($report->date_from) : Carbon::parse($report->date_generated)->startOfMonth();
        $endDate = $report->date_to ? Carbon::parse($report->date_to) : Carbon::parse($report->date_generated)->endOfMonth();
        $parameters = json_decode($report->parameters, true) ?? [];
        
        // Get report data based on the report type
        switch ($report->report_type) {
            case 'daily':
                $data = $this->getDailySalesData($startDate, $endDate, $parameters);
                break;
            case 'weekly':
                $data = $this->getWeeklySalesData($startDate, $endDate, $parameters);
                break;
            case 'monthly':
                $data = $this->getMonthlySalesData($startDate, $endDate, $parameters);
                break;
            case 'quarterly':
                $data = $this->getQuarterlySalesData($startDate, $endDate, $parameters);
                break;
            case 'yearly':
                $data = $this->getYearlySalesData($startDate, $endDate, $parameters);
                break;
            default:
                $data = $this->getDailySalesData($startDate, $endDate, $parameters);
        }
        
        return view('admin.sales_reports.show', compact('report', 'data', 'startDate', 'endDate'));
    }

    public function edit($id)
    {
        // Find the saved report
        $report = SalesReport::findOrFail($id);
        
        return view('admin.sales_reports.edit', compact('report'));
    }

    /**
     * Update the specified sales report.
     */
    public function update(Request $request, $id)
    {
        // Find the saved report
        $report = SalesReport::findOrFail($id);
        
        // Validate the incoming request
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'date_from' => 'required|date',
            'date_to' => 'required|date|after_or_equal:date_from',
            'report_type' => 'required|in:daily,weekly,monthly,quarterly,yearly',
            'total_sales' => 'required|numeric|min:0',
            'total_expenses' => 'required|numeric|min:0',
            'net_profit' => 'required|numeric',
        ]);
        
        // Update the report
        $report->update($validated + [
            'parameters' => json_encode($request->except([
                '_token', '_method', 'name', 'description', 'date_from', 'date_to', 
                'report_type', 'total_sales', 'total_expenses', 'net_profit'
            ])),
        ]);
        
        return redirect()->route('admin.sales_reports.show', $report->report_id)
                         ->with('success', 'Sales report updated successfully.');
    }

    /**
     * Remove the specified sales report.
     */
    public function destroy($report_id)
    {
        // Find and delete the report
        $report = SalesReport::findOrFail($report_id);
        $report->delete();
        
        return redirect()->route('admin.sales_reports.index')
                         ->with('success', 'Sales report deleted successfully.');
    }
    
    // Update the savedReports method to eager load relationships for efficiency
    public function savedReports()
    {
        $reports = SalesReport::with(['employee'])
                    ->orderBy('created_at', 'desc')
                    ->paginate(10);
        return view('admin.sales_reports.saved_reports', compact('reports'));
    }
    
    /**
     * Get daily sales data for a report
     */
    private function getDailySalesData($startDate, $endDate, $parameters = [])
    {
        // Get daily sales data - only include completed orders
        $dailySales = Order::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(total_amount) as total')
            )
            ->whereBetween('created_at', [$startDate, $endDate])
            ->where('order_status', 'Completed')
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('date')
            ->get();
            
        // Calculate total sales
        $totalSales = $dailySales->sum('total');
        
        // Get payment methods breakdown
        $paymentMethods = Payment::select(
                'pay_method',
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(amount_paid) as total')
            )
            ->whereBetween('pay_date', [$startDate, $endDate])
            ->where('pay_status', 'Paid')
            ->groupBy('pay_method')
            ->orderByDesc('total')
            ->get();
            
        return [
            'dailySales' => $dailySales,
            'totalSales' => $totalSales,
            'paymentMethods' => $paymentMethods
        ];
    }
    
    /**
     * Get monthly sales data for a report
     */
    private function getMonthlySalesData($startDate, $endDate, $parameters = [])
    {
        // Get monthly sales data
        $monthlySales = Order::select(
                DB::raw('YEAR(created_at) as year'),
                DB::raw('MONTH(created_at) as month'),
                DB::raw('SUM(total_amount) as total')
            )
            ->whereBetween('created_at', [$startDate, $endDate])
            ->where('order_status', 'Completed')
            ->groupBy(DB::raw('YEAR(created_at)'), DB::raw('MONTH(created_at)'))
            ->orderBy('year')
            ->orderBy('month')
            ->get();
            
        $totalSales = $monthlySales->sum('total');
        
        return [
            'monthlySales' => $monthlySales,
            'totalSales' => $totalSales
        ];
    }
    
    /**
     * Get product sales data for a report
     */
    private function getProductSalesData($startDate, $endDate, $parameters = [])
    {
        // Get top products
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
            ->where('orders.order_status', 'Completed')
            ->groupBy('products.prod_id', 'products.name', 'products.category')
            ->orderByDesc('revenue')
            ->limit(isset($parameters['limit']) ? $parameters['limit'] : 20)
            ->get();
            
        $totalSales = $topProducts->sum('revenue');
        
        return [
            'products' => $topProducts,
            'totalSales' => $totalSales
        ];
    }
    
    /**
     * Get category sales data for a report
     */
    private function getCategorySalesData($startDate, $endDate, $parameters = [])
    {
        // Get sales by category
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
            ->where('orders.order_status', 'Completed')
            ->groupBy('products.category')
            ->orderByDesc('total')
            ->get();
            
        $totalSales = $salesByCategory->sum('total');
        
        return [
            'categories' => $salesByCategory,
            'totalSales' => $totalSales
        ];
    }

    // Add weekly sales data method
    private function getWeeklySalesData($startDate, $endDate, $parameters = [])
    {
        // Group sales by week
        $weeklySales = Order::select(
                DB::raw('YEAR(created_at) as year'),
                DB::raw('WEEK(created_at, 1) as week'),
                DB::raw('SUM(total_amount) as total')
            )
            ->whereBetween('created_at', [$startDate, $endDate])
            ->where('order_status', 'Completed')
            ->groupBy(DB::raw('YEAR(created_at)'), DB::raw('WEEK(created_at, 1)'))
            ->orderBy('year')
            ->orderBy('week')
            ->get();
            
        $totalSales = $weeklySales->sum('total');
        
        return [
            'weeklySales' => $weeklySales,
            'totalSales' => $totalSales
        ];
    }

    // Add quarterly sales data method
    private function getQuarterlySalesData($startDate, $endDate, $parameters = [])
    {
        // Group sales by quarter
        $quarterlySales = Order::select(
                DB::raw('YEAR(created_at) as year'),
                DB::raw('QUARTER(created_at) as quarter'),
                DB::raw('SUM(total_amount) as total')
            )
            ->whereBetween('created_at', [$startDate, $endDate])
            ->where('order_status', 'Completed')
            ->groupBy(DB::raw('YEAR(created_at)'), DB::raw('QUARTER(created_at)'))
            ->orderBy('year')
            ->orderBy('quarter')
            ->get();
            
        $totalSales = $quarterlySales->sum('total');
        
        return [
            'quarterlySales' => $quarterlySales,
            'totalSales' => $totalSales
        ];
    }

    // Add yearly sales data method
    private function getYearlySalesData($startDate, $endDate, $parameters = [])
    {
        // Group sales by year
        $yearlySales = Order::select(
                DB::raw('YEAR(created_at) as year'),
                DB::raw('SUM(total_amount) as total')
            )
            ->whereBetween('created_at', [$startDate, $endDate])
            ->where('order_status', 'Completed')
            ->groupBy(DB::raw('YEAR(created_at)'))
            ->orderBy('year')
            ->get();
            
        $totalSales = $yearlySales->sum('total');
        
        return [
            'yearlySales' => $yearlySales,
            'totalSales' => $totalSales
        ];
    }
}
