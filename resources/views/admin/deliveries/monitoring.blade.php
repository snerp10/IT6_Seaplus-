@extends('layouts.admin')

@section('admin.content')
<div class="container">
    <h1>Order Monitoring</h1>
    <table class="table">
        <thead>
            <tr>
                <th>Order ID</th>
                <th>Customer Name</th>
                <th>Order Date</th>
                <th>Delivery Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($orders as $order)
                <tr>
                    <td>{{ $order->order_id }}</td>
                    <td>{{ $order->customer->name }}</td>
                    <td>{{ $order->order_date }}</td>
                    <td>{{ $order->delivery->delivery_status }}</td>
                    <td>
                        <a href="{{ route('admin.orders.show', $order->order_id) }}" class="btn btn-info">View</a>
                        <a href="{{ route('admin.deliveries.edit', $order->delivery->id) }}" class="btn btn-warning">Edit</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
