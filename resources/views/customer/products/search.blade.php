@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Search results for "{{ $query }}"</h1>
    <div class="row">
        @foreach($products as $product)
            <div class="col-md-4 mb-3">
                <div class="card">
                    <img src="{{ asset('storage/' . $product->image) }}" class="card-img-top" alt="{{ $product->name }}">
                    <div class="card-body">
                        <h5 class="card-title">{{ $product->name }}</h5>
                        <p class="card-text">{{ number_format($product->price, 2) }} PHP / {{ $product->unit }}</p>
                        @auth
                            <a href="{{ route('products.show', $product->product_id) }}" class="btn btn-primary">View</a>
                        @else
                            <a href="{{ route('login') }}" class="btn btn-warning">You must be logged in</a>
                        @endauth
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection
