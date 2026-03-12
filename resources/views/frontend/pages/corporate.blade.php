@extends('frontend.layouts.master')
@section('meta_title', __('Corporate') . ' || ' . $setting->app_name)

@section('contents')
    <x-frontend.breadcrumb :title="__('Corporate')" :links="[
        ['url' => route('home'), 'text' => __('Home')],
        ['url' => '', 'text' => __('Corporate')],
    ]" />

    <section class="lf-corp section-py-120">
        <div class="container">
            <div class="row align-items-center g-4">
                <div class="col-lg-6">
                    <h1 class="lf-corp__title">{{ __('Let your company cover your lesson fees') }}</h1>
                    <p class="lf-corp__lead">
                        {{ __('Improve your employees\' English skills. Fill out the form for a corporate training quote and we will get back to you.') }}
                    </p>
                    <div class="lf-corp__actions">
                        <a href="{{ route('corporate.form') }}" class="btn btn-two lf-corp__btn">
                            {{ __('Submit your company') }}
                        </a>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="lf-corp__art">
                        <img src="{{ asset('frontend/img/others/about_img.png') }}" alt="corporate">
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('styles')
    <style>
        .lf-corp {
            background: var(--tg-common-color-gray-2);
        }

        .lf-corp__title {
            font-weight: 1000;
            font-size: 44px;
            line-height: 1.05;
            color: var(--tg-heading-color);
            margin: 0 0 14px;
        }

        .lf-corp__lead {
            max-width: 560px;
            font-weight: 700;
            color: var(--tg-body-color);
            margin: 0 0 18px;
        }

        .lf-corp__actions {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
            align-items: center;
        }

        .lf-corp__btn {
            border-radius: 14px;
            padding: 14px 22px;
            font-weight: 900;
        }

        .lf-corp__art {
            background: rgba(255, 255, 255, 0.85);
            border: 1px solid var(--tg-border-2);
            border-radius: 22px;
            padding: 18px;
            box-shadow: 0 18px 46px rgba(22, 20, 57, 0.08);
        }

        .lf-corp__art img {
            width: 100%;
            height: auto;
            display: block;
        }

        @media (max-width: 991.98px) {
            .lf-corp__title {
                font-size: 34px;
            }
        }
    </style>
@endpush
