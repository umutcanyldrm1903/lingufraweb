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
    $steps = $pageData['steps'] ?? [];
    $fitFor = $pageData['fit_for'] ?? [];
    $fitNotFor = $pageData['fit_not_for'] ?? [];
    $packages = $pageData['packages'] ?? [];
    $pricingNotes = $pageData['pricing_notes'] ?? [];
    $pressBadges = $pageData['press_badges'] ?? [];
    $faqs = $pageData['faq'] ?? [];
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
    <section class="openp-shell">
        <div class="openp-noise"></div>

        <div class="openp-container">
            <header class="openp-topbar openp-reveal">
                <a class="openp-brand" href="{{ $homeUrl }}">
                    @if (!empty($setting?->logo))
                        <img src="{{ asset($setting->logo) }}" alt="{{ $siteName }}" />
                    @else
                        <span>{{ $siteName }}</span>
                    @endif
                </a>

                <nav class="openp-nav" aria-label="Bolumler">
                    @foreach ($topLinks as $link)
                        <a href="{{ $link['href'] }}">{{ $link['label'] }}</a>
                    @endforeach
                </nav>

                <div class="openp-actions">
                    <a class="openp-button openp-button--ghost" href="{{ $testUrl }}">Seviye Tespiti</a>
                    <a class="openp-button" href="{{ $applyUrl }}">Programa Basvur</a>
                </div>
            </header>

            <section class="openp-hero" id="overview">
                <div class="openp-hero__copy openp-reveal">
                    <span class="openp-kicker">{{ $pageData['eyebrow'] ?? 'LinguFranca' }}</span>
                    <h1>{{ $pageData['title'] ?? '' }}</h1>
                    <p>{{ $pageData['lead'] ?? '' }}</p>

                    @if (!empty($heroBadges))
                        <div class="openp-chip-row">
                            @foreach ($heroBadges as $badge)
                                <span class="openp-chip">{{ $badge }}</span>
                            @endforeach
                        </div>
                    @endif

                    <div class="openp-hero__actions">
                        <a class="openp-button" href="#programlar">Programlari Incele</a>
                        <a class="openp-button openp-button--ghost" href="#videolar">Video Kayitlarini Incele</a>
                    </div>
                </div>

                <div class="openp-hero__visual openp-reveal">
                    <article class="openp-visual-card">
                        <div class="openp-visual-card__media">
                            @if (!empty($primaryProgram['cover_url']))
                                <img src="{{ $primaryProgram['cover_url'] }}" alt="{{ $primaryProgram['title'] }}" />
                            @elseif (!empty($pageData['hero_primary_visual']))
                                <img src="{{ $pageData['hero_primary_visual'] }}" alt="{{ $siteName }}" />
                            @else
                                <div class="openp-visual-card__fallback">{{ strtoupper(substr($siteName, 0, 2)) }}</div>
                            @endif
                        </div>
                        <div class="openp-visual-card__body">
                            <span class="openp-chip">{{ $primaryProgram['label'] ?? 'Program' }}</span>
                            <strong>{{ $primaryProgram['title'] ?? $siteName }}</strong>
                            <p>{{ $primaryProgram['subtitle'] ?? ($pageData['hero_quote'] ?? '') }}</p>
                            <div class="openp-visual-card__meta">
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
                <section class="openp-strip openp-reveal">
                    @foreach ($pressBadges as $badge)
                        <span>{{ $badge }}</span>
                    @endforeach
                </section>
            @endif

            @if (!empty($heroStats))
                <section class="openp-metrics openp-reveal">
                    @foreach ($heroStats as $metric)
                        <article class="openp-metric">
                            <strong>{{ $metric['value'] }}</strong>
                            <span>{{ $metric['label'] }}</span>
                        </article>
                    @endforeach
                </section>
            @endif

            <section class="openp-section" id="sistem">
                <div class="openp-section__head openp-reveal">
                    <span class="openp-kicker">{{ $pageData['manifesto_eyebrow'] ?? 'Sistem' }}</span>
                    <h2>{{ $pageData['manifesto_title'] ?? '' }}</h2>
                    <p>{{ $pageData['manifesto_lead'] ?? '' }}</p>
                </div>

                <div class="openp-feature-grid">
                    @foreach ($manifestoPoints as $point)
                        <article class="openp-feature-card openp-reveal">
                            <span class="openp-chip">0{{ $loop->iteration }}</span>
                            <h3>{{ $point['title'] }}</h3>
                            <p>{{ $point['description'] }}</p>
                        </article>
                    @endforeach
                </div>
            </section>

            <section class="openp-section" id="programlar">
                <div class="openp-section__head openp-reveal">
                    <span class="openp-kicker">{{ $pageData['resource_eyebrow'] ?? 'Programlar' }}</span>
                    <h2>{{ $pageData['resource_title'] ?? '' }}</h2>
                    <p>{{ $pageData['hero_quote'] ?? '' }}</p>
                </div>

                <div class="openp-program-grid">
                    @foreach ($programs as $program)
                        <article class="openp-program-card openp-reveal">
                            <div class="openp-program-card__media">
                                @if (!empty($program['cover_url']))
                                    <img src="{{ $program['cover_url'] }}" alt="{{ $program['title'] }}" />
                                @endif
                            </div>
                            <div class="openp-program-card__body">
                                <span class="openp-chip">{{ $program['label'] }}</span>
                                <h3>{{ $program['title'] }}</h3>
                                <p>{{ $program['subtitle'] }}</p>
                                @if (!empty($program['bullets']))
                                    <ul class="openp-list">
                                        @foreach ($program['bullets'] as $bullet)
                                            <li>{{ $bullet }}</li>
                                        @endforeach
                                    </ul>
                                @endif
                                <div class="openp-program-card__meta">
                                    @if (!empty($program['meta']))
                                        <span>{{ $program['meta'] }}</span>
                                    @endif
                                    @if (!empty($program['result']))
                                        <strong class="openp-emphasis">{{ $program['result'] }}</strong>
                                    @endif
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
            </section>

            <section class="openp-section" id="structure">
                <div class="openp-section__head openp-reveal">
                    <span class="openp-kicker">{{ $pageData['process_eyebrow'] ?? 'Surec' }}</span>
                    <h2>{{ $pageData['process_title'] ?? '' }}</h2>
                    <p>PDF akisinin ortak omurgasi: once analiz, sonra kisisel plan, ardindan surekli iletisim ve duzenli performans takibi.</p>
                </div>

                <div class="openp-process-grid">
                    @foreach ($steps as $step)
                        <article class="openp-process-card openp-reveal">
                            <div class="openp-process-card__index">0{{ $loop->iteration }}</div>
                            <h3>{{ $step['title'] }}</h3>
                            <p>{{ $step['description'] }}</p>
                        </article>
                    @endforeach
                </div>

                <div class="openp-fit-grid">
                    <article class="openp-fit-card openp-reveal">
                        <span class="openp-chip">Kimler icin uygun</span>
                        <ul class="openp-list">
                            @foreach ($fitFor as $item)
                                <li>{{ $item }}</li>
                            @endforeach
                        </ul>
                    </article>

                    <article class="openp-fit-card openp-reveal">
                        <span class="openp-chip">Kimler icin uygun degil</span>
                        <ul class="openp-list">
                            @foreach ($fitNotFor as $item)
                                <li>{{ $item }}</li>
                            @endforeach
                        </ul>
                    </article>
                </div>

                <article class="openp-team-card openp-reveal">
                    <span class="openp-kicker">Performans ekibi</span>
                    <div class="openp-team-grid">
                        @foreach ($teamRoles as $role)
                            <span>{{ $role }}</span>
                        @endforeach
                    </div>
                </article>
            </section>

            <section class="openp-section" id="videolar">
                <div class="openp-section__head openp-reveal">
                    <span class="openp-kicker">{{ $pageData['proof_eyebrow'] ?? 'Videolar' }}</span>
                    <h2>{{ $pageData['proof_title'] ?? '' }}</h2>
                    <p>{{ $pageData['proof_lead'] ?? '' }}</p>
                </div>

                @if (!empty($featuredMedia))
                    <article class="openp-media-feature openp-reveal">
                        <div class="openp-media-feature__media">
                            <video controls preload="metadata" playsinline @if (!empty($featuredMedia['poster_url'])) poster="{{ $featuredMedia['poster_url'] }}" @endif>
                                <source src="{{ $featuredMedia['file_url'] }}" type="video/mp4">
                            </video>
                        </div>
                        <div class="openp-media-feature__body">
                            <span class="openp-chip">{{ $featuredMedia['category'] }}</span>
                            <h3>{{ $featuredMedia['title'] }}</h3>
                            <p>{{ $featuredMedia['description'] }}</p>
                            <strong class="openp-emphasis">{{ $featuredMedia['duration'] }}</strong>
                        </div>
                    </article>
                @endif

                <div class="openp-media-grid">
                    @foreach ($secondaryMedia as $media)
                        <article class="openp-media-card openp-reveal">
                            <div class="openp-media-card__mark"></div>
                            <span class="openp-chip">{{ $media['category'] }}</span>
                            <h3>{{ $media['title'] }}</h3>
                            <p>{{ $media['description'] }}</p>
                            <a class="openp-inline-link" href="{{ $media['file_url'] }}" target="_blank" rel="noopener">Videoyu ac</a>
                        </article>
                    @endforeach
                </div>
            </section>

            <section class="openp-cta openp-reveal" id="fiyat">
                <div class="openp-cta__inner">
                    <span class="openp-kicker">{{ $pageData['pricing_eyebrow'] ?? 'Fiyat' }}</span>
                    <h2>{{ $pageData['pricing_title'] ?? '' }}</h2>
                    <p>{{ $pageData['pricing_lead'] ?? '' }}</p>

                    <div class="openp-price-grid">
                        @foreach ($packages as $package)
                            <article class="openp-price-card">
                                <span class="openp-chip">{{ $package['name'] }}</span>
                                <h3>{{ $package['price'] }}</h3>
                                <p>{{ $package['unit'] }}</p>
                                <strong class="openp-emphasis">{{ $package['note'] }}</strong>
                            </article>
                        @endforeach
                    </div>

                    @if (!empty($pricingNotes))
                        <ul class="openp-list openp-list--notes">
                            @foreach ($pricingNotes as $note)
                                <li>{{ $note }}</li>
                            @endforeach
                        </ul>
                    @endif

                    <div class="openp-hero__actions">
                        <a class="openp-button" href="{{ $applyUrl }}">Programa Basvur</a>
                        <a class="openp-button openp-button--ghost" href="{{ $testUrl }}">Seviye Tespiti</a>
                    </div>
                </div>
            </section>

            @if (!empty($faqs))
                <section class="openp-section" id="sss">
                    <div class="openp-section__head openp-reveal">
                        <span class="openp-kicker">SSS</span>
                        <h2>Karar oncesi en cok sorulanlar</h2>
                    </div>

                    <div class="openp-faq-grid">
                        @foreach ($faqs as $faq)
                            <details class="openp-faq-item openp-reveal">
                                <summary>{{ $faq['question'] }}</summary>
                                <p>{{ $faq['answer'] }}</p>
                            </details>
                        @endforeach
                    </div>
                </section>
            @endif

            <section class="openp-cta openp-reveal">
                <div class="openp-cta__inner">
                    <span class="openp-kicker">Son adim</span>
                    <h2>{{ $pageData['cta_title'] ?? '' }}</h2>
                    <p>{{ $pageData['cta_text'] ?? '' }}</p>

                    <div class="openp-hero__actions">
                        <a class="openp-button" href="{{ $applyUrl }}">Programa Basvur</a>
                        <a class="openp-button openp-button--ghost" href="{{ $testUrl }}">Seviye Tespiti</a>
                    </div>
                </div>
            </section>
        </div>
    </section>
@endsection

@push('styles')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Sora:wght@600;700;800&display=swap" rel="stylesheet">

    <style>
        .openp-shell {
            position: relative;
            min-height: 100vh;
            overflow: hidden;
            padding: 24px 20px 80px;
            background:
                radial-gradient(circle at 20% 0%, rgba(99, 102, 241, 0.22), transparent 24%),
                radial-gradient(circle at 80% 10%, rgba(56, 189, 248, 0.12), transparent 18%),
                linear-gradient(180deg, #070b14 0%, #0b1220 100%);
            font-family: "Inter", sans-serif;
            color: #edf2ff;
        }

        .openp-noise {
            position: absolute;
            inset: 0;
            pointer-events: none;
            opacity: 0.08;
            background-image:
                linear-gradient(rgba(255, 255, 255, 0.05) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255, 255, 255, 0.05) 1px, transparent 1px);
            background-size: 64px 64px;
            mask-image: radial-gradient(circle at center, black 30%, transparent 100%);
        }

        .openp-container {
            position: relative;
            z-index: 1;
            width: min(1180px, 100%);
            margin: 0 auto;
        }

        .openp-topbar,
        .openp-strip,
        .openp-metrics,
        .openp-feature-card,
        .openp-program-card,
        .openp-process-card,
        .openp-fit-card,
        .openp-team-card,
        .openp-media-feature,
        .openp-media-card,
        .openp-cta,
        .openp-visual-card,
        .openp-price-card {
            border: 1px solid rgba(255, 255, 255, 0.08);
            background: linear-gradient(180deg, rgba(17, 24, 39, 0.88) 0%, rgba(9, 14, 24, 0.92) 100%);
            box-shadow: 0 30px 80px rgba(0, 0, 0, 0.28);
            backdrop-filter: blur(14px);
        }

        .openp-topbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            min-height: 78px;
            padding: 14px 18px;
            border-radius: 22px;
        }

        .openp-brand {
            display: inline-flex;
            align-items: center;
            min-width: 120px;
        }

        .openp-brand img {
            max-width: 122px;
            max-height: 40px;
            object-fit: contain;
        }

        .openp-nav,
        .openp-actions,
        .openp-hero__actions,
        .openp-chip-row {
            display: flex;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
        }

        .openp-nav a,
        .openp-inline-link {
            color: rgba(237, 242, 255, 0.82);
            font-size: 14px;
            font-weight: 600;
        }

        .openp-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 46px;
            padding: 0 18px;
            border-radius: 999px;
            background: linear-gradient(180deg, #6366f1 0%, #4f46e5 100%);
            color: #ffffff;
            font-size: 14px;
            font-weight: 700;
            border: 1px solid transparent;
            transition: transform 180ms ease, box-shadow 180ms ease;
            box-shadow: 0 18px 40px rgba(79, 70, 229, 0.28);
        }

        .openp-button:hover,
        .openp-button:focus-visible,
        .openp-inline-link:hover {
            transform: translateY(-1px);
        }

        .openp-button--ghost {
            background: rgba(255, 255, 255, 0.04);
            border-color: rgba(255, 255, 255, 0.1);
            box-shadow: none;
            color: #eef2ff;
        }

        .openp-kicker,
        .openp-chip {
            display: inline-flex;
            align-items: center;
            min-height: 30px;
            width: fit-content;
            padding: 0 12px;
            border-radius: 999px;
            background: rgba(99, 102, 241, 0.12);
            border: 1px solid rgba(99, 102, 241, 0.24);
            color: #c7d2fe;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .openp-hero {
            display: grid;
            grid-template-columns: minmax(0, 1fr) minmax(320px, 0.92fr);
            gap: 28px;
            align-items: center;
            padding: 72px 0 36px;
        }

        .openp-hero__copy,
        .openp-visual-card__body,
        .openp-section__head,
        .openp-feature-card,
        .openp-program-card__body,
        .openp-process-card,
        .openp-fit-card,
        .openp-team-card,
        .openp-media-feature__body,
        .openp-media-card,
        .openp-price-card,
        .openp-cta__inner {
            display: grid;
            gap: 14px;
        }

        .openp-hero h1,
        .openp-section__head h2,
        .openp-feature-card h3,
        .openp-program-card__body h3,
        .openp-process-card h3,
        .openp-fit-card h3,
        .openp-media-feature__body h3,
        .openp-media-card h3,
        .openp-price-card h3,
        .openp-cta h2,
        .openp-visual-card__body strong {
            margin: 0;
            font-family: "Sora", sans-serif;
            color: #ffffff;
            text-wrap: balance;
        }

        .openp-hero h1 {
            margin-top: 4px;
            font-size: clamp(40px, 5vw, 66px);
            line-height: 1;
            letter-spacing: -0.06em;
            background: linear-gradient(90deg, #f8fafc 0%, #c7d2fe 45%, #e2e8f0 100%);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }

        .openp-hero p,
        .openp-section__head p,
        .openp-feature-card p,
        .openp-program-card__body p,
        .openp-process-card p,
        .openp-media-feature__body p,
        .openp-media-card p,
        .openp-price-card p,
        .openp-visual-card__body p,
        .openp-list li,
        .openp-strip span,
        .openp-team-grid span,
        .openp-visual-card__meta span {
            margin: 0;
            color: rgba(199, 210, 254, 0.72);
            line-height: 1.75;
        }

        .openp-visual-card {
            overflow: hidden;
            border-radius: 26px;
        }

        .openp-visual-card__media {
            aspect-ratio: 16 / 10;
            background: radial-gradient(circle at 20% 20%, rgba(99, 102, 241, 0.2), transparent 30%), linear-gradient(145deg, rgba(22, 29, 49, 0.96), rgba(9, 14, 24, 1));
        }

        .openp-visual-card__media img,
        .openp-program-card__media img,
        .openp-media-feature__media video {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .openp-visual-card__fallback {
            width: 100%;
            height: 100%;
            display: grid;
            place-items: center;
            font-family: "Sora", sans-serif;
            font-size: clamp(64px, 10vw, 104px);
            font-weight: 800;
            color: #ffffff;
        }

        .openp-visual-card__body,
        .openp-program-card__body,
        .openp-media-feature__body,
        .openp-price-card {
            padding: 22px;
        }

        .openp-visual-card__meta,
        .openp-program-card__meta {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
        }

        .openp-strip {
            margin-top: 12px;
            padding: 16px 18px;
            border-radius: 20px;
            display: flex;
            flex-wrap: wrap;
            gap: 12px 18px;
        }

        .openp-metrics,
        .openp-feature-grid,
        .openp-program-grid,
        .openp-process-grid,
        .openp-fit-grid,
        .openp-media-grid,
        .openp-price-grid,
        .openp-faq-grid {
            display: grid;
            gap: 18px;
        }

        .openp-metrics {
            grid-template-columns: repeat(4, minmax(0, 1fr));
            padding: 18px;
            border-radius: 22px;
            margin-top: 18px;
        }

        .openp-metric {
            min-height: 110px;
            border-radius: 18px;
            padding: 18px;
            background: rgba(255, 255, 255, 0.03);
            display: grid;
            align-content: end;
            gap: 8px;
        }

        .openp-metric strong,
        .openp-process-card__index {
            font-family: "Sora", sans-serif;
            color: #ffffff;
            font-size: 30px;
        }

        .openp-section {
            padding-top: 92px;
            display: grid;
            gap: 26px;
        }

        .openp-feature-grid,
        .openp-process-grid,
        .openp-fit-grid,
        .openp-media-grid,
        .openp-price-grid,
        .openp-faq-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .openp-program-grid {
            grid-template-columns: 1fr;
        }

        .openp-feature-card,
        .openp-process-card,
        .openp-fit-card,
        .openp-team-card,
        .openp-media-card,
        .openp-price-card,
        .openp-faq-item {
            min-height: 100%;
            border-radius: 24px;
            padding: 24px;
        }

        .openp-program-card {
            overflow: hidden;
            border-radius: 24px;
            display: grid;
            grid-template-columns: minmax(260px, 0.82fr) minmax(0, 1.18fr);
        }

        .openp-program-card__media {
            min-height: 240px;
            background: linear-gradient(145deg, rgba(28, 41, 69, 0.96), rgba(13, 20, 32, 1));
        }

        .openp-list {
            margin: 0;
            padding-left: 18px;
            display: grid;
            gap: 8px;
        }

        .openp-emphasis {
            color: #ffffff;
            font-size: 14px;
            font-weight: 700;
        }

        .openp-team-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 12px;
        }

        .openp-team-grid span {
            display: inline-flex;
            align-items: center;
            min-height: 46px;
            padding: 0 14px;
            border-radius: 14px;
            background: rgba(255, 255, 255, 0.04);
        }

        .openp-media-feature {
            overflow: hidden;
            border-radius: 24px;
            display: grid;
            grid-template-columns: minmax(0, 1.12fr) minmax(300px, 0.88fr);
        }

        .openp-media-feature__media {
            min-height: 360px;
            background: #0d1524;
        }

        .openp-media-card__mark,
        .openp-result-card__mark {
            width: 42px;
            height: 42px;
            border-radius: 14px;
            background: linear-gradient(180deg, rgba(99, 102, 241, 0.34), rgba(59, 130, 246, 0.16));
            border: 1px solid rgba(99, 102, 241, 0.3);
        }

        .openp-cta {
            margin-top: 96px;
            border-radius: 28px;
            padding: 32px;
        }

        .openp-list--notes {
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 10px 18px;
        }

        .openp-faq-item summary {
            cursor: pointer;
            color: #ffffff;
            font-family: "Sora", sans-serif;
            font-size: 18px;
            font-weight: 700;
            list-style: none;
        }

        .openp-faq-item summary::-webkit-details-marker {
            display: none;
        }

        .openp-reveal {
            opacity: 0;
            transform: translateY(24px);
            transition: opacity 0.7s ease, transform 0.7s cubic-bezier(0.22, 1, 0.36, 1);
        }

        .openp-reveal.is-visible {
            opacity: 1;
            transform: none;
        }

        @media (max-width: 960px) {
            .openp-hero,
            .openp-program-card,
            .openp-media-feature,
            .openp-feature-grid,
            .openp-process-grid,
            .openp-fit-grid,
            .openp-media-grid,
            .openp-price-grid,
            .openp-faq-grid,
            .openp-list--notes {
                grid-template-columns: 1fr;
            }

            .openp-metrics {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 760px) {
            .openp-shell {
                padding: 14px 12px 56px;
            }

            .openp-topbar {
                flex-direction: column;
                align-items: stretch;
            }

            .openp-nav,
            .openp-actions {
                justify-content: flex-start;
            }

            .openp-hero {
                padding: 48px 0 24px;
            }

            .openp-metrics,
            .openp-team-grid {
                grid-template-columns: 1fr;
            }

            .openp-section {
                padding-top: 72px;
            }
        }
    </style>
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var targets = document.querySelectorAll('.openp-reveal');
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
                rootMargin: '0px 0px -50px 0px'
            });

            targets.forEach(function (element, index) {
                element.style.transitionDelay = Math.min(index * 70, 320) + 'ms';
                observer.observe(element);
            });
        });
    </script>
@endpush
