<?php

namespace Modules\BasicPayment\app\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AzampayBankInformationRequest extends FormRequest {
    public function authorize() {
        return true;
    }

    public function rules() {
        return [
            'merchant_account_number' => 'required',
            'merchant_mobile_number'  => 'required',
            'merchant_name'           => 'required',
            'otp'                     => 'required',
            'reference_id'            => 'required',
            'provider'                => 'required',
        ];
    }

    public function messages() {
        return [
            'merchant_account_number.required' => __('Merchant Account Number is required.'),
            'merchant_mobile_number.required'  => __('Merchant Mobile Number is required.'),
            'merchant_name.required'           => __('Merchant is required.'),
            'otp.required'                     => __('OTP is required.'),
            'reference_id.required'            => __('Reference id is required.'),
            'provider.required'                => __('Provider is required.'),
        ];
    }
}