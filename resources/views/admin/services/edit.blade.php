@extends('layouts.app')

@section('title', 'Edit Service - ' . $service->title)

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <!-- Header -->
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <span class="text-muted fw-light">Portfolio / Services /</span> Edit {{ $service->title }}
                    </h5>
                    <div class="d-flex gap-2">
                        <a href="{{ route('services.show', $service->id) }}" class="btn btn-info btn-sm">
                            <i class="bx bx-show me-1"></i>View
                        </a>
                        <a href="{{ route('services.index') }}" class="btn btn-secondary">
                            <i class="bx bx-arrow-back me-1"></i>Back to Services
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Form -->
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Service Information</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('services.update', $service->id) }}" method="POST" id="serviceForm" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="title" class="form-label">Service Title <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                       id="title" name="title" value="{{ old('title', $service->title) }}" required>
                                @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="icon" class="form-label">Icon Class</label>
                                <input type="text" class="form-control @error('icon') is-invalid @enderror" 
                                       id="icon" name="icon" value="{{ old('icon', $service->icon) }}" 
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
                                      placeholder="Brief description for service cards" maxlength="500" required>{{ old('short_description', $service->short_description) }}</textarea>
                            <small class="text-muted"><span id="shortDescCounter">{{ strlen($service->short_description) }}</span>/500 characters</small>
                            @error('short_description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Full Description <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="5" 
                                      placeholder="Detailed description of the service" required>{{ old('description', $service->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="starting_price" class="form-label">Starting Price ($)</label>
                                <input type="number" class="form-control @error('starting_price') is-invalid @enderror" 
                                       id="starting_price" name="starting_price" value="{{ old('starting_price', $service->starting_price) }}" 
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
                                        <option value="{{ $key }}" {{ old('price_unit', $service->price_unit) == $key ? 'selected' : '' }}>
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
                                   id="features" name="features" 
                                   value="{{ old('features', is_array($service->features) ? implode(', ', $service->features) : '') }}" 
                                   placeholder="Feature 1, Feature 2, Feature 3">
                            <small class="text-muted">Comma-separated list of service features</small>
                            @error('features')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="technologies" class="form-label">Technologies Used</label>
                            <input type="text" class="form-control @error('technologies') is-invalid @enderror" 
                                   id="technologies" name="technologies" 
                                   value="{{ old('technologies', is_array($service->technologies) ? implode(', ', $service->technologies) : '') }}" 
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
                                       {{ old('is_active', $service->is_active) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">
                                    <i class="bx bx-check-circle text-success"></i> Active
                                </label>
                                <small class="form-text text-muted d-block">Show this service on the website</small>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('services.index') }}" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary" id="submitBtn">
                                <i class="bx bx-check me-1"></i><span class="btn-text">Update Service</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Preview and Status -->
        <div class="col-md-4">
            <!-- Preview Card -->
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="card-title mb-0">Preview</h5>
                </div>
                <div class="card-body">
                    <div class="text-center" id="servicePreview">
                        <div class="mb-3">
                            <i id="previewIcon" class="bx bx-briefcase text-muted" style="font-size: 2.5rem;"></i>
                        </div>
                        <h6 id="previewTitle" class="mb-2">{{ $service->title }}</h6>
                        <p id="previewShortDesc" class="text-muted small mb-3">{{ $service->short_description }}</p>
                        <div id="previewPrice" class="mb-3">
                            @if($service->starting_price)
                                <span class="badge bg-primary">${{ number_format($service->starting_price, 2) }}{{ $service->price_unit ? ' / ' . ucfirst($service->price_unit) : '' }}</span>
                            @else
                                <span class="badge bg-info">Contact for pricing</span>
                            @endif
                        </div>
                        <div id="previewFeatures" class="mb-2">
                            @if($service->features && count($service->features) > 0)
                                @foreach($service->features as $feature)
                                    <span class="badge bg-info me-1 mb-1">{{ $feature }}</span>
                                @endforeach
                            @else
                                <small class="text-muted">No features added</small>
                            @endif
                        </div>
                        <div id="previewTechs">
                            @if($service->technologies && count($service->technologies) > 0)
                                @foreach($service->technologies as $tech)
                                    <span class="badge bg-warning me-1 mb-1">{{ $tech }}</span>
                                @endforeach
                            @else
                                <small class="text-muted">No technologies added</small>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Current Status -->
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="card-title mb-0">Current Status</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span>Status:</span>
                        <span class="badge bg-{{ $service->is_active ? 'success' : 'secondary' }}">
                            {{ $service->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span>Slug:</span>
                        <code>{{ $service->slug }}</code>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span>Order:</span>
                        <span class="badge bg-info">{{ $service->order }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span>Created:</span>
                        <span>{{ $service->created_at->format('M d, Y') }}</span>
                    </div>
                </div>
            </div>

            <!-- Current Image -->
            @if($service->image)
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="card-title mb-0">Current Image</h5>
                </div>
                <div class="card-body text-center">
                    <img src="{{ asset($service->image) }}" class="img-fluid rounded" alt="{{ $service->title }}">
                    <small class="text-muted d-block mt-2">Upload a new image to replace</small>
                </div>
            </div>
            @endif

            <!-- New Image Preview -->
            <div class="card" id="imagePreviewCard" style="display: none;">
                <div class="card-header">
                    <h5 class="card-title mb-0">New Image Preview</h5>
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
        btnText.html('<span class="spinner-border spinner-border-sm me-1" role="status"></span>Updating...');
        
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
                toastr.success(response.message || 'Service updated successfully!');
                // Stay on edit page but show success
                // Optionally redirect to index after delay
                // setTimeout(function() {
                //     window.location.href = '{{ route("services.index") }}';
                // }, 2000);
            } else {
                toastr.error(response.message || 'Failed to update service');
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
                toastr.error('Failed to update service. Please try again.');
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
    const title = $('#title').val() || '{{ $service->title }}';
    const shortDesc = $('#short_description').val() || '{{ $service->short_description }}';
    const icon = $('#icon').val() || '{{ $service->icon ?? 'bx-briefcase' }}';
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