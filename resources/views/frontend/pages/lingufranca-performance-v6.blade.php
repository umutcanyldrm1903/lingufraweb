@extends('frontend.layouts.master')

@php
    $siteName = $setting->app_name ?? config('app.name');
    $canonicalUrl = route('lingufranca-performance');
    $applyUrl = route('contact.index');
    $testUrl = route('placement-test.show');
    $homeUrl = route('home');
    $coverVisual = $pageData['hero_primary_visual'] ?? null;

    $sidebarLinks = [
        ['label' => 'Overview', 'href' => '#overview'],
        ['label' => 'Programlar', 'href' => '#programlar'],
        ['label' => 'Medya', 'href' => '#medya'],
        ['label' => 'Fiyat', 'href' => '#fiyat'],
        ['label' => 'SSS', 'href' => '#sss'],
    ];

    $moduleRows = [
        ['index' => '01', 'title' => 'Hero Alani', 'state' => 'Hazir', 'meta' => 'Koel shell'],
        ['index' => '02', 'title' => 'Program Akisi', 'state' => 'Bos', 'meta' => 'Doldurulacak'],
        ['index' => '03', 'title' => 'Video Raflari', 'state' => 'Bos', 'meta' => 'Doldurulacak'],
        ['index' => '04', 'title' => 'Fiyat Katmani', 'state' => 'Bos', 'meta' => 'Doldurulacak'],
        ['index' => '05', 'title' => 'SSS Katmani', 'state' => 'Bos', 'meta' => 'Doldurulacak'],
    ];

    $surfaceBlocks = [
        ['eyebrow' => 'Overview', 'title' => 'Ana Yapi Hazir', 'copy' => 'Bu bolum daha sonra net mesaj ve ana akisa donusturulecek.'],
        ['eyebrow' => 'Programs', 'title' => 'Program Kartlari Sifirlandi', 'copy' => 'Eski bloklar kaldirildi, sadece yeni duzenin iskeleti birakildi.'],
        ['eyebrow' => 'Media', 'title' => 'Video Alani Beklemede', 'copy' => 'Koel benzeri duzende featured panel ve alt liste sonra doldurulacak.'],
    ];

    $queueItems = [
        'Hero metni ve ana teklif',
        'Program bolumu ic yapisi',
        'Video rafi ve featured medya',
        'Fiyat bloklari',
        'SSS icerigi',
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
    <section class="koel-frame">
        <div class="koel-app-shell">
            <aside class="koel-sidebar">
                <a class="koel-brand" href="{{ $homeUrl }}">
                    @if (!empty($setting?->logo))
                        <img src="{{ asset($setting->logo) }}" alt="{{ $siteName }}" />
                    @else
                        <span>{{ $siteName }}</span>
                    @endif
                </a>

                <div class="koel-sidebar__group">
                    <span class="koel-sidebar__label">Workspace</span>
                    <nav class="koel-sidebar__nav" aria-label="Bolumler">
                        @foreach ($sidebarLinks as $link)
                            <a href="{{ $link['href'] }}">{{ $link['label'] }}</a>
                        @endforeach
                    </nav>
                </div>

                <div class="koel-sidebar__group">
                    <span class="koel-sidebar__label">Durum</span>
                    <div class="koel-status-stack">
                        <div class="koel-status-pill">
                            <span class="koel-status-pill__dot"></span>
                            Koel shell aktif
                        </div>
                        <div class="koel-status-pill">Icerik sifirlandi</div>
                        <div class="koel-status-pill">Birlikte doldurulacak</div>
                    </div>
                </div>

                <div class="koel-sidebar__footer">
                    <a class="koel-button koel-button--ghost" href="{{ $testUrl }}">Seviye Tespiti</a>
                    <a class="koel-button" href="{{ $applyUrl }}">Programa Basvur</a>
                </div>
            </aside>

            <main class="koel-main">
                <header class="koel-topbar">
                    <div class="koel-searchbar">
                        <span class="koel-searchbar__icon"></span>
                        <span>Bu sayfa su an sadece yeni template iskeletiyle acik</span>
                    </div>

                    <div class="koel-topbar__actions">
                        <span class="koel-tag">Koel Inspired</span>
                        <span class="koel-tag">Draft Shell</span>
                    </div>
                </header>

                <section class="koel-hero" id="overview">
                    <div class="koel-hero__cover">
                        @if (!empty($coverVisual))
                            <img src="{{ $coverVisual }}" alt="{{ $siteName }}" />
                        @else
                            <div class="koel-cover-fallback">{{ strtoupper(substr($siteName, 0, 2)) }}</div>
                        @endif
                    </div>

                    <div class="koel-hero__copy">
                        <span class="koel-kicker">Koel-style shell</span>
                        <h1>{{ $siteName }} Performans Sayfasi</h1>
                        <p>Mevcut performans sayfasinin eski icerigi ve onceki tasarim dili temizlendi. Simdi bu sayfada sadece Koel esintili yeni arayuz iskeleti aktif.</p>

                        <div class="koel-hero__actions">
                            <a class="koel-button" href="#programlar">Iskeleti Incele</a>
                            <a class="koel-button koel-button--ghost" href="#medya">Sonraki Alan</a>
                        </div>
                    </div>
                </section>

                <section class="koel-surface-grid">
                    @foreach ($surfaceBlocks as $block)
                        <article class="koel-surface-card">
                            <span class="koel-surface-card__eyebrow">{{ $block['eyebrow'] }}</span>
                            <h2>{{ $block['title'] }}</h2>
                            <p>{{ $block['copy'] }}</p>
                        </article>
                    @endforeach
                </section>

                <section class="koel-section" id="programlar">
                    <div class="koel-section__head">
                        <span class="koel-kicker">Programlar</span>
                        <h2>Yapilandirilacak alanlar</h2>
                    </div>

                    <div class="koel-list">
                        @foreach ($moduleRows as $row)
                            <article class="koel-list__row">
                                <span class="koel-list__index">{{ $row['index'] }}</span>
                                <div class="koel-list__content">
                                    <strong>{{ $row['title'] }}</strong>
                                    <span>{{ $row['meta'] }}</span>
                                </div>
                                <span class="koel-list__state">{{ $row['state'] }}</span>
                            </article>
                        @endforeach
                    </div>
                </section>

                <section class="koel-section" id="medya">
                    <div class="koel-section__head">
                        <span class="koel-kicker">Medya</span>
                        <h2>Featured panel ve alt raf burada kurulacak</h2>
                    </div>

                    <div class="koel-media-shelf">
                        <article class="koel-media-shelf__featured">
                            <div class="koel-media-shelf__art"></div>
                            <div class="koel-media-shelf__copy">
                                <strong>Featured medya alani</strong>
                                <p>Buyuk video ya da ana gorsel burada duracak.</p>
                            </div>
                        </article>

                        <div class="koel-media-shelf__rail">
                            <div class="koel-mini-tile">Video 01</div>
                            <div class="koel-mini-tile">Video 02</div>
                            <div class="koel-mini-tile">Video 03</div>
                            <div class="koel-mini-tile">Video 04</div>
                        </div>
                    </div>
                </section>
            </main>

            <aside class="koel-queue" id="fiyat">
                <div class="koel-queue__panel">
                    <span class="koel-kicker">Siradaki Isler</span>
                    <h2>Birlikte doldurulacak bloklar</h2>
                    <ul class="koel-queue__list">
                        @foreach ($queueItems as $item)
                            <li>{{ $item }}</li>
                        @endforeach
                    </ul>
                </div>

                <div class="koel-queue__panel">
                    <span class="koel-kicker">Fiyat</span>
                    <h3>Bu alan bos birakildi</h3>
                    <p>Koel duzeni oturduktan sonra pricing bloklari buraya eklenecek.</p>
                </div>

                <div class="koel-queue__panel" id="sss">
                    <span class="koel-kicker">SSS</span>
                    <h3>Bu alan bos birakildi</h3>
                    <p>SSS katmani sonraki turda icerikle birlikte eklenecek.</p>
                </div>
            </aside>
        </div>
    </section>
@endsection

@push('styles')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Sora:wght@500;600;700;800&display=swap" rel="stylesheet">

    <style>
        .koel-frame {
            min-height: 100vh;
            padding: 20px;
            background:
                radial-gradient(circle at top left, rgba(88, 208, 141, 0.14), transparent 22%),
                radial-gradient(circle at top right, rgba(76, 89, 122, 0.24), transparent 20%),
                linear-gradient(180deg, #0d1117 0%, #090d13 100%);
            color: #f3f5f7;
            font-family: "Manrope", sans-serif;
        }

        .koel-app-shell {
            width: min(1480px, 100%);
            min-height: calc(100vh - 40px);
            margin: 0 auto;
            display: grid;
            grid-template-columns: 240px minmax(0, 1fr) 320px;
            gap: 20px;
        }

        .koel-sidebar,
        .koel-main,
        .koel-queue {
            min-width: 0;
        }

        .koel-sidebar,
        .koel-queue__panel,
        .koel-topbar,
        .koel-hero,
        .koel-surface-card,
        .koel-list,
        .koel-media-shelf {
            border: 1px solid rgba(255, 255, 255, 0.06);
            background: rgba(17, 22, 29, 0.92);
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.28);
        }

        .koel-sidebar {
            border-radius: 26px;
            padding: 22px 18px;
            display: flex;
            flex-direction: column;
            gap: 26px;
        }

        .koel-brand {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            min-height: 68px;
            border-radius: 18px;
            background: rgba(255, 255, 255, 0.03);
        }

        .koel-brand img {
            max-width: 126px;
            max-height: 44px;
            object-fit: contain;
        }

        .koel-sidebar__group {
            display: grid;
            gap: 12px;
        }

        .koel-sidebar__label,
        .koel-kicker,
        .koel-surface-card__eyebrow {
            font-size: 11px;
            letter-spacing: 0.16em;
            text-transform: uppercase;
            color: #7ebf95;
            font-weight: 800;
        }

        .koel-sidebar__nav {
            display: grid;
            gap: 6px;
        }

        .koel-sidebar__nav a,
        .koel-brand,
        .koel-button,
        .koel-list__row,
        .koel-mini-tile {
            transition: background-color 180ms ease, border-color 180ms ease, transform 180ms ease;
        }

        .koel-sidebar__nav a {
            display: flex;
            align-items: center;
            min-height: 42px;
            padding: 0 14px;
            border-radius: 14px;
            color: #e9eef2;
            font-size: 14px;
            font-weight: 700;
            background: transparent;
        }

        .koel-sidebar__nav a:hover,
        .koel-sidebar__nav a:focus-visible {
            background: rgba(126, 191, 149, 0.1);
            color: #ffffff;
        }

        .koel-status-stack,
        .koel-hero__actions,
        .koel-topbar__actions {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .koel-status-pill,
        .koel-tag {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            min-height: 34px;
            padding: 0 12px;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.04);
            color: #c6d1d9;
            font-size: 12px;
            font-weight: 700;
        }

        .koel-status-pill__dot {
            width: 8px;
            height: 8px;
            border-radius: 999px;
            background: #58d08d;
            box-shadow: 0 0 0 6px rgba(88, 208, 141, 0.12);
        }

        .koel-sidebar__footer {
            margin-top: auto;
            display: grid;
            gap: 10px;
        }

        .koel-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 46px;
            padding: 0 16px;
            border-radius: 14px;
            border: 1px solid transparent;
            background: #58d08d;
            color: #07120a;
            font-size: 14px;
            font-weight: 800;
        }

        .koel-button:hover,
        .koel-button:focus-visible,
        .koel-mini-tile:hover,
        .koel-list__row:hover {
            transform: translateY(-1px);
        }

        .koel-button--ghost {
            border-color: rgba(255, 255, 255, 0.08);
            background: rgba(255, 255, 255, 0.04);
            color: #f3f5f7;
        }

        .koel-main {
            display: grid;
            gap: 20px;
        }

        .koel-topbar {
            min-height: 76px;
            padding: 16px 18px;
            border-radius: 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
        }

        .koel-searchbar {
            flex: 1;
            min-width: 0;
            min-height: 44px;
            padding: 0 16px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            gap: 12px;
            background: rgba(255, 255, 255, 0.04);
            color: #b8c1c9;
            font-size: 14px;
            font-weight: 600;
        }

        .koel-searchbar__icon {
            width: 12px;
            height: 12px;
            border: 2px solid #8aa6b8;
            border-radius: 999px;
            position: relative;
            flex: 0 0 auto;
        }

        .koel-searchbar__icon::after {
            content: "";
            position: absolute;
            right: -5px;
            bottom: -5px;
            width: 6px;
            height: 2px;
            background: #8aa6b8;
            transform: rotate(45deg);
            transform-origin: center;
        }

        .koel-hero {
            padding: 26px;
            border-radius: 30px;
            display: grid;
            grid-template-columns: 300px minmax(0, 1fr);
            gap: 28px;
            align-items: center;
        }

        .koel-hero__cover {
            aspect-ratio: 1;
            border-radius: 28px;
            overflow: hidden;
            background:
                linear-gradient(145deg, rgba(88, 208, 141, 0.26), rgba(48, 60, 78, 0.9)),
                #141a22;
            box-shadow: 0 26px 60px rgba(0, 0, 0, 0.3);
        }

        .koel-hero__cover img,
        .koel-media-shelf__art {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .koel-cover-fallback {
            width: 100%;
            height: 100%;
            display: grid;
            place-items: center;
            font-family: "Sora", sans-serif;
            font-size: clamp(56px, 7vw, 92px);
            font-weight: 800;
            letter-spacing: -0.06em;
            color: #ffffff;
        }

        .koel-hero__copy {
            display: grid;
            gap: 16px;
        }

        .koel-hero h1,
        .koel-surface-card h2,
        .koel-section__head h2,
        .koel-queue__panel h2,
        .koel-queue__panel h3,
        .koel-list__content strong {
            margin: 0;
            color: #ffffff;
            font-family: "Sora", sans-serif;
        }

        .koel-hero h1 {
            font-size: clamp(34px, 4vw, 56px);
            line-height: 1;
            letter-spacing: -0.05em;
            text-wrap: balance;
        }

        .koel-hero p,
        .koel-surface-card p,
        .koel-list__content span,
        .koel-queue__panel p,
        .koel-queue__list li {
            margin: 0;
            color: #afbcc6;
            line-height: 1.7;
        }

        .koel-surface-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 18px;
        }

        .koel-surface-card {
            min-height: 180px;
            padding: 22px;
            border-radius: 24px;
            display: grid;
            align-content: start;
            gap: 12px;
        }

        .koel-section {
            display: grid;
            gap: 16px;
        }

        .koel-section__head {
            display: grid;
            gap: 8px;
        }

        .koel-list {
            border-radius: 24px;
            overflow: hidden;
        }

        .koel-list__row {
            display: grid;
            grid-template-columns: 56px minmax(0, 1fr) auto;
            align-items: center;
            gap: 18px;
            min-height: 76px;
            padding: 0 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }

        .koel-list__row:last-child {
            border-bottom: 0;
        }

        .koel-list__index {
            color: #7ebf95;
            font-size: 13px;
            font-weight: 800;
            letter-spacing: 0.14em;
        }

        .koel-list__content {
            min-width: 0;
            display: grid;
            gap: 4px;
        }

        .koel-list__state {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 70px;
            min-height: 32px;
            padding: 0 12px;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.05);
            color: #f1f4f7;
            font-size: 12px;
            font-weight: 700;
        }

        .koel-media-shelf {
            padding: 20px;
            border-radius: 24px;
            display: grid;
            grid-template-columns: minmax(0, 1.12fr) minmax(260px, 0.88fr);
            gap: 18px;
        }

        .koel-media-shelf__featured {
            min-height: 260px;
            border-radius: 22px;
            overflow: hidden;
            display: grid;
            grid-template-columns: minmax(200px, 0.92fr) minmax(0, 1.08fr);
            background: rgba(255, 255, 255, 0.03);
        }

        .koel-media-shelf__art {
            background:
                radial-gradient(circle at 24% 28%, rgba(88, 208, 141, 0.26), transparent 24%),
                linear-gradient(145deg, #1a2530 0%, #111820 100%);
        }

        .koel-media-shelf__copy {
            padding: 22px;
            display: grid;
            align-content: end;
            gap: 8px;
        }

        .koel-media-shelf__copy strong,
        .koel-mini-tile {
            color: #ffffff;
            font-family: "Sora", sans-serif;
        }

        .koel-media-shelf__copy p {
            margin: 0;
            color: #afbcc6;
        }

        .koel-media-shelf__rail {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 14px;
        }

        .koel-mini-tile {
            min-height: 122px;
            padding: 18px;
            border-radius: 18px;
            display: flex;
            align-items: end;
            background:
                linear-gradient(180deg, rgba(255, 255, 255, 0.06), rgba(255, 255, 255, 0.02)),
                #131922;
            border: 1px solid rgba(255, 255, 255, 0.06);
        }

        .koel-queue {
            display: grid;
            align-content: start;
            gap: 20px;
        }

        .koel-queue__panel {
            border-radius: 24px;
            padding: 22px;
            display: grid;
            gap: 12px;
        }

        .koel-queue__list {
            margin: 0;
            padding-left: 18px;
            display: grid;
            gap: 10px;
        }

        @media (max-width: 1260px) {
            .koel-app-shell {
                grid-template-columns: 220px minmax(0, 1fr);
            }

            .koel-queue {
                grid-column: 2;
                grid-template-columns: repeat(3, minmax(0, 1fr));
            }
        }

        @media (max-width: 960px) {
            .koel-app-shell,
            .koel-hero,
            .koel-media-shelf,
            .koel-media-shelf__featured {
                grid-template-columns: 1fr;
            }

            .koel-surface-grid,
            .koel-queue {
                grid-template-columns: 1fr;
            }

            .koel-hero__cover {
                max-width: 360px;
            }
        }

        @media (max-width: 720px) {
            .koel-frame {
                padding: 12px;
            }

            .koel-app-shell {
                gap: 12px;
            }

            .koel-topbar {
                flex-direction: column;
                align-items: stretch;
            }

            .koel-list__row {
                grid-template-columns: 44px minmax(0, 1fr);
            }

            .koel-list__state {
                grid-column: 2;
                justify-self: start;
            }

            .koel-media-shelf__rail {
                grid-template-columns: 1fr;
            }
        }
    </style>
@endpush
