<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Employee;
use Illuminate\Support\Facades\Hash;

class AdminEmployeeController extends Controller
{
    public function index()
    {
        $employees = Employee::latest()->paginate(10);
        return view('admin.employees.index', compact('employees'));
    }

    public function create()
    {
        return view('admin.employees.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'fname' => ['required', 'string', 'max:255'],
            'mname' => ['nullable', 'string', 'max:255'],
            'lname' => ['required', 'string', 'max:255'],
            'birthdate' => ['required', 'date'],
            'contact_number' => ['required', 'string', 'max:20'],
            'email' => ['required', 'email', 'unique:employees,email'],
            'address' => ['required', 'string', 'max:255'],
            'position' => ['required', 'string', 'max:255'],
            'salary' => ['required', 'numeric']
        ]);

        try {
            $employee = Employee::create($validated);

            return redirect()
                ->route('admin.employees.index')
                ->with('success', 'Employee created successfully');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Failed to create employee');
        }
    }

    public function show(Employee $employee)
    {
        return view('admin.employees.show', compact('employee'));
    }

    public function edit(Employee $employee)
    {
        return view('admin.employees.edit', compact('employee'));
    }

    public function update(Request $request, Employee $employee)
    {
        $validated = $request->validate([
            'fname' => ['required', 'string', 'max:255'],
            'mname' => ['nullable', 'string', 'max:255'],
            'lname' => ['required', 'string', 'max:255'],
            'birthdate' => ['required', 'date'],
            'contact_number' => ['required', 'string', 'max:20'],
            'email' => ['required', 'email', 'unique:employees,email,' . $employee->emp_id . ',emp_id'],
            'address' => ['required', 'string', 'max:255'],
            'position' => ['required', 'string', 'max:255'],
            'salary' => ['required', 'numeric']
        ]);

        try {
            $employee->update($validated);

            return redirect()
                ->route('admin.employees.index')
                ->with('success', 'Employee updated successfully');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Failed to update employee');
        }
    }

    public function destroy(Employee $employee)
    {
        try {
            $employee->delete();
            return redirect()
                ->route('admin.employees.index')
                ->with('success', 'Employee deleted successfully');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete employee');
        }
    }
}