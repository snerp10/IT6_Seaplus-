@extends('layouts.admin')

@section('admin.content')
<div class="container">
    <h1>Inventory Records</h1>
    <a href="{{ route('admin.inventories.export') }}" class="btn btn-secondary mb-3">Export Inventory</a>
    <a href="{{ route('admin.inventories.low_stock_alerts') }}" class="btn btn-warning mb-3">Low Stock Alerts</a>
    <form action="{{ route('admin.inventories.stock_history') }}" method="GET" class="mb-3">
        <div class="form-group">
            <label for="prod_id">Select Product for Stock History:</label>
            <select name="prod_id" id="prod_id" class="form-control">
                @foreach($products as $product)
                    <option value="{{ $product->prod_id }}">{{ $product->name }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="btn btn-secondary">View Stock History</button>
    </form>
    <table class="table">
        <thead>
            <tr>
                <th>Product</th>
                <th>Current Stock</th>
                <th>Movement Type</th>
                <th>Quantity</th>
                <th>Movement Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($inventories as $inventory)
            <tr>
                <td>{{ $inventory->product->name }}</td>
                <td>{{ $inventory->curr_stock }}</td>
                <td>{{ $inventory->move_type }}</td>
                <td>{{ $inventory->quantity }}</td>
                <td>{{ $inventory->move_date }}</td>
                <td>
                    <a href="{{ route('admin.inventories.show', $inventory->inv_id) }}" class="btn btn-info">View</a>
                    <a href="{{ route('admin.inventories.edit', $inventory->inv_id) }}" class="btn btn-warning">Edit</a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection

