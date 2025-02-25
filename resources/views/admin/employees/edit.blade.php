@extends('layouts.admin')

@section('admin.content')
<div class="container">
    <h1>Edit Employee</h1>
    <a href="{{ route('admin.employees.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Back</a>
    <form action="{{ route('admin.employees.update', $employee->emp_id) }}" method="POST">
    @csrf
    @method('PUT')
    <div class="row">
        <div class="col-md-6">
            <div class="mb-3">
                <label for="fname" class="form-label">First Name</label>
                <input type="text" class="form-control" id="fname" name="fname" value="{{ old('fname', $employee->fname) }}" required maxlength="255">
            </div>
            <div class="mb-3">
                <label for="mname" class="form-label">Middle Name</label>
                <input type="text" class="form-control" id="mname" name="mname" value="{{ old('mname', $employee->mname) }}" maxlength="255">
            </div>
            <div class="mb-3">
                <label for="lname" class="form-label">Last Name</label>
                <input type="text" class="form-control" id="lname" name="lname" value="{{ old('lname', $employee->lname) }}" required maxlength="255">
            </div>
        </div>
        <div class="col-md-6">
            <div class="mb-3">
                <label for="birthdate" class="form-label">Birthdate</label>
                <input type="date" class="form-control" id="birthdate" name="birthdate" value="{{ old('birthdate', $employee->birthdate) }}" required>
            </div>
            <div class="mb-3">
                <label for="contact_number" class="form-label">Contact Number</label>
                <input type="text" class="form-control" id="contact_number" name="contact_number" value="{{ old('contact_number', $employee->contact_number) }}" required maxlength="20">
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $employee->email) }}" required>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="mb-3">
                <label for="address" class="form-label">Address</label>
                <input type="text" class="form-control" id="address" name="address" value="{{ old('address', $employee->address) }}" required maxlength="255">
            </div>
            <div class="mb-3">
                <label for="position" class="form-label">Position</label>
                <input type="text" class="form-control" id="position" name="position" value="{{ old('position', $employee->position) }}" required maxlength="255">
            </div>
        </div>
        <div class="col-md-6">
            <div class="mb-3">
                <label for="salary" class="form-label">Salary</label>
                <input type="number" class="form-control" id="salary" name="salary" value="{{ old('salary', $employee->salary) }}" required step="0.01">
            </div>
        </div>
    </div>
    <button type="submit" class="btn btn-primary mr-2 mb-3">Update Employee</button>
    </form> <!-- **Tama na ang closing tag** -->

<!-- **DELETE FORM** -->
    <form action="{{ route('admin.employees.destroy', $employee->emp_id) }}" method="POST" style="display:inline;">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-danger ml-2" onclick="return confirm('Are you sure you want to delete this employee?')">Remove Employee</button>
    </form>

</div> <!-- **Ito dapat ang closing tag ng container** -->
@endsection
