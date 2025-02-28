<?php

namespace App\Http\Controllers\Admin;

use App\Models\Delivery;
use App\Models\Order;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AdminDeliveryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $deliveries = Delivery::with(['order.customer'])->paginate(15);
        return view('admin.deliveries.index', compact('deliveries'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $orders = Order::whereDoesntHave('delivery')->get();
        return view('admin.deliveries.create', compact('orders'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,order_id',
            'delivery_date' => 'required|date',
            'street' => 'required|string',
            'city' => 'required|string', 
            'province' => 'required|string',
            'special_instructions' => 'nullable|string',
            'delivery_status' => 'required|string|in:Pending,Out for Delivery,Delivered,Cancelled',
            'delivery_cost' => 'required|numeric|min:0',
        ]);

        Delivery::create($request->all());

        return redirect()->route('admin.deliveries.index')
                         ->with('success', 'Delivery created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Delivery $delivery)
    {
        $delivery->load(['order.customer', 'order.orderDetails.product']);
        return view('admin.deliveries.show', compact('delivery'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Delivery $delivery)
    {
        $delivery->load('order');
        return view('admin.deliveries.edit', compact('delivery'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Delivery $delivery)
    {
        $request->validate([
            'delivery_date' => 'required|date',
            'street' => 'required|string',
            'city' => 'required|string',
            'province' => 'required|string', 
            'special_instructions' => 'nullable|string',
            'delivery_status' => 'required|string|in:Pending,Out for Delivery,Delivered,Cancelled',
            'delivery_cost' => 'required|numeric|min:0',
        ]);

        $delivery->update($request->all());

        return redirect()->route('admin.deliveries.index')
                         ->with('success', 'Delivery updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Delivery $delivery)
    {
        // Check if delivery can be deleted
        if ($delivery->delivery_status === 'Delivered') {
            return redirect()->route('admin.deliveries.index')
                             ->with('error', 'Delivered deliveries cannot be deleted.');
        }
        
        $delivery->delete();

        return redirect()->route('admin.deliveries.index')
                         ->with('success', 'Delivery deleted successfully.');
    }
    
    /**
     * Delivery monitoring page
     */
    public function monitoring()
    {
        $pendingDeliveries = Delivery::where('delivery_status', 'Pending')->count();
        $outForDeliveryCount = Delivery::where('delivery_status', 'Out for Delivery')->count();
        $deliveredCount = Delivery::where('delivery_status', 'Delivered')->count();
        $cancelledCount = Delivery::where('delivery_status', 'Cancelled')->count();
        
        $deliveries = Delivery::with('order.customer')
                             ->whereIn('delivery_status', ['Pending', 'Out for Delivery'])
                             ->orderBy('delivery_date')
                             ->get();
        
        return view('admin.deliveries.monitoring', compact(
            'deliveries',
            'pendingDeliveries',
            'outForDeliveryCount',
            'deliveredCount',
            'cancelledCount'
        ));
    }
}

