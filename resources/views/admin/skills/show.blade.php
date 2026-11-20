@extends('layouts.app')

@section('title', $skill->name . ' - Skill Details')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <!-- Header -->
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <span class="text-muted fw-light">Portfolio / Skills /</span> {{ $skill->name }}
                    </h5>
                    <div class="d-flex gap-2">
                        <a href="{{ route('skills.edit', $skill->id) }}" class="btn btn-primary btn-sm">
                            <i class="bx bx-edit me-1"></i>Edit
                        </a>
                        <a href="{{ route('skills.index') }}" class="btn btn-secondary">
                            <i class="bx bx-arrow-back me-1"></i>Back to Skills
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Main Content -->
        <div class="col-md-8">
            <!-- Skill Overview -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Skill Overview</h5>
                </div>
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-3 text-center">
                            @if($skill->icon)
                                @if(str_starts_with($skill->icon, 'bx-'))
                                    <i class="bx {{ $skill->icon }}" style="font-size: 4rem; color: {{ $skill->color ?? '#6c757d' }};"></i>
                                @else
                                    <i class="{{ $skill->icon }}" style="font-size: 4rem; color: {{ $skill->color ?? '#6c757d' }};"></i>
                                @endif
                            @else
                                <i class="bx bx-code text-muted" style="font-size: 4rem;"></i>
                            @endif
                        </div>
                        <div class="col-md-9">
                            <h4 class="mb-2">{{ $skill->name }}</h4>
                            <div class="mb-3">
                                <span class="badge bg-info me-2">{{ ucfirst(str_replace('_', ' ', $skill->category)) }}</span>
                                @if($skill->is_primary)
                                    <span class="badge bg-warning me-2"><i class="bx bx-star"></i> Primary</span>
                                @endif
                                <span class="badge bg-{{ $skill->is_active ? 'success' : 'secondary' }}">
                                    {{ $skill->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </div>
                            
                            <!-- Proficiency Bar -->
                            <div class="mb-3">
                                <div class="d-flex justify-content-between mb-1">
                                    <span class="fw-medium">Proficiency</span>
                                    <span class="text-muted">{{ $skill->proficiency }}%</span>
                                </div>
                                <div class="progress" style="height: 12px;">
                                    <div class="progress-bar" role="progressbar" 
                                         style="width: {{ $skill->proficiency }}%; background-color: {{ $skill->color ?? '#6c757d' }};"
                                         aria-valuenow="{{ $skill->proficiency }}" aria-valuemin="0" aria-valuemax="100">
                                    </div>
                                </div>
                            </div>

                            @if($skill->years_experience)
                                <p class="mb-0">
                                    <i class="bx bx-time text-muted me-1"></i>
                                    {{ $skill->years_experience }} year{{ $skill->years_experience != 1 ? 's' : '' }} of experience
                                </p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Description -->
            @if($skill->description)
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Description</h5>
                    </div>
                    <div class="card-body">
                        <p class="mb-0">{{ $skill->description }}</p>
                    </div>
                </div>
            @endif

            <!-- Certifications -->
            @if($skill->certifications && count($skill->certifications) > 0)
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Certifications</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @foreach($skill->certifications as $certification)
                                <div class="col-md-6 mb-2">
                                    <div class="d-flex align-items-center">
                                        <i class="bx bx-badge-check text-success me-2"></i>
                                        <span>{{ $certification }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="col-md-4">
            <!-- Skill Details -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Skill Details</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span>Category:</span>
                        <span class="badge bg-info">{{ ucfirst(str_replace('_', ' ', $skill->category)) }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span>Proficiency:</span>
                        <span class="badge bg-primary">{{ $skill->proficiency }}%</span>
                    </div>
                    @if($skill->years_experience)
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span>Experience:</span>
                            <span>{{ $skill->years_experience }} year{{ $skill->years_experience != 1 ? 's' : '' }}</span>
                        </div>
                    @endif
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span>Primary:</span>
                        <span class="badge bg-{{ $skill->is_primary ? 'warning' : 'secondary' }}">
                            {{ $skill->is_primary ? 'Yes' : 'No' }}
                        </span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span>Status:</span>
                        <span class="badge bg-{{ $skill->is_active ? 'success' : 'secondary' }}">
                            {{ $skill->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span>Order:</span>
                        <span class="badge bg-info">{{ $skill->order }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span>Slug:</span>
                        <code>{{ $skill->slug }}</code>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span>Created:</span>
                        <span>{{ $skill->created_at->format('M d, Y') }}</span>
                    </div>
                </div>
            </div>

            <!-- Technical Details -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Technical Details</h5>
                </div>
                <div class="card-body">
                    @if($skill->icon)
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span>Icon Class:</span>
                            <code>{{ $skill->icon }}</code>
                        </div>
                    @endif
                    @if($skill->color)
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span>Color:</span>
                            <div class="d-flex align-items-center">
                                <div class="me-2" style="width: 20px; height: 20px; background-color: {{ $skill->color }}; border-radius: 3px; border: 1px solid #dee2e6;"></div>
                                <code>{{ $skill->color }}</code>
                            </div>
                        </div>
                    @endif
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span>ID:</span>
                        <code>{{ $skill->id }}</code>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span>Updated:</span>
                        <span>{{ $skill->updated_at->format('M d, Y H:i') }}</span>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="card">
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('skills.edit', $skill->id) }}" class="btn btn-primary">
                            <i class="bx bx-edit me-1"></i>Edit Skill
                        </a>
                        
                        <button type="button" class="btn btn-{{ $skill->is_active ? 'warning' : 'success' }}" 
                                onclick="toggleStatus({{ $skill->id }})">
                            <i class="bx bx-{{ $skill->is_active ? 'hide' : 'show' }} me-1"></i>
                            {{ $skill->is_active ? 'Deactivate' : 'Activate' }}
                        </button>

                        <button type="button" class="btn btn-{{ $skill->is_primary ? 'outline-warning' : 'warning' }}" 
                                onclick="togglePrimary({{ $skill->id }})">
                            <i class="bx bx-star me-1"></i>
                            {{ $skill->is_primary ? 'Remove Primary' : 'Mark Primary' }}
                        </button>

                        <button type="button" class="btn btn-danger" onclick="deleteSkill({{ $skill->id }})">
                            <i class="bx bx-trash me-1"></i>Delete Skill
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function toggleStatus(id) {
    obConfirm('Are you sure you want to toggle this skill status?').then(function (ok) {
        if (!ok) return;
        $.post(`{{ url('/skills') }}/${id}/toggle-status`, {
            _token: $('meta[name="csrf-token"]').attr('content')
        })
        .done(function(response) {
            if (response.success) {
                toastr.success(response.message);
                setTimeout(() => location.reload(), 1000);
            } else {
                toastr.error(response.message);
            }
        })
        .fail(function() {
            toastr.error('Failed to toggle skill status');
        });
    });
}

function togglePrimary(id) {
    obConfirm('Are you sure you want to toggle this skill primary status?').then(function (ok) {
        if (!ok) return;
        $.post(`{{ url('/skills') }}/${id}/toggle-primary`, {
            _token: $('meta[name="csrf-token"]').attr('content')
        })
        .done(function(response) {
            if (response.success) {
                toastr.success(response.message);
                setTimeout(() => location.reload(), 1000);
            } else {
                toastr.error(response.message);
            }
        })
        .fail(function() {
            toastr.error('Failed to toggle primary status');
        });
    });
}

function deleteSkill(id) {
    obConfirm('Are you sure you want to delete this skill? This action cannot be undone.').then(function (ok) {
        if (!ok) return;
        const deleteUrl = `{{ url('/skills') }}/${id}`;
        console.log('Attempting to delete skill ID:', id);
        console.log('Delete URL:', deleteUrl);
        $.ajax({
            url: deleteUrl,
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'X-Requested-With': 'XMLHttpRequest'
            },
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                _method: 'DELETE'
            }
        })
        .done(function(response) {
            console.log('Delete response:', response);
            if (response.success) {
                toastr.success(response.message);
                setTimeout(() => window.location.href = '{{ route('skills.index') }}', 1000);
            } else {
                toastr.error(response.message);
            }
        })
        .fail(function(xhr, status, error) {
            console.error('Delete request failed:', xhr.responseText, status, error);
            if (xhr.responseJSON && xhr.responseJSON.message) {
                toastr.error(xhr.responseJSON.message);
            } else {
                toastr.error('Failed to delete skill: ' + error);
            }
        });
    });
}
</script>
@endpush