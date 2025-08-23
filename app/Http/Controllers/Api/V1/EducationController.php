<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Education;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class EducationController extends Controller
{
    /**
     * Get all active education records
     */
    public function index(Request $request)
    {
        try {
            $education = Education::where('is_active', true)
                ->orderBy('order', 'asc')
                ->orderBy('start_date', 'desc')
                ->get();

            $data = $education->map(function ($edu) {
                return [
                    'id' => $edu->id,
                    'institution' => $edu->institution,
                    'degree' => $edu->degree,
                    'field_of_study' => $edu->field_of_study,
                    'location' => $edu->location,
                    'description' => $edu->description,
                    'activities' => $edu->activities,
                    'start_date' => $edu->start_date,
                    'end_date' => $edu->end_date,
                    'is_current' => $edu->is_current,
                    'grade' => $edu->grade,
                    'order' => $edu->order,
                    'duration' => $edu->duration,
                    'formatted_duration' => $edu->formatted_duration,
                    'formatted_dates' => $edu->formatted_dates,
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $data,
                'meta' => [
                    'total' => $education->count(),
                    'current_education' => $education->where('is_current', true)->count(),
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to fetch education: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch education records.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }
}