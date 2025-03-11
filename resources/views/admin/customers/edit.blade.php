@extends('layouts.admin')

@section('admin.content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-user-edit"></i> Edit Customer
        </h1>
        <div>
            <a href="{{ route('admin.customers.index') }}" class="btn btn-secondary me-2">
                <i class="fas fa-arrow-left"></i> Back to Customers
            </a>
            <a href="{{ route('admin.customers.show', $customer->cus_id) }}" class="btn btn-info">
                <i class="fas fa-eye"></i> View Customer
            </a>
        </div>
    </div>

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-gradient-primary text-white">
            <h6 class="m-0 font-weight-bold">Edit Customer Information</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.customers.update', $customer->cus_id) }}" method="POST">
                @csrf
                @method('PUT')
                
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
                                        <input type="text" name="fname" id="fname" class="form-control @error('fname') is-invalid @enderror" value="{{ old('fname', $customer->fname) }}" required>
                                        @error('fname')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label for="lname" class="form-label fw-bold">Last Name <span class="text-danger">*</span></label>
                                        <input type="text" name="lname" id="lname" class="form-control @error('lname') is-invalid @enderror" value="{{ old('lname', $customer->lname) }}" required>
                                        @error('lname')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="form-group mb-3">
                                    <label for="mname" class="form-label fw-bold">Middle Name</label>
                                    <input type="text" name="mname" id="mname" class="form-control @error('mname') is-invalid @enderror" value="{{ old('mname', $customer->mname) }}">
                                    @error('mname')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="form-group mb-3">
                                    <label for="contact_number" class="form-label fw-bold">Contact Number <span class="text-danger">*</span></label>
                                    <input type="text" name="contact_number" id="contact_number" class="form-control @error('contact_number') is-invalid @enderror" value="{{ old('contact_number', $customer->contact_number) }}" required>
                                    @error('contact_number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group mb-3">
                                    <label for="email" class="form-label fw-bold">Email <span class="text-danger">*</span></label>
                                    <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $customer->email) }}" required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group mb-3">
                                    <label for="created_at" class="form-label fw-bold">Registration Date</label>
                                    <input type="text" class="form-control" value="{{ $customer->created_at->format('F d, Y') }}" disabled>
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
                                    <label for="street" class="form-label fw-bold">Street Address</label>
                                    <input type="text" name="street" id="street" class="form-control @error('street') is-invalid @enderror" value="{{ old('street', $customer->street) }}">
                                    @error('street')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="form-group mb-3">
                                    <label for="city" class="form-label fw-bold">City</label>
                                    <input type="text" name="city" id="city" class="form-control @error('city') is-invalid @enderror" value="{{ old('city', $customer->city) }}">
                                    @error('city')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="form-group mb-3">
                                    <label for="province" class="form-label fw-bold">Province</label>
                                    <input type="text" name="province" id="province" class="form-control @error('province') is-invalid @enderror" value="{{ old('province', $customer->province) }}">
                                    @error('province')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end">
                    <a href="{{ route('admin.customers.show', $customer->cus_id) }}" class="btn btn-secondary me-2">Cancel</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Update Customer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
