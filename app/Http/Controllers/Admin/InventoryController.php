<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public function index()
    {
        $inventories = Inventory::all();
        return view('admin.inventories.index', compact('inventories'));
    }

    public function create()
    {
        return view('admin.inventories.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'current_stock' => 'required|integer',
            'movement_id' => 'required|exists:movements,id',
            'movement_type' => 'required|in:Stock_in,Stock_out',
            'quantity' => 'required|integer',
            'movement_date' => 'required|date',
        ]);

        Inventory::create($request->all());

        return redirect()->route('admin.inventories.index')->with('success', 'Inventory record created successfully.');
    }

    public function show(Inventory $inventory)
    {
        return view('admin.inventories.show', compact('inventory'));
    }

    public function edit(Inventory $inventory)
    {
        return view('admin.inventories.edit', compact('inventory'));
    }

    public function update(Request $request, Inventory $inventory)
    {
        $request->validate([
            'current_stock' => 'required|integer',
            'movement_id' => 'required|exists:movements,id',
            'movement_type' => 'required|in:Stock_in,Stock_out',
            'quantity' => 'required|integer',
            'movement_date' => 'required|date',
        ]);

        $inventory->update($request->all());

        return redirect()->route('admin.inventories.index')->with('success', 'Inventory record updated successfully.');
    }

    public function destroy(Inventory $inventory)
    {
        $inventory->delete();

        return redirect()->route('admin.inventories.index')->with('success', 'Inventory record deleted successfully.');
    }
}
