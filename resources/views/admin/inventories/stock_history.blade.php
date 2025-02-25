@extends('layouts.admin')

@section('admin.content')
<div class="container">
    <h1>Stock Movement History</h1>
    <a href="{{ route('admin.inventories.index') }}" class="btn btn-secondary mb-3"><i class="fas fa-arrow-left"></i> Back</a>
    <table class="table">
        <thead>
            <tr>
                <th>Movement Type</th>
                <th>Quantity</th>
                <th>Current Stock</th>
                <th>Movement Date</th>
            </tr>
        </thead>
        <tbody>
            @foreach($stockHistory as $history)
            <tr>
                <td>{{ $history->move_type }}</td>
                <td>{{ $history->quantity }}</td>
                <td>{{ $history->curr_stock }}</td>
                <td>{{ $history->move_date }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection

