<?php
namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $category = $request->query('category');
        $products = Product::when($category, function($query) use ($category) {
            return $query->where('category', $category);
        })->where('stock', '>', 0)
          ->get();

        return view('customer.products.index', compact('products', 'category'));
    }
    public function show(Product $product)
    {
        return view('customer.products.show', compact('product'));
    }

    public function search(Request $request)
    {
        $query = $request->query('query');
        $products = Product::where('name', 'like', "%$query%")
            ->orWhere('category', 'like', "%$query%")
            ->get();

        return view('customer.products.search', compact('products', 'query'));
    }
}
