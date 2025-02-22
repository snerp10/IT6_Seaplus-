@extends('layouts.admin')

@section('admin.content')
<div class="container">
    <h1>Edit Delivery {{ $delivery->id }}</h1>

    <form action="{{ route('admin.deliveries.update', $delivery) }}" method="POST">
        @csrf
        @method('PATCH')

        <div class="mb-3">
            <label for="order_id" class="form-label">Order ID</label>
            <select name="order_id" class="form-control" required>
                @foreach($orders as $order)
                    <option value="{{ $order->id }}" {{ $order->id === $delivery->order_id ? 'selected' : '' }}>{{ $order->order_id }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="delivery_status" class="form-label">Status</label>
            <select name="delivery_status" class="form-control" required>
                <option value="Pending" {{ $delivery->delivery_status === 'Pending' ? 'selected' : '' }}>Pending</option>
                <option value="Processing" {{ $delivery->delivery_status === 'Processing' ? 'selected' : '' }}>Processing</option>
                <option value="In Transit" {{ $delivery->delivery_status === 'In Transit' ? 'selected' : '' }}>In Transit</option>
                <option value="Delivered" {{ $delivery->delivery_status === 'Delivered' ? 'selected' : '' }}>Delivered</option>
                <option value="Cancelled" {{ $delivery->delivery_status === 'Cancelled' ? 'selected' : '' }}>Cancelled</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="estimated_delivery_date" class="form-label">Estimated Delivery Date</label>
            <input type="date" name="estimated_delivery_date" class="form-control" value="{{ $delivery->estimated_delivery_date }}" required>
        </div>

        <div class="d-flex justify-content-between">
            <a href="{{ route('admin.deliveries.index') }}" class="btn btn-secondary">Cancel</a>
            <button type="submit" class="btn btn-primary">Update Delivery</button>
        </div>
    </form>
</div>
@endsection
