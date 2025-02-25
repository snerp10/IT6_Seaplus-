@extends('layouts.admin')

@section('admin.content')
<div class="container">
    <h1>Edit Payment</h1>
    <form action="{{ route('admin.payments.update', $payment->pay_id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="form-group">
            <label for="cus_id">Customer</label>
            <input type="text" class="form-control" id="cus_id" name="cus_id" value="{{ $payment->cus_id }}" required>
        </div>
        <div class="form-group">
            <label for="order_id">Order</label>
            <input type="text" class="form-control" id="order_id" name="order_id" value="{{ $payment->order_id }}" required>
        </div>
        <div class="form-group">
            <label for="amount_paid">Amount Paid</label>
            <input type="text" class="form-control" id="amount_paid" name="amount_paid" value="{{ $payment->amount_paid }}" required>
        </div>
        <div class="form-group">
            <label for="pay_date">Payment Date</label>
            <input type="date" class="form-control" id="pay_date" name="pay_date" value="{{ $payment->pay_date }}" required>
        </div>
        <div class="form-group">
            <label for="pay_method">Payment Method</label>
            <input type="text" class="form-control" id="pay_method" name="pay_method" value="{{ $payment->pay_method }}" required>
        </div>
        <div class="form-group">
            <label for="outstanding_balance">Outstanding Balance</label>
            <input type="text" class="form-control" id="outstanding_balance" name="outstanding_balance" value="{{ $payment->outstanding_balance }}" required>
        </div>
        <div class="form-group">
            <label for="invoice_number">Invoice Number</label>
            <input type="text" class="form-control" id="invoice_number" name="invoice_number" value="{{ $payment->invoice_number }}" required>
        </div>
        <button type="submit" class="btn btn-primary">Update</button>
    </form>
</div>
@endsection
