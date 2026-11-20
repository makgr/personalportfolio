@extends('layouts.app')

@section('title', 'Training Details')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <!-- Header -->
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <span class="text-muted fw-light">Portfolio / Training /</span> {{ $training->title }}
                    </h5>
                    <div class="d-flex gap-2">
                        <a href="{{ route('admin.training.edit', $training) }}" class="btn btn-primary">
                            <i class="bx bx-edit me-1"></i>Edit Training
                        </a>
                        <a href="{{ route('admin.training.index') }}" class="btn btn-secondary">
                            <i class="bx bx-arrow-back me-1"></i>Back to Training
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Training Details -->
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <!-- Training Header -->
                    <div class="d-flex align-items-start mb-4">
                        <div class="me-4">
                            <div class="training-icon">
                                <i class="{{ $training->icon }}" style="font-size: 3rem; color: {{ $training->color }};"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <h3 class="mb-2">{{ $training->title }}</h3>
                            <h6 class="text-muted mb-3">{{ $training->provider }}</h6>
                            
                            <div class="mb-3">
                                <span class="badge bg-{{ $training->type_badge_color }} me-2">{{ ucfirst($training->type) }}</span>
                                <span class="badge bg-{{ $training->category_badge_color }} me-2">{{ ucfirst(str_replace('_', ' ', $training->category)) }}</span>
                                <span class="badge bg-{{ $training->status_badge_color }} me-2">{{ ucfirst(str_replace('_', ' ', $training->status)) }}</span>
                                @if($training->is_featured)
                                    <span class="badge bg-warning"><i class="bx bx-star"></i> Featured</span>
                                @endif
                                @if(!$training->is_active)
                                    <span class="badge bg-secondary">Inactive</span>
                                @endif
                            </div>

                            @if($training->rating)
                                <div class="mb-2">
                                    {!! $training->rating_stars !!}
                                    <span class="text-muted ms-2">({{ $training->rating }}/5)</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Training Information -->
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="fw-bold mb-3">Training Details</h6>
                            <div class="info-group">
                                <div class="info-item mb-2">
                                    <strong>Start Date:</strong>
                                    <span class="ms-2">{{ $training->start_date ? $training->start_date->format('F j, Y') : 'Not specified' }}</span>
                                </div>
                                
                                <div class="info-item mb-2">
                                    <strong>End Date:</strong>
                                    <span class="ms-2">{{ $training->end_date ? $training->end_date->format('F j, Y') : 'Ongoing' }}</span>
                                </div>
                                
                                <div class="info-item mb-2">
                                    <strong>Duration:</strong>
                                    <span class="ms-2">{{ $training->formatted_duration }}</span>
                                </div>
                                
                                @if($training->instructor)
                                    <div class="info-item mb-2">
                                        <strong>Instructor:</strong>
                                        <span class="ms-2">{{ $training->instructor }}</span>
                                    </div>
                                @endif

                                @if($training->score)
                                    <div class="info-item mb-2">
                                        <strong>Score:</strong>
                                        <span class="ms-2">{{ $training->score }}%</span>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="col-md-6">
                            <h6 class="fw-bold mb-3">Credentials</h6>
                            <div class="info-group">
                                @if($training->credential_id)
                                    <div class="info-item mb-2">
                                        <strong>Credential ID:</strong>
                                        <span class="ms-2">{{ $training->credential_id }}</span>
                                    </div>
                                @endif
                                
                                @if($training->certificate_url)
                                    <div class="info-item mb-2">
                                        <strong>Certificate:</strong>
                                        <a href="{{ $training->certificate_url }}" target="_blank" class="ms-2 text-primary">
                                            <i class="bx bx-link-external me-1"></i>View Certificate
                                        </a>
                                    </div>
                                @endif

                                @if(!$training->credential_id && !$training->certificate_url)
                                    <div class="text-muted">No credentials available</div>
                                @endif
                            </div>

                            <h6 class="fw-bold mb-3 mt-4">Status Information</h6>
                            <div class="info-group">
                                <div class="info-item mb-2">
                                    <strong>Status:</strong>
                                    <span class="ms-2">{{ ucfirst(str_replace('_', ' ', $training->status)) }}</span>
                                </div>
                                
                                <div class="info-item mb-2">
                                    <strong>Display Order:</strong>
                                    <span class="ms-2">{{ $training->order }}</span>
                                </div>
                                
                                <div class="info-item mb-2">
                                    <strong>Featured:</strong>
                                    <span class="ms-2">{{ $training->is_featured ? 'Yes' : 'No' }}</span>
                                </div>
                                
                                <div class="info-item mb-2">
                                    <strong>Active:</strong>
                                    <span class="ms-2">{{ $training->is_active ? 'Yes' : 'No' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Description -->
                    @if($training->description)
                        <div class="mt-4">
                            <h6 class="fw-bold mb-3">Description</h6>
                            <div class="description-content">
                                <p class="text-muted">{{ $training->description }}</p>
                            </div>
                        </div>
                    @endif

                    <!-- Skills Gained -->
                    @if($training->skills_gained && count($training->skills_gained) > 0)
                        <div class="mt-4">
                            <h6 class="fw-bold mb-3">Skills Gained</h6>
                            <div class="skills-container">
                                @foreach($training->skills_gained as $skill)
                                    <span class="badge bg-light text-dark me-2 mb-2">{{ $skill }}</span>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Personal Notes -->
                    @if($training->notes)
                        <div class="mt-4">
                            <h6 class="fw-bold mb-3">Personal Notes</h6>
                            <div class="notes-content">
                                <div class="bg-light p-3 rounded">
                                    <p class="mb-0">{{ $training->notes }}</p>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Quick Actions & Timeline -->
        <div class="col-md-4">
            <!-- Quick Actions -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="card-title mb-0">Quick Actions</h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.training.edit', $training) }}" class="btn btn-primary">
                            <i class="bx bx-edit me-1"></i>Edit Training
                        </a>
                        
                        <button class="btn btn-{{ $training->is_active ? 'warning' : 'success' }} toggle-status" 
                                data-id="{{ $training->id }}" data-status="{{ $training->is_active ? 'inactive' : 'active' }}">
                            <i class="bx bx-{{ $training->is_active ? 'pause' : 'play' }} me-1"></i>
                            {{ $training->is_active ? 'Deactivate' : 'Activate' }}
                        </button>
                        
                        <button class="btn btn-{{ $training->is_featured ? 'secondary' : 'warning' }} toggle-featured" 
                                data-id="{{ $training->id }}" data-featured="{{ $training->is_featured ? 'false' : 'true' }}">
                            <i class="bx bx-star me-1"></i>
                            {{ $training->is_featured ? 'Remove Featured' : 'Mark Featured' }}
                        </button>
                        
                        @if($training->certificate_url)
                            <a href="{{ $training->certificate_url }}" target="_blank" class="btn btn-info">
                                <i class="bx bx-link-external me-1"></i>View Certificate
                            </a>
                        @endif
                        
                        <button class="btn btn-danger delete-training" data-id="{{ $training->id }}">
                            <i class="bx bx-trash me-1"></i>Delete Training
                        </button>
                    </div>
                </div>
            </div>

            <!-- Timeline -->
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title mb-0">Timeline</h6>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        <div class="timeline-item">
                            <div class="timeline-marker bg-success"></div>
                            <div class="timeline-content">
                                <h6 class="timeline-title">Created</h6>
                                <p class="timeline-text">{{ $training->created_at->format('M j, Y g:i A') }}</p>
                            </div>
                        </div>
                        
                        @if($training->created_at != $training->updated_at)
                            <div class="timeline-item">
                                <div class="timeline-marker bg-primary"></div>
                                <div class="timeline-content">
                                    <h6 class="timeline-title">Last Updated</h6>
                                    <p class="timeline-text">{{ $training->updated_at->format('M j, Y g:i A') }}</p>
                                </div>
                            </div>
                        @endif
                        
                        @if($training->start_date)
                            <div class="timeline-item">
                                <div class="timeline-marker bg-info"></div>
                                <div class="timeline-content">
                                    <h6 class="timeline-title">Training Started</h6>
                                    <p class="timeline-text">{{ $training->start_date->format('M j, Y') }}</p>
                                </div>
                            </div>
                        @endif
                        
                        @if($training->end_date)
                            <div class="timeline-item">
                                <div class="timeline-marker bg-{{ $training->status === 'completed' ? 'success' : 'warning' }}"></div>
                                <div class="timeline-content">
                                    <h6 class="timeline-title">Training {{ $training->status === 'completed' ? 'Completed' : 'Ended' }}</h6>
                                    <p class="timeline-text">{{ $training->end_date->format('M j, Y') }}</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.training-icon {
    padding: 1rem;
    border: 2px solid #f1f1f1;
    border-radius: 0.75rem;
    display: inline-block;
}

.info-group .info-item {
    border-bottom: 1px solid #f1f1f1;
    padding-bottom: 0.5rem;
}

.info-group .info-item:last-child {
    border-bottom: none;
    padding-bottom: 0;
}

.skills-container .badge {
    font-size: 0.875rem;
    padding: 0.5rem 0.75rem;
}

.timeline {
    position: relative;
    padding-left: 2rem;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 0.75rem;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #e3e3e3;
}

.timeline-item {
    position: relative;
    margin-bottom: 1.5rem;
}

.timeline-item:last-child {
    margin-bottom: 0;
}

.timeline-marker {
    position: absolute;
    left: -2rem;
    width: 1rem;
    height: 1rem;
    border-radius: 50%;
    border: 2px solid #fff;
    z-index: 1;
}

.timeline-content {
    background: #f8f9fa;
    padding: 0.75rem;
    border-radius: 0.375rem;
    border-left: 3px solid #dee2e6;
}

.timeline-title {
    margin: 0 0 0.25rem;
    font-size: 0.875rem;
    font-weight: 600;
}

.timeline-text {
    margin: 0;
    font-size: 0.8rem;
    color: #6c757d;
}
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    // Toggle Status
    $('.toggle-status').on('click', function() {
        const trainingId = $(this).data('id');
        const button = $(this);
        
        $.ajax({
            url: '{{ url("admin/training") }}/' + trainingId + '/toggle-status',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}'
            },
            beforeSend: function() {
                button.prop('disabled', true);
            },
            success: function(response) {
                if (response.success) {
                    toastr.success(response.message);
                    // Refresh page after short delay
                    setTimeout(function() {
                        window.location.reload();
                    }, 1000);
                } else {
                    toastr.error(response.message || 'Failed to update status');
                }
            },
            error: function(xhr) {
                console.error('Status toggle error:', xhr);
                toastr.error('Failed to update training status');
            },
            complete: function() {
                button.prop('disabled', false);
            }
        });
    });

    // Toggle Featured
    $('.toggle-featured').on('click', function() {
        const trainingId = $(this).data('id');
        const button = $(this);
        
        $.ajax({
            url: '{{ url("admin/training") }}/' + trainingId + '/toggle-featured',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}'
            },
            beforeSend: function() {
                button.prop('disabled', true);
            },
            success: function(response) {
                if (response.success) {
                    toastr.success(response.message);
                    // Refresh page after short delay
                    setTimeout(function() {
                        window.location.reload();
                    }, 1000);
                } else {
                    toastr.error(response.message || 'Failed to update featured status');
                }
            },
            error: function(xhr) {
                console.error('Featured toggle error:', xhr);
                toastr.error('Failed to update featured status');
            },
            complete: function() {
                button.prop('disabled', false);
            }
        });
    });

    // Delete Training
    $('.delete-training').on('click', function() {
        const trainingId = $(this).data('id');
        
        obConfirm('Are you sure you want to delete this training record? This action cannot be undone.').then(function (ok) {
            if (!ok) return;
            $.ajax({
                url: '{{ url("admin/training") }}/' + trainingId,
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    _method: 'DELETE'
                },
                beforeSend: function() {
                    toastr.info('Deleting training record...', 'Please wait');
                },
                success: function(response) {
                    if (response.success) {
                        toastr.success(response.message);
                        // Redirect to training index
                        setTimeout(function() {
                            window.location.href = '{{ route("admin.training.index") }}';
                        }, 1000);
                    } else {
                        toastr.error(response.message || 'Failed to delete training record');
                    }
                },
                error: function(xhr) {
                    console.error('Delete error:', xhr);
                    toastr.error('Failed to delete training record');
                }
            });
        });
    });
});
</script>
@endpush