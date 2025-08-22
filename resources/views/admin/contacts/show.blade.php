@extends('layouts.app')

@section('title', 'Message Details')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <!-- Header -->
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-0">
                            <span class="text-muted fw-light">Portfolio / Contact Messages /</span> {{ $contact->name }}
                        </h4>
                        <div class="mt-2">
                            @php
                                $statusColors = [
                                    'new' => 'danger',
                                    'read' => 'info',
                                    'replied' => 'success',
                                    'archived' => 'secondary'
                                ];
                                $statusIcons = [
                                    'new' => 'bx-envelope',
                                    'read' => 'bx-envelope-open',
                                    'replied' => 'bx-check-circle',
                                    'archived' => 'bx-archive'
                                ];
                                $typeColors = [
                                    'general' => 'secondary',
                                    'project' => 'primary',
                                    'job' => 'success',
                                    'collaboration' => 'info',
                                    'other' => 'warning'
                                ];
                            @endphp
                            <span class="badge bg-{{ $statusColors[$contact->status] ?? 'secondary' }}">
                                <i class="bx {{ $statusIcons[$contact->status] ?? 'bx-envelope' }} me-1"></i>{{ ucfirst($contact->status) }}
                            </span>
                            <span class="badge bg-{{ $typeColors[$contact->type] ?? 'secondary' }}">{{ ucfirst($contact->type) }}</span>
                            @if($contact->budget_range)
                                <span class="badge bg-success">{{ $contact->budget_range }}</span>
                            @endif
                        </div>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('contacts.index') }}" class="btn btn-outline-secondary">
                            <i class="bx bx-arrow-back me-1"></i>Back to Messages
                        </a>
                        
                        @if($contact->status === 'new')
                            <button type="button" class="btn btn-info" onclick="markAsRead({{ $contact->id }})">
                                <i class="bx bx-envelope-open me-1"></i>Mark as Read
                            </button>
                        @endif
                        
                        @if($contact->status !== 'replied')
                            <button type="button" class="btn btn-success" onclick="showReplyModal()">
                                <i class="bx bx-check me-1"></i>Mark as Replied
                            </button>
                        @endif
                        
                        @if($contact->status !== 'archived')
                            <button type="button" class="btn btn-warning" onclick="archiveMessage({{ $contact->id }})">
                                <i class="bx bx-archive me-1"></i>Archive
                            </button>
                        @endif
                        
                        <button type="button" class="btn btn-danger" onclick="deleteMessage({{ $contact->id }})">
                            <i class="bx bx-trash me-1"></i>Delete
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Main Content -->
        <div class="col-lg-8 col-md-12">
            <!-- Message Content -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">{{ $contact->subject }}</h5>
                </div>
                <div class="card-body">
                    <div class="message-content">
                        {!! nl2br(e($contact->message)) !!}
                    </div>
                </div>
            </div>

            <!-- Services Interested (if applicable) -->
            @if($contact->services_interested && count($contact->services_interested) > 0)
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bx bx-briefcase me-2"></i>Services of Interest
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-flex flex-wrap gap-2">
                        @foreach($contact->services_interested as $service)
                            <span class="badge bg-primary">{{ $service }}</span>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            <!-- Admin Notes -->
            @if($contact->admin_notes)
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bx bx-note me-2"></i>Admin Notes
                    </h5>
                </div>
                <div class="card-body">
                    <div class="admin-notes">
                        {!! nl2br(e($contact->admin_notes)) !!}
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4 col-md-12">
            <!-- Contact Information -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bx bx-user me-2"></i>Contact Information
                    </h5>
                </div>
                <div class="card-body">
                    <div class="contact-info">
                        <div class="mb-3">
                            <label class="form-label text-muted small">Name</label>
                            <div class="fw-bold">{{ $contact->name }}</div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label text-muted small">Email</label>
                            <div>
                                <a href="mailto:{{ $contact->email }}" class="text-decoration-none">
                                    <i class="bx bx-envelope me-1"></i>{{ $contact->email }}
                                </a>
                            </div>
                        </div>
                        
                        @if($contact->phone)
                        <div class="mb-3">
                            <label class="form-label text-muted small">Phone</label>
                            <div>
                                <a href="tel:{{ $contact->phone }}" class="text-decoration-none">
                                    <i class="bx bx-phone me-1"></i>{{ $contact->phone }}
                                </a>
                            </div>
                        </div>
                        @endif
                        
                        @if($contact->company)
                        <div class="mb-3">
                            <label class="form-label text-muted small">Company</label>
                            <div>
                                <i class="bx bx-buildings me-1"></i>{{ $contact->company }}
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Message Details -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bx bx-info-circle me-2"></i>Message Details
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label text-muted small">Received</label>
                        <div>{{ $contact->created_at->format('M d, Y \a\t g:i A') }}</div>
                        <small class="text-muted">{{ $contact->created_at->diffForHumans() }}</small>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label text-muted small">Message Type</label>
                        <div>
                            <span class="badge bg-{{ $typeColors[$contact->type] ?? 'secondary' }}">
                                {{ ucfirst($contact->type) }}
                            </span>
                        </div>
                    </div>
                    
                    @if($contact->replied_at)
                    <div class="mb-3">
                        <label class="form-label text-muted small">Replied At</label>
                        <div>{{ $contact->replied_at->format('M d, Y \a\t g:i A') }}</div>
                        <small class="text-muted">{{ $contact->replied_at->diffForHumans() }}</small>
                    </div>
                    @endif
                    
                    @if($contact->ip_address)
                    <div class="mb-3">
                        <label class="form-label text-muted small">IP Address</label>
                        <div><code>{{ $contact->ip_address }}</code></div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bx bx-zap me-2"></i>Quick Actions
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="mailto:{{ $contact->email }}?subject=Re: {{ $contact->subject }}" 
                           class="btn btn-outline-primary btn-sm">
                            <i class="bx bx-reply me-1"></i>Reply via Email
                        </a>
                        
                        @if($contact->phone)
                        <a href="tel:{{ $contact->phone }}" class="btn btn-outline-success btn-sm">
                            <i class="bx bx-phone me-1"></i>Call Now
                        </a>
                        @endif
                        
                        @if($contact->status !== 'archived')
                        <button type="button" class="btn btn-outline-warning btn-sm" onclick="archiveMessage({{ $contact->id }})">
                            <i class="bx bx-archive me-1"></i>Archive Message
                        </button>
                        @endif
                        
                        <button type="button" class="btn btn-outline-danger btn-sm" onclick="deleteMessage({{ $contact->id }})">
                            <i class="bx bx-trash me-1"></i>Delete Message
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Reply Modal -->
<div class="modal fade" id="replyModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Mark as Replied</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="replyForm">
                    <div class="mb-3">
                        <label for="adminNotes" class="form-label">Admin Notes (Optional)</label>
                        <textarea class="form-control" id="adminNotes" rows="4" 
                                  placeholder="Add any notes about how this inquiry was handled...">{{ $contact->admin_notes }}</textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success" onclick="markAsReplied({{ $contact->id }})">
                    <i class="bx bx-check me-1"></i>Mark as Replied
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function showReplyModal() {
    $('#replyModal').modal('show');
}

// Mark as read function
function markAsRead(id) {
    $.post(`{{ url('/contacts') }}/${id}/mark-as-read`, {
        _token: $('meta[name="csrf-token"]').attr('content')
    })
    .done(function(response) {
        if (response.success) {
            toastr.success(response.message);
            setTimeout(function() {
                location.reload();
            }, 1000);
        } else {
            toastr.error(response.message);
        }
    })
    .fail(function() {
        toastr.error('Failed to mark message as read');
    });
}

// Mark as replied function
function markAsReplied(id) {
    const notes = $('#adminNotes').val();
    
    $.post(`{{ url('/contacts') }}/${id}/mark-as-replied`, {
        _token: $('meta[name="csrf-token"]').attr('content'),
        admin_notes: notes
    })
    .done(function(response) {
        if (response.success) {
            toastr.success(response.message);
            $('#replyModal').modal('hide');
            setTimeout(function() {
                location.reload();
            }, 1000);
        } else {
            toastr.error(response.message);
        }
    })
    .fail(function() {
        toastr.error('Failed to mark message as replied');
    });
}

// Archive function
function archiveMessage(id) {
    if (confirm('Are you sure you want to archive this message?')) {
        $.post(`{{ url('/contacts') }}/${id}/archive`, {
            _token: $('meta[name="csrf-token"]').attr('content')
        })
        .done(function(response) {
            if (response.success) {
                toastr.success(response.message);
                setTimeout(function() {
                    location.reload();
                }, 1000);
            } else {
                toastr.error(response.message);
            }
        })
        .fail(function() {
            toastr.error('Failed to archive message');
        });
    }
}

// Delete message function
function deleteMessage(id) {
    if (confirm('Are you sure you want to delete this message? This action cannot be undone.')) {
        const deleteUrl = `{{ url('/contacts') }}/${id}`;
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
                setTimeout(function() {
                    window.location.href = '{{ route("contacts.index") }}';
                }, 1000);
            } else {
                toastr.error(response.message);
            }
        })
        .fail(function(xhr, status, error) {
            if (xhr.responseJSON && xhr.responseJSON.message) {
                toastr.error(xhr.responseJSON.message);
            } else {
                toastr.error('Failed to delete message: ' + error);
            }
        });
    }
}
</script>
@endpush