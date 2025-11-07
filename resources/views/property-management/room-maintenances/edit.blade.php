@extends('layouts.main')

@section('title', 'Edit Maintenance')

@section('title_page')
    Edit Maintenance
@endsection

@section('breadcrumb_title')
    <li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('pms.maintenances.index') }}">Room Maintenances</a></li>
    <li class="breadcrumb-item active">Edit</li>
@endsection

@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card card-primary card-outline">
                        <div class="card-header"><h3 class="card-title">Edit Maintenance</h3></div>
                        <div class="card-body">
                            @if(session('error'))
                                <div class="alert alert-danger">{{ session('error') }}</div>
                            @endif
                            <form method="POST" action="{{ route('pms.maintenances.update', $maintenance) }}">
                                @csrf
                                @method('PUT')
                                <div class="form-row">
                                    <div class="form-group col-md-4">
                                        <label>Building <span class="text-danger">*</span></label>
                                        <select name="building_id" id="building_id" class="form-control" required>
                                            <option value="">Select building</option>
                                            @foreach ($buildings as $b)
                                                <option value="{{ $b->id }}" {{ $maintenance->room->building_id == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label>Room <span class="text-danger">*</span></label>
                                        <select name="room_id" id="room_id" class="form-control" required>
                                            <option value="">Select room</option>
                                        </select>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label>Maintenance Type <span class="text-danger">*</span></label>
                                        <input type="text" name="maintenance_type" class="form-control" required value="{{ $maintenance->maintenance_type }}">
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-md-3">
                                        <label>Scheduled Date <span class="text-danger">*</span></label>
                                        <input type="date" name="scheduled_date" class="form-control" required value="{{ $maintenance->scheduled_date->format('Y-m-d') }}">
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label>Completed Date</label>
                                        <input type="date" name="completed_date" class="form-control" value="{{ $maintenance->completed_date ? $maintenance->completed_date->format('Y-m-d') : '' }}">
                                    </div>
                                    <div class="form-group col-md-2">
                                        <label>Status <span class="text-danger">*</span></label>
                                        <select name="status" class="form-control" required>
                                            <option value="scheduled" {{ $maintenance->status == 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                                            <option value="in_progress" {{ $maintenance->status == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                            <option value="completed" {{ $maintenance->status == 'completed' ? 'selected' : '' }}>Completed</option>
                                            <option value="cancelled" {{ $maintenance->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                        </select>
                                    </div>
                                    <div class="form-group col-md-2">
                                        <label>Cost</label>
                                        <input type="number" name="cost" class="form-control" step="0.01" min="0" value="{{ $maintenance->cost }}">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>Notes</label>
                                    <textarea name="notes" class="form-control" rows="3">{{ $maintenance->notes }}</textarea>
                                </div>
                                <div class="mt-3">
                                    <a href="{{ route('pms.maintenances.index') }}" class="btn btn-secondary">Cancel</a>
                                    <button type="submit" class="btn btn-primary">Update</button>
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

        const currentBuildingId = {{ $maintenance->room->building_id }};
        const currentRoomId = {{ $maintenance->room_id }};

        function loadRooms(buildingId){
            if(!buildingId){
                $('#room_id').prop('disabled', true).html('<option value="">Select room</option>');
                return;
            }
            $.get("{{ url('/pms/buildings') }}/"+buildingId+"/rooms", function(list){
                let opts = '<option value="">Select room</option>';
                list.forEach(function(r){
                    const label = r.room_number + ' - ' + r.room_type + ' (Floor ' + r.floor + ')';
                    const selected = r.id == currentRoomId ? 'selected' : '';
                    opts += '<option value="'+r.id+'" '+selected+'>'+label+'</option>';
                });
                $('#room_id').html(opts).prop('disabled', false);
            });
        }

        loadRooms(currentBuildingId);

        $('#building_id').on('change', function(){
            loadRooms($(this).val());
            $('#room_id').val('').trigger('change.select2');
        });
    });
</script>
@endpush

