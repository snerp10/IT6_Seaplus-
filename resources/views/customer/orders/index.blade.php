@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="card shadow-lg rounded-lg">
        <div class="card-header bg-primary text-white">
            <h1 class="text-center mb-0"><i class="fas fa-clipboard-list"></i> Orders</h1>
        </div>
        <div class="card-body">
            <table class="table table-hover table-bordered text-center align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>Order Date</th>
                        <th>Total Amount</th>
                        <th>Payment Status</th>
                        <th>Delivery Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($orders as $order)
                    <tr>
                        <td>{{ $order->order_date }}</td>
                        <td class="fw-bold text-success">₱{{ number_format($order->total_amount, 2) }}</td>
                        <td>
                            <span class="badge bg-{{ $order->payment_status == 'Paid' ? 'success' : 'warning' }}">
                                {{ $order->payment_status }}
                            </span>
                        </td>
                        <td>
                            <span class="badge bg-{{ $order->delivery->delivery_status == 'Delivered' ? 'success' : 'info' }}">
                                {{ $order->delivery->delivery_status }}
                            </span>
                        </td>
                        <td>
                            <a href="{{ route('orders.show', $order->order_id) }}" class="btn btn-sm btn-info">
                                <i class="fas fa-eye"></i> View
                            </a>
                            <a href="{{ route('orders.edit', $order->order_id) }}" class="btn btn-sm btn-warning">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <form action="{{ route('orders.destroy', $order->order_id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" 
                                    @if($order->payment_status === 'Paid') disabled @endif>
                                    <i class="fas fa-times"></i> Cancel
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
