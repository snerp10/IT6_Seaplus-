@extends('layouts.admin')

@section('admin.content')
<div class="container">
    <h1>Edit Supplier</h1>
    <form action="{{ route('admin.suppliers.update', $supplier->supp_id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label for="company_name" class="form-label">Company Name</label>
            <input type="text" class="form-control" id="company_name" name="company_name" value="{{ $supplier->company_name }}" required>
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" id="email" name="email" value="{{ $supplier->email }}" required>
        </div>
        <div class="mb-3">
            <label for="contact_number" class="form-label">Contact Number</label>
            <input type="text" class="form-control" id="contact_number" name="contact_number" value="{{ $supplier->contact_number }}" required>
        </div>
        <div class="mb-3">
            <label for="address" class="form-label">Address</label>
            <input type="text" class="form-control" id="address" name="address" value="{{ $supplier->address }}" required>
        </div>
        <div class="mb-3">
            <label for="prod_type" class="form-label">Product Type</label>
            <input type="text" class="form-control" id="prod_type" name="prod_type" value="{{ $supplier->prod_type }}" required>
        </div>
        <button type="submit" class="btn btn-primary">Update Supplier</button>
    </form>
</div>
@endsection
