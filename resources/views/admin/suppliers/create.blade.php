@extends('layouts.admin')

@section('title', 'Add New Supplier')

@section('admin.content')
<div class="container-fluid">
    <!-- Content Header -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><i class="fas fa-plus-circle mr-2"></i>Add New Supplier</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.suppliers.index') }}">Suppliers</a></li>
                        <li class="breadcrumb-item active">Add Supplier</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <!-- Status Messages -->
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle mr-2"></i> {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <!-- Main content -->
    <section class="content">
        <form action="{{ route('admin.suppliers.store') }}" method="POST">
            @csrf
            <div class="row">
                <!-- Supplier Information -->
                <div class="col-md-8">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Supplier Information</h6>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <label for="company_name">Company Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('company_name') is-invalid @enderror" 
                                       id="company_name" name="company_name" value="{{ old('company_name') }}" required>
                                @error('company_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="contact_number">Contact Number <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('contact_number') is-invalid @enderror" 
                                               id="contact_number" name="contact_number" value="{{ old('contact_number') }}" required>
                                        @error('contact_number')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="email">Email <span class="text-danger">*</span></label>
                                        <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                               id="email" name="email" value="{{ old('email') }}" required>
                                        @error('email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="street">Street Address <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('street') is-invalid @enderror" 
                                       id="street" name="street" value="{{ old('street') }}" required>
                                @error('street')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="city">City <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('city') is-invalid @enderror" 
                                               id="city" name="city" value="{{ old('city') }}" required>
                                        @error('city')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="province">Province <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('province') is-invalid @enderror" 
                                               id="province" name="province" value="{{ old('province') }}" required>
                                        @error('province')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="prod_type">Product Type <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('prod_type') is-invalid @enderror" 
                                       id="prod_type" name="prod_type" value="{{ old('prod_type') }}" 
                                       placeholder="e.g. Sand, Gravel, Construction Materials" required>
                                @error('prod_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="form-group">
                                <label for="notes">Additional Notes</label>
                                <textarea class="form-control @error('notes') is-invalid @enderror" 
                                          id="notes" name="notes" rows="3">{{ old('notes') }}</textarea>
                                @error('notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Supplier Details -->
                <div class="col-md-4">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Supplier Details</h6>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <div class="custom-control custom-switch mb-3">
                                    <input type="checkbox" class="custom-control-input" id="active_supplier" name="active_supplier" value="1" checked>
                                    <label class="custom-control-label" for="active_supplier">Active Supplier</label>
                                </div>
                                
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" id="preferred_supplier" name="preferred_supplier" value="1">
                                    <label class="custom-control-label" for="preferred_supplier">Preferred Supplier</label>
                                </div>
                            </div>
                            
                            <hr>
                            
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle mr-1"></i> You can add products to this supplier after creating the supplier record.
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Associated Products -->

            <div class="row">
                <div class="col-12">
                    <div class="card shadow mb-4">
                        <div class="card-body text-right">
                            <a href="{{ route('admin.suppliers.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times mr-1"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary ml-2">
                                <i class="fas fa-save mr-1"></i> Save Supplier
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </section>
</div>
@endsection
