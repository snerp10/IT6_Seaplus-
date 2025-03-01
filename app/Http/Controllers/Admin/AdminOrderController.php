<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use App\Models\Customer;
use App\Models\Delivery;
use App\Models\Payment;
use App\Models\Inventory;
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
        
        // Get basic statistics for the header
        $basicStats = $this->getBasicOrderStats();
        
        // Order by date descending and paginate
        $orders = $orders->orderBy('order_date', 'desc')->paginate(15);
        
        return view('admin.orders.index', array_merge(
            compact('orders'), 
            $basicStats
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
        \Log::debug('Store method called', $request->all());
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
        \Log::debug('Checkpoint 1');
        DB::beginTransaction();
        
        try {
            // Create the order (without pay_method)
            \Log::debug('Checkpoint 2');
            
            $order = Order::create([
                'cus_id' => $request->cus_id,
                'order_date' => now(),
                'total_amount' => 0, 
                'pay_status' => 'Pending',
                'order_type' => $request->order_type
            ]);
            \Log::debug('Order created successfully', ['order_id' => $order->order_id]);
        } catch (\Exception $e) {
            \Log::error('Order creation failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e; // Re-throw to be caught by the outer try-catching block
            }
        try {
            \Log::debug('Checkpoint 2.5');
            
            // Create initial payment record with pay_method
            $payment = Payment::create([
                'cus_id' => $request->cus_id,
                'order_id' => $order->order_id,
                'amount_paid' => 0,
                'change_amount' => 0.00,
                'outstanding_balance' => 0, // Set to 0 initially, update after calculating total
                'pay_date' => now(),
                'pay_method' => $request->pay_method,
                'reference_number' => $request->pay_method === 'GCash' ? ($request->reference_number ?? null) : null,
                'invoice_number' => 'INV-' . time() . '-' . $order->order_id, // Make more unique
                'pay_status' => 'Pending',
            ]);

            \Log::debug('Checkpoint 3');

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
            
            \Log::debug('Checkpoint 4');
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
                Inventory::create([
                    'prod_id' => $productId,
                    'curr_stock' => $product->getStockAttribute() - $quantity,
                    'move_type' => 'Stock_out',
                    'stock_out' => $quantity,
                    'stock_in' => 0,
                    'move_date' => now(),
                ]);
                
                $totalAmount += $subtotal;
            }
            
            // Update order with total amount
            $order->update(['total_amount' => $totalAmount]);
            
            // Update the outstanding balance in the initial payment record
            $payment->update([
                'outstanding_balance' => $totalAmount
            ]);
            
            DB::commit();
            
            \Log::debug('Order stored', $order->toArray());
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
    $subtotal = $order->orderDetails->sum('subtotal');
    $deliveryCost = $order->delivery->delivery_cost ?? 0;
    
    $orderSummary = [
        'subtotal' => $subtotal,
        'delivery_cost' => $deliveryCost,
        'total_paid' => $order->payments->sum('amount_paid'),
        'total_due' => $subtotal + $deliveryCost,
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
        $products = Product::where('status', 'Active')->get();
        $orderDetails = $order->orderDetails;
        $delivery = $order->delivery;
        
        return view('admin.orders.edit', compact('order', 'customers', 'products', 'orderDetails', 'delivery'));
    }

    /**
     * Update the specified order in storage.
     */
    public function update(Request $request, Order $order)
    {
        $validated = $request->validate([
            'pay_status' => 'required|in:Paid,Pending,Partially Paid,Cancelled',
            'order_type' => 'required|in:Retail,Bulk',
            'products' => 'nullable|array',
            'products.*.prod_id' => 'nullable|exists:products,prod_id',
            'products.*.quantity' => 'nullable|integer|min:0',
            'delivery_date' => 'nullable|date',
            'street' => 'nullable|string',
            'city' => 'nullable|string',
            'province' => 'nullable|string',
            'special_instructions' => 'nullable|string',
            'delivery_cost' => 'nullable|numeric|min:0',
        ]);
        
        DB::beginTransaction();
        
        try {
            // Update basic order information
            $order->update([
                'pay_status' => $validated['pay_status'],
                'order_type' => $validated['order_type']
            ]);
            
            // Update delivery information if provided
            if ($request->has('delivery_date') || $request->has('street')) {
                $order->delivery->update([
                    'delivery_date' => $request->delivery_date,
                    'street' => $request->street,
                    'city' => $request->city,
                    'province' => $request->province,
                    'special_instructions' => $request->special_instructions,
                    'delivery_cost' => $request->delivery_cost ?? 0
                ]);
            }
            
            // Handle product updates if products were submitted
            if ($request->has('products')) {
                $totalAmount = 0;
                $currentDetails = $order->orderDetails->keyBy('prod_id');
                $updatedProductIds = [];
                
                foreach ($request->products as $productData) {
                    // Skip empty product selections
                    if (empty($productData['prod_id'])) {
                        continue;
                    }
                    
                    $productId = $productData['prod_id'];
                    $quantity = (int)$productData['quantity'] ?? 0;
                    $updatedProductIds[] = $productId;
                    
                    // Skip if quantity is zero
                    if ($quantity <= 0) {
                        continue;
                    }
                    
                    $product = Product::findOrFail($productId);
                    $subtotal = $product->price * $quantity;
                    
                    // Check if this product is already in the order
                    if ($currentDetails->has($productId)) {
                        $detail = $currentDetails->get($productId);
                        $oldQuantity = $detail->quantity;
                        
                        // Update existing order detail if quantity changed
                        if ($quantity != $oldQuantity) {
                            // Update inventory - adjust the difference
                            $quantityDiff = $quantity - $oldQuantity;
                            
                            if ($quantityDiff > 0) {
                                // Adding more of the product - check if enough stock
                                if ($product->getStockAttribute() < $quantityDiff) {
                                    throw new \Exception("Insufficient stock for {$product->name}. Only {$product->getStockAttribute()} additional units available.");
                                }
                                
                                // Record stock out for additional quantity
                                Inventory::create([
                                    'prod_id' => $productId,
                                    'curr_stock' => $product->getStockAttribute() - $quantityDiff,
                                    'move_type' => 'Stock_out',
                                    'stock_out' => $quantityDiff,
                                    'stock_in' => 0,
                                    'move_date' => now(),
                                ]);
                            } elseif ($quantityDiff < 0) {
                                // Returning some of the product
                                Inventory::create([
                                    'prod_id' => $productId,
                                    'curr_stock' => $product->getStockAttribute() + abs($quantityDiff),
                                    'move_type' => 'Stock_in',
                                    'stock_in' => abs($quantityDiff),
                                    'stock_out' => 0,
                                    'move_date' => now(),
                                ]);
                            }
                            
                            // Update the order detail
                            $detail->update([
                                'quantity' => $quantity,
                                'subtotal' => $subtotal
                            ]);
                        }
                    } else {
                        // This is a new product being added to the order
                        // Check if enough stock
                        if ($product->getStockAttribute() < $quantity) {
                            throw new \Exception("Insufficient stock for {$product->name}. Only {$product->getStockAttribute()} available.");
                        }
                        
                        // Create new order detail
                        OrderDetail::create([
                            'order_id' => $order->order_id,
                            'prod_id' => $productId,
                            'quantity' => $quantity,
                            'subtotal' => $subtotal
                        ]);
                        
                        // Record stock out
                        Inventory::create([
                            'prod_id' => $productId,
                            'curr_stock' => $product->getStockAttribute() - $quantity,
                            'move_type' => 'Stock_out',
                            'stock_out' => $quantity,
                            'stock_in' => 0,
                            'move_date' => now(),
                        ]);
                    }
                    
                    // Add to total amount
                    $totalAmount += $subtotal;
                }
                
                // Handle products that were removed from the order
                foreach ($currentDetails as $prodId => $detail) {
                    if (!in_array($prodId, $updatedProductIds)) {
                        // Return the quantity to inventory
                        $returnQuantity = $detail->quantity;
                        
                        Inventory::create([
                            'prod_id' => $prodId,
                            'curr_stock' => $detail->product->getStockAttribute() + $returnQuantity,
                            'move_type' => 'Stock_in',
                            'stock_in' => $returnQuantity,
                            'stock_out' => 0,
                            'move_date' => now(),
                        ]);
                        
                        // Delete the order detail
                        $detail->delete();
                    }
                }
                
                // Add delivery cost to total
                $totalAmount += $order->delivery->delivery_cost ?? 0;
                
                // Update order with new total amount
                $order->update(['total_amount' => $totalAmount]);
                
                // Update payment outstanding balance if there's an initial payment record
                $initialPayment = $order->payments->where('amount_paid', 0)->first();
                if ($initialPayment) {
                    $initialPayment->update([
                        'outstanding_balance' => $totalAmount
                    ]);
                }
                
                // Check if payment status needs to be updated based on total
                $totalPaid = $order->payments->sum('amount_paid');
                if ($totalPaid >= $totalAmount && $totalAmount > 0) {
                    $order->update(['pay_status' => 'Paid']);
                } elseif ($totalPaid > 0 && $totalPaid < $totalAmount) {
                    $order->update(['pay_status' => 'Partially Paid']);
                }
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
                    Inventory::create([
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
            // Calculate the remaining balance after previous payments
            $totalPaid = $order->payments->sum('amount_paid');
            $remainingBalance = $order->total_amount - $totalPaid;
            $newPaymentAmount = $validated['amount_paid'];
            
            // Check if payment amount is valid
            if ($newPaymentAmount > $remainingBalance) {
                return redirect()->back()
                    ->with('error', 'Payment amount exceeds the remaining balance')
                    ->withInput();
            }
            
            // Create payment record
            $payment = Payment::create([
                'cus_id' => $order->cus_id,
                'order_id' => $order->order_id,
                'amount_paid' => $newPaymentAmount,
                'change_amount' => 0, // Add change if cash payment exceeds amount
                'outstanding_balance' => max(0, $remainingBalance - $newPaymentAmount),
                'pay_date' => now(),
                'pay_method' => $validated['pay_method'],
                'invoice_number' => 'INV-' . time() . '-' . $order->order_id,
                'pay_status' => 'Completed',
                'reference_number' => $request->reference_number ?? null,
            ]);
            
            // Update order status based on total payments
            $newTotalPaid = $totalPaid + $newPaymentAmount;
            if ($newTotalPaid >= $order->total_amount) {
                $order->update(['pay_status' => 'Paid']);
            } else {
                $order->update(['pay_status' => 'Partially Paid']);
            }
            
            DB::commit();
            
            return redirect()->route('admin.orders.show', $order)
                ->with('success', 'Payment of ₱' . number_format($newPaymentAmount, 2) . ' added successfully');
                
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
     * Dashboard view for order management with comprehensive analytics.
     */
    public function dashboard(Request $request) 
    {
        // Get basic stats
        $basicStats = $this->getBasicOrderStats();
        
        $totalOrders = $basicStats['total_orders'];
        
        // Additional analytics that are only needed for the dashboard
        $analytics = [
            // Sales analytics over time
            'monthly_sales' => Order::where('pay_status', 'Paid')
                ->selectRaw('MONTH(order_date) as month, SUM(total_amount) as total')
                ->whereYear('order_date', date('Y'))
                ->groupBy('month')
                ->orderBy('month')
                ->get()
                ->pluck('total', 'month')
                ->toArray(),
                
            // Product performance
            'top_products' => DB::table('order_details')
                ->join('products', 'order_details.prod_id', '=', 'products.prod_id')
                ->selectRaw('products.name, SUM(order_details.quantity) as total_quantity')
                ->groupBy('products.prod_id', 'products.name')
                ->orderByDesc('total_quantity')
                ->limit(5)
                ->get(),
                
            // Customer insights
            'top_customers' => DB::table('orders')
                ->join('customers', 'orders.cus_id', '=', 'customers.cus_id')
                ->selectRaw('customers.fname, customers.lname, COUNT(orders.order_id) as order_count, SUM(orders.total_amount) as total_spent')
                ->groupBy('customers.cus_id', 'customers.fname', 'customers.lname')
                ->orderByDesc('total_spent')
                ->limit(5)
                ->get(),
                
            // Delivery statistics
            'delivery_stats' => [
                'pending_deliveries' => Delivery::where('delivery_status', 'Pending')->count(),
                'out_for_delivery' => Delivery::where('delivery_status', 'Out for Delivery')->count(),
                'delivered' => Delivery::where('delivery_status', 'Delivered')->count(),
            ]
        ];
        
        $orderData = [
            'pay_method' => $request->pay_method,
            'order_type' => $request->order_type,
            'delivery_date' => $request->delivery_date,
            'street' => $request->street,
            'city' => $request->city,
            'province' => $request->province,
            'special_instructions' => $request->special_instructions,
            'delivery_cost' => $request->delivery_cost ?: 0,
            'products' => [],
            'subtotal' => 0
        ];
        
        $subtotal = 0;
        $customer = null;
        
        // Get customer if ID is provided
        if ($request->has('cus_id')) {
            $customer = Customer::find($request->cus_id);
        }
        
        // Process products
        if ($request->has('products')) {
            foreach ($request->products as $index => $productData) {
                if (!empty($productData['prod_id'])) {
                    $product = Product::findOrFail($productData['prod_id']);
                    $quantity = intval($productData['quantity']);
                    
                    if ($quantity <= 0) continue;
                    
                    // Check stock availability
                    if ($quantity > $product->getStockAttribute()) {
                        return back()->with('error', 'Not enough stock for ' . $product->name . '. Available: ' . $product->getStockAttribute());
                    }
                    
                    $productSubtotal = $product->price * $quantity;
                    $subtotal += $productSubtotal;
                    
                    $orderData['products'][] = [
                        'prod_id' => $product->prod_id,
                        'name' => $product->name,
                        'category' => $product->category,
                        'price' => $product->price,
                        'unit' => $product->unit,
                        'quantity' => $quantity,
                        'subtotal' => $productSubtotal,
                    ];
                }
            }
        }
        
        // Calculate total amount
        $orderData['subtotal'] = $subtotal;
        $orderData['total_amount'] = $subtotal + $orderData['delivery_cost'];
        
        return view('admin.orders.calculate', compact('orderData', 'customer', 'analytics', 'basicStats', 'totalOrders'));
    }

    /**
     * Calculate order details before final placement.
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
            'delivery_date' => 'nullable|date',
            'street' => 'nullable|string',
            'city' => 'nullable|string',
            'province' => 'nullable|string',
            'special_instructions' => 'nullable|string',
            'delivery_cost' => 'nullable|numeric|min:0'
        ]);

        $orderData = [
            'cus_id' => $request->cus_id,
            'pay_method' => $request->pay_method,
            'order_type' => $request->order_type,
            'delivery_date' => $request->delivery_date,
            'street' => $request->street,
            'city' => $request->city,
            'province' => $request->province,
            'special_instructions' => $request->special_instructions,
            'delivery_cost' => $request->delivery_cost ?: 0,
            'products' => [],
            'subtotal' => 0
        ];
        
        $subtotal = 0;
        $customer = Customer::findOrFail($request->cus_id);
        
        // Process products
        if ($request->has('products')) {
            foreach ($request->products as $index => $productData) {
                if (!empty($productData['prod_id'])) {
                    $product = Product::findOrFail($productData['prod_id']);
                    $quantity = intval($productData['quantity'] ?? 0);
                    
                    if ($quantity <= 0) continue;
                    
                    // Check stock availability
                    if ($quantity > $product->getStockAttribute()) {
                        return back()->with('error', 'Not enough stock for ' . $product->name . '. Available: ' . $product->getStockAttribute());
                    }
                    
                    $productSubtotal = $product->price * $quantity;
                    $subtotal += $productSubtotal;
                    
                    $orderData['products'][] = [
                        'prod_id' => $product->prod_id,
                        'name' => $product->name,
                        'category' => $product->category,
                        'price' => $product->price,
                        'unit' => $product->unit,
                        'quantity' => $quantity,
                        'subtotal' => $productSubtotal,
                    ];
                }
            }
        }
        
        // Calculate total amount
        $orderData['subtotal'] = $subtotal;
        $orderData['total_amount'] = $subtotal + $orderData['delivery_cost'];
        
        return view('admin.orders.calculate', compact('orderData', 'customer'));
    }

    /**
     * Get basic order statistics for dashboards and reports.
     *
     * @return array
     */
    private function getBasicOrderStats()
    {
        return [
            'total_orders' => Order::count(),
            'pending_orders' => Order::where('pay_status', 'Pending')->count(),
            'paid_orders' => Order::where('pay_status', 'Paid')->count(),
            'partially_paid_orders' => Order::where('pay_status', 'Partially Paid')->count(),
            'cancelled_orders' => Order::where('pay_status', 'Cancelled')->count(),
            'total_revenue' => Order::where('pay_status', 'Paid')->sum('total_amount'),
            'pending_revenue' => Order::where('pay_status', 'Pending')->sum('total_amount'),
        ];
    }
}
