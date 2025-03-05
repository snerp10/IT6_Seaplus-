@extends('layouts.admin')

@section('title', 'Supplier Details')

@section('admin.content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-truck-loading text-dark mr-2"></i> Supplier Details
        </h1>
        <div>
            <a href="{{ route('admin.suppliers.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Back to Suppliers
            </a>
            <a href="{{ route('admin.suppliers.edit', $supplier->supp_id) }}" class="btn btn-primary btn-sm">
                <i class="fas fa-edit"></i> Edit Supplier
            </a>
            <button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#deleteModal">
                <i class="fas fa-trash"></i> Delete
            </button>
        </div>
    </div>

    <!-- Status Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="row">
        <!-- Main Supplier Details Card -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-info-circle mr-1"></i> Supplier Information
                    </h6>
                    <span class="badge {{ $supplier->status == 'Active' ? 'bg-success' : 'bg-danger' }} text-white px-3 py-2">
                        {{ $supplier->status ?? 'Active' }}
                    </span>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <tbody>
                                <tr>
                                    <th style="width: 30%">ID</th>
                                    <td>SUPP-{{ str_pad($supplier->supp_id, 5, '0', STR_PAD_LEFT) }}</td>
                                </tr>
                                <tr>
                                    <th>Company Name</th>
                                    <td>{{ $supplier->company_name }}</td>
                                </tr>
                                <tr>
                                    <th>Contact Number</th>
                                    <td>{{ $supplier->contact_number }}</td>
                                </tr>
                                <tr>
                                    <th>Email</th>
                                    <td>{{ $supplier->email }}</td>
                                </tr>
                                <tr>
                                    <th>Address</th>
                                    <td>{{ $supplier->street }}, {{ $supplier->city }}, {{ $supplier->province }}</td>
                                </tr>
                                <tr>
                                    <th>Product Type</th>
                                    <td>{{ $supplier->prod_type }}</td>
                                </tr>
                                @if($supplier->notes)
                                <tr>
                                    <th>Additional Notes</th>
                                    <td>{{ $supplier->notes }}</td>
                                </tr>
                                @endif
                                <tr>
                                    <th>Created At</th>
                                    <td>{{ $supplier->created_at->format('F d, Y g:i A') }}</td>
                                </tr>
                                <tr>
                                    <th>Last Updated</th>
                                    <td>{{ $supplier->updated_at->format('F d, Y g:i A') }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <!-- Supplier Products -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-cubes mr-1"></i> Supplied Products
                    </h6>
                </div>
                <div class="card-body">
                    @if($suppliedProducts->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>ID</th>
                                        <th>Product Name</th>
                                        <th>Category</th>
                                        <th>Minimum Order</th>
                                        <th>Unit Price</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($suppliedProducts as $supplierProduct)
                                        <tr>
                                            <td>{{ $supplierProduct->product->prod_id }}</td>
                                            <td>{{ $supplierProduct->product->name }}</td>
                                            <td>{{ $supplierProduct->product->category }}</td>
                                            <td>{{ $supplierProduct->min_order_qty }} {{ $supplierProduct->product->unit }}</td>
                                            <td>₱{{ number_format($supplierProduct->product->price, 2) }}</td>
                                            <td>
                                                <a href="{{ route('admin.products.show', $supplierProduct->product->prod_id) }}" class="btn btn-sm btn-info">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle mr-1"></i> No products associated with this supplier yet.
                        </div>
                    @endif
                    
                    <div class="mt-3 text-right">
                        <a href="{{ route('admin.suppliers.edit', $supplier->supp_id) }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus-circle mr-1"></i> Add/Edit Products
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Side Cards -->
        <div class="col-xl-4 col-lg-5">
            <!-- Supplier Stats Card -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-chart-pie mr-1"></i> Supplier Statistics
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row mb-2">
                        <div class="col-6 text-muted">Products Supplied:</div>
                        <div class="col-6 font-weight-bold">{{ $suppliedProducts->count() }}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-6 text-muted">Status:</div>
                        <div class="col-6">
                            <span class="badge {{ $supplier->status == 'Active' ? 'bg-success' : 'bg-danger' }} text-white">
                                {{ $supplier->status ?? 'Active' }}
                            </span>
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-6 text-muted">Preferred Supplier:</div>
                        <div class="col-6">{{ $supplier->is_preferred ? 'Yes' : 'No' }}</div>
                    </div>
                    
                    <hr>
                    
                    <div class="alert alert-info mt-3 mb-0">
                        <small>
                            <i class="fas fa-info-circle"></i> This supplier has been registered since 
                            <strong>{{ $supplier->created_at->format('F d, Y') }}</strong>
                        </small>
                    </div>
                </div>
            </div>
            
            <!-- Quick Actions Card -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-tasks mr-1"></i> Quick Actions
                    </h6>
                </div>
                <div class="card-body">
                    <a href="{{ route('admin.suppliers.edit', $supplier->supp_id) }}" class="btn btn-primary btn-block mb-3">
                        <i class="fas fa-edit mr-1"></i> Edit Supplier Details
                    </a>
                    <a href="mailto:{{ $supplier->email }}" class="btn btn-info btn-block mb-3">
                        <i class="fas fa-envelope mr-1"></i> Contact Supplier
                    </a>
                    <button type="button" class="btn btn-danger btn-block" data-toggle="modal" data-target="#deleteModal">
                        <i class="fas fa-trash mr-1"></i> Delete Supplier
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-hidden="true">
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
                    <p>Are you sure you want to delete this supplier? This action cannot be undone.</p>
                    <p><strong>Company:</strong> {{ $supplier->company_name }}</p>
                    <p><strong>Products:</strong> {{ $suppliedProducts->count() }}</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <form action="{{ route('admin.suppliers.destroy', $supplier->supp_id) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    $(function() {
        // Initialize tooltips
        $('[data-toggle="tooltip"]').tooltip();
    });
</script>
@endpush
@endsection
