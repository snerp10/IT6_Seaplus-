@extends('layouts.admin')

@section('admin.content')
<div class="container">
    <h1>Export Inventory</h1>
    <form action="{{ route('admin.inventories.export') }}" method="POST">
        @csrf
        <button type="submit" class="btn btn-primary">Download CSV</button>
    </form>
</div>
@endsection

