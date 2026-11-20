<!-- Testimonial Actions Dropdown -->
<div class="dropdown">
    <button type="button" class="btn btn-sm btn-outline-primary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
        <i class="bx bx-dots-vertical-rounded"></i>
    </button>
    <ul class="dropdown-menu">
        <li>
            <a class="dropdown-item" href="{{ route('admin.testimonials.show', $testimonial->id) }}">
                <i class="bx bx-show me-1"></i> View Details
            </a>
        </li>
        <li>
            <a class="dropdown-item" href="{{ route('admin.testimonials.edit', $testimonial->id) }}">
                <i class="bx bx-edit me-1"></i> Edit
            </a>
        </li>
        <li><hr class="dropdown-divider"></li>
        <li>
            <button class="dropdown-item" onclick="toggleFeatured({{ $testimonial->id }})">
                <i class="bx bx-star me-1"></i> 
                {{ $testimonial->is_featured ? 'Remove Featured' : 'Mark Featured' }}
            </button>
        </li>
        <li>
            <button class="dropdown-item" onclick="toggleActive({{ $testimonial->id }})">
                <i class="bx bx-{{ $testimonial->is_active ? 'eye-off' : 'eye' }} me-1"></i> 
                {{ $testimonial->is_active ? 'Deactivate' : 'Activate' }}
            </button>
        </li>
        @if($testimonial->project)
        <li><hr class="dropdown-divider"></li>
        <li>
            <a class="dropdown-item" href="{{ route('admin.projects.show', $testimonial->project->id) }}">
                <i class="bx bx-folder me-1"></i> View Project
            </a>
        </li>
        @endif
        <li><hr class="dropdown-divider"></li>
        <li>
            <button class="dropdown-item text-danger" onclick="deleteTestimonial({{ $testimonial->id }})">
                <i class="bx bx-trash me-1"></i> Delete
            </button>
        </li>
    </ul>
</div>

<script>
// Toggle featured status
function toggleFeatured(id) {
    fetch(`/admin/testimonials/${id}/toggle-featured`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Reload the testimonials list or update the UI
            if (typeof loadTestimonials === 'function') {
                loadTestimonials(currentPage || 1);
            } else {
                location.reload();
            }
            
            toastr.success(data.message);
        } else {
            toastr.error(data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        toastr.error('Network error occurred');
    });
}

// Toggle active status
function toggleActive(id) {
    fetch(`/admin/testimonials/${id}/toggle-active`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Reload the testimonials list or update the UI
            if (typeof loadTestimonials === 'function') {
                loadTestimonials(currentPage || 1);
            } else {
                location.reload();
            }
            
            toastr.success(data.message);
        } else {
            toastr.error(data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        toastr.error('Network error occurred');
    });
}

// Delete testimonial
function deleteTestimonial(id) {
    obConfirm('Are you sure you want to delete this testimonial?').then(function (ok) {
        if (!ok) return;
        performDelete(id);
    });
}

function performDelete(id) {
    fetch(`/admin/testimonials/${id}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Reload the testimonials list or redirect
            if (typeof loadTestimonials === 'function') {
                loadTestimonials(currentPage || 1);
            } else {
                window.location.href = '/admin/testimonials';
            }
            
            toastr.success(data.message);
        } else {
            toastr.error(data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        toastr.error('Network error occurred');
    });
}
</script>