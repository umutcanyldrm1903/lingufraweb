<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Mpesa Checkout</title>
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset($setting->favicon) }}">
    <link rel="stylesheet" href="{{ asset('frontend/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('frontend/css/main.min.css') }}?v={{ $setting?->version }}">
    <link rel="stylesheet" href="{{ asset('global/toastr/toastr.min.css') }}">

</head>

<body>
    <section class="about-area-three section-py-120 vh-100 d-flex align-items-center justify-content-center">
        <div class="container d-flex justify-content-center">
            <div class="col-md-6">
                <div class="text-center mb-3">
                    <a href="{{ route('home') }}">
                        <img src="{{ asset($setting?->logo) }}" alt="{{ $setting?->app_name }}" width="220">
                    </a>
                </div>
                <div class="card singUp-wrap">
                    <div class="card-body">
                        <form
                            action="{{ isset($token) ? route('payment-api.mpesa-webview', ['bearer_token' => $token, 'order_id' => $order_id]) : route('pay-via-mpesa') }}"
                            method="post">
                            @csrf

                            <!-- Account Number -->
                            <div class="my-1 form-group">
                                <label for="msisdn">{{ __('Phone Number') }} <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="msisdn" name="msisdn"
                                    value="000000000001">
                                @error('msisdn')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <button class="mt-2 btn btn-primary">{{ __('Submit') }}</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- dynamic Toastr Notification -->
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
    </script>
</body>

</html>
