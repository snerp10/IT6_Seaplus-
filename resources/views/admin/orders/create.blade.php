@extends('layouts.admin')

@section('admin.content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-plus-circle"></i> Create New Order
        </h1>
        <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i> Back to Orders
        </a>
    </div>

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <form action="{{ route('admin.orders.calculate') }}" method="POST">
        @csrf
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card shadow mb-4 border-left-primary">
                    <div class="card-header py-3 bg-gradient-primary text-white">
                        <h6 class="m-0 font-weight-bold"><i class="fas fa-user"></i> Customer & Order Information</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="cus_id" class="form-label fw-bold">Select Customer <span class="text-danger">*</span></label>
                            <select name="cus_id" id="cus_id" class="form-control @error('cus_id') is-invalid @enderror" required>
                                <option value="">-- Select Customer --</option>
                                @foreach($customers as $customer)
                                    <option value="{{ $customer->cus_id }}" {{ old('cus_id') == $customer->cus_id ? 'selected' : '' }}>
                                        {{ $customer->fname }} {{ $customer->lname }} ({{ $customer->email }})
                                    </option>
                                @endforeach
                            </select>
                            @error('cus_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="pay_method" class="form-label fw-bold"><i class="fas fa-money-bill"></i> Payment Method <span class="text-danger">*</span></label>
                                    <select name="pay_method" class="form-control @error('pay_method') is-invalid @enderror" required>
                                        <option value="Cash" {{ old('pay_method') == 'Cash' ? 'selected' : '' }}>Cash</option>
                                        <option value="Cash on Delivery" {{ old('pay_method') == 'Cash on Delivery' ? 'selected' : '' }}>Cash on Delivery</option>
                                        <option value="GCash" {{ old('pay_method') == 'GCash' ? 'selected' : '' }}>GCash</option>
                                    </select>
                                    @error('pay_method')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="order_type" class="form-label fw-bold"><i class="fas fa-tags"></i> Order Type <span class="text-danger">*</span></label>
                                    <select name="order_type" id="order_type" class="form-control @error('order_type') is-invalid @enderror" required>
                                        <option value="Retail" {{ old('order_type') == 'Retail' ? 'selected' : '' }}>Retail</option>
                                        <option value="Bulk" {{ old('order_type') == 'Bulk' ? 'selected' : '' }}>Bulk</option>
                                    </select>
                                    @error('order_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="small text-info mt-1" id="bulk-info" style="display: none;">
                                        <i class="fas fa-info-circle"></i> Bulk orders require a minimum 30% downpayment.
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="delivery_date" class="form-label fw-bold"><i class="fas fa-calendar"></i> Delivery Date</label>
                            <input type="date" name="delivery_date" class="form-control" min="{{ date('Y-m-d') }}" 
                                value="{{ old('delivery_date') }}">
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="street" class="form-label fw-bold"><i class="fas fa-road"></i> Street</label>
                                    <input type="text" name="street" class="form-control" value="{{ old('street') }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="city" class="form-label fw-bold"><i class="fas fa-city"></i> City</label>
                                    <input type="text" name="city" class="form-control" value="{{ old('city') }}">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="province" class="form-label fw-bold"><i class="fas fa-map"></i> Province</label>
                                    <input type="text" name="province" class="form-control" value="{{ old('province') }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="delivery_cost" class="form-label fw-bold"><i class="fas fa-coins"></i> Delivery Cost</label>
                                    <div class="input-group">
                                        <span class="input-group-text">₱</span>
                                        <input type="number" name="delivery_cost" id="delivery_cost" class="form-control" 
                                            value="{{ old('delivery_cost', 0) }}" min="0" step="0.01">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="special_instructions" class="form-label fw-bold"><i class="fas fa-clipboard-list"></i> Special Instructions</label>
                            <textarea name="special_instructions" class="form-control" rows="2">{{ old('special_instructions') }}</textarea>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card shadow mb-4 border-left-success">
                    <div class="card-header py-3 bg-gradient-success text-white">
                        <h6 class="m-0 font-weight-bold"><i class="fas fa-shopping-cart"></i> Product Selection</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover" id="productTable">
                                <thead class="table-light">
                                    <tr>
                                        <th width="40%">Product</th>
                                        <th width="20%">Price</th>
                                        <th width="20%">Stock</th>
                                        <th width="20%">Quantity</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @for ($i = 0; $i < 5; $i++)
                                    <tr>
                                        <td>
                                            <select name="products[{{$i}}][prod_id]" class="form-control product-select">
                                                <option value="">-- Select Product --</option>
                                                @foreach($products as $product)
                                                    <option value="{{ $product->prod_id }}" 
                                                        data-price="{{ $product->price }}"
                                                        data-unit="{{ $product->unit }}"
                                                        data-stock="{{ $product->getStockAttribute() }}">
                                                        {{ $product->name }} - {{ $product->category }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td class="product-price">₱0.00</td>
                                        <td class="product-stock">0</td>
                                        <td>
                                            <input type="number" name="products[{{$i}}][quantity]" class="form-control quantity-input" min="0" value="0">
                                            <small class="unit-text"></small>
                                        </td>
                                    </tr>
                                    @endfor
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="alert alert-info mt-3">
                            <i class="fas fa-info-circle"></i> After selecting products and quantities, click "Calculate & Review" to see the total price and confirm your order.
                        </div>
                        
                        <div class="d-flex justify-content-end mt-3">
                            <button type="reset" class="btn btn-secondary me-2">
                                <i class="fas fa-undo"></i> Reset Form
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-check-circle"></i> Calculate & Review
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
    // Very simple JavaScript - only for showing product info and bulk order notice
    document.addEventListener('DOMContentLoaded', function() {
        // Show bulk order info based on selection
        const orderType = document.getElementById('order_type');
        const bulkInfo = document.getElementById('bulk-info');
        
        orderType.addEventListener('change', function() {
            bulkInfo.style.display = this.value === 'Bulk' ? 'block' : 'none';
        });
        
        // Initial check for bulk info display
        if (orderType.value === 'Bulk') {
            bulkInfo.style.display = 'block';
        }
        
        // Show product price and stock when a product is selected
        const productSelects = document.querySelectorAll('.product-select');
        productSelects.forEach(select => {
            select.addEventListener('change', function() {
                const row = this.closest('tr');
                const priceCell = row.querySelector('.product-price');
                const stockCell = row.querySelector('.product-stock');
                const unitText = row.querySelector('.unit-text');
                
                if (this.value) {
                    const option = this.options[this.selectedIndex];
                    const price = option.dataset.price;
                    const stock = option.dataset.stock;
                    const unit = option.dataset.unit;
                    
                    priceCell.textContent = `₱${parseFloat(price).toFixed(2)}`;
                    stockCell.textContent = stock;
                    unitText.textContent = unit;
                } else {
                    priceCell.textContent = '₱0.00';
                    stockCell.textContent = '0';
                    unitText.textContent = '';
                }
            });
        });
    });
</script>
@endpush
@endsection
