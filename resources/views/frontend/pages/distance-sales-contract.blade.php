@extends('frontend.layouts.master')
@section('meta_title', __('Mesafeli Satış Sözleşmesi') . ' || ' . ($setting?->app_name ?? config('app.name')))

@section('contents')
    @php
        $siteName = Cache::get('setting')?->site_name ?? ($setting?->app_name ?? config('app.name'));
        $siteAddress = Cache::get('setting')?->site_address ?? __('Adres bilgisi');
        $supportEmail = Cache::get('setting')?->site_email ?? ($setting?->email ?? config('mail.from.address'));
        $supportPhone = Cache::get('setting')?->site_phone ?? __('Telefon bilgisi');
    @endphp

    <x-frontend.breadcrumb :title="__('Mesafeli Satış Sözleşmesi')" :links="[
        ['url' => route('home'), 'text' => __('Home')],
        ['url' => '', 'text' => __('Mesafeli Satış Sözleşmesi')],
    ]" />

    <section class="about-area-three section-py-120">
        <div class="container">
            <div class="card singUp-wrap custom-page-body policy-card">
                <div class="card-body policy-body">
                    <h2>{{ __('Mesafeli Satış Sözleşmesi') }}</h2>
                    <p>{{ __('Bu sözleşme, :siteName üzerinden sunulan hizmetlere ilişkin olarak satıcı ve alıcı arasında elektronik ortamda kurulmuştur.', ['siteName' => $siteName]) }}</p>

                    <h3>{{ __('1. Taraflar') }}</h3>
                    <p><strong>{{ __('Satıcı') }}:</strong> {{ $siteName }}</p>
                    <p><strong>{{ __('Adres') }}:</strong> {{ $siteAddress }}</p>
                    <p><strong>{{ __('E-posta') }}:</strong> {{ $supportEmail ?: __('support@site.com') }}</p>
                    <p><strong>{{ __('Telefon') }}:</strong> {{ $supportPhone }}</p>
                    <p><strong>{{ __('Alıcı') }}:</strong> {{ __('Hizmeti satın alan kullanıcı') }}</p>

                    <h3>{{ __('2. Konu') }}</h3>
                    <p>{{ __('İşbu sözleşmenin konusu, alıcının :siteName üzerinden satın aldığı dijital içerik ve/veya online eğitim hizmetlerinin satışı ve ifasıdır.', ['siteName' => $siteName]) }}</p>

                    <h3>{{ __('3. Hizmet Bilgileri') }}</h3>
                    <ul>
                        <li>{{ __('Hizmetin türü, kapsamı ve kullanım süresi, satın alma sırasında belirtilen paket/plan detaylarında yer alır.') }}</li>
                        <li>{{ __('Hizmet bedeli ve ödeme yöntemi, ödeme ekranında alıcı tarafından onaylanır.') }}</li>
                    </ul>

                    <h3>{{ __('4. Ödeme ve İfa') }}</h3>
                    <ul>
                        <li>{{ __('Ödeme onaylandıktan sonra hizmet, alıcının hesabına tanımlanır ve erişime açılır.') }}</li>
                        <li>{{ __('Satıcı, teknik zorunluluklar veya mücbir sebepler halinde ifayı geciktirebilir; bu durumda alıcı bilgilendirilir.') }}</li>
                    </ul>

                    <h3>{{ __('5. Cayma Hakkı') }}</h3>
                    <ul>
                        <li>{{ __('Dijital içerik ve hizmetlerde, ifaya başlanmışsa cayma hakkı sınırlı olabilir.') }}</li>
                        <li>{{ __('Cayma hakkı ve iade talepleri, ilgili mevzuat hükümlerine göre değerlendirilir.') }}</li>
                    </ul>

                    <h3>{{ __('6. Uyuşmazlıkların Çözümü') }}</h3>
                    <p>{{ __('İşbu sözleşmeden doğabilecek uyuşmazlıklarda, Ticaret Bakanlığı tarafından ilan edilen parasal sınırlar dâhilinde Tüketici Hakem Heyetleri ve Tüketici Mahkemeleri yetkilidir.') }}</p>

                    <h3>{{ __('7. Yürürlük') }}</h3>
                    <p>{{ __('Alıcı, :siteName üzerinden sipariş oluşturup ödeme yaptığında işbu sözleşme hükümlerini kabul etmiş sayılır.', ['siteName' => $siteName]) }}</p>
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
