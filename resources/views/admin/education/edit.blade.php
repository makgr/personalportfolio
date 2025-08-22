@extends('layouts.app')

@section('title', 'Edit Education')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <!-- Header -->
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">
                        <span class="text-muted fw-light">Portfolio / Education /</span> Edit
                    </h4>
                    <div class="d-flex gap-2">
                        <a href="{{ route('education.show', $education->id) }}" class="btn btn-outline-info">
                            <i class="bx bx-show me-1"></i>View Details
                        </a>
                        <a href="{{ route('education.index') }}" class="btn btn-outline-secondary">
                            <i class="bx bx-arrow-back me-1"></i>Back to Education
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Form -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Education Information</h5>
                </div>
                <div class="card-body">
                    <form id="educationForm" action="{{ route('education.update', $education->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        <div class="row mb-3">
                            <!-- Institution -->
                            <div class="col-md-6">
                                <label class="form-label" for="institution">Institution <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="institution" name="institution" 
                                       placeholder="University of Example" value="{{ old('institution', $education->institution) }}" required>
                                <div class="invalid-feedback"></div>
                            </div>
                            
                            <!-- Location -->
                            <div class="col-md-6">
                                <label class="form-label" for="location">Location</label>
                                <input type="text" class="form-control" id="location" name="location" 
                                       placeholder="City, Country" value="{{ old('location', $education->location) }}">
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <!-- Degree -->
                            <div class="col-md-6">
                                <label class="form-label" for="degree">Degree <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="degree" name="degree" 
                                       placeholder="Bachelor of Science" value="{{ old('degree', $education->degree) }}" required>
                                <div class="invalid-feedback"></div>
                            </div>
                            
                            <!-- Field of Study -->
                            <div class="col-md-6">
                                <label class="form-label" for="field_of_study">Field of Study <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="field_of_study" name="field_of_study" 
                                       placeholder="Computer Science" value="{{ old('field_of_study', $education->field_of_study) }}" required>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <!-- Start Date -->
                            <div class="col-md-4">
                                <label class="form-label" for="start_date">Start Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="start_date" name="start_date" 
                                       value="{{ old('start_date', $education->start_date?->format('Y-m-d')) }}" required>
                                <div class="invalid-feedback"></div>
                            </div>
                            
                            <!-- End Date -->
                            <div class="col-md-4">
                                <label class="form-label" for="end_date">End Date</label>
                                <input type="date" class="form-control" id="end_date" name="end_date" 
                                       value="{{ old('end_date', $education->end_date?->format('Y-m-d')) }}"
                                       {{ $education->is_current ? 'disabled' : '' }}>
                                <div class="invalid-feedback"></div>
                                <div class="form-text">Leave empty if currently studying</div>
                            </div>
                            
                            <!-- Grade -->
                            <div class="col-md-4">
                                <label class="form-label" for="grade">Grade/GPA</label>
                                <input type="text" class="form-control" id="grade" name="grade" 
                                       placeholder="3.8/4.0 or First Class" value="{{ old('grade', $education->grade) }}">
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>

                        <!-- Current Education Checkbox -->
                        <div class="row mb-3">
                            <div class="col-12">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="is_current" name="is_current" 
                                           {{ old('is_current', $education->is_current) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_current">
                                        I am currently studying here
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Description -->
                        <div class="mb-3">
                            <label class="form-label" for="description">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="4" 
                                      placeholder="Describe your studies, projects, achievements...">{{ old('description', $education->description) }}</textarea>
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="row mb-3">
                            <!-- Activities -->
                            <div class="col-md-6">
                                <label class="form-label" for="activities">Activities & Societies</label>
                                <textarea class="form-control" id="activities" name="activities" rows="3" 
                                          placeholder="Student Council, Sports Club, Debate Society...">{{ old('activities', is_array($education->activities) ? implode(', ', $education->activities) : '') }}</textarea>
                                <div class="form-text">Separate multiple activities with commas</div>
                                <div class="invalid-feedback"></div>
                            </div>
                            
                            <!-- Courses -->
                            <div class="col-md-6">
                                <label class="form-label" for="courses">Relevant Courses</label>
                                <textarea class="form-control" id="courses" name="courses" rows="3" 
                                          placeholder="Data Structures, Algorithms, Web Development...">{{ old('courses', is_array($education->courses) ? implode(', ', $education->courses) : '') }}</textarea>
                                <div class="form-text">Separate multiple courses with commas</div>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <!-- Institution Logo -->
                            <div class="col-md-6">
                                <label class="form-label" for="institution_logo">Institution Logo</label>
                                <input type="file" class="form-control" id="institution_logo" name="institution_logo" 
                                       accept="image/*">
                                <div class="form-text">Maximum file size: 1MB. Supported formats: JPG, PNG, GIF</div>
                                <div class="invalid-feedback"></div>
                                
                                <!-- Current Logo -->
                                @if($education->institution_logo)
                                <div class="mt-2">
                                    <small class="text-muted">Current logo:</small><br>
                                    <img src="{{ asset($education->institution_logo) }}" alt="Current Logo" 
                                         style="max-width: 100px; max-height: 100px; object-fit: cover; border-radius: 8px;">
                                </div>
                                @endif
                                
                                <!-- New Image Preview -->
                                <div id="logoPreview" class="mt-2" style="display: none;">
                                    <small class="text-muted">New logo preview:</small><br>
                                    <img id="logoPreviewImg" src="" alt="Logo Preview" 
                                         style="max-width: 100px; max-height: 100px; object-fit: cover; border-radius: 8px;">
                                </div>
                            </div>
                            
                            <!-- Certificate URL -->
                            <div class="col-md-6">
                                <label class="form-label" for="certificate_url">Certificate URL</label>
                                <input type="url" class="form-control" id="certificate_url" name="certificate_url" 
                                       placeholder="https://certificates.example.com/..." value="{{ old('certificate_url', $education->certificate_url) }}">
                                <div class="form-text">Link to digital certificate or transcript</div>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>

                        <!-- Status -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="is_active" name="is_active" 
                                           {{ old('is_active', $education->is_active) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">
                                        Active (visible on portfolio)
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="row">
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary me-2" id="submitBtn">
                                    <i class="bx bx-save me-1"></i>Update Education
                                </button>
                                <a href="{{ route('education.index') }}" class="btn btn-outline-secondary">
                                    <i class="bx bx-x me-1"></i>Cancel
                                </a>
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
    // Image preview functionality
    $('#institution_logo').on('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                $('#logoPreviewImg').attr('src', e.target.result);
                $('#logoPreview').show();
            };
            reader.readAsDataURL(file);
        } else {
            $('#logoPreview').hide();
        }
    });

    // Handle current education checkbox
    $('#is_current').on('change', function() {
        if ($(this).is(':checked')) {
            $('#end_date').val('').prop('disabled', true);
        } else {
            $('#end_date').prop('disabled', false);
        }
    });

    // Form submission with AJAX
    $('#educationForm').on('submit', function(e) {
        e.preventDefault();
        
        // Clear previous errors
        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').text('');
        
        // Show loading state
        const submitBtn = $('#submitBtn');
        const originalText = submitBtn.html();
        submitBtn.html('<i class="bx bx-loader-alt bx-spin me-1"></i>Updating...').prop('disabled', true);
        
        // Create FormData for file upload
        const formData = new FormData(this);
        
        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .done(function(response) {
            if (response.success) {
                toastr.success(response.message);
                // Redirect to education index after a short delay
                setTimeout(function() {
                    window.location.href = '{{ route("education.index") }}';
                }, 1000);
            } else {
                toastr.error(response.message || 'Failed to update education record');
                submitBtn.html(originalText).prop('disabled', false);
            }
        })
        .fail(function(xhr) {
            if (xhr.status === 422) {
                // Validation errors
                const errors = xhr.responseJSON.errors;
                $.each(errors, function(field, messages) {
                    const input = $(`[name="${field}"]`);
                    input.addClass('is-invalid');
                    input.siblings('.invalid-feedback').text(messages[0]);
                });
                toastr.error('Please check the form for errors');
            } else {
                toastr.error('An error occurred while updating the education record');
            }
            submitBtn.html(originalText).prop('disabled', false);
        });
    });
});
</script>
@endpush