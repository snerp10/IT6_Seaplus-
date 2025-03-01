@extends('layouts.admin')

@section('admin.content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-calculator"></i> Order Confirmation
        </h1>
        <a href="{{ route('admin.orders.create') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i> Back to Order Form
        </a>
    </div>

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div class="row mb-4">
        <div class="col-lg-8">
            <!-- Order Details Card -->
            <div class="card shadow mb-4 border-left-primary">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between bg-gradient-primary text-white">
                    <h6 class="m-0 font-weight-bold">Order Details</h6>
                    <span class="badge bg-warning text-dark">{{ $orderData['order_type'] }}</span>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <h5 class="mb-3">Customer Information</h5>
                            <p><strong>Name:</strong> {{ $customer->fname }} {{ $customer->lname }}</p>
                            <p><strong>Email:</strong> {{ $customer->email }}</p>
                            <p><strong>Phone:</strong> {{ $customer->contact_number }}</p>
                        </div>
                        <div class="col-md-6">
                            <h5 class="mb-3">Order Information</h5>
                            <p><strong>Payment Method:</strong> {{ $orderData['pay_method'] }}</p>
                            <p><strong>Order Date:</strong> {{ now()->format('F d, Y') }}</p>
                            @if($orderData['order_type'] === 'Bulk')
                            <div class="alert alert-info py-2">
                                <i class="fas fa-info-circle"></i> Bulk orders require a minimum 30% downpayment.
                            </div>
                            @endif
                        </div>
                    </div>

                    <h5 class="mb-3">Products</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead class="bg-light">
                                <tr>
                                    <th width="5%">#</th>
                                    <th width="35%">Product</th>
                                    <th width="15%">Price</th>
                                    <th width="15%">Quantity</th>
                                    <th width="15%">Unit</th>
                                    <th width="15%">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($orderData['products'] as $index => $product)
                                <tr>
                                    <td>{{ $index+1 }}</td>
                                    <td>{{ $product['name'] }}</td>
                                    <td>₱{{ number_format($product['price'], 2) }}</td>
                                    <td>{{ $product['quantity'] }}</td>
                                    <td>{{ $product['unit'] }}</td>
                                    <td class="text-end">₱{{ number_format($product['subtotal'], 2) }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center">No products added to this order</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <h5 class="mt-4 mb-3">Delivery Information</h5>
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Delivery Address:</strong><br>
                                {{ $orderData['street'] }}<br>
                                {{ $orderData['city'] }}, {{ $orderData['province'] }}
                            </p>
                            <p><strong>Special Instructions:</strong><br>
                                {{ $orderData['special_instructions'] ?: 'None' }}
                            </p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Delivery Date:</strong> 
                                {{ $orderData['delivery_date'] ? date('F d, Y', strtotime($orderData['delivery_date'])) : 'Not specified' }}
                            </p>
                            <p><strong>Delivery Status:</strong> <span class="badge bg-warning text-dark">Pending</span></p>
                            <p><strong>Delivery Fee:</strong> ₱{{ number_format($orderData['delivery_cost'], 2) }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <!-- Order Summary Card -->
            <div class="card shadow mb-4 border-left-success">
                <div class="card-header py-3 bg-gradient-success text-white">
                    <h6 class="m-0 font-weight-bold"><i class="fas fa-file-invoice-dollar"></i> Order Summary</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <tr>
                                <th>Subtotal:</th>
                                <td class="text-end">₱{{ number_format($orderData['subtotal'], 2) }}</td>
                            </tr>
                            <tr>
                                <th>Delivery Fee:</th>
                                <td class="text-end">₱{{ number_format($orderData['delivery_cost'], 2) }}</td>
                            </tr>
                            <tr class="border-top border-2">
                                <th class="h5">Total:</th>
                                <td class="text-end h5">₱{{ number_format($orderData['total_amount'], 2) }}</td>
                            </tr>
                            @if($orderData['order_type'] === 'Bulk')
                            <tr class="text-info">
                                <th>Minimum Downpayment (30%):</th>
                                <td class="text-end">₱{{ number_format($orderData['total_amount'] * 0.3, 2) }}</td>
                            </tr>
                            @endif
                        </table>
                    </div>
                    
                    <div class="alert alert-warning mt-3">
                        <i class="fas fa-exclamation-triangle"></i> Please verify all details are correct before placing the order.
                    </div>
                    
                    <form action="{{ route('admin.orders.store') }}" method="POST">
                        @csrf
                        
                        <!-- Hidden inputs to carry over all the data -->
                        <input type="hidden" name="cus_id" value="{{ $orderData['cus_id'] }}">
                        <input type="hidden" name="pay_method" value="{{ $orderData['pay_method'] }}">
                        <input type="hidden" name="order_type" value="{{ $orderData['order_type'] }}">
                        <input type="hidden" name="delivery_date" value="{{ $orderData['delivery_date'] }}">
                        <input type="hidden" name="street" value="{{ $orderData['street'] }}">
                        <input type="hidden" name="city" value="{{ $orderData['city'] }}">
                        <input type="hidden" name="province" value="{{ $orderData['province'] }}">
                        <input type="hidden" name="special_instructions" value="{{ $orderData['special_instructions'] }}">
                        <input type="hidden" name="delivery_cost" value="{{ $orderData['delivery_cost'] }}">
                        
                        @foreach($orderData['products'] as $index => $product)
                            <input type="hidden" name="products[{{ $index }}][prod_id]" value="{{ $product['prod_id'] }}">
                            <input type="hidden" name="products[{{ $index }}][quantity]" value="{{ $product['quantity'] }}">
                        @endforeach
                        
                        <div class="d-grid gap-2 mt-4">
                            <a href="{{ route('admin.orders.create') }}" class="btn btn-secondary">
                                <i class="fas fa-edit"></i> Edit Order
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-check-circle"></i> Confirm & Place Order
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

