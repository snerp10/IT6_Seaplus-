<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AdminEmployeeController extends Controller
{
    
    public function index()
    {
        $employees = \App\Models\Employee::all();
        return view('admin.employees.index', compact('employees'));
    }

    public function create()
    {
        return view('admin.employees.create');
    }

    public function store(Request $request)
    {
        $employee = new \App\Models\Employee();
        $employee->name = $request->name;
        $employee->email = $request->email;
        $employee->role = $request->role;
        $employee->save();
        return redirect()->route('admin.employees.index');
    }

    public function edit($id)
    {
        $employee = \App\Models\Employee::find($id);
        return view('admin.employees.edit', compact('employee'));
    }

    public function update(Request $request, $id)
    {
        $employee = \App\Models\Employee::find($id);
        $employee->name = $request->name;
        $employee->email = $request->email;
        $employee->role = $request->role;
        $employee->save();
        return redirect()->route('admin.employees.index');
    }

    public function destroy($id)
    {
        \App\Models\Employee::find($id)->delete();
        return redirect()->route('admin.employees.index');
    }
}
