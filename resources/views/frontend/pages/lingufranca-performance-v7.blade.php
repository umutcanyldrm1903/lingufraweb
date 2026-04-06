@extends('frontend.layouts.master')

@php
    $siteName = $setting->app_name ?? config('app.name');
    $canonicalUrl = route('lingufranca-performance');
    $applyUrl = route('contact.index');
    $testUrl = route('placement-test.show');
    $homeUrl = route('home');

    $topLinks = $pageData['top_links'] ?? [];
    $heroBadges = $pageData['hero_badges'] ?? [];
    $heroStats = $pageData['hero_stats'] ?? [];
    $manifestoPoints = $pageData['manifesto_points'] ?? [];
    $resourceColumns = $pageData['resource_columns'] ?? [];
    $fitFor = $pageData['fit_for'] ?? [];
    $fitNotFor = $pageData['fit_not_for'] ?? [];
    $steps = $pageData['steps'] ?? [];
    $packages = $pageData['packages'] ?? [];
    $pricingNotes = $pageData['pricing_notes'] ?? [];
    $faqs = $pageData['faq'] ?? [];
    $pressBadges = $pageData['press_badges'] ?? [];
    $primaryProgram = $downloads[0] ?? null;
    $programs = $downloads ?? [];
    $featuredMedia = $mediaLibrary[0] ?? null;
    $secondaryMedia = array_slice($mediaLibrary ?? [], 1, 5);

    $teamRoles = [
        'Kurucu & Dil Kocu',
        'Ana Egitmen (Native / Turk)',
        'Mufredat Sorumlusu',
        'Rehber Mentor',
    ];
@endphp

@section('meta_title', ($pageData['meta_title'] ?? 'LinguFranca') . ' | ' . $siteName)
@section('meta_description', $pageData['meta_description'] ?? '')
@section('meta_keywords', $pageData['meta_keywords'] ?? '')
@section('canonical_url', $canonicalUrl)
@section('meta_image', $pageData['meta_image_url'] ?? '')
@section('hide_public_header', '1')
@section('hide_public_footer', '1')

@section('contents')
    <section class="dbp-shell">
        <div class="dbp-page">
            <header class="dbp-topbar dbp-reveal">
                <a class="dbp-brand" href="{{ $homeUrl }}">
                    @if (!empty($setting?->logo))
                        <img src="{{ asset($setting->logo) }}" alt="{{ $siteName }}" />
                    @else
                        <span>{{ $siteName }}</span>
                    @endif
                </a>

                <nav class="dbp-nav" aria-label="Bolumler">
                    @foreach ($topLinks as $link)
                        <a href="{{ $link['href'] }}">{{ $link['label'] }}</a>
                    @endforeach
                </nav>

                <div class="dbp-actions">
                    <a class="dbp-button dbp-button--ghost" href="{{ $testUrl }}">Seviye Tespiti</a>
                    <a class="dbp-button" href="{{ $applyUrl }}">Programa Basvur</a>
                </div>
            </header>

            <section class="dbp-hero" id="overview">
                <div class="dbp-hero__copy dbp-reveal">
                    @if (!empty($heroStats[3]['label']))
                        <span class="dbp-overline">{{ $heroStats[3]['value'] }} {{ $heroStats[3]['label'] }}</span>
                    @else
                        <span class="dbp-overline">{{ $pageData['eyebrow'] ?? 'LinguFranca' }}</span>
                    @endif

                    <h1>{{ $pageData['title'] ?? '' }}</h1>
                    <p>{{ $pageData['lead'] ?? '' }}</p>

                    <div class="dbp-actions">
                        <a class="dbp-button" href="{{ $applyUrl }}">Programa Basvur</a>
                        <a class="dbp-button dbp-button--ghost" href="#videolar">Video Kayitlarini Incele</a>
                    </div>

                    @if (!empty($heroBadges))
                        <div class="dbp-chip-row">
                            @foreach ($heroBadges as $badge)
                                <span class="dbp-chip">{{ $badge }}</span>
                            @endforeach
                        </div>
                    @endif
                </div>

                <div class="dbp-hero__media dbp-reveal">
                    <article class="dbp-hero-panel">
                        <div class="dbp-hero-panel__media">
                            @if (!empty($primaryProgram['cover_url']))
                                <img src="{{ $primaryProgram['cover_url'] }}" alt="{{ $primaryProgram['title'] }}" />
                            @elseif (!empty($pageData['hero_primary_visual']))
                                <img src="{{ $pageData['hero_primary_visual'] }}" alt="{{ $siteName }}" />
                            @endif
                        </div>

                        <div class="dbp-hero-panel__body">
                            <span class="dbp-kicker">{{ $primaryProgram['label'] ?? 'Program' }}</span>
                            <h2>{{ $primaryProgram['title'] ?? $siteName }}</h2>
                            <p>{{ $primaryProgram['subtitle'] ?? ($pageData['hero_quote'] ?? '') }}</p>

                            <div class="dbp-hero-panel__meta">
                                @if (!empty($primaryProgram['meta']))
                                    <span>{{ $primaryProgram['meta'] }}</span>
                                @endif
                                @if (!empty($primaryProgram['result']))
                                    <span>{{ $primaryProgram['result'] }}</span>
                                @endif
                            </div>
                        </div>
                    </article>
                </div>
            </section>

            @if (!empty($pressBadges))
                <section class="dbp-strip dbp-reveal">
                    @foreach ($pressBadges as $badge)
                        <span>{{ $badge }}</span>
                    @endforeach
                </section>
            @endif

            <section class="dbp-section" id="sistem">
                <div class="dbp-section__head dbp-reveal">
                    <span class="dbp-kicker">Degerlerimiz</span>
                    <h2>{{ $pageData['manifesto_title'] ?? '' }}</h2>
                    <p>{{ $pageData['manifesto_lead'] ?? '' }}</p>
                </div>

                <div class="dbp-value-grid">
                    @foreach ($manifestoPoints as $point)
                        <article class="dbp-value-card dbp-reveal">
                            <span class="dbp-value-card__label">Deger {{ $loop->iteration }}</span>
                            <h3>{{ $point['title'] }}</h3>
                            <p>{{ $point['description'] }}</p>
                        </article>
                    @endforeach
                </div>
            </section>

            <section class="dbp-section" id="uygunluk">
                <div class="dbp-section__head dbp-reveal">
                    <span class="dbp-kicker">Bu senin icin mi?</span>
                    <h2>{{ $pageData['fit_title'] ?? '' }}</h2>
                    <p>{{ $pageData['fit_lead'] ?? '' }}</p>
                </div>

                <div class="dbp-fit-layout">
                    <article class="dbp-fit-card dbp-reveal">
                        <span class="dbp-fit-card__title">Kimin icin degil</span>
                        <ul>
                            @foreach ($fitNotFor as $item)
                                <li>{{ $item }}</li>
                            @endforeach
                        </ul>
                    </article>

                    <article class="dbp-fit-card dbp-fit-card--positive dbp-reveal">
                        <span class="dbp-fit-card__title">Kimin icin</span>
                        <ul>
                            @foreach ($fitFor as $item)
                                <li>{{ $item }}</li>
                            @endforeach
                        </ul>
                    </article>
                </div>
            </section>

            <section class="dbp-section" id="programlar">
                <div class="dbp-section__head dbp-reveal">
                    <span class="dbp-kicker">Nelere eriseceksin?</span>
                    <h2>{{ $pageData['resource_title'] ?? '' }}</h2>
                    <p>{{ $pageData['hero_quote'] ?? '' }}</p>
                </div>

                <div class="dbp-resource-grid">
                    @foreach ($resourceColumns as $column)
                        <article class="dbp-resource-card dbp-reveal">
                            <h3>{{ $column['label'] }}</h3>
                            <ul>
                                @foreach ($column['items'] as $item)
                                    <li>{{ $item }}</li>
                                @endforeach
                            </ul>
                        </article>
                    @endforeach
                </div>

                <div class="dbp-program-grid">
                    @foreach ($programs as $program)
                        <article class="dbp-program-card dbp-reveal">
                            <div class="dbp-program-card__media">
                                @if (!empty($program['cover_url']))
                                    <img src="{{ $program['cover_url'] }}" alt="{{ $program['title'] }}" />
                                @endif
                            </div>
                            <div class="dbp-program-card__body">
                                <span class="dbp-kicker">{{ $program['label'] }}</span>
                                <h3>{{ $program['title'] }}</h3>
                                <p>{{ $program['subtitle'] }}</p>
                                <ul>
                                    @foreach ($program['bullets'] as $bullet)
                                        <li>{{ $bullet }}</li>
                                    @endforeach
                                </ul>
                                <div class="dbp-program-card__meta">
                                    @if (!empty($program['meta']))
                                        <span>{{ $program['meta'] }}</span>
                                    @endif
                                    @if (!empty($program['result']))
                                        <strong>{{ $program['result'] }}</strong>
                                    @endif
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
            </section>

            <section class="dbp-section" id="surec">
                <div class="dbp-section__head dbp-reveal">
                    <span class="dbp-kicker">Surec</span>
                    <h2>{{ $pageData['process_title'] ?? '' }}</h2>
                    <p>Tekerlegi yeniden icat etmene gerek yok. PDF'lerde anlatilan ortak omurga burada 4 net adima ayrildi.</p>
                </div>

                <div class="dbp-step-grid">
                    @foreach ($steps as $step)
                        <article class="dbp-step-card dbp-reveal">
                            <div class="dbp-step-card__index">0{{ $loop->iteration }}</div>
                            <span class="dbp-step-card__label">{{ $loop->iteration }}. adim</span>
                            <h3>{{ $step['title'] }}</h3>
                            <p>{{ $step['description'] }}</p>
                        </article>
                    @endforeach
                </div>

                <article class="dbp-team-card dbp-reveal">
                    <span class="dbp-kicker">Performans ekibi</span>
                    <div class="dbp-team-grid">
                        @foreach ($teamRoles as $role)
                            <span>{{ $role }}</span>
                        @endforeach
                    </div>
                </article>
            </section>

            <section class="dbp-section" id="videolar">
                <div class="dbp-section__head dbp-reveal">
                    <span class="dbp-kicker">{{ $pageData['proof_eyebrow'] ?? 'Basin ve ogrenci videolari' }}</span>
                    <h2>{{ $pageData['proof_title'] ?? '' }}</h2>
                    <p>{{ $pageData['proof_lead'] ?? '' }}</p>
                </div>

                @if (!empty($featuredMedia))
                    <article class="dbp-media-feature dbp-reveal">
                        <div class="dbp-media-feature__media">
                            <video controls preload="metadata" playsinline @if (!empty($featuredMedia['poster_url'])) poster="{{ $featuredMedia['poster_url'] }}" @endif>
                                <source src="{{ $featuredMedia['file_url'] }}" type="video/mp4">
                            </video>
                        </div>
                        <div class="dbp-media-feature__body">
                            <span class="dbp-kicker">{{ $featuredMedia['category'] }}</span>
                            <h3>{{ $featuredMedia['title'] }}</h3>
                            <p>{{ $featuredMedia['description'] }}</p>
                            <strong>{{ $featuredMedia['duration'] }}</strong>
                        </div>
                    </article>
                @endif

                <div class="dbp-media-grid">
                    @foreach ($secondaryMedia as $media)
                        <article class="dbp-media-card dbp-reveal">
                            <span class="dbp-kicker">{{ $media['category'] }}</span>
                            <h3>{{ $media['title'] }}</h3>
                            <p>{{ $media['description'] }}</p>
                            <a href="{{ $media['file_url'] }}" target="_blank" rel="noopener">Videoyu ac</a>
                        </article>
                    @endforeach
                </div>
            </section>

            <section class="dbp-section" id="fiyat">
                <div class="dbp-section__head dbp-reveal">
                    <span class="dbp-kicker">{{ $pageData['pricing_eyebrow'] ?? 'Fiyat' }}</span>
                    <h2>{{ $pageData['pricing_title'] ?? '' }}</h2>
                    <p>{{ $pageData['pricing_lead'] ?? '' }}</p>
                </div>

                <div class="dbp-price-grid">
                    @foreach ($packages as $package)
                        <article class="dbp-price-card dbp-reveal @if(!empty($package['featured'])) dbp-price-card--featured @endif">
                            <span class="dbp-kicker">{{ $package['name'] }}</span>
                            <h3>{{ $package['price'] }}</h3>
                            <p>{{ $package['unit'] }}</p>
                            <strong>{{ $package['note'] }}</strong>
                        </article>
                    @endforeach
                </div>

                @if (!empty($pricingNotes))
                    <div class="dbp-note-grid">
                        @foreach ($pricingNotes as $note)
                            <div class="dbp-note-card dbp-reveal">{{ $note }}</div>
                        @endforeach
                    </div>
                @endif
            </section>

            @if (!empty($faqs))
                <section class="dbp-section" id="sss">
                    <div class="dbp-section__head dbp-reveal">
                        <span class="dbp-kicker">SSS</span>
                        <h2>Karar oncesi en cok sorulanlar</h2>
                    </div>

                    <div class="dbp-faq-grid">
                        @foreach ($faqs as $faq)
                            <details class="dbp-faq-item dbp-reveal">
                                <summary>{{ $faq['question'] }}</summary>
                                <p>{{ $faq['answer'] }}</p>
                            </details>
                        @endforeach
                    </div>
                </section>
            @endif

            <section class="dbp-final-cta dbp-reveal">
                <span class="dbp-kicker">Son adim</span>
                <h2>{{ $pageData['cta_title'] ?? '' }}</h2>
                <p>{{ $pageData['cta_text'] ?? '' }}</p>
                <div class="dbp-actions">
                    <a class="dbp-button" href="{{ $applyUrl }}">Programa Basvur</a>
                    <a class="dbp-button dbp-button--ghost" href="{{ $testUrl }}">Seviye Tespiti</a>
                </div>
            </section>
        </div>
    </section>
@endsection

@push('styles')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Sora:wght@600;700;800&display=swap" rel="stylesheet">

    <style>
        .dbp-shell {
            min-height: 100vh;
            padding: 22px 18px 80px;
            background:
                radial-gradient(circle at top left, rgba(90, 74, 56, 0.25), transparent 24%),
                radial-gradient(circle at top right, rgba(120, 87, 52, 0.12), transparent 20%),
                linear-gradient(180deg, #0c0a09 0%, #120f0d 100%);
            color: #f4efe9;
            font-family: "Manrope", sans-serif;
        }

        .dbp-page {
            width: min(1180px, 100%);
            margin: 0 auto;
        }

        .dbp-topbar,
        .dbp-strip,
        .dbp-value-card,
        .dbp-fit-card,
        .dbp-resource-card,
        .dbp-program-card,
        .dbp-step-card,
        .dbp-team-card,
        .dbp-media-feature,
        .dbp-media-card,
        .dbp-price-card,
        .dbp-note-card,
        .dbp-faq-item,
        .dbp-final-cta,
        .dbp-hero-panel {
            border: 1px solid rgba(255, 255, 255, 0.08);
            background: linear-gradient(180deg, rgba(26, 20, 17, 0.96) 0%, rgba(14, 11, 10, 0.96) 100%);
            box-shadow: 0 28px 80px rgba(0, 0, 0, 0.28);
        }

        .dbp-topbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            min-height: 76px;
            padding: 14px 18px;
            border-radius: 22px;
        }

        .dbp-brand img {
            max-width: 128px;
            max-height: 42px;
            object-fit: contain;
        }

        .dbp-nav,
        .dbp-actions,
        .dbp-chip-row {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 12px;
        }

        .dbp-nav a {
            color: rgba(244, 239, 233, 0.8);
            font-size: 14px;
            font-weight: 600;
        }

        .dbp-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 46px;
            padding: 0 18px;
            border-radius: 999px;
            background: linear-gradient(180deg, #e8b476 0%, #d18f47 100%);
            color: #140f0b;
            font-size: 14px;
            font-weight: 800;
            border: 1px solid transparent;
            transition: transform 180ms ease;
        }

        .dbp-button--ghost {
            background: rgba(255, 255, 255, 0.04);
            border-color: rgba(255, 255, 255, 0.1);
            color: #f4efe9;
        }

        .dbp-button:hover,
        .dbp-button:focus-visible {
            transform: translateY(-1px);
        }

        .dbp-overline,
        .dbp-kicker,
        .dbp-chip,
        .dbp-value-card__label,
        .dbp-fit-card__title,
        .dbp-step-card__label {
            display: inline-flex;
            width: fit-content;
            align-items: center;
            min-height: 30px;
            padding: 0 12px;
            border-radius: 999px;
            background: rgba(209, 143, 71, 0.12);
            border: 1px solid rgba(209, 143, 71, 0.25);
            color: #e6ba85;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .dbp-hero {
            display: grid;
            grid-template-columns: minmax(0, 1.02fr) minmax(340px, 0.98fr);
            gap: 34px;
            align-items: center;
            padding: 84px 0 38px;
        }

        .dbp-hero__copy,
        .dbp-hero-panel__body,
        .dbp-section__head,
        .dbp-value-card,
        .dbp-resource-card,
        .dbp-program-card__body,
        .dbp-step-card,
        .dbp-team-card,
        .dbp-media-feature__body,
        .dbp-media-card,
        .dbp-price-card,
        .dbp-final-cta {
            display: grid;
            gap: 14px;
        }

        .dbp-hero h1,
        .dbp-section__head h2,
        .dbp-value-card h3,
        .dbp-resource-card h3,
        .dbp-program-card__body h3,
        .dbp-step-card h3,
        .dbp-media-feature__body h3,
        .dbp-media-card h3,
        .dbp-price-card h3,
        .dbp-final-cta h2,
        .dbp-hero-panel__body h2 {
            margin: 0;
            font-family: "Sora", sans-serif;
            color: #ffffff;
            text-wrap: balance;
        }

        .dbp-hero h1 {
            font-size: clamp(40px, 5vw, 68px);
            line-height: 0.98;
            letter-spacing: -0.05em;
        }

        .dbp-hero p,
        .dbp-section__head p,
        .dbp-value-card p,
        .dbp-resource-card li,
        .dbp-program-card__body p,
        .dbp-program-card__body li,
        .dbp-step-card p,
        .dbp-media-feature__body p,
        .dbp-media-card p,
        .dbp-price-card p,
        .dbp-note-card,
        .dbp-faq-item p,
        .dbp-fit-card li,
        .dbp-strip span,
        .dbp-team-grid span,
        .dbp-hero-panel__body p,
        .dbp-hero-panel__meta span {
            margin: 0;
            color: rgba(244, 239, 233, 0.72);
            line-height: 1.75;
        }

        .dbp-hero-panel {
            overflow: hidden;
            border-radius: 28px;
        }

        .dbp-hero-panel__media,
        .dbp-program-card__media {
            background: linear-gradient(145deg, rgba(41, 30, 25, 0.98), rgba(18, 14, 12, 1));
        }

        .dbp-hero-panel__media {
            aspect-ratio: 16 / 10;
        }

        .dbp-hero-panel__media img,
        .dbp-program-card__media img,
        .dbp-media-feature__media video {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .dbp-hero-panel__body,
        .dbp-program-card__body,
        .dbp-media-feature__body,
        .dbp-price-card {
            padding: 22px;
        }

        .dbp-hero-panel__meta,
        .dbp-program-card__meta {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
        }

        .dbp-strip {
            padding: 16px 18px;
            border-radius: 20px;
            display: flex;
            flex-wrap: wrap;
            gap: 12px 18px;
        }

        .dbp-section {
            padding-top: 100px;
            display: grid;
            gap: 28px;
        }

        .dbp-value-grid,
        .dbp-resource-grid,
        .dbp-program-grid,
        .dbp-step-grid,
        .dbp-media-grid,
        .dbp-price-grid,
        .dbp-note-grid,
        .dbp-faq-grid {
            display: grid;
            gap: 18px;
        }

        .dbp-value-grid,
        .dbp-resource-grid,
        .dbp-price-grid {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }

        .dbp-fit-layout,
        .dbp-media-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 18px;
        }

        .dbp-program-grid,
        .dbp-step-grid,
        .dbp-note-grid,
        .dbp-faq-grid {
            grid-template-columns: 1fr;
        }

        .dbp-fit-card,
        .dbp-value-card,
        .dbp-resource-card,
        .dbp-step-card,
        .dbp-media-card,
        .dbp-note-card,
        .dbp-faq-item {
            border-radius: 24px;
            padding: 24px;
        }

        .dbp-fit-card ul,
        .dbp-resource-card ul,
        .dbp-program-card__body ul {
            margin: 0;
            padding-left: 18px;
            display: grid;
            gap: 8px;
        }

        .dbp-fit-card--positive {
            background: linear-gradient(180deg, rgba(26, 20, 17, 0.96) 0%, rgba(18, 15, 13, 0.96) 100%);
        }

        .dbp-program-card {
            overflow: hidden;
            border-radius: 24px;
            display: grid;
            grid-template-columns: minmax(260px, 0.82fr) minmax(0, 1.18fr);
        }

        .dbp-program-card__media {
            min-height: 280px;
        }

        .dbp-program-card__meta strong,
        .dbp-media-feature__body strong,
        .dbp-price-card strong {
            color: #ffffff;
            font-size: 14px;
            font-weight: 700;
        }

        .dbp-step-grid {
            grid-template-columns: repeat(4, minmax(0, 1fr));
        }

        .dbp-step-card__index {
            font-family: "Sora", sans-serif;
            color: #ffffff;
            font-size: 28px;
        }

        .dbp-team-card {
            border-radius: 24px;
            padding: 24px;
        }

        .dbp-team-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 12px;
        }

        .dbp-team-grid span {
            display: inline-flex;
            align-items: center;
            min-height: 46px;
            padding: 0 14px;
            border-radius: 14px;
            background: rgba(255, 255, 255, 0.04);
        }

        .dbp-media-feature {
            overflow: hidden;
            border-radius: 24px;
            display: grid;
            grid-template-columns: minmax(0, 1.12fr) minmax(300px, 0.88fr);
        }

        .dbp-media-feature__media {
            min-height: 360px;
            background: #0d1524;
        }

        .dbp-media-card a {
            color: #e6ba85;
            font-size: 14px;
            font-weight: 700;
        }

        .dbp-price-card {
            min-height: 100%;
            border-radius: 24px;
        }

        .dbp-price-card--featured {
            background: linear-gradient(180deg, rgba(64, 38, 16, 0.98) 0%, rgba(24, 16, 10, 0.98) 100%);
        }

        .dbp-note-card {
            background: rgba(255, 255, 255, 0.03);
        }

        .dbp-faq-item summary {
            cursor: pointer;
            color: #ffffff;
            font-family: "Sora", sans-serif;
            font-size: 18px;
            font-weight: 700;
            list-style: none;
        }

        .dbp-faq-item summary::-webkit-details-marker {
            display: none;
        }

        .dbp-final-cta {
            margin-top: 100px;
            border-radius: 28px;
            padding: 32px;
        }

        .dbp-reveal {
            opacity: 0;
            transform: translateY(24px);
            transition: opacity 0.7s ease, transform 0.7s cubic-bezier(0.22, 1, 0.36, 1);
        }

        .dbp-reveal.is-visible {
            opacity: 1;
            transform: none;
        }

        @media (max-width: 1024px) {
            .dbp-hero,
            .dbp-program-card,
            .dbp-media-feature,
            .dbp-value-grid,
            .dbp-resource-grid,
            .dbp-step-grid,
            .dbp-price-grid,
            .dbp-team-grid {
                grid-template-columns: 1fr;
            }

            .dbp-fit-layout,
            .dbp-media-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 760px) {
            .dbp-shell {
                padding: 14px 12px 56px;
            }

            .dbp-topbar {
                flex-direction: column;
                align-items: stretch;
            }

            .dbp-nav,
            .dbp-actions {
                justify-content: flex-start;
            }

            .dbp-hero {
                padding: 48px 0 18px;
            }

            .dbp-section {
                padding-top: 72px;
            }
        }
    </style>
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var targets = document.querySelectorAll('.dbp-reveal');
            if (!targets.length) return;

            if (!('IntersectionObserver' in window)) {
                targets.forEach(function (element) {
                    element.classList.add('is-visible');
                });
                return;
            }

            var observer = new IntersectionObserver(function (entries) {
                entries.forEach(function (entry) {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('is-visible');
                        observer.unobserve(entry.target);
                    }
                });
            }, {
                threshold: 0.14,
                rootMargin: '0px 0px -48px 0px'
            });

            targets.forEach(function (element, index) {
                element.style.transitionDelay = Math.min(index * 70, 320) + 'ms';
                observer.observe(element);
            });
        });
    </script>
@endpush
