@extends('layouts.admin')

@section('title', 'Record Payment')

@section('admin.content')
<div class="container-fluid">
    <!-- Content Header -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><i class="fas fa-check-circle mr-2"></i>Record Payment</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.payments.index') }}">Payments</a></li>
                        <li class="breadcrumb-item active">Record Payment</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-12 mb-3">
                <a href="{{ route('admin.payments.index') }}" class="btn btn-default">
                    <i class="fas fa-arrow-left"></i> Back to Payments
                </a>
            </div>
            
            <div class="col-md-12">
                <div class="card card-primary card-outline">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-search mr-1"></i>
                            Find Order to Process Payment
                        </h3>
                    </div>
                    <div class="card-body">
                        @if(!isset($selectedOrder))
                            <form action="{{ route('admin.payments.create') }}" method="GET">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="order_id">Select Order</label>
                                            <select name="order_id" id="order_id" class="form-control select2" required>
                                                <option value="">Select an Order</option>
                                                @foreach($orders as $order)
                                                    <option value="{{ $order->order_id }}">
                                                        #{{ $order->order_id }} - {{ $order->customer->fname }} (₱{{ number_format($order->total_amount, 2) }}) - {{ ucfirst($order->pay_status) }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4 d-flex align-items-end">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-search mr-1"></i> Find Order
                                        </button>
                                    </div>
                                </div>
                            </form>
                        @else
                            <!-- Show order details and payment form if an order is selected -->
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="card card-info card-outline">
                                        <div class="card-header">
                                            <h3 class="card-title">
                                                <i class="fas fa-shopping-cart mr-1"></i>
                                                Order Information
                                            </h3>
                                        </div>
                                        <div class="card-body p-0">
                                            <table class="table table-striped">
                                                <tr>
                                                    <th>Order Number</th>
                                                    <td>#{{ $selectedOrder->order_id }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Customer</th>
                                                    <td>{{ $selectedOrder->customer->fname }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Subtotal</th>
                                                    <td>₱{{ number_format($selectedOrder->total_amount, 2) }}</td>
                                                </tr>
                                                @if($selectedOrder->delivery)
                                                <tr>
                                                    <th>Delivery Cost</th>
                                                    <td>₱{{ number_format($selectedOrder->delivery->delivery_cost, 2) }}</td>
                                                </tr>
                                                @endif
                                                <tr>
                                                    <th>Total Amount</th>
                                                    <td class="font-weight-bold">₱{{ number_format($selectedOrder->total_amount + ($selectedOrder->delivery ? $selectedOrder->delivery->delivery_cost : 0), 2) }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Order Status</th>
                                                    <td>
                                                        <span class="badge badge-{{ 
                                                            $selectedOrder->order_status == 'Completed' ? 'success' : 
                                                            ($selectedOrder->order_status == 'Processing' ? 'warning' : 'danger') 
                                                        }}">
                                                            {{ ucfirst($selectedOrder->order_status) }}
                                                        </span>
                                                    </td>
                                                </tr>
                                                @php
                                                    $totalPaid = $selectedOrder->payments()->where('pay_status', 'Paid')->sum('amount_paid');
                                                    $totalOrderAmount = $selectedOrder->total_amount + ($selectedOrder->delivery ? $selectedOrder->delivery->delivery_cost : 0);
                                                    $remainingAmount = max(0, $totalOrderAmount - $totalPaid);
                                                @endphp
                                                <tr>
                                                    <th>Paid Amount</th>
                                                    <td>₱{{ number_format($totalPaid, 2) }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Outstanding</th>
                                                    <td class="text-danger">
                                                        ₱{{ number_format($remainingAmount, 2) }}
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-8">
                                    <div class="card card-primary card-outline">
                                        <div class="card-header">
                                            <h3 class="card-title">
                                                <i class="fas fa-money-bill-wave mr-1"></i>
                                                {{ $existingPayment ? 'Update Payment' : 'Record Payment' }}
                                            </h3>
                                        </div>
                                        <div class="card-body">
                                            <form action="{{ route('admin.payments.store') }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="order_id" value="{{ $selectedOrder->order_id }}">
                                                <input type="hidden" name="cus_id" value="{{ $selectedOrder->cus_id }}">
                                                
                                                @if($existingPayment)
                                                    <input type="hidden" name="existing_payment_id" value="{{ $existingPayment->pay_id }}">
                                                    <div class="alert alert-info">
                                                        <i class="fas fa-info-circle"></i> You are updating an existing payment record for this order.
                                                        @if($existingPayment->pay_status != 'Paid')
                                                            <br>Previous status: {{ ucfirst($existingPayment->pay_status) }}
                                                        @endif
                                                        @if($existingPayment->amount_paid > 0)
                                                            <br>Previous amount paid: ₱{{ number_format($existingPayment->amount_paid, 2) }}
                                                        @endif
                                                    </div>
                                                @elseif($selectedOrder->order_status == 'Processing')
                                                    <div class="alert alert-warning">
                                                        <i class="fas fa-info-circle"></i> This order has a previous payment. You are recording a balance payment for the remaining amount.
                                                        <br>Previously paid: ₱{{ number_format($totalPaid, 2) }}
                                                        <br>Remaining amount: ₱{{ number_format($remainingAmount, 2) }}
                                                    </div>
                                                @endif
                                                
                                                @if($selectedOrder && $selectedOrder->order_type == 'Bulk')
                                                <div class="form-group">
                                                    <label>Payment Type</label>
                                                    @if($selectedOrder->order_status == 'Processing')
                                                        <!-- If processing, this is a balance payment -->
                                                        <input type="hidden" name="payment_type" value="balance">
                                                        <div class="alert alert-info">
                                                            <i class="fas fa-info-circle"></i> This is a balance payment for a bulk order where a down payment was previously made.
                                                        </div>
                                                    @else
                                                        <!-- If not processing, allow choice of down payment or full payment -->
                                                        <div class="custom-control custom-radio mb-2">
                                                            <input type="radio" id="payment_type_full" name="payment_type" class="custom-control-input" value="full" checked>
                                                            <label class="custom-control-label" for="payment_type_full">Full Payment</label>
                                                        </div>
                                                        <div class="custom-control custom-radio">
                                                            <input type="radio" id="payment_type_down" name="payment_type" class="custom-control-input" value="down">
                                                            <label class="custom-control-label" for="payment_type_down">Down Payment</label>
                                                        </div>
                                                        <div class="alert alert-info mt-2" id="downPaymentInfo" style="display: none;">
                                                            <i class="fas fa-info-circle"></i> Bulk orders require a minimum down payment of 30% 
                                                            (₱{{ number_format($selectedOrder->total_amount * 0.3, 2) }})
                                                        </div>
                                                    @endif
                                                </div>
                                                @endif

                                                <div class="form-group">
                                                    <label for="amount_paid">Payment Amount</label>
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">₱</span>
                                                        </div>
                                                        <input type="number" name="amount_paid" id="amount_paid" class="form-control @error('amount_paid') is-invalid @enderror" 
                                                            value="{{ old('amount_paid', $existingPayment ? $existingPayment->amount_paid : $remainingAmount) }}" 
                                                            step="0.01" min="0" required>
                                                        @error('amount_paid')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                    <label for="pay_method">Payment Method</label>
                                                    <input type="hidden" name="pay_method" value="{{ old('pay_method', isset($existingPayment) ? $existingPayment->pay_method : ($defaultPaymentMethod ?? 'cash')) }}">
                                                    <input type="text" class="form-control" value="{{ ucfirst(old('pay_method', isset($existingPayment) ? $existingPayment->pay_method : ($defaultPaymentMethod ?? 'cash'))) }}" readonly>
                                                    <small class="form-text text-muted">Payment method is carried over from order details</small>
                                                </div>

                                                <div class="form-group">
                                                    <label for="reference_number">Reference Number</label>
                                                    <div class="input-group">
                                                        <input type="text" name="reference_number" id="reference_number" 
                                                               class="form-control @error('reference_number') is-invalid @enderror" 
                                                               value="{{ old('reference_number', isset($existingPayment) ? $existingPayment->reference_number : ('REF-' . date('Ymd') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT))) }}" 
                                                               readonly>
                                                        <div class="input-group-append">
                                                            <button type="button" class="btn btn-outline-secondary" onclick="generateReference()">
                                                                <i class="fas fa-sync"></i> Generate New
                                                            </button>
                                                        </div>
                                                    </div>
                                                    @error('reference_number')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>

                                                <div class="form-group">
                                                    <label for="pay_date">Payment Date</label>
                                                    <input type="date" name="pay_date" id="pay_date" 
                                                        class="form-control @error('pay_date') is-invalid @enderror" 
                                                        value="{{ old('pay_date', isset($existingPayment) ? \Carbon\Carbon::parse($existingPayment->pay_date)->format('Y-m-d') : date('Y-m-d')) }}" required>
                                                    @error('pay_date')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>

                                                <!-- Hidden payment status field - will be auto-determined -->
                                                <input type="hidden" name="pay_status" id="pay_status" value="">

                                                <div class="form-group">
                                                    <label for="notes">Notes</label>
                                                    <textarea name="notes" id="notes" class="form-control @error('notes') is-invalid @enderror" rows="4">{{ old('notes', isset($existingPayment) ? $existingPayment->notes : '') }}</textarea>
                                                    @error('notes')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>

                                                <div class="form-group text-center mt-4">
                                                    <button type="submit" class="btn btn-primary btn-lg">
                                                        <i class="fas fa-save mr-1"></i> {{ isset($existingPayment) ? 'Update' : 'Record' }} Payment
                                                    </button>
                                                    <a href="{{ route('admin.payments.index') }}" class="btn btn-default btn-lg ml-2">
                                                        <i class="fas fa-times-circle mr-1"></i> Cancel
                                                    </a>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

@push('scripts')
<script>
    $(function() {
        // Initialize select2 for better dropdown experience
        $('.select2').select2({
            theme: 'bootstrap4'
        });
        
        // Auto-calculate payment status when amount changes
        function updatePaymentStatus() {
            // For payments, we always mark the payment itself as "Paid" when it's processed
            $('#pay_status').val('Paid');
        }
        
        // Initialize payment status on page load
        updatePaymentStatus();
        
        // Update payment status when amount changes
        $('#amount_paid').on('change', updatePaymentStatus);
        
        // Handle down payment calculation for bulk orders
        @if($selectedOrder && $selectedOrder->order_type == 'Bulk')
        $('input[name="payment_type"]').on('change', function() {
            const totalAmount = {{ $selectedOrder->total_amount + ($selectedOrder->delivery ? $selectedOrder->delivery->delivery_cost : 0) }};
            const remainingAmount = {{ $remainingAmount }};
            const downPaymentAmount = Math.round((totalAmount * 0.3) * 100) / 100;
            
            if ($(this).val() === 'down') {
                // Show down payment info
                $('#downPaymentInfo').show();
                
                @if($selectedOrder->order_status == 'Processing')
                    // This is already a balance payment, so set to remaining amount
                    $('#amount_paid').val(remainingAmount);
                @else
                    // For down payment logic:
                    // If remaining amount is the full amount (no payments yet), show 30%
                    // If remaining amount is less than full but more than 30%, show remaining
                    // If remaining amount is less than 30%, show remaining
                    
                    if (remainingAmount >= totalAmount) {
                        // No payments yet, show 30%
                        $('#amount_paid').val(downPaymentAmount);
                    } else if (remainingAmount >= downPaymentAmount) {
                        // Partial payment made, use remaining amount
                        $('#amount_paid').val(remainingAmount);
                    } else {
                        // Less than 30% remaining, use what's left
                        $('#amount_paid').val(remainingAmount);
                    }
                @endif
                
                // Set min attribute appropriately
                const minPayment = Math.min(downPaymentAmount, remainingAmount);
                $('#amount_paid').attr('min', minPayment > 0 ? minPayment : 0.01);
            } else {
                // Hide down payment info
                $('#downPaymentInfo').hide();
                // Full payment - set to remaining amount
                $('#amount_paid').val(remainingAmount);
                // Reset min attribute
                $('#amount_paid').attr('min', 0.01);
            }
        });
        @endif
        
        @if($selectedOrder && $selectedOrder->order_status == 'Processing')
        // For balance payments, always set to the remaining amount
        $('#amount_paid').val({{ $remainingAmount }});
        @endif
    });
    
    function generateReference() {
        const date = new Date();
        const dateString = date.getFullYear() +
            String(date.getMonth() + 1).padStart(2, '0') +
            String(date.getDate()).padStart(2, '0');
        const random = String(Math.floor(Math.random() * 9999)).padStart(4, '0');
        document.getElementById('reference_number').value = 'REF-' + dateString + '-' + random;
    }
</script>
@endpush
@endsection
