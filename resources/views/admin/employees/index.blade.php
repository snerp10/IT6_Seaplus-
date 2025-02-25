@extends('layouts.admin')

@section('admin.content')
<div class="container">
    <h1>Employees</h1>
    <a href="{{ route('admin.employees.create') }}" class="btn btn-primary mb-3"><i class="fas fa-plus"></i> Add Employee</a>
    <table class="table">
        <thead>
            <tr>
                <th>Name</th>
                <th>Birthdate</th>
                <th>Contact Number</th>
                <th>Email</th>
                <th>Address</th>
                <th>Position</th>
                <th>Salary</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($employees as $employee)
            <tr>
                <td>{{ $employee->fname }} {{ $employee->mname }} {{ $employee->lname }}</td>
                <td>{{ $employee->birthdate }}</td>
                <td>{{ $employee->contact_number }}</td>
                <td>{{ $employee->email }}</td>
                <td>{{ $employee->address }}</td>
                <td>{{ $employee->position }}</td>
                <td>{{ $employee->salary }}</td>
                <td>
                    <a href="{{ route('admin.employees.show', ['employee' => $employee->emp_id]) }}" class="btn btn-info mb-2 btn-block">View Employee</a>
                    <a href="{{ route('admin.employees.edit', ['employee' => $employee->emp_id]) }}" class="btn btn-warning mb-2 btn-block">Update Employee</a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection

