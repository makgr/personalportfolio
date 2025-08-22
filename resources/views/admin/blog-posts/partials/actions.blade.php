<div class="dropdown">
    <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
        <i class="bx bx-dots-vertical-rounded"></i>
    </button>
    <div class="dropdown-menu">
        <a class="dropdown-item" href="{{ route('blog-posts.show', $post->id) }}">
            <i class="bx bx-show me-1"></i> View
        </a>
        <a class="dropdown-item" href="{{ route('blog-posts.edit', $post->id) }}">
            <i class="bx bx-edit-alt me-1"></i> Edit
        </a>
        @if($post->status !== 'published')
            <button class="dropdown-item" onclick="publishPost({{ $post->id }})">
                <i class="bx bx-check me-1"></i> Publish
            </button>
        @else
            <button class="dropdown-item text-warning" onclick="unpublishPost({{ $post->id }})">
                <i class="bx bx-x me-1"></i> Unpublish  
            </button>
        @endif
        <div class="dropdown-divider"></div>
        <button class="dropdown-item text-danger" onclick="deletePost({{ $post->id }})">
            <i class="bx bx-trash me-1"></i> Delete
        </button>
    </div>
</div>