{{-- filepath: resources/views/customer/products/index.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row mb-4">
        <!-- Page Title and Category Filter -->
        <div class="col-md-8">
            <h1 class="h3 mb-0 text-gray-800">
                @if(request()->category)
                    {{ request()->category }} Products
                @else
                    All Products
                @endif
            </h1>
            <p class="mb-4 text-muted">Browse our selection of quality construction materials</p>
        </div>
        <div class="col-md-4">
            <form action="{{ route('customer.products.index') }}" method="GET" class="mb-0">
                <div class="input-group">
                    <select name="category" class="form-control">
                        <option value="">All Categories</option>
                        @foreach($categories as $category)
                            <option value="{{ $category }}" {{ request('category') == $category ? 'selected' : '' }}>
                                {{ $category }}
                            </option>
                        @endforeach
                    </select>
                    <div class="input-group-append">
                        <button class="btn btn-primary" type="submit">Filter</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Search Box -->
    <div class="row mb-4">
        <div class="col-md-6 offset-md-3">
            <form action="{{ route('customer.products.search') }}" method="GET" class="mb-0">
                <div class="input-group">
                    <input type="text" name="query" class="form-control" placeholder="Search products..." value="{{ request('query') }}">
                    <div class="input-group-append">
                        <button class="btn btn-accent" type="submit">
                            <i class="fas fa-search"></i> Search
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Products Grid -->
    <div class="row">
        @forelse($products as $product)
            <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                <div class="card h-100 shadow-sm product-card">
                    <div class="product-image">
                        @if($product->image)
                            <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="card-img-top">
                        @else
                            <img src="{{ asset('images/product-placeholder.jpg') }}" alt="No image" class="card-img-top">
                        @endif
                    </div>
                    <div class="card-body">
                        <span class="badge bg-secondary mb-2">{{ $product->category }}</span>
                        <h5 class="card-title">{{ $product->name }}</h5>
                        <p class="card-text text-muted">{{ $product->unit }}</p>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="h5 mb-0 text-primary">₱{{ number_format($product->pricing->first()->selling_price ?? 0, 2) }}</span>
                            <span class="badge bg-{{ $product->availability_class }}">{{ $product->availability }}</span>
                        </div>
                    </div>
                    <div class="card-footer bg-white border-top-0">
                        <div class="d-grid">
                            <a href="{{ route('customer.products.show', $product->prod_id) }}" class="btn btn-outline-primary">
                                View Details
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i> No products found.
                </div>
            </div>
        @endforelse
    </div>
</div>
@endsection

@push('styles')
<style>
    .product-image {
        height: 180px;
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .product-image img {
        object-fit: cover;
        height: 100%;
        width: 100%;
    }
    .product-card {
        transition: transform 0.2s;
    }
    .product-card:hover {
        transform: translateY(-5px);
    }
</style>
@endpush