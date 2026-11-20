@extends('layouts.app')

@section('title', 'Education Details')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <!-- Header -->
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-0">
                            <span class="text-muted fw-light">Portfolio / Education /</span> {{ $education->institution }}
                        </h4>
                        <div class="mt-2">
                            @php
                                $statusColor = $education->is_current ? 'success' : 'info';
                                $statusIcon = $education->is_current ? 'bx-time' : 'bx-check-circle';
                                $statusText = $education->is_current ? 'Current' : 'Completed';
                            @endphp
                            <span class="badge bg-{{ $statusColor }}">
                                <i class="bx {{ $statusIcon }} me-1"></i>{{ $statusText }}
                            </span>
                            @if(!$education->is_active)
                                <span class="badge bg-secondary">Inactive</span>
                            @endif
                        </div>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('education.index') }}" class="btn btn-outline-secondary">
                            <i class="bx bx-arrow-back me-1"></i>Back to Education
                        </a>
                        
                        <a href="{{ route('education.edit', $education->id) }}" class="btn btn-primary">
                            <i class="bx bx-edit me-1"></i>Edit
                        </a>
                        
                        <button type="button" class="btn btn-warning" onclick="toggleStatus({{ $education->id }})">
                            @if($education->is_active)
                                <i class="bx bx-x-circle me-1"></i>Deactivate
                            @else
                                <i class="bx bx-check-circle me-1"></i>Activate
                            @endif
                        </button>
                        
                        <button type="button" class="btn btn-danger" onclick="deleteEducation({{ $education->id }})">
                            <i class="bx bx-trash me-1"></i>Delete
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Main Content -->
        <div class="col-lg-8 col-md-12">
            <!-- Education Overview -->
            <div class="card mb-4">
                <div class="card-header d-flex align-items-center">
                    @if($education->institution_logo)
                        <img src="{{ asset($education->institution_logo) }}" alt="Institution Logo" 
                             class="me-3" style="width: 60px; height: 60px; object-fit: cover; border-radius: 12px;">
                    @endif
                    <div>
                        <h5 class="card-title mb-1">{{ $education->degree }}</h5>
                        <h6 class="text-muted">{{ $education->field_of_study }}</h6>
                    </div>
                </div>
                <div class="card-body">
                    @if($education->description)
                        <div class="education-description">
                            {!! nl2br(e($education->description)) !!}
                        </div>
                    @else
                        <p class="text-muted">No description provided.</p>
                    @endif
                </div>
            </div>

            <!-- Activities & Societies -->
            @if($education->activities && count($education->activities) > 0)
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bx bx-group me-2"></i>Activities & Societies
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-flex flex-wrap gap-2">
                        @foreach($education->activities as $activity)
                            <span class="badge bg-info">{{ $activity }}</span>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            <!-- Relevant Courses -->
            @if($education->courses && count($education->courses) > 0)
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bx bx-book me-2"></i>Relevant Courses
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-flex flex-wrap gap-2">
                        @foreach($education->courses as $course)
                            <span class="badge bg-warning">{{ $course }}</span>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4 col-md-12">
            <!-- Education Details -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bx bx-info-circle me-2"></i>Education Details
                    </h5>
                </div>
                <div class="card-body">
                    <div class="education-info">
                        <div class="mb-3">
                            <label class="form-label text-muted small">Institution</label>
                            <div class="fw-bold">{{ $education->institution }}</div>
                        </div>
                        
                        @if($education->location)
                        <div class="mb-3">
                            <label class="form-label text-muted small">Location</label>
                            <div>
                                <i class="bx bx-map me-1"></i>{{ $education->location }}
                            </div>
                        </div>
                        @endif
                        
                        <div class="mb-3">
                            <label class="form-label text-muted small">Degree</label>
                            <div>{{ $education->degree }}</div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label text-muted small">Field of Study</label>
                            <div>{{ $education->field_of_study }}</div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label text-muted small">Duration</label>
                            <div>
                                {{ $education->start_date->format('M Y') }} - 
                                {{ $education->is_current ? 'Present' : ($education->end_date ? $education->end_date->format('M Y') : 'Present') }}
                            </div>
                        </div>
                        
                        @if($education->grade)
                        <div class="mb-3">
                            <label class="form-label text-muted small">Grade/GPA</label>
                            <div>
                                <i class="bx bx-trophy me-1 text-success"></i>{{ $education->grade }}
                            </div>
                        </div>
                        @endif
                        
                        <div class="mb-3">
                            <label class="form-label text-muted small">Status</label>
                            <div>
                                <span class="badge bg-{{ $statusColor }}">
                                    <i class="bx {{ $statusIcon }} me-1"></i>{{ $statusText }}
                                </span>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label text-muted small">Visibility</label>
                            <div>
                                @if($education->is_active)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-secondary">Inactive</span>
                                @endif
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label text-muted small">Added</label>
                            <div>{{ $education->created_at->format('M d, Y \a\t g:i A') }}</div>
                            <small class="text-muted">{{ $education->created_at->diffForHumans() }}</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bx bx-zap me-2"></i>Quick Actions
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('education.edit', $education->id) }}" class="btn btn-outline-primary btn-sm">
                            <i class="bx bx-edit me-1"></i>Edit Education
                        </a>
                        
                        @if($education->certificate_url)
                        <a href="{{ $education->certificate_url }}" target="_blank" class="btn btn-outline-success btn-sm">
                            <i class="bx bx-certificate me-1"></i>View Certificate
                        </a>
                        @endif
                        
                        <button type="button" class="btn btn-outline-warning btn-sm" onclick="toggleStatus({{ $education->id }})">
                            @if($education->is_active)
                                <i class="bx bx-x-circle me-1"></i>Deactivate
                            @else
                                <i class="bx bx-check-circle me-1"></i>Activate
                            @endif
                        </button>
                        
                        <button type="button" class="btn btn-outline-danger btn-sm" onclick="deleteEducation({{ $education->id }})">
                            <i class="bx bx-trash me-1"></i>Delete Education
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Toggle status function
function toggleStatus(id) {
    $.post(`{{ url('/education') }}/${id}/toggle-status`, {
        _token: $('meta[name="csrf-token"]').attr('content')
    })
    .done(function(response) {
        if (response.success) {
            toastr.success(response.message);
            setTimeout(function() {
                location.reload();
            }, 1000);
        } else {
            toastr.error(response.message);
        }
    })
    .fail(function() {
        toastr.error('Failed to update education status');
    });
}

// Delete education function
function deleteEducation(id) {
    obConfirm('Are you sure you want to delete this education record? This action cannot be undone.').then(function (ok) {
        if (!ok) return;
        const deleteUrl = `{{ url('/education') }}/${id}`;
        $.ajax({
            url: deleteUrl,
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'X-Requested-With': 'XMLHttpRequest'
            },
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                _method: 'DELETE'
            }
        })
        .done(function(response) {
            if (response.success) {
                toastr.success(response.message);
                setTimeout(function() {
                    window.location.href = '{{ route("education.index") }}';
                }, 1000);
            } else {
                toastr.error(response.message);
            }
        })
        .fail(function(xhr, status, error) {
            if (xhr.responseJSON && xhr.responseJSON.message) {
                toastr.error(xhr.responseJSON.message);
            } else {
                toastr.error('Failed to delete education record: ' + error);
            }
        });
    });
}
</script>
@endpush