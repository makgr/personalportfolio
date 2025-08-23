<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\TeamService;
use App\Repositories\Contracts\TeamRepositoryInterface;
use App\Http\Requests\Admin\TeamRequest;
use App\Models\Team;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class TeamController extends Controller
{
    protected TeamService $teamService;
    protected TeamRepositoryInterface $teamRepository;

    public function __construct(
        TeamService $teamService,
        TeamRepositoryInterface $teamRepository
    ) {
        $this->middleware(['auth', 'admin']);
        $this->teamService = $teamService;
        $this->teamRepository = $teamRepository;
    }

    /**
     * Display a listing of team members
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Team::query()->orderBy('order', 'asc');
            
            return DataTables::of($query)
                ->addColumn('member_info', function ($team) {
                    $html = '<div class="d-flex align-items-center">';
                    if ($team->image) {
                        $html .= '<img src="' . asset($team->image) . '" class="rounded-circle me-2" style="width: 40px; height: 40px; object-fit: cover;" alt="' . $team->name . '">';
                    } else {
                        $html .= '<div class="avatar-initial rounded-circle bg-label-primary me-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; font-size: 14px;">' . $team->initials . '</div>';
                    }
                    $html .= '<div>';
                    $html .= '<strong>' . $team->name . '</strong>';
                    $html .= '<br><small class="text-muted">' . $team->position . '</small>';
                    $html .= '</div></div>';
                    return $html;
                })
                ->addColumn('department_badge', function ($team) {
                    if (!$team->department) {
                        return '<span class="text-muted">-</span>';
                    }
                    $color = $team->department_badge_color;
                    $department = ucfirst(str_replace('_', ' ', $team->department));
                    return '<span class="badge bg-' . $color . '">' . $department . '</span>';
                })
                ->addColumn('employment_badge', function ($team) {
                    $color = $team->employment_type_badge_color;
                    $type = ucfirst(str_replace('_', ' ', $team->employment_type));
                    return '<span class="badge bg-' . $color . '">' . $type . '</span>';
                })
                ->addColumn('contact_info', function ($team) {
                    $html = '';
                    if ($team->email) {
                        $html .= '<a href="mailto:' . $team->email . '" class="text-primary"><i class="bx bx-envelope"></i> ' . $team->email . '</a>';
                    }
                    if ($team->phone) {
                        $html .= ($html ? '<br>' : '') . '<span class="text-muted"><i class="bx bx-phone"></i> ' . $team->phone . '</span>';
                    }
                    return $html ?: '<span class="text-muted">-</span>';
                })
                ->addColumn('social_links', function ($team) {
                    $html = '';
                    $socialLinks = $team->social_links;
                    if (!empty($socialLinks)) {
                        foreach ($socialLinks as $platform => $url) {
                            $icon = match($platform) {
                                'linkedin' => 'bxl-linkedin',
                                'twitter' => 'bxl-twitter',
                                'github' => 'bxl-github',
                                'website' => 'bx-link-external',
                                default => 'bx-link'
                            };
                            $html .= '<a href="' . $url . '" target="_blank" class="text-primary me-2" title="' . ucfirst($platform) . '"><i class="bx ' . $icon . '"></i></a>';
                        }
                    }
                    return $html ?: '<span class="text-muted">-</span>';
                })
                ->addColumn('join_info', function ($team) {
                    $html = $team->formatted_join_date;
                    $html .= '<br><small class="text-muted">' . $team->tenure . '</small>';
                    return $html;
                })
                ->addColumn('status_badges', function ($team) {
                    $badges = '<span class="badge bg-label-' . ($team->is_active ? 'success' : 'secondary') . '">' . ($team->is_active ? 'Active' : 'Inactive') . '</span>';
                    if ($team->is_featured) {
                        $badges .= '<br><span class="badge bg-label-warning"><i class="bx bx-star"></i> Featured</span>';
                    }
                    return $badges;
                })
                ->addColumn('action', function ($team) {
                    $actions = '<div class="btn-group" role="group">';
                    $actions .= '<a href="' . route('teams.show', $team->id) . '" class="btn btn-sm btn-outline-info" title="View"><i class="bx bx-show"></i></a>';
                    $actions .= '<a href="' . route('teams.edit', $team->id) . '" class="btn btn-sm btn-outline-primary" title="Edit"><i class="bx bx-edit"></i></a>';
                    
                    // Toggle Active/Inactive
                    if ($team->is_active) {
                        $actions .= '<button class="btn btn-sm btn-outline-warning" data-ajax="' . route('teams.toggle-status', $team->id) . '" data-method="POST" data-loading="Updating..." title="Deactivate"><i class="bx bx-pause"></i></button>';
                    } else {
                        $actions .= '<button class="btn btn-sm btn-outline-success" data-ajax="' . route('teams.toggle-status', $team->id) . '" data-method="POST" data-loading="Updating..." title="Activate"><i class="bx bx-play"></i></button>';
                    }
                    
                    // Toggle Featured
                    if ($team->is_featured) {
                        $actions .= '<button class="btn btn-sm btn-outline-secondary" data-ajax="' . route('teams.toggle-featured', $team->id) . '" data-method="POST" data-loading="Updating..." title="Remove from Featured"><i class="bx bx-star"></i></button>';
                    } else {
                        $actions .= '<button class="btn btn-sm btn-outline-warning" data-ajax="' . route('teams.toggle-featured', $team->id) . '" data-method="POST" data-loading="Updating..." title="Mark as Featured"><i class="bx bx-star"></i></button>';
                    }
                    
                    $actions .= '<button class="btn btn-sm btn-outline-danger" data-ajax="' . route('teams.destroy', $team->id) . '" data-method="DELETE" data-confirm="Are you sure you want to delete this team member?" data-loading="Deleting..." data-on-success="location.reload()" title="Delete"><i class="bx bx-trash"></i></button>';
                    $actions .= '</div>';
                    
                    return $actions;
                })
                ->rawColumns(['member_info', 'department_badge', 'employment_badge', 'contact_info', 'social_links', 'join_info', 'status_badges', 'action'])
                ->make(true);
        }

        $stats = [
            'total' => Team::count(),
            'active' => Team::where('is_active', true)->count(), 
            'featured' => Team::where('is_featured', true)->count(),
            'full_time' => Team::where('employment_type', 'full_time')->count(),
        ];

        return view('admin.teams.index', compact('stats'));
    }

    /**
     * Show the form for creating a new team member
     */
    public function create()
    {
        $departments = [
            'engineering' => 'Engineering',
            'design' => 'Design',
            'marketing' => 'Marketing',
            'sales' => 'Sales',
            'hr' => 'Human Resources',
            'management' => 'Management',
            'other' => 'Other'
        ];

        $employmentTypes = [
            'full_time' => 'Full Time',
            'part_time' => 'Part Time',
            'contract' => 'Contract',
            'intern' => 'Intern'
        ];

        return view('admin.teams.create', compact('departments', 'employmentTypes'));
    }

    /**
     * Store a newly created team member
     */
    public function store(TeamRequest $request)
    {
        $data = $request->validated();
        $data['image'] = $request->file('image');
        
        $result = $this->teamService->createTeam($data);

        if ($request->ajax()) {
            if ($result['success']) {
                return response()->json([
                    'success' => true,
                    'message' => $result['message'],
                    'redirect' => route('teams.index')
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => $result['message']
            ], 422);
        }

        if ($result['success']) {
            return redirect()->route('teams.index')
                ->with('success', $result['message']);
        }

        return back()->withInput()
            ->withErrors(['error' => $result['message']]);
    }

    /**
     * Display the specified team member
     */
    public function show($team)
    {
        $teamId = is_object($team) ? $team->id : $team;
        $team = $this->teamRepository->find($teamId);
        if (!$team) {
            return redirect()->route('teams.index')
                ->with('error', 'Team member not found');
        }
        return view('admin.teams.show', compact('team'));
    }

    /**
     * Show the form for editing the specified team member
     */
    public function edit($team)
    {
        $teamId = is_object($team) ? $team->id : $team;
        $team = $this->teamRepository->find($teamId);
        if (!$team) {
            return redirect()->route('teams.index')
                ->with('error', 'Team member not found');
        }
        $departments = [
            'engineering' => 'Engineering',
            'design' => 'Design',
            'marketing' => 'Marketing',
            'sales' => 'Sales',
            'hr' => 'Human Resources',
            'management' => 'Management',
            'other' => 'Other'
        ];

        $employmentTypes = [
            'full_time' => 'Full Time',
            'part_time' => 'Part Time',
            'contract' => 'Contract',
            'intern' => 'Intern'
        ];

        // Convert skills array to comma-separated string for form
        $team->skills_string = is_array($team->skills) 
            ? implode(', ', $team->skills) 
            : '';

        return view('admin.teams.edit', compact('team', 'departments', 'employmentTypes'));
    }

    /**
     * Update the specified team member
     */
    public function update(TeamRequest $request, $team)
    {
        $teamId = is_object($team) ? $team->id : $team;
        $data = $request->validated();
        $data['image'] = $request->file('image');
        
        $result = $this->teamService->updateTeam($teamId, $data);

        if ($request->ajax()) {
            if ($result['success']) {
                return response()->json([
                    'success' => true,
                    'message' => $result['message'],
                    'redirect' => route('teams.index')
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => $result['message']
            ], 422);
        }

        if ($result['success']) {
            return redirect()->route('teams.index')
                ->with('success', $result['message']);
        }

        return back()->withInput()
            ->withErrors(['error' => $result['message']]);
    }

    /**
     * Remove the specified team member
     */
    public function destroy(Request $request, $team)
    {
        // Handle both model binding (Team object) and direct ID
        $teamId = is_object($team) ? $team->id : $team;
        $result = $this->teamService->deleteTeam($teamId);

        if ($request->ajax()) {
            if ($result['success']) {
                return response()->json([
                    'success' => true,
                    'message' => $result['message']
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => $result['message']
            ], 500);
        }

        if ($result['success']) {
            return redirect()->route('teams.index')
                ->with('success', $result['message']);
        }

        return back()->withErrors(['error' => $result['message']]);
    }

    /**
     * Toggle team member status (active/inactive)
     */
    public function toggleStatus($id)
    {
        $result = $this->teamRepository->toggleStatus($id);
        
        if ($result) {
            $team = $this->teamRepository->find($id);
            $status = $team->is_active ? 'activated' : 'deactivated';
            return response()->json([
                'success' => true,
                'message' => "Team member {$status} successfully!",
                'is_active' => $team->is_active
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Failed to update team member status.'
        ], 500);
    }

    /**
     * Toggle featured status
     */
    public function toggleFeatured($id)
    {
        $result = $this->teamRepository->toggleFeatured($id);
        
        if ($result) {
            $team = $this->teamRepository->find($id);
            $status = $team->is_featured ? 'marked as featured' : 'removed from featured';
            return response()->json([
                'success' => true,
                'message' => "Team member {$status} successfully!",
                'is_featured' => $team->is_featured
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Failed to update team member featured status.'
        ], 500);
    }

    /**
     * Update team members order
     */
    public function updateOrder(Request $request)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|exists:teams,id',
            'items.*.order' => 'required|integer|min:0'
        ]);

        $result = $this->teamRepository->updateOrder($request->items);
        
        if ($result) {
            return response()->json([
                'success' => true,
                'message' => 'Team members order updated successfully!'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Failed to update team members order.'
        ], 500);
    }
}
