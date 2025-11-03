@extends('layouts.main')

@section('title', 'Fuel Records')

@section('title_page')
    Fuel Records
@endsection

@section('breadcrumb_title')
    <li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
    <li class="breadcrumb-item active">Fuel Records</li>
@endsection

@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card card-primary card-outline">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-gas-pump mr-1"></i> Fuel Records</h3>
                        </div>
                        <div class="card-body">
                            <table id="fuel-table" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Form Number</th>
                                        <th>Date</th>
                                        <th>Vehicle</th>
                                        <th>Odometer</th>
                                        <th>Liters</th>
                                        <th>Cost</th>
                                        <th>Gas Station</th>
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
        $('#fuel-table').DataTable({
            processing: true,
            serverSide: false,
            ajax: {
                url: '{{ route('fuel-records.index') }}',
                dataSrc: 'data'
            },
            columns: [
                { data: null, orderable: false, searchable: false, render: function(data, type, row, meta) { return meta.row + meta.settings._iDisplayStart + 1; } },
                { data: 'form_number' },
                { data: 'date' },
                { data: 'vehicle' },
                { data: 'odometer' },
                { data: 'liters' },
                { data: 'cost' },
                { data: 'gas_station' },
            ],
            responsive: true,
            lengthChange: false,
            autoWidth: false
        });
    });
</script>
@endpush

