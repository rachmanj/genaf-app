@extends('layouts.main')

@section('title_page', 'Project Details')
@section('breadcrumb_title')
    <li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.projects.index') }}">Projects</a></li>
    <li class="breadcrumb-item active">Details</li>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card card-info">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-info-circle mr-1"></i>
                            Project Details
                        </h3>
                        <div class="card-tools">
                            <a href="{{ route('admin.projects.edit', $project->id) }}" class="btn btn-warning btn-sm">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <table class="table table-bordered">
                                    <tr>
                                        <th width="40%">Project Code:</th>
                                        <td><strong>{{ $project->code }}</strong></td>
                                    </tr>
                                    <tr>
                                        <th>Owner:</th>
                                        <td>{{ $project->owner }}</td>
                                    </tr>
                                    <tr>
                                        <th>Location:</th>
                                        <td>{{ $project->location }}</td>
                                    </tr>
                                    <tr>
                                        <th>Status:</th>
                                        <td>
                                            @if($project->is_active)
                                                <span class="badge badge-success">Active</span>
                                            @else
                                                <span class="badge badge-danger">Inactive</span>
                                            @endif
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <table class="table table-bordered">
                                    <tr>
                                        <th width="40%">Created At:</th>
                                        <td>{{ $project->created_at->format('d M Y H:i') }}</td>
                                    </tr>
                                    <tr>
                                        <th>Updated At:</th>
                                        <td>{{ $project->updated_at->format('d M Y H:i') }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <a href="{{ route('admin.projects.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to List
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
