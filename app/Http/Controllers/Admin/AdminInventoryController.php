<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Inventory;
use App\Models\Product;
use Illuminate\Http\Request;

class AdminInventoryController extends Controller
{
    public function index()
    {
        $inventories = Inventory::with('product')->get();
        $products = Product::all();
        return view('admin.inventories.index', compact('inventories', 'products'));
    }

    public function create()
    {
        $products = Product::all();
        return view('admin.inventories.create', compact('products'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'prod_id' => 'required|exists:products,prod_id',
            'curr_stock' => 'required|integer',
            'move_type' => 'required|in:Stock_in,Stock_out',
            'quantity' => 'required|integer',
            'move_date' => 'required|date',
        ]);

        Inventory::create($validated);

        return redirect()->route('admin.inventories.index')->with('success', 'Inventory record created successfully.');
    }

    public function show(Inventory $inventory)
    {
        return view('admin.inventories.show', compact('inventory'));
    }

    public function edit(Inventory $inventory)
    {
        $products = Product::all();
        return view('admin.inventories.edit', compact('inventory', 'products'));
    }

    public function update(Request $request, Inventory $inventory)
{
    $validated = $request->validate([
        'move_type' => 'required|in:Stock_in,Stock_out',
        'quantity' => 'required|integer|min:1',
        'move_date' => 'required|date'
    ]);

    // Kunin ang original quantity bago ang update
    $originalQuantity = $inventory->quantity;
    $product = $inventory->product; // Assuming may relationship na $inventory->product

    if ($validated['move_type'] === 'Stock_in') {
        // Alisin ang dating quantity bago idagdag ang bago
        $product->stock = ($product->stock - $originalQuantity) + $validated['quantity'];
        $inventory->curr_stock = $product->stock;
    } else {
        // Alisin ang dating quantity bago bawasan ulit ng bago
        $product->stock = ($product->stock + $originalQuantity) - $validated['quantity'];
        $inventory->curr_stock = $product->stock;
    }

    // I-update ang product stock
    $product->save();

    // I-update ang inventory record
    $inventory->move_type = $validated['move_type'];
    $inventory->quantity = $validated['quantity'];
    $inventory->move_date = $validated['move_date'];
    $inventory->save();

    return redirect()->route('admin.inventories.index')->with('success', 'Inventory record updated successfully.');
}


    public function destroy(Inventory $inventory)
    {
        $inventory->delete();

        return redirect()->route('admin.inventories.index')->with('success', 'Inventory record deleted successfully.');
    }

    public function export()
    {
        $inventories = Inventory::with('product')->get();

        $csvData = "Product Name,Current Stock,Movement Type,Quantity,Movement Date\n";
        foreach ($inventories as $inventory) {
            $csvData .= "{$inventory->product->name},{$inventory->curr_stock},{$inventory->move_type},{$inventory->quantity},{$inventory->move_date}\n";
        }

        $fileName = "inventory_export_" . date('Y_m_d_His') . ".csv";
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$fileName\"",
        ];

        return response()->make($csvData, 200, $headers);
    }

    public function lowStockAlerts()
    {
        $lowStockProducts = Product::where('stock', '<', 10)->get();
        return view('admin.inventories.low_stock_alerts', compact('lowStockProducts'));
    }

    public function stockHistory(Request $request)
    {
        $prod_id = $request->input('prod_id');
        $products = Product::all();
        $stockHistory = Inventory::where('prod_id', $prod_id)->get();
        $selectedProduct = Product::find($prod_id);

        return view('admin.inventories.stock_history', compact('products', 'stockHistory', 'selectedProduct'));
    }
}

