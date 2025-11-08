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
                                @can('sync vehicles')
                                    <button type="button"
                                            class="btn btn-outline-warning btn-sm mr-2"
                                            data-toggle="modal"
                                            data-target="#syncSelectedModal">
                                        <i class="fas fa-sync"></i> Sync Selected
                                    </button>
                                    <form action="{{ route('vehicles.import.sync-all') }}" method="POST" class="d-inline mr-2">
                                        @csrf
                                        <button type="submit" class="btn btn-warning btn-sm">
                                            <i class="fas fa-sync-alt"></i> Queue Sync All
                                        </button>
                                    </form>
                                @endcan
                                @can('import vehicles')
                                    <a href="{{ route('vehicles.import.index') }}" class="btn btn-outline-primary btn-sm mr-2">
                                        <i class="fas fa-cloud-download-alt"></i> Import from ARKFleet
                                    </a>
                                @endcan
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-3">
                                    <label for="filter-type">Filter by Type:</label>
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
                                    <th>Unit No</th>
                                    <th>Plate</th>
                                    <th>Brand</th>
                                    <th>Model</th>
                                    <th>Type</th>
                                    <th>Current Project</th>
                                    <th>Year</th>
                                    <th>Status</th>
                                    <th>Sync Status</th>
                                    <th>Last Synced</th>
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

        @can('sync vehicles')
            <div class="modal fade" id="syncSelectedModal" tabindex="-1" role="dialog" aria-labelledby="syncSelectedModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <form method="POST" action="{{ route('vehicles.import.sync-selected') }}">
                            @csrf
                            <div class="modal-header">
                                <h5 class="modal-title" id="syncSelectedModalLabel"><i class="fas fa-sync mr-2"></i>Queue Sync for Specific Units</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div class="form-group">
                                    <label for="sync-unit-numbers">
                                        Enter unit numbers (comma or newline separated)
                                    </label>
                                    <textarea id="sync-unit-numbers"
                                              name="unit_numbers_text"
                                              class="form-control"
                                              rows="5"
                                              placeholder="EX-001&#10;DT-002&#10;...."></textarea>
                                </div>
                                <p class="text-sm text-muted mb-0">
                                    The sync job will run in the background. Check system logs for completion details.
                                </p>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-warning btn-sm">
                                    <i class="fas fa-sync"></i> Queue Sync
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endcan
    </section>

@push('js')
<script>
    $(function () {
        var statusClassMap = {
            synced: 'badge-success',
            imported: 'badge-primary',
            queued: 'badge-info',
            processing: 'badge-info',
            success: 'badge-success',
            warning: 'badge-warning',
            failed: 'badge-danger',
            error: 'badge-danger',
            missing: 'badge-warning',
            never: 'badge-secondary'
        };

        var statusLabelFallback = {
            synced: 'Synced',
            imported: 'Imported',
            queued: 'Queued',
            processing: 'Processing',
            success: 'Success',
            warning: 'Warning',
            failed: 'Failed',
            error: 'Error',
            missing: 'Missing',
            never: 'Never Synced'
        };

        var dateFormatter = new Intl.DateTimeFormat(undefined, {
            year: 'numeric',
            month: 'short',
            day: '2-digit',
            hour: '2-digit',
            minute: '2-digit'
        });

        function escapeHtml(value) {
            if (!value) {
                return '';
            }
            return $('<div/>').text(value).html();
        }

        function renderSyncStatus(data, type, row) {
            if (type === 'sort' || type === 'type') {
                return (data || 'never').toString().toLowerCase();
            }

            var raw = (data || 'never').toString().toLowerCase();
            var badgeClass = statusClassMap[raw] || 'badge-secondary';
            var label = row.arkfleet_sync_status_label || statusLabelFallback[raw] || 'Unknown';
            var message = row.arkfleet_sync_message ? escapeHtml(row.arkfleet_sync_message) : '';

            return '<span class="badge badge-pill ' + badgeClass + '" title="' + message + '">' + escapeHtml(label) + '</span>';
        }

        function renderSyncTimestamp(data, type, row) {
            if (type === 'sort' || type === 'type') {
                return data || '';
            }

            if (!data) {
                return '<span class="text-muted">Never</span>';
            }

            var parsed = new Date(data);
            if (!isNaN(parsed.getTime())) {
                var relative = row && row.arkfleet_synced_at_human ? escapeHtml(row.arkfleet_synced_at_human) : '';
                var formatted = dateFormatter.format(parsed);

                if (relative) {
                    return '<span>' + relative + '</span><small class="text-muted d-block">' + formatted + '</small>';
                }

                return '<span>' + formatted + '</span>';
            }

            return escapeHtml(data);
        }

        var table = $('#vehicles-table').DataTable({
            processing: true,
            serverSide: false,
            ajax: {
                url: '{{ route('vehicles.index') }}',
                dataSrc: 'data'
            },
            columns: [
                { data: null, orderable: false, searchable: false, render: function(data, type, row, meta) { return meta.row + meta.settings._iDisplayStart + 1; } },
                { data: 'unit_no', name: 'unit_no' },
                { data: 'nomor_polisi', name: 'nomor_polisi' },
                { data: 'brand', name: 'brand' },
                { data: 'model', name: 'model' },
                { data: 'plant_group', name: 'plant_group' },
                { data: 'current_project_code', name: 'current_project_code' },
                { data: 'year', name: 'year' },
                { data: 'status', name: 'status' },
                { data: 'arkfleet_sync_status', name: 'arkfleet_sync_status', orderable: false, searchable: false, render: renderSyncStatus },
                { data: 'arkfleet_synced_at', name: 'arkfleet_synced_at', searchable: false, render: renderSyncTimestamp },
                { data: 'actions', name: 'actions', orderable: false, searchable: false },
            ],
            responsive: true,
            lengthChange: false,
            autoWidth: false
        });

        $('#filter-type').on('change', function() {
            var val = $(this).val();
            table.column(5).search(val ? '^' + val + '$' : '', true, false).draw();
        });
        $('#filter-status').on('change', function() {
            var val = $(this).val();
            table.column(8).search(val ? '^' + val + '$' : '', true, false).draw();
        });
        $('#clear-filters').on('click', function() {
            $('#filter-type').val('');
            $('#filter-status').val('');
            table.column(5).search('').column(8).search('').draw();
        });
    });
</script>
@endpush
@endsection

@can('sync vehicles')
    @push('modals')
        <div class="modal fade" id="syncSelectedModal" tabindex="-1" role="dialog" aria-labelledby="syncSelectedModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <form method="POST" action="{{ route('vehicles.import.sync-selected') }}">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title" id="syncSelectedModalLabel"><i class="fas fa-sync mr-2"></i>Queue Sync for Specific Units</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="sync-unit-numbers">
                                    Enter unit numbers (comma or newline separated)
                                </label>
                                <textarea id="sync-unit-numbers"
                                          name="unit_numbers_text"
                                          class="form-control"
                                          rows="5"
                                          placeholder="EX-001&#10;DT-002&#10;...."></textarea>
                            </div>
                            <p class="text-sm text-muted mb-0">
                                The sync job will run in the background. Check system logs for completion details.
                            </p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-warning btn-sm">
                                <i class="fas fa-sync"></i> Queue Sync
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endpush
@endcan


