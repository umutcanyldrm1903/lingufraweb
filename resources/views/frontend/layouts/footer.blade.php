@php
    $footerSetting = \Modules\FooterSetting\app\Models\FooterSetting::first();
    $nav_menu = menu_get_by_slug('nav-menu');
    $footer_menu_three = menu_get_by_slug('footer-col-three');

    $footerLogoPath = !empty($footerSetting?->logo)
        ? $footerSetting->logo
        : ($setting?->logo ?? null);
    $footerLogoPath = is_string($footerLogoPath) ? str_replace('\\', '/', $footerLogoPath) : $footerLogoPath;
    $footerAppName = $setting?->app_name ?? config('app.name');
    $footerLogoUrl = null;
    if ($footerLogoPath) {
        $footerLogoUrl =
            str_starts_with($footerLogoPath, 'http://') ||
            str_starts_with($footerLogoPath, 'https://') ||
            str_starts_with($footerLogoPath, '//') ||
            str_starts_with($footerLogoPath, 'data:')
                ? $footerLogoPath
                : asset($footerLogoPath);
    }
@endphp

<footer class="lf-footer" aria-label="Footer">
    <div class="container">
        <div class="lf-footer__grid">
            <div class="lf-footer__brand">
                <a class="lf-footer__logo" href="{{ route('home') }}">
                    @if ($footerLogoUrl)
                        <img src="{{ $footerLogoUrl }}" alt="{{ $footerAppName }}" loading="lazy"
                            onerror="this.style.display='none'; if(this.nextElementSibling){this.nextElementSibling.style.display='inline-flex';}">
                        <span class="lf-footer__logo-text lf-footer__logo-text--fallback"
                            style="display:none;">{{ $footerAppName }}</span>
                    @else
                        <span class="lf-footer__logo-text">{{ $footerAppName }}</span>
                    @endif
                </a>
                @if ($footerSetting?->footer_text)
                    <p class="lf-footer__text">{{ $footerSetting?->footer_text }}</p>
                @endif
                <div class="lf-footer__seo">
                    <h6 class="lf-footer__title">{{ __('Ogrenme Rotalari') }}</h6>
                    <div class="lf-footer__seo-links">
                        <a href="{{ route('lingufranca-performance') }}">{{ __('LinguFranca Performans Sistemi') }}</a>
                        <a href="{{ route('english-private-lessons') }}">{{ __('Ingilizce Ozel Ders') }}</a>
                        <a href="{{ route('english-private-lessons.online') }}">{{ __('Online Ingilizce Ozel Ders') }}</a>
                        <a href="{{ route('english-private-lessons.speaking') }}">{{ __('Ingilizce Konusma Dersi') }}</a>
                        <a href="{{ route('english-private-lessons.business') }}">{{ __('Is Ingilizcesi Ozel Ders') }}</a>
                        <a href="{{ route('english-private-lessons.istanbul') }}">{{ __('Istanbul Ingilizce Ozel Ders') }}</a>
                        <a href="{{ route('english-private-lessons.izmir') }}">{{ __('Izmir Ingilizce Ozel Ders') }}</a>
                        <a href="{{ route('corporate.index') }}">{{ __('Kurumsal Ingilizce Egitimi') }}</a>
                        <a href="{{ route('placement-test.show') }}">{{ __('Seviye Tespit Sinavi') }}</a>
                    </div>
                </div>
            </div>

            <div class="lf-footer__col">
                <h6 class="lf-footer__title">{{ __('Menu') }}</h6>
                @if ($nav_menu && count($nav_menu->menuItems))
                    <ul class="lf-footer__list">
                        @foreach ($nav_menu->menuItems as $menu)
                            @php
                                $hasChild = $menu->child && count($menu->child);
                                $href = $hasChild ? 'javascript:;' : url($menu?->link);

                                // Hide pricing/packages link in footer (requested).
                                $labelLower = strtolower(trim((string) ($menu?->label ?? '')));
                                $isPricingLink = str_contains($labelLower, 'fiyat') || str_contains($labelLower, 'pricing');
                            @endphp
                            @continue($isPricingLink)
                            <li><a href="{{ $href }}">{{ $menu?->label }}</a></li>
                        @endforeach
                    </ul>
                @endif
            </div>

            <div class="lf-footer__col lf-footer__col--actions">
                <h6 class="lf-footer__title">{{ __('Follow Us On') }}</h6>
                @if (count(getSocialLinks()) > 0)
                    <ul class="lf-footer__social">
                        @foreach (getSocialLinks() as $socialLink)
                            <li>
                                <a href="{{ $socialLink->link }}" target="_blank" rel="noopener">
                                    <img src="{{ asset($socialLink->icon) }}" alt="social">
                                </a>
                            </li>
                        @endforeach
                    </ul>
                @endif

                <div class="lf-footer__stores">
                    @if ($footerSetting?->google_play_link)
                        <a href="{{ $footerSetting->google_play_link }}" class="lf-footer__store" aria-label="Google Play">
                            <img src="{{ asset('frontend/img/others/google-play.svg') }}" alt="Google Play">
                        </a>
                    @endif
                    @if ($footerSetting?->apple_store_link)
                        <a href="{{ $footerSetting->apple_store_link }}" class="lf-footer__store" aria-label="Apple Store">
                            <img src="{{ asset('frontend/img/others/apple-store.svg') }}" alt="Apple Store">
                        </a>
                    @endif
                </div>
                <div class="lf-footer__payments" aria-label="{{ __('Payment methods') }}">
                    <span class="lf-footer__payments-label">{{ __('Ödeme Yöntemleri') }}</span>
                    <div class="lf-footer__payments-row">
                        <img src="{{ asset('frontend/img/payments/iyzico-band-white.svg') }}"
                            alt="{{ __('İyzico ile öde') }}" class="lf-footer__payments-logo lf-footer__payments-logo--iyzico">
                        <img src="{{ asset('frontend/img/payments/cc-visa.svg') }}" alt="Visa"
                            class="lf-footer__payments-logo">
                        <img src="{{ asset('frontend/img/payments/cc-mastercard.svg') }}" alt="Mastercard"
                            class="lf-footer__payments-logo">
                    </div>
                </div>
            </div>
        </div>

        <div class="lf-footer__bottom">
            <div class="lf-footer__copy">
                @if (Cache::get('setting')?->copyright_text)
                    <p>{{ Cache::get('setting')->copyright_text }}</p>
                @endif
            </div>
            <div class="lf-footer__legal">
                @php
                    $footerLegalItems = $footer_menu_three?->menuItems ?? [];
                    $footerLegalKeys = [];
                    foreach ($footerLegalItems as $footerItem) {
                        $footerLegalKeys[] = strtolower(trim((string) ($footerItem?->label ?? '')));
                        $footerLegalKeys[] = strtolower(trim((string) ($footerItem?->link ?? '')));
                    }
                    $hasDeliveryReturn = false;
                    $hasDistanceSales = false;
                    foreach ($footerLegalKeys as $footerKey) {
                        if (str_contains($footerKey, 'teslimat') || str_contains($footerKey, 'iade')) {
                            $hasDeliveryReturn = true;
                        }
                        if (str_contains($footerKey, 'mesafeli') || str_contains($footerKey, 'satis')) {
                            $hasDistanceSales = true;
                        }
                    }
                @endphp
                @if ($footer_menu_three && count($footer_menu_three->menuItems))
                    <ul class="lf-footer__legal-list">
                        @foreach ($footer_menu_three->menuItems as $footerMenuThree)
                            <li><a href="{{ url($footerMenuThree?->link) }}">{{ $footerMenuThree?->label }}</a></li>
                        @endforeach
                        @if (!$hasDeliveryReturn)
                            <li><a href="{{ route('delivery-return-terms') }}">{{ __('Teslimat ve İade Şartları') }}</a></li>
                        @endif
                        @if (!$hasDistanceSales)
                            <li><a href="{{ route('distance-sales-contract') }}">{{ __('Mesafeli Satış Sözleşmesi') }}</a></li>
                        @endif
                    </ul>
                @else
                    <ul class="lf-footer__legal-list">
                        <li><a href="{{ route('delivery-return-terms') }}">{{ __('Teslimat ve İade Şartları') }}</a></li>
                        <li><a href="{{ route('distance-sales-contract') }}">{{ __('Mesafeli Satış Sözleşmesi') }}</a></li>
                    </ul>
                @endif
            </div>
        </div>
    </div>
</footer>

<style>
    /* Sticky footer (keeps footer at the bottom on short pages) */
    body {
        min-height: 100vh;
        display: flex;
        flex-direction: column;
    }

    main.main-area {
        flex: 1 0 auto;
    }

    footer.lf-footer {
        flex: 0 0 auto;
        margin-top: auto;
    }

    footer.lf-footer,
    footer.lf-footer * {
        box-sizing: border-box;
    }

    footer.lf-footer {
        --lf-primary: var(--tg-theme-primary, #0e5c93);
        --lf-accent: var(--tg-theme-secondary, #f6a105);
        --lf-dark: var(--tg-common-color-dark, #0b3f6c);
        position: relative;
        overflow: hidden;
        padding: 42px 0 14px;
        background: linear-gradient(180deg, var(--lf-primary), #083a61);
        color: rgba(255, 255, 255, 0.86);
        border-top: 1px solid rgba(255, 255, 255, 0.10);
    }

    footer.lf-footer::before {
        content: "";
        position: absolute;
        inset: -120px -120px auto -120px;
        height: 320px;
        background: radial-gradient(circle at 20% 30%, rgba(246, 161, 5, 0.26), transparent 55%),
            radial-gradient(circle at 70% 20%, rgba(255, 255, 255, 0.10), transparent 55%);
        pointer-events: none;
        z-index: 0;
    }

    .lf-footer .container {
        position: relative;
        z-index: 1;
    }

    .lf-footer__grid {
        display: grid;
        grid-template-columns: 1.2fr 1fr 1fr;
        gap: 24px;
        align-items: start;
        padding-bottom: 18px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.12);
    }

    .lf-footer__brand {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .lf-footer__logo {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 9px 12px;
        border-radius: 18px;
        background: rgba(255, 255, 255, 0.96);
        border: 1px solid rgba(255, 255, 255, 0.18);
        box-shadow: 0 18px 44px rgba(0, 0, 0, 0.22);
        width: fit-content;
    }

    .lf-footer__logo img {
        max-height: 38px;
        width: auto;
        display: block;
    }

    .lf-footer__logo-text {
        font-weight: 1000;
        letter-spacing: 0.2px;
        color: var(--lf-primary);
        font-size: 18px;
    }

    .lf-footer__text {
        margin: 0;
        font-weight: 700;
        line-height: 1.6;
        color: rgba(255, 255, 255, 0.78);
        max-width: 420px;
        font-size: 13px;
    }

    .lf-footer__seo {
        display: grid;
        gap: 10px;
    }

    .lf-footer__seo-links {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }

    .lf-footer__seo-links a {
        display: inline-flex;
        align-items: center;
        min-height: 38px;
        padding: 8px 14px;
        border-radius: 999px;
        background: rgba(255, 255, 255, 0.08);
        border: 1px solid rgba(255, 255, 255, 0.12);
        font-size: 12px;
        letter-spacing: 0.2px;
    }

    .lf-footer__seo-links a:hover {
        background: rgba(246, 161, 5, 0.18);
        border-color: rgba(246, 161, 5, 0.42);
        color: #fff;
        text-decoration: none;
    }

    .lf-footer__title {
        margin: 0 0 12px;
        font-weight: 1000;
        color: rgba(255, 255, 255, 0.95);
        font-size: 13px;
        letter-spacing: 0.6px;
        text-transform: uppercase;
    }

    .lf-footer__list,
    .lf-footer__social,
    .lf-footer__legal-list {
        list-style: none;
        margin: 0;
        padding: 0;
    }

    .lf-footer__list {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 10px 16px;
    }

    .lf-footer a {
        color: rgba(255, 255, 255, 0.82);
        text-decoration: none;
        font-weight: 800;
        transition: color .2s ease, background .2s ease, transform .2s ease, border-color .2s ease;
    }

    .lf-footer__list a:hover,
    .lf-footer__legal a:hover {
        color: #fff;
        text-decoration: underline;
        text-decoration-color: rgba(246, 161, 5, 0.85);
        text-underline-offset: 4px;
    }

    .lf-footer__social {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-bottom: 12px;
    }

    .lf-footer__social a {
        width: 40px;
        height: 40px;
        border-radius: 14px;
        display: grid;
        place-items: center;
        background: rgba(255, 255, 255, 0.10);
        border: 1px solid rgba(255, 255, 255, 0.14);
        text-decoration: none;
    }

    .lf-footer__social a:hover {
        background: rgba(246, 161, 5, 0.22);
        border-color: rgba(246, 161, 5, 0.45);
        transform: translateY(-2px);
    }

    .lf-footer__social img {
        width: 18px;
        height: 18px;
        filter: brightness(0) invert(1);
    }

    .lf-footer__stores {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
        align-items: center;
    }

    .lf-footer__payments {
        margin-top: 12px;
        display: grid;
        gap: 10px;
    }

    .lf-footer__payments-label {
        font-weight: 900;
        letter-spacing: 0.6px;
        font-size: 12px;
        text-transform: uppercase;
        color: rgba(255, 255, 255, 0.92);
    }

    .lf-footer__payments-row {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 12px;
    }

    .lf-footer__payments-logo {
        height: 24px;
        width: auto;
        filter: brightness(0) invert(1);
        opacity: 0.9;
    }

    .lf-footer__payments-logo--iyzico {
        height: 26px;
        filter: none;
        opacity: 0.95;
    }

    .lf-footer__store {
        display: inline-flex;
        border-radius: 14px;
        overflow: hidden;
        background: rgba(255, 255, 255, 0.95);
        box-shadow: 0 14px 34px rgba(0, 0, 0, 0.16);
        transform: translateZ(0);
    }

    .lf-footer__store:hover {
        transform: translateY(-2px);
        box-shadow: 0 18px 44px rgba(0, 0, 0, 0.20);
    }

    .lf-footer__store img {
        max-height: 36px;
        display: block;
    }

    .lf-footer__bottom {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        flex-wrap: wrap;
        padding-top: 12px;
    }

    .lf-footer__copy p {
        margin: 0;
        color: rgba(255, 255, 255, 0.72);
        font-size: 13px;
        font-weight: 700;
    }

    .lf-footer__legal-list {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
        align-items: center;
        margin: 0;
        padding: 0;
    }

    .lf-footer__legal a {
        color: rgba(255, 255, 255, 0.78);
        font-weight: 800;
        font-size: 13px;
    }

    @media (max-width: 991.98px) {
        .lf-footer__grid {
            grid-template-columns: 1fr;
        }

        .lf-footer__list {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }
    }

    @media (max-width: 575.98px) {
        footer.lf-footer {
            padding: 34px 0 14px;
        }

        .lf-footer__list {
            grid-template-columns: 1fr;
        }

        .lf-footer__stores,
        .lf-footer__payments-row,
        .lf-footer__social {
            justify-content: flex-start;
        }

        .lf-footer__store {
            width: 100%;
            justify-content: center;
        }

        .lf-footer__store img {
            max-width: 100%;
            height: auto;
        }
    }
</style>
