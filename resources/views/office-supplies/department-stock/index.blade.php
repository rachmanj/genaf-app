@extends('layouts.main')

@section('title_page', 'Department Stock')
@section('breadcrumb_title', 'Department Stock')

@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-building mr-1"></i>
                                Department Stock Management
                            </h3>
                            <p class="text-muted mb-0">View stock distributions by department</p>
                        </div>
                        <div class="card-body">
                            <table id="tbl-department-stock" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Department Name</th>
                                        <th>Status</th>
                                        <th class="text-right">Total Distributions</th>
                                        <th class="text-right">Total Quantity</th>
                                        <th>Last Distribution</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
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
        $(document).ready(function() {
            var table = $('#tbl-department-stock').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('supplies.department-stock.index') }}",
                columns: [{
                        data: null,
                        name: 'index',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row, meta) {
                            return meta.row + meta.settings._iDisplayStart + 1;
                        }
                    },
                    {
                        data: 'department_name',
                        name: 'department_name'
                    },
                    {
                        data: 'status_badge',
                        name: 'status',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'total_distributions',
                        name: 'total_distributions',
                        className: 'text-right'
                    },
                    {
                        data: 'total_quantity',
                        name: 'total_quantity',
                        className: 'text-right'
                    },
                    {
                        data: 'last_distribution',
                        name: 'last_distribution'
                    },
                    {
                        data: 'actions',
                        name: 'actions',
                        orderable: false,
                        searchable: false
                    }
                ],
                order: [
                    [5, 'desc']
                ],
                pageLength: 25,
                responsive: true,
                autoWidth: false,
            });
        });
    </script>
@endpush
