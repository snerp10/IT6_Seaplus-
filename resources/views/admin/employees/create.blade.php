@extends('layouts.admin')

@section('title', 'Add New Employee')

@section('admin.content')
<div class="container-fluid">
    <!-- Content Header -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><i class="fas fa-user-plus mr-2"></i>Add New Employee</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.employees.index') }}">Employees</a></li>
                        <li class="breadcrumb-item active">Add Employee</li>
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
        <div class="container-fluid">
            <div class="card card-primary shadow">
                <div class="card-header">
                    <h3 class="card-title">Employee Details</h3>
                </div>
                <form action="{{ route('admin.employees.store') }}" method="POST">
                    @csrf
                    <div class="card-body">
                        <!-- Personal Information -->
                        <h5 class="mb-3">Personal Information</h5>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="lname">Last Name</label>
                                    <input type="text" class="form-control @error('lname') is-invalid @enderror" id="lname" name="lname" value="{{ old('lname') }}" required maxlength="50">
                                    @error('lname')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="fname">First Name</label>
                                    <input type="text" class="form-control @error('fname') is-invalid @enderror" id="fname" name="fname" value="{{ old('fname') }}" required maxlength="50">
                                    @error('fname')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="mname">Middle Name</label>
                                    <input type="text" class="form-control @error('mname') is-invalid @enderror" id="mname" name="mname" value="{{ old('mname') }}" maxlength="50">
                                    @error('mname')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="gender">Gender</label>
                                    <select class="form-control @error('gender') is-invalid @enderror" id="gender" name="gender">
                                        <option value="">Select Gender</option>
                                        <option value="Male" {{ old('gender') == 'Male' ? 'selected' : '' }}>Male</option>
                                        <option value="Female" {{ old('gender') == 'Female' ? 'selected' : '' }}>Female</option>
                                        <option value="Other" {{ old('gender') == 'Other' ? 'selected' : '' }}>Other</option>
                                    </select>
                                    @error('gender')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="birthdate">Birthdate</label>
                                    <input type="date" class="form-control @error('birthdate') is-invalid @enderror" id="birthdate" name="birthdate" value="{{ old('birthdate') }}" required>
                                    @error('birthdate')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="contact_number">Contact Number</label>
                                    <input type="text" class="form-control @error('contact_number') is-invalid @enderror" id="contact_number" name="contact_number" value="{{ old('contact_number') }}" required maxlength="20">
                                    @error('contact_number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="email">Email</label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required maxlength="100">
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Address Information -->
                        <h5 class="mt-4 mb-3">Address Information</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="street">Street</label>
                                    <input type="text" class="form-control @error('street') is-invalid @enderror" id="street" name="street" value="{{ old('street') }}" required maxlength="100">
                                    @error('street')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="barangay">Barangay</label>
                                    <input type="text" class="form-control @error('barangay') is-invalid @enderror" id="barangay" name="barangay" value="{{ old('barangay') }}" required maxlength="100">
                                    @error('barangay')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="city">City</label>
                                    <input type="text" class="form-control @error('city') is-invalid @enderror" id="city" name="city" value="{{ old('city') }}" required maxlength="100">
                                    @error('city')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="province">Province</label>
                                    <input type="text" class="form-control @error('province') is-invalid @enderror" id="province" name="province" value="{{ old('province') }}" required maxlength="100">
                                    @error('province')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="postal_code">Postal Code</label>
                                    <input type="text" class="form-control @error('postal_code') is-invalid @enderror" id="postal_code" name="postal_code" value="{{ old('postal_code') }}" maxlength="20">
                                    @error('postal_code')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="country">Country</label>
                                    <input type="text" class="form-control @error('country') is-invalid @enderror" id="country" name="country" value="{{ old('country', 'Philippines') }}" required maxlength="50">
                                    @error('country')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Employment Information -->
                        <h5 class="mt-4 mb-3">Employment Information</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="position">Position</label>
                                    <select class="form-control @error('position') is-invalid @enderror" id="position" name="position" required>
                                        <option value="Driver" {{ old('position') == 'Driver' ? 'selected' : '' }}>Driver</option>
                                        <option value="Staff" {{ old('position') == 'Staff' ? 'selected' : '' }}>Staff</option>
                                        <option value="Cashier" {{ old('position') == 'Cashier' ? 'selected' : '' }}>Cashier</option>
                                        <option value="Laborer" {{ old('position') == 'Laborer' ? 'selected' : '' }}>Laborer</option>
                                        <option value="Supervisor" {{ old('position') == 'Supervisor' ? 'selected' : '' }}>Supervisor</option>
                                        <option value="Manager" {{ old('position') == 'Manager' ? 'selected' : '' }}>Manager</option>
                                        <option value="Admin" {{ old('position') == 'Admin' ? 'selected' : '' }}>Admin</option>
                                    </select>
                                    @error('position')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="salary">Salary</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">₱</span>
                                        </div>
                                        <input type="number" class="form-control @error('salary') is-invalid @enderror" id="salary" name="salary" value="{{ old('salary') }}" required step="0.01" min="0">
                                        @error('salary')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Account Information -->
                        <h5 class="mt-4 mb-3">User Account Information</h5>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle mr-1"></i> A user account will be created for this employee to access the system.
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="password">Password</label>
                                    <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required>
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">Password must be at least 8 characters.</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="password_confirmation">Confirm Password</label>
                                    <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="card-footer">
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.employees.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left mr-1"></i> Back to List
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save mr-1"></i> Add Employee
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>
</div>
@endsection

