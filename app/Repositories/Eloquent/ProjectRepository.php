<?php

use App\Models\Project;
use App\Repositories\Contracts\ProjectRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Repositories\Eloquent\BaseRepository;

class ProjectRepository extends BaseRepository implements ProjectRepositoryInterface
{
    public function __construct(Project $model)
    {
        parent::__construct($model);
    }

    public function getAllProjects(): Collection
    {
        return $this->model
            ->with(['category', 'tags', 'media'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getPublishedProjects(): Collection
    {
        return $this->model
            ->where('status', 'published')
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->with(['category', 'tags'])
            ->orderBy('order', 'asc')
            ->orderBy('published_at', 'desc')
            ->get();
    }

    public function getFeaturedProjects(int $limit = 6): Collection
    {
        return $this->model
            ->where('status', 'published')
            ->where('is_featured', true)
            ->with(['category', 'media'])
            ->orderBy('order', 'asc')
            ->limit($limit)
            ->get();
    }

    public function getProjectsByCategory(int $categoryId): Collection
    {
        return $this->model
            ->where('category_id', $categoryId)
            ->where('status', 'published')
            ->with(['tags', 'media'])
            ->orderBy('order', 'asc')
            ->get();
    }

    public function paginatePublishedProjects(int $perPage = 12): LengthAwarePaginator
    {
        return $this->model
            ->where('status', 'published')
            ->whereNotNull('published_at')
            ->with(['category', 'tags', 'media'])
            ->orderBy('order', 'asc')
            ->paginate($perPage);
    }

    public function findBySlug(string $slug)
    {
        return $this->model
            ->where('slug', $slug)
            ->with(['category', 'tags', 'media', 'testimonials'])
            ->firstOrFail();
    }

    public function incrementViews(int $id): bool
    {
        return $this->model
            ->where('id', $id)
            ->increment('views') > 0;
    }

    public function attachTags(int $projectId, array $tagIds): void
    {
        $project = $this->find($projectId);
        if ($project) {
            $project->tags()->attach($tagIds);
        }
    }

    public function syncTags(int $projectId, array $tagIds): void
    {
        $project = $this->find($projectId);
        if ($project) {
            $project->tags()->sync($tagIds);
        }
    }
}
