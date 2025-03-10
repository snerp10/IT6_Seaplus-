@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0">Place New Order</h4>
        </div>
        <div class="card-body">
            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif
            
            <form action="{{ route('customer.orders.store') }}" method="POST">
                @csrf
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="order_type">Order Type</label>
                            <select name="order_type" id="order_type" class="form-control @error('order_type') is-invalid @enderror" required>
                                <option value="Retail" {{ old('order_type') == 'Retail' ? 'selected' : '' }}>Retail</option>
                                <option value="Bulk" {{ old('order_type') == 'Bulk' ? 'selected' : '' }}>Bulk</option>
                            </select>
                            <small class="form-text text-muted" id="order-type-hint">
                                Retail orders require full payment. Bulk orders require minimum 30% downpayment via GCash.
                            </small>
                            @error('order_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="pay_method">Payment Method</label>
                            <select name="pay_method" id="pay_method" class="form-control @error('pay_method') is-invalid @enderror" required>
                                <option value="GCash" {{ old('pay_method') == 'GCash' ? 'selected' : '' }}>GCash</option>
                                <option value="Cash on Delivery" {{ old('pay_method') == 'Cash on Delivery' ? 'selected' : '' }}>Cash on Delivery</option>
                            </select>
                            <small class="form-text text-muted" id="payment-hint">
                                For COD, payment will be collected upon delivery.
                            </small>
                            @error('pay_method')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <h5 class="mt-4 mb-3 border-bottom pb-2">Select Products</h5>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Product</th>
                                <th>Price</th>
                                <th>Available</th>
                                <th width="150">Quantity</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($products as $product)
                                <tr class="{{ isset($selectedProduct) && $selectedProduct->prod_id == $product->prod_id ? 'table-primary' : '' }}">
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if($product->image)
                                                <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="img-thumbnail mr-2" width="50">
                                            @endif
                                            <div>
                                                <strong>{{ $product->name }}</strong>
                                                <div class="small text-muted">{{ $product->category }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>₱{{ number_format($product->pricing->first()->selling_price ?? 0, 2) }} / {{ $product->unit }}</td>
                                    <td>{{ $product->getStockAttribute() }} {{ $product->unit }}(s)</td>
                                    <td>
                                        <input type="number" 
                                            name="products[{{ $product->prod_id }}][quantity]" 
                                            min="0" 
                                            max="{{ $product->getStockAttribute() }}" 
                                            value="{{ (isset($selectedProduct) && $selectedProduct->prod_id == $product->prod_id) ? 1 : 0 }}" 
                                            class="form-control product-quantity" 
                                            data-price="{{ $product->pricing->first()->selling_price ?? 0 }}"
                                            data-product-id="{{ $product->prod_id }}">
                                        <input type="hidden" name="products[{{ $product->prod_id }}][prod_id]" value="{{ $product->prod_id }}">
                                    </td>
                                    <td class="product-subtotal">₱0.00</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="4" class="text-end"><strong>Subtotal:</strong></td>
                                <td id="subtotal-amount">₱0.00</td>
                            </tr>
                            <tr>
                                <td colspan="4" class="text-end"><strong>Delivery Fee:</strong></td>
                                <td id="delivery-fee">₱100.00</td>
                            </tr>
                            <tr>
                                <td colspan="4" class="text-end"><strong>Total:</strong></td>
                                <td id="total-amount">₱100.00</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <div class="alert alert-info mt-3">
                    <i class="fas fa-info-circle"></i> After placing your order, you'll be asked to provide delivery details.
                </div>

                <div class="text-center mt-4">
                    <button type="submit" class="btn btn-primary" id="place-order-btn" disabled>
                        <i class="fas fa-check-circle mr-1"></i> Place Order
                    </button>
                    <a href="{{ route('customer.dashboard.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times-circle mr-1"></i> Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        // Calculate subtotal and total when quantities change
        $('.product-quantity').on('input', function() {
            let quantity = parseInt($(this).val()) || 0;
            let price = parseFloat($(this).data('price'));
            let subtotal = quantity * price;
            let maxStock = parseInt($(this).attr('max'));
            
            // Check if quantity exceeds available stock
            if (quantity > maxStock) {
                $(this).val(maxStock);
                quantity = maxStock;
                subtotal = quantity * price;
                
                // Show warning
                if (!$(this).next('.invalid-feedback').length) {
                    $(this).after('<div class="invalid-feedback d-block">Only ' + maxStock + ' available in stock.</div>');
                }
            } else {
                // Remove warning if exists
                $(this).next('.invalid-feedback').remove();
            }
            
            // Update subtotal
            $(this).closest('tr').find('.product-subtotal').text('₱' + subtotal.toFixed(2));
            
            // Update total
            updateTotal();
        });
        
        // Update totals when order type changes
        $('#order_type').on('change', function() {
            updateTotal();
            updatePaymentOptions();
        });
        
        // Update hints when payment method changes
        $('#pay_method').on('change', function() {
            updatePaymentHint();
        });
        
        function updateTotal() {
            let subtotal = 0;
            $('.product-quantity').each(function() {
                let quantity = parseInt($(this).val()) || 0;
                let price = parseFloat($(this).data('price'));
                subtotal += quantity * price;
            });
            
            // Calculate delivery fee based on order type
            let deliveryFee = $('#order_type').val() === 'Retail' ? 100 : 200;
            let total = subtotal + deliveryFee;
            
            $('#subtotal-amount').text('₱' + subtotal.toFixed(2));
            $('#delivery-fee').text('₱' + deliveryFee.toFixed(2));
            $('#total-amount').text('₱' + total.toFixed(2));
            
            // Enable/disable place order button
            $('#place-order-btn').prop('disabled', subtotal === 0);
        }
        
        function updatePaymentOptions() {
            let orderType = $('#order_type').val();
            let payMethod = $('#pay_method');
            
            // For retail orders, show appropriate hint
            if (orderType === 'Retail') {
                $('#order-type-hint').text('Retail orders include a ₱100 delivery fee.');
            } else {
                $('#order-type-hint').text('Bulk orders include a ₱200 delivery fee. Require minimum 30% downpayment via GCash.');
            }
            
            updatePaymentHint();
        }
        
        function updatePaymentHint() {
            let orderType = $('#order_type').val();
            let payMethod = $('#pay_method').val();
            
            if (payMethod === 'Cash on Delivery') {
                $('#payment-hint').text('For COD, payment will be collected upon delivery.');
            } else if (payMethod === 'GCash') {
                if (orderType === 'Bulk') {
                    $('#payment-hint').text('For bulk orders, minimum 30% downpayment is required via GCash.');
                } else {
                    $('#payment-hint').text('Full payment is required for retail orders via GCash.');
                }
            }
        }

        // Trigger update when page loads (for preselected product)
        $('.product-quantity').trigger('input');
        updatePaymentOptions();
    });
</script>
@endpush
@endsection
