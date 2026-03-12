<div class="dashboard__sidebar-wrap">
    <div class="dashboard__sidebar-title mb-20">
        <h6 class="title">{{ __('Welcome') }}, {{ userAuth()->name }}</h6>
    </div>
    <nav class="dashboard__sidebar-menu">
        <ul class="list-wrap">
            
            <li class="{{ Route::is('student.dashboard') ? 'active' : '' }}">
                <a href="{{ route('student.dashboard') }}">
                    <img src="{{asset('uploads/website-images/dashboard.svg')}}">
                    {{ __('Dashboard') }}
                </a>
            </li>

            <li class="{{ Route::is('student.orders.index') ? 'active' : '' }}">
                <a href="{{ route('student.orders.index') }}">
                    <img src="{{asset('uploads/website-images/order-history.svg')}}">
                    {{ __('Order History') }}
                </a>
            </li>

            <li class="{{ Route::is('student.enrolled-courses') ? 'active' : '' }}">
                <a href="{{ route('student.enrolled-courses') }}">
                    <i class="flaticon-mortarboard"></i>{{ __('Enrolled Courses') }}</a>
            </li>
            <li class="{{ Route::is('student.wishlist') ? 'active' : '' }}">
                <a href="{{ route('student.wishlist') }}">
                    <img src="{{asset('uploads/website-images/heart.svg')}}">{{ __('Wishlist') }}</a>
            </li>
            <li class="{{ Route::is('student.reviews.index') ? 'active' : '' }}">
                <a href="{{ route('student.reviews.index') }}">
                    <img src="{{asset('uploads/website-images/reviews.svg')}}">{{ __('Reviews') }}</a>
            </li>
            <li class="{{ Route::is('student.quiz-attempts') ? 'active' : '' }}">
                <a href="{{ route('student.quiz-attempts') }}">
                    <img src="{{asset('uploads/website-images/quiz.svg')}}">{{ __('My Quiz Attempts') }}</a>
            </li>
        </ul>
    </nav>
    <div class="dashboard__sidebar-title mt-30 mb-20">
        <h6 class="title">{{ __('User') }}</h6>
    </div>
    <nav class="dashboard__sidebar-menu">
        <ul class="list-wrap">
            <li>
                <a href="{{ route('student.setting.index') }}">
                    <i class="flaticon-user"></i>
                    {{ __('Profile Settings') }}
                </a>
            </li>
            <li>
                <a href="{{ route('logout') }}"
                    onclick="event.preventDefault(); $('#logout-form').trigger('submit');">
                    <img src="{{asset('uploads/website-images/logout.svg')}}">
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
