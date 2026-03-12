@php
    $resolveAsset = function ($path) {
        if (!$path || !is_string($path)) {
            return null;
        }
        $path = str_replace('\\', '/', $path);
        if (
            str_starts_with($path, 'http://') ||
            str_starts_with($path, 'https://') ||
            str_starts_with($path, '//') ||
            str_starts_with($path, 'data:')
        ) {
            return $path;
        }
        return asset($path);
    };

    $corporateImage = $resolveAsset(data_get($aboutSection, 'global_content.image')) ?: asset('frontend/img/others/about_img.png');
    $instructorImage = $resolveAsset(data_get($aboutSection, 'global_content.image_two')) ?: asset('frontend/img/instructor/instructor_details_thumb.png');
@endphp

<section class="lang-corporate">
    <div class="container">
        <div class="row align-items-center g-4">
            <div class="col-lg-6">
                <div class="lang-corporate__copy">
                    <p class="lang-corporate__kicker">{{ __('Corporate Training') }}</p>
                    <h2 class="lang-corporate__title">{{ __('Corporate language training for companies') }}</h2>
                    <p class="lang-corporate__lead">
                        {{ __('Improve your team\'s skills with online English training programs. Let your company cover the training costs and move forward with a flexible, reportable system.') }}
                    </p>
                    <div class="lang-corporate__actions">
                        <a href="{{ route('login') }}" class="lang-btn lang-btn--primary">
                            {{ __('Free trial lesson') }}
                        </a>
                        <a href="{{ route('corporate.index') }}" class="lang-btn lang-btn--outline">
                            {{ __('Submit your company') }}
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="lang-corporate__media" aria-hidden="true">
                    <div class="lang-corporate__media-bg"></div>
                    <img src="{{ $corporateImage }}" alt="corporate" class="lang-corporate__img" loading="lazy">
                </div>
            </div>
        </div>
    </div>
</section>

<section class="lang-instructor-cta">
    <div class="container">
        <div class="row align-items-center g-4">
            <div class="col-lg-6">
                <div class="lang-instructor-cta__illustration">
                    <div class="lang-instructor-cta__blob" aria-hidden="true"></div>
                    <img src="{{ $instructorImage }}" alt="instructor" class="lang-instructor-cta__img" loading="lazy">
                </div>
            </div>
            <div class="col-lg-6">
                <p class="lang-instructor-cta__kicker">{{ __('Become an Instructor') }}</p>
                <h2 class="lang-instructor-cta__title">{{ __('Become an Instructor') }}</h2>
                <p class="lang-instructor-cta__lead">
                    {{ __('Join our platform as an instructor, teach online, and manage your time freely. Track your journey with a modern panel and earning opportunities.') }}
                </p>
                <div class="lang-instructor-cta__list">
                    <div class="lang-instructor-cta__item"><span class="dot"></span>{{ __('Save time') }}</div>
                    <div class="lang-instructor-cta__item"><span class="dot"></span>{{ __('Location freedom') }}</div>
                    <div class="lang-instructor-cta__item"><span class="dot"></span>{{ __('Earning opportunity') }}</div>
                    <div class="lang-instructor-cta__item"><span class="dot"></span>{{ __('Modern interface') }}</div>
                </div>
                <a href="{{ route('become-instructor') }}" class="lang-btn lang-btn--primary lang-instructor-cta__btn">
                    {{ __('Become an Instructor') }}
                </a>
            </div>
        </div>
    </div>
</section>

@push('styles')
<style>
    .lang-corporate {
        background: var(--tg-common-color-gray-2);
        padding: 110px 0 86px;
        position: relative;
        overflow: hidden;
        --brand-primary: var(--tg-theme-primary);
        --brand-accent: var(--tg-theme-secondary);
        --brand-dark: var(--tg-common-color-dark);
    }

    .lang-corporate::before,
    .lang-corporate::after,
    .lang-instructor-cta::before,
    .lang-instructor-cta::after {
        content: '';
        position: absolute;
        width: 420px;
        height: 420px;
        border-radius: 50%;
        opacity: 0.10;
        pointer-events: none;
    }

    .lang-corporate::before {
        top: -200px;
        right: -210px;
        background: var(--brand-primary);
        opacity: 0.10;
    }

    .lang-corporate::after {
        bottom: -210px;
        left: -210px;
        background: var(--brand-accent);
        opacity: 0.10;
    }

    .lang-corporate__kicker {
        margin: 0 0 8px;
        font-weight: 1000;
        letter-spacing: 0.8px;
        color: var(--brand-primary);
        text-transform: uppercase;
        font-size: 12px;
    }

    .lang-corporate__title {
        font-weight: 1000;
        font-size: 40px;
        line-height: 1.06;
        color: var(--tg-heading-color);
        margin-bottom: 10px;
    }

    .lang-corporate__lead {
        color: var(--tg-body-color);
        margin-bottom: 16px;
        font-size: 16px;
        font-weight: 700;
    }

    .lang-corporate__actions {
        display: flex;
        gap: 14px;
        flex-wrap: wrap;
        align-items: center;
    }

    .lang-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 14px;
        padding: 12px 18px;
        font-weight: 1000;
        text-decoration: none;
        border: 2px solid transparent;
        line-height: 1;
        transition: transform .18s ease, box-shadow .18s ease, background .18s ease, color .18s ease,
            border-color .18s ease;
        white-space: nowrap;
    }

    .lang-btn--primary {
        background: var(--brand-accent);
        border-color: var(--brand-accent);
        color: var(--tg-common-color-black-3);
        box-shadow: 0 16px 36px rgba(0, 0, 0, 0.16);
    }

    .lang-btn--primary:hover {
        transform: translateY(-2px);
        background: var(--brand-primary);
        border-color: var(--brand-primary);
        color: var(--tg-common-color-white);
        box-shadow: 0 20px 46px rgba(0, 0, 0, 0.18);
    }

    .lang-btn--outline {
        background: rgba(255, 255, 255, 0.0);
        border-color: var(--brand-primary);
        color: var(--brand-primary);
    }

    .lang-btn--outline:hover {
        transform: translateY(-2px);
        background: rgba(14, 92, 147, 0.10);
        border-color: var(--brand-primary);
        color: var(--brand-primary);
    }

    .lang-corporate__media {
        position: relative;
        display: grid;
        place-items: center;
        z-index: 2;
    }

    .lang-corporate__media-bg {
        position: absolute;
        width: min(520px, 92%);
        aspect-ratio: 1 / 1;
        border-radius: 50%;
        background: radial-gradient(circle at 30% 30%, rgba(246, 161, 5, 0.22), transparent 60%),
            radial-gradient(circle at 65% 55%, rgba(14, 92, 147, 0.16), transparent 60%),
            rgba(255, 255, 255, 0.65);
        border: 1px solid rgba(14, 92, 147, 0.12);
        filter: blur(0);
    }

    .lang-corporate__img {
        width: min(560px, 100%);
        position: relative;
        z-index: 2;
        filter: drop-shadow(0 24px 60px rgba(0, 0, 0, 0.10));
    }

    .lang-instructor-cta {
        background: var(--tg-common-color-white);
        padding: 86px 0 110px;
        position: relative;
        overflow: hidden;
        --brand-primary: var(--tg-theme-primary);
        --brand-accent: var(--tg-theme-secondary);
    }

    .lang-instructor-cta::before {
        content: '';
        position: absolute;
        top: -210px;
        left: -210px;
        background: var(--brand-accent);
        border-radius: 50%;
        opacity: 0.14;
    }

    .lang-instructor-cta::after {
        content: '';
        position: absolute;
        bottom: -210px;
        right: -210px;
        background: var(--brand-primary);
        border-radius: 50%;
        opacity: 0.10;
    }

    .lang-instructor-cta__kicker {
        margin: 0 0 8px;
        font-weight: 1000;
        letter-spacing: 0.8px;
        color: var(--brand-primary);
        text-transform: uppercase;
        font-size: 12px;
    }

    .lang-instructor-cta__title {
        font-weight: 1000;
        font-size: 40px;
        line-height: 1.06;
        color: var(--tg-heading-color);
        margin-bottom: 10px;
    }

    .lang-instructor-cta__lead {
        color: var(--tg-body-color);
        margin-bottom: 16px;
        font-size: 16px;
        font-weight: 700;
    }

    .lang-instructor-cta__list {
        display: grid;
        gap: 10px;
        margin-bottom: 18px;
    }

    .lang-instructor-cta__item {
        display: flex;
        align-items: center;
        gap: 10px;
        font-weight: 800;
        color: var(--tg-common-color-black-2);
    }

    .lang-instructor-cta__item .dot {
        width: 12px;
        height: 12px;
        border-radius: 50%;
        background: var(--brand-primary);
        display: inline-block;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.12);
    }

    .lang-instructor-cta__btn {
        margin-top: 6px;
    }

    .lang-instructor-cta__illustration {
        text-align: center;
        position: relative;
        z-index: 2;
    }

    .lang-instructor-cta__blob {
        position: absolute;
        width: min(520px, 92%);
        aspect-ratio: 1 / 1;
        border-radius: 50%;
        background: var(--brand-accent);
        opacity: 0.92;
        left: 50%;
        transform: translateX(-50%);
        top: 10%;
        filter: drop-shadow(0 22px 60px rgba(0, 0, 0, 0.14));
    }

    .lang-instructor-cta__img {
        width: 100%;
        max-width: 560px;
        position: relative;
        z-index: 2;
        filter: drop-shadow(0 24px 60px rgba(0, 0, 0, 0.14));
    }

    @media (max-width: 991px) {
        .lang-corporate,
        .lang-instructor-cta {
            padding: 75px 0;
        }

        .lang-corporate__title,
        .lang-instructor-cta__title {
            font-size: 30px;
        }
    }
</style>
@endpush
