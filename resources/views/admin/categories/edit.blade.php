@extends('layouts.app')

@section('title', 'Edit Category')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <!-- Header -->
        <div class="row">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">
                            <span class="text-muted fw-light">Categories /</span> Edit Category
                        </h4>
                        <div class="d-flex gap-2">
                            <a href="{{ route('categories.index') }}" class="btn btn-outline-secondary">
                                <i class="bx bx-arrow-back me-1"></i>Back to Categories
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
                        <form id="categoryForm" data-ajax="{{ route('categories.update', $category->id) }}"
                            data-on-success="window.location.href = data.redirect">
                            @csrf
                            @method('PUT')

                            <!-- Basic Information -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h5 class="mb-3">Basic Information</h5>
                                </div>
                                <div class="col-md-6">
                                    <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                                        id="name" name="name" value="{{ old('name', $category->name) }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="slug" class="form-label">Slug</label>
                                    <input type="text" class="form-control @error('slug') is-invalid @enderror"
                                        id="slug" name="slug" value="{{ old('slug', $category->slug) }}"
                                        placeholder="Auto-generated">
                                    @error('slug')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">Leave empty for auto-generation from name</small>
                                </div>
                            </div>

                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <label for="type" class="form-label">Type <span class="text-danger">*</span></label>
                                    <select class="form-select @error('type') is-invalid @enderror" id="type"
                                        name="type" required>
                                        <option value="">Select Type</option>
                                        <option value="project"
                                            {{ old('type', $category->type) === 'project' ? 'selected' : '' }}>Project
                                        </option>
                                        <option value="blog"
                                            {{ old('type', $category->type) === 'blog' ? 'selected' : '' }}>Blog</option>
                                    </select>
                                    @error('type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="order" class="form-label">Display Order</label>
                                    <input type="number" class="form-control @error('order') is-invalid @enderror"
                                        id="order" name="order" value="{{ old('order', $category->order) }}"
                                        min="0">
                                    @error('order')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">Lower numbers appear first</small>
                                </div>
                            </div>

                            <div class="row mb-4">
                                <div class="col-12">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description"
                                        rows="3" placeholder="Optional description for this category">{{ old('description', $category->description) }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Styling -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h5 class="mb-3">Styling</h5>
                                </div>
                                <div class="col-md-6">
                                    <label for="color" class="form-label">Color</label>
                                    <input type="color"
                                        class="form-control form-control-color @error('color') is-invalid @enderror"
                                        id="color" name="color"
                                        value="{{ old('color', $category->color ?? '#6f6af8') }}">
                                    @error('color')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">Used for badges and visual elements</small>
                                </div>
                                <div class="col-md-6">
                                    <label for="icon" class="form-label">Icon Class</label>
                                    <input type="text" class="form-control @error('icon') is-invalid @enderror"
                                        id="icon" name="icon" value="{{ old('icon', $category->icon) }}"
                                        placeholder="e.g., bx bx-category">
                                    @error('icon')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">Bootstrap Icons (bx) or Font Awesome classes</small>
                                </div>
                            </div>

                            <!-- Status -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h5 class="mb-3">Status</h5>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-check form-switch">
                                        <input type="hidden" name="is_active" value="0">
                                        <input class="form-check-input @error('is_active') is-invalid @enderror"
                                            type="checkbox" id="is_active" name="is_active" value="1"
                                            {{ old('is_active', $category->is_active) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_active">
                                            Active
                                        </label>
                                        @error('is_active')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="text-muted d-block">Only active categories will be available for
                                            use</small>
                                    </div>
                                </div>
                            </div>

                            <!-- Content Information -->
                            @if ($category->projects()->count() > 0 || $category->blogPosts()->count() > 0)
                                <div class="row mb-4">
                                    <div class="col-12">
                                        <h5 class="mb-3">Content Usage</h5>
                                        <div class="alert alert-info">
                                            <i class="bx bx-info-circle me-2"></i>
                                            This category is currently being used by:
                                            @if ($category->projects()->count() > 0)
                                                <strong>{{ $category->projects()->count() }}</strong> project(s)
                                            @endif
                                            @if ($category->projects()->count() > 0 && $category->blogPosts()->count() > 0)
                                                and
                                            @endif
                                            @if ($category->blogPosts()->count() > 0)
                                                <strong>{{ $category->blogPosts()->count() }}</strong> blog post(s)
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <!-- Submit Button -->
                            <div class="row">
                                <div class="col-12">
                                    <div class="d-flex gap-2">
                                        <button type="submit" class="btn btn-primary">
                                            <span class="btn-loading d-none">
                                                <span class="spinner-border spinner-border-sm me-2" role="status"
                                                    aria-hidden="true"></span>
                                                Updating...
                                            </span>
                                            <span class="btn-text">
                                                <i class="bx bx-check me-1"></i>Update Category
                                            </span>
                                        </button>
                                        <a href="{{ route('categories.index') }}"
                                            class="btn btn-outline-secondary">Cancel</a>
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
