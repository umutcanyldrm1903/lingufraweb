<?php

namespace Modules\BasicPayment\app\Services;

use Exception;
use Modules\BasicPayment\app\Libraries\Mpesa\APIContext;
use Modules\BasicPayment\app\Libraries\Mpesa\APIMethodType;
use Modules\BasicPayment\app\Libraries\Mpesa\APIRequest;

class MpesaService {
    public function __construct(
        protected $mode,
        protected $market,
        protected $origin,
        protected $apiKey,
        protected $publicKey,
        protected $shortcode,
        protected $country,
        protected $port = 443,
        protected $address = 'openapi.m-pesa.com',
    ) {}

    public function getSessionKey(): string {
        $path = "/{$this->mode}/ipg/v2/{$this->market}/getSession/";
        $context = $this->createContext($this->apiKey, APIMethodType::GET, $path, false);

        $request = new APIRequest($context);
        $response = $request->execute();
        $decoded = json_decode($response->get_body());

        if (!isset($decoded->output_SessionID)) {
            throw new Exception('Could not fetch session key.');
        }

        return $decoded->output_SessionID;
    }

    public function makePayment(string $sessionKey, array $paymentData): object {
        $path = "/{$this->mode}/ipg/v2/{$this->market}/c2bPayment/singleStage/";
        $context = $this->createContext($sessionKey, APIMethodType::POST, $path);

        foreach ($paymentData as $key => $value) {
            $context->add_parameter($key, $value);
        }
        $context->add_parameter("input_Country", $this->country);
        $context->add_parameter("input_ServiceProviderCode", $this->shortcode);

        $request = new APIRequest($context);
        $response = $request->execute();
        $decoded = json_decode($response->get_body());

        if (!isset($decoded->output_ResponseCode) || $decoded->output_ResponseCode !== 'INS-0') {
            throw new Exception('Payment failed: ' . ($decoded->output_ResponseDesc ?? 'Unknown error'));
        }

        return $decoded;
    }
    private function createContext(
        string $apiKey,
        int $methodType,
        string $path,
        bool $useJson = true
    ): APIContext {
        $context = new APIContext();
        $context->set_api_key($apiKey);
        $context->set_public_key($this->publicKey);
        $context->set_ssl(true);
        $context->set_method_type($methodType);
        $context->set_address($this->address);
        $context->set_port($this->port);
        $context->set_path($path);
        $context->add_header('Origin', $this->origin);

        if ($useJson) {
            $context->add_header('Content-Type', 'application/json');
        }

        return $context;
    }

}
