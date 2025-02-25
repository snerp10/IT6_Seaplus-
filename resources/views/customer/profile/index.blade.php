@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-lg rounded-lg">
                <div class="card-header bg-primary text-white text-center">
                    <h3><i class="fas fa-user-circle"></i> Customer Profile</h3>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('customer.update') }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label class="form-label"><i class="fas fa-user"></i> First Name</label>
                            <input type="text" name="fname" class="form-control rounded-pill" 
                                   value="{{ old('fname', $customer->fname) }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label"><i class="fas fa-user"></i> Middle Name</label>
                            <input type="text" name="mname" class="form-control rounded-pill" 
                                   value="{{ old('mname', $customer->mname) }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label"><i class="fas fa-user"></i> Last Name</label>
                            <input type="text" name="lname" class="form-control rounded-pill" 
                                   value="{{ old('lname', $customer->lname) }}" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label"><i class="fas fa-envelope"></i> Email</label>
                            <input type="email" name="email" class="form-control rounded-pill" 
                                   value="{{ old('email', $customer->email) }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label"><i class="fas fa-birthday-cake"></i> Birth Date</label>
                            <input type="date" name="birthdate" class="form-control rounded-pill"
                                   value="{{ old('birthdate', $customer->birthdate) }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label"><i class="fas fa-phone"></i> Phone</label>
                            <input type="text" name="contact_number" class="form-control rounded-pill"
                                   value="{{ old('contact_number', $customer->contact_number) }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label"><i class="fas fa-map-marker-alt"></i> Address</label>
                            <textarea name="address" class="form-control rounded-3" rows="3">{{ old('address', $customer->address) }}</textarea>
                        </div>

                        <div class="text-center">       
                            <button type="submit" class="btn btn-primary rounded-pill px-4">
                                <i class="fas fa-save"></i> Update Profile
                            </button>
                            <a href="{{ route('dashboard.index') }}" class="btn btn-secondary rounded-pill px-4">
                                <i class="fas fa-arrow-left"></i> Back to Dashboard
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
