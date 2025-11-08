@extends('layouts.main')

@section('title', 'Import Vehicles')

@section('title_page')
    Import Vehicles
@endsection

@section('breadcrumb_title')
    <li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('vehicles.index') }}">Vehicles</a></li>
    <li class="breadcrumb-item active">Import</li>
@endsection

@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card card-primary card-outline">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-cloud-download-alt mr-1"></i>
                                Import Vehicles from ARKFleet
                                <span class="badge badge-light ml-2">{{ number_format($count) }}</span>
                            </h3>
                        </div>
                        <div class="card-body">
                            @if (session('error'))
                                <div class="alert alert-danger">
                                    <i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}
                                </div>
                            @endif

                            @if (session('success'))
                                <div class="alert alert-success">
                                    <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
                                </div>
                            @endif

                            @if (session('info'))
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle mr-2"></i>{{ session('info') }}
                                </div>
                            @endif

                            @if ($errorMessage)
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle mr-2"></i>{{ $errorMessage }}
                                </div>
                            @endif

                            <form method="GET" class="mb-4">
                                <div class="form-row">
                                    <div class="col-md-4 mb-3">
                                        <label for="filter-project-code" class="form-label">Project Code</label>
                                        <input
                                            type="text"
                                            class="form-control form-control-sm"
                                            id="filter-project-code"
                                            name="project_code"
                                            value="{{ $filters['project_code'] }}"
                                            placeholder="e.g. 000H"
                                        >
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="filter-status" class="form-label">Status</label>
                                        <select
                                            class="form-control form-control-sm"
                                            id="filter-status"
                                            name="status"
                                        >
                                            @php($statusOptions = ['' => 'All', 'ACTIVE' => 'Active', 'IN-ACTIVE' => 'Inactive', 'SCRAP' => 'Scrap', 'SOLD' => 'Sold', 'RFM' => 'Maintenance', 'RFU' => 'Ready for Use'])
                                            @foreach ($statusOptions as $value => $label)
                                                <option value="{{ $value }}" @selected($filters['status'] === $value)>
                                                    {{ $label }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="filter-plant-group" class="form-label">Plant Group</label>
                                        <input
                                            type="text"
                                            class="form-control form-control-sm"
                                            id="filter-plant-group"
                                            name="plant_group"
                                            value="{{ $filters['plant_group'] }}"
                                            placeholder="e.g. Excavator"
                                        >
                                    </div>
                                </div>
                                <div class="d-flex justify-content-end">
                                    <button type="submit" class="btn btn-primary btn-sm mr-2">
                                        <i class="fas fa-search mr-1"></i> Apply Filters
                                    </button>
                                    <a href="{{ route('vehicles.import.index') }}" class="btn btn-secondary btn-sm">
                                        <i class="fas fa-undo mr-1"></i> Reset
                                    </a>
                                </div>
                            </form>

                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div></div>
                                @can('sync vehicles')
                                    <form method="POST" action="{{ route('vehicles.import.sync-all') }}" class="d-inline">
                                        @csrf
                                        <input type="hidden" name="project_code" value="{{ $filters['project_code'] }}">
                                        <input type="hidden" name="status" value="{{ $filters['status'] }}">
                                        <input type="hidden" name="plant_group" value="{{ $filters['plant_group'] }}">
                                        <button type="submit" class="btn btn-warning btn-sm">
                                            <i class="fas fa-sync-alt mr-1"></i> Sync Filtered Results
                                        </button>
                                    </form>
                                @endcan
                            </div>

                            <form method="POST">
                                @csrf

                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped table-hover" id="arkfleet-table">
                                        <thead>
                                        <tr>
                                            <th style="width: 40px;">
                                                <input type="checkbox" id="select-all">
                                            </th>
                                            <th>Unit No</th>
                                            <th>Description</th>
                                            <th>Project Code</th>
                                            <th>Status</th>
                                            <th>Plant Group</th>
                                            <th>Active Date</th>
                                            <th>License Plate</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @forelse ($vehicles as $vehicle)
                                            <tr>
                                                <td>
                                                    @if (!empty($vehicle['unit_no']))
                                                        <input
                                                            type="checkbox"
                                                            name="unit_numbers[]"
                                                            value="{{ $vehicle['unit_no'] }}"
                                                            class="unit-checkbox"
                                                        >
                                                    @endif
                                                </td>
                                                <td>{{ $vehicle['unit_no'] ?? '—' }}</td>
                                                <td>{{ $vehicle['description'] ?? '—' }}</td>
                                                <td>{{ $vehicle['project_code'] ?? '—' }}</td>
                                                <td>
                                                    <span class="badge badge-pill badge-light">
                                                        {{ $vehicle['unitstatus'] ?? ($vehicle['status'] ?? 'Unknown') }}
                                                    </span>
                                                </td>
                                                <td>{{ $vehicle['plant_group'] ?? '—' }}</td>
                                                <td>{{ \Illuminate\Support\Arr::get($vehicle, 'active_date') ?? '—' }}</td>
                                                <td>{{ $vehicle['nomor_polisi'] ?? '—' }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="8" class="text-center text-muted">
                                                    @if ($errorMessage)
                                                        Unable to display ARKFleet data.
                                                    @else
                                                        No vehicles found for the selected filters.
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforelse
                                        </tbody>
                                    </table>
                                </div>

                                <div class="d-flex justify-content-between align-items-center mt-3">
                                    <p class="mb-0 text-muted">
                                        @if ($count > 0)
                                            Showing {{ $count }} equipment record{{ $count === 1 ? '' : 's' }} from ARKFleet.
                                        @else
                                            No equipment records available.
                                        @endif
                                    </p>

                                    <div>
                                        @can('sync vehicles')
                                            <button type="submit"
                                                    class="btn btn-outline-warning btn-sm mr-2 action-button"
                                                    formaction="{{ route('vehicles.import.sync-selected') }}"
                                                    @if (empty($vehicles)) disabled @endif>
                                                <i class="fas fa-sync mr-1"></i> Sync Selected
                                            </button>
                                        @endcan

                                        @can('import vehicles')
                                            <button type="submit"
                                                    class="btn btn-success btn-sm action-button"
                                                    formaction="{{ route('vehicles.import.store') }}"
                                                    @if (empty($vehicles)) disabled @endif>
                                                <i class="fas fa-file-import mr-1"></i> Import Selected
                                            </button>
                                        @endcan
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

@push('js')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var selectAll = document.getElementById('select-all');
            var checkboxes = document.querySelectorAll('.unit-checkbox');
            var actionButtons = document.querySelectorAll('.action-button');

            function toggleActionButtons() {
                var hasSelection = Array.from(checkboxes).some(function (checkbox) {
                    return checkbox.checked;
                });

                actionButtons.forEach(function (button) {
                    button.disabled = !hasSelection;
                });
            }

            if (selectAll) {
                selectAll.addEventListener('change', function () {
                    checkboxes.forEach(function (checkbox) {
                        checkbox.checked = selectAll.checked;
                    });

                    toggleActionButtons();
                });
            }

            checkboxes.forEach(function (checkbox) {
                checkbox.addEventListener('change', toggleActionButtons);
            });

            toggleActionButtons();

            $('#arkfleet-table').DataTable({
                pageLength: 10,
                lengthChange: false,
                order: [[1, 'asc']],
                responsive: true,
                language: {
                    emptyTable: '{{ $errorMessage ? 'Unable to load ARKFleet data.' : 'No vehicles available for the selected filters.' }}'
                }
            });
        });
    </script>
@endpush
@endsection

