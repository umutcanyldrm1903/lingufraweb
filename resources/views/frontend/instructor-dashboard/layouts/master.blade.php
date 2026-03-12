@extends('frontend.layouts.master')

@section('hide_public_header', '1')
@section('hide_public_footer', '1')
@php
    app()->setLocale('en');
@endphp

<!-- meta -->
@section('meta_title', __('Instructor Dashboard'))
<!-- end meta -->

@section('contents')
    @php
        $user = auth()->user();
        $setting = cache()->get('setting');

        $defaultAvatar = $setting?->default_avatar ?? 'uploads/website-images/default-avatar.png';
        $avatarPath = $user?->image ? asset($user->image) : asset($defaultAvatar);

        $zoomCredential = null;
        $zoomConnected = false;
        $roomReady = false;
        if ($user && Schema::hasTable('zoom_credentials')) {
            $zoomCredential = \App\Models\ZoomCredential::where('instructor_id', $user->id)->first();
            $zoomConnected = !empty($zoomCredential?->refresh_token);
            $roomReady = !empty($zoomCredential?->default_meeting_id);
        }

        $upcomingLessonCount = null;
        $uniqueStudentCount = null;
        if ($user && Schema::hasTable('student_live_lessons')) {
            $baseQuery = DB::table('student_live_lessons')->where('instructor_id', $user->id);
            if (Schema::hasColumn('student_live_lessons', 'status')) {
                $baseQuery->whereNotIn('status', ['cancelled_teacher', 'cancelled_student']);
            }

            $upcomingLessonCount = (clone $baseQuery)
                ->where('start_time', '>=', now())
                ->count();

            $uniqueStudentCount = (clone $baseQuery)
                ->whereNotNull('student_id')
                ->distinct()
                ->count('student_id');
        }
    @endphp

    <section class="instructor-panel">
        <div class="sp-hero"></div>
        <div class="container">
            <div class="sp-header">
                <div class="sp-profile">
                    <div class="sp-avatar">
                        <img src="{{ $avatarPath }}" alt="img">
                    </div>
                    <div>
                        <h4 class="sp-name">{{ $user?->name }}</h4>
                        <p class="sp-mail">{{ $user?->email }}</p>
                        @if ($user?->phone)
                            <p class="sp-phone">{{ $user->phone }}</p>
                        @endif
                    </div>
                </div>
                <div class="sp-actions">
                    <a href="{{ route('instructor.schedule.index') }}" class="sp-btn sp-btn-dark">{{ __('Schedule') }}</a>
                    <a href="{{ route('instructor.setting.index') }}" class="sp-btn sp-btn-light">{{ __('Profile') }}</a>
                    <a
                        href="{{ route('logout') }}"
                        class="sp-btn sp-btn-light"
                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                    >{{ __('Logout') }}</a>
                </div>
            </div>

            <div class="sp-planbar">
                <div class="sp-planbar__items">
                    <span class="sp-planbar__item">
                        <i class="fas fa-video"></i>
                        {{ __('Zoom') }}: <strong>{{ $zoomConnected ? __('Connected') : __('Not Connected') }}</strong>
                    </span>
                    <span class="sp-planbar__item">
                        <i class="fas fa-link"></i>
                        {{ __('Meeting Room') }}: <strong>{{ $roomReady ? __('Ready') : __('Not Created Yet') }}</strong>
                    </span>
                    @if ($upcomingLessonCount !== null)
                        <span class="sp-planbar__item">
                            <i class="fas fa-calendar-alt"></i>
                            {{ __('Upcoming Lessons') }}: <strong>{{ $upcomingLessonCount }}</strong>
                        </span>
                    @endif
                    @if ($uniqueStudentCount !== null)
                        <span class="sp-planbar__item">
                            <i class="fas fa-users"></i>
                            {{ __('Students') }}: <strong>{{ $uniqueStudentCount }}</strong>
                        </span>
                    @endif
                </div>
                <div class="sp-planbar__actions">
                    <a href="{{ route('instructor.zoom-setting.index') }}" class="sp-btn sp-btn-outline">
                        {{ $zoomConnected ? __('Zoom Setting') : __('Connect Zoom') }}
                    </a>
                </div>
            </div>

            <div class="sp-nav">
                <a class="{{ Route::is('instructor.dashboard') ? 'is-active' : '' }}" href="{{ route('instructor.dashboard') }}">{{ __('Home') }}</a>
                <a class="{{ Route::is('instructor.messages.*') ? 'is-active' : '' }}" href="{{ route('instructor.messages.index') }}">{{ __('Messages') }}</a>
                <a class="{{ Route::is('instructor.schedule.*') ? 'is-active' : '' }}" href="{{ route('instructor.schedule.index') }}">{{ __('Schedule') }}</a>
                <a class="{{ Route::is('instructor.lessons.*') ? 'is-active' : '' }}" href="{{ route('instructor.lessons.index') }}">{{ __('Lessons') }}</a>
                <a class="{{ Route::is('instructor.students.*') ? 'is-active' : '' }}" href="{{ route('instructor.students.index') }}">{{ __('Students') }}</a>
                <a class="{{ Route::is('instructor.homeworks.*') ? 'is-active' : '' }}" href="{{ route('instructor.homeworks.index') }}">{{ __('Homeworks') }}</a>
                <a class="{{ Route::is('instructor.guide.*') ? 'is-active' : '' }}" href="{{ route('instructor.guide.index') }}">{{ __('User Guide') }}</a>
                <a class="{{ Route::is('instructor.library.*') ? 'is-active' : '' }}" href="{{ route('instructor.library.index') }}">{{ __('Library') }}</a>
                <a class="{{ Route::is('instructor.agreement.*') ? 'is-active' : '' }}" href="{{ route('instructor.agreement.index') }}">{{ __('Agreement') }}</a>
                <a class="{{ Route::is('instructor.instructions.*') ? 'is-active' : '' }}" href="{{ route('instructor.instructions.index') }}">{{ __('Instructions') }}</a>
                <a class="{{ Route::is('instructor.reports.*') ? 'is-active' : '' }}" href="{{ route('instructor.reports.index') }}">{{ __('Reports') }}</a>
            </div>

            <div class="sp-card">
                @yield('dashboard-contents')
            </div>
        </div>
    </section>

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
    .instructor-panel{min-height:100vh;background:transparent;padding:0 0 90px;}
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
    .sp-actions{display:flex;gap:10px;flex-wrap:wrap;}
    .sp-btn{border-radius:12px;padding:10px 14px;font-weight:800;display:inline-block;text-decoration:none;}
    .sp-btn-dark{background:var(--student-dark);color:#fff;border:1px solid var(--student-dark);}
    .sp-btn-dark:hover{opacity:.9;color:#fff;}
    .sp-btn-light{background:#fff;color:#111827;border:1px solid #e5e7eb;}
    .sp-btn-light:hover{background:#f9fafb;color:#111827;border-color:#d1d5db;}
    .sp-btn-outline{background:transparent;color:#111827;border:1px solid #d1d5db;}
    .sp-btn-outline:hover{background:#f9fafb;color:#111827;border-color:#9ca3af;}

    .sp-planbar{margin-top:12px;background:#fff;border-radius:16px;box-shadow:0 14px 32px rgba(0,0,0,0.08);padding:12px 14px;display:flex;justify-content:space-between;align-items:center;gap:12px;flex-wrap:wrap;}
    .sp-planbar__items{display:flex;gap:12px;flex-wrap:wrap;align-items:center;}
    .sp-planbar__item{display:flex;align-items:center;gap:8px;background:#f9fafb;border:1px solid #e5e7eb;border-radius:999px;padding:8px 10px;font-weight:900;color:#111827;}
    .sp-planbar__item i{color:var(--student-dark);}
    .sp-planbar__item strong{font-weight:1000;}

    .sp-nav{margin:18px 0;display:flex;gap:16px;flex-wrap:wrap;align-items:center;}
    .sp-nav a{padding:10px 12px;border-radius:12px;font-weight:800;color:#1c1c1c;text-decoration:none;border:1px solid transparent;}
    .sp-nav a.is-active{background:var(--student-brand);border-color:var(--student-brand);color:#1c1c1c;}
    .sp-nav a:hover{background:#ffe7b3;border-color:var(--student-brand);color:#1c1c1c;}

    .sp-card{background:#fff;border-radius:18px;box-shadow:0 16px 36px rgba(0,0,0,0.08);padding:18px;}
    .dashboard__content-wrap,
    .dashboard__content-wrap-two{box-shadow:none;border:0;padding:0;}
    .dashboard__content-title .title{font-weight:900;color:#1c1c1c;}
    .dashboard__review-table table thead th{color:#1c1c1c;font-weight:900;}

    .instructor-panel .dashboard__content-wrap,
    .instructor-panel .dashboard__content-wrap-two{display:grid;gap:18px;background:transparent;}
    .instructor-panel .dashboard__content-title{background:#fff;border:1px solid #eef2f7;border-radius:16px;padding:16px 18px;box-shadow:0 14px 30px rgba(0,0,0,0.08);}
    .instructor-panel .dashboard__nav-wrap{background:#fff;border:1px solid #eef2f7;border-radius:16px;padding:10px 12px;box-shadow:0 14px 30px rgba(0,0,0,0.08);}
    .instructor-panel .nav-tabs{border-bottom:1px solid #eef2f7;gap:8px;}
    .instructor-panel .nav-tabs .nav-link{border:1px solid transparent;border-radius:999px;padding:8px 14px;font-weight:900;color:#111827;}
    .instructor-panel .nav-tabs .nav-link.active{background:#fff2d0;border-color:var(--student-brand);color:#111827;}
    .instructor-panel .instructor__profile-form-wrap{background:#fff;border:1px solid #eef2f7;border-radius:16px;padding:16px;box-shadow:0 14px 30px rgba(0,0,0,0.08);}
    .instructor-panel .dashboard__review-table{background:#fff;border:1px solid #eef2f7;border-radius:16px;padding:8px;box-shadow:0 14px 30px rgba(0,0,0,0.06);}
    .instructor-panel .dashboard__review-table table thead th{background:#fff7e8;}
    .instructor-panel .form-control:focus,
    .instructor-panel input:focus,
    .instructor-panel select:focus,
    .instructor-panel textarea:focus{border-color:var(--student-brand);box-shadow:0 0 0 3px rgba(246,161,5,0.2);}
</style>
@endpush

@push('scripts')
<script src="{{ asset('frontend/js/tinymce/js/tinymce/tinymce.min.js') }}"></script>
<script src="{{ asset('frontend/js/custom-tinymce.js') }}"></script>
@endpush
