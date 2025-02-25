<?php

namespace App\Http\Controllers\Admin;

use App\Models\Payment;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdminPaymentController extends Controller
{
    public function index()
    {
        $payments = \App\Models\Payment::all();
        return view('admin.payments.index', compact('payments'));
    }

    public function create()
    {
        return view('admin.payments.create');
    }

    public function store(Request $request)
    {
        $payment = \App\Models\Payment::create($request->all());
        return redirect()->route('admin.payments.index');
    }

    public function edit(\App\Models\Payment $payment)
    {
        return view('admin.payments.edit', compact('payment'));
    }

    public function update(Request $request, \App\Models\Payment $payment)
    {
        $payment->update($request->all());
        return redirect()->route('admin.payments.index');
    }

    public function destroy(\App\Models\Payment $payment)
    {
        $payment->delete();
        return redirect()->route('admin.payments.index');
    }
}
