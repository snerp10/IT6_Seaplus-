@extends('layouts.admin')

@section('admin.content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Edit Product</h1>
        <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i> Back to Products
        </a>
    </div>

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Product Information</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.products.update', $product->prod_id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="name" class="form-label">Product Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                id="name" name="name" value="{{ old('name', $product->name) }}" required>
                            @error('name')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="category" class="form-label">Category <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('category') is-invalid @enderror" 
                                id="category" name="category" value="{{ old('category', $product->category) }}" required>
                            @error('category')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="unit" class="form-label">Unit of Measurement <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('unit') is-invalid @enderror" 
                                id="unit" name="unit" value="{{ old('unit', $product->unit) }}" required>
                            <small class="form-text text-muted">Example: kg, pcs, box, etc.</small>
                            @error('unit')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                            <select class="form-control @error('status') is-invalid @enderror" id="status" name="status" required>
                                <option value="Active" {{ (old('status', $product->status) == 'Active') ? 'selected' : '' }}>Active</option>
                                <option value="Inactive" {{ (old('status', $product->status) == 'Inactive') ? 'selected' : '' }}>Inactive</option>
                            </select>
                            @error('status')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="supp_id" class="form-label">Supplier <span class="text-danger">*</span></label>
                            <select class="form-control @error('supp_id') is-invalid @enderror" id="supp_id" name="supp_id" required>
                                <option value="">Select Supplier</option>
                                @foreach($suppliers as $supplier)
                                    <option value="{{ $supplier->supp_id }}" 
                                        {{ old('supp_id', $product->supp_id) == $supplier->supp_id ? 'selected' : '' }}>
                                        {{ $supplier->company_name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('supp_id')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="original_price" class="form-label">Original Price (₱) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" class="form-control @error('original_price') is-invalid @enderror" 
                                id="original_price" name="original_price" 
                                value="{{ old('original_price', $product->pricing()->latest('start_date')->first()?->original_price) }}" required>
                            @error('original_price')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="selling_price" class="form-label">Selling Price (₱) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" class="form-control @error('selling_price') is-invalid @enderror" 
                                id="selling_price" name="selling_price" 
                                value="{{ old('selling_price', $product->pricing()->latest('start_date')->first()?->selling_price) }}" required>
                            @error('selling_price')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="markup" class="form-label">Markup (₱)</label>
                            <input type="text" class="form-control" id="markup" 
                                value="{{ number_format($product->pricing()->latest('start_date')->first()?->markup ?? 0, 2) }}" readonly>
                            <small class="form-text text-muted">Automatically calculated</small>
                        </div>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="description" class="form-label">Product Description</label>
                    <textarea class="form-control @error('description') is-invalid @enderror" 
                        id="description" name="description" rows="3">{{ old('description', $product->description ?? '') }}</textarea>
                    @error('description')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
                
                <hr>
                <div class="d-flex justify-content-between">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> Update Product
                    </button>
                    
                    <form action="{{ route('admin.products.destroy', $product->prod_id) }}" method="POST" 
                          onsubmit="return confirm('Are you sure you want to delete this product?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash me-1"></i> Delete Product
                        </button>
                    </form>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // Calculate markup on price change
    $('#original_price, #selling_price').on('input', function() {
        const originalPrice = parseFloat($('#original_price').val()) || 0;
        const sellingPrice = parseFloat($('#selling_price').val()) || 0;
        const markup = sellingPrice - originalPrice;
        $('#markup').val(markup.toFixed(2));
    });
});
</script>
@endpush
@endsection

