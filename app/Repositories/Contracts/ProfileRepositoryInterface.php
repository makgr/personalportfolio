<?php

namespace App\Repositories\Contracts;

interface ProfileRepositoryInterface extends BaseRepositoryInterface
{
    public function getActiveProfile();
    public function updateProfile(array $data);
}