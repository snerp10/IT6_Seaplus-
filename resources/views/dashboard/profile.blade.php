@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Customer Profile</div>
                <div class="card-body">
                    <form method="POST" action="{{ route('customer.profile.update') }}">
                        @csrf
                        @method('PUT')
                        
                        <div class="form-group">
                            <label>Name</label>
                            <input type="text" name="name" class="form-control" value="{{ $user->name }}" required>
                        </div>

                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" name="email" class="form-control" value="{{ $user->email }}" required>
                        </div>

                        <div class="form-group">
                            <label>Phone</label>
                            <input type="text" name="phone" class="form-control" value="{{ $user->phone }}">
                        </div>

                        <div class="form-group">
                            <label>Address</label>
                            <textarea name="address" class="form-control">{{ $user->address }}</textarea>
                        </div>

                        <button type="submit" class="btn btn-primary mt-3">Update Profile</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
