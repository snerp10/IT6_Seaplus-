@extends('layouts.admin')

@section('title', 'Payment Reports')

@section('admin.content')
<div class="container-fluid">
    <!-- Content Header -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><i class="fas fa-chart-bar mr-2"></i>Payment Reports &amp; Analytics</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.payments.index') }}">Payments</a></li>
                        <li class="breadcrumb-item active">Reports</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-12 mb-3">
                <a href="{{ route('admin.payments.index') }}" class="btn btn-default btn-sm">
                    <i class="fas fa-arrow-left"></i> Back to Payments
                </a>
                <a href="{{ route('admin.payments.export') }}" class="btn btn-success btn-sm ml-2">
                    <i class="fas fa-file-export"></i> Export Report Data
                </a>
            </div>
        </div>

        <!-- Date Range Filter -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Filter Report Data</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.payments.reports') }}" method="GET" class="mb-0">
                    <div class="row">
                        <div class="col-md-4">
                            <label for="date_from" class="form-label small">Start Date</label>
                            <input type="date" name="date_from" id="date_from" class="form-control form-control-sm" 
                                   value="{{ request('date_from', now()->subDays(30)->format('Y-m-d')) }}">
                        </div>
                        <div class="col-md-4">
                            <label for="date_to" class="form-label small">End Date</label>
                            <input type="date" name="date_to" id="date_to" class="form-control form-control-sm" 
                                   value="{{ request('date_to', now()->format('Y-m-d')) }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label d-block small">&nbsp;</label>
                            <button type="submit" class="btn btn-primary btn-sm mr-2">
                                <i class="fas fa-filter"></i> Apply Filter
                            </button>
                            <a href="{{ route('admin.payments.reports') }}" class="btn btn-secondary btn-sm">
                                <i class="fas fa-undo"></i> Reset
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="row mb-4">
            <!-- Total Payments -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-success shadow h-100 py-2">
                    <div class="card-body py-2">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Payments</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    ₱{{ number_format($paymentsByStatus->where('pay_status', 'completed')->sum('total'), 2) }}
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-money-bill-wave fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pending -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-warning shadow h-100 py-2">
                    <div class="card-body py-2">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Pending Payments</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    ₱{{ number_format($paymentsByStatus->where('pay_status', 'pending')->sum('total'), 2) }}
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-clock fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Failed -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-danger shadow h-100 py-2">
                    <div class="card-body py-2">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Failed Payments</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    ₱{{ number_format($paymentsByStatus->where('pay_status', 'failed')->sum('total'), 2) }}
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-times-circle fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Refunded -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-info shadow h-100 py-2">
                    <div class="card-body py-2">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Refunded</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    ₱{{ number_format($paymentsByStatus->where('pay_status', 'refunded')->sum('total'), 2) }}
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-undo-alt fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Payment Status Summary -->
            <div class="col-xl-6 col-lg-12">
                <div class="card shadow mb-4">
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold text-primary">Payment Status Summary</h6>
                    </div>
                    <div class="card-body">
                        <div class="chart-pie mb-4">
                            <canvas id="paymentStatusChart" height="300"></canvas>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm">
                                <thead class="thead-light">
                                    <tr>
                                        <th>Status</th>
                                        <th>Count</th>
                                        <th>Amount</th>
                                        <th>Percentage</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $totalAmount = $paymentsByStatus->sum('total');
                                        $statusColors = [
                                            'completed' => 'success',
                                            'pending' => 'warning',
                                            'failed' => 'danger',
                                            'refunded' => 'info'
                                        ];
                                    @endphp
                                    @foreach($paymentsByStatus as $status)
                                    <tr>
                                        <td>
                                            <span class="badge badge-{{ $statusColors[$status->pay_status] ?? 'secondary' }}">
                                                {{ ucfirst($status->pay_status) }}
                                            </span>
                                        </td>
                                        <td>{{ $status->count }}</td>
                                        <td class="text-right">₱{{ number_format($status->total, 2) }}</td>
                                        <td>{{ round(($status->total / ($totalAmount ?: 1)) * 100, 2) }}%</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="thead-light">
                                    <tr>
                                        <th>Total</th>
                                        <th>{{ $paymentsByStatus->sum('count') }}</th>
                                        <th class="text-right">₱{{ number_format($totalAmount, 2) }}</th>
                                        <th>100%</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payment Method Summary -->
            <div class="col-xl-6 col-lg-12">
                <div class="card shadow mb-4">
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold text-primary">Payment Method Distribution</h6>
                    </div>
                    <div class="card-body">
                        <div class="chart-pie mb-4">
                            <canvas id="paymentMethodChart" height="300"></canvas>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm">
                                <thead class="thead-light">
                                    <tr>
                                        <th>Method</th>
                                        <th>Count</th>
                                        <th>Amount</th>
                                        <th>Percentage</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $totalMethodAmount = $paymentsByMethod->sum('total');
                                        $methodColors = [
                                            'cash' => 'success',
                                            'credit_card' => 'primary',
                                            'debit_card' => 'secondary',
                                            'bank_transfer' => 'warning',
                                            'gcash' => 'info',
                                            'paymaya' => 'danger'
                                        ];
                                    @endphp
                                    @foreach($paymentsByMethod as $method)
                                    <tr>
                                        <td>{{ ucfirst(str_replace('_', ' ', $method->pay_method)) }}</td>
                                        <td>{{ $method->count }}</td>
                                        <td class="text-right">₱{{ number_format($method->total, 2) }}</td>
                                        <td>{{ round(($method->total / ($totalMethodAmount ?: 1)) * 100, 2) }}%</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="thead-light">
                                    <tr>
                                        <th>Total</th>
                                        <th>{{ $paymentsByMethod->sum('count') }}</th>
                                        <th class="text-right">₱{{ number_format($totalMethodAmount, 2) }}</th>
                                        <th>100%</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Daily Payment Trends -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Daily Payment Trends (Last 30 Days)</h6>
            </div>
            <div class="card-body">
                <div class="chart-area">
                    <canvas id="dailyPaymentsChart" height="100"></canvas>
                </div>
                <div class="mt-4 text-center small">
                    <span class="mr-2">
                        <i class="fas fa-circle text-primary"></i> Daily Payment Totals
                    </span>
                    <span class="mr-2">
                        <i class="fas fa-circle text-success"></i> Trend Line
                    </span>
                </div>
            </div>
        </div>
    </section>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    $(function() {
        // Status Chart
        const statusCtx = document.getElementById('paymentStatusChart').getContext('2d');
        const statusData = {
            labels: [
                @foreach($paymentsByStatus as $status)
                    '{{ ucfirst($status->pay_status) }}',
                @endforeach
            ],
            datasets: [{
                data: [
                    @foreach($paymentsByStatus as $status)
                        {{ $status->total }},
                    @endforeach
                ],
                backgroundColor: [
                    @foreach($paymentsByStatus as $status)
                        '{{ $status->pay_status == "completed" ? "#1cc88a" : 
                           ($status->pay_status == "pending" ? "#f6c23e" : 
                           ($status->pay_status == "refunded" ? "#36b9cc" : "#e74a3b")) }}',
                    @endforeach
                ],
                hoverBackgroundColor: [
                    @foreach($paymentsByStatus as $status)
                        '{{ $status->pay_status == "completed" ? "#17a673" : 
                           ($status->pay_status == "pending" ? "#dda20a" : 
                           ($status->pay_status == "refunded" ? "#258391" : "#be2617")) }}',
                    @endforeach
                ],
                hoverBorderColor: "rgba(234, 236, 244, 1)",
            }],
        };
        new Chart(statusCtx, {
            type: 'doughnut',
            data: statusData,
            options: {
                maintainAspectRatio: false,
                responsive: true,
                tooltips: {
                    callbacks: {
                        label: function(tooltipItem, data) {
                            const label = data.labels[tooltipItem.index];
                            const value = data.datasets[0].data[tooltipItem.index];
                            return `${label}: ₱${Number(value).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}`;
                        }
                    }
                },
                legend: {
                    position: 'right',
                    align: 'start'
                },
                cutout: '60%'
            }
        });

        // Method Chart
        const methodCtx = document.getElementById('paymentMethodChart').getContext('2d');
        const methodData = {
            labels: [
                @foreach($paymentsByMethod as $method)
                    '{{ ucfirst(str_replace("_", " ", $method->pay_method)) }}',
                @endforeach
            ],
            datasets: [{
                data: [
                    @foreach($paymentsByMethod as $method)
                        {{ $method->total }},
                    @endforeach
                ],
                backgroundColor: [
                    '#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b', '#5a5c69'
                ],
                hoverBackgroundColor: [
                    '#2e59d9', '#17a673', '#258391', '#dda20a', '#be2617', '#3a3b45'
                ],
                hoverBorderColor: "rgba(234, 236, 244, 1)",
            }],
        };
        new Chart(methodCtx, {
            type: 'pie',
            data: methodData,
            options: {
                maintainAspectRatio: false,
                responsive: true,
                tooltips: {
                    callbacks: {
                        label: function(tooltipItem, data) {
                            const label = data.labels[tooltipItem.index];
                            const value = data.datasets[0].data[tooltipItem.index];
                            return `${label}: ₱${Number(value).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}`;
                        }
                    }
                },
                legend: {
                    position: 'right',
                    align: 'start'
                }
            }
        });

        // Daily Payments Chart
        const dailyCtx = document.getElementById('dailyPaymentsChart').getContext('2d');
        const dailyData = {
            labels: [
                @foreach($dailyPayments as $daily)
                    '{{ \Carbon\Carbon::parse($daily->date)->format("M d") }}',
                @endforeach
            ],
            datasets: [{
                label: 'Daily Payment Total',
                data: [
                    @foreach($dailyPayments as $daily)
                        {{ $daily->total }},
                    @endforeach
                ],
                backgroundColor: 'rgba(78, 115, 223, 0.05)',
                borderColor: 'rgba(78, 115, 223, 1)',
                pointRadius: 3,
                pointBackgroundColor: 'rgba(78, 115, 223, 1)',
                pointBorderColor: 'rgba(78, 115, 223, 1)',
                pointHoverRadius: 5,
                pointHoverBackgroundColor: 'rgba(78, 115, 223, 1)',
                pointHoverBorderColor: 'rgba(78, 115, 223, 1)',
                pointHitRadius: 10,
                pointBorderWidth: 2,
                fill: true,
                lineTension: 0.3
            }]
        };
        new Chart(dailyCtx, {
            type: 'line',
            data: dailyData,
            options: {
                maintainAspectRatio: false,
                scales: {
                    x: {
                        grid: {
                            display: false
                        }
                    },
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return '₱' + Number(value).toLocaleString('en-US');
                            }
                        }
                    }
                },
                tooltips: {
                    callbacks: {
                        label: function(tooltipItem) {
                            return 'Total: ₱' + Number(tooltipItem.raw).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                        }
                    }
                }
            }
        });
    });
</script>
@endpush
@endsection
