@extends('layouts.admin')

@section('title', 'Inventory Management')

@section('admin.content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-warehouse text-dark mr-2"></i> Inventory Management
        </h1>
        <div>
            <a href="{{ route('admin.inventories.low_stock_alerts') }}" class="btn btn-warning btn-sm mr-2">
                <i class="fas fa-exclamation-triangle"></i> Low Stock Alerts
            </a>
            <a href="{{ route('admin.inventories.export') }}" class="btn btn-info btn-sm mr-2">
                <i class="fas fa-file-export"></i> Export Data
            </a>
            <a href="{{ route('admin.inventories.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus-circle"></i> Add Stock Movement
            </a>
        </div>
    </div>

    <!-- Status Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
            <i class="fas fa-exclamation-circle mr-2"></i> {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <!-- Inventory Statistics -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Products</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $products->count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-boxes fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Stock Movements</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $inventories->total() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exchange-alt fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Stock In Today</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $inventories->where('move_type', 'Stock_in')->where('move_date', '>=', \Carbon\Carbon::today())->count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-arrow-circle-down fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Stock Out Today</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $inventories->where('move_type', 'Stock_out')->where('move_date', '>=', \Carbon\Carbon::today())->count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-arrow-circle-up fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text">Filter Inventory Records</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.inventories.index') }}" method="GET" class="mb-0">
                <div class="row">
                    <div class="col-md-3">
                        <label for="prod_id" class="form-label small">Product</label>
                        <select name="prod_id" id="prod_id" class="form-control form-control-sm" onchange="this.form.submit()">
                            <option value="">All Products</option>
                            @foreach($products as $product)
                                <option value="{{ $product->prod_id }}" {{ $product->prod_id == request()->query('prod_id') ? 'selected' : '' }}>
                                    {{ $product->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="move_type" class="form-label small">Movement Type</label>
                        <select name="move_type" id="move_type" class="form-control form-control-sm" onchange="this.form.submit()">
                            <option value="">All Types</option>
                            <option value="Stock_in" {{ request('move_type') == 'Stock_in' ? 'selected' : '' }}>Stock In</option>
                            <option value="Stock_out" {{ request('move_type') == 'Stock_out' ? 'selected' : '' }}>Stock Out</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="date_from" class="form-label small">Date From</label>
                        <input type="date" name="date_from" id="date_from" class="form-control form-control-sm" value="{{ request('date_from') }}">
                    </div>
                    <div class="col-md-2">
                        <label for="date_to" class="form-label small">Date To</label>
                        <input type="date" name="date_to" id="date_to" class="form-control form-control-sm" value="{{ request('date_to') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label d-block small">&nbsp;</label>
                        <div class="d-flex">
                            <button type="submit" class="btn btn-primary btn-sm mr-2 d-flex align-items-center">
                                <i class="fas fa-search"></i> <span class="ml-1">Search</span>
                            </button>
                            <a href="{{ route('admin.inventories.index') }}" class="btn btn-secondary btn-sm d-flex align-items-center">
                                <i class="fas fa-undo"></i> <span class="ml-1">Reset</span>
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Inventory Table Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text">
                <i class="fas fa-list"></i> Inventory Movement Records
            </h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover" id="inventoryTable">
                    <thead class="thead-dark">
                        <tr>
                            <th class="text-center">Date</th>
                            <th class="text-center">Product</th>
                            <th class="text-center">Movement Type</th>
                            <th class="text-center">Stock In</th>
                            <th class="text-center">Stock Out</th> 
                            <th class="text-center">Current Stock</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($inventories as $inventory)
                            <tr>
                                <td>{{ $inventory->move_date->format('M d, Y') }}</td>
                                <td>
                                    <strong>{{ $inventory->product->name }}</strong>
                                    <br>
                                    <span class="badge bg-{{ 
                                        $inventory->product->category == 'Sand' ? 'secondary' : 
                                        ($inventory->product->category == 'Gravel' ? 'info' : 'primary') 
                                    }} text-white">
                                        {{ $inventory->product->category }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $inventory->move_type === 'Stock_in' ? 'success' : 'danger' }} text-white">
                                        {{ $inventory->move_type === 'Stock_in' ? 'Stock In' : 'Stock Out' }}
                                    </span>
                                </td>
                                <td>{{ $inventory->stock_in > 0 ? $inventory->stock_in : '-' }}</td>
                                <td>{{ $inventory->stock_out > 0 ? $inventory->stock_out : '-' }}</td>
                                <td>{{ $inventory->curr_stock }} {{ $inventory->product->unit }}</td>
                                <td>
                                    <div class="btn-group" role="group" style="column-gap: 0.25rem">
                                        <a href="{{ route('admin.inventories.show', $inventory->inv_id) }}" class="btn btn-sm btn-secondary" title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @if($inventory->created_at->isToday())
                                        <a href="{{ route('admin.inventories.edit', $inventory->inv_id) }}" class="btn btn-sm btn-primary" title="Edit Movement">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">No inventory records found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="mt-3">
                {{ $inventories->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    $(function() {
        // Initialize tooltips
        $('[title]').tooltip();
        
        // Initialize DataTable for better search and sorting
        $('#inventoryTable').DataTable({
            "paging": false,
            "lengthChange": false,
            "searching": false,
            "ordering": true,
            "info": false,
            "autoWidth": false,
            "responsive": true,
            "order": [[ 0, "desc" ]]
        });
    });
</script>
@endpush
@endsection

