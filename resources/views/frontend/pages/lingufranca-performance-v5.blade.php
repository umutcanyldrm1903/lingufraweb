@extends('frontend.layouts.master')

@php
    $siteName = $setting->app_name ?? config('app.name');
    $canonicalUrl = route('lingufranca-performance');
    $applyUrl = route('contact.index');
    $testUrl = route('placement-test.show');
    $homeUrl = route('home');

    // Keep using existing config-driven content, but present in a folio-like layout.
    $heroBadges = $pageData['hero_badges'] ?? [];
    $heroStats = $pageData['hero_stats'] ?? [];
    $milestones = $pageData['milestones'] ?? [];
    $pricingNotes = $pageData['pricing_notes'] ?? [];
    $featuredMedia = $mediaLibrary[0] ?? null;
    $secondaryMedia = array_slice($mediaLibrary, 1, 6);
@endphp

@section('meta_title', ($pageData['meta_title'] ?? 'LinguFranca') . ' | ' . $siteName)
@section('meta_description', $pageData['meta_description'] ?? '')
@section('meta_keywords', $pageData['meta_keywords'] ?? '')
@section('canonical_url', $canonicalUrl)
@section('meta_image', $pageData['meta_image_url'] ?? '')
@section('hide_public_header', '1')
@section('hide_public_footer', '1')

@section('contents')
    <section class="folio5">
        <div class="folio5-shell">
            <header class="folio5-topbar">
                <a class="folio5-brand" href="{{ $homeUrl }}">
                    @if (!empty($setting?->logo))
                        <img src="{{ asset($setting->logo) }}" alt="{{ $siteName }}" />
                    @else
                        <span>{{ $siteName }}</span>
                    @endif
                </a>

                <nav class="folio5-nav" aria-label="Bolumler">
                    <a href="#about">About</a>
                    <a href="#programlar">Programs</a>
                    <a href="#sonuclar">Results</a>
                    <a href="#videolar">Media</a>
                    <a href="#fiyat">Pricing</a>
                    <a href="#sss">FAQ</a>
                </nav>

                <div class="folio5-actions">
                    <a class="folio5-btn folio5-btn--ghost" href="{{ $testUrl }}">Seviye Tespiti</a>
                    <a class="folio5-btn" href="{{ $applyUrl }}">Programa Basvur</a>
                </div>
            </header>

            <section class="folio5-hero" id="top">
                <div class="folio5-hero__copy">
                    <div class="folio5-kicker">{{ $pageData['eyebrow'] ?? '' }}</div>
                    <h1 class="folio5-title">{{ $pageData['title'] ?? '' }}</h1>
                    <p class="folio5-lead">{{ $pageData['lead'] ?? '' }}</p>

                    @if (!empty($heroBadges))
                        <div class="folio5-chips">
                            @foreach ($heroBadges as $badge)
                                <span class="folio5-chip">{{ $badge }}</span>
                            @endforeach
                        </div>
                    @endif

                    <div class="folio5-hero__cta">
                        <a class="folio5-btn" href="{{ $applyUrl }}">Programa Basvur</a>
                        <a class="folio5-btn folio5-btn--ghost" href="#videolar">Video Kayitlarini Incele</a>
                    </div>

                    @if (!empty($heroStats))
                        <div class="folio5-metrics">
                            @foreach ($heroStats as $stat)
                                <article class="folio5-metric">
                                    <strong>{{ $stat['value'] }}</strong>
                                    <span>{{ $stat['label'] }}</span>
                                </article>
                            @endforeach
                        </div>
                    @endif
                </div>

                <div class="folio5-hero__panel">
                    @if (!empty($pageData['hero_primary_visual']))
                        <img class="folio5-cover" src="{{ $pageData['hero_primary_visual'] }}" alt="{{ $pageData['meta_title'] ?? '' }}" loading="lazy" />
                    @endif
                    <div class="folio5-quote">
                        <div class="folio5-quote__tag">{{ $pageData['hero_quote_title'] ?? '' }}</div>
                        <div class="folio5-quote__text">{{ $pageData['hero_quote'] ?? '' }}</div>
                    </div>
                </div>
            </section>

            <section class="folio5-section folio5-reveal" id="about">
                <div class="folio5-section__head">
                    <h2>{{ $pageData['manifesto_title'] ?? 'About' }}</h2>
                    <p>{{ $pageData['manifesto_lead'] ?? '' }}</p>
                </div>

                <div class="folio5-grid3">
                    @foreach (($pageData['manifesto_points'] ?? []) as $point)
                        <article class="folio5-card">
                            <div class="folio5-card__top">
                                <span class="folio5-badge">{{ $point['title'] }}</span>
                            </div>
                            <p>{{ $point['description'] }}</p>
                        </article>
                    @endforeach
                </div>
            </section>

            <section class="folio5-section folio5-reveal" id="programlar">
                <div class="folio5-section__head">
                    <h2>{{ $pageData['resource_title'] ?? 'Programs' }}</h2>
                    <p>{{ $pageData['resource_eyebrow'] ?? '' }}</p>
                </div>

                <div class="folio5-programs">
                    @foreach ($downloads as $program)
                        <article class="folio5-program">
                            <div class="folio5-program__media">
                                @if (!empty($program['cover_url']))
                                    <img src="{{ $program['cover_url'] }}" alt="{{ $program['title'] }}" loading="lazy" />
                                @endif
                            </div>
                            <div class="folio5-program__body">
                                <div class="folio5-program__meta">
                                    <span class="folio5-badge">{{ $program['label'] ?? '' }}</span>
                                    <span class="folio5-meta">{{ $program['result'] ?? '' }}</span>
                                </div>
                                <h3>{{ $program['title'] ?? '' }}</h3>
                                <p>{{ $program['subtitle'] ?? '' }}</p>
                                <ul class="folio5-list">
                                    @foreach (($program['bullets'] ?? []) as $b)
                                        <li>{{ $b }}</li>
                                    @endforeach
                                </ul>
                                <div class="folio5-program__footer">
                                    @if (!empty($program['file_url']))
                                        <a class="folio5-link" href="{{ $program['file_url'] }}" target="_blank" rel="noopener">Program Detayi</a>
                                    @endif
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
            </section>

            <section class="folio5-section folio5-reveal" id="sonuclar">
                <div class="folio5-section__head">
                    <h2>Results</h2>
                    <p>{{ $pageData['reasons_title'] ?? '' }}</p>
                </div>

                <div class="folio5-timeline">
                    @foreach (($pageData['steps'] ?? []) as $step)
                        <article class="folio5-tl-item">
                            <div class="folio5-tl-dot" aria-hidden="true"></div>
                            <div class="folio5-tl-body">
                                <h3>{{ $step['title'] }}</h3>
                                <p>{{ $step['description'] }}</p>
                            </div>
                        </article>
                    @endforeach
                </div>

                @if (!empty($milestones))
                    <div class="folio5-grid2 folio5-milestones">
                        @foreach ($milestones as $milestone)
                            <article class="folio5-card">
                                <div class="folio5-card__top">
                                    <span class="folio5-badge">{{ $milestone['label'] }}</span>
                                </div>
                                <ul class="folio5-list">
                                    @foreach (($milestone['items'] ?? []) as $item)
                                        <li>{{ $item }}</li>
                                    @endforeach
                                </ul>
                            </article>
                        @endforeach
                    </div>
                @endif
            </section>

            <section class="folio5-section folio5-reveal" id="videolar">
                <div class="folio5-section__head">
                    <h2>{{ $pageData['proof_title'] ?? 'Media' }}</h2>
                    <p>{{ $pageData['proof_lead'] ?? '' }}</p>
                </div>

                @if (!empty($featuredMedia))
                    <div class="folio5-media">
                        <article class="folio5-media__feature">
                            <video controls preload="metadata" playsinline @if (!empty($featuredMedia['poster_url'])) poster="{{ $featuredMedia['poster_url'] }}" @endif>
                                <source src="{{ $featuredMedia['file_url'] }}" type="video/mp4">
                            </video>
                            <div class="folio5-media__body">
                                <div class="folio5-program__meta">
                                    <span class="folio5-badge">{{ $featuredMedia['category'] ?? '' }}</span>
                                    <span class="folio5-meta">{{ $featuredMedia['duration'] ?? '' }}</span>
                                </div>
                                <h3>{{ $featuredMedia['title'] ?? '' }}</h3>
                                <p>{{ $featuredMedia['description'] ?? '' }}</p>
                                <a class="folio5-link" href="{{ $featuredMedia['file_url'] }}" target="_blank" rel="noopener">Videoyu yeni sekmede ac</a>
                            </div>
                        </article>

                        <div class="folio5-grid3">
                            @foreach ($secondaryMedia as $item)
                                <article class="folio5-card">
                                    <video controls preload="metadata" playsinline @if (!empty($item['poster_url'])) poster="{{ $item['poster_url'] }}" @endif>
                                        <source src="{{ $item['file_url'] }}" type="video/mp4">
                                    </video>
                                    <div class="folio5-media__body">
                                        <div class="folio5-program__meta">
                                            <span class="folio5-badge">{{ $item['category'] ?? '' }}</span>
                                            <span class="folio5-meta">{{ $item['duration'] ?? '' }}</span>
                                        </div>
                                        <h3>{{ $item['title'] ?? '' }}</h3>
                                        <p>{{ $item['description'] ?? '' }}</p>
                                    </div>
                                </article>
                            @endforeach
                        </div>
                    </div>
                @else
                    <div class="folio5-card">Video kayitlari gecici olarak yuklenemedi.</div>
                @endif
            </section>

            <section class="folio5-section folio5-reveal" id="fiyat">
                <div class="folio5-section__head">
                    <h2>{{ $pageData['pricing_title'] ?? 'Pricing' }}</h2>
                    <p>{{ $pageData['pricing_lead'] ?? '' }}</p>
                </div>

                <div class="folio5-grid2">
                    @foreach (($pageData['packages'] ?? []) as $package)
                        <article class="folio5-card @if (!empty($package['featured'])) folio5-card--featured @endif">
                            @if (!empty($package['featured']))
                                <div class="folio5-badge folio5-badge--float">Onerilen</div>
                            @endif
                            <div class="folio5-price-name">{{ $package['name'] ?? '' }}</div>
                            <div class="folio5-price">{{ $package['price'] ?? '' }}</div>
                            <div class="folio5-meta">{{ $package['unit'] ?? '' }}</div>
                            <p>{{ $package['note'] ?? '' }}</p>
                            <a class="folio5-btn folio5-btn--block" href="{{ $applyUrl }}">Basvur</a>
                        </article>
                    @endforeach
                </div>

                @if (!empty($pricingNotes))
                    <div class="folio5-grid2">
                        @foreach ($pricingNotes as $note)
                            <article class="folio5-card">
                                <p>{{ $note }}</p>
                            </article>
                        @endforeach
                    </div>
                @endif
            </section>

            <section class="folio5-section folio5-reveal" id="sss">
                <div class="folio5-section__head">
                    <h2>FAQ</h2>
                    <p>Karar oncesi en cok sorulanlar</p>
                </div>
                <div class="folio5-faq">
                    @foreach (($pageData['faq'] ?? []) as $faq)
                        <details class="folio5-faq__item">
                            <summary>{{ $faq['question'] }}</summary>
                            <p>{{ $faq['answer'] }}</p>
                        </details>
                    @endforeach
                </div>
            </section>

            <footer class="folio5-footer">
                <div>{{ $siteName }} | LinguFranca Performans Sistemi</div>
                <div class="folio5-footer__meta">
                    <span>Inspired by</span>
                    <a class="folio5-link" href="https://github.com/ayush013/folio" target="_blank" rel="noopener">ayush013/folio</a>
                    <span>·</span>
                    <a class="folio5-link" href="{{ asset('resources/licenses/folio.MIT-LICENSE.txt') }}" target="_blank" rel="noopener">MIT License</a>
                </div>
            </footer>
        </div>
    </section>
@endsection

@push('styles')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        .folio5 { background: #060812; color: #eaf1ff; font-family: Inter, system-ui, -apple-system, Segoe UI, sans-serif; padding: 18px 0 74px; }
        .folio5-shell { width: min(1180px, calc(100vw - 28px)); margin: 0 auto; }
        .folio5-topbar { position: sticky; top: 10px; z-index: 50; display: grid; grid-template-columns: auto 1fr auto; gap: 14px; align-items: center; padding: 12px 14px; border-radius: 16px; border: 1px solid rgba(255,255,255,.10); background: rgba(6,8,18,.72); backdrop-filter: blur(10px); }
        .folio5-brand { display: inline-flex; align-items: center; gap: 10px; text-decoration: none; color: #eaf1ff; font-weight: 900; }
        .folio5-brand img { height: 34px; width: auto; filter: brightness(0) invert(1); }
        .folio5-nav { display: flex; justify-content: center; gap: 14px; flex-wrap: wrap; }
        .folio5-nav a { color: rgba(234,241,255,.72); text-decoration: none; font-weight: 800; font-size: 13px; }
        .folio5-nav a:hover { color: #fff; }
        .folio5-actions { display: flex; gap: 10px; flex-wrap: wrap; justify-content: flex-end; }
        .folio5-btn { min-height: 44px; display: inline-flex; align-items: center; justify-content: center; padding: 0 18px; border-radius: 999px; border: 1px solid transparent; background: linear-gradient(135deg, #60a5fa, #2563eb); color: #061025; font-weight: 950; font-size: 11px; letter-spacing: .10em; text-transform: uppercase; text-decoration: none; }
        .folio5-btn--ghost { background: transparent; color: #eaf1ff; border-color: rgba(255,255,255,.20); }
        .folio5-btn--block { width: 100%; }
        .folio5-hero { display: grid; grid-template-columns: minmax(0, 1.08fr) minmax(340px, .92fr); gap: 22px; padding-top: 26px; align-items: start; }
        .folio5-kicker { display: inline-flex; align-items: center; min-height: 30px; padding: 0 12px; border-radius: 999px; border: 1px solid rgba(255,255,255,.14); background: rgba(255,255,255,.05); font-size: 10px; letter-spacing: .12em; text-transform: uppercase; font-weight: 900; width: fit-content; }
        .folio5-title { margin: 14px 0 0; font-size: clamp(42px, 5vw, 78px); line-height: .98; letter-spacing: -0.03em; font-weight: 950; }
        .folio5-lead { margin: 14px 0 0; color: rgba(234,241,255,.74); line-height: 1.9; font-weight: 650; max-width: 72ch; }
        .folio5-chips { margin-top: 16px; display: flex; gap: 10px; flex-wrap: wrap; }
        .folio5-chip { min-height: 34px; display: inline-flex; align-items: center; padding: 0 12px; border-radius: 999px; border: 1px solid rgba(255,255,255,.12); background: rgba(255,255,255,.04); color: rgba(234,241,255,.92); font-weight: 800; font-size: 12px; }
        .folio5-hero__cta { margin-top: 22px; display: flex; gap: 12px; flex-wrap: wrap; }
        .folio5-metrics { margin-top: 22px; display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 12px; }
        .folio5-metric { border: 1px solid rgba(255,255,255,.10); background: rgba(255,255,255,.04); border-radius: 18px; padding: 14px; }
        .folio5-metric strong { display: block; font-size: 28px; font-weight: 950; }
        .folio5-metric span { display: block; margin-top: 6px; color: rgba(234,241,255,.68); font-size: 11px; letter-spacing: .08em; text-transform: uppercase; font-weight: 850; }
        .folio5-hero__panel { border-radius: 22px; overflow: hidden; border: 1px solid rgba(255,255,255,.10); background: rgba(255,255,255,.03); }
        .folio5-cover { width: 100%; min-height: 320px; object-fit: cover; display: block; }
        .folio5-quote { padding: 16px 18px; border-top: 1px solid rgba(255,255,255,.08); background: rgba(0,0,0,.12); }
        .folio5-quote__tag { font-size: 10px; letter-spacing: .12em; text-transform: uppercase; font-weight: 900; color: rgba(234,241,255,.92); }
        .folio5-quote__text { margin-top: 10px; color: rgba(234,241,255,.80); font-weight: 650; line-height: 1.8; }
        .folio5-section { margin-top: 74px; }
        .folio5-section__head h2 { margin: 0; font-size: 34px; letter-spacing: -0.02em; font-weight: 950; }
        .folio5-section__head p { margin: 10px 0 0; color: rgba(234,241,255,.72); line-height: 1.85; font-weight: 650; max-width: 86ch; }
        .folio5-grid2, .folio5-grid3 { display: grid; gap: 14px; margin-top: 18px; }
        .folio5-grid2 { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        .folio5-grid3 { grid-template-columns: repeat(3, minmax(0, 1fr)); }
        .folio5-card { border: 1px solid rgba(255,255,255,.10); background: rgba(255,255,255,.04); border-radius: 20px; padding: 20px; box-shadow: 0 18px 60px rgba(0,0,0,.18); }
        .folio5-card--featured { border-color: rgba(96,165,250,.35); background: rgba(96,165,250,.10); }
        .folio5-card__top { display: flex; justify-content: space-between; align-items: center; gap: 10px; }
        .folio5-badge { display: inline-flex; align-items: center; min-height: 30px; padding: 0 12px; border-radius: 999px; border: 1px solid rgba(255,255,255,.14); background: rgba(255,255,255,.05); font-size: 11px; letter-spacing: .09em; text-transform: uppercase; font-weight: 900; }
        .folio5-badge--float { margin-bottom: 8px; width: fit-content; }
        .folio5-card p { margin: 12px 0 0; color: rgba(234,241,255,.74); line-height: 1.85; font-weight: 650; }
        .folio5-programs { margin-top: 18px; display: grid; gap: 16px; }
        .folio5-program { border: 1px solid rgba(255,255,255,.10); background: rgba(255,255,255,.03); border-radius: 22px; overflow: hidden; display: grid; grid-template-columns: minmax(260px, .78fr) minmax(0, 1.22fr); }
        .folio5-program__media img { width: 100%; height: 100%; min-height: 240px; object-fit: cover; display: block; }
        .folio5-program__body { padding: 20px; display: grid; gap: 10px; align-content: start; }
        .folio5-program__meta { display: flex; gap: 10px; flex-wrap: wrap; align-items: center; }
        .folio5-meta { color: rgba(234,241,255,.70); font-weight: 700; font-size: 12px; }
        .folio5-program__body h3 { margin: 0; font-size: 24px; font-weight: 950; letter-spacing: -0.02em; }
        .folio5-list { margin: 12px 0 0; padding: 0; list-style: none; display: grid; gap: 10px; }
        .folio5-list li { position: relative; padding-left: 16px; color: rgba(234,241,255,.74); line-height: 1.75; font-weight: 650; }
        .folio5-list li::before { content: ""; position: absolute; left: 0; top: 10px; width: 7px; height: 7px; border-radius: 999px; background: rgba(96,165,250,.85); }
        .folio5-link { color: rgba(234,241,255,.92); text-decoration: none; font-weight: 900; font-size: 11px; letter-spacing: .10em; text-transform: uppercase; }
        .folio5-link:hover { text-decoration: underline; }
        .folio5-timeline { margin-top: 18px; border-left: 1px solid rgba(255,255,255,.14); padding-left: 16px; display: grid; gap: 14px; }
        .folio5-tl-item { display: grid; grid-template-columns: 18px 1fr; gap: 12px; align-items: start; }
        .folio5-tl-dot { width: 12px; height: 12px; border-radius: 999px; background: rgba(96,165,250,.90); margin-top: 10px; box-shadow: 0 0 0 6px rgba(96,165,250,.12); }
        .folio5-tl-body h3 { margin: 0; font-size: 18px; font-weight: 950; }
        .folio5-tl-body p { margin: 8px 0 0; color: rgba(234,241,255,.74); line-height: 1.8; font-weight: 650; }
        .folio5-media { margin-top: 18px; display: grid; gap: 14px; }
        .folio5-media__feature { border: 1px solid rgba(255,255,255,.10); background: rgba(255,255,255,.03); border-radius: 22px; overflow: hidden; display: grid; grid-template-columns: minmax(0, 1.22fr) minmax(320px, .78fr); }
        .folio5-media__feature video { width: 100%; height: 100%; object-fit: cover; background: #000; }
        .folio5-card video { width: 100%; aspect-ratio: 16/10; border-radius: 14px; background: #000; display: block; }
        .folio5-media__body { padding: 16px; display: grid; gap: 10px; align-content: start; }
        .folio5-price-name { font-weight: 950; text-transform: uppercase; letter-spacing: .08em; font-size: 12px; color: rgba(234,241,255,.72); }
        .folio5-price { margin-top: 10px; font-size: 42px; letter-spacing: -0.03em; font-weight: 950; line-height: 1; }
        .folio5-faq { display: grid; gap: 12px; margin-top: 18px; }
        .folio5-faq__item { border: 1px solid rgba(255,255,255,.10); background: rgba(255,255,255,.03); border-radius: 20px; padding: 16px; }
        .folio5-faq__item summary { cursor: pointer; font-weight: 950; list-style: none; }
        .folio5-faq__item summary::-webkit-details-marker { display: none; }
        .folio5-faq__item p { margin: 10px 0 0; color: rgba(234,241,255,.74); line-height: 1.8; font-weight: 650; }
        .folio5-footer { margin-top: 26px; padding-top: 18px; border-top: 1px solid rgba(255,255,255,.10); display: flex; justify-content: space-between; gap: 12px; flex-wrap: wrap; color: rgba(234,241,255,.62); font-weight: 850; font-size: 12px; letter-spacing: .08em; text-transform: uppercase; }
        .folio5-footer__meta { display: inline-flex; gap: 10px; align-items: center; flex-wrap: wrap; }
        .folio5-reveal { opacity: 0; transform: translateY(16px); transition: opacity .6s ease, transform .6s ease; }
        .folio5-reveal.is-visible { opacity: 1; transform: translateY(0); }
        @media (max-width: 1100px) { .folio5-hero { grid-template-columns: 1fr; } .folio5-metrics { grid-template-columns: repeat(2, minmax(0, 1fr)); } .folio5-grid3 { grid-template-columns: repeat(2, minmax(0, 1fr)); } .folio5-program { grid-template-columns: 1fr; } .folio5-media__feature { grid-template-columns: 1fr; } }
        @media (max-width: 820px) { .folio5-topbar { grid-template-columns: 1fr; } .folio5-nav { display: none; } .folio5-grid2, .folio5-grid3 { grid-template-columns: 1fr; } .folio5-metrics { grid-template-columns: 1fr; } }
    </style>
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Basic reveal (folio uses GSAP; we keep it lightweight for now)
            const items = document.querySelectorAll('.folio5-reveal');
            if (!items.length) return;
            const obs = new IntersectionObserver((entries, observer) => {
                entries.forEach(e => {
                    if (e.isIntersecting) {
                        e.target.classList.add('is-visible');
                        observer.unobserve(e.target);
                    }
                });
            }, { threshold: 0.12 });
            items.forEach(i => obs.observe(i));
        });
    </script>
@endpush

