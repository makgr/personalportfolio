/*
 * Obsidian Light admin behaviour:
 *  - draws Lucide icons inside existing Boxicons <i> tags (views stay untouched)
 *  - collapsible sidebar rail
 *  - Ctrl/Cmd+K page search built from the sidebar links
 *  - obConfirm(): promise-based confirmation dialog
 *  - row action menus in scrolling tables open above the table instead of being clipped
 */
(function () {
    'use strict';

    /* ---------- Icons: Boxicons class -> Lucide icon ---------- */
    var ICONS = {
        'arrow-back': 'ArrowLeft', 'edit': 'Pencil', 'edit-alt': 'PencilLine', 'trash': 'Trash2',
        'check': 'Check', 'show': 'Eye', 'hide': 'EyeOff', 'check-circle': 'CircleCheck', 'star': 'Star',
        'x': 'X', 'briefcase': 'BriefcaseBusiness', 'plus': 'Plus', 'envelope': 'Mail', 'envelope-open': 'MailOpen',
        'book': 'GraduationCap', 'book-open': 'BookOpen', 'code': 'Code', 'code-alt': 'CodeXml',
        'code-block': 'SquareCode', 'time': 'Clock', 'time-five': 'Clock', 'user': 'UserRound', 'save': 'Save',
        'dots-vertical-rounded': 'EllipsisVertical', 'link-external': 'ExternalLink', 'info-circle': 'Info',
        'folder': 'FolderKanban', 'archive': 'Archive', 'x-circle': 'CircleX', 'minus': 'Minus',
        'github': 'Github', 'group': 'Users', 'category': 'Tags', 'buildings': 'Building2',
        'linkedin': 'Linkedin', 'upload': 'Upload', 'phone': 'Phone', 'chat': 'Quote', 'twitter': 'Twitter',
        'zap': 'Zap', 'trophy': 'Trophy', 'search': 'Search', 'refresh': 'RefreshCw', 'map': 'MapPin',
        'mail-send': 'Send', 'loader-alt': 'LoaderCircle', 'link': 'Link', 'image-add': 'ImagePlus',
        'download': 'Download', 'cog': 'Settings', 'certificate': 'Award', 'calendar': 'Calendar',
        'calendar-week': 'CalendarDays', 'calendar-alt': 'CalendarRange', 'youtube': 'Youtube',
        'instagram': 'Instagram', 'facebook': 'Facebook', 'wrench': 'Wrench', 'video': 'Video',
        'task': 'ListChecks', 'slider': 'SlidersHorizontal', 'share-alt': 'Share2', 'reply': 'Reply',
        'power-off': 'LogOut', 'note': 'StickyNote', 'menu': 'Menu', 'like': 'ThumbsUp',
        'home-circle': 'LayoutDashboard', 'file': 'FileText', 'chevron-left': 'ChevronLeft',
        'badge-check': 'BadgeCheck',
        // layout-only names (not real Boxicons)
        'ob-bell': 'Bell', 'ob-open': 'PanelLeftOpen', 'ob-close': 'PanelLeftClose',
        'ob-chart': 'ChartLine', 'ob-inbox': 'Inbox', 'ob-sparkles': 'Sparkles'
    };
    // Boxicons utility classes that are not icon names
    var MODIFIERS = /^(xs|sm|md|lg|spin|spin-hover|tada|tada-hover|flashing|burst|fade-left|fade-right|fade-up|fade-down|flip-horizontal|flip-vertical|rotate-90|rotate-180|rotate-270|border|border-circle|pull-left|pull-right)$/;

    function iconName(el) {
        var classes = el.classList;
        for (var i = 0; i < classes.length; i++) {
            var m = /^bx[sl]?-(.+)$/.exec(classes[i]);
            if (m && !MODIFIERS.test(m[1])) return m[1];
        }
        return null;
    }

    function draw(el) {
        if (!window.lucide) return;
        var name = iconName(el);
        var lucideName = name && ICONS[name];
        if (el.getAttribute('data-ob-icon') === (lucideName || '') && (!lucideName || el.firstElementChild)) return;
        var current = el.querySelector(':scope > svg.lucide');
        if (current) current.remove();
        if (!lucideName || !window.lucide.icons[lucideName]) {
            // No mapping: let the original Boxicons glyph show
            el.setAttribute('data-ob-icon', '');
            el.classList.remove('ob-lucide');
            return;
        }
        var svg = window.lucide.createElement(window.lucide.icons[lucideName]);
        svg.setAttribute('class', 'lucide');
        svg.setAttribute('aria-hidden', 'true');
        el.appendChild(svg);
        el.setAttribute('data-ob-icon', lucideName);
        if (!el.classList.contains('ob-lucide')) el.classList.add('ob-lucide');
    }

    var SELECTOR = 'i.bx, i.bxs, i.bxl';

    function drawAll(root) {
        if (root.matches && root.matches(SELECTOR)) draw(root);
        if (root.querySelectorAll) root.querySelectorAll(SELECTOR).forEach(draw);
    }

    function watchIcons() {
        drawAll(document.body);
        new MutationObserver(function (records) {
            records.forEach(function (r) {
                if (r.type === 'attributes') {
                    if (r.target.matches(SELECTOR)) draw(r.target);
                    return;
                }
                r.addedNodes.forEach(function (n) {
                    if (n.nodeType === 1) drawAll(n);
                });
            });
        }).observe(document.body, { childList: true, subtree: true, attributes: true, attributeFilter: ['class'] });
    }

    /* ---------- Sidebar rail ---------- */
    function setupRail() {
        var btn = document.querySelector('.ob-rail-toggle');
        if (!btn) return;
        var sync = function () {
            var rail = document.documentElement.classList.contains('menu-rail');
            btn.setAttribute('aria-pressed', rail ? 'true' : 'false');
            btn.setAttribute('aria-label', rail ? 'Expand sidebar' : 'Collapse sidebar');
            btn.querySelector('i').className = 'bx ' + (rail ? 'bx-ob-open' : 'bx-ob-close');
        };
        sync();
        btn.addEventListener('click', function () {
            var rail = document.documentElement.classList.toggle('menu-rail');
            try { localStorage.setItem('ob-menu-rail', rail ? '1' : '0'); } catch (e) { /* storage blocked */ }
            sync();
        });
        // Rail shows only icons: give each top-level link its label as a tooltip
        document.querySelectorAll('#layout-menu .menu-inner > .menu-item > .menu-link').forEach(function (a) {
            var label = a.querySelector('div');
            if (label) a.setAttribute('title', label.textContent.trim());
        });
    }

    /* ---------- Page search (Ctrl/Cmd + K) ---------- */
    function setupPalette() {
        var modalEl = document.getElementById('obPalette');
        if (!modalEl || !window.bootstrap) return;
        var modal = bootstrap.Modal.getOrCreateInstance(modalEl);
        var input = modalEl.querySelector('input');
        var list = modalEl.querySelector('.ob-palette-list');
        var empty = modalEl.querySelector('.ob-palette-empty');

        var items = [];
        document.querySelectorAll('#layout-menu a.menu-link[href]').forEach(function (a) {
            var href = a.getAttribute('href');
            if (!href || href.indexOf('javascript') === 0) return;
            var parent = a.closest('.menu-sub');
            var group = parent ? parent.parentElement.querySelector(':scope > .menu-link div') : null;
            items.push({
                href: href,
                label: a.textContent.trim(),
                group: group ? group.textContent.trim() : ''
            });
        });

        var active = 0;
        var shown = [];

        function render() {
            var q = input.value.trim().toLowerCase();
            shown = items.filter(function (it) {
                return !q || (it.label + ' ' + it.group).toLowerCase().indexOf(q) !== -1;
            });
            active = Math.min(active, Math.max(shown.length - 1, 0));
            list.innerHTML = '';
            shown.forEach(function (it, i) {
                var li = document.createElement('li');
                var a = document.createElement('a');
                a.href = it.href;
                a.id = 'ob-pal-' + i;
                a.setAttribute('role', 'option');
                a.setAttribute('aria-selected', i === active ? 'true' : 'false');
                a.textContent = it.label;
                if (it.group) {
                    var s = document.createElement('small');
                    s.textContent = it.group;
                    a.appendChild(s);
                }
                a.addEventListener('mousemove', function () { if (active !== i) { active = i; mark(); } });
                li.appendChild(a);
                list.appendChild(li);
            });
            empty.hidden = shown.length > 0;
            input.setAttribute('aria-activedescendant', shown.length ? 'ob-pal-' + active : '');
        }

        function mark() {
            list.querySelectorAll('a').forEach(function (a, i) {
                a.setAttribute('aria-selected', i === active ? 'true' : 'false');
                if (i === active) a.scrollIntoView({ block: 'nearest' });
            });
            input.setAttribute('aria-activedescendant', 'ob-pal-' + active);
        }

        input.addEventListener('input', function () { active = 0; render(); });
        input.addEventListener('keydown', function (e) {
            if (e.key === 'ArrowDown') { e.preventDefault(); active = Math.min(active + 1, shown.length - 1); mark(); }
            if (e.key === 'ArrowUp') { e.preventDefault(); active = Math.max(active - 1, 0); mark(); }
            if (e.key === 'Enter' && shown[active]) { e.preventDefault(); window.location.href = shown[active].href; }
        });
        modalEl.addEventListener('shown.bs.modal', function () { input.focus(); });
        modalEl.addEventListener('show.bs.modal', function () { input.value = ''; active = 0; render(); });

        document.addEventListener('keydown', function (e) {
            if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'k') {
                e.preventDefault();
                modal.show();
            }
        });
    }

    /* ---------- Confirmation dialog ---------- */
    window.obConfirm = function (message, options) {
        options = options || {};
        var el = document.getElementById('obConfirm');
        if (!el || !window.bootstrap) return Promise.resolve(window.confirm(message));
        var destructive = options.tone ? options.tone === 'danger' : /delete|remove|archive|cannot be undone/i.test(message);
        el.querySelector('.modal-title').textContent = options.title || (destructive ? 'Are you sure?' : 'Please confirm');
        el.querySelector('.ob-confirm-message').textContent = message;
        var icon = el.querySelector('.ob-confirm-icon');
        icon.classList.toggle('is-primary', !destructive);
        icon.querySelector('i').className = 'bx ' + (destructive ? 'bx-trash' : 'bx-info-circle');
        var ok = el.querySelector('[data-ob-confirm]');
        ok.className = 'btn ' + (destructive ? 'btn-danger' : 'btn-primary');
        ok.textContent = options.confirmText || (destructive ? (/archive/i.test(message) ? 'Archive' : 'Delete') : 'Confirm');

        return new Promise(function (resolve) {
            var modal = bootstrap.Modal.getOrCreateInstance(el);
            var result = false;
            var onOk = function () { result = true; modal.hide(); };
            ok.addEventListener('click', onOk);
            el.addEventListener('hidden.bs.modal', function done() {
                ok.removeEventListener('click', onOk);
                el.removeEventListener('hidden.bs.modal', done);
                resolve(result);
            });
            el.addEventListener('shown.bs.modal', function focus() {
                el.removeEventListener('shown.bs.modal', focus);
                el.querySelector('[data-bs-dismiss]').focus();
            });
            modal.show();
        });
    };

    /* ---------- Row action menus inside scrolling tables ---------- */
    // .table-responsive (and theme wrappers) clip anything that overflows, so a row's menu was cut off.
    // While open, the menu lives in <body>; Popper still anchors it to the button. It goes back on close.
    function setupTableDropdowns() {
        if (typeof bootstrap === 'undefined' || !bootstrap.Dropdown) return;
        var TABLE = '.table-responsive, .card-datatable, .dataTables_wrapper';
        var homes = new WeakMap();
        var toggles = new WeakMap();

        document.addEventListener('show.bs.dropdown', function (e) {
            var toggle = e.target;
            if (!toggle.closest || !toggle.closest(TABLE)) return;
            var menu = toggle.parentNode.querySelector('.dropdown-menu');
            if (!menu) return;
            homes.set(menu, toggle.parentNode);
            toggles.set(menu, toggle);
            menu.setAttribute('data-ob-floating', '');
            document.body.appendChild(menu);
        });

        // Bootstrap finds the toggle as the menu's sibling, which no longer holds in <body>; handle keys here
        document.addEventListener('keydown', function (e) {
            var menu = e.target.closest && e.target.closest('.dropdown-menu[data-ob-floating]');
            if (!menu || ['Escape', 'ArrowDown', 'ArrowUp'].indexOf(e.key) === -1) return;
            e.preventDefault();
            e.stopPropagation();
            var toggle = toggles.get(menu);
            if (e.key === 'Escape') {
                if (toggle) {
                    bootstrap.Dropdown.getOrCreateInstance(toggle).hide();
                    toggle.focus();
                }
                return;
            }
            var items = Array.prototype.slice.call(menu.querySelectorAll('.dropdown-item:not(.disabled):not(:disabled)'));
            var next = items.indexOf(document.activeElement) + (e.key === 'ArrowDown' ? 1 : -1);
            if (items.length) items[(next + items.length) % items.length].focus();
        }, true);

        document.addEventListener('hidden.bs.dropdown', function (e) {
            var menu = document.querySelector('body > .dropdown-menu[data-ob-floating]:not(.show)');
            var home = menu && homes.get(menu);
            if (!menu) return;
            menu.removeAttribute('data-ob-floating');
            if (home && home.isConnected) {
                home.appendChild(menu);
            } else {
                menu.remove();
            }
        });

        // Close the floating menu when the table scrolls or redraws, so it never points at a moved/removed row
        function closeFloating() {
            document.querySelectorAll('.table-responsive [data-bs-toggle="dropdown"][aria-expanded="true"]').forEach(function (t) {
                bootstrap.Dropdown.getOrCreateInstance(t).hide();
            });
            document.querySelectorAll('body > .dropdown-menu[data-ob-floating]').forEach(function (m) {
                if (!homes.get(m) || !homes.get(m).isConnected) m.remove();
            });
        }
        document.addEventListener('scroll', function (e) {
            if (e.target.classList && e.target.classList.contains('table-responsive')) closeFloating();
        }, true);
        if (window.jQuery) {
            window.jQuery(document).on('preDraw.dt', closeFloating);
        }
    }

    function init() {
        setupRail();
        setupPalette();
        watchIcons();
        setupTableDropdowns();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
