<div class="dropdown">
    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
        <i class="bx bx-dots-vertical-rounded"></i>
    </button>
    <ul class="dropdown-menu">
        <li>
            <a class="dropdown-item" href="{{ route('projects.show', $project->id) }}">
                <i class="bx bx-show me-2"></i>View
            </a>
        </li>
        <li>
            <a class="dropdown-item" href="{{ route('projects.edit', $project->id) }}">
                <i class="bx bx-edit me-2"></i>Edit
            </a>
        </li>
        <li><hr class="dropdown-divider"></li>
        
        @if($project->status === 'draft')
            <li>
                <button class="dropdown-item btn-publish" data-url="{{ route('projects.publish', $project->id) }}">
                    <i class="bx bx-upload me-2"></i>Publish
                </button>
            </li>
        @elseif($project->status === 'published')
            <li>
                <button class="dropdown-item btn-publish" data-url="{{ route('projects.unpublish', $project->id) }}">
                    <i class="bx bx-download me-2"></i>Unpublish
                </button>
            </li>
        @endif
        
        <li>
            <button class="dropdown-item btn-feature" 
                    data-url="{{ route('projects.feature', $project->id) }}"
                    data-featured="{{ $project->is_featured ? 'true' : 'false' }}">
                <i class="bx {{ $project->is_featured ? 'bx-star' : 'bx-star-half' }} me-2"></i>
                {{ $project->is_featured ? 'Remove Featured' : 'Mark Featured' }}
            </button>
        </li>
        
        <li><hr class="dropdown-divider"></li>
        <li>
            <button class="dropdown-item text-danger btn-delete" 
                    data-url="{{ route('projects.destroy', $project->id) }}"
                    data-title="{{ $project->title }}">
                <i class="bx bx-trash me-2"></i>Delete
            </button>
        </li>
    </ul>
</div>