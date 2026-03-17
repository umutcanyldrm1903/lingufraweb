@php
    $socialLinks = getSocialLinks();
    $instagramUrl = $socialLinks->first(fn($link) => str($link?->link)->contains('instagram'))?->link;
    $instagramUrl = $instagramUrl ?: $socialLinks->first(fn($link) => str($link?->icon)->contains('instagram'))?->link;
    $instaCards = ($selectedInstructors ?? collect())->take(4);
@endphp

<section class="lang-community section-py-110" id="instagram">
    <div class="container">
        <div class="lang-community__shell">
            <div class="row g-4 align-items-center">
                <div class="col-lg-5">
                    <p class="lang-community__eyebrow">{{ __('Topluluk') }}</p>
                    <h2 class="lang-community__title">{{ __('Instagram\'da bizi takip et') }}</h2>
                    <p class="lang-community__lead">
                        {{ __('Ekibimizi, ders atmosferini ve gunluk Ingilizce iceriklerini Instagram\'da kesfet. Marka dunyasini daha yakindan tani.') }}
                    </p>

                    <div class="lang-community__actions">
                        <a href="{{ $instagramUrl ?: 'javascript:;' }}"
                            class="lang-community__btn {{ $instagramUrl ? '' : 'is-disabled' }}"
                            @if ($instagramUrl) target="_blank" rel="noopener" @endif>
                            {{ __('Profili ac') }}
                        </a>
                    </div>
                </div>
                <div class="col-lg-7">
                    <div class="lang-community__grid">
                        @foreach ($instaCards as $instructor)
                            <a href="{{ route('instructor-details', ['id' => $instructor->id, 'slug' => Str::slug($instructor->name)]) }}"
                                class="lang-community__card">
                                <img src="{{ asset($instructor->image) }}" alt="{{ $instructor->name }}">
                                <span class="lang-community__badge">{{ __('Online Ders') }}</span>
                                <div class="lang-community__overlay">
                                    <strong>{{ $instructor->name }}</strong>
                                    <span>{{ $instructor->job_title ?: __('Egitmen') }}</span>
                                </div>
                            </a>
                        @endforeach
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
                radial-gradient(620px circle at 16% 12%, rgba(246, 161, 5, 0.14), transparent 48%),
                linear-gradient(135deg, #0a3d65 0%, #0e5c93 48%, #0b6ead 100%);
            position: relative;
            overflow: hidden;
        }

        .lang-community::after {
            content: '';
            position: absolute;
            inset: auto -100px -120px auto;
            width: 360px;
            height: 360px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.08);
            filter: blur(10px);
        }

        .lang-community__shell {
            position: relative;
            z-index: 1;
            border: 1px solid rgba(255, 255, 255, 0.16);
            border-radius: 30px;
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
            font-weight: 700;
            max-width: 500px;
        }

        .lang-community__actions {
            margin-top: 22px;
        }

        .lang-community__btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 50px;
            padding: 0 22px;
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

        .lang-community__grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 16px;
        }

        .lang-community__card {
            position: relative;
            display: block;
            overflow: hidden;
            min-height: 210px;
            border-radius: 22px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 18px 40px rgba(0, 0, 0, 0.16);
            text-decoration: none;
        }

        .lang-community__card img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
            transition: transform .24s ease;
        }

        .lang-community__card:hover img {
            transform: scale(1.04);
        }

        .lang-community__badge {
            position: absolute;
            top: 14px;
            left: 14px;
            z-index: 2;
            padding: 7px 10px;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.92);
            color: #163b73;
            font-size: 11px;
            font-weight: 900;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .lang-community__overlay {
            position: absolute;
            inset: auto 0 0 0;
            z-index: 2;
            display: grid;
            gap: 4px;
            padding: 18px 18px 16px;
            background: linear-gradient(180deg, rgba(7, 17, 30, 0) 0%, rgba(7, 17, 30, 0.78) 60%, rgba(7, 17, 30, 0.92) 100%);
        }

        .lang-community__overlay strong {
            color: #fff;
            font-size: 18px;
            font-weight: 900;
        }

        .lang-community__overlay span {
            color: rgba(255, 255, 255, 0.82);
            font-size: 13px;
            font-weight: 700;
        }

        @media (max-width: 991px) {
            .lang-community__title {
                font-size: 32px;
            }

            .lang-community__shell {
                padding: 24px;
            }
        }

        @media (max-width: 575px) {
            .lang-community__grid {
                grid-template-columns: 1fr;
            }

            .lang-community__card {
                min-height: 240px;
            }
        }
    </style>
@endpush
