@php
    $partnerBrands = ($brands ?? collect())->filter(fn($b) => !empty($b?->image))->values();
    $isMarquee = $partnerBrands->count() > 6;
@endphp

@if ($partnerBrands->count())
    <section class="lang-partners section-py-80" id="partners">
        <div class="container">
            <div class="row justify-content-center text-center mb-4">
                <div class="col-lg-8">
                    <p class="eyebrow lang-partners__eyebrow">{{ __('Corporate References') }}</p>
                    <h2 class="lang-partners__title">{{ __('Brands we work with') }}</h2>
                </div>
            </div>

            <div class="lang-partners__scroller" aria-label="{{ __('Partner logos') }}">
                <div class="lang-partners__track {{ $isMarquee ? 'is-marquee' : 'is-grid' }}">
                    @foreach ($partnerBrands as $brand)
                        <a href="{{ $brand?->url ?: 'javascript:;' }}" class="lang-partners__logo"
                            aria-label="{{ $brand?->name }}" @if ($brand?->url) target="_blank" rel="noopener" @endif>
                            <img src="{{ asset($brand?->image) }}" alt="{{ $brand?->name }}">
                        </a>
                    @endforeach

                    {{-- Duplicate for seamless marquee on large screens --}}
                    @if ($isMarquee)
                        @foreach ($partnerBrands as $brand)
                            <a href="{{ $brand?->url ?: 'javascript:;' }}" class="lang-partners__logo is-dup"
                                aria-hidden="true" tabindex="-1" @if ($brand?->url) target="_blank" rel="noopener" @endif>
                                <img src="{{ asset($brand?->image) }}" alt="">
                            </a>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
    </section>

    @push('styles')
        <style>
            .lang-partners {
                background: var(--tg-common-color-gray-8);
                --brand-primary: var(--tg-theme-primary);
                --brand-accent: var(--tg-theme-secondary);
            }

            .lang-partners__eyebrow {
                color: var(--brand-accent);
                font-weight: 900;
                letter-spacing: .3px;
            }

            .lang-partners__title {
                font-weight: 900;
                font-size: 28px;
                color: var(--tg-heading-color);
                margin: 0;
            }

            .lang-partners__scroller {
                position: relative;
                overflow: hidden;
                border-radius: 18px;
                background: rgba(255, 255, 255, 0.55);
                border: 1px solid var(--tg-border-2);
                box-shadow: 0 16px 40px rgba(0, 0, 0, 0.08);
                padding: 18px 14px;
            }

            .lang-partners__scroller::before,
            .lang-partners__scroller::after {
                content: '';
                position: absolute;
                top: 0;
                bottom: 0;
                width: 70px;
                pointer-events: none;
                z-index: 2;
            }

            .lang-partners__scroller::before {
                left: 0;
                background: linear-gradient(90deg, var(--tg-common-color-gray-8), rgba(245, 245, 244, 0));
            }

            .lang-partners__scroller::after {
                right: 0;
                background: linear-gradient(270deg, var(--tg-common-color-gray-8), rgba(245, 245, 244, 0));
            }

            .lang-partners__track {
                display: flex;
                gap: 16px;
                align-items: center;
                padding-right: 12px;
            }

            .lang-partners__track.is-marquee {
                width: max-content;
                animation: langPartnersScroll 28s linear infinite;
            }

            .lang-partners__track.is-grid {
                width: 100%;
                flex-wrap: wrap;
                justify-content: center;
                animation: none;
            }

            .lang-partners__logo {
                width: 170px;
                height: 64px;
                border-radius: 16px;
                background: var(--tg-common-color-white);
                border: 1px solid rgba(0, 0, 0, 0.06);
                box-shadow: 0 12px 30px rgba(0, 0, 0, 0.10);
                display: grid;
                place-items: center;
                padding: 10px 14px;
                text-decoration: none;
                transform: translateZ(0);
                transition: transform .2s ease, box-shadow .2s ease, border-color .2s ease;
            }

            .lang-partners__logo img {
                max-height: 34px;
                max-width: 100%;
                object-fit: contain;
                filter: grayscale(1);
                opacity: .85;
                transition: filter .2s ease, opacity .2s ease;
            }

            .lang-partners__logo:hover {
                transform: translateY(-4px);
                box-shadow: 0 16px 44px rgba(0, 0, 0, 0.14);
                border-color: rgba(0, 0, 0, 0.10);
            }

            .lang-partners__logo:hover img {
                filter: none;
                opacity: 1;
            }

            @keyframes langPartnersScroll {
                from {
                    transform: translateX(0);
                }

                to {
                    transform: translateX(-50%);
                }
            }

            @media (prefers-reduced-motion: reduce) {
                .lang-partners__track {
                    animation: none;
                }
            }

            @media (max-width: 991px) {
                .lang-partners__title {
                    font-size: 22px;
                }

                .lang-partners__track {
                    animation: none !important;
                    width: 100% !important;
                    flex-wrap: wrap;
                    justify-content: center;
                }

                .lang-partners__logo {
                    width: calc(50% - 10px);
                }

                .lang-partners__scroller::before,
                .lang-partners__scroller::after {
                    display: none;
                }
            }

            @media (max-width: 575px) {
                .lang-partners__logo {
                    width: 100%;
                }
            }
        </style>
    @endpush
@endif
