@extends('frontend.layouts.master')

@section('hide_public_header', '1')
@section('hide_public_footer', '1')

<!-- meta -->
@section('meta_title', __('Student Dashboard'))
<!-- end meta -->

@section('contents')
    <section class="student-panel">
        <div class="sp-hero"></div>
        <div class="container">
            <div class="sp-header">
                <div class="sp-profile">
                    <div class="sp-avatar">
                        <img src="{{ asset(auth()->user()->image) }}" alt="img">
                    </div>
                    <div>
                        <h4 class="sp-name">{{ auth()->user()->name }}</h4>
                        <p class="sp-mail">{{ auth()->user()->email }}</p>
                        @if(auth()->user()->phone)
                        <p class="sp-phone">{{ auth()->user()->phone }}</p>
                        @endif
                    </div>
                </div>
                <div class="sp-actions">
                    @if (instructorStatus() == 'approved')
                        <a href="{{ route('instructor.dashboard') }}" class="sp-btn sp-btn-dark">{{ __('Instructor Dashboard') }}</a>
                    @endif
                    <div class="sp-lang" role="navigation" aria-label="{{ __('Language') }}">
                        <a href="{{ route('set-language', ['code' => 'tr']) }}" class="sp-lang__btn {{ app()->getLocale() === 'tr' ? 'is-active' : '' }}">TR</a>
                        <a href="{{ route('set-language', ['code' => 'en']) }}" class="sp-lang__btn {{ app()->getLocale() === 'en' ? 'is-active' : '' }}">EN</a>
                    </div>
                    <a href="{{ route('student.setting.index') }}" class="sp-btn sp-btn-light">{{ __('Profile') }}</a>
                    <a
                        href="{{ route('logout') }}"
                        class="sp-btn sp-btn-light"
                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                    >{{ __('Logout') }}</a>
                </div>
            </div>

            @php
                $currentPlan = null;
                $assignedInstructorName = null;
                $studentPhoneDigits = preg_replace('/\D+/', '', (string) (auth()->user()?->phone ?? ''));

                if (Schema::hasTable('user_plans')) {
                    $currentPlan = \App\Models\UserPlan::query()->currentForUser((int) auth()->id())->first();
                    if (!empty($currentPlan?->assigned_instructor_id)) {
                        $assignedInstructorName = DB::table('users')->where('id', $currentPlan->assigned_instructor_id)->value('name');
                    }
                }

                $planKey = trim((string) ($currentPlan?->plan_key ?? ''));
                $lessonsRemaining = (int) ($currentPlan?->lessons_remaining ?? 0);
                $showTrialButton = ($planKey === '' && $lessonsRemaining <= 0);
            @endphp
            <div class="sp-planbar">
                <div class="sp-planbar__items">
                    <span class="sp-planbar__item">
                        <i class="fas fa-id-badge"></i>
                        {{ __('Plan') }}: <strong>{{ $currentPlan?->plan_title ?: __('No Plan') }}</strong>
                    </span>
                    <span class="sp-planbar__item">
                        <i class="fas fa-book"></i>
                        {{ __('Credits') }}: <strong>{{ $currentPlan?->lessons_remaining ?? 0 }}</strong> {{ __('Lessons') }}
                    </span>
                    <span class="sp-planbar__item">
                        <i class="fas fa-undo"></i>
                        {{ __('Cancellation Right') }}: <strong>{{ $currentPlan?->cancel_remaining ?? 0 }}</strong> {{ __('Lessons') }}
                    </span>
                    <span class="sp-planbar__item">
                        <i class="fas fa-user-tie"></i>
                        {{ __('Instructor') }}: <strong>{{ $assignedInstructorName ? (\Illuminate\Support\Str::before($assignedInstructorName, ' ') ?: $assignedInstructorName) : __('Not Assigned') }}</strong>
                    </span>
                </div>
                <div class="sp-planbar__actions">
                    <a href="{{ route('student.dashboard') }}#student-plans" class="sp-btn sp-btn-outline">{{ __('Packages') }}</a>
                    <a href="{{ route('student.invite') }}" class="sp-btn sp-btn-outline sp-btn-outline--accent">{{ __('Get Free Lessons') }}</a>
                    @if ($showTrialButton)
                        @if ($studentPhoneDigits === '')
                            <a href="{{ route('student.setting.index') }}" class="sp-btn sp-btn-outline">{{ __('Schedule Trial Lesson') }}</a>
                        @else
                            <button type="button" class="sp-btn sp-btn-outline" data-open-trial-modal>{{ __('Schedule Trial Lesson') }}</button>
                        @endif
                    @endif
                </div>
            </div>

            <div class="sp-nav">
                <a class="{{ request()->is('student/dashboard') ? 'is-active' : '' }}" href="{{ route('student.dashboard') }}">{{ __('Home') }}</a>
                <a class="{{ request()->is('student/instructors') ? 'is-active' : '' }}" href="{{ route('student.instructors') }}">{{ __('Instructors') }}</a>
                <a class="{{ Route::is('student.messages.*') ? 'is-active' : '' }}" href="{{ route('student.messages.index') }}">{{ __('Messages') }}</a>
                <a class="{{ Route::is('student.enrolled-courses') ? 'is-active' : '' }}" href="{{ route('student.enrolled-courses') }}">{{ __('My Lessons') }}</a>
                <a class="{{ Route::is('student.homeworks.*') ? 'is-active' : '' }}" href="{{ route('student.homeworks.index') }}">{{ __('Homeworks') }}</a>
                <a class="{{ Route::is('student.support.*') ? 'is-active' : '' }}" href="{{ route('student.support.index') }}">{{ __('Support') }}</a>
                <a class="{{ Route::is('student.guide.*') ? 'is-active' : '' }}" href="{{ route('student.guide.index') }}">{{ __('User Guide') }}</a>
                <a class="{{ Route::is('student.library.*') ? 'is-active' : '' }}" href="{{ route('student.library.index') }}">{{ __('Library') }}</a>
                <a class="{{ Route::is('student.reports.*') ? 'is-active' : '' }}" href="{{ route('student.reports.index') }}">{{ __('My Reports') }}</a>
            </div>

            <div class="sp-card">
                @yield('dashboard-contents')
            </div>
        </div>
    </section>

    @php
        $whatsappLeadPhone = preg_replace('/\D+/', '', (string) config('app.whatsapp_lead_phone', ''));
        $trialMessage = "Hello, I would like to request a trial lesson.\n"
            . 'Name: ' . (auth()->user()?->name ?? '') . "\n"
            . 'Phone: ' . (auth()->user()?->phone ?? '') . "\n"
            . 'Email: ' . (auth()->user()?->email ?? '') . "\n"
            . 'User ID: ' . (auth()->id() ?? '');
        $trialWhatsAppUrl = $whatsappLeadPhone !== ''
            ? 'https://wa.me/' . $whatsappLeadPhone . '?text=' . rawurlencode($trialMessage)
            : '';
    @endphp

    <form id="trial-request-form" action="{{ route('student.trial.request') }}" method="POST" class="d-none">
        @csrf
    </form>

    <div class="ce-modal" id="trial-modal" aria-hidden="true">
        <div class="ce-modal__backdrop" data-close-trial-modal></div>
        <div class="ce-modal__panel" role="dialog" aria-modal="true" aria-labelledby="trial-modal-title">
            <div class="ce-modal__title" id="trial-modal-title">{{ __('Schedule Trial Lesson') }}</div>
            <div class="ce-modal__text">
                {{ __('You are about to request a one-time free trial lesson from our support team!') }}
            </div>
            <div class="ce-modal__actions">
                <button type="button" class="ce-modal__cancel" data-close-trial-modal>{{ __('Cancel') }}</button>
                <button type="button" class="ce-modal__confirm" id="trial-modal-confirm">{{ __('Confirm') }}</button>
            </div>
        </div>
    </div>

    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
        @csrf
    </form>
@endsection

@push('styles')
<style>
    :root{
        --student-brand:#f6a105;
        --student-dark:#0e5c93;
    }
    body{
        background:
            radial-gradient(900px 420px at 15% 0%, rgba(14,92,147,.14), transparent 65%),
            radial-gradient(750px 360px at 90% 8%, rgba(246,161,5,.14), transparent 55%),
            linear-gradient(180deg, #f7fbff 0%, #f3f7fb 100%);
    }
    .student-panel{min-height:100vh;background:transparent;padding:0 0 90px;}
    .sp-hero{
        background: linear-gradient(90deg, rgba(14,92,147,.10), rgba(246,161,5,.08));
        height:140px;
        width:100%;
    }
    .sp-header{background:#fff;border-radius:16px;box-shadow:0 14px 32px rgba(0,0,0,0.08);padding:16px;margin-top:-70px;display:flex;justify-content:space-between;align-items:center;gap:14px;flex-wrap:wrap;}
    .sp-profile{display:flex;align-items:center;gap:12px;}
    .sp-avatar{width:72px;height:72px;border-radius:50%;overflow:hidden;border:4px solid var(--student-brand);box-shadow:0 8px 20px rgba(0,0,0,0.08);}
    .sp-avatar img{width:100%;height:100%;object-fit:cover;}
    .sp-name{margin:0;font-weight:900;color:#1c1c1c;}
    .sp-mail,.sp-phone{margin:0;color:#555;font-weight:700;}
    .sp-actions{display:flex;gap:10px;}
    .sp-lang{display:flex;gap:6px;align-items:center;}
    .sp-lang__btn{
        display:inline-flex;
        align-items:center;
        justify-content:center;
        height:40px;
        min-width:44px;
        padding:0 10px;
        border-radius:12px;
        border:1px solid #e5e7eb;
        background:#fff;
        color:#111827;
        font-weight:900;
        text-decoration:none;
    }
    .sp-lang__btn:hover{background:#f9fafb;color:#111827;border-color:#d1d5db;}
    .sp-lang__btn.is-active{
        border-color:var(--student-brand);
        background:#fff7e6;
        color:#111827;
    }
    .sp-btn{border-radius:12px;padding:10px 14px;font-weight:800;display:inline-block;}
    button.sp-btn{appearance:none;-webkit-appearance:none;background:transparent;}
    .sp-btn-dark{background:var(--student-dark);color:#fff;border:1px solid var(--student-dark);}
    .sp-btn-dark:hover{opacity:.9;color:#fff;}
    .sp-btn-light{background:#fff;color:#111827;border:1px solid #e5e7eb;}
    .sp-btn-light:hover{background:#f9fafb;color:#111827;border-color:#d1d5db;}
    .sp-btn-outline{background:transparent;color:#111827;border:1px solid #d1d5db;}
    .sp-btn-outline:hover{background:#f9fafb;color:#111827;border-color:#9ca3af;}
    .sp-btn-outline--accent{border-color:var(--student-brand);color:var(--student-brand);background:#fff;}
    .sp-btn-outline--accent:hover{background:#fff7e6;border-color:var(--student-brand);color:var(--student-brand);}

    .sp-planbar{margin-top:12px;background:#fff;border-radius:16px;box-shadow:0 14px 32px rgba(0,0,0,0.08);padding:12px 14px;display:flex;justify-content:space-between;align-items:center;gap:12px;flex-wrap:wrap;}
    .sp-planbar__items{display:flex;gap:12px;flex-wrap:wrap;align-items:center;}
    .sp-planbar__item{display:flex;align-items:center;gap:8px;background:#f9fafb;border:1px solid #e5e7eb;border-radius:999px;padding:8px 10px;font-weight:900;color:#111827;}
    .sp-planbar__item i{color:var(--student-dark);}
    .sp-planbar__item strong{font-weight:1000;}
    .sp-planbar__actions{display:flex;gap:10px;flex-wrap:wrap;align-items:center;}

    .sp-nav{margin:18px 0;display:flex;gap:16px;flex-wrap:wrap;align-items:center;}
    .sp-nav a{padding:10px 12px;border-radius:12px;font-weight:800;color:#1c1c1c;text-decoration:none;border:1px solid transparent;}
    .sp-nav a.is-active{background:var(--student-brand);border-color:var(--student-brand);color:#1c1c1c;}
    .sp-nav a:hover{background:#ffe7b3;border-color:var(--student-brand);color:#1c1c1c;}

    .sp-card{background:#fff;border-radius:18px;box-shadow:0 16px 36px rgba(0,0,0,0.08);padding:18px;}
    .dashboard__content-wrap,
    .dashboard__content-wrap-two{box-shadow:none;border:0;padding:0;}
    .dashboard__content-title .title{font-weight:900;color:#1c1c1c;}
    .dashboard__review-table table thead th{color:#1c1c1c;font-weight:900;}

    .ce-modal{position:fixed;inset:0;display:none;align-items:center;justify-content:center;z-index:9999;padding:16px;}
    .ce-modal.is-open{display:flex;}
    .ce-modal__backdrop{position:absolute;inset:0;background:rgba(15,23,42,.46);}
    .ce-modal__panel{
        position:relative;
        width:min(720px, 100%);
        background:#fff;
        border-radius:16px;
        box-shadow:0 30px 90px rgba(0,0,0,.35);
        border:1px solid #eef2f7;
        padding:18px;
    }
    .ce-modal__title{font-weight:1000;color:#0f172a;font-size:18px;margin-bottom:8px;}
    .ce-modal__text{color:#334155;font-weight:800;line-height:1.45;}
    .ce-modal__actions{display:flex;align-items:center;justify-content:flex-end;gap:10px;margin-top:14px;}
    .ce-modal__cancel{border:0;background:transparent;color:#64748b;font-weight:1000;padding:10px 12px;}
    .ce-modal__confirm{border:0;background:#f6a105;color:#111827;font-weight:1000;padding:10px 16px;border-radius:12px;}
    .ce-modal__confirm:hover{opacity:.92;}
</style>
@endpush

@push('scripts')
    <script>
        (function(){
            const modal = document.getElementById('trial-modal');
            if (!modal) return;

            const waUrl = @json($trialWhatsAppUrl);
            const openBtns = document.querySelectorAll('[data-open-trial-modal]');
            const closeBtns = modal.querySelectorAll('[data-close-trial-modal]');
            const confirmBtn = document.getElementById('trial-modal-confirm');

            const open = () => {
                modal.classList.add('is-open');
                modal.setAttribute('aria-hidden', 'false');
            };

            const close = () => {
                modal.classList.remove('is-open');
                modal.setAttribute('aria-hidden', 'true');
            };

            openBtns.forEach((btn) => btn.addEventListener('click', open));
            closeBtns.forEach((btn) => btn.addEventListener('click', close));

            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') close();
            });

            confirmBtn?.addEventListener('click', () => {
                if (waUrl) {
                    window.open(waUrl, '_blank', 'noopener,noreferrer');
                }
                document.getElementById('trial-request-form')?.submit();
                close();
            });
        })();
    </script>
@endpush
