@extends('layouts.admin')

@section('title', 'Payments Management')

@section('admin.content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-money-bill-wave text-dark mr-2"></i> Payments Management
        </h1>
        <div>
            <a href="{{ route('admin.payments.reports') }}" class="btn btn-info btn-sm mr-2">
                <i class="fas fa-chart-bar"></i> Payment Reports
            </a>
            <a href="{{ route('admin.payments.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-check-circle"></i> Record Payment
            </a>
        </div>
    </div>

    <!-- Status Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
            <i class="fas fa-exclamation-circle mr-2"></i> {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <!-- Payment Statistics -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Payments</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">₱{{ number_format($totalPaid, 2) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-money-bill-wave fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Pending</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">₱{{ number_format($pendingAmount, 2) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Cash Payments</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $paymentMethods['cash'] ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-cash-register fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Transactions</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ array_sum($paymentMethods) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exchange-alt fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Filter Payments</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.payments.index') }}" method="GET" class="mb-0">
                <div class="row">
                    <div class="col-md-3">
                        <label for="status" class="form-label small">Payment Status</label>
                        <select name="status" id="status" class="form-control form-control-sm" onchange="this.form.submit()">
                            <option value="">All Statuses</option>
                            <option value="pending" {{ request('status') == 'Pending' ? 'selected' : '' }}>Pending</option>
                            <option value="paid" {{ request('status') == 'Paid' ? 'selected' : '' }}>Paid</option>
                            <option value="partially paid" {{ request('status') == 'Partially Paid' ? 'selected' : '' }}>Partially Paid</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="payment_method" class="form-label small">Payment Method</label>
                        <select name="payment_method" id="payment_method" class="form-control form-control-sm" onchange="this.form.submit()">
                            <option value="">All Methods</option>
                            <option value="cash" {{ request('payment_method') == 'Cash' ? 'selected' : '' }}>Cash</option>
                            <option value="cash on Delivery" {{ request('payment_method') == 'Cash on Delivery' ? 'selected' : '' }}>Cash on Delivery</option>
                            <option value="gcash" {{ request('payment_method') == 'Gcash' ? 'selected' : '' }}>GCash</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="date_from" class="form-label small">Date From</label>
                        <input type="date" name="date_from" id="date_from" class="form-control form-control-sm" value="{{ request('date_from') }}">
                    </div>
                    <div class="col-md-2">
                        <label for="date_to" class="form-label small">Date To</label>
                        <input type="date" name="date_to" id="date_to" class="form-control form-control-sm" value="{{ request('date_to') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label d-block small">&nbsp;</label>
                        <div class="d-flex">
                            <button type="submit" class="btn btn-primary btn-sm mr-2">
                                <i class="fas fa-search"></i> Search
                            </button>
                            <a href="{{ route('admin.payments.index') }}" class="btn btn-secondary btn-sm">
                                <i class="fas fa-undo"></i> Reset
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Payments Table Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-list"></i> Payment List
            </h6>
            <div>
                <a href="{{ route('admin.payments.export') }}" class="btn btn-success btn-sm">
                    <i class="fas fa-file-export"></i> Export
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover">
                    <thead class="thead-dark">
                        <tr>
                            <th>ID</th>
                            <th>Reference #</th>
                            <th>Order #</th>
                            <th>Customer</th>
                            <th>Amount</th>
                            <th>Method</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($payments as $payment)
                        <tr>
                            <td>{{ $payment->pay_id }}</td>
                            <td>{{ $payment->reference_number ?? 'N/A' }}</td>
                            <td>
                                @if($payment->order)
                                    <a href="{{ route('admin.orders.show', $payment->order->order_id) }}">
                                        #{{ $payment->order->order_id }}
                                    </a>
                                @else
                                    N/A
                                @endif
                            </td>
                            <td>
                                @if($payment->customer)
                                    {{ $payment->customer->fname }}
                                @else
                                    N/A
                                @endif
                            </td>
                            <td class="text-right">₱{{ number_format($payment->amount_paid + $payment->delivery_cost, 2) }}</td>
                            <td>
                                <span class="badge bg-{{ 
                                    $payment->pay_method == 'Cash' ? 'success' : 
                                    ($payment->pay_method == 'Gcash' ? 'info' : 
                                    ($payment->pay_method == 'credit_card' ? 'primary' : 'secondary')) 
                                }} text-white">
                                    {{ ucfirst(str_replace('_', ' ', $payment->pay_method)) }}
                                </span>
                            </td>
                            <td>{{ \Carbon\Carbon::parse($payment->pay_date)->format('M d, Y') }}</td>
                            <td>
                                <span class="badge bg-{{ 
                                    $payment->pay_status == 'Paid' ? 'success' : 
                                    ($payment->pay_status == 'Partially Paid' ? 'warning' : 
                                    ($payment->pay_status == 'Pending' ? 'info' : 'danger')) 
                                }} text-white">
                                    {{ ucfirst($payment->pay_status) }}
                                </span>
                            </td>
                            <td>
                                <div class="btn-group">
                                    <a href="{{ route('admin.payments.show', $payment->pay_id) }}" class="btn btn-sm btn-info" title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.payments.edit', $payment->pay_id) }}" class="btn btn-sm btn-primary" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-danger delete-btn" 
                                            data-toggle="modal" 
                                            data-target="#deleteModal{{ $payment->pay_id }}"
                                            data-payment-id="{{ $payment->pay_id }}"
                                            data-payment-ref="{{ $payment->reference_number ?? 'Payment #'.$payment->pay_id }}" 
                                            title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>

                                <!-- Delete Modal -->
                                <div class="modal fade" id="deleteModal{{ $payment->pay_id }}" tabindex="-1" role="dialog" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header bg-danger text-white">
                                                <h5 class="modal-title">
                                                    <i class="fas fa-exclamation-triangle"></i> Confirm Delete
                                                </h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <p>Are you sure you want to delete this payment? This action cannot be undone.</p>
                                                <p><strong>Payment:</strong> {{ $payment->reference_number ?? 'Payment #'.$payment->pay_id }}</p>
                                                <p><strong>Amount:</strong> ₱{{ number_format($payment->amount_paid, 2) }}</p>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                                <form action="{{ route('admin.payments.destroy', $payment->pay_id) }}" method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger">Delete</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center">No payments found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $payments->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    $(function() {
        // Initialize tooltips
        $('[title]').tooltip();
        
        // Initialize DataTable for better search and sorting
        $('.table').DataTable({
            "paging": false,
            "lengthChange": false,
            "searching": false,
            "ordering": true,
            "info": false,
            "autoWidth": false,
            "responsive": true
        });
    });
</script>
@endpush
@endsection
