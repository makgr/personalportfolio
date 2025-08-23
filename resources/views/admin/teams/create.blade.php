@extends('layouts.app')

@section('title', 'Add Team Member')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <!-- Header -->
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">
                        <span class="text-muted fw-light">Teams /</span> Add Team Member
                    </h4>
                    <div class="d-flex gap-2">
                        <a href="{{ route('teams.index') }}" class="btn btn-outline-secondary">
                            <i class="bx bx-arrow-back me-1"></i>Back to Teams
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form id="teamForm" enctype="multipart/form-data">
                        @csrf
                        
                        <!-- Basic Information -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="mb-3">Basic Information</h5>
                            </div>
                            <div class="col-md-6">
                                <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="name" name="name" required>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-6">
                                <label for="position" class="form-label">Position <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="position" name="position" required>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="department" class="form-label">Department</label>
                                <select class="form-select" id="department" name="department">
                                    <option value="">Select Department</option>
                                    @foreach($departments as $key => $label)
                                        <option value="{{ $key }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="employment_type" class="form-label">Employment Type <span class="text-danger">*</span></label>
                                <select class="form-select" id="employment_type" name="employment_type" required>
                                    @foreach($employmentTypes as $key => $label)
                                        <option value="{{ $key }}" {{ $key === 'full_time' ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-12">
                                <label for="bio" class="form-label">Bio</label>
                                <textarea class="form-control" id="bio" name="bio" rows="3" placeholder="Brief bio about the team member"></textarea>
                            </div>
                        </div>

                        <!-- Contact Information -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="mb-3">Contact Information</h5>
                            </div>
                            <div class="col-md-6">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email">
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-6">
                                <label for="phone" class="form-label">Phone</label>
                                <input type="text" class="form-control" id="phone" name="phone">
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="location" class="form-label">Location</label>
                                <input type="text" class="form-control" id="location" name="location" placeholder="City, Country">
                            </div>
                            <div class="col-md-6">
                                <label for="join_date" class="form-label">Join Date</label>
                                <input type="date" class="form-control" id="join_date" name="join_date">
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
                                <small class="text-muted">Maximum file size: 1MB. Supported formats: JPG, PNG, GIF</small>
                            </div>
                            <div class="col-md-6">
                                <div id="imagePreview" class="text-center" style="display: none;">
                                    <img id="previewImg" src="" alt="Preview" class="img-thumbnail" style="max-width: 150px; max-height: 150px;">
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
                                <input type="url" class="form-control" id="linkedin" name="linkedin" placeholder="https://linkedin.com/in/username">
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-6">
                                <label for="twitter" class="form-label">Twitter URL</label>
                                <input type="url" class="form-control" id="twitter" name="twitter" placeholder="https://twitter.com/username">
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="github" class="form-label">GitHub URL</label>
                                <input type="url" class="form-control" id="github" name="github" placeholder="https://github.com/username">
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-6">
                                <label for="website" class="form-label">Personal Website</label>
                                <input type="url" class="form-control" id="website" name="website" placeholder="https://example.com">
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
                                <input type="text" class="form-control" id="skills" name="skills" placeholder="JavaScript, PHP, Laravel, Vue.js (comma-separated)">
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
                                <input type="text" class="form-control" id="icon" name="icon" value="bx-user" placeholder="bx-user">
                                <small class="text-muted">BoxIcons class name (e.g., bx-user, bx-code-alt)</small>
                            </div>
                            <div class="col-md-6">
                                <label for="color" class="form-label">Color</label>
                                <input type="color" class="form-control form-control-color" id="color" name="color" value="#007bff">
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="order" class="form-label">Display Order</label>
                                <input type="number" class="form-control" id="order" name="order" min="0" placeholder="0">
                                <small class="text-muted">Leave empty to auto-assign</small>
                            </div>
                        </div>

                        <!-- Status Options -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="mb-3">Status Options</h5>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="is_featured" name="is_featured">
                                    <label class="form-check-label" for="is_featured">
                                        Featured Team Member
                                    </label>
                                    <small class="text-muted d-block">Display this member prominently</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="is_active" name="is_active" checked>
                                    <label class="form-check-label" for="is_active">
                                        Active
                                    </label>
                                    <small class="text-muted d-block">Show this team member publicly</small>
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
                                        <i class="bx bx-check me-1"></i>Add Team Member
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
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
    $('#teamForm').submit(function(e) {
        e.preventDefault();
        
        const submitBtn = $('#submitBtn');
        const originalText = submitBtn.html();
        
        // Clear previous errors
        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').empty();
        
        // Show loading state
        submitBtn.prop('disabled', true).html('<i class="bx bx-loader-alt bx-spin me-1"></i>Adding...');
        
        const formData = new FormData(this);
        
        $.ajax({
            url: '{{ route("teams.store") }}',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    toastr.success(response.message);
                    if (response.redirect) {
                        setTimeout(() => {
                            window.location.href = response.redirect;
                        }, 1000);
                    }
                } else {
                    toastr.error(response.message || 'Failed to add team member');
                }
            },
            error: function(xhr) {
                console.error('Form submission error:', xhr);
                
                if (xhr.status === 422) {
                    // Validation errors
                    const errors = xhr.responseJSON.errors;
                    Object.keys(errors).forEach(function(field) {
                        const input = $('[name="' + field + '"]');
                        input.addClass('is-invalid');
                        input.siblings('.invalid-feedback').text(errors[field][0]);
                    });
                    toastr.error('Please correct the errors and try again');
                } else {
                    toastr.error('Failed to add team member. Please try again.');
                }
            },
            complete: function() {
                submitBtn.prop('disabled', false).html(originalText);
            }
        });
    });
});
</script>
@endpush