@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="card shadow-lg">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h2 class="mb-0">{{ $product->name }}</h2>
            <span class="badge bg-light text-dark">{{ $product->category }}</span>
        </div>
        <div class="card-body">
            <div class="row">
                <!-- Product Image (If Available) -->
                <div class="col-md-4 text-center">
                    <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="img-fluid rounded shadow-sm">
                </div>
                
                <!-- Product Details -->
                <div class="col-md-8">
                    <table class="table table-bordered">
                        <tr>
                            <th class="bg-light">Price:</th>
                            <td class="fw-bold text-success">₱{{ number_format($product->price, 2) }}</td>
                        </tr>
                        <tr>
                            <th class="bg-light">Unit:</th>
                            <td>{{ $product->unit }}</td>
                        </tr>
                        <tr>
                            <th class="bg-light">Category:</th>
                            <td class="text-primary">
                                {{ $product->category }}
                            </td>
                        </tr>
                    </table>

                    <!-- Order Button or Out of Stock Message -->
                    @if($product->stock > 0)
                        <form action="{{ route('orders.create') }}" method="GET">
                            <input type="hidden" name="prod_id" value="{{ $product->prod_id }}">
                            <button type="submit" class="btn btn-primary btn-lg w-100 mt-3 shadow-sm">
                                <i class="fas fa-shopping-cart"></i> Proceed to Order
                            </button>
                        </form>
                    @else
                        <div class="alert alert-warning text-center mt-3">
                            <i class="fas fa-exclamation-triangle"></i> This product is currently out of stock.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
