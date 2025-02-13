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

                    <!-- Form for Updating Order Type -->
                    <form action="{{ route('orders.update', $order->order_id) }}" method="POST">
                        @csrf
                        @method('PATCH')

                        <div class="mb-3">
                            <label class="form-label">Order Type</label>
                            <select name="order_type" class="form-control" {{ $order->payment_status === 'Paid' ? 'disabled' : '' }}>
                                <option value="Retail" {{ $order->order_type === 'Retail' ? 'selected' : '' }}>Retail</option>
                                <option value="Bulk" {{ $order->order_type === 'Bulk' ? 'selected' : '' }}>Bulk</option>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-primary">Update Order Type</button>
                    </form>

                    <hr>

                    <!-- Form for Updating Delivery Details -->
                    <form action="{{ route('delivery.update', $order->order_id) }}" method="POST">
                        @csrf
                        @method('PATCH')

                        <div class="mb-3">
                            <label class="form-label required">Delivery Address</label>
                            <textarea name="delivery_address" class="form-control" rows="3" required>{{ old('delivery_address', optional($order->delivery)->delivery_address) }}</textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label required">Preferred Delivery Date</label>
                            <input type="date" name="delivery_date" class="form-control" 
                                    value="{{ old('delivery_date', optional($order->delivery)->delivery_date) }}"
                                    required {{ $order->payment_status === 'Paid' ? 'disabled' : '' }}>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Special Instructions (Optional)</label>
                            <textarea name="special_instructions" class="form-control" rows="3">{{ old('special_instructions', optional($order->delivery)->special_instructions) }}</textarea>
                        </div>

                        <button type="submit" class="btn btn-primary">Update Delivery Details</button>
                    </form>

                    <div class="mt-4">
                        <a href="{{ route('orders.show', $order->order_id) }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </div>
            </div>
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
