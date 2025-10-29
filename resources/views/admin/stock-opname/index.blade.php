@extends('layouts.main')

@section('title_page', 'Stock Opname')
@section('breadcrumb_title', 'Stock Opname')

@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Stock Opname Sessions</h3>
                            <div class="card-tools">
                                @can('create stock opname')
                                    <a href="{{ route('stock-opname.create') }}" class="btn btn-primary btn-sm">
                                        <i class="fas fa-plus"></i> Create New Session
                                    </a>
                                @endcan
                            </div>
                        </div>
                        <div class="card-body">
                            <!-- Filters -->
                            <div class="row mb-3">
                                <div class="col-md-3">
                                    <select class="form-control" id="status-filter">
                                        <option value="">All Status</option>
                                        <option value="draft">Draft</option>
                                        <option value="in_progress">In Progress</option>
                                        <option value="completed">Completed</option>
                                        <option value="approved">Approved</option>
                                        <option value="cancelled">Cancelled</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <select class="form-control" id="type-filter">
                                        <option value="">All Types</option>
                                        <option value="manual">Manual</option>
                                        <option value="scheduled">Scheduled</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <input type="date" class="form-control" id="date-from-filter"
                                        placeholder="From Date">
                                </div>
                                <div class="col-md-3">
                                    <input type="date" class="form-control" id="date-to-filter" placeholder="To Date">
                                    <button class="btn btn-secondary btn-sm mt-1" onclick="clearFilters()">Clear
                                        Filters</button>
                                </div>
                            </div>

                            <table id="tbl-stock-opname" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Session Code</th>
                                        <th>Title</th>
                                        <th>Type</th>
                                        <th>Status</th>
                                        <th>Progress</th>
                                        <th>Variance Info</th>
                                        <th>Created By</th>
                                        <th>Created At</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {{-- DataTables will populate this --}}
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
            var table = $('#tbl-stock-opname').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('stock-opname.index') }}',
                    data: function(d) {
                        d.status = $('#status-filter').val();
                        d.type = $('#type-filter').val();
                        d.date_from = $('#date-from-filter').val();
                        d.date_to = $('#date-to-filter').val();
                    }
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'session_code',
                        name: 'session_code'
                    },
                    {
                        data: 'title',
                        name: 'title'
                    },
                    {
                        data: 'type_badge',
                        name: 'type',
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
                        data: 'progress',
                        name: 'progress',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'variance_info',
                        name: 'variance_info',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'creator_name',
                        name: 'creator_name'
                    },
                    {
                        data: 'created_at',
                        name: 'created_at'
                    },
                    {
                        data: 'actions',
                        name: 'actions',
                        orderable: false,
                        searchable: false
                    },
                ],
                order: [
                    [8, 'desc']
                ],
                pageLength: 25,
                responsive: true,
                autoWidth: false,
            });

            // Filter change events
            $('#status-filter, #type-filter, #date-from-filter, #date-to-filter').on('change', function() {
                table.ajax.reload();
            });

            // Show session messages
            @if (session('success'))
                toastr.success('{{ session('success') }}');
            @endif

            @if (session('error'))
                toastr.error('{{ session('error') }}');
            @endif
        });

        // Session action functions
        window.startSession = function(id) {
            Swal.fire({
                title: 'Start Session',
                text: "Are you sure you want to start this stock opname session?",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, start it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ url('supplies/stock-opname') }}/" + id + "/start",
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            toastr.success(response.message);
                            $('#tbl-stock-opname').DataTable().ajax.reload();
                        },
                        error: function(xhr) {
                            toastr.error(xhr.responseJSON.error || 'Failed to start session');
                        }
                    });
                }
            });
        };

        window.completeSession = function(id) {
            Swal.fire({
                title: 'Complete Session',
                text: "Are you sure you want to mark this session as completed?",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#007bff',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, complete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ url('supplies/stock-opname') }}/" + id + "/complete",
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            toastr.success(response.message);
                            $('#tbl-stock-opname').DataTable().ajax.reload();
                        },
                        error: function(xhr) {
                            toastr.error(xhr.responseJSON.error || 'Failed to complete session');
                        }
                    });
                }
            });
        };

        window.approveSession = function(id) {
            Swal.fire({
                title: 'Approve Session',
                text: "Are you sure you want to approve this session? This will automatically adjust stock levels.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, approve it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ url('supplies/stock-opname') }}/" + id + "/approve",
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            toastr.success(response.message);
                            $('#tbl-stock-opname').DataTable().ajax.reload();
                        },
                        error: function(xhr) {
                            toastr.error(xhr.responseJSON.error || 'Failed to approve session');
                        }
                    });
                }
            });
        };

        window.cancelSession = function(id) {
            Swal.fire({
                title: 'Cancel Session',
                text: "Are you sure you want to cancel this session?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, cancel it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ url('supplies/stock-opname') }}/" + id + "/cancel",
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            toastr.success(response.message);
                            $('#tbl-stock-opname').DataTable().ajax.reload();
                        },
                        error: function(xhr) {
                            toastr.error(xhr.responseJSON.error || 'Failed to cancel session');
                        }
                    });
                }
            });
        };

        function clearFilters() {
            $('#status-filter').val('');
            $('#type-filter').val('');
            $('#date-from-filter').val('');
            $('#date-to-filter').val('');
            $('#tbl-stock-opname').DataTable().ajax.reload();
        }
    </script>
@endpush
