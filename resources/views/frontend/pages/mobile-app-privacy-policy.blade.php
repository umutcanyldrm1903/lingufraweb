@extends('frontend.layouts.master')
@section('meta_title', __('Mobil Uygulama Gizlilik Politikasi') . ' || ' . ($setting?->app_name ?? config('app.name')))

@section('contents')
    @php
        $siteName = Cache::get('setting')?->site_name ?? ($setting?->app_name ?? config('app.name'));
        $supportEmail = Cache::get('setting')?->site_email ?? ($setting?->email ?? config('mail.from.address'));
        $supportPhone = Cache::get('setting')?->site_phone ?? null;
    @endphp

    <x-frontend.breadcrumb :title="__('Mobil Uygulama Gizlilik Politikasi')" :links="[
        ['url' => route('home'), 'text' => __('Home')],
        ['url' => '', 'text' => __('Mobil Uygulama Gizlilik Politikasi')],
    ]" />

    <section class="about-area-three section-py-120">
        <div class="container">
            <div class="card singUp-wrap custom-page-body policy-card">
                <div class="card-body policy-body">
                    <span class="policy-badge">MOBILE APP PRIVACY</span>
                    <h2>{{ __('Mobil Uygulama Gizlilik Politikasi') }}</h2>
                    <p class="policy-lead">
                        {{ __('Bu politika, :siteName mobil uygulamasi (iOS ve Android) icin gecerlidir. Web sitesine ait cerez ve site-ici izleme uygulamalari bu sayfadan ayridir. Web sitesiyle ilgili genel politika icin mevcut gizlilik sozlesmesini inceleyebilirsiniz.', ['siteName' => $siteName]) }}
                    </p>

                    <div class="policy-note">
                        <strong>App Store Review Note:</strong>
                        {{ __('Mobil uygulama; ad, e-posta adresi ve telefon numarasini yalnizca hesap olusturma, giris, ders rezervasyonu, destek ve hizmet sunumu amaclariyla kullanir. Bu veriler ucuncu taraf reklamcilik veya reklam amacli tracking icin kullanilmaz.') }}
                    </div>

                    <h3>{{ __('1. Veri Sorumlusu ve Iletisim') }}</h3>
                    <p>
                        {{ __('Bu mobil uygulama kapsamindaki kisisel veriler, :siteName tarafindan egitim hizmetlerinin sunulmasi, hesap yonetimi ve kullanici destegi amaclariyla islenir.', ['siteName' => $siteName]) }}
                    </p>
                    <ul>
                        <li>{{ __('Destek e-postasi') }}: <a href="mailto:{{ $supportEmail }}">{{ $supportEmail }}</a></li>
                        @if ($supportPhone)
                            <li>{{ __('Destek telefonu') }}: <a href="tel:{{ preg_replace('/\s+/', '', $supportPhone) }}">{{ $supportPhone }}</a></li>
                        @endif
                    </ul>

                    <h3>{{ __('2. Mobil Uygulamada Islenen Veriler') }}</h3>
                    <ul>
                        <li>{{ __('Hesap verileri: ad-soyad, e-posta adresi, telefon numarasi, kullanici rolu (ogrenci / egitmen).') }}</li>
                        <li>{{ __('Kimlik dogrulama verileri: giris bilgileri ve guvenli oturum verileri.') }}</li>
                        <li>{{ __('Ders ve rezervasyon verileri: satin alinan paket, ders kredileri, rezervasyonlar, canli ders zamanlari ve ders katilim kayitlari.') }}</li>
                        <li>{{ __('Mesajlasma ve egitim verileri: ogrenci-egitmen mesajlari, odevler, raporlar ve egitmen tarafindan paylasilan kutuphane materyalleri.') }}</li>
                        <li>{{ __('Teknik veriler: uygulama guvenligi, hata takibi, oturum dogrulamasi ve hizmet surekliligi icin gerekli cihaz / log verileri.') }}</li>
                        <li>{{ __('Odeme verileri: satin alinan paket, odeme durumu ve islem kayitlari. Kart bilgileri odeme kurulusunca islenir; uygulama tam kart numarasini saklamaz.') }}</li>
                    </ul>

                    <h3>{{ __('3. Verileri Hangi Amaclarla Isliyoruz?') }}</h3>
                    <ul>
                        <li>{{ __('Hesap olusturma, giris yapma ve kullanici profilini yonetme') }}</li>
                        <li>{{ __('Paket satin alma, ders kredisi tanimlama ve rezervasyon olusturma') }}</li>
                        <li>{{ __('Canli derslere katilim saglama ve ders surecini yurutme') }}</li>
                        <li>{{ __('Odev, rapor ve egitim materyallerini kullaniciya sunma') }}</li>
                        <li>{{ __('Ogrenci ve egitmen arasinda mesajlasma ve destek surecini yurutme') }}</li>
                        <li>{{ __('Guvenlik, suistimal onleme, hata tespiti ve hizmetin teknik surekliligini saglama') }}</li>
                    </ul>

                    <h3>{{ __('4. Ucuncu Taraf Hizmetler') }}</h3>
                    <p>{{ __('Mobil uygulama asagidaki hizmet saglayicilar ile entegre calisabilir:') }}</p>
                    <ul>
                        <li>{{ __('Zoom Meeting SDK / Zoom: canli derslerin uygulama icinde gerceklesmesi icin') }}</li>
                        <li>{{ __('Iyzico: ders paketlerine ait odeme islemlerinin yurutulmesi icin') }}</li>
                        <li>{{ __('Barindirma, e-posta ve teknik altyapi saglayicilari: uygulamanin temel islevlerini surdurmek icin') }}</li>
                    </ul>

                    <h3>{{ __('5. Tracking ve Reklamcilik') }}</h3>
                    <p>
                        {{ __(':siteName mobil uygulamasi, kullanici verilerini ucuncu taraf reklamcilik, capraz uygulama takibi, veri broker paylasimi veya hedefli reklam amaclariyla kullanmaz.', ['siteName' => $siteName]) }}
                    </p>
                    <ul>
                        <li>{{ __('Ad, e-posta adresi ve telefon numarasi yalnizca uygulama islevselligi ve hesap yonetimi icin kullanilir.') }}</li>
                        <li>{{ __('Mobil uygulama App Tracking Transparency izni gerektiren reklam amacli tracking faaliyeti yurutmez.') }}</li>
                        <li>{{ __('Toplanan iletisim verileri ucuncu taraf reklam aglariyla paylasilmaz.') }}</li>
                    </ul>

                    <h3>{{ __('6. Veriler Kimlige Bagli midir?') }}</h3>
                    <p>
                        {{ __('Evet. Ad, e-posta adresi ve telefon numarasi kullanicinin hesabina baglidir; cunku bunlar giris, hesap guvenligi, ders planlama ve destek sureclerinin ayrilmaz parcasidir.') }}
                    </p>

                    <h3>{{ __('7. Verileri Ne Kadar Sure Sakliyoruz?') }}</h3>
                    <p>
                        {{ __('Verileriniz, hizmet iliskisi devam ettigi surece ve ilgili hukuki yukumlulukler ile uyusmazlik yonetimi icin gerekli yasal saklama sureleri boyunca tutulur. Sure sonunda veriler silinir, yok edilir veya anonim hale getirilir.') }}
                    </p>

                    <h3>{{ __('8. Haklariniz') }}</h3>
                    <p>
                        {{ __('Yururlukteki veri koruma mevzuati kapsaminda; verilerinize erisim talep etme, duzeltme, silme, islemeyi sinirlama ve itiraz etme haklarina sahip olabilirsiniz. Talepleriniz icin bizimle iletisime gecebilirsiniz.') }}
                    </p>

                    <h3>{{ __('9. Web Sitesi Politikasiyla Iliski') }}</h3>
                    <p>
                        {{ __('Bu sayfa yalnizca mobil uygulama icindir. Web sitesine ait cerez politikasi, pazarlama tercihleri ve genel site kullanimina iliskin ayrintilar icin asagidaki genel gizlilik sozlesmesini inceleyin:') }}
                    </p>
                    <p>
                        <a href="{{ url('page/privacy-policy') }}" target="_blank" rel="noopener">
                            {{ __('Genel Gizlilik Sozlesmesi') }}
                        </a>
                    </p>

                    <p class="policy-updated">
                        {{ __('Son guncelleme') }}: 22.03.2026
                    </p>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('styles')
    <style>
        .policy-card {
            border-radius: 24px;
            border: 1px solid rgba(14, 92, 147, 0.12);
            box-shadow: 0 24px 60px rgba(15, 23, 42, 0.08);
            overflow: hidden;
        }

        .policy-body {
            padding: 40px;
        }

        .policy-badge {
            display: inline-flex;
            align-items: center;
            padding: 8px 14px;
            border-radius: 999px;
            background: rgba(243, 156, 18, 0.12);
            color: #c57b00;
            font-size: 12px;
            font-weight: 800;
            letter-spacing: 0.14em;
            margin-bottom: 14px;
        }

        .policy-lead {
            font-size: 18px;
            line-height: 1.8;
            color: var(--tg-heading-color);
            font-weight: 600;
        }

        .policy-note {
            margin: 24px 0;
            padding: 18px 20px;
            border-radius: 18px;
            background: linear-gradient(135deg, rgba(14, 92, 147, 0.08), rgba(243, 156, 18, 0.10));
            border: 1px solid rgba(14, 92, 147, 0.12);
            color: var(--tg-heading-color);
            font-weight: 600;
            line-height: 1.7;
        }

        .policy-body h2 {
            font-weight: 900;
            margin-bottom: 18px;
            color: var(--tg-heading-color);
        }

        .policy-body h3 {
            font-weight: 800;
            margin-top: 24px;
            margin-bottom: 12px;
            color: var(--tg-heading-color);
        }

        .policy-body p,
        .policy-body li {
            color: var(--tg-body-color);
            font-weight: 600;
            line-height: 1.8;
        }

        .policy-body ul {
            margin-bottom: 12px;
            padding-left: 20px;
        }

        .policy-body a {
            color: var(--tg-theme-primary);
            font-weight: 700;
        }

        .policy-updated {
            margin-top: 28px;
            padding-top: 20px;
            border-top: 1px solid rgba(14, 92, 147, 0.12);
            font-size: 14px;
            color: var(--tg-body-color);
        }

        @media (max-width: 767px) {
            .policy-body {
                padding: 24px 18px;
            }

            .policy-lead {
                font-size: 16px;
            }
        }
    </style>
@endpush
