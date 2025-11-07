@extends('layouts.main')

@section('title', 'Maintenance Details')

@section('title_page')
    Maintenance Details
@endsection

@section('breadcrumb_title')
    <li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('pms.maintenances.index') }}">Room Maintenances</a></li>
    <li class="breadcrumb-item active">Detail</li>
@endsection

@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card card-primary card-outline">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h3 class="card-title"><i class="fas fa-tools mr-1"></i> Maintenance #{{ $maintenance->form_number }}</h3>
                            <div class="card-tools">
                                <a href="{{ route('pms.maintenances.edit', $maintenance) }}" class="btn btn-sm btn-warning">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <table class="table table-sm">
                                        <tr><th>Form Number</th><td>{{ $maintenance->form_number }}</td></tr>
                                        <tr><th>Building</th><td>{{ $maintenance->room?->building?->name }}</td></tr>
                                        <tr><th>Room</th><td>{{ $maintenance->room?->room_number }} ({{ $maintenance->room?->room_type }})</td></tr>
                                        <tr><th>Maintenance Type</th><td>{{ $maintenance->maintenance_type }}</td></tr>
                                        <tr><th>Scheduled Date</th><td>{{ $maintenance->scheduled_date->format('Y-m-d') }}</td></tr>
                                        <tr><th>Completed Date</th><td>{{ $maintenance->completed_date ? $maintenance->completed_date->format('Y-m-d') : '-' }}</td></tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <table class="table table-sm">
                                        <tr><th>Status</th>
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
                                        <tr><th>Cost</th><td>{{ number_format($maintenance->cost, 0, ',', '.') }}</td></tr>
                                        <tr><th>Created At</th><td>{{ $maintenance->created_at->format('Y-m-d H:i') }}</td></tr>
                                        <tr><th>Updated At</th><td>{{ $maintenance->updated_at->format('Y-m-d H:i') }}</td></tr>
                                    </table>
                                </div>
                            </div>
                            @if($maintenance->notes)
                                <div class="row">
                                    <div class="col-12">
                                        <h5>Notes</h5>
                                        <p>{{ $maintenance->notes }}</p>
                                    </div>
                                </div>
                            @endif

                            <hr>

                            @if($maintenance->status !== 'completed')
                                <div class="row">
                                    <div class="col-12">
                                        <h5>Quick Actions</h5>
                                        <form method="POST" action="{{ route('pms.maintenances.complete', $maintenance) }}" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-success" onclick="return confirm('Mark this maintenance as completed?')">
                                                <i class="fas fa-check"></i> Mark as Completed
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('js')
<script>
    $(function() {
        @if (session('success'))
            toastr.success('{{ session('success') }}');
        @endif

        @if (session('error'))
            toastr.error('{{ session('error') }}');
        @endif
    });
</script>
@endpush

