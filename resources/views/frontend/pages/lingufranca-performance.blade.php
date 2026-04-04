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
    $generalProgram = $downloads[0] ?? null;
    $examPrograms = array_slice($downloads, 1);
    $generalMilestones = $pageData['milestones'][0]['items'] ?? [];
    $examMilestones = $pageData['milestones'][1]['items'] ?? [];
@endphp

@section('meta_title', $pageData['meta_title'] . ' | ' . $siteName)
@section('meta_description', $pageData['meta_description'])
@section('meta_keywords', $pageData['meta_keywords'])
@section('canonical_url', $canonicalUrl)
@section('meta_image', $pageData['meta_image_url'] ?? '')
@section('hide_public_header', '1')
@section('hide_public_footer', '1')

@section('contents')
    <section class="tw-flex tw-min-h-[100vh] tw-flex-col tw-bg-black tw-text-white">
        <header
            class="tw-max-w-lg:tw-px-4 tw-max-w-lg:tw-mr-auto tw-absolute tw-top-0 tw-z-20 tw-flex tw-h-[60px] tw-w-full tw-bg-opacity-0 tw-px-[5%] lg:tw-justify-around">
            <a class="tw-h-[50px] tw-w-[50px] tw-p-[4px]" href="{{ $homeUrl }}">
                @if (!empty($setting?->logo))
                    <img
                        src="{{ asset($setting->logo) }}"
                        alt="{{ $siteName }}"
                        class="tw-object tw-h-full tw-w-full"
                    />
                @endif
            </a>
            <div class="collapsible-header animated-collapse max-lg:tw-shadow-md" id="collapsed-header-items">
                <div
                    class="tw-flex tw-h-full tw-w-max tw-gap-5 tw-text-base max-lg:tw-mt-[30px] max-lg:tw-flex-col max-lg:tw-place-items-end max-lg:tw-gap-5 lg:tw-mx-auto lg:tw-place-items-center">
                    <a class="header-links" href="#programlar">Programlar</a>
                    <a class="header-links" href="#videolar">Videolar</a>
                    <a class="header-links" href="#fiyat">Fiyat</a>
                    <a class="header-links" href="#sss">SSS</a>
                </div>
                <div
                    class="tw-mx-4 tw-flex tw-place-items-center tw-gap-[20px] tw-text-base max-md:tw-w-full max-md:tw-flex-col max-md:tw-place-content-center">
                    <a
                        href="{{ $applyUrl }}"
                        aria-label="apply"
                        class="tw-rounded-full tw-bg-white tw-px-3 tw-py-2 tw-text-black tw-transition-transform tw-duration-[0.3s] hover:tw-translate-x-2">
                        <span>Programa Başvur</span>
                        <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
            </div>
            <button
                class="bi bi-list tw-absolute tw-right-3 tw-top-3 tw-z-50 tw-text-3xl tw-text-white lg:tw-hidden"
                onclick="toggleHeader()"
                aria-label="menu"
                id="collapse-btn"></button>
        </header>

        <section class="hero-section tw-relative tw-flex tw-min-h-[100vh] tw-w-full tw-flex-col tw-overflow-hidden max-md:tw-mt-[50px]" id="hero-section">
            <div class="tw-flex tw-h-full tw-min-h-[100vh] tw-w-full tw-flex-col tw-place-content-center tw-gap-6 tw-p-[5%] max-xl:tw-place-items-center max-lg:tw-p-4">
                <div class="tw-flex tw-flex-col tw-place-content-center tw-items-center">
                    <div class="reveal-up gradient-text tw-text-center tw-text-6xl tw-font-semibold tw-uppercase tw-leading-[80px] max-lg:tw-text-4xl max-md:tw-leading-snug">
                        <span>{{ $pageData['eyebrow'] }}</span>
                        <br />
                        <span>{{ $pageData['title'] }}</span>
                    </div>
                    <div class="reveal-up tw-mt-10 tw-max-w-[520px] tw-p-2 tw-text-center tw-text-gray-300 max-lg:tw-max-w-full">
                        {{ $pageData['lead'] }}
                    </div>
                    <div class="reveal-up tw-mt-10 tw-flex tw-place-items-center tw-gap-4">
                        <a class="btn tw-bg-[#6059f7] tw-shadow-lg tw-shadow-primary tw-transition-transform tw-duration-[0.3s] hover:tw-scale-x-[1.03]" href="{{ $applyUrl }}">
                            Programa Başvur
                        </a>
                        <a class="btn tw-flex tw-gap-2 !tw-bg-black !tw-text-white tw-transition-colors tw-duration-[0.3s] hover:!tw-bg-white hover:!tw-text-black" href="{{ $testUrl }}">
                            <i class="bi bi-play-circle-fill"></i>
                            <span>Seviye Tespiti</span>
                        </a>
                    </div>
                </div>

                <div class="reveal-up tw-relative tw-mt-8 tw-flex tw-w-full tw-place-content-center tw-place-items-center" id="dashboard-container">
                    <div class="tw-relative tw-max-w-[80%] tw-overflow-hidden tw-rounded-xl tw-bg-transparent max-md:tw-max-w-full" id="dashboard">
                        @if (!empty($pageData['hero_primary_visual']))
                            <img
                                src="{{ $pageData['hero_primary_visual'] }}"
                                alt="LinguFranca performans görseli"
                                class="tw-h-full tw-w-full tw-object-cover tw-opacity-90 max-lg:tw-object-contain"
                            />
                        @endif
                    </div>
                    <div class="hero-img-bg-grad tw-absolute tw-left-[20%] tw-top-5 tw-h-[200px] tw-w-[200px]"></div>
                </div>
            </div>
        </section>

        <section class="tw-relative tw-flex tw-w-full tw-flex-col tw-place-content-center tw-place-items-center tw-overflow-hidden tw-p-8">
            <h2 class="reveal-up tw-text-3xl max-md:tw-text-xl">Basında ve öğrenci videolarında görünen sistem</h2>
            <div class="reveal-up carousel-container">
                <div class="carousel tw-mt-6 tw-flex tw-w-full tw-gap-5 max-md:tw-gap-2">
                    @foreach ($pageData['press_badges'] as $badge)
                        <div class="carousel-img tw-h-[30px] tw-w-[180px] tw-text-center tw-text-sm tw-text-gray-300">{{ $badge }}</div>
                    @endforeach
                </div>
            </div>
        </section>

        <section class="tw-relative tw-flex tw-w-full tw-flex-col tw-place-content-center tw-place-items-center tw-overflow-hidden tw-p-6" id="programlar">
            <div class="tw-mt-8 tw-flex tw-flex-col tw-place-items-center tw-gap-5">
                <div class="reveal-up tw-mt-5 tw-flex tw-flex-col tw-gap-3 tw-text-center">
                    <h2 class="tw-text-4xl tw-font-medium tw-text-gray-200 max-md:tw-text-3xl">Sistemin omurgası</h2>
                    <p class="tw-max-w-[680px] tw-text-gray-300">{{ $pageData['manifesto_lead'] }}</p>
                </div>
                <div class="tw-mt-6 tw-flex tw-max-w-[90%] tw-flex-wrap tw-place-content-center tw-gap-8 max-lg:tw-flex-col">
                    @foreach ($pageData['manifesto_points'] as $point)
                        <div class="reveal-up tw-flex tw-h-[380px] tw-w-[450px] tw-flex-col tw-gap-3 tw-text-center max-md:tw-w-[320px]">
                            <div class="border-gradient tw-h-[200px] tw-w-full tw-overflow-hidden max-md:tw-h-[150px]">
                                <div class="tw-flex tw-h-full tw-w-full tw-place-content-center tw-place-items-end tw-p-2">
                                    <i class="bi bi-stars tw-text-7xl tw-text-gray-200 max-md:tw-text-5xl"></i>
                                </div>
                            </div>
                            <div class="tw-flex tw-flex-col tw-gap-4 tw-p-2">
                                <h3 class="tw-mt-4 tw-text-2xl tw-font-normal max-md:tw-text-xl">{{ $point['title'] }}</h3>
                                <div class="tw-text-gray-300">{{ $point['description'] }}</div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        <section class="tw-relative tw-flex tw-min-h-[80vh] tw-w-full tw-flex-col tw-place-content-center tw-place-items-center tw-overflow-hidden tw-p-6">
            <div class="tw-mt-8 tw-flex tw-flex-col tw-place-items-center tw-gap-5">
                <div class="reveal-up tw-mt-5 tw-flex tw-flex-col tw-gap-3 tw-text-center">
                    <h2 class="tw-text-4xl tw-font-medium tw-text-gray-200 max-md:tw-text-2xl">Program içerikleri</h2>
                </div>
                <div class="tw-mt-6 tw-flex tw-max-w-[90%] tw-flex-wrap tw-place-content-center tw-gap-8 max-lg:tw-flex-col">
                    @foreach ($pageData['resource_columns'] as $column)
                        <div class="reveal-up tw-flex tw-h-[220px] tw-w-[450px] tw-gap-8 tw-rounded-xl tw-border-[1px] tw-border-outlineColor tw-bg-secondary tw-p-8 max-md:tw-w-[320px]">
                            <div class="tw-text-4xl max-md:tw-text-2xl"><i class="bi bi-check2-circle"></i></div>
                            <div class="tw-flex tw-flex-col tw-gap-4">
                                <h3 class="tw-text-2xl max-md:tw-text-xl">{{ $column['label'] }}</h3>
                                <p class="tw-text-gray-300 max-md:tw-text-sm">{{ implode(' · ', $column['items']) }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        <section class="tw-relative tw-flex tw-min-h-[80vh] tw-w-full tw-flex-col tw-place-content-center tw-place-items-center tw-overflow-hidden tw-p-6">
            <div class="reveal-up tw-flex tw-min-h-[60vh] tw-place-content-center tw-place-items-center tw-gap-[10%] max-lg:tw-flex-col max-lg:tw-gap-10">
                <div class="tw-flex">
                    <div class="tw-max-h-[650px] tw-max-w-[850px] tw-overflow-hidden tw-rounded-lg tw-shadow-lg tw-shadow-[rgba(96,89,247,0.45)]">
                        @if (!empty($generalProgram['cover_url']))
                            <img
                                src="{{ $generalProgram['cover_url'] }}"
                                alt="{{ $generalProgram['title'] ?? 'Genel İngilizce' }}"
                                class="tw-h-full tw-w-full tw-object-cover"
                            />
                        @endif
                    </div>
                </div>
                <div class="tw-mt-6 tw-flex tw-max-w-[450px] tw-flex-col tw-gap-4">
                    <h3 class="tw-text-4xl tw-font-medium max-md:tw-text-2xl">{{ $generalProgram['title'] ?? 'Genel İngilizce' }}</h3>
                    <div class="tw-mt-4 tw-flex tw-flex-col tw-gap-3">
                        @foreach ($generalMilestones as $item)
                            <h4 class="tw-text-xl tw-font-medium">
                                <i class="bi bi-check-all !tw-text-2xl"></i>
                                {{ $item }}
                            </h4>
                        @endforeach
                        <span class="tw-text-lg tw-text-gray-300 max-md:tw-text-base">{{ $generalProgram['result'] ?? '' }}</span>
                        @if (!empty($generalProgram['file_url']))
                            <a href="{{ $generalProgram['file_url'] }}" target="_blank" rel="noopener" class="btn tw-mt-4">PDF'i Aç</a>
                        @endif
                    </div>
                </div>
            </div>
        </section>

        <section class="tw-relative tw-flex tw-min-h-[80vh] tw-w-full tw-flex-col tw-place-content-center tw-place-items-center tw-overflow-hidden tw-p-6" id="videolar">
            <div class="tw-mt-8 tw-flex tw-flex-col tw-place-items-center tw-gap-5">
                <div class="reveal-up tw-mt-5 tw-flex tw-flex-col tw-gap-3 tw-text-center">
                    <h2 class="tw-text-4xl tw-font-medium tw-text-gray-200 max-md:tw-text-3xl">Video kanıtları</h2>
                    <p class="tw-max-w-[680px] tw-text-gray-300">{{ $pageData['proof_lead'] }}</p>
                </div>
                <div class="tw-mt-6 tw-flex tw-max-w-[90%] tw-flex-wrap tw-place-content-center tw-gap-8 max-lg:tw-flex-col">
                    @foreach ($mediaLibrary as $item)
                        <div class="reveal-up tw-flex tw-h-[320px] tw-w-[360px] tw-flex-col tw-overflow-hidden tw-rounded-xl tw-border-[1px] tw-border-outlineColor tw-bg-secondary">
                            <div class="tw-relative tw-h-[180px] tw-w-full tw-bg-black">
                                @if (!empty($item['poster_url']))
                                    <img src="{{ $item['poster_url'] }}" alt="{{ $item['title'] }}" class="tw-h-full tw-w-full tw-object-cover">
                                @endif
                                <button
                                    type="button"
                                    class="btn tw-absolute tw-bottom-3 tw-left-3"
                                    data-video-url="{{ $item['file_url'] }}"
                                    data-video-title="{{ $item['title'] }}"
                                    data-video-poster="{{ $item['poster_url'] ?? '' }}"
                                    onclick="openVideoModal(this)">
                                    Videoyu Aç
                                </button>
                            </div>
                            <div class="tw-flex tw-flex-col tw-gap-2 tw-p-4">
                                <strong class="tw-text-sm tw-text-gray-400">{{ $item['category'] }} · {{ $item['duration'] }}</strong>
                                <h3 class="tw-text-lg">{{ $item['title'] }}</h3>
                                <p class="tw-text-sm tw-text-gray-300">{{ $item['description'] }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        <section class="tw-relative tw-flex tw-min-h-[80vh] tw-w-full tw-flex-col tw-place-content-center tw-place-items-center tw-overflow-hidden tw-p-6" id="fiyat">
            <div class="tw-mt-8 tw-flex tw-flex-col tw-place-items-center tw-gap-5">
                <div class="reveal-up tw-mt-5 tw-flex tw-flex-col tw-gap-3 tw-text-center">
                    <h2 class="tw-text-4xl tw-font-medium tw-text-gray-200 max-md:tw-text-3xl">{{ $pageData['pricing_title'] }}</h2>
                    <p class="tw-max-w-[680px] tw-text-gray-300">{{ $pageData['pricing_lead'] }}</p>
                </div>
                <div class="tw-mt-6 tw-flex tw-max-w-[90%] tw-flex-wrap tw-place-content-center tw-gap-8 max-lg:tw-flex-col">
                    @foreach ($pageData['packages'] as $package)
                        <div class="reveal-up tw-flex tw-h-[280px] tw-w-[320px] tw-flex-col tw-gap-3 tw-rounded-xl tw-border-[1px] tw-border-outlineColor tw-bg-secondary tw-p-6">
                            <strong class="tw-text-lg">{{ $package['name'] }}</strong>
                            <span class="tw-text-3xl tw-font-semibold">{{ $package['price'] }}</span>
                            <span class="tw-text-sm tw-text-gray-400">{{ $package['unit'] }}</span>
                            <p class="tw-text-sm tw-text-gray-300">{{ $package['note'] }}</p>
                            <a class="btn tw-mt-auto" href="{{ $applyUrl }}">Başvur</a>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        <section class="tw-relative tw-flex tw-w-full tw-flex-col tw-place-content-center tw-place-items-center tw-overflow-hidden tw-p-6" id="sss">
            <div class="tw-mt-8 tw-flex tw-flex-col tw-place-items-center tw-gap-5">
                <div class="reveal-up tw-mt-5 tw-flex tw-flex-col tw-gap-3 tw-text-center">
                    <h2 class="tw-text-4xl tw-font-medium tw-text-gray-200 max-md:tw-text-2xl">SSS</h2>
                </div>
                <div class="faq tw-mt-6 tw-flex tw-w-full tw-max-w-[800px] tw-flex-col tw-gap-3">
                    @foreach ($pageData['faq'] as $faq)
                        <div class="reveal-up tw-rounded-lg tw-border-[1px] tw-border-outlineColor tw-bg-secondary">
                            <button class="faq-accordion tw-flex tw-w-full tw-items-center tw-justify-between">
                                <span>{{ $faq['question'] }}</span>
                                <i class="bi bi-chevron-down"></i>
                            </button>
                            <div class="content">
                                <p>{{ $faq['answer'] }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        <section class="tw-relative tw-flex tw-w-full tw-flex-col tw-place-content-center tw-place-items-center tw-overflow-hidden tw-p-10">
            <div class="reveal-up tw-flex tw-flex-col tw-place-items-center tw-gap-4 tw-text-center">
                <h2 class="tw-text-3xl tw-font-medium">{{ $pageData['cta_title'] }}</h2>
                <p class="tw-max-w-[600px] tw-text-gray-300">{{ $pageData['cta_text'] }}</p>
                <div class="tw-flex tw-gap-4">
                    <a class="btn" href="{{ $applyUrl }}">Programa Başvur</a>
                    <a class="btn !tw-bg-black !tw-text-white" href="{{ $testUrl }}">Seviye Tespiti</a>
                </div>
            </div>
        </section>

        <footer class="tw-flex tw-w-full tw-flex-col tw-place-items-center tw-gap-4 tw-p-8 tw-text-sm tw-text-gray-400">
            <div>{{ $siteName }} · LinguFranca Performans Sistemi</div>
            <div class="tw-flex tw-gap-4">
                <a class="footer-link" href="{{ $homeUrl }}">Ana Sayfa</a>
                <a class="footer-link" href="{{ $applyUrl }}">İletişim</a>
                <a class="footer-link" href="{{ route('mobile-app-privacy-policy') }}">Gizlilik</a>
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
    <link rel="stylesheet" href="{{ asset('frontend/css/saasy-dark-tailwind.css') }}">
    <link rel="stylesheet" href="{{ asset('frontend/css/saasy-dark.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.3/font/bootstrap-icons.min.css" integrity="sha512-dPXYcDub/aeb08c63jRq/k6GaKccl256JQy/AnOq7CAnEZ9FzSL9wSbcZkMp4R26vBsMLFYH4kQ67/bbV8XaCQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
@endpush

@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js" integrity="sha512-3m9E7OrJcFAR2bE6a4s2U6fsPty2SlpxQekT2sJb0gwR0By/QLoM4E2eZpQU4yAZc9G5hvDXMd1q1vJx0o5s4A==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/ScrollTrigger.min.js" integrity="sha512-3P9QKf7GzO9bK5W7oMG0MzY6mFhVSG9s0cmef8Wwyq1y9wqQMRk1Cax4Ry0Y2h4xTn2u1blw2lW8XyS71W4YIg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="{{ asset('frontend/js/saasy-dark.js') }}"></script>
    <script>
        const modal = document.getElementById('lfpsVideoModal');
        const modalTitle = document.getElementById('lfpsVideoTitle');
        const player = document.getElementById('lfpsVideoPlayer');
        const closeModal = () => {
            modal.classList.remove('is-open');
            modal.setAttribute('aria-hidden', 'true');
            player.pause();
            player.removeAttribute('src');
            player.removeAttribute('poster');
            player.load();
            document.body.style.overflow = '';
        };
        const openVideoModal = (btn) => {
            const url = btn.getAttribute('data-video-url');
            const title = btn.getAttribute('data-video-title') || 'Video';
            const poster = btn.getAttribute('data-video-poster') || '';
            if (!url) return;
            modalTitle.textContent = title;
            player.src = url;
            if (poster) player.poster = poster;
            modal.classList.add('is-open');
            modal.setAttribute('aria-hidden', 'false');
            document.body.style.overflow = 'hidden';
            player.play().catch(() => {});
        };
        window.openVideoModal = openVideoModal;
        modal.querySelectorAll('[data-video-close]').forEach((el) => el.addEventListener('click', closeModal));
        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape' && modal.classList.contains('is-open')) closeModal();
        });
    </script>
@endpush
