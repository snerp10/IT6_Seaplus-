@extends('layouts.admin')

@section('admin.content')
<div class="container">
    <h1>Employee Details</h1>
    <a href="{{ route('admin.employees.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Back</a>
    <table class="table">
        <tr>
            <th>First Name:</th>
            <td>{{ $employee->fname }}</td>
        </tr>
        <tr>
            <th>Middle Name:</th>
            <td>{{ $employee->mname }}</td>
        </tr>
        <tr>
            <th>Last Name:</th>
            <td>{{ $employee->lname }}</td>
        </tr>
        <tr>
            <th>Birthdate:</th>
            <td>{{ $employee->birthdate }}</td>
        </tr>
        <tr>
            <th>Contact Number:</th>
            <td>{{ $employee->contact_number }}</td>
        </tr>
        <tr>
            <th>Email:</th>
            <td>{{ $employee->email }}</td>
        </tr>
        <tr>
            <th>Address:</th>
            <td>{{ $employee->address }}</td>
        </tr>
        <tr>
            <th>Position:</th>
            <td>{{ $employee->position }}</td>
        </tr>
        <tr>
            <th>Salary:</th>
            <td>{{ $employee->salary }}</td>
        </tr>
        <tr>
            <th>Role:</th>
            <td>{{ $employee->role }}</td>
        </tr>
    </table>
    <a href="{{ route('admin.employees.edit', ['employee' => $employee->emp_id]) }}" class="btn btn-warning">Edit Credentials</a>
</div>
@endsection

