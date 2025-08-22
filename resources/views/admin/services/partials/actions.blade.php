<div class="dropdown">
    <button type="button" class="btn btn-sm btn-icon dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
        <i class="bx bx-dots-vertical-rounded"></i>
    </button>
    <div class="dropdown-menu">
        <a class="dropdown-item" href="{{ route('services.show', $service->id) }}">
            <i class="bx bx-show me-1"></i> View
        </a>
        <a class="dropdown-item" href="{{ route('services.edit', $service->id) }}">
            <i class="bx bx-edit me-1"></i> Edit
        </a>
        <div class="dropdown-divider"></div>
        <button type="button" class="dropdown-item text-warning" onclick="toggleStatus({{ $service->id }})">
            <i class="bx bx-{{ $service->is_active ? 'hide' : 'show' }} me-1"></i>
            {{ $service->is_active ? 'Deactivate' : 'Activate' }}
        </button>
        <div class="dropdown-divider"></div>
        <button type="button" class="dropdown-item text-danger" onclick="deleteService({{ $service->id }})">
            <i class="bx bx-trash me-1"></i> Delete
        </button>
    </div>
</div>