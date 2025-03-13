@extends('layouts.admin')

@section('admin.content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-truck"></i> Delivery Management
        </h1>
        <div>
            <a href="{{ route('admin.deliveries.monitoring') }}" class="btn btn-info mr-2">
                <i class="fas fa-tv"></i> Delivery Monitoring
            </a>
            <a href="{{ route('admin.deliveries.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Assign Delivery to Unassigned Order
            </a>
            <a href="#" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#exportModal">
                <i class="fas fa-file-export"></i> Export Deliveries
            </a>
        </div>
    </div>

    <!-- Status Messages -->
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

    <!-- Delivery Statistics -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Deliveries</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $total_deliveries }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-truck fa-2x text-gray-300"></i>
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
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Pending Deliveries</div>
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
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Out for Delivery</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $out_for_delivery }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-shipping-fast fa-2x text-gray-300"></i>
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
    
    <!-- Additional status cards in a new row -->
    <div class="row mb-4">
        <div class="col-xl-4 col-md-6 mb-4">
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
        
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Failed</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $failed }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-times-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Returned</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $returned }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-undo fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text">Filter Deliveries</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.deliveries.index') }}" method="GET" class="mb-3">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label for="status" class="form-label">Delivery Status</label>
                        <select name="status" id="status" class="form-control">
                            <option value="">All Statuses</option>
                            <option value="Pending" {{ request('status') == 'Pending' ? 'selected' : '' }}>Pending</option>
                            <option value="Scheduled" {{ request('status') == 'Scheduled' ? 'selected' : '' }}>Scheduled</option>
                            <option value="Out for Delivery" {{ request('status') == 'Out for Delivery' ? 'selected' : '' }}>Out for Delivery</option>
                            <option value="Delivered" {{ request('status') == 'Delivered' ? 'selected' : '' }}>Delivered</option>
                            <option value="Failed" {{ request('status') == 'Failed' ? 'selected' : '' }}>Failed</option>
                            <option value="Returned" {{ request('status') == 'Returned' ? 'selected' : '' }}>Returned</option>
                        </select>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label for="date_from" class="form-label">Date From</label>
                        <input type="date" class="form-control" id="date_from" name="date_from" value="{{ request('date_from') }}">
                    </div>

                    <div class="col-md-4 mb-3">
                        <label for="date_to" class="form-label">Date To</label>
                        <input type="date" class="form-control" id="date_to" name="date_to" value="{{ request('date_to') }}">
                    </div>

                    <div class="col-md-12 text-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-filter"></i> Filter
                        </button>
                        <a href="{{ route('admin.deliveries.index') }}" class="btn btn-secondary">
                            <i class="fas fa-sync"></i> Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Deliveries Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text">Deliveries List</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="deliveriesTable">
                    <thead class="table-dark">
                        <tr>
                            <th class="text-center">ID</th>
                            <th class="text-center">Order #</th>
                            <th class="text-center">Customer</th>
                            <th class="text-center">Delivery Date</th>
                            <th class="text-center">Address</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($deliveries as $delivery)
                        <tr>
                            <td>{{ $delivery->delivery_id }}</td>
                            <td>
                                <a href="{{ route('admin.orders.show', $delivery->order->order_id) }}" class="text-primary font-weight-bold">
                                    #{{ $delivery->order->order_id }}
                                </a>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar me-2">
                                        <i class="fas fa-user-circle fa-2x text-primary"></i>
                                    </div>
                                    <div>
                                        <div class="font-weight-bold">{{ $delivery->order->customer->fname }} {{ $delivery->order->customer->lname }}</div>
                                        <div class="small text-muted">{{ $delivery->order->customer->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>{{ $delivery->delivery_date ? \Carbon\Carbon::parse($delivery->delivery_date)->format('M d, Y') : 'Not scheduled' }}</td>
                            <td>
                                <small>
                                    {{ $delivery->street }}<br>
                                    {{ $delivery->city }}, {{ $delivery->province }}
                                </small>
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
                                <div class="btn-group" role="group" style="column-gap: 0.25rem">
                                    <a href="{{ route('admin.deliveries.show', $delivery) }}" class="btn btn-sm btn-secondary">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.deliveries.edit', $delivery) }}" class="btn btn-sm btn-primary">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.deliveries.destroy', $delivery) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger delete-btn" 
                                                onclick="return confirm('Are you sure you want to delete this delivery? This action cannot be undone.')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center">No deliveries found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="mt-4">
                {{ $deliveries->withQueryString()->links() }}
            </div>
        </div>
    </div>
</div>

<!-- Export Modal -->
<div class="modal fade" id="exportModal" tabindex="-1" aria-labelledby="exportModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exportModalLabel">Export Deliveries</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('admin.deliveries.export') }}" method="GET" id="exportForm">
                    <div class="mb-3">
                        <label for="export-status" class="form-label">Delivery Status</label>
                        <select name="status" id="export-status" class="form-control">
                            <option value="">All Statuses</option>
                            <option value="Pending">Pending</option>
                            <option value="Scheduled">Scheduled</option>
                            <option value="Out for Delivery">Out for Delivery</option>
                            <option value="Delivered">Delivered</option>
                            <option value="Failed">Failed</option>
                            <option value="Returned">Returned</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="export-date-from" class="form-label">Date From</label>
                        <input type="date" class="form-control" id="export-date-from" name="date_from">
                    </div>
                    
                    <div class="mb-3">
                        <label for="export-date-to" class="form-label">Date To</label>
                        <input type="date" class="form-control" id="export-date-to" name="date_to">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary" form="exportForm">
                    <i class="fas fa-file-export"></i> Export
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Initialize DataTable
        $('#deliveriesTable').DataTable({
            "paging": false,
            "info": false,
            "searching": true,
            "responsive": true
        });
        
        // Set current date as default for export date fields if empty
        if (!$('#export-date-from').val()) {
            $('#export-date-from').val(new Date().toISOString().substring(0, 10));
        }
        
        if (!$('#export-date-to').val()) {
            $('#export-date-to').val(new Date().toISOString().substring(0, 10));
        }
    });
</script>
@endsection