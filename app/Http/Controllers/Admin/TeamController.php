<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Team;
use App\Services\MediaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Validation\Rule;

class TeamController extends Controller
{
    protected MediaService $mediaService;

    public function __construct(MediaService $mediaService)
    {
        $this->middleware(['auth', 'admin']);
        $this->mediaService = $mediaService;
    }

    /**
     * Display a listing of team members
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Team::query();
            
            return DataTables::of($query)
                ->addColumn('member_info', function ($team) {
                    $html = '<div class="d-flex align-items-center">';
                    if ($team->image) {
                        $html .= '<img src="' . $team->image . '" class="rounded-circle me-2" style="width: 40px; height: 40px; object-fit: cover;" alt="' . $team->name . '">';
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
                    $actions .= '<a href="' . route('teams.show', $team) . '" class="btn btn-sm btn-outline-info" title="View"><i class="bx bx-show"></i></a>';
                    $actions .= '<a href="' . route('teams.edit', $team) . '" class="btn btn-sm btn-outline-primary" title="Edit"><i class="bx bx-edit"></i></a>';
                    
                    // Toggle Active/Inactive
                    if ($team->is_active) {
                        $actions .= '<button class="btn btn-sm btn-outline-warning toggle-status" data-id="' . $team->id . '" data-status="inactive" title="Deactivate"><i class="bx bx-pause"></i></button>';
                    } else {
                        $actions .= '<button class="btn btn-sm btn-outline-success toggle-status" data-id="' . $team->id . '" data-status="active" title="Activate"><i class="bx bx-play"></i></button>';
                    }
                    
                    // Toggle Featured
                    if ($team->is_featured) {
                        $actions .= '<button class="btn btn-sm btn-outline-secondary toggle-featured" data-id="' . $team->id . '" data-featured="false" title="Remove from Featured"><i class="bx bx-star"></i></button>';
                    } else {
                        $actions .= '<button class="btn btn-sm btn-outline-warning toggle-featured" data-id="' . $team->id . '" data-featured="true" title="Mark as Featured"><i class="bx bx-star"></i></button>';
                    }
                    
                    $actions .= '<button class="btn btn-sm btn-outline-danger delete-team" data-id="' . $team->id . '" title="Delete"><i class="bx bx-trash"></i></button>';
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
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'department' => 'nullable|in:engineering,design,marketing,sales,hr,management,other',
            'bio' => 'nullable|string',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:255',
            'image' => 'nullable|image|max:1024',
            'linkedin' => 'nullable|url',
            'twitter' => 'nullable|url',
            'github' => 'nullable|url',
            'website' => 'nullable|url',
            'skills' => 'nullable|string',
            'join_date' => 'nullable|date',
            'employment_type' => 'required|in:full_time,part_time,contract,intern',
            'location' => 'nullable|string|max:255',
            'icon' => 'nullable|string|max:50',
            'color' => 'nullable|string|max:7|regex:/^#[a-fA-F0-9]{6}$/',
            'order' => 'nullable|integer|min:0',
            'is_featured' => 'boolean',
            'is_active' => 'boolean'
        ]);

        try {
            DB::beginTransaction();

            $data = $request->all();
            
            // Process skills from comma-separated string to array
            if ($request->skills) {
                $data['skills'] = array_map('trim', explode(',', $request->skills));
            }

            // Generate slug if not provided
            if (empty($data['slug'])) {
                $data['slug'] = Str::slug($data['name']);
                
                // Ensure unique slug
                $originalSlug = $data['slug'];
                $count = 1;
                while (Team::where('slug', $data['slug'])->exists()) {
                    $data['slug'] = $originalSlug . '-' . $count;
                    $count++;
                }
            }

            // Handle image upload
            if ($request->hasFile('image')) {
                $data['image'] = $this->mediaService->upload($request->file('image'), 'teams');
            }

            // Set defaults
            $data['icon'] = $data['icon'] ?: 'bx-user';
            $data['color'] = $data['color'] ?: '#007bff';
            $data['order'] = $data['order'] ?: Team::max('order') + 1;
            $data['is_featured'] = $request->boolean('is_featured');
            $data['is_active'] = $request->boolean('is_active', true);

            $team = Team::create($data);

            DB::commit();

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Team member added successfully!',
                    'redirect' => route('teams.index')
                ]);
            }

            return redirect()->route('teams.index')
                ->with('success', 'Team member added successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Team member creation failed: ' . $e->getMessage());

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to add team member. Please try again.'
                ], 500);
            }

            return back()->withInput()
                ->withErrors(['error' => 'Failed to add team member. Please try again.']);
        }
    }

    /**
     * Display the specified team member
     */
    public function show(Team $team)
    {
        return view('admin.teams.show', compact('team'));
    }

    /**
     * Show the form for editing the specified team member
     */
    public function edit(Team $team)
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

        // Convert skills array to comma-separated string for form
        $team->skills_string = is_array($team->skills) 
            ? implode(', ', $team->skills) 
            : '';

        return view('admin.teams.edit', compact('team', 'departments', 'employmentTypes'));
    }

    /**
     * Update the specified team member
     */
    public function update(Request $request, Team $team)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'department' => 'nullable|in:engineering,design,marketing,sales,hr,management,other',
            'bio' => 'nullable|string',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:255',
            'image' => 'nullable|image|max:1024',
            'linkedin' => 'nullable|url',
            'twitter' => 'nullable|url',
            'github' => 'nullable|url',
            'website' => 'nullable|url',
            'skills' => 'nullable|string',
            'join_date' => 'nullable|date',
            'employment_type' => 'required|in:full_time,part_time,contract,intern',
            'location' => 'nullable|string|max:255',
            'icon' => 'nullable|string|max:50',
            'color' => 'nullable|string|max:7|regex:/^#[a-fA-F0-9]{6}$/',
            'order' => 'nullable|integer|min:0',
            'is_featured' => 'boolean',
            'is_active' => 'boolean'
        ]);

        try {
            DB::beginTransaction();

            $data = $request->all();
            
            // Process skills from comma-separated string to array
            if ($request->skills) {
                $data['skills'] = array_map('trim', explode(',', $request->skills));
            } else {
                $data['skills'] = null;
            }

            // Update slug if name changed
            if ($data['name'] !== $team->name) {
                $data['slug'] = Str::slug($data['name']);
                
                // Ensure unique slug (excluding current record)
                $originalSlug = $data['slug'];
                $count = 1;
                while (Team::where('slug', $data['slug'])->where('id', '!=', $team->id)->exists()) {
                    $data['slug'] = $originalSlug . '-' . $count;
                    $count++;
                }
            }

            // Handle image upload
            if ($request->hasFile('image')) {
                // Delete old image
                if ($team->image) {
                    $this->mediaService->delete($team->image);
                }
                $data['image'] = $this->mediaService->upload($request->file('image'), 'teams');
            }

            // Set defaults and booleans
            $data['icon'] = $data['icon'] ?: 'bx-user';
            $data['color'] = $data['color'] ?: '#007bff';
            $data['is_featured'] = $request->boolean('is_featured');
            $data['is_active'] = $request->boolean('is_active', true);

            $team->update($data);

            DB::commit();

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Team member updated successfully!'
                ]);
            }

            return redirect()->route('teams.index')
                ->with('success', 'Team member updated successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Team member update failed: ' . $e->getMessage());

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update team member. Please try again.'
                ], 500);
            }

            return back()->withInput()
                ->withErrors(['error' => 'Failed to update team member. Please try again.']);
        }
    }

    /**
     * Remove the specified team member
     */
    public function destroy(Request $request, Team $team)
    {
        try {
            DB::beginTransaction();

            // Delete team member image
            if ($team->image) {
                $this->mediaService->delete($team->image);
            }

            $team->delete();

            DB::commit();

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Team member removed successfully!'
                ]);
            }

            return redirect()->route('teams.index')
                ->with('success', 'Team member removed successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Team member deletion failed: ' . $e->getMessage());

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to remove team member.'
                ], 500);
            }

            return back()->withErrors(['error' => 'Failed to remove team member.']);
        }
    }

    /**
     * Toggle team member status (active/inactive)
     */
    public function toggleStatus(Request $request, Team $team)
    {
        try {
            $team->update([
                'is_active' => !$team->is_active
            ]);

            $status = $team->is_active ? 'activated' : 'deactivated';

            return response()->json([
                'success' => true,
                'message' => "Team member {$status} successfully!",
                'is_active' => $team->is_active
            ]);

        } catch (\Exception $e) {
            Log::error('Team member status toggle failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to update team member status.'
            ], 500);
        }
    }

    /**
     * Toggle featured status
     */
    public function toggleFeatured(Request $request, Team $team)
    {
        try {
            $team->update([
                'is_featured' => !$team->is_featured
            ]);

            $status = $team->is_featured ? 'marked as featured' : 'removed from featured';

            return response()->json([
                'success' => true,
                'message' => "Team member {$status} successfully!",
                'is_featured' => $team->is_featured
            ]);

        } catch (\Exception $e) {
            Log::error('Team member featured toggle failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to update team member featured status.'
            ], 500);
        }
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

        try {
            DB::beginTransaction();

            foreach ($request->items as $item) {
                Team::where('id', $item['id'])->update(['order' => $item['order']]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Team members order updated successfully!'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Team members order update failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to update team members order.'
            ], 500);
        }
    }
}
