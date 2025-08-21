@extends('layouts.app')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <div class="col-lg-8 mb-4 order-0">
                <div class="card">
                    <div class="d-flex align-items-end row">
                        <div class="col-sm-7">
                            <div class="card-body">
                                <h5 class="card-title text-primary">Welcome back, {{ Auth::user()->name }}! 🎉</h5>
                                <p class="mb-4">
                                    Your portfolio is performing well with <span class="fw-bold">{{ $totalProjects ?? 0 }}</span> projects published.
                                    Keep creating amazing content!
                                </p>

                                <a href="{{ route('projects.create') }}" class="btn btn-sm btn-outline-primary">Add New Project</a>
                            </div>
                        </div>
                        <div class="col-sm-5 text-center text-sm-left">
                            <div class="card-body pb-0 px-0 px-md-4">
                                <img src="{{ asset('assets/img/illustrations/man-with-laptop-light.png') }}" height="140"
                                    alt="Portfolio Dashboard" data-app-dark-img="illustrations/man-with-laptop-dark.png"
                                    data-app-light-img="illustrations/man-with-laptop-light.png" />
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
                                            alt="projects" class="rounded" />
                                    </div>
                                </div>
                                <span class="fw-semibold d-block mb-1">Projects</span>
                                <h3 class="card-title mb-2">{{ $totalProjects ?? 0 }}</h3>
                                <small class="text-info fw-semibold">
                                    <a href="{{ route('projects.index') }}" class="text-decoration-none">
                                        <i class="bx bx-folder"></i> Manage
                                    </a>
                                </small>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-12 col-6 mb-4">
                        <div class="card">
                            <div class="card-body">
                                <div class="card-title d-flex align-items-start justify-content-between">
                                    <div class="avatar flex-shrink-0">
                                        <img src="{{ asset('assets/img/icons/unicons/wallet-info.png') }}" alt="Blog Posts"
                                            class="rounded" />
                                    </div>
                                </div>
                                <span>Blog Posts</span>
                                <h3 class="card-title text-nowrap mb-1">{{ $totalBlogs ?? 0 }}</h3>
                                <small class="text-info fw-semibold">
                                    <a href="{{ route('blog-posts.index') }}" class="text-decoration-none">
                                        <i class="bx bx-edit"></i> Manage
                                    </a>
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Portfolio Overview -->
            <div class="col-12 col-lg-8 order-2 order-md-3 order-lg-2 mb-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-header m-0 me-2 pb-3">Portfolio Overview</h5>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="d-flex align-items-center">
                                    <div class="avatar flex-shrink-0 me-3">
                                        <span class="avatar-initial rounded bg-label-primary">
                                            <i class="bx bx-briefcase"></i>
                                        </span>
                                    </div>
                                    <div>
                                        <h6 class="mb-0">Skills</h6>
                                        <small class="text-muted">{{ $totalSkills ?? 0 }} technical skills</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="d-flex align-items-center">
                                    <div class="avatar flex-shrink-0 me-3">
                                        <span class="avatar-initial rounded bg-label-success">
                                            <i class="bx bx-cog"></i>
                                        </span>
                                    </div>
                                    <div>
                                        <h6 class="mb-0">Services</h6>
                                        <small class="text-muted">{{ $totalServices ?? 0 }} services offered</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="d-flex align-items-center">
                                    <div class="avatar flex-shrink-0 me-3">
                                        <span class="avatar-initial rounded bg-label-info">
                                            <i class="bx bx-time"></i>
                                        </span>
                                    </div>
                                    <div>
                                        <h6 class="mb-0">Experience</h6>
                                        <small class="text-muted">{{ $totalExperience ?? 0 }} work experiences</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="d-flex align-items-center">
                                    <div class="avatar flex-shrink-0 me-3">
                                        <span class="avatar-initial rounded bg-label-warning">
                                            <i class="bx bx-envelope"></i>
                                        </span>
                                    </div>
                                    <div>
                                        <h6 class="mb-0">Messages</h6>
                                        <small class="text-muted">{{ $totalMessages ?? 0 }} contact messages</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mt-4">
                            <div class="d-flex justify-content-between align-items-center">
                                <h6 class="mb-0">Quick Actions</h6>
                            </div>
                            <div class="mt-3">
                                <a href="{{ route('projects.create') }}" class="btn btn-primary btn-sm me-2">
                                    <i class="bx bx-plus"></i> New Project
                                </a>
                                <a href="{{ route('blog-posts.create') }}" class="btn btn-info btn-sm me-2">
                                    <i class="bx bx-edit"></i> New Blog Post
                                </a>
                                <a href="{{ route('contacts.index') }}" class="btn btn-warning btn-sm">
                                    <i class="bx bx-envelope"></i> View Messages
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--/ Portfolio Overview -->
            <div class="col-12 col-md-8 col-lg-4 order-3 order-md-2">
                <div class="row">
                    <div class="col-12 mb-4">
                        <div class="card h-100">
                            <div class="card-header d-flex align-items-center justify-content-between">
                                <h5 class="card-title m-0">Recent Activity</h5>
                            </div>
                            <div class="card-body">
                                <ul class="p-0 m-0">
                                    <li class="d-flex mb-4 pb-1">
                                        <div class="avatar flex-shrink-0 me-3">
                                            <span class="avatar-initial rounded bg-label-primary">
                                                <i class="bx bx-folder"></i>
                                            </span>
                                        </div>
                                        <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                            <div class="me-2">
                                                <h6 class="mb-0">Projects</h6>
                                                <small class="text-muted">{{ $totalProjects }} total projects</small>
                                            </div>
                                            <div class="user-progress">
                                                <small class="fw-semibold">
                                                    <a href="{{ route('projects.index') }}" class="text-decoration-none">View</a>
                                                </small>
                                            </div>
                                        </div>
                                    </li>
                                    <li class="d-flex mb-4 pb-1">
                                        <div class="avatar flex-shrink-0 me-3">
                                            <span class="avatar-initial rounded bg-label-info">
                                                <i class="bx bx-edit"></i>
                                            </span>
                                        </div>
                                        <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                            <div class="me-2">
                                                <h6 class="mb-0">Blog Posts</h6>
                                                <small class="text-muted">{{ $totalBlogs }} published posts</small>
                                            </div>
                                            <div class="user-progress">
                                                <small class="fw-semibold">
                                                    <a href="{{ route('blog-posts.index') }}" class="text-decoration-none">View</a>
                                                </small>
                                            </div>
                                        </div>
                                    </li>
                                    <li class="d-flex mb-4 pb-1">
                                        <div class="avatar flex-shrink-0 me-3">
                                            <span class="avatar-initial rounded bg-label-warning">
                                                <i class="bx bx-envelope"></i>
                                            </span>
                                        </div>
                                        <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                            <div class="me-2">
                                                <h6 class="mb-0">Messages</h6>
                                                <small class="text-muted">{{ $totalMessages }} contact messages</small>
                                            </div>
                                            <div class="user-progress">
                                                <small class="fw-semibold">
                                                    <a href="{{ route('contacts.index') }}" class="text-decoration-none">View</a>
                                                </small>
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
