<?php
namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Inventory;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        // Build query with proper eager loading
        $query = Product::with(['pricing' => function($query) {
                $query->whereNull('end_date')->orWhere('end_date', '>=', now());
            }, 
            'inventories' => function($query) {
                $query->latest('inv_id')->limit(1);
            },
            'supplier'
        ]);
        
        // Filter by category if provided
        if ($request->has('category')) {
            $query->where('category', $request->category);
        }

        // Filter by status - only show active products to customers
        $query->where('status', 'Active');
        
        // Get products with stock information
        $products = $query->get();
        
        // Add availability status to each product
        $products->each(function($product) {
            $stock = $product->getStockAttribute();
            if ($stock > 10) {
                $product->availability = 'In Stock';
                $product->availability_class = 'success';
            } elseif ($stock > 0) {
                $product->availability = 'Low Stock';
                $product->availability_class = 'warning';
            } else {
                $product->availability = 'Out of Stock';
                $product->availability_class = 'danger';
            }
        });
        
        // Filter out out-of-stock products
        $products = $products->filter(function($product) {
            return $product->getStockAttribute() > 0;
        });
        
        // Get all distinct categories for the filter dropdown
        $categories = Product::select('category')->distinct()->pluck('category');
        
        return view('customer.products.index', compact('products', 'categories'));
    }

    public function show(Product $product)
    {
        // Load relationships needed for product details
        $product->load([
            'pricing' => function($query) {
                $query->whereNull('end_date')->orWhere('end_date', '>=', now());
            },
            'inventories' => function($query) {
                $query->latest('inv_id')->limit(1);
            },
            'supplier'
        ]);
        
        // Add availability information
        $stock = $product->getStockAttribute();
        if ($stock > 10) {
            $product->availability = 'In Stock';
            $product->availability_class = 'success';
        } elseif ($stock > 0) {
            $product->availability = 'Low Stock';
            $product->availability_class = 'warning';
        } else {
            $product->availability = 'Out of Stock';
            $product->availability_class = 'danger';
        }
        
        // Get related products from the same category
        $relatedProducts = Product::where('category', $product->category)
            ->where('prod_id', '!=', $product->prod_id)
            ->where('status', 'Active')
            ->take(4)
            ->get();
        
        // Add availability to related products
        $relatedProducts->each(function($product) {
            $stock = $product->getStockAttribute();
            if ($stock > 10) {
                $product->availability = 'In Stock';
                $product->availability_class = 'success';
            } elseif ($stock > 0) {
                $product->availability = 'Low Stock';
                $product->availability_class = 'warning';
            } else {
                $product->availability = 'Out of Stock';
                $product->availability_class = 'danger';
            }
        });
        
        // Filter out out-of-stock related products
        $relatedProducts = $relatedProducts->filter(function($product) {
            return $product->getStockAttribute() > 0;
        });
        
        return view('customer.products.show', compact('product', 'relatedProducts'));
    }

    public function search(Request $request)
    {
        $query = $request->input('query');
        
        if (empty($query)) {
            return redirect()->route('customer.products.index')
                ->with('info', 'Please enter a search term');
        }
        
        $products = Product::with(['pricing' => function($q) {
                $q->whereNull('end_date')->orWhere('end_date', '>=', now());
            }])
            ->where('status', 'Active')
            ->where(function($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('category', 'like', "%{$query}%");
                // Removed the reference to the non-existent 'description' column
            })
            ->get();
            
        // Add availability information
        $products->each(function($product) {
            $stock = $product->getStockAttribute();
            if ($stock > 10) {
                $product->availability = 'In Stock';
                $product->availability_class = 'success';
            } elseif ($stock > 0) {
                $product->availability = 'Low Stock';
                $product->availability_class = 'warning';
            } else {
                $product->availability = 'Out of Stock';
                $product->availability_class = 'danger';
            }
        });
        
        // Filter out out-of-stock products
        $products = $products->filter(function($product) {
            return $product->getStockAttribute() > 0;
        });
        
        return view('customer.products.search', compact('products', 'query'));
    }
}