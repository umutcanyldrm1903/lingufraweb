@extends('frontend.layouts.master')
@section('meta_title', __('Eğitmen Başvurusu'). ' || ' . $setting->app_name)
@section('contents')
    <!-- breadcrumb-area -->
    <x-frontend.breadcrumb
        :title="__('Eğitmen Başvurusu')"
        :links="[
            ['url' => route('home'), 'text' => __('Home')],
            ['url' => route('become-instructor'), 'text' => __('Eğitmen Başvurusu')],
        ]"
    />
    <!-- breadcrumb-area-end -->

    <section class="instructor-apply section-py-120">
        <div class="container">
            <div class="instructor-apply__shell">
                <div class="instructor-apply__intro">
                    <span class="instructor-apply__kicker">{{ __('Eğitmen Başvurusu') }}</span>
                    <h2 class="instructor-apply__title">{{ __('Başvurunu gönder, ekibimiz en kısa sürede inceleyip sana dönüş yapsın.') }}</h2>
                    <p class="instructor-apply__lead">
                        {{ __('Deneyimini, uzmanlık alanlarını ve müsaitlik bilgini paylaşman yeterli. Uygun bulunduğunda paneline onay düşer.') }}
                    </p>
                    <div class="instructor-apply__checks">
                        <div class="instructor-apply__check"><span class="dot"></span>{{ __('Başvuru ücretsizdir ve 1-2 iş günü içinde değerlendirilir.') }}</div>
                        <div class="instructor-apply__check"><span class="dot"></span>{{ __('Uygunluk sonrası eğitim panelin açılır ve öğrenci atanır.') }}</div>
                        <div class="instructor-apply__check"><span class="dot"></span>{{ __('Bilgilerin sadece değerlendirme için kullanılır.') }}</div>
                    </div>
                </div>

                <div class="instructor-apply__card">
                    <h3 class="instructor-apply__card-title">{{ __('Eğitmen Başvuru Formu') }}</h3>
                    <form method="POST" action="{{ route('become-instructor.create') }}" class="instructor-apply__form" enctype="multipart/form-data">
                        @csrf
                        <div class="instructor-apply__grid">
                            <div class="form-grp">
                                <label>{{ __('Uzmanlık Alanı') }} <code>*</code></label>
                                <input type="text" name="expertise" value="{{ old('expertise') }}" placeholder="{{ __('Örn: Konuşma pratiği, iş İngilizcesi') }}" required>
                                <x-frontend.validation-error name="expertise" />
                            </div>
                            <div class="form-grp">
                                <label>{{ __('Deneyim') }} <code>*</code></label>
                                <select name="experience_years" required>
                                    <option value="">{{ __('Seçiniz') }}</option>
                                    @foreach (['0-1 yıl', '1-3 yıl', '3-5 yıl', '5-10 yıl', '10+ yıl'] as $opt)
                                        <option value="{{ $opt }}" @selected(old('experience_years') === $opt)>{{ __($opt) }}</option>
                                    @endforeach
                                </select>
                                <x-frontend.validation-error name="experience_years" />
                            </div>
                            <div class="form-grp">
                                <label>{{ __('Ders Dili') }} <code>*</code></label>
                                <input type="text" name="lesson_languages" value="{{ old('lesson_languages') }}" placeholder="{{ __('Örn: Türkçe, İngilizce') }}" required>
                                <x-frontend.validation-error name="lesson_languages" />
                            </div>
                            <div class="form-grp">
                                <label>{{ __('Müsaitlik') }} <code>*</code></label>
                                <input type="text" name="availability" value="{{ old('availability') }}" placeholder="{{ __('Örn: Hafta içi 18:00-22:00') }}" required>
                                <x-frontend.validation-error name="availability" />
                            </div>
                            <div class="form-grp instructor-apply__full">
                                <label>{{ __('Kendini Tanıt') }} <code>*</code></label>
                                <textarea name="bio" rows="4" placeholder="{{ __('Kısa özgeçmiş, yaklaşımın ve hedeflerin...') }}" required>{{ old('bio') }}</textarea>
                                <x-frontend.validation-error name="bio" />
                            </div>
                            <div class="form-grp">
                                <label>{{ __('LinkedIn / Web (opsiyonel)') }}</label>
                                <input type="text" name="linkedin" value="{{ old('linkedin') }}" placeholder="{{ __('https://') }}">
                                <x-frontend.validation-error name="linkedin" />
                            </div>
                            <div class="form-grp instructor-apply__full">
                                <label>{{ __('Ek Not (opsiyonel)') }}</label>
                                <textarea name="extra_information" rows="3" placeholder="{{ __('Varsa eklemek istediğin notlar...') }}">{{ old('extra_information') }}</textarea>
                                <x-frontend.validation-error name="extra_information" />
                            </div>
                        </div>

                        @if ($instructorRequestSetting?->need_certificate == 1)
                            <div class="form-grp">
                                <label>{{ __('CV / Sertifika') }} <code>*</code></label>
                                <input type="file" class="form-control" name="certificate" required>
                                <x-frontend.validation-error name="certificate" />
                            </div>
                        @else
                            <div class="form-grp">
                                <label>{{ __('CV / Sertifika (opsiyonel)') }}</label>
                                <input type="file" class="form-control" name="certificate">
                                <x-frontend.validation-error name="certificate" />
                            </div>
                        @endif

                        @if ($instructorRequestSetting?->need_identity_scan == 1)
                            <div class="form-grp">
                                <label>{{ __('Kimlik / Belge') }} <code>*</code></label>
                                <input type="file" class="form-control" name="identity_scan" required>
                                <x-frontend.validation-error name="identity_scan" />
                            </div>
                        @endif

                        @if (Cache::get('setting')->recaptcha_status === 'active')
                            <div class="form-grp mt-3">
                                <div class="g-recaptcha" data-sitekey="{{ Cache::get('setting')->recaptcha_site_key }}"></div>
                                <x-frontend.validation-error name="g-recaptcha-response" />
                            </div>
                        @endif

                        <button type="submit" class="btn btn-two arrow-btn">
                            {{ __('Başvuruyu Gönder') }}
                            <img src="{{ asset('frontend/img/icons/right_arrow.svg') }}" alt="img" class="injectable">
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('styles')
<style>
    .instructor-apply {
        --brand-primary: var(--tg-theme-primary);
        --brand-accent: var(--tg-theme-secondary);
    }
    .instructor-apply__shell {
        display: grid;
        grid-template-columns: minmax(0, 1fr) minmax(0, 1fr);
        gap: 30px;
        align-items: start;
    }
    .instructor-apply__intro {
        display: grid;
        gap: 14px;
    }
    .instructor-apply__kicker {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-weight: 900;
        letter-spacing: .08em;
        text-transform: uppercase;
        color: var(--brand-primary);
        font-size: 12px;
    }
    .instructor-apply__title {
        margin: 0;
        font-weight: 1000;
        font-size: 34px;
        color: var(--tg-heading-color);
    }
    .instructor-apply__lead {
        margin: 0;
        font-weight: 700;
        color: var(--tg-body-color);
    }
    .instructor-apply__checks {
        display: grid;
        gap: 8px;
        margin-top: 8px;
    }
    .instructor-apply__check {
        display: flex;
        align-items: center;
        gap: 10px;
        font-weight: 800;
        color: var(--tg-common-color-black-2);
    }
    .instructor-apply__check .dot {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        background: var(--brand-accent);
        box-shadow: 0 6px 14px rgba(0, 0, 0, .12);
    }
    .instructor-apply__card {
        background: #fff;
        border-radius: 20px;
        box-shadow: 0 22px 60px rgba(15, 23, 42, 0.12);
        padding: 24px;
        border: 1px solid var(--tg-border-2);
    }
    .instructor-apply__card-title {
        margin: 0 0 18px;
        font-weight: 1000;
        color: var(--tg-heading-color);
        font-size: 20px;
    }
    .instructor-apply__grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 16px;
    }
    .instructor-apply__full {
        grid-column: 1 / -1;
    }
    .instructor-apply__form .form-grp label {
        display: block;
        margin-bottom: 6px;
        font-weight: 900;
    }
    .instructor-apply__form .form-grp input,
    .instructor-apply__form .form-grp select,
    .instructor-apply__form .form-grp textarea {
        width: 100%;
        border-radius: 12px;
        border: 1px solid #e5e7eb;
        padding: 12px 14px;
        font-weight: 700;
        color: #111827;
        background: #fff;
        outline: none;
    }
    .instructor-apply__form .form-grp input:focus,
    .instructor-apply__form .form-grp select:focus,
    .instructor-apply__form .form-grp textarea:focus {
        border-color: rgba(246, 161, 5, 0.65);
        box-shadow: 0 0 0 4px rgba(246, 161, 5, .18);
    }
    @media (max-width: 991px) {
        .instructor-apply__shell {
            grid-template-columns: 1fr;
        }
        .instructor-apply__title {
            font-size: 28px;
        }
    }
    @media (max-width: 575px) {
        .instructor-apply__grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush
