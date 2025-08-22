@extends('layouts.app')

@section('title', 'Experience Management')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <!-- Header -->
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">
                        <span class="text-muted fw-light">Portfolio /</span> Experience
                    </h4>
                    <div class="d-flex gap-2">
                        <a href="{{ route('experiences.create') }}" class="btn btn-primary">
                            <i class="bx bx-plus me-1"></i>Add Experience
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Experience Table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Work Experience</h5>
                </div>
                <div class="card-datatable">
                    <table id="experiencesTable" class="datatables-experiences table border-top">
                        <thead>
                            <tr>
                                <th>Company</th>
                                <th>Position</th>
                                <th>Type</th>
                                <th>Duration</th>
                                <th>Period</th>
                                <th>Technologies</th>
                                <th>Current</th>
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
    table = $('#experiencesTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ route("experiences.index") }}',
            beforeSend: function() {
                // Show loading overlay
                $('#experiencesTable_wrapper').addClass('position-relative');
                if (!$('#experiencesTable_wrapper .loading-overlay').length) {
                    $('#experiencesTable_wrapper').append(`
                        <div class="loading-overlay position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center bg-white bg-opacity-75" style="z-index: 999;">
                            <div class="text-center">
                                <div class="spinner-border text-primary mb-2" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                                <div class="text-muted">Loading experience...</div>
                            </div>
                        </div>
                    `);
                }
            },
            complete: function() {
                // Hide loading overlay
                $('#experiencesTable_wrapper .loading-overlay').remove();
            }
        },
        columns: [
            {data: 'company_logo_display', name: 'company_name', orderable: false, searchable: true},
            {data: 'position', name: 'position'},
            {data: 'employment_badge', name: 'employment_type', orderable: false, searchable: false},
            {data: 'duration_display', name: 'duration', orderable: false, searchable: false},
            {
                data: null,
                name: 'period',
                orderable: false,
                searchable: false,
                render: function(data, type, row) {
                    return row.start_date + ' - ' + row.end_date;
                }
            },
            {data: 'technologies_count', name: 'technologies', orderable: false, searchable: false},
            {data: 'current_badge', name: 'is_current', orderable: false, searchable: false},
            {data: 'status_badge', name: 'is_active', orderable: false, searchable: false},
            {data: 'actions', name: 'actions', orderable: false, searchable: false}
        ],
        order: [[4, 'desc']], // Order by start_date desc
        responsive: true,
        language: {
            processing: `
                <div class="d-flex align-items-center justify-content-center p-4">
                    <div class="spinner-border text-primary me-3" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <div class="text-muted">Loading experience...</div>
                </div>
            `,
            loadingRecords: `
                <div class="d-flex align-items-center justify-content-center p-4">
                    <div class="spinner-border text-primary me-3" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <div class="text-muted">Loading records...</div>
                </div>
            `,
            emptyTable: `
                <div class="text-center p-4">
                    <i class="bx bx-briefcase text-muted" style="font-size: 3rem;"></i>
                    <div class="mt-2">No experience found</div>
                    <div class="text-muted">Add your first work experience to get started</div>
                </div>
            `
        },
        dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"f>>' +
             '<"table-responsive"t>' +
             '<"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
    });
});

// Toggle status function
function toggleStatus(id) {
    if (confirm('Are you sure you want to toggle this experience status?')) {
        $.post(`{{ url('/experiences') }}/${id}/toggle-status`, {
            _token: $('meta[name="csrf-token"]').attr('content')
        })
        .done(function(response) {
            if (response.success) {
                toastr.success(response.message);
                table.ajax.reload(null, false); // Keep current page
            } else {
                toastr.error(response.message);
            }
        })
        .fail(function() {
            toastr.error('Failed to toggle experience status');
        });
    }
}

// Delete experience function
function deleteExperience(id) {
    if (confirm('Are you sure you want to delete this experience? This action cannot be undone.')) {
        const deleteUrl = `{{ url('/experiences') }}/${id}`;
        console.log('Attempting to delete experience ID:', id);
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
                table.ajax.reload(null, false); // Keep current page
            } else {
                toastr.error(response.message);
            }
        })
        .fail(function(xhr, status, error) {
            console.error('Delete request failed:', xhr.responseText, status, error);
            if (xhr.responseJSON && xhr.responseJSON.message) {
                toastr.error(xhr.responseJSON.message);
            } else {
                toastr.error('Failed to delete experience: ' + error);
            }
        });
    }
}
</script>
@endpush