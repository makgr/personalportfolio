@extends('layouts.app')

@section('title', 'Blog Posts Management')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <!-- Header -->
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">
                        <span class="text-muted fw-light">Content /</span> Blog Posts
                    </h4>
                    <div class="d-flex gap-2">
                        <a href="{{ route('blog-posts.create') }}" class="btn btn-primary">
                            <i class="bx bx-plus me-1"></i>Create New Post
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Blog Posts Table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">All Blog Posts</h5>
                </div>
                <div class="card-datatable">
                    <table id="blogPostsTable" class="datatables-posts table border-top">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Author</th>
                                <th>Category</th>
                                <th>Status</th>
                                <th>Featured</th>
                                <th>Views</th>
                                <th>Reading Time</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Initialize DataTable
    $('#blogPostsTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ route("blog-posts.index") }}',
        columns: [
            {data: 'title', name: 'title'},
            {data: 'author_name', name: 'author.name', orderable: false, searchable: false},
            {data: 'category_name', name: 'category.name', orderable: false, searchable: false},
            {data: 'status_badge', name: 'status', orderable: false, searchable: false},
            {data: 'featured_badge', name: 'is_featured', orderable: false, searchable: false},
            {data: 'views', name: 'views'},
            {data: 'reading_time', name: 'reading_time', orderable: false, searchable: false},
            {data: 'created_at', name: 'created_at'},
            {data: 'actions', name: 'actions', orderable: false, searchable: false}
        ],
        order: [[7, 'desc']], // Order by created_at desc
        responsive: true,
        language: {
            processing: '<div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div>'
        },
        dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"f>>' +
             '<"table-responsive"t>' +
             '<"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
    });
});

// Publish post function
function publishPost(id) {
    if (confirm('Are you sure you want to publish this blog post?')) {
        $.post(`/blog-posts/${id}/publish`, {
            _token: $('meta[name="csrf-token"]').attr('content')
        })
        .done(function(response) {
            if (response.success) {
                toastr.success(response.message);
                $('#blogPostsTable').DataTable().ajax.reload();
            } else {
                toastr.error(response.message);
            }
        })
        .fail(function() {
            toastr.error('Failed to publish blog post');
        });
    }
}

// Unpublish post function
function unpublishPost(id) {
    if (confirm('Are you sure you want to unpublish this blog post?')) {
        $.post(`/blog-posts/${id}/unpublish`, {
            _token: $('meta[name="csrf-token"]').attr('content')
        })
        .done(function(response) {
            if (response.success) {
                toastr.success(response.message);
                $('#blogPostsTable').DataTable().ajax.reload();
            } else {
                toastr.error(response.message);
            }
        })
        .fail(function() {
            toastr.error('Failed to unpublish blog post');
        });
    }
}

// Delete post function
function deletePost(id) {
    if (confirm('Are you sure you want to delete this blog post? This action cannot be undone.')) {
        $.ajax({
            url: `/blog-posts/${id}`,
            type: 'DELETE',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content')
            }
        })
        .done(function(response) {
            if (response.success) {
                toastr.success(response.message);
                $('#blogPostsTable').DataTable().ajax.reload();
            } else {
                toastr.error(response.message);
            }
        })
        .fail(function() {
            toastr.error('Failed to delete blog post');
        });
    }
}
</script>
@endpush