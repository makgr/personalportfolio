<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ProjectService;
use App\Repositories\Contracts\ProjectRepositoryInterface;
use App\Http\Requests\Admin\ProjectRequest;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    protected ProjectService $projectService;
    protected ProjectRepositoryInterface $projectRepository;

    public function __construct(
        ProjectService $projectService,
        ProjectRepositoryInterface $projectRepository
    ) {
        $this->middleware(['auth', 'admin']);
        $this->projectService = $projectService;
        $this->projectRepository = $projectRepository;
    }

    public function index()
    {
        $projects = $this->projectRepository->getAllProjects();
        return view('admin.projects.index', compact('projects'));
    }

    public function create()
    {
        $categories = Category::where('type', 'project')->get();
        $tags = Tag::all();
        return view('admin.projects.create', compact('categories', 'tags'));
    }

    public function store(ProjectRequest $request)
    {
        try {
            $project = $this->projectService->createProject(
                $request->validated(),
                $request->file('featured_image'),
                $request->file('gallery', [])
            );

            return redirect()
                ->route('admin.projects.index')
                ->with('success', 'Project created successfully');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Failed to create project: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $project = $this->projectRepository->findOrFail($id);
        $categories = Category::where('type', 'project')->get();
        $tags = Tag::all();

        return view('admin.projects.edit', compact('project', 'categories', 'tags'));
    }

    public function update(ProjectRequest $request, $id)
    {
        try {
            $project = $this->projectService->updateProject(
                $id,
                $request->validated(),
                $request->file('featured_image'),
                $request->file('gallery', [])
            );

            return redirect()
                ->route('admin.projects.edit', $id)
                ->with('success', 'Project updated successfully');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Failed to update project: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $this->projectService->deleteProject($id);

            return redirect()
                ->route('admin.projects.index')
                ->with('success', 'Project deleted successfully');
        } catch (\Exception $e) {
            return back()
                ->with('error', 'Failed to delete project: ' . $e->getMessage());
        }
    }
}
