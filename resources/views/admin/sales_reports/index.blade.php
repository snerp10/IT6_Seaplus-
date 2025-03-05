@extends('layouts.admin')

@section('title', 'Sales Reports')

@section('admin.content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-chart-line text-dark mr-2"></i> Sales Reports
        </h1>
        <div>
            <a href="{{ route('admin.sales_reports.export') }}" class="btn btn-info btn-sm">
                <i class="fas fa-file-csv"></i> Export CSV
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

    <!-- Sales Summary Statistics -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Sales</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">₱{{ number_format($totalSales, 2) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-money-bill-wave fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Orders</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $dailySales->count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-shopping-cart fa-2x text-gray-300"></i>
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
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Average Order Value</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                ₱{{ $dailySales->count() ? number_format($totalSales / $dailySales->count(), 2) : '0.00' }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calculator fa-2x text-gray-300"></i>
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
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Products Sold</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $topProducts->sum('units_sold') }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-box fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Date Range Filter -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Filter Sales Data</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.sales_reports.index') }}" method="GET" class="mb-0">
                <div class="row">
                    <div class="col-md-4">
                        <label for="date_from" class="form-label small">Start Date</label>
                        <input type="date" name="date_from" id="date_from" class="form-control form-control-sm" 
                               value="{{ $startDate->format('Y-m-d') }}">
                    </div>
                    <div class="col-md-4">
                        <label for="date_to" class="form-label small">End Date</label>
                        <input type="date" name="date_to" id="date_to" class="form-control form-control-sm" 
                               value="{{ $endDate->format('Y-m-d') }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label d-block small">&nbsp;</label>
                        <button type="submit" class="btn btn-primary btn-sm mr-2">
                            <i class="fas fa-filter"></i> Apply Filter
                        </button>
                        <a href="{{ route('admin.sales_reports.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-undo"></i> Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="row">
        <!-- Sales Trend Chart -->
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Sales Trend</h6>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="salesTrendChart" height="300"></canvas>
                    </div>
                    <div class="mt-4 text-center small">
                        <span class="mr-2">
                            <i class="fas fa-circle text-primary"></i> Daily Sales
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payment Method Distribution -->
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Payment Methods</h6>
                </div>
                <div class="card-body">
                    <div class="chart-pie mb-4">
                        <canvas id="paymentMethodsChart" height="250"></canvas>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered">
                            <thead>
                                <tr>
                                    <th>Method</th>
                                    <th>Count</th>
                                    <th>Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($paymentMethods as $method)
                                <tr>
                                    <td>{{ ucfirst(str_replace('_', ' ', $method->pay_method)) }}</td>
                                    <td>{{ $method->count }}</td>
                                    <td>₱{{ number_format($method->total, 2) }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="text-center">No payment data available</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Top Products -->
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Top Selling Products</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead class="thead-dark">
                                <tr>
                                    <th>Product</th>
                                    <th>Category</th>
                                    <th>Units Sold</th>
                                    <th>Revenue</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($topProducts as $product)
                                <tr>
                                    <td>{{ $product->name }}</td>
                                    <td>{{ $product->category }}</td>
                                    <td>{{ $product->units_sold }}</td>
                                    <td>₱{{ number_format($product->revenue, 2) }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center">No products data available</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sales by Category -->
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Sales by Category</h6>
                </div>
                <div class="card-body">
                    <div class="chart-pie mb-4">
                        <canvas id="categoryChart" height="250"></canvas>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered">
                            <thead>
                                <tr>
                                    <th>Category</th>
                                    <th>Amount</th>
                                    <th>%</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $totalCategorySales = $salesByCategory->sum('total'); @endphp
                                @forelse($salesByCategory as $category)
                                <tr>
                                    <td>{{ $category->category }}</td>
                                    <td>₱{{ number_format($category->total, 2) }}</td>
                                    <td>{{ number_format(($category->total / ($totalCategorySales ?: 1)) * 100, 1) }}%</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="text-center">No category data available</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Year over Year Comparison -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Monthly Sales Comparison ({{ Carbon\Carbon::now()->subYear()->year }} vs {{ Carbon\Carbon::now()->year }})</h6>
        </div>
        <div class="card-body">
            <div class="chart-bar">
                <canvas id="yearlyComparisonChart" height="300"></canvas>
            </div>
        </div>
    </div>

</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@2.9.4/dist/Chart.min.js"></script>
<script>
    $(function() {
        // Sales Trend Chart
        const salesTrendCtx = document.getElementById('salesTrendChart').getContext('2d');
        new Chart(salesTrendCtx, {
            type: 'line',
            data: {
                labels: [
                    @foreach($dailySales as $sale)
                        '{{ Carbon\Carbon::parse($sale->date)->format("M d") }}',
                    @endforeach
                ],
                datasets: [{
                    label: 'Daily Sales',
                    data: [
                        @foreach($dailySales as $sale)
                            {{ $sale->total }},
                        @endforeach
                    ],
                    backgroundColor: 'rgba(78, 115, 223, 0.05)',
                    borderColor: 'rgba(78, 115, 223, 1)',
                    pointRadius: 3,
                    pointBackgroundColor: 'rgba(78, 115, 223, 1)',
                    pointBorderColor: 'rgba(78, 115, 223, 1)',
                    pointHoverRadius: 3,
                    pointHoverBackgroundColor: 'rgba(78, 115, 223, 1)',
                    pointHoverBorderColor: 'rgba(78, 115, 223, 1)',
                    pointHitRadius: 10,
                    pointBorderWidth: 2,
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                maintainAspectRatio: false,
                layout: {
                    padding: {
                        left: 10,
                        right: 25,
                        top: 25,
                        bottom: 0
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false,
                            drawBorder: false
                        },
                        ticks: {
                            maxTicksLimit: 7
                        }
                    },
                    y: {
                        ticks: {
                            maxTicksLimit: 5,
                            padding: 10,
                            callback: function(value) {
                                return '₱' + value.toLocaleString();
                            }
                        },
                        grid: {
                            color: "rgb(234, 236, 244)",
                            zeroLineColor: "rgb(234, 236, 244)",
                            drawBorder: false,
                            borderDash: [2],
                            zeroLineBorderDash: [2]
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: "rgb(255,255,255)",
                        bodyColor: "#858796",
                        titleMarginBottom: 10,
                        titleColor: '#6e707e',
                        titleFontSize: 14,
                        borderColor: '#dddfeb',
                        borderWidth: 1,
                        xPadding: 15,
                        yPadding: 15,
                        displayColors: false,
                        intersect: false,
                        mode: 'index',
                        caretPadding: 10,
                        callbacks: {
                            label: function(tooltipItem) {
                                return 'Sales: ₱' + tooltipItem.raw.toLocaleString();
                            }
                        }
                    }
                }
            }
        });

        // Payment Methods Chart
        const paymentMethodsCtx = document.getElementById('paymentMethodsChart').getContext('2d');
        new Chart(paymentMethodsCtx, {
            type: 'doughnut',
            data: {
                labels: [
                    @foreach($paymentMethods as $method)
                        '{{ ucfirst(str_replace("_", " ", $method->pay_method)) }}',
                    @endforeach
                ],
                datasets: [{
                    data: [
                        @foreach($paymentMethods as $method)
                            {{ $method->total }},
                        @endforeach
                    ],
                    backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b'],
                    hoverBackgroundColor: ['#2e59d9', '#17a673', '#2c9faf', '#dda20a', '#be2617'],
                    hoverBorderColor: "rgba(234, 236, 244, 1)",
                }],
            },
            options: {
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'bottom'
                    },
                    tooltip: {
                        callbacks: {
                            label: function(tooltipItem) {
                                const dataLabel = tooltipItem.label;
                                const value = tooltipItem.raw;
                                return dataLabel + ': ₱' + value.toLocaleString();
                            }
                        }
                    }
                },
                cutout: '70%'
            }
        });

        // Category Chart
        const categoryCtx = document.getElementById('categoryChart').getContext('2d');
        new Chart(categoryCtx, {
            type: 'pie',
            data: {
                labels: [
                    @foreach($salesByCategory as $category)
                        '{{ $category->category }}',
                    @endforeach
                ],
                datasets: [{
                    data: [
                        @foreach($salesByCategory as $category)
                            {{ $category->total }},
                        @endforeach
                    ],
                    backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b', '#5a5c69'],
                    hoverBackgroundColor: ['#2e59d9', '#17a673', '#2c9faf', '#dda20a', '#be2617', '#373840'],
                    hoverBorderColor: "rgba(234, 236, 244, 1)",
                }],
            },
            options: {
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'bottom'
                    },
                    tooltip: {
                        callbacks: {
                            label: function(tooltipItem) {
                                const dataLabel = tooltipItem.label;
                                const value = tooltipItem.raw;
                                const total = tooltipItem.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = Math.round((value / total) * 100);
                                return dataLabel + ': ₱' + value.toLocaleString() + ' (' + percentage + '%)';
                            }
                        }
                    }
                }
            }
        });

        // Yearly Comparison Chart
        const yearlyCtx = document.getElementById('yearlyComparisonChart').getContext('2d');
        const months = ['Jan
</script>
@endpush
@endsection
