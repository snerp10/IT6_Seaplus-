@extends('layouts.admin')

@section('title', 'Payment Details')

@section('admin.content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-receipt text-primary"></i> Payment Details
        </h1>
        <div>
            <a href="{{ route('admin.payments.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Back to Payments
            </a>
            <a href="{{ route('admin.payments.edit', $payment->pay_id) }}" class="btn btn-primary btn-sm">
                <i class="fas fa-edit"></i> Edit Payment
            </a>
            @if($payment->order)
                <a href="{{ route('admin.orders.show', $payment->order->order_id) }}" class="btn btn-info btn-sm">
                    <i class="fas fa-shopping-cart"></i> View Order
                </a>
            @endif
            <button class="btn btn-secondary btn-sm" onclick="window.print();">
                <i class="fas fa-print"></i> Print
            </button>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <!-- Payment Overview -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Amount Paid</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">₱{{ number_format($payment->amount_paid, 2) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-money-bill-wave fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-{{ 
                $payment->pay_status == 'Paid' ? 'success' : 
                ($payment->pay_status == 'Partially Paid' ? 'warning' : 
                ($payment->pay_status == 'Refunded' ? 'info' : 
                ($payment->pay_status == 'Failed' ? 'danger' : 'secondary'))) 
            }} shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-{{ 
                                $payment->pay_status == 'Paid' ? 'success' : 
                                ($payment->pay_status == 'Partially Paid' ? 'warning' : 
                                ($payment->pay_status == 'Refunded' ? 'info' : 
                                ($payment->pay_status == 'Failed' ? 'danger' : 'secondary'))) 
                            }} text-uppercase mb-1">Status</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $payment->pay_status }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-{{ 
                                $payment->pay_status == 'Paid' ? 'check-circle' : 
                                ($payment->pay_status == 'Partially Paid' ? 'clock' : 
                                ($payment->pay_status == 'Refunded' ? 'undo' : 
                                ($payment->pay_status == 'Failed' ? 'times-circle' : 'question-circle'))) 
                            }} fa-2x text-gray-300"></i>
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
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Method</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ ucfirst($payment->pay_method) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-{{ $payment->pay_method == 'Cash' ? 'money-bill-alt' : ($payment->pay_method == 'GCash' ? 'mobile-alt' : 'credit-card') }} fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-dark shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-dark text-uppercase mb-1">Payment Date</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ \Carbon\Carbon::parse($payment->pay_date)->format('M d, Y') }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Payment Details Card -->
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text">
                        <i class="fas fa-file-invoice-dollar mr-1"></i> Payment Information
                    </h6>
                    <div class="dropdown no-arrow">
                        <a class="dropdown-toggle" href="#" role="button" id="paymentDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in" aria-labelledby="paymentDropdown">
                            <a class="dropdown-item" href="{{ route('admin.payments.edit', $payment->pay_id) }}">
                                <i class="fas fa-edit fa-sm fa-fw mr-2 text-gray-400"></i> Edit
                            </a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item text-danger" href="#" data-toggle="modal" data-target="#deletePaymentModal">
                                <i class="fas fa-trash fa-sm fa-fw mr-2 text-danger"></i> Delete
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="small text-muted mb-1">Reference Number</div>
                            <div class="h5 mb-0 font-weight-bold">{{ $payment->reference_number ?? 'N/A' }}</div>
                        </div>
                        <div class="col-md-6">
                            <div class="small text-muted mb-1">Invoice Number</div>
                            <div class="h5 mb-0 font-weight-bold">{{ $payment->invoice_number ?? 'N/A' }}</div>
                        </div>
                    </div>
                    
                    <hr class="my-4">
                    
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="small text-muted mb-1">Amount Paid</div>
                            <div class="h4 mb-0 font-weight-bold text-primary">₱{{ number_format($payment->amount_paid, 2) }}</div>
                        </div>
                        <div class="col-md-6">
                            @if($payment->outstanding_balance > 0)
                            <div class="small text-muted mb-1">Outstanding Balance</div>
                            <div class="h5 mb-0 font-weight-bold text-danger">₱{{ number_format($payment->outstanding_balance + ($payment->order->delivery ? $payment->order->delivery->delivery_cost : 0), 2) }}</div>
                            @elseif($payment->change_amount > 0)
                            <div class="small text-muted mb-1">Change Amount</div>
                            <div class="h5 mb-0 font-weight-bold text-success">₱{{ number_format($payment->change_amount, 2) }}</div>
                            @endif
                        </div>
                    </div>

                    <hr class="my-4">

                    <div class="row">
                        <div class="col-md-6">
                            <div class="small text-muted mb-1">Payment Status</div>
                            <div>
                                <span class="badge badge-{{ 
                                    $payment->pay_status == 'Paid' ? 'success' : 
                                    ($payment->pay_status == 'Partially Paid' ? 'warning' : 
                                    ($payment->pay_status == 'Refunded' ? 'info' : 
                                    ($payment->pay_status == 'Failed' ? 'danger' : 'secondary'))) 
                                }} px-3 py-2">{{ ucfirst($payment->pay_status) }}</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="small text-muted mb-1">Payment Method</div>
                            <div>
                                <span class="badge badge-{{ 
                                    $payment->pay_method == 'Cash' ? 'success' : 
                                    ($payment->pay_method == 'GCash' ? 'info' : 'secondary') 
                                }} px-3 py-2">
                                    <i class="fas fa-{{ 
                                        $payment->pay_method == 'Cash' ? 'money-bill-alt' : 
                                        ($payment->pay_method == 'GCash' ? 'mobile-alt' : 'credit-card') 
                                    }} mr-1"></i>
                                    {{ ucfirst($payment->pay_method) }}
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    @if($payment->notes)
                    <hr class="my-4">
                    <div class="row">
                        <div class="col-12">
                            <div class="small text-muted mb-1">Notes</div>
                            <div class="p-3 bg-light rounded">
                                {{ $payment->notes }}
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Customer & Order Information -->
        <div class="col-lg-6">
            <!-- Customer Information Card -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text">
                        <i class="fas fa-user mr-1"></i> Customer Information
                    </h6>
                </div>
                <div class="card-body">
                    @if($payment->customer)
                        <div class="row mb-4">
                            <div class="col-md-2 text-center">
                                <i class="fas fa-user-circle fa-4x text-gray-300"></i>
                            </div>
                            <div class="col-md-10">
                                <h5 class="font-weight-bold">{{ $payment->customer->fname }} {{ $payment->customer->lname }}</h5>
                                <p class="text-muted mb-0">
                                    <i class="fas fa-envelope mr-1"></i> {{ $payment->customer->email ?? 'N/A' }}
                                </p>
                                <p class="text-muted mb-0">
                                    <i class="fas fa-phone mr-1"></i> {{ $payment->customer->phone_no ?? 'N/A' }}
                                </p>
                            </div>
                        </div>
                        @if($payment->customer->street || $payment->customer->city || $payment->customer->province)
                        <div class="row">
                            <div class="col-12">
                                <div class="card bg-light">
                                    <div class="card-body p-3">
                                        <h6 class="font-weight-bold mb-2">Address</h6>
                                        <p class="mb-0">{{ $payment->customer->street ?? '' }}<br>
                                        {{ $payment->customer->city ?? '' }}{{ $payment->customer->city && $payment->customer->province ? ', ' : '' }}{{ $payment->customer->province ?? '' }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-user-slash fa-4x text-gray-300 mb-3"></i>
                            <p class="text-muted">No customer information available</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Order Information Card -->
            @if($payment->order)
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text">
                        <i class="fas fa-shopping-cart mr-1"></i> Order Information
                    </h6>
                    <a href="{{ route('admin.orders.show', $payment->order->order_id) }}" class="btn btn-sm btn-info">
                        View Order
                    </a>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="small text-muted mb-1">Order ID</div>
                            <div class="h5 mb-0 font-weight-bold">#{{ $payment->order->order_id }}</div>
                        </div>
                        <div class="col-md-6">
                            <div class="small text-muted mb-1">Order Date</div>
                            <div class="h5 mb-0">{{ \Carbon\Carbon::parse($payment->order->order_date)->format('M d, Y') }}</div>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="small text-muted mb-1">Order Status</div>
                            <span class="badge badge-{{ 
                                $payment->order->order_status == 'Completed' ? 'success' : 
                                ($payment->order->order_status == 'Processing' ? 'warning' : 
                                ($payment->order->order_status == 'Cancelled' ? 'danger' : 'secondary')) 
                            }} px-3 py-2">{{ ucfirst($payment->order->order_status) }}</span>
                        </div>
                        <div class="col-md-6">
                            <div class="small text-muted mb-1">Order Type</div>
                            <span class="badge badge-{{ $payment->order->order_type == 'Bulk' ? 'danger' : 'info' }} px-3 py-2">
                                {{ $payment->order->order_type }}
                            </span>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-12">
                            <div class="card bg-light">
                                <div class="card-body p-3">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <div class="small text-muted">Order Total</div>
                                            <div class="h5 mb-0 font-weight-bold">₱{{ number_format($payment->order->total_amount, 2) }}</div>
                                        </div>
                                        @if($payment->order->delivery)
                                        <div>
                                            <div class="small text-muted">Delivery Fee</div>
                                            <div class="h5 mb-0">₱{{ number_format($payment->order->delivery->delivery_cost, 2) }}</div>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Order Items -->
                    @if($orderProducts->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead class="thead-light">
                                <tr>
                                    <th>Item</th>
                                    <th class="text-center">Qty</th>
                                    <th class="text-right">Price</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($orderProducts as $item)
                                <tr>
                                    <td>
                                        <div class="font-weight-bold">{{ $item->product->name }}</div>
                                        <div class="small text-muted">{{ $item->product->category }}</div>
                                    </td>
                                    <td class="text-center">{{ $item->quantity }}</td>
                                    <td class="text-right">₱{{ number_format($item->subtotal, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @endif
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deletePaymentModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-exclamation-triangle mr-2"></i>Delete Payment
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center py-4">
                    <i class="fas fa-trash-alt fa-4x text-danger mb-3"></i>
                    <h5>Are you sure you want to delete this payment?</h5>
                    <p class="text-muted mb-0">This action cannot be undone. All payment data will be permanently removed.</p>
                    <div class="bg-light rounded p-3 mt-3 text-left">
                        <p class="mb-1"><strong>Payment ID:</strong> #{{ $payment->pay_id }}</p>
                        <p class="mb-1"><strong>Reference:</strong> {{ $payment->reference_number ?? 'N/A' }}</p>
                        <p class="mb-0"><strong>Amount:</strong> ₱{{ number_format($payment->amount_paid, 2) }}</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times mr-1"></i> Cancel
                    </button>
                    <form action="{{ route('admin.payments.destroy', $payment->pay_id) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash mr-1"></i> Delete Payment
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
