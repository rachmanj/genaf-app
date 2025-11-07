@extends('layouts.main')

@section('title', 'PMS Dashboard')

@section('title_page')
    PMS Dashboard
@endsection

@section('breadcrumb_title')
    <li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
    <li class="breadcrumb-item active">PMS Dashboard</li>
@endsection

@section('content')
    <section class="content">
        <div class="container-fluid">
            <!-- Filters -->
            <div class="row mb-3">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <form method="GET" action="{{ route('pms.dashboard.index') }}" class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Building</label>
                                        <select name="building_id" class="form-control select2" style="width: 100%;">
                                            <option value="">All Buildings</option>
                                            @foreach ($buildings as $building)
                                                <option value="{{ $building->id }}" {{ $selectedBuildingId == $building->id ? 'selected' : '' }}>
                                                    {{ $building->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>Date Range</label>
                                        <select name="date_range" class="form-control">
                                            <option value="week" {{ $dateRange == 'week' ? 'selected' : '' }}>This Week</option>
                                            <option value="month" {{ $dateRange == 'month' ? 'selected' : '' }}>This Month</option>
                                            <option value="year" {{ $dateRange == 'year' ? 'selected' : '' }}>This Year</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>&nbsp;</label>
                                        <button type="submit" class="btn btn-primary btn-block">Filter</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Overall Statistics -->
            <div class="row">
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3>{{ $overallStats['total_rooms'] }}</h3>
                            <p>Total Rooms</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-bed"></i>
                        </div>
                        <a href="{{ route('pms.rooms.index') }}" class="small-box-footer">
                            More info <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3>{{ $overallStats['active_reservations'] }}</h3>
                            <p>Active Reservations</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-calendar-check"></i>
                        </div>
                        <a href="{{ route('pms.reservations.index') }}" class="small-box-footer">
                            More info <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3>{{ number_format($overallStats['occupancy_rate'], 1) }}<sup style="font-size: 20px">%</sup></h3>
                            <p>Occupancy Rate</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <a href="{{ route('pms.calendar.index') }}" class="small-box-footer">
                            More info <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-danger">
                        <div class="inner">
                            <h3>IDR {{ number_format($overallStats['total_maintenance_cost'], 0, ',', '.') }}</h3>
                            <p>Maintenance Cost</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-tools"></i>
                        </div>
                        <a href="{{ route('pms.maintenances.index') }}" class="small-box-footer">
                            More info <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Additional Stats Row -->
            <div class="row">
                <div class="col-lg-3 col-6">
                    <div class="info-box">
                        <span class="info-box-icon bg-info elevation-1"><i class="fas fa-calendar"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Total Reservations</span>
                            <span class="info-box-number">{{ $overallStats['total_reservations'] }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="info-box mb-3">
                        <span class="info-box-icon bg-success elevation-1"><i class="fas fa-check-circle"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Completed</span>
                            <span class="info-box-number">{{ $overallStats['completed_reservations'] }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="info-box mb-3">
                        <span class="info-box-icon bg-warning elevation-1"><i class="fas fa-clock"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Pending Maintenance</span>
                            <span class="info-box-number">{{ $overallStats['pending_maintenance'] }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Building Stats (if building selected) -->
            @if(!empty($buildingStats))
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title"><i class="fas fa-building mr-1"></i> {{ $buildingStats['building']->name }} - Statistics</h3>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="description-block border-right">
                                            <span class="description-percentage text-success">{{ $buildingStats['occupancy_rate'] }}%</span>
                                            <h5 class="description-header">Occupancy Rate</h5>
                                            <span class="description-text">Current Period</span>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="description-block border-right">
                                            <span class="description-percentage text-info">{{ $buildingStats['total_rooms'] }}</span>
                                            <h5 class="description-header">Total Rooms</h5>
                                            <span class="description-text">Active Rooms</span>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="description-block border-right">
                                            <span class="description-percentage text-warning">{{ $buildingStats['total_reservations'] }}</span>
                                            <h5 class="description-header">Reservations</h5>
                                            <span class="description-text">This Period</span>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="description-block">
                                            <span class="description-percentage text-danger">IDR {{ number_format($buildingStats['maintenance_cost'], 0, ',', '.') }}</span>
                                            <h5 class="description-header">Maintenance Cost</h5>
                                            <span class="description-text">This Period</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Charts Row -->
            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header border-transparent">
                            <h3 class="card-title">Reservation Trends (Last 12 Months)</h3>
                        </div>
                        <div class="card-body">
                            <canvas id="reservationTrendsChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header border-transparent">
                            <h3 class="card-title">Status Distribution</h3>
                        </div>
                        <div class="card-body">
                            <canvas id="statusDistributionChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Reservations and Upcoming Maintenance -->
            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header border-transparent">
                            <h3 class="card-title">Recent Reservations</h3>
                            <div class="card-tools">
                                <a href="{{ route('pms.reservations.index') }}" class="btn btn-sm btn-primary">View All</a>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table m-0">
                                    <thead>
                                        <tr>
                                            <th>Form #</th>
                                            <th>Room</th>
                                            <th>Guest</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($recentReservations as $reservation)
                                            <tr>
                                                <td><a href="{{ route('pms.reservations.show', $reservation) }}">{{ $reservation->form_number }}</a></td>
                                                <td>{{ $reservation->room?->room_number }}</td>
                                                <td>{{ $reservation->guest_name }}</td>
                                                <td>
                                                    @php
                                                        $statusColors = [
                                                            'pending' => 'secondary',
                                                            'confirmed' => 'primary',
                                                            'checked_in' => 'success',
                                                            'checked_out' => 'info',
                                                            'cancelled' => 'danger',
                                                        ];
                                                        $color = $statusColors[$reservation->status] ?? 'secondary';
                                                    @endphp
                                                    <span class="badge badge-{{ $color }}">{{ ucfirst(str_replace('_', ' ', $reservation->status)) }}</span>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center">No recent reservations</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header border-transparent">
                            <h3 class="card-title">Upcoming Maintenance</h3>
                            <div class="card-tools">
                                <a href="{{ route('pms.maintenances.index') }}" class="btn btn-sm btn-primary">View All</a>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table m-0">
                                    <thead>
                                        <tr>
                                            <th>Form #</th>
                                            <th>Room</th>
                                            <th>Type</th>
                                            <th>Scheduled</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($upcomingMaintenance as $maintenance)
                                            <tr>
                                                <td><a href="{{ route('pms.maintenances.show', $maintenance) }}">{{ $maintenance->form_number }}</a></td>
                                                <td>{{ $maintenance->room?->room_number }}</td>
                                                <td>{{ $maintenance->maintenance_type }}</td>
                                                <td>{{ $maintenance->scheduled_date->format('Y-m-d') }}</td>
                                                <td>
                                                    @php
                                                        $statusColors = [
                                                            'scheduled' => 'info',
                                                            'in_progress' => 'warning',
                                                            'completed' => 'success',
                                                            'cancelled' => 'danger',
                                                        ];
                                                        $color = $statusColors[$maintenance->status] ?? 'secondary';
                                                    @endphp
                                                    <span class="badge badge-{{ $color }}">{{ ucfirst(str_replace('_', ' ', $maintenance->status)) }}</span>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center">No upcoming maintenance</td>
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
    </section>
@endsection

@push('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
    $(function() {
        $('.select2').select2({ theme: 'bootstrap4' });

        // Reservation Trends Chart
        const trendsCtx = document.getElementById('reservationTrendsChart');
        if (trendsCtx) {
            const trendsChart = new Chart(trendsCtx, {
                type: 'line',
                data: {
                    labels: {!! json_encode(array_column($reservationTrends, 'month')) !!},
                    datasets: [{
                        label: 'Reservations',
                        data: {!! json_encode(array_column($reservationTrends, 'count')) !!},
                        borderColor: 'rgb(75, 192, 192)',
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        tension: 0.1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1
                            }
                        }
                    }
                }
            });
        }

        // Status Distribution Chart
        const statusCtx = document.getElementById('statusDistributionChart');
        if (statusCtx) {
            const statusChart = new Chart(statusCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Pending', 'Confirmed', 'Checked In', 'Checked Out', 'Cancelled'],
                    datasets: [{
                        data: [
                            {{ $statusDistribution['pending'] }},
                            {{ $statusDistribution['confirmed'] }},
                            {{ $statusDistribution['checked_in'] }},
                            {{ $statusDistribution['checked_out'] }},
                            {{ $statusDistribution['cancelled'] }}
                        ],
                        backgroundColor: [
                            '#6c757d',
                            '#007bff',
                            '#28a745',
                            '#17a2b8',
                            '#dc3545'
                        ]
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });
        }
    });
</script>
@endpush

