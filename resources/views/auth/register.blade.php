@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card auth-card shadow-lg rounded-lg hover-card">
                <div class="card-header bg-white text-center py-4">
                    <div class="logo-circle mx-auto mb-3">
                        <img src="{{ asset('images/logo.png') }}" alt="KSM SeaPlus+">
                    </div>
                    <h3 class="m-0"><i class="fas fa-user-plus me-2 gold-text"></i> <span class="myrtle-text fw-bold">Create Account</span></h3>
                </div>
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('register') }}">
                        @csrf
                        <!-- Email & Password Section -->
                        <div class="row mb-4">
                            <div class="col-md-6 mb-3 mb-md-0">
                                <label class="form-label fw-semibold"><i class="fas fa-envelope gold-text me-2"></i>Email Address</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0"><i class="fas fa-envelope-open"></i></span>
                                    <input type="email" name="email" class="form-control border-start-0 @error('email') is-invalid @enderror" value="{{ old('email') }}" required placeholder="Enter your email">
                                </div>
                                @error('email')
                                    <span class="text-danger small mt-1">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold"><i class="fas fa-lock gold-text me-2"></i>Password</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0"><i class="fas fa-key"></i></span>
                                    <input type="password" name="password" class="form-control border-start-0 @error('password') is-invalid @enderror" required placeholder="Create a strong password">
                                </div>
                                @error('password')
                                    <span class="text-danger small mt-1">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <!-- Personal Information Section -->
                        <h5 class="card-title border-bottom pb-2 mb-3">
                            <i class="fas fa-id-card gold-text me-2"></i>Personal Information
                        </h5>
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">First Name</label>
                                <input type="text" name="fname" class="form-control @error('fname') is-invalid @enderror" value="{{ old('fname') }}" required placeholder="First name">
                                @error('fname')
                                    <span class="text-danger small mt-1">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Middle Name</label>
                                <input type="text" name="mname" class="form-control @error('mname') is-invalid @enderror" value="{{ old('mname') }}" placeholder="Middle name (optional)">
                                @error('mname')
                                    <span class="text-danger small mt-1">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Last Name</label>
                                <input type="text" name="lname" class="form-control @error('lname') is-invalid @enderror" value="{{ old('lname') }}" required placeholder="Last name">
                                @error('lname')
                                    <span class="text-danger small mt-1">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Gender</label>
                                <select name="gender" class="form-control @error('gender') is-invalid @enderror">
                                    <option value="">Select Gender</option>
                                    <option value="Male" {{ old('gender') == 'Male' ? 'selected' : '' }}>Male</option>
                                    <option value="Female" {{ old('gender') == 'Female' ? 'selected' : '' }}>Female</option>
                                    <option value="Other" {{ old('gender') == 'Other' ? 'selected' : '' }}>Other</option>
                                </select>
                                @error('gender')
                                    <span class="text-danger small mt-1">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Birthdate</label>
                                <input type="date" name="birthdate" class="form-control @error('birthdate') is-invalid @enderror" value="{{ old('birthdate') }}" required>
                                @error('birthdate')
                                    <span class="text-danger small mt-1">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Contact Number</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0"><i class="fas fa-phone"></i></span>
                                    <input type="text" name="contact_number" class="form-control border-start-0 @error('contact_number') is-invalid @enderror" value="{{ old('contact_number') }}" required placeholder="Your contact number">
                                </div>
                                @error('contact_number')
                                    <span class="text-danger small mt-1">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <!-- Address Information Section -->
                        <h5 class="card-title border-bottom pb-2 mb-3">
                            <i class="fas fa-map-marked-alt gold-text me-2"></i>Address Information
                        </h5>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Street</label>
                                <input type="text" name="street" class="form-control @error('street') is-invalid @enderror" value="{{ old('street') }}" placeholder="Street address">
                                @error('street')
                                    <span class="text-danger small mt-1">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Barangay</label>
                                <input type="text" name="barangay" class="form-control @error('barangay') is-invalid @enderror" value="{{ old('barangay') }}" placeholder="Barangay">
                                @error('barangay')
                                    <span class="text-danger small mt-1">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">City</label>
                                <input type="text" name="city" class="form-control @error('city') is-invalid @enderror" value="{{ old('city') }}" required placeholder="City">
                                @error('city')
                                    <span class="text-danger small mt-1">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Province</label>
                                <input type="text" name="province" class="form-control @error('province') is-invalid @enderror" value="{{ old('province') }}" required placeholder="Province">
                                @error('province')
                                    <span class="text-danger small mt-1">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Postal Code</label>
                                <input type="text" name="postal_code" class="form-control @error('postal_code') is-invalid @enderror" value="{{ old('postal_code') }}" placeholder="Postal code">
                                @error('postal_code')
                                    <span class="text-danger small mt-1">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-check mb-4">
                            <input class="form-check-input" type="checkbox" value="" id="terms_agreement" required>
                            <label class="form-check-label" for="terms_agreement">
                                I agree to the <a href="{{ route('terms') }}" target="_blank" class="myrtle-text fw-semibold">Terms of Service</a> and <a href="{{ route('privacy') }}" target="_blank" class="myrtle-text fw-semibold">Privacy Policy</a>
                            </label>
                        </div>

                        <div class="d-grid gap-2 col-md-4 mx-auto mb-4">
                            <button type="submit" class="btn btn-gold py-2 fw-semibold">
                                <i class="fas fa-user-plus me-2"></i> Create Account
                            </button>
                        </div>

                        <div class="text-center">
                            <p>Already have an account? 
                                <a href="{{ route('login') }}" class="gold-text fw-bold">Sign In</a>
                            </p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .logo-circle {
        width: 100px;
        height: 100px;
        border-radius: 50%;
        background: white;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.1);
        padding: 4px;
        border: 3px solid var(--gold-color);
        overflow: hidden;
    }
    
    .logo-circle img {
        max-width: 90%;
        max-height: 90%;
        object-fit: contain;
    }
</style>
@endsection
