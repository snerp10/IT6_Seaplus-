@extends('layouts.admin')

@section('admin.content')
<div class="container">
    <h1>Add Stock to Product {{ $product->name }}</h1>
    <form action="{{ route('admin.products.update', $product->prod_id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label class="form-label">Add Stock:</label>
            <input type="number" name="stock" class="form-control" value="0" required>
        </div>

        <button type="submit" class="btn btn-primary">Add Stock</button>
    </form>
</div>
@endsection

