<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\BlogCollection;
use App\Http\Resources\BlogResource;
use App\Models\BlogPost;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class BlogController extends Controller
{
    /**
     * Get all published blog posts
     */
    public function index(Request $request)
    {
        try {
            $query = BlogPost::with(['category', 'author', 'tagModels'])
                ->published();

            // Search filter
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                        ->orWhere('excerpt', 'like', "%{$search}%");
                });
            }

            // Category filter
            if ($request->filled('category')) {
                $query->whereHas('category', function ($q) use ($request) {
                    $q->where('slug', $request->category);
                });
            }

            // Tag filter
            if ($request->filled('tag')) {
                $query->whereHas('tagModels', function ($q) use ($request) {
                    $q->where('slug', $request->tag);
                });
            }

            // Sorting
            $sortBy = $request->get('sort', 'published_at');
            $sortOrder = $request->get('order', 'desc');
            $query->orderBy($sortBy, $sortOrder);

            // Pagination
            $perPage = $request->get('per_page', 12);
            $posts = $query->paginate($perPage);

            return new BlogCollection($posts);
        } catch (\Exception $e) {
            Log::error('Failed to fetch blog posts: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch blog posts'
            ], 500);
        }
    }

    /**
     * Get featured blog posts
     */
    public function featured(Request $request)
    {
        try {
            $limit = $request->get('limit', 5);
            
            $posts = BlogPost::with(['category', 'author'])
                ->published()
                ->featured()
                ->orderBy('published_at', 'desc')
                ->limit($limit)
                ->get();

            return new BlogCollection($posts);
        } catch (\Exception $e) {
            Log::error('Failed to fetch featured posts: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch featured posts'
            ], 500);
        }
    }

    /**
     * Get single blog post by slug
     */
    public function show(string $slug)
    {
        try {
            $post = BlogPost::with(['category', 'author', 'tagModels'])
                ->where('slug', $slug)
                ->published()
                ->firstOrFail();

            // Increment views
            $post->incrementViews();

            return new BlogResource($post);
        } catch (\Exception $e) {
            Log::error('Blog post not found: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Blog post not found'
            ], 404);
        }
    }
}
