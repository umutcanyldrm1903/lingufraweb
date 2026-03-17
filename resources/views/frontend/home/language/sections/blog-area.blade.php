@php
    $socialLinks = getSocialLinks();
    $instagramUrl = $socialLinks->first(fn($link) => str($link?->link)->contains('instagram'))?->link;
    $instagramUrl = $instagramUrl ?: $socialLinks->first(fn($link) => str($link?->icon)->contains('instagram'))?->link;
@endphp

<section class="lang-community section-py-110" id="instagram">
    <div class="container">
        <div class="lang-community__shell">
            <div class="row g-4 align-items-center">
                <div class="col-lg-5">
                    <p class="lang-community__eyebrow">{{ __('Topluluk') }}</p>
                    <h2 class="lang-community__title">{{ __('Instagram ve topluluk akışımızı keşfet') }}</h2>
                    <p class="lang-community__lead">
                        {{ __('Kısa ipuçları, ders atmosferi, kampanya duyuruları ve günlük İngilizce içerikleri burada ayrı bir sosyal vitrin olarak yer alır.') }}
                    </p>

                    <div class="lang-community__bullets">
                        <div class="lang-community__bullet">
                            <i class="fas fa-bolt"></i>
                            <span>{{ __('Günlük İngilizce mini içerikleri') }}</span>
                        </div>
                        <div class="lang-community__bullet">
                            <i class="fas fa-video"></i>
                            <span>{{ __('Ders anlarından kısa kesitler') }}</span>
                        </div>
                        <div class="lang-community__bullet">
                            <i class="fas fa-bell"></i>
                            <span>{{ __('Yeni paketler ve canlı duyurular') }}</span>
                        </div>
                    </div>

                    <div class="lang-community__actions">
                        <a href="{{ $instagramUrl ?: 'javascript:;' }}"
                            class="lang-community__btn {{ $instagramUrl ? '' : 'is-disabled' }}"
                            @if ($instagramUrl) target="_blank" rel="noopener" @endif>
                            {{ __('Instagram profilini aç') }}
                        </a>
                    </div>
                </div>

                <div class="col-lg-7">
                    <div class="lang-community__showcase">
                        <div class="lang-community__phone">
                            <div class="lang-community__phone-top">
                                <span>{{ __('LinguFranca') }}</span>
                                <i class="fab fa-instagram"></i>
                            </div>
                            <div class="lang-community__phone-hero">
                                <strong>{{ __('Topluluk akışı') }}</strong>
                                <p>{{ __('Derslerden kareler, eğitici kısa içerikler ve güncel duyurular tek bir yerde.') }}</p>
                            </div>
                            <div class="lang-community__phone-tags">
                                <span>{{ __('Reels') }}</span>
                                <span>{{ __('Story') }}</span>
                                <span>{{ __('Canlı yayın') }}</span>
                            </div>
                        </div>

                        <div class="lang-community__cards">
                            <article class="lang-community__card">
                                <span>{{ __('01') }}</span>
                                <strong>{{ __('İpucu serileri') }}</strong>
                                <p>{{ __('Telaffuz, kelime ve konuşma odaklı kısa paylaşımlar.') }}</p>
                            </article>
                            <article class="lang-community__card">
                                <span>{{ __('02') }}</span>
                                <strong>{{ __('Marka atmosferi') }}</strong>
                                <p>{{ __('Ders düzeni, ekip kültürü ve öğrenci deneyimi daha görünür olur.') }}</p>
                            </article>
                            <article class="lang-community__card">
                                <span>{{ __('03') }}</span>
                                <strong>{{ __('Hızlı yönlendirme') }}</strong>
                                <p>{{ __('Sosyal alan, öğretmen videolarından ayrı tutulur ve kullanıcıyı karıştırmaz.') }}</p>
                            </article>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@push('styles')
    <style>
        .lang-community {
            background:
                radial-gradient(620px circle at 14% 10%, rgba(246, 161, 5, 0.14), transparent 48%),
                linear-gradient(135deg, #0a3d65 0%, #0e5c93 48%, #0b6ead 100%);
            position: relative;
            overflow: hidden;
        }

        .lang-community::after {
            content: '';
            position: absolute;
            inset: auto -120px -140px auto;
            width: 380px;
            height: 380px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.08);
            filter: blur(12px);
        }

        .lang-community__shell {
            position: relative;
            z-index: 1;
            border: 1px solid rgba(255, 255, 255, 0.16);
            border-radius: 32px;
            padding: 34px;
            background: rgba(255, 255, 255, 0.06);
            box-shadow: 0 26px 70px rgba(0, 0, 0, 0.18);
            backdrop-filter: blur(12px);
        }

        .lang-community__eyebrow {
            margin: 0 0 12px;
            color: rgba(255, 255, 255, 0.76);
            font-size: 12px;
            font-weight: 900;
            letter-spacing: 0.22em;
            text-transform: uppercase;
        }

        .lang-community__title {
            margin: 0 0 12px;
            color: #fff;
            font-size: 40px;
            line-height: 1.04;
            font-weight: 1000;
        }

        .lang-community__lead {
            margin: 0;
            color: rgba(255, 255, 255, 0.9);
            font-size: 16px;
            line-height: 1.8;
            font-weight: 700;
            max-width: 500px;
        }

        .lang-community__bullets {
            display: grid;
            gap: 12px;
            margin-top: 24px;
        }

        .lang-community__bullet {
            display: flex;
            gap: 12px;
            align-items: center;
            color: #fff;
            font-size: 15px;
            font-weight: 800;
        }

        .lang-community__bullet i {
            width: 36px;
            height: 36px;
            display: inline-grid;
            place-items: center;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.14);
            color: #fff;
            flex: 0 0 auto;
        }

        .lang-community__actions {
            margin-top: 24px;
        }

        .lang-community__btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 52px;
            padding: 0 24px;
            border-radius: 999px;
            border: 1px solid rgba(255, 255, 255, 0.28);
            background: rgba(255, 255, 255, 0.12);
            color: #fff;
            font-size: 12px;
            font-weight: 1000;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            text-decoration: none;
            box-shadow: 0 16px 34px rgba(0, 0, 0, 0.16);
            transition: transform .18s ease, background .18s ease, color .18s ease;
        }

        .lang-community__btn:hover {
            transform: translateY(-2px);
            background: #fff;
            color: #0e5c93;
        }

        .lang-community__btn.is-disabled {
            opacity: 0.55;
            pointer-events: none;
        }

        .lang-community__showcase {
            display: grid;
            grid-template-columns: minmax(0, 320px) minmax(0, 1fr);
            gap: 18px;
            align-items: center;
        }

        .lang-community__phone {
            padding: 18px;
            border-radius: 28px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            background: linear-gradient(180deg, rgba(255, 255, 255, 0.94) 0%, rgba(238, 246, 255, 0.94) 100%);
            box-shadow: 0 20px 44px rgba(0, 0, 0, 0.18);
        }

        .lang-community__phone-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 18px;
            color: #0b3f6c;
            font-size: 14px;
            font-weight: 900;
        }

        .lang-community__phone-hero {
            padding: 18px;
            border-radius: 22px;
            background: linear-gradient(135deg, #0e5c93 0%, #1a7fc5 100%);
            color: #fff;
        }

        .lang-community__phone-hero strong {
            display: block;
            font-size: 22px;
            font-weight: 1000;
            line-height: 1.08;
            margin-bottom: 8px;
        }

        .lang-community__phone-hero p {
            margin: 0;
            font-size: 14px;
            line-height: 1.7;
            font-weight: 700;
            color: rgba(255, 255, 255, 0.9);
        }

        .lang-community__phone-tags {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 16px;
        }

        .lang-community__phone-tags span {
            display: inline-flex;
            align-items: center;
            min-height: 34px;
            padding: 0 12px;
            border-radius: 999px;
            background: #eef5fb;
            color: #0b3f6c;
            font-size: 12px;
            font-weight: 900;
        }

        .lang-community__cards {
            display: grid;
            gap: 16px;
        }

        .lang-community__card {
            padding: 20px;
            border-radius: 22px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            background: rgba(255, 255, 255, 0.12);
            box-shadow: 0 18px 40px rgba(0, 0, 0, 0.12);
        }

        .lang-community__card span {
            display: inline-flex;
            margin-bottom: 8px;
            color: rgba(255, 255, 255, 0.65);
            font-size: 12px;
            font-weight: 900;
            letter-spacing: 0.14em;
        }

        .lang-community__card strong {
            display: block;
            color: #fff;
            font-size: 20px;
            font-weight: 900;
            margin-bottom: 6px;
        }

        .lang-community__card p {
            margin: 0;
            color: rgba(255, 255, 255, 0.82);
            font-size: 14px;
            line-height: 1.7;
            font-weight: 700;
        }

        @media (max-width: 991px) {
            .lang-community__title {
                font-size: 32px;
            }

            .lang-community__shell {
                padding: 24px;
            }

            .lang-community__showcase {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 575px) {
            .lang-community__title {
                font-size: 28px;
            }
        }
    </style>
@endpush
