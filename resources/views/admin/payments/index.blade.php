@extends('layouts.admin')

@section('admin.content')
<div class="container">
    <h1>Payments</h1>
    <a href="{{ route('admin.payments.create') }}" class="btn btn-primary mb-3">Add Payment</a>
    <table class="table">
        <thead>
            <tr>
                <th>Customer</th>
                <th>Order</th>
                <th>Amount Paid</th>
                <th>Payment Date</th>
                <th>Payment Method</th>
                <th>Outstanding Balance</th>
                <th>Invoice Number</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($payments as $payment)
            <tr>
                <td>{{ $payment->customer->name }}</td>
                <td>{{ $payment->order->order_number }}</td>
                <td>{{ $payment->amount_paid }}</td>
                <td>{{ $payment->pay_date }}</td>
                <td>{{ $payment->pay_method }}</td>
                <td>{{ $payment->outstanding_balance }}</td>
                <td>{{ $payment->invoice_number }}</td>
                <td>
                    <a href="{{ route('admin.payments.edit', $payment->pay_id) }}" class="btn btn-warning">Edit</a>
                    <form action="{{ route('admin.payments.destroy', $payment->pay_id) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Delete</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
