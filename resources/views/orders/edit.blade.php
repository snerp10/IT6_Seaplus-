@extends('layouts.app')

@section('content')
<div class="container">
    @if(session('success'))
    <div class="alert alert-success mb-4">
        {{ session('success') }}
    </div>
    @endif

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
                        <!-- Order Details (Read-only) -->
                        <div class="mb-4">
                            <h4>Order Information</h4>
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
                                    <td>{{ $order->payment_status }}</td>
                                </tr>
                                <tr>
                                    <th>Total Amount:</th>
                                    <td>₱{{ number_format($order->total_amount, 2) }}</td>
                                </tr>
                            </table>
                        </div>

                        <!-- Editable Fields -->
                        <div class="mb-3">
                            <label class="form-label">Order Type</label>
                            <select name="order_type" class="form-control" {{ $order->payment_status === 'Paid' ? 'disabled' : '' }}>
                                <option value="Retail" {{ $order->order_type === 'Retail' ? 'selected' : '' }}>Retail</option>
                                <option value="Bulk" {{ $order->order_type === 'Bulk' ? 'selected' : '' }}>Bulk</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label required">Delivery Address</label>
                            <textarea name="delivery_address" class="form-control" rows="3" required>{{ old('delivery_address', $order->delivery_address) }}</textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label required">Preferred Delivery Schedule</label>
                            <input type="datetime-local" name="delivery_schedule" class="form-control" 
                                   value="{{ old('delivery_schedule', $order->delivery_schedule ? date('Y-m-d\TH:i', strtotime($order->delivery_schedule)) : '') }}"
                                   required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Special Instructions (Optional)</label>
                            <textarea name="special_instructions" class="form-control" rows="3">{{ old('special_instructions', $order->special_instructions) }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">Save and Proceed to Payment</button>
                    <a href="{{ route('orders.show', $order->order_id) }}" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.required:after {
    content: ' *';
    color: red;
}
</style>
@endsection
