<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Experience;
use App\Services\MediaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ExperienceController extends Controller
{
    protected MediaService $mediaService;

    public function __construct(MediaService $mediaService)
    {
        $this->middleware(['auth', 'admin']);
        $this->mediaService = $mediaService;
    }

    /**
     * Get all experiences
     */
    public function index(Request $request)
    {
        try {
            $query = Experience::query();

            // Search filter
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('company_name', 'like', "%{$search}%")
                        ->orWhere('position', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            }

            // Employment type filter
            if ($request->filled('employment_type')) {
                $query->where('employment_type', $request->employment_type);
            }

            // Current job filter
            if ($request->filled('is_current')) {
                $query->where('is_current', $request->boolean('is_current'));
            }

            // Sorting
            $sortBy = $request->get('sort_by', 'start_date');
            $sortOrder = $request->get('sort_order', 'desc');
            $query->orderBy($sortBy, $sortOrder);

            $experiences = $request->has('all') ? $query->get() : $query->paginate($request->get('per_page', 15));

            return response()->json([
                'success' => true,
                'data' => $experiences
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to fetch experiences: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch experiences',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store new experience
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'employment_type' => 'nullable|string|in:full-time,part-time,contract,freelance,internship',
            'location' => 'nullable|string|max:255',
            'description' => 'required|string',
            'responsibilities' => 'nullable|array',
            'responsibilities.*' => 'string',
            'achievements' => 'nullable|array',
            'achievements.*' => 'string',
            'technologies' => 'nullable|array',
            'technologies.*' => 'string|max:50',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'is_current' => 'boolean',
            'company_logo' => 'nullable|image|max:1024',
            'company_url' => 'nullable|url',
            'is_active' => 'boolean',
        ]);

        try {
            DB::transaction(function () use (&$experience, $request, $validated) {
                // If is_current is true, end_date should be null
                if ($validated['is_current'] ?? false) {
                    $validated['end_date'] = null;

                    // Set all other experiences as not current
                    Experience::where('is_current', true)->update(['is_current' => false]);
                }

                // Handle company logo upload
                if ($request->hasFile('company_logo')) {
                    $validated['company_logo'] = $this->mediaService->upload($request->file('company_logo'), 'companies');
                }

                $validated['order'] = Experience::max('order') + 1;

                $experience = Experience::create($validated);
            });

            return response()->json([
                'success' => true,
                'data' => $experience,
                'message' => 'Experience created successfully'
            ], 201);
        } catch (\Exception $e) {
            Log::error('Failed to create experience: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to create experience',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update experience
     */
    public function update(Request $request, $id)
    {
        $experience = Experience::findOrFail($id);

        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'employment_type' => 'nullable|string|in:full-time,part-time,contract,freelance,internship',
            'location' => 'nullable|string|max:255',
            'description' => 'required|string',
            'responsibilities' => 'nullable|array',
            'responsibilities.*' => 'string',
            'achievements' => 'nullable|array',
            'achievements.*' => 'string',
            'technologies' => 'nullable|array',
            'technologies.*' => 'string|max:50',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'is_current' => 'boolean',
            'company_logo' => 'nullable|image|max:1024',
            'company_url' => 'nullable|url',
            'is_active' => 'boolean',
        ]);

        try {
            DB::transaction(function () use ($experience, $request, $validated) {
                // If is_current is true, end_date should be null
                if ($validated['is_current'] ?? false) {
                    $validated['end_date'] = null;

                    // Set all other experiences as not current
                    Experience::where('id', '!=', $experience->id)
                        ->where('is_current', true)
                        ->update(['is_current' => false]);
                }

                // Handle company logo upload
                if ($request->hasFile('company_logo')) {
                    // Delete old logo
                    if ($experience->company_logo) {
                        $this->mediaService->delete($experience->company_logo);
                    }
                    $validated['company_logo'] = $this->mediaService->upload($request->file('company_logo'), 'companies');
                }

                $experience->update($validated);
            });

            return response()->json([
                'success' => true,
                'data' => $experience->fresh(),
                'message' => 'Experience updated successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to update experience: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update experience',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete experience
     */
    public function destroy($id)
    {
        try {
            DB::transaction(function () use ($id) {
                $experience = Experience::findOrFail($id);

                // Delete company logo
                if ($experience->company_logo) {
                    $this->mediaService->delete($experience->company_logo);
                }

                $experience->delete();
            });

            return response()->json([
                'success' => true,
                'message' => 'Experience deleted successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to delete experience: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete experience',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reorder experiences
     */
    public function reorder(Request $request)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|exists:experiences,id',
            'items.*.order' => 'required|integer|min:0'
        ]);

        try {
            DB::transaction(function () use ($request) {
                foreach ($request->items as $item) {
                    Experience::where('id', $item['id'])->update(['order' => $item['order']]);
                }
            });

            return response()->json([
                'success' => true,
                'message' => 'Experiences reordered successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to reorder experiences',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
