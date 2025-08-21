<?php

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface ContactRepositoryInterface extends BaseRepositoryInterface
{
    public function getAllContacts(): Collection;
    public function getNewContacts(): Collection;
    public function getContactsPaginated(int $perPage = 15): LengthAwarePaginator;
    public function markAsRead(int $id): bool;
    public function getContactsByStatus(string $status): Collection;
    public function getContactsByDateRange($fromDate, $toDate): Collection;
}