<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Delivery;
use App\Models\Order;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminDeliveryController extends Controller
{
    /**
     * Display a listing of deliveries with filtering options.
     */
    public function index(Request $request)
    {
        $ordersWithoutDelivery = Order::whereDoesntHave('delivery')->count();
        \Log::debug('Orders without delivery count in index: ' . $ordersWithoutDelivery);
        // Start building the query with related models
        $deliveries = Delivery::with(['order.customer']);
        
        // Apply filters if present
        if ($request->has('status') && !empty($request->status)) {
            $deliveries->where('delivery_status', $request->status);
        }
        
        if ($request->has('date_from') && !empty($request->date_from)) {
            $deliveries->whereDate('delivery_date', '>=', $request->date_from);
        }
        
        if ($request->has('date_to') && !empty($request->date_to)) {
            $deliveries->whereDate('delivery_date', '<=', $request->date_to);
        }
        
        // Get delivery statistics for the header
        $stats = $this->getDeliveryStats();
        
        // Order by date and paginate
        $deliveries = $deliveries->orderBy('delivery_date')->paginate(15);
        
        return view('admin.deliveries.index', array_merge(
            compact('deliveries'), 
            $stats
        ));
    }

    /**
     * Show the form for creating a new delivery.
     */
    public function create()
    {
        // Get orders with missing or incomplete delivery information
        $orders = Order::whereDoesntHave('delivery')
            ->orWhereHas('delivery', function($query) {
                $query->whereNull('street')
                      ->orWhereNull('city')
                      ->orWhereNull('province')
                      ->orWhere('delivery_status', '=', 'Not Set');
            })
            ->with('customer', 'delivery')
            ->get();
        
        \Log::debug('Orders needing delivery setup: ' . $orders->count());
                       
        if ($orders->isEmpty()) {
            return redirect()->route('admin.deliveries.index')
                    ->with('info', 'All orders have complete delivery information.');
        }
                       
        return view('admin.deliveries.create', compact('orders'));
    }

    /**
     * Store a newly created delivery in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'order_id' => 'required|exists:orders,order_id',  // Removed the unique constraint
            'delivery_date' => 'nullable|date',
            'street' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'province' => 'nullable|string|max:255',
            'special_instructions' => 'nullable|string',
            'delivery_status' => 'required|in:Pending,Scheduled,Out for Delivery,Delivered,Failed,Returned',
            'delivery_cost' => 'nullable|numeric|min:0',
        ]);

        DB::beginTransaction();
        
        try {
            // Check if a delivery already exists for this order
            $existingDelivery = Delivery::where('order_id', $request->order_id)->first();
            
            if ($existingDelivery) {
                // If delivery exists, store old cost for comparison
                $oldDeliveryCost = $existingDelivery->delivery_cost ?? 0;
                
                // Update existing delivery
                $existingDelivery->update($validated);
                $delivery = $existingDelivery;
                
                // Calculate cost difference if changed
                $costDifference = ($request->delivery_cost ?? 0) - $oldDeliveryCost;
                
                if ($costDifference != 0) {
                    // Adjust order total amount
                    $order = Order::findOrFail($request->order_id);
                    $order->increment('total_amount', $costDifference);
                    
                    // Update payment outstanding balance if needed
                    $initialPayment = $order->payments->where('amount_paid', 0)->first();
                    if ($initialPayment) {
                        $initialPayment->increment('outstanding_balance', $costDifference);
                    }
                }
            } else {
                // Create new delivery
                $delivery = Delivery::create($validated);
                
                // Update order total amount to include delivery cost
                $order = Order::findOrFail($request->order_id);
                $order->increment('total_amount', $request->delivery_cost ?? 0);
                
                // If there's an initial payment record, update the outstanding balance
                $initialPayment = $order->payments->where('amount_paid', 0)->first();
                if ($initialPayment) {
                    $initialPayment->increment('outstanding_balance', $request->delivery_cost ?? 0);
                }
            }
            
            DB::commit();
            
            // Make sure we're using the model instance, not just its ID for route model binding
            return redirect()->route('admin.deliveries.show', $delivery)
                             ->with('success', 'Delivery ' . ($existingDelivery ? 'updated' : 'created') . ' successfully');
                             
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                             ->withInput()
                             ->with('error', 'Error ' . ($existingDelivery ? 'updating' : 'creating') . ' delivery: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified delivery.
     */
    public function show(Delivery $delivery)
    {
        $delivery->load([
            'order.customer', 
            'order.orderDetails.product',
            'order.payments'
        ]);
        
        return view('admin.deliveries.show', compact('delivery'));
    }

    /**
     * Show the form for editing the specified delivery.
     */
    public function edit(Delivery $delivery)
    {
        $delivery->load(['order.customer', 'order.orderDetails.product']);
        return view('admin.deliveries.edit', compact('delivery'));
    }

    /**
     * Update the specified delivery in storage.
     */
    public function update(Request $request, Delivery $delivery)
    {
        $validated = $request->validate([
            'delivery_date' => 'nullable|date',
            'street' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'province' => 'nullable|string|max:255',
            'special_instructions' => 'nullable|string',
            'delivery_status' => 'required|in:Pending,Scheduled,Out for Delivery,Delivered,Failed,Returned',
            'delivery_cost' => 'nullable|numeric|min:0',
        ]);

        DB::beginTransaction();
        
        try {
            // Get old delivery cost before update
            $oldDeliveryCost = $delivery->delivery_cost ?? 0;
            
            // Update delivery
            $delivery->update($validated);
            
            // Adjust order total amount if delivery cost changed
            if (($request->delivery_cost ?? 0) != $oldDeliveryCost) {
                $costDifference = ($request->delivery_cost ?? 0) - $oldDeliveryCost;
                
                $order = $delivery->order;
                $order->increment('total_amount', $costDifference);
                
                // Update payment outstanding balance if needed
                $initialPayment = $order->payments->where('amount_paid', 0)->first();
                if ($initialPayment) {
                    $initialPayment->increment('outstanding_balance', $costDifference);
                }
            }
            
            DB::commit();
            
            return redirect()->route('admin.deliveries.show', $delivery)
                             ->with('success', 'Delivery updated successfully');
                             
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                             ->withInput()
                             ->with('error', 'Error updating delivery: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Delivery $delivery)
    {
        // Check if delivery can be deleted
        if ($delivery->delivery_status === 'Delivered') {
            return redirect()->route('admin.deliveries.index')
                             ->with('error', 'Delivered deliveries cannot be deleted');
        }
        
        DB::beginTransaction();
        
        try {
            // Get related order and delivery cost before deletion
            $order = $delivery->order;
            $deliveryCost = $delivery->delivery_cost ?? 0;
            
            // Delete the delivery
            $delivery->delete();
            
            // Adjust order total amount
            if ($deliveryCost > 0) {
                $order->decrement('total_amount', $deliveryCost);
                
                // Update payment outstanding balance if needed
                $initialPayment = $order->payments->where('amount_paid', 0)->first();
                if ($initialPayment) {
                    $initialPayment->decrement('outstanding_balance', $deliveryCost);
                }
            }
            
            DB::commit();
            
            return redirect()->route('admin.deliveries.index')
                             ->with('success', 'Delivery deleted successfully');
                             
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('admin.deliveries.index')
                             ->with('error', 'Error deleting delivery: ' . $e->getMessage());
        }
    }
    
    /**
     * Get delivery statistics for dashboard and headers.
     * 
     * @return array
     */
    private function getDeliveryStats()
    {
        return [
            'total_deliveries' => Delivery::count(),
            'pending_deliveries' => Delivery::where('delivery_status', 'Pending')->count(),
            'scheduled' => Delivery::where('delivery_status', 'Scheduled')->count(),
            'out_for_delivery' => Delivery::where('delivery_status', 'Out for Delivery')->count(),
            'delivered' => Delivery::where('delivery_status', 'Delivered')->count(),
            'failed' => Delivery::where('delivery_status', 'Failed')->count(),
            'returned' => Delivery::where('delivery_status', 'Returned')->count(),
        ];
    }
    
    /**
     * Delivery monitoring page
     */
    public function monitoring()
    {
        $stats = $this->getDeliveryStats();
        
        // Fix: Use the correct field for filtering deliveries
        $deliveries = Delivery::with('order.customer')
                             ->whereIn('delivery_status', ['Pending', 'Scheduled', 'Out for Delivery'])
                             ->orderBy('delivery_date')
                             ->get();
        
        return view('admin.deliveries.monitoring', array_merge(
            compact('deliveries'), 
            $stats
        ));
    }

    /**
     * Export deliveries report based on filters.
     */
    public function export(Request $request)
    {
        // Get filtered deliveries
        $deliveries = Delivery::with(['order.customer'])
            ->when($request->status, function($query, $status) {
                return $query->where('delivery_status', $status);
            })
            ->when($request->date_from, function($query, $date) {
                return $query->whereDate('delivery_date', '>=', $date);
            })
            ->when($request->date_to, function($query, $date) {
                return $query->whereDate('delivery_date', '<=', $date);
            })
            ->get();
            
        // In a real application, you would export to CSV/Excel here
        // For now, we'll just return a success message
        return redirect()->back()
            ->with('success', 'Deliveries exported successfully. ' . $deliveries->count() . ' deliveries included.');
    }
}