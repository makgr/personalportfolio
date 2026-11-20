@extends('layouts.app')

@section('title', 'Projects Management')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <!-- Header -->
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <span class="text-muted fw-light">Portfolio /</span> Projects
                    </h5>
                    <div>
                        <a href="{{ route('projects.create') }}" class="btn btn-primary">
                            <i class="bx bx-plus me-1"></i>Add Project
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Projects Table -->
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">All Projects</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover" id="projects-table">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Category</th>
                            <th>Status</th>
                            <th>Featured</th>
                            <th>Views</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Bulk Actions Modal -->
<div class="modal fade" id="bulkActionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Bulk Actions</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="bulkActionForm">
                    <div class="mb-3">
                        <label class="form-label">Select Action</label>
                        <select class="form-select" name="action" required>
                            <option value="">Choose action...</option>
                            <option value="publish">Publish Selected</option>
                            <option value="unpublish">Unpublish Selected</option>
                            <option value="feature">Mark as Featured</option>
                            <option value="unfeature">Remove Featured</option>
                            <option value="delete">Delete Selected</option>
                        </select>
                    </div>
                    <div class="alert alert-warning">
                        <i class="bx bx-info-circle me-2"></i>
                        <span id="selected-count">0</span> projects selected
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="executeBulkAction">Execute Action</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Initialize DataTable
    const table = $('#projects-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ route('projects.index') }}',
            type: 'GET'
        },
        columns: [
            { data: 'title', name: 'title' },
            { data: 'category.name', name: 'category.name', defaultContent: '-' },
            { data: 'status_badge', name: 'status', orderable: false, searchable: false },
            { data: 'featured_badge', name: 'is_featured', orderable: false, searchable: false },
            { data: 'views', name: 'views' },
            { data: 'created_at', name: 'created_at' },
            { data: 'actions', name: 'actions', orderable: false, searchable: false }
        ],
        order: [[5, 'desc']],
        pageLength: 25,
        responsive: true,
        language: {
            processing: '<div class="spinner-border spinner-border-sm text-primary" role="status"><span class="visually-hidden">Loading...</span></div>'
        },
        columnDefs: [
            {
                targets: 0,
                render: function(data, type, row) {
                    return '<div class="form-check">' +
                           '<input class="form-check-input row-checkbox" type="checkbox" value="' + row.id + '" id="check-' + row.id + '">' +
                           '<label class="form-check-label" for="check-' + row.id + '">' + data + '</label>' +
                           '</div>';
                }
            }
        ]
    });

    // Bulk Actions
    let selectedIds = [];
    
    // Handle checkbox changes
    $(document).on('change', '.row-checkbox', function() {
        const id = parseInt($(this).val());
        if ($(this).is(':checked')) {
            selectedIds.push(id);
        } else {
            selectedIds = selectedIds.filter(item => item !== id);
        }
        updateBulkActionButton();
    });

    // Update bulk action button
    function updateBulkActionButton() {
        const count = selectedIds.length;
        if (count > 0) {
            if ($('#bulkActionBtn').length === 0) {
                $('.card-header h5').after('<button class="btn btn-outline-primary btn-sm ms-2" id="bulkActionBtn">Bulk Actions (' + count + ')</button>');
            } else {
                $('#bulkActionBtn').text('Bulk Actions (' + count + ')');
            }
        } else {
            $('#bulkActionBtn').remove();
        }
        $('#selected-count').text(count);
    }

    // Handle bulk action button click
    $(document).on('click', '#bulkActionBtn', function() {
        $('#bulkActionModal').modal('show');
    });

    // Execute bulk action
    $('#executeBulkAction').click(function() {
        const action = $('#bulkActionForm select[name="action"]').val();
        if (!action || selectedIds.length === 0) return;

        const confirmMessage = action === 'delete' 
            ? 'Are you sure you want to delete the selected projects? This action cannot be undone.'
            : 'Are you sure you want to ' + action + ' the selected projects?';

        obConfirm(confirmMessage).then(function (ok) {
            if (!ok) return;
            $.ajax({
                url: '{{ route('projects.bulk-action') }}',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    action: action,
                    ids: selectedIds
                },
                success: function(response) {
                    if (response.success) {
                        toastr.success(response.message);
                        table.ajax.reload();
                        selectedIds = [];
                        updateBulkActionButton();
                        $('#bulkActionModal').modal('hide');
                    } else {
                        toastr.error(response.message);
                    }
                },
                error: function(xhr) {
                    toastr.error('An error occurred while processing your request.');
                }
            });
        });
    });

    // Handle individual actions
    $(document).on('click', '.btn-publish', function(e) {
        e.preventDefault();
        const url = $(this).data('url');
        const button = $(this);
        
        button.prop('disabled', true);
        
        $.ajax({
            url: url,
            type: 'POST',
            data: { _token: '{{ csrf_token() }}' },
            success: function(response) {
                if (response.success) {
                    toastr.success(response.message);
                    table.ajax.reload(null, false);
                } else {
                    toastr.error(response.message);
                }
            },
            error: function() {
                toastr.error('An error occurred.');
            },
            complete: function() {
                button.prop('disabled', false);
            }
        });
    });

    $(document).on('click', '.btn-delete', function(e) {
        e.preventDefault();
        const url = $(this).data('url');
        const title = $(this).data('title');
        
        obConfirm('Are you sure you want to delete "' + title + '"? This action cannot be undone.').then(function (ok) {
            if (!ok) return;
            $.ajax({
                url: url,
                type: 'DELETE',
                data: { _token: '{{ csrf_token() }}' },
                success: function(response) {
                    if (response.success) {
                        toastr.success(response.message);
                        table.ajax.reload();
                    } else {
                        toastr.error(response.message);
                    }
                },
                error: function() {
                    toastr.error('An error occurred while deleting the project.');
                }
            });
        });
    });
});
</script>
@endpush