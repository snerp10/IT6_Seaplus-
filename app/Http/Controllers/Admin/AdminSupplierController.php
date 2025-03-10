<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminSupplierController extends Controller
{
    public function index(Request $request)
    {
        $query = Supplier::query();
        
        // Apply filters
        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('company_name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%')
                  ->orWhere('contact_number', 'like', '%' . $request->search . '%');
            });
        }
        
        if ($request->product_type) {
            $query->where('prod_type', $request->product_type);
        }
        
        if ($request->location) {
            $query->where(function($q) use ($request) {
                $q->where('city', $request->location)
                  ->orWhere('province', $request->location);
            });
        }
        
        $suppliers = $query->paginate(10);
        
        // Get unique locations and product types for filters
        $locations = Supplier::select('city')->distinct()->pluck('city')->toArray();
        $productTypes = Supplier::select('prod_type')->distinct()->pluck('prod_type')->toArray();
        
        return view('admin.suppliers.index', compact('suppliers', 'locations', 'productTypes'));
    }

    public function create()
    {
        $products = Product::all();
        return view('admin.suppliers.create', compact('products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'company_name' => 'required|string|max:255',
            'contact_number' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'street' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'province' => 'required|string|max:255',
            'prod_type' => 'required|string|max:255',
        ]);

        DB::beginTransaction();
        try {
            // Set status based on the active_supplier checkbox
            $status = $request->has('active_supplier') ? 'Active' : 'Inactive';
            
            // Create the supplier
            $supplier = Supplier::create([
                'company_name' => $request->company_name,
                'contact_number' => $request->contact_number,
                'email' => $request->email,
                'street' => $request->street,
                'city' => $request->city,
                'province' => $request->province,
                'prod_type' => $request->prod_type,
                'notes' => $request->notes,
                'status' => $status,
                'is_preferred' => $request->has('preferred_supplier') ? 1 : 0,
            ]);

            // Handle direct product assignments
            if ($request->has('products')) {
                foreach ($request->products as $productData) {
                    if (!empty($productData['prod_id'])) {
                        // Update the product to have this supplier
                        $product = Product::find($productData['prod_id']);
                        if ($product) {
                            $product->supp_id = $supplier->supp_id;
                            $product->save();
                        }
                    }
                }
            }

            DB::commit();
            return redirect()->route('admin.suppliers.index')->with('success', 'Supplier created successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error creating supplier: ' . $e->getMessage())->withInput();
        }
    }

    public function show(Supplier $supplier)
    {
        // Load directly related products
        $suppliedProducts = Product::where('supp_id', $supplier->supp_id)->get();
            
        return view('admin.suppliers.show', compact('supplier', 'suppliedProducts'));
    }

    public function edit(Supplier $supplier)
    {
        // Get all products for selection
        $products = Product::all();
        
        // Get directly related products
        $supplierProducts = Product::where('supp_id', $supplier->supp_id)->get();
        
        return view('admin.suppliers.edit', compact('supplier', 'products', 'supplierProducts'));
    }

    public function update(Request $request, Supplier $supplier)
    {
        $request->validate([
            'company_name' => 'required|string|max:255',
            'contact_number' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'street' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'province' => 'required|string|max:255',
            'prod_type' => 'required|string|max:255',
        ]);
        
        DB::beginTransaction();
        
        try {
            $status = $request->has('active_supplier') ? 'Active' : 'Inactive';
            
            $supplier->update([
                'company_name' => $request->company_name,
                'contact_number' => $request->contact_number,
                'email' => $request->email,
                'street' => $request->street,
                'city' => $request->city,
                'province' => $request->province,
                'prod_type' => $request->prod_type,
                'notes' => $request->notes,
                'status' => $status,
                'is_preferred' => $request->has('preferred_supplier') ? 1 : 0,
            ]);

            // First, get the current products for this supplier
            $currentProductIds = $supplier->products->pluck('prod_id')->toArray();
            
            // Get the updated product IDs from the form
            $updatedProductIds = [];
            if ($request->has('products')) {
                foreach ($request->products as $productData) {
                    if (!empty($productData['prod_id'])) {
                        $updatedProductIds[] = $productData['prod_id'];
                    }
                }
            }
            
            // Find products to remove (products that were associated with this supplier but aren't in the new list)
            $productsToRemove = array_diff($currentProductIds, $updatedProductIds);
            
            // Remove supplier association from these products
            if (!empty($productsToRemove)) {
                Product::whereIn('prod_id', $productsToRemove)
                    ->where('supp_id', $supplier->supp_id)
                    ->update(['supp_id' => null]);
            }
            
            // Add or update product associations
            if ($request->has('products')) {
                foreach ($request->products as $productData) {
                    if (!empty($productData['prod_id'])) {
                        // Update the product to have this supplier
                        $product = Product::find($productData['prod_id']);
                        if ($product) {
                            $product->supp_id = $supplier->supp_id;
                            $product->save();
                        }
                    }
                }
            }

            DB::commit();
            return redirect()->route('admin.suppliers.show', $supplier->supp_id)->with('success', 'Supplier updated successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error updating supplier: ' . $e->getMessage())->withInput();
        }
    }
    
    public function export()
    {
        // Export logic would go here
        return redirect()->route('admin.suppliers.index')->with('success', 'Supplier data exported successfully');
    }
    
    public function destroy(Supplier $supplier)
    {
        try {
            $supplierName = $supplier->company_name;
            
            DB::beginTransaction();
            
            // Clear supplier reference from products
            Product::where('supp_id', $supplier->supp_id)->update(['supp_id' => null]);
            
            $supplier->delete();
            DB::commit();
            
            return redirect()->route('admin.suppliers.index')
                ->with('success', "Supplier '$supplierName' has been deleted successfully");
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('admin.suppliers.index')
                ->with('error', 'Failed to delete supplier: ' . $e->getMessage());
        }
    }
}
