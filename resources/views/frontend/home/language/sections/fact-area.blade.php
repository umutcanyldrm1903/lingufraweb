<section class="lang-stats">
    <div class="container">
        <div class="lang-stats__wrap">
            <div class="lang-stats__item">
                <div class="lang-stats__number">+{{ $counter?->global_content?->total_student_count ?? 20000 }}</div>
                <p class="lang-stats__label">{{ __('Students') }}</p>
            </div>
            <div class="lang-stats__item">
                <div class="lang-stats__number">+{{ $counter?->global_content?->total_instructor_count ?? 15000000 }}</div>
                <p class="lang-stats__label">{{ __('Instructor') }}</p>
            </div>
            <div class="lang-stats__item">
                <div class="lang-stats__number">+{{ $counter?->global_content?->total_course_count ?? 6 }}</div>
                <p class="lang-stats__label">{{ __('Years of experience') }}</p>
            </div>
        </div>
    </div>
</section>

@push('styles')
<style>
    :root{
        --brand-primary: var(--tg-theme-primary);
        --brand-accent: var(--tg-theme-secondary);
    }
    .lang-stats{background:transparent;padding:0 0 34px;margin-top:-46px;position:relative;z-index:5;}
    .lang-stats__wrap{display:flex;align-items:center;justify-content:center;gap:50px;flex-wrap:wrap;position:relative;background:rgba(255,255,255,0.88);border:1px solid rgba(255,255,255,0.55);border-radius:22px;padding:18px 16px;box-shadow:0 22px 60px rgba(0,0,0,0.14);backdrop-filter:blur(10px);}
    .lang-stats__wrap::after,.lang-stats__wrap::before{content:'';position:absolute;top:50%;transform:translateY(-50%);width:22px;height:22px;border-radius:50%;background:rgba(246,161,5,0.18);opacity:0.9;}
    .lang-stats__wrap::before{left:18px;}
    .lang-stats__wrap::after{right:18px;}
    .lang-stats__item{padding:10px 26px;text-align:center;}
    .lang-stats__number{font-weight:900;font-size:28px;color:var(--brand-primary);letter-spacing:0.3px;}
    .lang-stats__label{margin:4px 0 0;font-weight:800;color:#1f3b57;letter-spacing:0.2px;}
    @media(max-width:767px){.lang-stats{margin-top:-28px;}.lang-stats__wrap{gap:24px;}}
</style>
@endpush
