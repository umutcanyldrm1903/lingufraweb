<?php

namespace Modules\BasicPayment\app\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AzampayMnoInformationRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'account_number' => 'required|string|max:190',
            'external_id'   =>  'required',
            'provider' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'account_number.required' => __('Account Number is required.'),
            'provider.required' => __('Provider is required.'),
        ];
    }
}