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
        'fname'             => 'required|string|max:255',
        'mname'            => 'nullable|string|max:255',
        'lname'             => 'required|string|max:255',
        'birthdate'         => 'required|date',
        'email'            => 'required|email|max:255',
        'contact_number'   => 'required|string|max:20',
        'address' => 'nullable|string'
    ]);

    // Update User fields
    $customer->update([
        'fname'           => $validatedData['fname'],
        'mname'          => $validatedData['mname'],
        'lname'          => $validatedData['lname'],
        'birthdate'      => $validatedData['birthdate'],
        'email'          => $validatedData['email'],
        'contact_number' => $validatedData['contact_number'],
        'address'        => $validatedData['address']
    ]);
    return redirect()->route('customer.profile')->with('success', 'Profile updated successfully');
}

}