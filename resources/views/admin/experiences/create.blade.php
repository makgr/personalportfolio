@extends('layouts.app')

@section('title', 'Add New Experience')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <!-- Header -->
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <span class="text-muted fw-light">Portfolio / Experience /</span> Add New Experience
                    </h5>
                    <a href="{{ route('experiences.index') }}" class="btn btn-secondary">
                        <i class="bx bx-arrow-back me-1"></i>Back to Experience
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Create Form -->
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Experience Information</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('experiences.store') }}" method="POST" id="experienceForm" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="company_name" class="form-label">Company Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('company_name') is-invalid @enderror" 
                                       id="company_name" name="company_name" value="{{ old('company_name') }}" required>
                                @error('company_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="position" class="form-label">Position <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('position') is-invalid @enderror" 
                                       id="position" name="position" value="{{ old('position') }}" required>
                                @error('position')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="employment_type" class="form-label">Employment Type</label>
                                <select class="form-select @error('employment_type') is-invalid @enderror" 
                                        id="employment_type" name="employment_type">
                                    <option value="">Select Type</option>
                                    @foreach($employmentTypes as $key => $label)
                                        <option value="{{ $key }}" {{ old('employment_type') == $key ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('employment_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="location" class="form-label">Location</label>
                                <input type="text" class="form-control @error('location') is-invalid @enderror" 
                                       id="location" name="location" value="{{ old('location') }}" 
                                       placeholder="e.g., New York, NY (Remote)">
                                @error('location')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="start_date" class="form-label">Start Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('start_date') is-invalid @enderror" 
                                       id="start_date" name="start_date" value="{{ old('start_date') }}" required>
                                @error('start_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="end_date" class="form-label">End Date</label>
                                <input type="date" class="form-control @error('end_date') is-invalid @enderror" 
                                       id="end_date" name="end_date" value="{{ old('end_date') }}">
                                <small class="text-muted">Leave empty if this is your current job</small>
                                @error('end_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="company_logo" class="form-label">Company Logo</label>
                                <input type="file" class="form-control @error('company_logo') is-invalid @enderror" 
                                       id="company_logo" name="company_logo" accept="image/*">
                                <small class="text-muted">Optional company logo (max 1MB)</small>
                                @error('company_logo')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="company_url" class="form-label">Company Website</label>
                                <input type="url" class="form-control @error('company_url') is-invalid @enderror" 
                                       id="company_url" name="company_url" value="{{ old('company_url') }}" 
                                       placeholder="https://company.com">
                                @error('company_url')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Job Description <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="4" 
                                      placeholder="Brief overview of the role and your contributions" required>{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="responsibilities" class="form-label">Key Responsibilities</label>
                            <textarea class="form-control @error('responsibilities') is-invalid @enderror" 
                                      id="responsibilities" name="responsibilities" rows="4" 
                                      placeholder="• Responsibility 1&#10;• Responsibility 2&#10;• Responsibility 3">{{ old('responsibilities') }}</textarea>
                            <small class="text-muted">Enter each responsibility on a new line</small>
                            @error('responsibilities')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="achievements" class="form-label">Key Achievements</label>
                            <textarea class="form-control @error('achievements') is-invalid @enderror" 
                                      id="achievements" name="achievements" rows="3" 
                                      placeholder="• Achievement 1&#10;• Achievement 2&#10;• Achievement 3">{{ old('achievements') }}</textarea>
                            <small class="text-muted">Enter each achievement on a new line</small>
                            @error('achievements')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="technologies" class="form-label">Technologies Used</label>
                            <input type="text" class="form-control @error('technologies') is-invalid @enderror" 
                                   id="technologies" name="technologies" value="{{ old('technologies') }}" 
                                   placeholder="React, Laravel, Docker, AWS, MySQL">
                            <small class="text-muted">Comma-separated list of technologies</small>
                            @error('technologies')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" 
                                           id="is_current" name="is_current" value="1" 
                                           {{ old('is_current') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_current">
                                        <i class="bx bx-time text-primary"></i> Current Position
                                    </label>
                                    <small class="form-text text-muted d-block">Check if this is your current job</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" 
                                           id="is_active" name="is_active" value="1" 
                                           {{ old('is_active', true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">
                                        <i class="bx bx-check-circle text-success"></i> Active
                                    </label>
                                    <small class="form-text text-muted d-block">Show this experience on the website</small>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('experiences.index') }}" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary" id="submitBtn">
                                <i class="bx bx-check me-1"></i><span class="btn-text">Add Experience</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Preview Card -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Preview</h5>
                </div>
                <div class="card-body">
                    <div id="experiencePreview">
                        <div class="d-flex mb-3">
                            <div class="me-3">
                                <div id="previewLogo" class="bg-light rounded d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                    <i class="bx bx-buildings text-muted"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <h6 id="previewPosition" class="mb-1">Position Title</h6>
                                <p id="previewCompany" class="text-muted mb-1">Company Name</p>
                                <small id="previewPeriod" class="text-muted">Period</small>
                            </div>
                        </div>
                        
                        <div class="mb-2">
                            <small class="text-muted">Type:</small>
                            <span id="previewType" class="badge bg-secondary ms-1">Not specified</span>
                        </div>
                        
                        <div class="mb-2">
                            <small class="text-muted">Location:</small>
                            <span id="previewLocation" class="ms-1">Not specified</span>
                        </div>
                        
                        <div class="mb-3">
                            <small class="text-muted">Description:</small>
                            <p id="previewDesc" class="small mt-1">Job description will appear here...</p>
                        </div>
                        
                        <div id="previewTechs">
                            <small class="text-muted">Technologies:</small>
                            <div class="mt-1">
                                <small class="text-muted">No technologies added</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Logo Preview -->
            <div class="card mt-3" id="logoPreviewCard" style="display: none;">
                <div class="card-header">
                    <h5 class="card-title mb-0">Logo Preview</h5>
                </div>
                <div class="card-body text-center">
                    <img id="logoPreview" class="img-fluid" src="" alt="Company Logo" style="max-height: 80px;">
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// AJAX Form Submission
$(document).ready(function() {
    $('#experienceForm').on('submit', function(e) {
        e.preventDefault();
        
        const submitBtn = $('#submitBtn');
        const btnText = submitBtn.find('.btn-text');
        const originalText = btnText.text();
        
        // Show loading state
        submitBtn.prop('disabled', true);
        btnText.html('<span class="spinner-border spinner-border-sm me-1" role="status"></span>Adding...');
        
        // Clear previous errors
        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').remove();
        
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
                toastr.success(response.message || 'Experience added successfully!');
                // Redirect to experiences index after short delay
                setTimeout(function() {
                    window.location.href = '{{ route("experiences.index") }}';
                }, 1500);
            } else {
                toastr.error(response.message || 'Failed to add experience');
            }
        })
        .fail(function(xhr) {
            if (xhr.status === 422) {
                // Handle validation errors
                const errors = xhr.responseJSON.errors;
                if (errors) {
                    Object.keys(errors).forEach(function(field) {
                        const input = $(`[name="${field}"]`);
                        input.addClass('is-invalid');
                        input.after(`<div class="invalid-feedback">${errors[field][0]}</div>`);
                    });
                }
                toastr.error('Please check the form for errors');
            } else {
                toastr.error('Failed to add experience. Please try again.');
            }
        })
        .always(function() {
            // Reset button state
            submitBtn.prop('disabled', false);
            btnText.text(originalText);
        });
    });
});

// Logo preview
$('#company_logo').on('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            $('#logoPreview').attr('src', e.target.result);
            $('#logoPreviewCard').show();
            $('#previewLogo').html('<img src="' + e.target.result + '" alt="Company Logo" style="width: 100%; height: 100%; object-fit: contain;" class="rounded">');
        };
        reader.readAsDataURL(file);
    } else {
        $('#logoPreviewCard').hide();
        $('#previewLogo').html('<i class="bx bx-buildings text-muted"></i>');
    }
});

// Current position checkbox handler
$('#is_current').on('change', function() {
    if ($(this).is(':checked')) {
        $('#end_date').prop('disabled', true).val('');
    } else {
        $('#end_date').prop('disabled', false);
    }
    updatePreview();
});

function updatePreview() {
    const company = $('#company_name').val() || 'Company Name';
    const position = $('#position').val() || 'Position Title';
    const employmentType = $('#employment_type').val();
    const location = $('#location').val() || 'Not specified';
    const description = $('#description').val() || 'Job description will appear here...';
    const technologies = $('#technologies').val();
    const startDate = $('#start_date').val();
    const endDate = $('#end_date').val();
    const isCurrent = $('#is_current').is(':checked');
    
    // Update preview
    $('#previewCompany').text(company);
    $('#previewPosition').text(position);
    $('#previewLocation').text(location);
    $('#previewDesc').text(description.substring(0, 150) + (description.length > 150 ? '...' : ''));
    
    // Update employment type
    const typeColors = {
        'full-time': 'success',
        'part-time': 'info', 
        'contract': 'warning',
        'freelance': 'primary',
        'internship': 'secondary'
    };
    const typeColor = typeColors[employmentType] || 'secondary';
    const typeLabel = employmentType ? employmentType.replace('-', ' ').replace(/\b\w/g, l => l.toUpperCase()) : 'Not specified';
    $('#previewType').removeClass().addClass('badge bg-' + typeColor + ' ms-1').text(typeLabel);
    
    // Update period
    let period = 'Period';
    if (startDate) {
        const start = new Date(startDate).toLocaleDateString('en-US', { month: 'short', year: 'numeric' });
        const end = isCurrent ? 'Present' : (endDate ? new Date(endDate).toLocaleDateString('en-US', { month: 'short', year: 'numeric' }) : 'Present');
        period = start + ' - ' + end;
    }
    $('#previewPeriod').text(period);
    
    // Update technologies
    if (technologies) {
        const techList = technologies.split(',').map(t => t.trim()).filter(t => t);
        if (techList.length > 0) {
            const techHtml = techList.slice(0, 4).map(t => 
                '<span class="badge bg-info me-1 mb-1">' + t + '</span>'
            ).join('');
            const moreText = techList.length > 4 ? '<small class="text-muted d-block">+' + (techList.length - 4) + ' more</small>' : '';
            $('#previewTechs').html('<small class="text-muted">Technologies:</small><div class="mt-1">' + techHtml + moreText + '</div>');
        } else {
            $('#previewTechs').html('<small class="text-muted">Technologies:</small><div class="mt-1"><small class="text-muted">No technologies added</small></div>');
        }
    } else {
        $('#previewTechs').html('<small class="text-muted">Technologies:</small><div class="mt-1"><small class="text-muted">No technologies added</small></div>');
    }
}

// Update preview when inputs change
$(document).ready(function() {
    const inputs = ['company_name', 'position', 'employment_type', 'location', 'description', 'technologies', 'start_date', 'end_date'];
    inputs.forEach(id => {
        const element = $('#' + id);
        if (element.length) {
            element.on('input change', updatePreview);
        }
    });
    
    // Initial preview update
    updatePreview();
});
</script>
@endpush