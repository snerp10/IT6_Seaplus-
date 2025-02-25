@extends('layouts.admin')

@section('admin.content')
<div class="container">
    <h1>Add Employee</h1>
    <form action="{{ route('admin.employees.store') }}" method="POST">
        @csrf
        <div class="row">
            <div class="col">
                <div class="mb-3">
                    <label for="lname" class="form-label">Last Name</label>
                    <input type="text" class="form-control" id="lname" name="lname" required maxlength="50">
                </div>
                <div class="mb-3">
                    <label for="fname" class="form-label">First Name</label>
                    <input type="text" class="form-control" id="fname" name="fname" required maxlength="50">
                </div>
                <div class="mb-3">
                    <label for="mname" class="form-label">Middle Name</label>
                    <input type="text" class="form-control" id="mname" name="mname" maxlength="50">
                </div>
            </div>
            <div class="col">
                <div class="mb-3">
                    <label for="birthdate" class="form-label">Birthdate</label>
                    <input type="date" class="form-control" id="birthdate" name="birthdate" required>
                </div>
                <div class="mb-3">
                    <label for="contact_number" class="form-label">Contact Number</label>
                    <input type="text" class="form-control" id="contact_number" name="contact_number" required maxlength="20">
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" required maxlength="100">
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col">
                <div class="mb-3">
                    <label for="address" class="form-label">Address</label>
                    <input type="text" class="form-control" id="address" name="address" required maxlength="255">
                </div>
            </div>
            <div class="col">
                <div class="mb-3">
                    <label for="position" class="form-label">Position</label>
                    <input type="text" class="form-control" id="position" name="position" required maxlength="100">
                </div>
                <div class="mb-3">
                    <label for="salary" class="form-label">Salary</label>
                    <input type="number" class="form-control" id="salary" name="salary" required step="0.01">
                </div>
            </div>
        </div>
        <button type="submit" class="btn btn-primary">Add Employee</button>
    </form>
</div>
@endsection

