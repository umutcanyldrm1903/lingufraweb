@extends('frontend.layouts.master')

@section('meta_title', 'Ingilizce Ders, Ozel Ders ve Online Programlar | ' . ($setting->app_name ?? config('app.name')))
@section('meta_description', 'Turkiye genelinde ingilizce ders, online ingilizce ozel ders, speaking, is ingilizcesi ve sehir bazli birebir programlari tek platformda kesfet.')
@section('meta_keywords', 'ingilizce ders, ingilizce ozel ders, online ingilizce ozel ders, birebir ingilizce dersi, ingilizce konusma dersi, is ingilizcesi ozel ders')
@section('canonical_url', route('home'))
@section('meta_image', $setting->logo ?? $setting->favicon ?? '')

@section('contents')
    @if ($sectionSetting?->hero_section)
        @include('frontend.home.language.sections.banner-area')
    @endif

    @include('frontend.home.language.sections.partner-area')

    @if (view()->exists('frontend.home.language.sections.placement-test-area'))
        @include('frontend.home.language.sections.placement-test-area')
    @else
        <section class="section-py-100">
            <div class="container">
                <div class="alert alert-info d-flex justify-content-between align-items-center flex-wrap gap-2 mb-0">
                    <div>
                        <strong>{{ __('2 dakikalik seviye testi') }}</strong>
                        <div>{{ __('8 kisa soruyu cevapla, seviyeni ogren ve sana uygun paketi hemen gor.') }}</div>
                    </div>
                    <a href="{{ route('placement-test.show') }}" class="btn btn-primary">
                        {{ __('Teste basla') }}
                    </a>
                </div>
            </div>
        </section>
    @endif

    @if ($sectionSetting?->featured_course_section)
        @include('frontend.home.language.sections.course-area')
    @endif

    @if ($sectionSetting?->counter_section)
        @include('frontend.home.language.sections.fact-area')
    @endif

    @if ($sectionSetting?->instructor_section)
        @include('frontend.home.language.sections.instructor-area')
    @endif

    @if (($featuredInstructorVideos ?? collect())->count())
        @include('frontend.home.language.sections.instructor-video-area')
    @endif

    @if ($sectionSetting?->testimonial_section)
        @include('frontend.home.language.sections.testimonial-area')
    @endif

    @if ($sectionSetting?->about_section)
        @include('frontend.home.language.sections.about-area')
    @endif

    @if ($sectionSetting?->top_category_section)
        @include('frontend.home.language.sections.category-area')
    @endif

    @include('frontend.home.language.sections.video-showcase-area', [
        'videoSet' => [1, 4, 7],
        'videoTitle' => __('LinguFranca\'ya kisa bir bakis'),
        'videoSubtitle' => __('Platformu, egitim yaklasimini ve marka dunyasini tek bir premium video vitrinde kesfet.'),
        'videoPrefix' => __('Secim'),
    ])

    @if ($sectionSetting?->latest_blog_section)
        @include('frontend.home.language.sections.blog-area')
    @endif

    <section class="lf-seo-links section-py-100">
        <div class="container">
            <div class="lf-seo-links__shell">
                <div class="lf-seo-links__copy">
                    <span class="eyebrow">{{ __('Ogrenme rotalari') }}</span>
                    <h2 class="lf-seo-links__title">{{ __('Ihtiyacina gore dogru ingilizce ders tipini sec') }}</h2>
                    <p class="lf-seo-links__lead">
                        {{ __('Ingilizce ozel ders, online ders, speaking, is ingilizcesi ve sehir bazli sayfalar arasindan sana uygun rotayi sec. Egitmenleri, seviye tespitini ve program detaylarini tek akista karsilastir.') }}
                    </p>
                </div>
                <div class="lf-seo-links__grid">
                    <a href="{{ route('english-private-lessons') }}" class="lf-seo-links__item">
                        <strong>{{ __('Ingilizce Ozel Ders') }}</strong>
                        <span>{{ __('Tum ozel ders rotalarini karsilastir') }}</span>
                    </a>
                    <a href="{{ route('english-private-lessons.online') }}" class="lf-seo-links__item">
                        <strong>{{ __('Online Ingilizce Ozel Ders') }}</strong>
                        <span>{{ __('Esnek saatli canli ders planini gor') }}</span>
                    </a>
                    <a href="{{ route('english-private-lessons.speaking') }}" class="lf-seo-links__item">
                        <strong>{{ __('Ingilizce Konusma Dersi') }}</strong>
                        <span>{{ __('Speaking ve akicilik odagini incele') }}</span>
                    </a>
                    <a href="{{ route('english-private-lessons.business') }}" class="lf-seo-links__item">
                        <strong>{{ __('Is Ingilizcesi Ozel Ders') }}</strong>
                        <span>{{ __('Toplanti, sunum ve mail dili uzerine calis') }}</span>
                    </a>
                    <a href="{{ route('english-private-lessons.istanbul') }}" class="lf-seo-links__item">
                        <strong>{{ __('Istanbul Ingilizce Ozel Ders') }}</strong>
                        <span>{{ __('Yogun takvime uygun rota') }}</span>
                    </a>
                    <a href="{{ route('english-private-lessons.ankara') }}" class="lf-seo-links__item">
                        <strong>{{ __('Ankara Ingilizce Ozel Ders') }}</strong>
                        <span>{{ __('Planli ve hedef odakli ders akisina bak') }}</span>
                    </a>
                </div>
            </div>
        </div>
    </section>

    @if ($sectionSetting?->news_letter_section)
        @include('frontend.home.language.sections.newsletter-area')
    @endif

    <div class="lf-video-modal" id="lf-video-modal" aria-hidden="true">
        <div class="lf-video-modal__backdrop" data-video-modal-close></div>
        <div class="lf-video-modal__dialog" role="dialog" aria-modal="true" aria-label="{{ __('Video Player') }}">
            <button type="button" class="lf-video-modal__close" data-video-modal-close aria-label="{{ __('Close') }}">
                <i class="fas fa-times" aria-hidden="true"></i>
            </button>
            <video id="lf-video-modal-player" class="lf-video-modal__player" controls playsinline preload="metadata"></video>
        </div>
    </div>

@endsection

@push('structured_data')
    @php
        $homePageSchema = [
            '@context' => 'https://schema.org',
            '@type' => 'WebPage',
            'name' => $setting->app_name,
            'url' => route('home'),
            'description' => 'Turkiye genelinde ingilizce ders, online ozel ders, speaking ve is ingilizcesi programlari.',
            'about' => [
                ['@type' => 'Thing', 'name' => 'Ingilizce ders'],
                ['@type' => 'Thing', 'name' => 'Ingilizce ozel ders'],
                ['@type' => 'Thing', 'name' => 'Online ingilizce ozel ders'],
            ],
        ];
        $faqEntities = collect($faqs ?? [])
            ->filter(fn($faq) => filled($faq?->question) && filled($faq?->answer))
            ->map(fn($faq) => [
                '@type' => 'Question',
                'name' => trim(strip_tags((string) $faq->question)),
                'acceptedAnswer' => [
                    '@type' => 'Answer',
                    'text' => trim(strip_tags((string) $faq->answer)),
                ],
            ])
            ->values()
            ->all();
    @endphp
    <script type="application/ld+json">{!! json_encode($homePageSchema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}</script>
    @if (!empty($faqEntities))
        <script type="application/ld+json">
            {!! json_encode([
                '@context' => 'https://schema.org',
                '@type' => 'FAQPage',
                'mainEntity' => $faqEntities,
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}
        </script>
    @endif
@endpush

@push('styles')
    <style>
        :root {
            --tg-theme-primary: #0e5c93;
            --tg-theme-secondary: #f6a105;
            --tg-common-color-dark: #0b3f6c;
        }
    </style>
@endpush

@push('styles')
    <style>
        html {
            scroll-behavior: smooth;
        }

        body.home_language main.main-area {
            background:
                radial-gradient(1200px circle at 16% -10%, rgba(246, 161, 5, 0.12), transparent 55%),
                radial-gradient(900px circle at 88% 12%, rgba(14, 92, 147, 0.14), transparent 60%),
                linear-gradient(180deg, #ffffff 0%, #f7fbff 100%);
        }

        body.home_language .eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            letter-spacing: 0.14em;
            text-transform: uppercase;
            font-weight: 1000;
            font-size: 12px;
            margin-bottom: 10px;
        }

        body.home_language .eyebrow::before {
            content: '';
            width: 10px;
            height: 10px;
            border-radius: 999px;
            background: var(--tg-theme-secondary);
            box-shadow: 0 10px 26px rgba(246, 161, 5, 0.24);
        }

        body.home_language .btn:focus-visible,
        body.home_language a:focus-visible,
        body.home_language button:focus-visible,
        body.home_language input:focus-visible,
        body.home_language textarea:focus-visible,
        body.home_language select:focus-visible {
            outline: 3px solid rgba(246, 161, 5, 0.35);
            outline-offset: 2px;
        }

        /* Home: subtle animations */
        @media (prefers-reduced-motion: no-preference) {
            .ce-reveal-section {
                opacity: 0;
                transform: translateY(18px);
                will-change: opacity, transform;
                transition: opacity .85s cubic-bezier(.2, .8, .2, 1), transform .85s cubic-bezier(.2, .8, .2, 1);
            }

            .ce-reveal-section.is-in {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @media (prefers-reduced-motion: reduce) {
            .ce-reveal-section {
                opacity: 1 !important;
                transform: none !important;
                transition: none !important;
            }
        }

        /* Home mobile tightening */
        @media (max-width: 991px) {
            .cowboy-header-top {
                display: none !important;
            }

            #sticky-header .tgmenu__action {
                display: none !important;
            }

            #sticky-header .logo img {
                max-height: 38px;
                width: auto;
            }

            body.home_language .lang-hero {
                padding-top: 96px !important;
                padding-bottom: 52px !important;
            }

            body.home_language .lang-hero__title {
                font-size: 32px !important;
                line-height: 1.12 !important;
            }

            body.home_language .lang-hero__lead {
                font-size: 15px !important;
            }

            body.home_language .lang-hero__form-card {
                max-width: 100% !important;
                width: 100% !important;
                padding: 16px !important;
                border-radius: 16px !important;
            }

            body.home_language .lang-hero__orb,
            body.home_language .lang-hero__mesh,
            body.home_language .lang-corporate::before,
            body.home_language .lang-corporate::after,
            body.home_language .lang-instructor-cta::before,
            body.home_language .lang-instructor-cta::after,
            body.home_language .lang-journey::before,
            body.home_language .lang-journey::after,
            body.home_language .lang-testimonial::before,
            body.home_language .lang-testimonial::after,
            body.home_language .lang-app::after,
            body.home_language .lang-community::after {
                display: none !important;
            }

            body.home_language .lang-hero__feature-grid,
            body.home_language .lang-hero__stores,
            body.home_language .lang-app__phones {
                display: none !important;
            }

            body.home_language .lang-hero,
            body.home_language .lang-corporate,
            body.home_language .lang-instructor-cta,
            body.home_language .lang-journey,
            body.home_language .lang-testimonial,
            body.home_language .lang-community,
            body.home_language .lang-app,
            body.home_language .lf-teacher-vault,
            body.home_language .lf-video-showcase,
            body.home_language footer.lf-footer {
                overflow: visible !important;
            }

            body.home_language .lang-hero__rotator-wrap {
                width: 100%;
                justify-content: space-between;
                gap: 8px;
            }

            body.home_language .lang-hero__rotator {
                min-width: 0;
                flex: 1 1 auto;
            }

            body.home_language .lang-hero__rotator-item,
            body.home_language .lang-hero__bullet-list li,
            body.home_language .lang-corporate__lead,
            body.home_language .lang-instructor-cta__lead,
            body.home_language .lang-journey__lead,
            body.home_language .lang-community__lead,
            body.home_language .lang-app__lead,
            body.home_language .lf-teacher-vault__lead,
            body.home_language .lf-video-showcase__subtitle {
                font-size: 15px !important;
                line-height: 1.7 !important;
            }

            body.home_language .lang-corporate,
            body.home_language .lang-instructor-cta,
            body.home_language .lang-journey,
            body.home_language .lang-testimonial,
            body.home_language .lang-community,
            body.home_language .lang-app,
            body.home_language .lf-teacher-vault,
            body.home_language .lf-video-showcase {
                padding-top: 72px !important;
                padding-bottom: 72px !important;
            }

            body.home_language .lang-corporate__title,
            body.home_language .lang-instructor-cta__title,
            body.home_language .lang-journey__title,
            body.home_language .lang-community__title,
            body.home_language .lang-app__title,
            body.home_language .lang-testimonial__title,
            body.home_language .lf-teacher-vault__title,
            body.home_language .lf-video-showcase__title {
                font-size: 30px !important;
                line-height: 1.12 !important;
            }

            body.home_language .lang-corporate__media-bg,
            body.home_language .lang-instructor-cta__blob,
            body.home_language .lang-app__glow {
                display: none !important;
            }

            body.home_language .lang-corporate__img,
            body.home_language .lang-instructor-cta__img,
            body.home_language .lang-app__img {
                max-width: 320px !important;
                width: 100% !important;
            }

            body.home_language .lang-community__shell,
            body.home_language .lf-teacher-vault__shell,
            body.home_language .lf-seo-links__shell {
                padding: 24px !important;
                border-radius: 24px !important;
            }

            body.home_language .lang-community__showcase,
            body.home_language .lf-teacher-vault__footer {
                grid-template-columns: 1fr !important;
                flex-direction: column !important;
                align-items: flex-start !important;
            }

            body.home_language .lang-journey__grid,
            body.home_language .lang-community__showcase,
            body.home_language .lf-video-showcase__grid,
            body.home_language .lf-seo-links__grid {
                gap: 16px !important;
            }

            body.home_language .lf-seo-links__shell {
                grid-template-columns: 1fr !important;
            }
        }

        @media (max-width: 575px) {
            body.home_language .container,
            body.home_language .custom-container {
                padding-left: 14px !important;
                padding-right: 14px !important;
            }

            body.home_language .lang-hero__title {
                font-size: 28px !important;
            }

            body.home_language .lang-hero__copy {
                max-width: 100% !important;
            }

            body.home_language .lang-hero__rotator-wrap {
                flex-wrap: wrap;
                padding: 10px 12px !important;
            }

            body.home_language .lang-hero__rotator-label {
                flex: 0 0 auto;
            }

            body.home_language .lang-hero__rotator {
                width: 100% !important;
                min-width: 0 !important;
                min-height: 42px !important;
            }

            body.home_language .lang-hero__rotator-item {
                justify-content: flex-start !important;
                align-items: flex-start !important;
                white-space: normal !important;
                line-height: 1.35 !important;
            }

            body.home_language .lang-hero__bullet-list {
                gap: 10px !important;
                margin-bottom: 18px !important;
            }

            body.home_language .lang-hero__bullet-list li {
                font-size: 14px !important;
            }

            body.home_language .lang-hero__cta .btn {
                width: 100% !important;
                justify-content: center;
            }

            body.home_language .lang-corporate__actions,
            body.home_language .lang-journey__cta,
            body.home_language .lang-app__stores,
            body.home_language .lf-teacher-vault__actions {
                display: grid !important;
                gap: 10px !important;
                width: 100% !important;
            }

            body.home_language .lang-corporate__actions .lang-btn,
            body.home_language .lang-instructor-cta__btn,
            body.home_language .lang-journey__btn,
            body.home_language .lang-app__store,
            body.home_language .lf-teacher-vault__action,
            body.home_language .lf-teacher-vault__browse {
                width: 100% !important;
                justify-content: center !important;
            }

            body.home_language .lang-community__phone,
            body.home_language .lang-community__card,
            body.home_language .lf-teacher-vault__card {
                border-radius: 20px !important;
            }

            body.home_language .lang-corporate__title,
            body.home_language .lang-instructor-cta__title,
            body.home_language .lang-journey__title,
            body.home_language .lang-community__title,
            body.home_language .lang-app__title,
            body.home_language .lang-testimonial__title,
            body.home_language .lf-teacher-vault__title,
            body.home_language .lf-video-showcase__title {
                font-size: 26px !important;
            }

            body.home_language .lang-testimonial__card,
            body.home_language .lang-journey__step,
            body.home_language .lf-teacher-vault__body {
                padding-left: 16px !important;
                padding-right: 16px !important;
            }

            body.home_language .lf-seo-links__grid {
                grid-template-columns: 1fr !important;
            }

            body.home_language .lf-seo-links__item {
                min-height: auto;
            }
        }

        body.home_language .lf-video-showcase {
            position: relative;
            padding-top: 84px;
            padding-bottom: 84px;
        }

        body.home_language .lf-seo-links {
            position: relative;
            overflow: hidden;
            background: linear-gradient(180deg, #0f5c93 0%, #0b4f80 54%, #0a466f 100%);
        }

        body.home_language .lf-seo-links::before {
            content: '';
            position: absolute;
            inset: 0;
            background:
                radial-gradient(720px circle at 8% 18%, rgba(255, 255, 255, 0.08), transparent 52%),
                radial-gradient(680px circle at 92% 82%, rgba(246, 161, 5, 0.12), transparent 48%);
            pointer-events: none;
        }

        body.home_language .lf-seo-links .container {
            position: relative;
            z-index: 1;
        }

        body.home_language .lf-seo-links__shell {
            display: grid;
            grid-template-columns: minmax(0, 0.9fr) minmax(0, 1.1fr);
            gap: 24px;
            padding: 34px;
            border-radius: 32px;
            background: linear-gradient(135deg, rgba(13, 82, 131, 0.98), rgba(9, 58, 94, 0.98));
            color: #fff;
            box-shadow: 0 26px 60px rgba(3, 18, 32, 0.18);
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.08);
        }

        body.home_language .lf-seo-links__title {
            margin: 0 0 12px;
            color: #fff;
            font-size: clamp(28px, 3vw, 40px);
            line-height: 1.08;
            font-weight: 1000;
        }

        body.home_language .lf-seo-links__lead {
            margin: 0;
            color: rgba(255, 255, 255, 0.82);
            font-size: 17px;
            line-height: 1.8;
            font-weight: 600;
        }

        body.home_language .lf-seo-links__grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 14px;
        }

        body.home_language .lf-seo-links__item {
            display: grid;
            gap: 6px;
            min-height: 132px;
            padding: 20px;
            border-radius: 22px;
            background: rgba(255, 255, 255, 0.09);
            border: 1px solid rgba(255, 255, 255, 0.14);
            color: #fff;
            text-decoration: none;
            transition: transform 0.22s ease, background 0.22s ease, border-color 0.22s ease;
        }

        body.home_language .lf-seo-links__item:hover {
            transform: translateY(-4px);
            background: rgba(255, 255, 255, 0.14);
            border-color: rgba(246, 161, 5, 0.48);
        }

        body.home_language .lf-seo-links__item strong {
            font-size: 18px;
            line-height: 1.3;
            font-weight: 900;
        }

        body.home_language .lf-seo-links__item span {
            color: rgba(255, 255, 255, 0.8);
            font-size: 14px;
            font-weight: 700;
        }

        body.home_language .lf-seo-links__item--wide {
            grid-column: 1 / -1;
        }

        body.home_language .lf-video-showcase::before {
            content: '';
            position: absolute;
            inset: 0;
            background:
                radial-gradient(700px circle at 10% 10%, rgba(246, 161, 5, 0.1), transparent 55%),
                radial-gradient(680px circle at 95% 90%, rgba(14, 92, 147, 0.12), transparent 60%);
            pointer-events: none;
        }

        body.home_language .lf-video-showcase .container {
            position: relative;
            z-index: 1;
        }

        body.home_language .lf-video-showcase__head {
            max-width: 760px;
            margin: 0 auto 24px;
            text-align: center;
        }

        body.home_language .lf-video-showcase__title {
            margin: 0 0 8px;
            color: #0b3f6c;
            font-size: clamp(28px, 3.5vw, 42px);
            font-weight: 1000;
            line-height: 1.12;
        }

        body.home_language .lf-video-showcase__subtitle {
            margin: 0;
            color: #334155;
            font-size: 18px;
            font-weight: 600;
        }

        body.home_language .lf-video-showcase__grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 18px;
        }

        body.home_language .lf-video-showcase__card {
            --video-ratio: 16 / 9;
            position: relative;
            display: flex;
            align-items: stretch;
            border-radius: 20px;
            overflow: hidden;
            background: linear-gradient(180deg, #0f4f7d 0%, #0a3354 100%);
            border: 1px solid rgba(255, 255, 255, 0.2);
            aspect-ratio: var(--video-ratio);
            min-height: 260px;
            box-shadow: 0 20px 44px rgba(15, 23, 42, 0.22);
            transition: transform 0.22s ease, box-shadow 0.22s ease;
        }

        body.home_language .lf-video-showcase__card:hover {
            transform: translateY(-4px) scale(1.01);
            box-shadow: 0 28px 54px rgba(15, 23, 42, 0.28);
        }

        body.home_language .lf-video-showcase__media {
            position: relative;
            width: 100%;
            height: 100%;
            background: #0b1220;
        }

        body.home_language .lf-video-showcase__video {
            width: 100%;
            height: 100%;
            object-fit: contain;
            display: block;
            background: #0b1220;
        }

        body.home_language .lf-video-showcase__card::after {
            content: '';
            position: absolute;
            inset: auto 0 0;
            height: 35%;
            background: linear-gradient(180deg, rgba(2, 6, 23, 0) 0%, rgba(2, 6, 23, 0.65) 100%);
            pointer-events: none;
        }

        body.home_language .lf-video-showcase__badge {
            position: absolute;
            top: 12px;
            left: 12px;
            z-index: 2;
            background: rgba(246, 161, 5, 0.94);
            color: #111827;
            border-radius: 999px;
            padding: 6px 12px;
            font-size: 11px;
            font-weight: 1000;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        body.home_language .lf-video-showcase__meta {
            position: absolute;
            left: 12px;
            bottom: 12px;
            z-index: 2;
            color: #fff;
            font-size: 13px;
            font-weight: 800;
        }

        body.home_language .lf-video-showcase__expand {
            position: absolute;
            right: 12px;
            bottom: 12px;
            z-index: 3;
            border: 0;
            border-radius: 999px;
            min-height: 38px;
            padding: 8px 14px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 12px;
            font-weight: 900;
            color: #0f172a;
            background: #f6a105;
            box-shadow: 0 10px 24px rgba(246, 161, 5, 0.34);
            transition: transform 0.18s ease, box-shadow 0.18s ease;
        }

        body.home_language .lf-video-showcase__expand:hover {
            transform: translateY(-1px);
            box-shadow: 0 14px 30px rgba(246, 161, 5, 0.44);
        }

        body.home_language .lf-video-showcase__expand i {
            font-size: 13px;
            line-height: 1;
        }

        .lf-video-modal {
            position: fixed;
            inset: 0;
            z-index: 99999;
            display: none;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .lf-video-modal.is-open {
            display: flex;
        }

        .lf-video-modal__backdrop {
            position: absolute;
            inset: 0;
            background: rgba(2, 6, 23, 0.8);
            backdrop-filter: blur(2px);
        }

        .lf-video-modal__dialog {
            position: relative;
            width: min(1120px, 96vw);
            background: #020617;
            border-radius: 18px;
            border: 1px solid rgba(148, 163, 184, 0.36);
            box-shadow: 0 32px 72px rgba(2, 6, 23, 0.6);
            overflow: hidden;
        }

        .lf-video-modal__player {
            width: 100%;
            display: block;
            aspect-ratio: 16 / 9;
            background: #000;
        }

        .lf-video-modal__close {
            position: absolute;
            top: 12px;
            right: 12px;
            z-index: 2;
            width: 42px;
            height: 42px;
            border-radius: 999px;
            border: 0;
            color: #fff;
            background: rgba(15, 23, 42, 0.85);
            box-shadow: 0 10px 24px rgba(2, 6, 23, 0.42);
        }

        body.lf-video-modal-open {
            overflow: hidden;
        }

        @media (max-width: 991px) {
            body.home_language .lf-video-showcase {
                padding-top: 62px;
                padding-bottom: 62px;
            }

            body.home_language .lf-video-showcase__subtitle {
                font-size: 16px;
            }

            body.home_language .lf-video-showcase__grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            body.home_language .lf-seo-links__shell {
                grid-template-columns: 1fr;
                padding: 24px;
                border-radius: 24px;
            }
        }

        @media (max-width: 575px) {
            body.home_language .lf-video-showcase__grid {
                grid-template-columns: 1fr;
                gap: 14px;
            }

            body.home_language .lf-video-showcase__card {
                border-radius: 16px;
                min-height: 220px;
            }

            body.home_language .lf-video-showcase__title {
                font-size: 27px;
            }

            body.home_language .lf-video-showcase__subtitle {
                font-size: 15px;
            }

            body.home_language .lf-seo-links__grid {
                grid-template-columns: 1fr;
            }

            body.home_language .lf-seo-links__item {
                min-height: auto;
            }

            body.home_language .lf-seo-links__item--wide {
                grid-column: auto;
            }
        }
    </style>
@endpush

@push('scripts')
    <script>
        (() => {
            const root = document.querySelector('main.main-area');
            if (!root) return;

            const reduceMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

            const sections = Array.from(root.querySelectorAll(':scope > section'));
            if (!sections.length) return;

            const revealSections = sections.filter((section) => !section.classList.contains('lang-hero'));
            revealSections.forEach((section) => section.classList.add('ce-reveal-section'));

            const show = (section) => section.classList.add('is-in');

            const animateCounters = (() => {
                let done = false;
                return () => {
                    if (done || reduceMotion) return;
                    done = true;

                    document.querySelectorAll('.lang-stats__number').forEach((element) => {
                        const raw = (element.textContent || '').trim();
                        const endValue = parseInt(raw.replace(/[^\d]/g, ''), 10);
                        if (!Number.isFinite(endValue)) return;

                        const hasPlus = raw.includes('+');
                        const duration = 900;
                        const start = performance.now();
                        const fromValue = Math.max(0, Math.floor(endValue * 0.25));

                        const format = (value) => {
                            try {
                                return (hasPlus ? '+' : '') + value.toLocaleString('tr-TR');
                            } catch (e) {
                                return (hasPlus ? '+' : '') + String(value);
                            }
                        };

                        const tick = (now) => {
                            const t = Math.min((now - start) / duration, 1);
                            const eased = 1 - Math.pow(1 - t, 3);
                            const current = Math.floor(fromValue + (endValue - fromValue) * eased);
                            element.textContent = format(current);
                            if (t < 1) requestAnimationFrame(tick);
                        };

                        requestAnimationFrame(tick);
                    });
                };
            })();

            if (reduceMotion || !('IntersectionObserver' in window)) {
                revealSections.forEach(show);
                return;
            }

            const io = new IntersectionObserver((entries) => {
                entries.forEach((entry) => {
                    if (!entry.isIntersecting) return;
                    show(entry.target);
                    if (entry.target.classList.contains('lang-stats')) animateCounters();
                    io.unobserve(entry.target);
                });
            }, {
                threshold: 0.12
            });

            revealSections.forEach((section) => io.observe(section));
        })();
    </script>

    <script>
        (() => {
            const videos = Array.from(document.querySelectorAll('.js-lf-autoplay-video'));
            if (!videos.length) return;

            const syncAspectRatio = (video) => {
                const card = video.closest('.js-lf-video-card');
                if (!card) return;
                const width = Number(video.videoWidth || 0);
                const height = Number(video.videoHeight || 0);
                if (width > 0 && height > 0) {
                    card.style.setProperty('--video-ratio', `${width} / ${height}`);
                }
            };

            const ensureRatioListener = (video) => {
                if (video.dataset.ratioBound === '1') return;
                video.dataset.ratioBound = '1';
                video.addEventListener('loadedmetadata', () => syncAspectRatio(video));
                if (video.readyState >= 1) {
                    syncAspectRatio(video);
                }
            };

            const hydrateVideo = (video) => {
                ensureRatioListener(video);
                if (video.dataset.src && !video.src) {
                    video.src = video.dataset.src;
                    video.load();
                }
            };

            const safePlay = (video) => {
                const playPromise = video.play();
                if (playPromise && typeof playPromise.catch === 'function') {
                    playPromise.catch(() => {});
                }
            };

            if (!('IntersectionObserver' in window)) {
                videos.forEach((video) => {
                    hydrateVideo(video);
                    safePlay(video);
                });
                return;
            }

            const observer = new IntersectionObserver((entries) => {
                entries.forEach((entry) => {
                    const video = entry.target;
                    if (entry.isIntersecting && entry.intersectionRatio >= 0.35) {
                        hydrateVideo(video);
                        safePlay(video);
                    } else {
                        video.pause();
                    }
                });
            }, {
                threshold: [0, 0.35, 0.6],
                rootMargin: '120px 0px 120px 0px',
            });

            videos.forEach((video) => observer.observe(video));

            document.addEventListener('visibilitychange', () => {
                if (document.hidden) {
                    videos.forEach((video) => video.pause());
                    return;
                }
                videos.forEach((video) => {
                    const rect = video.getBoundingClientRect();
                    const visible = rect.top < window.innerHeight * 0.85 && rect.bottom > window.innerHeight * 0.15;
                    if (visible) safePlay(video);
                });
            });
        })();
    </script>

    <script>
        (() => {
            const modal = document.getElementById('lf-video-modal');
            const modalPlayer = document.getElementById('lf-video-modal-player');
            if (!modal || !modalPlayer) return;

            const openButtons = Array.from(document.querySelectorAll('.js-lf-open-video-modal'));
            if (!openButtons.length) return;

            const closeModal = () => {
                modal.classList.remove('is-open');
                modal.setAttribute('aria-hidden', 'true');
                document.body.classList.remove('lf-video-modal-open');
                modalPlayer.pause();
                modalPlayer.removeAttribute('src');
                modalPlayer.load();
            };

            const openModal = (src) => {
                if (!src) return;
                modalPlayer.src = src;
                modal.classList.add('is-open');
                modal.setAttribute('aria-hidden', 'false');
                document.body.classList.add('lf-video-modal-open');
                modalPlayer.currentTime = 0;
                modalPlayer.muted = false;
                modalPlayer.volume = 1;
                const playPromise = modalPlayer.play();
                if (playPromise && typeof playPromise.catch === 'function') {
                    playPromise.catch(() => {});
                }
            };

            openButtons.forEach((button) => {
                button.addEventListener('click', () => {
                    const card = button.closest('.js-lf-video-card');
                    const previewVideo = card ? card.querySelector('.js-lf-autoplay-video') : null;
                    const srcFromPreview = previewVideo ? (previewVideo.currentSrc || previewVideo.dataset.src || previewVideo.src) : '';
                    const src = button.dataset.videoSrc || srcFromPreview;
                    openModal(src);
                });
            });

            modal.querySelectorAll('[data-video-modal-close]').forEach((closeTarget) => {
                closeTarget.addEventListener('click', closeModal);
            });

            document.addEventListener('keydown', (event) => {
                if (event.key === 'Escape' && modal.classList.contains('is-open')) {
                    closeModal();
                }
            });
        })();
    </script>
@endpush
