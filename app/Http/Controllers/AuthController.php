<?php
namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Customer;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        // Find the customer by email
        $customer = Customer::where('email', $credentials['email'])->first();

        if ($customer) {
            // Find the user by customer ID
            $user = User::where('cus_id', $customer->cus_id)->first();

            if ($user && Hash::check($credentials['password'], $user->password)) {
                // Log in the user
                Auth::login($user);
                $request->session()->regenerate();

                if ($user->role === 'Customer') {
                    return redirect()->intended('/customer/dashboard');
                } else if ($user->role === 'Employee') {
                    return redirect()->intended('/employee/dashboard');
                }
            }
        }

        // Check if it's an employee
        $employee = Employee::where('email', $credentials['email'])->first();

        if ($employee) {
            // Find the user by employee ID
            $user = User::where('emp_id', $employee->emp_id)->first();

            if ($user->password) {
                // Log in the user
                Auth::login($user);
                $request->session()->regenerate();

                return redirect()->intended('/admin/dashboard');
            }
        }

        return back()->withErrors([
            'email' => 'Invalid credentials',
        ])->withInput($request->only('email'));
    }

    public function register(Request $request)
    {
        $request->validate([
            'email' => 'required|email|unique:customers,email',
            'password' => 'required|min:6',
            'first_name' => 'required',
            'middle_name' => 'nullable',
            'last_name' => 'required',
            'birthdate' => 'required',
            'phone' => 'required',
            'address' => 'required',
        ]);

        try {
            $customer = Customer::create([
                'fname' => $request->first_name,
                'mname' => $request->middle_name,
                'lname' => $request->last_name,
                'birthdate' => $request->birthdate,
                'contact_number' => $request->phone,
                'email' => $request->email,
                'address' => $request->address
            ]);

            if (!$customer) {
                throw new \Exception('Failed to create customer');
            }

            // Log the created customer ID for debugging
            \Log::info('Customer created with ID: ' . $customer->cus_id);

            $user = User::create([
                'username' => $request->first_name,
                'password' => Hash::make($request->password),
                'role' => 'Customer',
                'cus_id' => $customer->cus_id,
                'emp_id' => null
            ]);

            if (!$user) {
                throw new \Exception('Failed to create user');
            }

            // Log the created user ID for debugging
            \Log::info('User created with ID: ' . $user->user_id);

            return redirect('/login')->with('success', 'Registration successful!');
              
        } catch (\Exception $e) {
            \Log::error('Registration failed: ' . $e->getMessage());
            return back()->withInput()->withErrors(['error' => 'Registration failed. Please try again.']);
        }
    }

    public function logout()
    {
        Auth::logout();
        return redirect('/login');
    }
    
}