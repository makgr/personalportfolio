@extends('layouts.app')

@section('title', 'Contact Messages')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <!-- Header -->
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">
                        <span class="text-muted fw-light">Portfolio /</span> Contact Messages
                    </h4>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-outline-primary" onclick="refreshTable()">
                            <i class="bx bx-refresh me-1"></i>Refresh
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 col-6 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="card-title d-flex align-items-start justify-content-between">
                        <div class="avatar flex-shrink-0">
                            <i class="bx bx-envelope text-danger" style="font-size: 2rem;"></i>
                        </div>
                        <div class="dropdown">
                            <span class="badge bg-danger">{{ $statistics['new'] }}</span>
                        </div>
                    </div>
                    <span class="fw-semibold d-block mb-1">New Messages</span>
                    <h3 class="card-title text-nowrap mb-2">{{ $statistics['new'] }}</h3>
                    <small class="text-danger fw-semibold">Unread</small>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-6 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="card-title d-flex align-items-start justify-content-between">
                        <div class="avatar flex-shrink-0">
                            <i class="bx bx-envelope-open text-info" style="font-size: 2rem;"></i>
                        </div>
                        <div class="dropdown">
                            <span class="badge bg-info">{{ $statistics['read'] }}</span>
                        </div>
                    </div>
                    <span class="fw-semibold d-block mb-1">Read Messages</span>
                    <h3 class="card-title text-nowrap mb-2">{{ $statistics['read'] }}</h3>
                    <small class="text-success fw-semibold">Pending Reply</small>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-6 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="card-title d-flex align-items-start justify-content-between">
                        <div class="avatar flex-shrink-0">
                            <i class="bx bx-check-circle text-success" style="font-size: 2rem;"></i>
                        </div>
                        <div class="dropdown">
                            <span class="badge bg-success">{{ $statistics['replied'] }}</span>
                        </div>
                    </div>
                    <span class="fw-semibold d-block mb-1">Replied</span>
                    <h3 class="card-title text-nowrap mb-2">{{ $statistics['replied'] }}</h3>
                    <small class="text-success fw-semibold">Completed</small>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-6 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="card-title d-flex align-items-start justify-content-between">
                        <div class="avatar flex-shrink-0">
                            <i class="bx bx-archive text-secondary" style="font-size: 2rem;"></i>
                        </div>
                        <div class="dropdown">
                            <span class="badge bg-secondary">{{ $statistics['archived'] }}</span>
                        </div>
                    </div>
                    <span class="fw-semibold d-block mb-1">Archived</span>
                    <h3 class="card-title text-nowrap mb-2">{{ $statistics['archived'] }}</h3>
                    <small class="text-muted fw-semibold">Total: {{ $statistics['total'] }}</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Messages Table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">All Messages</h5>
                    <div class="d-flex gap-2">
                        <!-- Bulk Actions -->
                        <div class="dropdown">
                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" disabled id="bulkActionsBtn">
                                <i class="bx bx-check me-1"></i>Bulk Actions
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="#" onclick="bulkAction('mark_read')"><i class="bx bx-envelope-open me-1"></i>Mark as Read</a></li>
                                <li><a class="dropdown-item" href="#" onclick="bulkAction('mark_replied')"><i class="bx bx-check me-1"></i>Mark as Replied</a></li>
                                <li><a class="dropdown-item" href="#" onclick="bulkAction('archive')"><i class="bx bx-archive me-1"></i>Archive</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item text-danger" href="#" onclick="bulkAction('delete')"><i class="bx bx-trash me-1"></i>Delete</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="card-datatable">
                    <table id="contactsTable" class="datatables-contacts table border-top">
                        <thead>
                            <tr>
                                <th width="30">
                                    <input type="checkbox" class="form-check-input" id="selectAll">
                                </th>
                                <th></th>
                                <th>Sender</th>
                                <th>Subject</th>
                                <th>Message</th>
                                <th>Type</th>
                                <th>Budget</th>
                                <th>Status</th>
                                <th>Date</th>
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
var selectedIds = [];

$(document).ready(function() {
    // Initialize DataTable
    table = $('#contactsTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ route("contacts.index") }}',
            beforeSend: function() {
                // Show loading overlay
                $('#contactsTable_wrapper').addClass('position-relative');
                if (!$('#contactsTable_wrapper .loading-overlay').length) {
                    $('#contactsTable_wrapper').append(`
                        <div class="loading-overlay position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center bg-white bg-opacity-75" style="z-index: 999;">
                            <div class="text-center">
                                <div class="spinner-border text-primary mb-2" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                                <div class="text-muted">Loading messages...</div>
                            </div>
                        </div>
                    `);
                }
            },
            complete: function() {
                // Hide loading overlay
                $('#contactsTable_wrapper .loading-overlay').remove();
            }
        },
        columns: [
            {
                data: null,
                orderable: false,
                searchable: false,
                width: "30px",
                render: function(data, type, row) {
                    return '<input type="checkbox" class="form-check-input row-checkbox" value="' + row.id + '">';
                }
            },
            {data: 'priority_indicator', name: 'priority', orderable: false, searchable: false, width: "20px"},
            {data: 'sender_info', name: 'name', orderable: false, searchable: true},
            {data: 'subject', name: 'subject'},
            {data: 'message', name: 'message'},
            {data: 'type_badge', name: 'type', orderable: false, searchable: false},
            {data: 'budget_display', name: 'budget_range', orderable: false, searchable: false},
            {data: 'status_badge', name: 'status', orderable: false, searchable: false},
            {data: 'created_at', name: 'created_at'},
            {data: 'actions', name: 'actions', orderable: false, searchable: false, width: "120px"}
        ],
        order: [[8, 'desc']], // Order by created_at desc
        responsive: true,
        pageLength: 25,
        language: {
            processing: `
                <div class="d-flex align-items-center justify-content-center p-4">
                    <div class="spinner-border text-primary me-3" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <div class="text-muted">Loading messages...</div>
                </div>
            `,
            emptyTable: `
                <div class="text-center p-4">
                    <i class="bx bx-envelope text-muted" style="font-size: 3rem;"></i>
                    <div class="mt-2">No messages found</div>
                    <div class="text-muted">Contact messages will appear here</div>
                </div>
            `
        },
        dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"f>>' +
             '<"table-responsive"t>' +
             '<"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
    });

    // Handle individual checkbox selection
    $('#contactsTable').on('change', '.row-checkbox', function() {
        updateBulkActions();
    });

    // Handle select all checkbox
    $('#selectAll').on('change', function() {
        const isChecked = $(this).is(':checked');
        $('.row-checkbox').prop('checked', isChecked);
        updateBulkActions();
    });
});

function updateBulkActions() {
    selectedIds = [];
    $('.row-checkbox:checked').each(function() {
        selectedIds.push($(this).val());
    });

    if (selectedIds.length > 0) {
        $('#bulkActionsBtn').prop('disabled', false);
        $('#selectAll').prop('indeterminate', selectedIds.length > 0 && selectedIds.length < $('.row-checkbox').length);
    } else {
        $('#bulkActionsBtn').prop('disabled', true);
        $('#selectAll').prop('indeterminate', false);
    }
}

function refreshTable() {
    table.ajax.reload(null, false);
    // Also update statistics by reloading page
    setTimeout(function() {
        location.reload();
    }, 1000);
}

// Mark as read function
function markAsRead(id) {
    $.post(`{{ url('/contacts') }}/${id}/mark-as-read`, {
        _token: $('meta[name="csrf-token"]').attr('content')
    })
    .done(function(response) {
        if (response.success) {
            toastr.success(response.message);
            table.ajax.reload(null, false);
        } else {
            toastr.error(response.message);
        }
    })
    .fail(function() {
        toastr.error('Failed to mark message as read');
    });
}

// Mark as replied function  
function markAsReplied(id, notes = '') {
    $.post(`{{ url('/contacts') }}/${id}/mark-as-replied`, {
        _token: $('meta[name="csrf-token"]').attr('content'),
        admin_notes: notes
    })
    .done(function(response) {
        if (response.success) {
            toastr.success(response.message);
            table.ajax.reload(null, false);
        } else {
            toastr.error(response.message);
        }
    })
    .fail(function() {
        toastr.error('Failed to mark message as replied');
    });
}

// Archive function
function archiveMessage(id) {
    if (confirm('Are you sure you want to archive this message?')) {
        $.post(`{{ url('/contacts') }}/${id}/archive`, {
            _token: $('meta[name="csrf-token"]').attr('content')
        })
        .done(function(response) {
            if (response.success) {
                toastr.success(response.message);
                table.ajax.reload(null, false);
            } else {
                toastr.error(response.message);
            }
        })
        .fail(function() {
            toastr.error('Failed to archive message');
        });
    }
}

// Delete message function
function deleteMessage(id) {
    if (confirm('Are you sure you want to delete this message? This action cannot be undone.')) {
        const deleteUrl = `{{ url('/contacts') }}/${id}`;
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
            if (response.success) {
                toastr.success(response.message);
                table.ajax.reload(null, false);
            } else {
                toastr.error(response.message);
            }
        })
        .fail(function(xhr, status, error) {
            if (xhr.responseJSON && xhr.responseJSON.message) {
                toastr.error(xhr.responseJSON.message);
            } else {
                toastr.error('Failed to delete message: ' + error);
            }
        });
    }
}

// Bulk actions
function bulkAction(action) {
    if (selectedIds.length === 0) {
        toastr.warning('Please select messages to perform bulk action');
        return;
    }

    let confirmMessage = '';
    switch (action) {
        case 'delete':
            confirmMessage = `Are you sure you want to delete ${selectedIds.length} messages? This action cannot be undone.`;
            break;
        case 'mark_read':
            confirmMessage = `Mark ${selectedIds.length} messages as read?`;
            break;
        case 'mark_replied':
            confirmMessage = `Mark ${selectedIds.length} messages as replied?`;
            break;
        case 'archive':
            confirmMessage = `Archive ${selectedIds.length} messages?`;
            break;
    }

    if (confirm(confirmMessage)) {
        $.post('{{ route("contacts.bulk-action") }}', {
            _token: $('meta[name="csrf-token"]').attr('content'),
            action: action,
            ids: selectedIds
        })
        .done(function(response) {
            if (response.success) {
                toastr.success(response.message);
                table.ajax.reload(null, false);
                selectedIds = [];
                $('.row-checkbox').prop('checked', false);
                $('#selectAll').prop('checked', false);
                updateBulkActions();
            } else {
                toastr.error(response.message);
            }
        })
        .fail(function() {
            toastr.error('Failed to perform bulk action');
        });
    }
}
</script>
@endpush