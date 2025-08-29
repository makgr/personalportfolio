@extends('layouts.app')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <!-- Header -->
        <div class="row">
            <div class="col-12">
                <!-- Success/Error Messages -->
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <div class="card mb-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="card-title m-0">Newsletter Subscriber Details</h5>
                                <p class="card-text">View subscriber information and status</p>
                            </div>
                            <a href="{{ route('admin.newsletter-subscribers.index') }}" class="btn btn-outline-secondary">
                                <i class="bx bx-arrow-back"></i> Back to List
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Subscriber Information -->
            <div class="col-lg-8">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Subscriber Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Email Address</label>
                                <p class="form-control-plaintext">{{ $newsletterSubscriber->email }}</p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Name</label>
                                <p class="form-control-plaintext">{{ $newsletterSubscriber->name ?: 'Not provided' }}</p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Status</label>
                                <p class="form-control-plaintext">
                                    @if($newsletterSubscriber->status === 'verified')
                                        <span class="badge bg-success">
                                            <i class="bx bx-check"></i> Verified
                                        </span>
                                    @elseif($newsletterSubscriber->status === 'pending')
                                        <span class="badge bg-warning">
                                            <i class="bx bx-time"></i> Pending
                                        </span>
                                    @else
                                        <span class="badge bg-danger">
                                            <i class="bx bx-x"></i> Unsubscribed
                                        </span>
                                    @endif
                                </p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Source</label>
                                <p class="form-control-plaintext">{{ $newsletterSubscriber->source ?: 'Unknown' }}</p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">IP Address</label>
                                <p class="form-control-plaintext">{{ $newsletterSubscriber->ip_address ?: 'Not recorded' }}</p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Verification Token</label>
                                <p class="form-control-plaintext">
                                    @if($newsletterSubscriber->verification_token)
                                        <code class="text-muted">{{ Str::limit($newsletterSubscriber->verification_token, 20, '...') }}</code>
                                    @else
                                        <span class="text-muted">None</span>
                                    @endif
                                </p>
                            </div>
                        </div>

                        @if($newsletterSubscriber->preferences && count($newsletterSubscriber->preferences) > 0)
                            <div class="row">
                                <div class="col-12 mb-3">
                                    <label class="form-label fw-semibold">Preferences</label>
                                    <div class="form-control-plaintext">
                                        @foreach($newsletterSubscriber->preferences as $preference)
                                            <span class="badge bg-info me-1">{{ $preference }}</span>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Timestamps & Actions -->
            <div class="col-lg-4">
                <!-- Timestamps -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h6 class="card-title mb-0">Timeline</h6>
                    </div>
                    <div class="card-body">
                        <div class="timeline">
                            <div class="timeline-item">
                                <div class="timeline-point timeline-point-primary"></div>
                                <div class="timeline-content">
                                    <h6 class="mb-1">Subscribed</h6>
                                    <p class="mb-0 text-muted small">{{ $newsletterSubscriber->created_at->format('M d, Y \a\t H:i') }}</p>
                                    <small class="text-muted">{{ $newsletterSubscriber->created_at->diffForHumans() }}</small>
                                </div>
                            </div>

                            @if($newsletterSubscriber->verified_at)
                                <div class="timeline-item">
                                    <div class="timeline-point timeline-point-success"></div>
                                    <div class="timeline-content">
                                        <h6 class="mb-1">Verified</h6>
                                        <p class="mb-0 text-muted small">{{ $newsletterSubscriber->verified_at->format('M d, Y \a\t H:i') }}</p>
                                        <small class="text-muted">{{ $newsletterSubscriber->verified_at->diffForHumans() }}</small>
                                    </div>
                                </div>
                            @endif

                            @if($newsletterSubscriber->unsubscribed_at)
                                <div class="timeline-item">
                                    <div class="timeline-point timeline-point-danger"></div>
                                    <div class="timeline-content">
                                        <h6 class="mb-1">Unsubscribed</h6>
                                        <p class="mb-0 text-muted small">{{ $newsletterSubscriber->unsubscribed_at->format('M d, Y \a\t H:i') }}</p>
                                        <small class="text-muted">{{ $newsletterSubscriber->unsubscribed_at->diffForHumans() }}</small>
                                    </div>
                                </div>
                            @endif

                            <div class="timeline-item">
                                <div class="timeline-point timeline-point-info"></div>
                                <div class="timeline-content">
                                    <h6 class="mb-1">Last Updated</h6>
                                    <p class="mb-0 text-muted small">{{ $newsletterSubscriber->updated_at->format('M d, Y \a\t H:i') }}</p>
                                    <small class="text-muted">{{ $newsletterSubscriber->updated_at->diffForHumans() }}</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="card">
                    <div class="card-header">
                        <h6 class="card-title mb-0">Quick Actions</h6>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            @if($newsletterSubscriber->status !== 'verified')
                                <button type="button" class="btn btn-success" 
                                        onclick="updateStatus({{ $newsletterSubscriber->id }}, 'verified')">
                                    <i class="bx bx-check-circle"></i> Mark as Verified
                                </button>
                            @endif

                            @if($newsletterSubscriber->status !== 'pending')
                                <button type="button" class="btn btn-warning" 
                                        onclick="updateStatus({{ $newsletterSubscriber->id }}, 'pending')">
                                    <i class="bx bx-time"></i> Mark as Pending
                                </button>
                            @endif

                            @if($newsletterSubscriber->status !== 'unsubscribed')
                                <button type="button" class="btn btn-outline-danger" 
                                        onclick="updateStatus({{ $newsletterSubscriber->id }}, 'unsubscribed')">
                                    <i class="bx bx-x-circle"></i> Unsubscribe
                                </button>
                            @endif

                            <hr class="my-3">
                            
                            <button type="button" class="btn btn-danger" 
                                    onclick="deleteSubscriber({{ $newsletterSubscriber->id }})">
                                <i class="bx bx-trash"></i> Delete Subscriber
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .timeline {
            position: relative;
            padding-left: 1.5rem;
        }
        
        .timeline::before {
            content: '';
            position: absolute;
            left: 0.5rem;
            top: 0;
            bottom: 0;
            width: 2px;
            background: #e9ecef;
        }
        
        .timeline-item {
            position: relative;
            margin-bottom: 1.5rem;
        }
        
        .timeline-point {
            position: absolute;
            left: -1.5rem;
            top: 0.25rem;
            width: 1rem;
            height: 1rem;
            border-radius: 50%;
            border: 2px solid #fff;
            box-shadow: 0 0 0 2px #e9ecef;
        }
        
        .timeline-point-primary { background: #7c3aed; }
        .timeline-point-success { background: #059669; }
        .timeline-point-danger { background: #dc2626; }
        .timeline-point-info { background: #0ea5e9; }
        
        .timeline-content h6 {
            font-size: 0.875rem;
            margin-bottom: 0.25rem;
        }
    </style>
@endpush

@push('scripts')
    <script>
        function updateStatus(subscriberId, status) {
            if (confirm('Are you sure you want to change this subscriber\'s status?')) {
                fetch(`/admin/newsletter-subscribers/${subscriberId}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ status: status })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        toastr.success(data.message);
                        location.reload();
                    } else {
                        toastr.error(data.message || 'Failed to update status');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    toastr.error('An error occurred while updating status');
                });
            }
        }

        function deleteSubscriber(subscriberId) {
            if (confirm('Are you sure you want to delete this subscriber? This action cannot be undone.')) {
                fetch(`/admin/newsletter-subscribers/${subscriberId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        toastr.success(data.message);
                        window.location.href = '/admin/newsletter-subscribers';
                    } else {
                        toastr.error(data.message || 'Failed to delete subscriber');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    toastr.error('An error occurred while deleting subscriber');
                });
            }
        }
    </script>
@endpush