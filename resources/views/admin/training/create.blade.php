@extends('layouts.app')

@section('title', 'Create New Training')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <!-- Header -->
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <span class="text-muted fw-light">Portfolio / Training /</span> Create New Training
                    </h5>
                    <a href="{{ route('admin.training.index') }}" class="btn btn-secondary">
                        <i class="bx bx-arrow-back me-1"></i>Back to Training
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
                    <h5 class="card-title mb-0">Training Information</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.training.store') }}" method="POST" id="trainingForm">
                        @csrf
                        
                        <div class="row mb-3">
                            <div class="col-md-8">
                                <label for="title" class="form-label">Training Title <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                       id="title" name="title" value="{{ old('title') }}" required>
                                @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="provider" class="form-label">Provider <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('provider') is-invalid @enderror" 
                                       id="provider" name="provider" value="{{ old('provider') }}" required
                                       placeholder="e.g., Udemy, Coursera, MIT">
                                @error('provider')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="type" class="form-label">Type <span class="text-danger">*</span></label>
                                <select class="form-select @error('type') is-invalid @enderror" 
                                        id="type" name="type" required>
                                    <option value="">Select Type</option>
                                    @foreach($types as $key => $label)
                                        <option value="{{ $key }}" {{ old('type') == $key ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="category" class="form-label">Category <span class="text-danger">*</span></label>
                                <select class="form-select @error('category') is-invalid @enderror" 
                                        id="category" name="category" required>
                                    <option value="">Select Category</option>
                                    @foreach($categories as $key => $label)
                                        <option value="{{ $key }}" {{ old('category') == $key ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('category')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                                <select class="form-select @error('status') is-invalid @enderror" 
                                        id="status" name="status" required>
                                    @foreach($statuses as $key => $label)
                                        <option value="{{ $key }}" {{ old('status', 'completed') == $key ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('status')
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
                                <small class="text-muted">Leave empty if ongoing</small>
                                @error('end_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="duration_hours" class="form-label">Duration (Hours)</label>
                                <input type="number" class="form-control @error('duration_hours') is-invalid @enderror" 
                                       id="duration_hours" name="duration_hours" value="{{ old('duration_hours') }}" 
                                       min="1" max="10000">
                                @error('duration_hours')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="score" class="form-label">Score (%)</label>
                                <input type="number" class="form-control @error('score') is-invalid @enderror" 
                                       id="score" name="score" value="{{ old('score') }}" 
                                       min="0" max="100">
                                @error('score')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="rating" class="form-label">Personal Rating</label>
                                <select class="form-select @error('rating') is-invalid @enderror" id="rating" name="rating">
                                    <option value="">Not Rated</option>
                                    <option value="1" {{ old('rating') == '1' ? 'selected' : '' }}>⭐ 1 Star</option>
                                    <option value="2" {{ old('rating') == '2' ? 'selected' : '' }}>⭐⭐ 2 Stars</option>
                                    <option value="3" {{ old('rating') == '3' ? 'selected' : '' }}>⭐⭐⭐ 3 Stars</option>
                                    <option value="4" {{ old('rating') == '4' ? 'selected' : '' }}>⭐⭐⭐⭐ 4 Stars</option>
                                    <option value="5" {{ old('rating') == '5' ? 'selected' : '' }}>⭐⭐⭐⭐⭐ 5 Stars</option>
                                </select>
                                @error('rating')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="instructor" class="form-label">Instructor</label>
                                <input type="text" class="form-control @error('instructor') is-invalid @enderror" 
                                       id="instructor" name="instructor" value="{{ old('instructor') }}"
                                       placeholder="e.g., John Doe, Dr. Smith">
                                @error('instructor')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="credential_id" class="form-label">Credential ID</label>
                                <input type="text" class="form-control @error('credential_id') is-invalid @enderror" 
                                       id="credential_id" name="credential_id" value="{{ old('credential_id') }}"
                                       placeholder="Certificate or credential identifier">
                                @error('credential_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="certificate_url" class="form-label">Certificate URL</label>
                            <input type="url" class="form-control @error('certificate_url') is-invalid @enderror" 
                                   id="certificate_url" name="certificate_url" value="{{ old('certificate_url') }}"
                                   placeholder="https://example.com/certificate">
                            @error('certificate_url')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="3">{{ old('description') }}</textarea>
                            <small class="text-muted">Brief description of the training content</small>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="skills_gained" class="form-label">Skills Gained</label>
                            <input type="text" class="form-control @error('skills_gained') is-invalid @enderror" 
                                   id="skills_gained" name="skills_gained" value="{{ old('skills_gained') }}"
                                   placeholder="PHP, Laravel, MySQL, Redis (comma-separated)">
                            <small class="text-muted">Enter skills separated by commas</small>
                            @error('skills_gained')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="notes" class="form-label">Personal Notes</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror" 
                                      id="notes" name="notes" rows="3">{{ old('notes') }}</textarea>
                            <small class="text-muted">Your personal thoughts, reflections, or important points</small>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="icon" class="form-label">Icon Class</label>
                                <input type="text" class="form-control @error('icon') is-invalid @enderror" 
                                       id="icon" name="icon" value="{{ old('icon', 'bx-book') }}" 
                                       placeholder="e.g., bx-book, fa-graduation-cap, fas fa-certificate">
                                <small class="text-muted">Use Boxicons (bx-*) or Font Awesome (fa-, fab, fas, far, fal)</small>
                                @error('icon')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="color" class="form-label">Color</label>
                                <input type="color" class="form-control form-control-color @error('color') is-invalid @enderror" 
                                       id="color" name="color" value="{{ old('color', '#007bff') }}">
                                @error('color')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="order" class="form-label">Display Order</label>
                                <input type="number" class="form-control @error('order') is-invalid @enderror" 
                                       id="order" name="order" value="{{ old('order', 0) }}" min="0">
                                <small class="text-muted">Lower numbers appear first</small>
                                @error('order')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <div class="form-check-container mt-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" value="1" 
                                               id="is_featured" name="is_featured" {{ old('is_featured') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_featured">
                                            Featured Training
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" value="1" 
                                               id="is_active" name="is_active" {{ old('is_active', true) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_active">
                                            Active
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary me-2" id="submitBtn">
                                <span class="btn-text">Create Training</span>
                            </button>
                            <a href="{{ route('admin.training.index') }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Preview Panel -->
        <div class="col-md-4">
            <div class="card sticky-top" style="top: 1rem;">
                <div class="card-header">
                    <h6 class="card-title mb-0">Live Preview</h6>
                </div>
                <div class="card-body">
                    <div class="preview-container">
                        <div class="d-flex align-items-center mb-3">
                            <div class="preview-icon me-3">
                                <i id="preview-icon" class="bx bx-book" style="font-size: 2rem; color: #007bff;"></i>
                            </div>
                            <div>
                                <h6 class="mb-1" id="preview-title">Training Title</h6>
                                <small class="text-muted" id="preview-provider">Provider Name</small>
                            </div>
                        </div>
                        
                        <div class="mb-2">
                            <span class="badge bg-primary" id="preview-type">Type</span>
                            <span class="badge bg-info" id="preview-category">Category</span>
                            <span class="badge bg-success" id="preview-status">Status</span>
                        </div>
                        
                        <div class="preview-details">
                            <small class="text-muted d-block" id="preview-duration">Duration: Not specified</small>
                            <small class="text-muted d-block" id="preview-instructor">Instructor: Not specified</small>
                            <div class="mt-2" id="preview-rating"></div>
                        </div>
                        
                        <div class="mt-3">
                            <p class="text-muted small mb-2" id="preview-description">No description provided</p>
                        </div>
                        
                        <div class="mt-2" id="preview-skills"></div>
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
    // Initialize preview
    updatePreview();
    
    // Update preview when form fields change
    $('#title, #provider, #type, #category, #status, #description, #duration_hours, #instructor, #rating, #skills_gained, #icon, #color').on('input change', function() {
        updatePreview();
    });

    // AJAX form submission
    $('#trainingForm').on('submit', function(e) {
        e.preventDefault();
        
        const submitBtn = $('#submitBtn');
        const originalText = submitBtn.find('.btn-text').text();
        
        // Show loading state
        submitBtn.prop('disabled', true);
        submitBtn.find('.btn-text').html('<span class="spinner-border spinner-border-sm me-1" role="status"></span>Processing...');
        
        // Clear previous errors
        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').remove();
        
        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: $(this).serialize(),
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .done(function(response) {
            if (response.success) {
                toastr.success(response.message);
                // Redirect after short delay
                setTimeout(function() {
                    window.location.href = response.redirect;
                }, 1000);
            } else {
                toastr.error(response.message || 'Please check the form for errors');
            }
        })
        .fail(function(xhr) {
            if (xhr.status === 422) {
                // Validation errors
                const errors = xhr.responseJSON.errors;
                Object.keys(errors).forEach(function(field) {
                    const input = $(`[name="${field}"]`);
                    input.addClass('is-invalid');
                    input.after(`<div class="invalid-feedback">${errors[field][0]}</div>`);
                });
                toastr.error('Please check the form for errors');
            } else {
                toastr.error('An unexpected error occurred. Please try again.');
            }
        })
        .always(function() {
            // Reset button state
            submitBtn.prop('disabled', false);
            submitBtn.find('.btn-text').text(originalText);
        });
    });

    function updatePreview() {
        // Update icon with proper FontAwesome and Boxicons support
        const iconClass = $('#icon').val() || 'bx-book';
        const color = $('#color').val() || '#007bff';
        const iconElement = $('#preview-icon');
        
        // Handle different icon formats like the skills system
        iconElement.removeClass();
        if (iconClass.startsWith('bx-')) {
            // Boxicons
            iconElement.addClass('bx ' + iconClass);
        } else if (iconClass.startsWith('fa-')) {
            // Font Awesome without prefix - assume solid
            iconElement.addClass('fas ' + iconClass);
        } else if (iconClass.match(/^(fab|fas|far|fal|fad)\s/)) {
            // Font Awesome with prefix
            iconElement.attr('class', iconClass);
        } else if (iconClass) {
            // Any other icon class
            iconElement.attr('class', iconClass);
        } else {
            iconElement.addClass('bx bx-book text-muted');
        }
        iconElement.css('color', color);
        
        // Update text content
        $('#preview-title').text($('#title').val() || 'Training Title');
        $('#preview-provider').text($('#provider').val() || 'Provider Name');
        
        // Update badges
        const typeText = $('#type option:selected').text() || 'Type';
        const categoryText = $('#category option:selected').text() || 'Category';
        const statusText = $('#status option:selected').text() || 'Status';
        
        $('#preview-type').text(typeText);
        $('#preview-category').text(categoryText);
        $('#preview-status').text(statusText);
        
        // Update details
        const duration = $('#duration_hours').val();
        $('#preview-duration').text(duration ? `Duration: ${duration} hours` : 'Duration: Not specified');
        
        const instructor = $('#instructor').val();
        $('#preview-instructor').text(instructor ? `Instructor: ${instructor}` : 'Instructor: Not specified');
        
        // Update rating
        const rating = $('#rating').val();
        if (rating) {
            let stars = '';
            for (let i = 1; i <= 5; i++) {
                stars += i <= rating ? '⭐' : '☆';
            }
            $('#preview-rating').html(`<small class="text-warning">${stars} (${rating}/5)</small>`);
        } else {
            $('#preview-rating').empty();
        }
        
        // Update description
        const description = $('#description').val();
        $('#preview-description').text(description || 'No description provided');
        
        // Update skills
        const skills = $('#skills_gained').val();
        if (skills) {
            const skillsArray = skills.split(',').map(s => s.trim()).filter(s => s);
            const skillsBadges = skillsArray.map(skill => `<span class="badge bg-light text-dark me-1">${skill}</span>`).join('');
            $('#preview-skills').html(`<div class="mt-2"><small class="text-muted d-block mb-1">Skills:</small>${skillsBadges}</div>`);
        } else {
            $('#preview-skills').empty();
        }
    }
});
</script>
@endpush