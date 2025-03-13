@extends('layouts.admin')

@section('admin.content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="fas fa-exclamation-triangle text-warning"></i> Low Stock Alerts</h1>
        <div>
            <a href="{{ route('admin.inventories.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Add Stock
            </a>
            <a href="{{ route('admin.inventories.index') }}" class="btn btn-secondary">
                <i class="fas fa-list"></i> All Inventory Records
            </a>
        </div>
    </div>
    
    <div class="card shadow">
        <div class="card-header bg text-primary">
            <h6 class="m-0 font-weight-bold">Products with Low Stock (Less than 10 units)</h6>
        </div>
        <div class="card-body">
            @php
                // Ensure we only show products with stock < 10
                $filteredLowStockProducts = $lowStockProducts->filter(function($product) {
                    $currentStock = $product->inventories->first()->curr_stock ?? 0;
                    return $currentStock < 10;
                });
            @endphp
            
            @if($filteredLowStockProducts->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Product</th>
                                <th>Category</th>
                                <th>Current Stock</th>
                                <th>Last Updated</th>
                                <th>Supplier</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($filteredLowStockProducts as $product)
                                @php
                                    $currentStock = $product->inventories->first()->curr_stock ?? 0;
                                    $lastUpdated = $product->inventories->first()->updated_at ?? null;
                                @endphp
                                <tr class="{{ $currentStock <= 5 ? 'table-danger' : 'table-warning' }}">
                                    <td>{{ $product->name }}</td>
                                    <td>{{ $product->category }}</td>
                                    <td>
                                        <span class="fw-bold">{{ $currentStock }} {{ $product->unit }}</span>
                                    </td>
                                    <td>{{ $lastUpdated ? $lastUpdated->format('M d, Y H:i') : 'N/A' }}</td>
                                    <td>{{ $product->supplier->company_name ?? 'N/A' }}</td>
                                    <td>
                                        <a href="{{ route('admin.inventories.create') }}" class="btn btn-sm btn-success">
                                            <i class="fas fa-plus"></i> Restock
                                        </a>
                                        <a href="{{ route('admin.products.show', $product->prod_id) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i> View Product
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> Great! No products have low stock at the moment.
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

