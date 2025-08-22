@extends('layouts.app')

@section('title', 'Create Project')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <!-- Header -->
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <span class="text-muted fw-light">Portfolio / Projects /</span> Create New
                    </h5>
                    <a href="{{ route('projects.index') }}" class="btn btn-secondary">
                        <i class="bx bx-arrow-back me-1"></i>Back to Projects
                    </a>
                </div>
            </div>
        </div>
    </div>

    <form id="projectForm" data-ajax="{{ route('projects.store') }}" enctype="multipart/form-data">
        @csrf
        <div class="row">
            <!-- Main Form -->
            <div class="col-md-8">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Project Information</h5>
                    </div>
                    <div class="card-body">
                        <!-- Title -->
                        <div class="mb-3">
                            <label class="form-label" for="title">Project Title <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                   id="title" name="title" value="{{ old('title') }}" required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Slug -->
                        <div class="mb-3">
                            <label class="form-label" for="slug">URL Slug</label>
                            <input type="text" class="form-control @error('slug') is-invalid @enderror" 
                                   id="slug" name="slug" value="{{ old('slug') }}">
                            <div class="form-text">Leave empty to auto-generate from title</div>
                            @error('slug')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Short Description -->
                        <div class="mb-3">
                            <label class="form-label" for="short_description">Short Description <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('short_description') is-invalid @enderror" 
                                      id="short_description" name="short_description" rows="2" 
                                      maxlength="500" required>{{ old('short_description') }}</textarea>
                            <div class="form-text">
                                <span id="desc-count">0</span>/500 characters
                            </div>
                            @error('short_description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Description -->
                        <div class="mb-3">
                            <label class="form-label" for="description">Full Description <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="8" 
                                      required>{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Project URLs -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="project_url">Project URL</label>
                                    <input type="url" class="form-control @error('project_url') is-invalid @enderror" 
                                           id="project_url" name="project_url" value="{{ old('project_url') }}">
                                    @error('project_url')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="github_url">GitHub URL</label>
                                    <input type="url" class="form-control @error('github_url') is-invalid @enderror" 
                                           id="github_url" name="github_url" value="{{ old('github_url') }}">
                                    @error('github_url')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Demo URL -->
                        <div class="mb-3">
                            <label class="form-label" for="demo_url">Demo URL</label>
                            <input type="url" class="form-control @error('demo_url') is-invalid @enderror" 
                                   id="demo_url" name="demo_url" value="{{ old('demo_url') }}">
                            @error('demo_url')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Technologies & Features -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Technologies & Features</h5>
                    </div>
                    <div class="card-body">
                        <!-- Tech Stack -->
                        <div class="mb-3">
                            <label class="form-label" for="tech_stack">Technologies Used</label>
                            <input type="text" class="form-control" id="tech_stack_input" 
                                   placeholder="Enter technology and press Enter">
                            <div class="mt-2" id="tech-stack-tags">
                                <!-- Dynamic tech stack tags will appear here -->
                            </div>
                            <input type="hidden" name="tech_stack" id="tech_stack_hidden">
                        </div>

                        <!-- Features -->
                        <div class="mb-3">
                            <label class="form-label" for="features">Key Features</label>
                            <div id="features-container">
                                <div class="input-group mb-2">
                                    <input type="text" class="form-control" name="features[]" 
                                           placeholder="Enter a feature">
                                    <button class="btn btn-outline-danger remove-feature" type="button" style="display: none;">
                                        <i class="bx bx-minus"></i>
                                    </button>
                                </div>
                            </div>
                            <button type="button" class="btn btn-outline-primary btn-sm" id="add-feature">
                                <i class="bx bx-plus me-1"></i>Add Feature
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-md-4">
                <!-- Publish Settings -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Publish Settings</h5>
                    </div>
                    <div class="card-body">
                        <!-- Status -->
                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <select class="form-select @error('status') is-invalid @enderror" name="status">
                                <option value="draft" {{ old('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                                <option value="published" {{ old('status') == 'published' ? 'selected' : '' }}>Published</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Featured -->
                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="is_featured" 
                                       id="is_featured" value="1" {{ old('is_featured') ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_featured">
                                    Featured Project
                                </label>
                            </div>
                            <div class="form-text">Featured projects appear prominently on your portfolio</div>
                        </div>

                        <!-- Project Date -->
                        <div class="mb-3">
                            <label class="form-label" for="project_date">Project Date</label>
                            <input type="date" class="form-control @error('project_date') is-invalid @enderror" 
                                   id="project_date" name="project_date" value="{{ old('project_date') }}">
                            @error('project_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Project Details -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Project Details</h5>
                    </div>
                    <div class="card-body">
                        <!-- Category -->
                        <div class="mb-3">
                            <label class="form-label" for="category_id">Category</label>
                            <select class="form-select @error('category_id') is-invalid @enderror" 
                                    name="category_id">
                                <option value="">Select Category</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" 
                                            {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Type -->
                        <div class="mb-3">
                            <label class="form-label" for="type">Project Type</label>
                            <select class="form-select @error('type') is-invalid @enderror" name="type">
                                <option value="web" {{ old('type') == 'web' ? 'selected' : '' }}>Web Application</option>
                                <option value="mobile" {{ old('type') == 'mobile' ? 'selected' : '' }}>Mobile App</option>
                                <option value="desktop" {{ old('type') == 'desktop' ? 'selected' : '' }}>Desktop App</option>
                                <option value="design" {{ old('type') == 'design' ? 'selected' : '' }}>Design</option>
                                <option value="other" {{ old('type') == 'other' ? 'selected' : '' }}>Other</option>
                            </select>
                            @error('type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Client -->
                        <div class="mb-3">
                            <label class="form-label" for="client_name">Client Name</label>
                            <input type="text" class="form-control @error('client_name') is-invalid @enderror" 
                                   id="client_name" name="client_name" value="{{ old('client_name') }}">
                            @error('client_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Duration -->
                        <div class="mb-3">
                            <label class="form-label" for="duration_weeks">Duration (weeks)</label>
                            <input type="number" class="form-control @error('duration_weeks') is-invalid @enderror" 
                                   id="duration_weeks" name="duration_weeks" value="{{ old('duration_weeks') }}" min="1">
                            @error('duration_weeks')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- My Role -->
                        <div class="mb-3">
                            <label class="form-label" for="my_role">My Role</label>
                            <input type="text" class="form-control @error('my_role') is-invalid @enderror" 
                                   id="my_role" name="my_role" value="{{ old('my_role') }}" 
                                   placeholder="e.g., Full Stack Developer">
                            @error('my_role')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Media -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Project Media</h5>
                    </div>
                    <div class="card-body">
                        <!-- Featured Image -->
                        <div class="mb-3">
                            <label class="form-label" for="featured_image">Featured Image</label>
                            <input type="file" class="form-control @error('featured_image') is-invalid @enderror" 
                                   id="featured_image" name="featured_image" accept="image/png,image/jpeg">
                            <div class="form-text">Recommended size: 1200x800px, Max: 2MB</div>
                            @error('featured_image')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <!-- Image Preview -->
                            <div class="mt-3" id="featured-image-preview" style="display: none;">
                                <img id="featured-image-display" class="img-fluid rounded" style="max-height: 200px; opacity: 0.7; border: 2px dashed #007bff;">
                                <div class="mt-2">
                                    <small class="text-info">
                                        <i class="bx bx-info-circle me-1"></i>
                                        Image selected. Save project to upload.
                                    </small>
                                </div>
                            </div>
                        </div>

                        <!-- Gallery -->
                        <div class="mb-3">
                            <label class="form-label" for="gallery">Gallery Images</label>
                            <input type="file" class="form-control @error('gallery') is-invalid @enderror" 
                                   id="gallery" name="gallery[]" accept="image/*" multiple>
                            <div class="form-text">You can select multiple images</div>
                            @error('gallery')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="card">
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <span class="btn-text">
                                    <i class="bx bx-save me-1"></i>Create Project
                                </span>
                                <span class="btn-loading d-none">
                                    <span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                                    Creating...
                                </span>
                            </button>
                            <a href="{{ route('projects.index') }}" class="btn btn-secondary">
                                <i class="bx bx-x me-1"></i>Cancel
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Auto-generate slug from title
    $('#title').on('input', function() {
        const title = $(this).val();
        const slug = title.toLowerCase()
            .replace(/[^\w\s-]/g, '')
            .replace(/[\s_-]+/g, '-')
            .replace(/^-+|-+$/g, '');
        $('#slug').val(slug);
    });

    // Character counter for short description
    $('#short_description').on('input', function() {
        $('#desc-count').text($(this).val().length);
    });

    // Tech Stack Management
    let techStack = [];
    
    $('#tech_stack_input').on('keypress', function(e) {
        if (e.which === 13) { // Enter key
            e.preventDefault();
            const tech = $(this).val().trim();
            if (tech && !techStack.includes(tech)) {
                techStack.push(tech);
                updateTechStackDisplay();
                $(this).val('');
            }
        }
    });

    function updateTechStackDisplay() {
        const container = $('#tech-stack-tags');
        container.empty();
        
        techStack.forEach((tech, index) => {
            container.append(`
                <span class="badge bg-primary me-1 mb-1">
                    ${tech}
                    <button type="button" class="btn-close btn-close-white btn-sm ms-1" 
                            onclick="removeTech(${index})" aria-label="Remove ${tech}"></button>
                </span>
            `);
        });
        
        $('#tech_stack_hidden').val(JSON.stringify(techStack));
    }

    window.removeTech = function(index) {
        techStack.splice(index, 1);
        updateTechStackDisplay();
    };

    // Features Management
    $('#add-feature').click(function() {
        const container = $('#features-container');
        const newFeature = `
            <div class="input-group mb-2">
                <input type="text" class="form-control" name="features[]" placeholder="Enter a feature">
                <button class="btn btn-outline-danger remove-feature" type="button">
                    <i class="bx bx-minus"></i>
                </button>
            </div>
        `;
        container.append(newFeature);
        updateRemoveButtons();
    });

    $(document).on('click', '.remove-feature', function() {
        $(this).closest('.input-group').remove();
        updateRemoveButtons();
    });

    function updateRemoveButtons() {
        const features = $('.remove-feature');
        if (features.length === 1) {
            features.hide();
        } else {
            features.show();
        }
    }

    // Featured Image Preview
    $('#featured_image').on('change', function(e) {
        const file = e.target.files[0];
        const preview = $('#featured-image-preview');
        const display = $('#featured-image-display');
        
        if (file) {
            // Validate file type
            const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];
            if (!allowedTypes.includes(file.type)) {
                toastr.error('Please select a valid image file (JPG, PNG)');
                this.value = '';
                preview.hide();
                return;
            }
            
            // Validate file size (2MB)
            if (file.size > 2 * 1024 * 1024) {
                toastr.error('Image size must be less than 2MB');
                this.value = '';
                preview.hide();
                return;
            }
            
            // Show instant preview
            const reader = new FileReader();
            reader.onload = function(e) {
                display.attr('src', e.target.result);
                preview.show();
                toastr.info('Featured image selected. Save project to upload.', 'Image Ready');
            };
            reader.readAsDataURL(file);
        } else {
            preview.hide();
        }
    });

    // Add AJAX success callback to redirect to projects list
    const projectForm = document.getElementById('projectForm');
    if (projectForm) {
        projectForm.dataset.onSuccess = `
            toastr.success(data.message || 'Project created successfully!');
            setTimeout(() => {
                window.location.href = '{{ route("projects.index") }}';
            }, 1500);
        `;
    }

    // Initialize character count
    $('#desc-count').text($('#short_description').val().length);
});
</script>
@endpush