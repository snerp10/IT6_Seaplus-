<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Payment;
use App\Models\Delivery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Http\Controllers\PDF;

class PaymentController extends Controller
{
    public function create(Order $order)
    {
        // Authorization check
        if ($order->cus_id !== auth()->user()->customer->cus_id) {
            abort(403, 'Unauthorized action.');
        }
        
        // Load necessary relationships
        $order->load(['orderDetails.product', 'delivery', 'payments']);
        
        // Get payment method
        $paymentMethod = $order->payments->first()->pay_method ?? null;
        
        // If payment method is COD
        if ($paymentMethod === 'Cash on Delivery') {
            // For retail orders with COD, redirect to delivery details
            if ($order->order_type === 'Retail') {
                return redirect()->route('customer.delivery.edit', $order->order_id)
                    ->with('info', 'This is a Cash on Delivery order. Please complete your delivery details.');
            }
            // For bulk orders with COD, continue to payment screen for down payment
        }
        
        // Calculate total paid and remaining amount
        $totalPaid = $order->payments->where('pay_status', 'Paid')->sum('amount_paid');
        $remainingAmount = $order->total_amount - $totalPaid;
        
        // For bulk orders, calculate minimum downpayment (30%)
        $minimumPayment = $order->order_type === 'Bulk' ? ($order->total_amount * 0.30) : $remainingAmount;
        
        // Add notification that delivery details can be updated later
        $showDeliveryNotice = $order->delivery && 
                             ($order->delivery->street === 'To be updated' || 
                              $order->delivery->city === 'To be updated' || 
                              $order->delivery->province === 'To be updated');
        
        // Generate payment reference
        $paymentReference = 'ORD-' . $order->order_id . '-' . time();
        
        try {
            // Generate QR code data for GCash payment
            $qrData = [
                'amount' => $remainingAmount,
                'reference' => $paymentReference,
                'recipient' => 'KSM SeaPlus+',
                'message' => 'Payment for Order #' . $order->order_id
            ];
            
            // Use SVG format which doesn't require Imagick
            $qrCodeImage = QrCode::size(250)
                ->format('svg')
                ->generate(json_encode($qrData));
            
            $qrCodeBase64 = 'data:image/svg+xml;base64,' . base64_encode($qrCodeImage);
            $hasQRCode = true;
            
            return view('customer.payments.create', compact(
                'order', 'totalPaid', 'remainingAmount', 'minimumPayment', 
                'showDeliveryNotice', 'qrCodeBase64', 'hasQRCode', 'paymentReference'
            ));
        } catch (\Exception $e) {
            Log::error('Error generating QR code: ' . $e->getMessage());
            
            // Fall back to no QR code if there's an error
            return view('customer.payments.create', compact(
                'order', 'totalPaid', 'remainingAmount', 'minimumPayment', 
                'showDeliveryNotice', 'paymentReference'
            ))->with('info', 'QR code generation is not available. Please use the payment reference to complete your transaction.');
        }
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'order_id' => 'required|exists:orders,order_id',
            'amount_paid' => 'required|numeric|min:0.01',
            'payment_method' => 'required|string|in:Cash,GCash,credit_card',
            'reference_number' => 'nullable|string|required_if:payment_method,gcash,credit_card',
        ]);
        
        $order = Order::findOrFail($request->order_id);
        
        // Authorization check
        if ($order->cus_id !== auth()->user()->customer->cus_id) {
            abort(403, 'Unauthorized action.');
        }
        
        // Calculate total paid and remaining amount
        $totalPaid = $order->payments->where('pay_status', 'Paid')->sum('amount_paid');
        $remainingAmount = $order->total_amount - $totalPaid;
        $minimumPayment = $order->order_type === 'Bulk' ? ($order->total_amount * 0.30) : $remainingAmount;
        
        // Validate payment amount
        if ($request->amount_paid > $remainingAmount) {
            return back()->withInput()->with('error', 'Payment amount cannot exceed the remaining balance.');
        }
        
        // Map the payment method to the database values
        $payMethodMap = [
            'Cash' => 'Cash on Delivery',
            'GCash' => 'GCash',
            'credit_card' => 'Credit Card'
        ];
        
        $payMethod = $payMethodMap[$request->payment_method];
        
        // For bulk orders, ensure minimum down payment if using GCash
        if ($totalPaid == 0 && $order->order_type === 'Bulk' && $payMethod === 'GCash') {
            if ($request->amount_paid < $minimumPayment) {
                return back()->withInput()->with('error', "Bulk orders require a minimum down payment of 30% (₱" . number_format($minimumPayment, 2) . ")");
            }
        }
        
        // For retail orders with GCash, require full payment
        if ($totalPaid == 0 && $order->order_type === 'Retail' && $payMethod === 'GCash' && $request->amount_paid < $order->total_amount) {
            return back()->withInput()->with('error', "Retail orders with GCash require full payment.");
        }
        
        DB::beginTransaction();
        
        try {
            // Create payment record
            $payment = Payment::create([
                'cus_id' => $order->cus_id,
                'order_id' => $order->order_id,
                'amount_paid' => $request->amount_paid,
                'pay_method' => $payMethod,
                'reference_number' => $request->reference_number,
                'pay_date' => now(),
                'pay_status' => $payMethod === 'Cash on Delivery' ? 'Unpaid' : 'Paid',
                'outstanding_balance' => $remainingAmount - $request->amount_paid,
                'invoice_number' => 'INV-' . time() . '-' . $order->order_id
            ]);
            
            // Only update order status if this is a GCash payment (COD stays Pending until admin confirms)
            if ($payMethod === 'GCash' || $payMethod === 'Credit Card') {
                $newTotalPaid = $totalPaid + $request->amount_paid;
                
                if ($newTotalPaid >= $order->total_amount) {
                    // Fully paid
                    $order->update(['order_status' => 'Completed']);
                } else if ($order->order_type === 'Bulk' && $newTotalPaid >= $minimumPayment) {
                    // Bulk order with sufficient down payment
                    $order->update(['order_status' => 'Processing']);
                }
                
                // Update delivery status for processing
                if ($order->order_status === 'Processing' || $order->order_status === 'Completed') {
                    $order->delivery->update(['delivery_status' => 'Scheduled']);
                }
            } else if ($payMethod === 'Cash on Delivery') {
                // For COD, set order status to Processing
                $order->update(['order_status' => 'Processing']);
                $order->delivery->update(['delivery_status' => 'Scheduled']);
            }
            
            DB::commit();
            
            // If payment is successful and delivery details are default,
            // suggest the customer to update delivery details
            if ($order->delivery && 
                ($order->delivery->street === 'To be updated' || 
                 $order->delivery->city === 'To be updated' || 
                 $order->delivery->province === 'To be updated')) {
                return redirect()->route('customer.delivery.edit', $order->order_id)
                    ->with('success', 'Payment information saved! Please provide your delivery details.');
            } else {
                if ($payMethod === 'Cash on Delivery') {
                    return redirect()->route('customer.invoices.show', $order->order_id)
                        ->with('success', 'Your Cash on Delivery order has been placed successfully!');
                } else {
                    return redirect()->route('customer.invoices.show', $order->order_id)
                        ->with('success', 'Payment processed successfully!');
                }
            }
                
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Payment processing error: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Payment failed: ' . $e->getMessage());
        }
    }

    public function showInvoice(Order $order)
    {
        // Authorization check
        if ($order->cus_id !== auth()->user()->customer->cus_id) {
            abort(403, 'Unauthorized action.');
        }

        // Get the latest payment for this order
        $payment = Payment::where('order_id', $order->order_id)
            ->orderBy('created_at', 'desc')
            ->first();
            
        if (!$payment) {
            return redirect()->route('customer.orders.show', $order->order_id)
                ->with('error', 'No payment record found for this order.');
        }
        
        // Load order details
        $order->load(['orderDetails.product', 'customer', 'delivery']);
        
        // For Cash on Delivery orders, make sure the template shows the correct payment info
        if ($payment->pay_method === 'Cash on Delivery') {
            // This ensures the invoice template displays correctly for COD orders
            $payment->outstanding_balance = $payment->outstanding_balance ?: $order->total_amount;
        }
        
        return view('customer.payments.invoice', compact('order', 'payment'));
    }

    public function downloadInvoice(Order $order)
    {
        // Authorization check
        if ($order->cus_id !== auth()->user()->customer->cus_id) {
            abort(403, 'Unauthorized action.');
        }

        // Get the latest payment
        $payment = Payment::where('order_id', $order->order_id)
            ->orderBy('created_at', 'desc')
            ->first();
            
        if (!$payment) {
            return redirect()->route('customer.orders.show', $order->order_id)
                ->with('error', 'No payment record found for this order.');
        }
        
        // Load necessary relationships
        $order->load(['orderDetails.product', 'customer', 'delivery']);
        
        // Generate PDF
        $pdf = PDF::loadView('customer.payments.invoice_pdf', compact('order', 'payment'));
        
        return $pdf->download('invoice-' . $payment->invoice_number . '.pdf');
    }

    public function verifyGcashPayment(Request $request)
    {
        $request->validate([
            'reference_number' => 'required|string|min:10'
        ]);
        
        try {
            // Simulate verification - in production, connect to GCash API
            // In a real implementation, you would call the GCash API here to verify the payment
            $isValid = (strlen($request->reference_number) >= 10);
            $responseData = [
                'success' => $isValid,
                'message' => $isValid ? 'Payment verification successful' : 'Invalid reference number',
                'reference' => $request->reference_number
            ];
            
            // Return JSON response for Ajax requests
            if ($request->ajax()) {
                return response()->json($responseData);
            }
            
            // Return redirect response for form submissions
            if ($isValid) {
                return back()->with('success', 'Payment verified successfully');
            }
            
            return back()->with('error', 'Failed to verify payment: Invalid reference number');
            
        } catch (\Exception $e) {
            Log::error('GCash verification error: ' . $e->getMessage());
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Verification system error: ' . $e->getMessage()
                ], 500);
            }
            
            return back()->with('error', 'Verification system error: ' . $e->getMessage());
        }
    }
}
