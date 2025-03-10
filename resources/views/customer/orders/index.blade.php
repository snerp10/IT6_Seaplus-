@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="card shadow">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h4 class="mb-0">My Orders</h4>
            <a href="{{ route('customer.orders.create') }}" class="btn btn-light">
                <i class="fas fa-plus-circle"></i> Place New Order
            </a>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Order #</th>
                            <th>Date</th>
                            <th>Type</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Delivery Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($orders as $order)
                        <tr>
                            <td>{{ $order->order_id }}</td>
                            <td>{{ $order->created_at->format('M d, Y') }}</td>
                            <td>{{ $order->order_type }}</td>
                            <td>₱{{ number_format($order->total_amount, 2) }}</td>
                            <td>
                                @if($order->order_status == 'Completed')
                                    <span class="badge bg-success">{{ $order->order_status }}</span>
                                @elseif($order->order_status == 'Processing')
                                    <span class="badge bg-warning">{{ $order->order_status }}</span>
                                @elseif($order->order_status == 'Cancelled')
                                    <span class="badge bg-danger">{{ $order->order_status }}</span>
                                @else
                                    <span class="badge bg-secondary">{{ $order->order_status }}</span>
                                @endif
                            </td>
                            <td>
                                @if($order->delivery)
                                    <span class="badge bg-info">{{ $order->delivery->delivery_status }}</span>
                                @else
                                    <span class="badge bg-secondary">Not Set</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('customer.orders.show', $order->order_id) }}" class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i> View
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center">No orders found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="d-flex justify-content-center mt-4">
                {{ $orders->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
