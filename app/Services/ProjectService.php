<?php

namespace App\Services;

use App\Repositories\Contracts\ProjectRepositoryInterface;
use App\Models\Project;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class ProjectService
{
    protected ProjectRepositoryInterface $projectRepository;
    protected MediaService $mediaService;

    public function __construct(
        ProjectRepositoryInterface $projectRepository,
        MediaService $mediaService
    ) {
        $this->projectRepository = $projectRepository;
        $this->mediaService = $mediaService;
    }

    public function getAllProjects()
    {
        return $this->projectRepository->getAllProjects();
    }

    public function getPublishedProjects()
    {
        return $this->projectRepository->getPublishedProjects();
    }

    public function getFeaturedProjects($limit = 6)
    {
        return $this->projectRepository->getFeaturedProjects($limit);
    }

    public function findBySlug(string $slug)
    {
        $project = $this->projectRepository->findBySlug($slug);
        $this->projectRepository->incrementViews($project->id);
        return $project;
    }

    public function createProject(array $data, $featuredImage = null, $gallery = [])
    {
        return DB::transaction(function () use ($data, $featuredImage, $gallery) {
            try {
                // Generate slug if not provided
                if (!isset($data['slug']) || empty($data['slug'])) {
                    $data['slug'] = Str::slug($data['title']);
                }

                // Process tech_stack JSON string to array
                if (isset($data['tech_stack']) && is_string($data['tech_stack'])) {
                    $data['tech_stack'] = json_decode($data['tech_stack'], true) ?: [];
                }

                // Handle boolean fields that might not be present
                $data['is_featured'] = isset($data['is_featured']) && $data['is_featured'] ? true : false;

                // Auto-set published_at when status is published
                if (isset($data['status']) && $data['status'] === 'published' && empty($data['published_at'])) {
                    $data['published_at'] = now();
                }

                // Handle featured image
                if ($featuredImage) {
                    $data['featured_image'] = $this->mediaService->upload($featuredImage, 'project');
                }

                // Create project
                $project = $this->projectRepository->create($data);

                // Handle gallery images
                if (!empty($gallery)) {
                    foreach ($gallery as $image) {
                        $this->mediaService->attachToModel($project, $image, 'gallery');
                    }
                }

                // Sync tags
                if (isset($data['tags'])) {
                    $this->projectRepository->syncTags($project->id, $data['tags']);
                }

                // Create initial version
                $project->createVersion('Initial creation');

                return $project;
            } catch (\Exception $e) {
                Log::error('Failed to create project: ' . $e->getMessage());
                throw $e;
            }
        });
    }

    public function updateProject(int $id, array $data, $featuredImage = null, $gallery = [])
    {
        return DB::transaction(function () use ($id, $data, $featuredImage, $gallery) {
            try {
                $project = $this->projectRepository->findOrFail($id);

                // Update slug if title changed
                if (isset($data['title']) && $data['title'] !== $project->title) {
                    if (!isset($data['slug']) || empty($data['slug'])) {
                        $data['slug'] = Str::slug($data['title']);
                    }
                }

                // Process tech_stack JSON string to array
                if (isset($data['tech_stack']) && is_string($data['tech_stack'])) {
                    $data['tech_stack'] = json_decode($data['tech_stack'], true) ?: [];
                }

                // Handle boolean fields that might not be present
                $data['is_featured'] = isset($data['is_featured']) && $data['is_featured'] ? true : false;

                // Auto-set published_at when status changes to published
                if (isset($data['status']) && $data['status'] === 'published' && empty($project->published_at) && empty($data['published_at'])) {
                    $data['published_at'] = now();
                }

                // Handle featured image
                if ($featuredImage) {
                    // Delete old image
                    if ($project->featured_image) {
                        $this->mediaService->delete($project->featured_image);
                    }
                    $data['featured_image'] = $this->mediaService->upload($featuredImage, 'project');
                }

                // Update project
                $this->projectRepository->update($data, $id);

                // Handle gallery
                if (!empty($gallery)) {
                    foreach ($gallery as $image) {
                        $this->mediaService->attachToModel($project, $image, 'gallery');
                    }
                }

                // Sync tags
                if (isset($data['tags'])) {
                    $this->projectRepository->syncTags($id, $data['tags']);
                }

                return $project->fresh();
            } catch (\Exception $e) {
                Log::error('Failed to update project: ' . $e->getMessage());
                throw $e;
            }
        });
    }

    public function deleteProject(int $id)
    {
        return DB::transaction(function () use ($id) {
            try {
                $project = $this->projectRepository->findOrFail($id);

                // Delete featured image
                if ($project->featured_image) {
                    $this->mediaService->delete($project->featured_image);
                }

                // Delete gallery images (will auto-delete via model events)
                $project->media()->delete();

                // Delete project
                return $this->projectRepository->delete($id);
            } catch (\Exception $e) {
                Log::error('Failed to delete project: ' . $e->getMessage());
                throw $e;
            }
        });
    }

    public function publishProject(int $id)
    {
        return $this->projectRepository->update([
            'status' => 'published',
            'published_at' => now(),
        ], $id);
    }

    public function unpublishProject(int $id)
    {
        return $this->projectRepository->update([
            'status' => 'draft',
        ], $id);
    }

    public function bulkAction(string $action, array $ids)
    {
        return DB::transaction(function () use ($action, $ids) {
            switch ($action) {
                case 'delete':
                    foreach ($ids as $id) {
                        $this->deleteProject($id);
                    }
                    break;

                case 'publish':
                    foreach ($ids as $id) {
                        $this->publishProject($id);
                    }
                    break;

                case 'unpublish':
                    foreach ($ids as $id) {
                        $this->unpublishProject($id);
                    }
                    break;

                case 'feature':
                    Project::whereIn('id', $ids)
                        ->update(['is_featured' => true]);
                    break;

                case 'unfeature':
                    Project::whereIn('id', $ids)
                        ->update(['is_featured' => false]);
                    break;

                default:
                    throw new \InvalidArgumentException("Invalid bulk action: {$action}");
            }

            return ['affected_count' => count($ids)];
        });
    }
}
