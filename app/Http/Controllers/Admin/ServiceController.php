<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Services\MediaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ServiceController extends Controller
{
    protected MediaService $mediaService;

    public function __construct(MediaService $mediaService)
    {
        $this->middleware(['auth', 'admin']);
        $this->mediaService = $mediaService;
    }

    /**
     * Get all services
     */
    public function index(Request $request)
    {
        try {
            $query = Service::with('media');

            // Search filter
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                        ->orWhere('short_description', 'like', "%{$search}%");
                });
            }

            // Active filter
            if ($request->filled('is_active')) {
                $query->where('is_active', $request->boolean('is_active'));
            }

            // Sorting
            $sortBy = $request->get('sort_by', 'order');
            $sortOrder = $request->get('sort_order', 'asc');
            $query->orderBy($sortBy, $sortOrder);

            $services = $request->has('all') ? $query->get() : $query->paginate($request->get('per_page', 15));

            return response()->json([
                'success' => true,
                'data' => $services
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to fetch services: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch services',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store new service
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'short_description' => 'required|string|max:500',
            'description' => 'required|string',
            'icon' => 'nullable|string|max:255',
            'image' => 'nullable|image|max:2048',
            'features' => 'nullable|array',
            'features.*' => 'string|max:255',
            'technologies' => 'nullable|array',
            'technologies.*' => 'string|max:100',
            'starting_price' => 'nullable|numeric|min:0',
            'price_unit' => 'nullable|string|in:hour,project,month,year',
            'is_active' => 'boolean',
        ]);

        try {
            DB::transaction(function () use (&$service, $request, $validated) {
                $validated['slug'] = Str::slug($validated['title']);
                $validated['order'] = Service::max('order') + 1;

                // Handle image upload
                if ($request->hasFile('image')) {
                    $validated['image'] = $this->mediaService->upload($request->file('image'), 'services');
                }

                $service = Service::create($validated);

                // Attach additional media if provided
                if ($request->hasFile('gallery')) {
                    foreach ($request->file('gallery') as $file) {
                        $this->mediaService->attachToModel($service, $file, 'gallery');
                    }
                }
            });

            return response()->json([
                'success' => true,
                'data' => $service->load('media'),
                'message' => 'Service created successfully'
            ], 201);
        } catch (\Exception $e) {
            Log::error('Failed to create service: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to create service',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update service
     */
    public function update(Request $request, $id)
    {
        $service = Service::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'short_description' => 'required|string|max:500',
            'description' => 'required|string',
            'icon' => 'nullable|string|max:255',
            'image' => 'nullable|image|max:2048',
            'features' => 'nullable|array',
            'features.*' => 'string|max:255',
            'technologies' => 'nullable|array',
            'technologies.*' => 'string|max:100',
            'starting_price' => 'nullable|numeric|min:0',
            'price_unit' => 'nullable|string|in:hour,project,month,year',
            'is_active' => 'boolean',
        ]);

        try {
            DB::transaction(function () use ($service, $request, $validated) {
                if ($validated['title'] !== $service->title) {
                    $validated['slug'] = Str::slug($validated['title']);
                }

                // Handle image upload
                if ($request->hasFile('image')) {
                    // Delete old image
                    if ($service->image) {
                        $this->mediaService->delete($service->image);
                    }
                    $validated['image'] = $this->mediaService->upload($request->file('image'), 'services');
                }

                $service->update($validated);
            });

            return response()->json([
                'success' => true,
                'data' => $service->fresh()->load('media'),
                'message' => 'Service updated successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to update service: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update service',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete service
     */
    public function destroy($id)
    {
        try {
            DB::transaction(function () use ($id) {
                $service = Service::findOrFail($id);

                // Delete image
                if ($service->image) {
                    $this->mediaService->delete($service->image);
                }

                // Delete associated media
                $service->media()->delete();

                $service->delete();
            });

            return response()->json([
                'success' => true,
                'message' => 'Service deleted successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to delete service: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete service',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reorder services
     */
    public function reorder(Request $request)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|exists:services,id',
            'items.*.order' => 'required|integer|min:0'
        ]);

        try {
            DB::transaction(function () use ($request) {
                foreach ($request->items as $item) {
                    Service::where('id', $item['id'])->update(['order' => $item['order']]);
                }
            });

            return response()->json([
                'success' => true,
                'message' => 'Services reordered successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to reorder services',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
