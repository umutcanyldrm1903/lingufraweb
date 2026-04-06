@extends('frontend.layouts.master')

@php
    $siteName = $setting->app_name ?? config('app.name');
    $canonicalUrl = route('lingufranca-performance');
    $applyUrl = route('contact.index');
    $testUrl = route('placement-test.show');
    $homeUrl = route('home');
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
    $topLinks = $pageData['top_links'] ?? [];
    $heroBadges = $pageData['hero_badges'] ?? [];
    $heroStats = $pageData['hero_stats'] ?? [];
    $milestones = $pageData['milestones'] ?? [];
    $pricingNotes = $pageData['pricing_notes'] ?? [];
@endphp

@section('meta_title', $pageData['meta_title'] . ' | ' . $siteName)
@section('meta_description', $pageData['meta_description'])
@section('meta_keywords', $pageData['meta_keywords'])
@section('canonical_url', $canonicalUrl)
@section('meta_image', $pageData['meta_image_url'] ?? '')
@section('hide_public_header', '1')
@section('hide_public_footer', '1')

@section('contents')
    <section class="lfps-performance-shell">
        <div class="lfps-page">
            <header class="lfps-topbar">
                <a class="lfps-brand" href="{{ $homeUrl }}">
                    @if (!empty($setting?->logo))
                        <img src="{{ asset($setting->logo) }}" alt="{{ $siteName }}" />
                    @endif
                </a>

                <nav class="lfps-topbar__nav" aria-label="Bolumler">
                    @foreach ($topLinks as $link)
                        <a href="{{ $link['href'] }}">{{ $link['label'] }}</a>
                    @endforeach
                </nav>

                <div class="lfps-topbar__actions">
                    <a class="lfps-button lfps-button--ghost" href="{{ $testUrl }}">Seviye Tespiti</a>
                    <a class="lfps-button" href="{{ $applyUrl }}">Programa Basvur</a>
                </div>
            </header>

            <section class="lfps-hero">
                <div class="lfps-hero__copy">
                    <span class="lfps-kicker">{{ $pageData['eyebrow'] }}</span>
                    <h1>{{ $pageData['title'] }}</h1>
                    <p class="lfps-lead">{{ $pageData['lead'] }}</p>

                    @if (!empty($heroBadges))
                        <div class="lfps-chip-row">
                            @foreach ($heroBadges as $badge)
                                <span class="lfps-chip">{{ $badge }}</span>
                            @endforeach
                        </div>
                    @endif

                    <div class="lfps-hero__actions">
                        <a class="lfps-button" href="{{ $applyUrl }}">Programa Basvur</a>
                        <a class="lfps-button lfps-button--ghost" href="#videolar">Video Kayitlarini Incele</a>
                    </div>

                    @if (!empty($heroStats))
                        <div class="lfps-stat-grid">
                            @foreach ($heroStats as $stat)
                                <article class="lfps-stat-card">
                                    <strong>{{ $stat['value'] }}</strong>
                                    <span>{{ $stat['label'] }}</span>
                                </article>
                            @endforeach
                        </div>
                    @endif
                </div>

                <div class="lfps-hero__visual">
                    <article class="lfps-quote-card">
                        <span class="lfps-section-tag">{{ $pageData['hero_quote_title'] }}</span>
                        <p>{{ $pageData['hero_quote'] }}</p>
                    </article>

                    <div class="lfps-stack-grid">
                        @foreach ($downloads as $program)
                            <article class="lfps-stack-card">
                                @if (!empty($program['cover_url']))
                                    <div class="lfps-stack-card__cover" style="background-image:url('{{ $program['cover_url'] }}')"></div>
                                @endif
                                <div class="lfps-stack-card__body">
                                    <span class="lfps-stack-card__label">{{ $program['label'] }}</span>
                                    <h2>{{ $program['title'] }}</h2>
                                    <p>{{ $program['subtitle'] }}</p>
                                    <small>{{ $program['meta'] }} | {{ $program['result'] }}</small>
                                </div>
                            </article>
                        @endforeach
                    </div>
                </div>
            </section>

            <section class="lfps-proof-strip">
                <span>Basinda ve ogrenci videolarinda gorunen sistem</span>
                <div class="lfps-proof-strip__items">
                    @foreach ($pageData['press_badges'] as $badge)
                        <span>{{ $badge }}</span>
                    @endforeach
                </div>
            </section>

            <section class="lfps-section" id="sistem">
                <div class="lfps-section-head">
                    <span class="lfps-section-tag">{{ $pageData['manifesto_eyebrow'] }}</span>
                    <h2>{{ $pageData['manifesto_title'] }}</h2>
                    <p>{{ $pageData['manifesto_lead'] }}</p>
                </div>

                <div class="lfps-value-grid">
                    @foreach ($pageData['manifesto_points'] as $point)
                        <article class="lfps-value-card">
                            <span class="lfps-value-card__index">0{{ $loop->iteration }}</span>
                            <h3>{{ $point['title'] }}</h3>
                            <p>{{ $point['description'] }}</p>
                        </article>
                    @endforeach
                </div>
            </section>

            <section class="lfps-section">
                <div class="lfps-section-head lfps-section-head--left">
                    <span class="lfps-section-tag">{{ $pageData['fit_eyebrow'] }}</span>
                    <h2>{{ $pageData['fit_title'] }}</h2>
                    <p>{{ $pageData['fit_lead'] }}</p>
                </div>

                <div class="lfps-fit-grid">
                    <article class="lfps-fit-card">
                        <span class="lfps-fit-card__tag">Kimin icin</span>
                        <ul>
                            @foreach ($pageData['fit_for'] as $item)
                                <li>{{ $item }}</li>
                            @endforeach
                        </ul>
                    </article>

                    <article class="lfps-fit-card lfps-fit-card--muted">
                        <span class="lfps-fit-card__tag">Kimin icin degil</span>
                        <ul>
                            @foreach ($pageData['fit_not_for'] as $item)
                                <li>{{ $item }}</li>
                            @endforeach
                        </ul>
                    </article>
                </div>
            </section>

            <section class="lfps-section" id="programlar">
                <div class="lfps-section-head">
                    <span class="lfps-section-tag">{{ $pageData['resource_eyebrow'] }}</span>
                    <h2>{{ $pageData['resource_title'] }}</h2>
                </div>

                <div class="lfps-resource-grid">
                    @foreach ($pageData['resource_columns'] as $column)
                        <article class="lfps-resource-card">
                            <h3>{{ $column['label'] }}</h3>
                            <ul>
                                @foreach ($column['items'] as $item)
                                    <li>{{ $item }}</li>
                                @endforeach
                            </ul>
                        </article>
                    @endforeach
                </div>

                <div class="lfps-program-showcase">
                    @foreach ($downloads as $program)
                        <article class="lfps-program-panel">
                            <div class="lfps-program-panel__media">
                                @if (!empty($program['cover_url']))
                                    <img src="{{ $program['cover_url'] }}" alt="{{ $program['title'] }}" loading="lazy" />
                                @endif
                            </div>
                            <div class="lfps-program-panel__body">
                                <span class="lfps-section-tag">{{ $program['label'] }}</span>
                                <h3>{{ $program['title'] }}</h3>
                                <p>{{ $program['subtitle'] }}</p>
                                <ul>
                                    @foreach ($program['bullets'] as $bullet)
                                        <li>{{ $bullet }}</li>
                                    @endforeach
                                </ul>
                                <div class="lfps-program-panel__footer">
                                    <strong>{{ $program['result'] }}</strong>
                                    @if (!empty($program['file_url']))
                                        <a class="lfps-inline-link" href="{{ $program['file_url'] }}" target="_blank" rel="noopener">Program Detayi</a>
                                    @endif
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
            </section>

            <section class="lfps-section">
                <div class="lfps-section-head">
                    <span class="lfps-section-tag">{{ $pageData['process_eyebrow'] }}</span>
                    <h2>{{ $pageData['process_title'] }}</h2>
                </div>

                <div class="lfps-step-grid">
                    @foreach ($pageData['steps'] as $step)
                        <article class="lfps-step-card">
                            <span class="lfps-step-card__index">0{{ $loop->iteration }}</span>
                            <h3>{{ $step['title'] }}</h3>
                            <p>{{ $step['description'] }}</p>
                        </article>
                    @endforeach
                </div>
            </section>

            <section class="lfps-section">
                <div class="lfps-insight-grid">
                    @if (!empty($milestones))
                        <article class="lfps-insight-card">
                            <span class="lfps-section-tag">{{ $pageData['milestones_eyebrow'] }}</span>
                            <h2>Program sonunda neyi guclendirmek istiyoruz?</h2>
                            <div class="lfps-mini-grid">
                                @foreach ($milestones as $milestone)
                                    <div class="lfps-mini-card">
                                        <strong>{{ $milestone['label'] }}</strong>
                                        <ul>
                                            @foreach ($milestone['items'] as $item)
                                                <li>{{ $item }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endforeach
                            </div>
                        </article>
                    @endif

                    <article class="lfps-insight-card">
                        <span class="lfps-section-tag">{{ $pageData['reasons_eyebrow'] }}</span>
                        <h2>{{ $pageData['reasons_title'] }}</h2>
                        <div class="lfps-reason-list">
                            @foreach ($pageData['reasons'] as $reason)
                                <span>{{ $reason }}</span>
                            @endforeach
                        </div>
                    </article>
                </div>
            </section>

            <section class="lfps-section" id="videolar">
                <div class="lfps-section-head">
                    <span class="lfps-section-tag">{{ $pageData['proof_eyebrow'] }}</span>
                    <h2>{{ $pageData['proof_title'] }}</h2>
                    <p>{{ $pageData['proof_lead'] }}</p>
                </div>

                <div class="lfps-video-grid">
                    @forelse ($mediaLibrary as $item)
                        <article class="lfps-video-card">
                            <div class="lfps-video-card__media">
                                <video controls preload="metadata" playsinline @if (!empty($item['poster_url'])) poster="{{ $item['poster_url'] }}" @endif>
                                    <source src="{{ $item['file_url'] }}" type="video/mp4">
                                    Tarayiciniz video etiketini desteklemiyor.
                                </video>
                            </div>
                            <div class="lfps-video-card__body">
                                <span class="lfps-video-card__meta">{{ $item['category'] }} | {{ $item['duration'] }}</span>
                                <h3>{{ $item['title'] }}</h3>
                                <p>{{ $item['description'] }}</p>
                                <a class="lfps-inline-link" href="{{ $item['file_url'] }}" target="_blank" rel="noopener">Videoyu yeni sekmede ac</a>
                            </div>
                        </article>
                    @empty
                        <div class="lfps-empty-card">
                            Video kayitlari gecici olarak yuklenemedi.
                        </div>
                    @endforelse
                </div>
            </section>

            <section class="lfps-section" id="fiyat">
                <div class="lfps-section-head">
                    <span class="lfps-section-tag">{{ $pageData['pricing_eyebrow'] }}</span>
                    <h2>{{ $pageData['pricing_title'] }}</h2>
                    <p>{{ $pageData['pricing_lead'] }}</p>
                </div>

                <div class="lfps-pricing-grid">
                    @foreach ($pageData['packages'] as $package)
                        <article class="lfps-price-card @if (!empty($package['featured'])) lfps-price-card--featured @endif">
                            @if (!empty($package['featured']))
                                <span class="lfps-price-card__badge">Onerilen Paket</span>
                            @endif
                            <strong>{{ $package['name'] }}</strong>
                            <h3>{{ $package['price'] }}</h3>
                            <span>{{ $package['unit'] }}</span>
                            <p>{{ $package['note'] }}</p>
                            <a class="lfps-button" href="{{ $applyUrl }}">Basvur</a>
                        </article>
                    @endforeach
                </div>

                @if (!empty($pricingNotes))
                    <div class="lfps-note-grid">
                        @foreach ($pricingNotes as $note)
                            <div class="lfps-note-card">{{ $note }}</div>
                        @endforeach
                    </div>
                @endif
            </section>

            <section class="lfps-section" id="sss">
                <div class="lfps-section-head">
                    <span class="lfps-section-tag">SSS</span>
                    <h2>Karar oncesi en cok sorulanlar</h2>
                </div>
                <div class="lfps-faq-list">
                    @foreach ($pageData['faq'] as $faq)
                        <details class="lfps-faq-item">
                            <summary>{{ $faq['question'] }}</summary>
                            <p>{{ $faq['answer'] }}</p>
                        </details>
                    @endforeach
                </div>
            </section>

            <section class="lfps-cta-band">
                <div>
                    <span class="lfps-section-tag">Son adim</span>
                    <h2>{{ $pageData['cta_title'] }}</h2>
                    <p>{{ $pageData['cta_text'] }}</p>
                </div>
                <div class="lfps-cta-band__actions">
                    <a class="lfps-button" href="{{ $applyUrl }}">Programa Basvur</a>
                    <a class="lfps-button lfps-button--ghost" href="{{ $testUrl }}">Seviye Tespiti</a>
                </div>
            </section>

            <footer class="lfps-footer">
                <div>{{ $siteName }} | LinguFranca Performans Sistemi</div>
                <div class="lfps-footer__links">
                    <a href="{{ $homeUrl }}">Ana Sayfa</a>
                    <a href="{{ $applyUrl }}">Iletisim</a>
                    <a href="{{ route('mobile-app-privacy-policy') }}">Gizlilik</a>
                </div>
            </footer>
        </div>
    </section>
@endsection

@push('structured_data')
    <script type="application/ld+json">{!! json_encode([
        '@context' => 'https://schema.org',
        '@type' => 'Service',
        'name' => $pageData['meta_title'],
        'description' => $pageData['meta_description'],
        'url' => $canonicalUrl,
        'provider' => [
            '@type' => 'EducationalOrganization',
            'name' => $siteName,
            'url' => route('home'),
        ],
        'areaServed' => [
            '@type' => 'Country',
            'name' => 'Turkey',
        ],
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}</script>
    <script type="application/ld+json">{!! json_encode([
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
                'name' => $pageData['meta_title'],
                'item' => $canonicalUrl,
            ],
        ],
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}</script>
    @if (!empty($faqEntities))
        <script type="application/ld+json">{!! json_encode([
            '@context' => 'https://schema.org',
            '@type' => 'FAQPage',
            'mainEntity' => $faqEntities,
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}</script>
    @endif
@endpush

@push('styles')
    <link rel="stylesheet" href="{{ asset('frontend/css/saasy-dark-tailwind.css') }}">
    <link rel="stylesheet" href="{{ asset('frontend/css/saasy-dark.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.3/font/bootstrap-icons.min.css" integrity="sha512-dPXYcDub/aeb08c63jRq/k6GaKccl256JQy/AnOq7CAnEZ9FzSL9wSbcZkMp4R26vBsMLFYH4kQ67/bbV8XaCQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
@endpush

@push('scripts')
    <script src="{{ asset('frontend/js/saasy-dark.js') }}"></script>
@endpush
