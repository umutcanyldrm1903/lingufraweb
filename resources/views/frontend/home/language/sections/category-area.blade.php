<section class="lang-journey section-py-100">
    <div class="container custom-container">
        <div class="lang-journey__grid">
            <div class="lang-journey__intro">
                <p class="lang-journey__eyebrow">{{ __('Ogrenme yolculugun') }}</p>
                <h2 class="lang-journey__title">{{ __('Ozguvenli Ingilizceye giden net bir yol') }}</h2>
                <p class="lang-journey__lead">
                    {{ __('Seni adim adim yonlendiriyoruz: sade bir plan, destekleyici bir egitmen ve olculebilir ilerleme ile hedefini netlestiriyoruz.') }}
                </p>

                <div class="lang-journey__checks">
                    <div class="lang-journey__check"><span class="dot"></span>{{ __('Sana ozel plan ve canli dersler') }}</div>
                    <div class="lang-journey__check"><span class="dot"></span>{{ __('Seviyene uygun icerik ve net geri bildirim') }}</div>
                    <div class="lang-journey__check"><span class="dot"></span>{{ __('Esnek program ve raporlanabilir ilerleme') }}</div>
                </div>

                <div class="lang-journey__cta">
                    <a href="{{ route('register', ['role' => 'student']) }}" class="btn lang-journey__btn">{{ __('Hemen basla') }}</a>
                </div>
            </div>

            <div class="lang-journey__steps">
                <div class="lang-journey__step">
                    <div class="lang-journey__step-head">
                        <span class="lang-journey__step-index">01</span>
                        <span class="lang-journey__step-icon"><i class="fas fa-compass"></i></span>
                    </div>
                    <h3 class="lang-journey__step-title">{{ __('Hedefini belirle') }}</h3>
                    <p class="lang-journey__step-text">{{ __('Seviyeni ve odagini soyle, sana uygun yol haritasini birlikte hazirlayalim.') }}</p>
                </div>

                <div class="lang-journey__step">
                    <div class="lang-journey__step-head">
                        <span class="lang-journey__step-index">02</span>
                        <span class="lang-journey__step-icon"><i class="fas fa-user-friends"></i></span>
                    </div>
                    <h3 class="lang-journey__step-title">{{ __('Egitmeninle tanis') }}</h3>
                    <p class="lang-journey__step-text">{{ __('Ihtiyacini anlayan egitmenle esles, ritmini kur ve ilk derste guven kazan.') }}</p>
                </div>

                <div class="lang-journey__step">
                    <div class="lang-journey__step-head">
                        <span class="lang-journey__step-index">03</span>
                        <span class="lang-journey__step-icon"><i class="fas fa-chart-line"></i></span>
                    </div>
                    <h3 class="lang-journey__step-title">{{ __('Ilerlemeni takip et') }}</h3>
                    <p class="lang-journey__step-text">{{ __('Net hedeflerle haftadan haftaya gelisimini gor, motivasyonunu kaybetmeden ilerle.') }}</p>
                </div>
            </div>
        </div>
    </div>
</section>

@push('styles')
<style>
    .lang-journey {
        position: relative;
        overflow: hidden;
        background:
            radial-gradient(700px circle at 10% 20%, rgba(246, 161, 5, 0.09), transparent 50%),
            radial-gradient(620px circle at 85% 80%, rgba(14, 92, 147, 0.12), transparent 54%),
            linear-gradient(180deg, #f7fbff 0%, #eef5fb 100%);
        --brand-primary: var(--tg-theme-primary);
        --brand-accent: var(--tg-theme-secondary);
    }

    .lang-journey::before,
    .lang-journey::after {
        content: '';
        position: absolute;
        width: 420px;
        height: 420px;
        border-radius: 50%;
        opacity: 0.14;
        pointer-events: none;
    }

    .lang-journey::before {
        top: -180px;
        left: -180px;
        background: var(--brand-primary);
    }

    .lang-journey::after {
        bottom: -200px;
        right: -200px;
        background: var(--brand-accent);
    }

    .lang-journey .container {
        position: relative;
        z-index: 1;
    }

    .lang-journey__grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 40px;
        align-items: center;
    }

    .lang-journey__intro {
        display: grid;
        gap: 16px;
    }

    .lang-journey__eyebrow {
        margin: 0;
        font-weight: 900;
        letter-spacing: 0.2em;
        text-transform: uppercase;
        font-size: 12px;
        color: var(--brand-primary);
    }

    .lang-journey__title {
        margin: 0;
        font-weight: 1000;
        font-size: 40px;
        line-height: 1.05;
        color: var(--tg-heading-color);
    }

    .lang-journey__lead {
        margin: 0;
        color: var(--tg-body-color);
        font-weight: 700;
        font-size: 16px;
        max-width: 560px;
    }

    .lang-journey__checks {
        display: grid;
        gap: 10px;
    }

    .lang-journey__check {
        display: flex;
        gap: 10px;
        align-items: center;
        font-weight: 900;
        color: var(--tg-common-color-black-2);
    }

    .lang-journey__check .dot {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        background: var(--brand-accent);
        display: inline-block;
        box-shadow: 0 6px 14px rgba(0, 0, 0, 0.12);
    }

    .lang-journey__cta {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
        margin-top: 8px;
    }

    .lang-journey__btn {
        border-radius: 14px;
        font-weight: 900;
        padding: 12px 18px;
        background: var(--brand-accent);
        border-color: var(--brand-accent);
        color: var(--tg-common-color-black-3);
    }

    .lang-journey__btn:hover {
        background: var(--brand-primary);
        border-color: var(--brand-primary);
        color: var(--tg-common-color-white);
    }

    .lang-journey__steps {
        display: grid;
        gap: 18px;
    }

    .lang-journey__step {
        background: linear-gradient(160deg, #ffffff 0%, #f6f9fd 100%);
        border-radius: 22px;
        padding: 20px 22px;
        border: 1px solid rgba(14, 92, 147, 0.12);
        box-shadow: 0 20px 50px rgba(15, 23, 42, 0.11);
        display: grid;
        gap: 10px;
    }

    .lang-journey__step-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .lang-journey__step-index {
        font-weight: 1000;
        letter-spacing: 0.08em;
        color: var(--brand-primary);
        background: rgba(14, 92, 147, 0.12);
        border-radius: 999px;
        padding: 6px 12px;
        font-size: 12px;
    }

    .lang-journey__step-icon {
        width: 40px;
        height: 40px;
        border-radius: 14px;
        display: grid;
        place-items: center;
        background: rgba(246, 161, 5, 0.18);
        color: var(--brand-primary);
        font-size: 18px;
        border: 1px solid rgba(246, 161, 5, 0.25);
    }

    .lang-journey__step-title {
        margin: 0;
        font-weight: 1000;
        font-size: 18px;
        color: var(--tg-heading-color);
    }

    .lang-journey__step-text {
        margin: 0;
        color: var(--tg-body-color);
        font-weight: 700;
        font-size: 14px;
    }

    @media (max-width: 991px) {
        .lang-journey__grid {
            grid-template-columns: 1fr;
        }

        .lang-journey__title {
            font-size: 32px;
        }
    }
</style>
@endpush
