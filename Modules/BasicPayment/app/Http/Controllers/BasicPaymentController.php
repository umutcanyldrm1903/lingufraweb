<?php

namespace Modules\BasicPayment\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Modules\BasicPayment\app\Models\BasicPayment;
use Modules\BasicPayment\app\Models\PaymentGateway;
use Modules\Currency\app\Models\MultiCurrency;

class BasicPaymentController extends Controller {
    public function basicpayment() {
        checkAdminHasPermissionAndThrowException('basic.payment.view');
        $payment_info = BasicPayment::get();

        $basic_payment = [];

        foreach ($payment_info as $payment_item) {
            $basic_payment[$payment_item->key] = $payment_item->value;
        }

        $basic_payment = (object) $basic_payment;

        $payment_info = PaymentGateway::get();

        $payment_setting = [];
        foreach ($payment_info as $payment_item) {
            $payment_setting[$payment_item->key] = $payment_item->value;
        }

        $payment_setting = (object) $payment_setting;

        $currencies = MultiCurrency::get();

        return view('basicpayment::index', compact('payment_setting', 'basic_payment', 'currencies'));
    }

    public function update_stripe(Request $request) {
        checkAdminHasPermissionAndThrowException('basic.payment.update');
        $rules = [
            'stripe_key'    => 'required',
            'stripe_secret' => 'required',
            'stripe_charge' => 'required|numeric',
        ];
        $customMessages = [
            'stripe_key.required'    => __('Stripe key is required'),
            'stripe_secret.required' => __('Stripe secret is required'),
            'stripe_charge.required' => __('Gateway charge is required'),
            'stripe_charge.numeric'  => __('Gateway charge should be numeric'),
        ];

        $request->validate($rules, $customMessages);

        BasicPayment::where('key', 'stripe_key')->update(['value' => $request->stripe_key]);
        BasicPayment::where('key', 'stripe_secret')->update(['value' => $request->stripe_secret]);
        BasicPayment::where('key', 'stripe_charge')->update(['value' => $request->stripe_charge]);
        BasicPayment::where('key', 'stripe_status')->update(['value' => $request->stripe_status]);

        if ($request->file('stripe_image')) {
            $stripe_setting = BasicPayment::where('key', 'stripe_image')->first();
            $file_name = file_upload($request->stripe_image, 'uploads/custom-images/', $stripe_setting->value);
            $stripe_setting->value = $file_name;
            $stripe_setting->save();
        }
        $this->put_basic_payment_cache();

        $notification = __('Update Successfully');
        $notification = ['messege' => $notification, 'alert-type' => 'success'];

        return redirect()->back()->with($notification);
    }

    public function update_paypal(Request $request) {
        checkAdminHasPermissionAndThrowException('basic.payment.update');

        $rules = [
            'paypal_client_id'  => 'required',
            'paypal_secret_key' => 'required',
            'paypal_charge'     => 'required|numeric',
        ];

        $customMessages = [
            'paypal_client_id.required'  => __('Client is required'),
            'paypal_secret_key.required' => __('Secret key is required'),
            'paypal_charge.required'     => __('Gateway charge is required'),
            'paypal_charge.numeric'      => __('Gateway charge should be numeric'),
        ];

        $request->validate($rules, $customMessages);

        BasicPayment::where('key', 'paypal_client_id')->update(['value' => $request->paypal_client_id]);
        BasicPayment::where('key', 'paypal_secret_key')->update(['value' => $request->paypal_secret_key]);
        BasicPayment::where('key', 'paypal_charge')->update(['value' => $request->paypal_charge]);
        BasicPayment::where('key', 'paypal_status')->update(['value' => $request->paypal_status]);
        BasicPayment::where('key', 'paypal_account_mode')->update(['value' => $request->paypal_account_mode]);

        if ($request->file('paypal_image')) {
            $paypal_setting = BasicPayment::where('key', 'paypal_image')->first();
            $file_name = file_upload($request->paypal_image, 'uploads/custom-images/', $paypal_setting->value);
            $paypal_setting->value = $file_name;
            $paypal_setting->save();
        }
        $this->put_basic_payment_cache();

        $notification = __('Update Successfully');
        $notification = ['messege' => $notification, 'alert-type' => 'success'];

        return redirect()->back()->with($notification);
    }

    public function update_bank_payment(Request $request) {
        checkAdminHasPermissionAndThrowException('basic.payment.update');

        $rules = [
            'bank_information' => 'required',
        ];

        $customMessages = [
            'bank_information.required' => __('Bank information is required'),
            'bank_charge.required'      => __('Gateway charge is required'),
            'bank_charge.numeric'       => __('Gateway charge should be numeric'),
        ];

        $request->validate($rules, $customMessages);

        BasicPayment::where('key', 'bank_information')->update(['value' => $request->bank_information]);
        BasicPayment::where('key', 'bank_charge')->update(['value' => 0]);
        BasicPayment::where('key', 'bank_status')->update(['value' => $request->bank_status]);

        if ($request->file('bank_image')) {
            $bank_setting = BasicPayment::where('key', 'bank_image')->first();
            $file_name = file_upload($request->bank_image, 'uploads/custom-images/', $bank_setting->value);
            $bank_setting->value = $file_name;
            $bank_setting->save();
        }

        $this->put_basic_payment_cache();

        $notification = __('Update Successfully');
        $notification = ['messege' => $notification, 'alert-type' => 'success'];

        return redirect()->back()->with($notification);
    }
    public function update_offline_payment(Request $request) {
        checkAdminHasPermissionAndThrowException('basic.payment.update');

        BasicPayment::where('key', 'offline_charge')->update(['value' => 0]);
        BasicPayment::where('key', 'offline_status')->update(['value' => $request->offline_status]);

        if ($request->file('offline_image')) {
            $offline_setting = BasicPayment::where('key', 'offline_image')->first();
            $file_name = file_upload($request->offline_image, 'uploads/custom-images/', $offline_setting->value);
            $offline_setting->value = $file_name;
            $offline_setting->save();
        }

        $this->put_basic_payment_cache();

        $notification = __('Update Successfully');
        $notification = ['messege' => $notification, 'alert-type' => 'success'];

        return redirect()->back()->with($notification);
    }
    public function update_braintree(Request $request) {
        checkAdminHasPermissionAndThrowException('basic.payment.update');

        $rules = [
            'braintree_merchant_id' => 'required',
            'braintree_public_key'  => 'required',
            'braintree_private_key' => 'required',
            'braintree_charge'      => 'required|numeric',
            'braintree_currency'    => 'required|exists:multi_currencies,currency_code',
        ];
        $customMessages = [
            'braintree_merchant_id.required' => __('Merchant ID is required'),
            'braintree_public_key.required'  => __('Public key is required'),
            'braintree_private_key.required' => __('Private key is required'),
            'braintree_charge.required'      => __('Gateway charge is required'),
            'braintree_charge.numeric'       => __('Gateway charge should be numeric'),
            'braintree_currency.required'    => __('Currency name is required'),
            'braintree_currency.exists'      => __('Currency name is invalid.'),
        ];

        $request->validate($rules, $customMessages);

        BasicPayment::where('key', 'braintree_account_mode')->update(['value' => $request->braintree_account_mode]);
        BasicPayment::where('key', 'braintree_merchant_id')->update(['value' => $request->braintree_merchant_id]);
        BasicPayment::where('key', 'braintree_public_key')->update(['value' => $request->braintree_public_key]);
        BasicPayment::where('key', 'braintree_private_key')->update(['value' => $request->braintree_private_key]);
        BasicPayment::where('key', 'braintree_charge')->update(['value' => $request->braintree_charge]);
        BasicPayment::where('key', 'braintree_status')->update(['value' => $request->braintree_status]);
        BasicPayment::where('key', 'braintree_currency')->update(['value' => $request->braintree_currency]);

        if ($request->file('braintree_image')) {
            $braintree_setting = BasicPayment::where('key', 'braintree_image')->first();
            $file_name = file_upload($request->braintree_image, 'uploads/custom-images/', $braintree_setting->value);
            $braintree_setting->value = $file_name;
            $braintree_setting->save();
        }

        $this->put_basic_payment_cache();

        $notification = __('Update Successfully');
        $notification = ['messege' => $notification, 'alert-type' => 'success'];

        return redirect()->back()->with($notification);
    }
    public function mpesa_update(Request $request) {
        checkAdminHasPermissionAndThrowException('basic.payment.update');
        $rules = [
            'mpesa_account_mode' => 'required|in:sandbox,openapi',
            'mpesa_api_key'      => 'required',
            'mpesa_public_key'   => 'required',
            'mpesa_market'       => 'required|in:vodafoneGHA,vodacomTZN,vodacomLES,vodacomDRC,vodacomMOZ',
            'mpesa_origin'       => 'required',
            'mpesa_shortcode'    => 'required',
            'mpesa_charge'       => 'required|numeric',
            'mpesa_status'       => 'required',
        ];
        $customMessages = [
            'mpesa_account_mode.required' => __('Account Mode is required'),
            'mpesa_account_mode.in'       => __('Selected Account Mode is invalid'),
            'mpesa_api_key.required'      => __('API key is required'),
            'mpesa_public_key.required'   => __('Public key is required'),
            'mpesa_market.required'       => __('Market is required'),
            'mpesa_market.in'             => __('Selected market is invalid'),
            'mpesa_origin.required'       => __('Origin is required'),
            'mpesa_shortcode.required'    => __('ShortCode is required'),
            'mpesa_charge.required'       => __('Gateway charge is required'),
            'mpesa_charge.numeric'        => __('Gateway charge should be numeric'),
        ];

        $request->validate($rules, $customMessages);
        $mpesa_country = strtoupper(substr($request->mpesa_market, -3));

        BasicPayment::where('key', 'mpesa_account_mode')->update(['value' => $request->mpesa_account_mode]);
        BasicPayment::where('key', 'mpesa_market')->update(['value' => $request->mpesa_market]);
        BasicPayment::where('key', 'mpesa_country')->update(['value' => $mpesa_country]);
        BasicPayment::where('key', 'mpesa_api_key')->update(['value' => $request->mpesa_api_key]);
        BasicPayment::where('key', 'mpesa_public_key')->update(['value' => $request->mpesa_public_key]);
        BasicPayment::where('key', 'mpesa_origin')->update(['value' => $request->mpesa_origin]);
        BasicPayment::where('key', 'mpesa_shortcode')->update(['value' => $request->mpesa_shortcode]);
        BasicPayment::where('key', 'mpesa_charge')->update(['value' => $request->mpesa_charge]);
        BasicPayment::where('key', 'mpesa_status')->update(['value' => $request->mpesa_status]);

        if ($request->file('mpesa_image')) {
            $mpesa_setting = BasicPayment::where('key', 'mpesa_image')->first();
            $file_name = file_upload($request->mpesa_image, 'uploads/custom-images/', $mpesa_setting->value);
            $mpesa_setting->value = $file_name;
            $mpesa_setting->save();
        }

        $this->put_basic_payment_cache();

        $notification = __('Update Successfully');
        $notification = ['messege' => $notification, 'alert-type' => 'success'];

        return redirect()->back()->with($notification);
    }

    private function put_basic_payment_cache() {
        $payment_info = BasicPayment::get();
        $basic_payment = [];
        foreach ($payment_info as $payment_item) {
            $basic_payment[$payment_item->key] = $payment_item->value;
        }
        $basic_payment = (object) $basic_payment;
        Cache::put('basic_payment', $basic_payment);
    }
}
