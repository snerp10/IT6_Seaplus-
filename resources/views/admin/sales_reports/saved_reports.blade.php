@extends('layouts.admin')

@section('title', 'Saved Sales Reports')

@section('admin.content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-bookmark text-primary mr-2"></i> Saved Reports
        </h1>
        <div>
            <a href="{{ route('admin.sales_reports.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Create New Report
            </a>
            <a href="{{ route('admin.sales_reports.index') }}" class="btn btn-secondary btn-sm ml-2">
                <i class="fas fa-chart-line"></i> Default Report
            </a>
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

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Your Saved Reports</h6>
        </div>
        <div class="card-body">
            @if($reports->isEmpty())
                <div class="text-center py-5">
                    <i class="fas fa-file-alt fa-4x text-gray-300 mb-3"></i>
                    <p class="text-muted">No saved reports found. Create your first report to get started!</p>
                    <a href="{{ route('admin.sales_reports.create') }}" class="btn btn-primary mt-2">
                        <i class="fas fa-plus mr-1"></i> Create Report
                    </a>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" id="reportsTable">
                        <thead class="thead-light">
                            <tr>
                                <th>Name</th>
                                <th>Type</th>
                                <th>Date Range</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($reports as $report)
                                <tr>
                                    <td>
                                        <a href="{{ route('admin.sales_reports.show', $report->report_id) }}" class="font-weight-bold text-primary">
                                            {{ $report->name }}
                                        </a>
                                        @if($report->description)
                                            <div class="small text-muted">{{ Str::limit($report->description, 50) }}</div>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge badge-{{ 
                                            $report->report_type === 'daily' ? 'primary' : 
                                            ($report->report_type === 'monthly' ? 'success' : 
                                            ($report->report_type === 'product' ? 'info' : 'secondary')) 
                                        }}">
                                            {{ ucfirst($report->report_type) }}
                                        </span>
                                    </td>
                                    <td>{{ $report->date_from->format('M d, Y') }} - {{ $report->date_to->format('M d, Y') }}</td>
                                    <td>{{ $report->created_at->format('M d, Y') }}</td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="{{ route('admin.sales_reports.show', $report->report_id) }}" class="btn btn-sm btn-info" title="View">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.sales_reports.edit', $report->report_id) }}" class="btn btn-sm btn-primary" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-danger" data-toggle="modal" data-target="#deleteModal{{ $report->report_id }}" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>

                                        <!-- Delete Modal -->
                                        <div class="modal fade" id="deleteModal{{ $report->report_id }}" tabindex="-1" role="dialog" aria-hidden="true">
                                            <div class="modal-dialog" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header bg-danger text-white">
                                                        <h5 class="modal-title">
                                                            <i class="fas fa-trash-alt"></i> Delete Report
                                                        </h5>
                                                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <p>Are you sure you want to delete the report <strong>{{ $report->name }}</strong>?</p>
                                                        <p>This action cannot be undone.</p>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                                        <form action="{{ route('admin.sales_reports.destroy', $report->report_id) }}" method="POST">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-danger">Delete</button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <div class="mt-3">
                    {{ $reports->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
    $(function() {
        // Initialize DataTable
        $('#reportsTable').DataTable({
            "paging": false,
            "searching": true,
            "ordering": true,
            "info": false,
            "responsive": true
        });
    });
</script>
@endpush
@endsection
