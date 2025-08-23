<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Testimonial;
use App\Models\Project;
use App\Services\MediaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

class TestimonialController extends Controller
{
    protected MediaService $mediaService;

    public function __construct(MediaService $mediaService)
    {
        $this->middleware(['auth', 'admin']);
        $this->mediaService = $mediaService;
    }

    /**
     * Display a listing of testimonials
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Testimonial::with('project');
            
            return DataTables::of($query)
                ->addColumn('client_info', function ($testimonial) {
                    $html = '<div class="d-flex align-items-center">';
                    if ($testimonial->client_image) {
                        $html .= '<img src="' . $testimonial->client_image . '" class="rounded-circle me-2" style="width: 32px; height: 32px; object-fit: cover;" alt="' . $testimonial->client_name . '">';
                    } else {
                        $html .= '<div class="avatar-initial rounded-circle bg-label-primary me-2 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; font-size: 12px;">' . substr($testimonial->client_name, 0, 1) . '</div>';
                    }
                    $html .= '<div>';
                    $html .= '<strong>' . $testimonial->client_name . '</strong>';
                    if ($testimonial->client_linkedin) {
                        $html .= '<br><a href="' . $testimonial->client_linkedin . '" target="_blank" class="text-primary small"><i class="bx bxl-linkedin"></i> LinkedIn</a>';
                    }
                    $html .= '</div></div>';
                    return $html;
                })
                ->addColumn('company_position', function ($testimonial) {
                    $html = '';
                    if ($testimonial->client_company) {
                        $html .= '<strong>' . $testimonial->client_company . '</strong>';
                    }
                    if ($testimonial->client_position) {
                        $html .= ($html ? '<br>' : '') . $testimonial->client_position;
                    }
                    return $html ?: '<span class="text-muted">-</span>';
                })
                ->addColumn('content_preview', function ($testimonial) {
                    return '<div style="max-width: 200px;">' . Str::limit($testimonial->content, 100) . '</div>';
                })
                ->addColumn('rating_display', function ($testimonial) {
                    if (!$testimonial->rating) {
                        return '<span class="text-muted">No rating</span>';
                    }
                    return '<div class="rating">' . str_repeat('★', $testimonial->rating) . str_repeat('☆', 5 - $testimonial->rating) . '<br><small class="text-muted">(' . $testimonial->rating . '/5)</small></div>';
                })
                ->addColumn('project_info', function ($testimonial) {
                    if ($testimonial->project) {
                        return '<a href="' . route('admin.projects.show', $testimonial->project->id) . '" class="text-primary">' . $testimonial->project->title . '</a>';
                    }
                    return '<span class="text-muted">General</span>';
                })
                ->addColumn('status_badges', function ($testimonial) {
                    $badges = '<span class="badge bg-label-' . ($testimonial->is_active ? 'success' : 'secondary') . '">' . ($testimonial->is_active ? 'Active' : 'Inactive') . '</span>';
                    if ($testimonial->is_featured) {
                        $badges .= '<br><span class="badge bg-label-warning">Featured</span>';
                    }
                    return $badges;
                })
                ->addColumn('actions', function ($testimonial) {
                    $actions = '<div class="btn-group" role="group">';
                    $actions .= '<a href="' . route('admin.testimonials.show', $testimonial) . '" class="btn btn-sm btn-outline-info" title="View"><i class="bx bx-show"></i></a>';
                    $actions .= '<a href="' . route('admin.testimonials.edit', $testimonial) . '" class="btn btn-sm btn-outline-primary" title="Edit"><i class="bx bx-edit"></i></a>';
                    
                    if ($testimonial->is_featured) {
                        $actions .= '<button class="btn btn-sm btn-outline-secondary toggle-featured" data-id="' . $testimonial->id . '" title="Remove from Featured"><i class="bx bx-star"></i></button>';
                    } else {
                        $actions .= '<button class="btn btn-sm btn-outline-warning toggle-featured" data-id="' . $testimonial->id . '" title="Mark as Featured"><i class="bx bx-star"></i></button>';
                    }
                    
                    $actions .= '<button class="btn btn-sm btn-outline-danger delete-testimonial" data-id="' . $testimonial->id . '" title="Delete"><i class="bx bx-trash"></i></button>';
                    $actions .= '</div>';
                    
                    return $actions;
                })
                ->editColumn('created_at', function ($testimonial) {
                    return $testimonial->created_at->format('M d, Y');
                })
                ->rawColumns(['client_info', 'company_position', 'content_preview', 'rating_display', 'project_info', 'status_badges', 'actions'])
                ->make(true);
        }
        
        $stats = [
            'total' => Testimonial::count(),
            'featured' => Testimonial::where('is_featured', true)->count(),
            'active' => Testimonial::where('is_active', true)->count(),
            'high_rated' => Testimonial::where('rating', '>=', 4)->count(),
        ];
        
        return view('admin.testimonials.index', compact('stats'));
    }

    /**
     * Show the form for creating a new testimonial
     */
    public function create()
    {
        return view('admin.testimonials.create');
    }

    /**
     * Display the specified testimonial
     */
    public function show(Testimonial $testimonial)
    {
        $testimonial->load('project');
        return view('admin.testimonials.show', compact('testimonial'));
    }

    /**
     * Show the form for editing the specified testimonial
     */
    public function edit(Testimonial $testimonial)
    {
        $testimonial->load('project');
        return view('admin.testimonials.edit', compact('testimonial'));
    }


    /**
     * Store new testimonial
     */
    public function store(Request $request)
    {
        try {
            Log::info('Testimonial store method called', ['request_data' => $request->all()]);
            
            $validated = $request->validate([
                'client_name' => 'required|string|max:255',
                'client_position' => 'nullable|string|max:255',
                'client_company' => 'nullable|string|max:255',
                'content' => 'required|string',
                'rating' => 'nullable|integer|min:1|max:5',
                'client_image' => 'nullable|image|max:1024',
                'client_linkedin' => 'nullable|url',
                'project_id' => 'nullable|exists:projects,id',
            ]);

            // Handle checkbox values manually
            $validated['is_featured'] = $request->has('is_featured');
            $validated['is_active'] = $request->has('is_active');
            
            Log::info('Validation passed', ['validated_data' => $validated]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation failed', ['errors' => $e->errors()]);
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error in testimonial store method', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage()
            ], 500);
        }

        try {
            DB::transaction(function () use (&$testimonial, $request, $validated) {
                // Temporarily skip image upload to test basic functionality
                if ($request->hasFile('client_image')) {
                    // Skip image upload for now
                    Log::info('Image upload skipped for testing');
                    unset($validated['client_image']);
                }

                $validated['order'] = Testimonial::max('order') + 1;
                
                Log::info('About to create testimonial', ['validated_data' => $validated]);

                $testimonial = Testimonial::create($validated);
                
                Log::info('Testimonial created successfully', ['testimonial_id' => $testimonial->id]);
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
        ]);

        // Handle checkbox values manually
        $validated['is_featured'] = $request->has('is_featured');
        $validated['is_active'] = $request->has('is_active');

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
