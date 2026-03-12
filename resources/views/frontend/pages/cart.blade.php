@extends('frontend.layouts.master')
@section('meta_title', 'Cart' . ' || ' . $setting->app_name)

@section('contents')
    <!-- breadcrumb-area -->
    <x-frontend.breadcrumb :title="__('Cart')" :links="[['url' => route('home'), 'text' => __('Home')], ['url' => route('cart'), 'text' => __('Cart')]]" />
    <!-- breadcrumb-area-end -->

    <!-- cart-area -->
    <div class="cart__area section-py-120">
        <div class="container">
            <div class="cart-ui">
                @if ($cart_count > 0)
                    <div class="order-steps">
                        <div class="order-steps__item is-active" aria-current="step">
                            <span class="order-steps__badge">1</span>
                            <span class="order-steps__label">{{ __('Cart') }}</span>
                        </div>
                        <div class="order-steps__line" aria-hidden="true"></div>
                        <div class="order-steps__item">
                            <span class="order-steps__badge">2</span>
                            <span class="order-steps__label">{{ __('Make Payment') }}</span>
                        </div>
                    </div>
                @endif
            @auth('web')
                @foreach ($products as $item)
                    @if (in_array($item?->course?->id, session()->get('enrollments')))
                        <div class="alert alert-danger d-flex align-items-center" role="alert">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor"
                                class="bi bi-exclamation-triangle-fill flex-shrink-0 me-2" viewBox="0 0 16 16" role="img"
                                aria-label="Warning:">
                                <path
                                    d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z" />
                            </svg>
                            <div>
                                {{ __('You have items in your cart that you already purchased. before proceed please remove those from cart ') }}
                            </div>
                        </div>
                    @elseif (in_array($item?->course?->id, session()->get('instructor_courses')))
                        <div class="alert alert-danger d-flex align-items-center" role="alert">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor"
                                class="bi bi-exclamation-triangle-fill flex-shrink-0 me-2" viewBox="0 0 16 16" role="img"
                                aria-label="Warning:">
                                <path
                                    d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z" />
                            </svg>
                            <div>
                                {{ __('You have your own courses in your cart. before proceed please remove those from cart ') }}
                            </div>
                        </div>
                    @endif
                @endforeach

                @if ($cart_count > 0)
                    <div class="row">
                        <div class="col-lg-8">
                            <table class="table cart__table">
                                <thead>
                                    <tr>
                                        <th class="product__thumb">&nbsp;</th>
                                        <th class="product__name">{{ __('Course') }}</th>
                                        <th class="product__price">{{ __('Price') }}</th>
                                        <th class="product__remove">&nbsp;</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if (!empty($planCart))
                                        <tr>
                                            <td class="product__thumb pe-2">
                                                <div class="d-flex align-items-center justify-content-center"
                                                    style="width:72px;height:72px;border-radius:14px;background:#fff7e6;border:1px solid rgba(246,161,5,.55);font-size:28px;">
                                                    <i class="fas fa-gift"></i>
                                                </div>
                                            </td>
                                            <td class="product__name">
                                                <a href="{{ route('student.plans.index', ['plan' => $planCart['plan_key'] ?? '']) }}">
                                                    {{ $planCart['display_title'] ?? $planCart['title'] ?? __('Plan') }}
                                                </a>
                                                <br>
                                                <span class="badge bg-primary mt-2">{{ __('Package') }}</span>
                                            </td>
                                            <td class="product__price">{{ currency($planCart['price'] ?? 0) }}</td>
                                            <td class="product__remove">
                                                <a class="cart-remove" href="{{ route('student.plans.cart.remove') }}" aria-label="{{ __('Remove') }}">x</a>
                                            </td>
                                        </tr>
                                    @endif
                                    @foreach ($products as $product)
                                        <tr>
                                            <td class="product__thumb pe-2">
                                                <a href="{{ route('course.show', $product?->course?->slug) }}"><img
                                                        src="{{ asset($product?->course?->thumbnail) }}" alt=""></a>
                                            </td>
                                            <td class="product__name">
                                                <a
                                                    href="{{ route('course.show', $product?->course?->slug) }}">{{ $product?->course?->title }}</a>
                                                <br>
                                                @if (in_array($product?->course?->id, session()->get('enrollments')))
                                                    <span class="badge bg-warning mt-2">{{ __('Already purchased') }}</span>
                                                @elseif (in_array($product?->course?->id, session()->get('instructor_courses')))
                                                    <span class="badge bg-warning mt-2">{{ __('Own course') }}</span>
                                                @else
                                                @endif
                                            </td>
                                            <td class="product__price">{{ $product?->course?->discount > 0 ? currency($product?->course?->discount) : currency($product?->course?->price) }}</td>
                                            <td class="product__remove">
                                                <a class="cart-remove" href="{{ route('remove-cart-item', $product?->course?->slug) }}" aria-label="{{ __('Remove') }}">x</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                    <tr>
                                        <td colspan="6" class="cart__actions">
                                            <form action="{{ route('apply-coupon') }}" class="cart__actions-form coupon-form"
                                                method="POST">
                                                @csrf
                                                <input type="text" name="coupon" placeholder="Coupon code">
                                                <button type="submit" class="btn">{{ __('Apply coupon') }}</button>
                                            </form>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="col-lg-4">
                            <div class="cart__collaterals-wrap">
                                <h2 class="title">{{ __('Cart totals') }}</h2>
                                <ul class="list-wrap">
                                    <li>{{ __('Total Items') }}<span>{{ $cart_count }}</span></li>
                                    <li>
                                        @if (Session::has('coupon_code'))
                                            <p class="coupon-discount m-0">
                                                <span>{{ __('Discount') }}</span>
                                                <br>
                                                <small>{{ $coupon }} ({{ $discountPercent }} %)<a
                                                        class="ms-2 text-danger cart-remove cart-remove--mini" href="/remove-coupon" aria-label="{{ __('Remove') }}">x</a></small>
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
                                </ul>
                                <a href="{{ route('checkout.index') }}" class="btn">{{ __('Proceed to checkout') }}</a>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="w-100 text-center">
                        <img class="mb-4" src="{{ asset('uploads/website-images/empty-cart.png') }}" alt="">
                        <h4 class="text-center">{{ __('Cart is empty!') }}</h4>
                        <p class="text-center">
                            {{ __('Please add some courses in your cart.') }}
                        </p>
                    </div>
                @endif
            @else
                @foreach ($products as $item)
                    @if (in_array($item->id, session()->get('enrollments')))
                        <div class="alert alert-danger d-flex align-items-center" role="alert">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor"
                                class="bi bi-exclamation-triangle-fill flex-shrink-0 me-2" viewBox="0 0 16 16" role="img"
                                aria-label="Warning:">
                                <path
                                    d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z" />
                            </svg>
                            <div>
                                {{ __('You have items in your cart that you already purchased. before proceed please remove those from cart ') }}
                            </div>
                        </div>
                    @elseif (in_array($item->id, session()->get('instructor_courses')))
                        <div class="alert alert-danger d-flex align-items-center" role="alert">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor"
                                class="bi bi-exclamation-triangle-fill flex-shrink-0 me-2" viewBox="0 0 16 16" role="img"
                                aria-label="Warning:">
                                <path
                                    d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z" />
                            </svg>
                            <div>
                                {{ __('You have your own courses in your cart. before proceed please remove those from cart ') }}
                            </div>
                        </div>
                    @endif
                @endforeach

                @if ($cart_count > 0)
                    <div class="row">
                        <div class="col-lg-8">
                            <table class="table cart__table">
                                <thead>
                                    <tr>
                                        <th class="product__thumb">&nbsp;</th>
                                        <th class="product__name">{{ __('Course') }}</th>
                                        <th class="product__price">{{ __('Price') }}</th>
                                        <th class="product__remove">&nbsp;</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($products as $product)
                                        <tr>
                                            <td class="product__thumb pe-2">
                                                <a href="{{ route('course.show', $product->options['slug']) }}"><img
                                                        src="{{ asset($product->options['image']) }}" alt=""></a>
                                            </td>
                                            <td class="product__name">
                                                <a
                                                    href="{{ route('course.show', $product->options['slug']) }}">{{ $product->name }}</a>
                                                <br>
                                                @if (in_array($product->id, session()->get('enrollments')))
                                                    <span class="badge bg-warning mt-2">{{ __('Already purchased') }}</span>
                                                @elseif (in_array($product->id, session()->get('instructor_courses')))
                                                    <span class="badge bg-warning mt-2">{{ __('Own course') }}</span>
                                                @else
                                                @endif
                                            </td>
                                            <td class="product__price">{{ currency($product->price) }}</td>
                                            <td class="product__remove">
                                                <a class="cart-remove" href="{{ route('remove-cart-item', $product->rowId) }}" aria-label="{{ __('Remove') }}">x</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                    <tr>
                                        <td colspan="6" class="cart__actions">
                                            <form action="{{ route('apply-coupon') }}" class="cart__actions-form coupon-form"
                                                method="POST">
                                                @csrf
                                                <input type="text" name="coupon" placeholder="Coupon code">
                                                <button type="submit" class="btn">{{ __('Apply coupon') }}</button>
                                            </form>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="col-lg-4">
                            <div class="cart__collaterals-wrap">
                                <h2 class="title">{{ __('Cart totals') }}</h2>
                                <ul class="list-wrap">
                                    <li>{{ __('Total Items') }}<span>{{ $cart_count }}</span></li>
                                    <li>
                                        @if (Session::has('coupon_code'))
                                            <p class="coupon-discount m-0">
                                                <span>{{ __('Discount') }}</span>
                                                <br>
                                                <small>{{ $coupon }} ({{ $discountPercent }} %)<a
                                                        class="ms-2 text-danger cart-remove cart-remove--mini" href="/remove-coupon" aria-label="{{ __('Remove') }}">x</a></small>
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
                                </ul>
                                <a href="{{ route('checkout.index') }}" class="btn">{{ __('Proceed to checkout') }}</a>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="w-100 text-center">
                        <img class="mb-4" src="{{ asset('uploads/website-images/empty-cart.png') }}" alt="">
                        <h4 class="text-center">{{ __('Cart is empty!') }}</h4>
                        <p class="text-center">
                            {{ __('Please add some courses in your cart.') }}
                        </p>
                    </div>
                @endif
            @endauth
            </div>
        </div>
    </div>
    <!-- cart-area-end -->
@endsection

@push('styles')
<style>
@import url('https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;600;700&family=Space+Grotesk:wght@500;700&display=swap');

.cart-ui{--cart-ink:#0f172a;--cart-muted:#64748b;--cart-accent:#f6a105;--cart-accent-dark:#0e5c93;--cart-border:#e2e8f0;--cart-surface:#f8fafc;font-family:"Space Grotesk","DM Sans",sans-serif;background:radial-gradient(900px circle at 0 0, rgba(246,161,5,.12), transparent 45%),radial-gradient(900px circle at 100% 0, rgba(14,92,147,.12), transparent 40%),#f8fafc;border:1px solid var(--cart-border);border-radius:28px;padding:24px;box-shadow:0 30px 70px rgba(15,23,42,0.12);}
.cart-ui .row{--bs-gutter-x:24px;--bs-gutter-y:24px;}

.order-steps{display:flex;align-items:center;justify-content:center;gap:14px;flex-wrap:wrap;background:#fff;border:1px solid var(--cart-border);padding:12px 18px;border-radius:999px;box-shadow:0 12px 26px rgba(15,23,42,0.08);margin-bottom:24px;}
.order-steps__item{display:flex;align-items:center;gap:10px;font-weight:700;color:var(--cart-muted);}
.order-steps__item.is-active{color:var(--cart-ink);}
.order-steps__badge{width:30px;height:30px;border-radius:50%;display:grid;place-items:center;background:#e2e8f0;color:#0f172a;font-size:13px;font-weight:700;}
.order-steps__item.is-active .order-steps__badge{background:var(--cart-accent);box-shadow:0 10px 20px rgba(246,161,5,.35);}
.order-steps__line{width:52px;height:2px;background:#e2e8f0;}

.cart__table{border-collapse:separate;border-spacing:0 12px;}
.cart__table thead th{border:0;padding:12px 16px;color:var(--cart-muted);font-weight:700;text-transform:uppercase;font-size:12px;letter-spacing:.08em;}
.cart__table tbody tr{background:#fff;box-shadow:0 12px 26px rgba(15,23,42,0.08);}
.cart__table tbody td{border:0;padding:16px;vertical-align:middle;}
.cart__table tbody tr td:first-child{border-radius:16px 0 0 16px;}
.cart__table tbody tr td:last-child{border-radius:0 16px 16px 0;}
.cart__table .product__thumb img{border-radius:14px;box-shadow:0 10px 20px rgba(15,23,42,0.08);}
.cart__table .product__name a{font-weight:700;color:var(--cart-ink);}
.cart__table .product__price{font-weight:700;color:var(--cart-ink);}

.cart__actions{background:transparent !important;box-shadow:none !important;}
.cart__actions-form{display:flex;gap:12px;flex-wrap:wrap;align-items:center;}
.cart__actions-form input{flex:1 1 220px;border:1px solid var(--cart-border);border-radius:12px;padding:10px 12px;font-weight:600;}
.cart__actions-form .btn{border-radius:12px;background:var(--cart-accent);border:1px solid var(--cart-accent);color:#111827;font-weight:700;}
.cart__actions-form .btn:hover{opacity:.92;}

.cart__collaterals-wrap{border:1px solid var(--cart-border);border-radius:22px;background:#fff;box-shadow:0 18px 40px rgba(15,23,42,0.12);padding:20px;}
.cart__collaterals-wrap .title{font-weight:700;color:var(--cart-ink);margin-bottom:16px;}
.cart__collaterals-wrap .list-wrap{display:grid;gap:10px;margin:0 0 18px;padding:0;}
.cart__collaterals-wrap .list-wrap li{display:flex;align-items:center;justify-content:space-between;font-weight:700;color:var(--cart-ink);}
.cart__collaterals-wrap .amount{color:var(--cart-accent-dark);font-weight:700;}
.cart__collaterals-wrap .btn{width:100%;border-radius:14px;background:var(--cart-accent-dark);border:1px solid var(--cart-accent-dark);color:#fff;font-weight:700;padding:12px;}
.cart__collaterals-wrap .btn:hover{opacity:.92;}

.cart-remove{display:inline-flex;align-items:center;justify-content:center;width:34px;height:34px;border-radius:10px;border:1px solid #e2e8f0;background:#fff;color:#ef4444;font-weight:700;text-decoration:none;transition:transform .15s ease, box-shadow .15s ease;}
.cart-remove:hover{transform:translateY(-1px);box-shadow:0 10px 18px rgba(239,68,68,0.2);color:#ef4444;}
.cart-remove--mini{width:24px;height:24px;border-radius:6px;font-size:12px;}

@media (max-width:991.98px){
    .cart-ui{padding:18px;border-radius:22px;}
    .order-steps{border-radius:16px;}
}
@media (max-width:575.98px){
    .order-steps__line{display:none;}
    .cart__table{border-spacing:0 10px;}
    .cart__table tbody td{padding:12px;}
    .cart__actions-form{gap:8px;}
}
</style>
@endpush

@if (session('removeFromCart') &&
        $setting->google_tagmanager_status == 'active' &&
        $marketing_setting?->remove_from_cart)
    @php
        $removeFromCart = session('removeFromCart');
        session()->forget('removeFromCart');
    @endphp
    @push('scripts')
        <script>
            $(function() {
                dataLayer.push({
                    'event': 'removeFromCart',
                    'cart_details': @json($removeFromCart)
                });
            });
        </script>
    @endpush
@endif
