@extends('layouts.app')

@section('content')
<div class="container py-4">
    <!-- Verification check to ensure customer data exists -->
    @if(!auth()->user()->customer)
        <div class="alert alert-danger">
            <h4 class="alert-heading">Account Error</h4>
            <p>Your account doesn't have a valid customer profile. Please contact support.</p>
        </div>
    @else
        <!-- Welcome Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card bg-primary text-white shadow-lg border-0 rounded-lg">
                    <div class="card-body py-4">
                        <div class="d-flex align-items-center">
                            <div>
                                <h1 class="h3 mb-0">Welcome, {{ auth()->user()->customer->fname ?? 'Customer' }}!</h1>
                                <p class="mb-0 mt-2">
                                    <i class="fas fa-calendar-alt mr-1"></i> {{ now()->format('l, F d, Y') }}
                                    <span class="mx-2">|</span>
                                    <i class="fas fa-shopping-bag mr-1"></i> {{ $orderStats['total_orders'] }} Order(s)
                                </p>
                            </div>
                            <div class="ml-auto">
                                <a href="{{ route('customer.orders.create') }}" class="btn btn-light">
                                    <i class="fas fa-plus-circle"></i> New Order
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Dashboard Stats -->
        <div class="row mb-4">
            <!-- Total Orders -->
            <div class="col-md-3 col-sm-6 mb-3 mb-md-0">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Orders</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $orderStats['total_orders'] }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-shopping-cart fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Completed Orders -->
            <div class="col-md-3 col-sm-6 mb-3 mb-md-0">
                <div class="card border-left-success shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Completed</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $orderStats['completed_orders'] }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Processing Orders -->
            <div class="col-md-3 col-sm-6 mb-3 mb-sm-0">
                <div class="card border-left-warning shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">In Progress</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $orderStats['processing_orders'] }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-spinner fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Spent -->
            <div class="col-md-3 col-sm-6">
                <div class="card border-left-info shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Total Spent</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">₱{{ number_format($orderStats['total_spent'], 2) }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-money-bill-wave fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Recent Orders -->
            <div class="col-lg-8 mb-4">
                <div class="card shadow mb-4">
                    <div class="card-header py-3 d-flex justify-content-between align-items-center">
                        <h6 class="m-0 font-weight-bold text-primary">Recent Orders</h6>
                        <a href="{{ route('customer.orders.index') }}" class="btn btn-sm btn-primary">View All</a>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>Order #</th>
                                        <th>Date</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($recentOrders as $order)
                                        @if($order->cus_id == auth()->user()->customer->cus_id)
                                        <tr>
                                            <td>{{ $order->order_id }}</td>
                                            <td>{{ \Carbon\Carbon::parse($order->created_at)->format('M d, Y') }}</td>
                                            <td>₱{{ number_format($order->total_amount, 2) }}</td>
                                            <td>
                                                @if($order->order_status == 'Completed')
                                                    <span class="badge bg-success">{{ $order->order_status }}</span>
                                                @elseif($order->order_status == 'Processing')
                                                    <span class="badge bg-warning">{{ $order->order_status }}</span>
                                                @elseif($order->order_status == 'Cancelled')
                                                    <span class="badge bg-danger">{{ $order->order_status }}</span>
                                                @else
                                                    <span class="badge bg-secondary">{{ $order->order_status }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('customer.orders.show', $order->order_id) }}" class="btn btn-sm btn-info">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        @endif
                                    @empty
                                    <tr>
                                        <td colspan="5" class="text-center">No orders found</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Upcoming Deliveries -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Upcoming Deliveries</h6>
                    </div>
                    <div class="card-body">
                        @forelse($upcomingDeliveries as $delivery)
                            @if($delivery->order && $delivery->order->cus_id == auth()->user()->customer->cus_id)
                            <div class="border-left-info pl-3 py-2 mb-3">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <div class="font-weight-bold">Order #{{ $delivery->order->order_id ?? 'N/A' }}</div>
                                        <div>
                                            @if($delivery->street && $delivery->city && $delivery->province)
                                                {{ $delivery->street }}, {{ $delivery->city }}, {{ $delivery->province }}
                                            @else
                                                Address not provided
                                            @endif
                                        </div>
                                        <div class="text-muted">
                                            <i class="fas fa-calendar-day mr-1"></i>
                                            @if($delivery->delivery_date)
                                                {{ \Carbon\Carbon::parse($delivery->delivery_date)->format('M d, Y') }}
                                            @else
                                                Date not set
                                            @endif
                                        </div>
                                    </div>
                                    <div>
                                        <span class="badge bg-info">{{ $delivery->delivery_status ?? 'Pending' }}</span>
                                    </div>
                                </div>
                            </div>
                            @endif
                        @empty
                        <p class="text-center">No upcoming deliveries</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Right Side Widgets -->
            <div class="col-lg-4">
                <!-- Quick Actions -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Quick Actions</h6>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <a href="{{ route('customer.orders.create') }}" class="btn btn-primary btn-block">
                                <i class="fas fa-plus-circle mr-1"></i> Place New Order
                            </a>
                            <a href="{{ route('customer.products.index') }}" class="btn btn-info btn-block">
                                <i class="fas fa-search mr-1"></i> Browse Products
                            </a>
                            <a href="{{ route('customer.profile') }}" class="btn btn-secondary btn-block">
                                <i class="fas fa-user-edit mr-1"></i> Update Profile
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Product Categories -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Product Categories</h6>
                    </div>
                    <div class="card-body">
                        <div class="list-group">
                            @foreach($productCategories as $category)
                            <a href="{{ route('customer.products.index', ['category' => $category]) }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                <span>
                                    @if($category == 'Sand')
                                        <i class="fas fa-cube text-warning mr-2"></i>
                                    @elseif($category == 'Gravel')
                                        <i class="fas fa-mountain text-secondary mr-2"></i>
                                    @elseif($category == 'Hollow Blocks')
                                        <i class="fas fa-th-large text-primary mr-2"></i>
                                    @elseif($category == 'Hardware Supplies')
                                        <i class="fas fa-tools text-danger mr-2"></i>
                                    @else
                                        <i class="fas fa-box text-info mr-2"></i>
                                    @endif
                                    {{ $category }}
                                </span>
                                <span class="badge bg-primary rounded-pill">
                                    <i class="fas fa-angle-right"></i>
                                </span>
                            </a>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Pending Payments -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Pending Payments</h6>
                    </div>
                    <div class="card-body">
                        @forelse($pendingPayments as $payment)
                            @if($payment->order && $payment->order->cus_id == auth()->user()->customer->cus_id)
                            <div class="border-left-warning pl-3 py-2 mb-3">
                                <div class="font-weight-bold">Order #{{ $payment->order->order_id }}</div>
                                <div>Amount Due: ₱{{ number_format($payment->outstanding_balance, 2) }}</div>
                                <div class="text-muted small">
                                    <i class="fas fa-calendar mr-1"></i>
                                    Due: {{ \Carbon\Carbon::parse($payment->pay_date)->format('M d, Y') }}
                                </div>
                            </div>
                            @endif
                        @empty
                        <p class="text-center">No pending payments</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- Recommended Products -->
        <div class="row">
            <div class="col-12">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Recommended Products</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @forelse($recommendedProducts as $product)
                            <div class="col-md-4 mb-4">
                                <div class="card h-100">
                                    @if($product->image)
                                    <img src="{{ asset('storage/' . $product->image) }}" class="card-img-top" alt="{{ $product->name }}" style="height: 150px; object-fit: cover;">
                                    @else
                                    <div class="bg-light text-center py-4">
                                        <i class="fas fa-image fa-3x text-muted"></i>
                                    </div>
                                    @endif
                                    <div class="card-body">
                                        <h5 class="card-title">{{ $product->name }}</h5>
                                        <p class="card-text">
                                            <span class="badge bg-secondary mb-2">{{ $product->category }}</span><br>
                                            <strong>₱{{ number_format($product->pricing->first()->selling_price ?? 0, 2) }}</strong> per {{ $product->unit }}
                                        </p>
                                        <a href="{{ route('customer.products.show', $product->prod_id) }}" class="btn btn-primary btn-sm">View Details</a>
                                    </div>
                                </div>
                            </div>
                            @empty
                            <div class="col-12 text-center">
                                <p>No recommended products available</p>
                            </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Initialize any dashboard-specific scripts here
    });
</script>
@endpush