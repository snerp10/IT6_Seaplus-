@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Customer Dashboard</h1>
    <div class="row">
        <div class="col-md-6">
            <h2>Products</h2>
            <ul>
                <li><a href="{{ route('products.index', ['category' => 'Sand']) }}">Sand</a></li>
                <li><a href="{{ route('products.index', ['category' => 'Gravel']) }}">Gravel</a></li>
                <li><a href="{{ route('products.index', ['category' => 'Hollow Blocks']) }}">Hollow Blocks</a></li>
                <li><a href="{{ route('products.index', ['category' => 'Hardware Supplies']) }}">Hardware Supplies</a></li>
            </ul>
        </div>
        <div class="col-md-6">
            <h2>Orders</h2>
            <ul>
                <li><a href="{{ route('orders.index') }}">View Orders</a></li>
                <li><a href="{{ route('orders.create') }}">Place Order</a></li>
            </ul>
        </div>
    </div>
</div>
@endsection
