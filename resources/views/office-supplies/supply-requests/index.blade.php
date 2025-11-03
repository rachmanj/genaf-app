@extends('layouts.main')

@section('title_page', 'Supply Requests')
@section('breadcrumb_title', 'Supply Requests')

@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Supply Requests Management</h3>
                            @can('create supply requests')
                                <div class="card-tools">
                                    <a href="{{ route('supplies.requests.create') }}" class="btn btn-primary btn-sm">
                                        <i class="fas fa-plus"></i> Add New Request
                                    </a>
                                </div>
                            @endcan
                        </div>
                        <div class="card-body">
                            <!-- Filters -->
                            <div class="row mb-3">
                                <div class="col-md-3">
                                    <label for="filter-status">Filter by Status:</label>
                                    <select id="filter-status" class="form-control form-control-sm">
                                        <option value="">All Status</option>
                                        <option value="pending">Pending</option>
                                        <option value="approved">Approved</option>
                                        <option value="rejected">Rejected</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label for="filter-employee">Filter by Employee:</label>
                                    <select id="filter-employee" class="form-control form-control-sm">
                                        <option value="">All Employees</option>
                                        @foreach (\App\Models\User::whereHas('roles', function ($q) {
            $q->whereIn('name', ['employee', 'manager']);
        })->get() as $user)
                                            <option value="{{ $user->name }}">{{ $user->name }}</option>
                                        @endforeach
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

                            <table id="tbl-supply-requests" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Request #</th>
                                        <th>Employee</th>
                                        <th>Request Date</th>
                                        <th>Status</th>
                                        <th>Items Count</th>
                                        <th>Total Quantity</th>
                                        <th>Approved By</th>
                                        <th>Approved At</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Reject Modal -->
    <div class="modal fade" id="rejectModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Reject Supply Request</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <form id="rejectForm">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="rejection_reason">Rejection Reason:</label>
                            <textarea id="rejection_reason" name="rejection_reason" class="form-control" rows="3" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">Reject Request</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        $(document).ready(function() {
            var table = $('#tbl-supply-requests').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('supplies.requests.index') }}",
                    type: 'GET'
                },
                columns: [{
                        data: 'index',
                        name: 'index',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'employee_name',
                        name: 'employee_name'
                    },
                    {
                        data: 'request_date_formatted',
                        name: 'request_date'
                    },
                    {
                        data: 'status_badge',
                        name: 'status'
                    },
                    {
                        data: 'items_count',
                        name: 'items_count'
                    },
                    {
                        data: 'total_quantity',
                        name: 'total_quantity'
                    },
                    {
                        data: 'ga_admin_approver',
                        name: 'ga_admin_approver'
                    },
                    {
                        data: 'ga_admin_approved_at',
                        name: 'ga_admin_approved_at'
                    },
                    {
                        data: 'actions',
                        name: 'actions',
                        orderable: false,
                        searchable: false
                    }
                ],
                order: [
                    [1, 'desc']
                ],
                pageLength: 25,
                responsive: true,
                language: {
                    processing: "Loading..."
                }
            });

            // Status filter
            $('#filter-status').on('change', function() {
                var status = $(this).val();
                if (status === '') {
                    table.column(4).search('').draw();
                } else {
                    table.column(4).search('^' + status + '$', true, false).draw();
                }
            });

            // Employee filter
            $('#filter-employee').on('change', function() {
                var employee = $(this).val();
                if (employee === '') {
                    table.column(2).search('').draw();
                } else {
                    table.column(2).search('^' + employee + '$', true, false).draw();
                }
            });

            // Clear filters
            $('#clear-filters').on('click', function() {
                $('#filter-status').val('').trigger('change');
                $('#filter-employee').val('').trigger('change');
            });

            // Approve Department Head
            window.approveDeptHead = function(id) {
                Swal.fire({
                    title: 'Approve Request',
                    text: "Are you sure you want to approve this request as Department Head?",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#007bff',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, approve!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ url('supplies/requests') }}/" + id + "/approve-dept-head",
                            type: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                toastr.success(response.message);
                                table.ajax.reload();
                            },
                            error: function(xhr) {
                                toastr.error(xhr.responseJSON.error ||
                                    'Failed to approve request');
                            }
                        });
                    }
                });
            };

            // Reject Department Head
            window.rejectDeptHead = function(id) {
                Swal.fire({
                    title: 'Reject Request',
                    text: "Are you sure you want to reject this request as Department Head?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Yes, reject!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ url('supplies/requests') }}/" + id + "/reject-dept-head",
                            type: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                toastr.success(response.message);
                                table.ajax.reload();
                            },
                            error: function(xhr) {
                                toastr.error(xhr.responseJSON.error ||
                                    'Failed to reject request');
                            }
                        });
                    }
                });
            };

            // Approve GA Admin
            window.approveGAAdmin = function(id) {
                Swal.fire({
                    title: 'Approve Request',
                    text: "Are you sure you want to approve this request as GA Admin?",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#007bff',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, approve!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Get the request data to determine approved quantities
                        $.ajax({
                            url: "{{ url('supplies/requests') }}/" + id,
                            type: 'GET',
                            success: function(requestData) {
                                // Create approved_quantities array with numeric indices
                                var approvedQuantities = [];
                                if (requestData.items && requestData.items.length > 0) {
                                    requestData.items.forEach(function(item) {
                                        approvedQuantities.push(item
                                            .quantity); // Approve full quantity
                                    });
                                }

                                // Now approve with correct format
                                $.ajax({
                                    url: "{{ url('supplies/requests') }}/" + id +
                                        "/approve-ga-admin",
                                    type: 'POST',
                                    data: {
                                        _token: '{{ csrf_token() }}',
                                        approved_quantities: approvedQuantities
                                    },
                                    success: function(response) {
                                        toastr.success(response.message);
                                        table.ajax.reload();
                                    },
                                    error: function(xhr) {
                                        toastr.error(xhr.responseJSON.error ||
                                            'Failed to approve request');
                                    }
                                });
                            },
                            error: function(xhr) {
                                toastr.error('Failed to load request data');
                            }
                        });
                    }
                });
            };

            // Reject GA Admin
            window.rejectGAAdmin = function(id) {
                Swal.fire({
                    title: 'Reject Request',
                    text: "Are you sure you want to reject this request as GA Admin?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Yes, reject!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ url('supplies/requests') }}/" + id + "/reject-ga-admin",
                            type: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                toastr.success(response.message);
                                table.ajax.reload();
                            },
                            error: function(xhr) {
                                toastr.error(xhr.responseJSON.error ||
                                    'Failed to reject request');
                            }
                        });
                    }
                });
            };

            // Reject request
            window.rejectRequest = function(id) {
                $('#rejectModal').modal('show');
                $('#rejectForm').off('submit').on('submit', function(e) {
                    e.preventDefault();
                    $.ajax({
                        url: "{{ url('supplies/requests') }}/" + id + "/reject",
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            rejection_reason: $('#rejection_reason').val()
                        },
                        success: function(response) {
                            toastr.success(response.message);
                            $('#rejectModal').modal('hide');
                            $('#rejection_reason').val('');
                            table.ajax.reload();
                        },
                        error: function(xhr) {
                            toastr.error(xhr.responseJSON.error ||
                                'Failed to reject request');
                        }
                    });
                });
            };

            // Delete request
            window.deleteRequest = function(id) {
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ url('supplies/requests') }}/" + id,
                            type: 'DELETE',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                toastr.success(response.message);
                                table.ajax.reload();
                            },
                            error: function(xhr) {
                                toastr.error(xhr.responseJSON.error ||
                                    'Failed to delete request');
                            }
                        });
                    }
                });
            };

            // Approve Department Head
            window.approveDeptHead = function(id) {
                Swal.fire({
                    title: 'Approve Request',
                    text: "Are you sure you want to approve this request as Department Head?",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#28a745',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, approve!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ url('supplies/requests') }}/" + id + "/approve-dept-head",
                            type: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                toastr.success(response.message);
                                table.ajax.reload();
                            },
                            error: function(xhr) {
                                toastr.error(xhr.responseJSON.error ||
                                    'Failed to approve request');
                            }
                        });
                    }
                });
            };

            // Reject Department Head
            window.rejectDeptHead = function(id) {
                Swal.fire({
                    title: 'Reject Request',
                    text: "Are you sure you want to reject this request as Department Head?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, reject!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ url('supplies/requests') }}/" + id + "/reject-dept-head",
                            type: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                toastr.success(response.message);
                                table.ajax.reload();
                            },
                            error: function(xhr) {
                                toastr.error(xhr.responseJSON.error ||
                                    'Failed to reject request');
                            }
                        });
                    }
                });
            };

            // Approve GA Admin
            window.approveGAAdmin = function(id) {
                Swal.fire({
                    title: 'Approve Request',
                    text: "Are you sure you want to approve this request as GA Admin?",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#007bff',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, approve!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Get the request data to determine approved quantities
                        $.ajax({
                            url: "{{ url('supplies/requests') }}/" + id,
                            type: 'GET',
                            success: function(requestData) {
                                // Create approved_quantities array with numeric indices
                                var approvedQuantities = [];
                                if (requestData.items && requestData.items.length > 0) {
                                    requestData.items.forEach(function(item) {
                                        approvedQuantities.push(item
                                            .quantity); // Approve full quantity
                                    });
                                }

                                // Now approve with correct format
                                $.ajax({
                                    url: "{{ url('supplies/requests') }}/" + id +
                                        "/approve-ga-admin",
                                    type: 'POST',
                                    data: {
                                        _token: '{{ csrf_token() }}',
                                        approved_quantities: approvedQuantities
                                    },
                                    success: function(response) {
                                        toastr.success(response.message);
                                        table.ajax.reload();
                                    },
                                    error: function(xhr) {
                                        toastr.error(xhr.responseJSON.error ||
                                            'Failed to approve request');
                                    }
                                });
                            },
                            error: function(xhr) {
                                toastr.error('Failed to load request data');
                            }
                        });
                    }
                });
            };

            // Reject GA Admin
            window.rejectGAAdmin = function(id) {
                Swal.fire({
                    title: 'Reject Request',
                    text: "Are you sure you want to reject this request as GA Admin?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, reject!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ url('supplies/requests') }}/" + id + "/reject-ga-admin",
                            type: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                toastr.success(response.message);
                                table.ajax.reload();
                            },
                            error: function(xhr) {
                                toastr.error(xhr.responseJSON.error ||
                                    'Failed to reject request');
                            }
                        });
                    }
                });
            };

            // Show session messages
            @if (session('success'))
                toastr.success('{{ session('success') }}');
            @endif

            @if (session('error'))
                toastr.error('{{ session('error') }}');
            @endif
        });
    </script>
@endpush
