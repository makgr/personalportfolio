@extends('layouts.app')

@section('title', 'Testimonials Management')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <!-- Header -->
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">
                        <span class="text-muted fw-light">Portfolio /</span> Testimonials
                    </h4>
                    <div class="d-flex gap-2">
                        <a href="{{ route('admin.testimonials.create') }}" class="btn btn-primary">
                            <i class="bx bx-plus me-1"></i>Add New Testimonial
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Testimonial Statistics -->
    <div class="row mb-4">
        <div class="col-xl-3 col-lg-6 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div class="card-info">
                            <p class="card-text">Total Testimonials</p>
                            <div class="d-flex align-items-end mb-2">
                                <h4 class="card-title mb-0 me-2">{{ $stats['total'] }}</h4>
                            </div>
                            <small class="text-muted">All testimonials</small>
                        </div>
                        <div class="card-icon">
                            <span class="badge bg-label-primary rounded p-2">
                                <i class="bx bx-chat bx-sm"></i>
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
                            <p class="card-text">Featured</p>
                            <div class="d-flex align-items-end mb-2">
                                <h4 class="card-title mb-0 me-2">{{ $stats['featured'] }}</h4>
                            </div>
                            <small class="text-muted">Featured testimonials</small>
                        </div>
                        <div class="card-icon">
                            <span class="badge bg-label-warning rounded p-2">
                                <i class="bx bx-star bx-sm"></i>
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
                            <p class="card-text">High Rated</p>
                            <div class="d-flex align-items-end mb-2">
                                <h4 class="card-title mb-0 me-2">{{ $stats['high_rated'] }}</h4>
                            </div>
                            <small class="text-muted">4+ star ratings</small>
                        </div>
                        <div class="card-icon">
                            <span class="badge bg-label-success rounded p-2">
                                <i class="bx bx-like bx-sm"></i>
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
                            <p class="card-text">Active</p>
                            <div class="d-flex align-items-end mb-2">
                                <h4 class="card-title mb-0 me-2">{{ $stats['active'] }}</h4>
                            </div>
                            <small class="text-muted">Published testimonials</small>
                        </div>
                        <div class="card-icon">
                            <span class="badge bg-label-info rounded p-2">
                                <i class="bx bx-check-circle bx-sm"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Testimonials Table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Testimonials List</h5>
                </div>
                <div class="card-body">
                    <table id="testimonialsTable" class="table table-hover">
                        <thead>
                            <tr>
                                <th>Client</th>
                                <th>Company/Position</th>
                                <th>Content</th>
                                <th>Rating</th>
                                <th>Project</th>
                                <th>Status</th>
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
var table; // Declare table variable in global scope

$(document).ready(function() {
    // Initialize DataTable
    table = $('#testimonialsTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ route("admin.testimonials.index") }}',
            beforeSend: function() {
                // Show loading overlay
                $('#testimonialsTable_wrapper').addClass('position-relative');
                if (!$('#testimonialsTable_wrapper .loading-overlay').length) {
                    $('#testimonialsTable_wrapper').append(`
                        <div class="loading-overlay position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center bg-white bg-opacity-75" style="z-index: 999;">
                            <div class="text-center">
                                <div class="spinner-border text-primary mb-2" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                                <div class="text-muted">Loading testimonials...</div>
                            </div>
                        </div>
                    `);
                }
            },
            complete: function() {
                // Hide loading overlay
                $('#testimonialsTable_wrapper .loading-overlay').remove();
            }
        },
        columns: [
            { data: 'client_info', name: 'client_name' },
            { data: 'company_position', name: 'client_company', orderable: false },
            { data: 'content_preview', name: 'content', orderable: false },
            { data: 'rating_display', name: 'rating' },
            { data: 'project_info', name: 'project.title', orderable: false },
            { data: 'status_badges', name: 'is_active', orderable: false },
            { data: 'created_at', name: 'created_at' },
            { data: 'actions', name: 'actions', orderable: false, searchable: false }
        ],
        order: [[6, 'desc']], // Order by created_at desc
        pageLength: 25,
        responsive: true,
        drawCallback: function(settings) {
            // Reinitialize tooltips after each draw
            $('[data-bs-toggle="tooltip"]').tooltip();
        }
    });

    // Toggle Featured
    $(document).on('click', '.toggle-featured', function() {
        const testimonialId = $(this).data('id');
        const button = $(this);

        $.ajax({
            url: '{{ url("admin/testimonials") }}/' + testimonialId + '/toggle-featured',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}'
            },
            beforeSend: function() {
                button.prop('disabled', true);
            },
            success: function(response) {
                if (response.success) {
                    toastr.success(response.message);
                    table.ajax.reload(null, false);
                } else {
                    toastr.error(response.message || 'Failed to update featured status');
                }
            },
            error: function(xhr) {
                console.error('Featured toggle error:', xhr);
                toastr.error('Failed to update testimonial featured status');
            },
            complete: function() {
                button.prop('disabled', false);
            }
        });
    });

    // Delete Testimonial
    $(document).on('click', '.delete-testimonial', function() {
        const testimonialId = $(this).data('id');
        const button = $(this);

        if (confirm('Are you sure you want to delete this testimonial? This action cannot be undone.')) {
            $.ajax({
                url: '{{ url("admin/testimonials") }}/' + testimonialId,
                type: 'DELETE',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                beforeSend: function() {
                    button.prop('disabled', true);
                },
                success: function(response) {
                    if (response.success) {
                        toastr.success(response.message);
                        table.ajax.reload(null, false);
                    } else {
                        toastr.error(response.message || 'Failed to delete testimonial');
                    }
                },
                error: function(xhr) {
                    console.error('Delete error:', xhr);
                    toastr.error('Failed to delete testimonial');
                },
                complete: function() {
                    button.prop('disabled', false);
                }
            });
        }
    });
});
</script>
@endpush