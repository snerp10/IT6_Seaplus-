@extends('layouts.admin')

@section('title', 'View Sales Report')

@section('admin.content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-file-alt text-primary mr-2"></i> {{ $report->name }}
        </h1>
        <div>
            <a href="{{ route('admin.sales_reports.edit', $report->report_id) }}" class="btn btn-primary btn-sm mr-2">
                <i class="fas fa-edit"></i> Edit Report
            </a>
            <a href="{{ route('admin.sales_reports.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Back to Reports
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Sales
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">₱{{ number_format($report->total_sales, 2) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chart-line fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                Total Expenses
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">₱{{ number_format($report->total_expenses, 2) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-money-bill-wave fa-2x text-gray-300"></i>
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
                                Net Profit
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">₱{{ number_format($report->net_profit, 2) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Report Details</h6>
                </div>
                <div class="card-body">
                    <table class="table">
                        <tr>
                            <th style="width: 200px;">Report Name:</th>
                            <td>{{ $report->name }}</td>
                        </tr>
                        <tr>
                            <th>Report Type:</th>
                            <td>{{ ucfirst($report->report_type) }}</td>
                        </tr>
                        <tr>
                            <th>Date Range:</th>
                            <td>{{ $report->date_from->format('M d, Y') }} to {{ $report->date_to->format('M d, Y') }}</td>
                        </tr>
                        <tr>
                            <th>Generated On:</th>
                            <td>{{ $report->date_generated->format('M d, Y h:i A') }}</td>
                        </tr>
                        @if($report->description)
                        <tr>
                            <th>Description:</th>
                            <td>{{ $report->description }}</td>
                        </tr>
                        @endif
                        @if($report->employee)
                        <tr>
                            <th>Generated By:</th>
                            <td>{{ $report->employee->getFullNameAttribute() }}</td>
                        </tr>
                        @endif
                    </table>

                    <!-- Display main report data based on report type -->
                    @if(isset($data['dailySales']) && $report->report_type == 'daily')
                        <h5 class="mt-4">Daily Sales Breakdown</h5>
                        <div class="chart-area mt-4">
                            <canvas id="dailySalesChart"></canvas>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Actions</h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.sales_reports.edit', $report->report_id) }}" class="btn btn-primary btn-block">
                            <i class="fas fa-edit mr-1"></i> Edit Report
                        </a>
                        <button type="button" class="btn btn-success btn-block" onclick="window.print();">
                            <i class="fas fa-print mr-1"></i> Print Report
                        </button>
                        <button type="button" class="btn btn-danger btn-block" data-toggle="modal" data-target="#deleteReportModal">
                            <i class="fas fa-trash-alt mr-1"></i> Delete Report
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Report Modal -->
<div class="modal fade" id="deleteReportModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title"><i class="fas fa-trash-alt"></i> Delete Report</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this report?</p>
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

@push('scripts')
<script>
@if(isset($data['dailySales']) && $report->report_type == 'daily')
document.addEventListener('DOMContentLoaded', function() {
    // Set new default font family and font color to mimic Bootstrap's default styling
    Chart.defaults.global.defaultFontFamily = 'Nunito', '-apple-system,system-ui,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif';
    Chart.defaults.global.defaultFontColor = '#858796';

    // Function for formatting numbers
    function number_format(number, decimals, dec_point, thousands_sep) {
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

    // Daily Sales Chart
    var ctx = document.getElementById("dailySalesChart");
    var myLineChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: [
                @foreach($data['dailySales'] as $daily)
                    "{{ \Carbon\Carbon::parse($daily->date)->format('M d') }}",
                @endforeach
            ],
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
                data: [
                    @foreach($data['dailySales'] as $daily)
                        {{ $daily->total }},
                    @endforeach
                ],
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
});
@endif
</script>
@endpush
@endsection
