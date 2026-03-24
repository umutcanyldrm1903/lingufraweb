@extends('frontend.layouts.master')

@php
    $siteName = $setting->app_name ?? config('app.name');
    $canonicalUrl = route($pageData['route']);
    $primaryAction = $pageData['primary_action'] ?? null;
    $primaryActionUrl = $primaryAction ? route($primaryAction['route'], $primaryAction['params'] ?? []) : null;
    $secondaryActions = collect($pageData['secondary_actions'] ?? [])->map(function ($action) {
        $action['url'] = route($action['route'], $action['params'] ?? []);
        return $action;
    });
@endphp

@section('meta_title', $pageData['meta_title'] . ' | ' . $siteName)
@section('meta_description', $pageData['meta_description'])
@section('meta_keywords', $pageData['meta_keywords'])
@section('canonical_url', $canonicalUrl)
@section('meta_image', $setting->logo ?? $setting->favicon ?? '')

@section('contents')
    <x-frontend.breadcrumb :title="$pageData['breadcrumb']" :links="[
        ['url' => route('home'), 'text' => __('Home')],
        ['url' => '', 'text' => $pageData['breadcrumb']],
    ]" />

    <section class="lf-private-lessons section-pb-120">
        <div class="container">
            <div class="lf-private-lessons__hero">
                <div class="lf-private-lessons__hero-copy">
                    <span class="lf-private-lessons__eyebrow">{{ $pageData['eyebrow'] }}</span>
                    <h1 class="lf-private-lessons__title">{{ $pageData['title'] }}</h1>
                    <p class="lf-private-lessons__lead">{{ $pageData['lead'] }}</p>

                    @if (!empty($pageData['facts']))
                        <div class="lf-private-lessons__facts">
                            @foreach ($pageData['facts'] as $fact)
                                <span>{{ $fact }}</span>
                            @endforeach
                        </div>
                    @endif

                    <div class="lf-private-lessons__actions">
                        @if ($primaryActionUrl)
                            <a href="{{ $primaryActionUrl }}" class="btn btn-two">{{ $primaryAction['label'] }}</a>
                        @endif
                        @foreach ($secondaryActions as $action)
                            <a href="{{ $action['url'] }}" class="lf-private-lessons__ghost">{{ $action['label'] }}</a>
                        @endforeach
                    </div>
                </div>
                <div class="lf-private-lessons__stats">
                    @foreach ($pageData['stats'] ?? [] as $stat)
                        <article class="lf-private-lessons__stat-card">
                            <strong>{{ $stat['value'] }}</strong>
                            <span>{{ $stat['label'] }}</span>
                        </article>
                    @endforeach
                </div>
            </div>

            @if (!empty($pageData['benefits']))
                <section class="lf-private-lessons__section">
                    <div class="lf-private-lessons__section-head">
                        <h2>{{ $pageData['benefits_title'] ?? __('Neden bu rota?') }}</h2>
                    </div>
                    <div class="lf-private-lessons__grid">
                        @foreach ($pageData['benefits'] as $benefit)
                            <article class="lf-private-lessons__card">
                                <h3>{{ $benefit['title'] }}</h3>
                                <p>{{ $benefit['description'] }}</p>
                            </article>
                        @endforeach
                    </div>
                </section>
            @endif

            @if (!empty($pageData['tracks']))
                <section class="lf-private-lessons__section">
                    <div class="lf-private-lessons__section-head">
                        <h2>{{ $pageData['focus_title'] ?? __('Rotalar') }}</h2>
                    </div>
                    <div class="lf-private-lessons__grid">
                        @foreach ($pageData['tracks'] as $track)
                            <article class="lf-private-lessons__card lf-private-lessons__card--soft">
                                <h3>{{ $track['title'] }}</h3>
                                <p>{{ $track['description'] }}</p>
                                <a href="{{ route($track['route'], $track['params'] ?? []) }}">{{ $track['link_label'] }}</a>
                            </article>
                        @endforeach
                    </div>
                </section>
            @endif

            @if (!empty($pageData['steps']))
                <section class="lf-private-lessons__section">
                    <div class="lf-private-lessons__section-head">
                        <h2>{{ __('Ders plani nasil ilerler?') }}</h2>
                    </div>
                    <div class="lf-private-lessons__timeline">
                        @foreach ($pageData['steps'] as $index => $step)
                            <article class="lf-private-lessons__timeline-item">
                                <span class="lf-private-lessons__timeline-index">{{ $index + 1 }}</span>
                                <div>
                                    <h3>{{ $step['title'] }}</h3>
                                    <p>{{ $step['description'] }}</p>
                                </div>
                            </article>
                        @endforeach
                    </div>
                </section>
            @endif

            @if ($relatedPages->isNotEmpty())
                <section class="lf-private-lessons__section">
                    <div class="lf-private-lessons__section-head">
                        <h2>{{ __('Benzer sayfalar') }}</h2>
                    </div>
                    <div class="lf-private-lessons__grid">
                        @foreach ($relatedPages as $relatedPage)
                            <article class="lf-private-lessons__card lf-private-lessons__card--link">
                                <h3>{{ $relatedPage['title'] }}</h3>
                                <p>{{ $relatedPage['description'] }}</p>
                                <a href="{{ $relatedPage['url'] }}">{{ __('Sayfayi ac') }}</a>
                            </article>
                        @endforeach
                    </div>
                </section>
            @endif

            @if (!empty($pageData['faq']))
                <section class="lf-private-lessons__section">
                    <div class="lf-private-lessons__section-head">
                        <h2>{{ __('Sik sorulan sorular') }}</h2>
                    </div>
                    <div class="lf-private-lessons__faq">
                        @foreach ($pageData['faq'] as $faq)
                            <details class="lf-private-lessons__faq-item">
                                <summary>{{ $faq['question'] }}</summary>
                                <p>{{ $faq['answer'] }}</p>
                            </details>
                        @endforeach
                    </div>
                </section>
            @endif

            <section class="lf-private-lessons__cta">
                <div>
                    <span class="lf-private-lessons__eyebrow">{{ __('Hazir baslangic') }}</span>
                    <h2>{{ __('Ders planini bugun netlestir') }}</h2>
                    <p>{{ __('Hedefini sec, seviyeni olc ve programina uygun egitmeni tek akista bul.') }}</p>
                </div>
                <div class="lf-private-lessons__cta-actions">
                    @if ($primaryActionUrl)
                        <a href="{{ $primaryActionUrl }}" class="btn btn-two">{{ $primaryAction['label'] }}</a>
                    @endif
                    <a href="{{ route('placement-test.show') }}" class="lf-private-lessons__ghost">{{ __('Seviye tespiti') }}</a>
                </div>
            </section>
        </div>
    </section>
@endsection

@push('structured_data')
    @php
        $faqEntities = collect($pageData['faq'] ?? [])
            ->map(fn ($faq) => [
                '@type' => 'Question',
                'name' => $faq['question'],
                'acceptedAnswer' => [
                    '@type' => 'Answer',
                    'text' => $faq['answer'],
                ],
            ])
            ->values()
            ->all();

        $serviceSchema = [
            '@context' => 'https://schema.org',
            '@type' => 'Service',
            'name' => $pageData['breadcrumb'],
            'serviceType' => $pageData['breadcrumb'],
            'url' => $canonicalUrl,
            'description' => $pageData['meta_description'],
            'provider' => [
                '@type' => 'EducationalOrganization',
                'name' => $siteName,
                'url' => route('home'),
            ],
            'areaServed' => [
                '@type' => 'Place',
                'name' => $pageData['area_served'] ?? 'Turkey',
            ],
        ];

        $breadcrumbSchema = [
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
                    'name' => $pageData['breadcrumb'],
                    'item' => $canonicalUrl,
                ],
            ],
        ];
    @endphp
    <script type="application/ld+json">{!! json_encode($serviceSchema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}</script>
    <script type="application/ld+json">{!! json_encode($breadcrumbSchema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}</script>
    @if (!empty($faqEntities))
        <script type="application/ld+json">
            {!! json_encode([
                '@context' => 'https://schema.org',
                '@type' => 'FAQPage',
                'mainEntity' => $faqEntities,
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}
        </script>
    @endif
@endpush

@push('styles')
    <style>
        .lf-private-lessons {
            background:
                radial-gradient(900px circle at 12% 0%, rgba(246, 161, 5, 0.10), transparent 52%),
                linear-gradient(180deg, #f7fbff 0%, #ffffff 100%);
        }

        .lf-private-lessons__hero,
        .lf-private-lessons__cta {
            display: grid;
            grid-template-columns: minmax(0, 1.1fr) minmax(320px, 0.9fr);
            gap: 24px;
            padding: 34px;
            border-radius: 28px;
            background: linear-gradient(135deg, #0c4f7f, #0a3f67);
            color: #fff;
            box-shadow: 0 24px 54px rgba(10, 39, 63, 0.16);
        }

        .lf-private-lessons__section {
            margin-top: 28px;
        }

        .lf-private-lessons__section-head h2,
        .lf-private-lessons__title {
            font-weight: 1000;
            line-height: 1.06;
        }

        .lf-private-lessons__title {
            margin: 0 0 14px;
            font-size: clamp(34px, 4vw, 54px);
        }

        .lf-private-lessons__lead,
        .lf-private-lessons__cta p {
            margin: 0;
            color: rgba(255, 255, 255, 0.84);
            font-size: 17px;
            line-height: 1.85;
            font-weight: 600;
        }

        .lf-private-lessons__eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 12px;
            font-size: 12px;
            font-weight: 900;
            letter-spacing: 0.18em;
            text-transform: uppercase;
        }

        .lf-private-lessons__eyebrow::before {
            content: '';
            width: 10px;
            height: 10px;
            border-radius: 999px;
            background: #f6a105;
        }

        .lf-private-lessons__facts,
        .lf-private-lessons__actions,
        .lf-private-lessons__cta-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            margin-top: 20px;
        }

        .lf-private-lessons__facts span,
        .lf-private-lessons__ghost {
            display: inline-flex;
            align-items: center;
            min-height: 46px;
            padding: 0 18px;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.10);
            border: 1px solid rgba(255, 255, 255, 0.14);
            color: #fff;
            text-decoration: none;
            font-weight: 800;
        }

        .lf-private-lessons__stats,
        .lf-private-lessons__grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 16px;
        }

        .lf-private-lessons__stat-card,
        .lf-private-lessons__card,
        .lf-private-lessons__faq-item {
            padding: 22px;
            border-radius: 24px;
            background: #fff;
            border: 1px solid rgba(14, 92, 147, 0.12);
            box-shadow: 0 18px 42px rgba(22, 20, 57, 0.06);
        }

        .lf-private-lessons__stat-card {
            background: rgba(255, 255, 255, 0.10);
            border-color: rgba(255, 255, 255, 0.16);
        }

        .lf-private-lessons__stat-card strong {
            display: block;
            margin-bottom: 8px;
            font-size: 28px;
            line-height: 1;
        }

        .lf-private-lessons__stat-card span {
            color: rgba(255, 255, 255, 0.82);
            font-weight: 700;
            line-height: 1.7;
        }

        .lf-private-lessons__card h3,
        .lf-private-lessons__timeline-item h3,
        .lf-private-lessons__faq-item summary,
        .lf-private-lessons__cta h2 {
            margin: 0 0 10px;
            font-size: 22px;
            font-weight: 900;
        }

        .lf-private-lessons__card p,
        .lf-private-lessons__timeline-item p,
        .lf-private-lessons__faq-item p {
            margin: 0;
            color: var(--tg-body-color);
            font-weight: 600;
            line-height: 1.8;
        }

        .lf-private-lessons__card a {
            display: inline-flex;
            margin-top: 16px;
            color: var(--tg-theme-primary);
            font-weight: 900;
        }

        .lf-private-lessons__card--soft {
            background: linear-gradient(180deg, #ffffff, #f5fbff);
        }

        .lf-private-lessons__timeline {
            display: grid;
            gap: 14px;
        }

        .lf-private-lessons__timeline-item {
            display: grid;
            grid-template-columns: 60px minmax(0, 1fr);
            gap: 18px;
            align-items: start;
            padding: 22px;
            border-radius: 24px;
            background: #fff;
            border: 1px solid rgba(14, 92, 147, 0.12);
        }

        .lf-private-lessons__timeline-index {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 60px;
            height: 60px;
            border-radius: 18px;
            background: linear-gradient(135deg, #0e5c93, #0b3f6c);
            color: #fff;
            font-size: 24px;
            font-weight: 1000;
        }

        .lf-private-lessons__faq {
            display: grid;
            gap: 14px;
        }

        .lf-private-lessons__faq-item summary {
            cursor: pointer;
            list-style: none;
        }

        .lf-private-lessons__faq-item summary::-webkit-details-marker {
            display: none;
        }

        .lf-private-lessons__faq-item[open] {
            border-color: rgba(246, 161, 5, 0.35);
        }

        @media (max-width: 991.98px) {
            .lf-private-lessons__hero,
            .lf-private-lessons__cta,
            .lf-private-lessons__stats,
            .lf-private-lessons__grid {
                grid-template-columns: 1fr;
            }

            .lf-private-lessons__hero,
            .lf-private-lessons__cta {
                padding: 24px;
            }

            .lf-private-lessons__timeline-item {
                grid-template-columns: 1fr;
            }
        }
    </style>
@endpush
