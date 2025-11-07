@extends('layouts.main')

@section('title', 'Schedule Maintenance')

@section('title_page')
    Schedule Maintenance
@endsection

@section('breadcrumb_title')
    <li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('pms.maintenances.index') }}">Room Maintenances</a></li>
    <li class="breadcrumb-item active">Create</li>
@endsection

@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card card-primary card-outline">
                        <div class="card-header"><h3 class="card-title">Schedule New Maintenance</h3></div>
                        <div class="card-body">
                            @if(session('error'))
                                <div class="alert alert-danger">{{ session('error') }}</div>
                            @endif
                            <form method="POST" action="{{ route('pms.maintenances.store') }}">
                                @csrf
                                <div class="form-row">
                                    <div class="form-group col-md-4">
                                        <label>Building <span class="text-danger">*</span></label>
                                        <select name="building_id" id="building_id" class="form-control" required>
                                            <option value="">Select building</option>
                                            @foreach ($buildings as $b)
                                                <option value="{{ $b->id }}">{{ $b->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label>Room <span class="text-danger">*</span></label>
                                        <select name="room_id" id="room_id" class="form-control" required disabled>
                                            <option value="">Select room</option>
                                        </select>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label>Maintenance Type <span class="text-danger">*</span></label>
                                        <input type="text" name="maintenance_type" class="form-control" required placeholder="e.g., Cleaning, Repair, Inspection">
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-md-3">
                                        <label>Scheduled Date <span class="text-danger">*</span></label>
                                        <input type="date" name="scheduled_date" class="form-control" required>
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label>Completed Date</label>
                                        <input type="date" name="completed_date" class="form-control">
                                    </div>
                                    <div class="form-group col-md-2">
                                        <label>Status <span class="text-danger">*</span></label>
                                        <select name="status" class="form-control" required>
                                            <option value="scheduled">Scheduled</option>
                                            <option value="in_progress">In Progress</option>
                                            <option value="completed">Completed</option>
                                            <option value="cancelled">Cancelled</option>
                                        </select>
                                    </div>
                                    <div class="form-group col-md-2">
                                        <label>Cost</label>
                                        <input type="number" name="cost" class="form-control" step="0.01" min="0" value="0">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>Notes</label>
                                    <textarea name="notes" class="form-control" rows="3" placeholder="Additional notes..."></textarea>
                                </div>
                                <div class="mt-3">
                                    <a href="{{ route('pms.maintenances.index') }}" class="btn btn-secondary">Cancel</a>
                                    <button type="submit" class="btn btn-primary">Save</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('js')
<script>
    $(function(){
        $('#building_id, #room_id').select2({ theme: 'bootstrap4', width: '100%' });

        function loadRooms(buildingId){
            if(!buildingId){
                $('#room_id').prop('disabled', true).html('<option value="">Select room</option>');
                return;
            }
            $.get("{{ url('/pms/buildings') }}/"+buildingId+"/rooms", function(list){
                let opts = '<option value="">Select room</option>';
                list.forEach(function(r){
                    const label = r.room_number + ' - ' + r.room_type + ' (Floor ' + r.floor + ')';
                    opts += '<option value="'+r.id+'">'+label+'</option>';
                });
                $('#room_id').html(opts).prop('disabled', false);
            });
        }

        $('#building_id').on('change', function(){
            loadRooms($(this).val());
            $('#room_id').val('').trigger('change.select2');
        });

        const today = new Date().toISOString().split('T')[0];
        $('input[name="scheduled_date"]').attr('min', today);
        $('input[name="completed_date"]').attr('min', today);
    });
</script>
@endpush

