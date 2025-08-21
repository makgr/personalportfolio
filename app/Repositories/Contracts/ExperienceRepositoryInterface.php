<?php

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Collection;

interface ExperienceRepositoryInterface extends BaseRepositoryInterface
{
    public function getAllExperiences(): Collection;
    public function getActiveExperiences(): Collection;
    public function getCurrentExperience();
    public function getOrderedExperiences(): Collection;
    public function reorderExperiences(array $items): bool;
}