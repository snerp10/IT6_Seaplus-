@extends('layouts.admin')

@section('admin.content')
<div class="container">
    <h1>Products</h1>
    <a href="{{ route('admin.products.create') }}" class="btn btn-primary mb-3">Add Product</a>
    <table class="table">
        <thead>
            <tr>
                <th>Name</th>
                <th>Selling Price</th>
                <th>Stock Quantity</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($products as $product)
            <tr>
                <td>{{ $product->name }}</td>
                <td>{{ $product->price }}/{{$product->unit}}</td>
                <td>{{ $product->stock }}</td>
                <td>
                    <a href="{{ route('admin.products.show', $product->prod_id) }}" class="btn btn-info">View</a>
                    <a href="{{ route('admin.products.edit', $product->prod_id) }}" class="btn btn-warning">Add Stock</a>
                    <form action="{{ route('admin.products.destroy', $product->prod_id) }}" method="POST" style="display:inline;"
                        onsubmit="return confirm('Are you sure you want to delete this product?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Delete</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
