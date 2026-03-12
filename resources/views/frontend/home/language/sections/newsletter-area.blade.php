<section class="lang-app section-py-100">
    <div class="container">
        <div class="row align-items-center g-4">
            <div class="col-lg-6">
                <p class="eyebrow text-white">{{ __('Mobile App') }}</p>
                <h2 class="lang-app__title text-white">{{ __('Download our app and start your English journey today!') }}</h2>
                <p class="lang-app__lead text-white">{{ __('Live lessons, instructor selection, package management, and notifications at your fingertips.') }}</p>
                <ul class="lang-app__list text-white">
                    <li>{{ __('Stay connected with live lessons and notifications') }}</li>
                    <li>{{ __('Pick your favorite instructor and schedule a trial lesson instantly') }}</li>
                    <li>{{ __('Manage packages and track your progress') }}</li>
                </ul>
                <div class="lang-app__stores">
                    <a href="#" class="lang-app__store" aria-label="App Store">
                        <img src="{{ asset('frontend/img/others/apple-store.svg') }}" alt="App Store">
                    </a>
                    <a href="#" class="lang-app__store" aria-label="Google Play">
                        <img src="{{ asset('frontend/img/others/google-play.svg') }}" alt="Google Play">
                    </a>
                </div>
            </div>
            <div class="col-lg-6 text-center">
                <div class="lang-app__phones">
                    <div class="lang-app__glow"></div>
                    <img src="{{ asset($newsletterSection?->global_content?->image ?? 'frontend/img/placeholder/app.png') }}" alt="app" class="lang-app__img">
                </div>
            </div>
        </div>
    </div>
</section>

@push('styles')
<style>
    .lang-app {
        background: var(--tg-theme-primary);
        position: relative;
        overflow: hidden;
        --brand-primary: var(--tg-theme-primary);
        --brand-dark: var(--tg-common-color-dark);
        --brand-accent: var(--tg-theme-secondary);
    }

    .lang-app::after {
        content: '';
        position: absolute;
        top: -140px;
        right: -160px;
        width: 360px;
        height: 360px;
        border-radius: 50%;
        background: var(--brand-dark);
        opacity: 0.25;
    }

    .lang-app__title {
        font-weight: 900;
        font-size: 30px;
        margin-bottom: 8px;
    }

    .lang-app__lead {
        font-size: 16px;
        margin-bottom: 14px;
        max-width: 540px;
        font-weight: 700;
        color: rgba(255, 255, 255, 0.9);
    }

    .lang-app__list {
        padding-left: 0;
        list-style: none;
        margin: 0 0 18px;
        display: grid;
        gap: 8px;
        font-weight: 800;
    }

    .lang-app__list li {
        display: flex;
        align-items: center;
        gap: 10px;
        color: rgba(255, 255, 255, 0.92);
    }

    .lang-app__list li::before {
        content: '';
        width: 10px;
        height: 10px;
        border-radius: 50%;
        background: var(--brand-accent);
        flex: 0 0 auto;
    }

    .lang-app__stores {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
        align-items: center;
    }

    .lang-app__store {
        display: inline-flex;
        border-radius: 14px;
        overflow: hidden;
        box-shadow: 0 14px 34px rgba(0, 0, 0, 0.18);
        transform: translateZ(0);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        background: var(--tg-common-color-white);
    }

    .lang-app__store:hover {
        transform: translateY(-3px);
        box-shadow: 0 18px 44px rgba(0, 0, 0, 0.22);
    }

    .lang-app__store img {
        height: 46px;
        width: auto;
        display: block;
    }

    .lang-app__phones {
        position: relative;
        display: inline-block;
    }

    .lang-app__glow {
        position: absolute;
        inset: 10% 8%;
        background: rgba(255, 255, 255, 0.22);
        border-radius: 50%;
        filter: blur(34px);
        opacity: 0.7;
    }

    .lang-app__img {
        max-width: 430px;
        width: 100%;
        border-radius: 24px;
        box-shadow: 0 24px 70px rgba(0, 0, 0, 0.22);
        position: relative;
        z-index: 2;
        animation: floaty 6s ease-in-out infinite;
        background: var(--tg-common-color-white);
    }

    @keyframes floaty{0%{transform:translateY(0);}50%{transform:translateY(-8px);}100%{transform:translateY(0);}}
    @media(max-width:991px){.lang-app__title{font-size:24px;}}
    @media(max-width:575px){.lang-app{padding-top:80px;padding-bottom:80px;}}
</style>
@endpush
