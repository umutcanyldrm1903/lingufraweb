@extends('frontend.layouts.master')
@section('meta_title', __('Login') . ' || ' . $setting->app_name)
@section('contents')
    @php
        $intendedUrl = session()->get('url.intended');
        $forcedRole = request('role');

        $defaultRole = old('expected_role');
        if (!in_array($defaultRole, ['student', 'instructor'], true)) {
            $defaultRole = in_array($forcedRole, ['instructor', 'teacher'], true) ? 'instructor' : 'student';

            if ($defaultRole === 'student' && $intendedUrl && Str::contains($intendedUrl, '/instructor')) {
                $defaultRole = 'instructor';
            }
        }
    @endphp

    <section class="ce-login">
        <div class="ce-login__lang">
            @if (count(allLanguages()?->where('status', 1)) > 1)
                <form action="{{ route('set-language') }}" class="ce-login__lang-form">
                    <select name="code" class="ce-login__lang-select" onchange="this.form.submit()">
                        @foreach (allLanguages()?->where('status', 1) as $language)
                            <option value="{{ $language->code }}"
                                {{ getSessionLanguage() == $language->code ? 'selected' : '' }}>
                                {{ $language->name }}
                            </option>
                        @endforeach
                    </select>
                </form>
            @endif
        </div>

        <div class="ce-login__wrap">
            <div class="ce-login__switch {{ $defaultRole === 'instructor' ? 'is-instructor' : '' }}"
                role="tablist" aria-label="{{ __('Login type') }}">
                <span class="ce-login__switch-indicator" aria-hidden="true"></span>
                <a href="{{ route('login', ['role' => 'student']) }}"
                    class="ce-login__switch-btn {{ $defaultRole === 'student' ? 'is-active' : '' }}"
                    data-role="student" role="tab"
                    aria-selected="{{ $defaultRole === 'student' ? 'true' : 'false' }}">
                    {{ __('Student') }}
                </a>
                <a href="{{ route('login', ['role' => 'instructor']) }}"
                    class="ce-login__switch-btn {{ $defaultRole === 'instructor' ? 'is-active' : '' }}"
                    data-role="instructor" role="tab"
                    aria-selected="{{ $defaultRole === 'instructor' ? 'true' : 'false' }}">
                    {{ __('Instructor') }}
                </a>
            </div>

            <div class="ce-login__card">
                <div class="ce-login__kicker">{{ __('WELCOME BACK') }}</div>
                <h1 class="ce-login__title"
                    data-student-title="{{ __('Student Login') }}"
                    data-instructor-title="{{ __('Instructor Login') }}">
                    {{ $defaultRole === 'instructor' ? __('Instructor Login') : __('Student Login') }}
                </h1>

                <form method="POST" action="{{ route('user-login') }}" class="ce-login__form">
                    @csrf
                    <input type="hidden" name="expected_role" id="ce-expected-role" value="{{ $defaultRole }}">

                    <div class="ce-login__field">
                        <input id="email" type="email" placeholder="{{ __('Email Address') }}" value="{{ old('email') }}"
                            name="email" autocomplete="email" required>
                        <x-frontend.validation-error name="email" />
                    </div>
                    <div class="ce-login__field">
                        <input id="password" type="password" placeholder="{{ __('Password') }}" name="password"
                            autocomplete="current-password" required>
                    </div>

                    <div class="ce-login__meta">
                        <a href="{{ route('password.request') }}" class="ce-login__forgot">{{ __('Forgot your password?') }}</a>
                    </div>

                    @if (Cache::get('setting')->recaptcha_status === 'active')
                        <div class="ce-login__recaptcha">
                            <div class="g-recaptcha overflow-hidden"
                                data-sitekey="{{ Cache::get('setting')->recaptcha_site_key }}"></div>
                            <x-frontend.validation-error name="g-recaptcha-response" />
                        </div>
                    @endif

                    <button type="submit" class="ce-login__submit">{{ __('Continue') }}</button>

                </form>

                <div class="ce-login__bottom">
                    <span>{{ __('New here?') }}</span>
                    <a
                        href="{{ route('register', ['role' => $defaultRole === 'instructor' ? 'instructor' : 'student']) }}"
                        id="ce-register-link"
                        data-student-link="{{ route('register', ['role' => 'student']) }}"
                        data-instructor-link="{{ route('register', ['role' => 'instructor']) }}"
                    >
                        {{ __('Sign Up Here') }}
                    </a>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('styles')
    <style>
        /* Hide global header/footer only on login page */
        header,
        footer,
        .scroll__top {
            display: none !important;
        }

        main.main-area {
            padding: 0 !important;
        }

        .ce-login {
            --ce-primary: var(--tg-theme-primary);
            --ce-accent: var(--tg-theme-secondary);
            background: #ffffff;
            min-height: 100vh;
            display: grid;
            place-items: center;
            padding: 30px 16px;
            position: relative;
        }

        .ce-login__lang {
            position: absolute;
            top: 18px;
            right: 18px;
        }

        .ce-login__lang-select {
            border: 1px solid #e5e7eb;
            background: #fff;
            border-radius: 10px;
            padding: 8px 10px;
            font-weight: 800;
            color: #111827;
        }

        .ce-login__wrap {
            width: 100%;
            max-width: 420px;
        }

        .ce-login__switch {
            position: relative;
            display: flex;
            align-items: center;
            background: #fff;
            border: 1px solid #eef2f7;
            border-radius: 999px;
            padding: 6px;
            box-shadow: 0 18px 50px rgba(0, 0, 0, 0.10);
            width: 100%;
            max-width: 340px;
            margin: 0 auto 26px;
        }

        .ce-login__switch-indicator {
            position: absolute;
            top: 6px;
            bottom: 6px;
            left: 6px;
            width: calc(50% - 6px);
            border-radius: 999px;
            background: var(--ce-accent);
            box-shadow: 0 14px 30px rgba(0, 0, 0, 0.14);
            transition: transform .18s ease;
        }

        .ce-login__switch.is-instructor .ce-login__switch-indicator {
            transform: translateX(100%);
        }

        .ce-login__switch-btn {
            position: relative;
            z-index: 2;
            flex: 1;
            border: 0;
            background: transparent;
            border-radius: 999px;
            padding: 10px 14px;
            font-weight: 1000;
            color: #111827;
            cursor: pointer;
        }

        .ce-login__card {
            background: #fff;
            border-radius: 18px;
            box-shadow: 0 22px 70px rgba(0, 0, 0, 0.10);
            padding: 26px 22px 22px;
        }

        .ce-login__kicker {
            font-size: 12px;
            letter-spacing: .8px;
            font-weight: 900;
            color: #6b7280;
        }

        .ce-login__title {
            font-size: 24px;
            font-weight: 1000;
            color: #111827;
            margin: 8px 0 18px;
        }

        .ce-login__field input {
            width: 100%;
            border-radius: 12px;
            border: 1px solid #e5e7eb;
            padding: 13px 14px;
            font-weight: 700;
            color: #111827;
            background: #fff;
            outline: none;
        }

        .ce-login__field input:focus {
            border-color: rgba(246, 161, 5, 0.65);
            box-shadow: 0 0 0 4px rgba(246, 161, 5, 0.18);
        }

        .ce-login__field+ .ce-login__field {
            margin-top: 14px;
        }

        .ce-login__meta {
            display: flex;
            justify-content: flex-end;
            margin-top: 10px;
        }

        .ce-login__forgot {
            color: var(--ce-primary);
            font-weight: 900;
            text-decoration: underline;
        }

        .ce-login__recaptcha {
            margin-top: 14px;
        }

        .ce-login__submit {
            width: 100%;
            margin-top: 18px;
            border: 0;
            border-radius: 14px;
            padding: 14px 16px;
            background: var(--ce-accent);
            color: #fff;
            font-weight: 1000;
            box-shadow: 0 18px 46px rgba(0, 0, 0, 0.14);
            transition: transform .18s ease, box-shadow .18s ease, opacity .18s ease;
        }

        .ce-login__submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 22px 60px rgba(0, 0, 0, 0.18);
            opacity: .95;
        }


        .ce-login__bottom {
            margin-top: 18px;
            text-align: center;
            font-weight: 800;
            color: #6b7280;
        }

        .ce-login__bottom a {
            font-weight: 1000;
            color: #111827;
            text-decoration: underline;
            margin-left: 6px;
        }

        @media (max-width: 575px) {
            .ce-login__card {
                padding: 22px 18px 18px;
            }

            .ce-login__title {
                font-size: 22px;
            }
        }
    </style>
@endpush

@push('scripts')
    <script>
        (function() {
            const switchEl = document.querySelector('.ce-login__switch');
            const hiddenRole = document.getElementById('ce-expected-role');
            const titleEl = document.querySelector('.ce-login__title');
            const registerLink = document.getElementById('ce-register-link');
            if (!switchEl || !hiddenRole || !titleEl) return;

            const studentTitle = titleEl.getAttribute('data-student-title') || titleEl.textContent;
            const instructorTitle = titleEl.getAttribute('data-instructor-title') || titleEl.textContent;

            function setRole(role) {
                const normalizedRole = role === 'instructor' ? 'instructor' : 'student';
                hiddenRole.value = normalizedRole;
                titleEl.textContent = normalizedRole === 'instructor' ? instructorTitle : studentTitle;
                switchEl.classList.toggle('is-instructor', normalizedRole === 'instructor');
                if (window.history && window.history.replaceState) {
                    const url = new URL(window.location.href);
                    url.searchParams.set('role', normalizedRole);
                    window.history.replaceState({}, '', url.toString());
                }

                switchEl.querySelectorAll('.ce-login__switch-btn').forEach(btn => {
                    const isActive = btn.getAttribute('data-role') === normalizedRole;
                    btn.classList.toggle('is-active', isActive);
                    btn.setAttribute('aria-selected', isActive ? 'true' : 'false');
                });

                if (registerLink) {
                    const nextHref = normalizedRole === 'instructor'
                        ? registerLink.dataset.instructorLink
                        : registerLink.dataset.studentLink;
                    if (nextHref) registerLink.setAttribute('href', nextHref);
                }
            }

            switchEl.querySelectorAll('.ce-login__switch-btn').forEach(btn => {
                btn.addEventListener('click', function(event) {
                    event.preventDefault();
                    setRole(this.getAttribute('data-role'));
                });
            });

            setRole(hiddenRole.value);
        })();
    </script>
@endpush
