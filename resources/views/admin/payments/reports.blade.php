@extends('layouts.admin')

@section('title', 'Payment Reports')

@section('admin.content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-chart-bar text-primary mr-2"></i> Payment Reports
        </h1>
        <div>
            <a href="{{ route('admin.payments.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Back to Payments
            </a>
            <button class="btn btn-primary btn-sm" onclick="window.print()">
                <i class="fas fa-print"></i> Print Report
            </button>
        </div>
    </div>

    <!-- Date Range Filter -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Filter Date Range</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.payments.reports') }}" method="GET" class="form-inline">
                <div class="form-group mr-3">
                    <label for="date_from" class="mr-2">From:</label>
                    <input type="date" id="date_from" name="date_from" class="form-control" value="{{ $dateFrom }}">
                </div>
                <div class="form-group mr-3">
                    <label for="date_to" class="mr-2">To:</label>
                    <input type="date" id="date_to" name="date_to" class="form-control" value="{{ $dateTo }}">
                </div>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-filter"></i> Apply Filter
                </button>
            </form>
        </div>
    </div>

    <!-- Status Summary -->
    <div class="row">
        <div class="col-xl-6 col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Payment Status Summary</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="thead-light">
                                <tr>
                                    <th>Status</th>
                                    <th class="text-center">Count</th>
                                    <th class="text-right">Total Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $totalCount = 0; $grandTotal = 0; @endphp
                                @foreach($paymentsByStatus as $status)
                                <tr>
                                    <td>
                                        <span class="badge bg-{{ 
                                            $status->pay_status == 'Paid' ? 'success' : 
                                            ($status->pay_status == 'Partially Paid' ? 'warning' : 
                                            ($status->pay_status == 'Refunded' ? 'info' : 
                                            ($status->pay_status == 'Failed' ? 'danger' : 'secondary'))) 
                                        }} px-3 py-2">{{ $status->pay_status }}</span>
                                    </td>
                                    <td class="text-center">{{ $status->count }}</td>
                                    <td class="text-right">₱{{ number_format($status->total, 2) }}</td>
                                </tr>
                                @php 
                                    $totalCount += $status->count; 
                                    $grandTotal += $status->total; 
                                @endphp
                                @endforeach
                            </tbody>
                            <tfoot class="bg-light">
                                <tr>
                                    <th>Total</th>
                                    <th class="text-center">{{ $totalCount }}</th>
                                    <th class="text-right">₱{{ number_format($grandTotal, 2) }}</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    
                    <!-- Status Chart -->
                    <div class="chart-pie pt-4">
                        <canvas id="paymentStatusChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Method Summary -->
        <div class="col-xl-6 col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Payment Method Summary</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="thead-light">
                                <tr>
                                    <th>Method</th>
                                    <th class="text-center">Count</th>
                                    <th class="text-right">Total Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $totalCount = 0; $grandTotal = 0; @endphp
                                @foreach($paymentsByMethod as $method)
                                <tr>
                                    <td>{{ ucfirst($method->pay_method) }}</td>
                                    <td class="text-center">{{ $method->count }}</td>
                                    <td class="text-right">₱{{ number_format($method->total, 2) }}</td>
                                </tr>
                                @php 
                                    $totalCount += $method->count; 
                                    $grandTotal += $method->total; 
                                @endphp
                                @endforeach
                            </tbody>
                            <tfoot class="bg-light">
                                <tr>
                                    <th>Total</th>
                                    <th class="text-center">{{ $totalCount }}</th>
                                    <th class="text-right">₱{{ number_format($grandTotal, 2) }}</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    
                    <!-- Method Chart -->
                    <div class="chart-pie pt-4">
                        <canvas id="paymentMethodChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Daily Payments -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Daily Payments</h6>
        </div>
        <div class="card-body">
            <!-- Daily Chart -->
            <div class="chart-area">
                <canvas id="dailyPaymentsChart"></canvas>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    $(function() {
        // Set new default font family and font color to mimic Bootstrap's default styling
        Chart.defaults.global.defaultFontFamily = 'Nunito', '-apple-system,system-ui,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif';
        Chart.defaults.global.defaultFontColor = '#858796';
        
        // Status Chart
        var statusCtx = document.getElementById("paymentStatusChart");
        var statusLabels = [];
        var statusData = [];
        var statusColors = [];
        
        @foreach($paymentsByStatus as $status)
            statusLabels.push('{{ $status->pay_status }}');
            statusData.push({{ $status->count }});
            
            // Assign colors based on status
            @if($status->pay_status == 'Paid')
                statusColors.push('#1cc88a'); // success green
            @elseif($status->pay_status == 'Partially Paid')
                statusColors.push('#f6c23e'); // warning yellow
            @elseif($status->pay_status == 'Refunded')
                statusColors.push('#36b9cc'); // info blue
            @elseif($status->pay_status == 'Failed')
                statusColors.push('#e74a3b'); // danger red
            @else
                statusColors.push('#858796'); // secondary gray
            @endif
        @endforeach
        
        var statusChart = new Chart(statusCtx, {
            type: 'doughnut',
            data: {
                labels: statusLabels,
                datasets: [{
                    data: statusData,
                    backgroundColor: statusColors,
                    hoverBackgroundColor: statusColors,
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
                },
                legend: {
                    display: true,
                    position: 'bottom'
                },
                cutoutPercentage: 70,
            },
        });
        
        // Method Chart
        var methodCtx = document.getElementById("paymentMethodChart");
        var methodLabels = [];
        var methodData = [];
        var methodColors = ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b', '#858796'];
        
        @foreach($paymentsByMethod as $index => $method)
            methodLabels.push('{{ ucfirst($method->pay_method) }}');
            methodData.push({{ $method->count }});
        @endforeach
        
        var methodChart = new Chart(methodCtx, {
            type: 'doughnut',
            data: {
                labels: methodLabels,
                datasets: [{
                    data: methodData,
                    backgroundColor: methodColors.slice(0, methodLabels.length),
                    hoverBackgroundColor: methodColors.slice(0, methodLabels.length),
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
                },
                legend: {
                    display: true,
                    position: 'bottom'
                },
                cutoutPercentage: 70,
            },
        });
        
        // Daily Chart
        var dailyCtx = document.getElementById("dailyPaymentsChart");
        var dailyLabels = [];
        var dailyData = [];
        
        @foreach($dailyPayments as $payment)
            dailyLabels.push('{{ \Carbon\Carbon::parse($payment->date)->format("M d") }}');
            dailyData.push({{ $payment->total }});
        @endforeach
        
        var dailyChart = new Chart(dailyCtx, {
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
    });
</script>
@endpush
@endsection
