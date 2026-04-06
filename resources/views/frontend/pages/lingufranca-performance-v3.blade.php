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

    $featuredMedia = $mediaLibrary[0] ?? null;
    $secondaryMedia = array_slice($mediaLibrary, 1, 6);

    // Carousel içerikleri: program sonuçları + birkaç medya içgörüsü (isim yoksa başlık/metinle sunuyoruz)
    $resultSlides = collect($downloads)
        ->map(fn ($program) => [
            'title' => $program['title'] ?? '',
            'highlight' => $program['result'] ?? '',
            'text' => $program['subtitle'] ?? '',
            'tag' => $program['label'] ?? 'Sonuç',
        ])
        ->merge(
            collect($mediaLibrary)->take(4)->map(fn ($item) => [
                'title' => $item['title'] ?? '',
                'highlight' => $item['category'] ?? 'İçgörü',
                'text' => $item['description'] ?? '',
                'tag' => $item['duration'] ?? 'Video',
            ])
        )
        ->filter(fn ($item) => filled($item['title']) && filled($item['highlight']) && filled($item['text']))
        ->values()
        ->all();
@endphp

@section('meta_title', $pageData['meta_title'] . ' | ' . $siteName)
@section('meta_description', $pageData['meta_description'])
@section('meta_keywords', $pageData['meta_keywords'])
@section('canonical_url', $canonicalUrl)
@section('meta_image', $pageData['meta_image_url'] ?? '')
@section('hide_public_header', '1')
@section('hide_public_footer', '1')

@section('contents')
    <section class="lfv3">
        <div class="lfv3-container">
            <header class="lfv3-topbar">
                <a class="lfv3-brand" href="{{ $homeUrl }}">
                    @if (!empty($setting?->logo))
                        <img src="{{ asset($setting->logo) }}" alt="{{ $siteName }}" />
                    @endif
                </a>

                <nav class="lfv3-nav" aria-label="Bolumler">
                    @foreach ($topLinks as $link)
                        <a href="{{ $link['href'] }}">{{ $link['label'] }}</a>
                    @endforeach
                </nav>

                <div class="lfv3-topbar__actions">
                    <a class="lfv3-btn lfv3-btn--ghost" href="{{ $testUrl }}">Seviye Tespiti</a>
                    <a class="lfv3-btn" href="{{ $applyUrl }}">Programa Basvur</a>
                </div>
            </header>

            {{-- HERO --}}
            <section class="lfv3-hero">
                <div class="lfv3-hero__copy">
                    <span class="lfv3-kicker">{{ $pageData['eyebrow'] }}</span>
                    <h1>{{ $pageData['title'] }}</h1>
                    <p class="lfv3-lead">{{ $pageData['lead'] }}</p>

                    @if (!empty($heroBadges))
                        <div class="lfv3-chips" aria-label="Öne çıkanlar">
                            @foreach ($heroBadges as $badge)
                                <span class="lfv3-chip">{{ $badge }}</span>
                            @endforeach
                        </div>
                    @endif

                    <div class="lfv3-hero__cta">
                        <a class="lfv3-btn" href="{{ $applyUrl }}">Programa Basvur</a>
                        <a class="lfv3-btn lfv3-btn--ghost" href="#videolar">Video Kayitlarini Incele</a>
                    </div>

                    @if (!empty($heroStats))
                        <div class="lfv3-metrics" aria-label="Ölçümler">
                            @foreach ($heroStats as $stat)
                                <article class="lfv3-metric">
                                    <strong>{{ $stat['value'] }}</strong>
                                    <span>{{ $stat['label'] }}</span>
                                </article>
                            @endforeach
                        </div>
                    @endif
                </div>

                <div class="lfv3-hero__panel">
                    @if (!empty($pageData['hero_primary_visual']))
                        <img class="lfv3-hero__image" src="{{ $pageData['hero_primary_visual'] }}" alt="{{ $pageData['meta_title'] }}" loading="lazy" />
                    @endif
                    <div class="lfv3-quote">
                        <span class="lfv3-quote__tag">{{ $pageData['hero_quote_title'] }}</span>
                        <p class="lfv3-quote__text">{{ $pageData['hero_quote'] }}</p>
                    </div>
                </div>
            </section>

            {{-- PRESS STRIP --}}
            <section class="lfv3-press">
                <div class="lfv3-press__inner">
                    <span class="lfv3-press__label">Basinda ve ogrenci videolarinda gorunen sistem</span>
                    <div class="lfv3-press__badges">
                        @foreach ($pageData['press_badges'] as $badge)
                            <span class="lfv3-chip lfv3-chip--soft">{{ $badge }}</span>
                        @endforeach
                    </div>
                </div>
            </section>

            {{-- SYSTEM --}}
            <section class="lfv3-section lfv3-reveal" id="sistem">
                <div class="lfv3-section__head">
                    <span class="lfv3-kicker">{{ $pageData['manifesto_eyebrow'] }}</span>
                    <h2>{{ $pageData['manifesto_title'] }}</h2>
                    <p>{{ $pageData['manifesto_lead'] }}</p>
                </div>
                <div class="lfv3-grid3">
                    @foreach ($pageData['manifesto_points'] as $point)
                        <article class="lfv3-card">
                            <span class="lfv3-card__index">0{{ $loop->iteration }}</span>
                            <h3>{{ $point['title'] }}</h3>
                            <p>{{ $point['description'] }}</p>
                        </article>
                    @endforeach
                </div>
            </section>

            {{-- FIT --}}
            <section class="lfv3-section lfv3-reveal">
                <div class="lfv3-section__head">
                    <span class="lfv3-kicker">{{ $pageData['fit_eyebrow'] }}</span>
                    <h2>{{ $pageData['fit_title'] }}</h2>
                    <p>{{ $pageData['fit_lead'] }}</p>
                </div>
                <div class="lfv3-grid2">
                    <article class="lfv3-card lfv3-card--muted">
                        <h3>Kimin icin</h3>
                        <ul class="lfv3-list">
                            @foreach ($pageData['fit_for'] as $item)
                                <li>{{ $item }}</li>
                            @endforeach
                        </ul>
                    </article>
                    <article class="lfv3-card lfv3-card--muted">
                        <h3>Kimin icin degil</h3>
                        <ul class="lfv3-list">
                            @foreach ($pageData['fit_not_for'] as $item)
                                <li>{{ $item }}</li>
                            @endforeach
                        </ul>
                    </article>
                </div>
            </section>

            {{-- PROGRAMS / CONTENT --}}
            <section class="lfv3-section lfv3-reveal" id="programlar">
                <div class="lfv3-section__head">
                    <span class="lfv3-kicker">{{ $pageData['resource_eyebrow'] }}</span>
                    <h2>{{ $pageData['resource_title'] }}</h2>
                </div>

                <div class="lfv3-grid2">
                    @foreach ($pageData['resource_columns'] as $column)
                        <article class="lfv3-card">
                            <h3>{{ $column['label'] }}</h3>
                            <ul class="lfv3-list">
                                @foreach ($column['items'] as $item)
                                    <li>{{ $item }}</li>
                                @endforeach
                            </ul>
                        </article>
                    @endforeach
                </div>

                <div class="lfv3-programs">
                    @foreach ($downloads as $program)
                        <article class="lfv3-program">
                            @if (!empty($program['cover_url']))
                                <img class="lfv3-program__img" src="{{ $program['cover_url'] }}" alt="{{ $program['title'] }}" loading="lazy" />
                            @endif
                            <div class="lfv3-program__body">
                                <span class="lfv3-pill">{{ $program['label'] }}</span>
                                <h3>{{ $program['title'] }}</h3>
                                <p>{{ $program['subtitle'] }}</p>
                                <ul class="lfv3-list">
                                    @foreach ($program['bullets'] as $bullet)
                                        <li>{{ $bullet }}</li>
                                    @endforeach
                                </ul>
                                <div class="lfv3-program__footer">
                                    <strong class="lfv3-program__result">{{ $program['result'] }}</strong>
                                    @if (!empty($program['file_url']))
                                        <a class="lfv3-inline-link" href="{{ $program['file_url'] }}" target="_blank" rel="noopener">Program Detayi</a>
                                    @endif
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
            </section>

            {{-- PROCESS --}}
            <section class="lfv3-section lfv3-reveal">
                <div class="lfv3-section__head">
                    <span class="lfv3-kicker">{{ $pageData['process_eyebrow'] }}</span>
                    <h2>{{ $pageData['process_title'] }}</h2>
                </div>
                <div class="lfv3-grid4">
                    @foreach ($pageData['steps'] as $step)
                        <article class="lfv3-card">
                            <span class="lfv3-card__index">0{{ $loop->iteration }}</span>
                            <h3>{{ $step['title'] }}</h3>
                            <p>{{ $step['description'] }}</p>
                        </article>
                    @endforeach
                </div>
            </section>

            {{-- TESTIMONIALS / RESULTS CAROUSEL --}}
            @if (!empty($resultSlides))
                <section class="lfv3-section lfv3-reveal" id="sonuclar">
                    <div class="lfv3-section__head">
                        <span class="lfv3-kicker">Gercek deneyimlerden ozetler</span>
                        <h2>Sonuclar</h2>
                    </div>

                    <div class="lfv3-carousel" data-lfv3-carousel>
                        <div class="lfv3-carousel__track">
                            @foreach ($resultSlides as $slide)
                                <article class="lfv3-carousel__slide" aria-roledescription="slide">
                                    <div class="lfv3-carousel__top">
                                        <span class="lfv3-pill lfv3-pill--soft">{{ $slide['tag'] }}</span>
                                        <strong class="lfv3-carousel__highlight">{{ $slide['highlight'] }}</strong>
                                    </div>
                                    <h3 class="lfv3-carousel__title">{{ $slide['title'] }}</h3>
                                    <p class="lfv3-carousel__text">{{ $slide['text'] }}</p>
                                </article>
                            @endforeach
                        </div>
                        <div class="lfv3-carousel__dots">
                            @foreach ($resultSlides as $slide)
                                <button type="button" data-lfv3-dot aria-label="Slide {{ $loop->iteration }}" class="lfv3-carousel__dot"></button>
                            @endforeach
                        </div>
                    </div>
                </section>
            @endif

            {{-- MILESTONES + REASONS --}}
            <section class="lfv3-section lfv3-reveal">
                <div class="lfv3-grid2 lfv3-reason-grid">
                    <article class="lfv3-card">
                        <span class="lfv3-kicker">{{ $pageData['milestones_eyebrow'] }}</span>
                        <h3>Program sonunda neyi guclendirmek istiyoruz?</h3>
                        <div class="lfv3-subgrid2">
                            @foreach ($milestones as $milestone)
                                <div class="lfv3-subcard">
                                    <strong>{{ $milestone['label'] }}</strong>
                                    <ul class="lfv3-list lfv3-list--tight">
                                        @foreach ($milestone['items'] as $item)
                                            <li>{{ $item }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endforeach
                        </div>
                    </article>
                    <article class="lfv3-card lfv3-card--muted">
                        <span class="lfv3-kicker">{{ $pageData['reasons_eyebrow'] }}</span>
                        <h3>{{ $pageData['reasons_title'] }}</h3>
                        <div class="lfv3-chips">
                            @foreach ($pageData['reasons'] as $reason)
                                <span class="lfv3-chip lfv3-chip--soft">{{ $reason }}</span>
                            @endforeach
                        </div>
                    </article>
                </div>
            </section>

            {{-- VIDEO --}}
            <section class="lfv3-section lfv3-reveal" id="videolar">
                <div class="lfv3-section__head">
                    <span class="lfv3-kicker">{{ $pageData['proof_eyebrow'] }}</span>
                    <h2>{{ $pageData['proof_title'] }}</h2>
                    <p>{{ $pageData['proof_lead'] }}</p>
                </div>

                @if (!empty($featuredMedia))
                    <div class="lfv3-grid2 lfv3-video-layout">
                        <article class="lfv3-card lfv3-video-feature">
                            <video class="lfv3-video" controls preload="metadata" playsinline @if (!empty($featuredMedia['poster_url'])) poster="{{ $featuredMedia['poster_url'] }}" @endif>
                                <source src="{{ $featuredMedia['file_url'] }}" type="video/mp4">
                            </video>
                            <div class="lfv3-video-meta">
                                <span class="lfv3-pill lfv3-pill--soft">{{ $featuredMedia['category'] }} | {{ $featuredMedia['duration'] }}</span>
                                <h3>{{ $featuredMedia['title'] }}</h3>
                                <p>{{ $featuredMedia['description'] }}</p>
                                <a class="lfv3-inline-link" href="{{ $featuredMedia['file_url'] }}" target="_blank" rel="noopener">Videoyu yeni sekmede ac</a>
                            </div>
                        </article>

                        <div class="lfv3-grid2 lfv3-video-grid">
                            @foreach ($secondaryMedia as $item)
                                <article class="lfv3-card lfv3-video-card">
                                    <video class="lfv3-video" controls preload="metadata" playsinline @if (!empty($item['poster_url'])) poster="{{ $item['poster_url'] }}" @endif>
                                        <source src="{{ $item['file_url'] }}" type="video/mp4">
                                    </video>
                                    <div class="lfv3-video-meta">
                                        <span class="lfv3-pill lfv3-pill--soft">{{ $item['category'] }} | {{ $item['duration'] }}</span>
                                        <h3>{{ $item['title'] }}</h3>
                                        <p>{{ $item['description'] }}</p>
                                        <a class="lfv3-inline-link" href="{{ $item['file_url'] }}" target="_blank" rel="noopener">Videoyu yeni sekmede ac</a>
                                    </div>
                                </article>
                            @endforeach
                        </div>
                    </div>
                @else
                    <div class="lfv3-card lfv3-empty">Video kayitlari gecici olarak yuklenemedi.</div>
                @endif
            </section>

            {{-- PRICING --}}
            <section class="lfv3-section lfv3-reveal" id="fiyat">
                <div class="lfv3-section__head">
                    <span class="lfv3-kicker">{{ $pageData['pricing_eyebrow'] }}</span>
                    <h2>{{ $pageData['pricing_title'] }}</h2>
                    <p>{{ $pageData['pricing_lead'] }}</p>
                </div>

                <div class="lfv3-pricing">
                    @foreach ($pageData['packages'] as $package)
                        <article class="lfv3-card lfv3-pricing-card @if (!empty($package['featured'])) lfv3-pricing-card--featured @endif">
                            @if (!empty($package['featured']))
                                <span class="lfv3-badge">Onerilen Paket</span>
                            @endif
                            <strong class="lfv3-pricing-name">{{ $package['name'] }}</strong>
                            <h3 class="lfv3-pricing-price">{{ $package['price'] }}</h3>
                            <span class="lfv3-pricing-unit">{{ $package['unit'] }}</span>
                            <p class="lfv3-pricing-note">{{ $package['note'] }}</p>
                            <a class="lfv3-btn lfv3-btn--full" href="{{ $applyUrl }}">Basvur</a>
                        </article>
                    @endforeach
                </div>

                @if (!empty($pricingNotes))
                    <div class="lfv3-grid2 lfv3-notes">
                        @foreach ($pricingNotes as $note)
                            <div class="lfv3-note">{{ $note }}</div>
                        @endforeach
                    </div>
                @endif
            </section>

            {{-- FAQ --}}
            <section class="lfv3-section lfv3-reveal" id="sss">
                <div class="lfv3-section__head">
                    <span class="lfv3-kicker">SSS</span>
                    <h2>Karar oncesi en cok sorulanlar</h2>
                </div>
                <div class="lfv3-faq">
                    @foreach ($pageData['faq'] as $faq)
                        <details class="lfv3-faq-item">
                            <summary>{{ $faq['question'] }}</summary>
                            <p>{{ $faq['answer'] }}</p>
                        </details>
                    @endforeach
                </div>
            </section>

            {{-- CTA --}}
            <section class="lfv3-cta lfv3-reveal">
                <div class="lfv3-cta__copy">
                    <span class="lfv3-kicker">Son adim</span>
                    <h2>{{ $pageData['cta_title'] }}</h2>
                    <p>{{ $pageData['cta_text'] }}</p>
                </div>
                <div class="lfv3-cta__actions">
                    <a class="lfv3-btn" href="{{ $applyUrl }}">Programa Basvur</a>
                    <a class="lfv3-btn lfv3-btn--ghost" href="{{ $testUrl }}">Seviye Tespiti</a>
                </div>
            </section>

            <footer class="lfv3-footer lfv3-reveal">
                <div>{{ $siteName }} | LinguFranca Performans Sistemi</div>
                <div class="lfv3-footer-links">
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
    @if (!empty($faqEntities))
        <script type="application/ld+json">{!! json_encode([
            '@context' => 'https://schema.org',
            '@type' => 'FAQPage',
            'mainEntity' => $faqEntities,
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}</script>
    @endif
@endpush

@push('styles')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&family=Sora:wght@600;700;800&display=swap" rel="stylesheet">
    <style>
        .lfv3 {
            background:
                radial-gradient(circle at 0% 0%, rgba(96, 165, 250, 0.22), transparent 35%),
                radial-gradient(circle at 90% 10%, rgba(34, 211, 238, 0.12), transparent 28%),
                linear-gradient(180deg, #050814 0%, #070b16 45%, #050814 100%);
            color: #eaf1ff;
            padding: 18px 0 70px;
        }

        .lfv3-container {
            width: min(1200px, calc(100vw - 28px));
            margin: 0 auto;
        }

        .lfv3-topbar {
            position: sticky;
            top: 10px;
            z-index: 60;
            display: grid;
            grid-template-columns: auto 1fr auto;
            align-items: center;
            gap: 16px;
            padding: 12px 14px;
            border-radius: 16px;
            border: 1px solid rgba(255,255,255,.12);
            background: rgba(7, 11, 22, .72);
            backdrop-filter: blur(10px);
        }

        .lfv3-brand img {
            height: 34px;
            width: auto;
            filter: brightness(0) invert(1);
        }

        .lfv3-nav {
            display: flex;
            justify-content: center;
            gap: 14px;
            flex-wrap: wrap;
        }

        .lfv3-nav a {
            color: rgba(234, 241, 255, 0.78);
            text-decoration: none;
            font-weight: 800;
            font-size: 13px;
        }

        .lfv3-nav a:hover {
            color: #ffffff;
        }

        .lfv3-topbar__actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            justify-content: flex-end;
        }

        .lfv3-btn {
            min-height: 44px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0 18px;
            border-radius: 999px;
            border: 1px solid transparent;
            background: linear-gradient(135deg, #60a5fa 0%, #2563eb 100%);
            color: #061025;
            font-weight: 950;
            font-size: 11px;
            letter-spacing: .10em;
            text-transform: uppercase;
            text-decoration: none;
            transition: transform .18s ease;
        }

        .lfv3-btn:hover {
            transform: translateY(-1px);
        }

        .lfv3-btn--ghost {
            background: transparent;
            border-color: rgba(255,255,255,.22);
            color: #eaf1ff;
        }

        .lfv3-btn--full {
            width: 100%;
            justify-content: center;
        }

        .lfv3-kicker {
            display: inline-flex;
            align-items: center;
            min-height: 30px;
            padding: 0 12px;
            border-radius: 999px;
            border: 1px solid rgba(255,255,255,.16);
            background: rgba(255,255,255,.06);
            color: rgba(234,241,255,.95);
            font-weight: 900;
            font-size: 10px;
            letter-spacing: .12em;
            text-transform: uppercase;
        }

        .lfv3-hero {
            display: grid;
            grid-template-columns: minmax(0, 1.08fr) minmax(340px, .92fr);
            gap: 26px;
            padding-top: 26px;
            align-items: start;
        }

        .lfv3-hero__copy h1 {
            font-family: Sora, Inter, sans-serif;
            font-size: clamp(42px, 5.0vw, 74px);
            line-height: .98;
            margin: 12px 0 0;
            letter-spacing: -0.03em;
        }

        .lfv3-lead {
            margin: 14px 0 0;
            color: rgba(234,241,255,.78);
            font-weight: 650;
            font-size: 16px;
            line-height: 1.9;
            max-width: 68ch;
        }

        .lfv3-chips {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 16px;
        }

        .lfv3-chip {
            min-height: 34px;
            display: inline-flex;
            align-items: center;
            padding: 0 12px;
            border-radius: 999px;
            border: 1px solid rgba(255,255,255,.18);
            background: rgba(255,255,255,.05);
            color: rgba(234,241,255,.92);
            font-weight: 800;
            font-size: 12px;
        }

        .lfv3-chip--soft {
            background: rgba(96,165,250,.10);
            border-color: rgba(96,165,250,.25);
        }

        .lfv3-hero__cta {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
            margin-top: 22px;
        }

        .lfv3-metrics {
            display: grid;
            grid-template-columns: repeat(4, minmax(0,1fr));
            gap: 12px;
            margin-top: 22px;
        }

        .lfv3-metric {
            border-radius: 18px;
            padding: 14px 14px 16px;
            border: 1px solid rgba(255,255,255,.12);
            background: rgba(255,255,255,.04);
        }

        .lfv3-metric strong {
            display: block;
            font-size: 28px;
            font-weight: 950;
        }

        .lfv3-metric span {
            display: block;
            margin-top: 6px;
            color: rgba(234,241,255,.72);
            font-size: 11px;
            letter-spacing: .08em;
            text-transform: uppercase;
            font-weight: 850;
        }

        .lfv3-hero__panel {
            border-radius: 22px;
            overflow: hidden;
            border: 1px solid rgba(255,255,255,.12);
            background: rgba(255,255,255,.03);
        }

        .lfv3-hero__image {
            width: 100%;
            height: auto;
            display: block;
            object-fit: cover;
            min-height: 320px;
        }

        .lfv3-quote {
            padding: 16px 18px;
            border-top: 1px solid rgba(255,255,255,.10);
            background: rgba(0,0,0,.12);
        }

        .lfv3-quote__tag {
            display: block;
            font-size: 10px;
            letter-spacing: .12em;
            text-transform: uppercase;
            font-weight: 900;
            color: rgba(234,241,255,.92);
        }

        .lfv3-quote__text {
            margin-top: 10px;
            color: rgba(234,241,255,.82);
            font-weight: 650;
            line-height: 1.8;
        }

        .lfv3-press {
            margin-top: 18px;
            border-radius: 22px;
            border: 1px solid rgba(255,255,255,.12);
            background: rgba(255,255,255,.03);
        }

        .lfv3-press__inner {
            padding: 16px 16px;
            display: grid;
            grid-template-columns: 1fr;
            gap: 10px;
        }

        .lfv3-press__label {
            color: rgba(234,241,255,.78);
            font-weight: 800;
            font-size: 12px;
            letter-spacing: .08em;
            text-transform: uppercase;
        }

        .lfv3-section {
            margin-top: 70px;
        }

        .lfv3-section__head h2 {
            font-family: Sora, Inter, sans-serif;
            font-size: clamp(30px, 3.4vw, 52px);
            letter-spacing: -0.03em;
            margin: 14px 0 0;
            line-height: 1.05;
        }

        .lfv3-section__head p {
            margin-top: 12px;
            color: rgba(234,241,255,.76);
            font-weight: 650;
            line-height: 1.85;
            max-width: 78ch;
        }

        .lfv3-grid2, .lfv3-grid3, .lfv3-grid4 {
            display: grid;
            gap: 14px;
            margin-top: 18px;
        }

        .lfv3-grid2 { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        .lfv3-grid3 { grid-template-columns: repeat(3, minmax(0, 1fr)); }
        .lfv3-grid4 { grid-template-columns: repeat(4, minmax(0, 1fr)); }

        .lfv3-card {
            border-radius: 20px;
            padding: 22px;
            border: 1px solid rgba(255,255,255,.12);
            background: rgba(255,255,255,.04);
            box-shadow: 0 18px 60px rgba(0,0,0,.18);
        }

        .lfv3-card:hover {
            border-color: rgba(96,165,250,.35);
        }

        .lfv3-card--muted {
            background: rgba(37, 99, 235, .06);
            border-color: rgba(37, 99, 235, .22);
        }

        .lfv3-card__index {
            display: inline-block;
            font-size: 44px;
            color: rgba(234,241,255,.24);
            font-weight: 950;
            margin-bottom: 12px;
            line-height: 1;
        }

        .lfv3-card h3 {
            font-family: Sora, Inter, sans-serif;
            margin: 0;
            font-size: 22px;
            letter-spacing: -0.02em;
        }

        .lfv3-card p {
            margin-top: 10px;
            color: rgba(234,241,255,.76);
            line-height: 1.85;
            font-weight: 650;
        }

        .lfv3-list {
            list-style: none;
            padding: 0;
            margin: 14px 0 0;
            display: grid;
            gap: 10px;
        }

        .lfv3-list li {
            position: relative;
            padding-left: 16px;
            color: rgba(234,241,255,.78);
            font-weight: 650;
            line-height: 1.75;
        }

        .lfv3-list li::before {
            content: "";
            position: absolute;
            left: 0;
            top: 10px;
            width: 7px;
            height: 7px;
            border-radius: 999px;
            background: rgba(96,165,250,.85);
        }

        .lfv3-pill {
            display: inline-flex;
            align-items: center;
            min-height: 30px;
            padding: 0 12px;
            border-radius: 999px;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: .10em;
            font-weight: 900;
            border: 1px solid rgba(255,255,255,.16);
            background: rgba(255,255,255,.05);
        }

        .lfv3-pill--soft {
            background: rgba(96,165,250,.12);
            border-color: rgba(96,165,250,.26);
        }

        .lfv3-programs {
            margin-top: 18px;
            display: grid;
            gap: 16px;
        }

        .lfv3-program {
            border-radius: 22px;
            overflow: hidden;
            border: 1px solid rgba(255,255,255,.12);
            background: rgba(255,255,255,.03);
            display: grid;
            grid-template-columns: minmax(260px, .75fr) minmax(0, .25fr);
            min-height: 260px;
        }

        .lfv3-program__img {
            width: 100%;
            height: 100%;
            min-height: 260px;
            object-fit: cover;
            display: block;
            background: rgba(255,255,255,.05);
        }

        .lfv3-program__body {
            padding: 22px;
            display: grid;
            gap: 10px;
            align-content: start;
        }

        .lfv3-program__body h3 {
            font-size: 24px;
        }

        .lfv3-program__body p {
            margin-top: 0;
        }

        .lfv3-program__footer {
            margin-top: 8px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
        }

        .lfv3-program__result {
            color: rgba(234,241,255,.92);
            font-weight: 950;
            font-size: 14px;
            letter-spacing: .02em;
        }

        .lfv3-inline-link {
            color: rgba(234,241,255,.92);
            text-decoration: none;
            font-weight: 900;
            font-size: 11px;
            letter-spacing: .10em;
            text-transform: uppercase;
            padding: 10px 0 0;
        }

        .lfv3-inline-link:hover {
            text-decoration: underline;
        }

        .lfv3-carousel {
            margin-top: 18px;
            border-radius: 22px;
            border: 1px solid rgba(255,255,255,.12);
            background: rgba(255,255,255,.03);
            overflow: hidden;
        }

        .lfv3-carousel__track {
            display: flex;
            transition: transform .55s cubic-bezier(.2,.8,.2,1);
        }

        .lfv3-carousel__slide {
            min-width: 100%;
            padding: 26px;
        }

        .lfv3-carousel__top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
        }

        .lfv3-carousel__highlight {
            font-size: 18px;
            font-weight: 950;
            color: #ffffff;
        }

        .lfv3-carousel__title {
            margin: 12px 0 0;
            font-family: Sora, Inter, sans-serif;
            font-size: 28px;
        }

        .lfv3-carousel__text {
            margin-top: 12px;
            color: rgba(234,241,255,.78);
            line-height: 1.85;
            max-width: 76ch;
            font-weight: 650;
        }

        .lfv3-carousel__dots {
            padding: 0 26px 22px;
            display: flex;
            gap: 10px;
        }

        .lfv3-carousel__dot {
            width: 10px;
            height: 10px;
            border-radius: 999px;
            border: 0;
            background: rgba(255,255,255,.25);
            cursor: pointer;
            transition: width .2s ease, background .2s ease;
        }

        .lfv3-carousel__dot.is-active {
            width: 30px;
            background: #ffffff;
        }

        .lfv3-reason-grid {
            align-items: start;
        }

        .lfv3-subgrid2 {
            margin-top: 14px;
            display: grid;
            grid-template-columns: repeat(2, minmax(0,1fr));
            gap: 14px;
        }

        .lfv3-subcard {
            border-radius: 16px;
            padding: 14px;
            border: 1px solid rgba(255,255,255,.10);
            background: rgba(255,255,255,.04);
        }

        .lfv3-subcard strong {
            display: block;
            font-size: 14px;
            font-weight: 950;
            color: rgba(234,241,255,.92);
            text-transform: uppercase;
            letter-spacing: .03em;
        }

        .lfv3-list--tight {
            gap: 8px;
            margin-top: 10px;
        }

        .lfv3-video-layout {
            align-items: start;
        }

        .lfv3-video-feature, .lfv3-video-card {
            padding: 0;
            overflow: hidden;
        }

        .lfv3-video {
            width: 100%;
            height: 100%;
            aspect-ratio: 16 / 10;
            background: #000;
            display: block;
        }

        .lfv3-video-meta {
            padding: 18px 18px 22px;
        }

        .lfv3-video-grid {
            margin-top: 0;
        }

        .lfv3-video-card .lfv3-video {
            aspect-ratio: 16 / 10;
        }

        .lfv3-empty {
            padding: 26px;
            color: rgba(234,241,255,.78);
            font-weight: 700;
        }

        .lfv3-pricing {
            margin-top: 18px;
            display: grid;
            grid-template-columns: repeat(2, minmax(0,1fr));
            gap: 14px;
        }

        .lfv3-pricing-card {
            position: relative;
            padding: 26px;
        }

        .lfv3-pricing-card--featured {
            background: rgba(96,165,250,.16);
            border-color: rgba(96,165,250,.35);
        }

        .lfv3-badge {
            position: absolute;
            top: 14px;
            right: 14px;
            padding: 10px 12px;
            border-radius: 999px;
            border: 1px solid rgba(255,255,255,.18);
            background: rgba(255,255,255,.08);
            font-weight: 950;
            font-size: 10px;
            letter-spacing: .12em;
            text-transform: uppercase;
        }

        .lfv3-pricing-name {
            font-weight: 950;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: .08em;
        }

        .lfv3-pricing-price {
            margin-top: 10px;
            font-family: Sora, Inter, sans-serif;
            font-size: 44px;
            line-height: 1;
        }

        .lfv3-pricing-unit {
            display: block;
            margin-top: 6px;
            color: rgba(234,241,255,.72);
            font-weight: 750;
            font-size: 12px;
        }

        .lfv3-pricing-note {
            margin-top: 14px;
            color: rgba(234,241,255,.78);
            line-height: 1.85;
            font-weight: 650;
        }

        .lfv3-notes {
            margin-top: 14px;
        }

        .lfv3-note {
            border-radius: 18px;
            padding: 16px;
            border: 1px solid rgba(255,255,255,.10);
            background: rgba(255,255,255,.03);
            color: rgba(234,241,255,.78);
            font-weight: 650;
            line-height: 1.7;
        }

        .lfv3-faq {
            margin-top: 18px;
            display: grid;
            gap: 12px;
        }

        .lfv3-faq-item {
            border-radius: 20px;
            border: 1px solid rgba(255,255,255,.12);
            background: rgba(255,255,255,.03);
            padding: 18px 20px;
        }

        .lfv3-faq-item summary {
            cursor: pointer;
            font-weight: 950;
            font-size: 20px;
            line-height: 1.35;
        }

        .lfv3-faq-item summary::-webkit-details-marker { display: none; }

        .lfv3-faq-item p {
            margin-top: 12px;
            color: rgba(234,241,255,.78);
            line-height: 1.85;
            font-weight: 650;
        }

        .lfv3-cta {
            margin-top: 70px;
            padding: 26px;
            border-radius: 22px;
            border: 1px solid rgba(255,255,255,.12);
            background: rgba(255,255,255,.03);
            display: flex;
            justify-content: space-between;
            align-items: end;
            gap: 18px;
            flex-wrap: wrap;
        }

        .lfv3-cta h2 {
            font-family: Sora, Inter, sans-serif;
            font-size: clamp(30px, 3.8vw, 52px);
            margin-top: 14px;
            line-height: 1.05;
        }

        .lfv3-cta p {
            margin-top: 12px;
            color: rgba(234,241,255,.78);
            line-height: 1.85;
            font-weight: 650;
            max-width: 64ch;
        }

        .lfv3-cta__actions {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }

        .lfv3-footer {
            margin-top: 22px;
            padding-top: 18px;
            border-top: 1px solid rgba(255,255,255,.12);
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
            color: rgba(234,241,255,.72);
            text-transform: uppercase;
            letter-spacing: .08em;
            font-weight: 850;
            font-size: 12px;
        }

        .lfv3-footer-links {
            display: flex;
            gap: 14px;
            flex-wrap: wrap;
        }

        .lfv3-footer-links a {
            color: rgba(234,241,255,.72);
            text-decoration: none;
        }

        .lfv3-footer-links a:hover {
            color: #fff;
            text-decoration: underline;
        }

        .lfv3-reveal {
            opacity: 0;
            transform: translateY(18px);
            transition: opacity .65s ease, transform .65s ease;
        }

        .lfv3-reveal.is-visible {
            opacity: 1;
            transform: translateY(0);
        }

        @media (max-width: 1100px) {
            .lfv3-hero { grid-template-columns: 1fr; }
            .lfv3-metrics { grid-template-columns: repeat(2, minmax(0,1fr)); }
            .lfv3-grid4 { grid-template-columns: repeat(2, minmax(0,1fr)); }
            .lfv3-grid3 { grid-template-columns: repeat(2, minmax(0,1fr)); }
            .lfv3-program { grid-template-columns: 1fr; }
            .lfv3-program__img { min-height: 220px; }
            .lfv3-pricing { grid-template-columns: 1fr; }
        }

        @media (max-width: 820px) {
            .lfv3-nav { display: none; }
            .lfv3-topbar { grid-template-columns: 1fr; }
            .lfv3-cta { align-items: flex-start; }
            .lfv3-grid2 { grid-template-columns: 1fr; }
            .lfv3-grid3 { grid-template-columns: 1fr; }
            .lfv3-grid4 { grid-template-columns: 1fr; }
        }
    </style>
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Scroll reveal
            const revealItems = document.querySelectorAll('.lfv3-reveal');
            if (revealItems.length) {
                const observer = new IntersectionObserver(function (entries, obs) {
                    entries.forEach(function (entry) {
                        if (entry.isIntersecting) {
                            entry.target.classList.add('is-visible');
                            obs.unobserve(entry.target);
                        }
                    });
                }, { threshold: 0.14 });
                revealItems.forEach(function (item) { observer.observe(item); });
            }

            // Carousel
            const car = document.querySelector('[data-lfv3-carousel]');
            if (!car) return;

            const track = car.querySelector('.lfv3-carousel__track');
            const slides = car.querySelectorAll('.lfv3-carousel__slide');
            const dots = car.querySelectorAll('[data-lfv3-dot]');

            if (!track || slides.length === 0) return;

            let active = 0;
            let timer = null;

            function paint() {
                track.style.transform = 'translateX(-' + (active * 100) + '%)';
                dots.forEach(function (dot, idx) {
                    dot.classList.toggle('is-active', idx === active);
                });
            }

            function go(next) {
                active = (next + slides.length) % slides.length;
                paint();
            }

            function start() {
                stop();
                timer = setInterval(function () { go(active + 1); }, 4300);
            }

            function stop() {
                if (timer) { clearInterval(timer); timer = null; }
            }

            dots.forEach(function (dot, idx) {
                dot.addEventListener('click', function () {
                    go(idx);
                    start();
                });
            });

            car.addEventListener('mouseenter', stop);
            car.addEventListener('mouseleave', start);

            paint();
            start();
        });
    </script>
@endpush

