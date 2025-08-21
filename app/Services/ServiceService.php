<?php

namespace App\Services;

use App\Models\Service;
use App\Repositories\Contracts\ServiceRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ServiceService
{
    protected ServiceRepositoryInterface $serviceRepository;
    protected MediaService $mediaService;

    public function __construct(
        ServiceRepositoryInterface $serviceRepository,
        MediaService $mediaService
    ) {
        $this->serviceRepository = $serviceRepository;
        $this->mediaService = $mediaService;
    }

    public function getAllServices()
    {
        return $this->serviceRepository->getAllServices();
    }

    public function getActiveServices()
    {
        return $this->serviceRepository->getActiveServices();
    }

    public function createService(array $data, $image = null, array $gallery = [])
    {
        return DB::transaction(function () use ($data, $image, $gallery) {
            try {
                if (!isset($data['slug']) || empty($data['slug'])) {
                    $data['slug'] = Str::slug($data['title']);
                }

                $data['order'] = Service::max('order') + 1;

                if ($image) {
                    $data['image'] = $this->mediaService->upload($image, 'services');
                }

                $service = $this->serviceRepository->create($data);

                if (!empty($gallery)) {
                    foreach ($gallery as $file) {
                        $this->mediaService->attachToModel($service, $file, 'gallery');
                    }
                }

                return $service;
            } catch (\Exception $e) {
                Log::error('Failed to create service: ' . $e->getMessage());
                throw $e;
            }
        });
    }

    public function updateService(int $id, array $data, $image = null, array $gallery = [])
    {
        return DB::transaction(function () use ($id, $data, $image, $gallery) {
            try {
                $service = $this->serviceRepository->findOrFail($id);

                if (isset($data['title']) && $data['title'] !== $service->title) {
                    if (!isset($data['slug']) || empty($data['slug'])) {
                        $data['slug'] = Str::slug($data['title']);
                    }
                }

                if ($image) {
                    if ($service->image) {
                        $this->mediaService->delete($service->image);
                    }
                    $data['image'] = $this->mediaService->upload($image, 'services');
                }

                $this->serviceRepository->update($data, $id);

                if (!empty($gallery)) {
                    foreach ($gallery as $file) {
                        $this->mediaService->attachToModel($service, $file, 'gallery');
                    }
                }

                return $service->fresh();
            } catch (\Exception $e) {
                Log::error('Failed to update service: ' . $e->getMessage());
                throw $e;
            }
        });
    }

    public function deleteService(int $id)
    {
        return DB::transaction(function () use ($id) {
            try {
                $service = $this->serviceRepository->findOrFail($id);

                if ($service->image) {
                    $this->mediaService->delete($service->image);
                }

                $service->media()->delete();

                return $this->serviceRepository->delete($id);
            } catch (\Exception $e) {
                Log::error('Failed to delete service: ' . $e->getMessage());
                throw $e;
            }
        });
    }

    public function reorderServices(array $items)
    {
        return DB::transaction(function () use ($items) {
            try {
                return $this->serviceRepository->reorderServices($items);
            } catch (\Exception $e) {
                Log::error('Failed to reorder services: ' . $e->getMessage());
                throw $e;
            }
        });
    }

    public function toggleActive(int $id)
    {
        return DB::transaction(function () use ($id) {
            try {
                $service = $this->serviceRepository->findOrFail($id);
                $service->is_active = !$service->is_active;
                $service->save();
                
                return $service;
            } catch (\Exception $e) {
                Log::error('Failed to toggle active status: ' . $e->getMessage());
                throw $e;
            }
        });
    }
}