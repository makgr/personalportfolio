<?php

namespace App\Services;

use App\Repositories\Contracts\TeamRepositoryInterface;
use Illuminate\Support\Str;

class TeamService
{
    protected TeamRepositoryInterface $teamRepository;

    protected MediaService $mediaService;

    public function __construct(
        TeamRepositoryInterface $teamRepository,
        MediaService $mediaService
    ) {
        $this->teamRepository = $teamRepository;
        $this->mediaService = $mediaService;
    }

    public function createTeam(array $data): array
    {
        try {
            // Process skills from comma-separated string to array
            if (! empty($data['skills'])) {
                $data['skills'] = array_map('trim', explode(',', $data['skills']));
            }

            // Generate slug if not provided
            if (empty($data['slug'])) {
                $data['slug'] = Str::slug($data['name']);

                // Ensure unique slug
                $originalSlug = $data['slug'];
                $count = 1;
                while ($this->teamRepository->findBySlug($data['slug'])) {
                    $data['slug'] = $originalSlug.'-'.$count;
                    $count++;
                }
            }

            // Handle image upload
            if (! empty($data['image'])) {
                $data['image'] = $this->mediaService->upload($data['image'], 'teams');
            }

            // Set defaults
            $data['icon'] = $data['icon'] ?? 'bx-user';
            $data['color'] = $data['color'] ?? '#007bff';
            $data['order'] = $data['order'] ?? ($this->getMaxOrder() + 1);
            $data['is_featured'] = ! empty($data['is_featured']);
            $data['is_active'] = ! empty($data['is_active']);

            $team = $this->teamRepository->create($data);

            return [
                'success' => true,
                'message' => 'Team member created successfully!',
                'data' => $team,
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to create team member: '.$e->getMessage(),
            ];
        }
    }

    public function updateTeam(int $id, array $data): array
    {
        try {
            $team = $this->teamRepository->find($id);
            if (! $team) {
                return [
                    'success' => false,
                    'message' => 'Team member not found',
                ];
            }

            // Process skills from comma-separated string to array
            if (! empty($data['skills'])) {
                $data['skills'] = array_map('trim', explode(',', $data['skills']));
            } else {
                $data['skills'] = null;
            }

            // Update slug if name changed
            if ($data['name'] !== $team->name && empty($data['slug'])) {
                $data['slug'] = Str::slug($data['name']);

                // Ensure unique slug (excluding current record)
                $originalSlug = $data['slug'];
                $count = 1;
                while ($this->teamRepository->findBySlug($data['slug']) && $this->teamRepository->findBySlug($data['slug'])->id !== $team->id) {
                    $data['slug'] = $originalSlug.'-'.$count;
                    $count++;
                }
            }

            // Handle image upload
            if (! empty($data['image'])) {
                // Delete old image
                if ($team->image) {
                    $this->mediaService->delete($team->image);
                }
                $data['image'] = $this->mediaService->upload($data['image'], 'teams');
            } else {
                // No new upload: keep the existing image
                unset($data['image']);
            }

            // Set defaults and booleans
            $data['icon'] = $data['icon'] ?? 'bx-user';
            $data['color'] = $data['color'] ?? '#007bff';
            $data['is_featured'] = ! empty($data['is_featured']);
            $data['is_active'] = ! empty($data['is_active']);

            $this->teamRepository->update($data, $id);

            return [
                'success' => true,
                'message' => 'Team member updated successfully!',
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to update team member: '.$e->getMessage(),
            ];
        }
    }

    public function deleteTeam(int $id): array
    {
        try {
            $team = $this->teamRepository->find($id);
            if (! $team) {
                return [
                    'success' => false,
                    'message' => 'Team member not found',
                ];
            }

            // Delete team member image
            if ($team->image) {
                $this->mediaService->delete($team->image);
            }

            $this->teamRepository->delete($id);

            return [
                'success' => true,
                'message' => 'Team member deleted successfully!',
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to delete team member: '.$e->getMessage(),
            ];
        }
    }

    private function getMaxOrder(): int
    {
        return $this->teamRepository->getAllTeams()->max('order') ?? 0;
    }
}
