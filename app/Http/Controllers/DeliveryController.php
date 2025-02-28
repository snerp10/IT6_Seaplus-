<?php

namespace App\Http\Controllers;

use App\Models\Delivery;
use App\Models\Order;
use Illuminate\Http\Request;

class DeliveryController extends Controller
{
    public function edit(Order $order)
    {
        // Check if user is authorized
        if ($order->cus_id !== auth()->user()->cus_id) {
            abort(403, 'Unauthorized action.');
        }

        return view('customer.deliveries.edit', compact('order'));
    }

    public function update(Request $request, Order $order)
    {
        // Check if user is authorized
        if ($order->cus_id !== auth()->user()->cus_id) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'delivery_date' => 'required|date',
            'street' => 'required|string',
            'city' => 'required|string',
            'province' => 'required|string',
            'special_instructions' => 'nullable|string',
        ]);

        $delivery = $order->delivery;

        if (!$delivery) {
            $delivery = new Delivery();
            $delivery->order_id = $order->order_id;
        }

        $delivery->delivery_date = $request->delivery_date;
        $delivery->street = $request->street;
        $delivery->city = $request->city;
        $delivery->province = $request->province;
        $delivery->special_instructions = $request->special_instructions;
        $delivery->save();

        return redirect()->route('orders.show', $order->order_id)
                         ->with('success', 'Delivery details updated successfully!');
    }
}
