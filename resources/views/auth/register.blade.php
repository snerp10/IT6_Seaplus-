@extends('layouts.app')

@section('content')
<div class="container vh-100 d-flex justify-content-center align-items-center">
    <div class="col-md-8">
        <div class="card shadow-lg rounded-lg">
            <div class="card-header bg-success text-white text-center">
                <h3><i class="fas fa-user-plus"></i> Register</h3>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('register') }}">
                    @csrf
                    <!-- Email -->
                    <div class="mb-3">
                        <label class="form-label"><i class="fas fa-envelope"></i> Email</label>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required>
                        @error('email')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div class="mb-3">
                        <label class="form-label"><i class="fas fa-lock"></i> Password</label>
                        <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required>
                        @error('password')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Personal Information -->
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label"><i class="fas fa-user"></i> First Name</label>
                            <input type="text" name="fname" class="form-control @error('fname') is-invalid @enderror" value="{{ old('fname') }}" required>
                            @error('fname')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label"><i class="fas fa-user"></i> Middle Name</label>
                            <input type="text" name="mname" class="form-control @error('mname') is-invalid @enderror" value="{{ old('mname') }}">
                            @error('mname')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label"><i class="fas fa-user"></i> Last Name</label>
                            <input type="text" name="lname" class="form-control @error('lname') is-invalid @enderror" value="{{ old('lname') }}" required>
                            @error('lname')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label"><i class="fas fa-venus-mars"></i> Gender</label>
                            <select name="gender" class="form-control @error('gender') is-invalid @enderror">
                                <option value="">Select Gender</option>
                                <option value="Male" {{ old('gender') == 'Male' ? 'selected' : '' }}>Male</option>
                                <option value="Female" {{ old('gender') == 'Female' ? 'selected' : '' }}>Female</option>
                                <option value="Other" {{ old('gender') == 'Other' ? 'selected' : '' }}>Other</option>
                            </select>
                            @error('gender')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label"><i class="fas fa-calendar-alt"></i> Birthdate</label>
                            <input type="date" name="birthdate" class="form-control @error('birthdate') is-invalid @enderror" value="{{ old('birthdate') }}" required>
                            @error('birthdate')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label"><i class="fas fa-phone"></i> Contact Number</label>
                            <input type="text" name="contact_number" class="form-control @error('contact_number') is-invalid @enderror" value="{{ old('contact_number') }}" required>
                            @error('contact_number')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <!-- Address Information -->
                    <h5 class="mt-4 mb-3">Address Information</h5>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label"><i class="fas fa-road"></i> Street</label>
                            <input type="text" name="street" class="form-control @error('street') is-invalid @enderror" value="{{ old('street') }}">
                            @error('street')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label"><i class="fas fa-map-marker-alt"></i> Barangay</label>
                            <input type="text" name="barangay" class="form-control @error('barangay') is-invalid @enderror" value="{{ old('barangay') }}">
                            @error('barangay')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label"><i class="fas fa-city"></i> City</label>
                            <input type="text" name="city" class="form-control @error('city') is-invalid @enderror" value="{{ old('city') }}" required>
                            @error('city')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label"><i class="fas fa-map"></i> Province</label>
                            <input type="text" name="province" class="form-control @error('province') is-invalid @enderror" value="{{ old('province') }}" required>
                            @error('province')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label"><i class="fas fa-mailbox"></i> Postal Code</label>
                            <input type="text" name="postal_code" class="form-control @error('postal_code') is-invalid @enderror" value="{{ old('postal_code') }}">
                            @error('postal_code')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="text-center mt-4">
                        <button type="submit" class="btn btn-success rounded-pill px-4">
                            <i class="fas fa-user-plus"></i> Register
                        </button>
                    </div>

                    <div class="text-center mt-3">
                        <p>Already have an account? 
                            <a href="{{ route('login') }}" class="text-primary fw-bold">Login here</a>
                        </p>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
