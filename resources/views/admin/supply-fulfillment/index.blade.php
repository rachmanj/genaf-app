@extends('layouts.main')

@section('title_page', 'Supply Fulfillment')
@section('breadcrumb_title', 'Supply Fulfillment')

@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Approved Requests Pending Fulfillment</h3>
                        </div>
                        <div class="card-body">
                            <table id="tbl-fulfillment" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Request ID</th>
                                        <th>Department</th>
                                        <th>Request Date</th>
                                        <th>Items Count</th>
                                        <th>Status</th>
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

@push('scripts')
    <script>
        $(document).ready(function() {
            var table = $('#tbl-fulfillment').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('supplies.fulfillment.index') }}",
                columns: [{
                        data: 'index',
                        name: 'index',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'request_id',
                        name: 'request_id'
                    },
                    {
                        data: 'department_name',
                        name: 'department_name'
                    },
                    {
                        data: 'request_date',
                        name: 'request_date'
                    },
                    {
                        data: 'items_count',
                        name: 'items_count',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'status_badge',
                        name: 'status',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'actions',
                        name: 'actions',
                        orderable: false,
                        searchable: false
                    },
                ],
                order: [
                    [3, 'desc']
                ],
                pageLength: 25,
                responsive: true,
                autoWidth: false,
            });
        });
    </script>
@endpush
