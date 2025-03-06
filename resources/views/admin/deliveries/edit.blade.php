@extends('layouts.admin')

@section('admin.content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-truck"></i> Edit Delivery
        </h1>
        <div>
            <a href="{{ route('admin.deliveries.index') }}" class="btn btn-secondary mr-2">
                <i class="fas fa-arrow-left"></i> Back to Deliveries
            </a>
            <a href="{{ route('admin.orders.show', $delivery->order->order_id) }}" class="btn btn-primary">
                <i class="fas fa-shopping-cart"></i> View Order
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
            <h6 class="m-0 font-weight-bold">Edit Delivery for Order #{{ $delivery->order->order_id }}</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.deliveries.update', $delivery) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="card border-left-info shadow h-100">
                            <div class="card-header py-3 bg-light">
                                <h6 class="m-0 font-weight-bold text-primary">Delivery Details</h6>
                            </div>
                            <div class="card-body">
                                <div class="form-group mb-3">
                                    <label for="order_id" class="form-label fw-bold">Order</label>
                                    <input type="text" class="form-control" value="Order #{{ $delivery->order->order_id }} - {{ $delivery->order->customer->fname }} {{ $delivery->order->customer->lname }}" readonly>
                                </div>

                                <div class="form-group mb-3">
                                    <label for="delivery_date" class="form-label fw-bold">Delivery Date</label>
                                    <input type="date" name="delivery_date" id="delivery_date" class="form-control @error('delivery_date') is-invalid @enderror" value="{{ old('delivery_date', $delivery->delivery_date) }}" min="{{ date('Y-m-d') }}">
                                    @error('delivery_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group mb-3">
                                    <label for="delivery_status" class="form-label fw-bold">Delivery Status <span class="text-danger">*</span></label>
                                    <select name="delivery_status" id="delivery_status" class="form-control @error('delivery_status') is-invalid @enderror" required>
                                        <option value="Pending" {{ old('delivery_status', $delivery->delivery_status) == 'Pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="Scheduled" {{ old('delivery_status', $delivery->delivery_status) == 'Scheduled' ? 'selected' : '' }}>Scheduled</option>
                                        <option value="Out for Delivery" {{ old('delivery_status', $delivery->delivery_status) == 'Out for Delivery' ? 'selected' : '' }}>Out for Delivery</option>
                                        <option value="Delivered" {{ old('delivery_status', $delivery->delivery_status) == 'Delivered' ? 'selected' : '' }}>Delivered</option>
                                        <option value="Failed" {{ old('delivery_status', $delivery->delivery_status) == 'Failed' ? 'selected' : '' }}>Failed</option>
                                        <option value="Returned" {{ old('delivery_status', $delivery->delivery_status) == 'Returned' ? 'selected' : '' }}>Returned</option>
                                    </select>
                                    @error('delivery_status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="form-group mb-3">
                                    <label for="delivery_cost" class="form-label fw-bold">Delivery Cost</label>
                                    <div class="input-group">
                                        <span class="input-group-text">₱</span>
                                        <input type="number" name="delivery_cost" id="delivery_cost" class="form-control @error('delivery_cost') is-invalid @enderror" value="{{ old('delivery_cost', $delivery->delivery_cost) }}" min="0" step="0.01">
                                    </div>
                                    @error('delivery_cost')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">Changes to delivery cost will update the order's total amount.</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="card border-left-warning shadow h-100">
                            <div class="card-header py-3 bg-light">
                                <h6 class="m-0 font-weight-bold text-primary">Delivery Address</h6>
                            </div>
                            <div class="card-body">
                                <div class="form-group mb-3">
                                    <label for="street" class="form-label fw-bold">Street</label>
                                    <input type="text" name="street" id="street" class="form-control @error('street') is-invalid @enderror" value="{{ old('street', $delivery->street) }}">
                                    @error('street')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="form-group mb-3">
                                    <label for="city" class="form-label fw-bold">City</label>
                                    <input type="text" name="city" id="city" class="form-control @error('city') is-invalid @enderror" value="{{ old('city', $delivery->city) }}">
                                    @error('city')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="form-group mb-3">
                                    <label for="province" class="form-label fw-bold">Province</label>
                                    <input type="text" name="province" id="province" class="form-control @error('province') is-invalid @enderror" value="{{ old('province', $delivery->province) }}">
                                    @error('province')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="form-group mb-3">
                                    <label for="special_instructions" class="form-label fw-bold">Special Instructions</label>
                                    <textarea name="special_instructions" id="special_instructions" class="form-control @error('special_instructions') is-invalid @enderror" rows="3">{{ old('special_instructions', $delivery->special_instructions) }}</textarea>
                                    @error('special_instructions')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end">
                    <a href="{{ route('admin.deliveries.show', $delivery) }}" class="btn btn-secondary me-2">Cancel</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Update Delivery
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
