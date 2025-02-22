@extends('layouts.app')

@section('content')
<div class="container vh-100 d-flex justify-content-center align-items-center">
    <div class="col-md-6">
        <div class="card shadow-lg rounded-lg">
            <div class="card-header bg-success text-white text-center">
                <h3><i class="fas fa-user-plus"></i> Register</h3>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('register') }}">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label"><i class="fas fa-envelope"></i> Email</label>
                        <input type="email" name="email" class="form-control rounded-pill" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label"><i class="fas fa-lock"></i> Password</label>
                        <input type="password" name="password" class="form-control rounded-pill" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label"><i class="fas fa-user"></i> First Name</label>
                        <input type="text" name="first_name" class="form-control rounded-pill" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label"><i class="fas fa-user"></i> Last Name</label>
                        <input type="text" name="last_name" class="form-control rounded-pill" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label"><i class="fas fa-phone"></i> Phone</label>
                        <input type="text" name="phone" class="form-control rounded-pill" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label"><i class="fas fa-home"></i> Address</label>
                        <textarea name="address" class="form-control rounded-pill" required></textarea>
                    </div>

                    <div class="text-center">
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