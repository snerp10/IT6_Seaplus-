@extends('layouts.admin')

@section('title', 'Employee Management')

@section('admin.content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-users text-dark mr-2"></i> Employee Management
        </h1>
        <div>
            <a href="{{ route('admin.employees.create') }}" class="btn btn-info btn-sm mr-2">
                <i class="fas fa-file-export"></i> Export Data
            </a>
            <a href="{{ route('admin.employees.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-user-plus"></i> Add Employee
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

    <!-- Employee Statistics -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Employees</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $employees->count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
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
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Driver Count</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $employees->where('position', 'Driver')->count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-truck fa-2x text-gray-300"></i>
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
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Staff Count</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $employees->where('position', 'Cashier')->count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-tie fa-2x text-gray-300"></i>
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
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Labor Count</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $employees->where('position', 'Laborer')->count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-hard-hat fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Filter Employees</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.employees.index') }}" method="GET" class="mb-0">
                <div class="row">
                    <div class="col-md-4">
                        <label for="position" class="form-label small">Position</label>
                        <select name="position" id="position" class="form-control form-control-sm" onchange="this.form.submit()">
                            <option value="">All Positions</option>
                            <option value="Cashier" {{ request('position') == 'Cashier' ? 'selected' : '' }}>Cashier</option>
                            <option value="Driver" {{ request('position') == 'Driver' ? 'selected' : '' }}>Driver</option>
                            <option value="Laborer" {{ request('position') == 'Laborer' ? 'selected' : '' }}>Laborer</option>
                            <option value="Admin" {{ request('position') == 'Admin' ? 'selected' : '' }}>Admin</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="search" class="form-label small">Search</label>
                        <input type="text" name="search" id="search" class="form-control form-control-sm" value="{{ request('search') }}" placeholder="Name, Email or Contact #">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label d-block small">&nbsp;</label>
                        <div class="d-flex">
                            <button type="submit" class="btn btn-primary btn-sm mr-2">
                                <i class="fas fa-search"></i> Search
                            </button>
                            <a href="{{ route('admin.employees.index') }}" class="btn btn-secondary btn-sm">
                                <i class="fas fa-undo"></i> Reset
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Employees Table Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-list"></i> Employee List
            </h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover" id="employeesTable">
                    <thead class="thead-dark">
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Position</th>
                            <th>Contact</th>
                            <th>Email</th>
                            <th>Salary</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($employees as $employee)
                            <tr>
                                <td>{{ $employee->emp_id }}</td>
                                <td>
                                    <strong>{{ $employee->fname }} {{ $employee->lname }}</strong>
                                    @if($employee->mname)
                                        <small class="d-block text-muted">{{ $employee->mname }}</small>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-{{ 
                                        $employee->position == 'Driver' ? 'success' : 
                                        ($employee->position == 'Staff' ? 'info' : 
                                        ($employee->position == 'Labor' ? 'warning' : 
                                        ($employee->position == 'Supervisor' ? 'primary' : 'secondary'))) 
                                    }}">
                                        {{ $employee->position }}
                                    </span>
                                </td>
                                <td>{{ $employee->contact_number }}</td>
                                <td>{{ $employee->email }}</td>
                                <td>₱{{ number_format($employee->salary, 2) }}</td>
                                <td>
                                    <span class="badge bg-{{ $employee->status == 'Active' ? 'success' : 'danger' }}">
                                        {{ $employee->status ?? 'Active' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <a href="{{ route('admin.employees.show', $employee->emp_id) }}" class="btn btn-sm btn-info" title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.employees.edit', $employee->emp_id) }}" class="btn btn-sm btn-primary" title="Edit Employee">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-danger" 
                                                data-toggle="modal" 
                                                data-target="#deleteModal{{ $employee->emp_id }}"
                                                title="Delete Employee">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                        
                                        <!-- Delete Modal -->
                                        <div class="modal fade" id="deleteModal{{ $employee->emp_id }}" tabindex="-1" role="dialog" aria-hidden="true">
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
                                                        <p>Are you sure you want to delete this employee record? This action cannot be undone.</p>
                                                        <p><strong>Employee:</strong> {{ $employee->fname }} {{ $employee->lname }}</p>
                                                        <p><strong>Position:</strong> {{ $employee->position }}</p>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                                        <form action="{{ route('admin.employees.destroy', $employee->emp_id) }}" method="POST">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-danger">Delete</button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center">No employees found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="mt-3">
                {{ $employees->appends(request()->query())->links() }}
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
        $('#employeesTable').DataTable({
            "paging": false,
            "lengthChange": false,
            "searching": false,
            "ordering": true,
            "info": false,
            "autoWidth": false,
            "responsive": true,
            "columnDefs": [
                {"orderable": false, "targets": 7}
            ]
        });
    });
</script>
@endpush
@endsection

