<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\SkillResource;
use App\Services\SkillService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SkillController extends Controller
{
    protected SkillService $skillService;

    public function __construct(SkillService $skillService)
    {
        $this->skillService = $skillService;
    }

    /**
     * Get all active skills
     */
    public function index(Request $request)
    {
        try {
            $category = $request->get('category');
            $primary = $request->boolean('primary');

            if ($primary) {
                $skills = $this->skillService->getPrimarySkills();
            } elseif ($category) {
                $skills = $this->skillService->getSkillsByCategory($category);
            } else {
                $skills = $this->skillService->getActiveSkills();
            }

            return response()->json([
                'success' => true,
                'data' => SkillResource::collection($skills),
                'categories' => $this->getSkillCategories()
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to fetch skills: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch skills'
            ], 500);
        }
    }

    /**
     * Get skill categories with counts
     */
    protected function getSkillCategories()
    {
        return $this->skillService->getActiveSkills()
            ->groupBy('category')
            ->map(function ($skills, $category) {
                return [
                    'name' => $category,
                    'count' => $skills->count(),
                    'skills' => $skills->pluck('name')
                ];
            });
    }
}
