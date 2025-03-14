@extends('layouts.app')

@section('content')
<div class="container vh-100 d-flex justify-content-center align-items-center">
    <div class="col-md-5">
        <div class="card auth-card shadow-lg rounded-lg hover-card">
            <div class="card-header bg-white text-center py-4">
                <div class="logo-circle mx-auto mb-3">
                    <img src="{{ asset('images/logo.png') }}" alt="KSM SeaPlus+" height="60">
                </div>
                <h3 class="m-0"><i class="fas fa-sign-in-alt me-2 gold-text"></i> <span class="myrtle-text fw-bold">Sign In</span></h3>
            </div>
            <div class="card-body p-4">
                @if (session('status'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('status') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <div class="mb-4">
                        <label class="form-label fw-semibold"><i class="fas fa-envelope gold-text me-2"></i>Email Address</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="fas fa-envelope-open"></i></span>
                            <input type="email" name="email" class="form-control border-start-0" 
                                   value="{{ old('email') }}" required autofocus placeholder="Enter your email">
                        </div>
                        @error('email')
                            <span class="text-danger small mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold"><i class="fas fa-lock gold-text me-2"></i>Password</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="fas fa-key"></i></span>
                            <input type="password" name="password" class="form-control border-start-0" required placeholder="Enter your password">
                        </div>
                        @error('password')
                            <span class="text-danger small mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-check mb-4">
                        <input class="form-check-input" type="checkbox" value="" id="agreement" required>
                        <label class="form-check-label" for="agreement">
                            I agree to the <a href="{{ route('terms') }}" target="_blank" class="myrtle-text fw-semibold">Terms of Service</a> and <a href="{{ route('privacy') }}" target="_blank" class="myrtle-text fw-semibold">Privacy Policy</a>
                        </label>
                        @error('agreement')
                            <span class="text-danger small">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="d-grid gap-2 mb-4">
                        <button type="submit" class="btn btn-gold py-2 fw-semibold">
                            <i class="fas fa-sign-in-alt me-2"></i> Sign In
                        </button>
                    </div>

                    <div class="text-center">
                        <p>Don't have an account? 
                            <a href="{{ route('register') }}" class="gold-text fw-bold">Create Account</a>
                        </p>
                    </div>
                </form>
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

