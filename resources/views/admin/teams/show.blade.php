@extends('layouts.app')

@section('title', 'View Team Member')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <!-- Header -->
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">
                        <span class="text-muted fw-light">Teams /</span> {{ $team->name }}
                    </h4>
                    <div class="d-flex gap-2">
                        <a href="{{ route('teams.edit', $team->id) }}" class="btn btn-primary">
                            <i class="bx bx-edit me-1"></i>Edit
                        </a>
                        <a href="{{ route('teams.index') }}" class="btn btn-outline-secondary">
                            <i class="bx bx-arrow-back me-1"></i>Back to Teams
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Main Profile Card -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <!-- Profile Header -->
                    <div class="d-flex align-items-start mb-4">
                        @if($team->image)
                            <img src="{{ asset($team->image) }}" alt="{{ $team->name }}" class="rounded-circle me-4" style="width: 120px; height: 120px; object-fit: cover;">
                        @else
                            <div class="avatar-initial rounded-circle bg-label-primary me-4 d-flex align-items-center justify-content-center" style="width: 120px; height: 120px; font-size: 32px;">
                                {{ $team->initials }}
                            </div>
                        @endif
                        <div class="flex-grow-1">
                            <h3 class="mb-1">{{ $team->name }}</h3>
                            <h5 class="text-muted mb-2">{{ $team->position }}</h5>
                            
                            @if($team->department)
                                <span class="badge bg-{{ $team->department_badge_color }} me-2">{{ ucfirst(str_replace('_', ' ', $team->department)) }}</span>
                            @endif
                            <span class="badge bg-{{ $team->employment_type_badge_color }}">{{ ucfirst(str_replace('_', ' ', $team->employment_type)) }}</span>
                            
                            <div class="mt-2">
                                @if($team->is_active)
                                    <span class="badge bg-label-success"><i class="bx bx-check-circle me-1"></i>Active</span>
                                @else
                                    <span class="badge bg-label-secondary"><i class="bx bx-x-circle me-1"></i>Inactive</span>
                                @endif
                                
                                @if($team->is_featured)
                                    <span class="badge bg-label-warning"><i class="bx bx-star me-1"></i>Featured</span>
                                @endif
                            </div>

                            @if($team->bio)
                                <p class="mt-3 text-muted">{{ $team->bio }}</p>
                            @endif
                        </div>
                    </div>

                    <!-- Contact Information -->
                    @if($team->email || $team->phone || $team->location)
                    <div class="row mb-4">
                        <div class="col-12">
                            <h5 class="mb-3">Contact Information</h5>
                        </div>
                        @if($team->email)
                        <div class="col-md-6 mb-3">
                            <div class="d-flex align-items-center">
                                <div class="badge bg-label-primary rounded p-2 me-3">
                                    <i class="bx bx-envelope bx-sm"></i>
                                </div>
                                <div>
                                    <small class="text-muted d-block">Email</small>
                                    <a href="mailto:{{ $team->email }}" class="text-primary">{{ $team->email }}</a>
                                </div>
                            </div>
                        </div>
                        @endif
                        @if($team->phone)
                        <div class="col-md-6 mb-3">
                            <div class="d-flex align-items-center">
                                <div class="badge bg-label-info rounded p-2 me-3">
                                    <i class="bx bx-phone bx-sm"></i>
                                </div>
                                <div>
                                    <small class="text-muted d-block">Phone</small>
                                    <span>{{ $team->phone }}</span>
                                </div>
                            </div>
                        </div>
                        @endif
                        @if($team->location)
                        <div class="col-md-6 mb-3">
                            <div class="d-flex align-items-center">
                                <div class="badge bg-label-warning rounded p-2 me-3">
                                    <i class="bx bx-map bx-sm"></i>
                                </div>
                                <div>
                                    <small class="text-muted d-block">Location</small>
                                    <span>{{ $team->location }}</span>
                                </div>
                            </div>
                        </div>
                        @endif
                        @if($team->join_date)
                        <div class="col-md-6 mb-3">
                            <div class="d-flex align-items-center">
                                <div class="badge bg-label-success rounded p-2 me-3">
                                    <i class="bx bx-calendar bx-sm"></i>
                                </div>
                                <div>
                                    <small class="text-muted d-block">Joined</small>
                                    <span>{{ $team->formatted_join_date }} ({{ $team->tenure }})</span>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                    @endif

                    <!-- Skills -->
                    @if($team->skills && count($team->skills) > 0)
                    <div class="row mb-4">
                        <div class="col-12">
                            <h5 class="mb-3">Skills & Expertise</h5>
                            <div>
                                @foreach($team->skills as $skill)
                                    <span class="badge bg-label-secondary me-1 mb-1">{{ $skill }}</span>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Social Links -->
                    @if(!empty($team->social_links))
                    <div class="row">
                        <div class="col-12">
                            <h5 class="mb-3">Social Links</h5>
                            <div class="d-flex gap-3">
                                @foreach($team->social_links as $platform => $url)
                                    @php
                                        $iconMap = [
                                            'linkedin' => 'bxl-linkedin',
                                            'twitter' => 'bxl-twitter', 
                                            'github' => 'bxl-github',
                                            'website' => 'bx-link-external'
                                        ];
                                        $colorMap = [
                                            'linkedin' => 'primary',
                                            'twitter' => 'info',
                                            'github' => 'dark',
                                            'website' => 'success'
                                        ];
                                        $icon = $iconMap[$platform] ?? 'bx-link';
                                        $color = $colorMap[$platform] ?? 'secondary';
                                    @endphp
                                    <a href="{{ $url }}" target="_blank" class="btn btn-outline-{{ $color }}" title="{{ ucfirst($platform) }}">
                                        <i class="bx {{ $icon }} me-1"></i>{{ ucfirst($platform) }}
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar Actions -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('teams.edit', $team->id) }}" class="btn btn-primary">
                            <i class="bx bx-edit me-1"></i>Edit Information
                        </a>
                        <button type="button" class="btn btn-outline-{{ $team->is_featured ? 'secondary' : 'warning' }}" 
                                data-ajax="{{ route('teams.toggle-featured', $team->id) }}" 
                                data-method="POST"
                                data-loading="Updating...">
                            <i class="bx bx-star me-1"></i>{{ $team->is_featured ? 'Remove Featured' : 'Mark Featured' }}
                        </button>
                        <button type="button" class="btn btn-outline-{{ $team->is_active ? 'warning' : 'success' }}" 
                                data-ajax="{{ route('teams.toggle-status', $team->id) }}" 
                                data-method="POST"
                                data-loading="Updating...">
                            <i class="bx bx-{{ $team->is_active ? 'pause' : 'play' }} me-1"></i>{{ $team->is_active ? 'Deactivate' : 'Activate' }}
                        </button>
                        <hr>
                        <button type="button" class="btn btn-outline-danger" 
                                data-ajax="{{ route('teams.destroy', $team->id) }}" 
                                data-method="DELETE"
                                data-confirm="Are you sure you want to delete this team member? This action cannot be undone."
                                data-loading="Deleting..."
                                data-on-success="window.location.href = '{{ route('teams.index') }}'">
                            <i class="bx bx-trash me-1"></i>Delete Member
                        </button>
                    </div>
                </div>
            </div>

            <!-- Team Statistics Card -->
            <div class="card mt-3">
                <div class="card-header">
                    <h5 class="mb-0">Member Details</h5>
                </div>
                <div class="card-body">
                    <div class="row g-2 text-center">
                        <div class="col-6">
                            <div class="badge bg-label-{{ $team->is_active ? 'success' : 'secondary' }} rounded p-3 w-100">
                                <div class="d-flex flex-column align-items-center">
                                    <i class="bx bx-{{ $team->is_active ? 'check-circle' : 'x-circle' }} bx-lg mb-1"></i>
                                    <small>{{ $team->is_active ? 'Active' : 'Inactive' }}</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="badge bg-label-{{ $team->is_featured ? 'warning' : 'secondary' }} rounded p-3 w-100">
                                <div class="d-flex flex-column align-items-center">
                                    <i class="bx bx-star bx-lg mb-1"></i>
                                    <small>{{ $team->is_featured ? 'Featured' : 'Regular' }}</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mt-3">
                        <div class="col-12">
                            <div class="d-flex justify-content-between align-items-center py-2">
                                <span class="text-muted">Display Order:</span>
                                <span class="badge bg-label-info">{{ $team->order }}</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center py-2">
                                <span class="text-muted">Member Since:</span>
                                <span>{{ $team->formatted_join_date ?: 'N/A' }}</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center py-2">
                                <span class="text-muted">Last Updated:</span>
                                <span>{{ $team->updated_at->format('M d, Y') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

