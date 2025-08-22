@extends('layouts.app')

@section('title', 'Create New Service')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <!-- Header -->
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <span class="text-muted fw-light">Portfolio / Services /</span> Create New Service
                    </h5>
                    <a href="{{ route('services.index') }}" class="btn btn-secondary">
                        <i class="bx bx-arrow-back me-1"></i>Back to Services
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
                    <h5 class="card-title mb-0">Service Information</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('services.store') }}" method="POST" id="serviceForm" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="title" class="form-label">Service Title <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                       id="title" name="title" value="{{ old('title') }}" required>
                                @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="icon" class="form-label">Icon Class</label>
                                <input type="text" class="form-control @error('icon') is-invalid @enderror" 
                                       id="icon" name="icon" value="{{ old('icon') }}" 
                                       placeholder="e.g., bx-briefcase, fa-code, fab fa-wordpress">
                                <small class="text-muted">Use Boxicons (bx-*) or Font Awesome (fa-, fab, fas, far, fal)</small>
                                @error('icon')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="short_description" class="form-label">Short Description <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('short_description') is-invalid @enderror" 
                                      id="short_description" name="short_description" rows="2" 
                                      placeholder="Brief description for service cards" maxlength="500" required>{{ old('short_description') }}</textarea>
                            <small class="text-muted"><span id="shortDescCounter">0</span>/500 characters</small>
                            @error('short_description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Full Description <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="5" 
                                      placeholder="Detailed description of the service" required>{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="starting_price" class="form-label">Starting Price ($)</label>
                                <input type="number" class="form-control @error('starting_price') is-invalid @enderror" 
                                       id="starting_price" name="starting_price" value="{{ old('starting_price') }}" 
                                       min="0" step="0.01" placeholder="0.00">
                                <small class="text-muted">Leave empty for "Contact for pricing"</small>
                                @error('starting_price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="price_unit" class="form-label">Price Unit</label>
                                <select class="form-select @error('price_unit') is-invalid @enderror" 
                                        id="price_unit" name="price_unit">
                                    <option value="">Select Unit</option>
                                    @foreach($priceUnits as $key => $label)
                                        <option value="{{ $key }}" {{ old('price_unit') == $key ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('price_unit')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="image" class="form-label">Service Image</label>
                                <input type="file" class="form-control @error('image') is-invalid @enderror" 
                                       id="image" name="image" accept="image/*">
                                <small class="text-muted">Optional service illustration</small>
                                @error('image')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="features" class="form-label">Service Features</label>
                            <input type="text" class="form-control @error('features') is-invalid @enderror" 
                                   id="features" name="features" value="{{ old('features') }}" 
                                   placeholder="Feature 1, Feature 2, Feature 3">
                            <small class="text-muted">Comma-separated list of service features</small>
                            @error('features')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="technologies" class="form-label">Technologies Used</label>
                            <input type="text" class="form-control @error('technologies') is-invalid @enderror" 
                                   id="technologies" name="technologies" value="{{ old('technologies') }}" 
                                   placeholder="React, Laravel, MySQL, AWS">
                            <small class="text-muted">Comma-separated list of technologies</small>
                            @error('technologies')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" 
                                       id="is_active" name="is_active" value="1" 
                                       {{ old('is_active', true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">
                                    <i class="bx bx-check-circle text-success"></i> Active
                                </label>
                                <small class="form-text text-muted d-block">Show this service on the website</small>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('services.index') }}" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary" id="submitBtn">
                                <i class="bx bx-check me-1"></i><span class="btn-text">Create Service</span>
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
                    <div class="text-center" id="servicePreview">
                        <div class="mb-3">
                            <i id="previewIcon" class="bx bx-briefcase text-muted" style="font-size: 2.5rem;"></i>
                        </div>
                        <h6 id="previewTitle" class="mb-2">Service Title</h6>
                        <p id="previewShortDesc" class="text-muted small mb-3">Short description will appear here...</p>
                        <div id="previewPrice" class="mb-3">
                            <span class="badge bg-info">Contact for pricing</span>
                        </div>
                        <div id="previewFeatures" class="mb-2">
                            <small class="text-muted">No features added</small>
                        </div>
                        <div id="previewTechs">
                            <small class="text-muted">No technologies added</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Image Preview -->
            <div class="card mt-3" id="imagePreviewCard" style="display: none;">
                <div class="card-header">
                    <h5 class="card-title mb-0">Image Preview</h5>
                </div>
                <div class="card-body">
                    <img id="imagePreview" class="img-fluid rounded" src="" alt="Service Image">
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
    $('#serviceForm').on('submit', function(e) {
        e.preventDefault();
        
        const submitBtn = $('#submitBtn');
        const btnText = submitBtn.find('.btn-text');
        const originalText = btnText.text();
        
        // Show loading state
        submitBtn.prop('disabled', true);
        btnText.html('<span class="spinner-border spinner-border-sm me-1" role="status"></span>Creating...');
        
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
                toastr.success(response.message || 'Service created successfully!');
                // Redirect to services index after short delay
                setTimeout(function() {
                    window.location.href = '{{ route("services.index") }}';
                }, 1500);
            } else {
                toastr.error(response.message || 'Failed to create service');
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
                toastr.error('Failed to create service. Please try again.');
            }
        })
        .always(function() {
            // Reset button state
            submitBtn.prop('disabled', false);
            btnText.text(originalText);
        });
    });
});

// Character counter for short description
$('#short_description').on('input', function() {
    const length = $(this).val().length;
    $('#shortDescCounter').text(length);
    updatePreview();
});

// Image preview
$('#image').on('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            $('#imagePreview').attr('src', e.target.result);
            $('#imagePreviewCard').show();
        };
        reader.readAsDataURL(file);
    } else {
        $('#imagePreviewCard').hide();
    }
});

function updatePreview() {
    const title = $('#title').val() || 'Service Title';
    const shortDesc = $('#short_description').val() || 'Short description will appear here...';
    const icon = $('#icon').val() || 'bx-briefcase';
    const startingPrice = $('#starting_price').val();
    const priceUnit = $('#price_unit').val();
    const features = $('#features').val();
    const technologies = $('#technologies').val();
    
    // Update preview
    $('#previewTitle').text(title);
    $('#previewShortDesc').text(shortDesc);
    
    // Update icon
    const iconElement = $('#previewIcon');
    iconElement.removeClass().addClass('text-muted');
    if (icon.startsWith('bx-')) {
        // Boxicons
        iconElement.addClass('bx ' + icon);
    } else if (icon.startsWith('fa-')) {
        // Font Awesome without prefix - assume solid
        iconElement.addClass('fas ' + icon);
    } else if (icon.match(/^(fab|fas|far|fal|fad)\s/)) {
        // Font Awesome with prefix
        iconElement.addClass(icon);
    } else if (icon) {
        // Any other icon class
        iconElement.addClass(icon);
    } else {
        iconElement.addClass('bx bx-briefcase text-muted');
    }
    iconElement.css('font-size', '2.5rem');
    
    // Update price
    let priceHtml = '<span class="badge bg-info">Contact for pricing</span>';
    if (startingPrice) {
        const formattedPrice = '$' + parseFloat(startingPrice).toFixed(2);
        const unitText = priceUnit ? ' / ' + priceUnit.charAt(0).toUpperCase() + priceUnit.slice(1) : '';
        priceHtml = '<span class="badge bg-primary">' + formattedPrice + unitText + '</span>';
    }
    $('#previewPrice').html(priceHtml);
    
    // Update features
    if (features) {
        const featureList = features.split(',').map(f => f.trim()).filter(f => f);
        if (featureList.length > 0) {
            const featureHtml = featureList.slice(0, 3).map(f => 
                '<span class="badge bg-info me-1 mb-1">' + f + '</span>'
            ).join('');
            const moreText = featureList.length > 3 ? '<small class="text-muted">+' + (featureList.length - 3) + ' more</small>' : '';
            $('#previewFeatures').html(featureHtml + moreText);
        } else {
            $('#previewFeatures').html('<small class="text-muted">No features added</small>');
        }
    } else {
        $('#previewFeatures').html('<small class="text-muted">No features added</small>');
    }
    
    // Update technologies
    if (technologies) {
        const techList = technologies.split(',').map(t => t.trim()).filter(t => t);
        if (techList.length > 0) {
            const techHtml = techList.slice(0, 3).map(t => 
                '<span class="badge bg-warning me-1 mb-1">' + t + '</span>'
            ).join('');
            const moreText = techList.length > 3 ? '<small class="text-muted">+' + (techList.length - 3) + ' more</small>' : '';
            $('#previewTechs').html(techHtml + moreText);
        } else {
            $('#previewTechs').html('<small class="text-muted">No technologies added</small>');
        }
    } else {
        $('#previewTechs').html('<small class="text-muted">No technologies added</small>');
    }
}

// Update preview when inputs change
$(document).ready(function() {
    const inputs = ['title', 'short_description', 'icon', 'starting_price', 'price_unit', 'features', 'technologies'];
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