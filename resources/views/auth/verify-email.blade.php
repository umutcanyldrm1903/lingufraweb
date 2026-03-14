@extends('frontend.layouts.master')
@section('meta_title', __('Email Verification') . ' || ' . $setting->app_name)

@section('contents')
    <section class="singUp-area section-py-120">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-xl-6 col-lg-8">
                    <div class="singUp-wrap">
                        <h2 class="title">{{ __('Verify Your Email') }}</h2>
                        <p>{{ __('Please verify your email address before continuing.') }}</p>

                        @if (session('status') === 'verification-link-sent')
                            <div class="alert alert-success mt-3">
                                {{ __('A new verification link has been sent to your email address.') }}
                            </div>
                        @endif

                        <form method="POST" action="{{ route('verification.send') }}" class="account__form mt-4">
                            @csrf
                            <button type="submit" class="btn btn-two arrow-btn">
                                {{ __('Resend verification email') }}
                                <img src="{{ asset('frontend/img/icons/right_arrow.svg') }}" alt="img" class="injectable">
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

