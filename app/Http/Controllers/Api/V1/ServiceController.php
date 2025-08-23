<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Repositories\Contracts\ServiceRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ServiceController extends Controller
{
    protected ServiceRepositoryInterface $serviceRepository;

    public function __construct(ServiceRepositoryInterface $serviceRepository)
    {
        $this->serviceRepository = $serviceRepository;
    }

    /**
     * Get all active services
     */
    public function index(Request $request)
    {
        try {
            $services = $this->serviceRepository
                ->where('is_active', true)
                ->orderBy('order', 'asc')
                ->get();

            $data = $services->map(function ($service) {
                return [
                    'id' => $service->id,
                    'title' => $service->title,
                    'slug' => $service->slug,
                    'description' => $service->description,
                    'short_description' => $service->short_description,
                    'icon' => $service->icon,
                    'color' => $service->color,
                    'features' => $service->features,
                    'price' => $service->price,
                    'price_type' => $service->price_type,
                    'currency' => $service->currency,
                    'delivery_time' => $service->delivery_time,
                    'revisions' => $service->revisions,
                    'order' => $service->order,
                    'is_featured' => $service->is_featured,
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $data,
                'meta' => [
                    'total' => $services->count(),
                    'featured' => $services->where('is_featured', true)->count(),
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to fetch services: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch services.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }
}
