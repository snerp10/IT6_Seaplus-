@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Payment History</h1>

        <table class="table">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Payment Date</th>
                    <th>Amount Paid</th>
                    <th>Payment Method</th>
                </tr>
            </thead>
            <tbody>
                @foreach($payments as $payment)
                    <tr>
                        <td>{{ $payment->order->order_id }}</td>
                        <td>{{ $payment->payment_date }}</td>
                        <td>₱{{ number_format($payment->amount_paid, 2) }}</td>
                        <td>{{ $payment->payment_method }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection

