<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

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
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        // Get portfolio statistics
        $totalProjects = \App\Models\Project::count();
        $totalBlogs = \App\Models\BlogPost::count();
        $totalSkills = \App\Models\Skill::count();
        $totalServices = \App\Models\Service::count();
        $totalExperience = \App\Models\Experience::count();
        $totalMessages = \App\Models\ContactSubmission::count();

        return view('home', compact(
            'totalProjects',
            'totalBlogs', 
            'totalSkills',
            'totalServices',
            'totalExperience',
            'totalMessages'
        ));
    }
}
