<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Braintree Checkout</title>
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset($setting->favicon) }}">
    <link rel="stylesheet" href="{{ asset('frontend/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('frontend/css/main.min.css') }}?v={{ $setting?->version }}">
    <link rel="stylesheet" href="{{ asset('global/toastr/toastr.min.css') }}">
</head>
<style>
    label {
        width: 100%;
    }
</style>

<body>
    @php
        if (isset($token)) {
            $braintreeToken = route('payment-api.braintree.token', ['bearer_token' => $token]);
            $braintreeCheckout = route('payment-api.braintree.checkout', [
                'bearer_token' => $token,
                'order_id' => $order_id,
            ]);
            $successUrl = route('payment-api.webview-success-payment', ['bearer_token' => $token]);
            $failedUrl = route('payment-api.webview-failed-payment');
        } else {
            $braintreeToken = route('braintree.token');
            $braintreeCheckout = route('braintree.checkout');
            $successUrl = route('payment-success');
            $failedUrl = route('payment-failed');
        }
    @endphp

    <div id="preloader" class="toggleItem">
        <div id="loader" class="loader">
            <div class="loader-container">
                <div class="loader-icon"><img src="{{ asset($setting?->preloader) }}" alt="Preloader">
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Form -->
    <section class="about-area-three section-py-120 vh-100 d-flex align-items-center justify-content-center">
        <div class="container d-flex justify-content-center">
            <div class="col-md-7">
                <div class="text-center">
                    <a href="{{ route('home') }}" class="mb-3 toggleItem d-none">
                        <img src="{{ asset($setting?->logo) }}" alt="{{ $setting?->app_name }}" width="220">
                    </a>
                    <div id="dropin-container"></div>
                    <button class="mt-2 btn btn-primary d-none">{{ __('Pay Now') }}</button>
                </div>
            </div>
        </div>
    </section>

    <!-- jQuery CDN -->
    <script src="{{ asset('global/js/jquery-3.7.1.min.js') }}"></script>
    <!-- Braintree Drop-in JS -->
    <script src="https://js.braintreegateway.com/web/dropin/1.30.1/js/dropin.min.js"></script>
    <script src="{{ asset('global/toastr/toastr.min.js') }}"></script>

    <script>
        "use strict";
        (function($) {
            $(document).ready(function() {
                $.ajax({
                    url: "{{ $braintreeToken }}",
                    type: 'GET',
                    success: function(data) {
                        $('.toggleItem').toggleClass('d-none');
                        braintree.dropin.create({
                            authorization: data.token,
                            container: '#dropin-container',
                        }, function(createErr, instance) {
                            if (createErr) {
                                toastr.error(
                                    "{{ __('Payment failed, please try again') }}");
                                return;
                            }

                            $('.btn').removeClass('d-none');


                            $('.btn').on('click', function() {
                                let $button = $(this);
                                $button.attr('disabled', true).text(
                                    'Processing...');

                                instance.requestPaymentMethod(function(err,
                                    payload) {
                                    if (err) {
                                        $button.attr('disabled', false)
                                            .text('Pay Now');
                                        toastr.error(
                                            "{{ __('Payment failed, please try again') }}"
                                        );
                                        return;
                                    }

                                    $.ajax({
                                        url: "{{ $braintreeCheckout }}",
                                        type: 'POST',
                                        data: {
                                            _token: '{{ csrf_token() }}',
                                            payment_method_nonce: payload
                                                .nonce,
                                        },
                                        success: function(
                                            response) {
                                            if (response
                                                .success &&
                                                response
                                                .transaction_id
                                            ) {
                                                window.location
                                                    .href =
                                                    "{{ $successUrl }}";
                                            } else {
                                                window.location
                                                    .href =
                                                    "{{ $failedUrl }}";
                                            }
                                        },
                                        error: function(xhr) {
                                            window.location
                                                .href =
                                                "{{ $failedUrl }}";
                                        },
                                    });
                                });
                            });
                        });
                    },
                    error: function() {
                        window.location.href = "{{ $failedUrl }}";
                    }
                });
            });
        })(jQuery);
    </script>
</body>

</html>
