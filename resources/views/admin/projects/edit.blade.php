@extends('layouts.main')

@section('title_page', 'Edit Project')
@section('breadcrumb_title')
    <li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.projects.index') }}">Projects</a></li>
    <li class="breadcrumb-item active">Edit</li>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card card-warning">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-edit mr-1"></i>
                            Edit Project
                        </h3>
                    </div>
                    <form action="{{ route('admin.projects.update', $project->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="code">Project Code <span class="text-danger">*</span></label>
                                        <input type="text"
                                            class="form-control @error('code') is-invalid @enderror"
                                            id="code" name="code" value="{{ old('code', $project->code) }}"
                                            placeholder="Enter project code" required>
                                        @error('code')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="is_active">Status</label>
                                        <select class="form-control @error('is_active') is-invalid @enderror" id="is_active"
                                            name="is_active">
                                            <option value="1" {{ old('is_active', $project->is_active) == '1' ? 'selected' : '' }}>Active</option>
                                            <option value="0" {{ old('is_active', $project->is_active) == '0' ? 'selected' : '' }}>Inactive</option>
                                        </select>
                                        @error('is_active')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="owner">Owner <span class="text-danger">*</span></label>
                                        <input type="text"
                                            class="form-control @error('owner') is-invalid @enderror"
                                            id="owner" name="owner" value="{{ old('owner', $project->owner) }}"
                                            placeholder="Enter owner name" required>
                                        @error('owner')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="location">Location <span class="text-danger">*</span></label>
                                        <input type="text"
                                            class="form-control @error('location') is-invalid @enderror"
                                            id="location" name="location" value="{{ old('location', $project->location) }}"
                                            placeholder="Enter location" required>
                                        @error('location')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Update Project
                            </button>
                            <a href="{{ route('admin.projects.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Back to List
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
<script>
@if (session('success'))
    toastr.success('{{ session('success') }}');
@endif

@if (session('error'))
    toastr.error('{{ session('error') }}');
@endif
</script>
@endpush
