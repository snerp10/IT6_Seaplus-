@extends('layouts.app')

@section('welcome')
<div class="hero-section py-5" style="background: linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.7)), url('{{ asset('images/hero-bg.jpg') }}') no-repeat center center; background-size: cover; min-height: 500px;">
    <div class="container text-center text-white py-5">
        <h1 class="display-4 fw-bold mt-5">Quality Construction Materials</h1>
        <p class="lead">Your trusted provider of sand, gravel, and construction supplies</p>
        <div class="mt-4">
            <a href="{{ route('customer.products.index') }}" class="btn btn-primary btn-lg me-2">
                <i class="fas fa-box me-2"></i> Browse Products
            </a>
            <a href="{{ route('register') }}" class="btn btn-outline-light btn-lg">
                <i class="fas fa-user-plus me-2"></i> Sign Up Now
            </a>
        </div>
    </div>
</div>
@endsection

@section('content')
<div class="container py-5">
    <!-- Featured Products -->
    <h2 class="text-center mb-4">Featured Products</h2>
    <div class="row">
        @foreach($featuredProducts ?? [] as $product)
        <div class="col-md-4 mb-4">
            <div class="card h-100">
                @if($product->image)
                <img src="{{ asset('storage/' . $product->image) }}" class="card-img-top" alt="{{ $product->name }}">
                @else
                <div class="bg-light text-center p-5">
                    <i class="fas fa-image fa-3x text-secondary"></i>
                </div>
                @endif
                <div class="card-body">
                    <h5 class="card-title">{{ $product->name }}</h5>
                    <p class="card-text">{{ Str::limit($product->description, 100) }}</p>
                    <div class="d-flex justify-content-between align-items-center">
                        <strong class="text-primary">₱{{ number_format($product->pricing->first()->selling_price ?? 0, 2) }}</strong>
                        <a href="{{ route('customer.products.show', $product->prod_id) }}" class="btn btn-sm btn-outline-primary">View Details</a>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    
    <!-- About Section -->
    <div class="row py-5 align-items-center">
        <div class="col-md-6">
            <h2>About KSM SeaPlus+</h2>
            <p>KSM SeaPlus+ is a leading provider of construction materials in the region, specializing in high-quality sand and gravel products for building projects of all sizes.</p>
            <p>With years of experience in the industry, we have built a reputation for reliability, quality, and excellent customer service.</p>
            <a href="{{ url('/about') }}" class="btn btn-primary">Learn More About Us</a>
        </div>
        <div class="col-md-6">
            <img src="{{ asset('images/about-img.jpg') }}" alt="About KSM SeaPlus+" class="img-fluid rounded shadow">
        </div>
    </div>
    
    <!-- Services Section -->
    <h2 class="text-center mb-4">Our Services</h2>
    <div class="row text-center">
        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <i class="fas fa-truck fa-3x text-primary mb-3"></i>
                    <h5 class="card-title">Fast Delivery</h5>
                    <p class="card-text">We offer prompt delivery services to ensure your construction materials arrive on time.</p>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <i class="fas fa-gem fa-3x text-primary mb-3"></i>
                    <h5 class="card-title">Quality Products</h5>
                    <p class="card-text">All our materials undergo quality checks to ensure they meet industry standards.</p>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <i class="fas fa-headset fa-3x text-primary mb-3"></i>
                    <h5 class="card-title">Expert Support</h5>
                    <p class="card-text">Our team of experts is always ready to provide guidance on the right materials for your project.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
