@extends('layouts.app')

@section('title', 'Team Management')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <!-- Header -->
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">
                        <span class="text-muted fw-light">Portfolio /</span> Team
                    </h4>
                    <div class="d-flex gap-2">
                        <a href="{{ route('teams.create') }}" class="btn btn-primary">
                            <i class="bx bx-plus me-1"></i>Add Team Member
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Team Statistics -->
    <div class="row mb-4">
        <div class="col-xl-3 col-lg-6 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div class="card-info">
                            <p class="card-text">Total Team Members</p>
                            <div class="d-flex align-items-end mb-2">
                                <h4 class="card-title mb-0 me-2">{{ $stats['total'] }}</h4>
                            </div>
                            <small class="text-muted">All team members</small>
                        </div>
                        <div class="card-icon">
                            <span class="badge bg-label-primary rounded p-2">
                                <i class="bx bx-group bx-sm"></i>
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
                            <p class="card-text">Active Members</p>
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
                            <p class="card-text">Featured</p>
                            <div class="d-flex align-items-end mb-2">
                                <h4 class="card-title mb-0 me-2">{{ $stats['featured'] }}</h4>
                            </div>
                            <small class="text-muted">Featured members</small>
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
                            <p class="card-text">Full Time</p>
                            <div class="d-flex align-items-end mb-2">
                                <h4 class="card-title mb-0 me-2">{{ $stats['full_time'] }}</h4>
                            </div>
                            <small class="text-muted">Full-time employees</small>
                        </div>
                        <div class="card-icon">
                            <span class="badge bg-label-info rounded p-2">
                                <i class="bx bx-briefcase bx-sm"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Team Table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">All Team Members</h5>
                </div>
                <div class="card-datatable">
                    <table id="teamsTable" class="datatables-teams table border-top">
                        <thead>
                            <tr>
                                <th>Member</th>
                                <th>Department</th>
                                <th>Employment</th>
                                <th>Contact</th>
                                <th>Social</th>
                                <th>Join Date</th>
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
    table = $('#teamsTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ route("teams.index") }}',
            beforeSend: function() {
                // Show loading overlay
                $('#teamsTable_wrapper').addClass('position-relative');
                if (!$('#teamsTable_wrapper .loading-overlay').length) {
                    $('#teamsTable_wrapper').append(`
                        <div class="loading-overlay position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center bg-white bg-opacity-75" style="z-index: 999;">
                            <div class="text-center">
                                <div class="spinner-border text-primary mb-2" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                                <div class="text-muted">Loading team members...</div>
                            </div>
                        </div>
                    `);
                }
            },
            complete: function() {
                // Hide loading overlay
                $('#teamsTable_wrapper .loading-overlay').remove();
            }
        },
        columns: [
            {data: 'member_info', name: 'name'},
            {data: 'department_badge', name: 'department', orderable: false, searchable: false},
            {data: 'employment_badge', name: 'employment_type', orderable: false, searchable: false},
            {data: 'contact_info', name: 'email', orderable: false, searchable: false},
            {data: 'social_links', name: 'social_links', orderable: false, searchable: false},
            {data: 'join_info', name: 'join_date', orderable: false, searchable: false},
            {data: 'status_badges', name: 'is_active', orderable: false, searchable: false},
            {data: 'action', name: 'action', orderable: false, searchable: false}
        ],
        order: [[0, 'asc']], // Order by name asc
        responsive: true,
        language: {
            search: "",
            searchPlaceholder: "Search team members...",
            lengthMenu: "_MENU_",
            info: "Showing _START_ to _END_ of _TOTAL_ team members",
            infoEmpty: "No team members found",
            infoFiltered: "(filtered from _MAX_ total records)",
            zeroRecords: "No team members found",
            emptyTable: "No team members available"
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
        const teamId = $(this).data('id');
        const newStatus = $(this).data('status');
        const button = $(this);

        $.ajax({
            url: '{{ url("teams") }}/' + teamId + '/toggle-status',
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
                toastr.error('Failed to update team member status');
            },
            complete: function() {
                button.prop('disabled', false);
            }
        });
    });

    // Toggle Featured
    $(document).on('click', '.toggle-featured', function() {
        const teamId = $(this).data('id');
        const button = $(this);

        $.ajax({
            url: '{{ url("teams") }}/' + teamId + '/toggle-featured',
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

    // Delete Team Member
    $(document).on('click', '.delete-team', function() {
        const teamId = $(this).data('id');
        const button = $(this);

        if (confirm('Are you sure you want to remove this team member? This action cannot be undone.')) {
            $.ajax({
                url: '{{ url("teams") }}/' + teamId,
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    _method: 'DELETE'
                },
                beforeSend: function() {
                    button.prop('disabled', true);
                    toastr.info('Removing team member...', 'Please wait');
                },
                success: function(response) {
                    if (response.success) {
                        toastr.success(response.message);
                        table.ajax.reload(null, false);
                    } else {
                        toastr.error(response.message || 'Failed to remove team member');
                    }
                },
                error: function(xhr) {
                    console.error('Delete error:', xhr);
                    toastr.error('Failed to remove team member');
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