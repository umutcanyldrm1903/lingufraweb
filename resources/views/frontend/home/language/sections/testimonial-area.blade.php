<section class="lang-testimonial section-pb-130">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-7 text-center">
                <p class="eyebrow text-white">{{ __('Read Our Reviews!') }}</p>
                <h2 class="lang-testimonial__title text-white">{{ __('Real reviews, real progress') }}</h2>
                <p class="lang-testimonial__lead text-white">{{ __('Shared from lesson experiences') }}</p>
            </div>
        </div>
        <div class="row g-4 mt-3">
            @foreach (($testimonials ?? collect())->take(3) as $testimonial)
                <div class="col-lg-4 col-md-6">
                    <div class="lang-testimonial__card">
                        <div class="lang-testimonial__stars">
                            @for ($i = 0; $i < $testimonial->rating; $i++)
                                <i class="fas fa-star"></i>
                            @endfor
                        </div>
                        <p class="lang-testimonial__text">{{ $testimonial?->comment }}</p>
                        <div class="lang-testimonial__person">
                            <div>
                                <h5>{{ $testimonial?->name }}</h5>
                                <span>{{ $testimonial?->designation }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

@push('styles')
<style>
    .lang-testimonial {
        background: var(--tg-theme-primary);
        position: relative;
        overflow: hidden;
        padding-top: 110px;
        --brand-primary: var(--tg-theme-primary);
        --brand-accent: var(--tg-theme-secondary);
        --brand-dark: var(--tg-common-color-dark);
    }

    .lang-testimonial::before {
        content: '';
        position: absolute;
        top: -140px;
        right: -160px;
        width: 420px;
        height: 420px;
        border-radius: 50%;
        background: var(--brand-dark);
        opacity: 0.22;
    }

    .lang-testimonial::after {
        content: '';
        position: absolute;
        bottom: -160px;
        left: -180px;
        width: 460px;
        height: 460px;
        border-radius: 50%;
        background: var(--brand-accent);
        opacity: 0.12;
    }

    .lang-testimonial .container {
        position: relative;
        z-index: 2;
    }

    .lang-testimonial__title {
        font-weight: 900;
        font-size: 34px;
        margin-bottom: 8px;
    }

    .lang-testimonial__lead {
        color: rgba(255, 255, 255, 0.9);
        font-weight: 700;
    }

    .lang-testimonial__card {
        background: rgba(255, 255, 255, 0.98);
        border: 1px solid rgba(255, 255, 255, 0.25);
        border-radius: 18px;
        padding: 20px;
        box-shadow: 0 18px 46px rgba(0, 0, 0, 0.18);
        height: 100%;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        backdrop-filter: blur(8px);
    }

    .lang-testimonial__card:hover {
        transform: translateY(-6px);
        box-shadow: 0 22px 62px rgba(0, 0, 0, 0.22);
    }

    .lang-testimonial__stars {
        color: var(--brand-accent);
        margin-bottom: 10px;
    }

    .lang-testimonial__text {
        font-size: 15px;
        color: var(--tg-common-color-black-2);
        margin-bottom: 16px;
        font-weight: 600;
        min-height: 78px;
    }

    .lang-testimonial__person {
        display: flex;
        align-items: center;
        gap: 0;
    }

    .lang-testimonial__person h5 {
        margin: 0;
        font-weight: 900;
        color: var(--tg-common-color-black-2);
    }

    .lang-testimonial__person span {
        font-size: 13px;
        color: var(--tg-body-color);
        font-weight: 800;
    }

    @media (max-width: 991px) {
        .lang-testimonial {
            padding-top: 80px;
        }

        .lang-testimonial__title {
            font-size: 26px;
        }
    }
</style>
@endpush
