@extends('layouts.admin')

@section('title', 'Sales Reports')

@php
// Define the chart color function in PHP context
function getChartColor($index) {
    $colors = [
        '#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b', 
        '#6f42c1', '#fd7e14', '#20c9a6', '#858796', '#5a5c69'
    ];
    return $colors[$index % count($colors)];
}
@endphp

@section('admin.content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-chart-line text-primary mr-2"></i> Sales Reports
        </h1>
        <div>
            <a href="{{ route('admin.sales_reports.saved') }}" class="btn btn-info btn-sm mr-2">
                <i class="fas fa-bookmark"></i> Saved Reports
            </a>
            <a href="{{ route('admin.sales_reports.create') }}" class="btn btn-primary btn-sm mr-2">
                <i class="fas fa-plus"></i> Create Report
            </a>
            <a href="#" class="btn btn-success btn-sm" data-toggle="modal" data-target="#exportModal">
                <i class="fas fa-file-export"></i> Export Report
            </a>
            <button class="btn btn-secondary btn-sm ml-2" onclick="window.print()">
                <i class="fas fa-print"></i> Print Report
            </button>
        </div>
    </div>
    <!-- Sales Summary Row -->
    <div class="row mb-4">
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Sales ({{ $startDate->format('M d') }} - {{ $endDate->format('M d') }})
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">₱{{ number_format($totalSales, 2) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Average Daily Sales
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                ₱{{ number_format($dailySales->count() > 0 ? $totalSales / $dailySales->count() : 0, 2) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Total Transactions
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $paymentMethods->sum('count') }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-receipt fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Date Range Filter -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Filter Date Range</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.sales_reports.index') }}" method="GET" class="form-inline">
                <div class="form-group mb-2 mr-2">
                    <label for="date_from" class="mr-2">From:</label>
                    <input type="date" id="date_from" name="date_from" class="form-control" 
                           value="{{ $startDate->format('Y-m-d') }}">
                </div>
                <div class="form-group mb-2 mr-2">
                    <label for="date_to" class="mr-2">To:</label>
                    <input type="date" id="date_to" name="date_to" class="form-control" 
                           value="{{ $endDate->format('Y-m-d') }}">
                </div>
                <button type="submit" class="btn btn-primary mb-2">
                    <i class="fas fa-filter"></i> Apply Filter
                </button>
            </form>
        </div>
    </div>

    <div class="row">
        <!-- Daily Sales Chart -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Daily Sales</h6>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="dailySalesChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payment Methods Breakdown -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Payment Methods</h6>
                </div>
                <div class="card-body">
                    <div class="chart-pie pt-4 pb-2">
                        <canvas id="paymentMethodsChart"></canvas>
                    </div>
                    <div class="mt-4 text-center small">
                        @foreach($paymentMethods as $index => $method)
                        <span class="mr-2">
                            <i class="fas fa-circle" style="color: {{ getChartColor($index) }}"></i> {{ $method->pay_method }}
                        </span>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Category Sales Breakdown -->
        <div class="col-xl-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Sales by Category</h6>
                </div>
                <div class="card-body">
                    <div class="chart-bar">
                        <canvas id="categoryChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top Products -->
        <div class="col-xl-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Top Products</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" width="100%" cellspacing="0">
                            <thead class="thead-light">
                                <tr>
                                    <th>Product</th>
                                    <th>Category</th>
                                    <th class="text-center">Units Sold</th>
                                    <th class="text-right">Revenue</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($topProducts as $product)
                                <tr>
                                    <td>{{ $product->name }}</td>
                                    <td>{{ $product->category }}</td>
                                    <td class="text-center">{{ $product->units_sold }}</td>
                                    <td class="text-right">₱{{ number_format($product->revenue, 2) }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center">No product sales data available</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Export Modal -->
<div class="modal fade" id="exportModal" tabindex="-1" aria-labelledby="exportModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exportModalLabel">Export Sales Report</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="{{ route('admin.sales_reports.export') }}" method="GET" id="exportForm">
                    <div class="form-group">
                        <label for="export_date_from">Date From</label>
                        <input type="date" id="export_date_from" name="date_from" class="form-control" 
                               value="{{ $startDate->format('Y-m-d') }}">
                    </div>
                    <div class="form-group">
                        <label for="export_date_to">Date To</label>
                        <input type="date" id="export_date_to" name="date_to" class="form-control" 
                               value="{{ $endDate->format('Y-m-d') }}">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="submit" form="exportForm" class="btn btn-success">
                    <i class="fas fa-file-export"></i> Export
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Helper function to get chart colors
function getChartColor(index) {
    const colors = [
        '#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b', 
        '#6f42c1', '#fd7e14', '#20c9a6', '#858796', '#5a5c69'
    ];
    return colors[index % colors.length];
}

// Format number utility function
function number_format(number, decimals, dec_point, thousands_sep) {
    // *     example: number_format(1234.56, 2, ',', ' ');
    // *     return: '1 234,56'
    number = (number + '').replace(',', '').replace(' ', '');
    var n = !isFinite(+number) ? 0 : +number,
        prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
        sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
        dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
        s = '',
        toFixedFix = function(n, prec) {
            var k = Math.pow(10, prec);
            return '' + Math.round(n * k) / k;
        };
    // Fix for IE parseFloat(0.55).toFixed(0) = 0;
    s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
    if (s[0].length > 3) {
        s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
    }
    if ((s[1] || '').length < prec) {
        s[1] = s[1] || '';
        s[1] += new Array(prec - s[1].length + 1).join('0');
    }
    return s.join(dec);
}

document.addEventListener('DOMContentLoaded', function() {
    // Set new default font family and font color to mimic Bootstrap's default styling
    Chart.defaults.global.defaultFontFamily = 'Nunito', '-apple-system,system-ui,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif';
    Chart.defaults.global.defaultFontColor = '#858796';

    // Daily Sales Chart
    var ctx = document.getElementById("dailySalesChart");
    if (ctx) {
        var dailyLabels = [];
        var dailyData = [];
        
        @foreach($dailySales as $daily)
            dailyLabels.push("{{ \Carbon\Carbon::parse($daily->date)->format('M d') }}");
            dailyData.push({{ $daily->total }});
        @endforeach
        
        var myLineChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: dailyLabels,
                datasets: [{
                    label: "Revenue",
                    lineTension: 0.3,
                    backgroundColor: "rgba(78, 115, 223, 0.05)",
                    borderColor: "rgba(78, 115, 223, 1)",
                    pointRadius: 3,
                    pointBackgroundColor: "rgba(78, 115, 223, 1)",
                    pointBorderColor: "rgba(78, 115, 223, 1)",
                    pointHoverRadius: 3,
                    pointHoverBackgroundColor: "rgba(78, 115, 223, 1)",
                    pointHoverBorderColor: "rgba(78, 115, 223, 1)",
                    pointHitRadius: 10,
                    pointBorderWidth: 2,
                    data: dailyData,
                }],
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
                    xAxes: [{
                        time: {
                            unit: 'date'
                        },
                        gridLines: {
                            display: false,
                            drawBorder: false
                        },
                        ticks: {
                            maxTicksLimit: 7
                        }
                    }],
                    yAxes: [{
                        ticks: {
                            maxTicksLimit: 5,
                            padding: 10,
                            // Include a peso sign in the ticks
                            callback: function(value, index, values) {
                                return '₱' + number_format(value);
                            }
                        },
                        gridLines: {
                            color: "rgb(234, 236, 244)",
                            zeroLineColor: "rgb(234, 236, 244)",
                            drawBorder: false,
                            borderDash: [2],
                            zeroLineBorderDash: [2]
                        }
                    }],
                },
                legend: {
                    display: false
                },
                tooltips: {
                    backgroundColor: "rgb(255,255,255)",
                    bodyFontColor: "#858796",
                    titleMarginBottom: 10,
                    titleFontColor: '#6e707e',
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
                        label: function(tooltipItem, chart) {
                            var datasetLabel = chart.datasets[tooltipItem.datasetIndex].label || '';
                            return datasetLabel + ': ₱' + number_format(tooltipItem.yLabel);
                        }
                    }
                }
            }
        });
    }

    // Payment Methods Pie Chart
    var ctxPie = document.getElementById("paymentMethodsChart");
    if (ctxPie) {
        var methodLabels = [];
        var methodData = [];
        var methodColors = [];
        
        @foreach($paymentMethods as $index => $method)
            methodLabels.push("{{ $method->pay_method }}");
            methodData.push({{ $method->total }});
            methodColors.push(getChartColor({{ $index }}));
        @endforeach
        
        var myPieChart = new Chart(ctxPie, {
            type: 'doughnut',
            data: {
                labels: methodLabels,
                datasets: [{
                    data: methodData,
                    backgroundColor: methodColors,
                    hoverBackgroundColor: methodColors,
                    hoverBorderColor: "rgba(234, 236, 244, 1)",
                }],
            },
            options: {
                maintainAspectRatio: false,
                tooltips: {
                    backgroundColor: "rgb(255,255,255)",
                    bodyFontColor: "#858796",
                    borderColor: '#dddfeb',
                    borderWidth: 1,
                    xPadding: 15,
                    yPadding: 15,
                    displayColors: false,
                    caretPadding: 10,
                    callbacks: {
                        label: function(tooltipItem, data) {
                            var dataset = data.datasets[tooltipItem.datasetIndex];
                            var total = dataset.data.reduce(function(previousValue, currentValue) {
                                return previousValue + currentValue;
                            });
                            var currentValue = dataset.data[tooltipItem.index];
                            var percentage = Math.floor(((currentValue/total) * 100)+0.5);
                            return data.labels[tooltipItem.index] + ': ₱' + number_format(currentValue) + ' (' + percentage + '%)';
                        }
                    }
                },
                legend: {
                    display: true,
                    position: 'bottom'
                },
                cutoutPercentage: 70,
            },
        });
    }

    // Category Bar Chart
    var ctxBar = document.getElementById("categoryChart");
    if (ctxBar) {
        var catLabels = [];
        var catData = [];
        var catColors = [];
        
        @foreach($salesByCategory as $index => $category)
            catLabels.push("{{ $category->category }}");
            catData.push({{ $category->total }});
            catColors.push(getChartColor({{ $index }}));
        @endforeach
        
        var myBarChart = new Chart(ctxBar, {
            type: 'bar',
            data: {
                labels: catLabels,
                datasets: [{
                    label: "Revenue",
                    backgroundColor: catColors,
                    hoverBackgroundColor: catColors.map(c => c + '99'), // Add transparency
                    borderColor: "#4e73df",
                    data: catData,
                }],
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
                    xAxes: [{
                        gridLines: {
                            display: false,
                            drawBorder: false
                        },
                        maxBarThickness: 25,
                    }],
                    yAxes: [{
                        ticks: {
                            min: 0,
                            maxTicksLimit: 5,
                            padding: 10,
                            // Include a peso sign in the ticks
                            callback: function(value, index, values) {
                                return '₱' + number_format(value);
                            }
                        },
                        gridLines: {
                            color: "rgb(234, 236, 244)",
                            zeroLineColor: "rgb(234, 236, 244)",
                            drawBorder: false,
                            borderDash: [2],
                            zeroLineBorderDash: [2]
                        }
                    }],
                },
                legend: {
                    display: false
                },
                tooltips: {
                    titleMarginBottom: 10,
                    titleFontColor: '#6e707e',
                    titleFontSize: 14,
                    backgroundColor: "rgb(255,255,255)",
                    bodyFontColor: "#858796",
                    borderColor: '#dddfeb',
                    borderWidth: 1,
                    xPadding: 15,
                    yPadding: 15,
                    displayColors: false,
                    caretPadding: 10,
                    callbacks: {
                        label: function(tooltipItem, chart) {
                            var datasetLabel = chart.datasets[tooltipItem.datasetIndex].label || '';
                            return datasetLabel + ': ₱' + number_format(tooltipItem.yLabel);
                        }
                    }
                }
            }
        });
    }
});
</script>
@endpush
@endsection
