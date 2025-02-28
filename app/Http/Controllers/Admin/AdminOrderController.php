<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use App\Models\Customer;
use App\Models\Delivery;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminOrderController extends Controller
{
    /**
     * Display a listing of orders with filtering options.
     */
    public function index(Request $request)
    {
        // Start building the query with essential relationships
        $orders = Order::with(['customer', 'delivery', 'orderDetails.product']);
        
        // Apply filters if present
        if ($request->has('status') && !empty($request->status)) {
            $orders = $orders->where('pay_status', $request->status);
        }
        
        if ($request->has('type') && !empty($request->type)) {
            $orders = $orders->where('order_type', $request->type);
        }
        
        if ($request->has('date_from') && !empty($request->date_from)) {
            $orders = $orders->whereDate('order_date', '>=', $request->date_from);
        }
        
        if ($request->has('date_to') && !empty($request->date_to)) {
            $orders = $orders->whereDate('order_date', '<=', $request->date_to);
        }
        
        // Get order statistics
        $totalOrders = Order::count();
        $pendingOrders = Order::where('pay_status', 'Pending')->count();
        $paidOrders = Order::where('pay_status', 'Paid')->count();
        $todayOrders = Order::whereDate('order_date', today())->count();
        
        // Order by date descending and paginate
        $orders = $orders->orderBy('order_date', 'desc')->paginate(15);
        
        return view('admin.orders.index', compact(
            'orders', 
            'totalOrders', 
            'pendingOrders',
            'paidOrders',
            'todayOrders'
        ));
    }

    /**
     * Show the form for creating a new order.
     */
    public function create()
    {
        $customers = Customer::all();
        $products = Product::where('status', 'Active')->get();
        
        return view('admin.orders.create', compact('customers', 'products'));
    }

    /**
     * Store a newly created order in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'cus_id' => 'required|exists:customers,cus_id',
            'pay_method' => 'required',
            'order_type' => 'required|in:Retail,Bulk',
            'products' => 'required|array',
            'delivery_date' => 'nullable|date',
            'street' => 'nullable|string',
            'city' => 'nullable|string',
            'province' => 'nullable|string',
            'special_instructions' => 'nullable|string',
            'delivery_cost' => 'nullable|numeric|min:0'
        ]);
        
        DB::beginTransaction();
        
        try {
            // Create the order
            $order = Order::create([
                'cus_id' => $request->cus_id,
                'order_date' => now(),
                'total_amount' => 0,
                'pay_status' => 'Pending',
                'order_type' => $request->order_type
            ]);
            
            // Create the delivery record
            $delivery = Delivery::create([
                'order_id' => $order->order_id,
                'delivery_date' => $request->delivery_date,
                'street' => $request->street,
                'city' => $request->city,
                'province' => $request->province,
                'special_instructions' => $request->special_instructions,
                'delivery_status' => 'Pending',
                'delivery_cost' => $request->delivery_cost ?? 0
            ]);
            
            // Process products and calculate total
            $totalAmount = 0;
            
            foreach ($request->products as $productData) {
                // Skip empty product selections
                if (empty($productData['prod_id'])) {
                    continue;
                }
                
                $productId = $productData['prod_id'];
                $quantity = $productData['quantity'] ?? 1;
                
                // Validate quantity is positive
                if ($quantity <= 0) {
                    continue;
                }
                
                $product = Product::findOrFail($productId);
                
                // Check if we have enough stock
                if ($product->getStockAttribute() < $quantity) {
                    throw new \Exception("Insufficient stock for {$product->name}. Only {$product->getStockAttribute()} available.");
                }
                
                // Calculate subtotal
                $subtotal = $product->price * $quantity;
                
                // Create order detail
                OrderDetail::create([
                    'order_id' => $order->order_id,
                    'prod_id' => $productId,
                    'quantity' => $quantity,
                    'subtotal' => $subtotal
                ]);
                
                // Update product stock
                $product->inventories()->create([
                    'curr_stock' => $product->getStockAttribute() - $quantity,
                    'move_type' => 'Stock_out',
                    'stock_out' => $quantity,
                    'move_date' => now(),
                ]);
                
                $totalAmount += $subtotal;
            }
            
            // Update order with total amount
            $order->update(['total_amount' => $totalAmount]);
            
            DB::commit();
            
            return redirect()->route('admin.orders.show', $order->order_id)
                             ->with('success', 'Order created successfully');
        }
        catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                             ->withInput()
                             ->with('error', 'Error creating order: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified order with all related information.
     */
    public function show(Order $order)
    {
        // Load all relationships needed for the view
        $order->load([
            'customer',
            'orderDetails.product',
            'delivery',
            'payments'
        ]);
        
        // Calculate order summary
        $orderSummary = [
            'subtotal' => $order->orderDetails->sum('subtotal'),
            'delivery_cost' => $order->delivery->delivery_cost ?? 0,
            'total_paid' => $order->payments->sum('amount_paid'),
            'total_due' => $order->total_amount,
        ];
        
        return view('admin.orders.show', compact('order', 'orderSummary'));
    }

    /**
     * Show the form for editing the specified order.
     */
    public function edit(Order $order)
    {
        $order->load([
            'customer',
            'orderDetails.product',
            'delivery',
            'payments'
        ]);
        
        $customers = Customer::all();
        
        return view('admin.orders.edit', compact('order', 'customers'));
    }

    /**
     * Update the specified order in storage.
     */
    public function update(Request $request, Order $order)
    {
        $validated = $request->validate([
            'pay_status' => 'required|in:Paid,Pending,Partially Paid,Cancelled',
            'order_type' => 'required|in:Retail,Bulk',
        ]);
        
        DB::beginTransaction();
        
        try {
            $order->update($validated);
            
            // Update delivery information if provided
            if ($request->has('delivery_status')) {
                $request->validate([
                    'delivery_status' => 'required|in:Pending,Out for Delivery,Delivered,Cancelled',
                    'delivery_date' => 'required|date',
                ]);
                
                $order->delivery->update([
                    'delivery_status' => $request->delivery_status,
                    'delivery_date' => $request->delivery_date,
                    'special_instructions' => $request->special_instructions,
                ]);
            }
            
            DB::commit();
            
            return redirect()->route('admin.orders.show', $order)
                ->with('success', 'Order updated successfully');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Failed to update order: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified order from storage.
     */
    public function destroy(Order $order)
    {
        // Check if the order can be deleted (e.g., not delivered)
        if ($order->delivery && $order->delivery->delivery_status === 'Delivered') {
            return redirect()->back()
                ->with('error', 'Cannot delete an order that has already been delivered.');
        }
        
        DB::beginTransaction();
        
        try {
            // If needed, restore product inventory before deletion
            foreach ($order->orderDetails as $detail) {
                if ($detail->product) {
                    // Create inventory movement record for the returned stock
                    \App\Models\Inventory::create([
                        'prod_id' => $detail->prod_id,
                        'curr_stock' => $detail->product->getStockAttribute() + $detail->quantity,
                        'move_type' => 'Stock_in',
                        'stock_in' => $detail->quantity,
                        'stock_out' => 0,
                        'move_date' => now(),
                    ]);
                }
            }
            
            // Delete order (cascade will handle details, delivery, payments)
            $order->delete();
            
            DB::commit();
            
            return redirect()->route('admin.orders.index')
                ->with('success', 'Order deleted successfully');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Failed to delete order: ' . $e->getMessage());
        }
    }
    
    /**
     * Update order status (AJAX).
     */
    public function updateStatus(Request $request, Order $order)
    {
        $validated = $request->validate([
            'status' => 'required|in:Paid,Pending,Partially Paid,Cancelled',
        ]);
        
        $order->update(['pay_status' => $validated['status']]);
        
        return response()->json([
            'success' => true,
            'message' => 'Order status updated successfully'
        ]);
    }
    
    /**
     * Add payment to an order.
     */
    public function addPayment(Request $request, Order $order)
    {
        $validated = $request->validate([
            'amount_paid' => 'required|numeric|min:0',
            'pay_method' => 'required|string',
        ]);
        
        DB::beginTransaction();
        
        try {
            $currentOutstanding = $order->total_amount;
            $newPaymentAmount = $validated['amount_paid'];
            
            // Create payment record
            $payment = Payment::create([
                'cus_id' => $order->cus_id,
                'order_id' => $order->order_id,
                'amount_paid' => $newPaymentAmount,
                'outstanding_balance' => max(0, $currentOutstanding - $newPaymentAmount),
                'pay_date' => now(),
                'pay_method' => $validated['pay_method'],
                'invoice_number' => 'INV-' . time(),
            ]);
            
            // Update order status
            if ($currentOutstanding <= $newPaymentAmount) {
                $order->update(['pay_status' => 'Paid']);
            } else {
                $order->update(['pay_status' => 'Partially Paid']);
            }
            
            DB::commit();
            
            return redirect()->route('admin.orders.show', $order)
                ->with('success', 'Payment added successfully');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Failed to add payment: ' . $e->getMessage())
                ->withInput();
        }
    }
    
    /**
     * Export orders report.
     */
    public function export(Request $request)
    {
        // This would normally use a library like Maatwebsite/Laravel-Excel
        // For now, we'll just simulate the functionality
        
        // Get filtered orders
        $orders = Order::with(['customer', 'orderDetails'])
            ->when($request->status, function($query, $status) {
                return $query->where('pay_status', $status);
            })
            ->when($request->date_from, function($query, $date) {
                return $query->whereDate('order_date', '>=', $date);
            })
            ->when($request->date_to, function($query, $date) {
                return $query->whereDate('order_date', '<=', $date);
            })
            ->get();
            
        // Return an export file or response
        return redirect()->back()
            ->with('success', 'Orders exported successfully. ' . $orders->count() . ' orders included.');
    }
    
    /**
     * Dashboard view for order management.
     */
    public function dashboard() 
    {
        // Get summary statistics
        $stats = [
            'total_orders' => Order::count(),
            'paid_orders' => Order::where('pay_status', 'Paid')->count(),
            'pending_orders' => Order::where('pay_status', 'Pending')->count(),
            'total_sales' => Order::where('pay_status', 'Paid')->sum('total_amount'),
            'recent_orders' => Order::with('customer')->latest()->take(5)->get(),
        ];
        
        // Get delivery statistics
        $deliveryStats = [
            'pending_deliveries' => Delivery::where('delivery_status', 'Pending')->count(),
            'out_for_delivery' => Delivery::where('delivery_status', 'Out for Delivery')->count(),
            'delivered' => Delivery::where('delivery_status', 'Delivered')->count(),
        ];
        
        return view('admin.orders.dashboard', compact('stats', 'deliveryStats'));
    }

    /**
     * Calculate order details.
     */
    public function calculate(Request $request)
    {
        // Validate form input
        $request->validate([
            'cus_id' => 'required|exists:customers,cus_id',
            'pay_method' => 'required',
            'order_type' => 'required|in:Retail,Bulk',
            'products' => 'array',
            'products.*.prod_id' => 'nullable|exists:products,prod_id',
            'products.*.quantity' => 'nullable|numeric|min:0',
        ]);

        $orderData = [
            'cus_id' => $request->cus_id,
            'pay_method' => $request->pay_method,
            'order_type' => $request->order_type,
            'delivery_date' => $request->delivery_date,
            'special_instructions' => $request->special_instructions,
            'street' => $request->street,
            'city' => $request->city,
            'province' => $request->province,
            'delivery_cost' => $request->delivery_cost,
            'products' => [],
            'total_amount' => 0,
        ];

        // Calculate product subtotals
        foreach ($request->products as $index => $productData) {
            if (!empty($productData['prod_id'])) {
                $product = Product::findOrFail($productData['prod_id']);
                $quantity = intval($productData['quantity'] ?? 0);
                
                if ($quantity > 0) {
                    $subtotal = $product->price * $quantity;
                    
                    $orderData['products'][$index] = [
                        'prod_id' => $product->prod_id,
                        'name' => $product->name,
                        'price' => $product->price,
                        'unit' => $product->unit,
                        'quantity' => $quantity,
                        'subtotal' => $subtotal,
                    ];
                    
                    $orderData['total_amount'] += $subtotal;
                }
            }
        }

        // If creating the order
        if ($request->action === 'create') {
            return $this->createOrder($orderData);
        }

        // Store calculation in session and redisplay the form
        session(['order_data' => $orderData]);
        return redirect()->back();
    }

    protected function createOrder(array $orderData)
    {
        DB::beginTransaction();

        try {
            // Create the order
            $order = Order::create([
                'cus_id' => $orderData['cus_id'],
                'order_date' => now(),
                'total_amount' => $orderData['total_amount'],
                'pay_method' => $orderData['pay_method'],
                'order_type' => $orderData['order_type'],
                'pay_status' => 'Pending'
            ]);

            // Create delivery info
            Delivery::create([
                'order_id' => $order->order_id,
                'delivery_date' => $orderData['delivery_date'],
                'street' => $orderData['street'],
                'city' => $orderData['city'],
                'province' => $orderData['province'],
                'special_instructions' => $orderData['special_instructions'],
                'delivery_status' => 'Pending',
                'delivery_cost' => $orderData['delivery_cost'] ?? 0
            ]);

            // Create order details and update inventory
            foreach ($orderData['products'] as $product) {
                if (!empty($product['prod_id']) && $product['quantity'] > 0) {
                    OrderDetail::create([
                        'order_id' => $order->order_id,
                        'prod_id' => $product['prod_id'],
                        'quantity' => $product['quantity'],
                        'subtotal' => $product['subtotal']
                    ]);

                    // Update product stock and add inventory record
                    $productModel = Product::findOrFail($product['prod_id']);
                    $currentStock = $productModel->getStockAttribute();
                    $newStock = $currentStock - $product['quantity'];

                    Inventory::create([
                        'prod_id' => $product['prod_id'],
                        'curr_stock' => $newStock,
                        'move_type' => 'Stock_out',
                        'stock_out' => $product['quantity'],
                        'move_date' => now(),
                    ]);
                }
            }

            DB::commit();
            session()->forget('order_data');
            return redirect()->route('admin.orders.index')->with('success', 'Order created successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to create order: ' . $e->getMessage());
        }
    }
}
