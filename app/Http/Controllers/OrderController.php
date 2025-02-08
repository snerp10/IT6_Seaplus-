namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\OrderDetail;
use Illuminate\Http\Request;
use DB;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::where('customer_id', auth()->id())->latest()->get();
        return view('orders.index', compact('orders'));
    }

    public function create()
    {
        $products = Product::where('stock_quantity', '>', 0)->get();
        return view('orders.create', compact('products'));
    }

    public function store(Request $request)
    {
        DB::transaction(function () use ($request) {
            $order = Order::create([
                'customer_id' => auth()->id(),
                'order_date' => now(),
                'total_amount' => $request->total_amount,
                'payment_method' => $request->payment_method,
                'payment_status' => 'Unpaid',
                'order_type' => $request->order_type,
                'delivery_status' => 'Pending'
            ]);

            foreach ($request->products as $productId => $details) {
                if ($details['quantity'] > 0) {
                    $product = Product::find($productId);
                    OrderDetail::create([
                        'order_id' => $order->order_id,
                        'product_id' => $productId,
                        'quantity' => $details['quantity'],
                        'subtotal' => $product->price * $details['quantity']
                    ]);
                    
                    $product->decrement('stock_quantity', $details['quantity']);
                }
            }
        });

        return redirect()->route('orders.index')->with('success', 'Order placed successfully');
    }

    public function show(Order $order)
    {
        $this->authorize('view', $order);
        return view('orders.show', compact('order'));
    }

    public function edit(Order $order)
    {
        $this->authorize('update', $order);
        return view('orders.edit', compact('order'));
    }

    public function update(Request $request, Order $order)
    {
        $this->authorize('update', $order);
        $order->update($request->only(['payment_method', 'payment_status']));
        return redirect()->route('orders.show', $order);
    }

    public function destroy(Order $order)
    {
        $this->authorize('delete', $order);
        $order->delete();
        return redirect()->route('orders.index');
    }
}
