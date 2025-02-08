@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            <h2>Edit Order #{{ $order->order_id }}</h2>
        </div>
        <div class="card-body">
            <form action="{{ route('orders.update', $order->order_id) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label>Payment Method</label>
                            <select name="payment_method" class="form-control">
                                <option value="Cash" {{ $order->payment_method == 'Cash' ? 'selected' : '' }}>Cash</option>
                                <option value="GCash" {{ $order->payment_method == 'GCash' ? 'selected' : '' }}>GCash</option>
                            </select>
                        </div>

                        <div class="form-group mb-3">
                            <label>Payment Status</label>
                            <select name="payment_status" class="form-control">
                                <option value="Paid" {{ $order->payment_status == 'Paid' ? 'selected' : '' }}>Paid</option>
                                <option value="Unpaid" {{ $order->payment_status == 'Unpaid' ? 'selected' : '' }}>Unpaid</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <table class="table">
                            <tr>
                                <th>Order Date:</th>
                                <td>{{ $order->order_date }}</td>
                            </tr>
                            <tr>
                                <th>Total Amount:</th>
                                <td>₱{{ number_format($order->total_amount, 2) }}</td>
                            </tr>
                            <tr>
                                <th>Delivery Status:</th>
                                <td>{{ $order->delivery_status }}</td>
                            </tr>
                        </table>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">Update Order</button>
                <a href="{{ route('orders.show', $order->order_id) }}" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>
</div>
@endsection
