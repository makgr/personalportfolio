<?php

namespace App\Repositories\Eloquent;

use App\Models\Skill;
use App\Repositories\Contracts\SkillRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class SkillRepository extends BaseRepository implements SkillRepositoryInterface
{
    public function __construct(Skill $model)
    {
        parent::__construct($model);
    }

    public function getAllSkills(): Collection
    {
        return $this->model
            ->orderBy('order', 'asc')
            ->orderBy('category', 'asc')
            ->get();
    }

    public function getActiveSkills(): Collection
    {
        return $this->model
            ->where('is_active', true)
            ->orderBy('order', 'asc')
            ->get();
    }

    public function getPrimarySkills(): Collection
    {
        return $this->model
            ->where('is_primary', true)
            ->where('is_active', true)
            ->orderBy('proficiency', 'desc')
            ->orderBy('order', 'asc')
            ->get();
    }

    public function getSkillsByCategory(string $category): Collection
    {
        return $this->model
            ->where('category', $category)
            ->where('is_active', true)
            ->orderBy('proficiency', 'desc')
            ->get();
    }

    public function getOrderedSkills(): Collection
    {
        return $this->model
            ->orderBy('order', 'asc')
            ->orderBy('name', 'asc')
            ->get();
    }

    public function findBySlug(string $slug)
    {
        return $this->model
            ->where('slug', $slug)
            ->firstOrFail();
    }

    public function reorderSkills(array $items): bool
    {
        try {
            foreach ($items as $item) {
                $this->model
                    ->where('id', $item['id'])
                    ->update(['order' => $item['order']]);
            }
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}