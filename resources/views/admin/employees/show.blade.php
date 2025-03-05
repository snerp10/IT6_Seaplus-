@extends('layouts.admin')

@section('title', 'Employee Details')

@section('admin.content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-user text-dark mr-2"></i> Employee Details
        </h1>
        <div>
            <a href="{{ route('admin.employees.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Back to Employees
            </a>
            <a href="{{ route('admin.employees.edit', $employee->emp_id) }}" class="btn btn-primary btn-sm">
                <i class="fas fa-edit"></i> Edit Employee
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
        <!-- Main Employee Details Card -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-info-circle mr-1"></i> Personal Information
                    </h6>
                    <span class="badge bg-{{ $employee->status == 'Active' ? 'success' : 'danger' }} text-white px-3 py-2">
                        {{ $employee->status ?? 'Active' }}
                    </span>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <tbody>
                                <tr>
                                    <th style="width: 30%">Employee ID</th>
                                    <td>EMP-{{ str_pad($employee->emp_id, 5, '0', STR_PAD_LEFT) }}</td>
                                </tr>
                                <tr>
                                    <th>Full Name</th>
                                    <td>
                                        <strong>{{ $employee->fname }} {{ $employee->mname }} {{ $employee->lname }}</strong>
                                        @if($employee->nickname)
                                            <span class="ml-2 text-muted">({{ $employee->nickname }})</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Gender</th>
                                    <td>{{ $employee->gender ?? 'Not specified' }}</td>
                                </tr>
                                <tr>
                                    <th>Birthdate</th>
                                    <td>
                                        {{ \Carbon\Carbon::parse($employee->birthdate)->format('F d, Y') }}
                                        <span class="ml-2 text-muted">
                                            ({{ \Carbon\Carbon::parse($employee->birthdate)->age }} years old)
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Civil Status</th>
                                    <td>{{ $employee->civil_status ?? 'Not specified' }}</td>
                                </tr>
                                <tr>
                                    <th>Contact Number</th>
                                    <td>{{ $employee->contact_number }}</td>
                                </tr>
                                <tr>
                                    <th>Email</th>
                                    <td>{{ $employee->email }}</td>
                                </tr>
                                <tr>
                                    <th>Address</th>
                                    <td>{{ $employee->street . ', ' . $employee->barangay . ', ' . $employee->city }}</td>
                                </tr>
                                <tr>
                                    <th>Emergency Contact</th>
                                    <td>
                                        <div>{{ $employee->emergency_contact_name ?? 'Not specified' }}</div>
                                        <div>{{ $employee->emergency_contact_number ?? 'Not specified' }}</div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <!-- Employment History -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-history mr-1"></i> Employment History
                    </h6>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        <!-- This would ideally be populated from a employment_history table -->
                        <div class="timeline-item">
                            <div class="timeline-marker bg-success"></div>
                            <div class="timeline-content">
                                <h4 class="timeline-title">{{ $employee->position }}</h4>
                                <p class="timeline-date">{{ \Carbon\Carbon::parse($employee->hire_date ?? now()->subYears(1))->format('M d, Y') }} - Present</p>
                                <p>Started working as {{ $employee->position }} with a salary of ₱{{ number_format($employee->salary, 2) }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Side Cards -->
        <div class="col-xl-4 col-lg-5">
            <!-- Employee Profile Card -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-id-badge mr-1"></i> Employee Profile
                    </h6>
                </div>
                <div class="card-body text-center">
                    <img src="{{ $employee->photo_url ?? asset('images/employees/default.png') }}" alt="{{ $employee->fname }} {{ $employee->lname }}" 
                        class="img-profile rounded-circle mb-3" style="width: 150px; height: 150px; object-fit: cover; border: 5px solid #f8f9fc;">
                    
                    <h5>{{ $employee->fname }} {{ $employee->lname }}</h5>
                    <span class="badge badge-{{ 
                        $employee->position == 'Driver' ? 'success' : 
                        ($employee->position == 'Cashier' ? 'info' : 
                        ($employee->position == 'Laborer' ? 'warning' : 
                        ($employee->position == 'Admin' ? 'primary' : 'secondary'))) 
                    }} mb-3">{{ $employee->position }}</span>
                    
                    <div class="text-left mt-4">
                        <div class="row mb-2">
                            <div class="col-6 text-muted">Employee ID:</div>
                            <div class="col-6">EMP-{{ str_pad($employee->emp_id, 5, '0', STR_PAD_LEFT) }}</div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-6 text-muted">Department:</div>
                            <div class="col-6">{{ $employee->department ?? 'Operations' }}</div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-6 text-muted">Hire Date:</div>
                            <div class="col-6">{{ \Carbon\Carbon::parse($employee->hire_date ?? now()->subYears(1))->format('M d, Y') }}</div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-6 text-muted">Monthly Salary:</div>
                            <div class="col-6">₱{{ number_format($employee->salary, 2) }}</div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-6 text-muted">Status:</div>
                            <div class="col-6">
                                <span class="badge bg-{{ $employee->status == 'Active' ? 'success' : 'danger' }}">
                                    {{ $employee->status ?? 'Active' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Document Information Card -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-file-alt mr-1"></i> Document Information
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row mb-2">
                        <div class="col-6 text-muted">SSS Number:</div>
                        <div class="col-6">{{ $employee->sss_number ?? 'Not provided' }}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-6 text-muted">TIN Number:</div>
                        <div class="col-6">{{ $employee->tin_number ?? 'Not provided' }}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-6 text-muted">PhilHealth:</div>
                        <div class="col-6">{{ $employee->philhealth_number ?? 'Not provided' }}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-6 text-muted">HDMF/Pag-IBIG:</div>
                        <div class="col-6">{{ $employee->pagibig_number ?? 'Not provided' }}</div>
                    </div>
                    
                    <div class="mt-3">
                        <h6 class="font-weight-bold">Driver Information</h6>
                        <div class="row mb-2">
                            <div class="col-6 text-muted">License No:</div>
                            <div class="col-6">{{ $employee->drivers_license_number ?? 'N/A' }}</div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-6 text-muted">License Type:</div>
                            <div class="col-6">{{ $employee->drivers_license_type ?? 'N/A' }}</div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-6 text-muted">Expiry Date:</div>
                            <div class="col-6">{{ $employee->drivers_license_expiry ? \Carbon\Carbon::parse($employee->drivers_license_expiry)->format('M d, Y') : 'N/A' }}</div>
                        </div>
                    </div>
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
                    <p>Are you sure you want to delete this employee record? This action cannot be undone.</p>
                    <p><strong>Employee:</strong> {{ $employee->fname }} {{ $employee->lname }}</p>
                    <p><strong>Position:</strong> {{ $employee->position }}</p>
                    <p><strong>Hire Date:</strong> {{ \Carbon\Carbon::parse($employee->hire_date ?? now()->subYears(1))->format('M d, Y') }}</p>
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
@endsection

@push('scripts')
<script>
    $(function() {
        // Initialize tooltips
        $('[data-toggle="tooltip"]').tooltip();
    });
    
    // Add custom styles for timeline
    document.addEventListener('DOMContentLoaded', function() {
        const style = document.createElement('style');
        style.textContent = `
            .timeline {
                position: relative;
                padding-left: 30px;
            }
            .timeline-item {
                position: relative;
                padding-bottom: 20px;
                border-left: 2px solid #e4e4e4;
                margin-left: 10px;
                padding-left: 20px;
            }
            .timeline-marker {
                position: absolute;
                left: -6px;
                top: 0;
                width: 10px;
                height: 10px;
                border-radius: 50%;
            }
            .timeline-title {
                font-size: 18px;
                margin-bottom: 5px;
            }
            .timeline-date {
                color: #6c757d;
                font-size: 14px;
                margin-bottom: 10px;
            }
        `;
        document.head.appendChild(style);
    });
</script>
@endpush