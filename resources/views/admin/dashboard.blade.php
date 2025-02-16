@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Admin Dashboard</h1>
    <div class="row">
        <div class="col-md-6">
            <h2>Manage Products</h2>
            <ul>
                <li><a href="{{ route('admin.products.index') }}">View Products</a></li>
                <li><a href="{{ route('admin.products.create') }}">Add Product</a></li>
            </ul>
        </div>
        <div class="col-md-6">
            <h2>Manage Orders</h2>
            <ul>
                <li><a href="{{ route('admin.orders.index') }}">View Orders</a></li>
            </ul>
        </div>
        <div class="col-md-6">
            <h2>Manage Customers</h2>
            <ul>
                <li><a href="{{ route('admin.customers.index') }}">View Customers</a></li>
                <li><a href="{{ route('admin.customers.create') }}">Add Customer</a></li>
            </ul>
        </div>
        <div class="col-md-6">
            <h2>Manage Payments</h2>
            <ul>
                <li><a href="{{ route('admin.payments.index') }}">View Payments</a></li>
            </ul>
        </div>
    </div>
</div>
@endsection
