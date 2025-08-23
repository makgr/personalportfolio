<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CategoryController extends Controller
{
    /**
     * Display a listing of categories.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Category::query()->active()->ordered();

        // Filter by type if provided
        if ($request->has('type') && in_array($request->type, ['project', 'blog'])) {
            $query->byType($request->type);
        }

        // Include counts if requested
        if ($request->boolean('with_counts')) {
            $query->withCount(['projects', 'blogPosts']);
        }

        $categories = $query->select([
            'id',
            'name',
            'slug',
            'type',
            'description',
            'color',
            'icon',
            'order'
        ])->get();

        return response()->json([
            'success' => true,
            'data' => $categories,
            'message' => 'Categories retrieved successfully'
        ]);
    }

    /**
     * Display the specified category.
     */
    public function show(string $slug): JsonResponse
    {
        $category = Category::where('slug', $slug)
            ->where('is_active', true)
            ->withCount(['projects', 'blogPosts'])
            ->first();

        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'Category not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $category,
            'message' => 'Category retrieved successfully'
        ]);
    }

    /**
     * Get categories by type.
     */
    public function byType(string $type): JsonResponse
    {
        if (!in_array($type, ['project', 'blog'])) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid category type'
            ], 400);
        }

        $categories = Category::active()
            ->byType($type)
            ->ordered()
            ->withCount(['projects', 'blogPosts'])
            ->select([
                'id',
                'name',
                'slug',
                'type',
                'description',
                'color',
                'icon',
                'order'
            ])
            ->get();

        return response()->json([
            'success' => true,
            'data' => $categories,
            'message' => "Categories for {$type} retrieved successfully"
        ]);
    }
}