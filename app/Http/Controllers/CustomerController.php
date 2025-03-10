<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class CustomerController extends Controller
{
    public function show()
    {
        $customer = Auth::user()->customer;
        return view('customer.profile.show', compact('customer'));
    }
    
    public function update(Request $request)
    {
        $customer = Auth::user()->customer;
        
        $validated = $request->validate([
            'fname' => 'required|string|max:100',
            'lname' => 'required|string|max:100',
            'email' => [
                'required',
                'email',
                Rule::unique('customers', 'email')->ignore($customer->cus_id, 'cus_id')
            ],
            'phone' => 'required|string|max:15',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'province' => 'nullable|string|max:100',
        ]);
        
        $customer->update($validated);
        
        return redirect()->route('customer.profile')
            ->with('success', 'Profile updated successfully');
    }
}