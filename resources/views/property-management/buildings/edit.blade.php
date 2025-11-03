@extends('layouts.main')

@section('title', 'Edit Building')

@section('title_page')
    Edit Building
@endsection

@section('breadcrumb_title')
    <li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('pms.buildings.index') }}">Buildings</a></li>
    <li class="breadcrumb-item active">Edit</li>
@endsection

@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card card-primary card-outline">
                        <div class="card-header"><h3 class="card-title">Edit Building - {{ $building->name }}</h3></div>
                        <div class="card-body">
                            <form method="POST" action="{{ route('pms.buildings.update', $building) }}">
                                @csrf
                                @method('PUT')
                                <div class="form-row">
                                    <div class="form-group col-md-3">
                                        <label>Code</label>
                                        <input type="text" name="code" class="form-control" value="{{ old('code', $building->code) }}" required>
                                        @error('code') <small class="text-danger">{{ $message }}</small> @enderror
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label>Name</label>
                                        <input type="text" name="name" class="form-control" value="{{ old('name', $building->name) }}" required>
                                        @error('name') <small class="text-danger">{{ $message }}</small> @enderror
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label>Status</label>
                                        <select name="status" class="form-control">
                                            <option value="active" {{ old('status', $building->status)==='active'?'selected':'' }}>Active</option>
                                            <option value="inactive" {{ old('status', $building->status)==='inactive'?'selected':'' }}>Inactive</option>
                                        </select>
                                        @error('status') <small class="text-danger">{{ $message }}</small> @enderror
                                    </div>
                                </div>

                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label>Address Line 1</label>
                                        <input type="text" name="address_line1" class="form-control" value="{{ old('address_line1', $building->address_line1) }}">
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label>Address Line 2</label>
                                        <input type="text" name="address_line2" class="form-control" value="{{ old('address_line2', $building->address_line2) }}">
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-md-3">
                                        <label>City</label>
                                        <input type="text" name="city" class="form-control" value="{{ old('city', $building->city) }}">
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label>Province</label>
                                        <input type="text" name="province" class="form-control" value="{{ old('province', $building->province) }}">
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label>Country</label>
                                        <input type="text" name="country" class="form-control" value="{{ old('country', $building->country) }}">
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label>Postal Code</label>
                                        <input type="text" name="postal_code" class="form-control" value="{{ old('postal_code', $building->postal_code) }}">
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-md-3">
                                        <label>Latitude</label>
                                        <input type="number" step="0.00000001" name="latitude" class="form-control" value="{{ old('latitude', $building->latitude) }}">
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label>Longitude</label>
                                        <input type="number" step="0.00000001" name="longitude" class="form-control" value="{{ old('longitude', $building->longitude) }}">
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label>Timezone</label>
                                        <input type="text" name="timezone" class="form-control" value="{{ old('timezone', $building->timezone) }}">
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label>Contact Name</label>
                                        <input type="text" name="contact_name" class="form-control" value="{{ old('contact_name', $building->contact_name) }}">
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-md-3">
                                        <label>Contact Phone</label>
                                        <input type="text" name="contact_phone" class="form-control" value="{{ old('contact_phone', $building->contact_phone) }}">
                                    </div>
                                </div>

                                <div class="mt-3">
                                    <a href="{{ route('pms.buildings.index') }}" class="btn btn-secondary">Cancel</a>
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


