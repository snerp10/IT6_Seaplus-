@extends('layouts.admin')

@section('admin.content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>{{ $product->name }}</h2>
        <div>
            <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Products
            </a>
            <a href="{{ route('admin.products.edit', $product->prod_id) }}" class="btn btn-warning">
                <i class="fas fa-edit"></i> Edit Product
            </a>
        </div>
    </div>
    
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-md-8">
            <div class="card shadow mb-4">
                <div class="card-header">
                    <h5 class="m-0 font-weight-bold">Product Details</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <table class="table">
                                <tr>
                                    <th style="width: 30%">Product ID:</th>
                                    <td>{{ $product->prod_id }}</td>
                                </tr>
                                <tr>
                                    <th>Name:</th>
                                    <td>{{ $product->name }}</td>
                                </tr>
                                <tr>
                                    <th>Category:</th>
                                    <td>{{ $product->category }}</td>
                                </tr>
                                <tr>
                                    <th>Unit:</th>
                                    <td>{{ $product->unit }}</td>
                                </tr>
                                <tr>
                                    <th>Status:</th>
                                    <td>
                                        <span class="badge {{ $product->status == 'Active' ? 'bg-success' : 'bg-secondary' }}">
                                            {{ $product->status }}
                                        </span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table">
                                <tr>
                                    <th style="width: 40%">Supplier:</th>
                                    <td>{{ $product->supplier->company_name }}</td>
                                </tr>
                                <tr>
                                    <th>Original Price:</th>
                                    <td>₱{{ number_format($product->pricing()->latest('start_date')->first()?->original_price ?? 0, 2) }}</td>
                                </tr>
                                <tr>
                                    <th>Selling Price:</th>
                                    <td>₱{{ number_format($product->pricing()->latest('start_date')->first()?->selling_price ?? 0, 2) }}</td>
                                </tr>
                                <tr>
                                    <th>Markup:</th>
                                    <td>₱{{ number_format($product->pricing()->latest('start_date')->first()?->markup ?? 0, 2) }}</td>
                                </tr>
                                <tr>
                                    <th>Current Stock:</th>
                                    <td>
                                        <span class="badge {{ $product->getStockAttribute() < 10 ? 'bg-danger' : 'bg-success' }} fs-6">
                                            {{ $product->getStockAttribute() }} {{ $product->unit }}
                                        </span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="m-0 font-weight-bold">Add Stock</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.inventories.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="prod_id" value="{{ $product->prod_id }}">
                        <input type="hidden" name="move_type" value="Stock_in">
                        
                        <div class="mb-3">
                            <label for="quantity" class="form-label">Quantity to Add</label>
                            <input type="number" class="form-control" id="quantity" name="quantity" min="1" value="1" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="move_date" class="form-label">Movement Date</label>
                            <input type="date" class="form-control" id="move_date" name="move_date" value="{{ date('Y-m-d') }}" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="notes" class="form-label">Notes (Optional)</label>
                            <textarea class="form-control" id="notes" name="notes" rows="2"></textarea>
                        </div>
                        
                        <button type="submit" class="btn btn-success w-100">
                            <i class="fas fa-plus"></i> Add Stock
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header">
            <h5 class="m-0 font-weight-bold">Recent Stock Movements</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Type</th>
                            <th>Stock In</th>
                            <th>Stock Out</th>
                            <th>Current Stock</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($product->inventories()->latest('move_date')->take(5)->get() as $inventory)
                            <tr>
                                <td>{{ $inventory->move_date->format('M d, Y') }}</td>
                                <td>
                                    <span class="badge {{ $inventory->move_type === 'Stock_in' ? 'bg-success' : 'bg-danger' }}">
                                        {{ $inventory->move_type }}
                                    </span>
                                </td>
                                <td>{{ $inventory->stock_in }}</td>
                                <td>{{ $inventory->stock_out }}</td>
                                <td>{{ $inventory->curr_stock }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="text-center">
                <a href="{{ route('admin.inventories.index', ['prod_id' => $product->prod_id]) }}" class="btn btn-sm btn-primary">View All Inventory Records</a>
            </div>
        </div>
    </div>
</div>
@endsection
