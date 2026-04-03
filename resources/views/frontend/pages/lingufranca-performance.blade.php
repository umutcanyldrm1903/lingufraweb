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
@endphp

@section('meta_title', $pageData['meta_title'] . ' | ' . $siteName)
@section('meta_description', $pageData['meta_description'])
@section('meta_keywords', $pageData['meta_keywords'])
@section('canonical_url', $canonicalUrl)
@section('meta_image', $pageData['meta_image_url'] ?? '')
@section('hide_public_header', '1')
@section('hide_public_footer', '1')

@section('contents')
    <section class="lfps-page">
        <header class="lfps-topbar">
            <div class="lfps-shell lfps-topbar__inner">
                <a href="{{ $homeUrl }}" class="lfps-brand" aria-label="{{ $siteName }}">
                    @if (!empty($setting?->logo))
                        <img src="{{ asset($setting->logo) }}" alt="{{ $siteName }}">
                    @endif
                    <span>{{ $siteName }}</span>
                </a>
                <div class="lfps-topbar__actions">
                    <a href="{{ $homeUrl }}" class="lfps-chip">{{ __('Ana sayfa') }}</a>
                    <a href="{{ $testUrl }}" class="lfps-chip">{{ __('Seviye tespiti') }}</a>
                    <a href="{{ $applyUrl }}" class="lfps-button lfps-button--ghost">{{ __('Programa başvur') }}</a>
                </div>
            </div>
        </header>

        <div class="lfps-shell">
            <section class="lfps-hero" data-lfps-reveal>
                <div class="lfps-hero__copy">
                    <span class="lfps-eyebrow">{{ $pageData['eyebrow'] }}</span>
                    <h1>{{ $pageData['title'] }}</h1>
                    <p class="lfps-hero__lead">{{ $pageData['lead'] }}</p>
                    <div class="lfps-hero__badges">
                        @foreach ($pageData['hero_badges'] as $badge)
                            <span>{{ $badge }}</span>
                        @endforeach
                    </div>
                    <div class="lfps-hero__actions">
                        <a href="{{ $applyUrl }}" class="lfps-button">{{ __('Programa başvur') }}</a>
                        <a href="{{ $testUrl }}" class="lfps-button lfps-button--ghost">{{ __('Ön değerlendirme başlat') }}</a>
                    </div>
                    <div class="lfps-hero__stats">
                        @foreach ($pageData['hero_stats'] as $stat)
                            <article>
                                <strong>{{ $stat['value'] }}</strong>
                                <span>{{ $stat['label'] }}</span>
                            </article>
                        @endforeach
                    </div>
                </div>
                <div class="lfps-hero__visuals">
                    <div class="lfps-hero__frame lfps-hero__frame--primary">
                        @if (!empty($pageData['hero_primary_visual']))
                            <img src="{{ $pageData['hero_primary_visual'] }}" alt="{{ __('LinguFranca Genel İngilizce PDF kapağı') }}">
                        @endif
                    </div>
                    <div class="lfps-hero__frame lfps-hero__frame--secondary">
                        @if (!empty($pageData['hero_secondary_visual']))
                            <img src="{{ $pageData['hero_secondary_visual'] }}" alt="{{ __('LinguFranca Sınav Programı PDF kapağı') }}">
                        @endif
                    </div>
                </div>
            </section>

            <section class="lfps-marquee" data-lfps-reveal>
                @foreach ($pageData['press_badges'] as $badge)
                    <span>{{ $badge }}</span>
                @endforeach
            </section>

            <section class="lfps-section lfps-section--manifesto" data-lfps-reveal>
                <div class="lfps-section__head">
                    <span class="lfps-eyebrow">{{ $pageData['manifesto_eyebrow'] }}</span>
                    <h2>{{ $pageData['manifesto_title'] }}</h2>
                    <p>{{ $pageData['manifesto_lead'] }}</p>
                </div>
                <div class="lfps-values">
                    @foreach ($pageData['value_points'] as $point)
                        <article class="lfps-value">
                            <span class="lfps-value__index">{{ str_pad((string) ($loop->iteration), 2, '0', STR_PAD_LEFT) }}</span>
                            <h3>{{ $point['title'] }}</h3>
                            <p>{{ $point['description'] }}</p>
                        </article>
                    @endforeach
                </div>
            </section>

            <section class="lfps-section" data-lfps-reveal>
                <div class="lfps-section__head">
                    <span class="lfps-eyebrow">{{ __('Program dosyaları') }}</span>
                    <h2>{{ __('Zip içindeki tüm PDF akışları') }}</h2>
                    <p>{{ __('Her program kartı kendi PDF dosyasını açar. Genel İngilizce, IELTS ve PTE tarafını ayrı ayrı inceleyebilirsiniz.') }}</p>
                </div>
                <div class="lfps-programs">
                    @foreach ($downloads as $download)
                        <article class="lfps-program">
                            <div class="lfps-program__cover" @if(!empty($download['cover_url'])) style="background-image:url('{{ $download['cover_url'] }}')" @endif>
                                <span>{{ $download['meta'] }}</span>
                            </div>
                            <div class="lfps-program__body">
                                <h3>{{ $download['title'] }}</h3>
                                <p>{{ $download['subtitle'] }}</p>
                                <ul>
                                    @foreach ($download['bullets'] as $bullet)
                                        <li>{{ $bullet }}</li>
                                    @endforeach
                                </ul>
                            </div>
                            <div class="lfps-program__actions">
                                @if (!empty($download['file_url']))
                                    <a href="{{ $download['file_url'] }}" target="_blank" rel="noopener" class="lfps-button">{{ __('PDF aç') }}</a>
                                @endif
                            </div>
                        </article>
                    @endforeach
                </div>
            </section>

            <section class="lfps-section lfps-section--fit" data-lfps-reveal>
                <div class="lfps-section__head">
                    <span class="lfps-eyebrow">{{ $pageData['fit_eyebrow'] }}</span>
                    <h2>{{ $pageData['fit_title'] }}</h2>
                    <p>{{ $pageData['fit_lead'] }}</p>
                </div>
                <div class="lfps-fit">
                    <article class="lfps-fit__panel">
                        <h3>{{ __('Kimler için uygundur?') }}</h3>
                        <ul>
                            @foreach ($pageData['fit_for'] as $item)
                                <li>{{ $item }}</li>
                            @endforeach
                        </ul>
                    </article>
                    <article class="lfps-fit__panel lfps-fit__panel--muted">
                        <h3>{{ __('Kimler için uygun değildir?') }}</h3>
                        <ul>
                            @foreach ($pageData['fit_not_for'] as $item)
                                <li>{{ $item }}</li>
                            @endforeach
                        </ul>
                    </article>
                </div>
            </section>

            <section class="lfps-section" data-lfps-reveal>
                <div class="lfps-section__head">
                    <span class="lfps-eyebrow">{{ $pageData['includes_eyebrow'] }}</span>
                    <h2>{{ $pageData['includes_title'] }}</h2>
                </div>
                <div class="lfps-includes">
                    @foreach ($pageData['includes'] as $item)
                        <article>{{ $item }}</article>
                    @endforeach
                </div>
            </section>

            <section class="lfps-section" data-lfps-reveal>
                <div class="lfps-section__head">
                    <span class="lfps-eyebrow">{{ $pageData['process_eyebrow'] }}</span>
                    <h2>{{ $pageData['process_title'] }}</h2>
                </div>
                <div class="lfps-steps">
                    @foreach ($pageData['steps'] as $step)
                        <article class="lfps-step">
                            <strong>{{ str_pad((string) ($loop->iteration), 2, '0', STR_PAD_LEFT) }}</strong>
                            <h3>{{ $step['title'] }}</h3>
                            <p>{{ $step['description'] }}</p>
                        </article>
                    @endforeach
                </div>
            </section>

            <section class="lfps-section lfps-section--goalwords" data-lfps-reveal>
                <div class="lfps-section__head">
                    <span class="lfps-eyebrow">{{ $pageData['goals_eyebrow'] }}</span>
                </div>
                <div class="lfps-goalwords">
                    @foreach ($pageData['goal_words'] as $word)
                        <span>{{ $word }}</span>
                    @endforeach
                </div>
            </section>

            <section class="lfps-section" data-lfps-reveal>
                <div class="lfps-section__head">
                    <span class="lfps-eyebrow">{{ $pageData['proof_eyebrow'] }}</span>
                    <h2>{{ $pageData['proof_title'] }}</h2>
                    <p>{{ $pageData['proof_lead'] }}</p>
                </div>
                <div class="lfps-media-grid">
                    @foreach ($mediaLibrary as $item)
                        <article class="lfps-media-card">
                            <div class="lfps-media-card__meta">
                                <span>{{ $item['category'] }}</span>
                                <strong>{{ $item['duration'] }}</strong>
                            </div>
                            <h3>{{ $item['title'] }}</h3>
                            <p>{{ $item['description'] }}</p>
                            <button type="button" class="lfps-button lfps-button--ghost lfps-video-trigger"
                                data-video-url="{{ $item['file_url'] }}"
                                data-video-title="{{ $item['title'] }}">
                                {{ __('Videoyu aç') }}
                            </button>
                        </article>
                    @endforeach
                </div>
            </section>

            <section class="lfps-section lfps-section--pricing" data-lfps-reveal>
                <div class="lfps-section__head">
                    <span class="lfps-eyebrow">{{ $pageData['pricing_eyebrow'] }}</span>
                    <h2>{{ $pageData['pricing_title'] }}</h2>
                    <p>{{ $pageData['pricing_lead'] }}</p>
                </div>
                <div class="lfps-pricing">
                    <div class="lfps-pricing__plans">
                        @foreach ($pageData['packages'] as $package)
                            <article class="lfps-plan @if(!empty($package['featured'])) lfps-plan--featured @endif">
                                @if (!empty($package['featured']))
                                    <span class="lfps-plan__badge">{{ __('Önerilen') }}</span>
                                @endif
                                <h3>{{ $package['name'] }}</h3>
                                <strong>{{ $package['price'] }}</strong>
                                <p>{{ $package['unit'] }}</p>
                                <small>{{ $package['note'] }}</small>
                            </article>
                        @endforeach
                    </div>
                    <aside class="lfps-pricing__notes">
                        <h3>{{ __('Planlama ve ödeme notları') }}</h3>
                        <ul>
                            @foreach ($pageData['pricing_notes'] as $note)
                                <li>{{ $note }}</li>
                            @endforeach
                        </ul>
                    </aside>
                </div>
            </section>

            <section class="lfps-section" data-lfps-reveal>
                <div class="lfps-section__head">
                    <span class="lfps-eyebrow">{{ __('SSS') }}</span>
                    <h2>{{ __('Merak edebileceğiniz noktalar') }}</h2>
                </div>
                <div class="lfps-faq">
                    @foreach ($pageData['faq'] as $faq)
                        <details class="lfps-faq__item">
                            <summary>{{ $faq['question'] }}</summary>
                            <p>{{ $faq['answer'] }}</p>
                        </details>
                    @endforeach
                </div>
            </section>

            <section class="lfps-final" data-lfps-reveal>
                <div>
                    <span class="lfps-eyebrow">{{ __('Hazır başlangıç') }}</span>
                    <h2>{{ $pageData['cta_title'] }}</h2>
                    <p>{{ $pageData['cta_text'] }}</p>
                </div>
                <div class="lfps-final__actions">
                    <a href="{{ $applyUrl }}" class="lfps-button">{{ __('Programa başvur') }}</a>
                    <a href="{{ $testUrl }}" class="lfps-button lfps-button--ghost">{{ __('Seviye tespiti yap') }}</a>
                </div>
            </section>
        </div>

        <footer class="lfps-footer">
            <div class="lfps-shell lfps-footer__inner">
                <p>{{ $siteName }} · {{ __('LinguFranca Performans Sistemi landing page') }}</p>
                <div>
                    <a href="{{ $homeUrl }}">{{ __('Ana sayfa') }}</a>
                    <a href="{{ $applyUrl }}">{{ __('İletişim') }}</a>
                    <a href="{{ route('mobile-app-privacy-policy') }}">{{ __('Gizlilik') }}</a>
                </div>
            </div>
        </footer>

        <div class="lfps-video-modal" id="lfpsVideoModal" aria-hidden="true">
            <div class="lfps-video-modal__backdrop" data-video-close></div>
            <div class="lfps-video-modal__dialog" role="dialog" aria-modal="true" aria-labelledby="lfpsVideoTitle">
                <button type="button" class="lfps-video-modal__close" data-video-close aria-label="{{ __('Kapat') }}">×</button>
                <div class="lfps-video-modal__head">
                    <span class="lfps-eyebrow">{{ __('Video arşivi') }}</span>
                    <h3 id="lfpsVideoTitle">{{ __('Video') }}</h3>
                </div>
                <video id="lfpsVideoPlayer" controls playsinline preload="metadata"></video>
            </div>
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
    <style>
        .lfps-page {
            --lfps-bg: #171412;
            --lfps-surface: #211d1a;
            --lfps-surface-soft: #26211d;
            --lfps-border: rgba(233, 210, 176, 0.14);
            --lfps-accent: #e3c29a;
            --lfps-accent-strong: #f1d5b3;
            --lfps-text: #f6f0e8;
            --lfps-text-soft: rgba(246, 240, 232, 0.72);
            --lfps-blue: #0c57b7;
            background:
                radial-gradient(circle at top, rgba(42, 76, 130, 0.28), transparent 30%),
                linear-gradient(180deg, #14110f 0%, #181512 100%);
            color: var(--lfps-text);
            overflow: clip;
        }

        .lfps-page * { box-sizing: border-box; }
        .lfps-shell { width: min(1180px, calc(100vw - 32px)); margin: 0 auto; }
        .lfps-topbar { position: sticky; top: 0; z-index: 40; backdrop-filter: blur(18px); background: rgba(18, 15, 13, 0.72); border-bottom: 1px solid rgba(255, 255, 255, 0.04); }
        .lfps-topbar__inner, .lfps-footer__inner { display: flex; align-items: center; justify-content: space-between; gap: 18px; padding: 16px 0; }
        .lfps-brand, .lfps-footer a { display: inline-flex; align-items: center; gap: 12px; color: var(--lfps-text); text-decoration: none; font-weight: 900; letter-spacing: 0.04em; text-transform: uppercase; font-size: 12px; }
        .lfps-brand img { height: 34px; width: auto; filter: brightness(0) invert(1); }
        .lfps-topbar__actions, .lfps-hero__actions, .lfps-final__actions, .lfps-program__actions, .lfps-footer__inner div { display: flex; align-items: center; flex-wrap: wrap; gap: 12px; }
        .lfps-chip, .lfps-button { display: inline-flex; align-items: center; justify-content: center; min-height: 48px; padding: 0 20px; border-radius: 999px; text-decoration: none; font-weight: 800; border: 1px solid transparent; transition: transform 0.24s ease, background 0.24s ease, border-color 0.24s ease; }
        .lfps-chip { min-height: 40px; padding: 0 16px; color: var(--lfps-text-soft); border-color: rgba(255, 255, 255, 0.08); background: rgba(255, 255, 255, 0.03); }
        .lfps-button { background: var(--lfps-accent); color: #171412; box-shadow: 0 12px 30px rgba(227, 194, 154, 0.12); }
        .lfps-button--ghost { background: transparent; color: var(--lfps-text); border-color: rgba(255, 255, 255, 0.12); box-shadow: none; }
        .lfps-chip:hover, .lfps-button:hover { transform: translateY(-2px); }
        .lfps-hero { display: grid; grid-template-columns: minmax(0, 1.05fr) minmax(340px, 0.95fr); gap: 36px; padding: 72px 0 48px; min-height: calc(100svh - 88px); align-items: center; }
        .lfps-eyebrow { display: inline-flex; align-items: center; gap: 10px; margin-bottom: 16px; color: var(--lfps-accent); text-transform: uppercase; letter-spacing: 0.22em; font-size: 11px; font-weight: 900; }
        .lfps-eyebrow::before { content: ''; width: 10px; height: 10px; border-radius: 999px; background: var(--lfps-accent); box-shadow: 0 0 26px rgba(227, 194, 154, 0.45); }
        .lfps-hero h1, .lfps-section__head h2, .lfps-final h2 { margin: 0; font-size: clamp(40px, 6vw, 78px); line-height: 0.98; letter-spacing: -0.04em; font-weight: 1000; max-width: 10.5ch; }
        .lfps-section__head h2, .lfps-final h2 { font-size: clamp(32px, 4vw, 56px); max-width: 13ch; }
        .lfps-hero__lead, .lfps-section__head p, .lfps-final p { margin: 18px 0 0; max-width: 680px; color: var(--lfps-text-soft); font-size: 17px; line-height: 1.8; font-weight: 600; }
        .lfps-hero__badges, .lfps-marquee, .lfps-goalwords { display: flex; flex-wrap: wrap; gap: 12px; }
        .lfps-hero__badges { margin-top: 24px; }
        .lfps-hero__badges span, .lfps-marquee span, .lfps-goalwords span { display: inline-flex; align-items: center; min-height: 44px; padding: 0 16px; border-radius: 999px; border: 1px solid var(--lfps-border); background: rgba(255, 255, 255, 0.03); color: var(--lfps-text); font-weight: 700; }
        .lfps-hero__stats { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 14px; margin-top: 28px; }
        .lfps-hero__stats article, .lfps-value, .lfps-program, .lfps-fit__panel, .lfps-step, .lfps-media-card, .lfps-plan, .lfps-pricing__notes, .lfps-faq__item { border: 1px solid var(--lfps-border); background: linear-gradient(180deg, rgba(255, 255, 255, 0.04), rgba(255, 255, 255, 0.02)); border-radius: 26px; }
        .lfps-hero__stats article { padding: 18px; }
        .lfps-hero__stats strong { display: block; font-size: 28px; font-weight: 1000; color: var(--lfps-accent-strong); }
        .lfps-hero__stats span { display: block; margin-top: 6px; color: var(--lfps-text-soft); font-size: 13px; font-weight: 700; }
        .lfps-hero__visuals { position: relative; min-height: 640px; }
        .lfps-hero__frame { position: absolute; overflow: hidden; border-radius: 32px; border: 1px solid rgba(255, 255, 255, 0.1); box-shadow: 0 24px 70px rgba(0, 0, 0, 0.34); background: #fff; }
        .lfps-hero__frame img { display: block; width: 100%; height: 100%; object-fit: cover; }
        .lfps-hero__frame--primary { inset: 0 56px 90px 0; transform: rotate(-4deg); }
        .lfps-hero__frame--secondary { width: 58%; right: 0; bottom: 0; aspect-ratio: 1 / 1.2; transform: rotate(7deg); }
        .lfps-marquee { padding: 14px 0 10px; border-top: 1px solid rgba(255, 255, 255, 0.08); border-bottom: 1px solid rgba(255, 255, 255, 0.08); margin-bottom: 36px; }
        .lfps-section, .lfps-final { padding: 42px 0 0; }
        .lfps-section__head { margin-bottom: 28px; }
        .lfps-values, .lfps-includes, .lfps-steps, .lfps-media-grid, .lfps-programs, .lfps-pricing { display: grid; gap: 18px; }
        .lfps-values { grid-template-columns: repeat(3, minmax(0, 1fr)); }
        .lfps-value { padding: 26px; }
        .lfps-value__index { display: inline-block; margin-bottom: 32px; color: rgba(255, 255, 255, 0.28); font-size: 48px; font-weight: 1000; line-height: 1; }
        .lfps-value h3, .lfps-program h3, .lfps-fit__panel h3, .lfps-step h3, .lfps-media-card h3, .lfps-plan h3, .lfps-pricing__notes h3 { margin: 0; font-size: 24px; font-weight: 900; line-height: 1.15; }
        .lfps-value p, .lfps-program p, .lfps-fit__panel li, .lfps-step p, .lfps-media-card p, .lfps-plan p, .lfps-plan small, .lfps-pricing__notes li, .lfps-faq__item p { color: var(--lfps-text-soft); line-height: 1.75; font-size: 15px; font-weight: 600; }
        .lfps-programs { grid-template-columns: repeat(3, minmax(0, 1fr)); }
        .lfps-program { overflow: hidden; }
        .lfps-program__cover { position: relative; min-height: 290px; padding: 22px; background: linear-gradient(135deg, rgba(12, 87, 183, 0.85), rgba(23, 20, 18, 0.28)), linear-gradient(180deg, rgba(255, 255, 255, 0.08), transparent 65%), #f1ede7; background-position: center; background-size: cover; }
        .lfps-program__cover::after { content: ''; position: absolute; inset: 0; background: linear-gradient(180deg, rgba(15, 11, 9, 0.1) 0%, rgba(15, 11, 9, 0.78) 100%); }
        .lfps-program__cover span { position: relative; z-index: 1; display: inline-flex; min-height: 38px; padding: 0 14px; align-items: center; border-radius: 999px; background: rgba(23, 20, 18, 0.72); color: var(--lfps-text); font-size: 12px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.12em; }
        .lfps-program__body, .lfps-program__actions, .lfps-fit__panel, .lfps-step, .lfps-media-card, .lfps-plan, .lfps-pricing__notes, .lfps-faq__item { padding: 24px; }
        .lfps-program ul, .lfps-fit__panel ul, .lfps-pricing__notes ul { margin: 16px 0 0; padding: 0; list-style: none; }
        .lfps-program li, .lfps-fit__panel li, .lfps-pricing__notes li { position: relative; padding-left: 18px; }
        .lfps-program li + li, .lfps-fit__panel li + li, .lfps-pricing__notes li + li { margin-top: 12px; }
        .lfps-program li::before, .lfps-fit__panel li::before, .lfps-pricing__notes li::before { content: ''; position: absolute; left: 0; top: 11px; width: 7px; height: 7px; border-radius: 999px; background: var(--lfps-accent); }
        .lfps-fit { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 18px; }
        .lfps-fit__panel--muted { background: linear-gradient(180deg, rgba(39, 20, 20, 0.56), rgba(255, 255, 255, 0.02)); }
        .lfps-includes { grid-template-columns: repeat(4, minmax(0, 1fr)); }
        .lfps-includes article { min-height: 140px; padding: 22px; border-radius: 24px; border: 1px solid var(--lfps-border); background: rgba(255, 255, 255, 0.03); color: var(--lfps-text); font-weight: 800; line-height: 1.55; }
        .lfps-steps { grid-template-columns: repeat(4, minmax(0, 1fr)); }
        .lfps-step strong { display: inline-block; margin-bottom: 22px; color: rgba(255, 255, 255, 0.26); font-size: 48px; font-weight: 1000; line-height: 1; }
        .lfps-goalwords { gap: 14px; }
        .lfps-goalwords span { min-height: 68px; padding: 0 24px; font-size: clamp(16px, 2.5vw, 30px); letter-spacing: -0.03em; font-weight: 900; }
        .lfps-media-grid { grid-template-columns: repeat(3, minmax(0, 1fr)); }
        .lfps-media-card { display: flex; flex-direction: column; gap: 16px; min-height: 280px; background: radial-gradient(circle at top right, rgba(227, 194, 154, 0.18), transparent 42%), linear-gradient(180deg, rgba(255, 255, 255, 0.04), rgba(255, 255, 255, 0.02)); }
        .lfps-media-card__meta { display: flex; justify-content: space-between; gap: 14px; color: var(--lfps-accent); font-size: 12px; font-weight: 900; letter-spacing: 0.12em; text-transform: uppercase; }
        .lfps-media-card .lfps-button { margin-top: auto; align-self: flex-start; }
        .lfps-pricing { grid-template-columns: minmax(0, 1.2fr) minmax(320px, 0.8fr); align-items: start; }
        .lfps-pricing__plans { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 18px; }
        .lfps-plan { position: relative; min-height: 300px; }
        .lfps-plan--featured { background: linear-gradient(180deg, rgba(12, 87, 183, 0.18), rgba(255, 255, 255, 0.03)), linear-gradient(180deg, rgba(255, 255, 255, 0.04), rgba(255, 255, 255, 0.02)); }
        .lfps-plan__badge { position: absolute; top: 18px; right: 18px; display: inline-flex; min-height: 36px; padding: 0 14px; align-items: center; border-radius: 999px; background: var(--lfps-accent); color: #171412; font-size: 11px; font-weight: 900; letter-spacing: 0.14em; text-transform: uppercase; }
        .lfps-plan strong { display: block; margin: 18px 0 10px; font-size: clamp(28px, 3vw, 42px); line-height: 1.04; font-weight: 1000; color: var(--lfps-accent-strong); }
        .lfps-pricing__notes { min-height: 100%; }
        .lfps-faq { display: grid; gap: 14px; }
        .lfps-faq__item summary { list-style: none; cursor: pointer; font-size: 20px; font-weight: 900; line-height: 1.35; }
        .lfps-faq__item summary::-webkit-details-marker { display: none; }
        .lfps-faq__item p { margin: 14px 0 0; }
        .lfps-final { display: flex; align-items: end; justify-content: space-between; gap: 24px; padding-bottom: 58px; }
        .lfps-footer { border-top: 1px solid rgba(255, 255, 255, 0.08); color: rgba(246, 240, 232, 0.52); }
        .lfps-footer p { margin: 0; font-size: 12px; font-weight: 800; letter-spacing: 0.08em; text-transform: uppercase; }
        .lfps-video-modal { position: fixed; inset: 0; display: grid; place-items: center; padding: 20px; visibility: hidden; opacity: 0; pointer-events: none; transition: opacity 0.24s ease; z-index: 80; }
        .lfps-video-modal.is-open { visibility: visible; opacity: 1; pointer-events: auto; }
        .lfps-video-modal__backdrop { position: absolute; inset: 0; background: rgba(9, 7, 6, 0.74); backdrop-filter: blur(12px); }
        .lfps-video-modal__dialog { position: relative; z-index: 1; width: min(980px, calc(100vw - 24px)); padding: 22px; border-radius: 28px; border: 1px solid var(--lfps-border); background: #151210; box-shadow: 0 30px 100px rgba(0, 0, 0, 0.48); }
        .lfps-video-modal__close { position: absolute; top: 18px; right: 18px; width: 42px; height: 42px; border: 0; border-radius: 999px; background: rgba(255, 255, 255, 0.08); color: var(--lfps-text); font-size: 26px; line-height: 1; }
        .lfps-video-modal__head h3 { margin: 0 0 18px; font-size: 30px; font-weight: 1000; }
        .lfps-video-modal video { width: 100%; border-radius: 18px; background: #000; }
        [data-lfps-reveal] { opacity: 0; transform: translateY(22px); transition: opacity 0.5s ease, transform 0.5s ease; }
        [data-lfps-reveal].is-visible { opacity: 1; transform: translateY(0); }

        @media (max-width: 1199.98px) {
            .lfps-hero, .lfps-pricing { grid-template-columns: 1fr; }
            .lfps-hero { min-height: auto; padding-top: 56px; }
            .lfps-hero__visuals { min-height: 520px; }
            .lfps-values, .lfps-includes, .lfps-steps, .lfps-media-grid, .lfps-programs { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        }

        @media (max-width: 767.98px) {
            .lfps-topbar__inner, .lfps-final, .lfps-footer__inner { flex-direction: column; align-items: flex-start; }
            .lfps-shell { width: min(100vw - 20px, 1180px); }
            .lfps-hero { gap: 22px; padding-top: 34px; }
            .lfps-hero h1, .lfps-section__head h2, .lfps-final h2 { max-width: none; }
            .lfps-hero__stats, .lfps-values, .lfps-programs, .lfps-fit, .lfps-includes, .lfps-steps, .lfps-media-grid, .lfps-pricing__plans { grid-template-columns: 1fr; }
            .lfps-hero__visuals { min-height: 380px; }
            .lfps-hero__frame--primary { inset: 0 30px 70px 0; }
            .lfps-hero__frame--secondary { width: 62%; }
            .lfps-video-modal__dialog { padding: 18px; }
        }
    </style>
@endpush

@push('scripts')
    <script>
        (() => {
            const revealItems = document.querySelectorAll('[data-lfps-reveal]');
            const observer = new IntersectionObserver((entries) => {
                entries.forEach((entry) => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('is-visible');
                        observer.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.18 });
            revealItems.forEach((item) => observer.observe(item));

            const modal = document.getElementById('lfpsVideoModal');
            const modalTitle = document.getElementById('lfpsVideoTitle');
            const player = document.getElementById('lfpsVideoPlayer');
            if (!modal || !modalTitle || !player) return;

            const closeModal = () => {
                modal.classList.remove('is-open');
                modal.setAttribute('aria-hidden', 'true');
                player.pause();
                player.removeAttribute('src');
                player.load();
                document.body.style.overflow = '';
            };

            document.querySelectorAll('.lfps-video-trigger').forEach((button) => {
                button.addEventListener('click', () => {
                    const url = button.getAttribute('data-video-url');
                    const title = button.getAttribute('data-video-title') || 'Video';
                    if (!url) return;
                    modalTitle.textContent = title;
                    player.src = url;
                    modal.classList.add('is-open');
                    modal.setAttribute('aria-hidden', 'false');
                    document.body.style.overflow = 'hidden';
                    player.play().catch(() => {});
                });
            });

            modal.querySelectorAll('[data-video-close]').forEach((element) => {
                element.addEventListener('click', closeModal);
            });

            document.addEventListener('keydown', (event) => {
                if (event.key === 'Escape' && modal.classList.contains('is-open')) {
                    closeModal();
                }
            });
        })();
    </script>
@endpush
