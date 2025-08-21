<?php

namespace App\Services;

use App\Repositories\Contracts\ProfileRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProfileService
{
    protected ProfileRepositoryInterface $profileRepository;
    protected MediaService $mediaService;

    public function __construct(
        ProfileRepositoryInterface $profileRepository,
        MediaService $mediaService
    ) {
        $this->profileRepository = $profileRepository;
        $this->mediaService = $mediaService;
    }

    public function getActiveProfile()
    {
        return $this->profileRepository->getActiveProfile();
    }

    public function updateProfile(array $data, $avatar = null, $resume = null)
    {
        return DB::transaction(function () use ($data, $avatar, $resume) {
            try {
                $profile = $this->profileRepository->getActiveProfile();

                if ($avatar) {
                    if ($profile && $profile->avatar_url) {
                        $this->mediaService->delete($profile->avatar_url);
                    }
                    $data['avatar_url'] = $this->mediaService->upload($avatar, 'profile');
                }

                if ($resume) {
                    if ($profile && $profile->resume_url) {
                        $this->mediaService->delete($profile->resume_url);
                    }
                    $data['resume_url'] = $this->mediaService->upload($resume, 'profile');
                }

                return $this->profileRepository->updateProfile($data);
            } catch (\Exception $e) {
                Log::error('Failed to update profile: ' . $e->getMessage());
                throw $e;
            }
        });
    }

    public function getSocialLinks()
    {
        $profile = $this->getActiveProfile();
        return $profile ? $profile->social_links : [];
    }

    public function updateSocialLinks(array $socialLinks)
    {
        return DB::transaction(function () use ($socialLinks) {
            try {
                return $this->profileRepository->updateProfile([
                    'social_links' => $socialLinks
                ]);
            } catch (\Exception $e) {
                Log::error('Failed to update social links: ' . $e->getMessage());
                throw $e;
            }
        });
    }

    public function updateMetaTags(array $metaTags)
    {
        return DB::transaction(function () use ($metaTags) {
            try {
                return $this->profileRepository->updateProfile([
                    'meta_tags' => $metaTags
                ]);
            } catch (\Exception $e) {
                Log::error('Failed to update meta tags: ' . $e->getMessage());
                throw $e;
            }
        });
    }
}