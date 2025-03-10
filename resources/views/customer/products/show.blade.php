{{-- filepath: resources/views/customer/products/show.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('customer.dashboard.index') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('customer.products.index') }}">Products</a></li>
            <li class="breadcrumb-item"><a href="{{ route('customer.products.index', ['category' => $product->category]) }}">{{ $product->category }}</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $product->name }}</li>
        </ol>
    </nav>

    <div class="row mb-3">
        <div class="col-12">
            <a href="{{ route('customer.products.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i> Back to Products
            </a>
        </div>
    </div>

    <div class="card shadow">
        <div class="card-body p-0">
            <div class="row g-0">
                <div class="col-md-5">
                    @if($product->image)
                        <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="img-fluid rounded-start" style="max-height: 400px; width: 100%; object-fit: cover;">
                    @else
                        <div class="bg-light d-flex align-items-center justify-content-center" style="height: 400px;">
                            <i class="fas fa-image fa-4x text-secondary"></i>
                        </div>
                    @endif
                </div>
                <div class="col-md-7">
                    <div class="card-body p-4">
                        <h2 class="card-title mb-1">{{ $product->name }}</h2>
                        <span class="badge bg-secondary mb-3">{{ $product->category }}</span>
                        <h4 class="text-primary mb-3">₱{{ number_format($product->pricing->first()->selling_price ?? 0, 2) }} per {{ $product->unit }}</h4>
                        
                        <div class="d-flex align-items-center mb-3">
                            <div class="me-3">
                                <span class="fw-bold">Availability:</span>
                                <span class="text-{{ $product->availability_class }}">{{ $product->availability }}</span>
                            </div>
                        </div>
                        
                        <p class="card-text">{{ $product->description }}</p>
                        
                        <div class="mt-4">
                            <a href="{{ route('customer.orders.create', ['product' => $product->prod_id]) }}" class="btn btn-primary btn-lg">
                                <i class="fas fa-shopping-cart me-2"></i> Order Now
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Additional product details or related products can go here -->
    
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