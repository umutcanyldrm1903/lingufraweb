@extends('frontend.layouts.master')
@section('meta_title', __('Teslimat ve Iade Sartlari') . ' || ' . ($setting?->app_name ?? config('app.name')))

@section('contents')
    @php
        $siteName = Cache::get('setting')?->site_name ?? ($setting?->app_name ?? config('app.name'));
        $supportEmail = Cache::get('setting')?->site_email ?? ($setting?->email ?? config('mail.from.address'));
    @endphp

    <x-frontend.breadcrumb :title="__('Teslimat ve Iade Sartlari')" :links="[
        ['url' => route('home'), 'text' => __('Home')],
        ['url' => '', 'text' => __('Teslimat ve Iade Sartlari')],
    ]" />

    <section class="about-area-three section-py-120">
        <div class="container">
            <div class="card singUp-wrap custom-page-body policy-card">
                <div class="card-body policy-body">
                    <h2>{{ __('Teslimat ve Iade Sartlari') }}</h2>
                    <p>
                        {{ __(':siteName uzerinden sunulan hizmetler dijital icerik ve online egitim hizmetleridir. Fiziksel bir urun gonderimi yapilmaz.', ['siteName' => $siteName]) }}
                    </p>

                    <h3>{{ __('Teslimat') }}</h3>
                    <ul>
                        <li>{{ __('Odeme onayi sonrasi satin aldiginiz hizmet/plan, hesabiniza tanimlanir.') }}</li>
                        <li>{{ __('Erisim, kullanici hesabiniza uzerinden saglanir ve hizmet turune gore hemen ya da planlanan takvime gore baslar.') }}</li>
                        <li>{{ __('Erisimle ilgili bir sorun yasarsaniz destek ekibimizle iletisime gecebilirsiniz.') }}</li>
                    </ul>

                    <h3>{{ __('Iptal ve Iade') }}</h3>
                    <ul>
                        <li>{{ __('Iptal ve iade talepleri; hizmetin turu, kullanim durumu ve ilgili mevzuat hukumlerine gore degerlendirilir.') }}</li>
                        <li>{{ __('Dijital icerik ve hizmetlerde, hizmetin ifasina baslanmissa cayma hakki sinirli olabilir.') }}</li>
                        <li>{{ __('Iade talebiniz icin bizimle iletisime gecerek talep formu olusturabilirsiniz.') }}</li>
                    </ul>

                    <p>
                        {{ __('Talepleriniz icin bize yazin: :email', ['email' => $supportEmail ?: __('support@site.com')]) }}
                    </p>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('styles')
    <style>
        .policy-card {
            border-radius: 18px;
            border: 1px solid rgba(14, 92, 147, 0.12);
            box-shadow: 0 18px 40px rgba(15, 23, 42, 0.08);
        }

        .policy-body h2 {
            font-weight: 900;
            margin-bottom: 18px;
            color: var(--tg-heading-color);
        }

        .policy-body h3 {
            font-weight: 800;
            margin-top: 20px;
            margin-bottom: 10px;
            color: var(--tg-heading-color);
        }

        .policy-body p,
        .policy-body li {
            color: var(--tg-body-color);
            font-weight: 600;
            line-height: 1.7;
        }

        .policy-body ul {
            margin-bottom: 14px;
        }
    </style>
@endpush
