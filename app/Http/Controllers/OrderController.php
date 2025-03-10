<?php
namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\OrderDetail;
use App\Models\Customer;
use App\Models\Delivery;
use App\Models\Payment;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Inventory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index()
    {
        // Ensure we get the customer ID properly through the relationship
        $customerId = auth()->user()->customer->cus_id;
        
        $orders = Order::where('cus_id', $customerId)
            ->with(['orderDetails.product', 'payments', 'delivery'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);
            
        return view('customer.orders.index', compact('orders'));
    }

    public function create(Request $request)
    {
        // Get only active products with available stock
        $products = Product::where('status', 'Active')
            ->whereHas('inventories', function($query) {
                $query->select(DB::raw('MAX(inv_id) as inv_id'))
                    ->groupBy('prod_id')
                    ->havingRaw('MAX(curr_stock) > 0');
            })
            ->with(['pricing' => function($query) {
                $query->whereNull('end_date')
                    ->orWhere('end_date', '>=', now());
            }])
            ->get();
        
        // Check if a specific product was requested
        $selectedProductId = $request->query('product');
        $selectedProduct = null;
        
        if ($selectedProductId) {
            $selectedProduct = $products->firstWhere('prod_id', $selectedProductId);
        }
            
        return view('customer.orders.create', compact('products', 'selectedProduct'));
    }

    public function store(Request $request)
    {
        \Log::debug('STORING! Request data:', $request->all());
        
        try {
            $validatedData = $request->validate([
                'pay_method' => 'required|string|in:GCash,Cash on Delivery', // Changed 'COD' to 'Cash on Delivery'
                'order_type' => 'required|string|in:Retail,Bulk',
                'products' => 'required|array',
                'products.*.prod_id' => 'required|exists:products,prod_id',
                'products.*.quantity' => 'required|integer|min:0'
            ]);
            
            // Filter out products with zero quantity
            $productsToOrder = array_filter($request->products, function($product) {
                return isset($product['quantity']) && $product['quantity'] > 0;
            });
            
            // Check if any products were selected
            if (empty($productsToOrder)) {
                return back()->withInput()->with('error', 'Please select at least one product to order.');
            }
            
            \Log::debug('Validation passed successfully');
            
            DB::beginTransaction();
            \Log::debug('DB transaction started');

            // Get customer data through proper relationship
            $user = auth()->user();
            
            // Check if customer relation exists
            if (!$user || !$user->customer) {
                throw new \Exception("Customer profile not found for this account.");
            }
            
            $customer = $user->customer;

            // Create basic order
            $order = Order::create([
                'cus_id' => $customer->cus_id,
                'order_date' => now(),
                'total_amount' => 0, // We'll update this after adding items and delivery fee
                'order_status' => 'Pending',
                'order_type' => $validatedData['order_type']
            ]);

            // Create placeholder delivery with basic information
            Delivery::create([
                'order_id' => $order->order_id,
                'delivery_status' => 'Pending',
                'delivery_date' => now()->addDays(3), // Default delivery date
                'street' => $customer->address ?? 'To be updated',
                'city' => $customer->city ?? 'To be updated',
                'province' => $customer->province ?? 'To be updated',
                'delivery_cost' => $validatedData['order_type'] === 'Retail' ? 100 : 200 // Add delivery fee based on order type
            ]);

            // Process products and calculate total
            $totalAmount = 0;
            
            foreach ($productsToOrder as $productData) {
                $productId = $productData['prod_id'];
                $quantity = (int)$productData['quantity'];
                
                if ($quantity <= 0) continue;
                
                $product = Product::findOrFail($productId);
                $currentPrice = $product->pricing()->whereNull('end_date')
                    ->orWhere('end_date', '>=', now())
                    ->first();
                    
                if (!$currentPrice) {
                    throw new \Exception("Price information not found for product: {$product->name}");
                }
                
                // Check stock availability
                $currentStock = $product->getStockAttribute();
                if ($currentStock < $quantity) {
                    throw new \Exception("Insufficient stock for {$product->name}. Available: {$currentStock}");
                }

                // Calculate subtotal using current selling price
                $subtotal = $currentPrice->selling_price * $quantity;
                
                // Create order detail
                OrderDetail::create([
                    'order_id' => $order->order_id,
                    'prod_id' => $productId,
                    'quantity' => $quantity,
                    'subtotal' => $subtotal
                ]);

                // Update inventory
                Inventory::create([
                    'prod_id' => $productId,
                    'curr_stock' => $currentStock - $quantity,
                    'move_type' => 'Stock_out',
                    'stock_out' => $quantity,
                    'stock_in' => 0,
                    'move_date' => now()
                ]);
                
                $totalAmount += $subtotal;
            }

            // Add delivery fee to total amount
            $deliveryFee = $validatedData['order_type'] === 'Retail' ? 100 : 200;
            $totalAmount += $deliveryFee;
            
            // Update order total with the delivery fee included
            $order->update(['total_amount' => $totalAmount]);
            
            // Create initial payment record
            Payment::create([
                'order_id' => $order->order_id,
                'cus_id' => $customer->cus_id,
                'amount_paid' => 0,
                'outstanding_balance' => $totalAmount,
                'pay_date' => now(),
                'pay_method' => $validatedData['pay_method'],
                'pay_status' => 'Unpaid', // Changed from 'Pending' to 'Unpaid'
                'invoice_number' => 'INV-' . time() . '-' . $order->order_id
            ]);

            DB::commit();
            
            // Redirect directly to delivery edit instead of payment for Cash on Delivery
            if ($validatedData['pay_method'] === 'Cash on Delivery') {
                // For retail orders, redirect directly to delivery details
                if ($validatedData['order_type'] === 'Retail') {
                    return redirect()->route('customer.delivery.edit', $order->order_id)
                        ->with('success', 'Order created successfully! Please provide your delivery details.');
                }
                // For bulk orders with COD, redirect to payment for down payment
                else {
                    return redirect()->route('customer.orders.payment', $order->order_id)
                        ->with('success', 'Bulk order created! Please complete your down payment.');
                }
            }
            
            // Redirect to payment for GCash
            return redirect()->route('customer.orders.payment', $order->order_id)
                ->with('success', 'Order created successfully! Please complete your payment.');

        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation failed: ' . json_encode($e->errors()));
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Exception in order creation: ' . $e->getMessage());
            return back()->withInput()
                ->with('error', 'Failed to place order: ' . $e->getMessage());
        }
    }

    public function show(Order $order)
    {
        // Get customer ID properly
        $customerId = auth()->user()->customer->cus_id;
        
        // Authorization check - verify that the order belongs to the authenticated customer
        if ($order->cus_id !== $customerId) {
            \Log::warning('Unauthorized access attempt to order', [
                'user_id' => auth()->id(),
                'customer_id' => $customerId,
                'order_id' => $order->order_id,
                'order_customer_id' => $order->cus_id
            ]);
            abort(403, 'Unauthorized access: This order does not belong to your account.');
        }
        
        // Load relationships
        $order->load(['orderDetails.product', 'payments']);
        
        // Create delivery record if it doesn't exist
        if (!$order->delivery) {
            $delivery = new Delivery([
                'order_id' => $order->order_id,
                'delivery_status' => 'Pending'
            ]);
            $delivery->save();
            
            // Reload to include the new delivery
            $order->load('delivery');
        }
        
        return view('customer.orders.show', compact('order'));
    }

    public function destroy(Order $order)
    {
        // Get customer ID properly
        $customerId = auth()->user()->customer->cus_id;
        
        // Authorization check
        if ($order->cus_id !== $customerId) {
            \Log::warning('Unauthorized deletion attempt', [
                'user_id' => auth()->id(),
                'customer_id' => $customerId,
                'order_id' => $order->order_id
            ]);
            abort(403, 'Unauthorized action.');
        }

        // Only allow cancellation of pending orders
        if ($order->order_status !== 'Pending') {
            return back()->with('error', 'Only pending orders can be canceled.');
        }

        DB::beginTransaction();
        try {
            // Return products to inventory
            foreach ($order->orderDetails as $detail) {
                $product = Product::findOrFail($detail->prod_id);
                $currentStock = $product->getStockAttribute();
                
                // Create inventory record for returned stock
                Inventory::create([
                    'prod_id' => $detail->prod_id,
                    'curr_stock' => $currentStock + $detail->quantity,
                    'move_type' => 'Stock_in',
                    'stock_in' => $detail->quantity,
                    'stock_out' => 0,
                    'move_date' => now(),
                ]);
            }

            // Delete order (will cascade to details, delivery, payments)
            $order->delete();
            
            DB::commit();
            return redirect()->route('customer.orders.index')->with('success', 'Order canceled successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to cancel order: ' . $e->getMessage());
        }
    }
}
