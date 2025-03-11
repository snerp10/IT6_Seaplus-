@extends('layouts.admin')

@section('admin.content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-box-open text-dark"></i> Products Management
        </h1>
        <div>
            <a href="{{ route('admin.inventories.low_stock_alerts') }}" class="btn btn-warning mr-2">
                <i class="fas fa-exclamation-triangle"></i> Low Stock Alerts
            </a>
            <a href="{{ route('admin.products.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Add New Product
            </a>
        </div>
    </div>

    <!-- Status Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Product Statistics -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Products</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $products->count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-boxes fa-2x text-gray-300"></i>
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
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Active Products</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $products->where('status', 'Active')->count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
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
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Low Stock Items</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $products->filter(function($product) { return $product->getStockAttribute() < 10; })->count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
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
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Categories</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $products->pluck('category')->unique()->count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-tags fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Products Table Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Filter Products</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.products.index') }}" method="GET" class="mb-3">
                <div class="row">
                    <div class="col-md-4">
                        <label for="category" class="form-label">Filter by Category</label>
                        <select name="category" id="category" class="form-control" onchange="this.form.submit()">
                            <option value="">All Categories</option>
                            @foreach($products->pluck('category')->unique() as $category)
                                <option value="{{ $category }}" {{ $category == request()->query('category') ? 'selected' : '' }}>
                                    {{ $category }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="status" class="form-label">Filter by Status</label>
                        <select name="status" id="status" class="form-control" onchange="this.form.submit()">
                            <option value="">All Statuses</option>
                            <option value="Active" {{ request()->query('status') == 'Active' ? 'selected' : '' }}>Active</option>
                            <option value="Inactive" {{ request()->query('status') == 'Inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label d-block">&nbsp;</label>
                        <a href="{{ route('admin.products.index') }}" class="btn btn-secondary w-100">
                            <i class="fas fa-undo"></i> Reset Filters
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-list"></i> Product Inventory List
            </h6>
        </div>
        <div class="card-body">            
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-light">
                        <tr>
                            <th width="5%">ID</th>
                            <th width="20%">Product</th>
                            <th width="10%">Category</th>
                            <th width="15%">Price</th>
                            <th width="15%">Stock</th>
                            <th width="10%">Status</th>
                            <th width="25%">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($products as $product)
                            @if(
                                (request()->query('category') == '' || request()->query('category') == $product->category) &&
                                (request()->query('status') == '' || request()->query('status') == $product->status)
                            )
                            <tr class="{{ $product->getStockAttribute() == 0 ? 'table-danger' : '' }}">
                                <td>{{ $product->prod_id }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="product-icon me-2 text-primary">
                                            <i class="fas fa-box"></i>
                                        </div>
                                        <div>
                                            <strong>{{ $product->name }}</strong>
                                            <div class="small text-muted">{{ $product->supplier->company_name ?? 'No supplier' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td><span class="badge bg-secondary">{{ $product->category }}</span></td>
                                <td>
                                    <div class="fw-bold text-primary">₱{{ number_format($product->price, 2) }}</div>
                                    <small class="text-muted">per {{ $product->unit }}</small>
                                </td>
                                <td>
                                    @if($product->getStockAttribute() == 0)
                                        <span class="badge bg-danger fw-bold">Out of Stock</span>
                                    @elseif($product->getStockAttribute() < 10)
                                        <span class="badge bg-warning text-dark fw-bold">
                                            Low: {{ $product->getStockAttribute() }} {{ $product->unit }}
                                        </span>
                                    @else
                                        <span class="badge bg-success fw-bold">
                                            {{ $product->getStockAttribute() }} {{ $product->unit }}
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    @if($product->status == 'Active')
                                        <span class="badge bg-success"><i class="fas fa-check-circle"></i> Active</span>
                                    @else
                                        <span class="badge bg-secondary"><i class="fas fa-times-circle"></i> Inactive</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('admin.products.show', $product->prod_id) }}" 
                                           class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i> Details
                                        </a>
                                        <a href="{{ route('admin.products.edit', $product->prod_id) }}" 
                                           class="btn btn-sm btn-warning">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                        <button type="button" class="btn btn-sm btn-success" 
                                                onclick="location.href='{{ route('admin.products.show', $product->prod_id) }}#add-stock'">
                                            <i class="fas fa-plus-circle"></i>
                                        </button>
                                        <form action="{{ route('admin.products.destroy', $product->prod_id) }}" 
                                              method="POST" class="d-inline delete-product-form">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger delete-btn"
                                                    onclick="return confirm('Are you sure you want to delete this product? This action cannot be undone.')"
                                                    data-product-name="{{ $product->name }}">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteConfirmationModal" tabindex="-1" aria-labelledby="deleteConfirmationModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteConfirmationModalLabel">
                    <i class="fas fa-exclamation-triangle"></i> Confirm Deletion
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete the product: <strong id="productNameToDelete"></strong>?</p>
                <p class="text-danger">This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Delete Product</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        // Initialize DataTable with advanced features
        const productsTable = $('#productsTable').DataTable({
            "order": [[0, "desc"]],
            "pageLength": 10,
            "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
            "language": {
                "search": "Quick Search:",
                "lengthMenu": "Show _MENU_ products per page",
                "info": "Showing _START_ to _END_ of _TOTAL_ products"
            },
            "dom": '<"top"lf>rt<"bottom"ip>',
            "responsive": true
        });
        
        // Category filter
        $('#categoryFilter').on('change', function() {
            let category = $(this).val();
            
            productsTable.columns(2).search(category).draw();
        });
        
        // Status filter
        $('#statusFilter').on('change', function() {
            let status = $(this).val();
            
            // Custom filtering for status column
            $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
                if (!status) return true; // No filter applied
                
                const row = productsTable.row(dataIndex).node();
                const statusValue = $(row).find('.status-cell').attr('data-status');
                
                return statusValue === status;
            });
            
            productsTable.draw();
            // Remove the custom filter after drawing
            $.fn.dataTable.ext.search.pop();
        });
        
        // Reset filters button
        $('#resetFilters').on('click', function() {
            $('#categoryFilter').val('');
            $('#statusFilter').val('');
            
            // Clear all filters
            productsTable.search('').columns().search('').draw();
        });
        
        // Delete confirmation modal
        let formToSubmit = null;
        
        $('.delete-btn').on('click', function(e) {
            e.preventDefault();
            const productName = $(this).data('product-name');
            formToSubmit = $(this).closest('form');
            $('#productNameToDelete').text(productName);
            $('#deleteConfirmationModal').modal('show');
        });
        
        $('#confirmDeleteBtn').on('click', function() {
            if (formToSubmit) {
                formToSubmit.submit();
            }
            $('#deleteConfirmationModal').modal('hide');
        });
    });
</script>
@endpush
@endsection
