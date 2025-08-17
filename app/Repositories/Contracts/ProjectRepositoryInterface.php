<?php

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface ProjectRepositoryInterface extends BaseRepositoryInterface
{
    public function getAllProjects(): Collection;
    public function getPublishedProjects(): Collection;
    public function getFeaturedProjects(int $limit = 6): Collection;
    public function getProjectsByCategory(int $categoryId): Collection;
    public function paginatePublishedProjects(int $perPage = 12): LengthAwarePaginator;
    public function findBySlug(string $slug);
    public function incrementViews(int $id): bool;
    public function attachTags(int $projectId, array $tagIds): void;
    public function syncTags(int $projectId, array $tagIds): void;
}
