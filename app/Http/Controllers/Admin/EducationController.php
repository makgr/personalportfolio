<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Education;
use App\Services\MediaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

class EducationController extends Controller
{
    protected MediaService $mediaService;

    public function __construct(MediaService $mediaService)
    {
        $this->middleware(['auth', 'admin']);
        $this->mediaService = $mediaService;
    }

    /**
     * Display a listing of education records
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Education::query();
            
            return DataTables::of($query)
                ->addColumn('institution_info', function ($education) {
                    $html = '<div>';
                    if ($education->institution_logo) {
                        $html .= '<img src="' . asset($education->institution_logo) . '" alt="Logo" class="me-2" style="width: 40px; height: 40px; object-fit: cover; border-radius: 8px;">';
                    }
                    $html .= '<div class="d-inline-block">';
                    $html .= '<div class="fw-bold">' . e($education->institution) . '</div>';
                    if ($education->location) {
                        $html .= '<div class="text-muted small"><i class="bx bx-map me-1"></i>' . e($education->location) . '</div>';
                    }
                    $html .= '</div></div>';
                    return $html;
                })
                ->addColumn('education_details', function ($education) {
                    $html = '<div>';
                    $html .= '<div class="fw-bold">' . e($education->degree) . '</div>';
                    $html .= '<div class="text-muted">' . e($education->field_of_study) . '</div>';
                    if ($education->grade) {
                        $html .= '<div class="text-success small"><i class="bx bx-trophy me-1"></i>' . e($education->grade) . '</div>';
                    }
                    $html .= '</div>';
                    return $html;
                })
                ->addColumn('duration', function ($education) {
                    $start = $education->start_date->format('M Y');
                    $end = $education->is_current ? 'Present' : ($education->end_date ? $education->end_date->format('M Y') : 'Present');
                    return $start . ' - ' . $end;
                })
                ->addColumn('status_badge', function ($education) {
                    $statusColor = $education->is_current ? 'success' : 'info';
                    $statusIcon = $education->is_current ? 'bx-time' : 'bx-check-circle';
                    $statusText = $education->is_current ? 'Current' : 'Completed';
                    
                    $html = '<span class="badge bg-' . $statusColor . '"><i class="bx ' . $statusIcon . ' me-1"></i>' . $statusText . '</span>';
                    
                    if (!$education->is_active) {
                        $html .= '<br><span class="badge bg-secondary mt-1">Inactive</span>';
                    }
                    
                    return $html;
                })
                ->addColumn('activities_courses', function ($education) {
                    $html = '';
                    if ($education->activities && count($education->activities) > 0) {
                        $html .= '<div class="mb-1"><small class="text-muted">Activities:</small><br>';
                        foreach (array_slice($education->activities, 0, 2) as $activity) {
                            $html .= '<span class="badge bg-info me-1">' . e($activity) . '</span>';
                        }
                        if (count($education->activities) > 2) {
                            $html .= '<span class="badge bg-light text-dark">+' . (count($education->activities) - 2) . '</span>';
                        }
                        $html .= '</div>';
                    }
                    if ($education->courses && count($education->courses) > 0) {
                        $html .= '<div><small class="text-muted">Courses:</small><br>';
                        foreach (array_slice($education->courses, 0, 2) as $course) {
                            $html .= '<span class="badge bg-warning me-1">' . e($course) . '</span>';
                        }
                        if (count($education->courses) > 2) {
                            $html .= '<span class="badge bg-light text-dark">+' . (count($education->courses) - 2) . '</span>';
                        }
                        $html .= '</div>';
                    }
                    return $html ?: '<span class="text-muted">-</span>';
                })
                ->addColumn('actions', function ($education) {
                    return view('admin.education.partials.actions', compact('education'));
                })
                ->editColumn('description', function ($education) {
                    return $education->description ? Str::limit($education->description, 50) : '-';
                })
                ->rawColumns(['institution_info', 'education_details', 'status_badge', 'activities_courses', 'actions'])
                ->make(true);
        }

        return view('admin.education.index');
    }

    /**
     * Show the form for creating a new education record
     */
    public function create()
    {
        return view('admin.education.create');
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
            'grade' => 'nullable|string|max:50',
            'activities' => 'nullable|string',
            'courses' => 'nullable|string',
            'institution_logo' => 'nullable|image|max:1024',
            'certificate_url' => 'nullable|url',
        ]);

        // Convert string inputs to arrays
        if (!empty($validated['activities'])) {
            $validated['activities'] = array_filter(array_map('trim', explode(',', $validated['activities'])));
        }
        if (!empty($validated['courses'])) {
            $validated['courses'] = array_filter(array_map('trim', explode(',', $validated['courses'])));
        }

        // Handle checkbox values
        $validated['is_current'] = $request->has('is_current') ? true : false;
        $validated['is_active'] = $request->has('is_active') ? true : false;

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

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'data' => $education,
                    'message' => 'Education record created successfully'
                ]);
            }

            return redirect()->route('education.index')->with('success', 'Education record created successfully');
        } catch (\Exception $e) {
            Log::error('Failed to create education record: ' . $e->getMessage());
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to create education record',
                    'error' => $e->getMessage()
                ], 500);
            }

            return back()->withInput()->with('error', 'Failed to create education record: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified education record
     */
    public function show($id)
    {
        $education = Education::findOrFail($id);
        return view('admin.education.show', compact('education'));
    }

    /**
     * Show the form for editing the specified education record
     */
    public function edit($id)
    {
        $education = Education::findOrFail($id);
        return view('admin.education.edit', compact('education'));
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
            'grade' => 'nullable|string|max:50',
            'activities' => 'nullable|string',
            'courses' => 'nullable|string',
            'institution_logo' => 'nullable|image|max:1024',
            'certificate_url' => 'nullable|url',
        ]);

        // Convert string inputs to arrays
        if (!empty($validated['activities'])) {
            $validated['activities'] = array_filter(array_map('trim', explode(',', $validated['activities'])));
        }
        if (!empty($validated['courses'])) {
            $validated['courses'] = array_filter(array_map('trim', explode(',', $validated['courses'])));
        }

        // Handle checkbox values
        $validated['is_current'] = $request->has('is_current') ? true : false;
        $validated['is_active'] = $request->has('is_active') ? true : false;

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

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'data' => $education->fresh(),
                    'message' => 'Education record updated successfully'
                ]);
            }

            return redirect()->route('education.index')->with('success', 'Education record updated successfully');
        } catch (\Exception $e) {
            Log::error('Failed to update education record: ' . $e->getMessage());
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update education record',
                    'error' => $e->getMessage()
                ], 500);
            }

            return back()->withInput()->with('error', 'Failed to update education record: ' . $e->getMessage());
        }
    }

    /**
     * Delete education record
     */
    public function destroy(Request $request, $id)
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

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Education record deleted successfully'
                ]);
            }

            return redirect()->route('education.index')->with('success', 'Education record deleted successfully');
        } catch (\Exception $e) {
            Log::error('Failed to delete education record: ' . $e->getMessage());
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to delete education record',
                    'error' => $e->getMessage()
                ], 500);
            }

            return back()->with('error', 'Failed to delete education record: ' . $e->getMessage());
        }
    }

    /**
     * Toggle education status
     */
    public function toggleStatus($id)
    {
        try {
            $education = Education::findOrFail($id);
            $education->update(['is_active' => !$education->is_active]);

            return response()->json([
                'success' => true,
                'data' => $education,
                'message' => 'Education status updated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update education status',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reorder education records
     */
    public function reorder(Request $request)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|exists:education,id',
            'items.*.order' => 'required|integer'
        ]);

        try {
            DB::transaction(function () use ($request) {
                foreach ($request->items as $item) {
                    Education::where('id', $item['id'])->update(['order' => $item['order']]);
                }
            });

            return response()->json([
                'success' => true,
                'message' => 'Education records reordered successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to reorder education records',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bulk actions
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:delete,activate,deactivate',
            'ids' => 'required|array',
            'ids.*' => 'exists:education,id'
        ]);

        try {
            DB::transaction(function () use ($request) {
                $action = $request->action;
                $ids = $request->ids;

                switch ($action) {
                    case 'delete':
                        Education::whereIn('id', $ids)->delete();
                        break;

                    case 'activate':
                        Education::whereIn('id', $ids)->update(['is_active' => true]);
                        break;

                    case 'deactivate':
                        Education::whereIn('id', $ids)->update(['is_active' => false]);
                        break;
                }
            });

            return response()->json([
                'success' => true,
                'message' => 'Bulk action completed successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to perform bulk action',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
