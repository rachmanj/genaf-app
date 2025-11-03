@extends('layouts.main')

@section('title', 'Edit Room')

@section('title_page')
    Edit Room
@endsection

@section('breadcrumb_title')
    <li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('pms.rooms.index') }}">Rooms</a></li>
    <li class="breadcrumb-item active">Edit</li>
@endsection

@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card card-primary card-outline">
                        <div class="card-header"><h3 class="card-title">Edit Room - {{ $room->room_number }}</h3></div>
                        <div class="card-body">
                            <form method="POST" action="{{ route('pms.rooms.update', $room) }}">
                                @csrf
                                @method('PUT')
                                <div class="form-row">
                                    <div class="form-group col-md-4">
                                        <label>Building</label>
                                        <select name="building_id" class="form-control" required>
                                            @foreach ($buildings as $b)
                                                <option value="{{ $b->id }}" {{ old('building_id', $room->building_id)==$b->id?'selected':'' }}>{{ $b->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('building_id') <small class="text-danger">{{ $message }}</small> @enderror
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label>Room Number</label>
                                        <input type="text" name="room_number" class="form-control" value="{{ old('room_number', $room->room_number) }}" required>
                                        @error('room_number') <small class="text-danger">{{ $message }}</small> @enderror
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label>Room Type</label>
                                        <input type="text" name="room_type" class="form-control" value="{{ old('room_type', $room->room_type) }}" required>
                                        @error('room_type') <small class="text-danger">{{ $message }}</small> @enderror
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-md-3">
                                        <label>Floor</label>
                                        <input type="number" name="floor" class="form-control" value="{{ old('floor', $room->floor) }}" required>
                                        @error('floor') <small class="text-danger">{{ $message }}</small> @enderror
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label>Capacity</label>
                                        <input type="number" name="capacity" class="form-control" value="{{ old('capacity', $room->capacity) }}" required>
                                        @error('capacity') <small class="text-danger">{{ $message }}</small> @enderror
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label>Status</label>
                                        <select name="status" class="form-control" required>
                                            <option value="available" {{ old('status', $room->status)==='available'?'selected':'' }}>Available</option>
                                            <option value="occupied" {{ old('status', $room->status)==='occupied'?'selected':'' }}>Occupied</option>
                                            <option value="maintenance" {{ old('status', $room->status)==='maintenance'?'selected':'' }}>Maintenance</option>
                                        </select>
                                        @error('status') <small class="text-danger">{{ $message }}</small> @enderror
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label>Daily Rate (IDR)</label>
                                        <input type="number" step="0.01" name="daily_rate" class="form-control" value="{{ old('daily_rate', $room->daily_rate) }}" required>
                                        @error('daily_rate') <small class="text-danger">{{ $message }}</small> @enderror
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-md-9">
                                        <label>Description</label>
                                        <textarea name="description" class="form-control" rows="3">{{ old('description', $room->description) }}</textarea>
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label>Active</label>
                                        <select name="is_active" class="form-control">
                                            <option value="1" {{ old('is_active', (int)$room->is_active)=='1'?'selected':'' }}>Yes</option>
                                            <option value="0" {{ old('is_active', (int)$room->is_active)=='0'?'selected':'' }}>No</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <a href="{{ route('pms.rooms.index') }}" class="btn btn-secondary">Cancel</a>
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


