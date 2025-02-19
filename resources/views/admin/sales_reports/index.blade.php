@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Sales Reports</h1>
    <a href="{{ route('sales_reports.create') }}" class="btn btn-primary mb-3">Create Sales Report</a>
    <table class="table">
        <thead>
            <tr>
                <th>Date Generated</th>
                <th>Total Sales</th>
                <th>Total Expenses</th>
                <th>Net Profit</th>
                <th>Report Type</th>
                <th>Generated By</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($reports as $report)
            <tr>
                <td>{{ $report->date_generated }}</td>
                <td>₱{{ number_format($report->total_sales, 2) }}</td>
                <td>₱{{ number_format($report->total_expenses, 2) }}</td>
                <td>₱{{ number_format($report->net_profit, 2) }}</td>
                <td>{{ ucfirst($report->report_type) }}</td>
                <td>{{ $report->user->name }}</td>
                <td>
                    <a href="{{ route('sales_reports.show', $report->report_id) }}" class="btn btn-info">View</a>
                    <a href="{{ route('sales_reports.edit', $report->report_id) }}" class="btn btn-warning">Edit</a>
                    <form action="{{ route('sales_reports.destroy', $report->report_id) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Delete</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
