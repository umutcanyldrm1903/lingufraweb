<?php

namespace Modules\BasicPayment\app\Services;

use Modules\BasicPayment\app\Enums\PaymentGatewaySupportedCurrencyListEnum;
use Modules\BasicPayment\app\Services\PaymentMethodService;

class PaymentGatewayService extends PaymentMethodService {
    const MOLLIE = 'mollie';

    const RAZORPAY = 'razorpay';

    const FLUTTERWAVE = 'flutterwave';

    const INSTAMOJO = 'instamojo';

    const PAYSTACK = 'paystack';
    const AZAMPAY = 'azampay';
    const XENDIT = 'xendit';
    const IYZICO = 'iyzico';

    private $paymentSetting;
    private $activeStatus;

    /**
     * @var mixed
     */
    protected $previousService;

    /**
     * @param PaymentMethodService $previousService
     */
    public function __construct(?PaymentMethodService $previousService = null) {
        if (is_null($previousService)) {
            $previousService = PaymentMethodService::class;
        }

        $this->previousService = $previousService;
        $this->paymentSetting = $this->get_payment_gateway_info();
        $this->activeStatus = config('basicpayment.default_status.active_text');

        self::extendSupportedPayments([
            self::RAZORPAY,
            self::FLUTTERWAVE,
            self::MOLLIE,
            self::INSTAMOJO,
            self::PAYSTACK,
            self::AZAMPAY,
            self::XENDIT,
            self::IYZICO,
        ]);

        self::extendMultiCurrencySupported([
            self::RAZORPAY,
            self::FLUTTERWAVE,
            self::MOLLIE,
            self::INSTAMOJO,
            self::PAYSTACK,
            self::AZAMPAY,
            self::XENDIT,
            self::IYZICO,
        ]);

        self::additionalActiveGatewaysList([
            self::RAZORPAY    => [
                'name'   => 'RazorPay',
                'logo'   => asset($this->paymentSetting->razorpay_image ?? 'uploads/website-images/razorpay.png'),
                'status' => $this->paymentSetting->razorpay_status == $this->activeStatus,
            ],
            self::FLUTTERWAVE => [
                'name'   => 'FlutterWave',
                'logo'   => asset($this->paymentSetting->flutterwave_image ?? 'uploads/website-images/flutterwave.png'),
                'status' => $this->paymentSetting->flutterwave_status == $this->activeStatus,
            ],
            self::PAYSTACK    => [
                'name'   => 'PayStack',
                'logo'   => asset($this->paymentSetting->paystack_image ?? 'uploads/website-images/paystack.png'),
                'status' => $this->paymentSetting->paystack_status == $this->activeStatus,
            ],
            self::MOLLIE      => [
                'name'   => 'Mollie',
                'logo'   => asset($this->paymentSetting->mollie_image ?? 'uploads/website-images/mollie.png'),
                'status' => $this->paymentSetting->mollie_status == $this->activeStatus,
            ],
            self::INSTAMOJO   => [
                'name'   => 'Instamojo',
                'logo'   => asset($this->paymentSetting->instamojo_image ?? 'uploads/website-images/instamojo.png'),
                'status' => $this->paymentSetting->instamojo_status == $this->activeStatus,
            ],
            self::AZAMPAY     => [
                'name'   => 'Azampay',
                'logo'   => asset($this->paymentSetting->azampay_image ?? 'uploads/website-images/azampay.webp'),
                'status' => $this->paymentSetting->azampay_status == $this->activeStatus,
            ],
            self::XENDIT      => [
                'name'   => 'Xendit',
                'logo'   => asset($this->paymentSetting->xendit_image ?? 'uploads/website-images/xendit.png'),
                'status' => $this->paymentSetting->xendit_status == $this->activeStatus,
            ],
            self::IYZICO      => [
                'name'   => 'Iyzico',
                'logo'   => asset($this->paymentSetting->iyzico_image ?? 'uploads/website-images/iyzico.png'),
                'status' => ($this->paymentSetting->iyzico_status ?? null) == $this->activeStatus,
            ],
        ]);
    }

    /**
     * @param string $gatewayName
     */
    public function getPaymentName(string $gatewayName): ?string {
        return match ($gatewayName) {
            self::RAZORPAY => 'Razorpay',
            self::FLUTTERWAVE => 'Flutterwave',
            self::MOLLIE => 'Mollie',
            self::INSTAMOJO => 'Instamojo',
            self::PAYSTACK => 'Paystack',
            self::AZAMPAY => 'Azampay',
            self::XENDIT => 'Xendit',
            self::IYZICO => 'Iyzico',
            default => $this->previousService->getPaymentName($gatewayName),
        };
    }

    /**
     * @param string $gatewayName
     */
    public function getGatewayDetails(string $gatewayName): ?object {
        $paymentSetting = $this->get_payment_gateway_info();

        return match ($gatewayName) {
            self::RAZORPAY => (object) [
                'razorpay_key'         => $paymentSetting->razorpay_key ?? null,
                'razorpay_secret'      => $paymentSetting->razorpay_secret ?? null,
                'razorpay_name'        => $paymentSetting->razorpay_name ?? null,
                'razorpay_description' => $paymentSetting->razorpay_description ?? null,
                'razorpay_theme_color' => $paymentSetting->razorpay_theme_color ?? null,
                'razorpay_status'      => $paymentSetting->razorpay_status ?? null,
                'razorpay_image'       => $paymentSetting->razorpay_image ?? null,
                'currency_id'          => $paymentSetting->razorpay_currency_id ?? null,
                'charge'               => $paymentSetting->razorpay_charge ?? null,
            ],
            self::FLUTTERWAVE => (object) [
                'flutterwave_public_key' => $paymentSetting->flutterwave_public_key ?? null,
                'flutterwave_secret_key' => $paymentSetting->flutterwave_secret_key ?? null,
                'flutterwave_app_name'   => $paymentSetting->flutterwave_app_name ?? null,
                'charge'                 => $paymentSetting->flutterwave_charge ?? null,
                'currency_id'            => $paymentSetting->flutterwave_currency_id ?? null,
                'flutterwave_status'     => $paymentSetting->flutterwave_status ?? null,
                'flutterwave_image'      => $paymentSetting->flutterwave_image ?? null,
            ],
            self::PAYSTACK => (object) [
                'paystack_public_key' => $paymentSetting->paystack_public_key ?? null,
                'paystack_secret_key' => $paymentSetting->paystack_secret_key ?? null,
                'paystack_status'     => $paymentSetting->paystack_status ?? null,
                'charge'              => $paymentSetting->paystack_charge ?? null,
                'paystack_image'      => $paymentSetting->paystack_image ?? null,
                'currency_id'         => $paymentSetting->paystack_currency_id ?? null,
            ],
            self::MOLLIE => (object) [
                'mollie_key'    => $paymentSetting->mollie_key ?? null,
                'charge'        => $paymentSetting->mollie_charge ?? null,
                'mollie_image'  => $paymentSetting->mollie_image ?? null,
                'mollie_status' => $paymentSetting->mollie_status ?? null,
                'currency_id'   => $paymentSetting->mollie_currency_id ?? null,
            ],
            self::INSTAMOJO => (object) [
                'instamojo_account_mode'  => $paymentSetting->instamojo_account_mode ?? null,
                'instamojo_client_id'     => $paymentSetting->instamojo_client_id ?? null,
                'instamojo_client_secret' => $paymentSetting->instamojo_client_secret ?? null,
                'charge'                  => $paymentSetting->instamojo_charge ?? null,
                'instamojo_image'         => $paymentSetting->instamojo_image ?? null,
                'currency_id'             => $paymentSetting->instamojo_currency_id ?? null,
                'instamojo_status'        => $paymentSetting->instamojo_status ?? null,
            ],
            self::AZAMPAY => (object) [
                'azampay_app_name'      => $paymentSetting->azampay_app_name ?? null,
                'azampay_client_id'     => $paymentSetting->azampay_client_id ?? null,
                'azampay_client_secret' => $paymentSetting->azampay_client_secret ?? null,
                'azampay_token'         => $paymentSetting->azampay_token ?? null,
                'charge'                => $paymentSetting->azampay_charge ?? null,
                'azampay_image'         => $paymentSetting->azampay_image ?? null,
                'azampay_account_mode'  => $paymentSetting->azampay_account_mode ?? null,
                'azampay_status'        => $paymentSetting->azampay_status ?? null,
            ],
            self::XENDIT => (object) [
                'xendit_api_key' => $paymentSetting->xendit_api_key ?? null,
                'charge'         => $paymentSetting->xendit_charge ?? null,
                'xendit_image'   => $paymentSetting->xendit_image ?? null,
                'xendit_status'  => $paymentSetting->xendit_status ?? null,
            ],
            self::IYZICO => (object) [
                'iyzico_api_key'      => $paymentSetting->iyzico_api_key ?? null,
                'iyzico_secret_key'   => $paymentSetting->iyzico_secret_key ?? null,
                'iyzico_account_mode' => $paymentSetting->iyzico_account_mode ?? null,
                'charge'              => $paymentSetting->iyzico_charge ?? null,
                'iyzico_image'        => $paymentSetting->iyzico_image ?? null,
                'iyzico_status'       => $paymentSetting->iyzico_status ?? null,
            ],
            default => $this->previousService->getGatewayDetails($gatewayName),
        };
    }
    /**
     * @param string $gatewayName
     */
    public function isActive(string $gatewayName): bool {
        $gatewayDetails = $this->getGatewayDetails($gatewayName);

        return match ($gatewayName) {
            self::MOLLIE => $gatewayDetails->mollie_status == $this->activeStatus,
            self::RAZORPAY => $gatewayDetails->razorpay_status == $this->activeStatus,
            self::FLUTTERWAVE => $gatewayDetails->flutterwave_status == $this->activeStatus,
            self::INSTAMOJO => $gatewayDetails->instamojo_status == $this->activeStatus,
            self::PAYSTACK => $gatewayDetails->paystack_status == $this->activeStatus,
            self::AZAMPAY => $gatewayDetails->azampay_status == $this->activeStatus,
            self::XENDIT => $gatewayDetails->xendit_status == $this->activeStatus,
            self::IYZICO => $gatewayDetails->iyzico_status == $this->activeStatus,
            default => $this->previousService->isActive($gatewayName),
        };
    }

    /**
     * @param string $gatewayName
     */
    public function getIcon(string $gatewayName): string {
        return match ($gatewayName) {
            self::MOLLIE => 'fa-cc-mollie',
            self::RAZORPAY => 'fa-cc-razorpay',
            self::FLUTTERWAVE => 'fa-cc-flutterwave',
            self::INSTAMOJO => 'fa-cc-instamojo',
            self::PAYSTACK => 'fa-cc-paystack',
            self::AZAMPAY => 'fa-building-columns',
            self::XENDIT => 'fa-building-columns',
            self::IYZICO => 'fa-credit-card',
            default => $this->previousService->getIcon($gatewayName),
        };
    }

    /**
     * @param $gatewayName
     */
    public function getLogo($gatewayName): ?string {

        return match ($gatewayName) {
            self::MOLLIE => $this->paymentSetting->mollie_image ? asset($this->paymentSetting->mollie_image) : asset('uploads/website-images/mollie.png'),
            self::RAZORPAY => $this->paymentSetting->razorpay_image ? asset($this->paymentSetting->razorpay_image) : asset('uploads/website-images/razorpay.png'),
            self::FLUTTERWAVE => $this->paymentSetting->flutterwave_image ? asset($this->paymentSetting->flutterwave_image) : asset('uploads/website-images/flutterwave.png'),
            self::INSTAMOJO => $this->paymentSetting->instamojo_image ? asset($this->paymentSetting->instamojo_image) : asset('uploads/website-images/instamojo.png'),
            self::PAYSTACK => $this->paymentSetting->paystack_image ? asset($this->paymentSetting->paystack_image) : asset('uploads/website-images/paystack.png'),
            self::AZAMPAY => $this->paymentSetting->azampay_image ? asset($this->paymentSetting->azampay_image) : asset('uploads/website-images/azampay.webp'),
            self::XENDIT => $this->paymentSetting->xendit_image ? asset($this->paymentSetting->xendit_image) : asset('uploads/website-images/xendit.png'),
            self::IYZICO => ($this->paymentSetting->iyzico_image ?? null) ? asset($this->paymentSetting->iyzico_image) : asset('uploads/website-images/iyzico.png'),
            default => $this->previousService->getLogo($gatewayName),
        };
    }

    /**
     * @param $gatewayName
     * @param $code
     */
    public function isCurrencySupported($gatewayName, $code = null): bool {
        if (is_null($code)) {
            $code = getSessionCurrency();
        }

        return match ($gatewayName) {
            self::MOLLIE => PaymentGatewaySupportedCurrencyListEnum::isMollieSupportedCurrencies($code),
            self::RAZORPAY => PaymentGatewaySupportedCurrencyListEnum::isRazorpaySupportedCurrencies($code),
            self::FLUTTERWAVE => PaymentGatewaySupportedCurrencyListEnum::isFlutterwaveSupportedCurrencies($code),
            self::INSTAMOJO => PaymentGatewaySupportedCurrencyListEnum::isInstamojoSupportedCurrencies($code),
            self::PAYSTACK => PaymentGatewaySupportedCurrencyListEnum::isPaystackSupportedCurrencies($code),
            self::AZAMPAY => PaymentGatewaySupportedCurrencyListEnum::isAzampaySupportedCurrencies($code),
            self::XENDIT => PaymentGatewaySupportedCurrencyListEnum::isXenditSupportedCurrencies($code),
            self::IYZICO => PaymentGatewaySupportedCurrencyListEnum::isIyzicoSupportedCurrencies($code),
            default => $this->previousService->isCurrencySupported($gatewayName, $code),
        };
    }

    /**
     * @param $gatewayName
     */
    public function getSupportedCurrencies($gatewayName): array {
        return match ($gatewayName) {
            self::MOLLIE => PaymentGatewaySupportedCurrencyListEnum::getMollieSupportedCurrencies(),
            self::RAZORPAY => PaymentGatewaySupportedCurrencyListEnum::getRazorpaySupportedCurrencies(),
            self::FLUTTERWAVE => PaymentGatewaySupportedCurrencyListEnum::getFlutterwaveSupportedCurrencies(),
            self::INSTAMOJO => PaymentGatewaySupportedCurrencyListEnum::getInstamojoSupportedCurrencies(),
            self::PAYSTACK => PaymentGatewaySupportedCurrencyListEnum::getPaystackSupportedCurrencies(),
            self::AZAMPAY => PaymentGatewaySupportedCurrencyListEnum::getAzampaySupportedCurrencies(),
            self::XENDIT => PaymentGatewaySupportedCurrencyListEnum::getXenditSupportedCurrencies(),
            self::IYZICO => PaymentGatewaySupportedCurrencyListEnum::getIyzicoSupportedCurrencies(),
            default => $this->previousService->getSupportedCurrencies($gatewayName),
        };
    }

    /**
     * @param string $gatewayName
     */
    public function getBladeView(string $gatewayName): ?string {
        return match ($gatewayName) {
            self::MOLLIE => 'basicpayment::gateway-actions.mollie',
            self::RAZORPAY => 'basicpayment::gateway-actions.razorpay',
            self::FLUTTERWAVE => 'basicpayment::gateway-actions.flutterwave',
            self::INSTAMOJO => 'basicpayment::gateway-actions.instamojo',
            self::PAYSTACK => 'basicpayment::gateway-actions.paystack',
            self::AZAMPAY => 'basicpayment::gateway-actions.azampay',
            self::XENDIT => 'basicpayment::gateway-actions.xendit',
            self::IYZICO => 'basicpayment::gateway-actions.iyzico',
            default => $this->previousService->getBladeView($gatewayName),
        };
    }
}
