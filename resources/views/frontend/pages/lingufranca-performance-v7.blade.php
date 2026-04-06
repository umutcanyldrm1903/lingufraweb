@extends('frontend.layouts.master')

@php
    $siteName = $setting->app_name ?? config('app.name');
    $canonicalUrl = route('lingufranca-performance');
    $applyUrl = route('contact.index');
    $testUrl = route('placement-test.show');
    $homeUrl = route('home');

    $topLinks = $pageData['top_links'] ?? [];
    $heroBadges = array_slice($pageData['hero_badges'] ?? [], 0, 3);
    $heroStats = $pageData['hero_stats'] ?? [];
    $heroNotes = array_slice($heroStats, 0, 2);
    $proofBadges = array_slice($pageData['press_badges'] ?? [], 0, 3);
    $manifestoPoints = array_slice($pageData['manifesto_points'] ?? [], 0, 3);
    $resourceColumns = array_map(function ($column) {
        $column['items'] = array_slice($column['items'] ?? [], 0, 3);
        return $column;
    }, array_slice($pageData['resource_columns'] ?? [], 0, 2));
    $fitFor = array_slice($pageData['fit_for'] ?? [], 0, 4);
    $fitNotFor = array_slice($pageData['fit_not_for'] ?? [], 0, 4);
    $steps = array_slice($pageData['steps'] ?? [], 0, 4);
    $packages = array_slice($pageData['packages'] ?? [], 0, 2);
    $pricingNotes = array_slice($pageData['pricing_notes'] ?? [], 0, 4);
    $faqs = array_slice($pageData['faq'] ?? [], 0, 4);
    $programs = array_map(function ($program) {
        $program['bullets'] = array_slice($program['bullets'] ?? [], 0, 1);
        $program['teaser'] = $program['result'] ?? ($program['subtitle'] ?? '');
        return $program;
    }, $downloads ?? []);
    $primaryProgram = $programs[0] ?? null;
    $mediaCount = count($mediaLibrary ?? []);
    $featuredMedia = $mediaLibrary[0] ?? null;
    $secondaryMedia = array_slice($mediaLibrary ?? [], 1);

    $teamRoles = [
        'Kurucu & Dil Kocu',
        'Ana Egitmen (Native / Turk)',
        'Mufredat Sorumlusu',
        'Rehber Mentor',
    ];

    $heroOverline = '3 PDF + 6 video tek sayfada';
    $heroTitleShort = 'Tum performans arsivi bu sayfada.';
    $heroLeadShort = 'Genel Ingilizce, IELTS ve PTE deckleri ile tum basin ve ogrenci videolari dogrudan ekranda.';
    $systemTitleShort = 'Tum klasor tek akista.';
    $systemLeadShort = 'Drive klasorundeki program omurgasi, surec, fiyat ve medya kayitlari birlikte gorunur.';
    $fitTitleShort = 'Bu arsivde ne var?';
    $fitLeadShort = 'Program deckleri, surec anlatimlari, fiyat akislari ve sahadaki medya kayitlari ayni sayfada.';
    $resourceTitleShort = '3 ayri program akisi.';
    $resourceLeadShort = 'Genel Ingilizce, IELTS ve PTE icerikleri ayri deckler halinde tam sayfa akiyor.';
    $processTitleShort = '4 net adim.';
    $processLeadShort = 'Analiz, plan, uygulama, takip.';
    $proofTitleShort = 'Sistem ekranda da gorunuyor.';
    $proofLeadShort = 'Klasordeki basin ve ogrenci videolarinin tamami bu bolumde.';
    $pricingTitleShort = 'Net fiyat. Net paket.';
    $pricingLeadShort = 'Paketini sec, takvimini kur, basla.';
    $faqTitleShort = 'Karar oncesi en cok sorulanlar';
    $ctaTitleShort = 'Planini netlestir.';
    $ctaTextShort = 'Dogru akisi sec, seviye tespitiyle basla.';
    $deckGalleries = collect($deckGalleries ?? [])
        ->map(function ($deck) {
            $deck['eyebrow'] = match ($deck['slug'] ?? '') {
                'general-english' => 'Genel Ingilizce Akisi',
                'ielts-exam' => 'IELTS / TOEFL / YDS Akisi',
                'pte-academic' => 'PTE Academic Akisi',
                default => $deck['eyebrow'] ?? 'Program Akisi',
            };
            $deck['lead'] = match ($deck['slug'] ?? '') {
                'general-english' => 'Genel Ingilizce deckindeki tum sayfalar burada.',
                'ielts-exam' => 'IELTS deckindeki tum sayfalar burada.',
                'pte-academic' => 'PTE deckindeki tum sayfalar burada.',
                default => $deck['lead'] ?? '',
            };
            return $deck;
        })
        ->values()
        ->all();
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
                    <span class="dbp-overline">{{ $heroOverline }}</span>

                    <h1>{{ $heroTitleShort }}</h1>
                    <p>{{ $heroLeadShort }}</p>

                    @if (!empty($proofBadges))
                        <div class="dbp-hero__proofline">
                            @foreach ($proofBadges as $badge)
                                <span>{{ $badge }}</span>
                            @endforeach
                        </div>
                    @endif

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
                            <span class="dbp-hero-panel__stamp">Featured Flow</span>
                        </div>

                        <div class="dbp-hero-panel__body">
                            <span class="dbp-kicker">{{ $primaryProgram['label'] ?? 'Program' }}</span>
                            <h2>{{ $primaryProgram['title'] ?? $siteName }}</h2>
                            <p>{{ $primaryProgram['teaser'] ?? ($primaryProgram['subtitle'] ?? '') }}</p>

                            <div class="dbp-hero-panel__meta">
                                @if (!empty($primaryProgram['meta']))
                                    <span>{{ $primaryProgram['meta'] }}</span>
                                @endif
                                @if (!empty($heroBadges[0]))
                                    <span>{{ $heroBadges[0] }}</span>
                                @endif
                            </div>
                        </div>
                    </article>

                    @if (!empty($heroNotes))
                        <div class="dbp-hero-notes">
                            @foreach ($heroNotes as $metric)
                                <article class="dbp-hero-note">
                                    <strong>{{ $metric['value'] }}</strong>
                                    <span>{{ $metric['label'] }}</span>
                                </article>
                            @endforeach
                        </div>
                    @endif
                </div>
            </section>

            <section class="dbp-section" id="sistem">
                <div class="dbp-section__head dbp-reveal">
                    <span class="dbp-kicker">Sistem</span>
                    <h2>{{ $systemTitleShort }}</h2>
                    <p>{{ $systemLeadShort }}</p>
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
                    <h2>{{ $fitTitleShort }}</h2>
                    <p>{{ $fitLeadShort }}</p>
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
                    <h2>{{ $resourceTitleShort }}</h2>
                    <p>{{ $resourceLeadShort }}</p>
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
                                <p>{{ $program['teaser'] }}</p>
                                <ul>
                                    @foreach ($program['bullets'] as $bullet)
                                        <li>{{ $bullet }}</li>
                                    @endforeach
                                </ul>
                                <div class="dbp-program-card__meta">
                                    @if (!empty($program['meta']))
                                        <span>{{ $program['meta'] }}</span>
                                    @endif
                                    @if (!empty($program['subtitle']))
                                        <strong>{{ \Illuminate\Support\Str::limit($program['subtitle'], 58) }}</strong>
                                    @endif
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>

                @if (!empty($deckGalleries))
                    <div class="dbp-deck-stack">
                        @foreach ($deckGalleries as $deck)
                            <section class="dbp-deck-showcase dbp-reveal">
                                <div class="dbp-deck-showcase__head">
                                    <div class="dbp-deck-showcase__copy">
                                        <span class="dbp-kicker">{{ $deck['eyebrow'] }}</span>
                                        <h3>{{ $deck['title'] }}</h3>
                                        <p>{{ $deck['lead'] }}</p>
                                    </div>
                                    <span class="dbp-deck-showcase__count">{{ $deck['page_count'] }} sayfa</span>
                                </div>

                                <div class="dbp-deck-pages">
                                    @foreach ($deck['pages'] as $pageUrl)
                                        <figure class="dbp-deck-page">
                                            <img loading="lazy" decoding="async" src="{{ $pageUrl }}" alt="{{ $deck['title'] }} sayfa {{ $loop->iteration }}" />
                                        </figure>
                                    @endforeach
                                </div>
                            </section>
                        @endforeach
                    </div>
                @endif
            </section>

            <section class="dbp-section" id="surec">
                <div class="dbp-section__head dbp-reveal">
                    <span class="dbp-kicker">Surec</span>
                    <h2>{{ $processTitleShort }}</h2>
                    <p>{{ $processLeadShort }}</p>
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
                    <h2>{{ $proofTitleShort }}</h2>
                    <p>{{ $proofLeadShort }}</p>
                </div>

                @if (!empty($featuredMedia))
                    <article class="dbp-media-feature dbp-reveal">
                        <div class="dbp-media-feature__media">
                            <video controls preload="metadata" playsinline @if (!empty($featuredMedia['poster_url'])) poster="{{ $featuredMedia['poster_url'] }}" @endif>
                                <source src="{{ $featuredMedia['file_url'] }}" type="video/mp4">
                            </video>
                            <div class="dbp-media-feature__floating">
                                <span class="dbp-media-feature__play">Featured Proof</span>
                                <strong>{{ $featuredMedia['duration'] }}</strong>
                            </div>
                        </div>
                        <div class="dbp-media-feature__body">
                            <span class="dbp-kicker">{{ $featuredMedia['category'] }}</span>
                            <h3>{{ $featuredMedia['title'] }}</h3>
                            <p>{{ \Illuminate\Support\Str::limit($featuredMedia['description'] ?? '', 96) }}</p>
                            <div class="dbp-media-feature__meta">
                                <span>{{ $featuredMedia['duration'] }}</span>
                                <span>{{ $mediaCount }} video arsivi</span>
                            </div>
                            <div class="dbp-media-feature__actions">
                                <a class="dbp-button" href="{{ $featuredMedia['file_url'] }}" target="_blank" rel="noopener">Tam ekran ac</a>
                                <a class="dbp-button dbp-button--ghost" href="#fiyat">Paketleri incele</a>
                            </div>
                        </div>
                    </article>
                @endif

                <div class="dbp-media-grid">
                    @foreach ($secondaryMedia as $media)
                        <article class="dbp-media-card dbp-reveal">
                            <div class="dbp-media-card__thumb">
                                @if (!empty($media['poster_url']))
                                    <img src="{{ $media['poster_url'] }}" alt="{{ $media['title'] }}" />
                                @else
                                    <span>{{ $media['category'] }}</span>
                                @endif
                                <div class="dbp-media-card__overlay">
                                    <div class="dbp-media-card__meta">
                                        <span>{{ $media['category'] }}</span>
                                        <span>{{ $media['duration'] }}</span>
                                    </div>
                                    <h3>{{ $media['title'] }}</h3>
                                </div>
                            </div>
                            <p>{{ \Illuminate\Support\Str::limit($media['description'] ?? '', 78) }}</p>
                            <a href="{{ $media['file_url'] }}" target="_blank" rel="noopener">Videoyu ac</a>
                        </article>
                    @endforeach
                </div>
            </section>

            <section class="dbp-section" id="fiyat">
                <div class="dbp-section__head dbp-reveal">
                    <span class="dbp-kicker">{{ $pageData['pricing_eyebrow'] ?? 'Fiyat' }}</span>
                    <h2>{{ $pricingTitleShort }}</h2>
                    <p>{{ $pricingLeadShort }}</p>
                </div>

                <div class="dbp-price-grid">
                    @foreach ($packages as $package)
                        <article class="dbp-price-card dbp-reveal @if(!empty($package['featured'])) dbp-price-card--featured @endif">
                            @if (!empty($package['featured']))
                                <span class="dbp-price-card__badge">Onerilen paket</span>
                            @endif
                            <span class="dbp-kicker">{{ $package['name'] }}</span>
                            <h3>{{ $package['price'] }}</h3>
                            <p>{{ $package['unit'] }}</p>
                            <strong>{{ $package['note'] }}</strong>
                            <div class="dbp-price-card__actions">
                                <a class="dbp-button" href="{{ $applyUrl }}">Basvur</a>
                                <a class="dbp-button dbp-button--ghost" href="{{ $testUrl }}">Seviye Tespiti</a>
                            </div>
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
                        <h2>{{ $faqTitleShort }}</h2>
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
                <h2>{{ $ctaTitleShort }}</h2>
                <p>{{ $ctaTextShort }}</p>
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
                radial-gradient(circle at top left, rgba(96, 89, 247, 0.22), transparent 24%),
                radial-gradient(circle at top right, rgba(6, 35, 91, 0.18), transparent 22%),
                linear-gradient(180deg, #041128 0%, #071a42 45%, #05132f 100%);
            color: #eef3ff;
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
            background:
                linear-gradient(180deg, rgba(10, 29, 68, 0.96) 0%, rgba(6, 20, 47, 0.96) 100%);
            box-shadow: 0 28px 80px rgba(1, 9, 24, 0.34);
        }

        .dbp-topbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            min-height: 78px;
            padding: 16px 20px;
            border-radius: 24px;
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
            color: rgba(238, 243, 255, 0.78);
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
            background: linear-gradient(180deg, #6f68ff 0%, #6059f7 100%);
            color: #ffffff;
            font-size: 14px;
            font-weight: 800;
            border: 1px solid rgba(255, 255, 255, 0.08);
            transition: transform 180ms ease, box-shadow 180ms ease, background-color 180ms ease;
            box-shadow: 0 18px 40px rgba(96, 89, 247, 0.3);
        }

        .dbp-button--ghost {
            background: rgba(255, 255, 255, 0.04);
            border-color: rgba(255, 255, 255, 0.12);
            color: #eef3ff;
            box-shadow: none;
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
            background: rgba(96, 89, 247, 0.12);
            border: 1px solid rgba(96, 89, 247, 0.22);
            color: #cfd2ff;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .dbp-hero {
            display: grid;
            grid-template-columns: minmax(0, 1.02fr) minmax(340px, 0.98fr);
            gap: 42px;
            align-items: center;
            padding: 96px 0 62px;
            position: relative;
        }

        .dbp-hero::before {
            content: "";
            position: absolute;
            inset: 8% 0 auto 42%;
            height: 320px;
            border-radius: 999px;
            background: radial-gradient(circle, rgba(96, 89, 247, 0.22) 0%, rgba(96, 89, 247, 0) 70%);
            filter: blur(20px);
            pointer-events: none;
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
            max-width: 9ch;
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
            color: rgba(226, 235, 255, 0.74);
            line-height: 1.68;
        }

        .dbp-hero-panel {
            overflow: hidden;
            border-radius: 34px;
            transform: rotate(-1.2deg);
            position: relative;
            min-height: 620px;
        }

        .dbp-hero-panel::after {
            content: "";
            position: absolute;
            inset: auto -24px -36px auto;
            width: 220px;
            height: 220px;
            border-radius: 999px;
            background: radial-gradient(circle, rgba(96, 89, 247, 0.28), rgba(6, 35, 91, 0));
            filter: blur(2px);
            pointer-events: none;
        }

        .dbp-hero-panel__media,
        .dbp-program-card__media {
            background:
                radial-gradient(circle at 24% 24%, rgba(96, 89, 247, 0.16), transparent 24%),
                linear-gradient(145deg, rgba(9, 34, 91, 0.98), rgba(5, 20, 47, 1));
        }

        .dbp-hero-panel__media {
            position: absolute;
            inset: 0;
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
            padding: 24px;
        }

        .dbp-hero-panel__media::after {
            content: "";
            position: absolute;
            inset: 0;
            background:
                linear-gradient(180deg, rgba(4, 15, 36, 0.1) 0%, rgba(4, 15, 36, 0.1) 28%, rgba(4, 15, 36, 0.92) 100%);
        }

        .dbp-hero-panel__stamp {
            position: absolute;
            top: 20px;
            right: 20px;
            z-index: 2;
            display: inline-flex;
            align-items: center;
            min-height: 32px;
            padding: 0 14px;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.9);
            color: #06235b;
            font-size: 12px;
            font-weight: 800;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .dbp-hero__proofline {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .dbp-hero__proofline span {
            display: inline-flex;
            align-items: center;
            min-height: 34px;
            padding: 0 12px;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid rgba(255, 255, 255, 0.08);
            color: rgba(238, 243, 255, 0.82);
            font-size: 13px;
            font-weight: 700;
        }

        .dbp-hero-panel__meta,
        .dbp-program-card__meta {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
        }

        .dbp-hero__media {
            display: grid;
            gap: 18px;
        }

        .dbp-hero-panel__body {
            position: absolute;
            inset: auto 0 0 0;
            z-index: 2;
            padding: 32px;
            gap: 12px;
            background: linear-gradient(180deg, rgba(4, 15, 36, 0) 0%, rgba(4, 15, 36, 0.28) 22%, rgba(4, 15, 36, 0.92) 100%);
        }

        .dbp-hero-panel__body h2 {
            font-size: clamp(30px, 3vw, 42px);
            line-height: 1.02;
            max-width: 10ch;
        }

        .dbp-hero-panel__body p {
            max-width: 28ch;
        }

        .dbp-hero-notes {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 12px;
        }

        .dbp-hero-note {
            min-height: 96px;
            border-radius: 20px;
            padding: 18px;
            border: 1px solid rgba(255, 255, 255, 0.08);
            background: linear-gradient(180deg, rgba(13, 31, 77, 0.96) 0%, rgba(8, 24, 57, 0.96) 100%);
            box-shadow: 0 20px 50px rgba(1, 9, 24, 0.22);
            display: grid;
            align-content: end;
            gap: 6px;
        }

        .dbp-hero-note strong {
            font-family: "Sora", sans-serif;
            font-size: 26px;
            color: #ffffff;
        }

        .dbp-hero-note span {
            color: rgba(226, 235, 255, 0.72);
            font-size: 13px;
            font-weight: 600;
            line-height: 1.45;
        }

        .dbp-section {
            padding-top: 124px;
            display: grid;
            gap: 28px;
        }

        .dbp-section__head {
            max-width: 640px;
            gap: 12px;
        }

        .dbp-section__head h2 {
            font-size: clamp(30px, 3vw, 46px);
            line-height: 1.08;
        }

        .dbp-section__head p {
            max-width: 52ch;
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
            gap: 20px;
        }

        .dbp-value-grid {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }

        .dbp-resource-grid,
        .dbp-price-grid,
        .dbp-note-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .dbp-fit-layout,
        .dbp-media-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 22px;
        }

        .dbp-deck-stack {
            display: grid;
            gap: 32px;
            margin-top: 16px;
        }

        .dbp-deck-showcase {
            display: grid;
            gap: 24px;
            padding: 26px;
            border-radius: 32px;
            border: 1px solid rgba(255, 255, 255, 0.08);
            background: linear-gradient(180deg, rgba(10, 29, 68, 0.96) 0%, rgba(6, 20, 47, 0.96) 100%);
            box-shadow: 0 28px 80px rgba(1, 9, 24, 0.28);
        }

        .dbp-deck-showcase__head {
            display: flex;
            align-items: end;
            justify-content: space-between;
            gap: 18px;
        }

        .dbp-deck-showcase__copy {
            display: grid;
            gap: 10px;
            max-width: 56ch;
        }

        .dbp-deck-showcase__copy h3 {
            margin: 0;
            font-family: "Sora", sans-serif;
            color: #ffffff;
            font-size: clamp(28px, 2.8vw, 40px);
            line-height: 1.06;
        }

        .dbp-deck-showcase__copy p {
            margin: 0;
            color: rgba(226, 235, 255, 0.74);
            line-height: 1.68;
        }

        .dbp-deck-showcase__count {
            display: inline-flex;
            align-items: center;
            min-height: 38px;
            padding: 0 14px;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.06);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: #ffffff;
            font-size: 13px;
            font-weight: 800;
            white-space: nowrap;
        }

        .dbp-deck-pages {
            display: grid;
            gap: 18px;
        }

        .dbp-deck-page {
            margin: 0;
            border-radius: 24px;
            overflow: hidden;
            background: #ffffff;
            box-shadow: 0 18px 50px rgba(1, 9, 24, 0.24);
        }

        .dbp-deck-page img {
            width: 100%;
            height: auto;
            display: block;
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
            border-radius: 26px;
            padding: 28px;
        }

        .dbp-fit-card ul,
        .dbp-resource-card ul,
        .dbp-program-card__body ul {
            margin: 0;
            padding-left: 18px;
            display: grid;
            gap: 10px;
        }

        .dbp-fit-card--positive {
            background:
                linear-gradient(180deg, rgba(13, 31, 77, 0.98) 0%, rgba(8, 24, 57, 0.98) 100%);
        }

        .dbp-program-card {
            overflow: hidden;
            border-radius: 26px;
            display: grid;
            grid-template-columns: minmax(280px, 0.92fr) minmax(0, 1.08fr);
        }

        .dbp-program-card__media {
            min-height: 340px;
            position: relative;
        }

        .dbp-program-card__media::after {
            content: "";
            position: absolute;
            inset: 0;
            background: linear-gradient(180deg, rgba(4, 15, 36, 0.04) 0%, rgba(4, 15, 36, 0.52) 100%);
        }

        .dbp-program-card__meta strong,
        .dbp-media-feature__body strong,
        .dbp-price-card strong {
            color: #ffffff;
            font-size: 14px;
            font-weight: 700;
        }

        .dbp-program-card__body h3,
        .dbp-price-card h3,
        .dbp-media-feature__body h3 {
            font-size: clamp(26px, 2.6vw, 36px);
            line-height: 1.12;
        }

        .dbp-program-card__body {
            padding: 30px;
            gap: 12px;
            align-content: center;
        }

        .dbp-program-card__body p {
            color: rgba(255, 255, 255, 0.94);
            font-size: 20px;
            line-height: 1.35;
            max-width: 26ch;
        }

        .dbp-program-card__body ul {
            padding-left: 0;
            list-style: none;
        }

        .dbp-program-card__body li {
            position: relative;
            padding-left: 18px;
        }

        .dbp-program-card__body li::before {
            content: "";
            position: absolute;
            left: 0;
            top: 10px;
            width: 8px;
            height: 8px;
            border-radius: 999px;
            background: #6059f7;
            box-shadow: 0 0 0 6px rgba(96, 89, 247, 0.12);
        }

        .dbp-program-card__meta {
            margin-top: 8px;
            align-items: center;
        }

        .dbp-program-card__meta span,
        .dbp-program-card__meta strong {
            display: inline-flex;
            align-items: center;
            min-height: 34px;
            padding: 0 12px;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.08);
            font-size: 13px;
            font-weight: 700;
        }

        .dbp-step-grid {
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 22px;
        }

        .dbp-step-card__index {
            font-family: "Sora", sans-serif;
            color: #ffffff;
            font-size: 28px;
        }

        .dbp-team-card {
            border-radius: 26px;
            padding: 26px;
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
            border-radius: 30px;
            display: grid;
            grid-template-columns: minmax(0, 1.18fr) minmax(320px, 0.82fr);
            min-height: 520px;
        }

        .dbp-media-feature__media {
            min-height: 500px;
            position: relative;
            background:
                radial-gradient(circle at 24% 24%, rgba(96, 89, 247, 0.18), transparent 24%),
                #0d1524;
        }

        .dbp-media-feature__media::after {
            content: "";
            position: absolute;
            inset: 0;
            background:
                linear-gradient(180deg, rgba(4, 15, 36, 0.08) 0%, rgba(4, 15, 36, 0.16) 36%, rgba(4, 15, 36, 0.88) 100%);
            pointer-events: none;
        }

        .dbp-media-feature__floating {
            position: absolute;
            left: 26px;
            right: 26px;
            bottom: 26px;
            z-index: 2;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 14px;
        }

        .dbp-media-feature__play,
        .dbp-media-feature__floating strong {
            display: inline-flex;
            align-items: center;
            min-height: 38px;
            padding: 0 14px;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.14);
            backdrop-filter: blur(14px);
            color: #ffffff;
            font-size: 12px;
            font-weight: 800;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .dbp-media-feature__body {
            padding: 34px 32px;
            align-content: end;
        }

        .dbp-media-feature__meta,
        .dbp-media-card__meta {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .dbp-media-feature__meta span,
        .dbp-media-card__meta span {
            display: inline-flex;
            align-items: center;
            min-height: 34px;
            padding: 0 12px;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.08);
            color: rgba(238, 243, 255, 0.86);
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 0.04em;
            text-transform: uppercase;
        }

        .dbp-media-feature__actions {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 6px;
        }

        .dbp-media-card {
            overflow: hidden;
            gap: 16px;
            padding: 18px;
        }

        .dbp-media-card__thumb {
            aspect-ratio: 16 / 11;
            border-radius: 22px;
            overflow: hidden;
            background: linear-gradient(145deg, rgba(13, 31, 77, 0.96), rgba(8, 24, 57, 0.96));
            display: grid;
            place-items: center;
            position: relative;
        }

        .dbp-media-card__thumb::after {
            content: "";
            position: absolute;
            inset: 0;
            background: linear-gradient(180deg, rgba(4, 15, 36, 0.04) 0%, rgba(4, 15, 36, 0.24) 40%, rgba(4, 15, 36, 0.92) 100%);
            pointer-events: none;
        }

        .dbp-media-card__thumb img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .dbp-media-card__thumb span {
            color: rgba(238, 243, 255, 0.8);
            font-family: "Sora", sans-serif;
            font-size: 14px;
            font-weight: 700;
        }

        .dbp-media-card__overlay {
            position: absolute;
            inset: auto 18px 18px 18px;
            z-index: 2;
            display: grid;
            gap: 10px;
        }

        .dbp-media-card__overlay h3 {
            margin: 0;
            color: #ffffff;
            font-size: 24px;
            line-height: 1.05;
            max-width: 12ch;
        }

        .dbp-media-card a {
            color: #9fa3ff;
            font-size: 14px;
            font-weight: 700;
        }

        .dbp-price-card {
            min-height: 100%;
            border-radius: 26px;
            padding: 28px;
            gap: 16px;
            position: relative;
            overflow: hidden;
        }

        .dbp-price-card--featured {
            background:
                radial-gradient(circle at top right, rgba(255, 255, 255, 0.16), transparent 20%),
                linear-gradient(180deg, rgba(96, 89, 247, 0.98) 0%, rgba(61, 91, 230, 0.98) 100%);
        }

        .dbp-price-card h3 {
            letter-spacing: -0.03em;
            font-size: clamp(30px, 3vw, 44px);
            line-height: 1.04;
        }

        .dbp-price-card__badge {
            display: inline-flex;
            align-items: center;
            width: fit-content;
            min-height: 30px;
            padding: 0 12px;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.16);
            color: #ffffff;
            font-size: 12px;
            font-weight: 800;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .dbp-price-card__actions {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: auto;
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
            margin-top: 112px;
            border-radius: 30px;
            padding: 36px;
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
            .dbp-note-grid,
            .dbp-team-grid {
                grid-template-columns: 1fr;
            }

            .dbp-fit-layout,
            .dbp-media-grid {
                grid-template-columns: 1fr;
            }

            .dbp-hero-notes {
                grid-template-columns: 1fr;
            }

            .dbp-hero-panel {
                min-height: 520px;
                transform: none;
            }

            .dbp-media-feature__media {
                min-height: 360px;
            }

            .dbp-deck-showcase__head {
                flex-direction: column;
                align-items: start;
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
