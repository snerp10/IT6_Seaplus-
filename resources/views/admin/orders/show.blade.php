@extends('layouts.admin')

@section('admin.content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-file-invoice"></i> Order #{{ $order->order_id }}
        </h1>
        <div>
            <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary mr-2">
                <i class="fas fa-arrow-left"></i> Back to Orders
            </a>
            <a href="{{ route('admin.orders.edit', $order->order_id) }}" class="btn btn-warning mr-2">
                <i class="fas fa-edit"></i> Edit Order
            </a>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addPaymentModal">
                <i class="fas fa-money-bill"></i> Add Payment
            </button>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div class="row">
        <!-- Order Info Column -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between bg text-white">
                    <h6 class="m-0 font-weight-bold">Order Information</h6>
                    <span class="badge bg-{{ 
                        $order->order_status == 'Completed' ? 'success' : 
                        ($order->order_status == 'Processing' ? 'warning' : 
                        ($order->order_status == 'Cancelled' ? 'danger' : 'secondary'))
                    }}">{{ $order->order_status }}</span>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-borderless">
                            <tr>
                                <th>Order Date:</th>
                                <td>{{ \Carbon\Carbon::parse($order->order_date)->format('F d, Y h:i A') }}</td>
                            </tr>
                            <tr>
                                <th>Order Type:</th>
                                <td>{{ $order->order_type }}</td>
                            </tr>
                            <tr>
                                <th>Payment Method:</th>
                                <td>{{ $order->payments->first()->pay_method ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th>Total Amount:</th>
                                <td class="font-weight-bold">₱{{ number_format($order->total_amount, 2) . "+" . ($order->delivery ? "₱" . number_format($order->delivery->delivery_cost, 2) : "") }}</td>
                            </tr>
                            <tr>
                                <th>Amount Paid:</th>
                                <td>₱{{ number_format($orderSummary['total_paid'], 2) }}</td>
                            </tr>
                            <tr>
                                <th>Balance:</th>
                                <td class="font-weight-bold text-{{ ($orderSummary['total_due'] - $orderSummary['total_paid']) > 0 ? 'danger' : 'success' }}">
                                    ₱{{ number_format(max(0, $orderSummary['total_due'] - $orderSummary['total_paid']), 2) }}
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Customer Info Column -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3 bg-gradient-primary text-white">
                    <h6 class="m-0 font-weight-bold"><i class="fas fa-user"></i> Customer Information</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="avatar mr-3">
                            <i class="fas fa-user-circle fa-3x text-gray-300"></i>
                        </div>
                        <div>
                            <h5 class="mb-0">{{ $order->customer->fname }} {{ $order->customer->lname }}</h5>
                            <p class="text-muted mb-0">{{ $order->customer->email }}</p>
                        </div>
                    </div>
                    <hr>
                    <p><i class="fas fa-phone mr-2"></i> {{ $order->customer->phone_no }}</p>
                    <p><i class="fas fa-map-marker-alt mr-2"></i> 
                        {{ $order->customer->street }}, {{ $order->customer->city }}, {{ $order->customer->province }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Delivery Info Column -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between bg-gradient-info text-white">
                    <h6 class="m-0 font-weight-bold"><i class="fas fa-truck"></i> Delivery Information</h6>
                    <span class="badge bg-{{ 
                        $order->delivery->delivery_status == 'Delivered' ? 'success' : 
                        ($order->delivery->delivery_status == 'Out for Delivery' ? 'info' : 
                        ($order->delivery->delivery_status == 'Cancelled' ? 'danger' : 'warning'))
                    }}">{{ $order->delivery->delivery_status }}</span>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-borderless">
                            <tr>
                                <th>Delivery Date:</th>
                                <td>{{ $order->delivery->delivery_date ? \Carbon\Carbon::parse($order->delivery->delivery_date)->format('F d, Y') : 'Not scheduled' }}</td>
                            </tr>
                            <tr>
                                <th>Address:</th>
                                <td>
                                    {{ $order->delivery->street }}<br>
                                    {{ $order->delivery->city }}, {{ $order->delivery->province }}
                                </td>
                            </tr>
                            <tr>
                                <th>Special Instructions:</th>
                                <td>{{ $order->delivery->special_instructions ?: 'None' }}</td>
                            </tr>
                            <tr>
                                <th>Delivery Cost:</th>
                                <td>₱{{ number_format($order->delivery->delivery_cost, 2) }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Order Items Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-gradient-dark text-white">
            <h6 class="m-0 font-weight-bold"><i class="fas fa-shopping-cart"></i> Order Items</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="bg-light">
                        <tr>
                            <th>#</th>
                            <th>Product</th>
                            <th>Category</th>
                            <th>Price</th>
                            <th>Quantity</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($order->orderDetails as $index => $detail)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $detail->product->name }}</td>
                            <td>{{ $detail->product->category }}</td>
                            <td>₱{{ number_format($detail->product->price, 2) }}</td>
                            <td>{{ $detail->quantity }} {{ $detail->product->unit }}</td>
                            <td class="text-right">₱{{ number_format($detail->subtotal, 2) }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center">No items found</td>
                        </tr>
                        @endforelse
                    </tbody>
                    <tfoot class="bg-light">
                        <tr>
                            <th colspan="5" class="text-right">Subtotal:</th>
                            <th class="text-right">₱{{ number_format($orderSummary['subtotal'], 2) }}</th>
                        </tr>
                        <tr>
                            <th colspan="5" class="text-right">Delivery Fee:</th>
                            <th class="text-right">₱{{ number_format($orderSummary['delivery_cost'], 2) }}</th>
                        </tr>
                        <tr>
                            <th colspan="5" class="text-right">Total:</th>
                            <th class="text-right">₱{{ number_format($orderSummary['total_due'], 2) }}</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <!-- Payment History Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-gradient-info text-white">
            <h6 class="m-0 font-weight-bold"><i class="fas fa-credit-card"></i> Payment History</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="bg-light">
                        <tr>
                            <th>Date</th>
                            <th>Invoice #</th>
                            <th>Method</th>
                            <th>Reference #</th>
                            <th>Amount Paid</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($order->payments as $payment)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($payment->pay_date)->format('M d, Y h:i A') }}</td>
                            <td>{{ $payment->invoice_number }}</td>
                            <td>{{ $payment->pay_method }}</td>
                            <td>{{ $payment->reference_number ?: 'N/A' }}</td>
                            <td class="text-right">₱{{ number_format($payment->amount_paid, 2) }}</td>
                            <td>
                                <span class="badge bg-{{ 
                                    $payment->pay_status == 'Paid' ? 'success' : 'warning' 
                                }}">
                                    {{ $payment->pay_status }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center">No payment records found</td>
                        </tr>
                        @endforelse
                    </tbody>
                    <tfoot class="bg-light">
                        <tr>
                            <th colspan="4" class="text-right">Total Paid:</th>
                            <th class="text-right">₱{{ number_format($orderSummary['total_paid'], 2) }}</th>
                            <th></th>
                        </tr>
                        <tr>
                            <th colspan="4" class="text-right">Balance:</th>
                            <th class="text-right">₱{{ number_format(max(0, $orderSummary['total_due'] - $orderSummary['total_paid']), 2) }}</th>
                            <th></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Add Payment Modal -->
<div class="modal fade" id="addPaymentModal" tabindex="-1" aria-labelledby="addPaymentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.payments.create_from_order', $order->order_id) }}" method="POST">
                @csrf
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="addPaymentModalLabel">Add Payment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="amount_paid" class="form-label">Payment Amount <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">₱</span>
                            <input type="number" step="0.01" min="0.01" max="{{ $orderSummary['total_due'] - $orderSummary['total_paid'] }}" 
                                   class="form-control" id="amount_paid" name="amount_paid" required>
                        </div>
                        <small class="text-muted">Maximum amount: ₱{{ number_format($orderSummary['total_due'] - $orderSummary['total_paid'], 2) }}</small>
                    </div>

                    <div class="mb-3">
                        <label for="pay_method" class="form-label">Payment Method <span class="text-danger">*</span></label>
                        <select class="form-control" id="pay_method" name="pay_method" required>
                            <option value="Cash">Cash</option>
                            <option value="GCash">GCash</option>
                            <option value="Bank Transfer">Cash on Delivery</option>
                        </select>
                    </div>

                    <div class="mb-3" id="referenceNumberField" style="display: none;">
                        <label for="reference_number" class="form-label">Reference Number</label>
                        <input type="text" class="form-control" id="reference_number" name="reference_number">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Payment</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Show/hide reference number field based on payment method
        const payMethodSelect = document.getElementById('pay_method');
        const referenceField = document.getElementById('referenceNumberField');
        
        payMethodSelect.addEventListener('change', function() {
            if (this.value === 'GCash' || this.value === 'Bank Transfer') {
                referenceField.style.display = 'block';
            } else {
                referenceField.style.display = 'none';
            }
        });
    });
</script>
@endpush
@endsection
