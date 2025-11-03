@extends('layouts.main')

@section('title', 'Edit Supply')

@section('title_page')
    Edit Supply
@endsection

@section('breadcrumb_title')
    <li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('supplies.index') }}">Supplies</a></li>
    <li class="breadcrumb-item active">Edit</li>
@endsection

@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-8">
                    <div class="card card-primary card-outline">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-edit mr-1"></i>
                                Supply Information
                            </h3>
                        </div>
                        <form action="{{ route('supplies.update', $supply) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="code">Supply Code <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('code') is-invalid @enderror"
                                                id="code" name="code" value="{{ old('code', $supply->code) }}"
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
                                                id="name" name="name" value="{{ old('name', $supply->name) }}"
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
                                                        {{ old('category', $supply->category) == $key ? 'selected' : '' }}>
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
                                                        {{ old('unit', $supply->unit) == $key ? 'selected' : '' }}>
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
                                                value="{{ old('current_stock', $supply->current_stock) }}" min="0"
                                                required>
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
                                                name="min_stock" value="{{ old('min_stock', $supply->min_stock) }}"
                                                min="0" required>
                                            @error('min_stock')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="price">Price (Rp)</label>
                                            <input type="number" class="form-control @error('price') is-invalid @enderror"
                                                id="price" name="price" value="{{ old('price', $supply->price) }}"
                                                min="0" step="0.01" placeholder="Enter price (optional)">
                                            @error('price')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="description">Description</label>
                                    <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description"
                                        rows="3" placeholder="Enter supply description">{{ old('description', $supply->description) }}</textarea>
                                    @error('description')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Update Supply
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
                                Supply Details
                            </h3>
                        </div>
                        <div class="card-body">
                            <dl class="row">
                                <dt class="col-sm-4">Created:</dt>
                                <dd class="col-sm-8">{{ $supply->created_at->format('d M Y') }}</dd>

                                <dt class="col-sm-4">Updated:</dt>
                                <dd class="col-sm-8">{{ $supply->updated_at->format('d M Y') }}</dd>

                                <dt class="col-sm-4">Status:</dt>
                                <dd class="col-sm-8">
                                    @if ($supply->stock_status == 'out_of_stock')
                                        <span class="badge badge-danger">Out of Stock</span>
                                    @elseif($supply->stock_status == 'low_stock')
                                        <span class="badge badge-warning">Low Stock</span>
                                    @else
                                        <span class="badge badge-success">In Stock</span>
                                    @endif
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
