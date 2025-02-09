@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            <h2>Create New Order</h2>
        </div>
        <div class="card-body">
            <form action="{{ route('orders.store') }}" method="POST" id="orderForm">
                @csrf
                <div class="row">
                    <div class="col-md-8">
                        <div class="product-list">
                            @foreach($products as $product)
                            <div class="card mb-3">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-md-4">
                                            <h5>{{ $product->name }}</h5>
                                            <p class="text-muted">{{ $product->category }}</p>
                                        </div>
                                        <div class="col-md-3">
                                            <p>₱{{ number_format($product->price, 2) }} / {{ $product->unit }}</p>
                                        </div>
                                        <div class="col-md-3">
                                            <input type="number" 
                                                   name="products[{{ $product->product_id }}][quantity]"
                                                   class="form-control quantity-input"
                                                   data-price="{{ $product->price }}"
                                                   min="0"
                                                   max="{{ $product->stock }}"
                                                   value="0">
                                        </div>
                                        <div class="col-md-2">
                                            <span class="subtotal">₱0.00</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body">
                                <h4>Order Summary</h4>
                                <hr>
                                <div class="form-group mb-3">
                                    <label>Payment Method</label>
                                    <select name="payment_method" class="form-control" required>
                                        <option value="Cash">Cash</option>
                                        <option value="GCash">GCash</option>
                                    </select>
                                </div>
                                
                                <div class="form-group mb-3">
                                    <label>Order Type</label>
                                    <select name="order_type" class="form-control" required>
                                        <option value="Retail">Retail</option>
                                        <option value="Bulk">Bulk</option>
                                    </select>
                                </div>
                                
                                <div class="total-amount mt-4">
                                    <h5>Total Amount: ₱<span id="total">0.00</span></h5>
                                </div>
                                
                                <input type="hidden" name="total_amount" id="total_amount" value="0">
                                <button type="submit" class="btn btn-primary w-100 mt-3">Place Order</button>
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
