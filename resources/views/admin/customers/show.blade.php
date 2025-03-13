@extends('layouts.admin')

@section('admin.content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-user"></i> Customer Details
        </h1>
        <div>
            <a href="{{ route('admin.customers.index') }}" class="btn btn-secondary me-2">
                <i class="fas fa-arrow-left"></i> Back to Customers
            </a>
            <a href="{{ route('admin.customers.edit', $customer->cus_id) }}" class="btn btn-primary">
                <i class="fas fa-edit"></i> Edit Customer
            </a>
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
        <!-- Customer Details Card -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text">Customer Information</h6>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        <div class="display-4 text-primary">
                            <i class="fas fa-user-circle"></i>
                        </div>
                        <h4 class="mt-3">{{ $customer->fname }} {{ $customer->mname }} {{ $customer->lname }}</h4>
                        <p class="text-muted">Customer ID: {{ $customer->cus_id }}</p>
                    </div>
                    
                    <hr>
                    
                    <div class="row mb-2">
                        <div class="col-5 text-muted">Email:</div>
                        <div class="col-7 text-truncate">{{ $customer->email }}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-5 text-muted">Contact Number:</div>
                        <div class="col-7">{{ $customer->contact_number }}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-5 text-muted">Birthdate:</div>
                        <div class="col-7">{{ $customer->birthdate ? \Carbon\Carbon::parse($customer->birthdate)->format('M d, Y') : 'Not set' }}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-5 text-muted">Address:</div>
                        <div class="col-7">{{ $customer->address }}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-5 text-muted">City:</div>
                        <div class="col-7">{{ $customer->city }}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-5 text-muted">Province:</div>
                        <div class="col-7">{{ $customer->province }}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-5 text-muted">Postal Code:</div>
                        <div class="col-7">{{ $customer->postal_code }}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-5 text-muted">Country:</div>
                        <div class="col-7">{{ $customer->country }}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-5 text-muted">Registered:</div>
                        <div class="col-7">{{ $customer->created_at->format('M d, Y') }}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-5 text-muted">Last Updated:</div>
                        <div class="col-7">{{ $customer->updated_at->format('M d, Y') }}</div>
                    </div>
                    
                    <hr>
                    
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.customers.edit', $customer->cus_id) }}" class="btn btn-primary">
                            <i class="fas fa-edit"></i> Edit Customer
                        </a>
                        <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteCustomerModal">
                            <i class="fas fa-trash"></i> Delete Customer
                        </button>
                    </div>
                </div>
            </div>

            <!-- Customer Stats Card -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text">Customer Statistics</h6>
                </div>
                <div class="card-body">
                    <div class="row align-items-center mb-3">
                        <div class="col-8">
                            <h4 class="small font-weight-bold">Total Orders</h4>
                        </div>
                        <div class="col-4 text-end">
                            <h5 class="font-weight-bold text-primary">{{ $totalOrders }}</h5>
                        </div>
                    </div>
                    
                    <div class="row align-items-center mb-3">
                        <div class="col-8">
                            <h4 class="small font-weight-bold">Total Spent</h4>
                        </div>
                        <div class="col-4 text-end">
                            <h5 class="font-weight-bold text-success">₱{{ number_format($totalSpent, 2) }}</h5>
                        </div>
                    </div>
                    
                    <div class="row align-items-center">
                        <div class="col-8">
                            <h4 class="small font-weight-bold">Average Order Value</h4>
                        </div>
                        <div class="col-4 text-end">
                            <h5 class="font-weight-bold text-info">₱{{ number_format($avgOrderValue, 2) }}</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Order History Card -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text">Order History</h6>
                </div>
                <div class="card-body">
                    @if($orders->isEmpty())
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> This customer has not placed any orders yet.
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-bordered" id="ordersTable" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th class="text-center">Order #</th>
                                        <th class="text-center">Date</th>
                                        <th class="text-center">Type</th>
                                        <th class="text-center">Status</th>
                                        <th class="text-center">Total</th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($orders as $order)
                                        <tr>
                                            <td>{{ $order->order_id }}</td>
                                            <td>{{ $order->order_date->format('M d, Y') }}</td>
                                            <td>
                                                <span class="badge {{ $order->order_type == 'Retail' ? 'bg-info' : 'bg-primary' }}">
                                                    {{ $order->order_type }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge {{ 
                                                    $order->order_status == 'Completed' ? 'bg-success' : 
                                                    ($order->order_status == 'Pending' ? 'bg-warning' :
                                                    ($order->order_status == 'Processing' ? 'bg-primary' :
                                                    ($order->order_status == 'Cancelled' ? 'bg-danger' : 'bg-secondary')))
                                                }}">
                                                    {{ $order->order_status }}
                                                </span>
                                            </td>
                                            <td>₱{{ number_format($order->total_amount, 2) }}</td>
                                            <td>
                                                <a href="{{ route('admin.orders.show', $order->order_id) }}" class="btn btn-sm btn-info">
                                                    <i class="fas fa-eye"></i> View
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Additional Statistics or Information Cards could go here -->
            @if(!$orders->isEmpty())
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text">Order Statistics</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-xl-6">
                            <h4 class="small font-weight-bold mb-2">Status Breakdown</h4>
                            <div class="mb-4">
                                @php
                                    $statusCounts = $orders->groupBy('order_status')->map->count();
                                    $pendingCount = $statusCounts['Pending'] ?? 0;
                                    $processingCount = $statusCounts['Processing'] ?? 0;
                                    $completedCount = $statusCounts['Completed'] ?? 0;
                                    $cancelledCount = $statusCounts['Cancelled'] ?? 0;
                                @endphp
                                
                                <div class="d-flex justify-content-between mb-1">
                                    <span>Pending</span>
                                    <span>{{ $pendingCount }}</span>
                                </div>
                                <div class="progress mb-2" style="height: 5px;">
                                    <div class="progress-bar bg-warning" role="progressbar" style="width: {{ ($pendingCount / $orders->count()) * 100 }}%" aria-valuenow="{{ $pendingCount }}" aria-valuemin="0" aria-valuemax="{{ $orders->count() }}"></div>
                                </div>
                                
                                <div class="d-flex justify-content-between mb-1">
                                    <span>Processing</span>
                                    <span>{{ $processingCount }}</span>
                                </div>
                                <div class="progress mb-2" style="height: 5px;">
                                    <div class="progress-bar bg-primary" role="progressbar" style="width: {{ ($processingCount / $orders->count()) * 100 }}%" aria-valuenow="{{ $processingCount }}" aria-valuemin="0" aria-valuemax="{{ $orders->count() }}"></div>
                                </div>
                                
                                <div class="d-flex justify-content-between mb-1">
                                    <span>Completed</span>
                                    <span>{{ $completedCount }}</span>
                                </div>
                                <div class="progress mb-2" style="height: 5px;">
                                    <div class="progress-bar bg-success" role="progressbar" style="width: {{ ($completedCount / $orders->count()) * 100 }}%" aria-valuenow="{{ $completedCount }}" aria-valuemin="0" aria-valuemax="{{ $orders->count() }}"></div>
                                </div>
                                
                                <div class="d-flex justify-content-between mb-1">
                                    <span>Cancelled</span>
                                    <span>{{ $cancelledCount }}</span>
                                </div>
                                <div class="progress mb-2" style="height: 5px;">
                                    <div class="progress-bar bg-danger" role="progressbar" style="width: {{ ($cancelledCount / $orders->count()) * 100 }}%" aria-valuenow="{{ $cancelledCount }}" aria-valuemin="0" aria-valuemax="{{ $orders->count() }}"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-6">
                            <h4 class="small font-weight-bold mb-2">Order Type Breakdown</h4>
                            <div class="mb-4">
                                @php
                                    $typeCounts = $orders->groupBy('order_type')->map->count();
                                    $retailCount = $typeCounts['Retail'] ?? 0;
                                    $bulkCount = $typeCounts['Bulk'] ?? 0;
                                @endphp
                                
                                <div class="d-flex justify-content-between mb-1">
                                    <span>Retail</span>
                                    <span>{{ $retailCount }}</span>
                                </div>
                                <div class="progress mb-2" style="height: 5px;">
                                    <div class="progress-bar bg-info" role="progressbar" style="width: {{ ($retailCount / $orders->count()) * 100 }}%" aria-valuenow="{{ $retailCount }}" aria-valuemin="0" aria-valuemax="{{ $orders->count() }}"></div>
                                </div>
                                
                                <div class="d-flex justify-content-between mb-1">
                                    <span>Bulk</span>
                                    <span>{{ $bulkCount }}</span>
                                </div>
                                <div class="progress mb-2" style="height: 5px;">
                                    <div class="progress-bar bg-primary" role="progressbar" style="width: {{ ($bulkCount / $orders->count()) * 100 }}%" aria-valuenow="{{ $bulkCount }}" aria-valuemin="0" aria-valuemax="{{ $orders->count() }}"></div>
                                </div>
                            </div>
                            
                            <!-- Monthly Order Activity -->
                            <h4 class="small font-weight-bold mt-4 mb-2">Recent Activity</h4>
                            <div>
                                @php
                                    $recentMonths = collect();
                                    for ($i = 0; $i < 3; $i++) {
                                        $month = now()->subMonths($i);
                                        $recentMonths->put($month->format('Y-m'), $month->format('M Y'));
                                    }
                                    
                                    $recentOrderCounts = $orders
                                        ->where('order_date', '>=', now()->subMonths(3))
                                        ->groupBy(function($order) {
                                            return $order->order_date->format('Y-m');
                                        })
                                        ->map->count();
                                @endphp
                                
                                @foreach($recentMonths as $monthKey => $monthLabel)
                                    <div class="d-flex justify-content-between mb-1">
                                        <span>{{ $monthLabel }}</span>
                                        <span>{{ $recentOrderCounts[$monthKey] ?? 0 }} orders</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Delete Customer Modal -->
<div class="modal fade" id="deleteCustomerModal" tabindex="-1" aria-labelledby="deleteCustomerModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteCustomerModalLabel">
                    <i class="fas fa-exclamation-triangle"></i> Delete Customer
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this customer?</p>
                <p><strong>Name:</strong> {{ $customer->fname }} {{ $customer->lname }}</p>
                <p><strong>Email:</strong> {{ $customer->email }}</p>
                
                @if($totalOrders > 0)
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-circle"></i> This customer has {{ $totalOrders }} orders. 
                        Delete operation will fail. Consider marking them as inactive instead.
                    </div>
                @else
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle"></i> This action cannot be undone.
                    </div>
                @endif
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form action="{{ route('admin.customers.destroy', $customer->cus_id) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete Customer</button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        $('#ordersTable').DataTable({
            "order": [[0, "desc"]],
            "pageLength": 10,
            "lengthChange": false,
            "searching": false,
            "info": false
        });
    });
</script>
@endpush
@endsection
