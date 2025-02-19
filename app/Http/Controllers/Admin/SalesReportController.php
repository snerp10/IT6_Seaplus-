<?php

namespace App\Http\Controllers;

use App\Models\SalesReport;
use Illuminate\Http\Request;

class SalesReportController extends Controller
{
    public function index()
    {
        $reports = SalesReport::all();
        return view('sales_reports.index', compact('reports'));
    }

    public function create()
    {
        return view('sales_reports.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'date_generated' => 'required|date',
            'total_sales' => 'required|numeric',
            'total_expenses' => 'required|numeric',
            'net_profit' => 'required|numeric',
            'report_type' => 'required|in:daily,weekly,monthly',
            'generated_by' => 'required|exists:users,id',
        ]);

        SalesReport::create($request->all());

        return redirect()->route('sales_reports.index')->with('success', 'Sales report created successfully.');
    }

    public function show(SalesReport $salesReport)
    {
        return view('sales_reports.show', compact('salesReport'));
    }

    public function edit(SalesReport $salesReport)
    {
        return view('sales_reports.edit', compact('salesReport'));
    }

    public function update(Request $request, SalesReport $salesReport)
    {
        $request->validate([
            'date_generated' => 'required|date',
            'total_sales' => 'required|numeric',
            'total_expenses' => 'required|numeric',
            'net_profit' => 'required|numeric',
            'report_type' => 'required|in:daily,weekly,monthly',
            'generated_by' => 'required|exists:users,id',
        ]);

        $salesReport->update($request->all());

        return redirect()->route('sales_reports.index')->with('success', 'Sales report updated successfully.');
    }

    public function destroy(SalesReport $salesReport)
    {
        $salesReport->delete();

        return redirect()->route('sales_reports.index')->with('success', 'Sales report deleted successfully.');
    }
}
