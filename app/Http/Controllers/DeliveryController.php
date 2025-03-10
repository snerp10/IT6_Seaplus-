<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Delivery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DeliveryController extends Controller
{
    public function edit(Order $order)
    {
        // Authorization check
        if ($order->cus_id !== auth()->user()->cus_id) {
            abort(403, 'Unauthorized action.');
        }
        
        // Get or create delivery record
        $delivery = $order->delivery ?? new Delivery(['order_id' => $order->order_id]);
        
        return view('customer.deliveries.edit', compact('order', 'delivery'));
    }

    public function update(Request $request, Order $order)
    {
        // Authorization check
        if ($order->cus_id !== auth()->user()->cus_id) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'delivery_date' => 'required|date|after_or_equal:today',
            'street' => 'required|string',
            'city' => 'required|string',
            'province' => 'required|string',
            'special_instructions' => 'nullable|string',
        ]);
        
        DB::beginTransaction();
        
        try {
            $delivery = $order->delivery;

            if (!$delivery) {
                $delivery = new Delivery();
                $delivery->order_id = $order->order_id;
                $delivery->delivery_status = 'Pending';
                $delivery->delivery_cost = $order->order_type === 'Retail' ? 100 : 200;
            }

            // Update delivery details
            $delivery->delivery_date = $validated['delivery_date'];
            $delivery->street = $validated['street'];
            $delivery->city = $validated['city'];
            $delivery->province = $validated['province'];
            $delivery->special_instructions = $validated['special_instructions'];
            $delivery->save();
            
            // Find if this is a COD order
            $payment = $order->payments()->first();
            $isCodOrder = $payment && $payment->pay_method === 'Cash on Delivery';
            
            DB::commit();

            if ($isCodOrder) {
                // If it's a COD order, go directly to invoice
                return redirect()->route('customer.invoices.show', $order->order_id)
                    ->with('success', 'Delivery details saved. Your COD order has been placed!');
            } 
            // For pending orders with GCash payment, redirect to payment
            else if ($order->order_status === 'Pending') {
                return redirect()->route('customer.orders.payment', $order->order_id)
                    ->with('success', 'Delivery details saved. Please complete your payment.');
            }
            // Otherwise return to order details
            else {
                return redirect()->route('customer.orders.show', $order->order_id)
                    ->with('success', 'Delivery details updated successfully!');
            }
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Error updating delivery details: ' . $e->getMessage());
        }
    }
}
