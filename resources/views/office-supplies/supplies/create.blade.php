@extends('layouts.main')

@section('title', 'Add New Supply')

@section('title_page')
    Add New Supply
@endsection

@section('breadcrumb_title')
    <li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('supplies.index') }}">Supplies</a></li>
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
                                Supply Information
                            </h3>
                        </div>
                        <form action="{{ route('supplies.store') }}" method="POST">
                            @csrf
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="code">Supply Code <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('code') is-invalid @enderror"
                                                id="code" name="code" value="{{ old('code') }}"
                                                placeholder="Enter supply code" required>
                                            @error('code')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="name">Supply Name <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('name') is-invalid @enderror"
                                                id="name" name="name" value="{{ old('name') }}"
                                                placeholder="Enter supply name" required>
                                            @error('name')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="category">Category <span class="text-danger">*</span></label>
                                            <select class="form-control @error('category') is-invalid @enderror"
                                                id="category" name="category" required>
                                                <option value="">Select Category</option>
                                                @foreach ($categories as $key => $label)
                                                    <option value="{{ $key }}"
                                                        {{ old('category') == $key ? 'selected' : '' }}>
                                                        {{ $label }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('category')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="unit">Unit <span class="text-danger">*</span></label>
                                            <select class="form-control @error('unit') is-invalid @enderror" id="unit"
                                                name="unit" required>
                                                <option value="">Select Unit</option>
                                                @foreach ($units as $key => $label)
                                                    <option value="{{ $key }}"
                                                        {{ old('unit') == $key ? 'selected' : '' }}>
                                                        {{ $label }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('unit')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="current_stock">Current Stock <span
                                                    class="text-danger">*</span></label>
                                            <input type="number"
                                                class="form-control @error('current_stock') is-invalid @enderror"
                                                id="current_stock" name="current_stock"
                                                value="{{ old('current_stock', 0) }}" min="0" required>
                                            @error('current_stock')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="min_stock">Minimum Stock <span class="text-danger">*</span></label>
                                            <input type="number"
                                                class="form-control @error('min_stock') is-invalid @enderror" id="min_stock"
                                                name="min_stock" value="{{ old('min_stock', 0) }}" min="0" required>
                                            @error('min_stock')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="price">Price (Rp)</label>
                                            <input type="number" class="form-control @error('price') is-invalid @enderror"
                                                id="price" name="price" value="{{ old('price') }}" min="0"
                                                step="0.01" placeholder="Enter price (optional)">
                                            @error('price')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="description">Description</label>
                                    <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description"
                                        rows="3" placeholder="Enter supply description">{{ old('description') }}</textarea>
                                    @error('description')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Save Supply
                                </button>
                                <a href="{{ route('supplies.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Cancel
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card card-info card-outline">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-info-circle mr-1"></i>
                                Help
                            </h3>
                        </div>
                        <div class="card-body">
                            <h5>Supply Code</h5>
                            <p>Use a unique code to identify this supply item. Example: ATK-001, CLEAN-002</p>

                            <h5>Stock Management</h5>
                            <p>Set the minimum stock level to receive alerts when stock is running low.</p>

                            <h5>Categories</h5>
                            <ul>
                                <li><strong>ATK:</strong> Alat Tulis Kantor</li>
                                <li><strong>Cleaning:</strong> Peralatan Kebersihan</li>
                                <li><strong>Pantry:</strong> Perlengkapan Dapur</li>
                                <li><strong>IT:</strong> Perlengkapan IT</li>
                                <li><strong>Office:</strong> Perlengkapan Kantor</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
