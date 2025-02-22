@extends('layouts.admin')

@section('admin.content')
<div class="container">
    <div class="card">
        <div class="card-header">
            <h3>Create New Delivery</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.deliveries.store') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="order_id" class="form-label">Order</label>
                    <select name="order_id" id="order_id" class="form-control @error('order_id') is-invalid @enderror" required>
                        <option value="">Select Order</option>
                        @foreach($orders as $order)
                            <option value="{{ $order->order_id }}">
                                Order #{{ $order->order_id }} - {{ $order->customer->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('order_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="delivery_status" class="form-label">Status</label>
                    <select name="delivery_status" class="form-control @error('delivery_status') is-invalid @enderror" required>
                        <option value="Pending">Pending</option>
                        <option value="Processing">Processing</option>
                        <option value="In Transit">In Transit</option>
                        <option value="Delivered">Delivered</option>
                        <option value="Cancelled">Cancelled</option>
                    </select>
                    @error('delivery_status')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="estimated_delivery_date" class="form-label">Estimated Delivery Date</label>
                    <input type="date" name="estimated_delivery_date" class="form-control @error('estimated_delivery_date') is-invalid @enderror" required>
                    @error('estimated_delivery_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="notes" class="form-label">Notes</label>
                    <textarea name="notes" class="form-control @error('notes') is-invalid @enderror"></textarea>
                    @error('notes')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex justify-content-between">
                    <a href="{{ route('admin.deliveries.index') }}" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">Create Delivery</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection