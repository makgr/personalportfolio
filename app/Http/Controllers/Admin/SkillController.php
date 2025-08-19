<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Skill;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class SkillController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    /**
     * Get all skills with filtering
     */
    public function index(Request $request)
    {
        try {
            $query = Skill::query();

            // Search filter
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where('name', 'like', "%{$search}%");
            }

            // Category filter
            if ($request->filled('category')) {
                $query->where('category', $request->category);
            }

            // Primary filter
            if ($request->filled('is_primary')) {
                $query->where('is_primary', $request->boolean('is_primary'));
            }

            // Active filter
            if ($request->filled('is_active')) {
                $query->where('is_active', $request->boolean('is_active'));
            }

            // Sorting
            $sortBy = $request->get('sort_by', 'order');
            $sortOrder = $request->get('sort_order', 'asc');
            $query->orderBy($sortBy, $sortOrder);

            $skills = $request->has('all') ? $query->get() : $query->paginate($request->get('per_page', 15));

            return response()->json([
                'success' => true,
                'data' => $skills
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to fetch skills: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch skills',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store new skill
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:skills',
            'category' => 'required|in:frontend,backend,database,devops,mobile,tools,soft_skills,other',
            'proficiency' => 'required|integer|min:0|max:100',
            'years_experience' => 'nullable|numeric|min:0',
            'icon' => 'nullable|string|max:255',
            'color' => 'nullable|string|max:7',
            'description' => 'nullable|string',
            'certifications' => 'nullable|array',
            'is_primary' => 'boolean',
            'is_active' => 'boolean',
        ]);

        try {
            DB::transaction(function () use (&$skill, $validated) {
                $validated['slug'] = Str::slug($validated['name']);
                $validated['order'] = Skill::max('order') + 1;

                $skill = Skill::create($validated);
            });

            return response()->json([
                'success' => true,
                'data' => $skill,
                'message' => 'Skill created successfully'
            ], 201);
        } catch (\Exception $e) {
            Log::error('Failed to create skill: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to create skill',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update skill
     */
    public function update(Request $request, $id)
    {
        $skill = Skill::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:skills,name,' . $id,
            'category' => 'required|in:frontend,backend,database,devops,mobile,tools,soft_skills,other',
            'proficiency' => 'required|integer|min:0|max:100',
            'years_experience' => 'nullable|numeric|min:0',
            'icon' => 'nullable|string|max:255',
            'color' => 'nullable|string|max:7',
            'description' => 'nullable|string',
            'certifications' => 'nullable|array',
            'is_primary' => 'boolean',
            'is_active' => 'boolean',
        ]);

        try {
            DB::transaction(function () use ($skill, $validated) {
                if ($validated['name'] !== $skill->name) {
                    $validated['slug'] = Str::slug($validated['name']);
                }

                $skill->update($validated);
            });

            return response()->json([
                'success' => true,
                'data' => $skill->fresh(),
                'message' => 'Skill updated successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to update skill: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update skill',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete skill
     */
    public function destroy($id)
    {
        try {
            $skill = Skill::findOrFail($id);
            $skill->delete();

            return response()->json([
                'success' => true,
                'message' => 'Skill deleted successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to delete skill: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete skill',
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
