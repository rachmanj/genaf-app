@extends('layouts.main')

@section('title', 'PMS Calendar')

@section('title_page')
    PMS Calendar
@endsection

@section('breadcrumb_title')
    <li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
    <li class="breadcrumb-item active">PMS Calendar</li>
@endsection

@push('css')
    <link rel="stylesheet" href="{{ asset('adminlte/plugins/fullcalendar/main.min.css') }}">
    <style>
        .pms-calendar-card {
            min-height: 720px;
        }

        #pmsCalendar .fc-event.pms-event-reservation {
            cursor: pointer;
        }

        #pmsCalendar .fc-daygrid-day.fc-day-today {
            background-color: rgba(40, 167, 69, 0.08);
        }

        #pmsCalendar .fc .fc-toolbar-title {
            font-size: 1.25rem;
        }

        .pms-calendar-filters .select2-container {
            width: 100% !important;
        }
    </style>
@endpush

@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card card-primary card-outline pms-calendar-card">
                        <div class="card-header">
                            <h3 class="card-title"><i class="far fa-calendar-alt mr-1"></i> Property Management Calendar</h3>
                        </div>
                        <div class="card-body">
                            <div class="row pms-calendar-filters mb-3">
                                <div class="col-md-4">
                                    <label for="calendar-building">Building</label>
                                    <select id="calendar-building" class="form-control select2">
                                        <option value="">All Buildings</option>
                                        @foreach ($buildings as $building)
                                            <option value="{{ $building->id }}" {{ $building->id === $selectedBuildingId ? 'selected' : '' }}>
                                                {{ $building->name }} ({{ $building->code }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label for="calendar-status">Reservation Status</label>
                                    <select id="calendar-status" class="form-control select2" multiple>
                                        @foreach ($statusOptions as $value => $label)
                                            <option value="{{ $value }}" selected>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4 d-flex align-items-end">
                                    <button type="button" id="calendar-refresh" class="btn btn-outline-primary mr-2"><i class="fas fa-sync"></i> Refresh</button>
                                    <div id="calendar-loading" class="text-muted" style="display: none;">
                                        <i class="fas fa-spinner fa-spin"></i> Loading events...
                                    </div>
                                </div>
                            </div>

                            <div id="pmsCalendar"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="modal fade" id="pmsCalendarModal" tabindex="-1" role="dialog" aria-labelledby="pmsCalendarModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="pmsCalendarModalLabel">Event Details</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="pmsCalendarModalBody"></div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="cancelModalCalendar" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Cancel Reservation</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <form id="cancelFormCalendar" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Cancellation Reason (Optional)</label>
                            <textarea name="cancellation_reason" class="form-control" rows="3" placeholder="Enter reason for cancellation..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-danger">Cancel Reservation</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script src="{{ asset('adminlte/plugins/fullcalendar/main.min.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const calendarEl = document.getElementById('pmsCalendar');
            const buildingSelect = $('#calendar-building');
            const statusSelect = $('#calendar-status');
            const refreshButton = $('#calendar-refresh');
            const loadingIndicator = $('#calendar-loading');

            buildingSelect.select2({ theme: 'bootstrap4', placeholder: 'Select building', allowClear: true });
            statusSelect.select2({ theme: 'bootstrap4', placeholder: 'Filter status' });

            const calendar = new FullCalendar.Calendar(calendarEl, {
                themeSystem: 'bootstrap',
                initialView: 'dayGridMonth',
                height: 'auto',
                firstDay: 1,
                selectable: false,
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,listWeek'
                },
                events: function(fetchInfo, successCallback, failureCallback) {
                    loadingIndicator.show();
                    $.ajax({
                        url: '{{ route('pms.calendar.events') }}',
                        method: 'GET',
                        data: {
                            start: fetchInfo.startStr,
                            end: fetchInfo.endStr,
                            building_id: buildingSelect.val(),
                            status: statusSelect.val()
                        },
                        success: function(response) {
                            successCallback(response);
                        },
                        error: function(xhr) {
                            toastr.error('Failed to load calendar events.');
                            failureCallback(xhr);
                        },
                        complete: function() {
                            loadingIndicator.hide();
                        }
                    });
                },
                eventClick: function(info) {
                    info.jsEvent.preventDefault();
                    const props = info.event.extendedProps || {};
                    let bodyHtml = '';
                    let actionButtons = '';

                    if (props.type === 'reservation') {
                        const status = props.status || '';
                        const reservationId = props.reservationId;
                        const statusBadge = status === 'pending' ? '<span class="badge badge-secondary">Pending</span>' :
                                          status === 'confirmed' ? '<span class="badge badge-primary">Confirmed</span>' :
                                          status === 'checked_in' ? '<span class="badge badge-success">Checked In</span>' :
                                          status === 'checked_out' ? '<span class="badge badge-info">Checked Out</span>' :
                                          status === 'cancelled' ? '<span class="badge badge-danger">Cancelled</span>' : '';

                        bodyHtml = `
                            <dl class="row mb-3">
                                <dt class="col-sm-4">Guest</dt><dd class="col-sm-8">${props.guestName || '-'}</dd>
                                <dt class="col-sm-4">Room</dt><dd class="col-sm-8">${props.roomNumber || '-'} (${props.roomType || '-'})</dd>
                                <dt class="col-sm-4">Building</dt><dd class="col-sm-8">${props.building || '-'}</dd>
                                <dt class="col-sm-4">Status</dt><dd class="col-sm-8">${statusBadge}</dd>
                                <dt class="col-sm-4">Check-in</dt><dd class="col-sm-8">${props.checkIn || '-'}</dd>
                                <dt class="col-sm-4">Check-out</dt><dd class="col-sm-8">${props.checkOut || '-'}</dd>
                            </dl>`;

                        actionButtons = `
                            <div class="btn-group">
                                <a href="/pms/reservations/${reservationId}" class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i> View Details
                                </a>`;

                        if (status === 'pending') {
                            actionButtons += `
                                <form method="POST" action="/pms/reservations/${reservationId}/approve" class="d-inline">
                                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                    <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('Approve this reservation?')">
                                        <i class="fas fa-check"></i> Approve
                                    </button>
                                </form>`;
                        }
                        if (['pending', 'confirmed'].includes(status)) {
                            actionButtons += `
                                <form method="POST" action="/pms/reservations/${reservationId}/check-in" class="d-inline">
                                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                    <button type="submit" class="btn btn-sm btn-primary" onclick="return confirm('Check in this guest?')">
                                        <i class="fas fa-sign-in-alt"></i> Check In
                                    </button>
                                </form>`;
                        }
                        if (status === 'checked_in') {
                            actionButtons += `
                                <form method="POST" action="/pms/reservations/${reservationId}/check-out" class="d-inline">
                                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                    <button type="submit" class="btn btn-sm btn-warning" onclick="return confirm('Check out this guest?')">
                                        <i class="fas fa-sign-out-alt"></i> Check Out
                                    </button>
                                </form>`;
                        }
                        if (!['checked_out', 'cancelled'].includes(status)) {
                            actionButtons += `
                                <button type="button" class="btn btn-sm btn-danger cancel-reservation-calendar" data-id="${reservationId}">
                                    <i class="fas fa-times"></i> Cancel
                                </button>`;
                        }
                        actionButtons += `</div>`;
                    } else if (props.type === 'maintenance') {
                        bodyHtml = `
                            <dl class="row mb-0">
                                <dt class="col-sm-4">Room</dt><dd class="col-sm-8">${props.roomNumber || '-'}</dd>
                                <dt class="col-sm-4">Type</dt><dd class="col-sm-8">${props.maintenanceType || '-'}</dd>
                                <dt class="col-sm-4">Scheduled</dt><dd class="col-sm-8">${props.scheduledDate || '-'}</dd>
                                <dt class="col-sm-4">Completed</dt><dd class="col-sm-8">${props.completedDate || 'Pending'}</dd>
                            </dl>`;
                    } else {
                        bodyHtml = '<p>No additional information available.</p>';
                    }

                    $('#pmsCalendarModalLabel').text(info.event.title);
                    $('#pmsCalendarModalBody').html(bodyHtml + actionButtons);
                    $('#pmsCalendarModal').modal('show');
                }
            });

            calendar.render();

            function refetch() {
                calendar.refetchEvents();
            }

            buildingSelect.on('change', refetch);
            statusSelect.on('change', refetch);
            refreshButton.on('click', refetch);

            $(document).on('click', '.cancel-reservation-calendar', function() {
                const reservationId = $(this).data('id');
                $('#cancelFormCalendar').attr('action', '/pms/reservations/' + reservationId + '/cancel');
                $('#cancelModalCalendar').modal('show');
            });

            $(document).on('submit', '#cancelFormCalendar', function() {
                const form = $(this);
                $.ajax({
                    url: form.attr('action'),
                    method: 'POST',
                    data: form.serialize(),
                    success: function() {
                        $('#cancelModalCalendar').modal('hide');
                        calendar.refetchEvents();
                        toastr.success('Reservation cancelled successfully');
                    },
                    error: function(xhr) {
                        toastr.error(xhr.responseJSON?.message || 'Failed to cancel reservation');
                    }
                });
                return false;
            });

            $(document).on('submit', '#pmsCalendarModal form', function(e) {
                e.preventDefault();
                const form = $(this);
                $.ajax({
                    url: form.attr('action'),
                    method: 'POST',
                    data: form.serialize(),
                    success: function() {
                        $('#pmsCalendarModal').modal('hide');
                        calendar.refetchEvents();
                        toastr.success('Action completed successfully');
                    },
                    error: function(xhr) {
                        toastr.error(xhr.responseJSON?.message || 'Action failed');
                    }
                });
                return false;
            });
        });
    </script>
@endpush

