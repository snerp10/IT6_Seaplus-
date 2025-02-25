@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Products - {{ $category }}</h1>

    @if($products->isEmpty())
        <div class="alert alert-warning text-center">No products available.</div>
    @else
        <div class="row">
            @foreach($products as $product)
            <div class="col-md-4 mb-3">
                <div class="card">
                    <img src="{{ asset('storage/' . $product->image) }}" class="card-img-top" alt="{{ $product->name }}">
                    <div class="card-body">
                        <h5 class="card-title">{{ $product->name }}</h5>
                        <p class="card-text">{{ number_format($product->price, 2) }} PHP / {{ $product->unit }}</p>
                        <form action="{{ route('products.show', $product->prod_id) }}" method="GET" style="display:inline;">
                            @csrf
                            <button type="submit" class="btn btn-success">Add to Order</button>
                        </form>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    @endif
</div>

@endsection
