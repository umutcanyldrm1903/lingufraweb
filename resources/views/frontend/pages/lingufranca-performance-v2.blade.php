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
        ->merge(
            collect($mediaLibrary)->take(4)->map(fn ($item) => [
                'title' => $item['title'] ?? '',
                'highlight' => $item['category'] ?? 'Ogrenci Memnuniyeti',
                'text' => $item['description'] ?? '',
                'tag' => $item['duration'] ?? 'Video',
            ])
        )
        ->filter(fn ($item) => filled($item['title']) && filled($item['text']))
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
    <section class="lfv2">
        <div class="lfv2-wrap">
            <header class="lfv2-topbar">
                <a class="lfv2-brand" href="{{ $homeUrl }}">
                    @if (!empty($setting?->logo))
                        <img src="{{ asset($setting->logo) }}" alt="{{ $siteName }}" />
                    @endif
                </a>
                <nav class="lfv2-nav" aria-label="Bolumler">
                    @foreach ($topLinks as $link)
                        <a href="{{ $link['href'] }}">{{ $link['label'] }}</a>
                    @endforeach
                </nav>
                <div class="lfv2-actions">
                    <a class="lfv2-btn lfv2-btn--ghost" href="{{ $testUrl }}">Seviye Tespiti</a>
                    <a class="lfv2-btn" href="{{ $applyUrl }}">Programa Basvur</a>
                </div>
            </header>

            <section class="lfv2-hero">
                <div>
                    <span class="lfv2-kicker">{{ $pageData['eyebrow'] }}</span>
                    <h1>{{ $pageData['title'] }}</h1>
                    <p>{{ $pageData['lead'] }}</p>
                    <div class="lfv2-chip-row">
                        @foreach ($heroBadges as $badge)
                            <span>{{ $badge }}</span>
                        @endforeach
                    </div>
                    <div class="lfv2-actions">
                        <a class="lfv2-btn" href="{{ $applyUrl }}">Programa Basvur</a>
                        <a class="lfv2-btn lfv2-btn--ghost" href="#videolar">Video Kayitlarini Incele</a>
                    </div>
                    <div class="lfv2-stats">
                        @foreach ($heroStats as $stat)
                            <article>
                                <strong>{{ $stat['value'] }}</strong>
                                <span>{{ $stat['label'] }}</span>
                            </article>
                        @endforeach
                    </div>
                </div>
                <div class="lfv2-panel">
                    @if (!empty($pageData['hero_primary_visual']))
                        <img src="{{ $pageData['hero_primary_visual'] }}" alt="{{ $pageData['meta_title'] }}" loading="lazy" />
                    @endif
                    <div class="lfv2-quote">
                        <span>{{ $pageData['hero_quote_title'] }}</span>
                        <p>{{ $pageData['hero_quote'] }}</p>
                    </div>
                </div>
            </section>

            <section class="lfv2-marquee">
                @foreach ($pageData['press_badges'] as $badge)
                    <span>{{ $badge }}</span>
                @endforeach
            </section>

            <section class="lfv2-section" id="sistem">
                <span class="lfv2-kicker">{{ $pageData['manifesto_eyebrow'] }}</span>
                <h2>{{ $pageData['manifesto_title'] }}</h2>
                <p>{{ $pageData['manifesto_lead'] }}</p>
                <div class="lfv2-grid3">
                    @foreach ($pageData['manifesto_points'] as $point)
                        <article class="lfv2-card">
                            <small>0{{ $loop->iteration }}</small>
                            <h3>{{ $point['title'] }}</h3>
                            <p>{{ $point['description'] }}</p>
                        </article>
                    @endforeach
                </div>
            </section>

            <section class="lfv2-section">
                <span class="lfv2-kicker">{{ $pageData['fit_eyebrow'] }}</span>
                <h2>{{ $pageData['fit_title'] }}</h2>
                <p>{{ $pageData['fit_lead'] }}</p>
                <div class="lfv2-grid2">
                    <article class="lfv2-card">
                        <h3>Kimin icin</h3>
                        <ul>
                            @foreach ($pageData['fit_for'] as $item)
                                <li>{{ $item }}</li>
                            @endforeach
                        </ul>
                    </article>
                    <article class="lfv2-card">
                        <h3>Kimin icin degil</h3>
                        <ul>
                            @foreach ($pageData['fit_not_for'] as $item)
                                <li>{{ $item }}</li>
                            @endforeach
                        </ul>
                    </article>
                </div>
            </section>

            <section class="lfv2-section" id="programlar">
                <span class="lfv2-kicker">{{ $pageData['resource_eyebrow'] }}</span>
                <h2>{{ $pageData['resource_title'] }}</h2>
                <div class="lfv2-grid2">
                    @foreach ($pageData['resource_columns'] as $column)
                        <article class="lfv2-card">
                            <h3>{{ $column['label'] }}</h3>
                            <ul>
                                @foreach ($column['items'] as $item)
                                    <li>{{ $item }}</li>
                                @endforeach
                            </ul>
                        </article>
                    @endforeach
                </div>
                <div class="lfv2-programs">
                    @foreach ($downloads as $program)
                        <article class="lfv2-program">
                            @if (!empty($program['cover_url']))
                                <img src="{{ $program['cover_url'] }}" alt="{{ $program['title'] }}" loading="lazy" />
                            @endif
                            <div>
                                <span class="lfv2-kicker">{{ $program['label'] }}</span>
                                <h3>{{ $program['title'] }}</h3>
                                <p>{{ $program['subtitle'] }}</p>
                                <ul>
                                    @foreach ($program['bullets'] as $bullet)
                                        <li>{{ $bullet }}</li>
                                    @endforeach
                                </ul>
                                <div class="lfv2-program-footer">
                                    <strong>{{ $program['result'] }}</strong>
                                    @if (!empty($program['file_url']))
                                        <a href="{{ $program['file_url'] }}" target="_blank" rel="noopener">Program Detayi</a>
                                    @endif
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
            </section>

            <section class="lfv2-section lfv2-reveal">
                <span class="lfv2-kicker">{{ $pageData['process_eyebrow'] }}</span>
                <h2>{{ $pageData['process_title'] }}</h2>
                <div class="lfv2-grid4">
                    @foreach ($pageData['steps'] as $step)
                        <article class="lfv2-card">
                            <small>0{{ $loop->iteration }}</small>
                            <h3>{{ $step['title'] }}</h3>
                            <p>{{ $step['description'] }}</p>
                        </article>
                    @endforeach
                </div>
            </section>

            @if (!empty($resultSlides))
                <section class="lfv2-section lfv2-reveal" id="sonuclar">
                    <span class="lfv2-kicker">Sonuclar</span>
                    <h2>Gercek deneyimlerden ozetler</h2>
                    <div class="lfv2-slider" data-lfv2-slider>
                        <div class="lfv2-slider-track">
                            @foreach ($resultSlides as $slide)
                                <article class="lfv2-slide">
                                    <span class="lfv2-kicker">{{ $slide['tag'] }}</span>
                                    <h3>{{ $slide['title'] }}</h3>
                                    <strong>{{ $slide['highlight'] }}</strong>
                                    <p>{{ $slide['text'] }}</p>
                                </article>
                            @endforeach
                        </div>
                        <div class="lfv2-slider-dots">
                            @foreach ($resultSlides as $slide)
                                <button type="button" data-lfv2-dot aria-label="Slide {{ $loop->iteration }}"></button>
                            @endforeach
                        </div>
                    </div>
                </section>
            @endif

            <section class="lfv2-section lfv2-reveal">
                <div class="lfv2-grid2">
                    <article class="lfv2-card">
                        <span class="lfv2-kicker">{{ $pageData['milestones_eyebrow'] }}</span>
                        <h3>Program sonunda neyi guclendirmek istiyoruz?</h3>
                        <div class="lfv2-grid2">
                            @foreach ($milestones as $milestone)
                                <div class="lfv2-subcard">
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
                    <article class="lfv2-card">
                        <span class="lfv2-kicker">{{ $pageData['reasons_eyebrow'] }}</span>
                        <h3>{{ $pageData['reasons_title'] }}</h3>
                        <div class="lfv2-chip-row">
                            @foreach ($pageData['reasons'] as $reason)
                                <span>{{ $reason }}</span>
                            @endforeach
                        </div>
                    </article>
                </div>
            </section>

            <section class="lfv2-section lfv2-reveal" id="videolar">
                <span class="lfv2-kicker">{{ $pageData['proof_eyebrow'] }}</span>
                <h2>{{ $pageData['proof_title'] }}</h2>
                <p>{{ $pageData['proof_lead'] }}</p>
                @if (!empty($featuredMedia))
                    <article class="lfv2-feature-video">
                        <video controls preload="metadata" playsinline @if (!empty($featuredMedia['poster_url'])) poster="{{ $featuredMedia['poster_url'] }}" @endif>
                            <source src="{{ $featuredMedia['file_url'] }}" type="video/mp4">
                        </video>
                        <div>
                            <span class="lfv2-kicker">{{ $featuredMedia['category'] }} | {{ $featuredMedia['duration'] }}</span>
                            <h3>{{ $featuredMedia['title'] }}</h3>
                            <p>{{ $featuredMedia['description'] }}</p>
                            <a href="{{ $featuredMedia['file_url'] }}" target="_blank" rel="noopener">Videoyu yeni sekmede ac</a>
                        </div>
                    </article>
                    <div class="lfv2-grid3">
                        @foreach ($secondaryMedia as $item)
                            <article class="lfv2-card">
                                <video controls preload="metadata" playsinline @if (!empty($item['poster_url'])) poster="{{ $item['poster_url'] }}" @endif>
                                    <source src="{{ $item['file_url'] }}" type="video/mp4">
                                </video>
                                <h3>{{ $item['title'] }}</h3>
                                <p>{{ $item['description'] }}</p>
                            </article>
                        @endforeach
                    </div>
                @endif
            </section>

            <section class="lfv2-section lfv2-reveal" id="fiyat">
                <span class="lfv2-kicker">{{ $pageData['pricing_eyebrow'] }}</span>
                <h2>{{ $pageData['pricing_title'] }}</h2>
                <p>{{ $pageData['pricing_lead'] }}</p>
                <div class="lfv2-grid2">
                    @foreach ($pageData['packages'] as $package)
                        <article class="lfv2-card @if (!empty($package['featured'])) lfv2-card--light @endif">
                            <strong>{{ $package['name'] }}</strong>
                            <h3>{{ $package['price'] }}</h3>
                            <span>{{ $package['unit'] }}</span>
                            <p>{{ $package['note'] }}</p>
                            <a class="lfv2-btn" href="{{ $applyUrl }}">Basvur</a>
                        </article>
                    @endforeach
                </div>
                <div class="lfv2-grid3">
                    @foreach ($pricingNotes as $note)
                        <article class="lfv2-card"><p>{{ $note }}</p></article>
                    @endforeach
                </div>
            </section>

            <section class="lfv2-section lfv2-reveal" id="sss">
                <span class="lfv2-kicker">SSS</span>
                <h2>Karar oncesi en cok sorulanlar</h2>
                <div class="lfv2-faq">
                    @foreach ($pageData['faq'] as $faq)
                        <details class="lfv2-card">
                            <summary>{{ $faq['question'] }}</summary>
                            <p>{{ $faq['answer'] }}</p>
                        </details>
                    @endforeach
                </div>
            </section>

            <section class="lfv2-cta lfv2-reveal">
                <div>
                    <span class="lfv2-kicker">Son adim</span>
                    <h2>{{ $pageData['cta_title'] }}</h2>
                    <p>{{ $pageData['cta_text'] }}</p>
                </div>
                <div class="lfv2-actions">
                    <a class="lfv2-btn" href="{{ $applyUrl }}">Programa Basvur</a>
                    <a class="lfv2-btn lfv2-btn--ghost" href="{{ $testUrl }}">Seviye Tespiti</a>
                </div>
            </section>

            <footer class="lfv2-footer lfv2-reveal">
                <div>{{ $siteName }} | LinguFranca Performans Sistemi</div>
                <div class="lfv2-footer-links">
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
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&family=Sora:wght@600;700;800&display=swap" rel="stylesheet">
    <style>
        .lfv2 { background: #070b14; color: #edf2ff; font-family: Inter, sans-serif; padding: 24px 0 80px; }
        .lfv2-wrap { width: min(1160px, calc(100vw - 28px)); margin: 0 auto; }
        .lfv2 h1, .lfv2 h2, .lfv2 h3 { font-family: Sora, Inter, sans-serif; margin: 0; letter-spacing: -0.02em; }
        .lfv2 p { color: #b8c1d6; line-height: 1.8; }
        .lfv2-topbar { position: sticky; top: 10px; z-index: 20; display: grid; grid-template-columns: auto 1fr auto; gap: 16px; align-items: center; padding: 12px 14px; border-radius: 16px; border: 1px solid rgba(255,255,255,.12); background: rgba(9,14,25,.75); backdrop-filter: blur(8px); }
        .lfv2-brand img { height: 34px; width: auto; filter: brightness(0) invert(1); }
        .lfv2-nav { display: flex; justify-content: center; flex-wrap: wrap; gap: 12px; }
        .lfv2-nav a, .lfv2-footer a { color: #b8c1d6; text-decoration: none; font-size: 13px; font-weight: 700; }
        .lfv2-actions { display: flex; gap: 10px; flex-wrap: wrap; }
        .lfv2-btn { min-height: 44px; display: inline-flex; align-items: center; justify-content: center; border-radius: 999px; padding: 0 18px; border: 1px solid transparent; background: #fff; color: #0d1322; text-decoration: none; text-transform: uppercase; font-size: 11px; letter-spacing: .08em; font-weight: 900; }
        .lfv2-btn--ghost { background: transparent; border-color: rgba(255,255,255,.24); color: #edf2ff; }
        .lfv2-kicker { display: inline-flex; align-items: center; min-height: 30px; padding: 0 12px; border-radius: 999px; border: 1px solid rgba(255,255,255,.16); background: rgba(255,255,255,.06); color: #dbe3f7; font-size: 10px; letter-spacing: .12em; text-transform: uppercase; font-weight: 800; }
        .lfv2-hero { display: grid; grid-template-columns: minmax(0, 1.08fr) minmax(320px, .92fr); gap: 20px; padding-top: 28px; align-items: stretch; }
        .lfv2-hero h1 { font-size: clamp(42px, 5.2vw, 74px); max-width: 11ch; }
        .lfv2-hero p { margin: 14px 0 0; }
        .lfv2-chip-row { display: flex; gap: 10px; flex-wrap: wrap; margin-top: 18px; }
        .lfv2-chip-row span { min-height: 34px; display: inline-flex; align-items: center; border-radius: 999px; padding: 0 12px; border: 1px solid rgba(255,255,255,.14); background: rgba(255,255,255,.05); font-size: 12px; color: #dbe3f7; }
        .lfv2-stats { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 10px; margin-top: 18px; }
        .lfv2-stats article { border-radius: 14px; border: 1px solid rgba(255,255,255,.12); background: rgba(255,255,255,.04); padding: 12px; }
        .lfv2-stats strong { font-size: 27px; display: block; }
        .lfv2-stats span { font-size: 11px; color: #b8c1d6; text-transform: uppercase; letter-spacing: .08em; }
        .lfv2-panel, .lfv2-card, .lfv2-quote, .lfv2-subcard, .lfv2-feature-video, .lfv2-cta { border: 1px solid rgba(255,255,255,.12); border-radius: 20px; background: linear-gradient(180deg, #111a2e 0%, #131f35 100%); }
        .lfv2-panel { overflow: hidden; display: grid; }
        .lfv2-panel img { width: 100%; height: 100%; min-height: 260px; object-fit: cover; }
        .lfv2-quote { margin: 14px; padding: 16px; }
        .lfv2-quote span { font-size: 10px; text-transform: uppercase; letter-spacing: .11em; color: #dbe3f7; font-weight: 800; }
        .lfv2-marquee { margin-top: 20px; border-top: 1px solid rgba(255,255,255,.12); border-bottom: 1px solid rgba(255,255,255,.12); padding: 14px 0; display: flex; gap: 10px; flex-wrap: wrap; }
        .lfv2-marquee span { min-height: 34px; display: inline-flex; align-items: center; border-radius: 999px; padding: 0 12px; border: 1px solid rgba(255,255,255,.14); background: rgba(255,255,255,.05); font-size: 12px; }
        .lfv2-section { padding-top: 74px; position: relative; }
        .lfv2-section::before {
            content: "";
            position: absolute;
            left: 0;
            right: 0;
            top: 28px;
            height: 1px;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,.22), transparent);
        }
        .lfv2-section h2 { font-size: clamp(30px, 3.7vw, 52px); max-width: 12ch; margin-top: 12px; }
        .lfv2-grid2, .lfv2-grid3, .lfv2-grid4 { display: grid; gap: 14px; margin-top: 18px; }
        .lfv2-grid2 { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        .lfv2-grid3 { grid-template-columns: repeat(3, minmax(0, 1fr)); }
        .lfv2-grid4 { grid-template-columns: repeat(4, minmax(0, 1fr)); }
        .lfv2-card { padding: 20px; }
        .lfv2-card small { font-size: 34px; color: rgba(255,255,255,.28); font-weight: 800; }
        .lfv2-card h3 { font-size: 24px; margin-top: 8px; }
        .lfv2-card ul { margin: 12px 0 0; padding: 0; list-style: none; display: grid; gap: 9px; }
        .lfv2-card li { position: relative; padding-left: 15px; color: #b8c1d6; line-height: 1.7; }
        .lfv2-card li::before { content: ""; position: absolute; left: 0; top: 10px; width: 6px; height: 6px; border-radius: 999px; background: #dbe3f7; }
        .lfv2-programs { display: grid; gap: 14px; margin-top: 18px; }
        .lfv2-program { overflow: hidden; display: grid; grid-template-columns: minmax(220px, .78fr) minmax(0, 1.22fr); border: 1px solid rgba(255,255,255,.12); border-radius: 20px; background: linear-gradient(180deg, #111a2e 0%, #131f35 100%); }
        .lfv2-program img { width: 100%; height: 100%; object-fit: cover; }
        .lfv2-program > div { padding: 20px; display: grid; gap: 8px; }
        .lfv2-program-footer { display: flex; justify-content: space-between; align-items: center; gap: 10px; margin-top: 8px; flex-wrap: wrap; }
        .lfv2-program-footer a { color: #edf2ff; text-decoration: none; font-size: 11px; text-transform: uppercase; letter-spacing: .09em; font-weight: 800; }
        .lfv2-subcard { padding: 14px; border-radius: 14px; background: rgba(255,255,255,.04); }
        .lfv2-subcard strong { font-size: 13px; text-transform: uppercase; letter-spacing: .07em; }
        .lfv2-feature-video { display: grid; grid-template-columns: minmax(0, 1.24fr) minmax(300px, .76fr); overflow: hidden; margin-top: 16px; }
        .lfv2-feature-video video { width: 100%; height: 100%; object-fit: cover; background: #000; }
        .lfv2-feature-video > div { padding: 20px; display: grid; align-content: start; gap: 10px; }
        .lfv2-feature-video a { color: #edf2ff; text-decoration: none; font-size: 11px; text-transform: uppercase; letter-spacing: .09em; font-weight: 800; }
        .lfv2-card video { width: 100%; aspect-ratio: 16 / 10; border-radius: 12px; background: #000; margin-bottom: 12px; }
        .lfv2-card--light { background: linear-gradient(180deg, #f4f6fd 0%, #e6ebf8 100%); }
        .lfv2-card--light, .lfv2-card--light h3, .lfv2-card--light p, .lfv2-card--light strong, .lfv2-card--light span { color: #0d1322 !important; }
        .lfv2-card--light .lfv2-btn { background: #0d1322; color: #fff; }
        .lfv2-faq { display: grid; gap: 12px; margin-top: 16px; }
        .lfv2-faq summary { cursor: pointer; list-style: none; font-family: Sora, Inter, sans-serif; font-size: 20px; font-weight: 700; }
        .lfv2-faq summary::-webkit-details-marker { display: none; }
        .lfv2-slider { margin-top: 18px; overflow: hidden; border-radius: 20px; border: 1px solid rgba(255,255,255,.12); background: linear-gradient(180deg, #111a2e 0%, #131f35 100%); }
        .lfv2-slider-track { display: flex; transition: transform .55s cubic-bezier(.2,.8,.2,1); }
        .lfv2-slide { min-width: 100%; padding: 26px; }
        .lfv2-slide h3 { margin-top: 10px; font-size: 28px; }
        .lfv2-slide strong { display: block; margin-top: 8px; font-size: 18px; color: #eef3ff; }
        .lfv2-slide p { margin-top: 10px; max-width: 74ch; }
        .lfv2-slider-dots { display: flex; gap: 8px; padding: 0 26px 22px; }
        .lfv2-slider-dots button { width: 10px; height: 10px; border-radius: 999px; border: 0; background: rgba(255,255,255,.25); cursor: pointer; }
        .lfv2-slider-dots button.is-active { width: 28px; background: #ffffff; }
        .lfv2-cta { margin-top: 84px; padding: 24px; display: flex; align-items: end; justify-content: space-between; gap: 14px; }
        .lfv2-footer { margin-top: 24px; padding-top: 16px; border-top: 1px solid rgba(255,255,255,.12); color: #b8c1d6; display: flex; justify-content: space-between; flex-wrap: wrap; gap: 10px; text-transform: uppercase; letter-spacing: .08em; font-size: 12px; font-weight: 700; }
        .lfv2-footer-links { display: flex; gap: 12px; flex-wrap: wrap; }
        .lfv2-reveal { opacity: 0; transform: translateY(24px); transition: opacity .65s ease, transform .65s ease; }
        .lfv2-reveal.is-visible { opacity: 1; transform: translateY(0); }
        @media (max-width: 1100px) {
            .lfv2-hero, .lfv2-program, .lfv2-feature-video, .lfv2-grid4, .lfv2-grid3, .lfv2-grid2, .lfv2-cta { grid-template-columns: 1fr; }
            .lfv2-stats { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        }
        @media (max-width: 820px) {
            .lfv2-topbar { grid-template-columns: 1fr; }
            .lfv2-nav { display: none; }
            .lfv2 h1 { font-size: clamp(36px, 10vw, 48px); }
            .lfv2-stats { grid-template-columns: 1fr; }
        }
    </style>
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var revealItems = document.querySelectorAll('.lfv2-reveal');
            if (revealItems.length) {
                var revealObserver = new IntersectionObserver(function (entries, observer) {
                    entries.forEach(function (entry) {
                        if (entry.isIntersecting) {
                            entry.target.classList.add('is-visible');
                            observer.unobserve(entry.target);
                        }
                    });
                }, { threshold: 0.14 });
                revealItems.forEach(function (item) { revealObserver.observe(item); });
            }

            var slider = document.querySelector('[data-lfv2-slider]');
            if (!slider) return;

            var track = slider.querySelector('.lfv2-slider-track');
            var slides = slider.querySelectorAll('.lfv2-slide');
            var dots = slider.querySelectorAll('[data-lfv2-dot]');
            if (!track || !slides.length) return;

            var active = 0;
            var timer = null;

            function paint() {
                track.style.transform = 'translateX(-' + (active * 100) + '%)';
                dots.forEach(function (dot, index) {
                    dot.classList.toggle('is-active', index === active);
                });
            }

            function go(index) {
                active = (index + slides.length) % slides.length;
                paint();
            }

            function start() {
                stop();
                timer = setInterval(function () {
                    go(active + 1);
                }, 4200);
            }

            function stop() {
                if (timer) {
                    clearInterval(timer);
                    timer = null;
                }
            }

            dots.forEach(function (dot, index) {
                dot.addEventListener('click', function () {
                    go(index);
                    start();
                });
            });

            slider.addEventListener('mouseenter', stop);
            slider.addEventListener('mouseleave', start);

            paint();
            start();
        });
    </script>
@endpush
