@extends('layouts.admin')

@section('admin.content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Edit Supplier</h1>
        <a href="{{ route('admin.suppliers.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Suppliers
        </a>
    </div>

    <div class="card shadow">
        <div class="card-body">
            <form action="{{ route('admin.suppliers.update', $supplier->supp_id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="mb-3">
                    <label for="company_name" class="form-label">Company Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('company_name') is-invalid @enderror" 
                        id="company_name" name="company_name" value="{{ old('company_name', $supplier->company_name) }}" required>
                    @error('company_name')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                    <input type="email" class="form-control @error('email') is-invalid @enderror" 
                        id="email" name="email" value="{{ old('email', $supplier->email) }}" required>
                    @error('email')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="contact_number" class="form-label">Contact Number <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('contact_number') is-invalid @enderror" 
                        id="contact_number" name="contact_number" value="{{ old('contact_number', $supplier->contact_number) }}" required>
                    @error('contact_number')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <h5 class="mt-4 mb-3">Address Information</h5>
                <div class="mb-3">
                    <label for="street" class="form-label">Street <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('street') is-invalid @enderror" 
                        id="street" name="street" value="{{ old('street', $supplier->street) }}" required>
                    @error('street')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="city" class="form-label">City <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('city') is-invalid @enderror" 
                            id="city" name="city" value="{{ old('city', $supplier->city) }}" required>
                        @error('city')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="province" class="form-label">Province <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('province') is-invalid @enderror" 
                            id="province" name="province" value="{{ old('province', $supplier->province) }}" required>
                        @error('province')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label for="prod_type" class="form-label">Product Type <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('prod_type') is-invalid @enderror" 
                        id="prod_type" name="prod_type" value="{{ old('prod_type', $supplier->prod_type) }}" required>
                    @error('prod_type')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="d-flex justify-content-between">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Update Supplier
                    </button>
                    
                    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteSupplierModal">
                        <i class="fas fa-trash"></i> Delete Supplier
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteSupplierModal" tabindex="-1" aria-labelledby="deleteSupplierModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="deleteSupplierModalLabel">Confirm Delete</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this supplier? This action cannot be undone.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form action="{{ route('admin.suppliers.destroy', $supplier->supp_id) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Delete Supplier</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
