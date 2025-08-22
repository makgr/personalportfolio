<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Services\MediaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

class ServiceController extends Controller
{
    protected MediaService $mediaService;

    public function __construct(MediaService $mediaService)
    {
        $this->middleware(['auth', 'admin']);
        $this->mediaService = $mediaService;
    }

    /**
     * Display a listing of services
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Service::query();
            
            return DataTables::of($query)
                ->addColumn('price_display', function ($service) {
                    if (!$service->starting_price) {
                        return '<span class="badge bg-info">Contact for pricing</span>';
                    }
                    
                    $price = '$' . number_format($service->starting_price, 2);
                    if ($service->price_unit) {
                        $price .= ' / ' . ucfirst($service->price_unit);
                    }
                    
                    return '<span class="badge bg-primary">' . $price . '</span>';
                })
                ->addColumn('status_badge', function ($service) {
                    return $service->is_active 
                        ? '<span class="badge bg-success">Active</span>'
                        : '<span class="badge bg-secondary">Inactive</span>';
                })
                ->addColumn('icon_display', function ($service) {
                    if ($service->icon) {
                        // Handle Boxicons (bx-*)
                        if (str_starts_with($service->icon, 'bx-')) {
                            return '<i class="bx ' . $service->icon . ' text-primary" style="font-size: 1.5rem;"></i>';
                        }
                        // Handle Font Awesome (fa*, fab, fas, far, fal, fad)
                        elseif (preg_match('/^(fa[a-z]?-|fa\s)/', $service->icon)) {
                            // If it starts with fa- but no prefix, assume fas (solid)
                            if (str_starts_with($service->icon, 'fa-')) {
                                return '<i class="fas ' . $service->icon . ' text-primary" style="font-size: 1.5rem;"></i>';
                            } else {
                                return '<i class="' . $service->icon . ' text-primary" style="font-size: 1.5rem;"></i>';
                            }
                        }
                        // Handle any other icon class
                        else {
                            return '<i class="' . $service->icon . ' text-primary" style="font-size: 1.5rem;"></i>';
                        }
                    }
                    return '<i class="bx bx-briefcase text-muted" style="font-size: 1.5rem;"></i>';
                })
                ->addColumn('features_count', function ($service) {
                    $count = is_array($service->features) ? count($service->features) : 0;
                    return '<span class="badge bg-info">' . $count . ' features</span>';
                })
                ->addColumn('technologies_count', function ($service) {
                    $count = is_array($service->technologies) ? count($service->technologies) : 0;
                    return '<span class="badge bg-warning">' . $count . ' techs</span>';
                })
                ->addColumn('actions', function ($service) {
                    return view('admin.services.partials.actions', compact('service'));
                })
                ->editColumn('created_at', function ($service) {
                    return $service->created_at->format('M d, Y');
                })
                ->editColumn('short_description', function ($service) {
                    return Str::limit($service->short_description, 50);
                })
                ->rawColumns(['price_display', 'status_badge', 'icon_display', 'features_count', 'technologies_count', 'actions'])
                ->make(true);
        }
        
        return view('admin.services.index');
    }

    /**
     * Show the form for creating a new service
     */
    public function create()
    {
        $priceUnits = [
            'hour' => 'Hour',
            'project' => 'Project', 
            'month' => 'Month',
            'year' => 'Year'
        ];
        
        return view('admin.services.create', compact('priceUnits'));
    }

    /**
     * Display the specified service
     */
    public function show(Request $request, $id)
    {
        try {
            $service = Service::with('media')->findOrFail($id);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'data' => $service
                ]);
            }

            return view('admin.services.show', compact('service'));
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Service not found'
                ], 404);
            }

            return back()->with('error', 'Service not found');
        }
    }

    /**
     * Show the form for editing the specified service
     */
    public function edit($id)
    {
        $service = Service::with('media')->findOrFail($id);
        
        $priceUnits = [
            'hour' => 'Hour',
            'project' => 'Project', 
            'month' => 'Month',
            'year' => 'Year'
        ];
        
        return view('admin.services.edit', compact('service', 'priceUnits'));
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
            'features' => 'nullable|string',
            'technologies' => 'nullable|string',
            'starting_price' => 'nullable|numeric|min:0',
            'price_unit' => 'nullable|string|in:hour,project,month,year',
            'is_active' => 'boolean',
        ]);

        try {
            DB::transaction(function () use (&$service, $request, $validated) {
                $validated['slug'] = Str::slug($validated['title']);
                $validated['order'] = Service::max('order') + 1;

                // Handle checkboxes
                $validated['is_active'] = $request->has('is_active');

                // Handle arrays from form input (comma-separated strings)
                if (isset($validated['features']) && is_string($validated['features'])) {
                    $validated['features'] = array_filter(array_map('trim', explode(',', $validated['features'])));
                }
                if (isset($validated['technologies']) && is_string($validated['technologies'])) {
                    $validated['technologies'] = array_filter(array_map('trim', explode(',', $validated['technologies'])));
                }

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

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'data' => $service->load('media'),
                    'message' => 'Service created successfully'
                ], 201);
            }

            return redirect()
                ->route('services.index')
                ->with('success', 'Service created successfully');
        } catch (\Exception $e) {
            Log::error('Failed to create service: ' . $e->getMessage());
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to create service',
                    'error' => $e->getMessage()
                ], 500);
            }

            return back()
                ->withInput()
                ->with('error', 'Failed to create service: ' . $e->getMessage());
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
            'features' => 'nullable|string',
            'technologies' => 'nullable|string',
            'starting_price' => 'nullable|numeric|min:0',
            'price_unit' => 'nullable|string|in:hour,project,month,year',
            'is_active' => 'boolean',
        ]);

        try {
            DB::transaction(function () use ($service, $request, $validated) {
                if ($validated['title'] !== $service->title) {
                    $validated['slug'] = Str::slug($validated['title']);
                }

                // Handle checkboxes
                $validated['is_active'] = $request->has('is_active');

                // Handle arrays from form input (comma-separated strings)
                if (isset($validated['features']) && is_string($validated['features'])) {
                    $validated['features'] = array_filter(array_map('trim', explode(',', $validated['features'])));
                }
                if (isset($validated['technologies']) && is_string($validated['technologies'])) {
                    $validated['technologies'] = array_filter(array_map('trim', explode(',', $validated['technologies'])));
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

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'data' => $service->fresh()->load('media'),
                    'message' => 'Service updated successfully'
                ]);
            }

            return redirect()
                ->route('services.edit', $id)
                ->with('success', 'Service updated successfully');
        } catch (\Exception $e) {
            Log::error('Failed to update service: ' . $e->getMessage());
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update service',
                    'error' => $e->getMessage()
                ], 500);
            }

            return back()
                ->withInput()
                ->with('error', 'Failed to update service: ' . $e->getMessage());
        }
    }

    /**
     * Delete service
     */
    public function destroy(Request $request, $service)
    {
        try {
            $serviceId = (int) $service;
            $serviceRecord = Service::findOrFail($serviceId);

            DB::transaction(function () use ($serviceRecord) {
                // Delete image
                if ($serviceRecord->image) {
                    try {
                        $this->mediaService->delete($serviceRecord->image);
                    } catch (\Exception $e) {
                        Log::warning('Failed to delete service image: ' . $e->getMessage());
                    }
                }

                // Delete associated media
                $serviceRecord->media()->delete();

                $serviceRecord->delete();
            });

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Service deleted successfully'
                ]);
            }

            return redirect()
                ->route('services.index')
                ->with('success', 'Service deleted successfully');
        } catch (\Exception $e) {
            Log::error('Failed to delete service: ' . $e->getMessage());
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to delete service',
                    'error' => $e->getMessage()
                ], 500);
            }

            return back()
                ->with('error', 'Failed to delete service: ' . $e->getMessage());
        }
    }

    /**
     * Toggle service status
     */
    public function toggleStatus($id)
    {
        try {
            $service = Service::findOrFail($id);
            $service->is_active = !$service->is_active;
            $service->save();

            return response()->json([
                'success' => true,
                'data' => $service,
                'message' => $service->is_active ? 'Service activated' : 'Service deactivated'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to toggle service status',
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
