<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function show()
    {
        $user = auth()->user();
        $customer = $user->customer;
        return view('customer.profile.index', compact('user', 'customer'));
    }


    public function update(Request $request)
{   
    $user = auth()->user();
    $customer = $user->customer;

    // Validate all fields
    $validatedData = $request->validate([
        'name'             => 'required|string|max:255',
        'email'            => 'required|email|max:255',
        'contact_number'   => 'required|string|max:20',
        'address' => 'nullable|string'
    ]);

    // Update User fields
    $user->update([
        'name'           => $validatedData['name'],
        'email'          => $validatedData['email'],
        'contact_number' => $validatedData['contact_number']
    ]);

    // Update Customer field
    $customer->update([
        'address' => $validatedData['address'],
    ]);

    return redirect()->route('customer.profile')->with('success', 'Profile updated successfully');
}

}