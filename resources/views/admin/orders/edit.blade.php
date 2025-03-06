@extends('layouts.admin')

@section('admin.content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-edit"></i> Edit Order #{{ $order->order_id }}
        </h1>
        <a href="{{ route('admin.orders.show', $order->order_id) }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i> Back to Order Details
        </a>
    </div>

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card shadow border-left-warning">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between bg-gradient-warning text-white">
                    <h6 class="m-0 font-weight-bold">Order Information</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.orders.update', $order->order_id) }}" method="POST" id="orderForm">
                        @csrf
                        @method('PUT')
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="cus_id" class="form-label fw-bold"><i class="fas fa-user"></i> Customer <span class="text-danger">*</span></label>
                                <select name="cus_id" id="cus_id" class="form-control @error('cus_id') is-invalid @enderror" required>
                                    <option value="">-- Select Customer --</option>
                                    @foreach($customers as $customer)
                                        <option value="{{ $customer->cus_id }}" {{ $order->cus_id == $customer->cus_id ? 'selected' : '' }}>
                                            {{ $customer->fname }} {{ $customer->lname }} ({{ $customer->email }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('cus_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-3">
                                <label for="pay_method" class="form-label fw-bold"><i class="fas fa-money-bill"></i> Payment Method <span class="text-danger">*</span></label>
                                <select name="pay_method" class="form-control @error('pay_method') is-invalid @enderror" required>
                                    <option value="Cash" {{ $order->pay_method == 'Cash' ? 'selected' : '' }}>Cash</option>
                                    <option value="Cash on Delivery" {{ $order->pay_method == 'Cash on Delivery' ? 'selected' : '' }}>Cash on Delivery</option>
                                    <option value="GCash" {{ $order->pay_method == 'GCash' ? 'selected' : '' }}>GCash</option>
                                </select>
                                @error('pay_method')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-3">
                                <label for="order_type" class="form-label fw-bold"><i class="fas fa-tags"></i> Order Type <span class="text-danger">*</span></label>
                                <select name="order_type" id="order_type" class="form-control @error('order_type') is-invalid @enderror" required>
                                    <option value="Retail" {{ $order->order_type == 'Retail' ? 'selected' : '' }}>Retail</option>
                                    <option value="Bulk" {{ $order->order_type == 'Bulk' ? 'selected' : '' }}>Bulk</option>
                                </select>
                                @error('order_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                @if($order->order_type == 'Bulk')
                                <div class="small text-info mt-1">
                                    <i class="fas fa-info-circle"></i> Bulk orders require a minimum 30% downpayment.
                                </div>
                                @endif
                            </div>
                        </div>

                        <div class="card shadow mb-4 border-left-success">
                            <div class="card-header py-3 d-flex justify-content-between align-items-center bg-gradient-success text-white">
                                <h6 class="m-0 font-weight-bold"><i class="fas fa-shopping-cart"></i> Product Selection</h6>
                                <span class="badge bg-warning text-dark">Modify products and click "Update Order" to recalculate totals</span>
                            </div>
                            <div class="card-body">
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle"></i> To add a new product, select it from one of the empty rows below and set the quantity.
                                    To remove a product, set its quantity to 0. Changes to products will be processed when you submit the form.
                                </div>
                                
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover" id="productsTable">
                                        <thead class="table-light">
                                            <tr>
                                                <th width="5%">#</th>
                                                <th width="40%">Product</th>
                                                <th width="15%">Price</th>
                                                <th width="15%">Stock Available</th>
                                                <th width="10%">Quantity</th>
                                                <th width="15%">Subtotal</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($orderDetails as $index => $detail)
                                            <tr class="existing-product">
                                                <td>{{ $index+1 }}</td>
                                                <td>
                                                    <select name="products[{{$index}}][prod_id]" class="form-control product-select" required>
                                                        <option value="">-- Select Product --</option>
                                                        @foreach($products as $product)
                                                            <option value="{{ $product->prod_id }}" 
                                                                {{ $detail->prod_id == $product->prod_id ? 'selected' : '' }}
                                                                data-price="{{ $product->price }}"
                                                                data-unit="{{ $product->unit }}"
                                                                data-stock="{{ $product->getStockAttribute() }}">
                                                                {{ $product->name }} - {{ $product->category }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td class="product-price">₱{{ number_format($detail->product->price, 2) }}</td>
                                                <td class="product-stock">{{ $detail->product->getStockAttribute() + $detail->quantity }}</td>
                                                <td>
                                                    <input type="number" name="products[{{$index}}][quantity]" 
                                                        class="form-control quantity-input" min="0" 
                                                        value="{{ $detail->quantity }}">
                                                    <small class="unit-text">{{ $detail->product->unit }}</small>
                                                </td>
                                                <td class="text-end fw-bold product-subtotal">
                                                    ₱{{ number_format($detail->subtotal, 2) }}
                                                </td>
                                            </tr>
                                            @endforeach
                                            
                                            <!-- Empty rows for additional products -->
                                            @for ($i = count($orderDetails); $i < max(count($orderDetails) + 3, 5); $i++)
                                            <tr class="new-product">
                                                <td>{{ $i+1 }}</td>
                                                <td>
                                                    <select name="products[{{$i}}][prod_id]" class="form-control product-select">
                                                        <option value="">-- Add New Product --</option>
                                                        @foreach($products as $product)
                                                            <option value="{{ $product->prod_id }}"
                                                                data-price="{{ $product->price }}"
                                                                data-unit="{{ $product->unit }}"
                                                                data-stock="{{ $product->getStockAttribute() }}">
                                                                {{ $product->name }} - {{ $product->category }} 
                                                                ({{ $product->getStockAttribute() }} {{ $product->unit }} available)
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td class="product-price">₱0.00</td>
                                                <td class="product-stock">0</td>
                                                <td>
                                                    <input type="number" name="products[{{$i}}][quantity]" 
                                                        class="form-control quantity-input" min="0" value="0">
                                                    <small class="unit-text"></small>
                                                </td>
                                                <td class="text-end fw-bold product-subtotal">₱0.00</td>
                                            </tr>
                                            @endfor
                                        </tbody>
                                        <tfoot class="table-light">
                                            <tr>
                                                <th colspan="5" class="text-end">Products Subtotal:</th>
                                                <th class="text-end">₱{{ number_format($order->orderDetails->sum('subtotal'), 2) }}</th>
                                            </tr>
                                            <tr>
                                                <th colspan="5" class="text-end">Delivery Cost:</th>
                                                <th class="text-end">₱{{ number_format($order->delivery->delivery_cost ?? 0, 2) }}</th>
                                            </tr>
                                            <tr class="table-primary">
                                                <th colspan="5" class="text-end">Total Amount:</th>
                                                <th class="text-end">₱{{ number_format($order->total_amount, 2) }}</th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                                
                                <div class="mt-3">
                                    <div class="alert alert-warning">
                                        <i class="fas fa-exclamation-triangle"></i> All product changes will be processed when you click the "Update Order" button at the bottom of this form.
                                        Inventory will be automatically adjusted when products are added or removed.
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card shadow mb-4 border-left-info">
                            <div class="card-header py-3 bg-gradient-info text-white">
                                <h6 class="m-0 font-weight-bold"><i class="fas fa-truck"></i> Delivery Information</h6>
                            </div>
                            <div class="card-body">
                                <div class="row mb-3">
                                    <div class="col-md-4">
                                        <label for="delivery_date" class="form-label fw-bold"><i class="fas fa-calendar"></i> Delivery Date</label>
                                        <input type="date" name="delivery_date" class="form-control" min="{{ date('Y-m-d') }}" 
                                            value="{{ $delivery->delivery_date ?? '' }}">
                                    </div>
                                    <div class="col-md-8">
                                        <label for="special_instructions" class="form-label fw-bold"><i class="fas fa-clipboard-list"></i> Special Instructions</label>
                                        <input type="text" name="special_instructions" class="form-control" 
                                            value="{{ $delivery->special_instructions ?? '' }}" 
                                            placeholder="Any special requirements for delivery">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <label for="street" class="form-label fw-bold"><i class="fas fa-road"></i> Street</label>
                                        <input type="text" name="street" class="form-control"
                                            value="{{ $delivery->street ?? '' }}">
                                    </div>
                                    <div class="col-md-3">
                                        <label for="city" class="form-label fw-bold"><i class="fas fa-city"></i> City</label>
                                        <input type="text" name="city" class="form-control"
                                            value="{{ $delivery->city ?? '' }}">
                                    </div>
                                    <div class="col-md-3">
                                        <label for="province" class="form-label fw-bold"><i class="fas fa-map"></i> Province</label>
                                        <input type="text" name="province" class="form-control"
                                            value="{{ $delivery->province ?? '' }}">
                                    </div>
                                    <div class="col-md-2">
                                        <label for="delivery_cost" class="form-label fw-bold"><i class="fas fa-coins"></i> Cost</label>
                                        <div class="input-group">
                                            <span class="input-group-text">₱</span>
                                            <input type="number" name="delivery_cost" class="form-control" 
                                                value="{{ $delivery->delivery_cost ?? 0 }}" 
                                                min="0" step="0.01">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card shadow mb-4 border-left-danger">
                            <div class="card-header py-3 bg-gradient-danger text-white">
                                <h6 class="m-0 font-weight-bold"><i class="fas fa-file-invoice-dollar"></i> Payment Status</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <label for="order_status" class="form-label fw-bold">Order Status</label>
                                        <select name="order_status" class="form-control">
                                            <option value="Pending" {{ $order->order_status == 'Pending' ? 'selected' : '' }}>Pending</option>
                                            <option value="Processing" {{ $order->order_status == 'Processing' ? 'selected' : '' }}>Processing</option>
                                            <option value="Completed" {{ $order->order_status == 'Completed' ? 'selected' : '' }}>Completed</option>
                                            <option value="Cancelled" {{ $order->order_status == 'Cancelled' ? 'selected' : '' }}>Cancelled</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">Payment History</label>
                                        <div class="table-responsive">
                                            <table class="table table-sm table-bordered">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th>Date</th>
                                                        <th>Amount</th>
                                                        <th>Method</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @forelse($order->payments as $payment)
                                                    <tr>
                                                        <td>{{ date('M d, Y', strtotime($payment->pay_date)) }}</td>
                                                        <td>₱{{ number_format($payment->amount_paid, 2) }}</td>
                                                        <td>{{ $payment->pay_method }}</td>
                                                    </tr>
                                                    @empty
                                                    <tr>
                                                        <td colspan="3" class="text-center">No payments recorded</td>
                                                    </tr>
                                                    @endforelse
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i> Editing an order may affect inventory levels and payment records. Make changes carefully.
                        </div>

                        <div class="d-flex justify-content-end">
                            <a href="{{ route('admin.orders.show', $order->order_id) }}" class="btn btn-secondary me-2">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                            <button type="submit" name="action" value="update" class="btn btn-warning">
                                <i class="fas fa-save"></i> Update Order
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Simple product selection change handler - just update display values
        const productSelects = document.querySelectorAll('.product-select');
        
        productSelects.forEach(select => {
            select.addEventListener('change', function() {
                const row = this.closest('tr');
                const priceField = row.querySelector('.product-price');
                const stockField = row.querySelector('.product-stock');
                const unitText = row.querySelector('.unit-text');
                
                if (this.value) {
                    const option = this.options[this.selectedIndex];
                    priceField.textContent = `₱${parseFloat(option.dataset.price).toFixed(2)}`;
                    stockField.textContent = option.dataset.stock;
                    unitText.textContent = option.dataset.unit;
                } else {
                    priceField.textContent = '₱0.00';
                    stockField.textContent = '0';
                    unitText.textContent = '';
                }
            });
        });
    });
</script>
@endpush
@endsection