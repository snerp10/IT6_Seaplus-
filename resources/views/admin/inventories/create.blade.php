@extends('layouts.admin')

@section('admin.content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Add Stock Movement</h1>
        <a href="{{ route('admin.inventories.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Inventory
        </a>
    </div>

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div class="card shadow">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">New Inventory Movement</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.inventories.store') }}" method="POST">
                @csrf
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="prod_id" class="form-label">Select Product <span class="text-danger">*</span></label>
                        <select name="prod_id" id="prod_id" class="form-control @error('prod_id') is-invalid @enderror" required>
                            <option value="">-- Select Product --</option>
                            @foreach($products as $product)
                                <option value="{{ $product->prod_id }}" 
                                    {{ old('prod_id') == $product->prod_id ? 'selected' : '' }}
                                    data-current-stock="{{ $product->inventories->first()->curr_stock ?? 0 }}"
                                    data-unit="{{ $product->unit }}">
                                    {{ $product->name }} - {{ $product->category }}
                                </option>
                            @endforeach
                        </select>
                        @error('prod_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        
                        <div id="currentStockInfo" class="mt-2 d-none">
                            <div class="alert alert-info">
                                Current Stock: <strong><span id="currentStockValue">0</span> <span id="unitLabel">units</span></strong>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <label for="move_type" class="form-label">Movement Type <span class="text-danger">*</span></label>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="move_type" id="stockIn" value="Stock_in" 
                                {{ old('move_type', 'Stock_in') === 'Stock_in' ? 'checked' : '' }}>
                            <label class="form-check-label" for="stockIn">
                                <span class="badge bg-success">Stock In</span> (Add to inventory)
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="move_type" id="stockOut" value="Stock_out"
                                {{ old('move_type') === 'Stock_out' ? 'checked' : '' }}>
                            <label class="form-check-label" for="stockOut">
                                <span class="badge bg-danger">Stock Out</span> (Remove from inventory)
                            </label>
                        </div>
                        @error('move_type')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="quantity" class="form-label">Quantity <span class="text-danger">*</span></label>
                        <input type="number" class="form-control @error('quantity') is-invalid @enderror" 
                            id="quantity" name="quantity" min="1" value="{{ old('quantity', 1) }}" required>
                        @error('quantity')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6">
                        <label for="move_date" class="form-label">Movement Date <span class="text-danger">*</span></label>
                        <input type="date" class="form-control @error('move_date') is-invalid @enderror" 
                            id="move_date" name="move_date" value="{{ old('move_date', date('Y-m-d')) }}" required>
                        @error('move_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="notes" class="form-label">Notes</label>
                    <textarea class="form-control" id="notes" name="notes" rows="3">{{ old('notes') }}</textarea>
                    <small class="form-text text-muted">Optional: Add any notes about this inventory movement</small>
                </div>
                
                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Record Stock Movement
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // Show current stock info when product is selected
    $('#prod_id').change(function() {
        const selectedOption = $(this).find('option:selected');
        const currentStock = selectedOption.data('current-stock');
        const unit = selectedOption.data('unit');
        
        if ($(this).val() !== '') {
            $('#currentStockValue').text(currentStock);
            $('#unitLabel').text(unit);
            $('#currentStockInfo').removeClass('d-none');
        } else {
            $('#currentStockInfo').addClass('d-none');
        }
    });
    
    // Initialize if there's a selected value on page load (e.g., after validation error)
    if ($('#prod_id').val() !== '') {
        $('#prod_id').trigger('change');
    }
    
    // Prevent stock out quantity greater than current stock
    $('form').submit(function(e) {
        if ($('#stockOut').is(':checked')) {
            const currentStock = parseInt($('#currentStockValue').text());
            const quantity = parseInt($('#quantity').val());
            
            if (quantity > currentStock) {
                e.preventDefault();
                alert('Error: Cannot remove more stock than available. Current stock: ' + currentStock);
            }
        }
    });
});
</script>
@endpush
@endsection
