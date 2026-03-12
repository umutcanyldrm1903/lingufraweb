<div class="dashboard__sidebar-wrap">
    <div class="dashboard__sidebar-title mb-20">
        <h6 class="title">{{ __('Welcome') }}, {{ userAuth()->name }}</h6>
    </div>
    <nav class="dashboard__sidebar-menu">
        <ul class="list-wrap">
            <li class="{{ Route::is('instructor.dashboard') ? 'active' : '' }}">
                <a href="{{ route('instructor.dashboard') }}">
                    <img src="{{ asset('uploads/website-images/dashboard.svg') }}">{{ __('Dashboard') }}</a>
            </li>
            <li class="{{ Route::is('instructor.courses.*') ? 'active' : '' }}">
                <a href="{{ route('instructor.courses.index') }}">
                    <i class="flaticon-mortarboard"></i>
                    {{ __('Courses') }}
                </a>
            </li>
            @if (Module::has('CourseBundle') && Module::isEnabled('CourseBundle') && Route::has('instructor.course.bundle.index'))
                <li class="{{ Route::is('instructor.course.bundle.*') ? 'active' : '' }}">
                    <a href="{{ route('instructor.course.bundle.index') }}">
                        <img src="{{ asset('uploads/website-images/course_bundle.svg') }}">
                        {{ __('Course Bundle') }}
                    </a>
                </li>
            @endif
            <li class="{{ Route::is('instructor.lesson-questions.index') ? 'active' : '' }}">
                <a href="{{ route('instructor.lesson-questions.index') }}">
                    <img src="{{ asset('uploads/website-images/questions.svg') }}">
                    {{ __('Lesson Questions') }}
                </a>
            </li>

            <li class="{{ Route::is('instructor.payout.index') ? 'active' : '' }}">
                <a href="{{ route('instructor.payout.index') }}">
                    <img src="{{ asset('uploads/website-images/payout.svg') }}">
                    {{ __('Request Payout') }}
                </a>
            </li>
            <li class="{{ Route::is('instructor.announcements.index') ? 'active' : '' }}">
                <a href="{{ route('instructor.announcements.index') }}">
                    <img src="{{ asset('uploads/website-images/announcement.svg') }}">
                    {{ __('Announcement') }}
                </a>
            </li>
            <li class="{{ Route::is('instructor.my-sells.index') ? 'active' : '' }}">
                <a href="{{ route('instructor.my-sells.index') }}">
                    <img src="{{ asset('uploads/website-images/sales.svg') }}">
                    {{ __('My Sales') }}
                </a>
            </li>
            <li class="{{ Route::is('instructor.wishlist') ? 'active' : '' }}">
                <a href="{{ route('instructor.wishlist') }}">
                    <img src="{{ asset('uploads/website-images/heart.svg') }}">{{ __('Wishlist') }}</a>
            </li>
        </ul>
    </nav>
    <div class="dashboard__sidebar-title mt-30 mb-20">
        <h6 class="title">{{ __('User') }}</h6>
    </div>
    <nav class="dashboard__sidebar-menu">
        <ul class="list-wrap">
            <li class="{{ Route::is('instructor.zoom-setting.index') ? 'active' : '' }}">
                <a href="{{ route('instructor.zoom-setting.index') }}">
                    <img src="{{ asset('uploads/website-images/zoom.svg') }}">
                    {{ __('Zoom live setting') }}
                </a>
            </li>
            <li class="{{ Route::is('instructor.jitsi-setting.index') ? 'active' : '' }}">
                <a href="{{ route('instructor.jitsi-setting.index') }}">
                    <img src="{{ asset('uploads/website-images/zoom.svg') }}">
                    {{ __('Jitsi live setting') }}
                </a>
            </li>
            <li class="{{ Route::is('instructor.setting.index') ? 'active' : '' }}">
                <a href="{{ route('instructor.setting.index') }}">
                    <i class="flaticon-user"></i>
                    {{ __('Profile Settings') }}
                </a>
            </li>
            <li>
                <a href="{{ route('logout') }}" onclick="event.preventDefault(); $('#logout-form').trigger('submit');">
                    <img src="{{ asset('uploads/website-images/logout.svg') }}">
                    {{ __('Logout') }}
                </a>
            </li>
        </ul>
    </nav>
</div>

{{-- start admin logout form --}}
<form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
    @csrf
</form>
{{-- end admin logout form --}}
