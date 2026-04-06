@extends('frontend.layouts.master')

@php
    $siteName = $setting->app_name ?? config('app.name');
    $canonicalUrl = route('lingufranca-performance');
    $applyUrl = route('contact.index');
    $testUrl = route('placement-test.show');
    $homeUrl = route('home');
    $coverVisual = $pageData['hero_primary_visual'] ?? null;

    $topLinks = [
        ['label' => 'Overview', 'href' => '#overview'],
        ['label' => 'Sections', 'href' => '#sections'],
        ['label' => 'Structure', 'href' => '#structure'],
        ['label' => 'Results', 'href' => '#results'],
        ['label' => 'CTA', 'href' => '#cta'],
    ];

    $featureCards = [
        [
            'eyebrow' => 'Section 01',
            'title' => 'Hero yeniden kurulacak',
            'copy' => 'Ana mesaj, alt aciklama ve tek odakli gorsel bu alana yerlestirilecek.',
        ],
        [
            'eyebrow' => 'Section 02',
            'title' => 'Program bloklari yeniden kurulacak',
            'copy' => 'Eski kartlar yerine daha net, daha premium landing bloklari eklenecek.',
        ],
        [
            'eyebrow' => 'Section 03',
            'title' => 'Video ve proof katmani eklenecek',
            'copy' => 'Featured medya ve alt proof kartlari bu template akisina gore dizilecek.',
        ],
    ];

    $workflowCards = [
        ['title' => 'Hero / Intro', 'copy' => 'Open template benzeri merkezi acilis alani.'],
        ['title' => 'Features / Programlar', 'copy' => '3 kolonlu section ve ana destek bloklari.'],
        ['title' => 'Proof / CTA', 'copy' => 'Alt tarafta referans, sonuc ve donusum alani.'],
    ];

    $resultCards = [
        ['title' => 'Result Block 01', 'copy' => 'Burasi sonra testimonial, video ya da proof alanina donusecek.'],
        ['title' => 'Result Block 02', 'copy' => 'Burasi sonra testimonial, video ya da proof alanina donusecek.'],
        ['title' => 'Result Block 03', 'copy' => 'Burasi sonra testimonial, video ya da proof alanina donusecek.'],
    ];

    $metricCards = [
        ['value' => '01', 'label' => 'Yeni shell aktif'],
        ['value' => '02', 'label' => 'Icerik sifirlandi'],
        ['value' => '03', 'label' => 'Beraber doldurulacak'],
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
                    <span class="openp-kicker">Open-style shell</span>
                    <h1>Bu sayfa artik yeni template iskeletiyle aciliyor</h1>
                    <p>Eski performans sayfasi icerigi ve onceki dizilim geri plana cekildi. Simdi burada Open React template mantiginda yeniden kurulmus temiz bir landing shell aktif.</p>

                    <div class="openp-hero__actions">
                        <a class="openp-button" href="#sections">Sectionlari Incele</a>
                        <a class="openp-button openp-button--ghost" href="#cta">Birlikte Dolduralim</a>
                    </div>
                </div>

                <div class="openp-hero__visual openp-reveal">
                    <div class="openp-visual-card">
                        <div class="openp-visual-card__media">
                            @if (!empty($coverVisual))
                                <img src="{{ $coverVisual }}" alt="{{ $siteName }}" />
                            @else
                                <div class="openp-visual-card__fallback">{{ strtoupper(substr($siteName, 0, 2)) }}</div>
                            @endif
                        </div>
                        <div class="openp-visual-card__body">
                            <span class="openp-chip">Landing shell</span>
                            <strong>Hero preview alani</strong>
                            <p>Burasi sonra ana video, ana gorsel ya da featured panel olabilir.</p>
                        </div>
                    </div>
                </div>
            </section>

            <section class="openp-metrics openp-reveal">
                @foreach ($metricCards as $metric)
                    <article class="openp-metric">
                        <strong>{{ $metric['value'] }}</strong>
                        <span>{{ $metric['label'] }}</span>
                    </article>
                @endforeach
            </section>

            <section class="openp-section" id="sections">
                <div class="openp-section__head openp-reveal">
                    <span class="openp-kicker">Sections</span>
                    <h2>Open template dizilimi bu sayfaya tasindi</h2>
                    <p>Hero, features, workflows, proof ve CTA sirasiyla doldurulacak temiz iskelet hazirlandi.</p>
                </div>

                <div class="openp-feature-grid">
                    @foreach ($featureCards as $card)
                        <article class="openp-feature-card openp-reveal">
                            <span class="openp-chip">{{ $card['eyebrow'] }}</span>
                            <h3>{{ $card['title'] }}</h3>
                            <p>{{ $card['copy'] }}</p>
                        </article>
                    @endforeach
                </div>
            </section>

            <section class="openp-section openp-section--split" id="structure">
                <div class="openp-section__head openp-reveal">
                    <span class="openp-kicker">Structure</span>
                    <h2>Workflow kartlari burada sekillenecek</h2>
                    <p>Bu alan Open template’in spotlight/workflow mantigini aliyor. Sonraki turda her kutu gercek icerige donecek.</p>
                </div>

                <div class="openp-workflow-grid">
                    @foreach ($workflowCards as $card)
                        <article class="openp-workflow-card openp-reveal">
                            <div class="openp-workflow-card__media"></div>
                            <div class="openp-workflow-card__body">
                                <span class="openp-chip">Draft block</span>
                                <h3>{{ $card['title'] }}</h3>
                                <p>{{ $card['copy'] }}</p>
                            </div>
                        </article>
                    @endforeach
                </div>
            </section>

            <section class="openp-section" id="results">
                <div class="openp-section__head openp-reveal">
                    <span class="openp-kicker">Results</span>
                    <h2>Alt proof alanlari bos placeholder olarak birakildi</h2>
                    <p>Burasi sonra testimonial, video proof ya da sonuc kartlarina donecek.</p>
                </div>

                <div class="openp-result-grid">
                    @foreach ($resultCards as $card)
                        <article class="openp-result-card openp-reveal">
                            <div class="openp-result-card__mark"></div>
                            <h3>{{ $card['title'] }}</h3>
                            <p>{{ $card['copy'] }}</p>
                        </article>
                    @endforeach
                </div>
            </section>

            <section class="openp-cta openp-reveal" id="cta">
                <div class="openp-cta__inner">
                    <span class="openp-kicker">CTA</span>
                    <h2>Template giydirildi. Simdi icerigi birlikte doldurabiliriz.</h2>
                    <div class="openp-hero__actions">
                        <a class="openp-button" href="{{ $applyUrl }}">Hazirla</a>
                        <a class="openp-button openp-button--ghost" href="{{ $testUrl }}">Akisi Planla</a>
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
        .openp-metrics,
        .openp-feature-card,
        .openp-workflow-card,
        .openp-result-card,
        .openp-cta,
        .openp-visual-card {
            border: 1px solid rgba(255, 255, 255, 0.08);
            background:
                linear-gradient(180deg, rgba(17, 24, 39, 0.88) 0%, rgba(9, 14, 24, 0.92) 100%);
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
        .openp-hero__actions {
            display: flex;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
        }

        .openp-nav a {
            color: rgba(237, 242, 255, 0.78);
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
            transition: transform 180ms ease, box-shadow 180ms ease, background-size 180ms ease;
            box-shadow: 0 18px 40px rgba(79, 70, 229, 0.28);
        }

        .openp-button:hover,
        .openp-button:focus-visible {
            transform: translateY(-1px);
        }

        .openp-button--ghost {
            background: rgba(255, 255, 255, 0.04);
            border-color: rgba(255, 255, 255, 0.1);
            box-shadow: none;
            color: #eef2ff;
        }

        .openp-hero {
            display: grid;
            grid-template-columns: minmax(0, 1fr) minmax(320px, 0.92fr);
            gap: 28px;
            align-items: center;
            padding: 72px 0 48px;
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

        .openp-hero h1,
        .openp-section__head h2,
        .openp-feature-card h3,
        .openp-workflow-card h3,
        .openp-result-card h3,
        .openp-cta h2,
        .openp-visual-card__body strong {
            margin: 0;
            font-family: "Sora", sans-serif;
            color: #ffffff;
            text-wrap: balance;
        }

        .openp-hero h1 {
            margin-top: 16px;
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
        .openp-workflow-card p,
        .openp-result-card p,
        .openp-visual-card__body p {
            margin: 0;
            color: rgba(199, 210, 254, 0.68);
            line-height: 1.75;
        }

        .openp-hero__copy {
            display: grid;
            gap: 18px;
            max-width: 640px;
        }

        .openp-visual-card {
            overflow: hidden;
            border-radius: 26px;
        }

        .openp-visual-card__media {
            aspect-ratio: 16 / 10;
            background:
                radial-gradient(circle at 20% 20%, rgba(99, 102, 241, 0.2), transparent 30%),
                linear-gradient(145deg, rgba(22, 29, 49, 0.96), rgba(9, 14, 24, 1));
        }

        .openp-visual-card__media img {
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

        .openp-visual-card__body {
            padding: 22px;
            display: grid;
            gap: 10px;
        }

        .openp-metrics,
        .openp-feature-grid,
        .openp-workflow-grid,
        .openp-result-grid {
            display: grid;
            gap: 18px;
        }

        .openp-metrics {
            grid-template-columns: repeat(3, minmax(0, 1fr));
            padding: 18px;
            border-radius: 22px;
        }

        .openp-metric {
            min-height: 120px;
            border-radius: 18px;
            padding: 18px;
            background: rgba(255, 255, 255, 0.03);
            display: grid;
            align-content: end;
            gap: 8px;
        }

        .openp-metric strong {
            font-family: "Sora", sans-serif;
            color: #ffffff;
            font-size: 30px;
        }

        .openp-metric span {
            color: rgba(199, 210, 254, 0.68);
            font-size: 14px;
            font-weight: 600;
        }

        .openp-section {
            padding-top: 96px;
            display: grid;
            gap: 28px;
        }

        .openp-section__head {
            max-width: 760px;
            display: grid;
            gap: 12px;
        }

        .openp-feature-grid {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }

        .openp-feature-card,
        .openp-result-card {
            min-height: 230px;
            border-radius: 24px;
            padding: 24px;
            display: grid;
            align-content: start;
            gap: 12px;
        }

        .openp-workflow-grid {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }

        .openp-workflow-card {
            overflow: hidden;
            border-radius: 24px;
        }

        .openp-workflow-card__media {
            aspect-ratio: 7 / 5;
            background:
                radial-gradient(circle at 25% 30%, rgba(99, 102, 241, 0.35), transparent 24%),
                linear-gradient(145deg, rgba(30, 41, 59, 0.96), rgba(15, 23, 42, 1));
        }

        .openp-workflow-card__body {
            padding: 22px;
            display: grid;
            gap: 10px;
        }

        .openp-result-grid {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }

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

        .openp-cta__inner {
            max-width: 760px;
            display: grid;
            gap: 16px;
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
            .openp-feature-grid,
            .openp-workflow-grid,
            .openp-result-grid {
                grid-template-columns: 1fr;
            }

            .openp-metrics {
                grid-template-columns: repeat(3, minmax(0, 1fr));
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

            .openp-metrics {
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
