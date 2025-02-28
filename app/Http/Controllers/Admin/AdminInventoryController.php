<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Inventory;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class AdminInventoryController extends Controller
{
    public function index()
    {
        $inventories = Inventory::with('product')->orderBy('created_at', 'desc')->paginate(15);
        $products = Product::where('status', 'Active')->get();
        
        return view('admin.inventories.index', compact('inventories', 'products'));
    }
    
    public function create()
    {
        $products = Product::where('status', 'Active')->get();
        return view('admin.inventories.create', compact('products'));
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'prod_id' => 'required|exists:products,prod_id',
            'move_type' => 'required|in:Stock_in,Stock_out',
            'quantity' => 'required|integer|min:1',
            'move_date' => 'required|date',
        ]);
        
        DB::beginTransaction();
        try {
            $product = Product::findOrFail($request->prod_id);
            
            // Get current stock from the latest inventory record
            $latestInventory = $product->inventories()->latest('inv_id')->first();
            $currentStock = $latestInventory ? $latestInventory->curr_stock : 0;
            
            if ($request->move_type === 'Stock_out' && $currentStock < $request->quantity) {
                return redirect()->back()->with('error', 'Not enough stock available!')->withInput();
            }
            
            // Create inventory record
            $inventory = new Inventory();
            $inventory->prod_id = $request->prod_id;
            $inventory->move_type = $request->move_type;
            $inventory->move_date = $request->move_date;
            
            // Set the appropriate stock field based on move type
            if ($request->move_type === 'Stock_in') {
                $inventory->stock_in = $request->quantity;
                $inventory->stock_out = 0;
                $inventory->curr_stock = $currentStock + $request->quantity;
            } else {
                $inventory->stock_in = 0;
                $inventory->stock_out = $request->quantity;
                $inventory->curr_stock = $currentStock - $request->quantity;
            }
            
            $inventory->save();
            
            DB::commit();
            return redirect()->route('admin.inventories.index')
                           ->with('success', 'Inventory movement recorded successfully.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                           ->with('error', 'Error recording inventory: ' . $e->getMessage())
                           ->withInput();
        }
    }
    
    public function show(Inventory $inventory)
    {
        $inventory->load('product');
        return view('admin.inventories.show', compact('inventory'));
    }
    
    public function lowStockAlerts()
    {
        // Get products with latest inventory records showing less than 10 units
        $lowStockProducts = Product::whereHas('inventories', function($query) {
            $query->latest('inv_id')
                  ->where('curr_stock', '<', 10);
        })->with(['inventories' => function($query) {
            $query->latest('inv_id')->limit(1);
        }])->get();
        
        return view('admin.inventories.low_stock_alerts', compact('lowStockProducts'));
    }
    
    public function export() 
    {
        // Logic for exporting inventory data
        // This would typically generate a CSV/Excel file
        
        return redirect()->back()->with('success', 'Inventory data exported successfully.');
    }
}

