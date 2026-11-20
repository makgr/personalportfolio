@php
    // Breadcrumb from the route name, e.g. "admin.training.edit" -> Training / Edit
    $routeName = request()->route()?->getName() ?? '';
    $prefix = str_starts_with($routeName, 'admin.') ? 'admin.' : '';
    $segments = explode('.', substr($routeName, strlen($prefix)));
    $section = $routeName === 'home' ? null : ($segments[0] ?? null);
    $sectionLabel = $section ? \Illuminate\Support\Str::headline($section === 'contacts' ? 'messages' : $section) : null;
    $sectionUrl = $section && \Illuminate\Support\Facades\Route::has($prefix . $section . '.index')
        ? route($prefix . $section . '.index')
        : null;
    $actionLabel = ['create' => 'Add new', 'edit' => 'Edit', 'show' => 'Details'][$segments[1] ?? ''] ?? null;
    $avatar = $profile->avatar_url ? asset($profile->avatar_url) : asset('assets/img/avatars/1.png');
@endphp
<!DOCTYPE html>
<html lang="en" class="light-style layout-menu-fixed" dir="ltr" data-theme="theme-default"
    data-assets-path="{{ asset('assets') }}" data-template="vertical-menu-template-free">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <title>{{ trim($__env->yieldContent('title')) ?: ($sectionLabel ?? 'Dashboard') }} | {{ $profile->full_name ?: config('app.name') }}</title>

    <meta name="description" content="personal portfolio" />
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="icon" type="image/x-icon" href="{{ asset('assets/img/favicon/favicon.ico') }}" />

    <!-- Restore the collapsed sidebar before first paint -->
    <script>
        try {
            if (localStorage.getItem('ob-menu-rail') === '1') document.documentElement.classList.add('menu-rail');
        } catch (e) {}
    </script>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet" />

    <!-- Icons (Boxicons classes stay in the views; obsidian.js draws Lucide inside them) -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/fonts/boxicons.css') }}" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />

    <!-- Core CSS -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/core.css') }}" class="template-customizer-core-css" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/theme-default.css') }}"
        class="template-customizer-theme-css" />
    <link rel="stylesheet" href="{{ asset('assets/css/demo.css') }}" />

    <!-- Vendors CSS -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/apex-charts/apex-charts.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/toastr/toastr.min.css') }}" />
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">

    <!-- Obsidian Light design system -->
    <link rel="stylesheet" href="{{ asset('assets/css/obsidian.css') }}?v=4" />

    <!-- Page CSS -->
    @yield('css')
</head>

<body>
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            @include('layouts.sidebar')

            <div class="layout-page">
                <!-- Top bar -->
                <nav class="layout-navbar container-xxl navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme"
                    id="layout-navbar">
                    <div class="layout-menu-toggle navbar-nav align-items-xl-center me-2 d-xl-none">
                        <button type="button" class="ob-icon-btn" aria-label="Open menu">
                            <i class="bx bx-menu"></i>
                        </button>
                    </div>

                    <div class="d-flex align-items-center w-100 gap-3" id="navbar-collapse">
                        <div class="ob-page-heading flex-grow-1">
                            <p class="ob-page-title">{{ trim($__env->yieldContent('title')) ?: ($actionLabel ? $actionLabel . ' · ' . $sectionLabel : ($sectionLabel ?? 'Dashboard')) }}</p>
                            <nav aria-label="Breadcrumb" @unless ($sectionLabel) hidden @endunless>
                                <ol class="ob-breadcrumb d-none d-sm-flex">
                                    <li><a href="{{ route('home') }}">Dashboard</a></li>
                                    @if ($sectionLabel)
                                        <li>
                                            @if ($sectionUrl && $actionLabel)
                                                <a href="{{ $sectionUrl }}">{{ $sectionLabel }}</a>
                                            @else
                                                <span @unless ($actionLabel) aria-current="page" @endunless>{{ $sectionLabel }}</span>
                                            @endif
                                        </li>
                                    @endif
                                    @if ($actionLabel)
                                        <li><span aria-current="page">{{ $actionLabel }}</span></li>
                                    @endif
                                </ol>
                            </nav>
                        </div>

                        <button type="button" class="ob-search-trigger d-none d-md-inline-flex" data-bs-toggle="modal"
                            data-bs-target="#obPalette">
                            <i class="bx bx-search"></i>
                            <span>Go to page…</span>
                            <kbd>Ctrl K</kbd>
                        </button>
                        <button type="button" class="ob-icon-btn d-md-none" data-bs-toggle="modal"
                            data-bs-target="#obPalette" aria-label="Go to page">
                            <i class="bx bx-search"></i>
                        </button>

                        <!-- Notifications: new contact messages -->
                        <div class="dropdown">
                            <button type="button" class="ob-icon-btn" data-bs-toggle="dropdown" aria-expanded="false"
                                aria-label="Notifications{{ $newMessagesCount ? ', ' . $newMessagesCount . ' new' : '' }}">
                                <i class="bx bx-ob-bell"></i>
                                @if ($newMessagesCount)
                                    <span class="ob-dot">{{ $newMessagesCount > 9 ? '9+' : $newMessagesCount }}</span>
                                @endif
                            </button>
                            <div class="dropdown-menu dropdown-menu-end ob-notify-menu">
                                <div class="ob-notify-head">
                                    <strong class="text-body">New messages</strong>
                                    <a href="{{ route('contacts.index') }}" class="small">View all</a>
                                </div>
                                @forelse ($newMessages as $message)
                                    <a class="ob-notify-item" href="{{ route('contacts.show', $message) }}">
                                        <span class="ob-avatar-initials">{{ strtoupper(mb_substr($message->name, 0, 1)) }}</span>
                                        <div class="min-w-0">
                                            <strong>{{ $message->name }}</strong>
                                            <span>{{ $message->subject ?: \Illuminate\Support\Str::limit($message->message, 60) }}</span>
                                            <time datetime="{{ $message->created_at->toIso8601String() }}">{{ $message->created_at->diffForHumans() }}</time>
                                        </div>
                                    </a>
                                @empty
                                    <div class="ob-empty py-4">
                                        <div class="ob-empty-icon"><i class="bx bx-envelope-open"></i></div>
                                        <p class="mb-0">You're all caught up. New contact messages show up here.</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>

                        <!-- Profile -->
                        <div class="dropdown">
                            <button type="button" class="ob-icon-btn p-0" data-bs-toggle="dropdown" aria-expanded="false"
                                aria-label="Account menu">
                                <img src="{{ $avatar }}" alt="" class="rounded-circle" width="34" height="34"
                                    style="object-fit: cover" />
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end" style="min-width: 15rem">
                                <li class="px-2 py-2 d-flex align-items-center gap-2">
                                    <img src="{{ $avatar }}" alt="" class="rounded-circle" width="36" height="36"
                                        style="object-fit: cover" />
                                    <div class="min-w-0">
                                        <div class="fw-semibold text-body text-truncate" style="color: var(--ob-heading) !important">
                                            {{ Auth::user()->name }}</div>
                                        <small class="text-muted text-truncate d-block">{{ Auth::user()->email }}</small>
                                    </div>
                                </li>
                                <li><div class="dropdown-divider"></div></li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('profile.edit') }}">
                                        <i class="bx bx-user"></i> My profile
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('settings.index') }}">
                                        <i class="bx bx-slider"></i> Settings
                                    </a>
                                </li>
                                <li><div class="dropdown-divider"></div></li>
                                <li>
                                    <a class="dropdown-item text-danger" href="{{ route('logout') }}"
                                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                        <i class="bx bx-power-off"></i> Log out
                                    </a>
                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </div>
                </nav>

                <div class="content-wrapper">
                    @yield('content')

                    <footer class="content-footer footer">
                        <div class="container-xxl d-flex flex-wrap justify-content-between py-3">
                            <div>
                                © {{ date('Y') }}, made with ❤️ by
                                <a href="https://makgr.com" target="_blank" rel="noopener" class="footer-link fw-semibold">Mohammad Ali Khan</a>
                                &amp;
                                <a href="https://www.facebook.com/mehedi.care" target="_blank" rel="noopener"
                                    class="footer-link fw-semibold">Md. Mehedi Hasan Khan</a>
                            </div>
                        </div>
                    </footer>

                    <div class="content-backdrop fade"></div>
                </div>
            </div>
        </div>

        <div class="layout-overlay layout-menu-toggle"></div>
    </div>

    <!-- Page search -->
    <div class="modal fade ob-palette" id="obPalette" tabindex="-1" aria-label="Go to page" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="ob-palette-input">
                    <i class="bx bx-search"></i>
                    <input type="text" placeholder="Go to page…" aria-label="Search admin pages" role="combobox"
                        aria-controls="obPaletteList" aria-expanded="true" autocomplete="off" />
                    <kbd class="d-none d-sm-inline">Esc</kbd>
                </div>
                <ul class="ob-palette-list" id="obPaletteList" role="listbox"></ul>
                <div class="ob-palette-empty" hidden>No admin page matches that.</div>
            </div>
        </div>
    </div>

    <!-- Confirmation dialog (window.obConfirm) -->
    <div class="modal fade ob-confirm" id="obConfirm" tabindex="-1" aria-labelledby="obConfirmTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body pt-4">
                    <div class="ob-confirm-icon"><i class="bx bx-trash"></i></div>
                    <h5 class="modal-title mb-2" id="obConfirmTitle">Are you sure?</h5>
                    <p class="ob-confirm-message"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" data-ob-confirm>Delete</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Core JS -->
    <script src="{{ asset('assets/vendor/js/helpers.js') }}"></script>
    <script src="{{ asset('assets/js/config.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/jquery/jquery.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/popper/popper.js') }}"></script>
    <script src="{{ asset('assets/vendor/js/bootstrap.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>
    <script src="{{ asset('assets/vendor/js/menu.js') }}"></script>

    <!-- Vendors JS -->
    <script src="{{ asset('assets/vendor/libs/apex-charts/apexcharts.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/lucide/lucide.min.js') }}"></script>

    <script src="{{ asset('assets/js/main.js') }}"></script>
    <script src="{{ asset('assets/js/toastr/toastr.min.js') }}"></script>

    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>

    <script src="{{ asset('assets/js/admin-ajax.js') }}?v=7"></script>
    <script src="{{ asset('assets/js/obsidian.js') }}?v=1"></script>

    @stack('scripts')
</body>

</html>
