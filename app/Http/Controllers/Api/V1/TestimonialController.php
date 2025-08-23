<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Testimonial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TestimonialController extends Controller
{
    /**
     * Get all active testimonials
     */
    public function index(Request $request)
    {
        try {
            $testimonials = Testimonial::where('is_active', true)
                ->orderBy('order', 'asc')
                ->orderBy('created_at', 'desc')
                ->get();

            $data = $testimonials->map(function ($testimonial) {
                return [
                    'id' => $testimonial->id,
                    'name' => $testimonial->name,
                    'position' => $testimonial->position,
                    'company' => $testimonial->company,
                    'email' => $testimonial->email,
                    'avatar' => $testimonial->avatar ? asset($testimonial->avatar) : null,
                    'rating' => $testimonial->rating,
                    'content' => $testimonial->content,
                    'project' => $testimonial->project,
                    'is_featured' => $testimonial->is_featured,
                    'order' => $testimonial->order,
                    'initials' => $testimonial->initials,
                    'rating_stars' => str_repeat('★', $testimonial->rating) . str_repeat('☆', 5 - $testimonial->rating),
                    'created_at' => $testimonial->created_at,
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $data,
                'meta' => [
                    'total' => $testimonials->count(),
                    'featured' => $testimonials->where('is_featured', true)->count(),
                    'average_rating' => round($testimonials->avg('rating'), 1),
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to fetch testimonials: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch testimonials.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Get featured testimonials
     */
    public function featured(Request $request)
    {
        try {
            $testimonials = Testimonial::where('is_active', true)
                ->where('is_featured', true)
                ->orderBy('order', 'asc')
                ->get();

            $data = $testimonials->map(function ($testimonial) {
                return [
                    'id' => $testimonial->id,
                    'name' => $testimonial->name,
                    'position' => $testimonial->position,
                    'company' => $testimonial->company,
                    'avatar' => $testimonial->avatar ? asset($testimonial->avatar) : null,
                    'rating' => $testimonial->rating,
                    'content' => $testimonial->content,
                    'initials' => $testimonial->initials,
                    'rating_stars' => str_repeat('★', $testimonial->rating) . str_repeat('☆', 5 - $testimonial->rating),
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $data,
                'meta' => [
                    'total' => $testimonials->count(),
                    'average_rating' => round($testimonials->avg('rating'), 1),
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to fetch featured testimonials: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch featured testimonials.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }
}