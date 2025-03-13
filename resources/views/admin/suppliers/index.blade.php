@extends('layouts.admin')

@section('title', 'Suppliers Management')

@section('admin.content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-truck-loading text-dark mr-2"></i> Suppliers Management
        </h1>
        <div>
            <a href="{{ route('admin.suppliers.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus-circle"></i> Add New Supplier
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

    <!-- Supplier Statistics -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Suppliers</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $suppliers->total() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-building fa-2x text-gray-300"></i>
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
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Active Suppliers</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $suppliers->where('status', 'Active')->count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
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
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Preferred Suppliers</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $suppliers->where('is_preferred', true)->count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-star fa-2x text-gray-300"></i>
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
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Products Supplied</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $suppliers->sum('product_count') ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-cube fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text">Filter Suppliers</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.suppliers.index') }}" method="GET" class="mb-0">
                <div class="row">
                    <div class="col-md-3">
                        <label for="search" class="form-label small">Search</label>
                        <input type="text" name="search" id="search" class="form-control form-control-sm" 
                               value="{{ request('search') }}" placeholder="Name, Email, or Contact">
                    </div>
                    <div class="col-md-3">
                        <label for="product_type" class="form-label small">Product Type</label>
                        <select name="product_type" id="product_type" class="form-control form-control-sm">
                            <option value="">All Types</option>
                            @foreach($productTypes as $type)
                                <option value="{{ $type }}" {{ request('product_type') == $type ? 'selected' : '' }}>
                                    {{ $type }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="location" class="form-label small">Location</label>
                        <select name="location" id="location" class="form-control form-control-sm">
                            <option value="">All Locations</option>
                            @foreach($locations as $location)
                                <option value="{{ $location }}" {{ request('location') == $location ? 'selected' : '' }}>
                                    {{ $location }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label d-block small">&nbsp;</label>
                        <div class="d-flex">
                            <button type="submit" class="btn btn-primary btn-sm mr-2">
                                <i class="fas fa-search"></i> Search
                            </button>
                            <a href="{{ route('admin.suppliers.index') }}" class="btn btn-secondary btn-sm">
                                <i class="fas fa-undo"></i> Reset
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Suppliers Table Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text">
                <i class="fas fa-list"></i> Suppliers List
            </h6>
            <span>{{ $suppliers->total() }} suppliers found</span>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover">
                    <thead class="thead-dark">
                        <tr>
                            <th class="text-center">ID</th>
                            <th class="text-center">Company</th>
                            <th class="text-center">Contact</th>
                            <th class="text-center">Email</th>
                            <th class="text-center">City</th>
                            <th class="text-center">Product Type</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($suppliers as $supplier)
                        <tr>
                            <td>{{ $supplier->supp_id }}</td>
                            <td>
                                <strong>{{ $supplier->company_name }}</strong>
                                @if($supplier->is_preferred)
                                    <span class="badge bg-warning text-white ml-2">
                                        <i class="fas fa-star"></i> Preferred
                                    </span>
                                @endif
                            </td>
                            <td>{{ $supplier->contact_number }}</td>
                            <td>{{ $supplier->email }}</td>
                            <td>{{ $supplier->city }}, {{ $supplier->province }}</td>
                            <td>{{ $supplier->prod_type }}</td>
                            <td>
                                <div class="btn-group" role="group" style="column-gap: 0.25rem">
                                    <a href="{{ route('admin.suppliers.show', $supplier->supp_id) }}" class="btn btn-sm btn-secondary" title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.suppliers.edit', $supplier->supp_id) }}" class="btn btn-sm btn-primary" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.suppliers.destroy', $supplier->supp_id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="btn btn-sm btn-danger" 
                                                onclick="confirmDelete(this, '{{ $supplier->company_name }}', '{{ $supplier->contact_number }}')"
                                                title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center">No suppliers found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $suppliers->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Simple confirm delete function
    function confirmDelete(button, companyName, contact) {
        if (confirm('Are you sure you want to delete this supplier?\n\nCompany: ' + companyName + '\nContact: ' + contact + '\n\nThis action cannot be undone.')) {
            button.closest('form').submit();
        }
    }

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