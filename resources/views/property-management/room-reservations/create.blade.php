@extends('layouts.main')

@section('title', 'Create Reservation')

@section('title_page')
    Create Reservation
@endsection

@section('breadcrumb_title')
    <li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('pms.reservations.index') }}">Reservations</a></li>
    <li class="breadcrumb-item active">Create</li>
@endsection

@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card card-primary card-outline">
                        <div class="card-header"><h3 class="card-title">New Reservation</h3></div>
                        <div class="card-body">
                            @if(session('error'))
                                <div class="alert alert-danger">{{ session('error') }}</div>
                            @endif
                            <form method="POST" action="{{ route('pms.reservations.store') }}" id="reservation-form">
                                @csrf
                                <div class="form-row">
                                    <div class="form-group col-md-4">
                                        <label>Building</label>
                                        <select name="building_id" id="building_id" class="form-control" required>
                                            <option value="">Select building</option>
                                            @foreach ($buildings as $b)
                                                <option value="{{ $b->id }}">{{ $b->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label>Room</label>
                                        <select name="room_id" id="room_id" class="form-control" required disabled>
                                            <option value="">Select room</option>
                                        </select>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label>Guest Name</label>
                                        <input type="text" name="guest_name" id="guest_name" class="form-control" required>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-md-4">
                                        <label>Company</label>
                                        <input type="text" name="company" id="company" class="form-control">
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label>Phone</label>
                                        <input type="text" name="phone" id="phone" class="form-control" required>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label>Email</label>
                                        <input type="email" name="email" id="email" class="form-control">
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-md-3">
                                        <label>Check-in</label>
                                        <input type="date" name="check_in" id="check_in" class="form-control" required>
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label>Check-out</label>
                                        <input type="date" name="check_out" id="check_out" class="form-control" required>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label>Date Range (quick select)</label>
                                        <input type="text" id="date_range" class="form-control" placeholder="Select date range">
                                    </div>
                                    <div class="form-group col-md-6 align-self-end">
                                        <div id="availability_msg" class="mb-1"></div>
                                        <div id="cost_msg" class="text-muted"></div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>Notes</label>
                                    <textarea name="notes" class="form-control" rows="3"></textarea>
                                </div>
                                <div class="mt-3">
                                    <a href="{{ route('pms.reservations.index') }}" class="btn btn-secondary">Cancel</a>
                                    <button type="submit" class="btn btn-primary" id="btn-submit">Save</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('styles')
<link rel="stylesheet" href="{{ asset('adminlte/plugins/daterangepicker/daterangepicker.css') }}">
@endsection

@section('scripts')
<script src="{{ asset('adminlte/plugins/moment/moment.min.js') }}"></script>
<script src="{{ asset('adminlte/plugins/daterangepicker/daterangepicker.js') }}"></script>
<script>
    $(function(){
        // Enhance selects
        $('#building_id, #room_id').select2({ theme: 'bootstrap4', width: '100%' });

        // Set min dates (today) and enforce check_out >= check_in + 1
        const today = new Date().toISOString().split('T')[0];
        $('#check_in').attr('min', today);
        $('#check_out').attr('min', today);

        function addDays(dateStr, days){
            const d = new Date(dateStr);
            d.setDate(d.getDate() + days);
            return d.toISOString().split('T')[0];
        }
        function diffNights(ci, co){
            const a = new Date(ci), b = new Date(co);
            return Math.max(0, Math.round((b - a) / (1000*60*60*24)));
        }

        function loadRooms(buildingId){
            if(!buildingId){
                $('#room_id').prop('disabled', true).html('<option value="">Select room</option>');
                return;
            }
            $.get("{{ url('/pms/buildings') }}/"+buildingId+"/rooms", function(list){
                let opts = '<option value="">Select room</option>';
                list.forEach(function(r){
                    const label = r.room_number + ' - ' + r.room_type + ' (Floor ' + r.floor + ')';
                    opts += '<option value="'+r.id+'" data-rate="'+r.daily_rate+'" data-status="'+r.status+'">'+label+'</option>';
                });
                $('#room_id').html(opts).prop('disabled', false);
                $('#availability_msg').html('');
                $('#cost_msg').html('');
                $('#btn-submit').prop('disabled', true);
            });
        }

        function checkAvailability(){
            const roomId = $('#room_id').val();
            const ci = $('#check_in').val();
            const co = $('#check_out').val();
            if(!roomId || !ci || !co) return;
            $.post("{{ route('pms.reservations.check-availability') }}", {
                _token: '{{ csrf_token() }}',
                room_id: roomId,
                check_in: ci,
                check_out: co,
            }, function(resp){
                if(resp.available){
                    $('#availability_msg').html('<span class="badge badge-success">Room is available</span>');
                    $('#btn-submit').prop('disabled', false);
                } else {
                    $('#availability_msg').html('<span class="badge badge-danger">Room is NOT available for these dates</span>');
                    $('#btn-submit').prop('disabled', true);
                }
                // Calculate nights and estimated cost
                const nights = diffNights(ci, co);
                const rate = parseFloat($('#room_id option:selected').data('rate') || 0);
                const estimated = nights * rate;
                if(nights > 0 && rate > 0){
                    $('#cost_msg').text('Nights: ' + nights + ' • Estimated cost: IDR ' + estimated.toLocaleString('id-ID'));
                } else {
                    $('#cost_msg').text('');
                }
            });
        }

        $('#building_id').on('change', function(){
            loadRooms($(this).val());
            $('#room_id').val('');
            $('#room_id').trigger('change.select2');
            $('#check_in').val('');
            $('#check_out').val('');
            $('#check_out').attr('min', today);
            $('#availability_msg').html('');
            $('#cost_msg').html('');
            $('#btn-submit').prop('disabled', true);
        });

        let unavailableDates = [];

        function loadUnavailableDates(roomId) {
            if (!roomId) {
                unavailableDates = [];
                return;
            }
            $.get("{{ route('pms.reservations.unavailable-dates') }}", {
                room_id: roomId
            }, function(resp) {
                unavailableDates = resp.unavailable_dates || [];
                updateDatePickerDisabledDates();
            });
        }

        function updateDatePickerDisabledDates() {
            const checkInInput = $('#check_in');
            const checkOutInput = $('#check_out');
            
            // Update date range picker disabled dates
            if ($('#date_range').data('daterangepicker')) {
                $('#date_range').data('daterangepicker').remove();
            }
            
            $('#date_range').daterangepicker({
                minDate: moment(),
                autoUpdateInput: true,
                locale: { format: 'YYYY-MM-DD' },
                isInvalidDate: function(date) {
                    const dateStr = date.format('YYYY-MM-DD');
                    return unavailableDates.includes(dateStr);
                }
            }, function(start, end) {
                $('#check_in').val(start.format('YYYY-MM-DD')).trigger('change');
                $('#check_out').val(end.format('YYYY-MM-DD')).trigger('change');
            });
        }

        function isDateDisabled(dateStr) {
            return unavailableDates.includes(dateStr);
        }

        $('#room_id').on('change', function(){
            const roomId = $(this).val();
            $('#availability_msg').html('');
            $('#cost_msg').html('');
            $('#btn-submit').prop('disabled', true);
            loadUnavailableDates(roomId);
            checkAvailability();
        });

        $('#check_in').on('change', function(){
            const ci = $(this).val();
            if(ci){ $('#check_out').attr('min', addDays(ci, 1)); }
            checkAvailability();
        });
        $('#check_out').on('change', checkAvailability);

        // Date range picker linked to check_in/check_out
        function initDateRangePicker() {
            $('#date_range').daterangepicker({
                minDate: moment(),
                autoUpdateInput: true,
                locale: { format: 'YYYY-MM-DD' },
                isInvalidDate: function(date) {
                    const dateStr = date.format('YYYY-MM-DD');
                    return unavailableDates.includes(dateStr);
                }
            }, function(start, end){
                $('#check_in').val(start.format('YYYY-MM-DD')).trigger('change');
                $('#check_out').val(end.format('YYYY-MM-DD')).trigger('change');
            });
        }
        initDateRangePicker();

        // Guest info prefill
        let guestInfoTimeout;
        function fetchGuestInfo() {
            const guestName = $('#guest_name').val().trim();
            const phone = $('#phone').val().trim();
            if (!guestName || guestName.length < 3) return;

            clearTimeout(guestInfoTimeout);
            guestInfoTimeout = setTimeout(function() {
                $.get("{{ route('pms.reservations.guest-info') }}", {
                    guest_name: guestName,
                    phone: phone || null
                }, function(data) {
                    if (data.company && !$('#company').val()) {
                        $('#company').val(data.company);
                    }
                    if (data.phone && !$('#phone').val()) {
                        $('#phone').val(data.phone);
                    }
                    if (data.email && !$('#email').val()) {
                        $('#email').val(data.email);
                    }
                });
            }, 500);
        }

        $('#guest_name, #phone').on('blur', fetchGuestInfo);
    });
</script>
@endsection


