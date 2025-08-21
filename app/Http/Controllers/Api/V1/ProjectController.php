<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProjectCollection;
use App\Http\Resources\ProjectResource;
use App\Services\ProjectService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ProjectController extends Controller
{
    protected ProjectService $projectService;

    public function __construct(ProjectService $projectService)
    {
        $this->projectService = $projectService;
    }

    /**
     * Get all published projects
     */
    public function index(Request $request)
    {
        try {
            $projects = $this->projectService->getPublishedProjects();
            
            // Filter by category if provided
            if ($request->filled('category')) {
                $projects = $projects->where('category.slug', $request->category);
            }
            
            // Filter by type if provided
            if ($request->filled('type')) {
                $projects = $projects->where('type', $request->type);
            }
            
            // Limit results if requested
            if ($request->filled('limit')) {
                $projects = $projects->take((int) $request->limit);
            }

            return new ProjectCollection($projects);
        } catch (\Exception $e) {
            Log::error('Failed to fetch projects: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch projects',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get featured projects
     */
    public function featured(Request $request)
    {
        try {
            $limit = $request->get('limit', 6);
            $projects = $this->projectService->getFeaturedProjects($limit);
            
            return new ProjectCollection($projects);
        } catch (\Exception $e) {
            Log::error('Failed to fetch featured projects: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch featured projects'
            ], 500);
        }
    }

    /**
     * Get single project by slug
     */
    public function show(string $slug)
    {
        try {
            $project = $this->projectService->findBySlug($slug);
            
            return new ProjectResource($project);
        } catch (\Exception $e) {
            Log::error('Project not found: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Project not found'
            ], 404);
        }
    }
}
