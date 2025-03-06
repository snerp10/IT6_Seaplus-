@extends('layouts.admin')

@section('admin.content')
<div class="container-fluid">
    <!-- Content Header -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>
                        <i class="fas fa-edit mr-2"></i>Edit Payment #{{ $payment->pay_id }}
                    </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.payments.index') }}">Payments</a></li>
                        <li class="breadcrumb-item active">Edit</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-12 mb-3">
                <div class="float-left">
                    <a href="{{ route('admin.payments.index') }}" class="btn btn-default">
                        <i class="fas fa-arrow-left mr-1"></i> Back to Payments
                    </a>
                </div>
                <div class="float-right">
                    <a href="{{ route('admin.payments.show', $payment->pay_id) }}" class="btn btn-info">
                        <i class="fas fa-eye mr-1"></i> View Details
                    </a>
                </div>
            </div>
            
            <div class="col-md-12">
                <div class="card card-warning card-outline">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-money-bill-wave mr-1"></i>
                            Payment Information
                        </h3>
                    </div>
                    <div class="card-body">
                        @if($payment->pay_status == 'Paid')
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i> <strong>Warning:</strong> This payment has already been marked as "Paid". 
                            Modifying financial records that have been completed may affect accounting reconciliation. 
                            Any changes will be logged for audit purposes.
                        </div>
                        @endif

                        <form action="{{ route('admin.payments.update', $payment->pay_id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="order_id">Order <span class="text-muted">(Optional)</span></label>
                                        <select name="order_id" id="order_id" class="form-control select2 @error('order_id') is-invalid @enderror" 
                                                {{ $payment->pay_status == 'Paid' ? 'disabled' : '' }}>
                                            <option value="">No Order (Direct Payment)</option>
                                            @foreach($orders as $order)
                                                <option value="{{ $order->order_id }}" {{ (old('order_id', $payment->order_id) == $order->order_id) ? 'selected' : '' }}>
                                                    #{{ $order->order_id }} - {{ number_format($order->total_amount, 2) }} ({{ ucfirst($order->order_status) }})
                                                </option>
                                            @endforeach
                                        </select>
                                        @if($payment->pay_status == 'Paid')
                                            <input type="hidden" name="order_id" value="{{ $payment->order_id }}">
                                            <small class="text-muted">Order association cannot be changed for paid payments.</small>
                                        @endif
                                        @error('order_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="cus_id">Customer</label>
                                        <select name="cus_id" id="cus_id" class="form-control @error('cus_id') is-invalid @enderror" 
                                                {{ $payment->pay_status == 'Paid' ? 'disabled' : '' }} required>
                                            <option value="">Select Customer</option>
                                            @foreach($customers as $customer)
                                                <option value="{{ $customer->cus_id }}" {{ old('cus_id', $payment->cus_id) == $customer->cus_id ? 'selected' : '' }}>
                                                    {{ $customer->name }} ({{ $customer->email }})
                                                </option>
                                            @endforeach
                                        </select>
                                        @if($payment->pay_status == 'Paid')
                                            <input type="hidden" name="cus_id" value="{{ $payment->cus_id }}">
                                            <small class="text-muted">Customer association cannot be changed for paid payments.</small>
                                        @endif
                                        @error('cus_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="amount_paid">Amount</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">₱</span>
                                            </div>
                                            <input type="number" name="amount_paid" id="amount_paid" 
                                                class="form-control @error('amount_paid') is-invalid @enderror" 
                                                value="{{ old('amount_paid', $payment->amount_paid) }}" 
                                                step="0.01" min="0" 
                                                {{ $payment->pay_status == 'Paid' ? 'readonly' : '' }}
                                                required>
                                        </div>
                                        @if($payment->pay_status == 'Paid')
                                            <small class="text-muted">Payment amount cannot be changed for completed transactions.</small>
                                        @endif
                                        @error('amount_paid')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="pay_method">Payment Method</label>
                                        <select name="pay_method" id="pay_method" class="form-control @error('pay_method') is-invalid @enderror" 
                                                {{ $payment->pay_status == 'Paid' ? 'disabled' : '' }} required>
                                            <option value="">Select Method</option>
                                            <option value="cash" {{ old('pay_method', $payment->pay_method) == 'cash' ? 'selected' : '' }}>Cash</option>
                                            <option value="credit_card" {{ old('pay_method', $payment->pay_method) == 'credit_card' ? 'selected' : '' }}>Credit Card</option>
                                            <option value="debit_card" {{ old('pay_method', $payment->pay_method) == 'debit_card' ? 'selected' : '' }}>Debit Card</option>
                                            <option value="bank_transfer" {{ old('pay_method', $payment->pay_method) == 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                                            <option value="gcash" {{ old('pay_method', $payment->pay_method) == 'gcash' ? 'selected' : '' }}>GCash</option>
                                            <option value="paymaya" {{ old('pay_method', $payment->pay_method) == 'paymaya' ? 'selected' : '' }}>PayMaya</option>
                                        </select>
                                        @if($payment->pay_status == 'Paid')
                                            <input type="hidden" name="pay_method" value="{{ $payment->pay_method }}">
                                            <small class="text-muted">Payment method cannot be changed for completed transactions.</small>
                                        @endif
                                        @error('pay_method')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="reference_number">Reference Number</label>
                                        <input type="text" name="reference_number" id="reference_number" 
                                               class="form-control @error('reference_number') is-invalid @enderror" 
                                               value="{{ old('reference_number', $payment->reference_number) }}"
                                               {{ $payment->pay_status == 'Paid' ? 'readonly' : '' }}>
                                        @if($payment->pay_status == 'Paid')
                                            <small class="text-muted">Reference numbers cannot be changed for completed transactions.</small>
                                        @endif
                                        @error('reference_number')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="pay_date">Payment Date</label>
                                        <input type="date" name="pay_date" id="pay_date" 
                                               class="form-control @error('pay_date') is-invalid @enderror" 
                                               value="{{ old('pay_date', $payment->pay_date ? \Carbon\Carbon::parse($payment->pay_date)->format('Y-m-d') : '') }}" 
                                               required>
                                        @error('pay_date')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="text-muted">Payment date can be adjusted for correction purposes only.</small>
                                    </div>

                                    <div class="form-group">
                                        <label for="pay_status">Status</label>
                                        <select name="pay_status" id="pay_status" class="form-control @error('pay_status') is-invalid @enderror" required>
                                            <option value="Unpaid" {{ old('pay_status', $payment->pay_status) == 'Unpaid' ? 'selected' : '' }}>Unpaid</option>
                                            <option value="Partially Paid" {{ old('pay_status', $payment->pay_status) == 'Partially Paid' ? 'selected' : '' }}>Partially Paid</option>
                                            <option value="Paid" {{ old('pay_status', $payment->pay_status) == 'Paid' ? 'selected' : '' }}>Paid</option>
                                            <option value="Refunded" {{ old('pay_status', $payment->pay_status) == 'Refunded' ? 'selected' : '' }}>Refunded</option>
                                            <option value="Failed" {{ old('pay_status', $payment->pay_status) == 'Failed' ? 'selected' : '' }}>Failed</option>
                                        </select>
                                        @error('pay_status')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        @if($payment->pay_status == 'Paid')
                                            <small class="text-warning">Warning: Changing a payment from "Paid" status may affect order balances and accounting.</small>
                                        @endif
                                    </div>

                                    <div class="form-group">
                                        <label for="notes">Notes</label>
                                        <textarea name="notes" id="notes" class="form-control @error('notes') is-invalid @enderror" rows="4">{{ old('notes', $payment->notes) }}</textarea>
                                        @error('notes')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    @if($payment->order && $payment->order->order_type == 'Bulk')
                                    <div class="form-group">
                                        <label>Payment Type</label>
                                        <div class="custom-control custom-radio mb-2">
                                            <input type="radio" id="payment_type_full" name="payment_type" 
                                                   class="custom-control-input" value="full" 
                                                   {{ !strpos($payment->notes ?? '', 'Bulk Order Down Payment') ? 'checked' : '' }}
                                                   {{ $payment->pay_status == 'Paid' ? 'disabled' : '' }}>
                                            <label class="custom-control-label" for="payment_type_full">Full Payment</label>
                                        </div>
                                        <div class="custom-control custom-radio">
                                            <input type="radio" id="payment_type_down" name="payment_type" 
                                                   class="custom-control-input" value="down"
                                                   {{ strpos($payment->notes ?? '', 'Bulk Order Down Payment') !== false ? 'checked' : '' }}
                                                   {{ $payment->pay_status == 'Paid' ? 'disabled' : '' }}>
                                            <label class="custom-control-label" for="payment_type_down">Down Payment</label>
                                        </div>
                                        @if($payment->pay_status == 'Paid')
                                            <input type="hidden" name="payment_type" value="{{ strpos($payment->notes ?? '', 'Bulk Order Down Payment') !== false ? 'down' : 'full' }}">
                                        @endif
                                        <div class="alert alert-info mt-2" id="downPaymentInfo" style="{{ strpos($payment->notes ?? '', 'Bulk Order Down Payment') !== false ? '' : 'display: none;' }}">
                                            <i class="fas fa-info-circle"></i> Bulk orders require a minimum down payment of 30% 
                                            (₱{{ number_format($payment->order->total_amount * 0.3, 2) }})
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group text-center mt-4">
                                <button type="submit" class="btn btn-warning btn-lg">
                                    <i class="fas fa-save mr-1"></i> Update Payment
                                </button>
                                <a href="{{ route('admin.payments.index') }}" class="btn btn-default btn-lg ml-2">
                                    <i class="fas fa-times-circle mr-1"></i> Cancel
                                </a>
                                @if(request()->has('redirect_to_order') && $payment->order_id)
                                    <input type="hidden" name="redirect_to_order" value="1">
                                @endif
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection

@push('scripts')
<script>
    $(function() {
        // Initialize Select2 
        $('.select2').select2({
            theme: 'bootstrap4'
        });
        
        // Payment type toggle for bulk orders
        @if($payment->order && $payment->order->order_type == 'Bulk' && $payment->pay_status != 'Paid')
        $('input[name="payment_type"]').on('change', function() {
            const totalAmount = {{ $payment->order->total_amount }};
            const downPaymentAmount = Math.round((totalAmount * 0.3) * 100) / 100;
            
            if ($(this).val() === 'down') {
                // Show down payment info
                $('#downPaymentInfo').show();
                // If current amount is less than 30%, update it
                if (parseFloat($('#amount_paid').val()) < downPaymentAmount) {
                    $('#amount_paid').val(downPaymentAmount);
                }
                // Set min attribute to prevent amounts below 30%
                $('#amount_paid').attr('min', downPaymentAmount);
            } else {
                // Hide down payment info
                $('#downPaymentInfo').hide();
                // Reset min attribute
                $('#amount_paid').attr('min', 0.01);
            }
        });
        @endif
    });
</script>
@endpush
