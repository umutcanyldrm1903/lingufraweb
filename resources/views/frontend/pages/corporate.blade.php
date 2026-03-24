@extends('frontend.layouts.master')
@section('meta_title', __('Corporate') . ' || ' . $setting->app_name)
@section('meta_description', 'Corporate English training, employee lesson plans and placement support for teams that want measurable language development.')
@section('meta_keywords', 'corporate english training, employee english lessons, team english classes, company language training')
@section('canonical_url', route('corporate.index'))
@section('meta_image', $setting->logo ?? $setting->favicon ?? '')

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

    <section class="lf-corp-links section-pb-120">
        <div class="container">
            <div class="lf-corp-links__shell">
                <div class="lf-corp-links__intro">
                    <span class="lf-corp-links__eyebrow">{{ __('Baslangic Noktalari') }}</span>
                    <h2 class="lf-corp-links__title">{{ __('Ekibin icin dogru dil egitimi yollarini tek yerde topla') }}</h2>
                    <p class="lf-corp-links__lead">
                        {{ __('Kurumsal ziyaretciler once ozel ders rotalarini, sonra egitmenleri, seviye tespitini ve teklif formunu kolayca karsilastirabilsin.') }}
                    </p>
                </div>
                <div class="lf-corp-links__grid">
                    <a href="{{ route('english-private-lessons') }}" class="lf-corp-links__item">
                        <strong>{{ __('Ingilizce Ozel Ders') }}</strong>
                        <span>{{ __('Tum birebir ders rotalarini incele') }}</span>
                    </a>
                    <a href="{{ route('all-instructors') }}" class="lf-corp-links__item">
                        <strong>{{ __('Uzman Egitmenler') }}</strong>
                        <span>{{ __('Ekibine destek olabilecek profilleri gor') }}</span>
                    </a>
                    <a href="{{ route('placement-test.show') }}" class="lf-corp-links__item">
                        <strong>{{ __('Seviye Tespit Sinavi') }}</strong>
                        <span>{{ __('Planlama oncesi mevcut seviyeyi gor') }}</span>
                    </a>
                    <a href="{{ route('contact.index') }}" class="lf-corp-links__item">
                        <strong>{{ __('Ekiple Iletisime Gec') }}</strong>
                        <span>{{ __('Sirketin icin dogrudan yonlendirme al') }}</span>
                    </a>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('structured_data')
    @php
        $corporateSchema = [
            '@context' => 'https://schema.org',
            '@type' => 'Service',
            'name' => 'Corporate English Training',
            'serviceType' => 'Corporate English Training',
            'url' => route('corporate.index'),
            'description' => 'Corporate English training, employee lesson plans and placement support for teams.',
            'provider' => [
                '@type' => 'EducationalOrganization',
                'name' => $setting->app_name,
                'url' => route('home'),
            ],
            'areaServed' => [
                '@type' => 'Country',
                'name' => 'Turkey',
            ],
        ];
        $corporateBreadcrumbSchema = [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => [
                [
                    '@type' => 'ListItem',
                    'position' => 1,
                    'name' => 'Home',
                    'item' => route('home'),
                ],
                [
                    '@type' => 'ListItem',
                    'position' => 2,
                    'name' => 'Corporate',
                    'item' => route('corporate.index'),
                ],
            ],
        ];
    @endphp
    <script type="application/ld+json">{!! json_encode($corporateSchema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}</script>
    <script type="application/ld+json">{!! json_encode($corporateBreadcrumbSchema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}</script>
@endpush

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

        .lf-corp-links__shell {
            display: grid;
            grid-template-columns: minmax(0, 0.95fr) minmax(0, 1.05fr);
            gap: 22px;
            padding: 28px;
            border-radius: 26px;
            background: linear-gradient(135deg, #ffffff, #f6fbff);
            border: 1px solid rgba(14, 92, 147, 0.12);
            box-shadow: 0 18px 46px rgba(22, 20, 57, 0.08);
        }

        .lf-corp-links__eyebrow {
            display: inline-flex;
            margin-bottom: 10px;
            font-size: 12px;
            font-weight: 900;
            letter-spacing: 0.18em;
            text-transform: uppercase;
            color: var(--tg-theme-primary);
        }

        .lf-corp-links__title {
            margin: 0 0 12px;
            font-size: 36px;
            line-height: 1.08;
            font-weight: 1000;
            color: var(--tg-heading-color);
        }

        .lf-corp-links__lead {
            margin: 0;
            color: var(--tg-body-color);
            font-weight: 700;
            line-height: 1.8;
        }

        .lf-corp-links__grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 14px;
        }

        .lf-corp-links__item {
            display: grid;
            gap: 6px;
            min-height: 138px;
            padding: 20px;
            border-radius: 20px;
            background: #0b3f6c;
            color: #fff;
            text-decoration: none;
            box-shadow: 0 16px 32px rgba(11, 63, 108, 0.18);
            transition: transform 0.2s ease, box-shadow 0.2s ease, background 0.2s ease;
        }

        .lf-corp-links__item:hover {
            transform: translateY(-3px);
            background: #0e5c93;
            box-shadow: 0 20px 38px rgba(11, 63, 108, 0.24);
        }

        .lf-corp-links__item strong {
            font-size: 18px;
            line-height: 1.35;
            font-weight: 900;
        }

        .lf-corp-links__item span {
            font-size: 14px;
            line-height: 1.7;
            color: rgba(255, 255, 255, 0.78);
            font-weight: 700;
        }

        @media (max-width: 991.98px) {
            .lf-corp__title {
                font-size: 34px;
            }

            .lf-corp-links__shell,
            .lf-corp-links__grid {
                grid-template-columns: 1fr;
            }

            .lf-corp-links__title {
                font-size: 30px;
            }
        }
    </style>
@endpush
