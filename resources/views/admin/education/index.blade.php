@extends('layouts.app')

@section('title', 'Education Management')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <!-- Header -->
        <div class="row">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">
                            <span class="text-muted fw-light">Portfolio /</span> Education
                        </h4>
                        <div class="d-flex gap-2">
                            <a href="{{ route('education.create') }}" class="btn btn-primary">
                                <i class="bx bx-plus me-1"></i>Add Education
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Education Table -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">All Education Records</h5>
                        <div class="d-flex gap-2">
                            <!-- Bulk Actions -->
                            <div class="dropdown">
                                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button"
                                    data-bs-toggle="dropdown" disabled id="bulkActionsBtn">
                                    <i class="bx bx-check me-1"></i>Bulk Actions
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="#" onclick="bulkAction('activate')"><i
                                                class="bx bx-check me-1"></i>Activate</a></li>
                                    <li><a class="dropdown-item" href="#" onclick="bulkAction('deactivate')"><i
                                                class="bx bx-x me-1"></i>Deactivate</a></li>
                                    <li>
                                        <hr class="dropdown-divider">
                                    </li>
                                    <li><a class="dropdown-item text-danger" href="#"
                                            onclick="bulkAction('delete')"><i class="bx bx-trash me-1"></i>Delete</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="card-datatable">
                        <table id="educationTable" class="datatables-education table border-top">
                            <thead>
                                <tr>
                                    <th width="30">
                                        <input type="checkbox" class="form-check-input" id="selectAll">
                                    </th>
                                    <th>Institution</th>
                                    <th>Education Details</th>
                                    <th>Duration</th>
                                    <th>Status</th>
                                    <th>Activities & Courses</th>
                                    <th>Description</th>
                                    <th width="120">Actions</th>
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
            table = $('#educationTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('education.index') }}',
                    beforeSend: function() {
                        // Show loading overlay
                        $('#educationTable_wrapper').addClass('position-relative');
                        if (!$('#educationTable_wrapper .loading-overlay').length) {
                            $('#educationTable_wrapper').append(`
                        <div class="loading-overlay position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center bg-white bg-opacity-75" style="z-index: 999;">
                            <div class="text-center">
                                <div class="spinner-border text-primary mb-2" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                                <div class="text-muted">Loading education records...</div>
                            </div>
                        </div>
                    `);
                        }
                    },
                    complete: function() {
                        // Hide loading overlay
                        $('#educationTable_wrapper .loading-overlay').remove();
                    }
                },
                columns: [{
                        data: null,
                        orderable: false,
                        searchable: false,
                        width: "30px",
                        render: function(data, type, row) {
                            return '<input type="checkbox" class="form-check-input row-checkbox" value="' +
                                row.id + '">';
                        }
                    },
                    {
                        data: 'institution_info',
                        name: 'institution',
                        orderable: false,
                        searchable: true
                    },
                    {
                        data: 'education_details',
                        name: 'degree',
                        orderable: false,
                        searchable: true
                    },
                    {
                        data: 'duration',
                        name: 'start_date',
                        orderable: true,
                        searchable: false
                    },
                    {
                        data: 'status_badge',
                        name: 'is_current',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'activities_courses',
                        name: 'activities',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'description',
                        name: 'description',
                        orderable: false,
                        searchable: true
                    },
                    {
                        data: 'actions',
                        name: 'actions',
                        orderable: false,
                        searchable: false,
                        width: "120px"
                    }
                ],
                order: [
                    [3, 'desc']
                ], // Order by start_date desc
                responsive: true,
                pageLength: 25,
                language: {
                    processing: `
                <div class="d-flex align-items-center justify-content-center p-4">
                    <div class="spinner-border text-primary me-3" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <div class="text-muted">Loading education records...</div>
                </div>
            `,
                    emptyTable: `
                <div class="text-center p-4">
                    <i class="bx bx-book text-muted" style="font-size: 3rem;"></i>
                    <div class="mt-2">No education records found</div>
                    <div class="text-muted">Education records will appear here</div>
                </div>
            `
                },
                dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"f>>' +
                    '<"table-responsive"t>' +
                    '<"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
            });

            // Handle individual checkbox selection
            $('#educationTable').on('change', '.row-checkbox', function() {
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
                $('#selectAll').prop('indeterminate', selectedIds.length > 0 && selectedIds.length < $('.row-checkbox')
                    .length);
            } else {
                $('#bulkActionsBtn').prop('disabled', true);
                $('#selectAll').prop('indeterminate', false);
            }
        }

        // Toggle status function
        function toggleStatus(id) {
            $.post(`{{ url('/education') }}/${id}/toggle-status`, {
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
                    toastr.error('Failed to update education status');
                });
        }

        // Delete education function
        function deleteEducation(id) {
            obConfirm('Are you sure you want to delete this education record? This action cannot be undone.').then(function (ok) {
                if (!ok) return;
                const deleteUrl = `{{ url('/education') }}/${id}`;
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
                            toastr.error('Failed to delete education record: ' + error);
                        }
                    });
            });
        }

        // Bulk actions
        function bulkAction(action) {
            if (selectedIds.length === 0) {
                toastr.warning('Please select education records to perform bulk action');
                return;
            }

            let confirmMessage = '';
            switch (action) {
                case 'delete':
                    confirmMessage =
                        `Are you sure you want to delete ${selectedIds.length} education records? This action cannot be undone.`;
                    break;
                case 'activate':
                    confirmMessage = `Activate ${selectedIds.length} education records?`;
                    break;
                case 'deactivate':
                    confirmMessage = `Deactivate ${selectedIds.length} education records?`;
                    break;
            }

            obConfirm(confirmMessage).then(function (ok) {
                if (!ok) return;
                $.post('{{ route('education.bulk-action') }}', {
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
            });
        }
    </script>
@endpush
