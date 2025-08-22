@extends('layouts.app')

@section('title', 'Experience Details - ' . $experience->position . ' at ' . $experience->company_name)

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <!-- Header -->
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <span class="text-muted fw-light">Portfolio / Experience /</span> {{ $experience->position }}
                    </h5>
                    <div class="d-flex gap-2">
                        <a href="{{ route('experiences.edit', $experience->id) }}" class="btn btn-primary btn-sm">
                            <i class="bx bx-edit me-1"></i>Edit
                        </a>
                        <a href="{{ route('experiences.index') }}" class="btn btn-secondary">
                            <i class="bx bx-arrow-back me-1"></i>Back to Experience
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Main Content -->
        <div class="col-lg-8">
            <!-- Experience Information -->
            <div class="card mb-4">
                <div class="card-header d-flex align-items-center">
                    @if($experience->company_logo)
                        <img src="{{ asset($experience->company_logo) }}" alt="{{ $experience->company_name }}" style="width: 50px; height: 50px; object-fit: contain;" class="rounded me-3">
                    @else
                        <div class="bg-light rounded d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                            <i class="bx bx-buildings text-muted" style="font-size: 1.5rem;"></i>
                        </div>
                    @endif
                    <div>
                        <h5 class="card-title mb-0">{{ $experience->position }}</h5>
                        <p class="text-muted mb-0">{{ $experience->company_name }}</p>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-12">
                            <h6 class="text-muted mb-2">Description</h6>
                            <div class="card bg-light">
                                <div class="card-body">
                                    {!! nl2br(e($experience->description)) !!}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Responsibilities -->
            @if($experience->responsibilities && count($experience->responsibilities) > 0)
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bx bx-task text-primary me-1"></i>Key Responsibilities
                    </h5>
                </div>
                <div class="card-body">
                    <ul class="mb-0">
                        @foreach($experience->responsibilities as $responsibility)
                        <li class="mb-2">{{ $responsibility }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
            @endif

            <!-- Achievements -->
            @if($experience->achievements && count($experience->achievements) > 0)
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bx bx-trophy text-warning me-1"></i>Key Achievements
                    </h5>
                </div>
                <div class="card-body">
                    <ul class="mb-0">
                        @foreach($experience->achievements as $achievement)
                        <li class="mb-2">{{ $achievement }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
            @endif

            <!-- Technologies -->
            @if($experience->technologies && count($experience->technologies) > 0)
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bx bx-code-block text-info me-1"></i>Technologies Used
                    </h5>
                </div>
                <div class="card-body">
                    @foreach($experience->technologies as $tech)
                        <span class="badge bg-info me-2 mb-2">{{ $tech }}</span>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Experience Details -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Experience Details</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span>Status:</span>
                        <span class="badge bg-{{ $experience->is_active ? 'success' : 'secondary' }}">
                            {{ $experience->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </div>
                    
                    @if($experience->employment_type)
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span>Type:</span>
                        @php
                            $typeColors = [
                                'full-time' => 'success',
                                'part-time' => 'info',
                                'contract' => 'warning',
                                'freelance' => 'primary',
                                'internship' => 'secondary'
                            ];
                            $typeColor = $typeColors[$experience->employment_type] ?? 'secondary';
                        @endphp
                        <span class="badge bg-{{ $typeColor }}">
                            {{ ucfirst(str_replace('-', ' ', $experience->employment_type)) }}
                        </span>
                    </div>
                    @endif
                    
                    @if($experience->location)
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span>Location:</span>
                        <span>{{ $experience->location }}</span>
                    </div>
                    @endif
                    
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span>Duration:</span>
                        <span class="badge bg-info">{{ $experience->duration }}</span>
                    </div>
                    
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span>Period:</span>
                        <span>
                            {{ $experience->start_date->format('M Y') }} - 
                            {{ $experience->is_current ? 'Present' : ($experience->end_date ? $experience->end_date->format('M Y') : 'Present') }}
                        </span>
                    </div>
                    
                    @if($experience->is_current)
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span>Current:</span>
                        <span class="badge bg-success">
                            <i class="bx bx-time me-1"></i>Current Position
                        </span>
                    </div>
                    @endif
                    
                    @if($experience->company_url)
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span>Website:</span>
                        <a href="{{ $experience->company_url }}" target="_blank" class="btn btn-sm btn-outline-primary">
                            <i class="bx bx-link-external me-1"></i>Visit
                        </a>
                    </div>
                    @endif
                    
                    <div class="d-flex justify-content-between align-items-center">
                        <span>Order:</span>
                        <span class="badge bg-info">{{ $experience->order }}</span>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('experiences.edit', $experience->id) }}" class="btn btn-primary">
                            <i class="bx bx-edit me-1"></i>Edit Experience
                        </a>
                        <button type="button" class="btn btn-outline-warning" onclick="toggleStatus({{ $experience->id }})">
                            <i class="bx bx-{{ $experience->is_active ? 'hide' : 'show' }} me-1"></i>
                            {{ $experience->is_active ? 'Deactivate' : 'Activate' }}
                        </button>
                        <button type="button" class="btn btn-outline-danger" onclick="deleteExperience({{ $experience->id }})">
                            <i class="bx bx-trash me-1"></i>Delete Experience
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
// Toggle status function
function toggleStatus(id) {
    if (confirm('Are you sure you want to toggle this experience status?')) {
        $.post(`{{ url('/experiences') }}/${id}/toggle-status`, {
            _token: $('meta[name="csrf-token"]').attr('content')
        })
        .done(function(response) {
            if (response.success) {
                toastr.success(response.message);
                // Reload page to update status display
                setTimeout(function() {
                    location.reload();
                }, 1000);
            } else {
                toastr.error(response.message);
            }
        })
        .fail(function() {
            toastr.error('Failed to toggle experience status');
        });
    }
}

// Delete experience function
function deleteExperience(id) {
    if (confirm('Are you sure you want to delete this experience? This action cannot be undone.')) {
        const deleteUrl = `{{ url('/experiences') }}/${id}`;
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
            if (response.success) {
                toastr.success(response.message);
                // Redirect to experiences index after short delay
                setTimeout(function() {
                    window.location.href = '{{ route("experiences.index") }}';
                }, 1500);
            } else {
                toastr.error(response.message);
            }
        })
        .fail(function(xhr, status, error) {
            console.error('Delete request failed:', xhr.responseText, status, error);
            if (xhr.responseJSON && xhr.responseJSON.message) {
                toastr.error(xhr.responseJSON.message);
            } else {
                toastr.error('Failed to delete experience: ' + error);
            }
        });
    }
}
</script>
@endpush