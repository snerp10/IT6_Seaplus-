<?php

namespace App\Http\Controllers\Admin;

use App\Models\Employee;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class AdminEmployeeController extends Controller
{
    /**
     * Display a listing of all employees.
     */
    public function index(Request $request)
    {
        $query = Employee::query();
        
        // Filter by position if specified
        if ($request->has('position') && !empty($request->position)) {
            $query->where('position', $request->position);
        }
        
        // Search functionality
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('fname', 'like', "%{$search}%")
                  ->orWhere('mname', 'like', "%{$search}%")
                  ->orWhere('lname', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('contact_number', 'like', "%{$search}%");
            });
        }
        
        $employees = $query->orderBy('lname')->paginate(10);
        
        return view('admin.employees.index', compact('employees'));
    }

    /**
     * Show the form for creating a new employee.
     */
    public function create()
    {
        return view('admin.employees.create');
    }

    /**
     * Store a newly created employee in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'fname' => 'required|string|max:50',
            'mname' => 'nullable|string|max:50',
            'lname' => 'required|string|max:50',
            'gender' => 'nullable|string|in:Male,Female,Other',
            'birthdate' => 'required|date',
            'contact_number' => 'required|string|max:20',
            'email' => 'required|email|unique:employees,email|max:100',
            // Address fields
            'street' => 'required|string|max:100',
            'barangay' => 'required|string|max:100',
            'city' => 'required|string|max:100',
            'province' => 'required|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'country' => 'required|string|max:50',
            'position' => 'required|string|max:100',
            'salary' => 'required|numeric|min:0',
        ]);
        
        // Add additional fields
        $validated['status'] = 'Active';
        $validated['hired_date'] = now(); // Changed from hire_date to hired_date to match model
        
        DB::beginTransaction();
        try {
            Employee::create($validated);
            
            DB::commit();
            return redirect()->route('admin.employees.index')
                ->with('success', 'Employee created successfully.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors('Failed to create employee: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Display the specified employee.
     */
    public function show(Employee $employee)
    {
        return view('admin.employees.show', compact('employee'));
    }

    /**
     * Show the form for editing the specified employee.
     */
    public function edit(Employee $employee)
    {
        return view('admin.employees.edit', compact('employee'));
    }

    /**
     * Update the specified employee in storage.
     */
    public function update(Request $request, Employee $employee)
    {
        $validated = $request->validate([
            'fname' => 'required|string|max:50',
            'mname' => 'nullable|string|max:50',
            'lname' => 'required|string|max:50',
            'gender' => 'nullable|string|in:Male,Female,Other',
            'birthdate' => 'required|date',
            'contact_number' => 'required|string|max:20',
            'email' => ['required', 'email', 'max:100', Rule::unique('employees')->ignore($employee->emp_id, 'emp_id')],
            // Address fields
            'street' => 'required|string|max:100',
            'barangay' => 'required|string|max:100',
            'city' => 'required|string|max:100',
            'province' => 'required|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'country' => 'required|string|max:50',
            'position' => 'required|string|max:100',
            'salary' => 'required|numeric|min:0',
            'status' => 'nullable|in:Active,Inactive',
        ]);
        
        DB::beginTransaction();
        try {
            // If status isn't provided in the form, maintain the current status
            if (!isset($validated['status'])) {
                $validated['status'] = $employee->status ?? 'Active';
            }
            
            $employee->update($validated);
            
            DB::commit();
            return redirect()->route('admin.employees.show', $employee->emp_id)
                ->with('success', 'Employee updated successfully.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors('Failed to update employee: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Remove the specified employee from storage.
     */
    public function destroy(Employee $employee)
    {
        try {
            $employee->delete();
            return redirect()->route('admin.employees.index')
                ->with('success', 'Employee deleted successfully.');
        } catch (\Exception $e) {
            return back()->withErrors('Failed to delete employee: ' . $e->getMessage());
        }
    }
}