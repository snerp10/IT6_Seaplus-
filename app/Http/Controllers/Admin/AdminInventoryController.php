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

    public function edit(Inventory $inventory)
    {
        $inventory->load('product');
        $products = Product::where('status', 'Active')->get();
        
        // Only allow editing if the inventory record was created today
        if (!$inventory->created_at->isToday()) {
            return redirect()->route('admin.inventories.show', $inventory->inv_id)
                ->with('error', 'Only inventory movements from today can be edited.');
        }
        
        return view('admin.inventories.edit', compact('inventory', 'products'));
    }
    
    public function update(Request $request, Inventory $inventory)
    {
        // Only allow updating if the inventory record was created today
        if (!$inventory->created_at->isToday()) {
            return redirect()->route('admin.inventories.show', $inventory->inv_id)
                ->with('error', 'Only inventory movements from today can be edited.');
        }
        
        $request->validate([
            'prod_id' => 'required|exists:products,prod_id',
            'move_type' => 'required|in:Stock_in,Stock_out',
            'quantity' => 'required|integer|min:1',
            'move_date' => 'required|date',
        ]);
        
        DB::beginTransaction();
        try {
            $product = Product::findOrFail($request->prod_id);
            
            // If product is changed, recalculate based on all inventory records
            if ($inventory->prod_id != $request->prod_id) {
                // Reverting the effect of the old inventory record
                // This would require more complex logic to handle properly
                return redirect()->back()->with('error', 'Changing the product for an existing inventory movement is not allowed.')
                    ->withInput();
            }
            
            // Get the latest inventory record before the current one
            $previousInventory = Inventory::where('prod_id', $inventory->prod_id)
                ->where('inv_id', '<', $inventory->inv_id)
                ->latest('inv_id')
                ->first();
            
            // Get the previous stock level
            $previousStock = $previousInventory ? $previousInventory->curr_stock : 0;
            
            // Calculate new current stock after this movement
            if ($request->move_type === 'Stock_in') {
                $newCurrentStock = $previousStock + $request->quantity;
                $inventory->stock_in = $request->quantity;
                $inventory->stock_out = 0;
            } else {
                // For stock out, verify we have enough inventory
                if ($previousStock < $request->quantity) {
                    return redirect()->back()->with('error', 'Not enough stock available for stock out movement!')
                        ->withInput();
                }
                $newCurrentStock = $previousStock - $request->quantity;
                $inventory->stock_in = 0; 
                $inventory->stock_out = $request->quantity;
            }
            
            // Update the inventory record
            $inventory->prod_id = $request->prod_id;
            $inventory->move_type = $request->move_type;
            $inventory->move_date = $request->move_date;
            $inventory->curr_stock = $newCurrentStock;
            $inventory->save();
            
            // Now we need to update all subsequent inventory records
            $subsequentRecords = Inventory::where('prod_id', $request->prod_id)
                ->where('inv_id', '>', $inventory->inv_id)
                ->orderBy('inv_id')
                ->get();
                
            $runningStock = $newCurrentStock;
            
            foreach ($subsequentRecords as $record) {
                if ($record->move_type === 'Stock_in') {
                    $runningStock += $record->stock_in;
                } else {
                    $runningStock -= $record->stock_out;
                }
                $record->curr_stock = $runningStock;
                $record->save();
            }
            
            // Update the product's stock to match the most recent inventory record
            $latestInventory = Inventory::where('prod_id', $request->prod_id)
                ->latest('inv_id')
                ->first();
                
            // Remove attempt to update non-existent stock column
            // The application should rely on the inventory records for current stock levels
            // If you need to update product stock, first check what column name is used in your products table
            
            DB::commit();
            return redirect()->route('admin.inventories.show', $inventory->inv_id)
                ->with('success', 'Inventory movement updated successfully.');
                
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Error updating inventory: ' . $e->getMessage())
                ->withInput();
        }
    }
    
    /**
     * This method is kept for API compatibility but is no longer used in the UI
     */
    public function destroy(Inventory $inventory)
    {
        // Only allow deleting if the inventory record was created today
        if (!$inventory->created_at->isToday()) {
            return redirect()->route('admin.inventories.show', $inventory->inv_id)
                ->with('error', 'Only inventory movements from today can be deleted.');
        }
        
        $inventory->delete();
        
        return redirect()->route('admin.inventories.index')
            ->with('success', 'Inventory movement deleted successfully.');
    }
    
}

