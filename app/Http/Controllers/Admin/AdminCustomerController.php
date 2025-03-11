<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class AdminCustomerController extends Controller
{
    /**
     * Display a listing of all customers with search and filter functionality.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $query = Customer::query();
        
        // Add search functionality
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('fname', 'like', "%{$search}%")
                  ->orWhere('lname', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('contact_number', 'like', "%{$search}%");
            });
        }
        
        // Order by registration date (newest first) and paginate
        $customers = $query->orderBy('created_at', 'desc')->paginate(15);
        
        // Get statistics for the dashboard
        $totalCustomers = Customer::count();
        
        return view('admin.customers.index', compact(
            'customers',
            'totalCustomers'
        ));
    }

    /**
     * Show the form for creating a new customer.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('admin.customers.create');
    }

    /**
     * Store a newly created customer in storage.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'fname' => 'required|string|max:100',
            'mname' => 'nullable|string|max:100',
            'lname' => 'required|string|max:100',
            'email' => 'required|email|unique:customers,email|max:100',
            'contact_number' => 'required|string|max:15|unique:customers,contact_number', 
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'province' => 'nullable|string|max:100',
        ], [
            'email.unique' => 'This email address is already registered with another customer.'
        ]);
        
        DB::beginTransaction();
        
        try {
            // Create the customer
            $customer = Customer::create($validated);
            
            // Log the successful creation
            Log::info('Customer created successfully', ['customer_id' => $customer->cus_id]);
            
            DB::commit();
            return redirect()->route('admin.customers.index')
                ->with('success', 'Customer created successfully.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create customer', [
                'error' => $e->getMessage(),
                'data' => $request->except(['_token'])
            ]);
            
            return back()->with('error', 'Failed to create customer: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified customer with order history and metrics.
     *
     * @param Customer $customer
     * @return \Illuminate\View\View
     */
    public function show(Customer $customer)
    {
        try {
            // Get customer order history
            $orders = $customer->orders()->orderBy('order_date', 'desc')->get();
            
            // Calculate customer metrics
            $totalOrders = $orders->count();
            $totalSpent = $orders->where('order_status', 'Completed')->sum('total_amount');
            $avgOrderValue = $totalOrders > 0 ? $totalSpent / $totalOrders : 0;
            
            return view('admin.customers.show', compact(
                'customer', 
                'orders', 
                'totalOrders',
                'totalSpent',
                'avgOrderValue'
            ));
        } catch (\Exception $e) {
            Log::error('Error viewing customer details', [
                'customer_id' => $customer->cus_id,
                'error' => $e->getMessage()
            ]);
            
            return redirect()->route('admin.customers.index')
                ->with('error', 'Error loading customer details: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified customer.
     *
     * @param Customer $customer
     * @return \Illuminate\View\View
     */
    public function edit(Customer $customer)
    {
        return view('admin.customers.edit', compact('customer'));
    }

    /**
     * Update the specified customer in storage.
     *
     * @param Request $request
     * @param Customer $customer
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'fname' => 'required|string|max:100',
            'mname' => 'nullable|string|max:100',
            'lname' => 'required|string|max:100',
            'email' => [
                'required',
                'email',
                'max:100',
                Rule::unique('customers')->ignore($customer->cus_id, 'cus_id')
            ],
            'contact_number' => [
                'required',
                'string',
                'max:15',
                Rule::unique('customers')->ignore($customer->cus_id, 'cus_id')
            ],
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'province' => 'nullable|string|max:100',
        ]);
        
        DB::beginTransaction();
        
        try {
            // Update the customer
            $customer->update($validated);
            
            DB::commit();
            return redirect()->route('admin.customers.show', $customer->cus_id)
                ->with('success', 'Customer updated successfully.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update customer', [
                'customer_id' => $customer->cus_id,
                'error' => $e->getMessage()
            ]);
            
            return back()->with('error', 'Failed to update customer: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified customer from storage.
     *
     * @param Customer $customer
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Customer $customer)
    {
        // Check if customer has orders
        $orderCount = $customer->orders()->count();
        
        if ($orderCount > 0) {
            return back()->with('error', 'Cannot delete customer with existing orders.');
        }
        
        try {
            $customerName = $customer->fname . ' ' . $customer->lname;
            $customerId = $customer->cus_id;
            
            $customer->delete();
            
            Log::info('Customer deleted', [
                'customer_id' => $customerId,
                'customer_name' => $customerName
            ]);
            
            return redirect()->route('admin.customers.index')
                ->with('success', 'Customer deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to delete customer', [
                'customer_id' => $customer->cus_id,
                'error' => $e->getMessage()
            ]);
            
            return back()->with('error', 'Failed to delete customer: ' . $e->getMessage());
        }
    }
    
    /**
     * Export customers data to CSV/Excel.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function export(Request $request)
    {
        try {
            $query = Customer::query();
            
            // Apply filters if any
            if ($request->has('search') && !empty($request->search)) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('fname', 'like', "%{$search}%")
                      ->orWhere('lname', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                      // Removed phone_no search since the column doesn't exist
                });
            }
            
            $customers = $query->get();
            
            // In a real implementation, you'd generate a CSV/Excel file here
            // For now, return a message with count 
            return redirect()->back()->with('success', 'Export functionality coming soon! ' . $customers->count() . ' customers would be exported.');
        } catch (\Exception $e) {
            Log::error('Failed to export customers', ['error' => $e->getMessage()]);
            return back()->with('error', 'Failed to export customers: ' . $e->getMessage());
        }
    }
}
