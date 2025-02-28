<?php
namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\OrderDetail;
use App\Models\Customer;
use App\Models\Delivery;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Inventory;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests; // Import this
use DB;

class OrderController extends Controller
{
    use AuthorizesRequests;
    public function index()
    {
        $orders = Order::where('cus_id', auth()->id())->latest()->get();
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
            'pay_method' => 'required|string',
            'order_type' => 'required|string|in:Retail,Bulk',
            'products' => 'required|array',
            'products.*.quantity' => 'required|integer|min:0'
        ]);
        DB::beginTransaction();

        try {
            $customer = auth()->user()->customer;

            // Create basic order first
            $order = Order::create([
                'cus_id' => $customer->cus_id,
                'order_date' => now(),
                'total_amount' => 0,
                'pay_method' => $validatedData['pay_method'],
                'order_type' => $validatedData['order_type'],
                'pay_status' => 'Pending'
            ]);

            Delivery::create([
                'order_id' => $order->order_id,
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
                        'prod_id' => $productId,
                        'quantity' => $quantity,
                        'subtotal' => $subtotal
                    ]);

                    $product->decrement('stock', $quantity);
                    $totalAmount += $subtotal;

                    // Record stock out in inventory
                    Inventory::create([
                        'prod_id' => $productId,
                        'curr_stock' => $product->stock,
                        'move_type' => 'Stock_out',
                        'quantity' => $quantity,
                        'move_date' => now(),
                    ]);
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
       

        if ($order->pay_status === 'Paid') {
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
        \Log::info('Attempting to cancel order: ' . $order->order_id); // Add this line
        if ($order->pay_status === 'Paid') {
            return back()->with('error', 'Paid orders cannot be canceled.');
        }

        DB::beginTransaction();
        try {
            foreach ($order->orderDetails as $detail) {
                $product = Product::findOrFail($detail->prod_id);
                $product->increment('stock', $detail->quantity);

                Inventory::create([
                    'prod_id' => $product->prod_id,
                    'curr_stock' => $product->stock,
                    'move_type' => 'Stock_in(Canceled)',
                    'quantity' => $detail->quantity,
                    'move_date' => now(),
                ]);
            }

            $order->delete();
            DB::commit();
            return redirect()->route('orders.index')->with('success', 'Order canceled successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Order cancelation failed: ' . $e->getMessage());
            return back()->withInput()
                ->with('error', 'Failed to cancel order: ' . $e->getMessage());
        }
    }
  
    public function processPayment(Order $order)
    {
       
        
        $order->update(['pay_status' => 'Paid']);

        return redirect()->route('orders.show', $order->order_id)
                         ->with('success', 'Payment processed successfully!');
    }

}
