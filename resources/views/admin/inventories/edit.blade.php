@extends('layouts.admin')

@section('title', 'Edit Inventory Movement')

@section('admin.content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-edit text-dark mr-2"></i> Edit Inventory Movement
        </h1>
        <div>
            <a href="{{ route('admin.inventories.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Back to Inventory
            </a>
            <a href="{{ route('admin.inventories.show', $inventory->inv_id) }}" class="btn btn-info btn-sm">
                <i class="fas fa-eye"></i> View Details
            </a>
        </div>
    </div>

    <!-- Alert for today-only editing -->
    <div class="alert alert-info shadow-sm">
        <i class="fas fa-info-circle mr-2"></i> 
        Only inventory movements created today can be edited. Changes will affect all subsequent inventory records for this product.
    </div>

    <!-- Status Messages -->
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle mr-2"></i> {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <!-- Edit Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-boxes mr-1"></i> Edit Movement Details
            </h6>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.inventories.update', $inventory->inv_id) }}" method="POST">
                @csrf
                @method('PUT')
                
                <!-- Hidden product ID field - we don't allow changing the product -->
                <input type="hidden" name="prod_id" value="{{ $inventory->prod_id }}">
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="product_name" class="font-weight-bold">Product</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-cube p-1"></i></span>
                                </div>
                                <input type="text" class="form-control" id="product_name" value="{{ $inventory->product->name }}" disabled>
                            </div>
                            <small class="form-text text-muted">Product cannot be changed for an existing inventory movement.</small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="move_type" class="font-weight-bold">Movement Type</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        <i class="fas fa-exchange-alt p-1"></i>
                                    </span>
                                </div>
                                <select class="form-control @error('move_type') is-invalid @enderror" id="move_type" name="move_type" required>
                                    <option value="Stock_in" {{ $inventory->move_type == 'Stock_in' ? 'selected' : '' }}>
                                        Stock In (Inventory Addition)
                                    </option>
                                    <option value="Stock_out" {{ $inventory->move_type == 'Stock_out' ? 'selected' : '' }}>
                                        Stock Out (Inventory Reduction)
                                    </option>
                                </select>
                            </div>
                            @error('move_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="quantity" class="font-weight-bold">Quantity</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-hashtag p-1"></i></span>
                                </div>
                                <input type="number" class="form-control @error('quantity') is-invalid @enderror" 
                                       id="quantity" name="quantity" value="{{ $inventory->move_type === 'Stock_in' ? $inventory->stock_in : $inventory->stock_out }}" 
                                       required min="1">
                                <div class="input-group-append">
                                    <span class="input-group-text">{{ $inventory->product->unit }}</span>
                                </div>
                            </div>
                            @error('quantity')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">For stock out movements, ensure the quantity doesn't exceed available stock.</small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="move_date" class="font-weight-bold">Movement Date</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-calendar p-1"></i></span>
                                </div>
                                <input type="date" class="form-control @error('move_date') is-invalid @enderror" 
                                       id="move_date" name="move_date" 
                                       value="{{ $inventory->move_date->format('Y-m-d') }}" required>
                            </div>
                            @error('move_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="font-weight-bold">Current Stock Before This Movement</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-warehouse p-1"></i></span>
                                </div>
                                <input type="text" class="form-control" 
                                       value="{{ $inventory->move_type === 'Stock_in' ? 
                                                 $inventory->curr_stock - $inventory->stock_in : 
                                                 $inventory->curr_stock + $inventory->stock_out }} {{ $inventory->product->unit }}" 
                                       disabled>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="font-weight-bold">Stock After This Movement</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-chart-line p-1"></i></span>
                                </div>
                                <input type="text" class="form-control" id="calculated_stock" 
                                       value="{{ $inventory->curr_stock }} {{ $inventory->product->unit }}" disabled>
                            </div>
                            <small class="form-text text-muted">This will be recalculated based on your changes.</small>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="notes" class="font-weight-bold">Notes</label>
                    <textarea class="form-control" id="notes" name="notes" rows="3">{{ $inventory->notes ?? '' }}</textarea>
                    <small class="form-text text-muted">Optional: Add any relevant notes about this inventory movement.</small>
                </div>

                <div class="alert alert-warning mt-3">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    <strong>Warning:</strong> Editing this inventory movement will recalculate all subsequent stock levels for this product.
                </div>

                <div class="mt-4 text-right">
                    <a href="{{ route('admin.inventories.index') }}" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary ml-2">
                        <i class="fas fa-save mr-1"></i> Update Inventory
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(function() {
        // Calculate and update the projected stock based on user input
        function updateProjectedStock() {
            const moveType = $('#move_type').val();
            const quantity = parseInt($('#quantity').val()) || 0;
            
            // Get the previous stock from the controller's calculation
            const previousStock = {{ $inventory->move_type === 'Stock_in' ? 
                                     $inventory->curr_stock - $inventory->stock_in : 
                                     $inventory->curr_stock + $inventory->stock_out }};
            
            let newStock = previousStock;
            if (moveType === 'Stock_in') {
                newStock = previousStock + quantity;
            } else {
                newStock = previousStock - quantity;
            }
            
            // Update the calculated stock field
            $('#calculated_stock').val(newStock + ' {{ $inventory->product->unit }}');
            
            // Show warning if stock would go negative
            if (newStock < 0) {
                $('#quantity').addClass('is-invalid');
                $('<div class="invalid-feedback">This would result in negative inventory!</div>')
                    .insertAfter('#quantity')
                    .show();
            } else {
                $('#quantity').removeClass('is-invalid');
                $('#quantity').next('.invalid-feedback').remove();
            }
        }
        
        // Attach event handlers
        $('#move_type, #quantity').on('change input', updateProjectedStock);
        
        // Initialize on page load
        updateProjectedStock();
    });
</script>
@endpush

