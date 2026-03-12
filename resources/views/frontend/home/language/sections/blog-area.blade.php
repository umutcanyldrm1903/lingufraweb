@php
    $socialLinks = getSocialLinks();
    $instagramUrl = $socialLinks->first(fn($link) => str($link?->link)->contains('instagram'))?->link;
    $instagramUrl = $instagramUrl ?: $socialLinks->first(fn($link) => str($link?->icon)->contains('instagram'))?->link;

    $instaCards = ($selectedInstructors ?? collect())->take(8);
@endphp

<section class="lang-insta section-py-120" id="instagram">
    <div class="container">
        {{-- Only add the Cowboy-like top row; keep the rest (colors/layout) intact --}}
        <div class="row g-4 align-items-center lang-insta__cta-row">
            <div class="col-lg-4">
                <h2 class="lang-insta__cta-title">{{ __('Visit us on Instagram') }}<br>{{ __('Meet the team') }}</h2>
                <a href="{{ $instagramUrl ?: 'javascript:;' }}"
                    class="lang-insta__cta-btn {{ $instagramUrl ? '' : 'is-disabled' }}" @if ($instagramUrl) target="_blank"
                        rel="noopener" @endif>
                    {{ __('Visit') }}
                </a>
            </div>
            <div class="col-lg-5">
                <div class="lang-insta__cta-note">
                    <span class="lang-insta__cta-arrow" aria-hidden="true">
                        <svg viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M10 44c10-16 24-18 44-22" stroke="currentColor" stroke-width="4"
                                stroke-linecap="round" />
                            <path d="M45 18l9 4-6 8" stroke="currentColor" stroke-width="4" stroke-linecap="round"
                                stroke-linejoin="round" />
                        </svg>
                    </span>
                    <p class="lang-insta__cta-text">
                        {{ __('Meet our team on Instagram. Follow daily English posts to learn new words, tips, and phrases. Improve your English in a fun way!') }}
                    </p>
                </div>
            </div>
            <div class="col-lg-3 d-none d-lg-flex justify-content-end">
                <div class="lang-insta__cta-illustration" aria-hidden="true">
                    <img src="{{ asset('frontend/img/others/student_grp.png') }}" alt="">
                </div>
            </div>
        </div>

        <div class="lang-insta__bar"><span class="line"></span><span class="dot"></span></div>

        <div class="row g-3">
            @foreach ($instaCards as $instructor)
                <div class="col-lg-3 col-md-4 col-6">
                    <a href="{{ route('instructor-details', ['id' => $instructor->id, 'slug' => Str::slug($instructor->name)]) }}"
                        class="lang-insta__card">
                        <div class="lang-insta__img">
                            <img src="{{ asset($instructor->image) }}" alt="{{ $instructor->name }}">
                            <span class="lang-insta__tag">{{ __('Online Lesson') }}</span>
                            <span class="lang-insta__play"><i class="fas fa-play"></i></span>
                            <span class="lang-insta__ig"><i class="fab fa-instagram"></i></span>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>
    </div>
</section>

@push('styles')
    <style>
        :root {
            --brand-primary: var(--tg-theme-primary);
            --brand-dark: var(--tg-common-color-dark);
            --brand-accent: var(--tg-theme-secondary);
        }

        .lang-insta {
            background: var(--brand-primary);
            overflow: hidden;
            position: relative;
        }

        .lang-insta::after {
            content: '';
            position: absolute;
            top: -80px;
            right: -120px;
            width: 260px;
            height: 260px;
            border-radius: 50%;
            background: var(--brand-dark);
            opacity: 0.2;
        }

        /* Cowboy-like top row (added) */
        .lang-insta__cta-row {
            position: relative;
            z-index: 2;
            margin-bottom: 12px;
        }

        .lang-insta__cta-title {
            font-weight: 1000;
            font-size: 44px;
            line-height: 1.05;
            color: #fff;
            margin: 0;
        }

        .lang-insta__cta-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 999px;
            padding: 14px 26px;
            border: 2px solid rgba(255, 255, 255, 0.95);
            color: #fff;
            text-decoration: none;
            font-weight: 1000;
            text-transform: uppercase;
            font-size: 12px;
            letter-spacing: .4px;
            margin-top: 18px;
            background: transparent;
            transition: background .2s ease, color .2s ease, transform .2s ease;
        }

        .lang-insta__cta-btn:hover {
            background: #fff;
            color: var(--brand-primary);
            transform: translateY(-2px);
        }

        .lang-insta__cta-note {
            display: flex;
            gap: 16px;
            align-items: flex-start;
        }

        .lang-insta__cta-arrow {
            flex: 0 0 auto;
            color: rgba(255, 255, 255, 0.95);
        }

        .lang-insta__cta-arrow svg {
            width: 54px;
            height: 54px;
        }

        .lang-insta__cta-text {
            margin: 0;
            color: #fff;
            font-weight: 900;
            line-height: 1.55;
            max-width: 560px;
        }

        .lang-insta__cta-illustration {
            width: 240px;
            max-width: 100%;
            filter: drop-shadow(0 18px 34px rgba(0, 0, 0, 0.25));
        }

        .lang-insta__cta-illustration img {
            width: 100%;
            height: auto;
        }

        .lang-insta__bar {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
            margin: 18px 0;
            position: relative;
            z-index: 2;
        }

        .lang-insta__bar .line {
            display: inline-block;
            width: 70px;
            height: 4px;
            background: #fff;
            border-radius: 6px;
        }

        .lang-insta__bar .dot {
            display: inline-block;
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: var(--brand-accent);
        }

        .lang-insta__card {
            position: relative;
            overflow: hidden;
            border-radius: 16px;
            box-shadow: 0 12px 34px rgba(0, 0, 0, 0.18);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            border: 3px solid #fff;
            display: block;
        }

        .lang-insta__card:hover {
            transform: translateY(-6px) scale(1.02);
            box-shadow: 0 18px 44px rgba(0, 0, 0, 0.22);
        }

        .lang-insta__img {
            position: relative;
        }

        .lang-insta__img img {
            width: 100%;
            height: 160px;
            object-fit: cover;
            display: block;
            transition: transform 0.3s ease;
        }

        .lang-insta__card:hover img {
            transform: scale(1.06);
        }

        .lang-insta__tag {
            position: absolute;
            bottom: 10px;
            left: 10px;
            background: #fff;
            color: #1c1c1c;
            font-weight: 900;
            font-size: 12px;
            padding: 6px 10px;
            border-radius: 10px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.18);
            letter-spacing: 0.3px;
            text-transform: uppercase;
            z-index: 3;
        }

        .lang-insta__play {
            position: absolute;
            inset: 0;
            display: grid;
            place-items: center;
            background: linear-gradient(180deg, rgba(0, 0, 0, 0.05), rgba(0, 0, 0, 0.35));
            z-index: 2;
        }

        .lang-insta__play i {
            width: 54px;
            height: 54px;
            border-radius: 50%;
            background: var(--brand-accent);
            display: grid;
            place-items: center;
            color: var(--tg-common-color-black-3);
            box-shadow: 0 18px 40px rgba(0, 0, 0, 0.25);
        }

        .lang-insta__ig {
            position: absolute;
            top: 10px;
            right: 10px;
            width: 34px;
            height: 34px;
            border-radius: 10px;
            background: #fff;
            display: grid;
            place-items: center;
            color: var(--brand-primary);
            box-shadow: 0 10px 24px rgba(0, 0, 0, 0.18);
            z-index: 3;
        }

        .lang-insta .is-disabled {
            opacity: 0.55;
            pointer-events: none;
        }

        @media (max-width: 991px) {
            .lang-insta__cta-title {
                font-size: 34px;
            }
        }

        @media (max-width: 575px) {
            .lang-insta__cta-title {
                font-size: 30px;
            }

            .lang-insta__cta-arrow svg {
                width: 44px;
                height: 44px;
            }
        }
    </style>
@endpush
