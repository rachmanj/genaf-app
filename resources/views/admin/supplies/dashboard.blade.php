@extends('layouts.main')

@section('title_page')
    Office Supplies Dashboard
@endsection

@section('breadcrumb_title')
    <li class="breadcrumb-item"><a href="{{ route('supplies.index') }}">Supplies</a></li>
    <li class="breadcrumb-item active">Dashboard</li>
@endsection

@section('content')
    <!-- Summary Statistics -->
    <div class="row">
        <!-- Total Supplies -->
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ number_format($stats['total_supplies']) }}</h3>
                    <p>Total Supplies</p>
                </div>
                <div class="icon"><i class="fas fa-box"></i></div>
                <a href="{{ route('supplies.index') }}" class="small-box-footer">More info <i
                        class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>

        <!-- Active Supplies -->
        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ number_format($stats['active_supplies']) }}</h3>
                    <p>Active Supplies</p>
                </div>
                <div class="icon"><i class="fas fa-check-circle"></i></div>
                <a href="{{ route('supplies.index') }}?filter=active" class="small-box-footer">More info <i
                        class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>

        <!-- Low Stock Alert -->
        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ number_format($stats['low_stock_count']) }}</h3>
                    <p>Low Stock Items</p>
                </div>
                <div class="icon"><i class="fas fa-exclamation-triangle"></i></div>
                <a href="{{ route('supplies.index') }}?status=low_stock" class="small-box-footer">Review <i
                        class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>

        <!-- Out of Stock -->
        <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>{{ number_format($stats['out_of_stock_count']) }}</h3>
                    <p>Out of Stock</p>
                </div>
                <div class="icon"><i class="fas fa-times-circle"></i></div>
                <a href="{{ route('supplies.index') }}?status=out_of_stock" class="small-box-footer">Review <i
                        class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
    </div>

    <!-- Requests Statistics -->
    <div class="row">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-primary">
                <div class="inner">
                    <h3>{{ number_format($stats['total_requests']) }}</h3>
                    <p>Total Requests</p>
                </div>
                <div class="icon"><i class="fas fa-clipboard-list"></i></div>
                <a href="{{ route('supplies.requests.index') }}" class="small-box-footer">More info <i
                        class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ number_format($stats['pending_requests']) }}</h3>
                    <p>Pending Requests</p>
                </div>
                <div class="icon"><i class="fas fa-hourglass-half"></i></div>
                <a href="{{ route('supplies.requests.index') }}" class="small-box-footer">Review <i
                        class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ number_format($stats['approved_requests']) }}</h3>
                    <p>Approved Requests</p>
                </div>
                <div class="icon"><i class="fas fa-check"></i></div>
                <a href="{{ route('supplies.requests.index') }}" class="small-box-footer">More info <i
                        class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ number_format($stats['fulfilled_requests']) }}</h3>
                    <p>Fulfilled Requests</p>
                </div>
                <div class="icon"><i class="fas fa-truck"></i></div>
                <a href="{{ route('supplies.fulfillment.index') }}" class="small-box-footer">More info <i
                        class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
    </div>

    <!-- Transactions & Operations -->
    <div class="row">
        <div class="col-lg-4 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ number_format($stats['incoming_transactions']) }}</h3>
                    <p>Incoming Transactions</p>
                </div>
                <div class="icon"><i class="fas fa-arrow-down"></i></div>
                <a href="{{ route('supplies.transactions.index') }}?type=in" class="small-box-footer">More info <i
                        class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>

        <div class="col-lg-4 col-6">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>{{ number_format($stats['outgoing_transactions']) }}</h3>
                    <p>Outgoing Transactions</p>
                </div>
                <div class="icon"><i class="fas fa-arrow-up"></i></div>
                <a href="{{ route('supplies.transactions.index') }}?type=out" class="small-box-footer">More info <i
                        class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>

        <div class="col-lg-4 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ number_format($stats['distributions_this_month']) }}</h3>
                    <p>Distributions This Month</p>
                </div>
                <div class="icon"><i class="fas fa-share-alt"></i></div>
                <a href="{{ route('supplies.department-stock.index') }}" class="small-box-footer">More info <i
                        class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
    </div>

    <!-- Stock Opname & Additional Info -->
    <div class="row">
        <div class="col-lg-4 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ number_format($stats['active_opname_sessions']) }}</h3>
                    <p>Active Stock Opname</p>
                </div>
                <div class="icon"><i class="fas fa-clipboard-check"></i></div>
                <a href="{{ route('stock-opname.index') }}" class="small-box-footer">View Sessions <i
                        class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>

        <div class="col-lg-4 col-6">
            <div class="small-box bg-primary">
                <div class="inner">
                    <h3>{{ number_format($stats['pending_approval_opname']) }}</h3>
                    <p>Pending Approval Opname</p>
                </div>
                <div class="icon"><i class="fas fa-clock"></i></div>
                <a href="{{ route('stock-opname.index') }}" class="small-box-footer">Review <i
                        class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>

        <div class="col-lg-4 col-6">
            <div class="small-box bg-secondary">
                <div class="inner">
                    <h3>{{ number_format($stats['active_departments_with_stock']) }}</h3>
                    <p>Departments with Stock</p>
                </div>
                <div class="icon"><i class="fas fa-building"></i></div>
                <a href="{{ route('supplies.department-stock.index') }}" class="small-box-footer">More info <i
                        class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
    </div>

    <!-- Charts and Detailed Information -->
    <div class="row">
        <!-- Category Distribution Chart -->
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-chart-pie mr-1"></i>
                        Supplies by Category
                    </h3>
                </div>
                <div class="card-body">
                    <canvas id="categoryChart" style="min-height: 300px; height: 300px; max-height: 300px; max-width: 100%;"></canvas>
                    <div class="mt-3">
                        <table class="table table-sm table-hover">
                            <thead>
                                <tr>
                                    <th>Category</th>
                                    <th class="text-right">Count</th>
                                    <th class="text-right">Percentage</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $total_categories = $category_distribution->sum('count');
                                @endphp
                                @foreach($category_distribution as $category)
                                    @php
                                        $percentage = $total_categories > 0 ? round(($category->count / $total_categories) * 100, 1) : 0;
                                    @endphp
                                    <tr>
                                        <td>{{ $category->category }}</td>
                                        <td class="text-right">{{ number_format($category->count) }}</td>
                                        <td class="text-right">
                                            <span class="badge badge-info">{{ $percentage }}%</span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th>Total</th>
                                    <th class="text-right">{{ number_format($total_categories) }}</th>
                                    <th class="text-right">100%</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stock Status Distribution Chart -->
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-chart-pie mr-1"></i>
                        Stock Status Distribution
                    </h3>
                </div>
                <div class="card-body">
                    <canvas id="stockStatusChart" style="min-height: 300px; height: 300px; max-height: 300px; max-width: 100%;"></canvas>
                    <div class="mt-3 text-center">
                        <span class="badge badge-success mr-2">
                            <i class="fas fa-check-circle"></i> In Stock: {{ number_format($stock_status_counts['in_stock']) }}
                        </span>
                        <span class="badge badge-warning mr-2">
                            <i class="fas fa-exclamation-triangle"></i> Low Stock: {{ number_format($stock_status_counts['low_stock']) }}
                        </span>
                        <span class="badge badge-danger">
                            <i class="fas fa-times-circle"></i> Out of Stock: {{ number_format($stock_status_counts['out_of_stock']) }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Monthly Transactions Chart -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-chart-line mr-1"></i>
                        Monthly Transaction Trends (Last 6 Months)
                    </h3>
                </div>
                <div class="card-body">
                    <canvas id="monthlyTransactionChart" style="min-height: 300px; height: 300px; max-height: 300px; max-width: 100%;"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Supplies by Usage Chart -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-chart-bar mr-1"></i>
                        Top Supplies by Usage
                    </h3>
                </div>
                <div class="card-body">
                    @if(count($top_supplies_labels) > 0)
                        <canvas id="topSuppliesChart" style="min-height: 350px; height: 350px; max-height: 350px; max-width: 100%;"></canvas>
                        <div class="mt-3">
                            <table class="table table-sm table-hover">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Code</th>
                                        <th>Name</th>
                                        <th class="text-right">Total Distributed</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($top_supplies_by_usage as $index => $supply)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td><strong>{{ $supply->code }}</strong></td>
                                            <td>{{ $supply->name }}</td>
                                            <td class="text-right">
                                                <span class="badge badge-primary">{{ number_format($supply->total_distributed) }}</span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted text-center">No distribution data available</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Low Stock Items & Recent Requests -->
    <div class="row">
        <!-- Low Stock Items -->
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-exclamation-triangle mr-1 text-warning"></i>
                        Low Stock Items
                    </h3>
                </div>
                <div class="card-body p-0">
                    @if($low_stock_items->count() > 0)
                        <table class="table table-sm table-hover">
                            <thead>
                                <tr>
                                    <th>Code</th>
                                    <th>Name</th>
                                    <th class="text-right">Current</th>
                                    <th class="text-right">Min</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($low_stock_items as $item)
                                    <tr>
                                        <td><strong>{{ $item->code }}</strong></td>
                                        <td>{{ $item->name }}</td>
                                        <td class="text-right">{{ number_format($item->current_stock) }}</td>
                                        <td class="text-right">{{ number_format($item->min_stock) }}</td>
                                        <td>
                                            @if($item->current_stock == 0)
                                                <span class="badge badge-danger">Out of Stock</span>
                                            @else
                                                <span class="badge badge-warning">Low Stock</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div class="card-footer text-center">
                            <a href="{{ route('supplies.index') }}?status=low_stock" class="btn btn-sm btn-warning">
                                View All Low Stock Items
                            </a>
                        </div>
                    @else
                        <div class="p-3 text-center text-muted">
                            <i class="fas fa-check-circle fa-2x mb-2 text-success"></i>
                            <p>All supplies are well stocked!</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Recent Requests -->
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-clipboard-list mr-1"></i>
                        Recent Supply Requests
                    </h3>
                </div>
                <div class="card-body p-0">
                    @if($recent_requests->count() > 0)
                        <table class="table table-sm table-hover">
                            <thead>
                                <tr>
                                    <th>Request #</th>
                                    <th>Department</th>
                                    <th>Requestor</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recent_requests as $request)
                                    <tr>
                                        <td><strong>#{{ $request->id }}</strong></td>
                                        <td>{{ $request->department->department_name ?? 'N/A' }}</td>
                                        <td>{{ $request->employee->name ?? 'N/A' }}</td>
                                        <td>
                                            @php
                                                $badgeClass = match($request->status) {
                                                    'approved' => 'success',
                                                    'pending_dept_head', 'pending_ga_admin' => 'warning',
                                                    'rejected' => 'danger',
                                                    'fulfilled' => 'info',
                                                    default => 'secondary'
                                                };
                                            @endphp
                                            <span class="badge badge-{{ $badgeClass }}">
                                                {{ ucfirst(str_replace('_', ' ', $request->status)) }}
                                            </span>
                                        </td>
                                        <td>{{ $request->created_at->format('Y-m-d') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div class="card-footer text-center">
                            <a href="{{ route('supplies.requests.index') }}" class="btn btn-sm btn-primary">
                                View All Requests
                            </a>
                        </div>
                    @else
                        <div class="p-3 text-center text-muted">
                            <i class="fas fa-inbox fa-2x mb-2"></i>
                            <p>No recent requests</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
</div>

    <!-- Recent Transactions -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-exchange-alt mr-1"></i>
                        Recent Stock Transactions
                    </h3>
                </div>
                <div class="card-body p-0">
                    @if($recent_transactions->count() > 0)
                        <table class="table table-sm table-hover">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Type</th>
                                    <th>Supply</th>
                                    <th>Department</th>
                                    <th class="text-right">Quantity</th>
                                    <th>Reference</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recent_transactions as $transaction)
                                    <tr>
                                        <td>{{ $transaction->transaction_date->format('Y-m-d') }}</td>
                                        <td>
                                            @if($transaction->type === 'in')
                                                <span class="badge badge-success">
                                                    <i class="fas fa-arrow-down"></i> Incoming
                                                </span>
                                            @else
                                                <span class="badge badge-danger">
                                                    <i class="fas fa-arrow-up"></i> Outgoing
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            <strong>{{ $transaction->supply->code }}</strong><br>
                                            <small class="text-muted">{{ $transaction->supply->name }}</small>
                                        </td>
                                        <td>{{ $transaction->department->department_name ?? '-' }}</td>
                                        <td class="text-right">
                                            <strong>{{ number_format($transaction->quantity) }}</strong>
                                        </td>
                                        <td>
                                            <small>{{ $transaction->reference_no }}</small>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div class="card-footer text-center">
                            <a href="{{ route('supplies.transactions.index') }}" class="btn btn-sm btn-primary">
                                View All Transactions
                            </a>
                        </div>
                    @else
                        <div class="p-3 text-center text-muted">
                            <i class="fas fa-exchange-alt fa-2x mb-2"></i>
                            <p>No recent transactions</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .small-box {
            border-radius: 0.25rem;
            box-shadow: 0 0 1px rgba(0, 0, 0, 0.125), 0 1px 3px rgba(0, 0, 0, 0.2);
            display: block;
            margin-bottom: 20px;
            position: relative;
        }
        .small-box .inner {
            padding: 10px;
        }
        .small-box .small-box-footer {
            background-color: rgba(0, 0, 0, 0.1);
            color: rgba(255, 255, 255, 0.8);
            display: block;
            padding: 3px 0;
            position: relative;
            text-align: center;
            text-decoration: none;
            z-index: 10;
        }
    </style>
@endpush

@push('js')
    <!-- Chart.js -->
    <script src="{{ asset('adminlte/plugins/chart.js/Chart.min.js') }}"></script>
    
    <script>
        $(function () {
            // Category Distribution Pie Chart
            var categoryCtx = document.getElementById('categoryChart').getContext('2d');
            var categoryChart = new Chart(categoryCtx, {
                type: 'pie',
                data: {
                    labels: @json($category_chart_labels),
                    datasets: [{
                        data: @json($category_chart_data),
                        backgroundColor: @json($category_chart_colors),
                        borderWidth: 2,
                        borderColor: '#fff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 15,
                            usePointStyle: true
                        }
                    },
                    tooltips: {
                        callbacks: {
                            label: function(tooltipItem, data) {
                                var label = data.labels[tooltipItem.index] || '';
                                var value = data.datasets[0].data[tooltipItem.index];
                                var total = data.datasets[0].data.reduce((a, b) => a + b, 0);
                                var percentage = ((value / total) * 100).toFixed(1);
                                return label + ': ' + value + ' (' + percentage + '%)';
                            }
                        }
                    }
                }
            });

            // Stock Status Distribution Doughnut Chart
            var stockStatusCtx = document.getElementById('stockStatusChart').getContext('2d');
            var stockStatusChart = new Chart(stockStatusCtx, {
                type: 'doughnut',
                data: {
                    labels: ['In Stock', 'Low Stock', 'Out of Stock'],
                    datasets: [{
                        data: [
                            {{ $stock_status_counts['in_stock'] }},
                            {{ $stock_status_counts['low_stock'] }},
                            {{ $stock_status_counts['out_of_stock'] }}
                        ],
                        backgroundColor: ['#28a745', '#ffc107', '#dc3545'],
                        borderWidth: 2,
                        borderColor: '#fff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 15,
                            usePointStyle: true
                        }
                    },
                    tooltips: {
                        callbacks: {
                            label: function(tooltipItem, data) {
                                var label = data.labels[tooltipItem.index] || '';
                                var value = data.datasets[0].data[tooltipItem.index];
                                var total = data.datasets[0].data.reduce((a, b) => a + b, 0);
                                var percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                                return label + ': ' + value + ' (' + percentage + '%)';
                            }
                        }
                    }
                }
            });

            // Monthly Transaction Trends Line Chart
            var monthlyCtx = document.getElementById('monthlyTransactionChart').getContext('2d');
            var monthlyChart = new Chart(monthlyCtx, {
                type: 'line',
                data: {
                    labels: @json($monthly_chart_labels),
                    datasets: [{
                        label: 'Incoming',
                        data: @json($monthly_chart_incoming),
                        borderColor: '#28a745',
                        backgroundColor: 'rgba(40, 167, 69, 0.1)',
                        borderWidth: 2,
                        fill: true,
                        lineTension: 0.4,
                        pointRadius: 4,
                        pointHoverRadius: 6
                    }, {
                        label: 'Outgoing',
                        data: @json($monthly_chart_outgoing),
                        borderColor: '#dc3545',
                        backgroundColor: 'rgba(220, 53, 69, 0.1)',
                        borderWidth: 2,
                        fill: true,
                        lineTension: 0.4,
                        pointRadius: 4,
                        pointHoverRadius: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    legend: {
                        display: true,
                        position: 'top'
                    },
                    scales: {
                        yAxes: [{
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return value.toLocaleString();
                                }
                            }
                        }]
                    },
                    tooltips: {
                        mode: 'index',
                        intersect: false,
                        callbacks: {
                            label: function(tooltipItem, data) {
                                var label = data.datasets[tooltipItem.datasetIndex].label || '';
                                var value = tooltipItem.yLabel;
                                return label + ': ' + value.toLocaleString();
                            }
                        }
                    }
                }
            });

            // Top Supplies by Usage Bar Chart
            @if(count($top_supplies_labels) > 0)
            var topSuppliesCtx = document.getElementById('topSuppliesChart').getContext('2d');
            var topSuppliesChart = new Chart(topSuppliesCtx, {
                type: 'bar',
                data: {
                    labels: @json($top_supplies_labels),
                    datasets: [{
                        label: 'Total Distributed',
                        data: @json($top_supplies_data),
                        backgroundColor: 'rgba(60, 141, 188, 0.8)',
                        borderColor: 'rgba(60, 141, 188, 1)',
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    legend: {
                        display: false
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return value.toLocaleString();
                                }
                            }
                        },
                        xAxes: [{
                            ticks: {
                                maxRotation: 45,
                                minRotation: 45
                            }
                        }]
                    },
                    tooltips: {
                        callbacks: {
                            label: function(tooltipItem, data) {
                                var value = tooltipItem.yLabel;
                                return 'Distributed: ' + value.toLocaleString();
                            }
                        }
                    }
                }
            });
            @endif
        });
    </script>
@endpush
