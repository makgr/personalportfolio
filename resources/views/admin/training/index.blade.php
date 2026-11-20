@extends('layouts.app')

@section('title', 'Training Management')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <!-- Header -->
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">
                        <span class="text-muted fw-light">Portfolio /</span> Training
                    </h4>
                    <div class="d-flex gap-2">
                        <a href="{{ route('admin.training.create') }}" class="btn btn-primary">
                            <i class="bx bx-plus me-1"></i>Add New Training
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Training Statistics -->
    <div class="row mb-4">
        <div class="col-xl-3 col-lg-6 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div class="card-info">
                            <p class="card-text">Total Training</p>
                            <div class="d-flex align-items-end mb-2">
                                <h4 class="card-title mb-0 me-2">{{ $stats['total'] }}</h4>
                            </div>
                            <small class="text-muted">All training records</small>
                        </div>
                        <div class="card-icon">
                            <span class="badge bg-label-primary rounded p-2">
                                <i class="bx bx-book bx-sm"></i>
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
                            <p class="card-text">Completed</p>
                            <div class="d-flex align-items-end mb-2">
                                <h4 class="card-title mb-0 me-2">{{ $stats['completed'] }}</h4>
                            </div>
                            <small class="text-muted">Successfully completed</small>
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
                            <p class="card-text">In Progress</p>
                            <div class="d-flex align-items-end mb-2">
                                <h4 class="card-title mb-0 me-2">{{ $stats['in_progress'] }}</h4>
                            </div>
                            <small class="text-muted">Currently ongoing</small>
                        </div>
                        <div class="card-icon">
                            <span class="badge bg-label-primary rounded p-2">
                                <i class="bx bx-time-five bx-sm"></i>
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
                            <small class="text-muted">Highlighted training</small>
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
    </div>

    <!-- Training Table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">All Training Records</h5>
                </div>
                <div class="card-datatable">
                    <table id="trainingTable" class="datatables-training table border-top">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Provider</th>
                                <th>Type</th>
                                <th>Category</th>
                                <th>Status</th>
                                <th>Duration</th>
                                <th>Date Range</th>
                                <th>Rating</th>
                                <th>Featured</th>
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
    table = $('#trainingTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ route("admin.training.index") }}',
            beforeSend: function() {
                // Show loading overlay
                $('#trainingTable_wrapper').addClass('position-relative');
                if (!$('#trainingTable_wrapper .loading-overlay').length) {
                    $('#trainingTable_wrapper').append(`
                        <div class="loading-overlay position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center bg-white bg-opacity-75" style="z-index: 999;">
                            <div class="text-center">
                                <div class="spinner-border text-primary mb-2" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                                <div class="text-muted">Loading training records...</div>
                            </div>
                        </div>
                    `);
                }
            },
            complete: function() {
                // Hide loading overlay
                $('#trainingTable_wrapper .loading-overlay').remove();
            }
        },
        columns: [
            {data: 'title', name: 'title'},
            {data: 'provider', name: 'provider'},
            {data: 'type_badge', name: 'type', orderable: false, searchable: false},
            {data: 'category_badge', name: 'category', orderable: false, searchable: false},
            {data: 'status_badge', name: 'status', orderable: false, searchable: false},
            {data: 'duration_display', name: 'duration_hours', orderable: false, searchable: false},
            {data: 'date_range', name: 'start_date', orderable: false, searchable: false},
            {data: 'rating_display', name: 'rating', orderable: false, searchable: false},
            {data: 'featured_badge', name: 'is_featured', orderable: false, searchable: false},
            {data: 'action', name: 'action', orderable: false, searchable: false}
        ],
        order: [[0, 'asc']], // Order by title asc
        responsive: true,
        language: {
            search: "",
            searchPlaceholder: "Search training...",
            lengthMenu: "_MENU_",
            info: "Showing _START_ to _END_ of _TOTAL_ training records",
            infoEmpty: "No training records found",
            infoFiltered: "(filtered from _MAX_ total records)",
            zeroRecords: "No training records found",
            emptyTable: "No training records available"
        },
        dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>' +
             '<"row"<"col-sm-12"tr>>' +
             '<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
        lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
        pageLength: 25,
        drawCallback: function(settings) {
            // Reinitialize tooltips after each draw
            $('[data-bs-toggle="tooltip"]').tooltip();
        }
    });

    // Toggle Status
    $(document).on('click', '.toggle-status', function() {
        const trainingId = $(this).data('id');
        const newStatus = $(this).data('status');
        const button = $(this);

        $.ajax({
            url: '{{ url("admin/training") }}/' + trainingId + '/toggle-status',
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
                    toastr.error(response.message || 'Failed to update status');
                }
            },
            error: function(xhr) {
                console.error('Status toggle error:', xhr);
                toastr.error('Failed to update training status');
            },
            complete: function() {
                button.prop('disabled', false);
            }
        });
    });

    // Toggle Featured
    $(document).on('click', '.toggle-featured', function() {
        const trainingId = $(this).data('id');
        const button = $(this);

        $.ajax({
            url: '{{ url("admin/training") }}/' + trainingId + '/toggle-featured',
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
                toastr.error('Failed to update featured status');
            },
            complete: function() {
                button.prop('disabled', false);
            }
        });
    });

    // Delete Training
    $(document).on('click', '.delete-training', function() {
        const trainingId = $(this).data('id');
        const button = $(this);

        obConfirm('Are you sure you want to delete this training record? This action cannot be undone.').then(function (ok) {
            if (!ok) return;
            $.ajax({
                url: '{{ url("admin/training") }}/' + trainingId,
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    _method: 'DELETE'
                },
                beforeSend: function() {
                    button.prop('disabled', true);
                    toastr.info('Deleting training record...', 'Please wait');
                },
                success: function(response) {
                    if (response.success) {
                        toastr.success(response.message);
                        table.ajax.reload(null, false);
                    } else {
                        toastr.error(response.message || 'Failed to delete training record');
                    }
                },
                error: function(xhr) {
                    console.error('Delete error:', xhr);
                    toastr.error('Failed to delete training record');
                },
                complete: function() {
                    button.prop('disabled', false);
                }
            });
        });
    });
});
</script>
@endpush