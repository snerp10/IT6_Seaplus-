@extends('layouts.admin')

@section('admin.content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-truck"></i> Create New Delivery
        </h1>
        <a href="{{ route('admin.deliveries.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Deliveries
        </a>
    </div>

    <!-- Add this new alert to explain the purpose -->
    <div class="alert alert-info mb-4">
        <i class="fas fa-info-circle"></i> <strong>Note:</strong> This page is for creating deliveries for orders that were placed without delivery information. 
        Most deliveries are created automatically when an order is placed.
    </div>

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-gradient-primary text-white">
            <h6 class="m-0 font-weight-bold">Delivery Information</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.deliveries.store') }}" method="POST">
                @csrf
                
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="card border-left-info shadow h-100">
                            <div class="card-header py-3 bg-light">
                                <h6 class="m-0 font-weight-bold text-primary">Order Selection</h6>
                            </div>
                            <div class="card-body">
                                <div class="form-group mb-3">
                                    <label for="order_id" class="form-label fw-bold">Select Order <span class="text-danger">*</span></label>
                                    <select name="order_id" id="order_id" class="form-control @error('order_id') is-invalid @enderror" required>
                                        <option value="">-- Select Order --</option>
                                        @foreach($orders as $order)
                                            <option value="{{ $order->order_id }}" data-address="{{ $order->customer->street }}" data-city="{{ $order->customer->city }}" data-province="{{ $order->customer->province }}">
                                                Order #{{ $order->order_id }} - {{ $order->customer->fname }} {{ $order->customer->lname }} - ₱{{ number_format($order->total_amount, 2) }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('order_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group mb-3">
                                    <label for="emp_id" class="form-label fw-bold">Assign Driver</label>
                                    <select name="emp_id" id="emp_id" class="form-control @error('emp_id') is-invalid @enderror">
                                        <option value="">-- No Driver Assigned --</option>
                                        @foreach($drivers as $driver)
                                            <option value="{{ $driver->emp_id }}" {{ old('emp_id') == $driver->emp_id ? 'selected' : '' }}>
                                                {{ $driver->fname }} {{ $driver->lname }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('emp_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group mb-3">
                                    <label for="delivery_date" class="form-label fw-bold">Delivery Date</label>
                                    <input type="date" name="delivery_date" id="delivery_date" class="form-control @error('delivery_date') is-invalid @enderror" value="{{ old('delivery_date') }}" min="{{ date('Y-m-d') }}">
                                    @error('delivery_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group mb-3">
                                    <label for="delivery_status" class="form-label fw-bold">Delivery Status <span class="text-danger">*</span></label>
                                    <select name="delivery_status" id="delivery_status" class="form-control @error('delivery_status') is-invalid @enderror" required>
                                        <option value="Pending" {{ old('delivery_status') == 'Pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="Scheduled" {{ old('delivery_status') == 'Scheduled' ? 'selected' : '' }}>Scheduled</option>
                                        <option value="Out for Delivery" {{ old('delivery_status') == 'Out for Delivery' ? 'selected' : '' }}>Out for Delivery</option>
                                        <option value="Delivered" {{ old('delivery_status') == 'Delivered' ? 'selected' : '' }}>Delivered</option>
                                        <option value="Failed" {{ old('delivery_status') == 'Failed' ? 'selected' : '' }}>Failed</option>
                                        <option value="Returned" {{ old('delivery_status') == 'Returned' ? 'selected' : '' }}>Returned</option>
                                    </select>
                                    @error('delivery_status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="form-group mb-3">
                                    <label for="delivery_cost" class="form-label fw-bold">Delivery Cost</label>
                                    <div class="input-group">
                                        <span class="input-group-text">₱</span>
                                        <input type="number" name="delivery_cost" id="delivery_cost" class="form-control @error('delivery_cost') is-invalid @enderror" value="{{ old('delivery_cost', 0) }}" min="0" step="0.01">
                                    </div>
                                    @error('delivery_cost')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">This cost will be added to the order's total amount.</small>
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
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" id="useCustomerAddress" checked>
                                    <label class="form-check-label" for="useCustomerAddress">
                                        Use customer's address from order
                                    </label>
                                </div>

                                <div class="form-group mb-3">
                                    <label for="street" class="form-label fw-bold">Street</label>
                                    <input type="text" name="street" id="street" class="form-control @error('street') is-invalid @enderror" value="{{ old('street') }}">
                                    @error('street')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="form-group mb-3">
                                    <label for="city" class="form-label fw-bold">City</label>
                                    <input type="text" name="city" id="city" class="form-control @error('city') is-invalid @enderror" value="{{ old('city') }}">
                                    @error('city')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="form-group mb-3">
                                    <label for="province" class="form-label fw-bold">Province</label>
                                    <input type="text" name="province" id="province" class="form-control @error('province') is-invalid @enderror" value="{{ old('province') }}">
                                    @error('province')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="form-group mb-3">
                                    <label for="special_instructions" class="form-label fw-bold">Special Instructions</label>
                                    <textarea name="special_instructions" id="special_instructions" class="form-control @error('special_instructions') is-invalid @enderror" rows="3">{{ old('special_instructions') }}</textarea>
                                    @error('special_instructions')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> Creating a delivery will associate it with the selected order. The delivery cost will be added to the order's total amount.
                </div>

                <div class="d-flex justify-content-end">
                    <a href="{{ route('admin.deliveries.index') }}" class="btn btn-secondary me-2">Cancel</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Create Delivery
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Handle order selection to populate address fields
        const orderSelect = document.getElementById('order_id');
        const streetField = document.getElementById('street');
        const cityField = document.getElementById('city');
        const provinceField = document.getElementById('province');
        const useCustomerAddressCheckbox = document.getElementById('useCustomerAddress');
        
        // Function to populate address fields based on selected order
        function populateAddressFields() {
            if (orderSelect.value && useCustomerAddressCheckbox.checked) {
                const selectedOption = orderSelect.options[orderSelect.selectedIndex];
                streetField.value = selectedOption.dataset.address || '';
                cityField.value = selectedOption.dataset.city || '';
                provinceField.value = selectedOption.dataset.province || '';
            }
        }
        
        // Event listeners
        orderSelect.addEventListener('change', populateAddressFields);
        
        useCustomerAddressCheckbox.addEventListener('change', function() {
            if (this.checked) {
                populateAddressFields();
            } else {
                // Clear address fields if checkbox is unchecked
                streetField.value = '';
                cityField.value = '';
                provinceField.value = '';
            }
        });
        
        // Initial call to populate fields if order is already selected
        populateAddressFields();
    });
</script>
@endpush
@endsection
