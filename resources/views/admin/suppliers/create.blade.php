@extends('layouts.admin')

@section('admin.content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Add Supplier</h1>
        <a href="{{ route('admin.suppliers.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Suppliers
        </a>
    </div>

    <div class="card shadow">
        <div class="card-body">
            <form action="{{ route('admin.suppliers.store') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="company_name" class="form-label">Company Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('company_name') is-invalid @enderror" 
                        id="company_name" name="company_name" value="{{ old('company_name') }}" required>
                    @error('company_name')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                    <input type="email" class="form-control @error('email') is-invalid @enderror" 
                        id="email" name="email" value="{{ old('email') }}" required>
                    @error('email')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="contact_number" class="form-label">Contact Number <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('contact_number') is-invalid @enderror" 
                        id="contact_number" name="contact_number" value="{{ old('contact_number') }}" required>
                    @error('contact_number')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <h5 class="mt-4 mb-3">Address Information</h5>
                <div class="mb-3">
                    <label for="street" class="form-label">Street <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('street') is-invalid @enderror" 
                        id="street" name="street" value="{{ old('street') }}" required>
                    @error('street')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="city" class="form-label">City <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('city') is-invalid @enderror" 
                            id="city" name="city" value="{{ old('city') }}" required>
                        @error('city')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="province" class="form-label">Province <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('province') is-invalid @enderror" 
                            id="province" name="province" value="{{ old('province') }}" required>
                        @error('province')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label for="prod_type" class="form-label">Product Type <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('prod_type') is-invalid @enderror" 
                        id="prod_type" name="prod_type" value="{{ old('prod_type') }}" required>
                    @error('prod_type')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Add Supplier
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
