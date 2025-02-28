@extends('layouts.admin')

@section('admin.content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Inventory Management</h1>
        <div>
            <a href="{{ route('admin.inventories.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Add Stock Movement
            </a>
            <a href="{{ route('admin.inventories.export') }}" class="btn btn-secondary">
                <i class="fas fa-file-export"></i> Export Inventory
            </a>
            <a href="{{ route('admin.inventories.low_stock_alerts') }}" class="btn btn-warning">
                <i class="fas fa-exclamation-triangle"></i> Low Stock Alerts
            </a>
        </div>
    </div>
    
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Filter Inventory Records</h6>
        </div>
        <div class="card-body">
            <form action="" method="GET" class="mb-3">
                <div class="form-group">
                    <label for="prod_id">Select Product:</label>
                    <select name="prod_id" id="prod_id" class="form-control" onchange="this.form.submit()">
                        <option value="">All Products</option>
                        @foreach($products as $product)
                            <option value="{{ $product->prod_id }}" {{ $product->prod_id == request()->query('prod_id') ? 'selected' : '' }}>
                                {{ $product->name }} 
                            </option>
                        @endforeach
                    </select>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Inventory Movement Records</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="inventoryTable">
                    <thead class="table-light">
                        <tr>
                            <th>Date</th>
                            <th>Product</th>
                            <th>Movement Type</th>
                            <th>Stock In</th>
                            <th>Stock Out</th> 
                            <th>Current Stock</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($inventories as $inventory)
                            @if(request()->query('prod_id') == '' || request()->query('prod_id') == $inventory->prod_id)
                                <tr>
                                    <td>{{ $inventory->move_date->format('M d, Y') }}</td>
                                    <td>
                                        <strong>{{ $inventory->product->name }}</strong>
                                        <span class="badge bg-secondary">{{ $inventory->product->category }}</span>
                                    </td>
                                    <td>
                                        <span class="badge {{ $inventory->move_type === 'Stock_in' ? 'bg-success' : 'bg-danger' }}">
                                            {{ $inventory->move_type }}
                                        </span>
                                    </td>
                                    <td>{{ $inventory->stock_in > 0 ? $inventory->stock_in : '-' }}</td>
                                    <td>{{ $inventory->stock_out > 0 ? $inventory->stock_out : '-' }}</td>
                                    <td>{{ $inventory->curr_stock }} {{ $inventory->product->unit }}</td>
                                    <td>
                                        <a href="{{ route('admin.inventories.show', $inventory->inv_id) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                    </td>
                                </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                {{ $inventories->links() }}
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    $('#inventoryTable').DataTable({
        "paging": false,
        "info": false,
        "ordering": true,
        "order": [[ 0, "desc" ]],
        "searching": true
    });
});
</script>
@endpush
@endsection

