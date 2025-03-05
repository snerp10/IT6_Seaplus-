<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Pricing;
use App\Models\Inventory;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AdminProductController extends Controller
{
    public function index(Request $request)
    {
        $products = Product::with(['pricing' => function($query) {
            $query->whereNull('end_date')->orWhere('end_date', '>=', now());
        }, 'inventories' => function($query) {
            $query->latest('inv_id')->limit(1);
        }, 'supplier']);
        
        // Apply filters if present
        if ($request->has('category') && !empty($request->category)) {
            $products = $products->where('category', $request->category);
        }
        
        if ($request->has('status') && !empty($request->status)) {
            $products = $products->where('status', $request->status);
        }
        
        $products = $products->get();
        
        return view('admin.products.index', compact('products'));
    }

    public function create()
    {
        $suppliers = Supplier::all();
        return view('admin.products.create', compact('suppliers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'unit' => 'required|string|max:50',
            'status' => 'required|in:Active,Inactive',
            'supp_id' => 'required|exists:suppliers,supp_id',
            'original_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
        ]);

        DB::beginTransaction();
        
        try {
            // Create product with direct supplier relationship
            $product = Product::create([
                'name' => $validated['name'],
                'category' => $validated['category'],
                'unit' => $validated['unit'],
                'status' => $validated['status'],
                'supp_id' => $validated['supp_id'],
            ]);
            
            // Calculate markup
            $markup = $validated['selling_price'] - $validated['original_price'];
            
            // Create pricing record
            Pricing::create([
                'prod_id' => $product->prod_id,
                'original_price' => $validated['original_price'],
                'selling_price' => $validated['selling_price'],
                'markup' => $markup,
                'start_date' => now(),
            ]);
            
            // Create initial inventory record with zero stock
            Inventory::create([
                'prod_id' => $product->prod_id,
                'curr_stock' => 0,
                'move_type' => 'Stock_in',
                'stock_in' => 0,
                'stock_out' => 0,
                'move_date' => now(),
            ]);
            
            // Handle image upload if needed
            if ($request->hasFile('image')) {
                $path = $request->file('image')->store('products', 'public');
                $product->image = $path;
                $product->save();
            }
            
            DB::commit();
            
            return redirect()->route('admin.products.index')
                ->with('success', 'Product created successfully.');
                
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'Failed to create product: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show(Product $product)
    {
        $product->load([
            'pricing' => function($query) {
                $query->whereNull('end_date')->orWhere('end_date', '>=', now());
            }, 
            'inventories' => function($query) {
                $query->latest('inv_id')->limit(1);
            },
            'supplier'
        ]);
        
        return view('admin.products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        $product->load([
            'pricing' => function($query) {
                $query->whereNull('end_date')->orWhere('end_date', '>=', now());
            },
            'supplier'
        ]);
        
        $suppliers = Supplier::all();
        
        return view('admin.products.edit', compact('product', 'suppliers'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'unit' => 'required|string|max:50',
            'status' => 'required|in:Active,Inactive',
            'supp_id' => 'required|exists:suppliers,supp_id',
            'original_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
        ]);

        DB::beginTransaction();
        
        try {
            // Update product with direct supplier relationship
            $product->update([
                'name' => $validated['name'],
                'category' => $validated['category'],
                'unit' => $validated['unit'],
                'status' => $validated['status'],
                'supp_id' => $validated['supp_id'],
            ]);

            // Calculate markup
            $markup = $validated['selling_price'] - $validated['original_price'];
            
            // Update or create pricing record
            $currentPricing = $product->pricing()->whereNull('end_date')->orWhere('end_date', '>=', now())->first();
            
            if ($currentPricing) {
                // If price changed, end the current pricing and create a new one
                if ($currentPricing->selling_price != $validated['selling_price'] || 
                    $currentPricing->original_price != $validated['original_price']) {
                    
                    // End current pricing
                    $currentPricing->end_date = now();
                    $currentPricing->save();
                    
                    // Create new pricing
                    Pricing::create([
                        'prod_id' => $product->prod_id,
                        'original_price' => $validated['original_price'],
                        'selling_price' => $validated['selling_price'],
                        'markup' => $markup,
                        'start_date' => now(),
                    ]);
                }
            } else {
                // Create new pricing if none exists
                Pricing::create([
                    'prod_id' => $product->prod_id,
                    'original_price' => $validated['original_price'],
                    'selling_price' => $validated['selling_price'],
                    'markup' => $markup,
                    'start_date' => now(),
                ]);
            }
            
            // Handle image upload if needed
            if ($request->hasFile('image')) {
                // Remove old image if exists
                if ($product->image && Storage::disk('public')->exists($product->image)) {
                    Storage::disk('public')->delete($product->image);
                }
                
                $path = $request->file('image')->store('products', 'public');
                $product->image = $path;
                $product->save();
            }
            
            DB::commit();
            
            return redirect()->route('admin.products.index')
                ->with('success', 'Product updated successfully.');
                
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'Failed to update product: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy(Product $product)
    {
        // Check if product has order details
        if ($product->orderDetails()->count() > 0) {
            return redirect()->route('admin.products.index')
                ->with('error', 'Cannot delete product with existing orders.');
        }
        
        try {
            $product->delete();
            return redirect()->route('admin.products.index')
                ->with('success', 'Product deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->route('admin.products.index')
                ->with('error', 'Failed to delete product: ' . $e->getMessage());
        }
    }

    public function addStock(Request $request, Product $product)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
            'note' => 'nullable|string',
        ]);

        DB::beginTransaction();
        
        try {
            // Get current stock from the latest inventory record
            $latestInventory = $product->inventories()->latest('inv_id')->first();
            $currentStock = $latestInventory ? $latestInventory->curr_stock : 0;
            
            // Create new inventory record
            Inventory::create([
                'prod_id' => $product->prod_id,
                'curr_stock' => $currentStock + $request->quantity,
                'move_type' => 'Stock_in',
                'stock_in' => $request->quantity,
                'stock_out' => 0,
                'move_date' => now(),
            ]);
            
            DB::commit();
            
            return redirect()->route('admin.products.show', $product)
                ->with('success', 'Stock added successfully.');
                
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'Failed to add stock: ' . $e->getMessage())
                ->withInput();
        }
    }
}
