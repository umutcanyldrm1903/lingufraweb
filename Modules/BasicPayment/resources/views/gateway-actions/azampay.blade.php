<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Azampay Checkout</title>
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset($setting->favicon) }}">
    <link rel="stylesheet" href="{{ asset('frontend/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('frontend/css/main.min.css') }}?v={{ $setting?->version }}">
    <link rel="stylesheet" href="{{ asset('global/toastr/toastr.min.css') }}">

</head>

<body>
    <section class="about-area-three section-py-120 vh-100 d-flex align-items-center justify-content-center">
        <div class="container d-flex justify-content-center">
            <div class="col-md-7">
                <div class="text-center mb-3">
                    <a href="{{ route('home') }}">
                        <img src="{{ asset($setting?->logo) }}" alt="{{ $setting?->app_name }}" width="220">
                    </a>
                </div>
                <!-- Tabs -->
                <ul class="nav nav-tabs border-bottom-0" id="checkoutTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="mno-tab" data-bs-toggle="tab" data-bs-target="#mno"
                            type="button" role="tab" aria-controls="mno" aria-selected="true">
                            {{ __('MNO Checkout') }}
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="bank-tab" data-bs-toggle="tab" data-bs-target="#bank"
                            type="button" role="tab" aria-controls="bank" aria-selected="false">
                            {{ __('Bank Checkout') }}
                        </button>
                    </li>
                </ul>

                <div class="tab-content card p-3" id="checkoutTabsContent">
                    <!-- MNO Checkout Tab -->
                    <div class="tab-pane fade show active" id="mno" role="tabpanel" aria-labelledby="mno-tab">
                        <div class="card-body">
                            <form
                                action="{{ isset($token) ? route('payment-api.azampay-mno-webview', ['bearer_token' => $token, 'order_id' => $order_id]) : route('pay-with-azampay-mno') }}"
                                method="post">
                                @csrf

                                <!-- Account Number -->
                                <div class="my-1 form-group">
                                    <label for="account_number">{{ __('Account Number') }} <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="account_number"
                                        name="account_number">
                                    @error('account_number')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <!-- External Id -->
                                <div class="my-1 form-group">
                                    <label for="external_id">{{ __('External Id') }} <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="external_id" name="external_id">
                                    @error('external_id')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <!-- Provider -->
                                <div class="my-1 form-group">
                                    <label for="provider_mno">{{ __('Provider') }} <span
                                            class="text-danger">*</span></label>
                                    <select class="form-select" name="provider" id="provider_mno">
                                        <option value="">{{ __('Select') }}</option>
                                        <option value="Airtel">{{ __('Airtel') }}</option>
                                        <option value="Tigo">{{ __('Tigo') }}</option>
                                        <option value="Halopesa">{{ __('Halopesa') }}</option>
                                        <option value="Azampesa">{{ __('Azampesa') }}</option>
                                        <option value="Mpesa">{{ __('Mpesa') }}</option>
                                    </select>
                                    @error('provider')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <button class="mt-2 btn btn-primary">{{ __('Pay Now') }}</button>
                            </form>
                        </div>
                    </div>

                    <!-- Bank Checkout Tab -->
                    <div class="tab-pane fade" id="bank" role="tabpanel" aria-labelledby="bank-tab">
                        <div class="card-body">
                            <form
                                action="{{ isset($token) ? route('payment-api.azampay-by-bank-webview', ['bearer_token' => $token, 'order_id' => $order_id]) : route('pay-with-azampay-by-bank') }}"
                                method="post">
                                @csrf

                                <!-- Merchant Account Number -->
                                <div class="my-1 form-group">
                                    <label for="merchant_account_number">{{ __('Merchant Account Number') }} <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="merchant_account_number"
                                        name="merchant_account_number">
                                    @error('merchant_account_number')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <!-- Merchant Mobile Number -->
                                <div class="my-1 form-group">
                                    <label for="merchant_mobile_number">{{ __('Merchant Mobile Number') }} <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="merchant_mobile_number"
                                        name="merchant_mobile_number">
                                    @error('merchant_mobile_number')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <!-- Merchant Name -->
                                <div class="my-1 form-group">
                                    <label for="merchant_name">{{ __('Merchant Name') }} <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="merchant_name"
                                        name="merchant_name">
                                    @error('merchant_name')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <!-- OTP -->
                                <div class="my-1 form-group">
                                    <label for="otp">{{ __('OTP') }} <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="otp" name="otp">
                                    @error('otp')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <!-- Reference ID -->
                                <div class="my-1 form-group">
                                    <label for="reference_id">{{ __('Reference Id') }} <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="reference_id"
                                        name="reference_id">
                                    @error('reference_id')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <!-- Provider -->
                                <div class="my-1 form-group">
                                    <label for="provider_bank">{{ __('Provider') }} <span
                                            class="text-danger">*</span></label>
                                    <select class="form-select" name="provider" id="provider_bank">
                                        <option value="">{{ __('Select') }}</option>
                                        <option value="CRDB">{{ __('CRDB') }}</option>
                                        <option value="NMB">{{ __('NMB') }}</option>
                                    </select>
                                    @error('provider')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <button class="mt-2 btn btn-primary">{{ __('Pay Now') }}</button>
                            </form>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>
    <!-- dynamic Toastr Notification -->
    <script src="{{ asset('global/js/jquery-3.7.1.min.js') }}"></script>
    <script src="{{ asset('frontend/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('global/toastr/toastr.min.js') }}"></script>
    <script>
        "use strict";
        toastr.options.closeButton = true;
        toastr.options.progressBar = true;
        toastr.options.positionClass = 'toast-bottom-right';

        @session('messege')
        var type = "{{ Session::get('alert-type', 'info') }}"
        switch (type) {
            case 'info':
                toastr.info("{{ $value }}");
                break;
            case 'success':
                toastr.success("{{ $value }}");
                break;
            case 'warning':
                toastr.warning("{{ $value }}");
                break;
            case 'error':
                toastr.error("{{ $value }}");
                break;
        }
        @endsession

        $(document).on("submit", "form", function() {
            const $btn = $(this).find(".btn.btn-primary");
            if (!$btn.find('.spinner-border').length) {
                $btn.append('<span class="spinner-border spinner-border-sm ms-2 text-light"></span>').prop(
                    'disabled', true);
            }
        });
    </script>
</body>

</html>
