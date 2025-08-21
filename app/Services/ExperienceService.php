<?php

namespace App\Services;

use App\Models\Experience;
use App\Repositories\Contracts\ExperienceRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ExperienceService
{
    protected ExperienceRepositoryInterface $experienceRepository;
    protected MediaService $mediaService;

    public function __construct(
        ExperienceRepositoryInterface $experienceRepository,
        MediaService $mediaService
    ) {
        $this->experienceRepository = $experienceRepository;
        $this->mediaService = $mediaService;
    }

    public function getAllExperiences()
    {
        return $this->experienceRepository->getAllExperiences();
    }

    public function getActiveExperiences()
    {
        return $this->experienceRepository->getActiveExperiences();
    }

    public function getCurrentExperience()
    {
        return $this->experienceRepository->getCurrentExperience();
    }

    public function createExperience(array $data, $companyLogo = null)
    {
        return DB::transaction(function () use ($data, $companyLogo) {
            try {
                if ($data['is_current'] ?? false) {
                    $data['end_date'] = null;
                    Experience::where('is_current', true)->update(['is_current' => false]);
                }

                if ($companyLogo) {
                    $data['company_logo'] = $this->mediaService->upload($companyLogo, 'companies');
                }

                $data['order'] = Experience::max('order') + 1;

                return $this->experienceRepository->create($data);
            } catch (\Exception $e) {
                Log::error('Failed to create experience: ' . $e->getMessage());
                throw $e;
            }
        });
    }

    public function updateExperience(int $id, array $data, $companyLogo = null)
    {
        return DB::transaction(function () use ($id, $data, $companyLogo) {
            try {
                $experience = $this->experienceRepository->findOrFail($id);

                if ($data['is_current'] ?? false) {
                    $data['end_date'] = null;
                    Experience::where('id', '!=', $id)
                        ->where('is_current', true)
                        ->update(['is_current' => false]);
                }

                if ($companyLogo) {
                    if ($experience->company_logo) {
                        $this->mediaService->delete($experience->company_logo);
                    }
                    $data['company_logo'] = $this->mediaService->upload($companyLogo, 'companies');
                }

                $this->experienceRepository->update($data, $id);
                return $experience->fresh();
            } catch (\Exception $e) {
                Log::error('Failed to update experience: ' . $e->getMessage());
                throw $e;
            }
        });
    }

    public function deleteExperience(int $id)
    {
        return DB::transaction(function () use ($id) {
            try {
                $experience = $this->experienceRepository->findOrFail($id);

                if ($experience->company_logo) {
                    $this->mediaService->delete($experience->company_logo);
                }

                return $this->experienceRepository->delete($id);
            } catch (\Exception $e) {
                Log::error('Failed to delete experience: ' . $e->getMessage());
                throw $e;
            }
        });
    }

    public function reorderExperiences(array $items)
    {
        return DB::transaction(function () use ($items) {
            try {
                return $this->experienceRepository->reorderExperiences($items);
            } catch (\Exception $e) {
                Log::error('Failed to reorder experiences: ' . $e->getMessage());
                throw $e;
            }
        });
    }

    public function setAsCurrent(int $id)
    {
        return DB::transaction(function () use ($id) {
            try {
                Experience::where('is_current', true)->update(['is_current' => false]);
                
                $experience = $this->experienceRepository->findOrFail($id);
                $experience->update([
                    'is_current' => true,
                    'end_date' => null
                ]);
                
                return $experience;
            } catch (\Exception $e) {
                Log::error('Failed to set experience as current: ' . $e->getMessage());
                throw $e;
            }
        });
    }
}