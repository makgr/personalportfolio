<?php

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Collection;

interface CategoryRepositoryInterface extends BaseRepositoryInterface
{
    public function getActiveCategories(): Collection;
    public function getCategoriesByType(string $type): Collection;
    public function canDelete($id): bool;
    public function getOrderedCategories(): Collection;
    public function getBySlug(string $slug);
}