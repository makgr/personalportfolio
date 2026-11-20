@php
    // Sign-in must still render if the profiles table is empty or not migrated yet
    $profile = rescue(fn () => \App\Models\Profile::where('is_active', true)->first(), null, false);
    $ownerName = $profile->full_name ?? config('app.name');
    $initials = collect(preg_split('/\s+/', trim($ownerName)))->filter()->map(fn ($w) => mb_substr($w, 0, 1))->take(2)->implode('');
@endphp
<!DOCTYPE html>
<html lang="en" class="light-style" dir="ltr" data-theme="theme-default">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Sign in | {{ $ownerName }}</title>

    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}" />
    <link rel="alternate icon" href="{{ asset('assets/img/favicon/favicon.ico') }}" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/fonts/boxicons.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/core.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/theme-default.css') }}" />
    <link rel="stylesheet" href="@versioned('assets/css/obsidian.css')" />

    <style>
        body {
            background: var(--ob-surface);
        }

        .login {
            display: grid;
            grid-template-columns: minmax(0, 1fr) minmax(0, 1fr);
            min-height: 100vh;
        }

        /* Left: name panel */
        .login-aside {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: clamp(2rem, 4vw, 3.5rem);
            background: var(--ob-bg);
            border-right: 1px solid var(--ob-border);
        }

        .login-aside .ob-brand-mark {
            width: 2.75rem;
            height: 2.75rem;
            font-size: 1.0625rem;
            border-radius: var(--ob-radius);
        }

        .login-owner {
            margin: 0;
            font-size: clamp(1.75rem, 3.4vw, 3.5rem);
            font-weight: 600;
            line-height: 1.1;
            letter-spacing: -0.035em;
            color: var(--ob-heading);
            white-space: nowrap;
        }

        .login-role {
            margin: 1rem 0 0;
            font-size: 1rem;
            color: var(--ob-body);
            max-width: 26rem;
        }

        .login-foot {
            font-size: 0.8125rem;
            color: var(--ob-muted);
        }

        /* Right: form */
        .login-panel {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 1rem;
        }

        .login-form {
            width: 100%;
            max-width: 22rem;
        }

        .login-form h1 {
            font-size: 1.5rem;
            letter-spacing: -0.02em;
            margin-bottom: 0.375rem;
        }

        .login-form .lead-in {
            color: var(--ob-body);
            font-size: 0.9375rem;
            margin-bottom: 2rem;
        }

        .login-form .form-control {
            padding: 0.6875rem 0.875rem;
            font-size: 0.9375rem;
        }

        .password-wrap {
            position: relative;
        }

        .password-wrap .form-control {
            padding-right: 3rem;
        }

        .password-toggle {
            position: absolute;
            top: 0;
            right: 0;
            height: 100%;
            width: 3rem;
            border: 0;
            background: none;
            color: var(--ob-muted);
            font-size: 1.125rem;
            border-radius: 0 var(--ob-radius-sm) var(--ob-radius-sm) 0;
        }

        .password-toggle:hover {
            color: var(--ob-heading);
        }

        /* Edge adds its own reveal/clear icons; we already have a show/hide toggle */
        #password::-ms-reveal,
        #password::-ms-clear {
            display: none;
        }

        .login-form .btn-primary {
            padding: 0.6875rem;
            font-size: 0.9375rem;
        }

        .login-mobile-brand {
            display: none;
        }

        /* Aside: drifting glow, dot grid and a spotlight that follows the cursor */
        .login-aside {
            position: relative;
            overflow: hidden;
            isolation: isolate;
        }

        .login-aside::before {
            content: "";
            position: absolute;
            inset: 0;
            z-index: -1;
            background-image: radial-gradient(var(--ob-border) 1px, transparent 1px);
            background-size: 22px 22px;
            mask-image: radial-gradient(ellipse at 30% 60%, #000 20%, transparent 75%);
        }

        .login-aside::after {
            content: "";
            position: absolute;
            inset: 0;
            z-index: -1;
            background: radial-gradient(420px circle at var(--mx, 30%) var(--my, 60%), var(--ob-primary-ring), transparent 70%);
        }

        .login-blob {
            position: absolute;
            z-index: -2;
            border-radius: 50%;
            filter: blur(70px);
            opacity: 0.45;
            animation: blob-drift 18s ease-in-out infinite alternate;
        }

        .login-blob.one {
            width: 22rem;
            height: 22rem;
            background: var(--ob-secondary);
            top: -6rem;
            right: -6rem;
        }

        .login-blob.two {
            width: 18rem;
            height: 18rem;
            background: var(--ob-primary);
            bottom: -5rem;
            left: -4rem;
            opacity: 0.25;
            animation-delay: -9s;
        }

        @keyframes blob-drift {
            to {
                transform: translate(-3rem, 4rem) scale(1.15);
            }
        }

        .login-owner {
            background: linear-gradient(100deg, var(--ob-heading) 40%, var(--ob-primary) 60%, var(--ob-heading) 80%);
            background-size: 250% 100%;
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            animation: name-shine 6s var(--ob-ease) infinite;
        }

        @keyframes name-shine {
            0%, 40% {
                background-position: 100% 0;
            }
            100% {
                background-position: 0 0;
            }
        }

        .login-aside .ob-brand-mark {
            transition: transform 0.4s var(--ob-ease);
        }

        .login-aside .ob-brand-mark:hover {
            transform: rotate(-8deg) scale(1.08);
        }

        /* Staggered entrance */
        /* backwards fill: hidden during the delay, but hover transforms still work afterwards */
        .reveal {
            animation: reveal-up 0.6s var(--ob-ease) backwards;
            animation-delay: calc(var(--i, 0) * 70ms);
        }

        @keyframes reveal-up {
            from {
                opacity: 0;
                transform: translateY(12px);
            }
        }

        /* Inputs with leading icons */
        .field {
            position: relative;
        }

        .field > .field-icon {
            position: absolute;
            left: 0.875rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--ob-muted);
            font-size: 1.125rem;
            pointer-events: none;
            transition: color 0.2s;
        }

        .field .form-control {
            padding-left: 2.625rem;
            transition: border-color 0.2s, box-shadow 0.2s, transform 0.2s var(--ob-ease);
        }

        .field .form-control:focus {
            transform: translateY(-1px);
        }

        .field:focus-within > .field-icon {
            color: var(--ob-primary);
        }

        .caps-hint {
            display: flex;
            align-items: center;
            gap: 0.375rem;
            margin-top: 0.5rem;
            font-size: 0.8125rem;
            color: #b45309;
        }

        .login-form .btn-primary {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            transition: transform 0.2s var(--ob-ease), box-shadow 0.2s;
        }

        .login-form .btn-primary:hover:not(:disabled) {
            transform: translateY(-1px);
            box-shadow: 0 8px 20px -6px var(--ob-primary);
        }

        .login-form .btn-primary .bx {
            transition: transform 0.2s var(--ob-ease);
        }

        .login-form .btn-primary:hover .bx {
            transform: translateX(4px);
        }

        .login-form .btn-primary.is-loading .bx {
            display: none;
        }

        .shake {
            animation: shake 0.45s;
        }

        @keyframes shake {
            20%, 60% {
                transform: translateX(-6px);
            }
            40%, 80% {
                transform: translateX(6px);
            }
        }

        @media (prefers-reduced-motion: reduce) {
            *,
            *::before,
            *::after {
                animation: none !important;
                transition: none !important;
            }
        }

        @media (max-width: 767.98px) {
            .login {
                grid-template-columns: 1fr;
            }

            .login-aside {
                display: none;
            }

            .login-panel {
                align-items: flex-start;
                padding-top: 3rem;
            }

            .login-mobile-brand {
                display: flex;
                align-items: center;
                gap: 0.625rem;
                margin-bottom: 2.5rem;
                font-weight: 600;
                color: var(--ob-heading);
            }
        }
    </style>
</head>

<body>
    <main class="login">
        <section class="login-aside" aria-hidden="true">
            <span class="login-blob one"></span>
            <span class="login-blob two"></span>
            <span class="ob-brand-mark reveal">{{ strtoupper($initials) }}</span>
            <div>
                <p class="login-owner reveal" style="--i:1">{{ $ownerName }}</p>
                <p class="login-role reveal" style="--i:2">Manage the projects, writing and messages behind your portfolio.</p>
            </div>
            <p class="login-foot mb-0 reveal" style="--i:3">Portfolio admin</p>
        </section>

        <section class="login-panel">
            <form id="formAuthentication" class="login-form @if ($errors->any()) shake @endif" method="POST"
                action="{{ route('login') }}">
                @csrf
                <div class="login-mobile-brand">
                    <span class="ob-brand-mark">{{ strtoupper($initials) }}</span>
                    {{ $ownerName }}
                </div>

                <h1 class="reveal" style="--i:1">Sign in</h1>
                <p class="lead-in reveal" style="--i:2">Use your admin email and password.</p>

                <div class="mb-3 reveal" style="--i:3">
                    <label for="email" class="form-label">Email</label>
                    <div class="field">
                        <i class="bx bx-envelope field-icon" aria-hidden="true"></i>
                        <input type="email" id="email" name="email"
                            class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}"
                            required autocomplete="email" autofocus placeholder="you@example.com"
                            @error('email') aria-describedby="email-error" @enderror />
                    </div>
                    @error('email')
                        <div id="email-error" class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3 reveal" style="--i:4">
                    <label for="password" class="form-label">Password</label>
                    <div class="password-wrap field">
                        <i class="bx bx-lock-alt field-icon" aria-hidden="true"></i>
                        <input type="password" id="password" name="password"
                            class="form-control @error('password') is-invalid @enderror" required
                            autocomplete="current-password"
                            @error('password') aria-describedby="password-error" @enderror />
                        <button type="button" class="password-toggle" aria-label="Show password"
                            aria-controls="password">
                            <i class="bx bx-show" aria-hidden="true"></i>
                        </button>
                    </div>
                    <div class="caps-hint" id="caps-hint" role="status" hidden>
                        <i class="bx bx-error-circle" aria-hidden="true"></i> Caps Lock is on
                    </div>
                    @error('password')
                        <div id="password-error" class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-check mb-4 reveal" style="--i:5">
                    <input class="form-check-input" type="checkbox" id="remember" name="remember"
                        {{ old('remember') ? 'checked' : '' }} />
                    <label class="form-check-label" for="remember">Keep me signed in</label>
                </div>

                <button class="btn btn-primary w-100 loginbtn reveal" style="--i:6" type="submit">
                    Sign in <i class="bx bx-right-arrow-alt" aria-hidden="true"></i>
                </button>
            </form>
        </section>
    </main>

    <script src="{{ asset('assets/vendor/libs/lucide/lucide.min.js') }}"></script>
    <script src="@versioned('assets/js/obsidian.js')"></script>
    <script>
        const toggle = document.querySelector('.password-toggle');
        const password = document.getElementById('password');
        toggle.addEventListener('click', () => {
            const show = password.type === 'password';
            password.type = show ? 'text' : 'password';
            toggle.setAttribute('aria-label', show ? 'Hide password' : 'Show password');
            toggle.querySelector('i').className = show ? 'bx bx-hide' : 'bx bx-show';
        });

        const capsHint = document.getElementById('caps-hint');
        ['keydown', 'keyup'].forEach((type) => password.addEventListener(type, (e) => {
            capsHint.hidden = !e.getModifierState('CapsLock');
        }));
        password.addEventListener('blur', () => capsHint.hidden = true);

        const aside = document.querySelector('.login-aside');
        aside.addEventListener('pointermove', (e) => {
            const r = aside.getBoundingClientRect();
            aside.style.setProperty('--mx', `${e.clientX - r.left}px`);
            aside.style.setProperty('--my', `${e.clientY - r.top}px`);
        });

        document.getElementById('formAuthentication').addEventListener('submit', () => {
            const btn = document.querySelector('.loginbtn');
            btn.disabled = true;
            btn.classList.add('is-loading');
        });
    </script>
</body>

</html>
