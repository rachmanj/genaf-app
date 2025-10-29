@extends('layouts.main')

@section('title_page', 'Create Stock Opname Session')
@section('breadcrumb_title', 'Create Stock Opname Session')

@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Create New Stock Opname Session</h3>
                        </div>
                        <form action="{{ route('stock-opname.store') }}" method="POST">
                            @csrf
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="title">Session Title <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('title') is-invalid @enderror"
                                                id="title" name="title" placeholder="Enter session title"
                                                value="{{ old('title') }}" required>
                                            @error('title')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="type">Session Type <span class="text-danger">*</span></label>
                                            <select class="form-control @error('type') is-invalid @enderror" id="type"
                                                name="type" required>
                                                <option value="">Select Type</option>
                                                <option value="manual" {{ old('type') == 'manual' ? 'selected' : '' }}>
                                                    Manual</option>
                                                <option value="scheduled"
                                                    {{ old('type') == 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                                            </select>
                                            @error('type')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group" id="schedule-type-group" style="display: none;">
                                            <label for="schedule_type">Schedule Type</label>
                                            <select class="form-control @error('schedule_type') is-invalid @enderror"
                                                id="schedule_type" name="schedule_type">
                                                <option value="">Select Schedule</option>
                                                <option value="monthly"
                                                    {{ old('schedule_type') == 'monthly' ? 'selected' : '' }}>Monthly
                                                </option>
                                                <option value="quarterly"
                                                    {{ old('schedule_type') == 'quarterly' ? 'selected' : '' }}>Quarterly
                                                </option>
                                                <option value="yearly"
                                                    {{ old('schedule_type') == 'yearly' ? 'selected' : '' }}>Yearly</option>
                                            </select>
                                            @error('schedule_type')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="description">Description</label>
                                    <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description"
                                        rows="3" placeholder="Enter session description">{{ old('description') }}</textarea>
                                    @error('description')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label>Supplies to Include</label>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="select-all-supplies" checked>
                                        <label class="form-check-label" for="select-all-supplies">
                                            Select All Active Supplies
                                        </label>
                                    </div>
                                    <div class="row mt-2" style="max-height: 300px; overflow-y: auto;">
                                        @foreach ($supplies as $supply)
                                            <div class="col-md-4">
                                                <div class="form-check">
                                                    <input class="form-check-input supply-checkbox" type="checkbox"
                                                        name="supply_ids[]" value="{{ $supply->id }}"
                                                        id="supply_{{ $supply->id }}" checked>
                                                    <label class="form-check-label" for="supply_{{ $supply->id }}">
                                                        {{ $supply->name }} ({{ $supply->code }})
                                                    </label>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="notes">Notes</label>
                                    <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="3"
                                        placeholder="Enter any additional notes">{{ old('notes') }}</textarea>
                                    @error('notes')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">Create Session</button>
                                <a href="{{ route('stock-opname.index') }}" class="btn btn-default float-right">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            // Show/hide schedule type based on session type
            $('#type').on('change', function() {
                if ($(this).val() === 'scheduled') {
                    $('#schedule-type-group').show();
                    $('#schedule_type').prop('required', true);
                } else {
                    $('#schedule-type-group').hide();
                    $('#schedule_type').prop('required', false);
                }
            });

            // Select all supplies functionality
            $('#select-all-supplies').on('change', function() {
                $('.supply-checkbox').prop('checked', $(this).is(':checked'));
            });

            // Update select all checkbox when individual checkboxes change
            $('.supply-checkbox').on('change', function() {
                var totalCheckboxes = $('.supply-checkbox').length;
                var checkedCheckboxes = $('.supply-checkbox:checked').length;
                $('#select-all-supplies').prop('checked', totalCheckboxes === checkedCheckboxes);
            });

            // Show session messages
            @if (session('success'))
                toastr.success('{{ session('success') }}');
            @endif

            @if (session('error'))
                toastr.error('{{ session('error') }}');
            @endif
        });
    </script>
@endpush
