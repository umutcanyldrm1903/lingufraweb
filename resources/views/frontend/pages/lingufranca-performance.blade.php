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
@endphp

@section('meta_title', $pageData['meta_title'] . ' | ' . $siteName)
@section('meta_description', $pageData['meta_description'])
@section('meta_keywords', $pageData['meta_keywords'])
@section('canonical_url', $canonicalUrl)
@section('meta_image', $pageData['meta_image_url'] ?? '')
@section('hide_public_header', '1')
@section('hide_public_footer', '1')

@section('contents')
    <section class="lfps-performance-shell tw-flex tw-min-h-[100vh] tw-flex-col tw-text-white" style="background:#050b1c;">
        <header class="tw-max-w-lg:tw-px-4 tw-max-w-lg:tw-mr-auto tw-absolute tw-top-0 tw-z-20 tw-flex tw-h-[60px] tw-w-full tw-bg-opacity-0 tw-px-[5%] lg:tw-justify-around">
            <a class="tw-h-[50px] tw-w-[50px] tw-p-[4px]" href="{{ $homeUrl }}">
                @if (!empty($setting?->logo))
                    <img src="{{ asset($setting->logo) }}" alt="{{ $siteName }}" class="tw-object tw-h-full tw-w-full" />
                @endif
            </a>
            <div class="collapsible-header animated-collapse max-lg:tw-shadow-md" id="collapsed-header-items">
                <div class="tw-flex tw-h-full tw-w-max tw-gap-5 tw-text-base max-lg:tw-mt-[30px] max-lg:tw-flex-col max-lg:tw-place-items-end max-lg:tw-gap-5 lg:tw-mx-auto lg:tw-place-items-center">
                    <a class="header-links" href="#programlar">Programlar</a>
                    <a class="header-links" href="#videolar">Videolar</a>
                    <a class="header-links" href="#fiyat">Fiyat</a>
                    <a class="header-links" href="#sss">SSS</a>
                </div>
                <div class="tw-mx-4 tw-flex tw-place-items-center tw-gap-[20px] tw-text-base max-md:tw-w-full max-md:tw-flex-col max-md:tw-place-content-center">
                    <a href="{{ $applyUrl }}" aria-label="apply" class="tw-rounded-full tw-bg-white tw-px-3 tw-py-2 tw-text-black tw-transition-transform tw-duration-[0.3s] hover:tw-translate-x-2">
                        <span>Programa Basvur</span>
                        <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
            </div>
            <button class="bi bi-list tw-absolute tw-right-3 tw-top-3 tw-z-50 tw-text-3xl tw-text-white lg:tw-hidden" onclick="toggleHeader()" aria-label="menu" id="collapse-btn"></button>
        </header>

        <section class="hero-section tw-relative tw-flex tw-min-h-[100vh] tw-w-full tw-flex-col tw-overflow-hidden max-md:tw-mt-[50px]" id="hero-section">
            <div class="tw-flex tw-h-full tw-min-h-[100vh] tw-w-full tw-flex-col tw-place-content-center tw-gap-6 tw-p-[5%] max-xl:tw-place-items-center max-lg:tw-p-4">
                <div class="tw-flex tw-flex-col tw-place-content-center tw-items-center">
                    <div class="reveal-up gradient-text tw-text-center tw-text-6xl tw-font-semibold tw-uppercase tw-leading-[80px] max-lg:tw-text-4xl max-md:tw-leading-snug">
                        <span>{{ $pageData['eyebrow'] }}</span>
                        <br />
                        <span>{{ $pageData['title'] }}</span>
                    </div>
                    <div class="reveal-up tw-mt-10 tw-max-w-[620px] tw-p-2 tw-text-center tw-text-gray-200 max-lg:tw-max-w-full">
                        {{ $pageData['lead'] }}
                    </div>
                    <div class="reveal-up tw-mt-10 tw-flex tw-place-items-center tw-gap-4">
                        <a class="btn tw-bg-[#6059f7] tw-shadow-lg tw-shadow-primary tw-transition-transform tw-duration-[0.3s] hover:tw-scale-x-[1.03]" href="{{ $applyUrl }}">
                            Programa Basvur
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
                            <img src="{{ $pageData['hero_primary_visual'] }}" alt="LinguFranca performans gorseli" class="tw-h-full tw-w-full tw-object-cover tw-opacity-90 max-lg:tw-object-contain" />
                        @endif
                    </div>
                    <div class="hero-img-bg-grad tw-absolute tw-left-[20%] tw-top-5 tw-h-[200px] tw-w-[200px]"></div>
                </div>
            </div>
        </section>

        <section class="tw-relative tw-flex tw-w-full tw-flex-col tw-place-content-center tw-place-items-center tw-overflow-hidden tw-p-8">
            <h2 class="reveal-up tw-text-3xl max-md:tw-text-xl">Basinda ve ogrenci videolarinda gorunen sistem</h2>
            <div class="reveal-up carousel-container">
                <div class="carousel tw-mt-6 tw-flex tw-w-full tw-gap-5 max-md:tw-gap-2">
                    @foreach ($pageData['press_badges'] as $badge)
                        <div class="carousel-img tw-h-[30px] tw-w-[180px] tw-text-center tw-text-sm tw-text-gray-200">{{ $badge }}</div>
                    @endforeach
                </div>
            </div>
        </section>

        <section class="tw-relative tw-flex tw-w-full tw-flex-col tw-place-content-center tw-place-items-center tw-overflow-hidden tw-p-6" id="programlar">
            <div class="tw-mt-8 tw-flex tw-flex-col tw-place-items-center tw-gap-5">
                <div class="reveal-up tw-mt-5 tw-flex tw-flex-col tw-gap-3 tw-text-center">
                    <h2 class="tw-text-4xl tw-font-medium tw-text-gray-200 max-md:tw-text-3xl">Sistemin omurgasi</h2>
                    <p class="tw-max-w-[680px] tw-text-gray-200">{{ $pageData['manifesto_lead'] }}</p>
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
                                <div class="tw-text-gray-200">{{ $point['description'] }}</div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        <section class="tw-relative tw-flex tw-min-h-[80vh] tw-w-full tw-flex-col tw-place-content-center tw-place-items-center tw-overflow-hidden tw-p-6">
            <div class="tw-mt-8 tw-flex tw-flex-col tw-place-items-center tw-gap-5">
                <div class="reveal-up tw-mt-5 tw-flex tw-flex-col tw-gap-3 tw-text-center">
                    <h2 class="tw-text-4xl tw-font-medium tw-text-gray-200 max-md:tw-text-2xl">Program icerikleri</h2>
                </div>
                <div class="tw-mt-6 tw-flex tw-max-w-[90%] tw-flex-wrap tw-place-content-center tw-gap-8 max-lg:tw-flex-col">
                    @foreach ($pageData['resource_columns'] as $column)
                        <div class="reveal-up tw-flex tw-h-[220px] tw-w-[450px] tw-gap-8 tw-rounded-xl tw-border-[1px] tw-border-outlineColor tw-bg-secondary tw-p-8 max-md:tw-w-[320px]">
                            <div class="tw-text-4xl max-md:tw-text-2xl"><i class="bi bi-check2-circle"></i></div>
                            <div class="tw-flex tw-flex-col tw-gap-4">
                                <h3 class="tw-text-2xl max-md:tw-text-xl">{{ $column['label'] }}</h3>
                                <p class="tw-text-gray-200 max-md:tw-text-sm">{{ implode(' - ', $column['items']) }}</p>
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
                            <img src="{{ $generalProgram['cover_url'] }}" alt="{{ $generalProgram['title'] ?? 'Genel Ingilizce' }}" class="tw-h-full tw-w-full tw-object-cover" />
                        @endif
                    </div>
                </div>
                <div class="tw-mt-6 tw-flex tw-max-w-[450px] tw-flex-col tw-gap-4">
                    <h3 class="tw-text-4xl tw-font-medium max-md:tw-text-2xl">{{ $generalProgram['title'] ?? 'Genel Ingilizce' }}</h3>
                    <p class="tw-text-gray-200">{{ $generalProgram['subtitle'] ?? '' }}</p>
                    <div class="tw-mt-4 tw-flex tw-flex-col tw-gap-3">
                        @foreach ($generalMilestones as $item)
                            <h4 class="tw-text-xl tw-font-medium">
                                <i class="bi bi-check-all !tw-text-2xl"></i>
                                {{ $item }}
                            </h4>
                        @endforeach
                        <span class="tw-text-lg tw-text-gray-200 max-md:tw-text-base">{{ $generalProgram['result'] ?? '' }}</span>
                        @if (!empty($generalProgram['file_url']))
                            <a href="{{ $generalProgram['file_url'] }}" target="_blank" rel="noopener" class="btn tw-mt-4">Program Detayi</a>
                        @endif
                    </div>
                </div>
            </div>
        </section>

        @if (!empty($examPrograms))
            <section class="tw-relative tw-flex tw-w-full tw-flex-col tw-place-content-center tw-place-items-center tw-overflow-hidden tw-p-6">
                <div class="tw-mt-8 tw-flex tw-flex-col tw-place-items-center tw-gap-5">
                    <div class="reveal-up tw-mt-5 tw-flex tw-flex-col tw-gap-3 tw-text-center">
                        <h2 class="tw-text-4xl tw-font-medium tw-text-gray-200 max-md:tw-text-2xl">Sinav programlari</h2>
                        <p class="tw-max-w-[700px] tw-text-gray-200">IELTS ve PTE tarafini ayri kartlarla acik gosterdim. Sonuc vaadi ve calisma basliklari burada.</p>
                    </div>
                    <div class="lfps-program-grid tw-mt-6">
                        @foreach ($examPrograms as $program)
                            <article class="lfps-program-card reveal-up">
                                @if (!empty($program['cover_url']))
                                    <div class="lfps-program-card__cover" style="background-image:url('{{ $program['cover_url'] }}')"></div>
                                @endif
                                <div class="lfps-program-card__body">
                                    <span class="lfps-program-card__label">{{ $program['label'] }}</span>
                                    <h3 class="tw-text-2xl">{{ $program['title'] }}</h3>
                                    <p class="tw-mt-3 tw-text-gray-200">{{ $program['subtitle'] }}</p>
                                    <p class="tw-mt-3 tw-text-sm tw-text-indigo-100">{{ $program['result'] }}</p>
                                    <ul>
                                        @foreach ($program['bullets'] as $bullet)
                                            <li>{{ $bullet }}</li>
                                        @endforeach
                                    </ul>
                                    @if (!empty($program['file_url']))
                                        <a href="{{ $program['file_url'] }}" target="_blank" rel="noopener" class="btn tw-mt-5">Program Detayi</a>
                                    @endif
                                </div>
                            </article>
                        @endforeach
                    </div>
                </div>
            </section>
        @endif

        <section class="tw-relative tw-flex tw-min-h-[80vh] tw-w-full tw-flex-col tw-place-content-center tw-place-items-center tw-overflow-hidden tw-p-6" id="videolar">
            <div class="tw-mt-8 tw-flex tw-flex-col tw-place-items-center tw-gap-5">
                <div class="reveal-up tw-mt-5 tw-flex tw-flex-col tw-gap-3 tw-text-center">
                    <h2 class="tw-text-4xl tw-font-medium tw-text-gray-200 max-md:tw-text-3xl">Video kanitlari</h2>
                    <p class="tw-max-w-[680px] tw-text-gray-200">{{ $pageData['proof_lead'] }}</p>
                </div>
                <div class="lfps-video-grid tw-mt-6">
                    @foreach ($mediaLibrary as $item)
                        <article class="lfps-video-card reveal-up">
                            <video controls preload="metadata" poster="{{ $item['poster_url'] ?? '' }}">
                                <source src="{{ $item['file_url'] }}" type="video/mp4">
                                Tarayiciniz video etiketini desteklemiyor.
                            </video>
                            <div class="lfps-video-card__body">
                                <span class="lfps-video-card__meta">{{ $item['category'] }} · {{ $item['duration'] }}</span>
                                <h3 class="tw-text-lg">{{ $item['title'] }}</h3>
                                <p class="tw-text-sm tw-text-gray-200">{{ $item['description'] }}</p>
                            </div>
                        </article>
                    @endforeach
                    @if (empty($mediaLibrary))
                        <div class="reveal-up tw-col-span-full tw-rounded-xl tw-border tw-border-outlineColor tw-bg-secondary tw-p-6 tw-text-center tw-text-gray-200">
                            Video kayitlari gecici olarak yuklenemedi.
                        </div>
                    @endif
                </div>
            </div>
        </section>

        <section class="tw-relative tw-flex tw-min-h-[80vh] tw-w-full tw-flex-col tw-place-content-center tw-place-items-center tw-overflow-hidden tw-p-6" id="fiyat">
            <div class="tw-mt-8 tw-flex tw-flex-col tw-place-items-center tw-gap-5">
                <div class="reveal-up tw-mt-5 tw-flex tw-flex-col tw-gap-3 tw-text-center">
                    <h2 class="tw-text-4xl tw-font-medium tw-text-gray-200 max-md:tw-text-3xl">{{ $pageData['pricing_title'] }}</h2>
                    <p class="tw-max-w-[680px] tw-text-gray-200">{{ $pageData['pricing_lead'] }}</p>
                </div>
                <div class="tw-mt-6 tw-flex tw-max-w-[90%] tw-flex-wrap tw-place-content-center tw-gap-8 max-lg:tw-flex-col">
                    @foreach ($pageData['packages'] as $package)
                        <div class="reveal-up tw-flex tw-h-[280px] tw-w-[320px] tw-flex-col tw-gap-3 tw-rounded-xl tw-border-[1px] tw-border-outlineColor tw-bg-secondary tw-p-6">
                            <strong class="tw-text-lg">{{ $package['name'] }}</strong>
                            <span class="tw-text-3xl tw-font-semibold">{{ $package['price'] }}</span>
                            <span class="tw-text-sm tw-text-gray-300">{{ $package['unit'] }}</span>
                            <p class="tw-text-sm tw-text-gray-200">{{ $package['note'] }}</p>
                            <a class="btn tw-mt-auto" href="{{ $applyUrl }}">Basvur</a>
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
                <div class="lfps-faq-list tw-mt-6">
                    @foreach ($pageData['faq'] as $faq)
                        <details class="lfps-faq-item reveal-up">
                            <summary>{{ $faq['question'] }}</summary>
                            <div>
                                <p>{{ $faq['answer'] }}</p>
                            </div>
                        </details>
                    @endforeach
                </div>
            </div>
        </section>

        <section class="tw-relative tw-flex tw-w-full tw-flex-col tw-place-content-center tw-place-items-center tw-overflow-hidden tw-p-10">
            <div class="reveal-up tw-flex tw-flex-col tw-place-items-center tw-gap-4 tw-text-center">
                <h2 class="tw-text-3xl tw-font-medium">{{ $pageData['cta_title'] }}</h2>
                <p class="tw-max-w-[600px] tw-text-gray-200">{{ $pageData['cta_text'] }}</p>
                <div class="tw-flex tw-gap-4">
                    <a class="btn" href="{{ $applyUrl }}">Programa Basvur</a>
                    <a class="btn !tw-bg-black !tw-text-white" href="{{ $testUrl }}">Seviye Tespiti</a>
                </div>
            </div>
        </section>

        <footer class="tw-flex tw-w-full tw-flex-col tw-place-items-center tw-gap-4 tw-p-8 tw-text-sm tw-text-gray-300">
            <div>{{ $siteName }} | LinguFranca Performans Sistemi</div>
            <div class="tw-flex tw-gap-4">
                <a class="footer-link" href="{{ $homeUrl }}">Ana Sayfa</a>
                <a class="footer-link" href="{{ $applyUrl }}">Iletisim</a>
                <a class="footer-link" href="{{ route('mobile-app-privacy-policy') }}">Gizlilik</a>
            </div>
        </footer>
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
    <script src="{{ asset('frontend/js/saasy-dark.js') }}"></script>
@endpush
