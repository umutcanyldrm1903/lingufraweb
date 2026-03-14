@extends('frontend.layouts.master')
@section('meta_title', __('Confirm Password') . ' || ' . $setting->app_name)

@section('contents')
    <section class="singUp-area section-py-120">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-xl-6 col-lg-8">
                    <div class="singUp-wrap">
                        <h2 class="title">{{ __('Confirm Password') }}</h2>
                        <p>{{ __('Please confirm your password to continue.') }}</p>

                        <form method="POST" action="{{ route('password.confirm') }}" class="account__form">
                            @csrf
                            <div class="form-grp">
                                <label for="password">{{ __('Password') }} <code>*</code></label>
                                <input id="password" type="password" name="password" required
                                    placeholder="{{ __('Password') }}">
                                <x-frontend.validation-error name="password" />
                            </div>

                            <button type="submit" class="btn btn-two arrow-btn">
                                {{ __('Confirm') }}
                                <img src="{{ asset('frontend/img/icons/right_arrow.svg') }}" alt="img" class="injectable">
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

