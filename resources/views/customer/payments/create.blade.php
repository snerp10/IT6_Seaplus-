@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0">Process Payment</h4>
        </div>
        <div class="card-body">
            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            @if(session('info'))
                <div class="alert alert-info">{{ session('info') }}</div>
            @endif
            
            @if(isset($showDeliveryNotice) && $showDeliveryNotice)
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> 
                    After completing your payment, you'll be able to update your delivery details.
                </div>
            @endif
            
            <div class="alert alert-info">
                <h5><i class="fas fa-info-circle"></i> Payment Information</h5>
                <p>
                    <strong>GCash Payments:</strong>
                    @if($order->order_type === 'Retail')
                        Retail orders require full payment via GCash before delivery.
                    @else
                        Bulk orders require a minimum down payment of 30% (₱{{ number_format($minimumPayment, 2) }}) via GCash, with the remaining balance collected upon delivery.
                    @endif
                </p>
                <p>
                    <strong>Cash on Delivery (COD):</strong>
                    @if($order->order_type === 'Retail')
                        For retail orders, the full payment will be collected upon delivery.
                    @else
                        For bulk orders, a minimum down payment of 30% is required online, with the remaining balance collected upon delivery.
                    @endif
                </p>
            </div>
            
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header bg-light">
                            <h5 class="mb-0">Order Summary</h5>
                        </div>
                        <div class="card-body">
                            <table class="table table-sm">
                                <tr>
                                    <th>Order Number:</th>
                                    <td>#{{ $order->order_id }}</td>
                                </tr>
                                <tr>
                                    <th>Order Type:</th>
                                    <td>{{ $order->order_type }}</td>
                                </tr>
                                <tr>
                                    <th>Order Date:</th>
                                    <td>{{ $order->created_at->format('M d, Y') }}</td>
                                </tr>
                                <tr>
                                    <th>Subtotal:</th>
                                    <td>₱{{ number_format($order->total_amount, 2) }}</td>
                                </tr>
                                <tr>
                                    <th>Already Paid:</th>
                                    <td>₱{{ number_format($totalPaid, 2) }}</td>
                                </tr>
                                <tr>
                                    <th>Remaining Balance:</th>
                                    <td class="fw-bold text-danger">₱{{ number_format($remainingAmount, 2) }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card h-100">
                        <div class="card-header bg-light">
                            <h5 class="mb-0">Payment Information</h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('customer.payments.store') }}" method="POST">
                                @csrf
                                <input type="hidden" name="order_id" value="{{ $order->order_id }}">
                                
                                <div class="form-group mb-3">
                                    <label for="amount_paid">Amount to Pay</label>
                                    <div class="input-group">
                                        <span class="input-group-text">₱</span>
                                        <input type="number" 
                                            name="amount_paid" 
                                            id="amount_paid" 
                                            class="form-control @error('amount_paid') is-invalid @enderror" 
                                            value="{{ old('amount_paid', $order->order_type === 'Retail' ? $remainingAmount : $minimumPayment) }}" 
                                            step="0.01" 
                                            min="{{ $minimumPayment }}" 
                                            max="{{ $remainingAmount }}" 
                                            required>
                                    </div>
                                    <small class="form-text text-muted">
                                        @if($order->order_type === 'Bulk')
                                            Minimum required downpayment: ₱{{ number_format($minimumPayment, 2) }} (30% of total)
                                        @else
                                            Full payment required for retail orders.
                                        @endif
                                    </small>
                                    @error('amount_paid')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="form-group mb-3">
                                    <label for="payment_method">Payment Method</label>
                                    <select name="payment_method" id="payment_method" class="form-control @error('payment_method') is-invalid @enderror" required>
                                        <option value="">Select Payment Method</option>
                                        <option value="Cash" {{ old('payment_method') == 'Cash' ? 'selected' : '' }}>Cash</option>
                                        <option value="GCash" {{ old('payment_method') == 'GCash' ? 'selected' : '' }}>GCash</option>
                                    </select>
                                    @error('payment_method')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div id="gcash_fields" class="payment-method-fields mb-4" style="display: none;">
                                    <div class="card border-primary">
                                        <div class="card-header bg-primary text-white">
                                            <h5 class="mb-0">GCash Payment</h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="text-center mb-3">
                                                @if(isset($hasQRCode) && $hasQRCode)
                                                    <button type="button" id="show-qr-code" class="btn btn-outline-primary mb-2">
                                                        <i class="fas fa-qrcode me-2"></i> Show QR Code for Payment
                                                    </button>
                                                    
                                                    <div id="qr-code-container" class="p-3 border rounded bg-light" style="display: none;">
                                                        <h6>Scan this QR with GCash app</h6>
                                                        <img src="{{ $qrCodeBase64 ?? '' }}" alt="GCash QR Code" class="img-fluid mb-2">
                                                        <p class="text-muted small mb-0">Amount: ₱{{ number_format($remainingAmount, 2) }}</p>
                                                        <p class="text-muted small mb-0">Reference: {{ $paymentReference }}</p>
                                                        <div class="alert alert-info mt-2 small">
                                                            <i class="fas fa-info-circle me-1"></i> After payment, enter the reference number below
                                                        </div>
                                                    </div>
                                                @else
                                                    <div class="p-3 border rounded bg-light">
                                                        <h6>GCash Payment Reference</h6>
                                                        <div class="alert alert-info">
                                                            <strong>Please use this reference when making your GCash payment:</strong>
                                                            <div class="p-2 mt-2 border bg-white text-center">
                                                                <h5 class="mb-0">{{ $paymentReference }}</h5>
                                                            </div>
                                                        </div>
                                                        <p class="text-muted small mb-0">Amount: ₱{{ number_format($remainingAmount, 2) }}</p>
                                                        <p class="text-muted small">After payment, enter the GCash reference number below</p>
                                                    </div>
                                                @endif
                                            </div>
                                            
                                            <div class="form-group">
                                                <label for="reference_number">GCash Reference Number</label>
                                                <div class="input-group">
                                                    <input type="text" 
                                                        name="reference_number" 
                                                        id="reference_number" 
                                                        class="form-control @error('reference_number') is-invalid @enderror"
                                                        value="{{ old('reference_number') }}"
                                                        placeholder="Enter your GCash reference number">
                                                    <button type="button" id="verify-gcash" class="btn btn-secondary">
                                                        <i class="fas fa-check-circle"></i> Verify
                                                    </button>
                                                </div>
                                                <div id="verification-status" class="mt-1 small"></div>
                                                @error('reference_number')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            
                                            <!-- For testing purposes only -->
                                            <div class="mt-3">
                                                <button type="button" id="generate-test-ref" class="btn btn-outline-secondary btn-sm">
                                                    <i class="fas fa-random me-1"></i> Generate Test Reference
                                                </button>
                                                <small class="form-text text-muted">For demo purposes only</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div id="credit_card_fields" class="payment-method-fields mb-4" style="display: none;">
                                    <!-- Credit card inputs would go here -->
                                    <div class="form-group">
                                        <label for="reference_number">Card Transaction Reference</label>
                                        <input type="text" 
                                            name="reference_number" 
                                            class="form-control @error('reference_number') is-invalid @enderror"
                                            value="{{ old('reference_number') }}"
                                            placeholder="Enter transaction reference">
                                        @error('reference_number')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="text-center mt-4">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-credit-card mr-1"></i> Process Payment
                                    </button>
                                    <a href="{{ route('customer.orders.show', $order->order_id) }}" class="btn btn-secondary">
                                        <i class="fas fa-times-circle mr-1"></i> Cancel
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            
            <h5 class="mt-4 mb-3">Order Items</h5>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Unit Price</th>
                            <th>Quantity</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->orderDetails as $detail)
                            <tr>
                                <td>{{ $detail->product->name }}</td>
                                <td>₱{{ number_format($detail->subtotal / $detail->quantity, 2) }}</td>
                                <td>{{ $detail->quantity }} {{ $detail->product->unit }}(s)</td>
                                <td>₱{{ number_format($detail->subtotal, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        // Toggle payment method fields
        $('#payment_method').on('change', function() {
            const method = $(this).val();
            $('.payment-method-fields').hide();
            
            if (method === 'GCash') {
                $('#gcash_fields').show();
            } else if (method === 'credit_card') {
                $('#credit_card_fields').show();
            }
        });
        
        // Show the fields for the previously selected method (if page reloads after error)
        if ($('#payment_method').val()) {
            $('#payment_method').trigger('change');
        }
        
        // Only show QR code toggle button if the QR code is available
        @if(isset($hasQRCode) && $hasQRCode)
        // Toggle QR code display
        $('#show-qr-code').on('click', function() {
            $('#qr-code-container').slideToggle();
        });
        @endif
        
        // Generate test reference number
        $('#generate-test-ref').on('click', function() {
            const refNum = Math.floor(Math.random() * 9000000000000) + 1000000000000;
            $('#reference_number').val(refNum);
        });
        
        // Verify GCash payment
        $('#verify-gcash').on('click', function() {
            const refNumber = $('#reference_number').val();
            if (!refNumber) {
                $('#verification-status').html('<span class="text-danger">Please enter a reference number</span>');
                return;
            }
            
            $('#verification-status').html('<span class="text-muted"><i class="fas fa-spinner fa-spin"></i> Verifying...</span>');
            
            $.ajax({
                url: '{{ route("payments.verify-gcash") }}',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    reference_number: refNumber
                },
                success: function(response) {
                    if (response.success) {
                        $('#verification-status')
                            .html('<span class="text-success"><i class="fas fa-check-circle"></i> ' + response.message + '</span>');
                    } else {
                        $('#verification-status')
                            .html('<span class="text-danger"><i class="fas fa-times-circle"></i> ' + response.message + '</span>');
                    }
                },
                error: function() {
                    $('#verification-status')
                        .html('<span class="text-danger"><i class="fas fa-times-circle"></i> Verification failed</span>');
                }
            });
        });
    });
</script>
@endpush
@endsection
