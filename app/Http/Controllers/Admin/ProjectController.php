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

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $projects = $this->projectRepository->getAllProjects();
            return response()->json([
                'success' => true,
                'data' => $projects->map(function ($project) {
                    return [
                        'id' => $project->id,
                        'title' => $project->title,
                        'status' => $project->status,
                        'category' => $project->category?->name,
                        'is_featured' => $project->is_featured,
                        'views' => $project->views,
                        'created_at' => $project->created_at->format('M d, Y'),
                        'actions' => [
                            'edit' => route('projects.edit', $project->id),
                            'delete' => route('projects.destroy', $project->id)
                        ]
                    ];
                })
            ]);
        }
        
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

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Project created successfully',
                    'data' => $project->load('category', 'tags')
                ]);
            }

            return redirect()
                ->route('projects.index')
                ->with('success', 'Project created successfully');
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to create project',
                    'error' => $e->getMessage()
                ], 422);
            }

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

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Project updated successfully',
                    'data' => $project->load('category', 'tags')
                ]);
            }

            return redirect()
                ->route('projects.edit', $id)
                ->with('success', 'Project updated successfully');
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update project',
                    'error' => $e->getMessage()
                ], 422);
            }

            return back()
                ->withInput()
                ->with('error', 'Failed to update project: ' . $e->getMessage());
        }
    }

    public function destroy(Request $request, $id)
    {
        try {
            $this->projectService->deleteProject($id);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Project deleted successfully'
                ]);
            }

            return redirect()
                ->route('projects.index')
                ->with('success', 'Project deleted successfully');
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to delete project',
                    'error' => $e->getMessage()
                ], 422);
            }

            return back()
                ->with('error', 'Failed to delete project: ' . $e->getMessage());
        }
    }

    public function show(Request $request, $id)
    {
        try {
            $project = $this->projectRepository->findOrFail($id);
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'data' => $project->load('category', 'tags', 'media')
                ]);
            }

            return view('admin.projects.show', compact('project'));
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Project not found'
                ], 404);
            }

            return back()->with('error', 'Project not found');
        }
    }

    public function publish(Request $request, $id)
    {
        try {
            $project = $this->projectService->publishProject($id);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Project published successfully',
                    'data' => $project
                ]);
            }

            return back()->with('success', 'Project published successfully');
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to publish project',
                    'error' => $e->getMessage()
                ], 422);
            }

            return back()->with('error', 'Failed to publish project');
        }
    }

    public function unpublish(Request $request, $id)
    {
        try {
            $project = $this->projectService->unpublishProject($id);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Project unpublished successfully',
                    'data' => $project
                ]);
            }

            return back()->with('success', 'Project unpublished successfully');
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to unpublish project',
                    'error' => $e->getMessage()
                ], 422);
            }

            return back()->with('error', 'Failed to unpublish project');
        }
    }

    public function bulkAction(Request $request)
    {
        try {
            $action = $request->input('action');
            $ids = $request->input('ids', []);

            if (empty($ids) || !$action) {
                throw new \InvalidArgumentException('No items selected or action not specified');
            }

            $result = $this->projectService->bulkAction($action, $ids);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => "Bulk {$action} completed successfully",
                    'data' => $result
                ]);
            }

            return back()->with('success', "Bulk {$action} completed successfully");
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bulk action failed',
                    'error' => $e->getMessage()
                ], 422);
            }

            return back()->with('error', 'Bulk action failed: ' . $e->getMessage());
        }
    }
}
