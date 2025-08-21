<?php

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Collection;

interface SkillRepositoryInterface extends BaseRepositoryInterface
{
    public function getAllSkills(): Collection;
    public function getActiveSkills(): Collection;
    public function getPrimarySkills(): Collection;
    public function getSkillsByCategory(string $category): Collection;
    public function getOrderedSkills(): Collection;
    public function findBySlug(string $slug);
    public function reorderSkills(array $items): bool;
}