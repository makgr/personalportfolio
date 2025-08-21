<?php

namespace App\Repositories\Eloquent;

use App\Models\Profile;
use App\Repositories\Contracts\ProfileRepositoryInterface;

class ProfileRepository extends BaseRepository implements ProfileRepositoryInterface
{
    public function __construct(Profile $model)
    {
        parent::__construct($model);
    }

    public function getActiveProfile()
    {
        return $this->model
            ->where('is_active', true)
            ->first();
    }

    public function updateProfile(array $data)
    {
        $profile = $this->getActiveProfile();
        
        if (!$profile) {
            return $this->create($data + ['is_active' => true]);
        }
        
        $profile->update($data);
        return $profile;
    }
}