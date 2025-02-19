@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Employee Details</h1>
    <table class="table">
        <tr>
            <th>Name:</th>
            <td>{{ $employee->name }}</td>
        </tr>
        <tr>
            <th>Email:</th>
            <td>{{ $employee->email }}</td>
        </tr>
        <tr>
            <th>Role:</th>
            <td>{{ $employee->role }}</td>
        </tr>
    </table>
    <a href="{{ route('admin.employees.edit', $employee->id) }}" class="btn btn-warning">Edit</a>
    <form action="{{ route('admin.employees.destroy', $employee->id) }}" method="POST" style="display:inline;">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-danger">Delete</button>
    </form>
</div>
@endsection
