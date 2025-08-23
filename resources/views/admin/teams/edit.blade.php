@extends('layouts.app')

@section('title', 'Edit Team Member')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <!-- Header -->
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">
                        <span class="text-muted fw-light">Teams /</span> Edit Team Member
                    </h4>
                    <div class="d-flex gap-2">
                        <a href="{{ route('teams.show', $team->id) }}" class="btn btn-info">
                            <i class="bx bx-show me-1"></i>View
                        </a>
                        <a href="{{ route('teams.index') }}" class="btn btn-outline-secondary">
                            <i class="bx bx-arrow-back me-1"></i>Back to Teams
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <form id="teamForm" data-ajax="{{ route('teams.update', $team->id) }}" enctype="multipart/form-data" data-on-success="window.location.href = data.redirect">
                        @csrf
                        @method('PUT')
                        
                        <!-- Basic Information -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="mb-3">Basic Information</h5>
                            </div>
                            <div class="col-md-6">
                                <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                       id="name" name="name" value="{{ old('name', $team->name) }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="position" class="form-label">Position <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('position') is-invalid @enderror" 
                                       id="position" name="position" value="{{ old('position', $team->position) }}" required>
                                @error('position')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="department" class="form-label">Department</label>
                                <select class="form-select @error('department') is-invalid @enderror" 
                                        id="department" name="department">
                                    <option value="">Select Department</option>
                                    @foreach($departments as $key => $label)
                                        <option value="{{ $key }}" {{ old('department', $team->department) === $key ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('department')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="employment_type" class="form-label">Employment Type <span class="text-danger">*</span></label>
                                <select class="form-select @error('employment_type') is-invalid @enderror" 
                                        id="employment_type" name="employment_type" required>
                                    @foreach($employmentTypes as $key => $label)
                                        <option value="{{ $key }}" {{ old('employment_type', $team->employment_type) === $key ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('employment_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-12">
                                <label for="bio" class="form-label">Bio</label>
                                <textarea class="form-control @error('bio') is-invalid @enderror" 
                                          id="bio" name="bio" rows="3" 
                                          placeholder="Brief bio about the team member">{{ old('bio', $team->bio) }}</textarea>
                                @error('bio')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Contact Information -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="mb-3">Contact Information</h5>
                            </div>
                            <div class="col-md-6">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                       id="email" name="email" value="{{ old('email', $team->email) }}">
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="phone" class="form-label">Phone</label>
                                <input type="text" class="form-control @error('phone') is-invalid @enderror" 
                                       id="phone" name="phone" value="{{ old('phone', $team->phone) }}">
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="location" class="form-label">Location</label>
                                <input type="text" class="form-control @error('location') is-invalid @enderror" 
                                       id="location" name="location" value="{{ old('location', $team->location) }}" 
                                       placeholder="City, Country">
                                @error('location')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="join_date" class="form-label">Join Date</label>
                                <input type="date" class="form-control @error('join_date') is-invalid @enderror" 
                                       id="join_date" name="join_date" value="{{ old('join_date', $team->join_date?->format('Y-m-d')) }}">
                                @error('join_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Profile Image -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="mb-3">Profile Image</h5>
                            </div>
                            <div class="col-md-6">
                                <label for="image" class="form-label">Profile Photo</label>
                                <input type="file" class="form-control @error('image') is-invalid @enderror" 
                                       id="image" name="image" accept="image/*">
                                <small class="text-muted">Maximum file size: 1MB. Leave empty to keep current image.</small>
                                @error('image')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Preview</label>
                                <div id="image-preview" style="display: none;">
                                    <img id="preview-img" src="" alt="New Image Preview" 
                                         class="rounded" style="width: 100px; height: 100px; object-fit: cover;">
                                    <small class="d-block text-muted">New image preview</small>
                                </div>
                                @if($team->image)
                                <div id="current-image">
                                    <img src="{{ asset($team->image) }}" alt="{{ $team->name }}" 
                                         class="rounded" style="width: 100px; height: 100px; object-fit: cover;">
                                    <small class="d-block text-muted">Current image</small>
                                </div>
                                @else
                                <div id="no-current-image" class="text-muted">
                                    <i class="bx bx-image-add" style="font-size: 50px;"></i>
                                    <p>No current image</p>
                                </div>
                                @endif
                            </div>
                        </div>

                        <!-- Social Links -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="mb-3">Social Links</h5>
                            </div>
                            <div class="col-md-6">
                                <label for="linkedin" class="form-label">LinkedIn URL</label>
                                <input type="url" class="form-control @error('linkedin') is-invalid @enderror" 
                                       id="linkedin" name="linkedin" value="{{ old('linkedin', $team->linkedin) }}" 
                                       placeholder="https://linkedin.com/in/username">
                                @error('linkedin')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="twitter" class="form-label">Twitter URL</label>
                                <input type="url" class="form-control @error('twitter') is-invalid @enderror" 
                                       id="twitter" name="twitter" value="{{ old('twitter', $team->twitter) }}" 
                                       placeholder="https://twitter.com/username">
                                @error('twitter')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="github" class="form-label">GitHub URL</label>
                                <input type="url" class="form-control @error('github') is-invalid @enderror" 
                                       id="github" name="github" value="{{ old('github', $team->github) }}" 
                                       placeholder="https://github.com/username">
                                @error('github')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="website" class="form-label">Personal Website</label>
                                <input type="url" class="form-control @error('website') is-invalid @enderror" 
                                       id="website" name="website" value="{{ old('website', $team->website) }}" 
                                       placeholder="https://example.com">
                                @error('website')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Skills -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="mb-3">Skills & Expertise</h5>
                            </div>
                            <div class="col-12">
                                <label for="skills" class="form-label">Skills</label>
                                <input type="text" class="form-control @error('skills') is-invalid @enderror" 
                                       id="skills" name="skills" value="{{ old('skills', is_array($team->skills) ? implode(', ', $team->skills) : '') }}" 
                                       placeholder="JavaScript, PHP, Laravel, Vue.js (comma-separated)">
                                <small class="text-muted">Enter skills separated by commas</small>
                                @error('skills')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Settings -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="mb-3">Display Settings</h5>
                            </div>
                            <div class="col-md-6">
                                <label for="icon" class="form-label">Icon Class</label>
                                <input type="text" class="form-control @error('icon') is-invalid @enderror" 
                                       id="icon" name="icon" value="{{ old('icon', $team->icon) }}" 
                                       placeholder="bx-user">
                                <small class="text-muted">BoxIcons or FontAwesome class name (e.g., bx-user, fas fa-user)</small>
                                @error('icon')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="color" class="form-label">Color</label>
                                <input type="color" class="form-control form-control-color @error('color') is-invalid @enderror" 
                                       id="color" name="color" value="{{ old('color', $team->color) }}">
                                @error('color')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="order" class="form-label">Display Order</label>
                                <input type="number" class="form-control @error('order') is-invalid @enderror" 
                                       id="order" name="order" value="{{ old('order', $team->order) }}" 
                                       min="0" placeholder="0">
                                @error('order')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Status Options -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="mb-3">Status Options</h5>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check form-switch">
                                    <input class="form-check-input @error('is_featured') is-invalid @enderror" 
                                           type="checkbox" id="is_featured" name="is_featured" value="1"
                                           {{ old('is_featured', $team->is_featured) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_featured">
                                        Featured Team Member
                                    </label>
                                    <small class="text-muted d-block">Display this member prominently</small>
                                    @error('is_featured')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check form-switch">
                                    <input class="form-check-input @error('is_active') is-invalid @enderror" 
                                           type="checkbox" id="is_active" name="is_active" value="1"
                                           {{ old('is_active', $team->is_active) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">
                                        Active
                                    </label>
                                    <small class="text-muted d-block">Show this team member publicly</small>
                                    @error('is_active')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
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
                                    <button type="submit" class="btn btn-primary">
                                        <span class="btn-text">
                                            <i class="bx bx-check me-1"></i>Update Team Member
                                        </span>
                                        <span class="btn-loading d-none">
                                            <span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                                            Updating...
                                        </span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Sidebar Actions -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('teams.show', $team->id) }}" class="btn btn-info">
                            <i class="bx bx-show me-1"></i>View Profile
                        </a>
                        <button type="button" class="btn btn-outline-{{ $team->is_featured ? 'secondary' : 'warning' }}" 
                                data-ajax="{{ route('teams.toggle-featured', $team->id) }}" 
                                data-method="POST"
                                data-loading="Updating...">
                            <i class="bx bx-star me-1"></i>{{ $team->is_featured ? 'Remove Featured' : 'Mark Featured' }}
                        </button>
                        <button type="button" class="btn btn-outline-{{ $team->is_active ? 'warning' : 'success' }}" 
                                data-ajax="{{ route('teams.toggle-status', $team->id) }}" 
                                data-method="POST"
                                data-loading="Updating...">
                            <i class="bx bx-{{ $team->is_active ? 'pause' : 'play' }} me-1"></i>{{ $team->is_active ? 'Deactivate' : 'Activate' }}
                        </button>
                        <hr>
                        <button type="button" class="btn btn-outline-danger" 
                                data-ajax="{{ route('teams.destroy', $team->id) }}" 
                                data-method="DELETE"
                                data-confirm="Are you sure you want to delete this team member? This action cannot be undone."
                                data-loading="Deleting..."
                                data-on-success="window.location.href = '{{ route('teams.index') }}'">
                            <i class="bx bx-trash me-1"></i>Delete Member
                        </button>
                    </div>
                </div>
            </div>

            <!-- Team Member Info -->
            <div class="card mt-3">
                <div class="card-header">
                    <h5 class="mb-0">Member Info</h5>
                </div>
                <div class="card-body">
                    <div class="row g-2 text-center">
                        <div class="col-6">
                            <div class="badge bg-label-{{ $team->is_active ? 'success' : 'secondary' }} rounded p-3 w-100">
                                <div class="d-flex flex-column align-items-center">
                                    <i class="bx bx-{{ $team->is_active ? 'check-circle' : 'x-circle' }} bx-lg mb-1"></i>
                                    <small>{{ $team->is_active ? 'Active' : 'Inactive' }}</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="badge bg-label-{{ $team->is_featured ? 'warning' : 'secondary' }} rounded p-3 w-100">
                                <div class="d-flex flex-column align-items-center">
                                    <i class="bx bx-star bx-lg mb-1"></i>
                                    <small>{{ $team->is_featured ? 'Featured' : 'Regular' }}</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mt-3">
                        <div class="col-12">
                            <div class="d-flex justify-content-between align-items-center py-2">
                                <span class="text-muted">Display Order:</span>
                                <span class="badge bg-label-info">{{ $team->order }}</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center py-2">
                                <span class="text-muted">Member Since:</span>
                                <span>{{ $team->formatted_join_date ?: 'N/A' }}</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center py-2">
                                <span class="text-muted">Last Updated:</span>
                                <span>{{ $team->updated_at->format('M d, Y') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const imageInput = document.getElementById('image');
    const imagePreview = document.getElementById('image-preview');
    const previewImg = document.getElementById('preview-img');
    const currentImage = document.getElementById('current-image') || document.getElementById('no-current-image');
    
    imageInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        
        if (file) {
            // Validate file type
            if (!file.type.startsWith('image/')) {
                alert('Please select an image file.');
                this.value = '';
                return;
            }
            
            // Validate file size (1MB = 1048576 bytes)
            if (file.size > 1048576) {
                alert('File size must be less than 1MB.');
                this.value = '';
                return;
            }
            
            // Create preview
            const reader = new FileReader();
            reader.onload = function(e) {
                previewImg.src = e.target.result;
                imagePreview.style.display = 'block';
                if (currentImage) {
                    currentImage.style.display = 'none';
                }
            };
            reader.readAsDataURL(file);
        } else {
            // No file selected
            imagePreview.style.display = 'none';
            if (currentImage) {
                currentImage.style.display = 'block';
            }
        }
    });
});
</script>

@endsection