<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Http\Controllers\Controller;


class AdminProductController extends Controller
{
    public function index()
    {
        $products = \App\Models\Product::all();
        return view('admin.products.index', compact('products'));
    }

    public function create()
    {
        return view('admin.products.create');
    }

    public function store(Request $request)
    {
        $product = new \App\Models\Product;
        $product->name = $request->input('name');
        $product->price = $request->input('price');
        $product->category = $request->input('category');
        $product->unit = $request->input('unit');
        $product->stock = $request->input('stock');
        $product->save();
        return redirect()->route('admin.products.index');
    }

    public function edit(\App\Models\Product $product)
    {
        return view('admin.products.edit', compact('product'));
    }

    public function update(Request $request, \App\Models\Product $product)
    {
        $product->name = $request->input('name');
        $product->price = $request->input('price');
        $product->category = $request->input('category');
        $product->unit = $request->input('unit');
        $product->stock = $request->input('stock');
        $product->save();
        return redirect()->route('admin.products.index');
    }

    public function destroy(\App\Models\Product $product)
    {
        $product->delete();
        return redirect()->route('admin.products.index');
    }
}
