<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Http\Controllers\Controller;
use App\Models\Delivery;

class AdminOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $orders = \App\Models\Order::all();
        return view('admin.orders.index', compact('orders'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.orders.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'customer_name' => ['required', 'string', 'max:255'],
            'customer_email' => ['required', 'string', 'email', 'max:255'],
            'customer_contact_number' => ['required', 'string', 'max:255'],
            'order_date' => ['required', 'date'],
            'total_amount' => ['required', 'numeric'],
            'payment_status' => ['required', 'string'],
            'delivery_status' => ['required', 'string'],
        ]);

        $order = \App\Models\Order::create([
            'customer_name' => $request->input('customer_name'),
            'customer_email' => $request->input('customer_email'),
            'customer_contact_number' => $request->input('customer_contact_number'),
            'order_date' => $request->input('order_date'),
            'total_amount' => $request->input('total_amount'),
            'payment_status' => $request->input('payment_status'),
            'delivery_status' => $request->input('delivery_status'),
        ]);

        return redirect()->route('admin.orders.index')
            ->with('success', 'Order created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $order = \App\Models\Order::findOrFail($id);
        return view('admin.orders.show', compact('order'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $order = \App\Models\Order::findOrFail($id);
        return view('admin.orders.edit', compact('order'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'customer_name' => ['required', 'string', 'max:255'],
            'customer_email' => ['required', 'string', 'email', 'max:255'],
            'customer_contact_number' => ['required', 'string', 'max:255'],
            'order_date' => ['required', 'date'],
            'total_amount' => ['required', 'numeric'],
            'payment_status' => ['required', 'string'],
            'delivery_status' => ['required', 'string'],
        ]);

        $order = \App\Models\Order::findOrFail($id);

        $order->customer_name = $request->input('customer_name');
        $order->customer_email = $request->input('customer_email');
        $order->customer_contact_number = $request->input('customer_contact_number');
        $order->order_date = $request->input('order_date');
        $order->total_amount = $request->input('total_amount');
        $order->payment_status = $request->input('payment_status');
        $order->delivery_status = $request->input('delivery_status');

        $order->save();

        return redirect()->route('admin.orders.index')
            ->with('success', 'Order updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $order = \App\Models\Order::findOrFail($id);
        $order->delete();

        return redirect()->route('admin.orders.index')
            ->with('success', 'Order deleted successfully.');
    }
}
