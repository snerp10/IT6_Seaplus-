<?php
namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Customer;
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


        if (Auth::attempt($credentials)) {
            // Regenerate session
            $request->session()->regenerate();
            
            if(Auth::user()->role === 'Customer') {
                return redirect()->intended('/customer/dashboard');
            } else if(Auth::user()->role === 'Admin') {
                return redirect()->intended('/admin/dashboard');
            } else if(Auth::user()->role === 'Employee') {
                return redirect()->intended('/employee/dashboard');
            }
            // Redirect to dashboard
            return redirect()->intended('/dashboard');
        }
    
        return back()->withErrors([
            'email' => 'Invalid credentials',
        ])->withInput($request->only('email'));
    }

    public function register(Request $request)
    {
        $request->validate([
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
            'first_name' => 'required',
            'last_name' => 'required',
            'phone' => 'required',
            'address' => 'required'
        ]);

        try {
            $user = User::create([
                'name' => $request->first_name . ' ' . $request->last_name,
                'email' => $request->email,
                'username' => strtolower($request->first_name),
                'password' => Hash::make($request->password),
                'role' => 'Customer',
                'contact_number' => $request->phone,
                'status' => 'Active'
            ]);

            if (!$user) {
                throw new \Exception('Failed to create user');
            }

        
            $customer = Customer::create([
                'user_id' => $user->user_id, // Changed from $user->id to $user->user_id
                'address' => $request->address,
                'customer_type' => 'Regular'
            ]);

            Auth::login($customer);
            return redirect('/login')->with('success', 'Registration successful!');
              
            } catch (\Exception $e) {
                return back()->withInput()->withErrors(['error' => 'Registration failed. Please try again.']);
            }
        }

        public function logout()
        {
            Auth::logout();
            return redirect('/login');
        }
    
}