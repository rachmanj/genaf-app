@extends('layouts.main')

@section('title', 'Vehicle Detail')

@section('title_page')
    Vehicle Detail
@endsection

@section('breadcrumb_title')
    <li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('vehicles.index') }}">Vehicles</a></li>
    <li class="breadcrumb-item active">{{ $vehicle->unit_no ?? $vehicle->nomor_polisi ?? 'Vehicle' }}</li>
@endsection

@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="card card-primary card-outline">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">
                        <i class="fas fa-car mr-2"></i>
                        {{ $vehicle->unit_no ?? $vehicle->nomor_polisi ?? 'Vehicle Detail' }}
                    </h3>
                </div>
                <div class="card-body">
                    <ul class="nav nav-pills mb-3" id="vehicle-tabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="vehicle-overview-tab" data-toggle="pill" href="#vehicle-overview" role="tab"
                               aria-controls="vehicle-overview" aria-selected="true">
                                Overview
                            </a>
                        </li>
                        @can('view vehicle documents')
                            <li class="nav-item">
                                <a class="nav-link" id="vehicle-documents-tab" data-toggle="pill" href="#vehicle-documents" role="tab"
                                   aria-controls="vehicle-documents" aria-selected="false">
                                    Documents
                                </a>
                            </li>
                        @endcan
                    </ul>

                    <div class="tab-content" id="vehicle-tab-content">
                        <div class="tab-pane fade show active" id="vehicle-overview" role="tabpanel"
                             aria-labelledby="vehicle-overview-tab">
                            <div class="row">
                                <div class="col-md-6">
                                    <dl class="row">
                                        <dt class="col-sm-5">Unit Number</dt>
                                        <dd class="col-sm-7">{{ $vehicle->unit_no ?? '—' }}</dd>

                                        <dt class="col-sm-5">License Plate</dt>
                                        <dd class="col-sm-7">{{ $vehicle->nomor_polisi ?? '—' }}</dd>

                                        <dt class="col-sm-5">Brand</dt>
                                        <dd class="col-sm-7">{{ $vehicle->brand ?? '—' }}</dd>

                                        <dt class="col-sm-5">Model</dt>
                                        <dd class="col-sm-7">{{ $vehicle->model ?? '—' }}</dd>

                                        <dt class="col-sm-5">Year</dt>
                                        <dd class="col-sm-7">{{ $vehicle->year ?? '—' }}</dd>
                                    </dl>
                                </div>
                                <div class="col-md-6">
                                    <dl class="row">
                                        <dt class="col-sm-5">Plant Group</dt>
                                        <dd class="col-sm-7">{{ $vehicle->plant_group ?? '—' }}</dd>

                                        <dt class="col-sm-5">Current Project</dt>
                                        <dd class="col-sm-7">{{ $vehicle->current_project_code ?? '—' }}</dd>

                                        <dt class="col-sm-5">Status</dt>
                                        <dd class="col-sm-7">
                                            <span class="badge badge-pill badge-info text-capitalize">{{ $vehicle->status }}</span>
                                        </dd>

                                        <dt class="col-sm-5">ArkFleet Sync Status</dt>
                                        <dd class="col-sm-7 text-capitalize">{{ $vehicle->arkfleet_sync_status ?? 'never' }}</dd>

                                        <dt class="col-sm-5">Last ArkFleet Sync</dt>
                                        <dd class="col-sm-7">
                                            {{ optional($vehicle->arkfleet_synced_at)?->format('d M Y H:i') ?? 'Never' }}
                                        </dd>
                                    </dl>
                                </div>
                            </div>
                            @if ($vehicle->remarks)
                                <div class="alert alert-light border mt-3">
                                    <strong>Remarks:</strong>
                                    <span class="d-block mt-1">{{ $vehicle->remarks }}</span>
                                </div>
                            @endif
                        </div>

                        @can('view vehicle documents')
                            <div class="tab-pane fade" id="vehicle-documents" role="tabpanel" aria-labelledby="vehicle-documents-tab">
                                @if (session('success'))
                                    <div class="alert alert-success">
                                        <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
                                    </div>
                                @endif

                                @if ($errors->any())
                                    <div class="alert alert-danger">
                                        <i class="fas fa-exclamation-circle mr-2"></i>
                                        Please correct the highlighted fields and try again.
                                    </div>
                                @endif

                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <div>
                                        <h5 class="mb-0">Document Records</h5>
                                        <small class="text-muted">Track registrations, inspections, and renewals.</small>
                                    </div>
                                    <div>
                                        @can('create vehicle documents')
                                            <button type="button" class="btn btn-primary btn-sm btn-add-document">
                                                <i class="fas fa-plus mr-1"></i> Add Document
                                            </button>
                                        @endcan
                                    </div>
                                </div>

                                <div class="table-responsive">
                                    <table class="table table-striped table-bordered mb-0">
                                        <thead class="thead-light">
                                        <tr>
                                            <th>Type</th>
                                            <th>Document No</th>
                                            <th>Supplier</th>
                                            <th>Amount</th>
                                            <th>Document Date</th>
                                            <th>Due Date</th>
                                            <th>Status</th>
                                            <th>File</th>
                                            <th style="width: 160px;">Actions</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @forelse ($vehicle->documents as $document)
                                            @php
                                                $documentView = $documentsForView[$document->id] ?? [];
                                                $amountFormatted = $documentView['amount_formatted'] ?? null;
                                                $docDate = optional($document->document_date)?->format('d M Y');
                                                $dueDate = optional($document->due_date)?->format('d M Y');
                                            @endphp
                                            <tr data-document='@json($documentView)'>
                                                <td>{{ $documentView['type_name'] ?? '—' }}</td>
                                                <td>{{ $document->document_number ?? '—' }}</td>
                                                <td>{{ $document->supplier ?? '—' }}</td>
                                                <td>{{ $amountFormatted ? 'Rp ' . $amountFormatted : '—' }}</td>
                                                <td>{{ $docDate ?? '—' }}</td>
                                                <td>{{ $dueDate ?? '—' }}</td>
                                                <td>
                                                    <span class="badge {{ $documentView['status_class'] ?? 'badge-secondary' }}">
                                                        {{ $documentView['status_label'] ?? '—' }}
                                                    </span>
                                                </td>
                                                <td>
                                                    @if (!empty($documentView['file_url']))
                                                        @can('download vehicle documents')
                                                            <a href="{{ route('vehicle-documents.download', $document) }}"
                                                               class="btn btn-outline-secondary btn-sm" title="Download">
                                                                <i class="fas fa-file-download"></i>
                                                            </a>
                                                        @else
                                                            <span class="text-muted">Attached</span>
                                                        @endcan
                                                    @else
                                                        <span class="text-muted">None</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="btn-group btn-group-sm" role="group">
                                                        <button type="button"
                                                                class="btn btn-outline-info btn-history-document"
                                                                data-document-id="{{ $document->id }}">
                                                            <i class="fas fa-history"></i>
                                                        </button>
                                                        @can('edit vehicle documents')
                                                            <button type="button"
                                                                    class="btn btn-outline-primary btn-edit-document"
                                                                    data-document-id="{{ $document->id }}">
                                                                <i class="fas fa-edit"></i>
                                                            </button>
                                                        @endcan
                                                        @can('delete vehicle documents')
                                                            <form class="d-inline delete-document-form"
                                                                  action="{{ route('vehicle-documents.destroy', $document) }}"
                                                                  method="POST">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn btn-outline-danger">
                                                                    <i class="fas fa-trash-alt"></i>
                                                                </button>
                                                            </form>
                                                        @endcan
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="9" class="text-center text-muted">
                                                    No documents recorded for this vehicle yet.
                                                </td>
                                            </tr>
                                        @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endcan
                    </div>
                </div>
            </div>
        </div>
    </section>

    @can('view vehicle documents')
        <div class="modal fade" id="documentModal" tabindex="-1" role="dialog" aria-labelledby="documentModalLabel"
             aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <form id="document-form" method="POST" action="{{ route('vehicle-documents.store') }}"
                          enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="_method" value="POST">
                        <input type="hidden" name="vehicle_id" value="{{ $vehicle->id }}">
                        <input type="hidden" name="form_context" value="">
                        <input type="hidden" name="document_id" value="">

                        <div class="modal-header">
                            <h5 class="modal-title" id="documentModalLabel">
                                <i class="fas fa-file-medical mr-2"></i>
                                Document
                            </h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="document-type">Document Type</label>
                                    <select class="form-control" id="document-type" name="vehicle_document_type_id" required>
                                        <option value="">Select type</option>
                                        @foreach ($documentTypes as $type)
                                            <option value="{{ $type->id }}">{{ $type->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="document-number">Document Number</label>
                                    <input type="text" class="form-control" id="document-number" name="document_number"
                                           placeholder="Enter document number" required>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="document-date">Document Date</label>
                                    <input type="date" class="form-control" id="document-date" name="document_date" required>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="due-date">Due Date</label>
                                    <input type="date" class="form-control" id="due-date" name="due_date" required>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="document-supplier">Supplier / Issuer</label>
                                    <input type="text" class="form-control" id="document-supplier" name="supplier"
                                           placeholder="e.g., Samsat Balikpapan">
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="document-amount">Amount (Rp)</label>
                                    <input type="number" class="form-control" id="document-amount" name="amount"
                                           placeholder="e.g., 1500000" min="0" step="0.01">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="document-notes">Notes</label>
                                <textarea class="form-control" id="document-notes" name="notes" rows="3"
                                          placeholder="Add remarks or renewal notes"></textarea>
                            </div>
                            <div class="form-group">
                                <label for="document-file">Attachment</label>
                                <input type="file" class="form-control-file" id="document-file" name="file"
                                       accept=".pdf,.jpg,.jpeg,.png">
                                <small class="form-text text-muted">
                                    PDF or image up to 5 MB.
                                </small>
                                <div class="mt-2 existing-file d-none">
                                    <i class="fas fa-paperclip mr-1"></i>
                                    <a href="#" target="_blank" class="existing-file-link">View current file</a>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary btn-sm">
                                <i class="fas fa-save mr-1"></i> Save Document
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="modal fade" id="documentHistoryModal" tabindex="-1" role="dialog"
             aria-labelledby="documentHistoryModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="documentHistoryModalLabel">
                            <i class="fas fa-history mr-2"></i>
                            Document History
                        </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="revision-list">
                            <p class="text-muted mb-0">Select a document to view its revision history.</p>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    @endcan
@endsection

@push('js')
    @can('view vehicle documents')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                var documentModal = $('#documentModal');
                var documentForm = $('#document-form');
                var methodInput = documentForm.find('input[name="_method"]');
                var formContextInput = documentForm.find('input[name="form_context"]');
                var documentIdInput = documentForm.find('input[name="document_id"]');
                var existingFileContainer = documentForm.find('.existing-file');
                var existingFileLink = documentForm.find('.existing-file-link');
                var historyModal = $('#documentHistoryModal');
                var revisionList = historyModal.find('.revision-list');
                var documentsById = @json($documentsForView);
                var storeAction = documentForm.attr('action');
                var updateActionTemplate = @json(route('vehicle-documents.update', ['vehicle_document' => '__document__']));

                function resetForm() {
                    documentForm[0].reset();
                    methodInput.val('POST');
                    formContextInput.val('');
                    documentIdInput.val('');
                    existingFileContainer.addClass('d-none');
                    existingFileLink.attr('href', '#');
                }

            @can('create vehicle documents')
                $('.btn-add-document').on('click', function () {
                    resetForm();
                    documentForm.attr('action', storeAction);
                    formContextInput.val('create');
                    documentModal.find('.modal-title').html('<i class="fas fa-file-medical mr-2"></i>Add Document');
                    documentModal.modal('show');
                });
            @endcan

            @can('edit vehicle documents')
                $('.btn-edit-document').on('click', function () {
                    var documentId = $(this).data('documentId').toString();
                    var documentData = documentsById[documentId] || {};
                    resetForm();
                    methodInput.val('PUT');
                    formContextInput.val('edit');
                    documentIdInput.val(documentId);
                    documentForm.attr('action', updateActionTemplate.replace('__document__', documentId));
                    documentModal.find('.modal-title').html('<i class="fas fa-edit mr-2"></i>Edit Document');

                    documentForm.find('select[name="vehicle_document_type_id"]').val(documentData.vehicle_document_type_id || '');
                    documentForm.find('input[name="document_number"]').val(documentData.document_number || '');
                    documentForm.find('input[name="document_date"]').val(documentData.document_date || '');
                    documentForm.find('input[name="due_date"]').val(documentData.due_date || '');
                    documentForm.find('input[name="supplier"]').val(documentData.supplier || '');
                    documentForm.find('input[name="amount"]').val(documentData.amount || '');
                    documentForm.find('textarea[name="notes"]').val(documentData.notes || '');

                    if (documentData.file_url) {
                        existingFileContainer.removeClass('d-none');
                        existingFileLink.attr('href', documentData.file_url);
                    }

                    documentModal.modal('show');
                });
            @endcan

                $('.btn-history-document').on('click', function () {
                    var documentId = $(this).data('documentId').toString();
                    var documentData = documentsById[documentId] || {};
                    var revisions = documentData.revisions || [];
                    revisionList.empty();

                    if (revisions.length === 0) {
                        revisionList.append('<p class="text-muted mb-0">No history available for this document yet.</p>');
                    } else {
                        revisions.forEach(function (revision) {
                            var supplier = revision.supplier ? revision.supplier : '—';
                            var amount = revision.amount_formatted ? 'Rp ' + revision.amount_formatted : '—';
                            var documentDate = revision.document_date ? revision.document_date : '—';
                            var dueDate = revision.due_date ? revision.due_date : '—';
                            var notes = revision.notes ? revision.notes : '—';
                            var changedBy = revision.changed_by ? revision.changed_by : 'System';
                            var changedAt = revision.changed_at ? revision.changed_at : '—';
                            var fileLink = revision.file_url ? '<a href="' + revision.file_url + '" target="_blank">View attachment</a>' : '—';

                            revisionList.append(
                                '<div class="border rounded p-3 mb-3">' +
                                '<div class="d-flex justify-content-between mb-2">' +
                                '<strong>' + changedAt + '</strong>' +
                                '<span class="text-muted">Updated by ' + changedBy + '</span>' +
                                '</div>' +
                                '<div class="row small">' +
                                '<div class="col-md-6">' +
                                '<p class="mb-1"><strong>Document No:</strong> ' + (revision.document_number || '—') + '</p>' +
                                '<p class="mb-1"><strong>Document Date:</strong> ' + documentDate + '</p>' +
                                '<p class="mb-1"><strong>Due Date:</strong> ' + dueDate + '</p>' +
                                '</div>' +
                                '<div class="col-md-6">' +
                                '<p class="mb-1"><strong>Supplier:</strong> ' + supplier + '</p>' +
                                '<p class="mb-1"><strong>Amount:</strong> ' + amount + '</p>' +
                                '<p class="mb-1"><strong>Attachment:</strong> ' + fileLink + '</p>' +
                                '</div>' +
                                '</div>' +
                                '<p class="mb-0"><strong>Notes:</strong> ' + notes + '</p>' +
                                '</div>'
                            );
                        });
                    }

                    historyModal.modal('show');
                });

                $('.delete-document-form').on('submit', function (event) {
                    if (!confirm('Delete this document? This action cannot be undone.')) {
                        event.preventDefault();
                    }
                });

                var oldFormContext = @json(old('form_context'));
                var hasErrors = {{ $errors->any() ? 'true' : 'false' }};

                if (hasErrors && oldFormContext) {
                    var oldValues = @json(old());

                    if (oldFormContext === 'create') {
                        $('.btn-add-document').trigger('click');
                        documentForm.find('select[name="vehicle_document_type_id"]').val(oldValues.vehicle_document_type_id || '');
                        documentForm.find('input[name="document_number"]').val(oldValues.document_number || '');
                        documentForm.find('input[name="document_date"]').val(oldValues.document_date || '');
                        documentForm.find('input[name="due_date"]').val(oldValues.due_date || '');
                        documentForm.find('input[name="supplier"]').val(oldValues.supplier || '');
                        documentForm.find('input[name="amount"]').val(oldValues.amount || '');
                        documentForm.find('textarea[name="notes"]').val(oldValues.notes || '');
                    } else if (oldFormContext === 'edit' && oldValues.document_id) {
                        var documentId = oldValues.document_id.toString();
                        var documentData = documentsById[documentId] || {};
                        $('.btn-edit-document[data-document-id="' + documentId + '"]').trigger('click');

                        documentForm.find('select[name="vehicle_document_type_id"]').val(oldValues.vehicle_document_type_id || documentData.vehicle_document_type_id || '');
                        documentForm.find('input[name="document_number"]').val(oldValues.document_number || documentData.document_number || '');
                        documentForm.find('input[name="document_date"]').val(oldValues.document_date || documentData.document_date || '');
                        documentForm.find('input[name="due_date"]').val(oldValues.due_date || documentData.due_date || '');
                        documentForm.find('input[name="supplier"]').val(oldValues.supplier || documentData.supplier || '');
                        documentForm.find('input[name="amount"]').val(oldValues.amount || documentData.amount || '');
                        documentForm.find('textarea[name="notes"]').val(oldValues.notes || documentData.notes || '');
                    }
                }
            });
        </script>
    @endcan
@endpush
