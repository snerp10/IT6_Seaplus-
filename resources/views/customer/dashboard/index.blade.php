@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="card shadow-lg rounded-lg">
        <div class="card-header bg-primary text-white">
            <h1 class="text-center mb-0">Customer Dashboard</h1>
        </div>
        <div class="card-body">
            <div class="row">
                <!-- Products Section -->
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm mb-3">
                        <div class="card-header bg-secondary text-white">
                            <h2 class="mb-0"><i class="fas fa-box"></i> Products</h2>
                        </div>
                        <div class="card-body">
                            <ul class="list-group">
                                <li class="list-group-item">
                                    <a href="{{ route('products.index', ['category' => 'Sand']) }}" class="text-dark">
                                        <i class="fas fa-cube text-warning"></i> Sand
                                    </a>
                                </li>
                                <li class="list-group-item">
                                    <a href="{{ route('products.index', ['category' => 'Gravel']) }}" class="text-dark">
                                        <i class="fas fa-mountain text-secondary"></i> Gravel
                                    </a>
                                </li>
                                <li class="list-group-item">
                                    <a href="{{ route('products.index', ['category' => 'Hollow Blocks']) }}" class="text-dark">
                                        <i class="fas fa-th-large text-primary"></i> Hollow Blocks
                                    </a>
                                </li>
                                <li class="list-group-item">
                                    <a href="{{ route('products.index', ['category' => 'Hardware Supplies']) }}" class="text-dark">
                                        <i class="fas fa-tools text-danger"></i> Hardware Supplies
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Orders Section -->
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-success text-white">
                            <h2 class="mb-0"><i class="fas fa-shopping-cart"></i> Orders</h2>
                        </div>
                        <div class="card-body">
                            <ul class="list-group">
                                <li class="list-group-item">
                                    <a href="{{ route('orders.index') }}" class="text-dark">
                                        <i class="fas fa-eye text-info"></i> View Orders
                                    </a>
                                </li>
                                <li class="list-group-item">
                                    <a href="{{ route('orders.create') }}" class="text-dark">
                                        <i class="fas fa-plus-circle text-success"></i> Place Order
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
