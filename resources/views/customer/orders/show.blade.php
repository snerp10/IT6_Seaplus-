@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row mb-3">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <h1 class="h3 mb-0">Order #{{ $order->order_id }}</h1>
            <div>
                @if($order->order_status === 'Pending')
                    <form action="{{ route('customer.orders.destroy', $order->order_id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to cancel this order?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-times-circle"></i> Cancel Order
                        </button>
                    </form>
                @endif
                <a href="{{ route('customer.orders.index') }}" class="btn btn-secondary ms-2">
                    <i class="fas fa-arrow-left"></i> Back to Orders
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <!-- Order Details Card -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h5 class="mb-0 font-weight-bold text-primary">Order Details</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Order Date:</strong> {{ $order->created_at->format('M d, Y h:i A') }}</p>
                            <p><strong>Order Type:</strong> {{ $order->order_type }}</p>
                            <p>
                                <strong>Status:</strong>
                                @if($order->order_status == 'Completed')
                                    <span class="badge bg-success">{{ $order->order_status }}</span>
                                @elseif($order->order_status == 'Processing')
                                    <span class="badge bg-warning">{{ $order->order_status }}</span>
                                @elseif($order->order_status == 'Cancelled')
                                    <span class="badge bg-danger">{{ $order->order_status }}</span>
                                @else
                                    <span class="badge bg-secondary">{{ $order->order_status }}</span>
                                @endif
                            </p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Payment Method:</strong> {{ $order->payments->first() ? $order->payments->first()->pay_method : 'Not specified' }}</p>
                            <p><strong>Total Amount:</strong> ₱{{ number_format($order->total_amount, 2) }}</p>
                            
                            @if($order->payments->first() && $order->payments->first()->pay_method == 'Cash on Delivery')
                            <div class="alert alert-info mt-2 mb-0">
                                <small><i class="fas fa-info-circle"></i> Cash on Delivery order. <a href="{{ route('customer.invoices.show', $order->order_id) }}">View invoice</a> for details.</small>
                            </div>
                            @endif
                            
                            <p>
                                <strong>Payment Status:</strong>
                                @php
                                    $totalPaid = $order->payments->where('pay_status', 'Paid')->sum('amount_paid');
                                    $remainingAmount = $order->total_amount - $totalPaid;
                                @endphp
                                
                                @if($remainingAmount <= 0)
                                    <span class="badge bg-success">Paid</span>
                                @elseif($totalPaid > 0)
                                    <span class="badge bg-warning">Partially Paid</span>
                                @else
                                    <span class="badge bg-danger">Unpaid</span>
                                @endif
                            </p>
                        </div>
                    </div>

                    <h6 class="mt-4">Ordered Items</h6>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Price</th>
                                    <th>Quantity</th>
                                    <th>Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($order->orderDetails as $detail)
                                <tr>
                                    <td>{{ $detail->product->name }}</td>
                                    <td>₱{{ number_format($detail->subtotal / $detail->quantity, 2) }}</td>
                                    <td>{{ $detail->quantity }} {{ $detail->product->unit }}(s)</td>
                                    <td>₱{{ number_format($detail->subtotal, 2) }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center">No items found</td>
                                </tr>
                                @endforelse
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="3" class="text-end">Total:</th>
                                    <th>₱{{ number_format($order->total_amount, 2) }}</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    @if($remainingAmount > 0 && $order->order_status !== 'Cancelled')
                    <div class="mt-3 text-center">
                        <a href="{{ route('customer.orders.payment', $order->order_id) }}" class="btn btn-success">
                            <i class="fas fa-credit-card"></i> Make Payment
                        </a>
                    </div>
                    @endif
                </div>
            </div>
            
            <!-- Payments History -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h5 class="mb-0 font-weight-bold text-primary">Payment History</h5>
                </div>
                <div class="card-body">
                    @if($order->payments->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Amount</th>
                                    <th>Method</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->payments as $payment)
                                <tr>
                                    <td>{{ $payment->created_at->format('M d, Y') }}</td>
                                    <td>₱{{ number_format($payment->amount_paid, 2) }}</td>
                                    <td>{{ $payment->pay_method }}</td>
                                    <td>
                                        @if($payment->pay_status == 'Paid')
                                            <span class="badge bg-success">{{ $payment->pay_status }}</span>
                                        @elseif($payment->pay_status == 'Unpaid' || $payment->pay_method == 'Cash on Delivery')
                                            <span class="badge bg-danger">{{ $payment->pay_status }}</span>
                                        @else
                                            <span class="badge bg-secondary">{{ $payment->pay_status }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($payment->pay_status == 'Paid')
                                        <a href="{{ route('customer.invoices.show', $order->order_id) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-file-invoice"></i> Invoice
                                        </a>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <p class="text-center">No payments recorded yet.</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <!-- Delivery Information -->
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 font-weight-bold text-primary">Delivery Information</h5>
                    @if($order->order_status !== 'Cancelled' && $order->order_status !== 'Completed')
                    <a href="{{ route('customer.delivery.edit', $order->order_id) }}" class="btn btn-sm btn-primary">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                    @endif
                </div>
                <div class="card-body">
                    @if($order->delivery)
                        <p>
                            <strong>Status:</strong>
                            <span class="badge bg-info">{{ $order->delivery->delivery_status }}</span>
                        </p>
                        <p><strong>Delivery Date:</strong> {{ $order->delivery->delivery_date ? $order->delivery->delivery_date->format('M d, Y') : 'Not scheduled' }}</p>
                        
                        @if($order->delivery->street)
                            <p><strong>Address:</strong><br>
                            {{ $order->delivery->street }}<br>
                            {{ $order->delivery->city }}, {{ $order->delivery->province }}</p>
                        @else
                            <p>No delivery address provided.</p>
                        @endif
                        
                        @if($order->delivery->special_instructions)
                            <p><strong>Special Instructions:</strong><br>
                            {{ $order->delivery->special_instructions }}</p>
                        @endif
                    @else
                        <p class="text-center">No delivery information available.</p>
                        @if($order->order_status !== 'Cancelled')
                            <div class="text-center mt-3">
                                <a href="{{ route('customer.delivery.edit', $order->order_id) }}" class="btn btn-primary">
                                    <i class="fas fa-truck"></i> Set Delivery Details
                                </a>
                            </div>
                        @endif
                    @endif
                </div>
            </div>
            
            <!-- Need Help Card -->
            <div class="card shadow">
                <div class="card-header py-3">
                    <h5 class="mb-0 font-weight-bold text-primary">Need Help?</h5>
                </div>
                <div class="card-body">
                    <p>If you have any questions about your order, feel free to contact our customer service.</p>
                    <div class="text-center">
                        <a href="{{ url('/contact') }}" class="btn btn-secondary">
                            <i class="fas fa-headset"></i> Contact Us
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
