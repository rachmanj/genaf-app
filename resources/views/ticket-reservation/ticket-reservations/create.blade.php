@extends('layouts.main')

@section('title', 'Create Ticket Reservation')
@section('title_page', 'Create Ticket Reservation')

@section('breadcrumb_title')
    <li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('ticket-reservations.index') }}">Ticket Reservations</a></li>
    <li class="breadcrumb-item active">Create</li>
@endsection

@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-8">
                    <div class="card card-primary card-outline">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-plus mr-1"></i>
                                Ticket Reservation Information
                            </h3>
                        </div>
                        <form action="{{ route('ticket-reservations.store') }}" method="POST">
                            @csrf
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="ticket_type">Ticket Type <span class="text-danger">*</span></label>
                                            <select class="form-control @error('ticket_type') is-invalid @enderror"
                                                id="ticket_type" name="ticket_type" required>
                                                <option value="">Select Type</option>
                                                <option value="flight" {{ old('ticket_type') == 'flight' ? 'selected' : '' }}>
                                                    <i class="fas fa-plane"></i> Flight
                                                </option>
                                                <option value="train" {{ old('ticket_type') == 'train' ? 'selected' : '' }}>
                                                    <i class="fas fa-train"></i> Train
                                                </option>
                                                <option value="bus" {{ old('ticket_type') == 'bus' ? 'selected' : '' }}>
                                                    <i class="fas fa-bus"></i> Bus
                                                </option>
                                                <option value="hotel" {{ old('ticket_type') == 'hotel' ? 'selected' : '' }}>
                                                    <i class="fas fa-bed"></i> Hotel
                                                </option>
                                            </select>
                                            @error('ticket_type')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="destination">Destination <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('destination') is-invalid @enderror"
                                                id="destination" name="destination" value="{{ old('destination') }}"
                                                placeholder="e.g., Jakarta, Bali, Singapore" required>
                                            @error('destination')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="departure_date">Departure Date <span
                                                    class="text-danger">*</span></label>
                                            <input type="date" class="form-control @error('departure_date') is-invalid @enderror"
                                                id="departure_date" name="departure_date" value="{{ old('departure_date') }}"
                                                required>
                                            @error('departure_date')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="return_date">Return Date (Optional)</label>
                                            <input type="date" class="form-control @error('return_date') is-invalid @enderror"
                                                id="return_date" name="return_date" value="{{ old('return_date') }}">
                                            <small class="text-muted">Leave empty for one-way trips</small>
                                            @error('return_date')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="cost">Estimated Cost (IDR) <span
                                                    class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">Rp</span>
                                                </div>
                                                <input type="number" class="form-control @error('cost') is-invalid @enderror"
                                                    id="cost" name="cost" value="{{ old('cost') }}"
                                                    placeholder="Enter estimated cost" min="0" step="1000" required>
                                            </div>
                                            @error('cost')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="notes">Additional Notes</label>
                                            <textarea class="form-control @error('notes') is-invalid @enderror"
                                                id="notes" name="notes" rows="3"
                                                placeholder="Enter any additional information about the reservation">{{ old('notes') }}</textarea>
                                            @error('notes')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save mr-1"></i> Submit Reservation
                                </button>
                                <a href="{{ route('ticket-reservations.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-times mr-1"></i> Cancel
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
            // Set minimum date to today
            var today = new Date().toISOString().split('T')[0];
            $('#departure_date').attr('min', today);
            $('#return_date').attr('min', today);

            // Update return date minimum when departure date changes
            $('#departure_date').on('change', function() {
                $('#return_date').attr('min', $(this).val());
            });

            // Format cost input on blur
            $('#cost').on('blur', function() {
                var value = $(this).val();
                if (value) {
                    // Format with thousand separators for display (but keep original value for form submission)
                }
            });
        });
    </script>
@endpush