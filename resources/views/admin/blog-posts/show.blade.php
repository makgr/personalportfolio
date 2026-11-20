@extends('layouts.app')

@section('title', $post->title . ' - Blog Post Details')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <!-- Header -->
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <span class="text-muted fw-light">Content / Blog Posts /</span> {{ $post->title }}
                    </h5>
                    <div class="d-flex gap-2">
                        <a href="{{ route('blog-posts.edit', $post->id) }}" class="btn btn-primary btn-sm">
                            <i class="bx bx-edit me-1"></i>Edit
                        </a>
                        <a href="{{ route('blog-posts.index') }}" class="btn btn-secondary">
                            <i class="bx bx-arrow-back me-1"></i>Back to Posts
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Main Content -->
        <div class="col-md-8">
            <!-- Featured Image -->
            @if($post->featured_image)
                <div class="card mb-4">
                    <div class="card-body p-0">
                        <img src="{{ asset($post->featured_image) }}" 
                             alt="{{ $post->title }}" 
                             class="img-fluid w-100" 
                             style="max-height: 400px; object-fit: cover;">
                    </div>
                </div>
            @endif

            <!-- Post Content -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Post Content</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-sm-3 fw-medium">Title:</div>
                        <div class="col-sm-9">{{ $post->title }}</div>
                    </div>
                    
                    @if($post->slug)
                        <div class="row mb-3">
                            <div class="col-sm-3 fw-medium">URL Slug:</div>
                            <div class="col-sm-9">
                                <code>{{ $post->slug }}</code>
                                <small class="text-muted d-block">{{ url('/blog/' . $post->slug) }}</small>
                            </div>
                        </div>
                    @endif

                    <div class="row mb-3">
                        <div class="col-sm-3 fw-medium">Excerpt:</div>
                        <div class="col-sm-9">{{ $post->excerpt }}</div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-sm-3 fw-medium">Content:</div>
                        <div class="col-sm-9">
                            <div class="bg-light p-3 rounded">
                                {!! nl2br(e($post->content)) !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tags & SEO -->
            @if($post->tags || ($post->meta_tags && count($post->meta_tags) > 0))
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Tags & SEO</h5>
                    </div>
                    <div class="card-body">
                        @if($post->tags && count($post->tags) > 0)
                            <div class="row mb-3">
                                <div class="col-sm-3 fw-medium">Tags:</div>
                                <div class="col-sm-9">
                                    @foreach($post->tags as $tag)
                                        <span class="badge bg-primary me-1 mb-1">{{ $tag }}</span>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        @if($post->meta_tags && isset($post->meta_tags['title']))
                            <div class="row mb-3">
                                <div class="col-sm-3 fw-medium">Meta Title:</div>
                                <div class="col-sm-9">{{ $post->meta_tags['title'] }}</div>
                            </div>
                        @endif

                        @if($post->meta_tags && isset($post->meta_tags['description']))
                            <div class="row mb-3">
                                <div class="col-sm-3 fw-medium">Meta Description:</div>
                                <div class="col-sm-9">{{ $post->meta_tags['description'] }}</div>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="col-md-4">
            <!-- Status Information -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Post Status</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span>Status:</span>
                        <span class="badge bg-{{ $post->status == 'published' ? 'success' : ($post->status == 'draft' ? 'warning' : ($post->status == 'scheduled' ? 'info' : 'secondary')) }}">
                            {{ ucfirst($post->status) }}
                        </span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span>Views:</span>
                        <span class="badge bg-info">{{ number_format($post->views) }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span>Likes:</span>
                        <span class="badge bg-success">{{ number_format($post->likes) }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span>Featured:</span>
                        <span class="badge bg-{{ $post->is_featured ? 'success' : 'secondary' }}">
                            {{ $post->is_featured ? 'Yes' : 'No' }}
                        </span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span>Comments:</span>
                        <span class="badge bg-{{ $post->allow_comments ? 'success' : 'secondary' }}">
                            {{ $post->allow_comments ? 'Enabled' : 'Disabled' }}
                        </span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span>Created:</span>
                        <span>{{ $post->created_at->format('M d, Y') }}</span>
                    </div>
                </div>
            </div>

            <!-- Post Details -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Post Details</h5>
                </div>
                <div class="card-body">
                    @if($post->author)
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span>Author:</span>
                            <span>{{ $post->author->name }}</span>
                        </div>
                    @endif

                    @if($post->category)
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span>Category:</span>
                            <span class="badge bg-secondary">{{ $post->category->name }}</span>
                        </div>
                    @endif

                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span>Reading Time:</span>
                        <span>{{ $post->reading_time }} min</span>
                    </div>

                    @if($post->published_at)
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span>Published:</span>
                            <span>{{ $post->published_at->format('M d, Y') }}</span>
                        </div>
                    @endif

                    @if($post->scheduled_at)
                        <div class="d-flex justify-content-between align-items-center">
                            <span>Scheduled:</span>
                            <span>{{ $post->scheduled_at->format('M d, Y H:i') }}</span>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Actions -->
            <div class="card">
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('blog-posts.edit', $post->id) }}" class="btn btn-primary">
                            <i class="bx bx-edit me-1"></i>Edit Post
                        </a>
                        
                        @if($post->status !== 'published')
                            <button type="button" class="btn btn-success" onclick="publishPost({{ $post->id }})">
                                <i class="bx bx-check me-1"></i>Publish
                            </button>
                        @else
                            <button type="button" class="btn btn-warning" onclick="unpublishPost({{ $post->id }})">
                                <i class="bx bx-x me-1"></i>Unpublish
                            </button>
                        @endif

                        <button type="button" class="btn btn-danger" onclick="deletePost({{ $post->id }})">
                            <i class="bx bx-trash me-1"></i>Delete Post
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
function publishPost(id) {
    obConfirm('Are you sure you want to publish this blog post?').then(function (ok) {
        if (!ok) return;
        $.post(`{{ url('/blog-posts') }}/${id}/publish`, {
            _token: $('meta[name="csrf-token"]').attr('content')
        })
        .done(function(response) {
            if (response.success) {
                toastr.success(response.message);
                setTimeout(() => location.reload(), 1000);
            } else {
                toastr.error(response.message);
            }
        })
        .fail(function() {
            toastr.error('Failed to publish blog post');
        });
    });
}

function unpublishPost(id) {
    obConfirm('Are you sure you want to unpublish this blog post?').then(function (ok) {
        if (!ok) return;
        $.post(`{{ url('/blog-posts') }}/${id}/unpublish`, {
            _token: $('meta[name="csrf-token"]').attr('content')
        })
        .done(function(response) {
            if (response.success) {
                toastr.success(response.message);
                setTimeout(() => location.reload(), 1000);
            } else {
                toastr.error(response.message);
            }
        })
        .fail(function() {
            toastr.error('Failed to unpublish blog post');
        });
    });
}

function deletePost(id) {
    obConfirm('Are you sure you want to delete this blog post? This action cannot be undone.').then(function (ok) {
        if (!ok) return;
        const deleteUrl = `{{ url('/blog-posts') }}/${id}`;
        console.log('Attempting to delete blog post ID:', id);
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
                setTimeout(() => window.location.href = '/blog-posts', 1000);
            } else {
                toastr.error(response.message);
            }
        })
        .fail(function(xhr, status, error) {
            console.error('Delete request failed:', xhr.responseText, status, error);
            if (xhr.responseJSON && xhr.responseJSON.message) {
                toastr.error(xhr.responseJSON.message);
            } else {
                toastr.error('Failed to delete blog post: ' + error);
            }
        });
    });
}
</script>
@endpush