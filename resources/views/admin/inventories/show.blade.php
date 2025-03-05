@extends('layouts.admin')

@section('title', 'Inventory Details')

@section('admin.content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-boxes text-dark mr-2"></i> Inventory Movement Details
        </h1>
        <div>
            <a href="{{ route('admin.inventories.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Back to Inventory
            </a>
            @if($inventory->created_at->isToday())
                <a href="{{ route('admin.inventories.edit', $inventory->inv_id) }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-edit"></i> Edit Movement
                </a>
            @endif
        </div>
    </div>

    <!-- Status Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="row">
        <!-- Main Inventory Details Card -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-info-circle mr-1"></i> Inventory Movement Information
                    </h6>
                    <span class="badge badge-{{ $inventory->move_type === 'Stock_in' ? 'success' : 'danger' }} text-white px-3 py-2">
                        {{ $inventory->move_type === 'Stock_in' ? 'STOCK IN' : 'STOCK OUT' }}
                    </span>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <tbody>
                                <tr>
                                    <th style="width: 30%">Reference ID</th>
                                    <td>INV-{{ str_pad($inventory->inv_id, 5, '0', STR_PAD_LEFT) }}</td>
                                </tr>
                                <tr>
                                    <th>Product</th>
                                    <td>
                                        <strong>{{ $inventory->product->name }}</strong>
                                        <span class="badge badge-{{ 
                                            $inventory->product->category == 'Sand' ? 'secondary' : 
                                            ($inventory->product->category == 'Gravel' ? 'info' : 'primary') 
                                        }} ml-2">{{ $inventory->product->category }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Movement Type</th>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if($inventory->move_type === 'Stock_in')
                                                <i class="fas fa-arrow-circle-down text-success mr-2" style="font-size: 1.5rem;"></i>
                                                <span>Stock In (Inventory Addition)</span>
                                            @else
                                                <i class="fas fa-arrow-circle-up text-danger mr-2" style="font-size: 1.5rem;"></i>
                                                <span>Stock Out (Inventory Reduction)</span>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Quantity</th>
                                    <td>
                                        <span class="font-weight-bold" style="font-size: 1.1rem;">
                                            {{ $inventory->move_type === 'Stock_in' ? $inventory->stock_in : $inventory->stock_out }}
                                        </span>
                                        <small class="text-muted ml-2">{{ $inventory->product->unit }}</small>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Stock After Movement</th>
                                    <td>
                                        <span class="font-weight-bold">{{ $inventory->curr_stock }}</span>
                                        <small class="text-muted ml-2">{{ $inventory->product->unit }}</small>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Movement Date</th>
                                    <td>{{ $inventory->move_date->format('F d, Y') }}</td>
                                </tr>
                                @if($inventory->notes)
                                <tr>
                                    <th>Notes</th>
                                    <td>{{ $inventory->notes }}</td>
                                </tr>
                                @endif
                                <tr>
                                    <th>Created By</th>
                                    <td>{{ $inventory->created_by ?? 'System' }}</td>
                                </tr>
                                <tr>
                                    <th>Created At</th>
                                    <td>{{ $inventory->created_at->format('F d, Y g:i A') }}</td>
                                </tr>
                                <tr>
                                    <th>Last Updated</th>
                                    <td>{{ $inventory->updated_at->format('F d, Y g:i A') }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Side Cards -->
        <div class="col-xl-4 col-lg-5">
            <!-- Product Card -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-cube mr-1"></i> Product Information
                    </h6>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        <img src="{{ $inventory->product->image_url ?? asset('images/products/default.png') }}" alt="{{ $inventory->product->name }}" 
                            class="img-thumbnail" style="max-width: 150px; max-height: 150px;">
                    </div>
                    
                    <h5 class="text-center mb-3">{{ $inventory->product->name }}</h5>
                    
                    <div class="row mb-2">
                        <div class="col-6 text-muted">Category:</div>
                        <div class="col-6">{{ $inventory->product->category }}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-6 text-muted">Current Stock:</div>
                        <div class="col-6">
                            <span class="font-weight-bold 
                            {{ $inventory->product->stock <= $inventory->product->min_stock ? 'text-danger' : 'text-success' }}">
                                {{ $inventory->product->stock }} {{ $inventory->product->unit }}
                            </span>
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-6 text-muted">Min Stock:</div>
                        <div class="col-6">{{ $inventory->product->min_stock ?? 'N/A' }} {{ $inventory->product->unit }}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-6 text-muted">Unit Price:</div>
                        <div class="col-6">₱{{ number_format($inventory->product->price, 2) }}</div>
                    </div>
                    
                    <div class="mt-3 text-center">
                        <a href="{{ route('admin.products.show', $inventory->product->prod_id) }}" class="btn btn-info btn-sm">
                            <i class="fas fa-eye mr-1"></i> View Product Details
                        </a>
                    </div>
                </div>
            </div>

            <!-- Stock Movement Summary Card -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-chart-line mr-1"></i> Stock History
                    </h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm mb-0">
                            <thead class="thead-light">
                                <tr>
                                    <th>Date</th>
                                    <th>Type</th>
                                    <th>Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Sample previous movements for this product - in a real app, you'd fetch this from DB -->
                                @foreach(\App\Models\Inventory::where('prod_id', $inventory->prod_id)
                                    ->where('inv_id', '!=', $inventory->inv_id)
                                    ->latest('move_date')
                                    ->limit(5)
                                    ->get() as $movement)
                                <tr>
                                    <td>{{ $movement->move_date->format('M d, Y') }}</td>
                                    <td>
                                        <span class="badge badge-{{ $movement->move_type === 'Stock_in' ? 'success' : 'danger' }}">
                                            {{ $movement->move_type === 'Stock_in' ? 'In' : 'Out' }}
                                        </span>
                                    </td>
                                    <td>{{ $movement->move_type === 'Stock_in' ? $movement->stock_in : $movement->stock_out }}</td>
                                </tr>
                                @endforeach
                                @if(\App\Models\Inventory::where('prod_id', $inventory->prod_id)->where('inv_id', '!=', $inventory->inv_id)->count() == 0)
                                <tr>
                                    <td colspan="3" class="text-center">No previous movements found</td>
                                </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                    <div class="p-3 text-center border-top">
                        <a href="{{ route('admin.inventories.index', ['prod_id' => $inventory->prod_id]) }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-history mr-1"></i> View All Movements
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(function() {
        // Initialize tooltips
        $('[data-toggle="tooltip"]').tooltip();
    });
</script>
@endpush
