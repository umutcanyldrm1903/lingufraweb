<!doctype html>
<html class="no-js" lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    @php
        $siteName = $setting->app_name ?? config('app.name');
        $metaTitle = trim($__env->yieldContent('meta_title', $siteName));
        $rawMetaDescription = trim($__env->yieldContent('meta_description', ''));
        $metaDescription = $rawMetaDescription !== ''
            ? $rawMetaDescription
            : sprintf(
                '%s ile online dil egitimi, canli dersler, kurumsal cozumler ve uzman egitmenlerle gelisim programlarini kesfedin.',
                $siteName
            );
        $metaKeywords = trim($__env->yieldContent('meta_keywords', ''));
        $metaRobots = trim($__env->yieldContent('meta_robots', 'index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1'));
        $canonicalUrl = trim($__env->yieldContent('canonical_url', url()->current())) ?: url()->current();
        $metaImagePath = trim($__env->yieldContent('meta_image', ''));
        $defaultMetaImagePath = trim((string) ($setting->logo ?? $setting->favicon ?? ''));
        $resolvedMetaImagePath = str_replace('\\', '/', $metaImagePath !== '' ? $metaImagePath : $defaultMetaImagePath);
        $metaImage = null;

        if ($resolvedMetaImagePath !== '') {
            $metaImage = \Illuminate\Support\Str::startsWith($resolvedMetaImagePath, ['http://', 'https://', '//', 'data:'])
                ? $resolvedMetaImagePath
                : asset($resolvedMetaImagePath);
        }

        $hrefLang = app()->getLocale() === 'tr' ? 'tr-TR' : str_replace('_', '-', app()->getLocale());
        $organizationSchema = array_filter([
            '@context' => 'https://schema.org',
            '@type' => 'EducationalOrganization',
            'name' => $siteName,
            'url' => url('/'),
            'logo' => $metaImage,
            'image' => $metaImage,
        ]);
        $websiteSchema = [
            '@context' => 'https://schema.org',
            '@type' => 'WebSite',
            'name' => $siteName,
            'url' => url('/'),
            'potentialAction' => [
                '@type' => 'SearchAction',
                'target' => route('courses') . '?search={search_term_string}',
                'query-input' => 'required name=search_term_string',
            ],
        ];
    @endphp
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>{{ $metaTitle }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="{{ $metaDescription }}">
    @if ($metaKeywords !== '')
        <meta name="keywords" content="{{ $metaKeywords }}">
    @endif
    <meta name="robots" content="{{ $metaRobots }}">
    <meta name="author" content="{{ $siteName }}">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="canonical" href="{{ $canonicalUrl }}">
    <link rel="alternate" href="{{ $canonicalUrl }}" hreflang="{{ $hrefLang }}">
    <link rel="alternate" href="{{ $canonicalUrl }}" hreflang="x-default">
    <meta property="og:type" content="@yield('og_type', 'website')">
    <meta property="og:title" content="{{ $metaTitle }}">
    <meta property="og:description" content="{{ $metaDescription }}">
    <meta property="og:url" content="{{ $canonicalUrl }}">
    <meta property="og:site_name" content="{{ $siteName }}">
    @if ($metaImage)
        <meta property="og:image" content="{{ $metaImage }}">
        <meta name="twitter:image" content="{{ $metaImage }}">
    @endif
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $metaTitle }}">
    <meta name="twitter:description" content="{{ $metaDescription }}">

    <!-- Custom Meta -->
    @stack('custom_meta')
    <script type="application/ld+json">{!! json_encode($organizationSchema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}</script>
    <script type="application/ld+json">{!! json_encode($websiteSchema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}</script>
    @stack('structured_data')
    <!-- Favicon -->
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset($setting->favicon) }}">
    <!-- CSS here -->
    @include('frontend.layouts.styles')
    <!-- CustomCSS here -->
    @stack('styles')
    @if (customCode()?->css)
        <style>
            {!! customCode()->css !!}
        </style>
    @endif

    {{-- dynamic header scripts --}}
    @include('frontend.layouts.header-scripts')

    @php
        setEnrollmentIdsInSession();
        setInstructorCourseIdsInSession();
        $theme_name = session()->has('demo_theme') ? session()->get('demo_theme') : DEFAULT_HOMEPAGE;
    @endphp
</head>
<body class="{{ isRoute('home', "home_{$theme_name}") }}">
    @if ($setting->google_tagmanager_status == 'active')
        <!-- Google Tag Manager (noscript) -->
        <noscript><iframe src="https://www.googletagmanager.com/ns.html?id={{ $setting->google_tagmanager_id }}"
                height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
        <!-- End Google Tag Manager (noscript) -->
    @endif

    @if ($setting->preloader_status == 1 && !request()->is('student/*') && !request()->is('instructor/*'))
        <!--Preloader-->
        <div id="preloader">
            <div id="loader" class="loader">
                <div class="loader-container">
                    <div class="loader-icon"><img src="{{ asset($setting->preloader) }}" alt="Preloader">
                    </div>
                </div>
            </div>
        </div>
        <!--Preloader-end -->
    @endif

    <!-- Scroll-top -->
    <button class="scroll__top scroll-to-target" data-target="html" aria-label="Scroll Top">
        <i class="tg-flaticon-arrowhead-up"></i>
    </button>
    <!-- Scroll-top-end-->

    <!-- header-area -->
    @if (trim($__env->yieldContent('hide_public_header')) === '')
        @include('frontend.layouts.header')
    @endif
    <!-- header-area-end -->

    <!-- main-area -->
    <main class="main-area fix">
        @yield('contents')
    </main>
    <!-- main-area-end -->

    <!-- modal-area -->
    @include('frontend.partials.modal')
    @include('frontend.instructor-dashboard.course.partials.add-new-section-modal')
    <!-- modal-area -->

    <!-- footer-area -->
    @if (trim($__env->yieldContent('hide_public_footer')) === '')
        @include('frontend.layouts.footer')
    @endif
    <!-- footer-area-end -->


    <!-- JS here -->
    @include('frontend.layouts.scripts')

    <!-- Language Translation Variables -->
    @include('global.dynamic-js-variables')

    <!-- Page specific js -->
    @if (session('registerUser') && $setting->google_tagmanager_status == 'active' && $marketing_setting?->register)
        @php
            $registerUser = session('registerUser');
            session()->forget('registerUser');
        @endphp
        <script>
            $(function() {
                dataLayer.push({
                    'event': 'newStudent',
                    'student_info': @json($registerUser)
                });
            });
        </script>
    @endif
    @stack('scripts')
    @if (customCode()?->javascript)
        <script>
            "use strict";
            {!! customCode()->javascript !!}
        </script>
    @endif
</body>

</html>
