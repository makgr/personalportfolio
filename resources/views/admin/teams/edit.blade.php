@extends('layouts.app')

@section('title', 'Edit Team Member')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <!-- Header -->
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">
                        <span class="text-muted fw-light">Teams /</span> Edit {{ $team->name }}
                    </h4>
                    <div class="d-flex gap-2">
                        <a href="{{ route('teams.show', $team) }}" class="btn btn-outline-info">
                            <i class="bx bx-show me-1"></i>View
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
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <form id="teamEditForm" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        <!-- Basic Information -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="mb-3">Basic Information</h5>
                            </div>
                            <div class="col-md-6">
                                <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="name" name="name" value="{{ $team->name }}" required>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-6">
                                <label for="position" class="form-label">Position <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="position" name="position" value="{{ $team->position }}" required>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="department" class="form-label">Department</label>
                                <select class="form-select" id="department" name="department">
                                    <option value="">Select Department</option>
                                    @foreach($departments as $key => $label)
                                        <option value="{{ $key }}" {{ $team->department === $key ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="employment_type" class="form-label">Employment Type <span class="text-danger">*</span></label>
                                <select class="form-select" id="employment_type" name="employment_type" required>
                                    @foreach($employmentTypes as $key => $label)
                                        <option value="{{ $key }}" {{ $team->employment_type === $key ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-12">
                                <label for="bio" class="form-label">Bio</label>
                                <textarea class="form-control" id="bio" name="bio" rows="3">{{ $team->bio }}</textarea>
                            </div>
                        </div>

                        <!-- Contact Information -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="mb-3">Contact Information</h5>
                            </div>
                            <div class="col-md-6">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" value="{{ $team->email }}">
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-6">
                                <label for="phone" class="form-label">Phone</label>
                                <input type="text" class="form-control" id="phone" name="phone" value="{{ $team->phone }}">
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="location" class="form-label">Location</label>
                                <input type="text" class="form-control" id="location" name="location" value="{{ $team->location }}">
                            </div>
                            <div class="col-md-6">
                                <label for="join_date" class="form-label">Join Date</label>
                                <input type="date" class="form-control" id="join_date" name="join_date" value="{{ $team->join_date ? $team->join_date->format('Y-m-d') : '' }}">
                            </div>
                        </div>

                        <!-- Profile Image -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="mb-3">Profile Image</h5>
                            </div>
                            <div class="col-md-6">
                                <label for="image" class="form-label">Profile Photo</label>
                                <input type="file" class="form-control" id="image" name="image" accept="image/*">
                                <small class="text-muted">Maximum file size: 1MB. Leave empty to keep current image.</small>
                            </div>
                            <div class="col-md-6">
                                @if($team->image)
                                    <div class="current-image mb-2">
                                        <label class="form-label">Current Image</label>
                                        <div>
                                            <img src="{{ $team->image }}" alt="{{ $team->name }}" class="img-thumbnail" style="max-width: 100px;">
                                        </div>
                                    </div>
                                @endif
                                <div id="imagePreview" class="text-center" style="display: none;">
                                    <label class="form-label">Preview</label>
                                    <div>
                                        <img id="previewImg" src="" alt="Preview" class="img-thumbnail" style="max-width: 100px;">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Social Links -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="mb-3">Social Links</h5>
                            </div>
                            <div class="col-md-6">
                                <label for="linkedin" class="form-label">LinkedIn URL</label>
                                <input type="url" class="form-control" id="linkedin" name="linkedin" value="{{ $team->linkedin }}">
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-6">
                                <label for="twitter" class="form-label">Twitter URL</label>
                                <input type="url" class="form-control" id="twitter" name="twitter" value="{{ $team->twitter }}">
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="github" class="form-label">GitHub URL</label>
                                <input type="url" class="form-control" id="github" name="github" value="{{ $team->github }}">
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-6">
                                <label for="website" class="form-label">Personal Website</label>
                                <input type="url" class="form-control" id="website" name="website" value="{{ $team->website }}">
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>

                        <!-- Skills -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="mb-3">Skills & Expertise</h5>
                            </div>
                            <div class="col-12">
                                <label for="skills" class="form-label">Skills</label>
                                <input type="text" class="form-control" id="skills" name="skills" value="{{ $team->skills_string }}">
                                <small class="text-muted">Enter skills separated by commas</small>
                            </div>
                        </div>

                        <!-- Settings -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="mb-3">Display Settings</h5>
                            </div>
                            <div class="col-md-6">
                                <label for="icon" class="form-label">Icon Class</label>
                                <input type="text" class="form-control" id="icon" name="icon" value="{{ $team->icon }}">
                                <small class="text-muted">BoxIcons class name</small>
                            </div>
                            <div class="col-md-6">
                                <label for="color" class="form-label">Color</label>
                                <input type="color" class="form-control form-control-color" id="color" name="color" value="{{ $team->color }}">
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="order" class="form-label">Display Order</label>
                                <input type="number" class="form-control" id="order" name="order" value="{{ $team->order }}" min="0">
                            </div>
                        </div>

                        <!-- Status Options -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="mb-3">Status Options</h5>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="is_featured" name="is_featured" {{ $team->is_featured ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_featured">
                                        Featured Team Member
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="is_active" name="is_active" {{ $team->is_active ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">
                                        Active
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="row">
                            <div class="col-12">
                                <hr class="my-4">
                                <div class="d-flex justify-content-between">
                                    <a href="{{ route('teams.index') }}" class="btn btn-outline-secondary">
                                        <i class="bx bx-x me-1"></i>Cancel
                                    </a>
                                    <button type="submit" class="btn btn-primary" id="submitBtn">
                                        <i class="bx bx-check me-1"></i>Update Team Member
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Current Information Sidebar -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Current Information</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        @if($team->image)
                            <img src="{{ $team->image }}" alt="{{ $team->name }}" class="rounded-circle me-3" style="width: 60px; height: 60px; object-fit: cover;">
                        @else
                            <div class="avatar-initial rounded-circle bg-label-primary me-3 d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                {{ $team->initials }}
                            </div>
                        @endif
                        <div>
                            <h6 class="mb-1">{{ $team->name }}</h6>
                            <small class="text-muted">{{ $team->position }}</small>
                        </div>
                    </div>

                    <div class="row g-3 text-nowrap">
                        <div class="col-6">
                            <div class="d-flex align-items-center">
                                <div class="badge bg-label-{{ $team->employment_type_badge_color }} rounded p-2 me-2">
                                    <i class="bx bx-briefcase bx-xs"></i>
                                </div>
                                <div>
                                    <small class="text-muted d-block">Employment</small>
                                    <h6 class="mb-0">{{ ucfirst(str_replace('_', ' ', $team->employment_type)) }}</h6>
                                </div>
                            </div>
                        </div>
                        @if($team->department)
                        <div class="col-6">
                            <div class="d-flex align-items-center">
                                <div class="badge bg-label-{{ $team->department_badge_color }} rounded p-2 me-2">
                                    <i class="bx bx-buildings bx-xs"></i>
                                </div>
                                <div>
                                    <small class="text-muted d-block">Department</small>
                                    <h6 class="mb-0">{{ ucfirst(str_replace('_', ' ', $team->department)) }}</h6>
                                </div>
                            </div>
                        </div>
                        @endif
                        @if($team->join_date)
                        <div class="col-6">
                            <div class="d-flex align-items-center">
                                <div class="badge bg-label-info rounded p-2 me-2">
                                    <i class="bx bx-calendar bx-xs"></i>
                                </div>
                                <div>
                                    <small class="text-muted d-block">Tenure</small>
                                    <h6 class="mb-0">{{ $team->tenure }}</h6>
                                </div>
                            </div>
                        </div>
                        @endif
                        <div class="col-6">
                            <div class="d-flex align-items-center">
                                <div class="badge bg-label-{{ $team->is_active ? 'success' : 'secondary' }} rounded p-2 me-2">
                                    <i class="bx bx-{{ $team->is_active ? 'check-circle' : 'x-circle' }} bx-xs"></i>
                                </div>
                                <div>
                                    <small class="text-muted d-block">Status</small>
                                    <h6 class="mb-0">{{ $team->is_active ? 'Active' : 'Inactive' }}</h6>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($team->skills && count($team->skills) > 0)
                    <div class="mt-4">
                        <small class="text-muted">Skills</small>
                        <div class="mt-2">
                            @foreach($team->skills as $skill)
                                <span class="badge bg-label-secondary me-1 mb-1">{{ $skill }}</span>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header">
                    <h5 class="mb-0">Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <button type="button" class="btn btn-outline-{{ $team->is_featured ? 'secondary' : 'warning' }}" onclick="toggleFeatured({{ $team->id }})">
                            <i class="bx bx-star me-1"></i>{{ $team->is_featured ? 'Remove Featured' : 'Mark Featured' }}
                        </button>
                        <button type="button" class="btn btn-outline-{{ $team->is_active ? 'warning' : 'success' }}" onclick="toggleStatus({{ $team->id }})">
                            <i class="bx bx-{{ $team->is_active ? 'pause' : 'play' }} me-1"></i>{{ $team->is_active ? 'Deactivate' : 'Activate' }}
                        </button>
                        <button type="button" class="btn btn-outline-danger" onclick="deleteMember({{ $team->id }})">
                            <i class="bx bx-trash me-1"></i>Delete Member
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
$(document).ready(function() {
    // Image preview
    $('#image').change(function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                $('#previewImg').attr('src', e.target.result);
                $('#imagePreview').show();
            }
            reader.readAsDataURL(file);
        } else {
            $('#imagePreview').hide();
        }
    });

    // Form submission
    $('#teamEditForm').submit(function(e) {
        e.preventDefault();
        
        const submitBtn = $('#submitBtn');
        const originalText = submitBtn.html();
        
        // Clear previous errors
        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').empty();
        
        // Show loading state
        submitBtn.prop('disabled', true).html('<i class="bx bx-loader-alt bx-spin me-1"></i>Updating...');
        
        const formData = new FormData(this);
        
        $.ajax({
            url: '{{ route("teams.update", $team) }}',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    toastr.success(response.message);
                    // Reload page to show updated data
                    setTimeout(() => {
                        location.reload();
                    }, 1000);
                } else {
                    toastr.error(response.message || 'Failed to update team member');
                }
            },
            error: function(xhr) {
                console.error('Form submission error:', xhr);
                
                if (xhr.status === 422) {
                    const errors = xhr.responseJSON.errors;
                    Object.keys(errors).forEach(function(field) {
                        const input = $('[name="' + field + '"]');
                        input.addClass('is-invalid');
                        input.siblings('.invalid-feedback').text(errors[field][0]);
                    });
                    toastr.error('Please correct the errors and try again');
                } else {
                    toastr.error('Failed to update team member. Please try again.');
                }
            },
            complete: function() {
                submitBtn.prop('disabled', false).html(originalText);
            }
        });
    });
});

// Quick actions
function toggleFeatured(teamId) {
    $.ajax({
        url: '{{ url("teams") }}/' + teamId + '/toggle-featured',
        type: 'POST',
        data: { _token: '{{ csrf_token() }}' },
        success: function(response) {
            if (response.success) {
                toastr.success(response.message);
                setTimeout(() => location.reload(), 1000);
            } else {
                toastr.error(response.message || 'Failed to update featured status');
            }
        },
        error: function() {
            toastr.error('Failed to update featured status');
        }
    });
}

function toggleStatus(teamId) {
    $.ajax({
        url: '{{ url("teams") }}/' + teamId + '/toggle-status',
        type: 'POST',
        data: { _token: '{{ csrf_token() }}' },
        success: function(response) {
            if (response.success) {
                toastr.success(response.message);
                setTimeout(() => location.reload(), 1000);
            } else {
                toastr.error(response.message || 'Failed to update status');
            }
        },
        error: function() {
            toastr.error('Failed to update status');
        }
    });
}

function deleteMember(teamId) {
    if (confirm('Are you sure you want to delete this team member? This action cannot be undone.')) {
        $.ajax({
            url: '{{ url("teams") }}/' + teamId,
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                _method: 'DELETE'
            },
            success: function(response) {
                if (response.success) {
                    toastr.success(response.message);
                    setTimeout(() => {
                        window.location.href = '{{ route("teams.index") }}';
                    }, 1000);
                } else {
                    toastr.error(response.message || 'Failed to delete team member');
                }
            },
            error: function() {
                toastr.error('Failed to delete team member');
            }
        });
    }
}
</script>
@endpush