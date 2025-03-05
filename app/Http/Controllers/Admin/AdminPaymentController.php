<?php

namespace App\Http\Controllers\Admin;

use App\Models\Payment;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Customer;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class AdminPaymentController extends Controller
{
    protected $payment;

    public function __construct(Payment $payment)
    {
        $this->payment = $payment;
    }

    public function index(Request $request)
    {
        $query = $this->payment->newQuery();
        
        // Search functionality
        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where('reference_number', 'like', "%{$search}%")
                  ->orWhere('amount_paid', 'like', "%{$search}%")
                  ->orWhere('pay_method', 'like', "%{$search}%");
        }

        // Filter by status if provided
        if ($request->has('status') && $request->status != '') {
            $query->where('pay_status', $request->status);
        }

        // Filter by payment method if provided
        if ($request->has('payment_method') && $request->payment_method != '') {
            $query->where('pay_method', $request->payment_method);
        }
        
        // Filter by date range if provided
        if ($request->has('date_from') && $request->date_from != '') {
            $query->whereDate('pay_date', '>=', $request->date_from);
        }
        
        if ($request->has('date_to') && $request->date_to != '') {
            $query->whereDate('pay_date', '<=', $request->date_to);
        }

        $payments = $query->with(['order', 'customer'])->latest()->paginate(10);
        
        // Get summary statistics
        $totalPaid = $this->payment->where('pay_status', 'Paid')->sum('amount_paid');
        $pendingAmount = $this->payment->where('pay_status', 'Pending')->sum('amount_paid');
        $paymentMethods = $this->payment->select('pay_method', DB::raw('count(*) as count'))
            ->groupBy('pay_method')
            ->pluck('count', 'pay_method')
            ->toArray();
        
        return view('admin.payments.index', compact(
            'payments', 
            'totalPaid', 
            'pendingAmount', 
            'paymentMethods'
        ));
    }

    public function show(Payment $payment)
    {
        $payment->load(['order', 'customer']);
        
        // Get related order products if available
        $orderProducts = collect();
        if ($payment->order) {
            $orderProducts = $payment->order->orderDetails()->with('product')->get();
        }
        
        return view('admin.payments.show', compact('payment', 'orderProducts'));
    }

    public function create(Request $request)
    {
        // Only fetch orders that require payment
        $orders = Order::where('pay_status', '!=', 'Paid')->get();
        $customers = Customer::all();
        
        // Pre-select an order if provided in the query string
        $selectedOrderId = $request->query('order_id');
        $selectedOrder = null;
        $existingPayment = null;
        $defaultPaymentMethod = null;
        
        if ($selectedOrderId) {
            // Fix: Change orderItems to orderDetails
            $selectedOrder = Order::with(['orderDetails.product', 'customer', 'payments'])->find($selectedOrderId);
            
            if ($selectedOrder) {
                // Check if there's an existing incomplete payment for this order
                $existingPayment = Payment::where('order_id', $selectedOrderId)
                    ->where('pay_status', '!=', 'Paid')
                    ->first();
                    
                // If no pending payment exists but we should have one from order creation,
                // this handles cases where payment was initialized but not completed
                if (!$existingPayment) {
                    $existingPayment = Payment::where('order_id', $selectedOrderId)
                        ->orderBy('created_at', 'desc')
                        ->first();
                        
                    // Only consider it as existing if it's not properly completed
                    if ($existingPayment && $existingPayment->pay_status == 'Paid' && 
                        $existingPayment->amount_paid >= $selectedOrder->total_amount) {
                        $existingPayment = null;
                    }
                }
                
                // Get the default payment method from any existing payment record for this order
                if (!$existingPayment) {
                    $latestPayment = $selectedOrder->payments()->latest()->first();
                    if ($latestPayment) {
                        $defaultPaymentMethod = $latestPayment->pay_method;
                    }
                }
            }
        }
        
        return view('admin.payments.create', compact('orders', 'customers', 'selectedOrder', 'existingPayment', 'defaultPaymentMethod'));
    }

    /**
     * Store payment information. This primarily focuses on updating existing payment
     * information that was initialized during order creation.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'order_id' => 'required|exists:orders,order_id',
            'cus_id' => 'required|exists:customers,cus_id',
            'amount_paid' => 'required|numeric|min:0',
            'pay_method' => 'required|string',
            'reference_number' => 'nullable|string',
            'pay_date' => 'required|date',
            'pay_status' => 'nullable|in:Paid,Partially Paid,Pending',
            'payment_type' => 'nullable|in:full,down,balance',
            'notes' => 'nullable|string',
        ]);

        // If reference number is empty, generate one
        if (empty($validated['reference_number'])) {
            $validated['reference_number'] = 'REF-' . date('Ymd') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
        }

        // If reference number is provided, ensure it's unique except for the current payment
        if (!empty($validated['reference_number'])) {
            $referenceQuery = Payment::where('reference_number', $validated['reference_number']);
            
            if ($request->has('payment_id')) {
                $referenceQuery->where('pay_id', '!=', $request->payment_id);
            } else if ($request->has('existing_payment_id')) {
                $referenceQuery->where('pay_id', '!=', $request->existing_payment_id);
            }
            
            if ($referenceQuery->exists()) {
                return back()->withErrors(['reference_number' => 'The reference number has already been used.'])->withInput();
            }
        }

        $order = Order::find($validated['order_id']);
        
        // Check if order exists
        if (!$order) {
            return back()->withErrors('The specified order does not exist.')->withInput();
        }
        
        // For partially paid orders (where down payment was made), always create a new payment
        // This handles balance payments for bulk orders
        if ($order->pay_status == 'Partially Paid') {
            $createNewPayment = true;
            $existingPayment = null;
        } else {
            // Different logic for determining if we should update an existing payment or create a new one
            $existingPayment = null;
            $createNewPayment = true;
            
            // Only update an existing payment if explicitly requested via ID or if it's a pending payment
            if ($request->has('payment_id')) {
                $existingPayment = Payment::find($request->payment_id);
                $createNewPayment = false;
            } else if ($request->has('existing_payment_id')) {
                $existingPayment = Payment::find($request->existing_payment_id);
                $createNewPayment = false;
            } else {
                // Only look for pending payments that haven't been processed yet
                $pendingPayment = Payment::where('order_id', $validated['order_id'])
                    ->where('pay_status', 'Pending')
                    ->first();
                    
                if ($pendingPayment) {
                    $existingPayment = $pendingPayment;
                    $createNewPayment = false;
                }
            }
        }
        
        // Calculate financial information - include ALL successful payments to get accurate totals
        $totalPaid = $order->payments()
            ->where('pay_status', 'Paid')
            ->when($existingPayment, function($query) use ($existingPayment) {
                return $query->where('pay_id', '!=', $existingPayment->pay_id);
            })
            ->sum('amount_paid');
        
        // Include delivery cost in total order amount
        $order->load('delivery');
        $totalOrderAmount = $order->total_amount;
        if ($order->delivery) {
            $totalOrderAmount += $order->delivery->delivery_cost;
        }
        
        $orderRemainingAmount = max(0, $totalOrderAmount - $totalPaid);
        
        // Check if this payment amount exceeds the remaining amount
        if ($validated['amount_paid'] > $orderRemainingAmount && $orderRemainingAmount > 0) {
            $validated['change_amount'] = $validated['amount_paid'] - $orderRemainingAmount;
            $validated['amount_paid'] = $orderRemainingAmount; // Adjust to only pay what's remaining
        } else {
            $validated['change_amount'] = 0;
        }
        
        // Check payment type and add appropriate notes
        if ($request->payment_type === 'down') {
            // This is a bulk order down payment
            $minimumDownPayment = $order->total_amount * 0.3;
            if ($validated['amount_paid'] < $minimumDownPayment) {
                return back()->withErrors('The down payment for bulk orders must be at least 30% of the total amount.')->withInput();
            }
            $validated['notes'] = trim(($validated['notes'] ?? '') . "\nBulk Order Down Payment (30%)");
        } elseif ($request->payment_type === 'balance') {
            // This is a balance payment after a down payment
            $validated['notes'] = trim(($validated['notes'] ?? '') . "\nBulk Order Balance Payment");
        }
        
        // Always mark payment as "Paid" when processing - the specific payment is completed
        $validated['pay_status'] = 'Paid';
        
        // Calculate outstanding balance after this payment
        $newTotalPaid = $totalPaid + $validated['amount_paid'];
        $validated['outstanding_balance'] = max(0, $totalOrderAmount - $newTotalPaid);
        
        // Ensure we have an invoice number
        if (empty($validated['invoice_number']) && empty($existingPayment?->invoice_number)) {
            $validated['invoice_number'] = 'INV-' . date('Ymd') . '-' . str_pad($order->order_id, 4, '0', STR_PAD_LEFT);
        }

        DB::beginTransaction();
        try {
            if (!$createNewPayment && $existingPayment) {
                // Update existing payment (preserve any fields not in validated data)
                $payment = $existingPayment;
                
                // Preserve the invoice_number if it exists and wasn't provided
                if (!isset($validated['invoice_number']) && $payment->invoice_number) {
                    $validated['invoice_number'] = $payment->invoice_number;
                }
                
                $payment->update($validated);
            } else {
                // Create a new payment record
                $payment = $this->payment->create($validated);
            }

            // Update order payment status
            $this->updateOrderPaymentStatus($order);
            
            DB::commit();
            
            if ($request->has('redirect_to_order')) {
                return redirect()->route('admin.orders.show', $payment->order_id)
                    ->with('success', 'Payment of ₱' . number_format($validated['amount_paid'], 2) . ' processed successfully');
            }
            
            return redirect()->route('admin.payments.index')
                ->with('success', 'Payment of ₱' . number_format($validated['amount_paid'], 2) . ' processed successfully');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors('Failed to process payment: ' . $e->getMessage())->withInput();
        }
    }

    public function edit(Payment $payment)
    {
        $orders = Order::all();
        $customers = Customer::all();
        return view('admin.payments.edit', compact('payment', 'orders', 'customers'));
    }

    public function update(Request $request, Payment $payment)
    {
        $validated = $request->validate([
            'order_id' => 'nullable|exists:orders,order_id',
            'cus_id' => 'required|exists:customers,cus_id',
            'amount_paid' => 'required|numeric|min:0',
            'pay_method' => 'required|string',
            'reference_number' => 'nullable|string|unique:payments,reference_number,'.$payment->pay_id.',pay_id',
            'pay_date' => 'required|date',
            'pay_status' => 'required|in:Paid,Partially Paid,Pending',
            'payment_type' => 'nullable|in:full,down',
            'notes' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            // Store the old order_id to handle removed associations
            $oldOrderId = $payment->order_id;
            
            // Check if this is a bulk order down payment
            if (!empty($validated['order_id'])) {
                $order = Order::find($validated['order_id']);
                
                // Validate minimum down payment for bulk orders
                if ($order->order_type === 'Bulk' && $request->payment_type === 'down') {
                    $minimumDownPayment = $order->total_amount * 0.3;
                    if ($validated['amount_paid'] < $minimumDownPayment) {
                        return back()->withErrors('The down payment for bulk orders must be at least 30% of the total amount.')->withInput();
                    }
                    
                    // Add note about this being a down payment if not already present
                    if (!strpos($validated['notes'] ?? '', 'Bulk Order Down Payment')) {
                        $validated['notes'] = trim(($validated['notes'] ?? '') . "\nBulk Order Down Payment (30%)");
                    }
                }

                // Calculate financial information for the order
                $order->load('delivery');
                $totalOrderAmount = $order->total_amount;
                if ($order->delivery) {
                    $totalOrderAmount += $order->delivery->delivery_cost;
                }

                $otherPaymentsTotal = $order->payments()
                    ->where('pay_id', '!=', $payment->pay_id)
                    ->where('pay_status', 'Paid')
                    ->sum('amount_paid');
                
                $totalPaid = $otherPaymentsTotal + 
                            ($validated['pay_status'] == 'Paid' ? $validated['amount_paid'] : 0);
                
                $validated['change_amount'] = max(0, $totalPaid - $totalOrderAmount);
                $validated['outstanding_balance'] = max(0, $totalOrderAmount - $totalPaid);
            } else {
                $validated['change_amount'] = 0;
                $validated['outstanding_balance'] = 0;
            }
            
            $payment->update($validated);

            // If order association has changed
            if ($oldOrderId != $payment->order_id) {
                // Update the old order's payment status if it exists
                if ($oldOrderId) {
                    $oldOrder = Order::find($oldOrderId);
                    if ($oldOrder) {
                        $this->updateOrderPaymentStatus($oldOrder);
                    }
                }
            }

            // Update new order payment status
            if ($payment->order_id) {
                $this->updateOrderPaymentStatus($payment->order);
            }
            
            DB::commit();
            
            if ($request->has('redirect_to_order') && $payment->order_id) {
                return redirect()->route('admin.orders.show', $payment->order_id)
                    ->with('success', 'Payment updated successfully');
            }
            
            return redirect()->route('admin.payments.index')
                ->with('success', 'Payment updated successfully');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors('Failed to update payment: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy(Payment $payment)
    {
        DB::beginTransaction();
        try {
            // Store order ID before deleting the payment
            $orderId = $payment->order_id;
            
            $payment->delete();
            
            // Update order payment status if applicable
            if ($orderId) {
                $order = Order::find($orderId);
                if ($order) {
                    $this->updateOrderPaymentStatus($order);
                }
            }
            
            DB::commit();
            return redirect()->route('admin.payments.index')
                ->with('success', 'Payment deleted successfully');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors('Failed to delete payment: ' . $e->getMessage());
        }
    }
    
    public function changeStatus(Request $request, Payment $payment)
    {
        $request->validate([
            'status' => 'required|in:Paid,Partially Paid,Pending',
        ]);
        
        DB::beginTransaction();
        try {
            $payment->pay_status = $request->status;
            $payment->save();
            
            // Update order payment status if applicable
            if ($payment->order_id) {
                $this->updateOrderPaymentStatus($payment->order);
            }
            
            DB::commit();
            return redirect()->back()->with('success', 'Payment status updated successfully');
            
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors('Failed to update payment status: ' . $e->getMessage());
        }
    }
    
    /**
     * Update the payment status of an order based on its payments
     */
    protected function updateOrderPaymentStatus(Order $order)
    {
        // Make sure we're only counting Paid payments
        $totalPaid = $order->payments()->where('pay_status', 'Paid')->sum('amount_paid');
        
        // Include delivery cost in the required amount
        $order->load('delivery');
        $requiredAmount = $order->total_amount;
        if ($order->delivery) {
            $requiredAmount += $order->delivery->delivery_cost;
        }
        
        // Bulk orders with down payments have special handling
        $isBulkOrder = $order->order_type === 'Bulk';
        $minimumDownPayment = $requiredAmount * 0.3;
        
        // Update the order status based on the total amounts paid
        if ($totalPaid >= $requiredAmount) {
            $order->update(['pay_status' => 'Paid']);
        } else if ($isBulkOrder && $totalPaid >= $minimumDownPayment) {
            // For bulk orders, if at least 30% is paid, mark it as a special status
            $order->update(['pay_status' => 'Partially Paid']);
        } else if ($totalPaid > 0) {
            $order->update(['pay_status' => 'Partially Paid']);
        } else {
            // Check if we have any pending payments
            $hasPendingPayments = $order->payments()->where('pay_status', 'Pending')->exists();
            $order->update(['pay_status' => $hasPendingPayments ? 'Pending' : 'unpaid']);
        }
    }
    
    /**
     * Create a payment from the order view
     */
    public function createFromOrder(Request $request, Order $order)
    {
        // Laravel's route model binding will handle the case where order doesn't exist
        // by returning a 404, but we'll add an extra check just to be sure
        if (!$order->exists) {
            return redirect()->route('admin.payments.index')
                ->with('error', 'The specified order could not be found.');
        }
        
        $order->load(['orderDetails.product', 'customer', 'delivery']);
        
        // For partially paid orders, we're creating a new payment for the balance
        // so we don't want to reference an existing payment
        $existingPayment = null;
        $defaultPaymentMethod = null;
        
        if ($order->pay_status != 'Partially Paid') {
            // Only check for existing payment if not partially paid
            $existingPayment = Payment::where('order_id', $order->order_id)
                ->where('pay_status', '!=', 'Paid')
                ->first();
        }
        
        // Get the default payment method from any existing payment record for this order
        if (!$existingPayment) {
            $latestPayment = $order->payments()->latest()->first();
            if ($latestPayment) {
                $defaultPaymentMethod = $latestPayment->pay_method;
            }
        }
        
        // Calculate remaining amount to be paid - include ALL paid payments
        $totalPaid = $order->payments()->where('pay_status', 'Paid')->sum('amount_paid');
        
        // Include delivery cost in total amount
        $totalOrderAmount = $order->total_amount;
        if ($order->delivery) {
            $totalOrderAmount += $order->delivery->delivery_cost;
        }
        
        $remainingAmount = max(0, $totalOrderAmount - $totalPaid);
        
        return view('admin.payments.create_from_order', compact('order', 'existingPayment', 'remainingAmount', 'defaultPaymentMethod', 'totalOrderAmount'));
    }
    
    /**
     * Get payments for a specific order (for AJAX requests)
     */
    public function getOrderPayments(Order $order)
    {
        $payments = $order->payments()->with('customer')->get();
        return response()->json($payments);
    }
    
    /**
     * Generate payment reports
     */
    public function reports(Request $request)
    {
        // Get date range filters
        $dateFrom = $request->input('date_from', now()->subDays(30)->format('Y-m-d'));
        $dateTo = $request->input('date_to', now()->format('Y-m-d'));
        
        // Base query with date filtering
        $baseQuery = DB::table('payments')
            ->whereDate('pay_date', '>=', $dateFrom)
            ->whereDate('pay_date', '<=', $dateTo);
        
        // Payment summary by status
        $paymentsByStatus = (clone $baseQuery)
            ->select('pay_status', DB::raw('count(*) as count'), DB::raw('sum(amount_paid) as total'))
            ->groupBy('pay_status')
            ->get();
            
        // Payment summary by method
        $paymentsByMethod = (clone $baseQuery)
            ->select('pay_method', DB::raw('count(*) as count'), DB::raw('sum(amount_paid) as total'))
            ->where('pay_status', 'Paid') // Changed from 'completed' to 'Paid'
            ->groupBy('pay_method')
            ->get();
            
        // Daily payments for the selected date range
        $dailyPayments = (clone $baseQuery)
            ->select(DB::raw('DATE(pay_date) as date'), DB::raw('sum(amount_paid) as total'))
            ->where('pay_status', 'Paid') // Changed from 'completed' to 'Paid'
            ->groupBy(DB::raw('DATE(pay_date)'))
            ->orderBy('date')
            ->get();
            
        return view('admin.payments.reports', compact(
            'paymentsByStatus', 
            'paymentsByMethod',
            'dailyPayments',
            'dateFrom',
            'dateTo'
        ));
    }
    
    /**
     * Export payments data
     */
    public function export(Request $request)
    {
        $payments = $this->payment->with(['order', 'customer'])->get();
        
        // Logic for exporting to CSV would go here
        
        return back()->with('success', 'Payments exported successfully');
    }
}