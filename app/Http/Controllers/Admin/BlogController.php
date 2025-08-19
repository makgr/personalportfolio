<?php

namespace App\Http\Controllers\Admin;

use App\Models\Tag;
use App\Models\BlogPost;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Services\BlogService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\Admin\BlogRequest;

class BlogController extends Controller
{
    protected BlogService $blogService;

    public function __construct(BlogService $blogService)
    {
        $this->middleware(['auth', 'admin']);
        $this->blogService = $blogService;
    }

    /**
     * Get all blog posts with filtering and pagination
     */
    public function index(Request $request)
    {
        try {
            $query = BlogPost::with(['category', 'author', 'tagModels']);

            // Search filter
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                        ->orWhere('excerpt', 'like', "%{$search}%")
                        ->orWhere('content', 'like', "%{$search}%");
                });
            }

            // Status filter
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            // Category filter
            if ($request->filled('category_id')) {
                $query->where('category_id', $request->category_id);
            }

            // Date range filter
            if ($request->filled('date_from')) {
                $query->whereDate('created_at', '>=', $request->date_from);
            }
            if ($request->filled('date_to')) {
                $query->whereDate('created_at', '<=', $request->date_to);
            }

            // Sorting
            $sortBy = $request->get('sort_by', 'created_at');
            $sortOrder = $request->get('sort_order', 'desc');
            $query->orderBy($sortBy, $sortOrder);

            // Pagination
            $perPage = $request->get('per_page', 15);
            $posts = $query->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $posts,
                'message' => 'Blog posts retrieved successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to fetch blog posts: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch blog posts',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get single blog post
     */
    public function show($id)
    {
        try {
            $post = BlogPost::with(['category', 'author', 'tagModels', 'media', 'versions'])
                ->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $post
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Blog post not found'
            ], 404);
        }
    }

    /**
     * Store new blog post
     */
    public function store(BlogRequest $request)
    {
        try {
            $result = DB::transaction(function () use ($request) {
                $data = $request->validated();

                // Set author
                $data['author_id'] = Auth::user()->id;

                // Handle featured image
                $featuredImage = $request->file('featured_image');

                // Handle tags
                $tags = $request->input('tags', []);

                // Create blog post
                $post = $this->blogService->createBlogPost(
                    $data,
                    $featuredImage,
                    $tags
                );

                return $post;
            });

            return response()->json([
                'success' => true,
                'data' => $result->load(['category', 'author', 'tagModels']),
                'message' => 'Blog post created successfully'
            ], 201);
        } catch (\Exception $e) {
            Log::error('Failed to create blog post: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to create blog post',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update blog post
     */
    public function update(BlogRequest $request, $id)
    {
        try {
            $result = DB::transaction(function () use ($request, $id) {
                $data = $request->validated();

                // Handle featured image
                $featuredImage = $request->file('featured_image');

                // Handle tags
                $tags = $request->input('tags', []);

                // Update blog post
                $post = $this->blogService->updateBlogPost(
                    $id,
                    $data,
                    $featuredImage,
                    $tags
                );

                return $post;
            });

            return response()->json([
                'success' => true,
                'data' => $result->load(['category', 'author', 'tagModels']),
                'message' => 'Blog post updated successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to update blog post: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update blog post',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete blog post
     */
    public function destroy($id)
    {
        try {
            DB::transaction(function () use ($id) {
                $this->blogService->deleteBlogPost($id);
            });

            return response()->json([
                'success' => true,
                'message' => 'Blog post deleted successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to delete blog post: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete blog post',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Publish blog post
     */
    public function publish($id)
    {
        try {
            $post = $this->blogService->publishBlogPost($id);

            return response()->json([
                'success' => true,
                'data' => $post,
                'message' => 'Blog post published successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to publish blog post',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Unpublish blog post
     */
    public function unpublish($id)
    {
        try {
            $post = $this->blogService->unpublishBlogPost($id);

            return response()->json([
                'success' => true,
                'data' => $post,
                'message' => 'Blog post unpublished successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to unpublish blog post',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Schedule blog post
     */
    public function schedule(Request $request, $id)
    {
        $request->validate([
            'scheduled_at' => 'required|date|after:now'
        ]);

        try {
            $post = $this->blogService->scheduleBlogPost($id, $request->scheduled_at);

            return response()->json([
                'success' => true,
                'data' => $post,
                'message' => 'Blog post scheduled successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to schedule blog post',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bulk actions
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:delete,publish,unpublish,archive',
            'ids' => 'required|array',
            'ids.*' => 'exists:blog_posts,id'
        ]);

        try {
            DB::transaction(function () use ($request) {
                $action = $request->action;
                $ids = $request->ids;

                switch ($action) {
                    case 'delete':
                        foreach ($ids as $id) {
                            $this->blogService->deleteBlogPost($id);
                        }
                        break;

                    case 'publish':
                        BlogPost::whereIn('id', $ids)->update([
                            'status' => 'published',
                            'published_at' => now()
                        ]);
                        break;

                    case 'unpublish':
                        BlogPost::whereIn('id', $ids)->update([
                            'status' => 'draft'
                        ]);
                        break;

                    case 'archive':
                        BlogPost::whereIn('id', $ids)->update([
                            'status' => 'archived'
                        ]);
                        break;
                }
            });

            return response()->json([
                'success' => true,
                'message' => 'Bulk action completed successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to perform bulk action',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Toggle featured status
     */
    public function toggleFeatured($id)
    {
        try {
            $post = BlogPost::findOrFail($id);
            $post->is_featured = !$post->is_featured;
            $post->save();

            return response()->json([
                'success' => true,
                'data' => $post,
                'message' => $post->is_featured ? 'Blog post featured' : 'Blog post unfeatured'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to toggle featured status',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get blog statistics
     */
    public function statistics()
    {
        try {
            $stats = [
                'total' => BlogPost::count(),
                'published' => BlogPost::where('status', 'published')->count(),
                'draft' => BlogPost::where('status', 'draft')->count(),
                'scheduled' => BlogPost::where('status', 'scheduled')->count(),
                'featured' => BlogPost::where('is_featured', true)->count(),
                'total_views' => BlogPost::sum('views'),
                'total_likes' => BlogPost::sum('likes'),
                'recent_posts' => BlogPost::with(['category', 'author'])
                    ->latest()
                    ->limit(5)
                    ->get()
            ];

            return response()->json([
                'success' => true,
                'data' => $stats
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get statistics',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
