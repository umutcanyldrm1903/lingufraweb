@extends('frontend.layouts.master')
@section('meta_title', __('Corporate Form') . ' || ' . $setting->app_name)

@section('contents')
    <x-frontend.breadcrumb :title="__('Corporate')" :links="[
        ['url' => route('home'), 'text' => __('Home')],
        ['url' => route('corporate.index'), 'text' => __('Corporate')],
        ['url' => '', 'text' => __('Form')],
    ]" />

    <section class="lf-corp-form section-py-120">
        <div class="container">
            <div class="lf-cf">
                <div class="lf-cf__head">
                    <h1 class="lf-cf__title">{{ __('Let your company cover your lesson fees!') }}</h1>
                    <p class="lf-cf__sub">
                        {{ __('Fill in the details for a corporate training quote. Our team will get back to you shortly.') }}
                    </p>
                </div>

                <div class="row g-4 align-items-start">
                    <div class="col-lg-5">
                        <div class="lf-cf__steps">
                            <div class="lf-step">
                                <span class="lf-step__num">1</span>
                                <span class="lf-step__text">{{ __('Fill out the contact form') }}</span>
                            </div>
                            <div class="lf-step">
                                <span class="lf-step__num">2</span>
                                <span class="lf-step__text">{{ __('Introduce the training to your company') }}</span>
                            </div>
                            <div class="lf-step">
                                <span class="lf-step__num">3</span>
                                <span class="lf-step__text">{{ __('Get a corporate quote for lessons') }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-7">
                        <form action="{{ route('corporate.submit') }}" method="POST" class="lf-cf__card">
                            @csrf

                            <h4 class="lf-cf__card-title">{{ __('Company Information') }}</h4>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="form-grp">
                                        <label>{{ __('Contact first name') }} <code>*</code></label>
                                        <input class="form-control" type="text" name="company_contact_first_name"
                                            value="{{ old('company_contact_first_name') }}" required>
                                        <x-frontend.validation-error name="company_contact_first_name" />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-grp">
                                        <label>{{ __('Contact last name') }} <code>*</code></label>
                                        <input class="form-control" type="text" name="company_contact_last_name"
                                            value="{{ old('company_contact_last_name') }}" required>
                                        <x-frontend.validation-error name="company_contact_last_name" />
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-grp">
                                        <label>{{ __('Company name') }} <code>*</code></label>
                                        <input class="form-control" type="text" name="company_name"
                                            value="{{ old('company_name') }}" required>
                                        <x-frontend.validation-error name="company_name" />
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-grp">
                                        <label>{{ __('Company phone') }} <code>*</code></label>
                                        <input class="form-control" type="text" name="company_phone"
                                            value="{{ old('company_phone') }}" required>
                                        <x-frontend.validation-error name="company_phone" />
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-grp">
                                        <label>{{ __('Corporate email') }} <code>*</code></label>
                                        <input class="form-control" type="email" name="company_email"
                                            value="{{ old('company_email') }}" required>
                                        <x-frontend.validation-error name="company_email" />
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-grp">
                                        <label>{{ __('Number of trainees') }} <code>*</code></label>
                                        <input class="form-control" type="number" min="1" name="people_count"
                                            value="{{ old('people_count') }}" required>
                                        <x-frontend.validation-error name="people_count" />
                                    </div>
                                </div>
                            </div>

                            <div class="lf-cf__divider"></div>

                            <h4 class="lf-cf__card-title">{{ __('Your Information') }}</h4>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="form-grp">
                                        <label>{{ __('First name') }} <code>*</code></label>
                                        <input class="form-control" type="text" name="your_first_name"
                                            value="{{ old('your_first_name') }}" required>
                                        <x-frontend.validation-error name="your_first_name" />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-grp">
                                        <label>{{ __('Last name') }} <code>*</code></label>
                                        <input class="form-control" type="text" name="your_last_name"
                                            value="{{ old('your_last_name') }}" required>
                                        <x-frontend.validation-error name="your_last_name" />
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-grp">
                                        <label>{{ __('Email address') }} <code>*</code></label>
                                        <input class="form-control" type="email" name="your_email"
                                            value="{{ old('your_email') }}" required>
                                        <x-frontend.validation-error name="your_email" />
                                    </div>
                                </div>
                            </div>

                            <div class="lf-cf__actions">
                                <button type="submit" class="btn btn-two lf-cf__btn">{{ __('Get a Quote') }}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('styles')
    <style>
        .lf-corp-form {
            background: var(--tg-common-color-gray-2);
        }

        .lf-cf__head {
            text-align: center;
            margin-bottom: 24px;
        }

        .lf-cf__title {
            font-weight: 1000;
            font-size: 44px;
            margin: 0 0 10px;
            color: var(--tg-heading-color);
        }

        .lf-cf__sub {
            margin: 0;
            font-weight: 700;
            color: var(--tg-body-color);
        }

        .lf-cf__steps {
            display: grid;
            gap: 14px;
            position: sticky;
            top: 120px;
        }

        .lf-step {
            display: flex;
            gap: 12px;
            align-items: center;
            padding: 14px 16px;
            border-radius: 18px;
            border: 1px solid var(--tg-border-2);
            background: rgba(255, 255, 255, 0.85);
            box-shadow: 0 16px 40px rgba(22, 20, 57, 0.08);
        }

        .lf-step__num {
            width: 42px;
            height: 42px;
            border-radius: 12px;
            display: grid;
            place-items: center;
            font-weight: 1000;
            background: var(--tg-theme-secondary);
            color: var(--tg-common-color-black-3);
            border: 1px solid rgba(0, 0, 0, 0.12);
        }

        .lf-step__text {
            font-weight: 900;
            color: var(--tg-heading-color);
        }

        .lf-cf__card {
            background: rgba(255, 255, 255, 0.92);
            border: 1px solid var(--tg-border-2);
            border-radius: 22px;
            padding: 22px;
            box-shadow: 0 18px 46px rgba(22, 20, 57, 0.10);
        }

        .lf-cf__card-title {
            font-weight: 1000;
            color: var(--tg-heading-color);
            margin: 0 0 12px;
        }

        .lf-cf__divider {
            height: 1px;
            background: var(--tg-border-2);
            margin: 18px 0;
        }

        .lf-corp-form .form-grp label {
            display: block;
            margin-bottom: 6px;
            font-weight: 900;
            color: var(--tg-heading-color);
        }

        .lf-corp-form .form-grp code {
            color: #ef4444;
        }

        .lf-cf__actions {
            display: flex;
            justify-content: flex-end;
            margin-top: 16px;
        }

        .lf-cf__btn {
            border-radius: 14px;
            padding: 12px 20px;
            font-weight: 900;
        }

        @media (max-width: 991.98px) {
            .lf-cf__title {
                font-size: 32px;
            }

            .lf-cf__steps {
                position: static;
            }
        }
    </style>
@endpush
