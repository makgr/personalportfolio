<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Repositories\Contracts\TeamRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TeamController extends Controller
{
    protected TeamRepositoryInterface $teamRepository;

    public function __construct(TeamRepositoryInterface $teamRepository)
    {
        $this->teamRepository = $teamRepository;
    }

    /**
     * Get all active team members
     */
    public function index(Request $request)
    {
        try {
            $teams = $this->teamRepository
                ->where('is_active', true)
                ->orderBy('order', 'asc')
                ->get();

            $data = $teams->map(function ($team) {
                return [
                    'id' => $team->id,
                    'name' => $team->name,
                    'slug' => $team->slug,
                    'position' => $team->position,
                    'department' => $team->department,
                    'employment_type' => $team->employment_type,
                    'bio' => $team->bio,
                    'email' => $team->email,
                    'phone' => $team->phone,
                    'image' => $team->image ? asset($team->image) : null,
                    'social_links' => $team->social_links,
                    'skills' => $team->skills,
                    'join_date' => $team->join_date,
                    'is_featured' => $team->is_featured,
                    'order' => $team->order,
                    'initials' => $team->initials,
                    'tenure' => $team->tenure,
                    'formatted_join_date' => $team->formatted_join_date,
                    'department_badge_color' => $team->department_badge_color,
                    'employment_type_badge_color' => $team->employment_type_badge_color,
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $data,
                'meta' => [
                    'total' => $teams->count(),
                    'featured' => $teams->where('is_featured', true)->count(),
                    'departments' => $teams->pluck('department')->unique()->filter()->values()->toArray(),
                    'employment_types' => $teams->pluck('employment_type')->unique()->values()->toArray(),
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to fetch team members: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch team members.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Get featured team members
     */
    public function featured(Request $request)
    {
        try {
            $teams = $this->teamRepository
                ->where('is_active', true)
                ->where('is_featured', true)
                ->orderBy('order', 'asc')
                ->get();

            $data = $teams->map(function ($team) {
                return [
                    'id' => $team->id,
                    'name' => $team->name,
                    'position' => $team->position,
                    'department' => $team->department,
                    'bio' => $team->bio,
                    'image' => $team->image ? asset($team->image) : null,
                    'social_links' => $team->social_links,
                    'initials' => $team->initials,
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $data,
                'meta' => [
                    'total' => $teams->count(),
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to fetch featured team members: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch featured team members.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Get team member by slug
     */
    public function show(Request $request, $slug)
    {
        try {
            $team = $this->teamRepository
                ->where('slug', $slug)
                ->where('is_active', true)
                ->first();

            if (!$team) {
                return response()->json([
                    'success' => false,
                    'message' => 'Team member not found.'
                ], 404);
            }

            $data = [
                'id' => $team->id,
                'name' => $team->name,
                'slug' => $team->slug,
                'position' => $team->position,
                'department' => $team->department,
                'employment_type' => $team->employment_type,
                'bio' => $team->bio,
                'email' => $team->email,
                'phone' => $team->phone,
                'image' => $team->image ? asset($team->image) : null,
                'social_links' => $team->social_links,
                'skills' => $team->skills,
                'join_date' => $team->join_date,
                'is_featured' => $team->is_featured,
                'tenure' => $team->tenure,
                'formatted_join_date' => $team->formatted_join_date,
            ];

            return response()->json([
                'success' => true,
                'data' => $data
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to fetch team member: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch team member.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }
}