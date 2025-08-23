<?php

namespace App\Repositories\Eloquent;

use App\Models\Team;
use App\Repositories\Contracts\TeamRepositoryInterface;
use App\Repositories\Eloquent\BaseRepository;
use Illuminate\Database\Eloquent\Collection;

class TeamRepository extends BaseRepository implements TeamRepositoryInterface
{
    public function __construct(Team $model)
    {
        parent::__construct($model);
    }

    public function getAllTeams(): Collection
    {
        return $this->model->orderBy('order', 'asc')->get();
    }

    public function getActiveTeams(): Collection
    {
        return $this->model->active()->ordered()->get();
    }

    public function getFeaturedTeams(int $limit = 6): Collection
    {
        return $this->model->featured()->active()->ordered()->take($limit)->get();
    }

    public function getTeamsByDepartment(string $department): Collection
    {
        return $this->model->byDepartment($department)->active()->ordered()->get();
    }

    public function findBySlug(string $slug)
    {
        return $this->model->where('slug', $slug)->first();
    }

    public function toggleStatus(int $id): bool
    {
        $team = $this->find($id);
        if ($team) {
            $team->update(['is_active' => !$team->is_active]);
            return true;
        }
        return false;
    }

    public function toggleFeatured(int $id): bool
    {
        $team = $this->find($id);
        if ($team) {
            $team->update(['is_featured' => !$team->is_featured]);
            return true;
        }
        return false;
    }

    public function updateOrder(array $items): bool
    {
        try {
            foreach ($items as $item) {
                $this->model->where('id', $item['id'])->update(['order' => $item['order']]);
            }
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}