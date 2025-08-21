<?php

namespace App\Repositories\Eloquent;

use App\Models\ContactSubmission;
use App\Repositories\Contracts\ContactRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class ContactRepository extends BaseRepository implements ContactRepositoryInterface
{
    public function __construct(ContactSubmission $model)
    {
        parent::__construct($model);
    }

    public function getAllContacts(): Collection
    {
        return $this->model
            ->latest()
            ->get();
    }

    public function getNewContacts(): Collection
    {
        return $this->model
            ->where('status', 'new')
            ->latest()
            ->get();
    }

    public function getContactsPaginated(int $perPage = 15): LengthAwarePaginator
    {
        return $this->model
            ->latest()
            ->paginate($perPage);
    }

    public function markAsRead(int $id): bool
    {
        return $this->update(['status' => 'read', 'read_at' => now()], $id);
    }

    public function getContactsByStatus(string $status): Collection
    {
        return $this->model
            ->where('status', $status)
            ->latest()
            ->get();
    }

    public function getContactsByDateRange($fromDate, $toDate): Collection
    {
        return $this->model
            ->whereBetween('created_at', [$fromDate, $toDate])
            ->latest()
            ->get();
    }
}