<?php

use Illuminate\Support\Facades\Route;
use Modules\BasicPayment\app\Http\Controllers\API\PaymentController as PaymentApiController;
use Modules\BasicPayment\app\Http\Controllers\API\PaypalPaymentController;
use Modules\BasicPayment\app\Http\Controllers\BasicPaymentController;
use Modules\BasicPayment\app\Http\Controllers\BraintreePaymentController;
use Modules\BasicPayment\app\Http\Controllers\FrontPaymentController;
use Modules\BasicPayment\app\Http\Controllers\PaymentController;
use Modules\BasicPayment\app\Http\Controllers\PaymentGatewayController;
use App\Http\Controllers\API\IyzicoNativePaymentController;

Route::group(['as' => 'admin.', 'prefix' => 'admin', 'middleware' => ['auth:admin', 'translation']], function () {

    Route::controller(BasicPaymentController::class)->group(function () {

        Route::get('basicpayment', 'basicpayment')->name('basicpayment');
        Route::put('update-stripe', 'update_stripe')->name('update-stripe');
        Route::put('update-paypal', 'update_paypal')->name('update-paypal');
        Route::put('update-bank-payment', 'update_bank_payment')->name('update-bank-payment');
        Route::put('update-offline-payment', 'update_offline_payment')->name('update-offline-payment');
        Route::put('update-braintree', 'update_braintree')->name('update-braintree');
        Route::put('mpesa-update', 'mpesa_update')->name('mpesa-update');

    });

    Route::controller(PaymentGatewayController::class)->group(function () {
        Route::put('razorpay-update', 'razorpay_update')->name('razorpay-update');
        Route::put('flutterwave-update', 'flutterwave_update')->name('flutterwave-update');
        Route::put('paystack-update', 'paystack_update')->name('paystack-update');
        Route::put('mollie-update', 'mollie_update')->name('mollie-update');
        Route::put('instamojo-update', 'instamojo_update')->name('instamojo-update');
        Route::put('azampay-update', 'azampay_update')->name('azampay-update');
        Route::put('xendit-update', 'xendit_update')->name('xendit-update');
        Route::put('iyzico-update', 'iyzico_update')->name('iyzico-update');
    });

});
Route::group(['middleware' => ['auth', 'verified']], function () {
    Route::controller(PaymentController::class)->group(function () {
        Route::post('place-order/{method}', 'placeOrder')->name('place.order');
        Route::get('payment', 'index')->name('payment');

        Route::get('payment-success', 'payment_success')->name('payment-success');
        Route::get('payment-failed', 'payment_failed')->name('payment-failed');

        Route::post('pay-via-bank', 'pay_via_bank')->name('pay-via-bank');
        Route::post('pay-via-offline', 'pay_via_offline')->name('pay-via-offline');
        Route::post('pay-via-free-gateway', 'pay_via_free_gateway')->name('pay-via-free-gateway');

        Route::get('pay-via-paypal', 'pay_via_paypal')->name('pay-via-paypal');
        Route::post('pay-via-stripe', 'pay_via_stripe')->name('pay-via-stripe');
        Route::get('pay-via-stripe', 'stripe_success')->name('stripe-success');

        Route::post('pay-via-razorpay', 'pay_via_razorpay')->name('pay-via-razorpay');

        Route::get('pay-via-mollie', 'pay_via_mollie')->name('pay-via-mollie');
        Route::get('mollie-payment-success', 'mollie_payment_success')->name('mollie-payment-success');

        Route::post('pay-via-flutterwave', 'flutterwave_payment')->name('pay-via-flutterwave');
        Route::get('pay-via-paystack', 'paystack_payment')->name('pay-via-paystack');

        Route::post('pay-with-azampay-mno', 'pay_with_azampay_mno')->name('pay-with-azampay-mno');
        Route::post('pay-with-azampay-by-bank', 'pay_with_azampay_by_bank')->name('pay-with-azampay-by-bank');

        Route::get('pay-via-instamojo', 'pay_via_instamojo')->name('pay-via-instamojo');
        Route::get('instamojo-success', 'instamojo_success')->name('instamojo-success');

        Route::get('pay-via-xendit', 'pay_via_xendit')->name('pay-via-xendit');
        Route::get('xendit-payment-verify', 'xendit_payment_verify')->name('xendit-payment-verify');

        Route::post('pay-via-mpesa', 'pay_via_mpesa')->name('pay-via-mpesa');
    });
    Route::controller(BraintreePaymentController::class)->group(function () {
        Route::get('braintree-token', 'token')->name('braintree.token');
        Route::post('braintree-checkout', 'checkout')->name('braintree.checkout');
    });
    Route::get('paypal-success-payment', [FrontPaymentController::class, 'paypal_success'])->name('paypal-success-payment');
});

Route::group(['as' => 'payment-api.'], function () {
    Route::get('app/payment', [PaymentApiController::class, 'payment'])->name('payment');

    Route::get('webview-success-payment', [PaymentApiController::class, 'payment_success'])->name('webview-success-payment')->middleware('payment.api');
    Route::get('webview-failed-payment', [PaymentApiController::class, 'payment_failed'])->name('webview-failed-payment');

    Route::get('paypal-webview', [PaypalPaymentController::class, 'pay_via_paypal'])->name('paypal-webview')->middleware('payment.api');
    Route::get('paypal-success', [PaypalPaymentController::class, 'paypal_success'])->name('paypal-success')->middleware('payment.api');

    Route::post('stripe-webview', [PaymentApiController::class, 'stripe_pay'])->middleware('payment.api')->name('stripe-webview');
    Route::get('stripe-webview', [PaymentApiController::class, 'stripe_success'])->middleware('payment.api');

    Route::get('mollie-webview', [PaymentApiController::class, 'pay_via_mollie'])->name('mollie-webview')->middleware('payment.api');
    Route::get('mollie-success', [PaymentApiController::class, 'mollie_success'])->name('mollie-success')->middleware('payment.api');

    Route::post('razorpay-webview', [PaymentApiController::class, 'pay_via_razorpay'])->name('razorpay-webview')->middleware('payment.api');
    Route::post('flutterwave-webview', [PaymentApiController::class, 'flutterwave_payment'])->name('flutterwave-webview')->middleware('payment.api');
    Route::get('paystack-webview', [PaymentApiController::class, 'paystack_payment'])->name('paystack-webview')->middleware('payment.api');

    Route::get('instamojo-webview', [PaymentApiController::class, 'pay_via_instamojo'])->name('instamojo-webview')->middleware('payment.api');
    Route::get('instamojo-webview-success', [PaymentApiController::class, 'instamojo_success'])->name('instamojo-success')->middleware('payment.api');

    Route::post('bank-webview', [PaymentApiController::class, 'pay_via_bank'])->name('bank-webview')->middleware('payment.api');
    Route::post('offline-webview', [PaymentApiController::class, 'pay_via_offline'])->name('offline-webview')->middleware('payment.api');

    Route::post('azampay-mno-webview', [PaymentApiController::class, 'pay_with_azampay_mno'])->name('azampay-mno-webview')->middleware('payment.api');
    Route::post('azampay-by-bank-webview', [PaymentApiController::class, 'pay_with_azampay_by_bank'])->name('azampay-by-bank-webview')->middleware('payment.api');

    Route::get('xendit-webview', [PaymentApiController::class, 'pay_via_xendit'])->name('xendit-webview')->middleware('payment.api');
    Route::get('xendit-webview-verify', [PaymentApiController::class, 'xendit_payment_verify'])->name('xendit.payment.verify')->middleware('payment.api');

    Route::post('mpesa-webview', [PaymentApiController::class, 'pay_via_mpesa'])->name('mpesa-webview')->middleware('payment.api');

    Route::controller(BraintreePaymentController::class)->group(function () {
        Route::get('braintree-webview-token', 'token')->name('braintree.token')->middleware('payment.api');
        Route::post('braintree-webview-checkout', 'checkout')->name('braintree.checkout')->middleware('payment.api');
    });
});

Route::post('iyzico-callback', [PaymentController::class, 'iyzico_callback'])->name('iyzico-callback');
Route::post('iyzico-3ds-callback', [IyzicoNativePaymentController::class, 'threeDsCallback'])->name('iyzico-3ds-callback');
