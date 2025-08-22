@extends('layouts.app')

@section('title', 'Skills Management')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <!-- Header -->
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">
                        <span class="text-muted fw-light">Portfolio /</span> Skills
                    </h4>
                    <div class="d-flex gap-2">
                        <a href="{{ route('skills.create') }}" class="btn btn-primary">
                            <i class="bx bx-plus me-1"></i>Add New Skill
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Skills Table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">All Skills</h5>
                </div>
                <div class="card-datatable">
                    <table id="skillsTable" class="datatables-skills table border-top">
                        <thead>
                            <tr>
                                <th>Icon</th>
                                <th>Name</th>
                                <th>Category</th>
                                <th>Proficiency</th>
                                <th>Experience</th>
                                <th>Status</th>
                                <th>Primary</th>
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
    var table = $('#skillsTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ route("skills.index") }}',
            beforeSend: function() {
                // Show loading overlay
                $('#skillsTable_wrapper').addClass('position-relative');
                if (!$('#skillsTable_wrapper .loading-overlay').length) {
                    $('#skillsTable_wrapper').append(`
                        <div class="loading-overlay position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center bg-white bg-opacity-75" style="z-index: 999;">
                            <div class="text-center">
                                <div class="spinner-border text-primary mb-2" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                                <div class="text-muted">Loading skills...</div>
                            </div>
                        </div>
                    `);
                }
            },
            complete: function() {
                // Hide loading overlay
                $('#skillsTable_wrapper .loading-overlay').remove();
            }
        },
        columns: [
            {data: 'icon_display', name: 'icon', orderable: false, searchable: false},
            {data: 'name', name: 'name'},
            {data: 'category_badge', name: 'category', orderable: false, searchable: false},
            {data: 'proficiency_badge', name: 'proficiency', orderable: false, searchable: false},
            {data: 'years_experience', name: 'years_experience'},
            {data: 'status_badge', name: 'is_active', orderable: false, searchable: false},
            {data: 'primary_badge', name: 'is_primary', orderable: false, searchable: false},
            {data: 'created_at', name: 'created_at'},
            {data: 'actions', name: 'actions', orderable: false, searchable: false}
        ],
        order: [[7, 'desc']], // Order by created_at desc
        responsive: true,
        language: {
            processing: `
                <div class="d-flex align-items-center justify-content-center p-4">
                    <div class="spinner-border text-primary me-3" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <div class="text-muted">Loading skills...</div>
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
                    <i class="bx bx-code text-muted" style="font-size: 3rem;"></i>
                    <div class="mt-2">No skills found</div>
                    <div class="text-muted">Add your first skill to get started</div>
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
    if (confirm('Are you sure you want to toggle this skill status?')) {
        $.post(`{{ url('/skills') }}/${id}/toggle-status`, {
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
            toastr.error('Failed to toggle skill status');
        });
    }
}

// Toggle primary function
function togglePrimary(id) {
    if (confirm('Are you sure you want to toggle this skill primary status?')) {
        $.post(`{{ url('/skills') }}/${id}/toggle-primary`, {
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
            toastr.error('Failed to toggle primary status');
        });
    }
}

// Delete skill function
function deleteSkill(id) {
    if (confirm('Are you sure you want to delete this skill? This action cannot be undone.')) {
        const deleteUrl = `{{ url('/skills') }}/${id}`;
        console.log('Attempting to delete skill ID:', id);
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
                toastr.error('Failed to delete skill: ' + error);
            }
        });
    }
}
</script>
@endpush