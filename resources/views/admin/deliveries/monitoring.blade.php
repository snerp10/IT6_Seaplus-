@extends('layouts.admin')

@section('admin.content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-tv"></i> Delivery Monitoring Dashboard
        </h1>
        <a href="{{ route('admin.deliveries.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Deliveries
        </a>
    </div>
    
    <!-- Delivery Status Summary -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Pending</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $pending_deliveries }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
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
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Scheduled</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $scheduled }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar fa-2x text-gray-300"></i>
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
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Out for Delivery</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $out_for_delivery }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-truck fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Delivered</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $delivered }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Active Deliveries Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Active Deliveries</h6>
            <div class="dropdown no-arrow">
                <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                </a>
                <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in" aria-labelledby="dropdownMenuLink">
                    <a class="dropdown-item" href="#" id="refreshData"><i class="fas fa-sync fa-sm fa-fw mr-2 text-gray-400"></i> Refresh Data</a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="{{ route('admin.deliveries.index') }}"><i class="fas fa-list fa-sm fa-fw mr-2 text-gray-400"></i> View All Deliveries</a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="activeDeliveriesTable" width="100%" cellspacing="0">
                    <thead class="thead-dark">
                        <tr>
                            <th class="text-center">Order #</th>
                            <th class="text-center">Customer</th>
                            <th class="text-center">Delivery Date</th>
                            <th class="text-center">Address</th>
                            <th class="text-center">Driver</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($deliveries as $delivery)
                        <tr>
                            <td>
                                <a href="{{ route('admin.orders.show', $delivery->order->order_id) }}" class="font-weight-bold">
                                    #{{ $delivery->order->order_id }}
                                </a>
                            </td>
                            <td>
                                {{ $delivery->order->customer->fname }} {{ $delivery->order->customer->lname }}<br>
                                <small class="text-muted">{{ $delivery->order->customer->phone_no }}</small>
                            </td>
                            <td>{{ $delivery->delivery_date ? \Carbon\Carbon::parse($delivery->delivery_date)->format('M d, Y') : 'Not scheduled' }}</td>
                            <td>
                                <small>
                                    {{ $delivery->street }}<br>
                                    {{ $delivery->city }}, {{ $delivery->province }}
                                </small>
                            </td>
                            <td>
                                @if($delivery->driver)
                                    <span class="text-success">
                                        <i class="fas fa-user-check"></i> {{ $delivery->driver->fname }} {{ $delivery->driver->lname }}
                                    </span>
                                @else
                                    <span class="text-danger">
                                        <i class="fas fa-user-times"></i> Not Assigned
                                    </span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-{{ 
                                    $delivery->delivery_status == 'Delivered' ? 'success' : 
                                    ($delivery->delivery_status == 'Out for Delivery' ? 'info' : 
                                    ($delivery->delivery_status == 'Scheduled' ? 'primary' : 
                                    ($delivery->delivery_status == 'Failed' ? 'danger' : 
                                    ($delivery->delivery_status == 'Returned' ? 'warning' : 'secondary')))) 
                                }}">
                                    {{ $delivery->delivery_status }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('admin.deliveries.edit', $delivery) }}" class="btn btn-sm btn-primary">
                                    <i class="fas fa-edit"></i> Update
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center">No active deliveries found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Quick Update Modal -->
<div class="modal fade" id="quickUpdateModal" tabindex="-1" aria-labelledby="quickUpdateModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="quickUpdateModalLabel">Quick Update Delivery Status</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="quickUpdateForm" action="" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="form-group mb-3">
                        <label for="emp_id"><strong>Assign Driver:</strong></label>
                        <select name="emp_id" id="emp_id" class="form-control">
                            <option value="">-- Select Driver --</option>
                            @foreach($drivers as $driver)
                                <option value="{{ $driver->emp_id }}">{{ $driver->fname }} {{ $driver->lname }}</option>
                            @endforeach
                        </select>
                        <small class="form-text text-muted">Leave empty to not assign a driver</small>
                    </div>
                    
                    <div class="form-group mb-3">
                        <label for="quick_delivery_status"><strong>Update Status:</strong></label>
                        <select name="delivery_status" class="form-control" id="quick_delivery_status">
                            <option value="Pending">Pending</option>
                            <option value="Scheduled">Scheduled</option>
                            <option value="Out for Delivery">Out for Delivery</option>
                            <option value="Delivered">Delivered</option>
                            <option value="Failed">Failed</option>
                            <option value="Returned">Returned</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="special_instructions"><strong>Special Notes:</strong></label>
                        <textarea name="special_instructions" id="special_instructions" class="form-control" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Update Status</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        // Initialize DataTable
        $('#activeDeliveriesTable').DataTable({
            "paging": true,
            "info": true,
            "searching": true,
            "responsive": true
        });
        
        // Update the quick update modal handler for Bootstrap 5
        $('.quick-update-btn').on('click', function() {
            const updateUrl = $(this).data('url');
            $('#quickUpdateForm').attr('action', updateUrl);
            
            // You may also want to add this to pre-populate driver selection
            const deliveryId = $(this).data('delivery-id');
            
            // Fetch delivery details if needed
            // You could make an AJAX call here to get current values
            
            // No need to manually show the modal as data-bs-toggle will handle it
        });
        
        // Auto refresh data every 5 minutes
        const refreshInterval = 5 * 60 * 1000; // 5 minutes
        setInterval(function() {
            location.reload();
        }, refreshInterval);
        
        // Manual refresh
        $('#refreshData').on('click', function(e) {
            e.preventDefault();
            location.reload();
        });
    });
</script>
@endpush

@endsection
