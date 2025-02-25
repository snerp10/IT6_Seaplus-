@extends('layouts.admin')

@section('admin.content')
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
                    <a href="{{ route('admin.products.edit', $product->prod_id) }}" class="btn btn-warning mt-3">Add Stock</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
