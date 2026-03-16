@php
    $partnerBrands = collect([
        [
            'name' => 'CNN Turk',
            'url' => 'https://www.cnnturk.com/',
            'image' => asset('frontend/img/brand-logos/cnn-turk-logo.png'),
        ],
        [
            'name' => 'Beyaz TV',
            'url' => 'https://www.beyaztv.com.tr/',
            'image' => asset('frontend/img/brand-logos/beyaz-tv-tr.webp'),
        ],
        [
            'name' => 'TV8',
            'url' => 'https://www.tv8.com.tr/',
            'image' => asset('frontend/img/brand-logos/tv8-yeni-logo.png'),
        ],
    ]);
@endphp

<section class="lang-partners section-py-80" id="partners">
    <div class="container">
        <div class="row justify-content-center text-center mb-4">
            <div class="col-lg-8">
                <p class="eyebrow lang-partners__eyebrow">{{ __('Corporate References') }}</p>
                <h2 class="lang-partners__title">{{ __('Brands we work with') }}</h2>
            </div>
        </div>

        <div class="lang-partners__grid" aria-label="{{ __('Partner logos') }}">
            @foreach ($partnerBrands as $brand)
                <a href="{{ $brand['url'] }}" class="lang-partners__logo" target="_blank" rel="noopener"
                    aria-label="{{ $brand['name'] }}">
                    <img src="{{ $brand['image'] }}" alt="{{ $brand['name'] }}">
                </a>
            @endforeach
        </div>
    </div>
</section>

@push('styles')
    <style>
        .lang-partners {
            background: var(--tg-common-color-gray-8);
        }

        .lang-partners__eyebrow {
            color: var(--tg-theme-secondary);
            font-weight: 900;
            letter-spacing: .3px;
        }

        .lang-partners__title {
            margin: 0;
            color: var(--tg-heading-color);
            font-size: 28px;
            font-weight: 900;
        }

        .lang-partners__grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 20px;
        }

        .lang-partners__logo {
            min-height: 108px;
            padding: 20px 24px;
            border: 1px solid rgba(14, 92, 147, 0.12);
            border-radius: 22px;
            background:
                linear-gradient(180deg, rgba(255, 255, 255, 0.98) 0%, rgba(245, 248, 252, 0.98) 100%);
            box-shadow: 0 18px 42px rgba(15, 23, 42, 0.08);
            display: grid;
            place-items: center;
            text-decoration: none;
        }

        .lang-partners__logo img {
            max-width: 100%;
            max-height: 58px;
            object-fit: contain;
            display: block;
        }

        @media (max-width: 991px) {
            .lang-partners__title {
                font-size: 22px;
            }

            .lang-partners__grid {
                grid-template-columns: 1fr;
            }

            .lang-partners__logo {
                min-height: 96px;
            }
        }
    </style>
@endpush
