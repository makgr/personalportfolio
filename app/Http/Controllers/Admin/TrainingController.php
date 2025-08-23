<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Training;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Validation\Rule;

class TrainingController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    /**
     * Display a listing of training records
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Training::query();
            
            return DataTables::of($query)
                ->addColumn('status_badge', function ($training) {
                    $color = $training->status_badge_color;
                    $status = ucfirst(str_replace('_', ' ', $training->status));
                    return '<span class="badge bg-' . $color . '">' . $status . '</span>';
                })
                ->addColumn('category_badge', function ($training) {
                    $color = $training->category_badge_color;
                    $category = ucfirst(str_replace('_', ' ', $training->category));
                    return '<span class="badge bg-' . $color . '">' . $category . '</span>';
                })
                ->addColumn('type_badge', function ($training) {
                    $color = $training->type_badge_color;
                    $type = ucfirst($training->type);
                    return '<span class="badge bg-' . $color . '">' . $type . '</span>';
                })
                ->addColumn('rating_display', function ($training) {
                    if (!$training->rating) {
                        return '<span class="text-muted">Not rated</span>';
                    }
                    return $training->rating_stars . ' <small class="text-muted">(' . $training->rating . '/5)</small>';
                })
                ->addColumn('duration_display', function ($training) {
                    return $training->formatted_duration;
                })
                ->addColumn('date_range', function ($training) {
                    $start = $training->start_date ? $training->start_date->format('M Y') : 'N/A';
                    $end = $training->end_date ? $training->end_date->format('M Y') : 'Ongoing';
                    return $start . ' - ' . $end;
                })
                ->addColumn('featured_badge', function ($training) {
                    if ($training->is_featured) {
                        return '<span class="badge bg-warning"><i class="bx bx-star"></i> Featured</span>';
                    }
                    return '';
                })
                ->addColumn('action', function ($training) {
                    $actions = '<div class="btn-group" role="group">';
                    $actions .= '<a href="' . route('admin.training.show', $training) . '" class="btn btn-sm btn-outline-info" title="View"><i class="bx bx-show"></i></a>';
                    $actions .= '<a href="' . route('admin.training.edit', $training) . '" class="btn btn-sm btn-outline-primary" title="Edit"><i class="bx bx-edit"></i></a>';
                    
                    // Toggle Active/Inactive
                    if ($training->is_active) {
                        $actions .= '<button class="btn btn-sm btn-outline-warning toggle-status" data-id="' . $training->id . '" data-status="inactive" title="Deactivate"><i class="bx bx-pause"></i></button>';
                    } else {
                        $actions .= '<button class="btn btn-sm btn-outline-success toggle-status" data-id="' . $training->id . '" data-status="active" title="Activate"><i class="bx bx-play"></i></button>';
                    }
                    
                    // Toggle Featured
                    if ($training->is_featured) {
                        $actions .= '<button class="btn btn-sm btn-outline-secondary toggle-featured" data-id="' . $training->id . '" data-featured="false" title="Remove from Featured"><i class="bx bx-star"></i></button>';
                    } else {
                        $actions .= '<button class="btn btn-sm btn-outline-warning toggle-featured" data-id="' . $training->id . '" data-featured="true" title="Mark as Featured"><i class="bx bx-star"></i></button>';
                    }
                    
                    $actions .= '<button class="btn btn-sm btn-outline-danger delete-training" data-id="' . $training->id . '" title="Delete"><i class="bx bx-trash"></i></button>';
                    $actions .= '</div>';
                    
                    return $actions;
                })
                ->rawColumns(['status_badge', 'category_badge', 'type_badge', 'rating_display', 'featured_badge', 'action'])
                ->make(true);
        }

        $stats = [
            'total' => Training::count(),
            'completed' => Training::where('status', 'completed')->count(),
            'in_progress' => Training::where('status', 'in_progress')->count(),
            'featured' => Training::where('is_featured', true)->count(),
        ];

        return view('admin.training.index', compact('stats'));
    }

    /**
     * Show the form for creating a new training record
     */
    public function create()
    {
        $categories = [
            'technical' => 'Technical',
            'soft_skills' => 'Soft Skills',
            'business' => 'Business',
            'design' => 'Design',
            'other' => 'Other'
        ];

        $types = [
            'course' => 'Course',
            'certification' => 'Certification',
            'workshop' => 'Workshop',
            'bootcamp' => 'Bootcamp',
            'degree' => 'Degree'
        ];

        $statuses = [
            'completed' => 'Completed',
            'in_progress' => 'In Progress',
            'planned' => 'Planned'
        ];

        return view('admin.training.create', compact('categories', 'types', 'statuses'));
    }

    /**
     * Store a newly created training record
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'provider' => 'required|string|max:255',
            'type' => 'required|in:course,certification,workshop,bootcamp,degree',
            'category' => 'required|in:technical,soft_skills,business,design,other',
            'description' => 'nullable|string',
            'certificate_url' => 'nullable|url',
            'credential_id' => 'nullable|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'duration_hours' => 'nullable|integer|min:1|max:10000',
            'status' => 'required|in:completed,in_progress,planned',
            'score' => 'nullable|integer|min:0|max:100',
            'skills_gained' => 'nullable|string',
            'instructor' => 'nullable|string|max:255',
            'rating' => 'nullable|numeric|min:1|max:5',
            'notes' => 'nullable|string',
            'icon' => 'nullable|string|max:50',
            'color' => 'nullable|string|max:7|regex:/^#[a-fA-F0-9]{6}$/',
            'order' => 'nullable|integer|min:0',
            'is_featured' => 'boolean',
            'is_active' => 'boolean'
        ]);

        try {
            DB::beginTransaction();

            $data = $request->all();
            
            // Process skills_gained from comma-separated string to array
            if ($request->skills_gained) {
                $data['skills_gained'] = array_map('trim', explode(',', $request->skills_gained));
            }

            // Generate slug if not provided
            if (empty($data['slug'])) {
                $data['slug'] = Str::slug($data['title']);
                
                // Ensure unique slug
                $originalSlug = $data['slug'];
                $count = 1;
                while (Training::where('slug', $data['slug'])->exists()) {
                    $data['slug'] = $originalSlug . '-' . $count;
                    $count++;
                }
            }

            // Set defaults
            $data['icon'] = $data['icon'] ?: 'bx-book';
            $data['color'] = $data['color'] ?: '#007bff';
            $data['order'] = $data['order'] ?: Training::max('order') + 1;
            $data['is_featured'] = $request->boolean('is_featured');
            $data['is_active'] = $request->boolean('is_active', true);

            $training = Training::create($data);

            DB::commit();

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Training record created successfully!',
                    'redirect' => route('admin.training.index')
                ]);
            }

            return redirect()->route('admin.training.index')
                ->with('success', 'Training record created successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Training creation failed: ' . $e->getMessage());

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to create training record. Please try again.'
                ], 500);
            }

            return back()->withInput()
                ->withErrors(['error' => 'Failed to create training record. Please try again.']);
        }
    }

    /**
     * Display the specified training record
     */
    public function show(Training $training)
    {
        return view('admin.training.show', compact('training'));
    }

    /**
     * Show the form for editing the specified training record
     */
    public function edit(Training $training)
    {
        $categories = [
            'technical' => 'Technical',
            'soft_skills' => 'Soft Skills',
            'business' => 'Business',
            'design' => 'Design',
            'other' => 'Other'
        ];

        $types = [
            'course' => 'Course',
            'certification' => 'Certification',
            'workshop' => 'Workshop',
            'bootcamp' => 'Bootcamp',
            'degree' => 'Degree'
        ];

        $statuses = [
            'completed' => 'Completed',
            'in_progress' => 'In Progress',
            'planned' => 'Planned'
        ];

        // Convert skills_gained array to comma-separated string for form
        $training->skills_gained_string = is_array($training->skills_gained) 
            ? implode(', ', $training->skills_gained) 
            : '';

        return view('admin.training.edit', compact('training', 'categories', 'types', 'statuses'));
    }

    /**
     * Update the specified training record
     */
    public function update(Request $request, Training $training)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'provider' => 'required|string|max:255',
            'type' => 'required|in:course,certification,workshop,bootcamp,degree',
            'category' => 'required|in:technical,soft_skills,business,design,other',
            'description' => 'nullable|string',
            'certificate_url' => 'nullable|url',
            'credential_id' => 'nullable|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'duration_hours' => 'nullable|integer|min:1|max:10000',
            'status' => 'required|in:completed,in_progress,planned',
            'score' => 'nullable|integer|min:0|max:100',
            'skills_gained' => 'nullable|string',
            'instructor' => 'nullable|string|max:255',
            'rating' => 'nullable|numeric|min:1|max:5',
            'notes' => 'nullable|string',
            'icon' => 'nullable|string|max:50',
            'color' => 'nullable|string|max:7|regex:/^#[a-fA-F0-9]{6}$/',
            'order' => 'nullable|integer|min:0',
            'is_featured' => 'boolean',
            'is_active' => 'boolean'
        ]);

        try {
            DB::beginTransaction();

            $data = $request->all();
            
            // Process skills_gained from comma-separated string to array
            if ($request->skills_gained) {
                $data['skills_gained'] = array_map('trim', explode(',', $request->skills_gained));
            } else {
                $data['skills_gained'] = null;
            }

            // Update slug if title changed
            if ($data['title'] !== $training->title) {
                $data['slug'] = Str::slug($data['title']);
                
                // Ensure unique slug (excluding current record)
                $originalSlug = $data['slug'];
                $count = 1;
                while (Training::where('slug', $data['slug'])->where('id', '!=', $training->id)->exists()) {
                    $data['slug'] = $originalSlug . '-' . $count;
                    $count++;
                }
            }

            // Set defaults and booleans
            $data['icon'] = $data['icon'] ?: 'bx-book';
            $data['color'] = $data['color'] ?: '#007bff';
            $data['is_featured'] = $request->boolean('is_featured');
            $data['is_active'] = $request->boolean('is_active', true);

            $training->update($data);

            DB::commit();

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Training record updated successfully!'
                ]);
            }

            return redirect()->route('admin.training.index')
                ->with('success', 'Training record updated successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Training update failed: ' . $e->getMessage());

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update training record. Please try again.'
                ], 500);
            }

            return back()->withInput()
                ->withErrors(['error' => 'Failed to update training record. Please try again.']);
        }
    }

    /**
     * Remove the specified training record
     */
    public function destroy(Request $request, Training $training)
    {
        try {
            $training->delete();

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Training record deleted successfully!'
                ]);
            }

            return redirect()->route('admin.training.index')
                ->with('success', 'Training record deleted successfully!');

        } catch (\Exception $e) {
            Log::error('Training deletion failed: ' . $e->getMessage());

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to delete training record.'
                ], 500);
            }

            return back()->withErrors(['error' => 'Failed to delete training record.']);
        }
    }

    /**
     * Toggle training status (active/inactive)
     */
    public function toggleStatus(Request $request, Training $training)
    {
        try {
            $training->update([
                'is_active' => !$training->is_active
            ]);

            $status = $training->is_active ? 'activated' : 'deactivated';

            return response()->json([
                'success' => true,
                'message' => "Training record {$status} successfully!",
                'is_active' => $training->is_active
            ]);

        } catch (\Exception $e) {
            Log::error('Training status toggle failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to update training status.'
            ], 500);
        }
    }

    /**
     * Toggle featured status
     */
    public function toggleFeatured(Request $request, Training $training)
    {
        try {
            $training->update([
                'is_featured' => !$training->is_featured
            ]);

            $status = $training->is_featured ? 'marked as featured' : 'removed from featured';

            return response()->json([
                'success' => true,
                'message' => "Training record {$status} successfully!",
                'is_featured' => $training->is_featured
            ]);

        } catch (\Exception $e) {
            Log::error('Training featured toggle failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to update training featured status.'
            ], 500);
        }
    }

    /**
     * Update training order
     */
    public function updateOrder(Request $request)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|exists:training,id',
            'items.*.order' => 'required|integer|min:0'
        ]);

        try {
            DB::beginTransaction();

            foreach ($request->items as $item) {
                Training::where('id', $item['id'])->update(['order' => $item['order']]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Training order updated successfully!'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Training order update failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to update training order.'
            ], 500);
        }
    }
}