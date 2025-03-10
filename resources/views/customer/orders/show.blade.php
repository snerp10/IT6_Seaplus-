@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h2>Order #{{ $order->order_id }}</h2>
                <div>
                    <a href="{{ route('orders.edit', $order->order_id) }}" class="btn btn-warning">Edit Order</a>
                    <form action="{{ route('orders.destroy', $order->order_id) }}" 
                        method="POST" 
                        style="display:inline;"
                        onsubmit="return confirm('Are you sure you want to cancel this order?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger" 
                            @if($order->pay_status === 'Paid') disabled @endif>
                            Cancel Order
                        </button>
                        @if($order->pay_status === 'Paid')
                            <div class="btn-group" role="group">
                                <a href="{{ route('invoices.show', $order->order_id) }}" class="btn btn-info">
                                    <i class="fas fa-file-invoice"></i> View Invoice
                                </a>
                            </div>
                        @endif

                    </form>

                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="row mb-4">
                <div class="col-md-6">
                    <table class="table">
                        <tr>
                            <th>Order Date:</th>
                            <td>{{ $order->order_date }}</td>
                        </tr>
                        <tr>
                            <th>Payment Method:</th>
                            <td>{{ $order->pay_method }}</td>
                        </tr>
                        <tr>
                            <th>Payment Status:</th>
                            <td>
                                <span class="badge bg-{{ $order->pay_status == 'Paid' ? 'success' : 'warning' }}">
                                    {{ $order->pay_status }}
                                </span>
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <table class="table">
                        <tr>
                            <th>Order Type:</th>
                            <td>{{ $order->order_type }}</td>
                        </tr>
                        <tr>
                            <th>Delivery Status:</th>
                            <td>
                                <span class="badge bg-{{ $order->delivery->delivery_status == 'Delivered' ? 'success' : 'info' }}">
                                    {{ $order->delivery->delivery_status }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th>Subtotal:</th>
                            <td>₱{{ number_format($order->orderDetails->sum('subtotal'), 2) }}</td>
                        </tr>
                        <tr>
                            <th>Balance:</th>
                            <td>₱{{ number_format($order->total_amount, 2) }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <h4>Order Items</h4>
            <table class="table">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Category</th>
                        <th>Quantity</th>
                        <th>Unit Price</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->orderDetails as $detail)
                    <tr>
                        <td>{{ $detail->product->name }}</td>
                        <td>{{ $detail->product->category }}</td>
                        <td>{{ $detail->quantity }} {{ $detail->product->unit_of_measurement }}</td>
                        <td>₱{{ number_format($detail->product->price, 2) }}</td>
                        <td>₱{{ number_format($detail->subtotal, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="4" class="text-end">Total:</th>
                        <th>₱{{ number_format($order->orderDetails->sum('subtotal'), 2) }}</th>
                    </tr>
                    @if($order->orderDetails->sum('subtotal') != $order->total_amount)
                        <th colspan="4" class="text-end">Paid:</th>
                        <th>₱{{ number_format($order->total_amount - $order->orderDetails->sum('subtotal'), 2) }}</th>
                    @endif
                    <tr>
                        <th colspan="4" class="text-end">Remaining Balance:</th>
                        <th>₱{{ number_format($order->total_amount, 2) }}</th>
                    </tr>
                </tfoot>
            </table>
            <div class="mt-4 text-end">
                @if($order->pay_status != 'Paid')
                    <form action="{{ route('orders.payment', $order->order_id) }}" method="POST" style="display:inline;">
                         @csrf
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-money-bill"></i> Proceed to Payment
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
