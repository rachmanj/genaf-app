<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class ProjectController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $projects = Project::query();

            return DataTables::of($projects)
                ->addIndexColumn()
                ->addColumn('status_badge', function ($project) {
                    $badgeClass = $project->is_active ? 'badge-success' : 'badge-danger';
                    $statusText = $project->is_active ? 'Active' : 'Inactive';
                    return '<span class="badge ' . $badgeClass . '">' . $statusText . '</span>';
                })
                ->addColumn('actions', function ($project) {
                    $actions = '<div class="btn-group" role="group">';
                    $actions .= '<a href="' . route('admin.projects.edit', $project->id) . '" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i></a>';
                    $actions .= '<form action="' . route('admin.projects.destroy', $project->id) . '" method="POST" class="d-inline">';
                    $actions .= csrf_field();
                    $actions .= method_field('DELETE');
                    $actions .= '<button type="submit" class="btn btn-danger btn-sm" onclick="return confirm(\'Are you sure?\')"><i class="fas fa-trash"></i></button>';
                    $actions .= '</form>';
                    $actions .= '</div>';
                    return $actions;
                })
                ->rawColumns(['status_badge', 'actions'])
                ->make(true);
        }

        return view('admin.projects.index');
    }

    public function create()
    {
        return view('admin.projects.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|string|max:255|unique:projects,code',
            'owner' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'is_active' => 'boolean',
        ]);

        Project::create([
            'code' => $request->code,
            'owner' => $request->owner,
            'location' => $request->location,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('admin.projects.index')
            ->with('success', 'Project created successfully.');
    }

    public function show(Project $project)
    {
        return view('admin.projects.show', compact('project'));
    }

    public function edit(Project $project)
    {
        return view('admin.projects.edit', compact('project'));
    }

    public function update(Request $request, Project $project)
    {
        $request->validate([
            'code' => 'required|string|max:255|unique:projects,code,' . $project->id,
            'owner' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'is_active' => 'boolean',
        ]);

        $project->update([
            'code' => $request->code,
            'owner' => $request->owner,
            'location' => $request->location,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('admin.projects.index')
            ->with('success', 'Project updated successfully.');
    }

    public function destroy(Project $project)
    {
        $project->delete();

        return redirect()->route('admin.projects.index')
            ->with('success', 'Project deleted successfully.');
    }
}
