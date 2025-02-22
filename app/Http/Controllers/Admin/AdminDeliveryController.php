<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\Delivery;
use App\Models\Order;
use App\Http\Controllers\Controller;

class AdminDeliveryController extends Controller
{
    public function index()
    {
        $deliveries = Delivery::with(['order', 'order.customer'])
        ->latest()
        ->paginate(8); // Add pagination here
    
        return view('admin.deliveries.index', compact('deliveries'));
    }
    
    public function show(Delivery $delivery)
    {
        return view('admin.deliveries.view', compact('delivery'));
    }
    public function edit(Delivery $delivery)
    {
        return view('admin.deliveries.edit', compact('delivery'));
    }

    public function update(Request $request, Delivery $delivery)
    {
        $validated = $request->validate([
            'delivery_address' => 'required|string|max:255',
            'delivery_date' => 'required|date',
            'delivery_status' => 'required|string',
        ]);

        $delivery->update($validated);

        return redirect()->route('admin.deliveries.index')->with('success', 'Delivery updated successfully.');
    }

    public function monitoring()
    {
        $orders = Order::whereHas('delivery', function ($query) {
            $query->where('delivery_status', '!=', 'Delivered');
        })->get();
        return view('admin.deliveries.monitoring', compact('orders'));
    }

    public function confirmDelivery(Order $order)
    {
        $order->delivery->update(['delivery_status' => 'Delivered']);
        return redirect()->route('admin.deliveries.monitoring')->with('success', 'Delivery confirmed successfully.');
    }
}

