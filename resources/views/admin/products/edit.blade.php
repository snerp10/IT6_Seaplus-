@extends('layouts.admin')

@section('admin.content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-edit"></i> Edit Product
        </h1>
        <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Products
        </a>
    </div>

    <!-- Alert Messages -->
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-gradient-primary text-white">
            <h6 class="m-0 font-weight-bold">Product Information</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.products.update', $product->prod_id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="row">
                    <div class="col-md-6">
                        <div class="card border-left-primary shadow h-100 mb-4">
                            <div class="card-header py-3 bg-light">
                                <h6 class="m-0 font-weight-bold text-primary">Basic Information</h6>
                            </div>
                            <div class="card-body">
                                <div class="form-group mb-3">
                                    <label for="name" class="form-label fw-bold">Product Name <span class="text-danger">*</span></label>
                                    <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $product->name) }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group mb-3">
                                    <label for="category" class="form-label fw-bold">Category <span class="text-danger">*</span></label>
                                    <select name="category" id="category" class="form-control @error('category') is-invalid @enderror" required>
                                        <option value="">-- Select Category --</option>
                                        <option value="Sand" {{ old('category', $product->category) == 'Sand' ? 'selected' : '' }}>Sand</option>
                                        <option value="Gravel" {{ old('category', $product->category) == 'Gravel' ? 'selected' : '' }}>Gravel</option>
                                        <option value="Hollow Blocks" {{ old('category', $product->category) == 'Hollow Blocks' ? 'selected' : '' }}>Hollow Blocks</option>
                                        <option value="Hardware Supplies" {{ old('category', $product->category) == 'Hardware Supplies' ? 'selected' : '' }}>Hardware Supplies</option>
                                        <option value="Other" {{ old('category', $product->category) == 'Other' ? 'selected' : '' }}>Other</option>
                                    </select>
                                    @error('category')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group mb-3">
                                    <label for="unit" class="form-label fw-bold">Unit of Measurement <span class="text-danger">*</span></label>
                                    <input type="text" name="unit" id="unit" class="form-control @error('unit') is-invalid @enderror" value="{{ old('unit', $product->unit) }}" required>
                                    @error('unit')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group mb-3">
                                    <label for="supp_id" class="form-label fw-bold">Supplier</label>
                                    <select name="supp_id" id="supp_id" class="form-control @error('supp_id') is-invalid @enderror">
                                        <option value="">-- No Supplier --</option>
                                        @foreach($suppliers as $supplier)
                                            <option value="{{ $supplier->supp_id }}" {{ old('supp_id', $product->supp_id) == $supplier->supp_id ? 'selected' : '' }}>
                                                {{ $supplier->company_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('supp_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="card border-left-info shadow h-100 mb-4">
                            <div class="card-header py-3 bg-light">
                                <h6 class="m-0 font-weight-bold text-primary">Pricing & Description</h6>
                            </div>
                            <div class="card-body">
                                <div class="form-group mb-3">
                                    <label for="selling_price" class="form-label fw-bold">Selling Price <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text">₱</span>
                                        <input type="number" name="selling_price" id="selling_price" class="form-control @error('selling_price') is-invalid @enderror" value="{{ old('selling_price', $product->pricing->first()->selling_price ?? 0) }}" step="0.01" min="0" required>
                                    </div>
                                    @error('selling_price')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group mb-3">
                                    <label for="original_price" class="form-label fw-bold">Original Price</label>
                                    <div class="input-group">
                                        <span class="input-group-text">₱</span>
                                        <input type="number" name="original_price" id="original_price" class="form-control @error('original_price') is-invalid @enderror" value="{{ old('original_price', $product->pricing->first()->original_price ?? 0) }}" step="0.01" min="0">
                                    </div>
                                    @error('original_price')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group mb-3">
                                    <label for="description" class="form-label fw-bold">Product Description</label>
                                    <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror" rows="4">{{ old('description', $product->description) }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <div class="card border-left-warning shadow h-100 mb-4">
                            <div class="card-header py-3 bg-light">
                                <h6 class="m-0 font-weight-bold text-primary">Product Image</h6>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="image" class="form-label fw-bold">Product Image</label>
                                    <input type="file" name="image" id="image" class="form-control @error('image') is-invalid @enderror">
                                    @error('image')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                @if($product->image)
                                <div class="mt-3">
                                    <p><strong>Current Image:</strong></p>
                                    <div class="d-flex align-items-center">
                                        <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="img-thumbnail" style="max-width: 200px; max-height: 200px;">
                                        <div class="ms-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="remove_image" id="remove_image" value="1">
                                                <label class="form-check-label" for="remove_image">
                                                    Remove existing image
                                                </label>
                                            </div>
                                            <small class="text-muted">Check this option to remove the current image without uploading a new one.</small>
                                        </div>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <div class="card border-left-success shadow h-100 mb-4">
                            <div class="card-header py-3 bg-light">
                                <h6 class="m-0 font-weight-bold text-primary">Inventory Settings</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="min_stock_level" class="form-label fw-bold">Minimum Stock Level</label>
                                            <input type="number" name="min_stock_level" id="min_stock_level" class="form-control @error('min_stock_level') is-invalid @enderror" value="{{ old('min_stock_level', $product->min_stock_level) }}" min="0">
                                            @error('min_stock_level')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <small class="text-muted">Set the minimum stock level for low stock alerts</small>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="status" class="form-label fw-bold">Product Status</label>
                                            <select name="status" id="status" class="form-control @error('status') is-invalid @enderror">
                                                <option value="Active" {{ old('status', $product->status) == 'Active' ? 'selected' : '' }}>Active</option>
                                                <option value="Inactive" {{ old('status', $product->status) == 'Inactive' ? 'selected' : '' }}>Inactive</option>
                                                <option value="Out of Stock" {{ old('status', $product->status) == 'Out of Stock' ? 'selected' : '' }}>Out of Stock</option>
                                                <option value="Discontinued" {{ old('status', $product->status) == 'Discontinued' ? 'selected' : '' }}>Discontinued</option>
                                            </select>
                                            @error('status')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end">
                    <a href="{{ route('admin.products.index') }}" class="btn btn-secondary me-2">Cancel</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Update Product
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Handle image removal checkbox
        const removeImageCheckbox = document.getElementById('remove_image');
        const imageInput = document.getElementById('image');
        
        if (removeImageCheckbox) {
            removeImageCheckbox.addEventListener('change', function() {
                if (this.checked) {
                    imageInput.disabled = true;
                } else {
                    imageInput.disabled = false;
                }
            });
        }
    });
</script>
@endpush
@endsection
