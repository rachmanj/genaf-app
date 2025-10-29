@extends('layouts.main')

@section('title_page', 'Stock Opname Session Details')
@section('breadcrumb_title', 'Stock Opname Session Details')

@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Session: {{ $session->session_code }}</h3>
                            <div class="card-tools">
                                @if ($session->canBeStarted())
                                    <button onclick="startSession({{ $session->id }})" class="btn btn-success btn-sm">
                                        <i class="fas fa-play"></i> Start Session
                                    </button>
                                @endif
                                @if ($session->canBeCompleted())
                                    <button onclick="completeSession({{ $session->id }})" class="btn btn-primary btn-sm">
                                        <i class="fas fa-check"></i> Complete Session
                                    </button>
                                @endif
                                @if ($session->canBeApproved())
                                    <button onclick="approveSession({{ $session->id }})" class="btn btn-success btn-sm">
                                        <i class="fas fa-check-double"></i> Approve Session
                                    </button>
                                @endif
                                @if ($session->canBeCancelled())
                                    <button onclick="cancelSession({{ $session->id }})" class="btn btn-danger btn-sm">
                                        <i class="fas fa-times"></i> Cancel Session
                                    </button>
                                @endif
                            </div>
                        </div>
                        <div class="card-body">
                            <!-- Session Details -->
                            <div class="row">
                                <div class="col-md-6">
                                    <h5>Session Information</h5>
                                    <table class="table table-sm">
                                        <tr>
                                            <td><strong>Session Code:</strong></td>
                                            <td>{{ $session->session_code }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Title:</strong></td>
                                            <td>{{ $session->title }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Type:</strong></td>
                                            <td>
                                                @if ($session->type === 'manual')
                                                    <span class="badge badge-primary">Manual</span>
                                                @else
                                                    <span class="badge badge-info">Scheduled</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Status:</strong></td>
                                            <td>
                                                @if ($session->status === 'draft')
                                                    <span class="badge badge-secondary">Draft</span>
                                                @elseif($session->status === 'in_progress')
                                                    <span class="badge badge-warning">In Progress</span>
                                                @elseif($session->status === 'completed')
                                                    <span class="badge badge-info">Completed</span>
                                                @elseif($session->status === 'approved')
                                                    <span class="badge badge-success">Approved</span>
                                                @else
                                                    <span class="badge badge-danger">Cancelled</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Created By:</strong></td>
                                            <td>{{ $session->creator->name ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Created At:</strong></td>
                                            <td>{{ $session->created_at->format('M d, Y H:i') }}</td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <h5>Progress & Statistics</h5>
                                    <table class="table table-sm">
                                        <tr>
                                            <td><strong>Total Items:</strong></td>
                                            <td>{{ number_format($session->total_items) }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Counted Items:</strong></td>
                                            <td>{{ number_format($session->counted_items) }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Progress:</strong></td>
                                            <td>
                                                <div class="progress progress-sm">
                                                    <div class="progress-bar bg-primary" role="progressbar"
                                                        style="width: {{ $session->getProgressPercentage() }}%"
                                                        aria-valuenow="{{ $session->getProgressPercentage() }}"
                                                        aria-valuemin="0" aria-valuemax="100"></div>
                                                </div>
                                                <small>{{ $session->getProgressPercentage() }}%</small>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Items with Variance:</strong></td>
                                            <td>{{ number_format($session->items_with_variance) }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Total Variance Value:</strong></td>
                                            <td>Rp {{ number_format($session->total_variance_value, 2) }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Accuracy Rate:</strong></td>
                                            <td>{{ $session->getAccuracyRate() }}%</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>

                            @if ($session->description)
                                <div class="row mt-3">
                                    <div class="col-12">
                                        <h5>Description</h5>
                                        <p>{{ $session->description }}</p>
                                    </div>
                                </div>
                            @endif

                            @if ($session->notes)
                                <div class="row mt-3">
                                    <div class="col-12">
                                        <h5>Notes</h5>
                                        <p>{{ $session->notes }}</p>
                                    </div>
                                </div>
                            @endif

                            <hr>

                            <!-- Items Table -->
                            <h5>Session Items</h5>
                            <div class="row mb-3">
                                <div class="col-md-3">
                                    <select class="form-control" id="status-filter">
                                        <option value="">All Status</option>
                                        <option value="pending">Pending</option>
                                        <option value="counting">Counting</option>
                                        <option value="counted">Counted</option>
                                        <option value="verified">Verified</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <select class="form-control" id="variance-filter">
                                        <option value="">All Items</option>
                                        <option value="with_variance">With Variance</option>
                                        <option value="no_variance">No Variance</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <select class="form-control" id="reason-filter">
                                        <option value="">All Reasons</option>
                                        <option value="damaged">Damaged</option>
                                        <option value="expired">Expired</option>
                                        <option value="lost">Lost</option>
                                        <option value="found">Found</option>
                                        <option value="miscount">Miscount</option>
                                        <option value="other">Other</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <button class="btn btn-secondary btn-sm" onclick="clearFilters()">Clear Filters</button>
                                </div>
                            </div>

                            <table id="tbl-session-items" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Supply Name</th>
                                        <th>Code</th>
                                        <th>Unit</th>
                                        <th>System Stock</th>
                                        <th>Actual Count</th>
                                        <th>Variance</th>
                                        <th>Variance Value</th>
                                        <th>Status</th>
                                        <th>Reason</th>
                                        <th>Photo</th>
                                        <th>Counter</th>
                                        <th>Counted At</th>
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
            var table = $('#tbl-session-items').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('stock-opname.items.index', $session->id) }}',
                    data: function(d) {
                        d.status = $('#status-filter').val();
                        d.variance = $('#variance-filter').val();
                        d.reason = $('#reason-filter').val();
                    }
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'supply_name',
                        name: 'supply_name'
                    },
                    {
                        data: 'supply_code',
                        name: 'supply_code'
                    },
                    {
                        data: 'supply_unit',
                        name: 'supply_unit'
                    },
                    {
                        data: 'system_stock',
                        name: 'system_stock'
                    },
                    {
                        data: 'actual_count',
                        name: 'actual_count',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'variance',
                        name: 'variance',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'variance_value',
                        name: 'variance_value'
                    },
                    {
                        data: 'status_badge',
                        name: 'status',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'reason_code_badge',
                        name: 'reason_code',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'photo',
                        name: 'photo',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'counter_name',
                        name: 'counter_name'
                    },
                    {
                        data: 'counted_at',
                        name: 'counted_at'
                    },
                    {
                        data: 'actions',
                        name: 'actions',
                        orderable: false,
                        searchable: false
                    },
                ],
                order: [
                    [1, 'asc']
                ],
                pageLength: 25,
                responsive: true,
                autoWidth: false,
            });

            // Filter change events
            $('#status-filter, #variance-filter, #reason-filter').on('change', function() {
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
                            location.reload();
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
                            location.reload();
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
                            location.reload();
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
                            location.reload();
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
            $('#variance-filter').val('');
            $('#reason-filter').val('');
            $('#tbl-session-items').DataTable().ajax.reload();
        }
    </script>
@endpush
