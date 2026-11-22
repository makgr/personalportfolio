@extends('layouts.app')

@section('title', 'Create New Blog Post')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <!-- Header -->
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <span class="text-muted fw-light">Content / Blog Posts /</span> Create New Post
                    </h5>
                    <a href="{{ route('blog-posts.index') }}" class="btn btn-secondary">
                        <i class="bx bx-arrow-back me-1"></i>Back to Posts
                    </a>
                </div>
            </div>
        </div>
    </div>

    <form id="blogPostForm" data-ajax="{{ route('blog-posts.store') }}" enctype="multipart/form-data">
        @csrf
        
        <div class="row">
            <!-- Main Form -->
            <div class="col-md-8">
                <!-- Basic Information -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Post Content</h5>
                    </div>
                    <div class="card-body">
                        <!-- Title -->
                        <div class="mb-3">
                            <label class="form-label" for="title">Post Title <span class="text-danger">*</span></label>
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
                            <div class="form-text" id="slug-preview">URL will be: <span id="slug-url">/blog/your-post-title</span></div>
                            @error('slug')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Excerpt -->
                        <div class="mb-3">
                            <label class="form-label" for="excerpt">Excerpt <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('excerpt') is-invalid @enderror" 
                                      id="excerpt" name="excerpt" rows="3" 
                                      maxlength="300" required>{{ old('excerpt') }}</textarea>
                            <div class="form-text">
                                <span id="excerpt-count">0</span>/300 characters. This will be shown in post previews.
                            </div>
                            @error('excerpt')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Content -->
                        <div class="mb-3">
                            <label class="form-label" for="content">Post Content <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('content') is-invalid @enderror" 
                                      id="content" name="content" rows="15" 
                                      required>{{ old('content') }}</textarea>
                            @error('content')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- SEO & Tags -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">SEO & Tags</h5>
                    </div>
                    <div class="card-body">
                        <!-- Tags -->
                        <div class="mb-3">
                            <label class="form-label" for="tags_input">Tags</label>
                            <input type="text" class="form-control" id="tags_input" 
                                   placeholder="Enter tag and press Enter">
                            <div class="mt-2" id="tags-display">
                                <!-- Dynamic tags will appear here -->
                            </div>
                            <input type="hidden" name="tags" id="tags_hidden">
                        </div>

                        <!-- Meta Tags -->
                        <div class="mb-3">
                            <label class="form-label" for="meta_title">Meta Title</label>
                            <input type="text" class="form-control @error('meta_tags.title') is-invalid @enderror" 
                                   id="meta_title" name="meta_tags[title]" value="{{ old('meta_tags.title') }}" 
                                   maxlength="60">
                            <div class="form-text">
                                <span id="meta-title-count">0</span>/60 characters
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="meta_description">Meta Description</label>
                            <textarea class="form-control @error('meta_tags.description') is-invalid @enderror" 
                                      id="meta_description" name="meta_tags[description]" rows="2" 
                                      maxlength="160">{{ old('meta_tags.description') }}</textarea>
                            <div class="form-text">
                                <span id="meta-desc-count">0</span>/160 characters
                            </div>
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
                                <option value="draft" {{ old('status', 'draft') == 'draft' ? 'selected' : '' }}>Draft</option>
                                <option value="published" {{ old('status') == 'published' ? 'selected' : '' }}>Published</option>
                                <option value="scheduled" {{ old('status') == 'scheduled' ? 'selected' : '' }}>Scheduled</option>
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
                                    Featured Post
                                </label>
                            </div>
                        </div>

                        <!-- Allow Comments -->
                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="allow_comments" 
                                       id="allow_comments" value="1" {{ old('allow_comments', true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="allow_comments">
                                    Allow Comments
                                </label>
                            </div>
                        </div>

                        <!-- Scheduled Date (if status is scheduled) -->
                        <div class="mb-3" id="scheduled_at_group" style="display: none;">
                            <label class="form-label" for="scheduled_at">Scheduled Date</label>
                            <input type="datetime-local" class="form-control @error('scheduled_at') is-invalid @enderror" 
                                   id="scheduled_at" name="scheduled_at" value="{{ old('scheduled_at') }}">
                            @error('scheduled_at')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Post Details -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Post Details</h5>
                    </div>
                    <div class="card-body">
                        <!-- Category -->
                        <div class="mb-3">
                            <label class="form-label" for="category_id">Category</label>
                            <select class="form-select @error('category_id') is-invalid @enderror" name="category_id">
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
                    </div>
                </div>

                <!-- Featured Image -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Featured Image</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <input type="file" class="form-control @error('featured_image') is-invalid @enderror" 
                                   id="featured_image" name="featured_image" accept="image/*">
                            <div class="form-text">Recommended size: 1200x630px, Max: 2MB</div>
                            @error('featured_image')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <!-- Image preview -->
                        <div id="image-preview" class="d-none">
                            <img id="preview-img" src="" alt="Preview" class="img-fluid rounded mb-2">
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="card">
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <span class="btn-text">
                                    <i class="bx bx-save me-1"></i>Create Blog Post
                                </span>
                                <span class="btn-loading d-none">
                                    <span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                                    Creating...
                                </span>
                            </button>
                            <a href="{{ route('blog-posts.index') }}" class="btn btn-secondary">
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
    let tags = [];

    // Auto-generate slug from title
    $('#title').on('input', function() {
        const title = $(this).val();
        const slug = title.toLowerCase()
            .replace(/[^\w\s-]/g, '')
            .replace(/[\s_-]+/g, '-')
            .replace(/^-+|-+$/g, '');
        $('#slug').val(slug);
        $('#slug-url').text('/blog/' + (slug || 'your-post-title'));
    });

    // Character counters
    $('#excerpt').on('input', function() {
        $('#excerpt-count').text($(this).val().length);
    });

    $('#meta_title').on('input', function() {
        $('#meta-title-count').text($(this).val().length);
    });

    $('#meta_description').on('input', function() {
        $('#meta-desc-count').text($(this).val().length);
    });

    // Status change handler
    $('select[name="status"]').on('change', function() {
        const scheduled = $('#scheduled_at_group');
        if ($(this).val() === 'scheduled') {
            scheduled.show();
            $('#scheduled_at').attr('required', true);
        } else {
            scheduled.hide();
            $('#scheduled_at').attr('required', false);
        }
    });

    // Tags management
    // Typed text counts on Enter or on leaving the box; commas split it into several tags
    function addTypedTags(input) {
        input.val().split(',').map(t => t.trim()).filter(t => t && !tags.includes(t)).forEach(t => tags.push(t));
        input.val('');
        updateTagsDisplay();
    }
    $('#tags_input').on('keypress', function(e) {
        if (e.which === 13) {
            e.preventDefault();
            addTypedTags($(this));
        }
    }).on('blur', function() {
        addTypedTags($(this));
    });

    function updateTagsDisplay() {
        const container = $('#tags-display');
        container.empty();
        
        tags.forEach((tag, index) => {
            container.append(`
                <span class="badge bg-primary me-1 mb-1">
                    ${tag}
                    <button type="button" class="btn-close btn-close-white btn-sm ms-1" 
                            onclick="removeTag(${index})" aria-label="Remove ${tag}"></button>
                </span>
            `);
        });
        
        $('#tags_hidden').val(JSON.stringify(tags));
    }

    window.removeTag = function(index) {
        tags.splice(index, 1);
        updateTagsDisplay();
    };

    // Image preview
    $('#featured_image').on('change', function() {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                $('#preview-img').attr('src', e.target.result);
                $('#image-preview').removeClass('d-none');
            };
            reader.readAsDataURL(file);
        } else {
            $('#image-preview').addClass('d-none');
        }
    });

    // Initialize character counters
    $('#excerpt-count').text($('#excerpt').val().length);
    $('#meta-title-count').text($('#meta_title').val().length);
    $('#meta-desc-count').text($('#meta_description').val().length);
});
</script>
@endpush