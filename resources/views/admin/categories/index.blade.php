@extends('layouts.app')

@section('title', 'Categories Management')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <!-- Header -->
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">
                        <span class="text-muted fw-light">Content /</span> Categories
                    </h4>
                    <div class="d-flex gap-2">
                        <a href="{{ route('categories.create') }}" class="btn btn-primary">
                            <i class="bx bx-plus me-1"></i>Add Category
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Category Statistics -->
    <div class="row mb-4">
        <div class="col-xl-3 col-lg-6 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div class="card-info">
                            <p class="card-text">Total Categories</p>
                            <div class="d-flex align-items-end mb-2">
                                <h4 class="card-title mb-0 me-2">{{ $stats['total'] }}</h4>
                            </div>
                            <small class="text-muted">All categories</small>
                        </div>
                        <div class="card-icon">
                            <span class="badge bg-label-primary rounded p-2">
                                <i class="bx bx-category bx-sm"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div class="card-info">
                            <p class="card-text">Active Categories</p>
                            <div class="d-flex align-items-end mb-2">
                                <h4 class="card-title mb-0 me-2">{{ $stats['active'] }}</h4>
                            </div>
                            <small class="text-muted">Currently active</small>
                        </div>
                        <div class="card-icon">
                            <span class="badge bg-label-success rounded p-2">
                                <i class="bx bx-check-circle bx-sm"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div class="card-info">
                            <p class="card-text">Project Categories</p>
                            <div class="d-flex align-items-end mb-2">
                                <h4 class="card-title mb-0 me-2">{{ $stats['project'] }}</h4>
                            </div>
                            <small class="text-muted">For projects</small>
                        </div>
                        <div class="card-icon">
                            <span class="badge bg-label-info rounded p-2">
                                <i class="bx bx-folder bx-sm"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div class="card-info">
                            <p class="card-text">Blog Categories</p>
                            <div class="d-flex align-items-end mb-2">
                                <h4 class="card-title mb-0 me-2">{{ $stats['blog'] }}</h4>
                            </div>
                            <small class="text-muted">For blog posts</small>
                        </div>
                        <div class="card-icon">
                            <span class="badge bg-label-warning rounded p-2">
                                <i class="bx bx-edit bx-sm"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Categories Table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">All Categories</h5>
                </div>
                <div class="card-datatable">
                    <table id="categoriesTable" class="datatables-categories table border-top">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Type</th>
                                <th>Slug</th>
                                <th>Color</th>
                                <th>Icon</th>
                                <th>Content Count</th>
                                <th>Order</th>
                                <th>Status</th>
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
var table; // Declare table variable in global scope

$(document).ready(function() {
    // Initialize DataTable
    table = $('#categoriesTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ route("categories.index") }}',
            beforeSend: function() {
                // Show loading overlay
                $('.card-datatable').addClass('position-relative');
                $('.card-datatable').append('<div class="loading-overlay"><div class="spinner-border" role="status"><span class="visually-hidden">Loading...</span></div></div>');
            },
            complete: function() {
                // Hide loading overlay
                $('.loading-overlay').remove();
                $('.card-datatable').removeClass('position-relative');
            }
        },
        columns: [
            { data: 'name', name: 'name' },
            { data: 'type_badge', name: 'type', orderable: false },
            { data: 'slug', name: 'slug' },
            { data: 'color_preview', name: 'color', orderable: false },
            { data: 'icon_preview', name: 'icon', orderable: false },
            { data: 'content_count', name: 'content_count', orderable: false },
            { data: 'order', name: 'order' },
            { data: 'status_badge', name: 'is_active', orderable: false },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ],
        order: [[6, 'asc']], // Order by 'order' column
        pageLength: 25,
        responsive: true,
        language: {
            search: "Search categories:",
            lengthMenu: "Show _MENU_ categories",
            info: "Showing _START_ to _END_ of _TOTAL_ categories",
            infoEmpty: "No categories found",
            infoFiltered: "(filtered from _MAX_ total categories)"
        },
        drawCallback: function() {
            // Re-bind AJAX event handlers after each table draw
            $('[data-ajax]').off('click.admin-ajax').on('click.admin-ajax', function(e) {
                // This will be handled by AdminAjax auto event delegation
            });
        }
    });

    // Refresh table data when needed
    window.refreshCategoriesTable = function() {
        if (table) {
            table.ajax.reload(null, false); // Keep current page
        }
    };
});
</script>
@endpush