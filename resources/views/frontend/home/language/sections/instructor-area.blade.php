<section class="lang-instructors section-py-100">
    <div class="container">
        <div class="row justify-content-center text-center mb-4">
            <div class="col-lg-8">
                <p class="eyebrow lang-instructors__eyebrow">{{ __('Instructors') }}</p>
                <h2 class="lang-instructors__title">{{ __('Choose your favorite instructor and plan your lesson') }}</h2>
            </div>
        </div>
        <div class="lang-instructors__list">
            @foreach ($selectedInstructors as $index => $instructor)
                @break($index === 5)
                <div class="lang-instructors__card">
                    <div class="lang-instructors__avatar">
                        <img src="{{ asset($instructor->image) }}" alt="{{ $instructor->first_name }}">
                        <span class="lang-instructors__badge">{{ __('Online Lesson') }}</span>
                    </div>
                    <h4>{{ $instructor->first_name }}</h4>
                    <p class="lang-instructors__role">{{ $instructor->job_title }}</p>
                </div>
            @endforeach
        </div>

        @if (Route::has('all-instructors'))
            <div class="text-center mt-4">
                <a href="{{ route('all-instructors') }}" class="btn lang-instructors__more">{{ __('View more') }}</a>
            </div>
        @endif
    </div>
</section>

@push('styles')
<style>
    :root{
        --brand-primary: var(--tg-theme-primary);
        --brand-accent: var(--tg-theme-secondary);
    }
    .lang-instructors{background:#e6eef7;}
    .lang-instructors__eyebrow{color:var(--brand-accent);font-weight:900;letter-spacing:0.4px;}
    .lang-instructors__title{font-weight:900;font-size:30px;color:#0e1a2c;}
    .lang-instructors__list{display:flex;gap:18px;justify-content:center;flex-wrap:wrap;}
    .lang-instructors__card{background:#f7fbff;border:2px solid #d5e4f3;border-radius:16px;padding:16px;width:175px;text-align:center;box-shadow:0 12px 28px rgba(0,0,0,0.08);transition:transform 0.2s ease, box-shadow 0.2s ease;}
    .lang-instructors__card:hover{transform:translateY(-4px);box-shadow:0 18px 38px rgba(0,0,0,0.12);}
    .lang-instructors__avatar{position:relative;display:inline-block;margin-bottom:10px;}
    .lang-instructors__avatar img{width:90px;height:90px;object-fit:cover;border-radius:50%;border:6px solid #fff;box-shadow:0 10px 24px rgba(0,0,0,0.12);}
    .lang-instructors__badge{position:absolute;bottom:-12px;left:50%;transform:translateX(-50%);background:var(--brand-accent);color:#1c1c1c;font-weight:800;font-size:11px;padding:4px 10px;border-radius:12px;box-shadow:0 8px 20px rgba(0,0,0,0.16);text-transform:uppercase;letter-spacing:0.4px;}
    .lang-instructors__card h4{margin:14px 0 4px;font-weight:800;font-size:16px;color:#0e1a2c;}
    .lang-instructors__role{margin:0;color:#345372;font-size:13px;font-weight:700;}
    .lang-instructors__more{border-radius:12px;font-weight:900;padding:12px 18px;background:var(--brand-accent);border-color:var(--brand-accent);color:var(--tg-common-color-black-3);}
    .lang-instructors__more:hover{background:var(--brand-primary);border-color:var(--brand-primary);color:#fff;}
    @media(max-width:991px){.lang-instructors__title{font-size:24px;}.lang-instructors__card{width:160px;}}
    @media(max-width:575px){.lang-instructors__card{width:47%;}}
</style>
@endpush
