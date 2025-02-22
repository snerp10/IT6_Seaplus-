<?php
namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\OrderDetail;
use App\Models\Customer;
use App\Models\Delivery;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests; // Import this
use DB;

class OrderController extends Controller
{
    use AuthorizesRequests;
    public function index()
    {
        $orders = Order::where('customer_id', auth()->id())->latest()->get();
        return view('customer.orders.index', compact('orders'));
    }

    public function create()
    {
        $products = Product::where('stock', '>', 0)->get();
        return view('customer.orders.create', compact('products'));
    }

    public function store(Request $request)
    {
         // Validate the incoming request data
         $validatedData = $request->validate([
            'payment_method' => 'required|string',
            'order_type'     => 'required|string|in:Retail,Bulk',
            'products'       => 'required|array',
            'products.*.quantity' => 'required|integer|min:0'
        ]);
        DB::beginTransaction();
        
        try {
            $customer = Customer::where('user_id', auth()->id())->firstOrFail();
            
            // Create basic order first
            $order = Order::create([
                'customer_id' => $customer->customer_id,
                'order_date' => now(),
                'total_amount' => 0,
                'payment_method' => $validatedData['payment_method'], 
                'order_type' => $validatedData['order_type'],
                'payment_status' => 'Pending'
            ]);

            Delivery::create([
                'order_id' => $order->order_id,
                'truck_driver' => 'TBD', // Default value, update later
                'delivery_status' => 'Pending',
                'delivery_cost' => 0 // Default cost, update later
            ]);

            // Process products and calculate total
            $totalAmount = 0;
            foreach ($request->products as $productId => $details) {
                $quantity = intval($details['quantity']);
                if ($quantity > 0) {
                    $product = Product::findOrFail($productId);
                    if ($product->stock < $quantity) {
                        throw new \Exception("Insufficient stock for {$product->name}");
                    }

                    $subtotal = $product->price * $quantity;
                    OrderDetail::create([
                        'order_id' => $order->order_id,
                        'product_id' => $productId,
                        'quantity' => $quantity,
                        'subtotal' => $subtotal
                    ]);

                    $product->decrement('stock', $quantity);
                    $totalAmount += $subtotal;
                }
            }

            $order->update(['total_amount' => $totalAmount]);

            DB::commit();
            return redirect()->route('orders.edit', $order->order_id)
                           ->with('success', 'Order created! Please add delivery details.');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Order creation failed: ' . $e->getMessage());
            return back()->withInput()
                        ->with('error', 'Failed to place order: ' . $e->getMessage());
        }
    }
        


    public function show(Order $order)
    {
        $order->load('delivery');
        return view('customer.orders.show', compact('order'));
    }

    public function edit(Order $order)
    {
        
        return view('customer.orders.edit', compact('order'));
    }

    public function update(Request $request, Order $order)
    {
       

        if ($order->payment_status === 'Paid') {
            return back()->with('error', 'Paid orders cannot be modified.');
        }

        $validated = $request->validate([
            'order_type' => 'required|string|in:Retail,Bulk'
        ]);

        $order->update($validated);

        return redirect()->route('orders.payment', $order)
                        ->with('success', 'Order updated! Proceed to payment.');
    }

    public function destroy(Order $order)
    {
        
        $order->delete();
        return redirect()->route('orders.index')->with('success', 'Order deleted successfully.');
    }
  
    public function processPayment(Order $order)
    {
       
        
        $order->update(['payment_status' => 'Paid']);

        return redirect()->route('orders.show', $order->order_id)
                         ->with('success', 'Payment processed successfully!');
    }

}
