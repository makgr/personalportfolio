<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Experience;
use App\Services\MediaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

class ExperienceController extends Controller
{
    protected MediaService $mediaService;

    public function __construct(MediaService $mediaService)
    {
        $this->middleware(['auth', 'admin']);
        $this->mediaService = $mediaService;
    }

    /**
     * Display a listing of experiences
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Experience::query();
            
            return DataTables::of($query)
                ->addColumn('company_logo_display', function ($experience) {
                    if ($experience->company_logo) {
                        return '<img src="' . asset($experience->company_logo) . '" alt="' . $experience->company_name . '" style="width: 40px; height: 40px; object-fit: contain;" class="rounded">';
                    }
                    return '<div class="bg-light rounded d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;"><i class="bx bx-buildings text-muted"></i></div>';
                })
                ->addColumn('employment_badge', function ($experience) {
                    $colors = [
                        'full-time' => 'success',
                        'part-time' => 'info',
                        'contract' => 'warning',
                        'freelance' => 'primary',
                        'internship' => 'secondary'
                    ];
                    $color = $colors[$experience->employment_type] ?? 'secondary';
                    return '<span class="badge bg-' . $color . '">' . ucfirst(str_replace('-', ' ', $experience->employment_type ?? 'Not specified')) . '</span>';
                })
                ->addColumn('duration_display', function ($experience) {
                    return '<span class="badge bg-info">' . $experience->duration . '</span>';
                })
                ->addColumn('current_badge', function ($experience) {
                    return $experience->is_current 
                        ? '<span class="badge bg-success"><i class="bx bx-time"></i> Current</span>'
                        : '<span class="badge bg-secondary">Past</span>';
                })
                ->addColumn('status_badge', function ($experience) {
                    return $experience->is_active 
                        ? '<span class="badge bg-success">Active</span>'
                        : '<span class="badge bg-secondary">Inactive</span>';
                })
                ->addColumn('technologies_count', function ($experience) {
                    $count = is_array($experience->technologies) ? count($experience->technologies) : 0;
                    return '<span class="badge bg-info">' . $count . ' tech' . ($count != 1 ? 's' : '') . '</span>';
                })
                ->addColumn('actions', function ($experience) {
                    return view('admin.experiences.partials.actions', compact('experience'));
                })
                ->editColumn('start_date', function ($experience) {
                    return $experience->start_date->format('M Y');
                })
                ->editColumn('end_date', function ($experience) {
                    return $experience->is_current ? 'Present' : ($experience->end_date ? $experience->end_date->format('M Y') : 'N/A');
                })
                ->editColumn('description', function ($experience) {
                    return Str::limit($experience->description, 50);
                })
                ->rawColumns(['company_logo_display', 'employment_badge', 'duration_display', 'current_badge', 'status_badge', 'technologies_count', 'actions'])
                ->make(true);
        }
        
        return view('admin.experiences.index');
    }

    /**
     * Show the form for creating a new experience
     */
    public function create()
    {
        $employmentTypes = [
            'full-time' => 'Full Time',
            'part-time' => 'Part Time',
            'contract' => 'Contract',
            'freelance' => 'Freelance',
            'internship' => 'Internship'
        ];
        
        return view('admin.experiences.create', compact('employmentTypes'));
    }

    /**
     * Display the specified experience
     */
    public function show(Request $request, $id)
    {
        try {
            $experience = Experience::findOrFail($id);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'data' => $experience
                ]);
            }

            return view('admin.experiences.show', compact('experience'));
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Experience not found'
                ], 404);
            }

            return back()->with('error', 'Experience not found');
        }
    }

    /**
     * Show the form for editing the specified experience
     */
    public function edit($id)
    {
        $experience = Experience::findOrFail($id);
        
        $employmentTypes = [
            'full-time' => 'Full Time',
            'part-time' => 'Part Time',
            'contract' => 'Contract',
            'freelance' => 'Freelance',
            'internship' => 'Internship'
        ];
        
        return view('admin.experiences.edit', compact('experience', 'employmentTypes'));
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
            'responsibilities' => 'nullable|string',
            'achievements' => 'nullable|string',
            'technologies' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'is_current' => 'boolean',
            'company_logo' => 'nullable|image|max:1024',
            'company_url' => 'nullable|url',
            'is_active' => 'boolean',
        ]);

        try {
            DB::transaction(function () use (&$experience, $request, $validated) {
                // Handle checkboxes
                $validated['is_current'] = $request->has('is_current');
                $validated['is_active'] = $request->has('is_active');

                // If is_current is true, end_date should be null
                if ($validated['is_current']) {
                    $validated['end_date'] = null;

                    // Set all other experiences as not current
                    Experience::where('is_current', true)->update(['is_current' => false]);
                }

                // Handle arrays from form input (comma-separated strings)
                if (isset($validated['responsibilities']) && is_string($validated['responsibilities'])) {
                    $validated['responsibilities'] = array_filter(array_map('trim', explode("\n", $validated['responsibilities'])));
                }
                if (isset($validated['achievements']) && is_string($validated['achievements'])) {
                    $validated['achievements'] = array_filter(array_map('trim', explode("\n", $validated['achievements'])));
                }
                if (isset($validated['technologies']) && is_string($validated['technologies'])) {
                    $validated['technologies'] = array_filter(array_map('trim', explode(',', $validated['technologies'])));
                }

                // Handle company logo upload
                if ($request->hasFile('company_logo')) {
                    $validated['company_logo'] = $this->mediaService->upload($request->file('company_logo'), 'companies');
                }

                $validated['order'] = Experience::max('order') + 1;

                $experience = Experience::create($validated);
            });

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'data' => $experience,
                    'message' => 'Experience created successfully'
                ], 201);
            }

            return redirect()
                ->route('experiences.index')
                ->with('success', 'Experience created successfully');
        } catch (\Exception $e) {
            Log::error('Failed to create experience: ' . $e->getMessage());
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to create experience',
                    'error' => $e->getMessage()
                ], 500);
            }

            return back()
                ->withInput()
                ->with('error', 'Failed to create experience: ' . $e->getMessage());
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
            'responsibilities' => 'nullable|string',
            'achievements' => 'nullable|string',
            'technologies' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'is_current' => 'boolean',
            'company_logo' => 'nullable|image|max:1024',
            'company_url' => 'nullable|url',
            'is_active' => 'boolean',
        ]);

        try {
            DB::transaction(function () use ($experience, $request, $validated) {
                // Handle checkboxes
                $validated['is_current'] = $request->has('is_current');
                $validated['is_active'] = $request->has('is_active');

                // If is_current is true, end_date should be null
                if ($validated['is_current']) {
                    $validated['end_date'] = null;

                    // Set all other experiences as not current
                    Experience::where('id', '!=', $experience->id)
                        ->where('is_current', true)
                        ->update(['is_current' => false]);
                }

                // Handle arrays from form input (comma-separated strings)
                if (isset($validated['responsibilities']) && is_string($validated['responsibilities'])) {
                    $validated['responsibilities'] = array_filter(array_map('trim', explode("\n", $validated['responsibilities'])));
                }
                if (isset($validated['achievements']) && is_string($validated['achievements'])) {
                    $validated['achievements'] = array_filter(array_map('trim', explode("\n", $validated['achievements'])));
                }
                if (isset($validated['technologies']) && is_string($validated['technologies'])) {
                    $validated['technologies'] = array_filter(array_map('trim', explode(',', $validated['technologies'])));
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

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'data' => $experience->fresh(),
                    'message' => 'Experience updated successfully'
                ]);
            }

            return redirect()
                ->route('experiences.edit', $id)
                ->with('success', 'Experience updated successfully');
        } catch (\Exception $e) {
            Log::error('Failed to update experience: ' . $e->getMessage());
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update experience',
                    'error' => $e->getMessage()
                ], 500);
            }

            return back()
                ->withInput()
                ->with('error', 'Failed to update experience: ' . $e->getMessage());
        }
    }

    /**
     * Delete experience
     */
    public function destroy(Request $request, $experience)
    {
        try {
            $experienceId = (int) $experience;
            $experienceRecord = Experience::findOrFail($experienceId);

            DB::transaction(function () use ($experienceRecord) {
                // Delete company logo
                if ($experienceRecord->company_logo) {
                    try {
                        $this->mediaService->delete($experienceRecord->company_logo);
                    } catch (\Exception $e) {
                        Log::warning('Failed to delete company logo: ' . $e->getMessage());
                    }
                }

                $experienceRecord->delete();
            });

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Experience deleted successfully'
                ]);
            }

            return redirect()
                ->route('experiences.index')
                ->with('success', 'Experience deleted successfully');
        } catch (\Exception $e) {
            Log::error('Failed to delete experience: ' . $e->getMessage());
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to delete experience',
                    'error' => $e->getMessage()
                ], 500);
            }

            return back()
                ->with('error', 'Failed to delete experience: ' . $e->getMessage());
        }
    }

    /**
     * Toggle experience status
     */
    public function toggleStatus($id)
    {
        try {
            $experience = Experience::findOrFail($id);
            $experience->is_active = !$experience->is_active;
            $experience->save();

            return response()->json([
                'success' => true,
                'data' => $experience,
                'message' => $experience->is_active ? 'Experience activated' : 'Experience deactivated'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to toggle experience status',
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
