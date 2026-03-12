@php
    $siteName = Cache::get('setting')?->site_name ?? 'Lingu Franca';
    $mainImage = asset($aboutSection?->global_content?->image ?? 'frontend/img/others/about_img.png');
    $secondImage = asset($aboutSection?->global_content?->image_two ?? $mainImage);
    $thirdImage = asset($aboutSection?->global_content?->image_three ?? $secondImage);
    $fourthImage = asset($aboutSection?->global_content?->image_four ?? $thirdImage);
@endphp

@extends('frontend.layouts.master')

@section('meta_title', $seo_setting['about_page']['seo_title'])
@section('meta_description', $seo_setting['about_page']['seo_description'])

@section('contents')
    <section class="cowboy-about-full">
        <div class="cowboy-about-full__shapes">
            <span class="c-dot c-dot--1"></span>
            <span class="c-dot c-dot--2"></span>
            <span class="c-dot c-dot--3"></span>
            <span class="c-dot c-dot--4"></span>
            <span class="c-path c-path--1"></span>
            <span class="c-path c-path--2"></span>
        </div>
        <div class="container">
            <div class="row gy-5 align-items-center">
                <div class="col-lg-4">
                    <h2 class="c-title">{{ __('Who are we?') }}</h2>
                    <p class="c-text">
                        {{ __(':siteName is one of the leading one-on-one English learning platforms in Turkey. We provide international quality at accessible prices and match each learner with Turkish or foreign instructors for a safe learning experience.', ['siteName' => $siteName]) }}
                    </p>
                </div>
                <div class="col-lg-4">
                    <div class="c-stack c-stack--center">
                        <img src="{{ $mainImage }}" class="c-photo c-photo--main" alt="about">
                        <img src="{{ $secondImage }}" class="c-photo c-photo--mini" alt="about">
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="c-block">
                        <h3>{{ __('Why :siteName?', ['siteName' => $siteName]) }}</h3>
                        <h4>{{ __('What sets us apart') }}</h4>
                        <h5>{{ __('Curated Instructor List') }}</h5>
                        <p>{{ __('We select instructors carefully and present them based on experience and profiles. Choose the instructor you want and get started with a single click.') }}</p>
                        <h5>{{ __('Reliable Infrastructure') }}</h5>
                        <p>{{ __('We deliver lessons on a robust web infrastructure and cloud systems, protecting your account with reliable payment solutions.') }}</p>
                    </div>
                </div>
            </div>

            <div class="row gy-5 align-items-center mt-2">
                <div class="col-lg-4">
                    <div class="c-block c-block--left">
                        <h3>{{ __('Affordable Pricing') }}</h3>
                        <p>{{ __('We offer transparent pricing tailored to local conditions. By processing in Turkish Lira, we reduce high foreign currency costs.') }}</p>
                        <h5>{{ __('A Learning Platform') }}</h5>
                        <p>{{ __('From beginner to advanced, our simple interface and materials support every step of the learning journey.') }}</p>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="c-stack c-stack--vertical">
                        <img src="{{ $thirdImage }}" class="c-photo c-photo--main" alt="about">
                        <img src="{{ $fourthImage }}" class="c-photo c-photo--mini" alt="about">
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="c-block c-block--right">
                        <h3>{{ __('Turkish Support') }}</h3>
                        <p>{{ __('For students who prefer Turkish language support, we offer the option to work with Turkish-speaking instructors.') }}</p>
                        <h5>{{ __('Continuous and Interactive Learning') }}</h5>
                        <p>{{ __('With uninterrupted lessons, we build strong communication and learning bonds between instructors and students.') }}</p>
                    </div>
                </div>
            </div>

            <div class="c-cta">
                <h3>{{ __('Start today!') }}</h3>
                <div class="c-cta__btns">
                    <a href="{{ route('login') }}" class="btn c-btn c-btn--ghost">{{ __('Login') }}</a>
                    <a href="{{ route('register') }}" class="btn c-btn c-btn--solid">{{ __('Free Trial') }}</a>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('styles')
<style>
    :root{
        --brand-primary:#0e5c93;
        --brand-dark:#0b3f6c;
        --brand-accent:#f36f25;
    }
    .cowboy-about-full{position:relative;padding:80px 0 60px;background:var(--brand-primary);color:#fff;overflow:hidden;}
    .cowboy-about-full__shapes .c-dot{position:absolute;border-radius:50%;opacity:0.45;}
    .c-dot--1{width:24px;height:24px;top:12%;left:6%;background:var(--brand-accent);}
    .c-dot--2{width:16px;height:16px;top:42%;right:12%;background:#ffd7b4;}
    .c-dot--3{width:18px;height:18px;bottom:18%;left:14%;background:var(--brand-accent);}
    .c-dot--4{width:14px;height:14px;bottom:10%;right:22%;background:#ffd7b4;}
    .c-path{position:absolute;border:2px dashed rgba(255,255,255,0.35);border-radius:50%;}
    .c-path--1{width:320px;height:320px;right:6%;top:34%;}
    .c-path--2{width:260px;height:260px;left:8%;bottom:6%;}

    .c-title{font-weight:900;font-size:32px;margin-bottom:10px;}
    .c-text{font-weight:600;color:#e8f1fb;line-height:1.6;}
    .c-block{color:#fff;line-height:1.6;}
    .c-block h3{font-weight:900;font-size:26px;margin-bottom:8px;}
    .c-block h4{font-weight:900;font-size:24px;margin:6px 0;}
    .c-block h5{font-weight:800;font-size:20px;margin:10px 0 4px;}
    .c-block p{color:#e8f1fb;font-weight:600;margin-bottom:8px;}
    .c-stack{position:relative;display:flex;flex-direction:column;align-items:center;gap:14px;}
    .c-stack--center .c-photo--mini{position:absolute;bottom:-30px;left:-30px;}
    .c-stack--vertical .c-photo--mini{position:absolute;bottom:-26px;right:-30px;}
    .c-photo{border-radius:16px;overflow:hidden;box-shadow:0 20px 44px rgba(0,0,0,0.2);border:6px solid rgba(255,255,255,0.18);}
    .c-photo--main{max-width:360px;width:100%;}
    .c-photo--mini{width:140px;}
    .c-cta{text-align:center;margin-top:60px;}
    .c-cta h3{font-weight:900;font-size:28px;margin-bottom:16px;}
    .c-cta__btns{display:flex;justify-content:center;gap:12px;flex-wrap:wrap;}
    .c-btn{border-radius:14px;padding:12px 18px;font-weight:800;}
    .c-btn--solid{background:var(--brand-accent);border:1px solid var(--brand-accent);color:#1c1c1c;}
    .c-btn--ghost{background:#fff;border:1px solid #fff;color:var(--brand-primary);}
    .c-btn:hover{opacity:0.92;}
    @media(max-width:991px){
        .cowboy-about-full{padding:70px 0 50px;}
        .c-title{font-size:28px;}
        .c-block h3{font-size:24px;}
    }
    @media(max-width:575px){
        .c-stack--center .c-photo--mini,
        .c-stack--vertical .c-photo--mini{position:relative;left:auto;right:auto;bottom:auto;margin-top:10px;}
        .c-stack{align-items:flex-start;}
        .c-photo--mini{width:120px;}
    }
</style>
@endpush
