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

    $resultSlides = collect($downloads)
        ->map(fn ($program) => [
            'title' => $program['title'] ?? '',
            'highlight' => $program['result'] ?? '',
            'text' => $program['subtitle'] ?? '',
            'tag' => $program['label'] ?? 'Program',
        ])
        ->filter(fn ($item) => filled($item['title']) && filled($item['text']))
        ->values()
        ->all();

    $tabs = [
        ['id' => 'about', 'label' => 'About'],
        ['id' => 'programlar', 'label' => 'Programlar'],
        ['id' => 'sonuclar', 'label' => 'Sonuclar'],
        ['id' => 'videolar', 'label' => 'Videolar'],
        ['id' => 'fiyat', 'label' => 'Fiyat'],
        ['id' => 'sss', 'label' => 'SSS'],
    ];
@endphp

@section('meta_title', $pageData['meta_title'] . ' | ' . $siteName)
@section('meta_description', $pageData['meta_description'])
@section('meta_keywords', $pageData['meta_keywords'])
@section('canonical_url', $canonicalUrl)
@section('meta_image', $pageData['meta_image_url'] ?? '')
@section('hide_public_header', '1')
@section('hide_public_footer', '1')

@section('contents')
    <section class="lfv4">
        <div class="lfv4-shell">
            <header class="lfv4-topbar">
                <a class="lfv4-logo" href="{{ $homeUrl }}">
                    @if (!empty($setting?->logo))
                        <img src="{{ asset($setting->logo) }}" alt="{{ $siteName }}" />
                    @else
                        <span>{{ $siteName }}</span>
                    @endif
                </a>
                <div class="lfv4-topbar__actions">
                    <a class="lfv4-btn lfv4-btn--ghost" href="{{ $testUrl }}">Seviye Tespiti</a>
                    <a class="lfv4-btn" href="{{ $applyUrl }}">Programa Basvur</a>
                </div>
            </header>

            <section class="lfv4-hero">
                <div class="lfv4-hero__meta">
                    <div class="lfv4-avatar" aria-hidden="true">
                        <span>{{ mb_substr((string) ($pageData['meta_title'] ?? 'L'), 0, 1) }}</span>
                    </div>
                    <div>
                        <div class="lfv4-subline">
                            <span class="lfv4-handle">@{{ Str::slug((string) ($siteName ?? 'lingufranca'), '-') }}</span>
                            <span class="lfv4-dot">·</span>
                            <span>{{ $pageData['eyebrow'] }}</span>
                        </div>
                        <h1 class="lfv4-title">{{ $pageData['meta_title'] }}</h1>
                        <p class="lfv4-lead">{{ $pageData['lead'] }}</p>

                        @if (!empty($heroBadges))
                            <div class="lfv4-badges">
                                @foreach ($heroBadges as $badge)
                                    <span class="lfv4-pill">{{ $badge }}</span>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

                @if (!empty($heroStats))
                    <div class="lfv4-stats" aria-label="Özet">
                        @foreach ($heroStats as $stat)
                            <div class="lfv4-stat">
                                <strong>{{ $stat['value'] }}</strong>
                                <span>{{ $stat['label'] }}</span>
                            </div>
                        @endforeach
                    </div>
                @endif
            </section>

            <nav class="lfv4-tabs" aria-label="Sections">
                <div class="lfv4-tabs__inner">
                    @foreach ($tabs as $tab)
                        <a class="lfv4-tab" href="#{{ $tab['id'] }}">{{ $tab['label'] }}</a>
                    @endforeach
                </div>
            </nav>

            <main class="lfv4-main">
                <section class="lfv4-section" id="about">
                    <div class="lfv4-grid">
                        <article class="lfv4-card lfv4-card--wide">
                            <h2>About</h2>
                            <p>{{ $pageData['manifesto_lead'] }}</p>
                            <div class="lfv4-mini">
                                @foreach ($pageData['press_badges'] as $badge)
                                    <span class="lfv4-pill lfv4-pill--soft">{{ $badge }}</span>
                                @endforeach
                            </div>
                        </article>
                        @foreach ($pageData['manifesto_points'] as $point)
                            <article class="lfv4-card">
                                <h3>{{ $point['title'] }}</h3>
                                <p>{{ $point['description'] }}</p>
                            </article>
                        @endforeach
                    </div>

                    <div class="lfv4-split">
                        <article class="lfv4-card">
                            <h3>Kimin icin</h3>
                            <ul class="lfv4-list">
                                @foreach ($pageData['fit_for'] as $item)
                                    <li>{{ $item }}</li>
                                @endforeach
                            </ul>
                        </article>
                        <article class="lfv4-card">
                            <h3>Kimin icin degil</h3>
                            <ul class="lfv4-list">
                                @foreach ($pageData['fit_not_for'] as $item)
                                    <li>{{ $item }}</li>
                                @endforeach
                            </ul>
                        </article>
                    </div>
                </section>

                <section class="lfv4-section" id="programlar">
                    <header class="lfv4-section__head">
                        <h2>{{ $pageData['resource_title'] }}</h2>
                        <p>{{ $pageData['resource_eyebrow'] }}</p>
                    </header>

                    <div class="lfv4-split">
                        @foreach ($pageData['resource_columns'] as $column)
                            <article class="lfv4-card">
                                <h3>{{ $column['label'] }}</h3>
                                <ul class="lfv4-list">
                                    @foreach ($column['items'] as $item)
                                        <li>{{ $item }}</li>
                                    @endforeach
                                </ul>
                            </article>
                        @endforeach
                    </div>

                    <div class="lfv4-programs">
                        @foreach ($downloads as $program)
                            <article class="lfv4-program">
                                @if (!empty($program['cover_url']))
                                    <div class="lfv4-program__media">
                                        <img src="{{ $program['cover_url'] }}" alt="{{ $program['title'] }}" loading="lazy" />
                                    </div>
                                @endif
                                <div class="lfv4-program__body">
                                    <div class="lfv4-subline">
                                        <span class="lfv4-pill lfv4-pill--soft">{{ $program['label'] }}</span>
                                        <span class="lfv4-dot">·</span>
                                        <span class="lfv4-muted">{{ $program['result'] }}</span>
                                    </div>
                                    <h3>{{ $program['title'] }}</h3>
                                    <p>{{ $program['subtitle'] }}</p>
                                    <ul class="lfv4-list">
                                        @foreach ($program['bullets'] as $bullet)
                                            <li>{{ $bullet }}</li>
                                        @endforeach
                                    </ul>
                                    <div class="lfv4-program__footer">
                                        @if (!empty($program['file_url']))
                                            <a class="lfv4-link" href="{{ $program['file_url'] }}" target="_blank" rel="noopener">Program Detayi</a>
                                        @endif
                                    </div>
                                </div>
                            </article>
                        @endforeach
                    </div>
                </section>

                <section class="lfv4-section" id="sonuclar">
                    <header class="lfv4-section__head">
                        <h2>Results</h2>
                        <p>{{ $pageData['reasons_title'] }}</p>
                    </header>

                    <div class="lfv4-carousel" data-lfv4-carousel>
                        <div class="lfv4-carousel__track">
                            @foreach ($resultSlides as $slide)
                                <article class="lfv4-carousel__slide">
                                    <div class="lfv4-subline">
                                        <span class="lfv4-pill lfv4-pill--soft">{{ $slide['tag'] }}</span>
                                        <span class="lfv4-dot">·</span>
                                        <span class="lfv4-muted">{{ $slide['highlight'] }}</span>
                                    </div>
                                    <h3>{{ $slide['title'] }}</h3>
                                    <p>{{ $slide['text'] }}</p>
                                </article>
                            @endforeach
                        </div>
                        <div class="lfv4-carousel__dots">
                            @foreach ($resultSlides as $slide)
                                <button type="button" class="lfv4-dotbtn" data-lfv4-dot aria-label="Slide {{ $loop->iteration }}"></button>
                            @endforeach
                        </div>
                    </div>
                </section>

                <section class="lfv4-section" id="videolar">
                    <header class="lfv4-section__head">
                        <h2>{{ $pageData['proof_title'] }}</h2>
                        <p>{{ $pageData['proof_lead'] }}</p>
                    </header>

                    @if (!empty($featuredMedia))
                        <div class="lfv4-video-layout">
                            <article class="lfv4-card lfv4-video lfv4-video--feature">
                                <video controls preload="metadata" playsinline @if (!empty($featuredMedia['poster_url'])) poster="{{ $featuredMedia['poster_url'] }}" @endif>
                                    <source src="{{ $featuredMedia['file_url'] }}" type="video/mp4">
                                </video>
                                <div class="lfv4-video__body">
                                    <div class="lfv4-subline">
                                        <span class="lfv4-pill lfv4-pill--soft">{{ $featuredMedia['category'] }}</span>
                                        <span class="lfv4-dot">·</span>
                                        <span class="lfv4-muted">{{ $featuredMedia['duration'] }}</span>
                                    </div>
                                    <h3>{{ $featuredMedia['title'] }}</h3>
                                    <p>{{ $featuredMedia['description'] }}</p>
                                    <a class="lfv4-link" href="{{ $featuredMedia['file_url'] }}" target="_blank" rel="noopener">Videoyu yeni sekmede ac</a>
                                </div>
                            </article>

                            <div class="lfv4-video-grid">
                                @foreach ($secondaryMedia as $item)
                                    <article class="lfv4-card lfv4-video">
                                        <video controls preload="metadata" playsinline @if (!empty($item['poster_url'])) poster="{{ $item['poster_url'] }}" @endif>
                                            <source src="{{ $item['file_url'] }}" type="video/mp4">
                                        </video>
                                        <div class="lfv4-video__body">
                                            <div class="lfv4-subline">
                                                <span class="lfv4-pill lfv4-pill--soft">{{ $item['category'] }}</span>
                                                <span class="lfv4-dot">·</span>
                                                <span class="lfv4-muted">{{ $item['duration'] }}</span>
                                            </div>
                                            <h3>{{ $item['title'] }}</h3>
                                            <p>{{ $item['description'] }}</p>
                                        </div>
                                    </article>
                                @endforeach
                            </div>
                        </div>
                    @else
                        <div class="lfv4-card">Video kayitlari gecici olarak yuklenemedi.</div>
                    @endif
                </section>

                <section class="lfv4-section" id="fiyat">
                    <header class="lfv4-section__head">
                        <h2>{{ $pageData['pricing_title'] }}</h2>
                        <p>{{ $pageData['pricing_lead'] }}</p>
                    </header>

                    <div class="lfv4-pricing">
                        @foreach ($pageData['packages'] as $package)
                            <article class="lfv4-card lfv4-price @if (!empty($package['featured'])) lfv4-price--featured @endif">
                                @if (!empty($package['featured']))
                                    <span class="lfv4-badge">Onerilen</span>
                                @endif
                                <strong class="lfv4-muted">{{ $package['name'] }}</strong>
                                <h3 class="lfv4-price__big">{{ $package['price'] }}</h3>
                                <p class="lfv4-muted">{{ $package['unit'] }}</p>
                                <p>{{ $package['note'] }}</p>
                                <a class="lfv4-btn lfv4-btn--block" href="{{ $applyUrl }}">Basvur</a>
                            </article>
                        @endforeach
                    </div>

                    @if (!empty($pricingNotes))
                        <div class="lfv4-notes">
                            @foreach ($pricingNotes as $note)
                                <div class="lfv4-note">{{ $note }}</div>
                            @endforeach
                        </div>
                    @endif
                </section>

                <section class="lfv4-section" id="sss">
                    <header class="lfv4-section__head">
                        <h2>SSS</h2>
                        <p>Karar oncesi en cok sorulanlar</p>
                    </header>
                    <div class="lfv4-faq">
                        @foreach ($pageData['faq'] as $faq)
                            <details class="lfv4-faq__item">
                                <summary>{{ $faq['question'] }}</summary>
                                <p>{{ $faq['answer'] }}</p>
                            </details>
                        @endforeach
                    </div>
                </section>
            </main>

            <footer class="lfv4-footer">
                <div>{{ $siteName }} | LinguFranca Performans Sistemi</div>
                <div class="lfv4-footer__links">
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
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        .lfv4 {
            --bg: #ffffff;
            --text: #0b1020;
            --muted: rgba(11, 16, 32, 0.62);
            --border: rgba(11, 16, 32, 0.10);
            --soft: rgba(11, 16, 32, 0.04);
            --brand: #0b1020;
            --accent: #2563eb;
            background: var(--bg);
            color: var(--text);
            font-family: Inter, system-ui, -apple-system, Segoe UI, sans-serif;
            padding: 18px 0 60px;
        }

        .lfv4-shell {
            width: min(1100px, calc(100vw - 28px));
            margin: 0 auto;
        }

        .lfv4-topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 14px;
            padding: 10px 0 14px;
        }

        .lfv4-logo {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
            color: var(--text);
            font-weight: 900;
        }

        .lfv4-logo img {
            height: 34px;
            width: auto;
        }

        .lfv4-topbar__actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            justify-content: flex-end;
        }

        .lfv4-btn {
            min-height: 40px;
            padding: 0 14px;
            border-radius: 999px;
            border: 1px solid var(--border);
            background: var(--brand);
            color: #fff;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: 900;
            font-size: 12px;
            letter-spacing: 0.02em;
        }

        .lfv4-btn--ghost {
            background: transparent;
            color: var(--text);
        }

        .lfv4-btn--block {
            width: 100%;
        }

        .lfv4-hero {
            border: 1px solid var(--border);
            background: linear-gradient(180deg, #ffffff 0%, #fbfbfd 100%);
            border-radius: 18px;
            padding: 18px;
        }

        .lfv4-hero__meta {
            display: grid;
            grid-template-columns: 54px 1fr;
            gap: 14px;
            align-items: start;
        }

        .lfv4-avatar {
            width: 54px;
            height: 54px;
            border-radius: 16px;
            border: 1px solid var(--border);
            background: var(--soft);
            display: grid;
            place-items: center;
            font-weight: 950;
            font-size: 20px;
        }

        .lfv4-subline {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: var(--muted);
            font-weight: 700;
            font-size: 13px;
            flex-wrap: wrap;
        }

        .lfv4-handle {
            color: var(--text);
            font-weight: 900;
        }

        .lfv4-dot {
            opacity: 0.5;
        }

        .lfv4-title {
            margin: 10px 0 0;
            font-size: clamp(28px, 3.2vw, 40px);
            line-height: 1.1;
            letter-spacing: -0.03em;
            font-weight: 950;
        }

        .lfv4-lead {
            margin: 12px 0 0;
            color: var(--muted);
            line-height: 1.75;
            font-weight: 600;
            max-width: 78ch;
        }

        .lfv4-badges {
            margin-top: 12px;
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .lfv4-pill {
            display: inline-flex;
            align-items: center;
            min-height: 32px;
            padding: 0 12px;
            border-radius: 999px;
            border: 1px solid var(--border);
            background: #fff;
            color: var(--text);
            font-weight: 800;
            font-size: 12px;
        }

        .lfv4-pill--soft {
            background: rgba(37, 99, 235, 0.06);
            border-color: rgba(37, 99, 235, 0.22);
            color: #0f2f88;
        }

        .lfv4-stats {
            margin-top: 14px;
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 10px;
        }

        .lfv4-stat {
            border: 1px solid var(--border);
            border-radius: 16px;
            background: #fff;
            padding: 12px;
        }

        .lfv4-stat strong {
            display: block;
            font-weight: 950;
            font-size: 22px;
        }

        .lfv4-stat span {
            display: block;
            margin-top: 6px;
            color: var(--muted);
            font-weight: 700;
            font-size: 12px;
        }

        .lfv4-tabs {
            position: sticky;
            top: 0;
            z-index: 10;
            background: rgba(255,255,255,0.92);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid var(--border);
            margin-top: 18px;
        }

        .lfv4-tabs__inner {
            display: flex;
            gap: 10px;
            overflow: auto;
            padding: 10px 0;
        }

        .lfv4-tab {
            white-space: nowrap;
            text-decoration: none;
            color: var(--muted);
            font-weight: 800;
            font-size: 13px;
            padding: 10px 12px;
            border-radius: 999px;
            border: 1px solid transparent;
        }

        .lfv4-tab.is-active {
            color: var(--text);
            border-color: var(--border);
            background: #fff;
        }

        .lfv4-main {
            margin-top: 18px;
            display: grid;
            gap: 30px;
        }

        .lfv4-section__head h2 {
            margin: 0;
            font-size: 22px;
            font-weight: 950;
            letter-spacing: -0.02em;
        }

        .lfv4-section__head p {
            margin: 8px 0 0;
            color: var(--muted);
            font-weight: 600;
        }

        .lfv4-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 12px;
        }

        .lfv4-card {
            border: 1px solid var(--border);
            border-radius: 18px;
            background: #fff;
            padding: 16px;
        }

        .lfv4-card--wide {
            grid-column: 1 / -1;
        }

        .lfv4-card h2, .lfv4-card h3 {
            margin: 0;
            font-weight: 950;
            letter-spacing: -0.02em;
        }

        .lfv4-card p {
            margin: 10px 0 0;
            color: var(--muted);
            line-height: 1.75;
            font-weight: 600;
        }

        .lfv4-mini {
            margin-top: 12px;
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .lfv4-split {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 12px;
            margin-top: 12px;
        }

        .lfv4-list {
            list-style: none;
            padding: 0;
            margin: 12px 0 0;
            display: grid;
            gap: 10px;
        }

        .lfv4-list li {
            position: relative;
            padding-left: 16px;
            color: var(--muted);
            line-height: 1.7;
            font-weight: 600;
        }

        .lfv4-list li::before {
            content: "";
            position: absolute;
            left: 0;
            top: 10px;
            width: 6px;
            height: 6px;
            border-radius: 999px;
            background: rgba(37, 99, 235, 0.9);
        }

        .lfv4-programs {
            margin-top: 12px;
            display: grid;
            gap: 12px;
        }

        .lfv4-program {
            border: 1px solid var(--border);
            border-radius: 18px;
            overflow: hidden;
            background: #fff;
            display: grid;
            grid-template-columns: minmax(240px, 0.75fr) minmax(0, 1.25fr);
        }

        .lfv4-program__media img {
            width: 100%;
            height: 100%;
            display: block;
            object-fit: cover;
            min-height: 220px;
        }

        .lfv4-program__body {
            padding: 16px;
            display: grid;
            gap: 10px;
            align-content: start;
        }

        .lfv4-program__body h3 {
            margin: 0;
            font-size: 18px;
            font-weight: 950;
            letter-spacing: -0.02em;
        }

        .lfv4-program__footer {
            margin-top: 6px;
        }

        .lfv4-link {
            color: #0f2f88;
            font-weight: 900;
            font-size: 12px;
            text-decoration: none;
        }

        .lfv4-link:hover { text-decoration: underline; }

        .lfv4-muted { color: var(--muted); }

        .lfv4-carousel {
            border: 1px solid var(--border);
            background: #fff;
            border-radius: 18px;
            overflow: hidden;
        }

        .lfv4-carousel__track {
            display: flex;
            transition: transform 0.55s cubic-bezier(.2,.8,.2,1);
        }

        .lfv4-carousel__slide {
            min-width: 100%;
            padding: 16px;
        }

        .lfv4-carousel__slide h3 { margin: 10px 0 0; font-weight: 950; }
        .lfv4-carousel__slide p { margin: 10px 0 0; color: var(--muted); line-height: 1.75; font-weight: 600; }

        .lfv4-carousel__dots {
            display: flex;
            gap: 8px;
            padding: 0 16px 14px;
        }

        .lfv4-dotbtn {
            width: 10px;
            height: 10px;
            border-radius: 999px;
            border: 0;
            background: rgba(11,16,32,0.22);
            cursor: pointer;
            transition: width 0.2s ease, background 0.2s ease;
        }

        .lfv4-dotbtn.is-active { width: 28px; background: var(--brand); }

        .lfv4-video-layout {
            display: grid;
            grid-template-columns: minmax(0, 1.2fr) minmax(0, 0.8fr);
            gap: 12px;
        }

        .lfv4-video-grid {
            display: grid;
            gap: 12px;
        }

        .lfv4-video { padding: 0; overflow: hidden; }
        .lfv4-video video { width: 100%; aspect-ratio: 16/10; display: block; background: #000; }
        .lfv4-video__body { padding: 14px 16px 16px; }
        .lfv4-video__body h3 { margin: 10px 0 0; font-weight: 950; }
        .lfv4-video__body p { margin: 10px 0 0; color: var(--muted); line-height: 1.7; font-weight: 600; }

        .lfv4-pricing {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 12px;
        }

        .lfv4-price { position: relative; }
        .lfv4-price--featured { border-color: rgba(37, 99, 235, 0.28); background: rgba(37, 99, 235, 0.04); }
        .lfv4-badge {
            position: absolute;
            top: 14px;
            right: 14px;
            background: #0b1020;
            color: #fff;
            font-size: 11px;
            font-weight: 900;
            padding: 8px 10px;
            border-radius: 999px;
        }

        .lfv4-price__big { margin: 10px 0 0; font-size: 34px; letter-spacing: -0.03em; }

        .lfv4-notes { margin-top: 12px; display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 10px; }
        .lfv4-note { border: 1px solid var(--border); border-radius: 16px; background: #fff; padding: 14px; color: var(--muted); font-weight: 600; line-height: 1.7; }

        .lfv4-faq { display: grid; gap: 10px; }
        .lfv4-faq__item { border: 1px solid var(--border); border-radius: 18px; background: #fff; padding: 14px 16px; }
        .lfv4-faq__item summary { cursor: pointer; font-weight: 950; list-style: none; }
        .lfv4-faq__item summary::-webkit-details-marker { display: none; }
        .lfv4-faq__item p { margin: 10px 0 0; color: var(--muted); line-height: 1.75; font-weight: 600; }

        .lfv4-footer {
            margin-top: 26px;
            padding-top: 16px;
            border-top: 1px solid var(--border);
            display: flex;
            justify-content: space-between;
            gap: 10px;
            flex-wrap: wrap;
            color: var(--muted);
            font-weight: 800;
            font-size: 12px;
            letter-spacing: 0.02em;
            text-transform: uppercase;
        }

        .lfv4-footer__links { display: flex; gap: 12px; flex-wrap: wrap; }
        .lfv4-footer__links a { color: var(--muted); text-decoration: none; }
        .lfv4-footer__links a:hover { color: var(--text); text-decoration: underline; }

        @media (max-width: 980px) {
            .lfv4-grid { grid-template-columns: 1fr; }
            .lfv4-stats { grid-template-columns: repeat(2, minmax(0, 1fr)); }
            .lfv4-split { grid-template-columns: 1fr; }
            .lfv4-program { grid-template-columns: 1fr; }
            .lfv4-video-layout { grid-template-columns: 1fr; }
            .lfv4-pricing { grid-template-columns: 1fr; }
            .lfv4-notes { grid-template-columns: 1fr; }
        }
    </style>
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Tabs active highlight (scroll spy)
            const tabs = Array.from(document.querySelectorAll('.lfv4-tab'));
            const sections = tabs.map(a => document.querySelector(a.getAttribute('href'))).filter(Boolean);
            if (tabs.length && sections.length) {
                const obs = new IntersectionObserver((entries) => {
                    const visible = entries.filter(e => e.isIntersecting).sort((a,b) => b.intersectionRatio - a.intersectionRatio)[0];
                    if (!visible) return;
                    const id = visible.target.id;
                    tabs.forEach(t => t.classList.toggle('is-active', t.getAttribute('href') === ('#' + id)));
                }, { rootMargin: '-20% 0px -70% 0px', threshold: [0.08, 0.14, 0.22] });
                sections.forEach(s => obs.observe(s));
            }

            // Results carousel
            const car = document.querySelector('[data-lfv4-carousel]');
            if (!car) return;
            const track = car.querySelector('.lfv4-carousel__track');
            const slides = car.querySelectorAll('.lfv4-carousel__slide');
            const dots = car.querySelectorAll('[data-lfv4-dot]');
            if (!track || !slides.length) return;

            let active = 0;
            let timer = null;
            function paint() {
                track.style.transform = 'translateX(-' + (active * 100) + '%)';
                dots.forEach((d, i) => d.classList.toggle('is-active', i === active));
            }
            function go(i) {
                active = (i + slides.length) % slides.length;
                paint();
            }
            function start() {
                stop();
                timer = setInterval(() => go(active + 1), 4200);
            }
            function stop() { if (timer) { clearInterval(timer); timer = null; } }
            dots.forEach((d, i) => d.addEventListener('click', () => { go(i); start(); }));
            car.addEventListener('mouseenter', stop);
            car.addEventListener('mouseleave', start);
            paint();
            start();
        });
    </script>
@endpush

