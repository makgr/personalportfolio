<div class="dropdown">
    <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <i class="bx bx-dots-vertical-rounded"></i>
    </button>
    <div class="dropdown-menu">
        <a class="dropdown-item" href="{{ route('contacts.show', $contact->id) }}">
            <i class="bx bx-show me-1"></i>View Details
        </a>
        
        @if($contact->status === 'new')
            <a class="dropdown-item" href="#" onclick="markAsRead({{ $contact->id }})">
                <i class="bx bx-envelope-open me-1"></i>Mark as Read
            </a>
        @endif
        
        @if($contact->status !== 'replied')
            <a class="dropdown-item" href="#" onclick="markAsReplied({{ $contact->id }})">
                <i class="bx bx-check me-1"></i>Mark as Replied
            </a>
        @endif
        
        @if($contact->status !== 'archived')
            <a class="dropdown-item" href="#" onclick="archiveMessage({{ $contact->id }})">
                <i class="bx bx-archive me-1"></i>Archive
            </a>
        @endif
        
        <div class="dropdown-divider"></div>
        
        <a class="dropdown-item text-danger" href="#" onclick="deleteMessage({{ $contact->id }})">
            <i class="bx bx-trash me-1"></i>Delete
        </a>
    </div>
</div>