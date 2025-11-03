@extends('layouts.main')

@section('title', 'Vehicle Maintenance')

@section('title_page')
    Vehicle Maintenance
@endsection

@section('breadcrumb_title')
    <li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
    <li class="breadcrumb-item active">Vehicle Maintenance</li>
@endsection

@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card card-primary card-outline">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-tools mr-1"></i> Vehicle Maintenance</h3>
                        </div>
                        <div class="card-body">
                            <table id="maintenance-table" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Form Number</th>
                                        <th>Date</th>
                                        <th>Vehicle</th>
                                        <th>Type</th>
                                        <th>Odometer</th>
                                        <th>Cost</th>
                                        <th>Vendor</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </section>
@endsection

@push('js')
<script>
    $(function () {
        $('#maintenance-table').DataTable({
            processing: true,
            serverSide: false,
            ajax: {
                url: '{{ route('vehicle-maintenance.index') }}',
                dataSrc: 'data'
            },
            columns: [
                { data: null, orderable: false, searchable: false, render: function(data, type, row, meta) { return meta.row + meta.settings._iDisplayStart + 1; } },
                { data: 'form_number' },
                { data: 'service_date' },
                { data: 'vehicle' },
                { data: 'maintenance_type' },
                { data: 'odometer' },
                { data: 'cost' },
                { data: 'vendor' },
            ],
            responsive: true,
            lengthChange: false,
            autoWidth: false
        });
    });
</script>
@endpush

