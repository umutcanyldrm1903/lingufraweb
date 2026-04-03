@extends('frontend.layouts.master')

@php
    $siteName = $setting->app_name ?? config('app.name');
    $canonicalUrl = route('lingufranca-performance');
    $applyUrl = route('contact.index');
    $testUrl = route('placement-test.show');
    $homeUrl = route('home');
    $topLinks = $pageData['top_links'] ?? [];
    $heroVisuals = array_values(array_filter([
        $pageData['hero_primary_visual'] ?? null,
        $pageData['hero_secondary_visual'] ?? null,
        $pageData['hero_tertiary_visual'] ?? null,
    ]));
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
                @if (!empty($topLinks))
                    <nav class="lfps-nav" aria-label="Landing navigation">
                        @foreach ($topLinks as $link)
                            <a href="{{ $link['href'] }}">{{ $link['label'] }}</a>
                        @endforeach
                    </nav>
                @endif
                <div class="lfps-topbar__actions">
                    <a href="{{ $testUrl }}" class="lfps-chip">Seviye Tespiti</a>
                    <a href="{{ $applyUrl }}" class="lfps-button lfps-button--ghost">Programa Başvur</a>
                </div>
            </div>
        </header>

        <section class="lfps-hero">
            <div class="lfps-shell lfps-hero__inner">
                <div class="lfps-hero__copy" data-lfps-reveal>
                    <span class="lfps-eyebrow">{{ $pageData['eyebrow'] }}</span>
                    <h1>{{ $pageData['title'] }}</h1>
                    <p class="lfps-hero__lead">{{ $pageData['lead'] }}</p>
                    <div class="lfps-hero__badges">
                        @foreach ($pageData['hero_badges'] as $badge)
                            <span>{{ $badge }}</span>
                        @endforeach
                    </div>
                    <div class="lfps-hero__actions">
                        <a href="{{ $applyUrl }}" class="lfps-button">Programa Başvur</a>
                        <a href="{{ $testUrl }}" class="lfps-button lfps-button--ghost">Ön Değerlendirme Başlat</a>
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
                <div class="lfps-hero__visual" data-lfps-reveal>
                    <div class="lfps-stack">
                        @foreach ($heroVisuals as $visual)
                            <figure class="lfps-stack__card lfps-stack__card--{{ $loop->iteration }}">
                                <img src="{{ $visual }}" alt="LinguFranca program kapağı {{ $loop->iteration }}">
                            </figure>
                        @endforeach
                    </div>
                    <article class="lfps-hero__quote">
                        <span>{{ $pageData['hero_quote_title'] }}</span>
                        <p>{{ $pageData['hero_quote'] }}</p>
                    </article>
                </div>
            </div>
        </section>

        <div class="lfps-shell">
            <section class="lfps-marquee" data-lfps-reveal>
                @foreach ($pageData['press_badges'] as $badge)
                    <span>{{ $badge }}</span>
                @endforeach
            </section>

            <section class="lfps-section" id="programlar" data-lfps-reveal>
                <div class="lfps-section__head">
                    <span class="lfps-eyebrow">Program Akışları</span>
                    <h2>Zip içindeki PDF’leri doğrudan üç programa ayırdık</h2>
                    <p>Her kart gerçek PDF dosyasını açar. Metinlerin omurgası doğrudan sunumlardaki amaç, süreç ve vaatlerden kuruldu.</p>
                </div>
                <div class="lfps-programs">
                    @foreach ($downloads as $download)
                        <article class="lfps-program">
                            <div class="lfps-program__cover" @if (!empty($download['cover_url'])) style="background-image:url('{{ $download['cover_url'] }}')" @endif>
                                <div class="lfps-program__cover-meta">
                                    <span>{{ $download['label'] }}</span>
                                    <strong>{{ $download['meta'] }}</strong>
                                </div>
                            </div>
                            <div class="lfps-program__body">
                                <p class="lfps-program__result">{{ $download['result'] }}</p>
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
                                    <a href="{{ $download['file_url'] }}" target="_blank" rel="noopener" class="lfps-button">PDF’i Aç</a>
                                @endif
                                <a href="{{ $applyUrl }}" class="lfps-inline-link">Bilgi Al</a>
                            </div>
                        </article>
                    @endforeach
                </div>
            </section>

            <section class="lfps-editorial" id="sistem">
                <div class="lfps-editorial__sticky" data-lfps-reveal>
                    <span class="lfps-eyebrow">{{ $pageData['manifesto_eyebrow'] }}</span>
                    <h2>{{ $pageData['manifesto_title'] }}</h2>
                    <p>{{ $pageData['manifesto_lead'] }}</p>
                </div>
                <div class="lfps-editorial__flow">
                    @foreach ($pageData['manifesto_points'] as $point)
                        <article class="lfps-editorial__card" data-lfps-reveal>
                            <span class="lfps-editorial__index">{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                            <h3>{{ $point['title'] }}</h3>
                            <p>{{ $point['description'] }}</p>
                        </article>
                    @endforeach
                    <article class="lfps-editorial__card lfps-editorial__card--wide" data-lfps-reveal>
                        <span class="lfps-eyebrow">Sunumlardan çıkan ortak yapı</span>
                        <div class="lfps-briefs">
                            <div>
                                <h3>Kademe kademe ilerleyen sistem</h3>
                                <p>PDF’lerdeki akış; önce temel yapı, sonra kontrollü geçiş, ardından ölçüm ve optimizasyon. Amaç hız değil, sağlam ve kalıcı gelişim.</p>
                            </div>
                            <div>
                                <h3>Aktif katılım şartı</h3>
                                <p>“Bu program kısa yol arayanlar için değil” mesajı tüm sunumlarda tekrar ediyor. Disiplin, tekrar, aktif katılım ve yol haritası sistemi taşıyan ana kavramlar.</p>
                            </div>
                        </div>
                    </article>
                </div>
            </section>

            <section class="lfps-section" data-lfps-reveal>
                <div class="lfps-section__head">
                    <span class="lfps-eyebrow">{{ $pageData['resource_eyebrow'] }}</span>
                    <h2>{{ $pageData['resource_title'] }}</h2>
                </div>
                <div class="lfps-resource-columns">
                    @foreach ($pageData['resource_columns'] as $column)
                        <article class="lfps-resource">
                            <h3>{{ $column['label'] }}</h3>
                            <ul>
                                @foreach ($column['items'] as $item)
                                    <li>{{ $item }}</li>
                                @endforeach
                            </ul>
                        </article>
                    @endforeach
                </div>
            </section>

            <section class="lfps-section" id="videolar" data-lfps-reveal>
                <div class="lfps-section__head">
                    <span class="lfps-eyebrow">{{ $pageData['proof_eyebrow'] }}</span>
                    <h2>{{ $pageData['proof_title'] }}</h2>
                    <p>{{ $pageData['proof_lead'] }}</p>
                </div>
                <div class="lfps-media-grid">
                    @foreach ($mediaLibrary as $item)
                        <article class="lfps-media-card">
                            <div class="lfps-media-card__poster" @if (!empty($item['poster_url'])) style="background-image:url('{{ $item['poster_url'] }}')" @endif>
                                <div class="lfps-media-card__poster-meta">
                                    <span>{{ $item['category'] }}</span>
                                    <strong>{{ $item['duration'] }}</strong>
                                </div>
                                <button type="button"
                                    class="lfps-media-card__play lfps-video-trigger"
                                    data-video-url="{{ $item['file_url'] }}"
                                    data-video-title="{{ $item['title'] }}"
                                    data-video-poster="{{ $item['poster_url'] ?? '' }}">
                                    Videoyu Aç
                                </button>
                            </div>
                            <div class="lfps-media-card__body">
                                <h3>{{ $item['title'] }}</h3>
                                <p>{{ $item['description'] }}</p>
                            </div>
                        </article>
                    @endforeach
                </div>
            </section>

            <section class="lfps-fit" id="kimler">
                <article class="lfps-fit__panel" data-lfps-reveal>
                    <span class="lfps-eyebrow">{{ $pageData['fit_eyebrow'] }}</span>
                    <h2>{{ $pageData['fit_title'] }}</h2>
                    <p>{{ $pageData['fit_lead'] }}</p>
                    <h3>Kimler İçin Uygun?</h3>
                    <ul>
                        @foreach ($pageData['fit_for'] as $item)
                            <li>{{ $item }}</li>
                        @endforeach
                    </ul>
                </article>
                <article class="lfps-fit__panel lfps-fit__panel--muted" data-lfps-reveal>
                    <span class="lfps-eyebrow">Sınır Çizgisi</span>
                    <h2>Kimler İçin Uygun Değil?</h2>
                    <p>Programın dili net: zahmetsiz sonuç beklentisi olan, sisteme katılım göstermeyecek ya da sabit müfredat isteyen profiller için kurgulanmadı.</p>
                    <ul>
                        @foreach ($pageData['fit_not_for'] as $item)
                            <li>{{ $item }}</li>
                        @endforeach
                    </ul>
                </article>
            </section>

            <section class="lfps-section" id="surec">
                <div class="lfps-section__head" data-lfps-reveal>
                    <span class="lfps-eyebrow">{{ $pageData['process_eyebrow'] }}</span>
                    <h2>{{ $pageData['process_title'] }}</h2>
                </div>
                <div class="lfps-process">
                    <div class="lfps-steps">
                        @foreach ($pageData['steps'] as $step)
                            <article class="lfps-step" data-lfps-reveal>
                                <strong>{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</strong>
                                <h3>{{ $step['title'] }}</h3>
                                <p>{{ $step['description'] }}</p>
                            </article>
                        @endforeach
                    </div>
                    <aside class="lfps-process__aside">
                        <article class="lfps-sidecard" data-lfps-reveal>
                            <span class="lfps-eyebrow">{{ $pageData['milestones_eyebrow'] }}</span>
                            @foreach ($pageData['milestones'] as $milestone)
                                <h3>{{ $milestone['label'] }}</h3>
                                <div class="lfps-goalwords">
                                    @foreach ($milestone['items'] as $word)
                                        <span>{{ $word }}</span>
                                    @endforeach
                                </div>
                            @endforeach
                        </article>
                        <article class="lfps-sidecard" data-lfps-reveal>
                            <span class="lfps-eyebrow">{{ $pageData['reasons_eyebrow'] }}</span>
                            <h3>{{ $pageData['reasons_title'] }}</h3>
                            <ul class="lfps-reasons">
                                @foreach ($pageData['reasons'] as $reason)
                                    <li>{{ $reason }}</li>
                                @endforeach
                            </ul>
                        </article>
                    </aside>
                </div>
            </section>

            <section class="lfps-section" id="fiyat" data-lfps-reveal>
                <div class="lfps-section__head">
                    <span class="lfps-eyebrow">{{ $pageData['pricing_eyebrow'] }}</span>
                    <h2>{{ $pageData['pricing_title'] }}</h2>
                    <p>{{ $pageData['pricing_lead'] }}</p>
                </div>
                <div class="lfps-pricing">
                    <div class="lfps-pricing__plans">
                        @foreach ($pageData['packages'] as $package)
                            <article class="lfps-plan @if (!empty($package['featured'])) lfps-plan--featured @endif">
                                @if (!empty($package['featured']))
                                    <span class="lfps-plan__badge">Önerilen</span>
                                @endif
                                <h3>{{ $package['name'] }}</h3>
                                <strong>{{ $package['price'] }}</strong>
                                <p>{{ $package['unit'] }}</p>
                                <small>{{ $package['note'] }}</small>
                            </article>
                        @endforeach
                    </div>
                    <aside class="lfps-pricing__notes">
                        <h3>Planlama ve Ödeme Notları</h3>
                        <ul>
                            @foreach ($pageData['pricing_notes'] as $note)
                                <li>{{ $note }}</li>
                            @endforeach
                        </ul>
                    </aside>
                </div>
            </section>

            <section class="lfps-section" id="sss" data-lfps-reveal>
                <div class="lfps-section__head">
                    <span class="lfps-eyebrow">SSS</span>
                    <h2>Merak edebileceğiniz noktalar</h2>
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
                    <span class="lfps-eyebrow">Hazır Başlangıç</span>
                    <h2>{{ $pageData['cta_title'] }}</h2>
                    <p>{{ $pageData['cta_text'] }}</p>
                </div>
                <div class="lfps-final__actions">
                    <a href="{{ $applyUrl }}" class="lfps-button">Programa Başvur</a>
                    <a href="{{ $testUrl }}" class="lfps-button lfps-button--ghost">Seviye Tespiti Yap</a>
                </div>
            </section>
        </div>

        <footer class="lfps-footer">
            <div class="lfps-shell lfps-footer__inner">
                <p>{{ $siteName }} · LinguFranca Performans Sistemi</p>
                <div>
                    <a href="{{ $homeUrl }}">Ana Sayfa</a>
                    <a href="{{ $applyUrl }}">İletişim</a>
                    <a href="{{ route('mobile-app-privacy-policy') }}">Gizlilik</a>
                </div>
            </div>
        </footer>

        <div class="lfps-video-modal" id="lfpsVideoModal" aria-hidden="true">
            <div class="lfps-video-modal__backdrop" data-video-close></div>
            <div class="lfps-video-modal__dialog" role="dialog" aria-modal="true" aria-labelledby="lfpsVideoTitle">
                <button type="button" class="lfps-video-modal__close" data-video-close aria-label="Kapat">×</button>
                <div class="lfps-video-modal__head">
                    <span class="lfps-eyebrow">Video Arşivi</span>
                    <h3 id="lfpsVideoTitle">Video</h3>
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
    <link rel="stylesheet" href="{{ asset('frontend/css/lingufranca-performance.css') }}">
@endpush

@push('scripts')
    <script src="{{ asset('frontend/js/lingufranca-performance.js') }}"></script>
@endpush
