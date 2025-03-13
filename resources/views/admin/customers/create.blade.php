@extends('layouts.admin')

@section('admin.content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-user-plus"></i> Create New Customer
        </h1>
        <a href="{{ route('admin.customers.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Customers
        </a>
    </div>

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-gradient-primary text-white">
            <h6 class="m-0 font-weight-bold">Customer Information</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.customers.store') }}" method="POST">
                @csrf
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="card border-left-primary shadow h-100 mb-4">
                            <div class="card-header py-3 bg-light">
                                <h6 class="m-0 font-weight-bold text-primary">Personal Information</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="fname" class="form-label fw-bold">First Name <span class="text-danger">*</span></label>
                                        <input type="text" name="fname" id="fname" class="form-control @error('fname') is-invalid @enderror" value="{{ old('fname') }}" required>
                                        @error('fname')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label for="lname" class="form-label fw-bold">Last Name <span class="text-danger">*</span></label>
                                        <input type="text" name="lname" id="lname" class="form-control @error('lname') is-invalid @enderror" value="{{ old('lname') }}" required>
                                        @error('lname')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="form-group mb-3">
                                    <label for="mname" class="form-label fw-bold">Middle Name</label>
                                    <input type="text" name="mname" id="mname" class="form-control @error('mname') is-invalid @enderror" value="{{ old('mname') }}">
                                    @error('mname')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="form-group mb-3">
                                    <label for="birthdate" class="form-label fw-bold">Birthdate <span class="text-danger">*</span></label>
                                    <input type="date" name="birthdate" id="birthdate" class="form-control @error('birthdate') is-invalid @enderror" value="{{ old('birthdate') }}" required>
                                    @error('birthdate')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="form-group mb-3">
                                    <label for="contact_number" class="form-label fw-bold">Contact Number <span class="text-danger">*</span></label>
                                    <input type="text" name="contact_number" id="contact_number" class="form-control @error('contact_number') is-invalid @enderror" value="{{ old('contact_number') }}" required>
                                    @error('contact_number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group mb-3">
                                    <label for="email" class="form-label fw-bold">Email <span class="text-danger">*</span></label>
                                    <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="card border-left-info shadow h-100 mb-4">
                            <div class="card-header py-3 bg-light">
                                <h6 class="m-0 font-weight-bold text-primary">Address Information</h6>
                            </div>
                            <div class="card-body">
                                <div class="form-group mb-3">
                                    <label for="address" class="form-label fw-bold">Street Address</label>
                                    <input type="text" name="address" id="address" class="form-control @error('address') is-invalid @enderror" value="{{ old('address') }}">
                                    @error('address')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="form-group mb-3">
                                    <label for="city" class="form-label fw-bold">City</label>
                                    <input type="text" name="city" id="city" class="form-control @error('city') is-invalid @enderror" value="{{ old('city') }}">
                                    @error('city')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="form-group mb-3">
                                    <label for="province" class="form-label fw-bold">Province</label>
                                    <input type="text" name="province" id="province" class="form-control @error('province') is-invalid @enderror" value="{{ old('province') }}">
                                    @error('province')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="form-group mb-3">
                                    <label for="postal_code" class="form-label fw-bold">Postal Code <span class="text-danger">*</span></label>
                                    <input type="text" name="postal_code" id="postal_code" class="form-control @error('postal_code') is-invalid @enderror" value="{{ old('postal_code') }}" required>
                                    @error('postal_code')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="form-group mb-3">
                                    <label for="country" class="form-label fw-bold">Country <span class="text-danger">*</span></label>
                                    <input type="text" name="country" id="country" class="form-control @error('country') is-invalid @enderror" value="{{ old('country', 'Philippines') }}" required>
                                    @error('country')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- User Account Information -->
                <div class="card border-left-success shadow h-100 mb-4">
                    <div class="card-header py-3 bg-light">
                        <h6 class="m-0 font-weight-bold text-primary">User Account Information</h6>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> A user account with login credentials will be created for this customer.
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="password" class="form-label fw-bold">Password <span class="text-danger">*</span></label>
                                <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror" required>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Password must be at least 8 characters.</small>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="password_confirmation" class="form-label fw-bold">Confirm Password <span class="text-danger">*</span></label>
                                <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" required>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end">
                    <a href="{{ route('admin.customers.index') }}" class="btn btn-secondary me-2">Cancel</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Create Customer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
