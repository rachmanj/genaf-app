@extends('layouts.main')

@section('title_page', 'Edit Meeting Room Reservation')
@section('breadcrumb_title', 'Edit Meeting Room Reservation')

@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card card-primary card-outline">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-edit"></i> Edit Reservation</h3>
                            <div class="card-tools">
                                <a href="{{ route('meeting-rooms.reservations.index') }}" class="btn btn-secondary btn-sm">
                                    <i class="fas fa-arrow-left"></i> Back
                                </a>
                            </div>
                        </div>
                        <form action="{{ route('meeting-rooms.reservations.update', $reservation) }}" method="POST" id="reservation-form">
                            @csrf
                            @method('PUT')
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="requested_room_id">Requested Room</label>
                                            <select name="requested_room_id" id="requested_room_id" class="form-control @error('requested_room_id') is-invalid @enderror">
                                                <option value="">-- Select Room (Optional) --</option>
                                                @foreach($meetingRooms as $room)
                                                    <option value="{{ $room->id }}" {{ old('requested_room_id', $reservation->requested_room_id) == $room->id ? 'selected' : '' }}>
                                                        {{ $room->name }} ({{ $room->location }}, Capacity: {{ $room->capacity }})
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('requested_room_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="location">Location <span class="text-danger">*</span></label>
                                            <input type="text" name="location" id="location" 
                                                class="form-control @error('location') is-invalid @enderror"
                                                value="{{ old('location', $reservation->location) }}" required>
                                            @error('location')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="meeting_title">Meeting Title <span class="text-danger">*</span></label>
                                            <input type="text" name="meeting_title" id="meeting_title" 
                                                class="form-control @error('meeting_title') is-invalid @enderror"
                                                value="{{ old('meeting_title', $reservation->meeting_title) }}" required>
                                            @error('meeting_title')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="meeting_date_start">Meeting Date Start <span class="text-danger">*</span></label>
                                            <input type="date" name="meeting_date_start" id="meeting_date_start" 
                                                class="form-control @error('meeting_date_start') is-invalid @enderror"
                                                value="{{ old('meeting_date_start', $reservation->meeting_date_start->format('Y-m-d')) }}" required>
                                            @error('meeting_date_start')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="meeting_date_end">Meeting Date End</label>
                                            <input type="date" name="meeting_date_end" id="meeting_date_end" 
                                                class="form-control @error('meeting_date_end') is-invalid @enderror"
                                                value="{{ old('meeting_date_end', $reservation->meeting_date_end ? $reservation->meeting_date_end->format('Y-m-d') : '') }}">
                                            <small class="form-text text-muted">Leave empty for single-day meeting</small>
                                            @error('meeting_date_end')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="participant_count">Number of Participants <span class="text-danger">*</span></label>
                                            <input type="number" name="participant_count" id="participant_count" 
                                                class="form-control @error('participant_count') is-invalid @enderror"
                                                value="{{ old('participant_count', $reservation->participant_count) }}" min="1" required>
                                            @error('participant_count')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="meeting_time_start">Meeting Time Start <span class="text-danger">*</span></label>
                                            <input type="time" name="meeting_time_start" id="meeting_time_start" 
                                                class="form-control @error('meeting_time_start') is-invalid @enderror"
                                                value="{{ old('meeting_time_start', \Carbon\Carbon::parse($reservation->meeting_time_start)->format('H:i')) }}" required>
                                            @error('meeting_time_start')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="meeting_time_end">Meeting Time End <span class="text-danger">*</span></label>
                                            <input type="time" name="meeting_time_end" id="meeting_time_end" 
                                                class="form-control @error('meeting_time_end') is-invalid @enderror"
                                                value="{{ old('meeting_time_end', \Carbon\Carbon::parse($reservation->meeting_time_end)->format('H:i')) }}" required>
                                            @error('meeting_time_end')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="required_facilities">Required Facilities</label>
                                            <textarea name="required_facilities" id="required_facilities" rows="2"
                                                class="form-control @error('required_facilities') is-invalid @enderror"
                                                placeholder="e.g., Ruang meeting & TV">{{ old('required_facilities', $reservation->required_facilities) }}</textarea>
                                            @error('required_facilities')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="notes">Notes</label>
                                            <textarea name="notes" id="notes" rows="3"
                                                class="form-control @error('notes') is-invalid @enderror"
                                                placeholder="Additional notes">{{ old('notes', $reservation->notes) }}</textarea>
                                            @error('notes')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <hr>
                                <h5>Consumption Requests</h5>
                                <p class="text-muted">Select consumption items for each day of the meeting</p>
                                
                                <div id="consumption-container">
                                    <!-- Consumption requests will be dynamically generated here -->
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Update Reservation
                                </button>
                                <a href="{{ route('meeting-rooms.reservations.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Cancel
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('js')
    <script>
        $(document).ready(function() {
            var existingConsumption = @json($reservation->consumptionRequests->groupBy('consumption_date')->map(function($dayRequests) {
                return $dayRequests->mapWithKeys(function($req) {
                    return [$req->consumption_type => [
                        'requested' => $req->requested,
                        'description' => $req->description
                    ]];
                });
            }));

            function getDatesBetween(startDate, endDate) {
                const dates = [];
                const start = new Date(startDate);
                const end = endDate ? new Date(endDate) : new Date(startDate);
                const currentDate = new Date(start);
                
                while (currentDate <= end) {
                    dates.push(new Date(currentDate).toISOString().split('T')[0]);
                    currentDate.setDate(currentDate.getDate() + 1);
                }
                
                return dates;
            }

            function updateConsumptionForm() {
                const startDate = $('#meeting_date_start').val();
                const endDate = $('#meeting_date_end').val() || startDate;
                
                if (!startDate) {
                    $('#consumption-container').html('');
                    return;
                }

                const dates = getDatesBetween(startDate, endDate);
                const consumptionTypes = [
                    { key: 'coffee_break_morning', label: 'Coffee Break Pagi' },
                    { key: 'coffee_break_afternoon', label: 'Coffee Break Sore' },
                    { key: 'lunch', label: 'Lunch' },
                    { key: 'dinner', label: 'Dinner' }
                ];

                let html = '';
                dates.forEach(function(date) {
                    const dateObj = new Date(date);
                    const dateLabel = dateObj.toLocaleDateString('id-ID', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
                    const existing = existingConsumption[date] || {};
                    
                    html += '<div class="card mb-3 consumption-day-card" data-date="' + date + '">';
                    html += '<div class="card-header bg-light">';
                    html += '<h6 class="mb-0">' + dateLabel + '</h6>';
                    html += '</div>';
                    html += '<div class="card-body">';
                    html += '<input type="hidden" name="consumption[' + date + '][date]" value="' + date + '">';
                    
                    consumptionTypes.forEach(function(type) {
                        const existingData = existing[type.key] || {};
                        const isChecked = existingData.requested ? 'checked' : '';
                        const descValue = existingData.description || '';
                        const descDisabled = isChecked ? '' : 'disabled';
                        
                        html += '<div class="row mb-2">';
                        html += '<div class="col-md-3">';
                        html += '<div class="form-check">';
                        html += '<input class="form-check-input consumption-checkbox" type="checkbox" ';
                        html += 'name="consumption[' + date + '][' + type.key + ']" ';
                        html += 'id="consumption_' + date + '_' + type.key + '" ';
                        html += 'value="1" ' + isChecked + '>';
                        html += '<label class="form-check-label" for="consumption_' + date + '_' + type.key + '">';
                        html += type.label;
                        html += '</label>';
                        html += '</div>';
                        html += '</div>';
                        html += '<div class="col-md-9">';
                        html += '<input type="text" ';
                        html += 'name="consumption[' + date + '][' + type.key + '_desc]" ';
                        html += 'class="form-control form-control-sm consumption-desc" ';
                        html += 'placeholder="Description/Type of ' + type.label + '" ';
                        html += 'value="' + descValue + '" ';
                        html += descDisabled + '>';
                        html += '</div>';
                        html += '</div>';
                    });
                    
                    html += '</div>';
                    html += '</div>';
                });

                $('#consumption-container').html(html);

                // Enable/disable description fields based on checkbox
                $('.consumption-checkbox').on('change', function() {
                    const descField = $(this).closest('.row').find('.consumption-desc');
                    if ($(this).is(':checked')) {
                        descField.prop('disabled', false);
                    } else {
                        descField.prop('disabled', true).val('');
                    }
                });
            }

            // Update consumption form when dates change
            $('#meeting_date_start, #meeting_date_end').on('change', function() {
                updateConsumptionForm();
            });

            // Initialize consumption form
            updateConsumptionForm();

            // Form submission with SweetAlert confirmation
            $('#reservation-form').on('submit', function(e) {
                e.preventDefault();
                
                Swal.fire({
                    title: 'Update Reservation Request?',
                    text: "Please review all details before submitting.",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#28a745',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Yes, update!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        this.submit();
                    }
                });
            });
        });
    </script>
@endpush
