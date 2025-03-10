@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="card shadow">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h4 class="mb-0">Invoice #{{ $payment->invoice_number }}</h4>
            <div>
                <a href="{{ route('customer.invoices.download', $order->order_id) }}" class="btn btn-light">
                    <i class="fas fa-download"></i> Download
                </a>
                <a href="{{ route('customer.orders.show', $order->order_id) }}" class="btn btn-light ms-2">
                    <i class="fas fa-eye"></i> View Order
                </a>
            </div>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            
            <div class="row mb-4">
                <div class="col-md-6">
                    <h5 class="mb-2">Billing Information</h5>
                    <address>
                        <strong>{{ $order->customer->fname }} {{ $order->customer->lname }}</strong><br>
                        {{ $order->customer->address }}<br>
                        {{ $order->customer->city }}, {{ $order->customer->province }}<br>
                        <abbr title="Phone">P:</abbr> {{ $order->customer->phone }}
                    </address>
                </div>
                <div class="col-md-6 text-md-end">
                    <h5 class="mb-2">Invoice Details</h5>
                    <p>
                        <strong>Invoice Number:</strong> {{ $payment->invoice_number }}<br>
                        <strong>Date:</strong> {{ $payment->created_at->format('M d, Y') }}<br>
                        <strong>Order ID:</strong> {{ $order->order_id }}<br>
                        <strong>Payment Method:</strong> 
                        @if($payment->pay_method === 'Cash on Delivery')
                            <span class="badge bg-warning">Cash on Delivery</span>
                        @else
                            {{ $payment->pay_method }}
                        @endif
                    </p>
                </div>
            </div>
            
            <div class="row mb-4">
                <div class="col-md-12">
                    <h5 class="mb-3">Delivery Information</h5>
                    <p>
                        <strong>Delivery Address:</strong> 
                        {{ $order->delivery->street }}, {{ $order->delivery->city }}, {{ $order->delivery->province }}<br>
                        <strong>Scheduled Date:</strong> {{ $order->delivery->delivery_date ? $order->delivery->delivery_date->format('M d, Y') : '' }}<br>
                        @if($order->delivery->special_instructions)
                            <strong>Special Instructions:</strong> {{ $order->delivery->special_instructions }}
                        @endif
                    </p>
                </div>
            </div>
            
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Unit Price</th>
                            <th>Quantity</th>
                            <th class="text-end">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->orderDetails as $detail)
                        <tr>
                            <td>{{ $detail->product->name }}</td>
                            <td>₱{{ number_format($detail->subtotal / $detail->quantity, 2) }}</td>
                            <td>{{ $detail->quantity }} {{ $detail->product->unit }}(s)</td>
                            <td class="text-end">₱{{ number_format($detail->subtotal, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3" class="text-end"><strong>Subtotal:</strong></td>
                            <td class="text-end">₱{{ number_format($order->orderDetails->sum('subtotal'), 2) }}</td>
                        </tr>
                        <tr>
                            <td colspan="3" class="text-end"><strong>Delivery Fee:</strong></td>
                            <td class="text-end">₱{{ number_format($order->delivery->delivery_cost, 2) }}</td>
                        </tr>
                        <tr>
                            <td colspan="3" class="text-end"><strong>Total:</strong></td>
                            <td class="text-end">₱{{ number_format($order->total_amount, 2) }}</td>
                        </tr>
                        @if($payment->pay_method === 'GCash')
                        <tr>
                            <td colspan="3" class="text-end"><strong>Paid Amount:</strong></td>
                            <td class="text-end">₱{{ number_format($payment->amount_paid, 2) }}</td>
                        </tr>
                        @if($payment->outstanding_balance > 0)
                        <tr>
                            <td colspan="3" class="text-end"><strong>Balance Due:</strong></td>
                            <td class="text-end">₱{{ number_format($payment->outstanding_balance, 2) }}</td>
                        </tr>
                        @endif
                        @elseif($payment->pay_method === 'COD')
                        <tr>
                            <td colspan="3" class="text-end"><strong>Amount Due (COD):</strong></td>
                            <td class="text-end">₱{{ number_format($payment->outstanding_balance, 2) }}</td>
                        </tr>
                        @endif
                    </tfoot>
                </table>
            </div>
            
            <div class="row mt-4">
                <div class="col-md-12">
                    @if($payment->pay_method === 'Cash on Delivery')
                        <div class="alert alert-info">
                            <h5><i class="fas fa-info-circle me-2"></i> Cash on Delivery</h5>
                            <p class="mb-0">Please prepare the exact amount of ₱{{ number_format($payment->outstanding_balance, 2) }} upon delivery. Our delivery personnel will collect the payment.</p>
                        </div>
                    @endif
                    
                    @if($payment->pay_method === 'GCash' && $payment->pay_status === 'Paid')
                        <div class="alert alert-success">
                            <h5><i class="fas fa-check-circle me-2"></i> Payment Completed</h5>
                            <p class="mb-0">Your payment has been received. Thank you for your order!</p>
                            @if($payment->reference_number)
                                <p class="mb-0 mt-1"><strong>Reference Number:</strong> {{ $payment->reference_number }}</p>
                            @endif
                        </div>
                    @endif
                    
                    @if($payment->pay_method === 'GCash' && $payment->pay_status !== 'Paid' && $order->order_type === 'Bulk')
                        <div class="alert alert-warning">
                            <h5><i class="fas fa-exclamation-triangle me-2"></i> Partial Payment</h5>
                            <p class="mb-0">You have made a down payment of ₱{{ number_format($payment->amount_paid, 2) }}. The remaining balance of ₱{{ number_format($payment->outstanding_balance, 2) }} will be collected upon delivery.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="card-footer">
            <div class="text-center">
                <p class="mb-0">Thank you for your business!</p>
            </div>
        </div>
    </div>
</div>
@endsection