@extends('layouts.main')

@section('title', 'Vehicles')

@section('title_page')
    Vehicles
@endsection

@section('breadcrumb_title')
    <li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
    <li class="breadcrumb-item active">Vehicles</li>
@endsection

@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card card-primary card-outline">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-car mr-1"></i> Vehicles</h3>
                            <div class="card-tools">
                                @can('create vehicles')
                                    <a href="{{ route('vehicles.create') }}" class="btn btn-primary btn-sm">
                                        <i class="fas fa-plus"></i> Add Vehicle
                                    </a>
                                @endcan
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-3">
                                    <label for="filter-type">Filter by Type:</nlabel>
                                    <select id="filter-type" class="form-control form-control-sm">
                                        <option value="">All Types</option>
                                        <option value="MPV">MPV</option>
                                        <option value="Sedan">Sedan</option>
                                        <option value="Pickup">Pickup</option>
                                        <option value="SUV">SUV</option>
                                        <option value="Truck">Truck</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label for="filter-status">Filter by Status:</label>
                                    <select id="filter-status" class="form-control form-control-sm">
                                        <option value="">All Status</option>
                                        <option value="Active">Active</option>
                                        <option value="Maintenance">Maintenance</option>
                                        <option value="Retired">Retired</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label>&nbsp;</label>
                                    <div>
                                        <button type="button" id="clear-filters" class="btn btn-secondary btn-sm">
                                            <i class="fas fa-times"></i> Clear Filters
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <table id="vehicles-table" class="table table-bordered table-striped">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Plate</th>
                                    <th>Brand</th>
                                    <th>Model</th>
                                    <th>Type</th>
                                    <th>Year</th>
                                    <th>Status</th>
                                    <th>Actions</th>
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

@push('js')
<script>
    $(function () {
        var table = $('#vehicles-table').DataTable({
            processing: true,
            serverSide: false,
            ajax: {
                url: '{{ route('vehicles.index') }}',
                dataSrc: 'data'
            },
            columns: [
                { data: null, orderable: false, searchable: false, render: function(data, type, row, meta) { return meta.row + meta.settings._iDisplayStart + 1; } },
                { data: 'plate_number', name: 'plate_number' },
                { data: 'brand', name: 'brand' },
                { data: 'model', name: 'model' },
                { data: 'type', name: 'type' },
                { data: 'year', name: 'year' },
                { data: 'status', name: 'status' },
                { data: 'actions', name: 'actions', orderable: false, searchable: false },
            ],
            responsive: true,
            lengthChange: false,
            autoWidth: false
        });

        $('#filter-type').on('change', function() {
            var val = $(this).val();
            table.column(4).search(val ? '^' + val + '$' : '', true, false).draw();
        });
        $('#filter-status').on('change', function() {
            var val = $(this).val();
            table.column(6).search(val ? '^' + val + '$' : '', true, false).draw();
        });
        $('#clear-filters').on('click', function() {
            $('#filter-type').val('');
            $('#filter-status').val('');
            table.column(4).search('').column(6).search('').draw();
        });
    });
</script>
@endpush
@endsection


