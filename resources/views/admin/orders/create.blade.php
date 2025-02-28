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

    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card shadow border-left-primary">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between bg-gradient-primary text-white">
                    <h6 class="m-0 font-weight-bold">Order Information</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.orders.calculate') }}" method="POST" id="orderForm">
                        @csrf
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="cus_id" class="form-label fw-bold"><i class="fas fa-user"></i> Customer <span class="text-danger">*</span></label>
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
                            <div class="col-md-3">
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
                            <div class="col-md-3">
                                <label for="order_type" class="form-label fw-bold"><i class="fas fa-tags"></i> Order Type <span class="text-danger">*</span></label>
                                <select name="order_type" id="order_type" class="form-control @error('order_type') is-invalid @enderror" required>
                                    <option value="Retail" {{ old('order_type') == 'Retail' ? 'selected' : '' }}>Retail</option>
                                    <option value="Bulk" {{ old('order_type') == 'Bulk' ? 'selected' : '' }}>Bulk</option>
                                </select>
                                @error('order_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                @if(old('order_type') == 'Bulk' || session('order_data.order_type') == 'Bulk')
                                <div class="small text-info mt-1">
                                    <i class="fas fa-info-circle"></i> Bulk orders require a minimum 30% downpayment.
                                </div>
                                @endif
                            </div>
                        </div>

                        <div class="card shadow mb-4 border-left-success">
                            <div class="card-header py-3 bg-gradient-success text-white">
                                <h6 class="m-0 font-weight-bold"><i class="fas fa-shopping-cart"></i> Product Selection</h6>
                            </div>
                            <div class="card-body">
                                <p class="text-muted"><i class="fas fa-info-circle"></i> Select products and quantities, then click "Calculate Total" to update</p>
                                
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover">
                                        <thead class="table-light">
                                            <tr>
                                                <th width="5%">#</th>
                                                <th width="40%">Product</th>
                                                <th width="15%">Price</th>
                                                <th width="15%">Quantity</th>
                                                <th width="25%">Subtotal</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php 
                                                $orderData = session('order_data', ['products' => []]);
                                                $productData = $orderData['products'] ?? [];
                                                $totalAmount = $orderData['total_amount'] ?? 0;
                                            @endphp
                                            
                                            @for ($i = 0; $i < 5; $i++)
                                            <tr>
                                                <td>{{ $i+1 }}</td>
                                                <td>
                                                    <select name="products[{{$i}}][prod_id]" class="form-control">
                                                        <option value="">-- Select Product --</option>
                                                        @foreach($products as $product)
                                                            @php
                                                                $isSelected = false;
                                                                if(isset($productData[$i]) && $productData[$i]['prod_id'] == $product->prod_id) {
                                                                    $isSelected = true;
                                                                }
                                                            @endphp
                                                            <option value="{{ $product->prod_id }}" {{ $isSelected ? 'selected' : '' }}>
                                                                {{ $product->name }} - {{ $product->category }} 
                                                                ({{ $product->getStockAttribute() }} {{ $product->unit }} available)
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td>
                                                    @if(isset($productData[$i]) && isset($productData[$i]['price']))
                                                        ₱{{ number_format($productData[$i]['price'], 2) }}
                                                    @else
                                                        ₱0.00
                                                    @endif
                                                </td>
                                                <td>
                                                    <input type="number" name="products[{{$i}}][quantity]" 
                                                        class="form-control" min="0" 
                                                        value="{{ isset($productData[$i]) ? $productData[$i]['quantity'] : 0 }}">
                                                    @if(isset($productData[$i]) && isset($productData[$i]['unit']))
                                                        <small>{{ $productData[$i]['unit'] }}</small>
                                                    @endif
                                                </td>
                                                <td class="text-end fw-bold">
                                                    @if(isset($productData[$i]) && isset($productData[$i]['subtotal']))
                                                        ₱{{ number_format($productData[$i]['subtotal'], 2) }}
                                                    @else
                                                        ₱0.00
                                                    @endif
                                                </td>
                                            </tr>
                                            @endfor
                                        </tbody>
                                        <tfoot class="table-light">
                                            <tr>
                                                <th colspan="4" class="text-end">Total Amount:</th>
                                                <th class="text-end">₱{{ number_format($totalAmount, 2) }}</th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                                <div class="d-flex justify-content-end mt-3">
                                    <button type="submit" name="action" value="calculate" class="btn btn-info">
                                        <i class="fas fa-calculator"></i> Calculate Total
                                    </button>
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
                                            value="{{ old('delivery_date', isset($orderData['delivery_date']) ? $orderData['delivery_date'] : '') }}">
                                    </div>
                                    <div class="col-md-8">
                                        <label for="special_instructions" class="form-label fw-bold"><i class="fas fa-clipboard-list"></i> Special Instructions</label>
                                        <input type="text" name="special_instructions" class="form-control" 
                                            value="{{ old('special_instructions', isset($orderData['special_instructions']) ? $orderData['special_instructions'] : '') }}" 
                                            placeholder="Any special requirements for delivery">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <label for="street" class="form-label fw-bold"><i class="fas fa-road"></i> Street</label>
                                        <input type="text" name="street" class="form-control"
                                            value="{{ old('street', isset($orderData['street']) ? $orderData['street'] : '') }}">
                                    </div>
                                    <div class="col-md-3">
                                        <label for="city" class="form-label fw-bold"><i class="fas fa-city"></i> City</label>
                                        <input type="text" name="city" class="form-control"
                                            value="{{ old('city', isset($orderData['city']) ? $orderData['city'] : '') }}">
                                    </div>
                                    <div class="col-md-3">
                                        <label for="province" class="form-label fw-bold"><i class="fas fa-map"></i> Province</label>
                                        <input type="text" name="province" class="form-control"
                                            value="{{ old('province', isset($orderData['province']) ? $orderData['province'] : '') }}">
                                    </div>
                                    <div class="col-md-2">
                                        <label for="delivery_cost" class="form-label fw-bold"><i class="fas fa-coins"></i> Cost</label>
                                        <div class="input-group">
                                            <span class="input-group-text">₱</span>
                                            <input type="number" name="delivery_cost" class="form-control" 
                                                value="{{ old('delivery_cost', isset($orderData['delivery_cost']) ? $orderData['delivery_cost'] : 0) }}" 
                                                min="0" step="0.01">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> Review your order details before submitting. You can add payments after the order is created.
                        </div>

                        <div class="d-flex justify-content-end">
                            <button type="reset" class="btn btn-secondary me-2">
                                <i class="fas fa-undo"></i> Reset Form
                            </button>
                            <button type="submit" name="action" value="create" class="btn btn-primary">
                                <i class="fas fa-save"></i> Create Order
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection