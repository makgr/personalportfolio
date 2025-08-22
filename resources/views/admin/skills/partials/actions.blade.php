<div class="dropdown">
    <button type="button" class="btn btn-sm btn-icon dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
        <i class="bx bx-dots-vertical-rounded"></i>
    </button>
    <div class="dropdown-menu">
        <a class="dropdown-item" href="{{ route('skills.show', $skill->id) }}">
            <i class="bx bx-show me-1"></i> View
        </a>
        <a class="dropdown-item" href="{{ route('skills.edit', $skill->id) }}">
            <i class="bx bx-edit me-1"></i> Edit
        </a>
        <div class="dropdown-divider"></div>
        <button type="button" class="dropdown-item text-warning" onclick="toggleStatus({{ $skill->id }})">
            <i class="bx bx-{{ $skill->is_active ? 'hide' : 'show' }} me-1"></i>
            {{ $skill->is_active ? 'Deactivate' : 'Activate' }}
        </button>
        <button type="button" class="dropdown-item text-info" onclick="togglePrimary({{ $skill->id }})">
            <i class="bx bx-{{ $skill->is_primary ? 'star-half' : 'star' }} me-1"></i>
            {{ $skill->is_primary ? 'Remove Primary' : 'Mark Primary' }}
        </button>
        <div class="dropdown-divider"></div>
        <button type="button" class="dropdown-item text-danger" onclick="deleteSkill({{ $skill->id }})">
            <i class="bx bx-trash me-1"></i> Delete
        </button>
    </div>
</div>