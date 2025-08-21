<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Models\Skill;
use App\Models\Project;
use App\Models\Service;
use App\Models\BlogPost;
use App\Models\Analytics;
use App\Models\Testimonial;
use Illuminate\Http\Request;
use App\Models\ContactSubmission;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    /**
     * Get dashboard data
     */
    public function index(Request $request)
    {
        try {
            // Date range for analytics
            $dateRange = $request->get('range', 30); // days
            $startDate = Carbon::now()->subDays($dateRange);

            // Statistics
            $stats = [
                'projects' => [
                    'total' => Project::count(),
                    'published' => Project::where('status', 'published')->count(),
                    'featured' => Project::where('is_featured', true)->count(),
                    'views' => Project::sum('views')
                ],
                'blog' => [
                    'total' => BlogPost::count(),
                    'published' => BlogPost::where('status', 'published')->count(),
                    'featured' => BlogPost::where('is_featured', true)->count(),
                    'views' => BlogPost::sum('views'),
                    'likes' => BlogPost::sum('likes')
                ],
                'contacts' => [
                    'total' => ContactSubmission::count(),
                    'new' => ContactSubmission::where('status', 'new')->count(),
                    'today' => ContactSubmission::whereDate('created_at', today())->count(),
                    'this_week' => ContactSubmission::where('created_at', '>=', Carbon::now()->startOfWeek())->count()
                ],
                'skills' => [
                    'total' => Skill::count(),
                    'primary' => Skill::where('is_primary', true)->count(),
                    'categories' => Skill::select('category', DB::raw('count(*) as count'))
                        ->groupBy('category')
                        ->pluck('count', 'category')
                ],
                'services' => [
                    'total' => Service::count(),
                    'active' => Service::where('is_active', true)->count()
                ],
                'testimonials' => [
                    'total' => Testimonial::count(),
                    'featured' => Testimonial::where('is_featured', true)->count(),
                    'average_rating' => Testimonial::avg('rating')
                ]
            ];

            // Analytics data
            $analytics = [
                'visitors' => Analytics::where('visited_at', '>=', $startDate)
                    ->select(DB::raw('DATE(visited_at) as date'), DB::raw('COUNT(DISTINCT visitor_id) as count'))
                    ->groupBy('date')
                    ->orderBy('date')
                    ->get(),
                'pageviews' => Analytics::where('visited_at', '>=', $startDate)
                    ->select(DB::raw('DATE(visited_at) as date'), DB::raw('COUNT(*) as count'))
                    ->groupBy('date')
                    ->orderBy('date')
                    ->get(),
                'top_pages' => Analytics::where('visited_at', '>=', $startDate)
                    ->select('page_url', DB::raw('COUNT(*) as views'))
                    ->groupBy('page_url')
                    ->orderBy('views', 'desc')
                    ->limit(10)
                    ->get(),
                'devices' => Analytics::where('visited_at', '>=', $startDate)
                    ->select('device_type', DB::raw('COUNT(*) as count'))
                    ->groupBy('device_type')
                    ->get(),
                'browsers' => Analytics::where('visited_at', '>=', $startDate)
                    ->select('browser', DB::raw('COUNT(*) as count'))
                    ->groupBy('browser')
                    ->get(),
                'referrers' => Analytics::where('visited_at', '>=', $startDate)
                    ->whereNotNull('referrer')
                    ->select('referrer', DB::raw('COUNT(*) as count'))
                    ->groupBy('referrer')
                    ->orderBy('count', 'desc')
                    ->limit(10)
                    ->get()
            ];

            // Recent items
            $recent = [
                'projects' => Project::with('category')
                    ->latest()
                    ->limit(5)
                    ->get(),
                'posts' => BlogPost::with(['category', 'author'])
                    ->latest()
                    ->limit(5)
                    ->get(),
                'contacts' => ContactSubmission::latest()
                    ->limit(5)
                    ->get(),
                'testimonials' => Testimonial::with('project')
                    ->latest()
                    ->limit(5)
                    ->get()
            ];

            return response()->json([
                'success' => true,
                'data' => [
                    'stats' => $stats,
                    'analytics' => $analytics,
                    'recent' => $recent
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to fetch dashboard data: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch dashboard data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get quick stats for header/widgets
     */
    public function quickStats()
    {
        try {
            $stats = [
                'new_messages' => ContactSubmission::where('status', 'new')->count(),
                'today_visitors' => Analytics::whereDate('visited_at', today())->count(),
                'total_projects' => Project::where('status', 'published')->count(),
                'total_posts' => BlogPost::where('status', 'published')->count()
            ];

            return response()->json([
                'success' => true,
                'data' => $stats
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch quick stats',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
