@extends('layouts.admin')

@section('admin.content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Manage Deliveries</h2>
        <a href="{{ route('admin.deliveries.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> New Delivery
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Customer</th>
                            <th>Status</th>
                            <th>Est. Delivery Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($deliveries as $delivery)
                        <tr>
                            <td>{{ $delivery->order->order_id }}</td>
                            <td>{{ $delivery->order->customer->fname . ' ' . $delivery->order->customer->mname . ' ' . $delivery->order->customer->lname }}</td>
                            <td>
                                <span class="badge bg-{{ $delivery->delivery_status === 'Delivered' ? 'success' : 'info' }}">
                                    {{ $delivery->delivery_status }}
                                </span>
                            </td>
                            <td>{{ $delivery->delivery_date ? $delivery->delivery_date->format('M d, Y') : 'Not set' }}</td>
                            <td>
                                <div class="btn-group">
                                    <a href="{{ route('admin.deliveries.show', $delivery) }}" class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.deliveries.edit', $delivery) }}" class="btn btn-sm btn-warning">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.deliveries.destroy', $delivery) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            {{ $deliveries->links() }}
        </div>
    </div>
</div>
@endsection