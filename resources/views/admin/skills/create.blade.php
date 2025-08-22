@extends('layouts.app')

@section('title', 'Create New Skill')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <!-- Header -->
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <span class="text-muted fw-light">Portfolio / Skills /</span> Create New Skill
                    </h5>
                    <a href="{{ route('skills.index') }}" class="btn btn-secondary">
                        <i class="bx bx-arrow-back me-1"></i>Back to Skills
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
                    <h5 class="card-title mb-0">Skill Information</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('skills.store') }}" method="POST" id="skillForm">
                        @csrf
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="name" class="form-label">Skill Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                       id="name" name="name" value="{{ old('name') }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
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
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="proficiency" class="form-label">Proficiency (%) <span class="text-danger">*</span></label>
                                <input type="range" class="form-range" id="proficiency" name="proficiency" 
                                       min="0" max="100" value="{{ old('proficiency', 50) }}" oninput="updateProficiencyDisplay(this.value)">
                                <div class="d-flex justify-content-between">
                                    <small class="text-muted">0%</small>
                                    <small class="text-muted"><span id="proficiencyValue">{{ old('proficiency', 50) }}</span>%</small>
                                    <small class="text-muted">100%</small>
                                </div>
                                @error('proficiency')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="years_experience" class="form-label">Years of Experience</label>
                                <input type="number" class="form-control @error('years_experience') is-invalid @enderror" 
                                       id="years_experience" name="years_experience" value="{{ old('years_experience') }}" 
                                       min="0" max="50" step="0.5">
                                @error('years_experience')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="icon" class="form-label">Icon Class</label>
                                <input type="text" class="form-control @error('icon') is-invalid @enderror" 
                                       id="icon" name="icon" value="{{ old('icon') }}" 
                                       placeholder="e.g., bx-code, fab fa-php">
                                <small class="text-muted">Use Boxicons (bx-*) or Font Awesome classes</small>
                                @error('icon')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="color" class="form-label">Color</label>
                                <input type="color" class="form-control form-control-color @error('color') is-invalid @enderror" 
                                       id="color" name="color" value="{{ old('color', '#6c757d') }}">
                                @error('color')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="3" 
                                      placeholder="Brief description of your experience with this skill">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="certifications" class="form-label">Certifications</label>
                            <input type="text" class="form-control @error('certifications') is-invalid @enderror" 
                                   id="certifications" name="certifications" value="{{ old('certifications') }}" 
                                   placeholder="Comma-separated list of certifications">
                            <small class="text-muted">Enter certifications separated by commas</small>
                            @error('certifications')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="order" class="form-label">Display Order</label>
                            <input type="number" class="form-control @error('order') is-invalid @enderror" 
                                   id="order" name="order" value="{{ old('order') }}" 
                                   min="0" placeholder="Leave empty for auto-order">
                            @error('order')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" 
                                           id="is_primary" name="is_primary" value="1" 
                                           {{ old('is_primary') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_primary">
                                        <i class="bx bx-star text-warning"></i> Primary Skill
                                    </label>
                                    <small class="form-text text-muted d-block">Mark as a primary/featured skill</small>
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
                                    <small class="form-text text-muted d-block">Show this skill on the website</small>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('skills.index') }}" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bx bx-check me-1"></i>Create Skill
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
                    <div class="text-center" id="skillPreview">
                        <div class="mb-3">
                            <i id="previewIcon" class="bx bx-code text-muted" style="font-size: 2.5rem;"></i>
                        </div>
                        <h6 id="previewName" class="mb-2">Skill Name</h6>
                        <span id="previewCategory" class="badge bg-secondary mb-2">Category</span>
                        <div class="progress mb-2" style="height: 8px;">
                            <div id="previewProgress" class="progress-bar" role="progressbar" style="width: 50%"></div>
                        </div>
                        <small id="previewProficiency" class="text-muted">50% Proficiency</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function updateProficiencyDisplay(value) {
    document.getElementById('proficiencyValue').textContent = value;
    updatePreview();
}

function updatePreview() {
    const name = document.getElementById('name').value || 'Skill Name';
    const category = document.getElementById('category').value || 'category';
    const proficiency = document.getElementById('proficiency').value;
    const icon = document.getElementById('icon').value || 'bx-code';
    const color = document.getElementById('color').value;
    
    // Update preview
    document.getElementById('previewName').textContent = name;
    document.getElementById('previewCategory').textContent = category.replace('_', ' ').toUpperCase();
    document.getElementById('previewProficiency').textContent = proficiency + '% Proficiency';
    
    // Update icon
    const iconElement = document.getElementById('previewIcon');
    iconElement.className = '';
    if (icon.startsWith('bx-')) {
        iconElement.className = 'bx ' + icon;
    } else if (icon) {
        iconElement.className = icon;
    } else {
        iconElement.className = 'bx bx-code text-muted';
    }
    iconElement.style.color = color;
    iconElement.style.fontSize = '2.5rem';
    
    // Update progress bar
    const progressBar = document.getElementById('previewProgress');
    progressBar.style.width = proficiency + '%';
    progressBar.style.backgroundColor = color;
}

// Update preview when inputs change
document.addEventListener('DOMContentLoaded', function() {
    const inputs = ['name', 'category', 'proficiency', 'icon', 'color'];
    inputs.forEach(id => {
        const element = document.getElementById(id);
        if (element) {
            element.addEventListener('input', updatePreview);
            element.addEventListener('change', updatePreview);
        }
    });
    
    // Initial preview update
    updatePreview();
});
</script>
@endpush