@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="card shadow-lg">
        <div class="card-header bg-primary text-white">
            <h2 class="mb-0">🛒 Create New Order</h2>
        </div>
        <div class="card-body">
            <form action="{{ route('orders.store') }}" method="POST" id="orderForm">
                @csrf
                <div class="row">
                    <!-- Product Selection -->
                    <div class="col-md-8">
                        <div class="product-list">
                            @foreach($products as $product)
                            <div class="card mb-3 shadow-sm">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <!-- Product Name & Category -->
                                        <div class="col-md-4">
                                            <h5 class="fw-bold">{{ $product->name }}</h5>
                                            <span class="badge bg-secondary">{{ $product->category }}</span>
                                        </div>

                                        <!-- Price & Unit -->
                                        <div class="col-md-3">
                                            <p class="text-success fw-bold mb-0">₱{{ number_format($product->price, 2) }}</p>
                                            <small class="text-muted">per {{ $product->unit }}</small>
                                        </div>

                                        <!-- Quantity Input -->
                                        <div class="col-md-3">
                                            <input type="number" 
                                                   name="products[{{ $product->product_id }}][quantity]"
                                                   class="form-control quantity-input"
                                                   data-price="{{ $product->price }}"
                                                   min="0"
                                                   max="{{ $product->stock }}"
                                                   value="0">
                                        </div>

                                        <!-- Subtotal -->
                                        <div class="col-md-2 text-end">
                                            <span class="subtotal fw-bold text-primary">₱0.00</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    
                    <!-- Order Summary -->
                    <div class="col-md-4">
                        <div class="card shadow-sm">
                            <div class="card-body">
                                <h4 class="text-center fw-bold">📝 Order Summary</h4>
                                <hr>

                                <!-- Payment Method -->
                                <div class="mb-3">
                                    <label class="fw-bold">💰 Payment Method</label>
                                    <select name="payment_method" class="form-control" required>
                                        <option value="Cash">Cash</option>
                                        <option value="GCash">GCash</option>
                                    </select>
                                </div>
                                
                                <!-- Order Type -->
                                <div class="mb-3">
                                    <label class="fw-bold">📦 Order Type</label>
                                    <select name="order_type" class="form-control" required>
                                        <option value="Retail">Retail</option>
                                        <option value="Bulk">Bulk</option>
                                    </select>
                                </div>
                                
                                <!-- Total Amount -->
                                <div class="total-amount text-center mt-4">
                                    <h5 class="fw-bold text-danger">Total Amount: ₱<span id="total">0.00</span></h5>
                                </div>
                                
                                <input type="hidden" name="total_amount" id="total_amount" value="0">

                                <!-- Place Order Button -->
                                <button type="submit" class="btn btn-primary w-100 mt-3 shadow-sm" id="submitOrder">
                                    <i class="fas fa-check-circle"></i> Place Order and Add Delivery Details
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>


@push('scripts')
<script>
document.getElementById('orderForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const total = parseFloat(document.getElementById('total_amount').value);
    if (total <= 0) {
        alert('Please add at least one product to your order.');
        return;
    }

    // Submit form normally if validation passes
    this.submit();
});

document.querySelectorAll('.quantity-input').forEach(input => {
    input.addEventListener('change', updateSubtotals);
});

function updateSubtotals() {
    let total = 0;
    document.querySelectorAll('.quantity-input').forEach(input => {
        const price = parseFloat(input.dataset.price);
        const quantity = parseInt(input.value);
        const subtotal = price * quantity;
        input.closest('.card-body').querySelector('.subtotal').textContent = 
            '₱' + subtotal.toFixed(2);
        total += subtotal;
    });
    
    document.getElementById('total').textContent = total.toFixed(2);
    document.getElementById('total_amount').value = total;
}
</script>
@endpush
@endsection
