@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h3 class="mb-0">Charge Invoice #{{ $payment->invoice_number }}</h3>
        </div>
        <div class="card-body">
            <div class="row mb-4">
                <div class="col-md-6">
                    <h5 class="mb-3">Order Details</h5>
                    <p><strong>Order ID:</strong> #{{ $order->order_id }}</p>
                    <p><strong>Order Type:</strong> {{ $order->order_type }}</p>
                    <p><strong>Order Date:</strong> {{ $order->order_date }}</p>
                    <p><strong>Payment Method:</strong> {{ $payment->payment_method }}</p>
                </div>
                <div class="col-md-6 text-end">
                    <h5 class="mb-3">Payment Summary</h5>
                    <p><strong>Total Amount:</strong> ₱{{ number_format($order->total_amount, 2) }}</p>
                    <p><strong>Amount Paid:</strong> ₱{{ number_format($payment->amount_paid, 2) }}</p>
                    <p><strong>Outstanding Balance:</strong> ₱{{ number_format($payment->outstanding_balance, 2) }}</p>
                    <p><strong>Payment Status:</strong> 
                        <span class="badge bg-{{ $order->payment_status === 'Paid' ? 'success' : 'warning' }}">
                            {{ $order->payment_status }}
                        </span>
                    </p>
                </div>
            </div>

            @if($order->order_type === 'Bulk' && $payment->outstanding_balance > 0)
                <div class="alert alert-info">
                    <h5>Bulk Order Payment Terms</h5>
                    <p>Required Downpayment (30%): ₱{{ number_format($order->total_amount * 0.30, 2) }}</p>
                    <p>Remaining Balance: ₱{{ number_format($payment->outstanding_balance, 2) }}</p>
                </div>
            @endif

            <div class="text-center mt-4">
                <a href="{{ route('orders.show', $order->order_id) }}" class="btn btn-primary">Back to Order</a>
                <button onclick="window.print()" class="btn btn-secondary">Print Invoice</button>
            </div>
        </div>
    </div>
</div>
@endsection