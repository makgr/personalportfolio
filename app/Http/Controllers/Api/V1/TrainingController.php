<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Training;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TrainingController extends Controller
{
    /**
     * Get all active training records
     */
    public function index(Request $request)
    {
        try {
            $trainings = Training::where('is_active', true)
                ->orderBy('order', 'asc')
                ->orderBy('end_date', 'desc')
                ->get();

            $data = $trainings->map(function ($training) {
                return [
                    'id' => $training->id,
                    'title' => $training->title,
                    'provider' => $training->provider,
                    'category' => $training->category,
                    'description' => $training->description,
                    'skills_gained' => $training->skills_gained,
                    'start_date' => $training->start_date,
                    'end_date' => $training->end_date,
                    'duration_hours' => $training->duration_hours,
                    'certificate_url' => $training->certificate_url,
                    'is_featured' => $training->is_featured,
                    'order' => $training->order,
                    'formatted_duration' => $training->formatted_duration,
                    'status' => $training->status,
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $data,
                'meta' => [
                    'total' => $trainings->count(),
                    'featured' => $trainings->where('is_featured', true)->count(),
                    'categories' => $trainings->pluck('category')->unique()->values()->toArray(),
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to fetch training: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch training records.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Get featured training records
     */
    public function featured(Request $request)
    {
        try {
            $trainings = Training::where('is_active', true)
                ->where('is_featured', true)
                ->orderBy('order', 'asc')
                ->orderBy('end_date', 'desc')
                ->get();

            $data = $trainings->map(function ($training) {
                return [
                    'id' => $training->id,
                    'title' => $training->title,
                    'provider' => $training->provider,
                    'category' => $training->category,
                    'description' => $training->description,
                    'start_date' => $training->start_date,
                    'end_date' => $training->end_date,
                    'certificate_url' => $training->certificate_url,
                    'formatted_duration' => $training->formatted_duration,
                    'status' => $training->status,
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $data,
                'meta' => [
                    'total' => $trainings->count(),
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to fetch featured training: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch featured training.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }
}