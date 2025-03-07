@extends('layouts.admin')

@section('title', 'Edit Sales Report')

@section('admin.content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-edit text-primary mr-2"></i> Edit Sales Report
        </h1>
        <a href="{{ route('admin.sales_reports.show', $report->report_id) }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left"></i> Back to Report
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Edit "{{ $report->name }}"</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.sales_reports.update', $report->report_id) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="name">Report Name<span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" value="{{ old('name', $report->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="report_type">Report Type<span class="text-danger">*</span></label>
                            <select class="form-control @error('report_type') is-invalid @enderror" 
                                    id="report_type" name="report_type" required>
                                <option value="daily" {{ old('report_type', $report->report_type) == 'daily' ? 'selected' : '' }}>Daily Sales</option>
                                <option value="weekly" {{ old('report_type', $report->report_type) == 'weekly' ? 'selected' : '' }}>Weekly Summary</option>
                                <option value="monthly" {{ old('report_type', $report->report_type) == 'monthly' ? 'selected' : '' }}>Monthly Summary</option>
                                <option value="quarterly" {{ old('report_type', $report->report_type) == 'quarterly' ? 'selected' : '' }}>Quarterly Analysis</option>
                                <option value="yearly" {{ old('report_type', $report->report_type) == 'yearly' ? 'selected' : '' }}>Yearly Overview</option>
                            </select>
                            @error('report_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="date_from">Date From<span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('date_from') is-invalid @enderror" 
                                   id="date_from" name="date_from" value="{{ old('date_from', $report->date_from->format('Y-m-d')) }}" required>
                            @error('date_from')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="date_to">Date To<span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('date_to') is-invalid @enderror" 
                                   id="date_to" name="date_to" value="{{ old('date_to', $report->date_to->format('Y-m-d')) }}" required>
                            @error('date_to')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                
                <div class="form-group mb-3">
                    <label for="description">Description</label>
                    <textarea class="form-control @error('description') is-invalid @enderror" 
                              id="description" name="description" rows="3">{{ old('description', $report->description) }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="total_sales">Total Sales<span class="text-danger">*</span></label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">₱</span>
                                </div>
                                <input type="number" step="0.01" min="0" class="form-control @error('total_sales') is-invalid @enderror" 
                                       id="total_sales" name="total_sales" value="{{ old('total_sales', $report->total_sales) }}" required>
                                @error('total_sales')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="total_expenses">Total Expenses<span class="text-danger">*</span></label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">₱</span>
                                </div>
                                <input type="number" step="0.01" min="0" class="form-control @error('total_expenses') is-invalid @enderror" 
                                       id="total_expenses" name="total_expenses" value="{{ old('total_expenses', $report->total_expenses) }}" required>
                                @error('total_expenses')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="net_profit">Net Profit<span class="text-danger">*</span></label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">₱</span>
                                </div>
                                <input type="number" step="0.01" class="form-control @error('net_profit') is-invalid @enderror" 
                                       id="net_profit" name="net_profit" value="{{ old('net_profit', $report->net_profit) }}" required>
                                @error('net_profit')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="form-group text-center">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save mr-1"></i> Update Report
                    </button>
                    <a href="{{ route('admin.sales_reports.show', $report->report_id) }}" class="btn btn-secondary ml-2">
                        <i class="fas fa-times mr-1"></i> Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Auto-calculate net profit when total sales or expenses change
    $(function() {
        $('#total_sales, #total_expenses').on('input', function() {
            const totalSales = parseFloat($('#total_sales').val()) || 0;
            const totalExpenses = parseFloat($('#total_expenses').val()) || 0;
            const netProfit = totalSales - totalExpenses;
            $('#net_profit').val(netProfit.toFixed(2));
        });
    });
</script>
@endpush
@endsection
