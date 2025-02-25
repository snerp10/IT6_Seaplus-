@extends('layouts.admin')

@section('admin.content')
<div class="container">
    <h1>Inventory Details</h1>
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Product: {{ $inventory->product->name }}</h5>
            <p class="card-text"><strong>Current Stock:</strong> {{ $inventory->curr_stock }}</p>
            <p class="card-text"><strong>Movement Type:</strong> {{ $inventory->move_type }}</p>
            <p class="card-text"><strong>Quantity Moved:</strong> {{ $inventory->quantity }}</p>
            <p class="card-text"><strong>Movement Date:</strong> {{ $inventory->move_date }}</p>
            <p class="card-text"><strong>Created At:</strong> {{ $inventory->created_at->format('Y-m-d H:i:s') }}</p>
            <p class="card-text"><strong>Last Updated:</strong> {{ $inventory->updated_at->format('Y-m-d H:i:s') }}</p>
            <a href="{{ route('admin.inventories.index') }}" class="btn btn-secondary">Back to Inventory List</a>
            <a href="{{ route('admin.inventories.edit', $inventory->inv_id) }}" class="btn btn-warning">Edit Inventory</a>
        </div>
    </div>
</div>
@endsection
