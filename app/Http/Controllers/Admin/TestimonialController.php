<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Testimonial;
use App\Models\Project;
use App\Services\MediaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TestimonialController extends Controller
{
    protected MediaService $mediaService;

    public function __construct(MediaService $mediaService)
    {
        $this->middleware(['auth', 'admin']);
        $this->mediaService = $mediaService;
    }

    /**
     * Get all testimonials
     */
    public function index(Request $request)
    {
        try {
            $query = Testimonial::with('project');

            // Search filter
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('client_name', 'like', "%{$search}%")
                        ->orWhere('client_company', 'like', "%{$search}%")
                        ->orWhere('content', 'like', "%{$search}%");
                });
            }

            // Project filter
            if ($request->filled('project_id')) {
                $query->where('project_id', $request->project_id);
            }

            // Featured filter
            if ($request->filled('is_featured')) {
                $query->where('is_featured', $request->boolean('is_featured'));
            }

            // Rating filter
            if ($request->filled('min_rating')) {
                $query->where('rating', '>=', $request->min_rating);
            }

            // Active filter
            if ($request->filled('is_active')) {
                $query->where('is_active', $request->boolean('is_active'));
            }

            // Sorting
            $sortBy = $request->get('sort_by', 'created_at');
            $sortOrder = $request->get('sort_order', 'desc');
            $query->orderBy($sortBy, $sortOrder);

            $testimonials = $request->has('all') ? $query->get() : $query->paginate($request->get('per_page', 15));

            return response()->json([
                'success' => true,
                'data' => $testimonials
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to fetch testimonials: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch testimonials',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store new testimonial
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_name' => 'required|string|max:255',
            'client_position' => 'nullable|string|max:255',
            'client_company' => 'nullable|string|max:255',
            'content' => 'required|string',
            'rating' => 'nullable|integer|min:1|max:5',
            'client_image' => 'nullable|image|max:1024',
            'client_linkedin' => 'nullable|url',
            'project_id' => 'nullable|exists:projects,id',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
        ]);

        try {
            DB::transaction(function () use (&$testimonial, $request, $validated) {
                // Handle client image upload
                if ($request->hasFile('client_image')) {
                    $validated['client_image'] = $this->mediaService->upload($request->file('client_image'), 'testimonials');
                }

                $validated['order'] = Testimonial::max('order') + 1;

                $testimonial = Testimonial::create($validated);
            });

            return response()->json([
                'success' => true,
                'data' => $testimonial->load('project'),
                'message' => 'Testimonial created successfully'
            ], 201);
        } catch (\Exception $e) {
            Log::error('Failed to create testimonial: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to create testimonial',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update testimonial
     */
    public function update(Request $request, $id)
    {
        $testimonial = Testimonial::findOrFail($id);

        $validated = $request->validate([
            'client_name' => 'required|string|max:255',
            'client_position' => 'nullable|string|max:255',
            'client_company' => 'nullable|string|max:255',
            'content' => 'required|string',
            'rating' => 'nullable|integer|min:1|max:5',
            'client_image' => 'nullable|image|max:1024',
            'client_linkedin' => 'nullable|url',
            'project_id' => 'nullable|exists:projects,id',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
        ]);

        try {
            DB::transaction(function () use ($testimonial, $request, $validated) {
                // Handle client image upload
                if ($request->hasFile('client_image')) {
                    // Delete old image
                    if ($testimonial->client_image) {
                        $this->mediaService->delete($testimonial->client_image);
                    }
                    $validated['client_image'] = $this->mediaService->upload($request->file('client_image'), 'testimonials');
                }

                $testimonial->update($validated);
            });

            return response()->json([
                'success' => true,
                'data' => $testimonial->fresh()->load('project'),
                'message' => 'Testimonial updated successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to update testimonial: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update testimonial',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete testimonial
     */
    public function destroy($id)
    {
        try {
            DB::transaction(function () use ($id) {
                $testimonial = Testimonial::findOrFail($id);

                // Delete client image
                if ($testimonial->client_image) {
                    $this->mediaService->delete($testimonial->client_image);
                }

                $testimonial->delete();
            });

            return response()->json([
                'success' => true,
                'message' => 'Testimonial deleted successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to delete testimonial: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete testimonial',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Toggle featured status
     */
    public function toggleFeatured($id)
    {
        try {
            $testimonial = Testimonial::findOrFail($id);
            $testimonial->is_featured = !$testimonial->is_featured;
            $testimonial->save();

            return response()->json([
                'success' => true,
                'data' => $testimonial,
                'message' => $testimonial->is_featured ? 'Testimonial featured' : 'Testimonial unfeatured'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to toggle featured status',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reorder testimonials
     */
    public function reorder(Request $request)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|exists:testimonials,id',
            'items.*.order' => 'required|integer|min:0'
        ]);

        try {
            DB::transaction(function () use ($request) {
                foreach ($request->items as $item) {
                    Testimonial::where('id', $item['id'])->update(['order' => $item['order']]);
                }
            });

            return response()->json([
                'success' => true,
                'message' => 'Testimonials reordered successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to reorder testimonials',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
