<div class="dropdown">
    <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <i class="bx bx-dots-vertical-rounded"></i>
    </button>
    <div class="dropdown-menu">
        <a class="dropdown-item" href="{{ route('education.show', $education->id) }}">
            <i class="bx bx-show me-1"></i>View Details
        </a>
        
        <a class="dropdown-item" href="{{ route('education.edit', $education->id) }}">
            <i class="bx bx-edit me-1"></i>Edit
        </a>
        
        <a class="dropdown-item" href="#" onclick="toggleStatus({{ $education->id }})">
            @if($education->is_active)
                <i class="bx bx-x-circle me-1"></i>Deactivate
            @else
                <i class="bx bx-check-circle me-1"></i>Activate
            @endif
        </a>
        
        @if($education->certificate_url)
        <a class="dropdown-item" href="{{ $education->certificate_url }}" target="_blank">
            <i class="bx bx-certificate me-1"></i>View Certificate
        </a>
        @endif
        
        <div class="dropdown-divider"></div>
        
        <a class="dropdown-item text-danger" href="#" onclick="deleteEducation({{ $education->id }})">
            <i class="bx bx-trash me-1"></i>Delete
        </a>
    </div>
</div>