@extends('layouts.admin')

@section('admin.content')
<div class="container">
    <h1>Supplier Details</h1>
    <a href="{{ route('admin.suppliers.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Back</a>
    <table class="table">
        <tr>
            <th>Company Name:</th>
            <td>{{ $supplier->company_name }}</td>
        </tr>
        <tr>
            <th>Email:</th>
            <td>{{ $supplier->email }}</td>
        </tr>
        <tr>
            <th>Contact Number:</th>
            <td>{{ $supplier->contact_number }}</td>
        </tr>
        <tr>
            <th>Address:</th>
            <td>{{ $supplier->address }}</td>
        </tr>
        <tr>
            <th>Payment Terms:</th>
            <td>{{ $supplier->prod_type }}</td>
        </tr>
    </table>
    <a href="{{ route('admin.suppliers.edit', $supplier->supp_id) }}" class="btn btn-warning">Edit Supplier</a>
</div>
@endsection
