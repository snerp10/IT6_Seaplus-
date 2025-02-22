@extends('layouts.admin')

@section('admin.content')
<div class="container">
    <h1>Employees</h1>
    <a href="{{ route('admin.employees.create') }}" class="btn btn-primary mb-3">Add Employee</a>
    <table class="table">
        <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($employees as $employee)
            <tr>
                <td>{{ $employee->name }}</td>
                <td>{{ $employee->email }}</td>
                <td>{{ $employee->role }}</td>
                <td>
                    <a href="{{ route('admin.employees.show', $employee->id) }}" class="btn btn-info">View</a>
                    <a href="{{ route('admin.employees.edit', $employee->id) }}" class="btn btn-warning">Edit</a>
                    <form action="{{ route('admin.employees.destroy', $employee->id) }}" method="POST" style="display:inline;">
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
