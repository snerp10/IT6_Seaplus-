@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0">Delivery Information</h4>
        </div>
        <div class="card-body">
            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif
            
            <form action="{{ route('customer.delivery.update', $order->order_id) }}" method="POST">
                @csrf
                @method('PATCH')
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="delivery_date">Delivery Date</label>
                            <input type="date" 
                                name="delivery_date" 
                                id="delivery_date"
                                class="form-control @error('delivery_date') is-invalid @enderror" 
                                value="{{ old('delivery_date', $delivery->delivery_date ? $delivery->delivery_date->format('Y-m-d') : date('Y-m-d', strtotime('+1 day'))) }}" 
                                min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                                required>
                            <small class="form-text text-muted">Select a date at least one day from today</small>
                            @error('delivery_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="form-group mb-3">
                            <label for="street">Street Address</label>
                            <input type="text" 
                                name="street" 
                                id="street"
                                class="form-control @error('street') is-invalid @enderror" 
                                value="{{ old('street', $delivery->street) }}" 
                                required>
                            @error('street')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="city">City</label>
                            <input type="text" 
                                name="city" 
                                id="city"
                                class="form-control @error('city') is-invalid @enderror" 
                                value="{{ old('city', $delivery->city) }}" 
                                required>
                            @error('city')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="form-group mb-3">
                            <label for="province">Province</label>
                            <input type="text" 
                                name="province" 
                                id="province"
                                class="form-control @error('province') is-invalid @enderror" 
                                value="{{ old('province', $delivery->province) }}" 
                                required>
                            @error('province')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                
                <div class="form-group mb-3">
                    <label for="special_instructions">Special Instructions</label>
                    <textarea 
                        name="special_instructions" 
                        id="special_instructions"
                        class="form-control @error('special_instructions') is-invalid @enderror" 
                        rows="3">{{ old('special_instructions', $delivery->special_instructions) }}</textarea>
                    <small class="form-text text-muted">Any special delivery instructions or landmarks to help locate your address</small>
                    @error('special_instructions')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="text-center mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save mr-1"></i> Save Delivery Details
                    </button>
                    <a href="{{ route('customer.orders.show', $order->order_id) }}" class="btn btn-secondary">
                        <i class="fas fa-times-circle mr-1"></i> Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
