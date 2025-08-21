<?php

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Collection;

interface ServiceRepositoryInterface extends BaseRepositoryInterface
{
    public function getAllServices(): Collection;
    public function getActiveServices(): Collection;
    public function getOrderedServices(): Collection;
    public function findBySlug(string $slug);
    public function reorderServices(array $items): bool;
}