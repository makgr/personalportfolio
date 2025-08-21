<?php

namespace App\Repositories\Eloquent;

use App\Models\Service;
use App\Repositories\Contracts\ServiceRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class ServiceRepository extends BaseRepository implements ServiceRepositoryInterface
{
    public function __construct(Service $model)
    {
        parent::__construct($model);
    }

    public function getAllServices(): Collection
    {
        return $this->model
            ->with('media')
            ->orderBy('order', 'asc')
            ->get();
    }

    public function getActiveServices(): Collection
    {
        return $this->model
            ->where('is_active', true)
            ->with('media')
            ->orderBy('order', 'asc')
            ->get();
    }

    public function getOrderedServices(): Collection
    {
        return $this->model
            ->orderBy('order', 'asc')
            ->orderBy('title', 'asc')
            ->get();
    }

    public function findBySlug(string $slug)
    {
        return $this->model
            ->where('slug', $slug)
            ->with('media')
            ->firstOrFail();
    }

    public function reorderServices(array $items): bool
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