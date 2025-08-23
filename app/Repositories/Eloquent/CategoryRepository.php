<?php

namespace App\Repositories\Eloquent;

use App\Models\Category;
use App\Repositories\Contracts\CategoryRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class CategoryRepository extends BaseRepository implements CategoryRepositoryInterface
{
    public function __construct(Category $model)
    {
        parent::__construct($model);
    }

    public function getActiveCategories(): Collection
    {
        return $this->model->active()->ordered()->get();
    }

    public function getCategoriesByType(string $type): Collection
    {
        return $this->model->byType($type)->ordered()->get();
    }

    public function canDelete($id): bool
    {
        $category = $this->find($id);
        
        if (!$category) {
            return false;
        }

        // Check if category has any projects
        $hasProjects = $category->projects()->count() > 0;
        
        // Check if category has any blog posts
        $hasBlogPosts = $category->blogPosts()->count() > 0;
        
        // Category can be deleted only if it has no content
        return !$hasProjects && !$hasBlogPosts;
    }

    public function getOrderedCategories(): Collection
    {
        return $this->model->ordered()->get();
    }

    public function getBySlug(string $slug)
    {
        return $this->model->where('slug', $slug)->first();
    }

    public function delete($id): bool
    {
        if (!$this->canDelete($id)) {
            return false;
        }
        
        return parent::delete($id);
    }
}