<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    public function create(Order $order)
    {
        return view('orders.show', compact('order'));
    }

    public function store(Request $request)
    {
        $order = Order::findOrFail($request->order_id);
        
        $payment = Payment::create([
            'customer_id' => auth()->id(),
            'order_id' => $order->order_id,
            'amount_paid' => $request->amount_paid,
            'payment_date' => now(),
            'payment_method' => $request->payment_method,
            'outstanding_balance' => $order->total_amount - $request->amount_paid,
            'invoice_number' => 'INV-' . time()
        ]);

        if($payment->outstanding_balance <= 0) {
            $order->update([
                'payment_status' => 'Paid',
                'delivery_status' => 'Processing'
            ]);
        }
        

        return redirect()->route('orders.payment', $order->order_id)
            ->with('success', 'Payment processed successfully!');
    }

    public function gcashRedirect(Request $request)
    {
        $validated = $request->validate([
            'order_id' => 'required|exists:orders,order_id',
            'amount_paid' => 'required|numeric'
        ]);

        $order = Order::findOrFail($request->order_id);
        
        // Verify exact amount
        if (floatval($request->amount_paid) != floatval($order->total_amount)) {
            return back()->with('error', 'Please enter the exact amount for GCash payment.');
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

            // Clear payment session data
            $request->session()->forget(['gcash_payment_id', 'gcash_payment_details']);

            return redirect()->route('orders.index')
                           ->with('success', 'GCash payment processed successfully!');

        } catch (\Exception $e) {
            return redirect()->route('payments.create')
                           ->with('error', 'GCash payment failed: ' . $e->getMessage());
        }
    }
}
