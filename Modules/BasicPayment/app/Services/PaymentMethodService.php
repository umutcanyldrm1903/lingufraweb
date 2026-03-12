<?php

namespace Modules\BasicPayment\app\Services;

use App\Traits\GetGlobalInformationTrait;
use Illuminate\Support\Facades\Session;
use Modules\BasicPayment\app\Enums\BasicPaymentSupportedCurrencyListEnum;
use Modules\BasicPayment\app\Interfaces\PaymentMethodInterface;
use Modules\Currency\app\Models\MultiCurrency;

class PaymentMethodService implements PaymentMethodInterface {
    use GetGlobalInformationTrait;

    const STRIPE = 'stripe';

    const PAYPAL = 'paypal';

    const BANK_PAYMENT = 'bank';
    const OFFLINE_PAYMENT = 'offline';
    const BRAINTREE_PAYMENT = 'braintree';
    const MPESA = 'mpesa';

    // Holds the supported payment gateways
    protected static array $supportedPayments = [
        self::STRIPE,
        self::PAYPAL,
        self::BANK_PAYMENT,
        self::OFFLINE_PAYMENT,
        self::BRAINTREE_PAYMENT,
        self::MPESA,
    ];

    /**
     * @param array $additionalPayments
     */
    public static function extendSupportedPayments(array $additionalPayments): void {
        static::$supportedPayments = array_unique(
            array_merge(static::$supportedPayments, $additionalPayments)
        );
    }

    // Holds the gateways that supports multi currency
    protected static array $multiCurrencySupported = [
        self::STRIPE,
        self::PAYPAL,
        self::BANK_PAYMENT,
        self::OFFLINE_PAYMENT,
        self::BRAINTREE_PAYMENT,
        self::MPESA,
    ];

    /**
     * @param array $additionalGateways
     */
    public static function extendMultiCurrencySupported(array $additionalGateways): void {
        static::$multiCurrencySupported = array_unique(
            array_merge(static::$multiCurrencySupported, $additionalGateways)
        );
    }

    /**
     * Undocumented function
     */
    public function getSupportedPayments(): array {
        return self::$supportedPayments;
    }

    /**
     * Get the value of the current gateway's constant.
     */
    public function getValue($currentGateway): ?string {
        return in_array($currentGateway, self::$supportedPayments, true)
        ? $currentGateway
        : null;
    }

    /**
     * Undocumented function
     */
    public function isSupportedGateway(string $gatewayName): bool {
        return in_array(strtolower($gatewayName), self::$supportedPayments, true);
    }

    /**
     * Undocumented function
     */
    public function isSupportsMultiCurrency(string $gatewayName): bool {
        return in_array(strtolower($gatewayName), self::$multiCurrencySupported, true);
    }

    /**
     * Undocumented function
     */
    public function getPaymentName(string $gatewayName): ?string {
        return match ($gatewayName) {
            self::STRIPE => 'Stripe',
            self::PAYPAL => 'PayPal',
            self::BANK_PAYMENT => 'Bank Payment',
            self::OFFLINE_PAYMENT => 'Offline Payment',
            self::BRAINTREE_PAYMENT => 'Braintree Payment',
            self::MPESA => 'Mpesa',
            default => null,
        };
    }

    /**
     * Undocumented function
     */
    public function getGatewayDetails(string $gatewayName): ?object {
        $basicPayment = $this->get_basic_payment_info();

        return match ($gatewayName) {
            self::STRIPE => (object) [
                'stripe_key'    => $basicPayment->stripe_key ?? null,
                'stripe_secret' => $basicPayment->stripe_secret ?? null,
                'currency_id'   => $basicPayment->stripe_currency_id ?? null,
                'stripe_status' => $basicPayment->stripe_status ?? null,
                'charge'        => $basicPayment->stripe_charge ?? null,
                'stripe_image'  => $basicPayment->stripe_image ?? null,
            ],
            self::PAYPAL => (object) [
                'paypal_client_id'    => $basicPayment->paypal_client_id ?? null,
                'paypal_secret_key'   => $basicPayment->paypal_secret_key ?? null,
                'paypal_account_mode' => $basicPayment->paypal_account_mode ?? null,
                'currency_id'         => $basicPayment->paypal_currency_id ?? null,
                'charge'              => $basicPayment->paypal_charge ?? null,
                'paypal_status'       => $basicPayment->paypal_status ?? null,
                'paypal_image'        => $basicPayment->paypal_image ?? null,
            ],
            self::BANK_PAYMENT => (object) [
                'bank_information' => $basicPayment->bank_information ?? null,
                'bank_status'      => $basicPayment->bank_status ?? null,
                'bank_image'       => $basicPayment->bank_image ?? null,
                'charge'           => $basicPayment->bank_charge ?? null,
                'currency_id'      => $basicPayment->bank_currency_id ?? null,
            ],
            self::OFFLINE_PAYMENT => (object) [
                'offline_status'      => $basicPayment->offline_status ?? null,
                'offline_image'       => $basicPayment->offline_image ?? null,
                'charge'           => $basicPayment->offline_charge ?? null,
                'currency_id'      => $basicPayment->offline_currency_id ?? null,
            ],
            self::BRAINTREE_PAYMENT => (object) [
                'braintree_status'      => $basicPayment->braintree_status ?? null,
                'braintree_currency'      => $basicPayment->braintree_currency ?? null,
                'braintree_image'       => $basicPayment->braintree_image ?? null,
                'charge'           => $basicPayment->braintree_charge ?? null,
                'currency_id'      => $basicPayment->braintree_currency_id ?? null,
                'braintree_account_mode'      => $basicPayment->braintree_account_mode ?? null,
                'braintree_merchant_id'      => $basicPayment->braintree_merchant_id ?? null,
                'braintree_public_key'      => $basicPayment->braintree_public_key ?? null,
                'braintree_private_key'      => $basicPayment->braintree_private_key ?? null,
            ],
            self::MPESA => (object) [
                'mpesa_market'     => $basicPayment->mpesa_market ?? null,
                'mpesa_country'    => $basicPayment->mpesa_country ?? null,
                'mpesa_origin'     => $basicPayment->mpesa_origin ?? null,
                'mpesa_shortcode'  => $basicPayment->mpesa_shortcode ?? null,
                'mpesa_api_key'    => $basicPayment->mpesa_api_key ?? null,
                'mpesa_public_key' => $basicPayment->mpesa_public_key ?? null,
                'charge'           => $basicPayment->mpesa_charge ?? null,
                'mpesa_image'      => $basicPayment->mpesa_image ?? null,
                'mpesa_status'     => $basicPayment->mpesa_status ?? null,
            ],
            
            default => (object) false,
        };
    }

    /**
     * Undocumented function
     */
    public function isActive(string $gatewayName): bool {
        $gatewayDetails = $this->getGatewayDetails($gatewayName);
        $activeStatus = config('basicpayment.default_status.active_text');

        return match ($gatewayName) {
            self::STRIPE => $gatewayDetails->stripe_status == $activeStatus,
            self::PAYPAL => $gatewayDetails->paypal_status == $activeStatus,
            self::BANK_PAYMENT => $gatewayDetails->bank_status == $activeStatus,
            self::OFFLINE_PAYMENT => $gatewayDetails->offline_status == $activeStatus,
            self::BRAINTREE_PAYMENT => $gatewayDetails->braintree_status == $activeStatus,
            self::MPESA => $gatewayDetails->mpesa_status == $activeStatus,
            default => false,
        };
    }

    /**
     * Undocumented function
     */
    public function getIcon(string $gatewayName): string {
        return match ($gatewayName) {
            self::STRIPE => 'fa-cc-stripe',
            self::PAYPAL => 'fa-cc-paypal',
            self::BANK_PAYMENT => 'fa-credit-card',
            self::OFFLINE_PAYMENT => 'fa-cash-register',
            self::BRAINTREE_PAYMENT => 'fa-university',
            self::MPESA => 'fa-building-columns',
            default => null,
        };
    }

    /**
     * Undocumented function
     *
     * @param [type] $gatewayName
     */
    public function getLogo($gatewayName): ?string {
        $basicPayment = $this->get_basic_payment_info();

        return match ($gatewayName) {
            self::STRIPE => $basicPayment->stripe_image ? asset($basicPayment->stripe_image) : asset('uploads/website-images/stripe.png'),
            self::PAYPAL => $basicPayment->paypal_image ? asset($basicPayment->paypal_image) : asset('uploads/website-images/paypal.png'),
            self::BANK_PAYMENT => $basicPayment->bank_image ? asset($basicPayment->bank_image) : asset('uploads/website-images/bank-pay.png'),
            self::OFFLINE_PAYMENT => $basicPayment->offline_image ? asset($basicPayment->offline_image) : asset('uploads/website-images/offline_payment.webp'),
            self::BRAINTREE_PAYMENT => $basicPayment->braintree_image ? asset($basicPayment->braintree_image) : asset('uploads/website-images/braintree.webp'),
            self::MPESA => $basicPayment?->mpesa_image ? asset($basicPayment?->mpesa_image) : asset('uploads/website-images/mpesa.webp'),
            default => null,
        };
    }


    protected static array $additionalActiveGateways = [];
    /**
     * Add additional active gateways to the list.
     *
     * @param array $additionalActiveGatewaysList
     */
    public static function additionalActiveGatewaysList(array $additionalActiveGatewaysList): void
    {
        static::$additionalActiveGateways = array_merge(static::$additionalActiveGateways, $additionalActiveGatewaysList);
    }
    public function getActiveGatewaysWithDetails(): array
    {
        $basicPayment = $this->get_basic_payment_info();
        $activeStatus = config('basicpayment.default_status.active_text');

        // Base gateways
        $gateways = [
            self::STRIPE => [
                'name' => 'Stripe',
                'logo' => asset($basicPayment->stripe_image ?? 'uploads/website-images/stripe.png'),
                'status' => $basicPayment->stripe_status == $activeStatus,
            ],
            self::PAYPAL => [
                'name' => 'PayPal',
                'logo' => asset($basicPayment->paypal_image ?? 'uploads/website-images/paypal.png'),
                'status' => $basicPayment->paypal_status == $activeStatus,
            ],
            self::BANK_PAYMENT => [
                'name' => 'Bank Payment',
                'logo' => asset($basicPayment->bank_image ?? 'uploads/website-images/bank-pay.png'),
                'status' => $basicPayment->bank_status == $activeStatus,
            ],
            self::OFFLINE_PAYMENT => [
                'name' => 'Offline Payment',
                'logo' => asset($basicPayment->offline_image ?? 'uploads/website-images/offline_payment.webp'),
                'status' => $basicPayment->offline_status == $activeStatus,
            ],
            self::BRAINTREE_PAYMENT => [
                'name' => 'Braintree Payment',
                'logo' => asset($basicPayment?->braintree_image ?? 'uploads/website-images/braintree.webp'),
                'status' => $basicPayment->braintree_status == $activeStatus,
            ],
            self::MPESA       => [
                'name'   => 'Mpesa',
                'logo'   => asset($basicPayment?->mpesa_image ?? 'uploads/website-images/mpesa.webp'),
                'status' => $basicPayment?->mpesa_status == $activeStatus,
            ],
        ];

        // Merge base gateways with additional gateways
        $allGateways = array_merge($gateways, static::$additionalActiveGateways);

        // Filter only active gateways
        return array_filter($allGateways, fn($gateway) => $gateway['status'] === true);
    }

    /**
     * Undocumented function
     *
     * @param [type] $gatewayName
     * @param [type] $code
     */
    public function isCurrencySupported($gatewayName, $code = null): bool {
        if (is_null($code)) {
            $code = getSessionCurrency();
        }
        $basicPayment = $this->get_basic_payment_info();

        return match ($gatewayName) {
            self::STRIPE => BasicPaymentSupportedCurrencyListEnum::isStripeSupportedCurrencies($code),
            self::PAYPAL => BasicPaymentSupportedCurrencyListEnum::isPaypalSupportedCurrencies($code),
            self::BANK_PAYMENT => str($code)->lower() == str(MultiCurrency::where('is_default', 'yes')->first()->currency_code)->lower(),
            self::OFFLINE_PAYMENT => str($code)->lower() == str(MultiCurrency::where('is_default', 'yes')->first()->currency_code)->lower(),
            self::BRAINTREE_PAYMENT => str($code)->lower() == str($basicPayment?->braintree_currency ?? 'USD')->lower(),
            self::MPESA => BasicPaymentSupportedCurrencyListEnum::isMpesaSupportedCurrencies($code),
            default => false,
        };
    }

    /**
     * Undocumented function
     *
     * @param [type] $gatewayName
     */
    public function getSupportedCurrencies($gatewayName): array {
        $basicPayment = $this->get_basic_payment_info();
        
        return match ($gatewayName) {
            self::STRIPE => BasicPaymentSupportedCurrencyListEnum::getStripeSupportedCurrencies(),
            self::PAYPAL => BasicPaymentSupportedCurrencyListEnum::getPaypalSupportedCurrencies(),
            self::BANK_PAYMENT => MultiCurrency::where('is_default', 'yes')->pluck('currency_code')->toArray(),
            self::OFFLINE_PAYMENT => MultiCurrency::where('is_default', 'yes')->pluck('currency_code')->toArray(),
            self::BRAINTREE_PAYMENT => [$basicPayment?->braintree_currency],
            self::MPESA => BasicPaymentSupportedCurrencyListEnum::getMpesaSupportedCurrencies(),
            default => [],
        };
    }

    /**
     * @param string $gatewayName
     */
    public function getBladeView(string $gatewayName): ?string {
        return match ($gatewayName) {
            self::STRIPE => 'basicpayment::gateway-actions.stripe',
            self::PAYPAL => 'basicpayment::gateway-actions.paypal',
            self::BANK_PAYMENT => 'basicpayment::gateway-actions.bank',
            self::OFFLINE_PAYMENT => 'basicpayment::gateway-actions.offline',
            self::BRAINTREE_PAYMENT => 'basicpayment::gateway-actions.braintree',
            self::MPESA => 'basicpayment::gateway-actions.mpesa',
            default => null,
        };
    }

    /**
     * Undocumented function
     *
     * @param $gatewayName
     * @param $amount
     * @param $currency_code
     */
    public function getPayableAmount($gatewayName, $amount,$currency_code = null): object {
        return $this->calculate_payable_charge($amount, $gatewayName,$currency_code);
    }

    /**
     * Undocumented function
     */
    public static function removeSessions(): void {
        Session::forget([
            'after_success_url',
            'after_failed_url',
            'order',
            'payable_amount',
            'gateway_charge',
            'after_success_gateway',
            'after_success_transaction',
            'subscription_plan_id',
            'payable_with_charge',
            'payable_currency',
            'subscription_plan_id',
            'paid_amount',
            'payment_details',
            'cart',
            'coupon_code',
            'offer_percentage',
            'coupon_discount_amount',
            'gateway_charge_in_usd',
        ]);
    }
}
