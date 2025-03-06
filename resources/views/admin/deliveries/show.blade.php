@extends('layouts.admin')

@section('admin.content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-truck"></i> Delivery Details
        </h1>
        <div>
            <a href="{{ route('admin.deliveries.index') }}" class="btn btn-secondary mr-2">
                <i class="fas fa-arrow-left"></i> Back to Deliveries
            </a>
            <a href="{{ route('admin.deliveries.edit', $delivery) }}" class="btn btn-warning mr-2">
                <i class="fas fa-edit"></i> Edit Delivery
            </a>
            <a href="{{ route('admin.orders.show', $delivery->order->order_id) }}" class="btn btn-primary">
                <i class="fas fa-shopping-cart"></i> View Order
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div class="row">
        <!-- Delivery Info Column -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Delivery Information</h6>
                    <span class="badge bg-{{ 
                        $delivery->delivery_status == 'Delivered' ? 'success' : 
                        ($delivery->delivery_status == 'Out for Delivery' ? 'info' : 
                        ($delivery->delivery_status == 'Scheduled' ? 'primary' : 
                        ($delivery->delivery_status == 'Failed' ? 'danger' : 
                        ($delivery->delivery_status == 'Returned' ? 'warning' : 'secondary')))) 
                    }} px-3 py-2">
                        {{ $delivery->delivery_status }}
                    </span>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-borderless">
                            <tr>
                                <th>Delivery ID:</th>
                                <td>{{ $delivery->id }}</td>
                            </tr>
                            <tr>
                                <th>Delivery Date:</th>
                                <td>{{ $delivery->delivery_date ? \Carbon\Carbon::parse($delivery->delivery_date)->format('F d, Y') : 'Not scheduled' }}</td>
                            </tr>
                            <tr>
                                <th>Address:</th>
                                <td>
                                    {{ $delivery->street }}<br>
                                    {{ $delivery->city }}, {{ $delivery->province }}
                                </td>
                            </tr>
                            <tr>
                                <th>Special Instructions:</th>
                                <td>{{ $delivery->special_instructions ?: 'None' }}</td>
                            </tr>
                            <tr>
                                <th>Delivery Cost:</th>
                                <td>₱{{ number_format($delivery->delivery_cost, 2) }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Customer Info Column -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3 bg-info text-white">
                    <h6 class="m-0 font-weight-bold"><i class="fas fa-user"></i> Customer Information</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="avatar mr-3">
                            <i class="fas fa-user-circle fa-3x text-gray-300"></i>
                        </div>
                        <div>
                            <h5 class="mb-0">{{ $delivery->order->customer->fname }} {{ $delivery->order->customer->lname }}</h5>
                            <p class="text-muted mb-0">{{ $delivery->order->customer->email }}</p>
                        </div>
                    </div>
                    <hr>
                    <p><i class="fas fa-phone mr-2"></i> {{ $delivery->order->customer->phone_no }}</p>
                    <p><i class="fas fa-map-marker-alt mr-2"></i> 
                        {{ $delivery->order->customer->street }}, {{ $delivery->order->customer->city }}, {{ $delivery->order->customer->province }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Order Info Column -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3 bg-success text-white">
                    <h6 class="m-0 font-weight-bold"><i class="fas fa-shopping-cart"></i> Order Information</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-borderless">
                            <tr>
                                <th>Order ID:</th>
                                <td># {{ $delivery->order->order_id }}</td>
                            </tr>
                            <tr>
                                <th>Order Date:</th>
                                <td>{{ \Carbon\Carbon::parse($delivery->order->order_date)->format('F d, Y') }}</td>
                            </tr>
                            <tr>
                                <th>Order Type:</th>
                                <td>{{ $delivery->order->order_type }}</td>
                            </tr>
                            <tr>
                                <th>Order Status:</th>
                                <td>
                                    <span class="badge bg-{{ 
                                        $delivery->order->order_status == 'Completed' ? 'success' : 
                                        ($delivery->order->order_status == 'Processing' ? 'warning' : 
                                        ($delivery->order->order_status == 'Cancelled' ? 'danger' : 
                                        ($delivery->order->order_status == 'Pending' ? 'info' : 'secondary')))
                                    }}">
                                        {{ $delivery->order->order_status }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <th>Order Total:</th>
                                <td>₱{{ number_format($delivery->order->total_amount, 2) }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Order Items Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-gradient-dark text-white">
            <h6 class="m-0 font-weight-bold"><i class="fas fa-box-open"></i> Order Items</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="bg-light">
                        <tr>
                            <th>#</th>
                            <th>Product</th>
                            <th>Category</th>
                            <th>Price</th>
                            <th>Quantity</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($delivery->order->orderDetails as $index => $detail)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $detail->product->name }}</td>
                            <td>{{ $detail->product->category }}</td>
                            <td>₱{{ number_format($detail->product->price, 2) }}</td>
                            <td>{{ $detail->quantity }} {{ $detail->product->unit }}</td>
                            <td class="text-end">₱{{ number_format($detail->subtotal, 2) }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center">No items found in this order</td>
                        </tr>
                        @endforelse
                    </tbody>
                    <tfoot class="bg-light">
                        <tr>
                            <th colspan="5" class="text-end">Subtotal:</th>
                            <th class="text-end">₱{{ number_format($delivery->order->orderDetails->sum('subtotal'), 2) }}</th>
                        </tr>
                        <tr>
                            <th colspan="5" class="text-end">Delivery Fee:</th>
                            <th class="text-end">₱{{ number_format($delivery->delivery_cost, 2) }}</th>
                        </tr>
                        <tr>
                            <th colspan="5" class="text-end">Total:</th>
                            <th class="text-end">₱{{ number_format($delivery->order->total_amount, 2) }}</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
    
    <!-- Delete Button at Bottom -->
    <div class="d-flex justify-content-end mb-4">
        <form action="{{ route('admin.deliveries.destroy', $delivery) }}" method="POST" class="d-inline"
              onsubmit="return confirm('Are you sure you want to delete this delivery? This action cannot be undone.')">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger" {{ $delivery->delivery_status == 'Delivered' ? 'disabled' : '' }}>
                <i class="fas fa-trash"></i> Delete Delivery
            </button>
        </form>
    </div>
</div>
@endsection