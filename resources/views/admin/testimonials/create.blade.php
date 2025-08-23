@extends('layouts.app')

@section('title', 'Create Testimonial')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <!-- Header -->
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">
                        <span class="text-muted fw-light">Portfolio / Testimonials /</span> Create
                    </h4>
                    <div class="d-flex gap-2">
                        <a href="{{ route('admin.testimonials.index') }}" class="btn btn-outline-secondary">
                            <i class="bx bx-arrow-back me-1"></i>Back to Testimonials
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Form Column -->
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Testimonial Information</h5>
                </div>
                <div class="card-body">
                    <form id="testimonial-form" enctype="multipart/form-data">
                        @csrf
                        
                        <!-- Client Information -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="client_name" class="form-label">Client Name *</label>
                                <input type="text" class="form-control" id="client_name" name="client_name" required>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-6">
                                <label for="client_position" class="form-label">Position</label>
                                <input type="text" class="form-control" id="client_position" name="client_position" placeholder="e.g., CEO, Marketing Manager">
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="client_company" class="form-label">Company</label>
                                <input type="text" class="form-control" id="client_company" name="client_company" placeholder="Company name">
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-6">
                                <label for="client_linkedin" class="form-label">LinkedIn Profile</label>
                                <input type="url" class="form-control" id="client_linkedin" name="client_linkedin" placeholder="https://linkedin.com/in/username">
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>

                        <!-- Client Image -->
                        <div class="mb-3">
                            <label for="client_image" class="form-label">Client Photo</label>
                            <input type="file" class="form-control" id="client_image" name="client_image" accept="image/*">
                            <div class="form-text">Upload client profile photo (optional). Max size: 1MB</div>
                            <div class="invalid-feedback"></div>
                        </div>

                        <!-- Testimonial Content -->
                        <div class="mb-3">
                            <label for="content" class="form-label">Testimonial Content *</label>
                            <textarea class="form-control" id="content" name="content" rows="5" required placeholder="Share the client's feedback about your work..."></textarea>
                            <div class="form-text">Write the testimonial content as provided by the client</div>
                            <div class="invalid-feedback"></div>
                        </div>

                        <!-- Rating and Project -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="rating" class="form-label">Rating</label>
                                <select class="form-select" id="rating" name="rating">
                                    <option value="">Select rating</option>
                                    <option value="5">5 Stars (Excellent)</option>
                                    <option value="4">4 Stars (Very Good)</option>
                                    <option value="3">3 Stars (Good)</option>
                                    <option value="2">2 Stars (Fair)</option>
                                    <option value="1">1 Star (Poor)</option>
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-6">
                                <label for="project_id" class="form-label">Related Project</label>
                                <select class="form-select" id="project_id" name="project_id">
                                    <option value="">Select project (optional)</option>
                                    <!-- Projects will be loaded via AJAX -->
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>

                        <!-- Status Options -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="is_active" name="is_active" checked>
                                    <label class="form-check-label" for="is_active">
                                        Active (Visible on website)
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="is_featured" name="is_featured">
                                    <label class="form-check-label" for="is_featured">
                                        Featured (Priority display)
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.testimonials.index') }}" class="btn btn-outline-secondary">
                                <i class="bx bx-x me-1"></i>Cancel
                            </a>
                            <button type="submit" class="btn btn-primary" id="submit-btn">
                                <span class="spinner-border spinner-border-sm me-1" style="display: none;" id="submit-spinner"></span>
                                <i class="bx bx-check me-1" id="submit-icon"></i>Create Testimonial
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Preview Column -->
        <div class="col-lg-4">
            <div class="card sticky-top" style="top: 1rem;">
                <div class="card-header">
                    <h6 class="card-title mb-0">Live Preview</h6>
                </div>
                <div class="card-body">
                    <div id="testimonial-preview">
                        <div class="text-center text-muted py-4">
                            <i class="bx bx-chat" style="font-size: 48px;"></i>
                            <p class="mt-2">Fill out the form to see preview</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    loadProjects();
    setupFormListeners();
    setupFormSubmission();
});

// Load available projects
function loadProjects() {
    fetch('/projects?all=1', {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.data) {
            const select = document.getElementById('project_id');
            data.data.forEach(project => {
                const option = document.createElement('option');
                option.value = project.id;
                option.textContent = project.title;
                select.appendChild(option);
            });
        }
    })
    .catch(error => console.error('Failed to load projects:', error));
}

// Setup form listeners for live preview
function setupFormListeners() {
    const form = document.getElementById('testimonial-form');
    const inputs = form.querySelectorAll('input, textarea, select');
    
    inputs.forEach(input => {
        input.addEventListener('input', updatePreview);
        input.addEventListener('change', updatePreview);
    });

    // Image preview
    document.getElementById('client_image').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                updatePreview();
            };
            reader.readAsDataURL(file);
        } else {
            updatePreview();
        }
    });
}

// Update live preview
function updatePreview() {
    const formData = new FormData(document.getElementById('testimonial-form'));
    const preview = document.getElementById('testimonial-preview');
    
    const clientName = formData.get('client_name') || 'Client Name';
    const clientPosition = formData.get('client_position');
    const clientCompany = formData.get('client_company');
    const content = formData.get('content') || 'Testimonial content will appear here...';
    const rating = parseInt(formData.get('rating'));
    const isActive = formData.has('is_active');
    const isFeatured = formData.has('is_featured');
    
    // Get image preview
    let imagePreview = '';
    const imageFile = document.getElementById('client_image').files[0];
    if (imageFile) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const imgSrc = e.target.result;
            renderPreview(clientName, clientPosition, clientCompany, content, rating, isActive, isFeatured, imgSrc);
        };
        reader.readAsDataURL(imageFile);
    } else {
        renderPreview(clientName, clientPosition, clientCompany, content, rating, isActive, isFeatured);
    }
}

function renderPreview(clientName, clientPosition, clientCompany, content, rating, isActive, isFeatured, imageSrc = null) {
    const preview = document.getElementById('testimonial-preview');
    
    // Build position/company text
    let positionCompany = '';
    if (clientPosition && clientCompany) {
        positionCompany = `${clientPosition} at ${clientCompany}`;
    } else if (clientPosition) {
        positionCompany = clientPosition;
    } else if (clientCompany) {
        positionCompany = clientCompany;
    }

    // Build rating stars
    let ratingStars = '';
    if (rating) {
        ratingStars = `
            <div class="rating mb-2">
                ${'★'.repeat(rating)}${'☆'.repeat(5 - rating)}
                <small class="text-muted ms-1">(${rating}/5)</small>
            </div>
        `;
    }

    // Build status badges
    let statusBadges = '';
    const badges = [];
    if (isFeatured) badges.push('<span class="badge bg-warning">Featured</span>');
    if (isActive) badges.push('<span class="badge bg-success">Active</span>');
    if (!isActive) badges.push('<span class="badge bg-secondary">Inactive</span>');
    if (badges.length > 0) {
        statusBadges = `<div class="mb-2">${badges.join(' ')}</div>`;
    }

    preview.innerHTML = `
        <div class="testimonial-card border rounded p-3">
            ${statusBadges}
            
            <div class="d-flex align-items-start mb-3">
                ${imageSrc ? 
                    `<img src="${imageSrc}" alt="${clientName}" class="rounded-circle me-3" style="width: 50px; height: 50px; object-fit: cover;">` :
                    `<div class="avatar-initial rounded-circle bg-label-primary me-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                        ${clientName.charAt(0).toUpperCase()}
                    </div>`
                }
                <div class="flex-grow-1">
                    <h6 class="mb-1">${clientName}</h6>
                    ${positionCompany ? `<small class="text-muted">${positionCompany}</small>` : ''}
                </div>
            </div>
            
            ${ratingStars}
            
            <blockquote class="mb-0">
                <p class="fst-italic">"${content}"</p>
            </blockquote>
        </div>
    `;
}

// Setup form submission
function setupFormSubmission() {
    document.getElementById('testimonial-form').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const submitBtn = document.getElementById('submit-btn');
        const submitSpinner = document.getElementById('submit-spinner');
        const submitIcon = document.getElementById('submit-icon');
        
        // Show loading state
        submitBtn.disabled = true;
        submitSpinner.style.display = 'inline-block';
        submitIcon.style.display = 'none';
        
        const formData = new FormData(this);
        
        fetch('{{ route("admin.testimonials.store") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => {
            console.log('Response status:', response.status);
            console.log('Response headers:', response.headers);
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            console.log('Response data:', data);
            if (data.success) {
                toastr.success(data.message);
                setTimeout(() => {
                    window.location.href = '{{ route("admin.testimonials.index") }}';
                }, 1500);
            } else {
                handleFormErrors(data.errors || {});
                toastr.error(data.message || 'Please fix the errors below');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            toastr.error('Network error occurred');
        })
        .finally(() => {
            // Reset button state
            submitBtn.disabled = false;
            submitSpinner.style.display = 'none';
            submitIcon.style.display = 'inline-block';
        });
    });
}

// Handle form validation errors
function handleFormErrors(errors) {
    // Clear previous errors
    document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
    document.querySelectorAll('.invalid-feedback').forEach(el => el.textContent = '');
    
    // Show new errors
    for (const [field, messages] of Object.entries(errors)) {
        const input = document.querySelector(`[name="${field}"]`);
        if (input) {
            input.classList.add('is-invalid');
            const feedback = input.parentNode.querySelector('.invalid-feedback');
            if (feedback) {
                feedback.textContent = messages[0];
            }
        }
    }
}


// Initial preview update
updatePreview();
</script>
@endpush
@endsection