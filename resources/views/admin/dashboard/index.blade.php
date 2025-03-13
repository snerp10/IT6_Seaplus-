@extends('layouts.admin')

@section('admin.content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-tachometer-alt text-dark mr-2"></i> Admin Dashboard
        </h1>
        <div>
            <a href="{{ route('admin.sales_reports.create') }}" class="btn btn-primary btn-sm shadow-sm">
                <i class="fas fa-download fa-sm text-white-50 mr-1"></i> Generate Report
            </a>
        </div>
    </div>

    <!-- Stats Row -->
    <div class="row mb-4">
        <!-- Revenue Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Today's Revenue</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">₱{{ number_format($todayRevenue, 2) }}</div>
                            <div class="small mt-2 text-{{ $weeklyGrowth >= 0 ? 'success' : 'danger' }}">
                                <i class="fas fa-arrow-{{ $weeklyGrowth >= 0 ? 'up' : 'down' }}"></i>
                                {{ number_format(abs($weeklyGrowth), 1) }}% since last week
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-money-bill-wave fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Orders Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Orders</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($totalOrders) }}</div>
                            <div class="progress mt-3 progress-sm">
                                <div class="progress-bar bg-success" role="progressbar" style="width: 75%" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-shopping-cart fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Customers Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Total Customers</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($totalCustomers) }}</div>
                            <div class="row mt-2">
                                <div class="col">
                                    <div class="progress progress-sm">
                                        <div class="progress-bar bg-info" role="progressbar" style="width: 60%" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Delivery Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Pending Deliveries</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($pendingDeliveries) }}</div>
                            <div class="small mt-2">
                                <a href="{{ route('admin.deliveries.monitoring') }}" class="text-warning">
                                    View Delivery Monitor <i class="fas fa-arrow-right ml-1"></i>
                                </a>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-truck fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row">
        <!-- Revenue Chart -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text">Revenue Overview (Last 30 Days)</h6>
                    <div class="dropdown no-arrow">
                        <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in" aria-labelledby="dropdownMenuLink">
                            <div class="dropdown-header">View Options:</div>
                            <a class="dropdown-item" href="#">Daily</a>
                            <a class="dropdown-item" href="#">Weekly</a>
                            <a class="dropdown-item" href="#">Monthly</a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="{{ route('admin.sales_reports.index') }}">View Detailed Reports</a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="revenueChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Category Distribution Chart -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text">Sales by Category</h6>
                    <div class="dropdown no-arrow">
                        <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in" aria-labelledby="dropdownMenuLink">
                            <div class="dropdown-header">View Options:</div>
                            <a class="dropdown-item" href="#">By Revenue</a>
                            <a class="dropdown-item" href="#">By Quantity</a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="{{ route('admin.products.index') }}">View All Products</a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="chart-pie pt-4 pb-2">
                        <canvas id="categoriesChart"></canvas>
                    </div>
                    <div class="mt-4 text-center small">
                        @foreach($salesByCategory as $index => $category)
                            <span class="mr-2">
                                <i class="fas fa-circle chart-color-{{ $index }}"></i> {{ $category->product_category }}
                            </span>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Content Row -->
    <div class="row">
        <!-- Recent Orders Card -->
        <div class="col-xl-6 col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text">Recent Orders</h6>
                    <a href="{{ route('admin.orders.index') }}" class="text small font-weight-bold">View All</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover m-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="border-0">Order #</th>
                                    <th class="border-0">Customer</th>
                                    <th class="border-0">Amount</th>
                                    <th class="border-0">Status</th>
                                    <th class="border-0">Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentOrders as $order)
                                <tr>
                                    <td>
                                        <a href="{{ route('admin.orders.show', $order->order_id) }}" class="font-weight-bold">#{{ $order->order_id }}</a>
                                    </td>
                                    <td>
                                        {{ optional($order->customer)->fname }} {{ optional($order->customer)->lname }}
                                    </td>
                                    <td>₱{{ number_format($order->total_amount, 2) }}</td>
                                    <td>
                                        <span class="badge bg-{{ 
                                            $order->order_status == 'Completed' ? 'success' : 
                                            ($order->order_status == 'Processing' ? 'info' : 
                                            ($order->order_status == 'Pending' ? 'warning' : 'secondary')) 
                                        }}">{{ $order->order_status }}</span>
                                    </td>
                                    <td>{{ $order->created_at->format('M d, H:i') }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4">No recent orders found.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-6 col-lg-6">
            <div class="row">
                <!-- Low Stock Items Card -->
                <div class="col-12">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                            <h6 class="m-0 font-weight-bold text">Low Stock Items</h6>
                            <a href="{{ route('admin.inventories.low_stock_alerts') }}" class="text small font-weight-bold">View All</a>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover m-0">
                                    <thead class="bg-light">
                                        <tr>
                                            <th class="border-0">Product</th>
                                            <th class="border-0">Category</th>
                                            <th class="border-0">Current Stock</th>
                                            <th class="border-0">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($lowStockItems as $item)
                                        <tr>
                                            <td class="font-weight-bold">{{ $item->product_name }}</td>
                                            <td>{{ $item->category }}</td>
                                            <td>
                                                <span class="badge bg-danger">{{ $item->current_stock }}</span>
                                            </td>
                                            <td>
                                                <a href="{{ route('admin.inventories.create') }}?prod_id={{ $item->prod_id }}" class="btn btn-sm btn-outline-primary">Restock</a>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="4" class="text-center py-4">No low stock items found.</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Replace System Notifications section with Activity Summary -->

                <div class="col-12">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                            <h6 class="m-0 font-weight-bold text">
                                <i class="fas fa-history me-1"></i> Recent Activity
                            </h6>
                            <span class="badge bg-primary">{{ $notifications->count() }}</span>
                        </div>
                        <div class="card-body p-0">
                            <!-- Display recent activities (orders, inventory changes, etc.) -->
                            <div class="list-group list-group-flush">
                                @if($recentOrders->count() > 0)
                                    @foreach($recentOrders->take(3) as $order)
                                        <div class="list-group-item d-flex align-items-center">
                                            <div class="me-3">
                                                <div class="icon-circle bg-success text-white">
                                                    <i class="fas fa-shopping-bag"></i>
                                                </div>
                                            </div>
                                            <div>
                                                <div class="small text-muted">{{ $order->created_at->diffForHumans() }}</div>
                                                <div>New order <a href="{{ route('admin.orders.show', $order->order_id) }}">#{{ $order->order_id }}</a> 
                                                by {{ optional($order->customer)->fname }} {{ optional($order->customer)->lname }}</div>
                                            </div>
                                        </div>
                                    @endforeach
                                @endif
                                
                                @if(isset($lowStockItems) && $lowStockItems->count() > 0)
                                    <div class="list-group-item d-flex align-items-center">
                                        <div class="me-3">
                                            <div class="icon-circle bg-warning text-white">
                                                <i class="fas fa-exclamation-triangle"></i>
                                            </div>
                                        </div>
                                        <div>
                                            <div class="small text-muted">Today</div>
                                            <div>{{ $lowStockItems->count() }} products are running low on stock</div>
                                        </div>
                                    </div>
                                @endif
                                
                                <div class="text-center py-3">
                                    <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#allNotificationsModal">
                                        <i class="fas fa-bell me-1"></i> View All Notifications
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Monthly Revenue Trend -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text">Monthly Revenue Trend</h6>
                    <div class="dropdown no-arrow">
                        <a class="dropdown-toggle" href="#" role="button" id="monthlyRevDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in" aria-labelledby="monthlyRevDropdown">
                            <div class="dropdown-header">Export Options:</div>
                            <a class="dropdown-item" href="#"><i class="fas fa-file-excel fa-sm fa-fw mr-2 text-gray-400"></i>Excel</a>
                            <a class="dropdown-item" href="#"><i class="fas fa-file-pdf fa-sm fa-fw mr-2 text-gray-400"></i>PDF</a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="chart-bar">
                        <canvas id="monthlyRevenueChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Set new default font family and font color to mimic Bootstrap's default styling
    Chart.defaults.global.defaultFontFamily = 'Nunito', '-apple-system,system-ui,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif';
    Chart.defaults.global.defaultFontColor = '#858796';

    function number_format(number, decimals, dec_point, thousands_sep) {
        // Format numbers with commas and decimal places
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

    // Revenue Chart
    var ctx = document.getElementById('revenueChart');
    var myLineChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: {!! json_encode($chartData['dates']) !!},
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
                data: {!! json_encode($chartData['revenues']) !!},
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

    // Pie Chart for Categories
    var ctxPie = document.getElementById("categoriesChart");
    var myPieChart = new Chart(ctxPie, {
        type: 'doughnut',
        data: {
            labels: {!! json_encode($chartData['categories']) !!},
            datasets: [{
                data: {!! json_encode($chartData['categoryRevenues']) !!},
                backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b', '#5a5c69', '#6f42c1', '#fd7e14'],
                hoverBackgroundColor: ['#2e59d9', '#17a673', '#2c9faf', '#dda20a', '#be2617', '#3a3b45', '#5d35a5', '#da6302'],
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
                        var label = data.labels[tooltipItem.index];
                        var value = data.datasets[0].data[tooltipItem.index];
                        return label + ': ₱' + number_format(value);
                    }
                }
            },
            legend: {
                display: false
            },
            cutoutPercentage: 70,
        },
    });

    // Bar Chart for Monthly Revenue
    var ctxBar = document.getElementById("monthlyRevenueChart");
    var myBarChart = new Chart(ctxBar, {
        type: 'bar',
        data: {
            labels: {!! json_encode($chartData['months']) !!},
            datasets: [{
                label: "Revenue",
                backgroundColor: "#4e73df",
                hoverBackgroundColor: "#2e59d9",
                borderColor: "#4e73df",
                data: {!! json_encode($chartData['monthlyRevenues']) !!},
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
                    maxBarThickness: 60,
                }],
                yAxes: [{
                    ticks: {
                        min: 0,
                        maxTicksLimit: 5,
                        padding: 10,
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
            },
        }
    });

    // Add custom CSS for the notification icon circles
    var style = document.createElement('style');
    style.innerHTML = `
        .icon-circle {
            height: 2.5rem;
            width: 2.5rem;
            border-radius: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .chart-color-0 { color: #4e73df; }
        .chart-color-1 { color: #1cc88a; }
        .chart-color-2 { color: #36b9cc; }
        .chart-color-3 { color: #f6c23e; }
        .chart-color-4 { color: #e74a3b; }
        .chart-color-5 { color: #5a5c69; }
        .chart-color-6 { color: #6f42c1; }
        .chart-color-7 { color: #fd7e14; }
    `;
    document.head.appendChild(style);
});
</script>
@endpush
@endsection