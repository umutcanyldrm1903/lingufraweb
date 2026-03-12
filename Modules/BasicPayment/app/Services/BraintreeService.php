<?php

namespace Modules\BasicPayment\app\Services;

use App\Traits\GetGlobalInformationTrait;
use Braintree\Gateway;

class BraintreeService {
    use GetGlobalInformationTrait;
    protected $gateway;

    public function __construct() {
        $basicPayment = $this->get_basic_payment_info();

        $this->gateway = new Gateway([
            'environment' => $basicPayment?->braintree_account_mode,
            'merchantId'  => $basicPayment?->braintree_merchant_id,
            'publicKey'   => $basicPayment?->braintree_public_key,
            'privateKey'  => $basicPayment?->braintree_private_key,
        ]);
    }

    public function gateway() {
        return $this->gateway;
    }
}
