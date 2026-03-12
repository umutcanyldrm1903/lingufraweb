<?php

namespace Modules\BasicPayment\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Modules\BasicPayment\app\Models\PaymentGateway;

class PaymentGatewayController extends Controller {
    public function razorpay_update(Request $request) {
        checkAdminHasPermissionAndThrowException('basic.payment.update');
        $rules = [
            'razorpay_key'         => 'required',
            'razorpay_secret'      => 'required',
            'razorpay_charge'      => 'required|numeric',
            'razorpay_name'        => 'required',
            'razorpay_description' => 'required',
            'razorpay_theme_color' => 'required',
        ];
        $customMessages = [
            'razorpay_key.required'         => __('Razorpay key is required'),
            'razorpay_secret.required'      => __('Razorpay secret is required'),
            'razorpay_charge.required'      => __('Gateway charge is required'),
            'razorpay_charge.numeric'       => __('Gateway charge should be numeric'),
            'razorpay_name.required'        => __('Name is required'),
            'razorpay_description.required' => __('Description is required'),
            'razorpay_theme_color.required' => __('Theme is required'),
        ];

        $request->validate($rules, $customMessages);

        PaymentGateway::where('key', 'razorpay_key')->update(['value' => $request->razorpay_key]);
        PaymentGateway::where('key', 'razorpay_secret')->update(['value' => $request->razorpay_secret]);
        PaymentGateway::where('key', 'razorpay_charge')->update(['value' => $request->razorpay_charge]);
        PaymentGateway::where('key', 'razorpay_status')->update(['value' => $request->razorpay_status]);
        PaymentGateway::where('key', 'razorpay_name')->update(['value' => $request->razorpay_name]);
        PaymentGateway::where('key', 'razorpay_description')->update(['value' => $request->razorpay_description]);
        PaymentGateway::where('key', 'razorpay_theme_color')->update(['value' => $request->razorpay_theme_color]);

        if ($request->file('razorpay_image')) {
            $razorpay_setting = PaymentGateway::where('key', 'razorpay_image')->first();
            $file_name = file_upload($request->razorpay_image, 'uploads/custom-images/', $razorpay_setting->value);
            $razorpay_setting->value = $file_name;
            $razorpay_setting->save();
        }

        $this->put_payment_cache();

        $notification = __('Update Successfully');
        $notification = ['messege' => $notification, 'alert-type' => 'success'];

        return redirect()->back()->with($notification);

    }

    public function flutterwave_update(Request $request) {
        checkAdminHasPermissionAndThrowException('basic.payment.update');
        $rules = [
            'flutterwave_public_key' => 'required',
            'flutterwave_secret_key' => 'required',
            'flutterwave_charge'     => 'required|numeric',
            'flutterwave_app_name'   => 'required',
        ];
        $customMessages = [
            'flutterwave_public_key.required' => __('Public key is required'),
            'flutterwave_secret_key.required' => __('Secret key is required'),
            'flutterwave_charge.required'     => __('Gateway charge is required'),
            'flutterwave_charge.numeric'      => __('Gateway charge should be numeric'),
            'flutterwave_app_name.required'   => __('Name is required'),
        ];

        $request->validate($rules, $customMessages);

        PaymentGateway::where('key', 'flutterwave_charge')->update(['value' => $request->flutterwave_charge]);
        PaymentGateway::where('key', 'flutterwave_public_key')->update(['value' => $request->flutterwave_public_key]);
        PaymentGateway::where('key', 'flutterwave_secret_key')->update(['value' => $request->flutterwave_secret_key]);
        PaymentGateway::where('key', 'flutterwave_app_name')->update(['value' => $request->flutterwave_app_name]);
        PaymentGateway::where('key', 'flutterwave_status')->update(['value' => $request->flutterwave_status]);

        if ($request->file('flutterwave_image')) {
            $flutterwave_setting = PaymentGateway::where('key', 'flutterwave_image')->first();
            $file_name = file_upload($request->flutterwave_image, 'uploads/custom-images/', $flutterwave_setting->value);
            $flutterwave_setting->value = $file_name;
            $flutterwave_setting->save();
        }

        $this->put_payment_cache();

        $notification = __('Update Successfully');
        $notification = ['messege' => $notification, 'alert-type' => 'success'];

        return redirect()->back()->with($notification);

    }

    public function paystack_update(Request $request) {
        checkAdminHasPermissionAndThrowException('basic.payment.update');
        $rules = [
            'paystack_public_key' => 'required',
            'paystack_secret_key' => 'required',
            'paystack_charge'     => 'required|numeric',
        ];
        $customMessages = [
            'paystack_public_key.required' => __('Public key is required'),
            'paystack_secret_key.required' => __('Secret key is required'),
            'paystack_charge.required'     => __('Gateway charge is required'),
            'paystack_charge.numeric'      => __('Gateway charge should be numeric'),
        ];

        $request->validate($rules, $customMessages);

        PaymentGateway::where('key', 'paystack_charge')->update(['value' => $request->paystack_charge]);
        PaymentGateway::where('key', 'paystack_public_key')->update(['value' => $request->paystack_public_key]);
        PaymentGateway::where('key', 'paystack_secret_key')->update(['value' => $request->paystack_secret_key]);
        PaymentGateway::where('key', 'paystack_status')->update(['value' => $request->paystack_status]);

        if ($request->file('paystack_image')) {
            $paystack_setting = PaymentGateway::where('key', 'paystack_image')->first();
            $file_name = file_upload($request->paystack_image, 'uploads/custom-images/', $paystack_setting->value);
            $paystack_setting->value = $file_name;
            $paystack_setting->save();
        }

        $this->put_payment_cache();

        $notification = __('Update Successfully');
        $notification = ['messege' => $notification, 'alert-type' => 'success'];

        return redirect()->back()->with($notification);

    }

    public function mollie_update(Request $request) {
        checkAdminHasPermissionAndThrowException('basic.payment.update');
        $rules = [
            'mollie_key'    => 'required',
            'mollie_charge' => 'required|numeric',
        ];
        $customMessages = [
            'mollie_key.required'    => __('Mollie key is required'),
            'mollie_charge.required' => __('Gateway charge is required'),
            'mollie_charge.numeric'  => __('Gateway charge should be numeric'),
        ];

        $request->validate($rules, $customMessages);

        PaymentGateway::where('key', 'mollie_charge')->update(['value' => $request->mollie_charge]);
        PaymentGateway::where('key', 'mollie_key')->update(['value' => $request->mollie_key]);
        PaymentGateway::where('key', 'mollie_status')->update(['value' => $request->mollie_status]);

        if ($request->file('mollie_image')) {
            $mollie_setting = PaymentGateway::where('key', 'mollie_image')->first();
            $file_name = file_upload($request->mollie_image, 'uploads/custom-images/', $mollie_setting->value);
            $mollie_setting->value = $file_name;
            $mollie_setting->save();
        }

        $this->put_payment_cache();

        $notification = __('Update Successfully');
        $notification = ['messege' => $notification, 'alert-type' => 'success'];

        return redirect()->back()->with($notification);
    }

    public function instamojo_update(Request $request) {
        checkAdminHasPermissionAndThrowException('basic.payment.update');
        $rules = [
            'instamojo_api_key'    => 'required',
            'instamojo_auth_token' => 'required',
            'instamojo_charge'     => 'required|numeric',
        ];
        $customMessages = [
            'instamojo_api_key.required'    => __('API key is required'),
            'instamojo_auth_token.required' => __('Auth token is required'),
            'instamojo_charge.required'     => __('Gateway charge is required'),
            'instamojo_charge.numeric'      => __('Gateway charge should be numeric'),
        ];

        $request->validate($rules, $customMessages);

        PaymentGateway::where('key', 'instamojo_charge')->update(['value' => $request->instamojo_charge]);
        PaymentGateway::where('key', 'instamojo_api_key')->update(['value' => $request->instamojo_api_key]);
        PaymentGateway::where('key', 'instamojo_auth_token')->update(['value' => $request->instamojo_auth_token]);
        PaymentGateway::where('key', 'instamojo_status')->update(['value' => $request->instamojo_status]);
        PaymentGateway::where('key', 'instamojo_account_mode')->update(['value' => $request->instamojo_account_mode]);

        if ($request->file('instamojo_image')) {
            $instamojo_setting = PaymentGateway::where('key', 'instamojo_image')->first();
            $file_name = file_upload($request->instamojo_image, 'uploads/custom-images/', $instamojo_setting->value);
            $instamojo_setting->value = $file_name;
            $instamojo_setting->save();
        }

        $this->put_payment_cache();

        $notification = __('Update Successfully');
        $notification = ['messege' => $notification, 'alert-type' => 'success'];

        return redirect()->back()->with($notification);
    }
    public function azampay_update(Request $request) {
        checkAdminHasPermissionAndThrowException('basic.payment.update');
        $rules = [
            'azampay_client_id'     => 'required',
            'azampay_client_secret' => 'required',
            'azampay_account_mode'  => 'required',
            'azampay_token'         => 'required',
            'azampay_charge'        => 'required|numeric',
            'azampay_status'        => 'required',
            'azampay_app_name'      => 'required',
        ];
        $customMessages = [
            'azampay_client_id.required'     => __('Client id is required'),
            'azampay_client_secret.required' => __('Client secret is required'),
            'azampay_account_mode.required'  => __('Account mode is required'),
            'azampay_token.required'         => __('Token is required'),
            'azampay_charge.required'        => __('Gateway charge is required'),
            'azampay_charge.numeric'         => __('Gateway charge should be numeric'),
            'azampay_app_name.required'      => __('Name is required'),
        ];

        $request->validate($rules, $customMessages);

        PaymentGateway::where('key', 'azampay_client_id')->update(['value' => $request->azampay_client_id]);
        PaymentGateway::where('key', 'azampay_client_secret')->update(['value' => $request->azampay_client_secret]);
        PaymentGateway::where('key', 'azampay_account_mode')->update(['value' => $request->azampay_account_mode]);
        PaymentGateway::where('key', 'azampay_token')->update(['value' => $request->azampay_token]);
        PaymentGateway::where('key', 'azampay_charge')->update(['value' => $request->azampay_charge]);
        PaymentGateway::where('key', 'azampay_status')->update(['value' => $request->azampay_status]);
        PaymentGateway::where('key', 'azampay_app_name')->update(['value' => $request->azampay_app_name]);

        if ($request->file('azampay_image')) {
            $azampay_setting = PaymentGateway::where('key', 'azampay_image')->first();
            $file_name = file_upload($request->azampay_image, 'uploads/custom-images/', $azampay_setting->value);
            $azampay_setting->value = $file_name;
            $azampay_setting->save();
        }

        cache()->forget('payment_setting');

        $notification = __('Update Successfully');
        $notification = ['messege' => $notification, 'alert-type' => 'success'];

        return redirect()->back()->with($notification);

    }
    public function xendit_update(Request $request) {
        checkAdminHasPermissionAndThrowException('basic.payment.update');
        $rules = [
            'xendit_api_key'      => 'required',
            'xendit_charge'       => 'required|numeric',
            'xendit_status'       => 'required',
        ];
        $customMessages = [
            'xendit_api_key.required'      => __('API key is required'),
            'xendit_charge.required'       => __('Gateway charge is required'),
            'xendit_charge.numeric'        => __('Gateway charge should be numeric'),
        ];

        $request->validate($rules, $customMessages);

        PaymentGateway::where('key', 'xendit_api_key')->update(['value' => $request->xendit_api_key]);
        PaymentGateway::where('key', 'xendit_charge')->update(['value' => $request->xendit_charge]);
        PaymentGateway::where('key', 'xendit_status')->update(['value' => $request->xendit_status]);

        if ($request->file('xendit_image')) {
            $xendit_setting = PaymentGateway::where('key', 'xendit_image')->first();
            $file_name = file_upload($request->xendit_image, 'uploads/custom-images/', $xendit_setting->value);
            $xendit_setting->value = $file_name;
            $xendit_setting->save();
        }

        cache()->forget('payment_setting');

        $notification = __('Update Successfully');
        $notification = ['messege' => $notification, 'alert-type' => 'success'];

        return redirect()->back()->with($notification);

    }

    public function iyzico_update(Request $request) {
        checkAdminHasPermissionAndThrowException('basic.payment.update');
        $rules = [
            'iyzico_api_key'      => 'required',
            'iyzico_secret_key'   => 'required',
            'iyzico_account_mode' => 'required',
            'iyzico_charge'       => 'required|numeric',
            'iyzico_status'       => 'required',
        ];
        $customMessages = [
            'iyzico_api_key.required'      => __('API key is required'),
            'iyzico_secret_key.required'   => __('Secret key is required'),
            'iyzico_account_mode.required' => __('Account mode is required'),
            'iyzico_charge.required'       => __('Gateway charge is required'),
            'iyzico_charge.numeric'        => __('Gateway charge should be numeric'),
        ];

        $request->validate($rules, $customMessages);

        $upsertGatewayValue = function (string $key, $value): void {
            $gateway = PaymentGateway::where('key', $key)->first();
            if (!$gateway) {
                $gateway = new PaymentGateway();
                $gateway->key = $key;
            }
            $gateway->value = $value;
            $gateway->save();
        };

        $upsertGatewayValue('iyzico_api_key', $request->iyzico_api_key);
        $upsertGatewayValue('iyzico_secret_key', $request->iyzico_secret_key);
        $upsertGatewayValue('iyzico_account_mode', $request->iyzico_account_mode);
        $upsertGatewayValue('iyzico_charge', $request->iyzico_charge);
        $upsertGatewayValue('iyzico_status', $request->iyzico_status);

        if ($request->file('iyzico_image')) {
            $iyzico_setting = PaymentGateway::where('key', 'iyzico_image')->first();
            if (!$iyzico_setting) {
                $iyzico_setting = new PaymentGateway();
                $iyzico_setting->key = 'iyzico_image';
                $iyzico_setting->value = 'uploads/website-images/iyzico.png';
            }
            $file_name = file_upload($request->iyzico_image, 'uploads/custom-images/', $iyzico_setting->value);
            $iyzico_setting->value = $file_name;
            $iyzico_setting->save();
        }

        $this->put_payment_cache();

        $notification = __('Update Successfully');
        $notification = ['messege' => $notification, 'alert-type' => 'success'];

        return redirect()->back()->with($notification);
    }

    private function put_payment_cache() {
        $payment_info = PaymentGateway::get();
        $payment_setting = [];
        foreach ($payment_info as $payment_item) {
            $payment_setting[$payment_item->key] = $payment_item->value;
        }

        $payment_setting = (object) $payment_setting;
        Cache::put('payment_setting', $payment_setting);
    }
}
