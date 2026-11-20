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

    <link rel="icon" type="image/x-icon" href="{{ asset('assets/img/favicon/favicon.ico') }}" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/fonts/boxicons.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/core.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/theme-default.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/obsidian.css') }}?v=4" />

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
            font-size: clamp(2.5rem, 4.6vw, 4rem);
            font-weight: 600;
            line-height: 1.04;
            letter-spacing: -0.035em;
            color: var(--ob-heading);
            max-width: 11ch;
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

        .login-form .btn-primary {
            padding: 0.6875rem;
            font-size: 0.9375rem;
        }

        .login-mobile-brand {
            display: none;
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
            <span class="ob-brand-mark">{{ strtoupper($initials) }}</span>
            <div>
                <p class="login-owner">{{ $ownerName }}</p>
                <p class="login-role">Manage the projects, writing and messages behind your portfolio.</p>
            </div>
            <p class="login-foot mb-0">Portfolio admin</p>
        </section>

        <section class="login-panel">
            <form id="formAuthentication" class="login-form" method="POST" action="{{ route('login') }}">
                @csrf
                <div class="login-mobile-brand">
                    <span class="ob-brand-mark">{{ strtoupper($initials) }}</span>
                    {{ $ownerName }}
                </div>

                <h1>Sign in</h1>
                <p class="lead-in">Use your admin email and password.</p>

                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" id="email" name="email"
                        class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}"
                        required autocomplete="email" autofocus
                        @error('email') aria-describedby="email-error" @enderror />
                    @error('email')
                        <div id="email-error" class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <div class="password-wrap">
                        <input type="password" id="password" name="password"
                            class="form-control @error('password') is-invalid @enderror" required
                            autocomplete="current-password"
                            @error('password') aria-describedby="password-error" @enderror />
                        <button type="button" class="password-toggle" aria-label="Show password"
                            aria-controls="password">
                            <i class="bx bx-show" aria-hidden="true"></i>
                        </button>
                    </div>
                    @error('password')
                        <div id="password-error" class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-check mb-4">
                    <input class="form-check-input" type="checkbox" id="remember" name="remember"
                        {{ old('remember') ? 'checked' : '' }} />
                    <label class="form-check-label" for="remember">Keep me signed in</label>
                </div>

                <button class="btn btn-primary w-100 loginbtn" type="submit">Sign in</button>
            </form>
        </section>
    </main>

    <script src="{{ asset('assets/vendor/libs/lucide/lucide.min.js') }}"></script>
    <script src="{{ asset('assets/js/obsidian.js') }}?v=1"></script>
    <script>
        const toggle = document.querySelector('.password-toggle');
        const password = document.getElementById('password');
        toggle.addEventListener('click', () => {
            const show = password.type === 'password';
            password.type = show ? 'text' : 'password';
            toggle.setAttribute('aria-label', show ? 'Hide password' : 'Show password');
            toggle.querySelector('i').className = show ? 'bx bx-hide' : 'bx bx-show';
        });

        document.getElementById('formAuthentication').addEventListener('submit', () => {
            const btn = document.querySelector('.loginbtn');
            btn.disabled = true;
            btn.classList.add('is-loading');
        });
    </script>
</body>

</html>
