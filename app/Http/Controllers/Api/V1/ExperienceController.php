<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Repositories\Contracts\ExperienceRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ExperienceController extends Controller
{
    protected ExperienceRepositoryInterface $experienceRepository;

    public function __construct(ExperienceRepositoryInterface $experienceRepository)
    {
        $this->experienceRepository = $experienceRepository;
    }

    /**
     * Get all active experiences
     */
    public function index(Request $request)
    {
        try {
            $experiences = $this->experienceRepository
                ->where('is_active', true)
                ->orderBy('order', 'asc')
                ->orderBy('start_date', 'desc')
                ->get();

            $data = $experiences->map(function ($experience) {
                return [
                    'id' => $experience->id,
                    'company' => $experience->company_name,
                    'position' => $experience->position,
                    'employment_type' => $experience->employment_type,
                    'location' => $experience->location,
                    'description' => $experience->description,
                    'technologies' => $experience->technologies,
                    'achievements' => $experience->achievements,
                    'start_date' => $experience->start_date,
                    'end_date' => $experience->end_date,
                    'is_current' => $experience->is_current,
                    'order' => $experience->order,
                    'duration' => $experience->duration,
                    'formatted_duration' => $experience->formatted_duration,
                    'formatted_dates' => $experience->formatted_dates,
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $data,
                'meta' => [
                    'total' => $experiences->count(),
                    'current_experiences' => $experiences->where('is_current', true)->count(),
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to fetch experiences: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch experiences.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }
}
