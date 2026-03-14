@extends('frontend.layouts.master')

@section('meta_title', (blank(data_get($seo_setting, 'home_page.seo_title')) || strcasecmp((string) data_get($seo_setting, 'home_page.seo_title'), 'SkillGro') === 0) ? ($setting->app_name ?? config('app.name')) : data_get($seo_setting, 'home_page.seo_title'))
@section('meta_description', $seo_setting['home_page']['seo_description'])
@section('meta_keywords', '')

@section('contents')
    {{-- NOTE: We render the LANGUAGE theme sections here to mirror the Cowboy orange design on the main home. --}}
    @if ($sectionSetting?->hero_section)
        @include('frontend.home.language.sections.banner-area')
    @endif

    @if ($sectionSetting?->counter_section)
        @include('frontend.home.language.sections.fact-area')
    @endif

    @if (view()->exists('frontend.home.language.sections.placement-test-area'))
        @include('frontend.home.language.sections.placement-test-area')
    @else
        <section class="section-py-100">
            <div class="container">
                <div class="alert alert-info d-flex justify-content-between align-items-center flex-wrap gap-2 mb-0">
                    <div>
                        <strong>{{ __('2-Minute English Level Test') }}</strong>
                        <div>{{ __('Answer 8 quick questions and get an instant level result with a recommended plan.') }}</div>
                    </div>
                    <a href="{{ route('placement-test.show') }}" class="btn btn-primary">
                        {{ __('Start Level Test') }}
                    </a>
                </div>
            </div>
        </section>
    @endif

    @if ($sectionSetting?->latest_blog_section)
        @include('frontend.home.language.sections.blog-area')
    @endif

    @if ($sectionSetting?->instructor_section)
        @include('frontend.home.language.sections.instructor-area')
    @endif

    @if ($sectionSetting?->featured_course_section)
        @include('frontend.home.language.sections.course-area')
    @endif

    @if ($sectionSetting?->testimonial_section)
        @include('frontend.home.language.sections.testimonial-area')
    @endif

    @if ($sectionSetting?->top_category_section)
        @include('frontend.home.language.sections.category-area')
    @endif

    @if ($sectionSetting?->about_section)
        @include('frontend.home.language.sections.about-area')
    @endif

    @if ($sectionSetting?->news_letter_section)
        @include('frontend.home.language.sections.newsletter-area')
    @endif
@endsection

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
@endpush
