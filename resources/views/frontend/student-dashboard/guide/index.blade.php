@extends('frontend.student-dashboard.layouts.master')

@section('dashboard-contents')
    @php
        $thumbs = [
            asset('frontend/img/bg/breadcrumb_bg.jpg'),
            asset('frontend/img/bg/video_bg.jpg'),
            asset('frontend/img/bg/instructor_bg.jpg'),
        ];
        $videos = collect([
            ['title' => 'How to reserve your weekly lessons', 'thumb' => $thumbs[0], 'url' => null],
            ['title' => 'How to join your upcoming class', 'thumb' => $thumbs[1], 'url' => null],
            ['title' => 'How to message your instructor', 'thumb' => $thumbs[2], 'url' => null],
            ['title' => 'How to use your cancellation rights', 'thumb' => $thumbs[0], 'url' => null],
            ['title' => 'How to follow homework and reports', 'thumb' => $thumbs[1], 'url' => null],
            ['title' => 'How to use the student library', 'thumb' => $thumbs[2], 'url' => null],
            ['title' => 'How to update your profile settings', 'thumb' => $thumbs[0], 'url' => null],
            ['title' => 'How to contact support quickly', 'thumb' => $thumbs[1], 'url' => null],
        ]);
    @endphp

    <div class="sp-guide">
        <h2 class="sp-page-title">{{ __('User Guide') }}</h2>
        <p class="sp-page-subtitle">{{ __('Watch the user guide videos below to better understand the system and quickly find answers to your questions.') }}</p>

        <div class="sp-guide__wrap">
            <div class="swiper sp-guide-swiper">
                <div class="swiper-wrapper">
                    @foreach ($videos as $video)
                        <div class="swiper-slide">
                            <div class="sp-guide-card">
                                <div class="sp-guide-card__thumb" style="background-image:url('{{ $video['thumb'] }}')">
                                    <div class="sp-guide-card__play" aria-hidden="true">
                                        <i class="fas fa-play"></i>
                                    </div>
                                </div>
                                <div class="sp-guide-card__title">{{ __($video['title']) }}</div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <button type="button" class="sp-guide__nav sp-guide__prev" aria-label="Previous">
                <i class="fas fa-chevron-left"></i>
            </button>
            <button type="button" class="sp-guide__nav sp-guide__next" aria-label="Next">
                <i class="fas fa-chevron-right"></i>
            </button>
        </div>

        <div class="sp-guide__pagination"></div>
    </div>
@endsection

@push('styles')
    <style>
        .sp-page-title{margin:6px 0 8px;text-align:center;font-weight:1000;color:var(--student-brand);}
        .sp-page-subtitle{margin:0 auto 18px;text-align:center;max-width:820px;color:#111827;font-weight:900;}

        .sp-guide__wrap{position:relative;background:rgba(246,161,5,.45);border-radius:18px;padding:18px 54px;box-shadow:0 14px 32px rgba(0,0,0,0.05);}
        .sp-guide-card{background:#fff;border-radius:16px;overflow:hidden;box-shadow:0 10px 24px rgba(0,0,0,0.06);border:1px solid rgba(17,24,39,.06);}
        .sp-guide-card__thumb{height:140px;background-size:cover;background-position:center;position:relative;}
        .sp-guide-card__thumb::after{content:"";position:absolute;inset:0;background:linear-gradient(to bottom, rgba(0,0,0,.08), rgba(0,0,0,.45));}
        .sp-guide-card__play{position:absolute;left:50%;top:50%;transform:translate(-50%,-50%);width:54px;height:54px;border-radius:50%;background:rgba(0,0,0,.55);display:grid;place-items:center;z-index:2;color:#fff;border:2px solid rgba(255,255,255,.6);}
        .sp-guide-card__title{padding:12px 12px 14px;font-weight:1000;color:#111827;min-height:54px;display:flex;align-items:center;}

        .sp-guide__nav{position:absolute;top:50%;transform:translateY(-50%);width:40px;height:40px;border-radius:50%;border:0;background:var(--student-brand);color:#111827;display:grid;place-items:center;box-shadow:0 14px 28px rgba(0,0,0,0.12);}
        .sp-guide__prev{left:10px;}
        .sp-guide__next{right:10px;}
        .sp-guide__nav:hover{opacity:.92;}

        .sp-guide__pagination{margin-top:12px;text-align:center;font-weight:1000;color:var(--student-brand);}

        @media(max-width:575.98px){
            .sp-guide__wrap{padding:14px 44px;}
            .sp-guide-card__thumb{height:160px;}
        }
    </style>
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            if (typeof Swiper === 'undefined') return;
            new Swiper('.sp-guide-swiper', {
                slidesPerView: 4,
                spaceBetween: 14,
                navigation: {
                    nextEl: '.sp-guide__next',
                    prevEl: '.sp-guide__prev',
                },
                pagination: {
                    el: '.sp-guide__pagination',
                    type: 'fraction',
                },
                breakpoints: {
                    0: { slidesPerView: 1 },
                    576: { slidesPerView: 2 },
                    992: { slidesPerView: 4 },
                },
            });
        });
    </script>
@endpush
