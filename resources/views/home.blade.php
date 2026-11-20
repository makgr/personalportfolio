@extends('layouts.app')

@section('title', 'Dashboard')

@php
    $localNow = now(config('app.display_timezone'));
    $hour = $localNow->hour;
    $greeting = $hour < 12 ? 'Good morning' : ($hour < 18 ? 'Good afternoon' : 'Good evening');
    $firstName = \Illuminate\Support\Str::before(trim(Auth::user()->name), ' ');
@endphp

@section('css')
    <style>
        .dash-skel-row {
            display: flex;
            align-items: center;
            gap: 0.875rem;
            padding: 0.875rem 1.5rem;
        }

        .dash-skel-row + .dash-skel-row {
            border-top: 1px solid var(--ob-border-soft);
        }

        .dash-fade {
            animation: dash-fade 0.35s var(--ob-ease);
        }

        @keyframes dash-fade {
            from {
                opacity: 0;
                transform: translateY(4px);
            }
        }

        @media (prefers-reduced-motion: reduce) {
            .dash-fade,
            .ob-skeleton {
                animation: none;
            }
        }
    </style>
@endsection

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">

        <!-- Greeting -->
        <div class="d-flex flex-wrap align-items-end justify-content-between gap-3 mb-4 ob-greeting">
            <div>
                <h4>{{ $greeting }}, {{ $firstName }}</h4>
                <p>{{ $localNow->format('l, j F Y') }}</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('blog-posts.create') }}" class="btn btn-soft"><i class="bx bx-edit"></i> New post</a>
                <a href="{{ route('projects.create') }}" class="btn btn-primary"><i class="bx bx-plus"></i> New project</a>
            </div>
        </div>

        <!-- Stats -->
        <div class="row row-cols-2 row-cols-md-3 row-cols-xl-5 g-3 mb-4" data-slot="stats" aria-busy="true">
            @for ($i = 0; $i < 5; $i++)
                <div class="col">
                    <div class="card h-100">
                        <div class="ob-stat">
                            <div class="ob-stat-top">
                                <span class="ob-skeleton" style="width: 40%; height: 0.875rem"></span>
                                <span class="ob-skeleton" style="width: 2rem; height: 2rem; border-radius: 8px"></span>
                            </div>
                            <div class="ob-skeleton my-2" style="width: 35%; height: 1.75rem"></div>
                            <div class="ob-skeleton" style="width: 60%; height: 0.75rem"></div>
                        </div>
                    </div>
                </div>
            @endfor
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
                        <div id="activityChart" style="min-height: 280px" aria-busy="true">
                            <div class="ob-skeleton" style="height: 280px; border-radius: var(--ob-radius-sm)"></div>
                        </div>
                        <div class="ob-empty pt-0" id="activityEmpty" hidden>
                            <p class="mb-0">No visits or messages in the last 30 days yet. The chart fills in as people find your portfolio.</p>
                        </div>
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
                            {{-- Tones match the stat cards: project violet, post rose, skill sky --}}
                            <a href="{{ route('projects.create') }}" class="ob-tone-violet"><i class="bx bx-folder"></i> Add project</a>
                            <a href="{{ route('blog-posts.create') }}" class="ob-tone-rose"><i class="bx bx-edit"></i> Write post</a>
                            <a href="{{ route('skills.create') }}" class="ob-tone-sky"><i class="bx bx-code-alt"></i> Add skill</a>
                            <a href="{{ route('services.create') }}" class="ob-tone-emerald"><i class="bx bx-briefcase"></i> Add service</a>
                            <a href="{{ route('experiences.create') }}" class="ob-tone-amber"><i class="bx bx-time"></i> Add experience</a>
                            <a href="{{ route('admin.testimonials.create') }}" class="ob-tone-violet"><i class="bx bx-chat"></i> Add testimonial</a>
                            <a href="{{ route('profile.edit') }}" class="ob-tone-zinc"><i class="bx bx-user"></i> Edit profile</a>
                            <a href="{{ route('settings.index') }}" class="ob-tone-zinc"><i class="bx bx-slider"></i> Site settings</a>
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
                    <div data-slot="projects" aria-busy="true">
                        @for ($i = 0; $i < 5; $i++)
                            <div class="dash-skel-row">
                                <span class="ob-skeleton" style="width: 2.5rem; height: 2.5rem; border-radius: 8px"></span>
                                <span class="ob-skeleton flex-grow-1" style="height: 0.875rem; max-width: 14rem"></span>
                                <span class="ob-skeleton ms-auto" style="width: 4rem; height: 1.25rem"></span>
                                <span class="ob-skeleton" style="width: 4.5rem; height: 0.75rem"></span>
                            </div>
                        @endfor
                    </div>
                </div>
            </div>

            <!-- Recent messages -->
            <div class="col-xl-5">
                <div class="card h-100">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <h5 class="card-title">Recent messages</h5>
                        <a href="{{ route('contacts.index') }}" class="small fw-medium">View all</a>
                    </div>
                    <div data-slot="messages" aria-busy="true">
                        @for ($i = 0; $i < 5; $i++)
                            <div class="dash-skel-row">
                                <span class="ob-skeleton" style="width: 2.25rem; height: 2.25rem; border-radius: 50%"></span>
                                <div class="flex-grow-1">
                                    <div class="ob-skeleton mb-2" style="width: 40%; height: 0.875rem"></div>
                                    <div class="ob-skeleton" style="width: 75%; height: 0.75rem"></div>
                                </div>
                                <span class="ob-skeleton" style="width: 2.5rem; height: 0.75rem"></span>
                            </div>
                        @endfor
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        (function () {
            const fill = (slot, html) => {
                const el = document.querySelector(`[data-slot="${slot}"]`);
                el.innerHTML = html;
                el.removeAttribute('aria-busy');
                el.classList.add('dash-fade');
            };

            const errorHtml = '<div class="ob-empty"><p class="mb-2">Could not load this section.</p>' +
                '<button type="button" class="btn btn-secondary btn-sm" data-retry>Try again</button></div>';

            const showError = () => {
                // Stats row is a grid of .col items, so wrap the message in a full-width column
                fill('stats', `<div class="col-12"><div class="card">${errorHtml}</div></div>`);
                fill('projects', errorHtml);
                fill('messages', errorHtml);
                const chartEl = document.getElementById('activityChart');
                chartEl.innerHTML = errorHtml;
                chartEl.removeAttribute('aria-busy');
                document.querySelectorAll('[data-retry]').forEach((b) => b.addEventListener('click', () => location.reload()));
            };

            const renderChart = (data) => {
                const el = document.getElementById('activityChart');
                el.innerHTML = '';
                el.removeAttribute('aria-busy');
                el.classList.add('dash-fade');

                const total = data.visits.reduce((a, b) => a + b, 0) + data.messages.reduce((a, b) => a + b, 0);
                document.getElementById('activityEmpty').hidden = total > 0;

                if (typeof ApexCharts === 'undefined') return;

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
            };

            fetch(@json(route('home.data')), {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                credentials: 'same-origin',
            })
                .then((res) => {
                    if (!res.ok) throw new Error(`HTTP ${res.status}`);
                    return res.json();
                })
                .then((data) => {
                    fill('stats', data.stats);
                    fill('projects', data.projects);
                    fill('messages', data.messages);
                    renderChart(data.chart);
                })
                .catch(showError);
        })();
    </script>
@endpush
