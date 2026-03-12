<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Traits\GetGlobalInformationTrait;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Support\Facades\Session;

class CheckOutController extends Controller
{
    use GetGlobalInformationTrait;
    function index()
    {
        $user = userAuth();
        $planCart = Session::get('student_plan_cart');
        $planCartCount = $planCart ? 1 : 0;

        $courseCartCount = $user->cart_count;
        $cart_count = $courseCartCount + $planCartCount;
    
        if($cart_count == 0){
            return redirect()->route('courses')->with(['messege' => __('Please add some courses in your cart.'), 'alert-type' => 'error']);
        }

        if ($planCartCount > 0 && $courseCartCount > 0) {
            return redirect()->route('cart')->with([
                'messege' => __('Paket ve kurs aynı sepette satın alınamaz. Lütfen birini kaldırın.'),
                'alert-type' => 'error',
            ]);
        }

        $planPrice = is_array($planCart) ? (float) ($planCart['price'] ?? 0) : 0;
        $cartTotal = $user->cart_total + $planPrice;
        $discountPercent = Session::has('offer_percentage') ? Session::get('offer_percentage') : 0;
        $discountAmount = ($cartTotal * $discountPercent) / 100;
        $total = currency($cartTotal - $discountAmount);
        $coupon = Session::has('coupon_code') ? Session::get('coupon_code') : '';

        $payable_amount = $cartTotal - $discountAmount;
        Session::put('payable_amount', $payable_amount);

        $paymentService = app(\Modules\BasicPayment\app\Services\PaymentMethodService::class);
        $activeGateways = $paymentService->getActiveGatewaysWithDetails();


        return view('frontend.pages.checkout')->with([
            'cart_count' => $cart_count,
            'total' => $total,
            'discountAmount' => $discountAmount,
            'discountPercent' => $discountPercent,
            'coupon' => $coupon,
            'payable_amount' => $payable_amount,
            'paymentService' => $paymentService,
            'activeGateways' => $activeGateways,
        ]);
    }
}
