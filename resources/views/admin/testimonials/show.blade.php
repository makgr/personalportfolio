@extends('layouts.app')

@section('title', 'View Testimonial')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <!-- Header -->
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">
                        <span class="text-muted fw-light">Portfolio / Testimonials /</span> {{ $testimonial->client_name }}
                    </h4>
                    <div class="d-flex gap-2">
                        <a href="{{ route('admin.testimonials.edit', $testimonial->id) }}" class="btn btn-primary">
                            <i class="bx bx-edit me-1"></i>Edit
                        </a>
                        <a href="{{ route('admin.testimonials.index') }}" class="btn btn-outline-secondary">
                            <i class="bx bx-arrow-back me-1"></i>Back to Testimonials
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Main Content -->
        <div class="col-lg-8">
            <!-- Testimonial Content -->
            <div class="card mb-4">
                <div class="card-header">
                    <div class="d-flex align-items-center justify-content-between">
                        <h5 class="card-title mb-0">Testimonial Details</h5>
                        <div class="d-flex gap-2">
                            @if($testimonial->is_featured)
                                <span class="badge bg-warning">Featured</span>
                            @endif
                            <span class="badge bg-{{ $testimonial->is_active ? 'success' : 'secondary' }}">
                                {{ $testimonial->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Client Information -->
                    <div class="d-flex align-items-start mb-4">
                        @if($testimonial->client_image)
                            <img src="{{ $testimonial->client_image }}" alt="{{ $testimonial->client_name }}" 
                                 class="rounded-circle me-4" style="width: 80px; height: 80px; object-fit: cover;">
                        @else
                            <div class="avatar-initial rounded-circle bg-label-primary me-4 d-flex align-items-center justify-content-center" 
                                 style="width: 80px; height: 80px; font-size: 24px; font-weight: 600;">
                                {{ substr($testimonial->client_name, 0, 1) }}
                            </div>
                        @endif
                        
                        <div class="flex-grow-1">
                            <h4 class="mb-1">{{ $testimonial->client_name }}</h4>
                            
                            @if($testimonial->client_position || $testimonial->client_company)
                                <p class="text-muted mb-2">
                                    @if($testimonial->client_position && $testimonial->client_company)
                                        {{ $testimonial->client_position }} at {{ $testimonial->client_company }}
                                    @elseif($testimonial->client_position)
                                        {{ $testimonial->client_position }}
                                    @else
                                        {{ $testimonial->client_company }}
                                    @endif
                                </p>
                            @endif
                            
                            @if($testimonial->client_linkedin)
                                <a href="{{ $testimonial->client_linkedin }}" target="_blank" class="btn btn-outline-primary btn-sm">
                                    <i class="bx bxl-linkedin me-1"></i>View LinkedIn Profile
                                </a>
                            @endif
                        </div>
                    </div>

                    <!-- Rating -->
                    @if($testimonial->rating)
                        <div class="mb-4">
                            <h6>Rating</h6>
                            <div class="d-flex align-items-center">
                                <div class="rating me-2" style="font-size: 1.5rem; color: #ffab00;">
                                    {!! str_repeat('★', $testimonial->rating) !!}{!! str_repeat('☆', 5 - $testimonial->rating) !!}
                                </div>
                                <span class="text-muted">({{ $testimonial->rating }}/5)</span>
                            </div>
                        </div>
                    @endif

                    <!-- Testimonial Content -->
                    <div class="mb-4">
                        <h6>Testimonial</h6>
                        <blockquote class="blockquote">
                            <p class="mb-0 fst-italic">"{{ $testimonial->content }}"</p>
                        </blockquote>
                    </div>

                    <!-- Related Project -->
                    @if($testimonial->project)
                        <div class="mb-4">
                            <h6>Related Project</h6>
                            <div class="d-flex align-items-center">
                                <div class="me-3">
                                    @if($testimonial->project->featured_image)
                                        <img src="{{ $testimonial->project->featured_image }}" alt="{{ $testimonial->project->title }}"
                                             class="rounded" style="width: 48px; height: 48px; object-fit: cover;">
                                    @else
                                        <div class="bg-label-primary rounded d-flex align-items-center justify-content-center"
                                             style="width: 48px; height: 48px;">
                                            <i class="bx bx-folder"></i>
                                        </div>
                                    @endif
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-1">
                                        <a href="{{ route('admin.projects.show', $testimonial->project->id) }}" class="text-decoration-none">
                                            {{ $testimonial->project->title }}
                                        </a>
                                    </h6>
                                    @if($testimonial->project->description)
                                        <p class="text-muted small mb-0">
                                            {{ Str::limit($testimonial->project->description, 100) }}
                                        </p>
                                    @endif
                                </div>
                                <a href="{{ route('admin.projects.show', $testimonial->project->id) }}" 
                                   class="btn btn-outline-primary btn-sm">
                                    <i class="bx bx-link-external me-1"></i>View Project
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Testimonial Timeline -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Activity Timeline</h5>
                </div>
                <div class="card-body">
                    <ul class="timeline">
                        <li class="timeline-item timeline-item-transparent">
                            <span class="timeline-point timeline-point-success"></span>
                            <div class="timeline-event">
                                <div class="timeline-header mb-1">
                                    <h6 class="mb-0">Testimonial Created</h6>
                                    <small class="text-muted">{{ $testimonial->created_at->format('M d, Y \a\t g:i A') }}</small>
                                </div>
                                <p class="mb-2">Initial testimonial entry was created</p>
                            </div>
                        </li>
                        
                        @if($testimonial->created_at != $testimonial->updated_at)
                        <li class="timeline-item timeline-item-transparent">
                            <span class="timeline-point timeline-point-info"></span>
                            <div class="timeline-event">
                                <div class="timeline-header mb-1">
                                    <h6 class="mb-0">Last Updated</h6>
                                    <small class="text-muted">{{ $testimonial->updated_at->format('M d, Y \a\t g:i A') }}</small>
                                </div>
                                <p class="mb-2">Testimonial information was modified</p>
                            </div>
                        </li>
                        @endif
                        
                        @if($testimonial->is_featured)
                        <li class="timeline-item timeline-item-transparent">
                            <span class="timeline-point timeline-point-warning"></span>
                            <div class="timeline-event">
                                <div class="timeline-header mb-1">
                                    <h6 class="mb-0">Featured Status</h6>
                                </div>
                                <p class="mb-2">This testimonial is marked as featured</p>
                            </div>
                        </li>
                        @endif
                        
                        <li class="timeline-item timeline-item-transparent">
                            <span class="timeline-point timeline-point-{{ $testimonial->is_active ? 'success' : 'secondary' }}"></span>
                            <div class="timeline-event">
                                <div class="timeline-header mb-1">
                                    <h6 class="mb-0">Current Status</h6>
                                </div>
                                <p class="mb-2">
                                    Testimonial is currently 
                                    <strong class="text-{{ $testimonial->is_active ? 'success' : 'muted' }}">
                                        {{ $testimonial->is_active ? 'active and visible' : 'inactive' }}
                                    </strong>
                                </p>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Quick Actions -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="card-title mb-0">Quick Actions</h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.testimonials.edit', $testimonial->id) }}" class="btn btn-primary">
                            <i class="bx bx-edit me-1"></i>Edit Testimonial
                        </a>
                        
                        <button type="button" class="btn btn-outline-{{ $testimonial->is_featured ? 'warning' : 'secondary' }}" 
                                onclick="toggleFeatured({{ $testimonial->id }})">
                            <i class="bx bx-star me-1"></i>
                            {{ $testimonial->is_featured ? 'Remove from Featured' : 'Mark as Featured' }}
                        </button>
                        
                        <hr>
                        
                        <button type="button" class="btn btn-outline-danger" onclick="deleteTestimonial({{ $testimonial->id }})">
                            <i class="bx bx-trash me-1"></i>Delete Testimonial
                        </button>
                    </div>
                </div>
            </div>

            <!-- Statistics -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="card-title mb-0">Statistics</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span>Order Position:</span>
                        <span class="badge bg-label-info">{{ $testimonial->order }}</span>
                    </div>
                    
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span>Character Count:</span>
                        <span class="badge bg-label-secondary">{{ strlen($testimonial->content) }}</span>
                    </div>
                    
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span>Word Count:</span>
                        <span class="badge bg-label-secondary">{{ str_word_count($testimonial->content) }}</span>
                    </div>
                    
                    @if($testimonial->client_image)
                    <div class="d-flex align-items-center justify-content-between">
                        <span>Has Photo:</span>
                        <span class="badge bg-label-success">Yes</span>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Metadata -->
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title mb-0">Metadata</h6>
                </div>
                <div class="card-body">
                    <div class="row g-2">
                        <div class="col-6">
                            <div class="d-flex flex-column">
                                <span class="text-muted small">ID</span>
                                <span class="fw-semibold">{{ $testimonial->id }}</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="d-flex flex-column">
                                <span class="text-muted small">Order</span>
                                <span class="fw-semibold">{{ $testimonial->order }}</span>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="d-flex flex-column">
                                <span class="text-muted small">Created At</span>
                                <span class="fw-semibold">{{ $testimonial->created_at->format('M d, Y g:i A') }}</span>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="d-flex flex-column">
                                <span class="text-muted small">Updated At</span>
                                <span class="fw-semibold">{{ $testimonial->updated_at->format('M d, Y g:i A') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Toggle featured status
function toggleFeatured(id) {
    const currentStatus = {{ $testimonial->is_featured ? 'true' : 'false' }};
    const action = currentStatus ? 'remove from featured' : 'mark as featured';
    
    if (confirm(`Are you sure you want to ${action} this testimonial?`)) {
        performToggleFeatured(id);
    }
}

function performToggleFeatured(id) {
    fetch('{{ route("admin.testimonials.toggle-featured", $testimonial->id) }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            toastr.success(data.message);
            setTimeout(() => {
                location.reload();
            }, 1000);
        } else {
            toastr.error(data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        toastr.error('Network error occurred');
    });
}

// Delete testimonial
function deleteTestimonial(id) {
    if (confirm('Are you sure you want to delete this testimonial? This action cannot be undone.')) {
        performDelete(id);
    }
}

function performDelete(id) {
    fetch('{{ route("admin.testimonials.destroy", $testimonial->id) }}', {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            toastr.success(data.message);
            setTimeout(() => {
                window.location.href = '{{ route("admin.testimonials.index") }}';
            }, 1500);
        } else {
            toastr.error(data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        toastr.error('Network error occurred');
    });
}
</script>
@endpush
@endsection