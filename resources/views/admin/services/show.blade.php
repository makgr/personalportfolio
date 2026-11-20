@extends('layouts.app')

@section('title', 'Service Details - ' . $service->title)

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <!-- Header -->
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <span class="text-muted fw-light">Portfolio / Services /</span> {{ $service->title }}
                    </h5>
                    <div class="d-flex gap-2">
                        <a href="{{ route('services.edit', $service->id) }}" class="btn btn-primary btn-sm">
                            <i class="bx bx-edit me-1"></i>Edit
                        </a>
                        <a href="{{ route('services.index') }}" class="btn btn-secondary">
                            <i class="bx bx-arrow-back me-1"></i>Back to Services
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Main Content -->
        <div class="col-lg-8">
            <!-- Service Information -->
            <div class="card mb-4">
                <div class="card-header d-flex align-items-center">
                    @if($service->icon)
                        @if(str_starts_with($service->icon, 'bx-'))
                            <i class="bx {{ $service->icon }} text-primary me-2" style="font-size: 1.5rem;"></i>
                        @elseif(str_starts_with($service->icon, 'fa-'))
                            <i class="fas {{ $service->icon }} text-primary me-2" style="font-size: 1.5rem;"></i>
                        @else
                            <i class="{{ $service->icon }} text-primary me-2" style="font-size: 1.5rem;"></i>
                        @endif
                    @else
                        <i class="bx bx-briefcase text-primary me-2" style="font-size: 1.5rem;"></i>
                    @endif
                    <h5 class="card-title mb-0">{{ $service->title }}</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-12">
                            <h6 class="text-muted mb-2">Short Description</h6>
                            <p class="mb-0">{{ $service->short_description }}</p>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-12">
                            <h6 class="text-muted mb-2">Full Description</h6>
                            <div class="card bg-light">
                                <div class="card-body">
                                    {!! nl2br(e($service->description)) !!}
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($service->image)
                    <div class="row mb-3">
                        <div class="col-12">
                            <h6 class="text-muted mb-2">Service Image</h6>
                            <img src="{{ asset($service->image) }}" 
                                 class="img-fluid rounded shadow-sm" 
                                 alt="{{ $service->title }}"
                                 data-bs-toggle="modal" 
                                 data-bs-target="#imageModal"
                                 style="cursor: pointer; max-height: 300px;">
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Features -->
            @if($service->features && count($service->features) > 0)
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bx bx-check-circle text-success me-1"></i>Features
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($service->features as $feature)
                        <div class="col-md-6 mb-2">
                            <div class="d-flex align-items-center">
                                <i class="bx bx-check text-success me-2"></i>
                                <span>{{ $feature }}</span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            <!-- Technologies -->
            @if($service->technologies && count($service->technologies) > 0)
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bx bx-code-block text-info me-1"></i>Technologies
                    </h5>
                </div>
                <div class="card-body">
                    @foreach($service->technologies as $tech)
                        <span class="badge bg-info me-2 mb-2">{{ $tech }}</span>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Service Status -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Service Status</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span>Status:</span>
                        <span class="badge bg-{{ $service->is_active ? 'success' : 'secondary' }}">
                            {{ $service->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span>Slug:</span>
                        <code>{{ $service->slug }}</code>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span>Order:</span>
                        <span class="badge bg-info">{{ $service->order }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span>Created:</span>
                        <span>{{ $service->created_at->format('M d, Y') }}</span>
                    </div>
                </div>
            </div>

            <!-- Pricing -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Pricing</h5>
                </div>
                <div class="card-body text-center">
                    @if($service->starting_price)
                        <div class="mb-2">
                            <span class="h4 text-primary">${{ number_format($service->starting_price, 2) }}</span>
                            @if($service->price_unit)
                                <span class="text-muted"> / {{ ucfirst($service->price_unit) }}</span>
                            @endif
                        </div>
                        <small class="text-muted">Starting from</small>
                    @else
                        <div class="mb-2">
                            <span class="badge bg-info p-2">Contact for pricing</span>
                        </div>
                        <small class="text-muted">Custom quote required</small>
                    @endif
                </div>
            </div>

            <!-- Actions -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('services.edit', $service->id) }}" class="btn btn-primary">
                            <i class="bx bx-edit me-1"></i>Edit Service
                        </a>
                        <button type="button" class="btn btn-outline-warning" onclick="toggleStatus({{ $service->id }})">
                            <i class="bx bx-{{ $service->is_active ? 'hide' : 'show' }} me-1"></i>
                            {{ $service->is_active ? 'Deactivate' : 'Activate' }}
                        </button>
                        <button type="button" class="btn btn-outline-danger" onclick="deleteService({{ $service->id }})">
                            <i class="bx bx-trash me-1"></i>Delete Service
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Image Modal -->
@if($service->image)
<div class="modal fade" id="imageModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ $service->title }} - Service Image</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <img src="{{ asset($service->image) }}" class="img-fluid" alt="{{ $service->title }}">
            </div>
        </div>
    </div>
</div>
@endif
@endsection

@push('scripts')
<script>
// Toggle status function
function toggleStatus(id) {
    obConfirm('Are you sure you want to toggle this service status?').then(function (ok) {
        if (!ok) return;
        $.post(`{{ url('/services') }}/${id}/toggle-status`, {
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
            toastr.error('Failed to toggle service status');
        });
    });
}

// Delete service function
function deleteService(id) {
    obConfirm('Are you sure you want to delete this service? This action cannot be undone.').then(function (ok) {
        if (!ok) return;
        const deleteUrl = `{{ url('/services') }}/${id}`;
        console.log('Attempting to delete service ID:', id);
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
                // Redirect to services index after short delay
                setTimeout(function() {
                    window.location.href = '{{ route("services.index") }}';
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
                toastr.error('Failed to delete service: ' + error);
            }
        });
    });
}
</script>
@endpush