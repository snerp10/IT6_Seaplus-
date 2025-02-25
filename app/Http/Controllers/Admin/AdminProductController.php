<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Inventory;
use App\Models\Supplier;

use App\Http\Controllers\Controller;


class AdminProductController extends Controller
{
    public function index()
    {
        $products = Product::all();
        return view('admin.products.index', compact('products'));
    }

    public function create()
    {
        $suppliers = Supplier::all();
        return view('admin.products.create', compact('suppliers'));
    }

    public function store(Request $request)
    {
        $product = Product::firstOrNew(['name' => $request->input('name')]);
        $product->price = $request->input('price');
        $product->category = $request->input('category');
        $product->unit = $request->input('unit');
        $product->stock += $request->input('stock');
        $product->supp_id = $request->input('supp_id');
        $product->save();

        $inventory = new Inventory;
        $inventory->prod_id = $product->prod_id;
        $inventory->curr_stock = $product->stock;
        $inventory->move_type = 'Stock_in';
        $inventory->quantity = $request->input('stock');
        $inventory->move_date = now();
        $inventory->save();

        return redirect()->route('admin.products.index');
    }
    
    public function show(Product $product)
    {
        return view('admin.products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        return view('admin.products.edit', compact('product'));
    }

    public function update(Request $request, Product $product)
    {
        $product->stock += $request->input('stock');
        $product->save();

        $inventory = new Inventory;
        $inventory->prod_id = $product->prod_id;
        $inventory->curr_stock = $product->stock;
        $inventory->move_type = 'Stock_in';
        $inventory->quantity = $request->input('stock');
        $inventory->move_date = now();
        $inventory->save();

        return redirect()->route('admin.products.index');
    }

    public function destroy(\App\Models\Product $product)
    {
        $product->delete();
        return redirect()->route('admin.products.index');
    }
}
