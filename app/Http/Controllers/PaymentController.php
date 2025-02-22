<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Payment;
use App\Models\Delivery;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use PDF;
use Storage;

class PaymentController extends Controller
{
    public function create(Order $order)
    {
        $totalAmount = $order->total_amount;
        $minimumDownpayment = $order->order_type === 'Bulk' ? ($totalAmount * 0.30) : $totalAmount;
        
        return view('customer.payments.create', compact('order', 'minimumDownpayment'));
    }

    public function store(Request $request)
    {
        $order = Order::findOrFail($request->order_id);
        $minimumDownpayment = $order->order_type === 'Bulk' ? ($order->total_amount * 0.30) : $order->total_amount;    
        
        // Validate minimum downpayment for bulk orders
        if ($order->order_type === 'Bulk' && $request->amount_paid < $minimumDownpayment) {
            return back()->with('error', 'Bulk orders require a minimum downpayment of 30% (₱' . number_format($minimumDownpayment, 2) . ')');
        }

        $payment = Payment::create([
            'customer_id' => auth()->id(),
            'order_id' => $order->order_id,
            'amount_paid' => $request->amount_paid,
            'payment_date' => now(),
            'payment_method' => $request->payment_method,
            'outstanding_balance' => $order->total_amount - $request->amount_paid,
            'invoice_number' => 'INV-' . time()
        ]);

        // Update the outstanding balance of the order
        $order->update([
            'total_amount' => $payment->outstanding_balance,
            'payment_status' => $payment->outstanding_balance <= 0 ? 'Paid' : 'Partially Paid',
            'delivery_status' => 'Processing'
        ]);

        $delivery = Delivery::where('order_id', $order->order_id)->first();
        if ($delivery) {
            $delivery->update([
                'delivery_status' => 'Processing'
            ]);
        }

        return view('customer.payments.invoice', compact('order', 'payment'))
            ->with('success', 'Payment processed successfully!');
    }

    public function gcashRedirect(Request $request)
    {
        $validated = $request->validate([
            'order_id' => 'required|exists:orders,order_id',
            'amount_paid' => 'required|numeric|min:0'
           ]);

        $order = Order::findOrFail($request->order_id);

        // Calculate minimum required payment
        $minimumPayment = $order->order_type === 'Bulk' ? ($order->total_amount * 0.30) : $order->total_amount;

    // Validate payment amount
    if ($order->order_type === 'Bulk') {
        if ($request->amount_paid < $minimumPayment) {
            return back()->with('error', 'Bulk orders require a minimum downpayment of 30% (₱' . number_format($minimumPayment, 2) . ')');
        }
    } else {
        if ($request->amount_paid != $order->total_amount) {
            return back()->with('error', 'Please enter the exact amount for Retail orders.');
        }
    }

    // Store payment details in session
    session([
        'gcash_payment_details' => json_encode([
            'orderId' => $order->order_id,
            'amount' => $request->amount_paid
        ])
    ]);

    // For demo purposes, directly process the payment
    return $this->processGcashPayment($request);
}


    private function processGcashPayment(Request $request)
    {
        try {
            $paymentDetails = json_decode($request->session()->get('gcash_payment_details'));
            $order = Order::findOrFail($paymentDetails->orderId);
            
            $payment = Payment::create([
                'customer_id' => auth()->id(),
                'order_id' => $order->order_id,
                'amount_paid' => $paymentDetails->amount,
                'payment_date' => now(),
                'payment_method' => 'GCash',
                'outstanding_balance' => 0,
                'invoice_number' => 'GCASH-' . time()
            ]);

            // Update order status to Paid
            $order->update([
                'payment_status' => 'Paid',
                'delivery_status' => 'Processing'
            ]);
            $delivery = Delivery::where('order_id', $order->order_id)->first();
            if ($delivery) {
                $delivery->update([
                    'delivery_status' => 'Processing'
                ]);
            }
            // Clear payment session data
            $request->session()->forget(['gcash_payment_id', 'gcash_payment_details']);

            return redirect()->route('orders.index')
                           ->with('success', 'GCash payment processed successfully!');

        } catch (\Exception $e) {
            return redirect()->route('payments.create')
                           ->with('error', 'GCash payment failed: ' . $e->getMessage());
        }
    }

    public function showInvoice(Order $order)
    {
        // Check if user is authorized to view this invoice
        if ($order->customer_id !== auth()->id()) {
            abort(403);
        }

        $payment = Payment::where('order_id', $order->order_id)
                        ->latest()
                        ->firstOrFail();

        return view('customer.payments.invoice', compact('order', 'payment'));
    }
}
