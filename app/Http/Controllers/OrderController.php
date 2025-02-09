<?php
namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\OrderDetail;
use App\Models\Customer;
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
        return view('orders.index', compact('orders'));
    }

    public function create()
    {
        $products = Product::where('stock', '>', 0)->get();
        return view('orders.create', compact('products'));
    }

    public function store(Request $request)
    {
        

        // Start database transaction
        DB::beginTransaction();
        
        try {
            // Get customer ID from authenticated user
            $customer = Customer::where('user_id', auth()->id())->first();
            
            // Create the order
            $order = Order::create([
                'customer_id' => $customer->customer_id,
                'order_date' => now(),
                'total_amount' => $request->total_amount,
                'payment_method' => $request->payment_method,
                'payment_status' => 'Unpaid', // Default status
                'order_type' => $request->order_type,
                'delivery_status' => 'Pending' // Default status
            ]);

            // Process each product in the order
            foreach ($request->products as $productId => $details) {
                $quantity = intval($details['quantity']);
                if ($quantity > 0) {
                    $product = Product::findOrFail($productId);
                    
                    // Check if enough stock available
                    if ($product->stock < $quantity) {
                        throw new \Exception("Insufficient stock for {$product->name}");
                    }

                    // Create order detail
                    OrderDetail::create([
                        'order_id' => $order->order_id,
                        'product_id' => $productId,
                        'quantity' => $quantity,
                        'subtotal' => $product->price * $quantity
                    ]);

                    // Update product stock
                    $product->decrement('stock', $quantity);
                }
            }

            DB::commit();
            return redirect()->route('orders.show', $order->order_id)
                           ->with('success', 'Order placed successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                        ->with('error', 'Failed to place order: ' . $e->getMessage());
        }
    }
        


    public function show(Order $order)
    {
        if (auth()->id() !== $order->customer_id) {
            abort(403, 'Unauthorized action.');
        }
        return view('orders.show', compact('order'));

    }

    public function edit(Order $order)
    {
        if (auth()->id() !== $order->customer_id) {
            abort(403, 'Unauthorized action.');
        }
        return view('orders.edit', compact('order'));
    }

    public function update(Request $request, Order $order)
    {
        if (auth()->id() !== $order->customer_id) {
            abort(403, 'Unauthorized action.');
        }
        $order->update($request->only(['payment_method', 'payment_status']));
        return redirect()->route('orders.show', $order);
    }

    public function destroy(Order $order)
    {
        if (auth()->id() !== $order->customer_id) {
            abort(403, 'Unauthorized action.');
        }
        $order->delete();
        return redirect()->route('orders.index');
    }
}
