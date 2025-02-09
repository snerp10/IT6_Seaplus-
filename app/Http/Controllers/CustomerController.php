<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function show()
    {
        $customer = auth()->user()->customer;
        return view('customer.profile', compact('customer'));
    }

    public function update(Request $request)
    {
        $customer = auth()->user()->customer;
        $customer->update($request->validated());
        return redirect()->route('customer.profile')->with('success', 'Profile updated successfully');
    }
}