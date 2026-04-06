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
    $primaryProgram = $downloads[0] ?? null;
    $secondaryPrograms = array_slice($downloads, 1);
    $featuredMedia = $mediaLibrary[0] ?? null;
    $remainingMedia = array_slice($mediaLibrary, 1);
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
                    @if (!empty($primaryProgram))
                        <article class="lfps-hero-program">
                            <div class="lfps-hero-program__media">
                                @if (!empty($primaryProgram['cover_url']))
                                    <img src="{{ $primaryProgram['cover_url'] }}" alt="{{ $primaryProgram['title'] }}" loading="lazy" />
                                @endif
                            </div>
                            <div class="lfps-hero-program__body">
                                <span class="lfps-section-tag">{{ $primaryProgram['label'] }}</span>
                                <h2>{{ $primaryProgram['title'] }}</h2>
                                <p>{{ $primaryProgram['subtitle'] }}</p>
                                <div class="lfps-hero-program__meta">
                                    <span>{{ $primaryProgram['meta'] }}</span>
                                    <span>{{ $primaryProgram['result'] }}</span>
                                </div>
                            </div>
                        </article>
                    @endif

                    <div class="lfps-hero-side-grid">
                        <article class="lfps-quote-card">
                            <span class="lfps-section-tag">{{ $pageData['hero_quote_title'] }}</span>
                            <p>{{ $pageData['hero_quote'] }}</p>
                        </article>

                        @foreach ($secondaryPrograms as $program)
                            <article class="lfps-mini-program">
                                <span class="lfps-stack-card__label">{{ $program['label'] }}</span>
                                <h3>{{ $program['title'] }}</h3>
                                <p>{{ $program['result'] }}</p>
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
                <div class="lfps-split-block">
                    <div class="lfps-split-block__intro">
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
                </div>
            </section>

            <section class="lfps-section" id="programlar">
                <div class="lfps-section-head">
                    <span class="lfps-section-tag">{{ $pageData['resource_eyebrow'] }}</span>
                    <h2>{{ $pageData['resource_title'] }}</h2>
                </div>

                <div class="lfps-program-stage">
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

                @if (!empty($featuredMedia))
                    <div class="lfps-video-stage">
                        <article class="lfps-video-card lfps-video-card--feature">
                            <div class="lfps-video-card__media">
                                <video controls preload="metadata" playsinline @if (!empty($featuredMedia['poster_url'])) poster="{{ $featuredMedia['poster_url'] }}" @endif>
                                    <source src="{{ $featuredMedia['file_url'] }}" type="video/mp4">
                                    Tarayiciniz video etiketini desteklemiyor.
                                </video>
                            </div>
                            <div class="lfps-video-card__body">
                                <span class="lfps-video-card__meta">{{ $featuredMedia['category'] }} | {{ $featuredMedia['duration'] }}</span>
                                <h3>{{ $featuredMedia['title'] }}</h3>
                                <p>{{ $featuredMedia['description'] }}</p>
                                <a class="lfps-inline-link" href="{{ $featuredMedia['file_url'] }}" target="_blank" rel="noopener">Videoyu yeni sekmede ac</a>
                            </div>
                        </article>

                        <div class="lfps-video-grid">
                            @foreach ($remainingMedia as $item)
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
                            @endforeach
                        </div>
                    </div>
                @else
                    <div class="lfps-empty-card">
                        Video kayitlari gecici olarak yuklenemedi.
                    </div>
                @endif
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
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        .lfps-performance-shell {
            --lfps-navy: #06235b;
            --lfps-navy-soft: #0d367f;
            --lfps-accent: #6059f7;
            --lfps-accent-soft: #8f8aff;
            --lfps-slate: #545d70;
            --lfps-text: #12233f;
            --lfps-muted: #5e6b86;
            --lfps-white: #ffffff;
            position: relative;
            overflow: hidden;
            padding: 28px 0 72px;
            background: #f4f7fc;
            color: var(--lfps-text);
            font-family: "Plus Jakarta Sans", sans-serif;
        }

        .lfps-performance-shell::before {
            content: "";
            position: absolute;
            inset: 0 0 auto;
            height: 760px;
            background:
                radial-gradient(circle at 12% 18%, rgba(96, 89, 247, 0.34), transparent 22%),
                radial-gradient(circle at 86% 10%, rgba(106, 163, 255, 0.18), transparent 20%),
                linear-gradient(180deg, #041836 0%, #06235b 48%, #0c3276 100%);
            pointer-events: none;
        }

        .lfps-performance-shell * {
            box-sizing: border-box;
        }

        .lfps-performance-shell a {
            color: inherit;
            text-decoration: none;
        }

        .lfps-page {
            position: relative;
            z-index: 1;
            width: min(1180px, calc(100% - 32px));
            margin: 0 auto;
        }

        .lfps-topbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 18px;
            padding: 16px 20px;
            border: 1px solid rgba(255, 255, 255, 0.18);
            border-radius: 24px;
            background: rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(14px);
            box-shadow: 0 24px 56px rgba(4, 20, 54, 0.18);
        }

        .lfps-brand {
            display: inline-flex;
            align-items: center;
            flex-shrink: 0;
        }

        .lfps-brand img {
            display: block;
            max-height: 54px;
            width: auto;
        }

        .lfps-topbar__nav,
        .lfps-topbar__actions,
        .lfps-hero__actions,
        .lfps-cta-band__actions,
        .lfps-proof-strip__items,
        .lfps-chip-row,
        .lfps-footer__links,
        .lfps-reason-list {
            display: flex;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
        }

        .lfps-topbar__nav {
            justify-content: center;
        }

        .lfps-topbar__nav a {
            color: rgba(255, 255, 255, 0.88);
            font-size: 14px;
            font-weight: 600;
        }

        .lfps-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 48px;
            padding: 0 20px;
            border: 1px solid transparent;
            border-radius: 999px;
            background: linear-gradient(135deg, #6059f7 0%, #7f79ff 100%);
            color: #ffffff;
            font-size: 14px;
            font-weight: 700;
            box-shadow: 0 18px 34px rgba(96, 89, 247, 0.24);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .lfps-button:hover {
            transform: translateY(-1px);
            box-shadow: 0 22px 38px rgba(96, 89, 247, 0.3);
        }

        .lfps-button--ghost {
            border-color: rgba(255, 255, 255, 0.28);
            background: rgba(255, 255, 255, 0.08);
            color: #ffffff;
            box-shadow: none;
        }

        .lfps-hero {
            display: grid;
            grid-template-columns: minmax(0, 1.08fr) minmax(0, 0.92fr);
            gap: 28px;
            align-items: stretch;
            padding: 72px 0 48px;
        }

        .lfps-hero__copy,
        .lfps-hero__visual {
            display: flex;
            flex-direction: column;
        }

        .lfps-hero__visual {
            gap: 18px;
        }

        .lfps-hero-program {
            display: grid;
            grid-template-columns: minmax(220px, 0.92fr) minmax(0, 1.08fr);
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.16);
            border-radius: 30px;
            background: rgba(255, 255, 255, 0.1);
            box-shadow: 0 30px 60px rgba(7, 26, 67, 0.18);
        }

        .lfps-hero-program__media img {
            display: block;
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .lfps-hero-program__body {
            padding: 28px;
        }

        .lfps-hero-program__body h2 {
            margin: 12px 0 0;
            color: #ffffff;
            font-size: 30px;
            line-height: 1.15;
            font-weight: 800;
        }

        .lfps-hero-program__body p {
            margin: 14px 0 0;
            color: rgba(255, 255, 255, 0.84);
            line-height: 1.8;
        }

        .lfps-hero-program__meta {
            display: grid;
            gap: 8px;
            margin-top: 18px;
            color: rgba(255, 255, 255, 0.76);
            font-size: 14px;
            font-weight: 600;
        }

        .lfps-hero-side-grid {
            display: grid;
            grid-template-columns: 1.2fr 1fr 1fr;
            gap: 18px;
        }

        .lfps-mini-program {
            padding: 22px;
            border: 1px solid rgba(255, 255, 255, 0.14);
            border-radius: 24px;
            background: rgba(255, 255, 255, 0.08);
            box-shadow: 0 30px 60px rgba(7, 26, 67, 0.18);
        }

        .lfps-mini-program h3 {
            margin: 0;
            color: #ffffff;
            font-size: 18px;
            line-height: 1.35;
            font-weight: 700;
        }

        .lfps-mini-program p {
            margin: 10px 0 0;
            color: rgba(255, 255, 255, 0.76);
            line-height: 1.7;
            font-size: 14px;
        }

        .lfps-kicker,
        .lfps-section-tag,
        .lfps-fit-card__tag,
        .lfps-price-card__badge {
            display: inline-flex;
            width: fit-content;
            padding: 8px 14px;
            border: 1px solid rgba(255, 255, 255, 0.18);
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.1);
            color: #ffffff;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.12em;
            text-transform: uppercase;
        }

        .lfps-hero h1 {
            margin: 18px 0 0;
            color: #ffffff;
            font-size: clamp(36px, 5vw, 64px);
            line-height: 1.02;
            font-weight: 800;
            letter-spacing: -0.04em;
        }

        .lfps-lead,
        .lfps-hero .lfps-quote-card p,
        .lfps-proof-strip > span,
        .lfps-proof-strip__items span,
        .lfps-chip,
        .lfps-stack-card__body p,
        .lfps-stack-card__body small,
        .lfps-stat-card span {
            color: rgba(255, 255, 255, 0.82);
        }

        .lfps-lead {
            margin: 18px 0 0;
            font-size: 18px;
            line-height: 1.8;
            max-width: 720px;
        }

        .lfps-chip-row {
            margin-top: 28px;
        }

        .lfps-chip {
            padding: 10px 14px;
            border: 1px solid rgba(255, 255, 255, 0.14);
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.08);
            font-size: 13px;
            font-weight: 600;
        }

        .lfps-hero__actions {
            margin-top: 30px;
        }

        .lfps-stat-grid,
        .lfps-value-grid,
        .lfps-resource-grid,
        .lfps-step-grid,
        .lfps-insight-grid,
        .lfps-pricing-grid,
        .lfps-video-grid,
        .lfps-note-grid,
        .lfps-mini-grid,
        .lfps-fit-grid,
        .lfps-program-showcase,
        .lfps-stack-grid {
            display: grid;
            gap: 18px;
        }

        .lfps-stat-grid {
            grid-template-columns: repeat(4, minmax(0, 1fr));
            margin-top: 32px;
        }

        .lfps-stat-card,
        .lfps-quote-card,
        .lfps-stack-card,
        .lfps-proof-strip,
        .lfps-price-card,
        .lfps-cta-band {
            border: 1px solid rgba(255, 255, 255, 0.14);
            border-radius: 28px;
            background: rgba(255, 255, 255, 0.08);
            box-shadow: 0 30px 60px rgba(7, 26, 67, 0.18);
        }

        .lfps-stat-card {
            padding: 18px;
        }

        .lfps-stat-card strong {
            display: block;
            color: #ffffff;
            font-size: 24px;
            font-weight: 800;
        }

        .lfps-quote-card {
            padding: 24px;
        }

        .lfps-stack-card {
            display: grid;
            grid-template-columns: 160px minmax(0, 1fr);
            overflow: hidden;
        }

        .lfps-stack-card__cover {
            min-height: 180px;
            background-size: cover;
            background-position: center;
        }

        .lfps-stack-card__body {
            padding: 24px;
        }

        .lfps-stack-card__label {
            display: inline-block;
            margin-bottom: 8px;
            color: #ffffff;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 0.1em;
            text-transform: uppercase;
        }

        .lfps-stack-card__body h2 {
            margin: 0;
            color: #ffffff;
            font-size: 24px;
            line-height: 1.2;
            font-weight: 700;
        }

        .lfps-proof-strip {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 18px;
            padding: 20px 24px;
            margin-top: 10px;
        }

        .lfps-proof-strip > span {
            font-size: 16px;
            font-weight: 700;
        }

        .lfps-section {
            padding-top: 84px;
        }

        .lfps-split-block {
            display: grid;
            grid-template-columns: minmax(0, 0.9fr) minmax(0, 1.1fr);
            gap: 24px;
            align-items: start;
        }

        .lfps-split-block__intro {
            position: sticky;
            top: 24px;
        }

        .lfps-split-block__intro h2 {
            margin: 18px 0 0;
            color: #0e2450;
            font-size: clamp(28px, 3vw, 42px);
            line-height: 1.1;
            font-weight: 800;
        }

        .lfps-split-block__intro p {
            margin: 16px 0 0;
            color: var(--lfps-muted);
            line-height: 1.8;
        }

        .lfps-section-head {
            max-width: 760px;
            margin: 0 auto 28px;
            text-align: center;
        }

        .lfps-section-head--left {
            margin-left: 0;
            margin-right: 0;
            text-align: left;
        }

        .lfps-section-head .lfps-section-tag,
        .lfps-insight-card .lfps-section-tag,
        .lfps-cta-band .lfps-section-tag,
        .lfps-fit-card__tag,
        .lfps-price-card__badge {
            border-color: rgba(96, 89, 247, 0.16);
            background: rgba(96, 89, 247, 0.08);
            color: #6059f7;
        }

        .lfps-section-head h2,
        .lfps-insight-card h2,
        .lfps-cta-band h2 {
            margin: 18px 0 0;
            color: #0e2450;
            font-size: clamp(28px, 3vw, 42px);
            line-height: 1.1;
            font-weight: 800;
            letter-spacing: -0.03em;
        }

        .lfps-section-head p,
        .lfps-insight-card p,
        .lfps-cta-band p {
            margin: 16px 0 0;
            color: var(--lfps-muted);
            font-size: 16px;
            line-height: 1.8;
        }

        .lfps-value-grid,
        .lfps-step-grid {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }

        .lfps-fit-grid,
        .lfps-insight-grid,
        .lfps-resource-grid,
        .lfps-pricing-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .lfps-program-stage {
            display: grid;
            gap: 28px;
        }

        .lfps-video-grid,
        .lfps-note-grid,
        .lfps-mini-grid {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }

        .lfps-video-stage {
            display: grid;
            gap: 18px;
        }

        .lfps-value-card,
        .lfps-fit-card,
        .lfps-resource-card,
        .lfps-step-card,
        .lfps-insight-card,
        .lfps-note-card,
        .lfps-video-card,
        .lfps-program-panel,
        .lfps-faq-item {
            border: 1px solid rgba(9, 41, 102, 0.08);
            border-radius: 28px;
            background: #ffffff;
            box-shadow: 0 24px 50px rgba(17, 35, 70, 0.08);
        }

        .lfps-value-card,
        .lfps-fit-card,
        .lfps-resource-card,
        .lfps-step-card,
        .lfps-insight-card,
        .lfps-note-card,
        .lfps-price-card,
        .lfps-video-card__body,
        .lfps-program-panel__body {
            padding: 28px;
        }

        .lfps-value-card__index,
        .lfps-step-card__index {
            display: inline-flex;
            margin-bottom: 14px;
            color: #6059f7;
            font-size: 14px;
            font-weight: 800;
            letter-spacing: 0.14em;
        }

        .lfps-value-card h3,
        .lfps-fit-card h3,
        .lfps-resource-card h3,
        .lfps-step-card h3,
        .lfps-video-card h3,
        .lfps-program-panel__body h3,
        .lfps-price-card h3 {
            margin: 0;
            color: #0e2450;
            font-size: 24px;
            line-height: 1.25;
            font-weight: 700;
        }

        .lfps-value-card p,
        .lfps-fit-card li,
        .lfps-resource-card li,
        .lfps-step-card p,
        .lfps-video-card p,
        .lfps-program-panel__body p,
        .lfps-program-panel__body li,
        .lfps-note-card,
        .lfps-mini-card li,
        .lfps-price-card p,
        .lfps-price-card span,
        .lfps-faq-item p {
            color: var(--lfps-muted);
            line-height: 1.8;
        }

        .lfps-fit-card ul,
        .lfps-resource-card ul,
        .lfps-program-panel__body ul,
        .lfps-mini-card ul {
            margin: 16px 0 0;
            padding-left: 18px;
        }

        .lfps-fit-card--muted,
        .lfps-cta-band,
        .lfps-price-card {
            background: linear-gradient(180deg, #0a2d6b 0%, #06235b 100%);
            border-color: rgba(6, 35, 91, 0.08);
        }

        .lfps-fit-card--muted li,
        .lfps-fit-card--muted .lfps-fit-card__tag,
        .lfps-cta-band h2,
        .lfps-cta-band p,
        .lfps-cta-band .lfps-section-tag,
        .lfps-price-card strong,
        .lfps-price-card h3,
        .lfps-price-card p,
        .lfps-price-card span {
            color: #ffffff;
        }

        .lfps-price-card--featured {
            background: linear-gradient(135deg, #6059f7 0%, #3c5fe4 100%);
        }

        .lfps-program-panel {
            display: grid;
            grid-template-columns: minmax(260px, 0.86fr) minmax(0, 1.14fr);
            overflow: hidden;
        }

        .lfps-program-panel__media img {
            display: block;
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .lfps-program-panel__body {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .lfps-program-panel__footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            flex-wrap: wrap;
            margin-top: auto;
            padding-top: 8px;
            color: #0e2450;
            font-weight: 700;
        }

        .lfps-inline-link {
            color: #6059f7;
            font-weight: 700;
        }

        .lfps-mini-card {
            padding: 22px;
            border: 1px solid rgba(9, 41, 102, 0.08);
            border-radius: 22px;
            background: #f7f9ff;
        }

        .lfps-mini-card strong,
        .lfps-video-card__meta {
            color: #0e2450;
        }

        .lfps-reason-list span {
            display: inline-flex;
            padding: 12px 14px;
            border-radius: 16px;
            background: #f0f3ff;
            color: #3f4f6e;
            font-size: 14px;
            font-weight: 600;
        }

        .lfps-video-card {
            overflow: hidden;
        }

        .lfps-video-card--feature {
            display: grid;
            grid-template-columns: minmax(0, 1.15fr) minmax(320px, 0.85fr);
            align-items: stretch;
        }

        .lfps-video-card--feature .lfps-video-card__media video {
            height: 100%;
            min-height: 100%;
            aspect-ratio: auto;
        }

        .lfps-video-card__media video {
            display: block;
            width: 100%;
            aspect-ratio: 16 / 10;
            background: #0d1933;
        }

        .lfps-video-card__meta {
            display: inline-block;
            margin-bottom: 8px;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 0.12em;
            text-transform: uppercase;
        }

        .lfps-empty-card {
            padding: 26px;
            border: 1px solid rgba(9, 41, 102, 0.08);
            border-radius: 24px;
            background: #ffffff;
            color: var(--lfps-muted);
            text-align: center;
        }

        .lfps-faq-item {
            overflow: hidden;
        }

        .lfps-faq-item summary {
            cursor: pointer;
            list-style: none;
            padding: 22px 24px;
            color: #0e2450;
            font-size: 18px;
            font-weight: 700;
        }

        .lfps-faq-item summary::-webkit-details-marker {
            display: none;
        }

        .lfps-faq-item p {
            margin: 0;
            padding: 0 24px 24px;
        }

        .lfps-price-card__badge {
            border-color: rgba(255, 255, 255, 0.16);
            background: rgba(255, 255, 255, 0.14);
        }

        .lfps-price-card .lfps-button {
            margin-top: auto;
            background: #ffffff;
            color: #06235b;
            box-shadow: none;
        }

        .lfps-price-card .lfps-button:hover {
            box-shadow: none;
        }

        .lfps-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 18px;
            padding: 48px 2px 0;
            color: #5b6984;
            font-size: 14px;
            font-weight: 500;
        }

        @media (max-width: 1100px) {
            .lfps-hero,
            .lfps-hero-program,
            .lfps-hero-side-grid,
            .lfps-program-panel,
            .lfps-video-card--feature,
            .lfps-split-block,
            .lfps-fit-grid,
            .lfps-insight-grid,
            .lfps-resource-grid,
            .lfps-pricing-grid,
            .lfps-video-grid,
            .lfps-note-grid,
            .lfps-mini-grid,
            .lfps-value-grid,
            .lfps-step-grid {
                grid-template-columns: 1fr;
            }

            .lfps-stat-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .lfps-split-block__intro {
                position: static;
            }
        }

        @media (max-width: 720px) {
            .lfps-performance-shell {
                padding-top: 18px;
            }

            .lfps-performance-shell::before {
                height: 960px;
            }

            .lfps-page {
                width: min(100% - 20px, 1180px);
            }

            .lfps-topbar,
            .lfps-proof-strip,
            .lfps-footer,
            .lfps-cta-band {
                flex-direction: column;
                align-items: flex-start;
            }

            .lfps-topbar__nav,
            .lfps-topbar__actions {
                width: 100%;
                justify-content: flex-start;
            }

            .lfps-hero {
                padding-top: 42px;
            }

            .lfps-stack-card {
                grid-template-columns: 1fr;
            }

            .lfps-hero-program {
                grid-template-columns: 1fr;
            }

            .lfps-stack-card__cover {
                min-height: 220px;
            }

            .lfps-section {
                padding-top: 58px;
            }

            .lfps-value-card,
            .lfps-fit-card,
            .lfps-resource-card,
            .lfps-step-card,
            .lfps-insight-card,
            .lfps-note-card,
            .lfps-video-card__body,
            .lfps-program-panel__body,
            .lfps-price-card,
            .lfps-quote-card {
                padding: 22px;
            }

            .lfps-hero h1 {
                font-size: 34px;
            }
        }
    </style>
@endpush
