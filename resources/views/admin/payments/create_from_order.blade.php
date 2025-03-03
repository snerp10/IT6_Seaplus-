@extends('layouts.admin')

@section('title', 'Record Payment for Order')

@section('admin.content')
<div class="container-fluid">
    <!-- Content Header -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>
                        <i class="fas fa-money-check-alt mr-2"></i>Record Payment for Order #{{ $order->order_id }}
                    </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.orders.index') }}">Orders</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.orders.show', $order->order_id) }}">Order #{{ $order->order_number }}</a></li>
                        <li class="breadcrumb-item active">Add Payment</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <div class="row">
        <div class="col-12 mb-3">
            <div class="float-left">
                <a href="{{ route('admin.orders.show', $order->order_id) }}" class="btn btn-default">
                    <i class="fas fa-arrow-left mr-1"></i> Back to Order
                </a>
            </div>
        </div>
    </div>

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
                            <td>#{{ $order->order_number }}</td>
                        </tr>
                        <tr>
                            <th>Customer</th>
                            <td>{{ $order->customer->cus_name }}</td>
                        </tr>
                        <tr>
                            <th>Subtotal</th>
                            <td>₱{{ number_format($order->total_amount, 2) }}</td>
                        </tr>
                        @if($order->delivery)
                        <tr>
                            <th>Delivery Cost</th>
                            <td>₱{{ number_format($order->delivery->delivery_cost, 2) }}</td>
                        </tr>
                        @endif
                        <tr>
                            <th>Total Amount</th>
                            <td class="font-weight-bold">₱{{ number_format(isset($totalOrderAmount) ? $totalOrderAmount : ($order->total_amount + ($order->delivery ? $order->delivery->delivery_cost : 0)), 2) }}</td>
                        </tr>
                        <tr>
                            <th>Payment Status</th>
                            <td>
                                <span class="badge badge-{{ 
                                    $order->payment_status == 'paid' ? 'success' : 
                                    ($order->payment_status == 'partial' ? 'warning' : 'danger') 
                                }}">
                                    {{ ucfirst($order->payment_status) }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th>Paid Amount</th>
                            <td>₱{{ number_format($order->payments()->where('pay_status', 'Paid')->sum('amount_paid'), 2) }}</td>
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
                        <input type="hidden" name="order_id" value="{{ $order->order_id }}">
                        <input type="hidden" name="redirect_to_order" value="1">
                        <input type="hidden" name="cus_id" value="{{ $order->cus_id }}">
                        
                        @if($existingPayment && $order->pay_status != 'Partially Paid')
                            <input type="hidden" name="payment_id" value="{{ $existingPayment->pay_id }}">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i> You are updating an existing payment record for this order.
                                @if($existingPayment->amount_paid > 0)
                                    <br>Previous amount paid: ₱{{ number_format($existingPayment->amount_paid, 2) }}
                                @endif
                                @if($existingPayment->pay_status != 'Paid')
                                    <br>Previous status: {{ ucfirst($existingPayment->pay_status) }}
                                @endif
                            </div>
                        @elseif($order->pay_status == 'Partially Paid')
                            <div class="alert alert-warning">
                                <i class="fas fa-info-circle"></i> This order has a previous payment. You are recording a balance payment for the remaining amount.
                                <br>Previously paid: ₱{{ number_format($order->payments()->where('pay_status', 'Paid')->sum('amount_paid'), 2) }}
                                <br>Remaining amount: ₱{{ number_format($remainingAmount, 2) }}
                            </div>
                        @endif

                        <!-- Amount outstanding card -->
                        <div class="card bg-light mb-3">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h5>Order Total:</h5>
                                        <h3 class="text-primary">₱{{ number_format(isset($totalOrderAmount) ? $totalOrderAmount : ($order->total_amount + ($order->delivery ? $order->delivery->delivery_cost : 0)), 2) }}</h3>
                                    </div>
                                    <div class="col-md-6 text-right">
                                        <h5>Amount Remaining:</h5>
                                        <h3 class="text-{{ $remainingAmount > 0 ? 'danger' : 'success' }}">
                                            ₱{{ number_format($remainingAmount, 2) }}
                                        </h3>
                                    </div>
                                </div>
                            </div>
                        </div>

                        @if($order->order_type == 'Bulk')
                        <div class="form-group">
                            <label>Payment Type</label>
                            @if($order->pay_status == 'Partially Paid')
                                <!-- If partially paid, this is a balance payment -->
                                <input type="hidden" name="payment_type" value="balance">
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle"></i> This is a balance payment for a bulk order where a down payment was previously made.
                                </div>
                            @else
                                <!-- If not partially paid, allow choice of down payment or full payment -->
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
                                    (₱{{ number_format($order->total_amount * 0.3, 2) }})
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
                                       value="{{ old('amount_paid', $remainingAmount ?? $order->total_amount) }}" 
                                       step="0.01" min="0" required>
                                @error('amount_paid')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <small class="form-text text-muted">
                                Enter the amount being paid for this order.
                            </small>
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

                        <div class="form-group text-center mt-4">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-save mr-1"></i> {{ isset($existingPayment) ? 'Update' : 'Record' }} Payment
                            </button>
                            <a href="{{ route('admin.orders.show', $order->order_id) }}" class="btn btn-secondary btn-lg">
                                <i class="fas fa-times mr-1"></i> Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    $(function() {
        // Auto-calculate payment status when amount changes
        function updatePaymentStatus() {
            $('#pay_status').val('Paid');
        }
        
        // Initialize payment status on page load
        updatePaymentStatus();
        
        // Update payment status when amount changes
        $('#amount_paid').on('change', updatePaymentStatus);
        
        // Add a clear indication of what's happening with the payment
        const totalAmount = {{ isset($totalOrderAmount) ? $totalOrderAmount : ($order->total_amount + ($order->delivery ? $order->delivery->delivery_cost : 0)) }};
        const currentPaid = {{ $order->payments()->where('pay_status', 'Paid')->sum('amount_paid') }};
        const remainingAmount = {{ $remainingAmount }};
        
        console.log(`Total order amount (including delivery): ${totalAmount}`);
        console.log(`Already paid amount: ${currentPaid}`);
        console.log(`Remaining to be paid: ${remainingAmount}`);
        
        // Handle down payment calculation for bulk orders
        @if($order->order_type == 'Bulk')
        $('input[name="payment_type"]').on('change', function() {
            const downPaymentAmount = Math.round((totalAmount * 0.3) * 100) / 100;
            
            if ($(this).val() === 'down') {
                // Show down payment info
                $('#downPaymentInfo').show();
                
                @if($order->pay_status == 'Partially Paid')
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
        
        @if($order->pay_status == 'Partially Paid')
        // For balance payments, always set to the remaining amount
        $('#amount_paid').val(remainingAmount);
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
