<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Education;
use App\Services\MediaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EducationController extends Controller
{
    protected MediaService $mediaService;

    public function __construct(MediaService $mediaService)
    {
        $this->middleware(['auth', 'admin']);
        $this->mediaService = $mediaService;
    }

    /**
     * Get all education records
     */
    public function index(Request $request)
    {
        try {
            $query = Education::query();

            // Search filter
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('institution', 'like', "%{$search}%")
                        ->orWhere('degree', 'like', "%{$search}%")
                        ->orWhere('field_of_study', 'like', "%{$search}%");
                });
            }

            // Current education filter
            if ($request->filled('is_current')) {
                $query->where('is_current', $request->boolean('is_current'));
            }

            // Sorting
            $sortBy = $request->get('sort_by', 'start_date');
            $sortOrder = $request->get('sort_order', 'desc');
            $query->orderBy($sortBy, $sortOrder);

            $education = $request->has('all') ? $query->get() : $query->paginate($request->get('per_page', 15));

            return response()->json([
                'success' => true,
                'data' => $education
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to fetch education records: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch education records',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store new education record
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'institution' => 'required|string|max:255',
            'degree' => 'required|string|max:255',
            'field_of_study' => 'required|string|max:255',
            'description' => 'nullable|string',
            'location' => 'nullable|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'is_current' => 'boolean',
            'grade' => 'nullable|string|max:50',
            'activities' => 'nullable|array',
            'activities.*' => 'string',
            'courses' => 'nullable|array',
            'courses.*' => 'string',
            'institution_logo' => 'nullable|image|max:1024',
            'certificate_url' => 'nullable|url',
            'is_active' => 'boolean',
        ]);

        try {
            DB::transaction(function () use (&$education, $request, $validated) {
                // If is_current is true, end_date should be null
                if ($validated['is_current'] ?? false) {
                    $validated['end_date'] = null;
                }

                // Handle institution logo upload
                if ($request->hasFile('institution_logo')) {
                    $validated['institution_logo'] = $this->mediaService->upload($request->file('institution_logo'), 'institutions');
                }

                $validated['order'] = Education::max('order') + 1;

                $education = Education::create($validated);
            });

            return response()->json([
                'success' => true,
                'data' => $education,
                'message' => 'Education record created successfully'
            ], 201);
        } catch (\Exception $e) {
            Log::error('Failed to create education record: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to create education record',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update education record
     */
    public function update(Request $request, $id)
    {
        $education = Education::findOrFail($id);

        $validated = $request->validate([
            'institution' => 'required|string|max:255',
            'degree' => 'required|string|max:255',
            'field_of_study' => 'required|string|max:255',
            'description' => 'nullable|string',
            'location' => 'nullable|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'is_current' => 'boolean',
            'grade' => 'nullable|string|max:50',
            'activities' => 'nullable|array',
            'activities.*' => 'string',
            'courses' => 'nullable|array',
            'courses.*' => 'string',
            'institution_logo' => 'nullable|image|max:1024',
            'certificate_url' => 'nullable|url',
            'is_active' => 'boolean',
        ]);

        try {
            DB::transaction(function () use ($education, $request, $validated) {
                // If is_current is true, end_date should be null
                if ($validated['is_current'] ?? false) {
                    $validated['end_date'] = null;
                }

                // Handle institution logo upload
                if ($request->hasFile('institution_logo')) {
                    // Delete old logo
                    if ($education->institution_logo) {
                        $this->mediaService->delete($education->institution_logo);
                    }
                    $validated['institution_logo'] = $this->mediaService->upload($request->file('institution_logo'), 'institutions');
                }

                $education->update($validated);
            });

            return response()->json([
                'success' => true,
                'data' => $education->fresh(),
                'message' => 'Education record updated successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to update education record: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update education record',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete education record
     */
    public function destroy($id)
    {
        try {
            DB::transaction(function () use ($id) {
                $education = Education::findOrFail($id);

                // Delete institution logo
                if ($education->institution_logo) {
                    $this->mediaService->delete($education->institution_logo);
                }

                $education->delete();
            });

            return response()->json([
                'success' => true,
                'message' => 'Education record deleted successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to delete education record: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete education record',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
