@once
@php
    $priceLeadPhone = preg_replace('/\D+/', '', (string) (config('app.whatsapp_lead_phone') ?: '+90 537 773 04 97'));
    $heroShortTitle = $hero?->content?->short_title ?? __('Canli Online Dersler');
    $heroTitle = $hero?->content?->title ?? __('Kariyerinizi guclendirecek Ingilizce deneyimi');
    $heroSubtitle = $hero?->content?->sub_title ?? __('Native egitmenler, esnek saatler ve olculebilir ilerleme ile seviyenizi hizla yukseltebilirsiniz.');

    $rotatingHighlights = [
        __('Konusma odakli egitim'),
        __('Sinav hazirlik programi'),
        __('Is Ingilizcesi ve sunum'),
        __('Cocuklar icin ozel akis'),
    ];

    $heroBullets = [
        __('Canli birebir dersler ile her oturumda aktif konusma pratigi yapin.'),
        __('Rezervasyon, odev ve ilerleme takibini tek panelden yonetin.'),
        __('Seviyenize gore kisisel program ile hedefinize sistemli ilerleyin.'),
    ];

    $heroFeatureCards = [
        [
            'icon' => 'fas fa-bolt',
            'title' => __('Hizli Baslangic'),
            'text' => __('2 dakikada seviye analizi ve planlama'),
        ],
        [
            'icon' => 'fas fa-user-check',
            'title' => __('Dogru Egitmen'),
            'text' => __('Profil, uzmanlik ve saat bazli secim'),
        ],
        [
            'icon' => 'fas fa-chart-line',
            'title' => __('Olculebilir Gelisim'),
            'text' => __('Raporlar ve duzenli performans takibi'),
        ],
        [
            'icon' => 'fas fa-headset',
            'title' => __('Surekli Destek'),
            'text' => __('Mesaj ve destek kanali ile hizli cozum'),
        ],
    ];
@endphp

<section class="lang-hero">
    <div class="lang-hero__bg-video-wrap" aria-hidden="true">
        <video
            id="lang-hero-bg-video"
            class="lang-hero__bg-video"
            autoplay
            muted
            loop
            playsinline
            preload="auto"
            data-fallback="{{ asset('uploads/website-videos/home-showcase/home-video-02.mp4') }}"
        >
            <source src="{{ asset('uploads/website-videos/home-showcase/home-video-04.mp4') }}" type="video/mp4">
            <source src="{{ asset('uploads/website-videos/home-showcase-web/home-video-04.mp4') }}" type="video/mp4">
        </video>
    </div>

    <span class="lang-hero__orb lang-hero__orb--one" aria-hidden="true"></span>
    <span class="lang-hero__orb lang-hero__orb--two" aria-hidden="true"></span>
    <span class="lang-hero__mesh" aria-hidden="true"></span>

    <div class="container">
        <div class="row align-items-center g-4">
            <div class="col-lg-7">
                <div class="lang-hero__copy">
                    <p class="lang-hero__eyebrow">
                        <span class="lang-hero__eyebrow-dot"></span>{{ $heroShortTitle }}
                    </p>

                    <h1 class="lang-hero__title">{!! clean(processText($heroTitle)) !!}</h1>
                    <p class="lang-hero__lead">{!! clean(processText($heroSubtitle)) !!}</p>

                    <div class="lang-hero__rotator-wrap">
                        <span class="lang-hero__rotator-label">{{ __('Odak') }}</span>
                        <div class="lang-hero__rotator" data-hero-rotator>
                            @foreach ($rotatingHighlights as $line)
                                <span class="lang-hero__rotator-item {{ $loop->first ? 'is-active' : '' }}">{{ $line }}</span>
                            @endforeach
                        </div>
                    </div>

                    <ul class="lang-hero__bullet-list">
                        @foreach ($heroBullets as $bullet)
                            <li>{{ $bullet }}</li>
                        @endforeach
                    </ul>

                    <div class="lang-hero__feature-grid">
                        @foreach ($heroFeatureCards as $feature)
                            <article class="lang-hero__feature-card">
                                <span class="lang-hero__feature-icon"><i class="{{ $feature['icon'] }}" aria-hidden="true"></i></span>
                                <div>
                                    <h3>{{ $feature['title'] }}</h3>
                                    <p>{{ $feature['text'] }}</p>
                                </div>
                            </article>
                        @endforeach
                    </div>

                    <div class="lang-hero__cta">
                        <a href="{{ route('login') }}" class="btn lang-hero__btn">{{ __('Ucretsiz deneme dersi') }}</a>
                        <a href="#lang-packages" class="btn lang-hero__btn lang-hero__btn--ghost">{{ __('Paketleri kesfet') }}</a>
                    </div>

                    <div class="lang-hero__stores">
                        <a href="#" class="lang-hero__store" aria-label="App Store">
                            <img src="{{ asset('frontend/img/others/apple-store.svg') }}" alt="App Store">
                        </a>
                        <a href="#" class="lang-hero__store" aria-label="Google Play">
                            <img src="{{ asset('frontend/img/others/google-play.svg') }}" alt="Google Play">
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="lang-hero__visual">
                    <div class="lang-hero__form-card">
                        <div class="lang-hero__form-head">
                            <h3 class="lang-hero__form-title">{{ __('Fiyat bilgisi almak icin formu doldur') }}</h3>
                        </div>
                        <form id="hero-price-form" class="lang-hero__form" data-wa-phone="{{ $priceLeadPhone }}" novalidate>
                            <label class="hero-field">
                                <span>{{ __('Ad') }} *</span>
                                <input type="text" name="first_name" autocomplete="given-name" required>
                            </label>
                            <label class="hero-field">
                                <span>{{ __('Soyad') }} *</span>
                                <input type="text" name="last_name" autocomplete="family-name" required>
                            </label>
                            <label class="hero-field">
                                <span>{{ __('E-posta') }} *</span>
                                <input type="email" name="email" autocomplete="email" required>
                            </label>
                            <div class="hero-field">
                                <span>{{ __('Telefon tipi') }}</span>
                                <div class="hero-toggle" role="radiogroup" aria-label="{{ __('Telefon tipi') }}">
                                    <label class="hero-toggle__option">
                                        <input type="radio" name="phone_type" value="Mobil" checked>
                                        <span>{{ __('Mobil') }}</span>
                                    </label>
                                    <label class="hero-toggle__option">
                                        <input type="radio" name="phone_type" value="Sabit Hat">
                                        <span>{{ __('Sabit Hat') }}</span>
                                    </label>
                                </div>
                            </div>
                            <div class="hero-field">
                                <span>{{ __('Telefon') }} *</span>
                                <div class="hero-phone" role="group" aria-label="{{ __('Telefon') }}">
                                    <input type="text" name="phone_part_1" inputmode="numeric" maxlength="1" placeholder="0"
                                        aria-label="{{ __('Telefon ilk hane') }}" required>
                                    <input type="text" name="phone_part_2" inputmode="numeric" maxlength="3" placeholder="XXX"
                                        aria-label="{{ __('Telefon orta hane') }}" required>
                                    <input type="text" name="phone_part_3" inputmode="numeric" maxlength="3" placeholder="XXX"
                                        aria-label="{{ __('Telefon orta hane') }}" required>
                                    <input type="text" name="phone_part_4" inputmode="numeric" maxlength="4" placeholder="XXXX"
                                        aria-label="{{ __('Telefon son hane') }}" required>
                                </div>
                            </div>
                            <label class="hero-field">
                                <span>{{ __('Kullanici yasi?') }}</span>
                                <select name="age_group">
                                    <option value="" selected>{{ __('Seciniz') }}</option>
                                    <option value="0-12">0-12</option>
                                    <option value="13-17">13-17</option>
                                    <option value="18-24">18-24</option>
                                    <option value="25-34">25-34</option>
                                    <option value="35-44">35-44</option>
                                    <option value="45+">45+</option>
                                </select>
                            </label>
                            <div class="hero-checks">
                                <label class="hero-check">
                                    <input type="checkbox" name="consent_marketing" required>
                                    <span>{{ __('Tarafima ticari elektronik ileti gonderilmesini ETK Bilgilendirme Metni cercevesinde kabul ediyorum.') }}</span>
                                </label>
                                <label class="hero-check">
                                    <input type="checkbox" name="consent_privacy" required>
                                    <span>{{ __("Kisisel verilerimin islenmesine, saklanmasina ve aktarilmasina iliskin Aydinlatma Metni'ni okudum, anladim.") }}</span>
                                </label>
                            </div>
                            <div class="hero-actions">
                                <p class="hero-required">* {{ __('Zorunlu') }}</p>
                                <button type="submit" class="hero-submit">
                                    <i class="fas fa-lock" aria-hidden="true"></i>{{ __('Gonder') }}
                                </button>
                            </div>
                            <p class="hero-footnote">
                                {{ __("Gonder butonuna basarak Kullanim Kosullari'ni ve Gizlilik Politikasi'ni kabul etmis olursunuz.") }}
                            </p>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@push('styles')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Manrope:wght@500;700;800;900&display=swap');

    .lang-hero {
        position: relative;
        overflow: hidden;
        padding: 138px 0 92px;
        color: #fff;
        background: #041728;
        font-family: 'Manrope', 'Segoe UI', sans-serif;
    }

    .lang-hero__bg-video-wrap {
        position: absolute;
        inset: 0;
        z-index: 0;
        pointer-events: none;
        overflow: hidden;
    }

    .lang-hero__bg-video {
        width: 100%;
        height: 100%;
        display: block;
        object-fit: cover;
        object-position: center center;
        filter: saturate(1.04) contrast(1.06);
    }

    .lang-hero::before {
        content: '';
        position: absolute;
        inset: 0;
        z-index: 1;
        pointer-events: none;
        background:
            linear-gradient(90deg, rgba(2, 8, 18, 0.18) 0%, rgba(2, 8, 18, 0.44) 56%, rgba(2, 8, 18, 0.58) 100%),
            linear-gradient(90deg, rgba(255, 255, 255, 0.04) 1px, transparent 1px),
            linear-gradient(180deg, rgba(255, 255, 255, 0.04) 1px, transparent 1px);
        background-size: 100% 100%, 42px 42px, 42px 42px;
    }

    .lang-hero__orb {
        position: absolute;
        border-radius: 999px;
        filter: blur(2px);
        pointer-events: none;
        z-index: 1;
    }

    .lang-hero__orb--one {
        width: 420px;
        height: 420px;
        left: -120px;
        top: -100px;
        background: radial-gradient(circle, rgba(246, 161, 5, 0.30) 0%, rgba(246, 161, 5, 0) 70%);
        animation: floatOrb 9s ease-in-out infinite;
    }

    .lang-hero__orb--two {
        width: 520px;
        height: 520px;
        right: -180px;
        bottom: -220px;
        background: radial-gradient(circle, rgba(109, 196, 255, 0.26) 0%, rgba(109, 196, 255, 0) 72%);
        animation: floatOrb 11s ease-in-out infinite reverse;
    }

    .lang-hero__mesh {
        position: absolute;
        left: -100px;
        bottom: -110px;
        width: 280px;
        height: 280px;
        pointer-events: none;
        z-index: 1;
        background-image: radial-gradient(circle, rgba(255, 255, 255, 0.26) 2px, transparent 2px);
        background-size: 20px 20px;
        opacity: .24;
    }

    .lang-hero .container,
    .lang-hero .row,
    .lang-hero__copy,
    .lang-hero__visual,
    .lang-hero__form-card {
        position: relative;
        z-index: 2;
    }

    .lang-hero__copy {
        max-width: 650px;
    }

    .lang-hero__eyebrow {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        min-height: 34px;
        padding: 8px 14px;
        margin: 0 0 16px;
        border-radius: 999px;
        border: 1px solid rgba(255, 255, 255, 0.30);
        background: rgba(255, 255, 255, 0.10);
        color: #fff;
        font-size: 11px;
        letter-spacing: 0.09em;
        font-weight: 900;
        text-transform: uppercase;
    }

    .lang-hero__eyebrow-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: #f6a105;
        box-shadow: 0 0 0 0 rgba(246, 161, 5, 0.55);
        animation: pulseDot 1.8s ease-out infinite;
    }

    .lang-hero__title {
        margin: 0;
        font-size: clamp(38px, 5.2vw, 66px);
        line-height: 1.03;
        font-weight: 900;
        color: #ffffff;
        text-wrap: balance;
        text-shadow: 0 18px 42px rgba(2, 6, 23, 0.45);
    }

    .lang-hero__lead {
        margin: 14px 0 14px;
        font-size: 18px;
        line-height: 1.55;
        color: #e2eeff;
        font-weight: 700;
        max-width: 600px;
    }

    .lang-hero__rotator-wrap {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 16px;
        border: 1px solid rgba(255, 255, 255, 0.24);
        border-radius: 12px;
        padding: 8px 12px;
        background: rgba(2, 6, 23, 0.24);
    }

    .lang-hero__rotator-label {
        display: inline-flex;
        align-items: center;
        min-height: 26px;
        padding: 5px 10px;
        border-radius: 999px;
        background: rgba(246, 161, 5, 0.98);
        color: #111827;
        font-size: 10px;
        font-weight: 900;
        letter-spacing: .08em;
        text-transform: uppercase;
    }

    .lang-hero__rotator {
        position: relative;
        min-width: min(60vw, 320px);
        min-height: 30px;
    }

    .lang-hero__rotator-item {
        position: absolute;
        inset: 0;
        display: flex;
        align-items: center;
        opacity: 0;
        transform: translateY(8px);
        transition: opacity .36s ease, transform .36s ease;
        color: #fff;
        font-size: 18px;
        font-weight: 900;
        white-space: nowrap;
    }

    .lang-hero__rotator-item.is-active {
        opacity: 1;
        transform: translateY(0);
    }

    .lang-hero__bullet-list {
        margin: 0 0 20px;
        padding-left: 18px;
        color: #e7f0ff;
        font-weight: 700;
        font-size: 16px;
        line-height: 1.58;
    }

    .lang-hero__bullet-list li {
        margin-bottom: 5px;
    }

    .lang-hero__feature-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 10px;
        margin-bottom: 20px;
    }

    .lang-hero__feature-card {
        display: grid;
        grid-template-columns: 36px minmax(0, 1fr);
        gap: 10px;
        align-items: start;
        padding: 11px 12px;
        border-radius: 14px;
        border: 1px solid rgba(255, 255, 255, 0.22);
        background: rgba(255, 255, 255, 0.10);
        box-shadow: 0 12px 24px rgba(2, 6, 23, 0.24);
    }

    .lang-hero__feature-card:hover {
        transform: translateY(-1px);
        border-color: rgba(255, 255, 255, 0.34);
    }

    .lang-hero__feature-icon {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: rgba(246, 161, 5, 0.95);
        color: #111827;
        font-size: 15px;
        box-shadow: 0 8px 16px rgba(246, 161, 5, 0.3);
    }

    .lang-hero__feature-card h3 {
        margin: 0 0 3px;
        font-size: 14px;
        color: #fff;
        font-weight: 900;
    }

    .lang-hero__feature-card p {
        margin: 0;
        font-size: 12px;
        color: #d8e7ff;
        font-weight: 700;
        line-height: 1.4;
    }

    .lang-hero__cta {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
        margin-bottom: 12px;
    }

    .lang-hero__btn {
        border-radius: 14px;
        padding: 12px 18px;
        font-weight: 900;
        border: 1px solid #f6a105;
        background: #f6a105;
        color: #111827;
        box-shadow: 0 14px 30px rgba(246, 161, 5, 0.30);
    }

    .lang-hero__btn:hover {
        background: #d88a00;
        border-color: #d88a00;
        color: #111827;
    }

    .lang-hero__btn--ghost {
        background: rgba(255, 255, 255, 0.11);
        border-color: rgba(255, 255, 255, 0.34);
        color: #fff;
        box-shadow: none;
    }

    .lang-hero__btn--ghost:hover {
        background: rgba(255, 255, 255, 0.2);
        border-color: rgba(255, 255, 255, 0.5);
        color: #fff;
    }

    .lang-hero__stores {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }

    .lang-hero__store {
        display: inline-flex;
        border-radius: 12px;
        overflow: hidden;
        background: #fff;
        box-shadow: 0 12px 26px rgba(2, 6, 23, 0.28);
    }

    .lang-hero__store img {
        height: 44px;
        width: auto;
        display: block;
    }

    .lang-hero__visual {
        display: flex;
        justify-content: flex-end;
    }

    .lang-hero__form-card {
        background: rgba(255, 255, 255, 0.94);
        border-radius: 20px;
        border: 1px solid rgba(191, 219, 254, 0.62);
        box-shadow: 0 28px 66px rgba(2, 6, 23, 0.38);
        backdrop-filter: blur(8px);
        max-width: 390px;
        width: 100%;
        color: #0f172a;
        overflow: hidden;
    }

    .lang-hero__form-head {
        padding: 14px 18px;
        text-align: center;
        background: linear-gradient(180deg, #f8fbff 0%, #edf4ff 100%);
        border-bottom: 1px solid rgba(15, 23, 42, 0.08);
    }

    .lang-hero__form-title {
        margin: 0;
        font-weight: 900;
        letter-spacing: 0.12em;
        color: #0b4f81;
        font-size: 12px;
        line-height: 1.3;
        text-transform: uppercase;
    }

    .lang-hero__form {
        display: grid;
        gap: 10px;
        padding: 16px 20px 20px;
    }

    .hero-field {
        display: grid;
        gap: 6px;
        font-weight: 800;
        color: #0f172a;
        font-size: 12px;
    }

    .hero-field input,
    .hero-field select {
        border: 0;
        border-bottom: 1px solid #cbd5e1;
        padding: 6px 2px;
        font-weight: 700;
        color: #0f172a;
        background: transparent;
        font-size: 13px;
    }

    .hero-field input:focus,
    .hero-field select:focus {
        outline: 0;
        border-bottom-color: #f6a105;
    }

    .hero-toggle {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        border-radius: 10px;
        border: 1px solid #cbd5e1;
        overflow: hidden;
        background: #f8fafc;
    }

    .hero-toggle__option {
        display: block;
        text-align: center;
        font-weight: 900;
        color: #64748b;
        cursor: pointer;
    }

    .hero-toggle__option input {
        display: none;
    }

    .hero-toggle__option span {
        display: block;
        padding: 8px;
        font-size: 12px;
    }

    .hero-toggle__option input:checked + span {
        background: #f6a105;
        color: #1c1c1c;
    }

    .hero-phone {
        display: grid;
        grid-template-columns: 36px repeat(3, minmax(0, 1fr));
        gap: 8px;
        align-items: center;
    }

    .hero-phone input {
        padding: 6px 4px;
        text-align: center;
        border: 1px solid transparent;
        border-bottom: 1px solid #cbd5e1;
        border-radius: 0;
        font-weight: 800;
    }

    .hero-phone input:focus {
        border-bottom-color: #f6a105;
    }

    .hero-checks {
        display: grid;
        gap: 6px;
        margin-top: 2px;
    }

    .hero-check {
        display: flex;
        gap: 8px;
        align-items: flex-start;
        font-weight: 800;
        color: #475569;
        font-size: 10px;
        line-height: 1.35;
    }

    .hero-check input {
        margin-top: 3px;
        accent-color: #f6a105;
    }

    .hero-actions {
        display: flex;
        align-items: center;
        gap: 10px;
        justify-content: space-between;
        margin-top: 4px;
    }

    .hero-required {
        margin: 0;
        font-weight: 900;
        color: #94a3b8;
        font-size: 10px;
        flex: 0 0 auto;
    }

    .hero-submit {
        flex: 1;
        border-radius: 999px;
        padding: 10px 12px;
        font-weight: 900;
        font-size: 12px;
        background: #f6a105;
        border: 1px solid #f6a105;
        color: #1c1c1c;
        box-shadow: 0 12px 22px rgba(0, 0, 0, 0.14);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        text-transform: uppercase;
        letter-spacing: 0.04em;
    }

    .hero-submit:hover {
        background: #0e5c93;
        border-color: #0e5c93;
        color: #fff;
    }

    .hero-footnote {
        margin: 0;
        font-size: 9px;
        color: #64748b;
        font-weight: 700;
        text-align: center;
        line-height: 1.3;
    }

    @keyframes pulseDot {
        0% {
            box-shadow: 0 0 0 0 rgba(246, 161, 5, 0.55);
        }
        100% {
            box-shadow: 0 0 0 12px rgba(246, 161, 5, 0);
        }
    }

    @keyframes floatOrb {
        0% {
            transform: translateY(0) translateX(0);
        }
        50% {
            transform: translateY(-18px) translateX(10px);
        }
        100% {
            transform: translateY(0) translateX(0);
        }
    }

    @media (max-width: 1199px) {
        .lang-hero {
            padding: 124px 0 74px;
        }

        .lang-hero__title {
            font-size: clamp(34px, 4.6vw, 56px);
        }
    }

    @media (max-width: 991px) {
        .lang-hero {
            padding: 100px 0 62px;
        }

        .lang-hero__copy {
            text-align: center;
            margin: 0 auto;
        }

        .lang-hero__rotator-wrap {
            justify-content: center;
            width: min(100%, 460px);
            margin-left: auto;
            margin-right: auto;
        }

        .lang-hero__rotator {
            min-width: 220px;
            width: 100%;
        }

        .lang-hero__rotator-item {
            justify-content: center;
            font-size: 16px;
        }

        .lang-hero__bullet-list {
            text-align: left;
            max-width: 640px;
            margin-left: auto;
            margin-right: auto;
        }

        .lang-hero__feature-grid,
        .lang-hero__cta,
        .lang-hero__stores {
            justify-content: center;
        }

        .lang-hero__visual {
            justify-content: center;
        }

        .lang-hero__form-card {
            max-width: 100%;
        }
    }

    @media (max-width: 575px) {
        .lang-hero {
            padding: 90px 0 52px;
        }

        .lang-hero__title {
            font-size: clamp(30px, 9.2vw, 40px);
        }

        .lang-hero__lead {
            font-size: 15px;
        }

        .lang-hero__rotator-wrap {
            width: 100%;
        }

        .lang-hero__rotator-item {
            font-size: 15px;
        }

        .lang-hero__feature-grid {
            grid-template-columns: 1fr;
        }

        .lang-hero__btn {
            width: 100%;
            justify-content: center;
        }
    }

    @media (max-width: 767px) {
        .lang-hero {
            background:
                radial-gradient(520px circle at 12% 12%, rgba(246, 161, 5, 0.18), transparent 46%),
                radial-gradient(680px circle at 100% 0%, rgba(109, 196, 255, 0.16), transparent 42%),
                linear-gradient(180deg, #071a2d 0%, #0b3558 56%, #09223c 100%);
        }

        .lang-hero__bg-video-wrap {
            display: none;
        }

        .lang-hero::before {
            background:
                linear-gradient(180deg, rgba(2, 8, 18, 0.12) 0%, rgba(2, 8, 18, 0.26) 100%),
                linear-gradient(90deg, rgba(255, 255, 255, 0.03) 1px, transparent 1px),
                linear-gradient(180deg, rgba(255, 255, 255, 0.03) 1px, transparent 1px);
            background-size: 100% 100%, 34px 34px, 34px 34px;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    (() => {
        const bgVideo = document.getElementById('lang-hero-bg-video');
        if (!bgVideo) return;

        const tryPlay = () => {
            bgVideo.muted = true;
            const playPromise = bgVideo.play();
            if (playPromise && typeof playPromise.catch === 'function') {
                playPromise.catch(() => {});
            }
        };

        const onError = () => {
            const fallback = bgVideo.dataset.fallback || '';
            const currentSrc = bgVideo.currentSrc || '';
            const fallbackName = fallback.split('/').pop();
            if (!fallback || (fallbackName && currentSrc.includes(fallbackName))) return;
            bgVideo.src = fallback;
            bgVideo.load();
            tryPlay();
        };

        bgVideo.addEventListener('canplay', tryPlay, { passive: true });
        bgVideo.addEventListener('error', onError, { passive: true });
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', tryPlay, { once: true });
        } else {
            tryPlay();
        }
    })();
</script>

<script>
    (() => {
        const rotator = document.querySelector('[data-hero-rotator]');
        if (!rotator) return;

        const items = Array.from(rotator.querySelectorAll('.lang-hero__rotator-item'));
        if (items.length < 2) return;

        let index = 0;
        setInterval(() => {
            items[index].classList.remove('is-active');
            index = (index + 1) % items.length;
            items[index].classList.add('is-active');
        }, 2400);
    })();
</script>

<script>
    (() => {
        const form = document.getElementById('hero-price-form');
        if (!form) return;

        const labels = {
            title: @json(__('Pricing information request')),
            firstName: @json(__('First name')),
            lastName: @json(__('Last name')),
            email: @json(__('Email')),
            phoneType: @json(__('Telefon tipi')),
            phone: @json(__('Telefon')),
            age: @json(__('Age')),
            page: @json(__('Page')),
        };

        const getValue = (name) => {
            const field = form.querySelector(`[name="${name}"]`);
            if (!field) return '';
            if (field.type === 'radio') {
                const checked = form.querySelector(`[name="${name}"]:checked`);
                return checked ? checked.value : '';
            }
            return (field.value || '').trim();
        };

        const getPhone = () => {
            const parts = [
                getValue('phone_part_1'),
                getValue('phone_part_2'),
                getValue('phone_part_3'),
                getValue('phone_part_4'),
            ].filter(Boolean);
            return parts.join(' ');
        };

        const buildMessage = () => {
            const lines = [
                labels.title,
                `${labels.firstName}: ${getValue('first_name')}`,
                `${labels.lastName}: ${getValue('last_name')}`,
                `${labels.email}: ${getValue('email')}`,
                `${labels.phoneType}: ${getValue('phone_type')}`,
                `${labels.phone}: ${getPhone()}`,
                `${labels.age}: ${getValue('age_group') || '-'}`,
                `${labels.page}: ${window.location.href.split('#')[0]}`,
            ];
            return lines.join('\n');
        };

        const buildWhatsAppUrl = (message) => {
            const waPhone = (form.dataset.waPhone || '').trim();
            const encoded = encodeURIComponent(message);
            if (waPhone) return `https://wa.me/${waPhone}?text=${encoded}`;
            return `https://wa.me/?text=${encoded}`;
        };

        form.addEventListener('submit', (event) => {
            event.preventDefault();
            if (!form.reportValidity()) return;
            const message = buildMessage();
            window.open(buildWhatsAppUrl(message), '_blank', 'noopener,noreferrer');
        });
    })();
</script>
@endpush
@endonce
