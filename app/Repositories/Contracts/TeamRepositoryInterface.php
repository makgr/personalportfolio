<?php

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Collection;

interface TeamRepositoryInterface extends BaseRepositoryInterface
{
    public function getAllTeams(): Collection;
    public function getActiveTeams(): Collection;
    public function getFeaturedTeams(int $limit = 6): Collection;
    public function getTeamsByDepartment(string $department): Collection;
    public function findBySlug(string $slug);
    public function toggleStatus(int $id): bool;
    public function toggleFeatured(int $id): bool;
    public function updateOrder(array $items): bool;
}