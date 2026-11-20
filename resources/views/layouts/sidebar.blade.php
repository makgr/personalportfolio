<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    @php
        $brandName = $profile->full_name ?: config('app.name');
        $initials = collect(preg_split('/\s+/', trim($brandName)))->filter()->map(fn ($w) => mb_substr($w, 0, 1))->take(2)->implode('');
    @endphp
    <div class="app-brand demo">
        <a href="{{ route('home') }}" class="app-brand-link gap-2 min-w-0">
            <span class="ob-brand-mark" aria-hidden="true">{{ strtoupper($initials) }}</span>
            <span class="app-brand-text menu-text text-truncate">{{ $brandName }}</span>
        </a>

        <button type="button" class="ob-rail-toggle" aria-label="Collapse sidebar" aria-pressed="false">
            <i class="bx bx-ob-close"></i>
        </button>

        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-block d-xl-none"
            aria-label="Close menu">
            <i class="bx bx-chevron-left bx-sm align-middle"></i>
        </a>
    </div>

    <div class="menu-inner-shadow"></div>

    <ul class="menu-inner py-1">
        <!-- Dashboard -->
        <li class="menu-item {{ request()->routeIs('home') ? 'active' : '' }}">
            <a href="{{ route('home') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-home-circle"></i>
                <div data-i18n="Analytics">Dashboard</div>
            </a>
        </li>

        <!-- Portfolio Management -->
        <li class="menu-header small text-uppercase"><span class="menu-header-text">Portfolio</span></li>
        
        <!-- Projects -->
        <li class="menu-item {{ request()->routeIs('projects.*') ? 'active open' : '' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-folder"></i>
                <div data-i18n="Projects">Projects</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item {{ request()->routeIs('projects.index') ? 'active' : '' }}">
                    <a href="{{ route('projects.index') }}" class="menu-link">
                        <div data-i18n="All Projects">All Projects</div>
                    </a>
                </li>
                <li class="menu-item {{ request()->routeIs('projects.create') ? 'active' : '' }}">
                    <a href="{{ route('projects.create') }}" class="menu-link">
                        <div data-i18n="Add Project">Add Project</div>
                    </a>
                </li>
            </ul>
        </li>

        <!-- Blog Posts -->
        <li class="menu-item {{ request()->routeIs('blog-posts.*') ? 'active open' : '' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-edit"></i>
                <div data-i18n="Blog Posts">Blog Posts</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item {{ request()->routeIs('blog-posts.index') ? 'active' : '' }}">
                    <a href="{{ route('blog-posts.index') }}" class="menu-link">
                        <div data-i18n="All Posts">All Posts</div>
                    </a>
                </li>
                <li class="menu-item {{ request()->routeIs('blog-posts.create') ? 'active' : '' }}">
                    <a href="{{ route('blog-posts.create') }}" class="menu-link">
                        <div data-i18n="Add Post">Add Post</div>
                    </a>
                </li>
            </ul>
        </li>

        <!-- Skills -->
        <li class="menu-item {{ request()->routeIs('skills.*') ? 'active open' : '' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-code-alt"></i>
                <div data-i18n="Skills">Skills</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item {{ request()->routeIs('skills.index') ? 'active' : '' }}">
                    <a href="{{ route('skills.index') }}" class="menu-link">
                        <div data-i18n="All Skills">All Skills</div>
                    </a>
                </li>
                <li class="menu-item {{ request()->routeIs('skills.create') ? 'active' : '' }}">
                    <a href="{{ route('skills.create') }}" class="menu-link">
                        <div data-i18n="Add Skill">Add Skill</div>
                    </a>
                </li>
            </ul>
        </li>

        <!-- Services -->
        <li class="menu-item {{ request()->routeIs('services.*') ? 'active open' : '' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-briefcase"></i>
                <div data-i18n="Services">Services</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item {{ request()->routeIs('services.index') ? 'active' : '' }}">
                    <a href="{{ route('services.index') }}" class="menu-link">
                        <div data-i18n="All Services">All Services</div>
                    </a>
                </li>
                <li class="menu-item {{ request()->routeIs('services.create') ? 'active' : '' }}">
                    <a href="{{ route('services.create') }}" class="menu-link">
                        <div data-i18n="Add Service">Add Service</div>
                    </a>
                </li>
            </ul>
        </li>

        <!-- Experience -->
        <li class="menu-item {{ request()->routeIs('experiences.*') ? 'active open' : '' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-time"></i>
                <div data-i18n="Experience">Experience</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item {{ request()->routeIs('experiences.index') ? 'active' : '' }}">
                    <a href="{{ route('experiences.index') }}" class="menu-link">
                        <div data-i18n="All Experience">All Experience</div>
                    </a>
                </li>
                <li class="menu-item {{ request()->routeIs('experiences.create') ? 'active' : '' }}">
                    <a href="{{ route('experiences.create') }}" class="menu-link">
                        <div data-i18n="Add Experience">Add Experience</div>
                    </a>
                </li>
            </ul>
        </li>

        <!-- Education -->
        <li class="menu-item {{ request()->routeIs('education.*') ? 'active open' : '' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-book"></i>
                <div data-i18n="Education">Education</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item {{ request()->routeIs('education.index') ? 'active' : '' }}">
                    <a href="{{ route('education.index') }}" class="menu-link">
                        <div data-i18n="All Education">All Education</div>
                    </a>
                </li>
                <li class="menu-item {{ request()->routeIs('education.create') ? 'active' : '' }}">
                    <a href="{{ route('education.create') }}" class="menu-link">
                        <div data-i18n="Add Education">Add Education</div>
                    </a>
                </li>
            </ul>
        </li>

        <!-- Training -->
        <li class="menu-item {{ request()->routeIs('admin.training.*') ? 'active open' : '' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-book-open"></i>
                <div data-i18n="Training">Training</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item {{ request()->routeIs('admin.training.index') ? 'active' : '' }}">
                    <a href="{{ route('admin.training.index') }}" class="menu-link">
                        <div data-i18n="All Training">All Training</div>
                    </a>
                </li>
                <li class="menu-item {{ request()->routeIs('admin.training.create') ? 'active' : '' }}">
                    <a href="{{ route('admin.training.create') }}" class="menu-link">
                        <div data-i18n="Add Training">Add Training</div>
                    </a>
                </li>
            </ul>
        </li>

        <!-- Testimonials -->
        <li class="menu-item {{ request()->routeIs('admin.testimonials.*') ? 'active open' : '' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-chat"></i>
                <div data-i18n="Testimonials">Testimonials</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item {{ request()->routeIs('admin.testimonials.index') ? 'active' : '' }}">
                    <a href="{{ route('admin.testimonials.index') }}" class="menu-link">
                        <div data-i18n="All Testimonials">All Testimonials</div>
                    </a>
                </li>
                <li class="menu-item {{ request()->routeIs('admin.testimonials.create') ? 'active' : '' }}">
                    <a href="{{ route('admin.testimonials.create') }}" class="menu-link">
                        <div data-i18n="Add Testimonial">Add Testimonial</div>
                    </a>
                </li>
            </ul>
        </li>

        <!-- Teams -->
        <li class="menu-item {{ request()->routeIs('teams.*') ? 'active open' : '' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-group"></i>
                <div data-i18n="Teams">Teams</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item {{ request()->routeIs('teams.index') ? 'active' : '' }}">
                    <a href="{{ route('teams.index') }}" class="menu-link">
                        <div data-i18n="All Team Members">All Team Members</div>
                    </a>
                </li>
                <li class="menu-item {{ request()->routeIs('teams.create') ? 'active' : '' }}">
                    <a href="{{ route('teams.create') }}" class="menu-link">
                        <div data-i18n="Add Team Member">Add Team Member</div>
                    </a>
                </li>
            </ul>
        </li>

        <!-- Categories -->
        <li class="menu-item {{ request()->routeIs('categories.*') ? 'active' : '' }}">
            <a href="{{ route('categories.index') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-category"></i>
                <div data-i18n="Categories">Categories</div>
            </a>
        </li>

        <!-- Contact Messages -->
        <li class="menu-item {{ request()->routeIs('contacts.*') ? 'active' : '' }}">
            <a href="{{ route('contacts.index') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-envelope"></i>
                <div data-i18n="Messages">Messages</div>
            </a>
        </li>

        <!-- Newsletter Subscribers -->
        <li class="menu-item {{ request()->routeIs('admin.newsletter-subscribers.*') ? 'active' : '' }}">
            <a href="{{ route('admin.newsletter-subscribers.index') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-mail-send"></i>
                <div data-i18n="Newsletter">Newsletter</div>
            </a>
        </li>

        <!-- Settings -->
        <li class="menu-header small text-uppercase"><span class="menu-header-text">Account</span></li>
        
        <li class="menu-item {{ request()->routeIs('profile.*') ? 'active' : '' }}">
            <a href="{{ route('profile.edit') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-user"></i>
                <div data-i18n="Profile">Profile</div>
            </a>
        </li>

        <li class="menu-item {{ request()->routeIs('settings.*') ? 'active' : '' }}">
            <a href="{{ route('settings.index') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-slider"></i>
                <div data-i18n="Settings">Settings</div>
            </a>
        </li>
    </ul>
</aside>
