@extends('frontend.layouts.master')
@section('meta_title', 'Checkout' . ' || ' . $setting->app_name)
@section('contents')
    <!-- breadcrumb-area -->
    <x-frontend.breadcrumb :title="__('Make Payment')" :links="[
        ['url' => route('home'), 'text' => __('Home')],
        ['url' => route('checkout.index'), 'text' => __('Make Payment')],
    ]" />
    <!-- breadcrumb-area-end -->

    <!-- checkout-area -->
    <div class="checkout__area section-py-120">
        <div class="preloader-two preloader-two-fixed d-none">
            <div class="loader-icon-two"><img src="{{ asset(Cache::get('setting')->preloader) }}" alt="Preloader"></div>
        </div>
        <div class="container">
            <div class="checkout-ui">
                @if ($cart_count > 0)
                    <div class="order-steps">
                        <div class="order-steps__item">
                            <span class="order-steps__badge">1</span>
                            <span class="order-steps__label">{{ __('Cart') }}</span>
                        </div>
                        <div class="order-steps__line" aria-hidden="true"></div>
                        <div class="order-steps__item is-active" aria-current="step">
                            <span class="order-steps__badge">2</span>
                            <span class="order-steps__label">{{ __('Make Payment') }}</span>
                        </div>
                    </div>
                @endif
            <div class="row">
                <div class="col-lg-8">
                    <div id="show_currency_notifications">
                        <div class="alert alert-warning d-none"></div>
                    </div>
                    <div class="wsus__payment_area">
                        <div class="row">
                            @if ($payable_amount > 0)
                                @foreach ($activeGateways as $gatewayKey => $gatewayDetails)
                                    <div class="col-lg-3 col-6 col-sm-4">
                                        <a class="wsus__single_payment place-order-btn" data-method="{{ $gatewayKey }}">
                                            <img src="{{ asset($gatewayDetails['logo']) }}"
                                                alt="{{ $gatewayDetails['name'] }}" class="img-fluid w-100">
                                        </a>
                                    </div>
                                @endforeach
                            @else
                                <div class="col-lg-3 col-6 col-sm-4">
                                    <form action="{{ route('pay-via-free-gateway') }}" method="POST">
                                        @csrf
                                        <button class="wsus__single_payment border-0">
                                            <img src="{{ asset('uploads/website-images/buy_now.png') }}"
                                                alt="Pay with stripe" class="img-fluid w-100">
                                        </button>
                                    </form>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="checkout-ui__payment-logos" aria-label="{{ __('Odeme yontemleri') }}">
                        <span class="checkout-ui__payment-label">{{ __('İyzico ile güvenli ödeme') }}</span>
                        <div class="checkout-ui__payment-logos-row">
                            <img src="{{ asset('frontend/img/payments/iyzico-band-colored.svg') }}"
                                alt="{{ __('İyzico ile öde') }}">
                            <img src="{{ asset('frontend/img/payments/cc-visa.svg') }}" alt="Visa">
                            <img src="{{ asset('frontend/img/payments/cc-mastercard.svg') }}" alt="Mastercard">
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="cart__collaterals-wrap payment_slidebar">
                        <h2 class="title">{{ __('Cart totals') }}</h2>
                        <ul class="list-wrap pb-0">
                            <li>{{ __('Total Items') }}<span>{{ $cart_count }}</span></li>
                            <li>
                                @if (Session::has('coupon_code'))
                                    <p class="coupon-discount m-0">
                                        <span>{{ __('Discount') }}</span>
                                        <br>
                                        <small>{{ $coupon }} ({{ $discountPercent }} %)<a class="ms-2 text-danger cart-remove cart-remove--mini"
                                                href="/remove-coupon" aria-label="{{ __('Remove') }}">x</a></small>
                                    </p>
                                    <span class="discount-amount">{{ currency($discountAmount) }}</span>
                                @else
                                    <p class="coupon-discount m-0">
                                        <span>{{ __('Discount') }}</span>
                                    </p>
                                    <span class="discount-amount">{{ currency(0) }}</span>
                                @endif
                            </li>
                            <li>{{ __('Total') }} <span class="amount">{{ $total }}</span></li>

                            @if ($payable_amount > 0)
                                <h6 class="bold payable-bold">{{ __('payable with gateway charge') }}:</h6>

                                @php
                                    $currency = getSessionCurrency();
                                @endphp

                                @foreach ($activeGateways as $gatewayKey => $gatewayDetails)
                                    @if ($paymentService->isCurrencySupported($gatewayKey))
                                        @php
                                            $payableDetails = $paymentService->getPayableAmount(
                                                $gatewayKey,
                                                $payable_amount,
                                            );
                                        @endphp

                                        <p class="payable-text">
                                            {{ $gatewayDetails['name'] }}:
                                            <span>{{ $payableDetails->payable_with_charge }} {{ $currency }}</span>
                                        </p>
                                    @endif
                                @endforeach
                            @endif
                        </ul>
                    </div>
                </div>
            </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
<style>
@import url('https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;600;700&family=Space+Grotesk:wght@500;700&display=swap');

.checkout-ui{--cart-ink:#0f172a;--cart-muted:#64748b;--cart-accent:#f6a105;--cart-accent-dark:#0e5c93;--cart-border:#e2e8f0;font-family:"Space Grotesk","DM Sans",sans-serif;background:radial-gradient(900px circle at 0 0, rgba(246,161,5,.12), transparent 45%),radial-gradient(900px circle at 100% 0, rgba(14,92,147,.12), transparent 40%),#f8fafc;border:1px solid var(--cart-border);border-radius:28px;padding:24px;box-shadow:0 30px 70px rgba(15,23,42,0.12);}
.checkout-ui .row{--bs-gutter-x:24px;--bs-gutter-y:24px;}

.order-steps{display:flex;align-items:center;justify-content:center;gap:14px;flex-wrap:wrap;background:#fff;border:1px solid var(--cart-border);padding:12px 18px;border-radius:999px;box-shadow:0 12px 26px rgba(15,23,42,0.08);margin-bottom:24px;}
.order-steps__item{display:flex;align-items:center;gap:10px;font-weight:700;color:var(--cart-muted);}
.order-steps__item.is-active{color:var(--cart-ink);}
.order-steps__badge{width:30px;height:30px;border-radius:50%;display:grid;place-items:center;background:#e2e8f0;color:#0f172a;font-size:13px;font-weight:700;}
.order-steps__item.is-active .order-steps__badge{background:var(--cart-accent);box-shadow:0 10px 20px rgba(246,161,5,.35);}
.order-steps__line{width:52px;height:2px;background:#e2e8f0;}

.checkout-ui .wsus__payment_area{background:#fff;border:1px solid var(--cart-border);border-radius:22px;padding:18px;box-shadow:0 18px 40px rgba(15,23,42,0.12);}
.checkout-ui .wsus__single_payment{display:flex;align-items:center;justify-content:center;background:#fff;border:1px solid var(--cart-border);border-radius:18px;padding:12px;min-height:86px;box-shadow:0 12px 26px rgba(15,23,42,0.08);transition:transform .15s ease, box-shadow .15s ease;}
.checkout-ui .wsus__single_payment:hover{transform:translateY(-2px);box-shadow:0 18px 34px rgba(15,23,42,0.12);}
.checkout-ui .place-order-btn{cursor:pointer;}
.checkout-ui .wsus__single_payment img{width:auto !important;max-width:100%;max-height:64px;height:auto;object-fit:contain;display:block;}
.checkout-ui__payment-logos{margin-top:16px;padding:12px 14px;border-radius:16px;border:1px dashed var(--cart-border);background:#fff;display:flex;flex-direction:column;gap:10px;align-items:center;justify-content:center;text-align:center;}
.checkout-ui__payment-label{font-weight:700;color:var(--cart-muted);font-size:12px;letter-spacing:0.3px;text-transform:uppercase;}
.checkout-ui__payment-logos-row{display:flex;gap:12px;flex-wrap:wrap;align-items:center;justify-content:center;}
.checkout-ui__payment-logos-row img{height:28px;width:auto;display:block;}

.checkout-ui .cart__collaterals-wrap{border:1px solid var(--cart-border);border-radius:22px;background:#fff;box-shadow:0 18px 40px rgba(15,23,42,0.12);padding:20px;}
.checkout-ui .cart__collaterals-wrap .title{font-weight:700;color:var(--cart-ink);margin-bottom:16px;}
.checkout-ui .cart__collaterals-wrap .list-wrap{display:grid;gap:10px;margin:0 0 18px;padding:0;}
.checkout-ui .cart__collaterals-wrap .list-wrap li{display:flex;align-items:center;justify-content:space-between;font-weight:700;color:var(--cart-ink);}
.checkout-ui .cart__collaterals-wrap .amount{color:var(--cart-accent-dark);font-weight:700;}
.checkout-ui .payable-bold{margin-top:12px;color:var(--cart-muted);font-weight:700;}
.checkout-ui .payable-text{display:flex;align-items:center;justify-content:space-between;margin:6px 0;color:var(--cart-ink);font-weight:600;}

.cart-remove{display:inline-flex;align-items:center;justify-content:center;width:24px;height:24px;border-radius:6px;border:1px solid #e2e8f0;background:#fff;color:#ef4444;font-weight:700;text-decoration:none;transition:transform .15s ease, box-shadow .15s ease;}
.cart-remove:hover{transform:translateY(-1px);box-shadow:0 10px 18px rgba(239,68,68,0.2);color:#ef4444;}
.cart-remove--mini{width:24px;height:24px;border-radius:6px;font-size:12px;}

@media (max-width:991.98px){
    .checkout-ui{padding:18px;border-radius:22px;}
    .order-steps{border-radius:16px;}
}
@media (max-width:575.98px){
    .order-steps__line{display:none;}
}
</style>
@endpush

@push('scripts')
    <script src="{{ asset('frontend/js/default/checkout.js') }}"></script>
@endpush
