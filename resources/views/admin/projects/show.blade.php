@extends('layouts.app')

@section('title', $project->title . ' - Project Details')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <!-- Header -->
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <span class="text-muted fw-light">Portfolio / Projects /</span> {{ $project->title }}
                    </h5>
                    <div class="d-flex gap-2">
                        @if($project->project_url)
                            <a href="{{ $project->project_url }}" target="_blank" class="btn btn-outline-success btn-sm">
                                <i class="bx bx-link-external me-1"></i>Live Demo
                            </a>
                        @endif
                        @if($project->github_url)
                            <a href="{{ $project->github_url }}" target="_blank" class="btn btn-outline-dark btn-sm">
                                <i class="bx bxl-github me-1"></i>GitHub
                            </a>
                        @endif
                        <a href="{{ route('projects.edit', $project->id) }}" class="btn btn-primary btn-sm">
                            <i class="bx bx-edit me-1"></i>Edit
                        </a>
                        <a href="{{ route('projects.index') }}" class="btn btn-secondary">
                            <i class="bx bx-arrow-back me-1"></i>Back to Projects
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Main Content -->
        <div class="col-md-8">
            <!-- Featured Image -->
            @if($project->featured_image)
                <div class="card mb-4">
                    <div class="card-body p-0">
                        <img src="{{ asset($project->featured_image) }}" 
                             alt="{{ $project->title }}" 
                             class="img-fluid w-100" 
                             style="max-height: 400px; object-fit: cover;">
                    </div>
                </div>
            @endif

            <!-- Project Information -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Project Information</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-sm-3 fw-medium">Title:</div>
                        <div class="col-sm-9">{{ $project->title }}</div>
                    </div>
                    
                    @if($project->slug)
                        <div class="row mb-3">
                            <div class="col-sm-3 fw-medium">URL Slug:</div>
                            <div class="col-sm-9">
                                <code>{{ $project->slug }}</code>
                                <small class="text-muted d-block">{{ url('/projects/' . $project->slug) }}</small>
                            </div>
                        </div>
                    @endif

                    <div class="row mb-3">
                        <div class="col-sm-3 fw-medium">Short Description:</div>
                        <div class="col-sm-9">{{ $project->short_description }}</div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-sm-3 fw-medium">Description:</div>
                        <div class="col-sm-9">
                            <div class="bg-light p-3 rounded">
                                {{ $project->description }}
                            </div>
                        </div>
                    </div>

                    <!-- Project URLs -->
                    @if($project->project_url || $project->github_url || $project->demo_url)
                        <div class="row mb-3">
                            <div class="col-sm-3 fw-medium">Links:</div>
                            <div class="col-sm-9">
                                @if($project->project_url)
                                    <div class="mb-1">
                                        <i class="bx bx-link text-success me-1"></i>
                                        <a href="{{ $project->project_url }}" target="_blank">Live Project</a>
                                    </div>
                                @endif
                                @if($project->github_url)
                                    <div class="mb-1">
                                        <i class="bx bxl-github text-dark me-1"></i>
                                        <a href="{{ $project->github_url }}" target="_blank">GitHub Repository</a>
                                    </div>
                                @endif
                                @if($project->demo_url)
                                    <div class="mb-1">
                                        <i class="bx bx-video text-info me-1"></i>
                                        <a href="{{ $project->demo_url }}" target="_blank">Demo</a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Technologies & Features -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Technologies & Features</h5>
                </div>
                <div class="card-body">
                    @if($project->tech_stack && count($project->tech_stack) > 0)
                        <div class="row mb-3">
                            <div class="col-sm-3 fw-medium">Technologies:</div>
                            <div class="col-sm-9">
                                @foreach($project->tech_stack as $tech)
                                    <span class="badge bg-primary me-1 mb-1">{{ $tech }}</span>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if($project->features && count($project->features) > 0)
                        <div class="row mb-3">
                            <div class="col-sm-3 fw-medium">Key Features:</div>
                            <div class="col-sm-9">
                                <ul class="list-unstyled mb-0">
                                    @foreach($project->features as $feature)
                                        <li class="mb-1">
                                            <i class="bx bx-check text-success me-1"></i>{{ $feature }}
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Gallery Images -->
            @if($project->media && $project->media->count() > 0)
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Project Gallery</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            @foreach($project->media as $media)
                                <div class="col-md-6">
                                    <div class="position-relative">
                                        <img src="{{ asset($media->path) }}" 
                                             alt="Gallery image" 
                                             class="img-fluid rounded shadow-sm"
                                             style="cursor: pointer;"
                                             data-bs-toggle="modal" 
                                             data-bs-target="#imageModal"
                                             data-bs-image="{{ asset($media->path) }}">
                                        @if($media->caption)
                                            <div class="mt-1">
                                                <small class="text-muted">{{ $media->caption }}</small>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="col-md-4">
            <!-- Status Information -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Project Status</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span>Status:</span>
                        <span class="badge bg-{{ $project->status == 'published' ? 'success' : ($project->status == 'draft' ? 'warning' : 'secondary') }}">
                            {{ ucfirst($project->status) }}
                        </span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span>Views:</span>
                        <span class="badge bg-info">{{ number_format($project->views) }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span>Featured:</span>
                        <span class="badge bg-{{ $project->is_featured ? 'success' : 'secondary' }}">
                            {{ $project->is_featured ? 'Yes' : 'No' }}
                        </span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span>Created:</span>
                        <span>{{ $project->created_at->format('M d, Y') }}</span>
                    </div>
                </div>
            </div>

            <!-- Project Details -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Project Details</h5>
                </div>
                <div class="card-body">
                    @if($project->category)
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span>Category:</span>
                            <span class="badge bg-secondary">{{ $project->category->name }}</span>
                        </div>
                    @endif

                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span>Type:</span>
                        <span class="text-capitalize">{{ $project->type }}</span>
                    </div>

                    @if($project->client_name)
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span>Client:</span>
                            <span>{{ $project->client_name }}</span>
                        </div>
                    @endif

                    @if($project->duration_weeks)
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span>Duration:</span>
                            <span>{{ $project->duration_weeks }} weeks</span>
                        </div>
                    @endif

                    @if($project->my_role)
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span>My Role:</span>
                            <span>{{ $project->my_role }}</span>
                        </div>
                    @endif

                    @if($project->project_date)
                        <div class="d-flex justify-content-between align-items-center">
                            <span>Project Date:</span>
                            <span>{{ $project->project_date->format('M Y') }}</span>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Actions -->
            <div class="card">
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('projects.edit', $project->id) }}" class="btn btn-primary">
                            <i class="bx bx-edit me-1"></i>Edit Project
                        </a>
                        
                        @if($project->status !== 'published')
                            <button type="button" class="btn btn-success" onclick="publishProject({{ $project->id }})">
                                <i class="bx bx-check me-1"></i>Publish
                            </button>
                        @else
                            <button type="button" class="btn btn-warning" onclick="unpublishProject({{ $project->id }})">
                                <i class="bx bx-x me-1"></i>Unpublish
                            </button>
                        @endif

                        <button type="button" class="btn btn-danger" onclick="deleteProject({{ $project->id }})">
                            <i class="bx bx-trash me-1"></i>Delete Project
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Image Modal -->
<div class="modal fade" id="imageModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Project Image</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <img id="modalImage" src="" alt="Project image" class="img-fluid">
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Handle image modal
    $('#imageModal').on('show.bs.modal', function (event) {
        const button = $(event.relatedTarget);
        const imageSrc = button.data('bs-image');
        $('#modalImage').attr('src', imageSrc);
    });
});

function publishProject(id) {
    obConfirm('Are you sure you want to publish this project?').then(function (ok) {
        if (!ok) return;
        $.post(`/projects/${id}/publish`, {
            _token: $('meta[name="csrf-token"]').attr('content')
        })
        .done(function(response) {
            if (response.success) {
                toastr.success(response.message);
                setTimeout(() => location.reload(), 1000);
            } else {
                toastr.error(response.message);
            }
        })
        .fail(function() {
            toastr.error('Failed to publish project');
        });
    });
}

function unpublishProject(id) {
    obConfirm('Are you sure you want to unpublish this project?').then(function (ok) {
        if (!ok) return;
        $.post(`/projects/${id}/unpublish`, {
            _token: $('meta[name="csrf-token"]').attr('content')
        })
        .done(function(response) {
            if (response.success) {
                toastr.success(response.message);
                setTimeout(() => location.reload(), 1000);
            } else {
                toastr.error(response.message);
            }
        })
        .fail(function() {
            toastr.error('Failed to unpublish project');
        });
    });
}

function deleteProject(id) {
    obConfirm('Are you sure you want to delete this project? This action cannot be undone.').then(function (ok) {
        if (!ok) return;
        $.ajax({
            url: `/projects/${id}`,
            type: 'DELETE',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content')
            }
        })
        .done(function(response) {
            if (response.success) {
                toastr.success(response.message);
                setTimeout(() => window.location.href = '/projects', 1000);
            } else {
                toastr.error(response.message);
            }
        })
        .fail(function() {
            toastr.error('Failed to delete project');
        });
    });
}
</script>
@endpush