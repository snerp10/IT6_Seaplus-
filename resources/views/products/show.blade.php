@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            <h2>{{ $product->name }}</h2>
            <span class="badge bg-primary">{{ $product->category }}</span>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <table class="table">
                        <tr>
                            <th>Price:</th>
                            <td>₱{{ number_format($product->price, 2) }}</td>
                        </tr>
                        <tr>
                            <th>Unit:</th>
                            <td>{{ $product->unit }}</td>
                        </tr>
                        <tr>
                            <th>Available Stock:</th>
                            <td>{{ $product->stock }}</td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    @if($product->stock > 0)
                    <form action="{{ route('orders.create') }}" method="GET">
                        <input type="hidden" name="product_id" value="{{ $product->product_id }}">
                        <div class="form-group">
                            <label>Quantity:</label>
                            <input type="number" name="quantity" class="form-control" 
                                   min="1" max="{{ $product->stock }}" required>
                        </div>
                        <button type="submit" class="btn btn-primary mt-3">Proceed to Order</button>
                    </form>
                    @else
                    <div class="alert alert-warning">
                        This product is currently out of stock.
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
