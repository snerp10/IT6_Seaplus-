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
        DB::beginTransaction();
        
        try {
            $customer = Customer::where('user_id', auth()->id())->firstOrFail();
            
            // Create basic order first
            $order = Order::create([
                'customer_id' => $customer->customer_id,
                'order_date' => now(),
                'total_amount' => 0,
                'payment_method' => $request->payment_method, 
                'order_type' => $request->order_type,
                'payment_status' => 'Pending',
                'delivery_status' => 'Pending',
                'delivery_address' => null,
                'delivery_schedule' => null,
                'special_instructions' => null
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

        if ($order->payment_status === 'Paid') {
            return back()->with('error', 'Paid orders cannot be modified.');
        }

        // Only validate delivery-related fields
        $validated = $request->validate([
            'delivery_address' => 'required|string|max:500',
            'delivery_schedule' => 'required|date|after:now',
            'special_instructions' => 'nullable|string|max:500'
        ]);

        $order->update($validated);

        // Redirect to payment page after adding delivery details
        return redirect()->route('orders.payment', $order)
                       ->with('success', 'Delivery details saved. Please complete your payment.');
    }

    public function destroy(Order $order)
    {
        if (auth()->id() !== $order->customer_id) {
            abort(403, 'Unauthorized action.');
        }
        $order->delete();
        return redirect()->route('orders.index');
    }
  
    public function processPayment(Order $order)
    {
        try {
            if ($order->customer_id !== auth()->id()) {
                abort(403, 'Unauthorized action.');
            }

            $order->update([
                'payment_status' => 'Paid',
                'delivery_status' => 'Processing'
            ]);            return redirect()->route('orders.show', $order->order_id)                            ->with('success', 'Payment processed successfully!');        } catch (\Exception $e) {            return back()->with('error', 'Payment processing failed: ' . $e->getMessage());        }    }}