@extends('frontend.layouts.master')
@section('meta_title', __('Register') . ' || ' . $setting->app_name)

@section('contents')
    @php
        $forcedRole = strtolower(trim((string) request('role')));
        $defaultRole = in_array($forcedRole, ['instructor', 'teacher'], true) ? 'instructor' : 'student';
        if ($forcedRole === '') {
            $defaultRole = old('role', $defaultRole);
        }
    @endphp
    <section class="ce-reg ce-reg--simple">
        <div class="ce-reg__lang">
            @if (count(allLanguages()?->where('status', 1)) > 1)
                <form action="{{ route('set-language') }}" class="ce-reg__lang-form">
                    <select name="code" class="ce-reg__lang-select" onchange="this.form.submit()">
                        @foreach (allLanguages()?->where('status', 1) as $language)
                            <option value="{{ $language->code }}" {{ getSessionLanguage() == $language->code ? 'selected' : '' }}>
                                {{ $language->name }}
                            </option>
                        @endforeach
                    </select>
                </form>
            @endif
        </div>

        <div class="ce-reg__layout">
            <div class="ce-reg__left">
                <h1 class="ce-reg__q">{{ __('Create your account') }}</h1>
                <p class="ce-reg__lead">{{ __('Fill in the short form to get started.') }}</p>
                <div class="ce-reg__brand">
                    <img src="{{ asset($setting?->logo) }}" alt="{{ $setting?->app_name }}">
                </div>
            </div>

            <div class="ce-reg__right">
                <form method="POST" action="{{ route('register', ['role' => $defaultRole]) }}" class="ce-reg__form">
                    @csrf
                    <input type="hidden" name="role" value="{{ $defaultRole }}">

                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="form-grp">
                                <label>{{ __('Full Name') }} <code>*</code></label>
                                <input type="text" class="form-control" name="full_name" value="{{ old('full_name') }}" placeholder="{{ __('Full Name') }}" autocomplete="name" required>
                                <x-frontend.validation-error name="full_name" />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-grp">
                                <label>{{ __('Phone (WhatsApp)') }}</label>
                                <input type="text" class="form-control" name="phone" value="{{ old('phone') }}" placeholder="{{ __('Phone Number') }}" autocomplete="tel" inputmode="tel">
                                <x-frontend.validation-error name="phone" />
                            </div>
                        </div>
                        @if ($defaultRole !== 'instructor')
                            <div class="col-md-12">
                                <div class="form-grp">
                                    <label>{{ __('Referral Code') }}</label>
                                    <input
                                        type="text"
                                        class="form-control"
                                        name="referral_code"
                                        value="{{ old('referral_code', request()->query('ref')) }}"
                                        placeholder="{{ __('Referral Code') }}"
                                        autocomplete="off"
                                    >
                                    <x-frontend.validation-error name="referral_code" />
                                </div>
                            </div>
                        @endif
                        <div class="col-md-12">
                            <div class="form-grp">
                                <label>{{ __('Email') }} <code>*</code></label>
                                <input type="email" class="form-control" name="email" value="{{ old('email') }}" placeholder="{{ __('Email') }}" autocomplete="email" required>
                                <x-frontend.validation-error name="email" />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-grp">
                                <label>{{ __('Password') }} <code>*</code></label>
                                <input type="password" class="form-control" name="password" placeholder="{{ __('Password') }}" autocomplete="new-password" required>
                                <x-frontend.validation-error name="password" />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-grp">
                                <label>{{ __('Confirm Password') }} <code>*</code></label>
                                <input type="password" class="form-control" name="password_confirmation" placeholder="{{ __('Confirm Password') }}" autocomplete="new-password" required>
                                <x-frontend.validation-error name="password_confirmation" />
                            </div>
                        </div>
                    </div>

                    <div class="ce-checks">
                        <label class="ce-check">
                            <input type="checkbox" name="accept_terms" value="1" @checked(old('accept_terms')) required>
                            <span>
                                {{ __('User Agreement') }} /
                                <a href="{{ url('page/terms-and-conditions') }}" target="_blank" rel="noopener">{{ __('Terms of Use') }}</a>
                                {{ __('and') }}
                                <a href="{{ url('page/privacy-policy') }}" target="_blank" rel="noopener">{{ __('Privacy Policy') }}</a>
                                {{ __('I agree.') }}
                            </span>
                        </label>
                        <x-frontend.validation-error name="accept_terms" />

                        <label class="ce-check">
                            <input type="checkbox" name="marketing_consent" value="1" @checked(old('marketing_consent'))>
                            <span>{{ __('I want to be informed about promotions, announcements, and special offers.') }}</span>
                        </label>
                    </div>

                    @if (Cache::get('setting')->recaptcha_status === 'active')
                        <div class="mt-3">
                            <div class="g-recaptcha overflow-hidden" data-sitekey="{{ Cache::get('setting')->recaptcha_site_key }}"></div>
                            <x-frontend.validation-error name="g-recaptcha-response" />
                        </div>
                    @endif

                    <div class="ce-actions">
                        <button type="submit" class="btn btn-two">{{ __('Sign Up') }}</button>
                    </div>

                    <div class="ce-login-link">
                        <span>{{ __('Already have an account?') }}</span>
                        <a href="{{ route('login') }}">{{ __('Log in') }}</a>
                    </div>
                </form>
            </div>
        </div>
    </section>
@endsection

@push('styles')
    <style>
        header,
        footer,
        .scroll__top {
            display: none !important;
        }

        main.main-area {
            padding: 0 !important;
        }

        .ce-reg {
            min-height: 100vh;
            position: relative;
            background: #fff;
        }

        .ce-reg__lang {
            position: absolute;
            top: 16px;
            right: 16px;
            z-index: 10;
        }

        .ce-reg__lang-select {
            border: 1px solid #e5e7eb;
            background: #fff;
            border-radius: 10px;
            padding: 8px 10px;
            font-weight: 800;
            color: #111827;
        }

        .ce-reg__layout {
            display: grid;
            grid-template-columns: minmax(0, 1fr) minmax(0, 1fr);
            min-height: 100vh;
        }

        .ce-reg__left {
            background: #0e5c93;
            padding: 90px 40px 30px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            gap: 18px;
        }

        .ce-reg__q {
            margin: 0;
            font-weight: 1000;
            color: #fff;
            font-size: 56px;
            line-height: 1.05;
        }

        .ce-reg__lead {
            margin: 0;
            color: #e2e8f0;
            font-weight: 700;
            font-size: 16px;
            max-width: 420px;
        }

        .ce-reg__brand {
            margin-top: auto;
            display: flex;
            justify-content: center;
        }

        .ce-reg__brand img {
            max-width: 170px;
            height: auto;
            filter: drop-shadow(0 12px 22px rgba(0, 0, 0, .18));
        }

        .ce-reg__right {
            padding: 90px 26px 26px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .ce-reg__form {
            width: 100%;
            max-width: 760px;
        }

        .ce-reg .form-grp label {
            display: block;
            margin-bottom: 6px;
            font-weight: 900;
            color: #111827;
        }

        .ce-reg .form-grp input,
        .ce-reg .form-grp select,
        .ce-reg .form-grp textarea {
            width: 100%;
            border-radius: 12px;
            border: 1px solid #e5e7eb;
            padding: 12px 14px;
            font-weight: 700;
            color: #111827;
            background: #fff;
            outline: none;
        }

        .ce-reg .form-grp input:focus,
        .ce-reg .form-grp select:focus,
        .ce-reg .form-grp textarea:focus {
            border-color: rgba(5, 41, 246, 0.65);
            box-shadow: 0 0 0 4px rgba(246, 161, 5, .18);
        }

        .ce-reg .form-grp code {
            color: #ef4444;
        }

        .ce-checks {
            margin-top: 14px;
            display: grid;
            gap: 10px;
        }

        .ce-check {
            display: flex;
            gap: 10px;
            align-items: flex-start;
            font-weight: 800;
            color: #111827;
        }

        .ce-check input {
            margin-top: 3px;
        }

        .ce-reg input[type="checkbox"] {
            width: 18px;
            height: 18px;
            accent-color: #0a5894;
        }

        .ce-actions {
            margin-top: 18px;
            display: flex;
            justify-content: flex-end;
        }

        .ce-login-link {
            margin-top: 14px;
            display: flex;
            gap: 8px;
            justify-content: center;
            font-weight: 800;
            color: #6b7280;
        }

        .ce-login-link a {
            color: var(--tg-theme-primary);
            font-weight: 1000;
            text-decoration: underline;
        }

        @media (max-width: 991.98px) {
            .ce-reg__layout {
                grid-template-columns: 1fr;
            }

            .ce-reg__left {
                min-height: 240px;
                padding: 90px 22px 22px;
            }

            .ce-reg__q {
                font-size: 40px;
            }

            .ce-reg__right {
                padding-top: 26px;
            }
        }
    </style>
@endpush
