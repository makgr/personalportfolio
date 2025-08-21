@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <!-- Header -->
    <div class="row">
        <div class="col-lg-8 mb-4 order-0">
            <div class="card">
                <div class="d-flex align-items-end row">
                    <div class="col-sm-7">
                        <div class="card-body">
                            <h5 class="card-title text-primary">Welcome back, {{ auth()->user()->name }}! 🎉</h5>
                            <p class="mb-4">
                                Your portfolio is performing well. Here's what's happening with your projects and blog.
                            </p>
                            <a href="{{ route('projects.create') }}" class="btn btn-sm btn-outline-primary">
                                Create New Project
                            </a>
                        </div>
                    </div>
                    <div class="col-sm-5 text-center text-sm-left">
                        <div class="card-body pb-0 px-0 px-md-4">
                            <img src="{{ asset('assets/img/illustrations/man-with-laptop-light.png') }}" 
                                 height="140" alt="View Badge User" 
                                 data-app-dark-img="illustrations/man-with-laptop-dark.png" 
                                 data-app-light-img="illustrations/man-with-laptop-light.png">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-md-4 order-1">
            <div class="row">
                <div class="col-lg-6 col-md-12 col-6 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="card-title d-flex align-items-start justify-content-between">
                                <div class="avatar flex-shrink-0">
                                    <img src="{{ asset('assets/img/icons/unicons/chart-success.png') }}" 
                                         alt="chart success" class="rounded">
                                </div>
                                <div class="dropdown">
                                    <button class="btn p-0" type="button" id="cardOpt3" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="bx bx-dots-vertical-rounded"></i>
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-end" aria-labelledby="cardOpt3">
                                        <a class="dropdown-item" href="{{ route('projects.index') }}">View All</a>
                                    </div>
                                </div>
                            </div>
                            <span class="fw-semibold d-block mb-1">Projects</span>
                            <h3 class="card-title mb-2" id="total-projects">-</h3>
                            <small class="text-success fw-semibold">
                                <i class="bx bx-up-arrow-alt"></i> 
                                <span id="projects-change">+0%</span>
                            </small>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 col-md-12 col-6 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="card-title d-flex align-items-start justify-content-between">
                                <div class="avatar flex-shrink-0">
                                    <img src="{{ asset('assets/img/icons/unicons/wallet-info.png') }}" 
                                         alt="Credit Card" class="rounded">
                                </div>
                                <div class="dropdown">
                                    <button class="btn p-0" type="button" id="cardOpt6" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="bx bx-dots-vertical-rounded"></i>
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-end" aria-labelledby="cardOpt6">
                                        <a class="dropdown-item" href="{{ route('blog-posts.index') }}">View All</a>
                                    </div>
                                </div>
                            </div>
                            <span class="fw-semibold d-block mb-1">Blog Posts</span>
                            <h3 class="card-title text-nowrap mb-1" id="total-posts">-</h3>
                            <small class="text-success fw-semibold">
                                <i class="bx bx-up-arrow-alt"></i> 
                                <span id="posts-change">+0%</span>
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row">
        <div class="col-12 col-sm-6 col-lg-3 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div class="content-left">
                            <span class="text-muted fw-medium d-block mb-2">Total Views</span>
                            <div class="d-flex align-items-end mt-2">
                                <h4 class="mb-0 me-2" id="total-views">-</h4>
                            </div>
                        </div>
                        <div class="avatar">
                            <span class="avatar-initial rounded bg-label-primary">
                                <i class="bx bx-show text-primary"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-lg-3 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div class="content-left">
                            <span class="text-muted fw-medium d-block mb-2">New Messages</span>
                            <div class="d-flex align-items-end mt-2">
                                <h4 class="mb-0 me-2" id="new-messages">-</h4>
                            </div>
                        </div>
                        <div class="avatar">
                            <span class="avatar-initial rounded bg-label-info">
                                <i class="bx bx-envelope text-info"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-lg-3 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div class="content-left">
                            <span class="text-muted fw-medium d-block mb-2">Featured Items</span>
                            <div class="d-flex align-items-end mt-2">
                                <h4 class="mb-0 me-2" id="featured-items">-</h4>
                            </div>
                        </div>
                        <div class="avatar">
                            <span class="avatar-initial rounded bg-label-warning">
                                <i class="bx bx-star text-warning"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-lg-3 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div class="content-left">
                            <span class="text-muted fw-medium d-block mb-2">Active Skills</span>
                            <div class="d-flex align-items-end mt-2">
                                <h4 class="mb-0 me-2" id="active-skills">-</h4>
                            </div>
                        </div>
                        <div class="avatar">
                            <span class="avatar-initial rounded bg-label-success">
                                <i class="bx bx-code-alt text-success"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts and Recent Items -->
    <div class="row">
        <!-- Analytics Chart -->
        <div class="col-md-6 col-lg-4 col-xl-4 order-0 mb-4">
            <div class="card h-100">
                <div class="card-header d-flex align-items-center justify-content-between pb-0">
                    <div class="card-title mb-0">
                        <h5 class="m-0 me-2">Monthly Analytics</h5>
                        <small class="text-muted">Page Views</small>
                    </div>
                    <div class="dropdown">
                        <button class="btn p-0" type="button" id="analyticsChart" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="bx bx-dots-vertical-rounded"></i>
                        </button>
                        <div class="dropdown-menu dropdown-menu-end" aria-labelledby="analyticsChart">
                            <a class="dropdown-item" href="javascript:void(0);">Select All</a>
                            <a class="dropdown-item" href="javascript:void(0);">Refresh</a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-center">
                        <canvas id="pageViewsChart" height="200"></canvas>
                    </div>
                    <div class="text-center mt-4">
                        <div class="d-flex justify-content-around">
                            <div>
                                <h6 class="mb-1" id="this-month-views">-</h6>
                                <small class="text-muted">This Month</small>
                            </div>
                            <div>
                                <h6 class="mb-1" id="last-month-views">-</h6>
                                <small class="text-muted">Last Month</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Projects -->
        <div class="col-md-6 col-lg-4 order-1 mb-4">
            <div class="card h-100">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="card-title m-0 me-2">Recent Projects</h5>
                    <a href="{{ route('projects.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
                </div>
                <div class="card-body" id="recent-projects">
                    <div class="text-center py-4">
                        <div class="spinner-border spinner-border-sm text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Blog Posts -->
        <div class="col-md-12 col-lg-4 order-2 mb-4">
            <div class="card h-100">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="card-title m-0 me-2">Recent Posts</h5>
                    <a href="{{ route('blog-posts.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
                </div>
                <div class="card-body" id="recent-posts">
                    <div class="text-center py-4">
                        <div class="spinner-border spinner-border-sm text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="row">
        <div class="col-md-6 col-lg-8 mb-3">
            <div class="card h-100">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="card-title m-0 me-2">Recent Messages</h5>
                    <a href="{{ route('contacts.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
                </div>
                <div class="table-responsive text-nowrap">
                    <table class="table table-hover" id="recent-messages-table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Subject</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="6" class="text-center py-4">
                                    <div class="spinner-border spinner-border-sm text-primary" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-4 mb-3">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="card-title m-0">Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('projects.create') }}" class="btn btn-primary">
                            <i class="bx bx-plus me-2"></i>New Project
                        </a>
                        <a href="{{ route('blog-posts.create') }}" class="btn btn-outline-primary">
                            <i class="bx bx-edit me-2"></i>New Blog Post
                        </a>
                        <a href="{{ route('skills.create') }}" class="btn btn-outline-secondary">
                            <i class="bx bx-code-alt me-2"></i>Add Skill
                        </a>
                        <a href="{{ route('services.create') }}" class="btn btn-outline-info">
                            <i class="bx bx-briefcase me-2"></i>New Service
                        </a>
                        <hr>
                        <a href="{{ route('profile.edit') }}" class="btn btn-outline-warning">
                            <i class="bx bx-user me-2"></i>Edit Profile
                        </a>
                        <a href="{{ route('settings.index') }}" class="btn btn-outline-dark">
                            <i class="bx bx-cog me-2"></i>Settings
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
$(document).ready(function() {
    // Load dashboard data
    loadDashboardData();
    
    // Refresh data every 5 minutes
    setInterval(loadDashboardData, 300000);
    
    function loadDashboardData() {
        $.ajax({
            url: '{{ route('admin.dashboard') }}',
            type: 'GET',
            data: { ajax: true },
            success: function(response) {
                if (response.success) {
                    updateDashboardStats(response.data);
                    updateRecentItems(response.data);
                    updateChart(response.data.analytics);
                }
            },
            error: function(xhr) {
                console.error('Failed to load dashboard data:', xhr.responseText);
            }
        });
    }
    
    function updateDashboardStats(data) {
        const stats = data.stats;
        
        // Update main counters
        $('#total-projects').text(stats.projects.total || 0);
        $('#total-posts').text(stats.blog.total || 0);
        $('#total-views').text(formatNumber(stats.projects.views + stats.blog.views));
        $('#new-messages').text(stats.contacts.new || 0);
        $('#featured-items').text((stats.projects.featured || 0) + (stats.blog.featured || 0));
        $('#active-skills').text(stats.skills.total || 0);
        
        // Update monthly views
        $('#this-month-views').text(formatNumber(data.analytics?.this_month_views || 0));
        $('#last-month-views').text(formatNumber(data.analytics?.last_month_views || 0));
    }
    
    function updateRecentItems(data) {
        const recent = data.recent;
        
        // Recent Projects
        let projectsHtml = '';
        if (recent.projects && recent.projects.length > 0) {
            recent.projects.forEach(function(project) {
                const statusColor = project.status === 'published' ? 'success' : (project.status === 'draft' ? 'warning' : 'secondary');
                projectsHtml += `
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="d-flex align-items-center">
                            <div class="avatar avatar-xs me-2">
                                <span class="avatar-initial rounded-circle bg-label-${statusColor}">
                                    <i class="bx bx-folder"></i>
                                </span>
                            </div>
                            <div>
                                <h6 class="mb-0">${project.title.length > 20 ? project.title.substring(0, 20) + '...' : project.title}</h6>
                                <small class="text-muted">${project.category?.name || 'No Category'}</small>
                            </div>
                        </div>
                        <div class="text-end">
                            <span class="badge bg-${statusColor}">${project.status}</span>
                        </div>
                    </div>
                `;
            });
        } else {
            projectsHtml = '<div class="text-center text-muted py-4">No projects yet</div>';
        }
        $('#recent-projects').html(projectsHtml);
        
        // Recent Posts
        let postsHtml = '';
        if (recent.posts && recent.posts.length > 0) {
            recent.posts.forEach(function(post) {
                const statusColor = post.status === 'published' ? 'success' : (post.status === 'draft' ? 'warning' : 'secondary');
                postsHtml += `
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="d-flex align-items-center">
                            <div class="avatar avatar-xs me-2">
                                <span class="avatar-initial rounded-circle bg-label-${statusColor}">
                                    <i class="bx bx-edit"></i>
                                </span>
                            </div>
                            <div>
                                <h6 class="mb-0">${post.title.length > 20 ? post.title.substring(0, 20) + '...' : post.title}</h6>
                                <small class="text-muted">${post.author?.name || 'Unknown'}</small>
                            </div>
                        </div>
                        <div class="text-end">
                            <span class="badge bg-${statusColor}">${post.status}</span>
                        </div>
                    </div>
                `;
            });
        } else {
            postsHtml = '<div class="text-center text-muted py-4">No posts yet</div>';
        }
        $('#recent-posts').html(postsHtml);
        
        // Recent Messages
        updateRecentMessages(recent.contacts);
    }
    
    function updateRecentMessages(contacts) {
        let tbody = '';
        if (contacts && contacts.length > 0) {
            contacts.forEach(function(contact) {
                const statusColor = contact.status === 'new' ? 'primary' : (contact.status === 'read' ? 'info' : 'success');
                tbody += `
                    <tr>
                        <td>${contact.name}</td>
                        <td>${contact.email}</td>
                        <td>${contact.subject ? (contact.subject.length > 20 ? contact.subject.substring(0, 20) + '...' : contact.subject) : 'No Subject'}</td>
                        <td><span class="badge bg-${statusColor}">${contact.status}</span></td>
                        <td>${formatDate(contact.created_at)}</td>
                        <td>
                            <a href="/contacts/${contact.id}" class="btn btn-xs btn-outline-primary">
                                <i class="bx bx-show"></i>
                            </a>
                        </td>
                    </tr>
                `;
            });
        } else {
            tbody = '<tr><td colspan="6" class="text-center text-muted py-4">No messages yet</td></tr>';
        }
        $('#recent-messages-table tbody').html(tbody);
    }
    
    function updateChart(analyticsData) {
        // Simple line chart for page views
        const ctx = document.getElementById('pageViewsChart');
        if (ctx) {
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                    datasets: [{
                        label: 'Page Views',
                        data: analyticsData?.weekly_views || [12, 19, 3, 5, 2, 3, 9],
                        borderColor: 'rgb(75, 192, 192)',
                        backgroundColor: 'rgba(75, 192, 192, 0.1)',
                        tension: 0.1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0
                            }
                        }
                    }
                }
            });
        }
    }
    
    function formatNumber(num) {
        if (num >= 1000000) {
            return (num / 1000000).toFixed(1) + 'M';
        } else if (num >= 1000) {
            return (num / 1000).toFixed(1) + 'K';
        }
        return num.toString();
    }
    
    function formatDate(dateString) {
        const date = new Date(dateString);
        return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
    }
});
</script>
@endpush