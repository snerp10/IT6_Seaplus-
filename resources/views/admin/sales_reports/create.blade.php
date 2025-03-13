@extends('layouts.admin')

@section('title', 'Create Sales Report')

@section('admin.content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-file-alt text-dark mr-2"></i> Create New Sales Report
        </h1>
        <a href="{{ route('admin.sales_reports.index') }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left"></i> Back to Reports
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text">Report Configuration</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.sales_reports.store') }}" method="POST">
                @csrf
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="name">Report Name<span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" value="{{ old('name') }}" required>
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
                                <option value="daily" {{ old('report_type') == 'daily' ? 'selected' : '' }}>Daily Sales</option>
                                <option value="weekly" {{ old('report_type') == 'weekly' ? 'selected' : '' }}>Weekly Summary</option>
                                <option value="monthly" {{ old('report_type') == 'monthly' ? 'selected' : '' }}>Monthly Summary</option>
                                <option value="quarterly" {{ old('report_type') == 'quarterly' ? 'selected' : '' }}>Quarterly Analysis</option>
                                <option value="yearly" {{ old('report_type') == 'yearly' ? 'selected' : '' }}>Yearly Overview</option>
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
                                   id="date_from" name="date_from" value="{{ old('date_from', now()->subDays(30)->format('Y-m-d')) }}" required>
                            @error('date_from')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="date_to">Date To<span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('date_to') is-invalid @enderror" 
                                   id="date_to" name="date_to" value="{{ old('date_to', now()->format('Y-m-d')) }}" required>
                            @error('date_to')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                
                <div class="form-group mb-3">
                    <label for="description">Description</label>
                    <textarea class="form-control @error('description') is-invalid @enderror" 
                              id="description" name="description" rows="3">{{ old('description') }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="alert alert-info">
                    <i class="fas fa-info-circle mr-1"></i> Financial metrics like Total Sales, Expenses, and Net Profit will be automatically calculated based on your selected date range.
                </div>
                
                <div class="form-group text-center">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save mr-1"></i> Create Report
                    </button>
                    <a href="{{ route('admin.sales_reports.index') }}" class="btn btn-secondary ml-2">
                        <i class="fas fa-times mr-1"></i> Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
