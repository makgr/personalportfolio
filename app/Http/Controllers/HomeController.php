<?php

namespace App\Http\Controllers;

use App\Models\Analytics;
use App\Models\BlogPost;
use App\Models\ContactSubmission;
use App\Models\Project;
use App\Models\Skill;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the dashboard shell; its data loads asynchronously from data().
     */
    public function index(): Renderable
    {
        return view('home');
    }

    /**
     * Dashboard data: rendered HTML for the stats, projects and messages sections, plus the chart series.
     */
    public function data(): JsonResponse
    {
        $since = Carbon::today()->subDays(29);

        $stats = [
            'projects' => Project::count(),
            'projectsPublished' => Project::where('status', 'published')->count(),
            'skills' => Skill::count(),
            'messages' => ContactSubmission::count(),
            'messagesNew' => ContactSubmission::new()->count(),
            'blogPosts' => BlogPost::count(),
            'blogPostsPublished' => BlogPost::published()->count(),
            'visitors' => Analytics::where('visited_at', '>=', $since)->distinct('visitor_id')->count('visitor_id'),
        ];

        // Daily series for the last 30 days (oldest first), zero-filled
        $days = collect(range(0, 29))->map(fn ($i) => $since->copy()->addDays($i)->toDateString());
        $daily = fn ($query, $column) => $query
            ->where($column, '>=', $since)
            ->selectRaw("DATE($column) as day, COUNT(*) as total")
            ->groupBy('day')
            ->pluck('total', 'day');

        $messagesByDay = $daily(ContactSubmission::query(), 'created_at');
        $visitsByDay = $daily(Analytics::query(), 'visited_at');

        $recentProjects = Project::with('category')->latest()->take(5)->get();
        $recentMessages = ContactSubmission::latest()->take(5)->get();

        return response()->json([
            'stats' => view('home.stats', compact('stats'))->render(),
            'projects' => view('home.projects', compact('recentProjects'))->render(),
            'messages' => view('home.messages', compact('recentMessages'))->render(),
            'chart' => [
                'labels' => $days->values(),
                'messages' => $days->map(fn ($d) => (int) ($messagesByDay[$d] ?? 0))->values(),
                'visits' => $days->map(fn ($d) => (int) ($visitsByDay[$d] ?? 0))->values(),
            ],
        ]);
    }
}
