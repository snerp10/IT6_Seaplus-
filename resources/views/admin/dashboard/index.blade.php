@extends('layouts.admin')

@section('admin.content')
<div class="container">
    <h1 class="mb-4">Admin Dashboard</h1>
    <div class="row">
        <div class="col-md-3 mb-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Total Orders</h5>
                    <p class="card-text display-4">{{ $totalOrders }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Total Products</h5>
                    <p class="card-text display-4">{{ $totalProducts }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Total Sales</h5>
                    <p class="card-text display-4">₱{{ number_format($totalSales, 2) }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Pending Deliveries</h5>
                    <p class="card-text display-4">{{ $pendingDeliveries }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection