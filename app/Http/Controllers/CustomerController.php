<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class CustomerController extends Controller
{
    /**
     * Show the customer profile.
     */
    public function show()
    {
        $customer = Customer::where('cus_id', auth()->user()->cus_id)->first();
        return view('customer.profile', compact('customer'));
    }

    /**
     * Update the customer profile.
     */
    public function update(Request $request)
    {
        $request->validate([
            'fname' => 'required|string|max:255',
            'mname' => 'nullable|string|max:255',
            'lname' => 'required|string|max:255',
            'gender' => 'nullable|string|in:Male,Female,Other',
            'birthdate' => 'required|date',
            'contact_number' => 'required|string|max:15',
            'street' => 'nullable|string|max:255',
            'barangay' => 'nullable|string|max:255',
            'city' => 'required|string|max:255',
            'province' => 'required|string|max:255',
            'postal_code' => 'nullable|string|max:10',
        ]);

        $customer = Customer::findOrFail(auth()->user()->cus_id);
        
        $customer->update([
            'fname' => $request->fname,
            'mname' => $request->mname,
            'lname' => $request->lname,
            'gender' => $request->gender,
            'birthdate' => $request->birthdate,
            'contact_number' => $request->contact_number,
            'street' => $request->street,
            'barangay' => $request->barangay,
            'city' => $request->city,
            'province' => $request->province,
            'postal_code' => $request->postal_code,
        ]);

        // Update password if provided
        if ($request->filled('password')) {
            $request->validate([
                'password' => 'required|string|min:8|confirmed',
            ]);
            
            $user = $customer->user;
            $user->password = Hash::make($request->password);
            $user->save();
        }

        return redirect()->route('customer.profile')
                         ->with('success', 'Profile updated successfully.');
    }
}