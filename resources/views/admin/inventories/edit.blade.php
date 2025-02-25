@extends('layouts.admin')

@section('admin.content')
<div class="container">
    <h1>Edit Inventory Movement</h1>
    <form action="{{ route('admin.inventories.update', $inventory->inv_id) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="mb-3">
            <label for="product_name" class="form-label">Product Name</label>
            <input type="text" class="form-control" id="product_name" name="product_name" value="{{ $inventory->product->name }}" disabled>
        </div>
        
        <div class="mb-3">
            <label for="current_stock" class="form-label">Current Stock</label>
            <input type="number" class="form-control" id="current_stock" name="current_stock" value="{{ $inventory->curr_stock }}" disabled>
        </div>
        
        <div class="mb-3">
            <label for="move_type" class="form-label">Movement Type</label>
            <select class="form-control" id="move_type" name="move_type" required>
                <option value="Stock_in" {{ $inventory->move_type == 'Stock_in' ? 'selected' : '' }}>Stock In</option>
                <option value="Stock_out" {{ $inventory->move_type == 'Stock_out' ? 'selected' : '' }}>Stock Out</option>
            </select>
        </div>
        
        <div class="mb-3">
            <label for="quantity" class="form-label">Quantity Moved</label>
            <input type="number" class="form-control" id="quantity" name="quantity" value="{{ $inventory->quantity }}" required min="1">
        </div>
        
        <div class="mb-3">
            <label for="move_date" class="form-label">Movement Date</label>
            <input type="date" class="form-control" id="move_date" name="move_date" value="{{ $inventory->move_date }}" required>
        </div>
        
        <a href="{{ route('admin.inventories.index') }}" class="btn btn-secondary">Back to Inventory List</a>
        <button type="submit" class="btn btn-primary">Update Inventory</button>
    </form>
</div>
@endsection

