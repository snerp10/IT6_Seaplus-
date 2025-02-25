@extends('layouts.admin')

@section('admin.content')
<div class="container">
    <h1>Low Stock Alerts</h1>
    @if($lowStockProducts->isEmpty())
        <div class="alert alert-warning text-center">There are no low stock products at the moment.</div>
    @else
        <table class="table">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Current Stock</th>
                </tr>
            </thead>
            <tbody>
                @foreach($lowStockProducts as $product)
                <tr>
                    <td>{{ $product->name }}</td>
                    <td>{{ $product->stock }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
@endsection

