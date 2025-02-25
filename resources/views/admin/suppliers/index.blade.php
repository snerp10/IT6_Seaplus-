@extends('layouts.admin')

@section('admin.content')
<div class="container">
    <h1>Suppliers</h1>
    <a href="{{ route('admin.suppliers.create') }}" class="btn btn-primary mb-3">Add Supplier</a>
    <table class="table">
        <thead>
            <tr>
                <th>Company Name</th>
                <th>Email</th>
                <th>Contact Number</th>
                <th>Address</th>
                <th>Product Type</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($suppliers as $supplier)
            <tr>
                <td>{{ $supplier->company_name }}</td>
                <td>{{ $supplier->email }}</td>
                <td>{{ $supplier->contact_number }}</td>
                <td>{{ $supplier->address }}</td>
                <td>{{ $supplier->prod_type }}</td>
                <td>
                    <a href="{{ route('admin.suppliers.show', $supplier->supp_id) }}" class="btn btn-info">View</a>
                    <a href="{{ route('admin.suppliers.edit', $supplier->supp_id) }}" class="btn btn-warning">Edit</a>
                    <form action="{{ route('admin.suppliers.destroy', $supplier->supp_id) }}" method="POST" style="display:inline;">
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
