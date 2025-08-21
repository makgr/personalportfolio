<?php

namespace App\Repositories\Eloquent;

use App\Models\Experience;
use App\Repositories\Contracts\ExperienceRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class ExperienceRepository extends BaseRepository implements ExperienceRepositoryInterface
{
    public function __construct(Experience $model)
    {
        parent::__construct($model);
    }

    public function getAllExperiences(): Collection
    {
        return $this->model
            ->orderBy('start_date', 'desc')
            ->get();
    }

    public function getActiveExperiences(): Collection
    {
        return $this->model
            ->where('is_active', true)
            ->orderBy('start_date', 'desc')
            ->get();
    }

    public function getCurrentExperience()
    {
        return $this->model
            ->where('is_current', true)
            ->where('is_active', true)
            ->first();
    }

    public function getOrderedExperiences(): Collection
    {
        return $this->model
            ->orderBy('order', 'asc')
            ->orderBy('start_date', 'desc')
            ->get();
    }

    public function reorderExperiences(array $items): bool
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