@extends('layouts.app')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <!-- Header -->
        <div class="row">
            <div class="col-12">
                <!-- Success/Error Messages -->
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <div class="card mb-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="card-title m-0">Newsletter Subscribers</h5>
                                <p class="card-text">Manage newsletter subscribers and their status</p>
                            </div>
                            <div>
                                <a href="#" class="btn btn-success" onclick="exportSubscribers()">
                                    <i class="bx bx-download"></i> Export
                                </a>
                            </div>
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
                                <i class="bx bx-group bx-sm text-primary"></i>
                            </div>
                        </div>
                        <span class="fw-semibold d-block mb-1">Total Subscribers</span>
                        <h3 class="card-title mb-2">{{ number_format($stats['total']) }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-6 mb-4">
                <div class="card">
                    <div class="card-body">
                        <div class="card-title d-flex align-items-start justify-content-between">
                            <div class="avatar flex-shrink-0">
                                <i class="bx bx-calendar bx-sm text-info"></i>
                            </div>
                        </div>
                        <span class="fw-semibold d-block mb-1">Today</span>
                        <h3 class="card-title mb-2">{{ number_format($stats['today']) }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-6 mb-4">
                <div class="card">
                    <div class="card-body">
                        <div class="card-title d-flex align-items-start justify-content-between">
                            <div class="avatar flex-shrink-0">
                                <i class="bx bx-calendar-week bx-sm text-success"></i>
                            </div>
                        </div>
                        <span class="fw-semibold d-block mb-1">This Week</span>
                        <h3 class="card-title mb-2">{{ number_format($stats['this_week']) }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-6 mb-4">
                <div class="card">
                    <div class="card-body">
                        <div class="card-title d-flex align-items-start justify-content-between">
                            <div class="avatar flex-shrink-0">
                                <i class="bx bx-calendar-alt bx-sm text-warning"></i>
                            </div>
                        </div>
                        <span class="fw-semibold d-block mb-1">This Month</span>
                        <h3 class="card-title mb-2">{{ number_format($stats['this_month']) }}</h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('admin.newsletter-subscribers.index') }}" class="row g-3" id="filterForm">
                    <div class="col-md-4">
                        <label for="search" class="form-label">Search by Email</label>
                        <input type="text" class="form-control" id="search" name="search" 
                               value="{{ request('search') }}" placeholder="Search by email...">
                    </div>
                    <div class="col-md-3">
                        <label for="date_from" class="form-label">From Date</label>
                        <input type="date" class="form-control" id="date_from" name="date_from" 
                               value="{{ request('date_from') }}">
                    </div>
                    <div class="col-md-3">
                        <label for="date_to" class="form-label">To Date</label>
                        <input type="date" class="form-control" id="date_to" name="date_to" 
                               value="{{ request('date_to') }}">
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary me-2">Filter</button>
                        <a href="{{ route('admin.newsletter-subscribers.index') }}" class="btn btn-outline-secondary">Clear</a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Subscribers List -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Subscribers List ({{ $subscribers->total() }})</h5>
            </div>
            <div class="table-responsive text-nowrap">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Email</th>
                            <th>Subscribed Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($subscribers as $subscriber)
                            <tr>
                                <td>
                                    <strong>{{ $subscriber->email }}</strong>
                                </td>
                                <td>
                                    <span>{{ $subscriber->created_at->format('M d, Y H:i') }}</span>
                                    <br>
                                    <small class="text-muted">{{ $subscriber->created_at->diffForHumans() }}</small>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-outline-danger" 
                                            onclick="deleteSubscriber({{ $subscriber->id }})">
                                        <i class="bx bx-trash"></i> Delete
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center py-4">
                                    <div class="d-flex flex-column align-items-center">
                                        <i class="bx bx-mail-send bx-lg text-muted mb-2"></i>
                                        <p class="text-muted mb-0">No newsletter subscribers found</p>
                                        @if(request()->hasAny(['search', 'date_from', 'date_to']))
                                            <small class="text-muted">Try adjusting your filters</small>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($subscribers->hasPages())
                <div class="card-footer">
                    {{ $subscribers->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function deleteSubscriber(subscriberId) {
            obConfirm('Are you sure you want to delete this subscriber? This action cannot be undone.').then(function (ok) {
                if (!ok) return;
                fetch(`/admin/newsletter-subscribers/${subscriberId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        toastr.success(data.message);
                        location.reload();
                    } else {
                        toastr.error(data.message || 'Failed to delete subscriber');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    toastr.error('An error occurred while deleting subscriber');
                });
            });
        }

        function exportSubscribers() {
            // Get current filter values
            const form = document.getElementById('filterForm');
            const formData = new FormData(form);
            const params = new URLSearchParams();
            
            // Add non-empty values to params
            for (let [key, value] of formData.entries()) {
                if (value.trim() !== '') {
                    params.append(key, value);
                }
            }
            
            // Build export URL with current filters
            const exportUrl = '{{ route("admin.newsletter-subscribers.export") }}' + '?' + params.toString();
            
            // Show loading message
            toastr.info('Preparing export... Please wait.', 'Exporting');
            
            // Trigger download
            window.location.href = exportUrl;
        }

        // Set max date for date inputs to today
        document.addEventListener('DOMContentLoaded', function() {
            const today = new Date().toISOString().split('T')[0];
            document.getElementById('date_from').setAttribute('max', today);
            document.getElementById('date_to').setAttribute('max', today);
            
            // Ensure date_from is not greater than date_to
            document.getElementById('date_from').addEventListener('change', function() {
                const dateFrom = this.value;
                if (dateFrom) {
                    document.getElementById('date_to').setAttribute('min', dateFrom);
                }
            });
            
            document.getElementById('date_to').addEventListener('change', function() {
                const dateTo = this.value;
                if (dateTo) {
                    document.getElementById('date_from').setAttribute('max', dateTo);
                }
            });
        });
    </script>
@endpush