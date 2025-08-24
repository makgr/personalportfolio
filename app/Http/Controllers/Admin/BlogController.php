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
use Yajra\DataTables\Facades\DataTables;

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
        if ($request->ajax()) {
            $query = BlogPost::with(['category', 'author', 'tagModels']);
            
            return DataTables::of($query)
                ->addColumn('status_badge', function ($post) {
                    $colors = [
                        'published' => 'success',
                        'draft' => 'warning', 
                        'scheduled' => 'info',
                        'archived' => 'secondary'
                    ];
                    $color = $colors[$post->status] ?? 'primary';
                    return '<span class="badge bg-' . $color . '">' . ucfirst($post->status) . '</span>';
                })
                ->addColumn('featured_badge', function ($post) {
                    return $post->is_featured 
                        ? '<span class="badge bg-info"><i class="bx bx-star"></i> Featured</span>'
                        : '';
                })
                ->addColumn('author_name', function ($post) {
                    return $post->author ? $post->author->name : 'Unknown';
                })
                ->addColumn('category_name', function ($post) {
                    return $post->category ? $post->category->name : 'Uncategorized';
                })
                ->addColumn('reading_time', function ($post) {
                    return $post->reading_time . ' min read';
                })
                ->addColumn('actions', function ($post) {
                    return view('admin.blog-posts.partials.actions', compact('post'));
                })
                ->editColumn('created_at', function ($post) {
                    return $post->created_at->format('M d, Y');
                })
                ->editColumn('views', function ($post) {
                    return number_format($post->views);
                })
                ->rawColumns(['status_badge', 'featured_badge', 'actions'])
                ->make(true);
        }
        
        return view('admin.blog-posts.index');
    }

    /**
     * Show create form
     */
    public function create()
    {
        $categories = Category::where('type', 'blog')->active()->get();
        $tags = Tag::orderBy('name')->get();
        return view('admin.blog-posts.create', compact('categories', 'tags'));
    }

    /**
     * Show edit form
     */
    public function edit($id)
    {
        $post = BlogPost::with(['category', 'tagModels', 'media'])->findOrFail($id);
        $categories = Category::where('type', 'blog')->active()->get();
        $tags = Tag::orderBy('name')->get();
        return view('admin.blog-posts.edit', compact('post', 'categories', 'tags'));
    }

    /**
     * Get single blog post
     */
    public function show(Request $request, $id)
    {
        try {
            $post = BlogPost::with(['category', 'author', 'tagModels', 'media', 'versions'])
                ->findOrFail($id);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'data' => $post
                ]);
            }

            return view('admin.blog-posts.show', compact('post'));
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Blog post not found'
                ], 404);
            }

            return back()->with('error', 'Blog post not found');
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
                
                // Handle checkboxes (they won't be in request if unchecked)
                $data['is_featured'] = $request->has('is_featured');
                $data['allow_comments'] = $request->has('allow_comments');

                // Set author
                $data['author_id'] = Auth::user()->id;

                // Handle featured image
                $featuredImage = $request->file('featured_image');

                // Handle tags
                $tagsJson = $request->input('tags', '[]');
                $tags = is_string($tagsJson) ? json_decode($tagsJson, true) : $tagsJson;

                // Create blog post
                $post = $this->blogService->createBlogPost(
                    $data,
                    $featuredImage,
                    $tags
                );

                return $post;
            });

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'data' => $result->load(['category', 'author', 'tagModels']),
                    'message' => 'Blog post created successfully'
                ]);
            }

            return redirect()
                ->route('blog-posts.show', $result->id)
                ->with('success', 'Blog post created successfully');
        } catch (\Exception $e) {
            Log::error('Failed to create blog post: ' . $e->getMessage());
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to create blog post',
                    'error' => $e->getMessage()
                ], 422);
            }

            return back()
                ->withInput()
                ->with('error', 'Failed to create blog post: ' . $e->getMessage());
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
                
                // Handle checkboxes (they won't be in request if unchecked)
                $data['is_featured'] = $request->has('is_featured');
                $data['allow_comments'] = $request->has('allow_comments');

                // Handle featured image
                $featuredImage = $request->file('featured_image');

                // Handle tags
                $tagsJson = $request->input('tags', '[]');
                $tags = is_string($tagsJson) ? json_decode($tagsJson, true) : $tagsJson;

                // Update blog post
                $post = $this->blogService->updateBlogPost(
                    $id,
                    $data,
                    $featuredImage,
                    $tags
                );

                return $post;
            });

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'data' => $result->load(['category', 'author', 'tagModels']),
                    'message' => 'Blog post updated successfully'
                ]);
            }

            return redirect()
                ->route('blog-posts.edit', $id)
                ->with('success', 'Blog post updated successfully');
        } catch (\Exception $e) {
            Log::error('Failed to update blog post: ' . $e->getMessage());
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update blog post',
                    'error' => $e->getMessage()
                ], 422);
            }

            return back()
                ->withInput()
                ->with('error', 'Failed to update blog post: ' . $e->getMessage());
        }
    }

    /**
     * Delete blog post
     */
    public function destroy(Request $request, $blog_post)
    {
        try {
            // $blog_post is the ID from the route, not the model instance
            $postId = (int) $blog_post;
            
            DB::transaction(function () use ($postId) {
                $this->blogService->deleteBlogPost($postId);
            });

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Blog post deleted successfully'
                ]);
            }

            return redirect()
                ->route('blog-posts.index')
                ->with('success', 'Blog post deleted successfully');
        } catch (\Exception $e) {
            Log::error('Failed to delete blog post: ' . $e->getMessage());
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to delete blog post',
                    'error' => $e->getMessage()
                ], 500);
            }

            return back()
                ->with('error', 'Failed to delete blog post: ' . $e->getMessage());
        }
    }

    /**
     * Publish blog post
     */
    public function publish($post)
    {
        try {
            $postId = $post instanceof BlogPost ? $post->id : $post;
            $post = $this->blogService->publishBlogPost($postId);

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
    public function unpublish($post)
    {
        try {
            $postId = $post instanceof BlogPost ? $post->id : $post;
            $post = $this->blogService->unpublishBlogPost($postId);

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
