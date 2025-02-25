<?php

namespace App\Http\Controllers;

use App\Models\Delivery;
use App\Models\Order;
use Illuminate\Http\Request;

class DeliveryController extends Controller
{
    public function edit(Order $order)
    {
        \Log::info('Authenticated user ID: ' . auth()->id());
        \Log::info('Order customer ID: ' . $order->cus_id);
        // Authorization check: siguraduhin na ang logged-in user ay may access sa order na ito.
        if (auth()->id() !== $order->cus_id) {
            abort(403, 'Unauthorized action.');
        }

        // Directly retrieve the delivery record using the relationship.
        $delivery = $order->delivery; // assumes one-to-one relationship is properly set up.
        return view('deliveries.edit', compact('order', 'delivery'));
    }

    public function update(Request $request, Order $order)
    {
        \Log::info('Authenticated user ID: ' . auth()->id());
        \Log::info('Order customer ID: ' . $order->cus_id);

        // Authorization check: siguraduhin na ang logged-in user ay may access sa order na ito.
        if (auth()->id() !== $order->cus_id) {
            abort(403, 'Unauthorized action.');
        }

        $delivery = Delivery::where('order_id', $order->order_id)->first();

        if (!$delivery) {
            return redirect()->back()->with('error', 'No delivery record found.');
        }

        // Validate request input
        $validated = $request->validate([
            'delivery_address'    => 'required|string|max:255',
            'delivery_date'       => 'required|date',
            'special_instructions'=> 'nullable|string'
        ]);

        $validated['delivery_date'] = date('Y-m-d', strtotime($validated['delivery_date']));


        $delivery->update($validated);

        return redirect()->route('orders.payment', $order->order_id)->with('success', 'Delivery details updated.');

    }
}
