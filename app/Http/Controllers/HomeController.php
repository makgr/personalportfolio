<?php

namespace App\Http\Controllers;

use App\Models\Analytics;
use App\Models\BlogPost;
use App\Models\ContactSubmission;
use App\Models\Project;
use App\Models\Skill;
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
     * Show the admin dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
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

        $chart = [
            'labels' => $days->values(),
            'messages' => $days->map(fn ($d) => $messagesByDay[$d] ?? 0)->values(),
            'visits' => $days->map(fn ($d) => $visitsByDay[$d] ?? 0)->values(),
        ];

        $recentProjects = Project::with('category')->latest()->take(5)->get();
        $recentMessages = ContactSubmission::latest()->take(5)->get();

        return view('home', compact('stats', 'chart', 'recentProjects', 'recentMessages'));
    }
}
