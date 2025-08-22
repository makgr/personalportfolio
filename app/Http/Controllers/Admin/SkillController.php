<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Skill;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

class SkillController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    /**
     * Display a listing of skills
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Skill::query();
            
            return DataTables::of($query)
                ->addColumn('proficiency_badge', function ($skill) {
                    $percentage = $skill->proficiency;
                    $color = 'secondary';
                    if ($percentage >= 80) $color = 'success';
                    elseif ($percentage >= 60) $color = 'primary';
                    elseif ($percentage >= 40) $color = 'warning';
                    
                    return '<span class="badge bg-' . $color . '">' . $percentage . '%</span>';
                })
                ->addColumn('category_badge', function ($skill) {
                    $colors = [
                        'frontend' => 'info',
                        'backend' => 'success',
                        'database' => 'warning',
                        'devops' => 'danger',
                        'mobile' => 'primary',
                        'tools' => 'secondary',
                        'soft_skills' => 'dark',
                        'other' => 'light'
                    ];
                    $color = $colors[$skill->category] ?? 'secondary';
                    return '<span class="badge bg-' . $color . '">' . ucfirst(str_replace('_', ' ', $skill->category)) . '</span>';
                })
                ->addColumn('status_badge', function ($skill) {
                    return $skill->is_active 
                        ? '<span class="badge bg-success">Active</span>'
                        : '<span class="badge bg-secondary">Inactive</span>';
                })
                ->addColumn('primary_badge', function ($skill) {
                    return $skill->is_primary 
                        ? '<span class="badge bg-warning"><i class="bx bx-star"></i> Primary</span>'
                        : '';
                })
                ->addColumn('icon_display', function ($skill) {
                    if ($skill->icon) {
                        // Handle Boxicons (bx-*)
                        if (str_starts_with($skill->icon, 'bx-')) {
                            return '<i class="bx ' . $skill->icon . ' text-primary" style="font-size: 1.5rem;"></i>';
                        }
                        // Handle Font Awesome (fa*, fab, fas, far, fal, fad)
                        elseif (preg_match('/^(fa[a-z]?-|fa\s)/', $skill->icon)) {
                            // If it starts with fa- but no prefix, assume fas (solid)
                            if (str_starts_with($skill->icon, 'fa-')) {
                                return '<i class="fas ' . $skill->icon . ' text-primary" style="font-size: 1.5rem;"></i>';
                            } else {
                                return '<i class="' . $skill->icon . ' text-primary" style="font-size: 1.5rem;"></i>';
                            }
                        }
                        // Handle any other icon class
                        else {
                            return '<i class="' . $skill->icon . ' text-primary" style="font-size: 1.5rem;"></i>';
                        }
                    }
                    return '<i class="bx bx-code text-muted" style="font-size: 1.5rem;"></i>';
                })
                ->addColumn('actions', function ($skill) {
                    return view('admin.skills.partials.actions', compact('skill'));
                })
                ->editColumn('created_at', function ($skill) {
                    return $skill->created_at->format('M d, Y');
                })
                ->editColumn('years_experience', function ($skill) {
                    return $skill->years_experience ? $skill->years_experience . ' year' . ($skill->years_experience != 1 ? 's' : '') : 'N/A';
                })
                ->rawColumns(['proficiency_badge', 'category_badge', 'status_badge', 'primary_badge', 'icon_display', 'actions'])
                ->make(true);
        }
        
        return view('admin.skills.index');
    }

    /**
     * Show the form for creating a new skill
     */
    public function create()
    {
        $categories = [
            'frontend' => 'Frontend',
            'backend' => 'Backend',
            'database' => 'Database',
            'devops' => 'DevOps',
            'mobile' => 'Mobile',
            'tools' => 'Tools',
            'soft_skills' => 'Soft Skills',
            'other' => 'Other'
        ];
        
        return view('admin.skills.create', compact('categories'));
    }

    /**
     * Store new skill
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:skills',
            'category' => 'required|in:frontend,backend,database,devops,mobile,tools,soft_skills,other',
            'proficiency' => 'required|integer|min:0|max:100',
            'years_experience' => 'nullable|numeric|min:0',
            'icon' => 'nullable|string|max:255',
            'color' => 'nullable|string|max:7',
            'description' => 'nullable|string',
            'certifications' => 'nullable|string',
            'is_primary' => 'boolean',
            'is_active' => 'boolean',
            'order' => 'nullable|integer|min:0'
        ]);

        try {
            $data = $request->all();
            
            // Generate slug
            $data['slug'] = Str::slug($data['name']);
            
            // Handle checkboxes
            $data['is_primary'] = $request->has('is_primary');
            $data['is_active'] = $request->has('is_active') ? true : false;
            
            // Handle certifications as array
            if ($data['certifications']) {
                $data['certifications'] = array_filter(array_map('trim', explode(',', $data['certifications'])));
            } else {
                $data['certifications'] = [];
            }
            
            // Set order if not provided
            if (!$data['order']) {
                $data['order'] = Skill::max('order') + 1;
            }

            $skill = Skill::create($data);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'data' => $skill,
                    'message' => 'Skill created successfully'
                ]);
            }

            return redirect()
                ->route('skills.index')
                ->with('success', 'Skill created successfully');
        } catch (\Exception $e) {
            Log::error('Failed to create skill: ' . $e->getMessage());
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to create skill',
                    'error' => $e->getMessage()
                ], 422);
            }

            return back()
                ->withInput()
                ->with('error', 'Failed to create skill: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified skill
     */
    public function show(Request $request, $id)
    {
        try {
            $skill = Skill::findOrFail($id);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'data' => $skill
                ]);
            }

            return view('admin.skills.show', compact('skill'));
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Skill not found'
                ], 404);
            }

            return back()->with('error', 'Skill not found');
        }
    }

    /**
     * Show the form for editing the specified skill
     */
    public function edit($id)
    {
        $skill = Skill::findOrFail($id);
        
        $categories = [
            'frontend' => 'Frontend',
            'backend' => 'Backend',
            'database' => 'Database',
            'devops' => 'DevOps',
            'mobile' => 'Mobile',
            'tools' => 'Tools',
            'soft_skills' => 'Soft Skills',
            'other' => 'Other'
        ];
        
        return view('admin.skills.edit', compact('skill', 'categories'));
    }

    /**
     * Update skill
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:skills,name,' . $id,
            'category' => 'required|in:frontend,backend,database,devops,mobile,tools,soft_skills,other',
            'proficiency' => 'required|integer|min:0|max:100',
            'years_experience' => 'nullable|numeric|min:0',
            'icon' => 'nullable|string|max:255',
            'color' => 'nullable|string|max:7',
            'description' => 'nullable|string',
            'certifications' => 'nullable|string',
            'is_primary' => 'boolean',
            'is_active' => 'boolean',
            'order' => 'nullable|integer|min:0'
        ]);

        try {
            $skill = Skill::findOrFail($id);
            $data = $request->all();
            
            // Update slug if name changed
            if ($data['name'] !== $skill->name) {
                $data['slug'] = Str::slug($data['name']);
            }
            
            // Handle checkboxes
            $data['is_primary'] = $request->has('is_primary');
            $data['is_active'] = $request->has('is_active') ? true : false;
            
            // Handle certifications as array
            if ($data['certifications']) {
                $data['certifications'] = array_filter(array_map('trim', explode(',', $data['certifications'])));
            } else {
                $data['certifications'] = [];
            }

            $skill->update($data);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'data' => $skill,
                    'message' => 'Skill updated successfully'
                ]);
            }

            return redirect()
                ->route('skills.edit', $id)
                ->with('success', 'Skill updated successfully');
        } catch (\Exception $e) {
            Log::error('Failed to update skill: ' . $e->getMessage());
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update skill',
                    'error' => $e->getMessage()
                ], 422);
            }

            return back()
                ->withInput()
                ->with('error', 'Failed to update skill: ' . $e->getMessage());
        }
    }

    /**
     * Delete skill
     */
    public function destroy(Request $request, $skill)
    {
        try {
            $skillId = (int) $skill;
            $skillRecord = Skill::findOrFail($skillId);
            $skillRecord->delete();

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Skill deleted successfully'
                ]);
            }

            return redirect()
                ->route('skills.index')
                ->with('success', 'Skill deleted successfully');
        } catch (\Exception $e) {
            Log::error('Failed to delete skill: ' . $e->getMessage());
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to delete skill',
                    'error' => $e->getMessage()
                ], 500);
            }

            return back()
                ->with('error', 'Failed to delete skill: ' . $e->getMessage());
        }
    }

    /**
     * Toggle skill status
     */
    public function toggleStatus($id)
    {
        try {
            $skill = Skill::findOrFail($id);
            $skill->is_active = !$skill->is_active;
            $skill->save();

            return response()->json([
                'success' => true,
                'data' => $skill,
                'message' => $skill->is_active ? 'Skill activated' : 'Skill deactivated'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to toggle skill status',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reorder skills
     */
    public function reorder(Request $request)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|exists:skills,id',
            'items.*.order' => 'required|integer|min:0'
        ]);

        try {
            DB::transaction(function () use ($request) {
                foreach ($request->items as $item) {
                    Skill::where('id', $item['id'])->update(['order' => $item['order']]);
                }
            });

            return response()->json([
                'success' => true,
                'message' => 'Skills reordered successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to reorder skills',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Toggle primary status
     */
    public function togglePrimary($id)
    {
        try {
            $skill = Skill::findOrFail($id);
            $skill->is_primary = !$skill->is_primary;
            $skill->save();

            return response()->json([
                'success' => true,
                'data' => $skill,
                'message' => $skill->is_primary ? 'Skill marked as primary' : 'Skill unmarked as primary'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to toggle primary status',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bulk update proficiency
     */
    public function bulkUpdateProficiency(Request $request)
    {
        $request->validate([
            'skills' => 'required|array',
            'skills.*.id' => 'required|exists:skills,id',
            'skills.*.proficiency' => 'required|integer|min:0|max:100'
        ]);

        try {
            DB::transaction(function () use ($request) {
                foreach ($request->skills as $skillData) {
                    Skill::where('id', $skillData['id'])
                        ->update(['proficiency' => $skillData['proficiency']]);
                }
            });

            return response()->json([
                'success' => true,
                'message' => 'Proficiency levels updated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update proficiency levels',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
