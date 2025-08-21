<?php

namespace App\Services;

use App\Models\Skill;
use App\Repositories\Contracts\SkillRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class SkillService
{
    protected SkillRepositoryInterface $skillRepository;

    public function __construct(SkillRepositoryInterface $skillRepository)
    {
        $this->skillRepository = $skillRepository;
    }

    public function getAllSkills()
    {
        return $this->skillRepository->getAllSkills();
    }

    public function getActiveSkills()
    {
        return $this->skillRepository->getActiveSkills();
    }

    public function getPrimarySkills()
    {
        return $this->skillRepository->getPrimarySkills();
    }

    public function getSkillsByCategory(string $category)
    {
        return $this->skillRepository->getSkillsByCategory($category);
    }

    public function createSkill(array $data)
    {
        return DB::transaction(function () use ($data) {
            try {
                if (!isset($data['slug']) || empty($data['slug'])) {
                    $data['slug'] = Str::slug($data['name']);
                }

                $data['order'] = Skill::max('order') + 1;

                return $this->skillRepository->create($data);
            } catch (\Exception $e) {
                Log::error('Failed to create skill: ' . $e->getMessage());
                throw $e;
            }
        });
    }

    public function updateSkill(int $id, array $data)
    {
        return DB::transaction(function () use ($id, $data) {
            try {
                $skill = $this->skillRepository->findOrFail($id);

                if (isset($data['name']) && $data['name'] !== $skill->name) {
                    if (!isset($data['slug']) || empty($data['slug'])) {
                        $data['slug'] = Str::slug($data['name']);
                    }
                }

                $this->skillRepository->update($data, $id);
                return $skill->fresh();
            } catch (\Exception $e) {
                Log::error('Failed to update skill: ' . $e->getMessage());
                throw $e;
            }
        });
    }

    public function deleteSkill(int $id)
    {
        return DB::transaction(function () use ($id) {
            try {
                return $this->skillRepository->delete($id);
            } catch (\Exception $e) {
                Log::error('Failed to delete skill: ' . $e->getMessage());
                throw $e;
            }
        });
    }

    public function reorderSkills(array $items)
    {
        return DB::transaction(function () use ($items) {
            try {
                return $this->skillRepository->reorderSkills($items);
            } catch (\Exception $e) {
                Log::error('Failed to reorder skills: ' . $e->getMessage());
                throw $e;
            }
        });
    }

    public function togglePrimary(int $id)
    {
        return DB::transaction(function () use ($id) {
            try {
                $skill = $this->skillRepository->findOrFail($id);
                $skill->is_primary = !$skill->is_primary;
                $skill->save();
                
                return $skill;
            } catch (\Exception $e) {
                Log::error('Failed to toggle primary status: ' . $e->getMessage());
                throw $e;
            }
        });
    }

    public function bulkUpdateProficiency(array $skills)
    {
        return DB::transaction(function () use ($skills) {
            try {
                foreach ($skills as $skillData) {
                    $this->skillRepository->update(
                        ['proficiency' => $skillData['proficiency']], 
                        $skillData['id']
                    );
                }
                return true;
            } catch (\Exception $e) {
                Log::error('Failed to bulk update proficiency: ' . $e->getMessage());
                throw $e;
            }
        });
    }
}