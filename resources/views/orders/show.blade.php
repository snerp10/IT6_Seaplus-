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
                        <button type="submit" class="btn btn-danger">Cancel Order</button>
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
                            <td>{{ $order->payment_method }}</td>
                        </tr>
                        <tr>
                            <th>Payment Status:</th>
                            <td>
                                <span class="badge bg-{{ $order->payment_status == 'Paid' ? 'success' : 'warning' }}">
                                    {{ $order->payment_status }}
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
                                <span class="badge bg-{{ $order->delivery_status == 'Delivered' ? 'success' : 'info' }}">
                                    {{ $order->delivery_status }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th>Total Amount:</th>
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
                        <th>₱{{ number_format($order->total_amount, 2) }}</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
@endsection
