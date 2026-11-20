@extends('layouts.app')

@section('title', 'Dashboard')

@php
    $hour = now()->hour;
    $greeting = $hour < 12 ? 'Good morning' : ($hour < 18 ? 'Good afternoon' : 'Good evening');
    $firstName = \Illuminate\Support\Str::before(trim(Auth::user()->name), ' ');
    $statusBadge = [
        'published' => 'bg-label-success',
        'draft' => 'bg-label-secondary',
        'archived' => 'bg-label-warning',
    ];
    $cards = [
        ['label' => 'Projects', 'icon' => 'bx-folder', 'value' => $stats['projects'],
            'meta' => $stats['projectsPublished'] . ' published', 'url' => route('projects.index')],
        ['label' => 'Skills', 'icon' => 'bx-code-alt', 'value' => $stats['skills'],
            'meta' => 'Shown on your portfolio', 'url' => route('skills.index')],
        ['label' => 'Messages', 'icon' => 'bx-envelope', 'value' => $stats['messages'],
            'meta' => $stats['messagesNew'] ? $stats['messagesNew'] . ' new' : 'No new messages', 'url' => route('contacts.index')],
        ['label' => 'Blog posts', 'icon' => 'bx-edit', 'value' => $stats['blogPosts'],
            'meta' => $stats['blogPostsPublished'] . ' published', 'url' => route('blog-posts.index')],
        ['label' => 'Visitors', 'icon' => 'bx-ob-chart', 'value' => $stats['visitors'],
            'meta' => 'Unique, last 30 days', 'url' => null],
    ];
@endphp

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">

        <!-- Greeting -->
        <div class="d-flex flex-wrap align-items-end justify-content-between gap-3 mb-4 ob-greeting">
            <div>
                <h4>{{ $greeting }}, {{ $firstName }}</h4>
                <p>{{ now()->format('l, j F Y') }}</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('blog-posts.create') }}" class="btn btn-secondary"><i class="bx bx-edit"></i> New post</a>
                <a href="{{ route('projects.create') }}" class="btn btn-primary"><i class="bx bx-plus"></i> New project</a>
            </div>
        </div>

        <!-- Stats -->
        <div class="row row-cols-2 row-cols-md-3 row-cols-xl-5 g-3 mb-4">
            @foreach ($cards as $card)
                <div class="col">
                    @if ($card['url'])
                        <a href="{{ $card['url'] }}" class="ob-stat-link">
                    @endif
                    <div class="card h-100">
                        <div class="ob-stat">
                            <div class="ob-stat-top">
                                <span>{{ $card['label'] }}</span>
                                <span class="ob-stat-icon"><i class="bx {{ $card['icon'] }}"></i></span>
                            </div>
                            <div class="ob-stat-value">{{ number_format($card['value']) }}</div>
                            <div class="ob-stat-meta">{{ $card['meta'] }}</div>
                        </div>
                    </div>
                    @if ($card['url'])
                        </a>
                    @endif
                </div>
            @endforeach
        </div>

        <div class="row g-3 mb-4">
            <!-- Activity chart -->
            <div class="col-xl-8">
                <div class="card h-100">
                    <div class="card-header d-flex flex-wrap align-items-center justify-content-between gap-2">
                        <div>
                            <h5 class="card-title">Activity</h5>
                            <small class="text-muted">Portfolio visits and contact messages per day</small>
                        </div>
                        <div class="ob-segmented" role="group" aria-label="Date range">
                            <button type="button" data-range="7" aria-pressed="false">7 days</button>
                            <button type="button" data-range="30" aria-pressed="true">30 days</button>
                        </div>
                    </div>
                    <div class="card-body pt-2">
                        <div id="activityChart" style="min-height: 280px"></div>
                        @if (!array_sum($chart['visits']->all()) && !array_sum($chart['messages']->all()))
                            <div class="ob-empty pt-0">
                                <p class="mb-0">No visits or messages in the last 30 days yet. The chart fills in as people find your portfolio.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Quick actions -->
            <div class="col-xl-4">
                <div class="card h-100">
                    <div class="card-header">
                        <h5 class="card-title">Quick actions</h5>
                    </div>
                    <div class="card-body">
                        <div class="ob-quick">
                            <a href="{{ route('projects.create') }}"><i class="bx bx-folder"></i> Add project</a>
                            <a href="{{ route('blog-posts.create') }}"><i class="bx bx-edit"></i> Write post</a>
                            <a href="{{ route('skills.create') }}"><i class="bx bx-code-alt"></i> Add skill</a>
                            <a href="{{ route('services.create') }}"><i class="bx bx-briefcase"></i> Add service</a>
                            <a href="{{ route('experiences.create') }}"><i class="bx bx-time"></i> Add experience</a>
                            <a href="{{ route('admin.testimonials.create') }}"><i class="bx bx-chat"></i> Add testimonial</a>
                            <a href="{{ route('profile.edit') }}"><i class="bx bx-user"></i> Edit profile</a>
                            <a href="{{ route('settings.index') }}"><i class="bx bx-slider"></i> Site settings</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3">
            <!-- Recent projects -->
            <div class="col-xl-7">
                <div class="card h-100">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <h5 class="card-title">Recent projects</h5>
                        <a href="{{ route('projects.index') }}" class="small fw-medium">View all</a>
                    </div>
                    @if ($recentProjects->isEmpty())
                        <div class="ob-empty">
                            <div class="ob-empty-icon"><i class="bx bx-folder"></i></div>
                            <h6>No projects yet</h6>
                            <p>Add your first project so it shows up on your portfolio.</p>
                            <a href="{{ route('projects.create') }}" class="btn btn-primary btn-sm"><i class="bx bx-plus"></i> Add project</a>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table align-middle">
                                <thead>
                                    <tr>
                                        <th>Project</th>
                                        <th>Category</th>
                                        <th>Status</th>
                                        <th class="text-end">Added</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($recentProjects as $project)
                                        <tr>
                                            <td>
                                                <a href="{{ route('projects.show', $project) }}" class="d-flex align-items-center gap-3 text-body">
                                                    @if ($project->featured_image)
                                                        <img src="{{ asset($project->featured_image) }}" alt="" class="ob-thumb" loading="lazy">
                                                    @else
                                                        <span class="ob-thumb"></span>
                                                    @endif
                                                    <strong class="text-truncate" style="max-width: 16rem">{{ $project->title }}</strong>
                                                </a>
                                            </td>
                                            <td>{{ $project->category->name ?? '—' }}</td>
                                            <td>
                                                <span class="badge {{ $statusBadge[$project->status] ?? 'bg-label-secondary' }}">
                                                    {{ ucfirst($project->status) }}
                                                </span>
                                            </td>
                                            <td class="text-end text-muted small">{{ $project->created_at->format('j M Y') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Recent messages -->
            <div class="col-xl-5">
                <div class="card h-100">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <h5 class="card-title">Recent messages</h5>
                        <a href="{{ route('contacts.index') }}" class="small fw-medium">View all</a>
                    </div>
                    @forelse ($recentMessages as $message)
                        <a href="{{ route('contacts.show', $message) }}" class="ob-list-row">
                            <span class="ob-avatar-initials">{{ strtoupper(mb_substr($message->name, 0, 1)) }}</span>
                            <div class="ob-list-main">
                                <strong>
                                    {{ $message->name }}
                                    @if ($message->status === 'new')
                                        <span class="badge bg-label-primary ms-1">New</span>
                                    @endif
                                </strong>
                                <span>{{ $message->subject ?: \Illuminate\Support\Str::limit($message->message, 70) }}</span>
                            </div>
                            <time datetime="{{ $message->created_at->toIso8601String() }}">{{ $message->created_at->diffForHumans(null, true) }}</time>
                        </a>
                    @empty
                        <div class="ob-empty">
                            <div class="ob-empty-icon"><i class="bx bx-envelope-open"></i></div>
                            <h6>No messages yet</h6>
                            <p>Messages sent through your portfolio's contact form land here.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        (function () {
            const data = @json($chart);
            const el = document.getElementById('activityChart');
            if (!el || typeof ApexCharts === 'undefined') return;

            const fmt = (d) => new Date(d + 'T00:00:00').toLocaleDateString(undefined, { day: 'numeric', month: 'short' });
            const slice = (n) => ({
                labels: data.labels.slice(-n).map(fmt),
                visits: data.visits.slice(-n),
                messages: data.messages.slice(-n),
            });

            const initial = slice(30);
            const chart = new ApexCharts(el, {
                chart: { type: 'area', height: 280, fontFamily: 'Inter, sans-serif', toolbar: { show: false }, zoom: { enabled: false } },
                series: [
                    { name: 'Visits', data: initial.visits },
                    { name: 'Messages', data: initial.messages },
                ],
                xaxis: {
                    categories: initial.labels,
                    tickAmount: 6,
                    axisBorder: { show: false },
                    axisTicks: { show: false },
                    labels: { style: { colors: '#a1a1aa', fontSize: '12px' }, rotate: 0 },
                },
                // max: keep a visible scale when every value is 0 (Apex otherwise prints "Infinity")
                yaxis: { min: 0, max: (m) => Math.max(Math.ceil(m), 4), tickAmount: 4, labels: { style: { colors: '#a1a1aa', fontSize: '12px' }, formatter: (v) => Math.round(v) } },
                colors: ['#8b5cf6', '#a1a1aa'],
                stroke: { curve: 'smooth', width: [2.5, 2], dashArray: [0, 4] },
                fill: { type: 'gradient', gradient: { shadeIntensity: 0, opacityFrom: 0.16, opacityTo: 0, stops: [0, 95] } },
                grid: { borderColor: '#f4f4f5', strokeDashArray: 0, padding: { left: 4, right: 8 } },
                dataLabels: { enabled: false },
                legend: { position: 'top', horizontalAlign: 'left', fontSize: '13px', labels: { colors: '#52525b' }, markers: { size: 5 } },
                tooltip: { shared: true, intersect: false },
                markers: { size: 0, hover: { size: 4 } },
            });
            chart.render();

            document.querySelectorAll('.ob-segmented [data-range]').forEach((btn) => {
                btn.addEventListener('click', () => {
                    document.querySelectorAll('.ob-segmented [data-range]').forEach((b) => b.setAttribute('aria-pressed', b === btn ? 'true' : 'false'));
                    const next = slice(Number(btn.dataset.range));
                    chart.updateOptions({ xaxis: { categories: next.labels } }, false, false);
                    chart.updateSeries([{ data: next.visits }, { data: next.messages }]);
                });
            });
        })();
    </script>
@endpush
