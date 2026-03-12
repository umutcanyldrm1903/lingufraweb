<?php

namespace Modules\BasicPayment\app\Http\Controllers\API;

use Alphaolomi\Azampay\AzampayService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Frontend\BankInformationRequest;
use App\Jobs\DefaultMailJob;
use App\Mail\DefaultMail;
use App\Models\Course;
use App\Traits\GetGlobalInformationTrait;
use App\Traits\MailSenderTrait;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Modules\BasicPayment\app\Enums\BasicPaymentSupportedCurrencyListEnum;
use Modules\BasicPayment\app\Http\Requests\AzampayBankInformationRequest;
use Modules\BasicPayment\app\Http\Requests\AzampayMnoInformationRequest;
use Modules\BasicPayment\app\Services\MpesaService;
use Modules\GlobalSetting\app\Models\EmailTemplate;
use Modules\Order\app\Models\Enrollment;
use Modules\Order\app\Models\Order;
use Modules\Order\app\Models\OrderItem;
use Mollie\Laravel\Facades\Mollie;
use Razorpay\Api\Api;
use Xendit\Configuration;
use Xendit\Invoice\CreateInvoiceRequest;
use Xendit\Invoice\InvoiceApi;

class PaymentController extends Controller {
    use GetGlobalInformationTrait, MailSenderTrait;
    private $paymentService;
    public function __construct() {
        $this->paymentService = app(\Modules\BasicPayment\app\Services\PaymentMethodService::class);
    }
    public function all_payment(): JsonResponse {
        $data = $this->paymentService->getActiveGatewaysWithDetails();
        if ($data) {
            return response()->json(['status' => 'success', 'data' => $data], 200);
        }
        return response()->json(['status' => 'error', 'message' => 'Not Found!'], 404);
    }
    public function pay_via_bank(BankInformationRequest $request) {
        $bankDetails = json_encode($request->only(['bank_name', 'account_number', 'routing_number', 'branch', 'transaction']));

        $allPayments = Order::whereNotNull('payment_details')->get();

        foreach ($allPayments as $payment) {
            $paymentDetailsJson = json_decode($payment?->payment_details, true);

            if (isset($paymentDetailsJson['account_number']) && $paymentDetailsJson['account_number'] == $request->account_number) {
                if (isset($paymentDetailsJson['transaction']) && $paymentDetailsJson['transaction'] == $request->transaction) {
                    $after_failed_url = route('payment-api.webview-failed-payment');
                    return redirect($after_failed_url);
                }
            }
        }
        Session::put('after_success_transaction', $request->transaction);
        Session::put('payment_details', $bankDetails);

        $after_success_url = route('payment-api.webview-success-payment', ['bearer_token' => request()->bearer_token]);
        return redirect($after_success_url);
    }
    public function pay_with_azampay_mno(AzampayMnoInformationRequest $request) {
        $after_success_url = route('payment-api.webview-success-payment', ['bearer_token' => request()->bearer_token]);
        $after_failed_url = route('payment-api.webview-failed-payment');

        try {
            // Load payment gateway credentials
            $paymentSetting = $this->get_payment_gateway_info();
            // Set config dynamically
            config([
                'azampay.appName'      => $paymentSetting->azampay_app_name,
                'azampay.clientId'     => $paymentSetting->azampay_client_id,
                'azampay.clientSecret' => $paymentSetting->azampay_client_secret,
                'azampay.environment'  => $paymentSetting->azampay_account_mode,
                'azampay.token'        => $paymentSetting->azampay_token,
            ]);

            // Get session data
            $payable_currency = session()->get('payable_currency');
            $paid_amount = session()->get('paid_amount');

            // Azampay service
            $azampay = new AzampayService();

            // Prepare payload
            $payload = [
                'amount'        => $paid_amount,
                'currency'      => $payable_currency,
                'accountNumber' => $request->account_number,
                'externalId'    => $request->external_id,
                'provider'      => $request->provider,
            ];

            $response = $azampay->mobileCheckout($payload);

            if (isset($response['success']) && $response['success']) {
                session([
                    'after_success_url'         => $after_success_url,
                    'after_failed_url'          => $after_failed_url,
                    'after_success_transaction' => $response['transactionId'] ?? null,
                ]);
                $response['azampay_checkout_type'] = 'mno_checkout';
                Session::put('payment_details', $response);

                return redirect($after_success_url);
            } else {
                return redirect($after_failed_url);
            }
        } catch (\Throwable $e) {
            info($e->getMessage());
            info($e);
            return redirect($after_failed_url);
        }
    }
    public function pay_with_azampay_by_bank(AzampayBankInformationRequest $request) {
        $after_success_url = route('payment-api.webview-success-payment', ['bearer_token' => request()->bearer_token]);
        $after_failed_url = route('payment-api.webview-failed-payment');
        try {
            // Load payment gateway credentials
            $paymentSetting = $this->get_payment_gateway_info();
            // Set config dynamically
            config([
                'azampay.appName'      => $paymentSetting->azampay_app_name,
                'azampay.clientId'     => $paymentSetting->azampay_client_id,
                'azampay.clientSecret' => $paymentSetting->azampay_client_secret,
                'azampay.environment'  => $paymentSetting->azampay_account_mode,
                'azampay.token'        => $paymentSetting->azampay_token,
            ]);

            // Get session data
            $payable_currency = session()->get('payable_currency');
            $paid_amount = session()->get('paid_amount');

            // Azampay service
            $azampay = new AzampayService();

            // Prepare payload
            $payload = [
                'amount'                => $paid_amount,
                'currencyCode'          => $payable_currency,
                'merchantAccountNumber' => $request->merchant_account_number,
                'merchantMobileNumber'  => $request->merchant_mobile_number,
                'merchantName'          => $request->merchant_name,
                'otp'                   => $request->otp,
                'provider'              => $request->provider,
                'referenceId'           => $request->reference_id,
            ];

            $response = $azampay->bankCheckout($payload);

            if (isset($response['success']) && $response['success']) {
                session([
                    'after_success_url'         => $after_success_url,
                    'after_failed_url'          => $after_failed_url,
                    'after_success_transaction' => $response['data']['properties']['ReferenceID'] ?? null,
                ]);
                $response['azampay_checkout_type'] = 'bank_checkout';
                Session::put('payment_details', $response);

                return redirect($after_success_url);
            } else {
                return redirect($after_failed_url);
            }
        } catch (\Throwable $e) {
            info($e->getMessage());
            info($e);
            return redirect($after_failed_url);
        }
    }
    public function pay_via_xendit() {
        try {
            $paymentSetting = $this->get_payment_gateway_info();
            Configuration::setXenditKey($paymentSetting->xendit_api_key);

            $apiInstance = new InvoiceApi();

            // Get session data
            $payable_currency = session()->get('payable_currency');
            $paid_amount = session()->get('paid_amount');

            $order = session()->get('order');
            $user = auth('sanctum')->user();

            $order_items = OrderItem::select('id', 'price', 'course_id', 'commission_rate')->with('course')->where('order_id', $order->id)->get();
            $items = [];
            foreach ($order_items as $key => $order_item) {
                $items[] = [
                    'name'     => $order_item?->course?->title ?? "item_" . (++$key),
                    'price'    => currencyWithoutIcon($order_item?->price, $payable_currency),
                    'quantity' => $order_item->qty ?? 1,
                ];
            }

            $createInvoiceRequest = new CreateInvoiceRequest([
                'amount'               => $paid_amount,
                'currency'             => $payable_currency,
                'external_id'          => $order->invoice_id,
                'description'          => cache()->get('setting')->app_name . ' payment',
                'payer_email'          => $user->email,
                'items'                => $items,
                'success_redirect_url' => route('payment-api.xendit.payment.verify', ['bearer_token' => request()->bearer_token]),
            ]);

            $forUserId = null; // string | Business ID of the sub-account merchant (XP feature)

            $result = $apiInstance->createInvoice($createInvoiceRequest, $forUserId);

            session()->put('xendit_payment_id', $result['id']);

            return redirect($result['invoice_url'] . '?bearer_token=' . request()->bearer_token);
        } catch (\Throwable $e) {
            info($e->getMessage());
            session()->forget('xendit_payment_id');
            return redirect(route('payment-api.webview-failed-payment'));
        }
    }

    public function xendit_payment_verify() {
        try {
            $paymentSetting = $this->get_payment_gateway_info();
            Configuration::setXenditKey($paymentSetting->xendit_api_key);
            $apiInstance = new InvoiceApi();

            $invoiceId = session()->get('xendit_payment_id', null);
            session()->forget('xendit_payment_id');

            $result = $apiInstance->getInvoiceById($invoiceId, null);

            if ($result) {
                if (in_array($result['status'], ["SETTLED", "PAID"])) {
                    $transaction = $result['id'];
                    Session::put('after_success_transaction', $transaction);
                    $paymentDetails = [
                        'transaction_id'               => $transaction,
                        'status'                       => $result['status'],
                        'payment_method'               => $result['payment_method'],
                        'currency'                     => $result['currency'],
                        'amount'                       => $result['amount'],
                        'invoice_url'                  => $result['invoice_url'],
                        'payer_email'                  => $result['payer_email'],
                        'description'                  => $result['description'],
                        'merchant_name'                => $result['merchant_name'],
                        'merchant_profile_picture_url' => $result['merchant_profile_picture_url'],
                    ];
                    Session::put('payment_details', $paymentDetails);
                }
                return redirect(route('payment-api.webview-success-payment', ['bearer_token' => request()->bearer_token]));
            }
        } catch (\Throwable $e) {
            info($e->getMessage());
            session()->forget('xendit_payment_id');
            return redirect(route('payment-api.webview-failed-payment'));
        }
    }
    public function pay_via_mpesa(Request $request) {
        $request->validate([
            'msisdn' => ['required', 'string', 'regex:/^[0-9]{12,14}$/'],
        ], [
            'msisdn.required' => __('The phone number is required.'),
            'msisdn.string'   => __('The phone number must be a valid string.'),
            'msisdn.regex'    => __('The phone number must be 12 to 14 digits long and contain only numbers.'),
        ]);

        try {
            $payment_setting = $this->get_basic_payment_info();
            $mpesaService = new MpesaService(
                mode: $payment_setting->mpesa_account_mode,
                market: $payment_setting->mpesa_market,
                origin: $payment_setting->mpesa_origin,
                apiKey: $payment_setting->mpesa_api_key,
                publicKey: $payment_setting->mpesa_public_key,
                shortcode: $payment_setting->mpesa_shortcode,
                country: $payment_setting->mpesa_country,
            );

            // Step 1: Get M-Pesa session key
            $sessionKey = $mpesaService->getSessionKey();

            $payable_currency = session()->get('payable_currency');
            $paid_amount = session()->get('paid_amount');

            // Step 2: Prepare payment data
            $paymentData = [
                "input_Amount"                   => $paid_amount,
                "input_CustomerMSISDN"           => $request->msisdn,
                "input_Currency"                 => $payable_currency,
                "input_ThirdPartyConversationID" => Str::random(32),
                "input_TransactionReference"     => 'TXN' . now()->timestamp,
                "input_PurchasedItemsDesc"       => 'Course enrollment payment',
            ];

            // Step 3: Call M-Pesa API
            $result = $mpesaService->makePayment($sessionKey, $paymentData);

            // Step 4: Handle response
            if (($result->output_ResponseCode ?? '') === 'INS-0') {
                Session::put('after_success_transaction', $result->output_ThirdPartyConversationID);
                Session::put('payment_details', json_encode($result));

                return redirect()->route('payment-api.webview-success-payment', ['bearer_token' => request()->bearer_token]);
            }

            return redirect()->route('payment-api.webview-failed-payment');

        } catch (Exception $e) {
            info($e->getMessage());
            return redirect()->route('payment-api.webview-failed-payment');
        }
    }
    public function pay_via_offline(Request $request) {
        $request->validate([
            'payment_receipt' => 'required|mimes:jpeg,jpg,png,gif,webp,svg,pdf,docx|max:2048',
        ], [
            'payment_receipt.required' => __('Offline Payment Receipt is required'),
            'payment_receipt.mimes'    => __('The Offline Payment Receipt must be a file of type: jpeg, jpg, png, gif, webp, svg, pdf, docx.'),
            'payment_receipt.max'      => __('The Offline Payment Receipt may not be greater than 2048 kilobytes.'),
        ]);
        $user = auth()->user();
        if (!$user) {
            abort(401);
        }
        $order_id = $request?->order_id ?? null;
        $order = $user?->orders()->where('invoice_id', $order_id)->where('status', 'pending')->first();
        if (!$order) {
            abort(404);
        }
        if ($request->hasFile('payment_receipt')) {
            $file_name = file_upload($request->payment_receipt);
            Session::put('after_success_transaction', uniqid('offline_txn_', true));
            Session::put('payment_details', $file_name);
        }
        $after_success_url = route('payment-api.webview-success-payment', ['bearer_token' => request()->bearer_token]);
        return redirect($after_success_url);
    }
    public function placeOrder($paymentMethod) {
        $user = auth()->user();
        if (!$user) {
            return response()->json(['status' => 'error', 'message' => 'UnAuthenticated'], 401);
        }

        $activeGateways = array_keys($this->paymentService->getActiveGatewaysWithDetails());
        if (!in_array($paymentMethod, $activeGateways)) {
            return response()->json(['status' => 'error', 'message' => 'The selected payment method is now inactive.'], 400);
        }

        $payable_currency = strtoupper(request()->query('currency', 'USD'));

        if (!$this->paymentService->isCurrencySupported($paymentMethod, $payable_currency)) {
            $supportedCurrencies = $this->paymentService->getSupportedCurrencies($paymentMethod);
            return response()->json(['status' => 'error', 'message' => 'You are trying to use unsupported currency', 'supportCurrency' => sprintf(
                '%s %s: %s',
                strtoupper($paymentMethod),
                'supports only these types of currencies',
                implode(', ', $supportedCurrencies)
            )], 400);
        }

        if ($user->cart_count == 0) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Please add some courses in your cart.',
            ], 404);

        }

        try {
            $payable_amount = $user->cart_total;
            $calculatePayableCharge = $this->paymentService->getPayableAmount($paymentMethod, $payable_amount, $payable_currency);

            DB::beginTransaction();

            $paid_amount = $calculatePayableCharge?->payable_amount + $calculatePayableCharge?->gateway_charge;

            if (in_array($paymentMethod, ['Razorpay', 'Stripe'])) {
                $allCurrencyCodes = BasicPaymentSupportedCurrencyListEnum::getStripeSupportedCurrencies();

                if (in_array(Str::upper($calculatePayableCharge?->currency_code), $allCurrencyCodes['non_zero_currency_codes'])) {
                    $paid_amount = $paid_amount;
                } elseif (in_array(Str::upper($calculatePayableCharge?->currency_code), $allCurrencyCodes['three_digit_currency_codes'])) {
                    $paid_amount = (int) rtrim(strval($paid_amount), '0');
                } else {
                    $paid_amount = floatval($paid_amount / 100);
                }
            }

            $order = Order::create([
                'invoice_id'              => Str::random(10),
                'buyer_id'                => $user->id,
                'has_coupon'              => Session::has('coupon_code') ? 1 : 0,
                'coupon_code'             => Session::get('coupon_code'),
                'coupon_discount_percent' => Session::get('offer_percentage'),
                'coupon_discount_amount'  => Session::get('coupon_discount_amount'),
                'payment_method'          => $paymentMethod,
                'payment_status'          => 'pending',
                'payable_amount'          => $payable_amount,
                'gateway_charge'          => $calculatePayableCharge?->gateway_charge,
                'payable_with_charge'     => $calculatePayableCharge?->payable_with_charge,
                'paid_amount'             => $paid_amount,
                'payable_currency'        => $calculatePayableCharge?->currency_code,
                'conversion_rate'         => $calculatePayableCharge?->currency_rate,
                'commission_rate'         => Cache::get('setting')->commission_rate,
            ]);
            $data_layer_order_items = [];

            $carts = $user->carts()->with('course:id,title,slug,price,discount')->get(['id', 'user_id', 'course_id']);

            foreach ($carts as $item) {
                $order_item = [
                    'order_id'        => $order->id,
                    'price'           => $item->course->price,
                    'course_id'       => $item->course->id,
                    'commission_rate' => Cache::get('setting')->commission_rate,
                ];
                OrderItem::create([
                    'order_id'        => $order->id,
                    'price'           => $item->course->price,
                    'course_id'       => $item->course->id,
                    'commission_rate' => Cache::get('setting')->commission_rate,
                ]);
                $data_layer_order_items[] = [
                    'course_name' => $item->course->title,
                    'price'       => currency($item->course->price),
                    'url'         => route('course.show', $item->course->slug),
                ];

                // insert instructor commission to his wallet
                $commissionAmount = $item->course->price * ($order->commission_rate / 100);
                $amountAfterCommission = $item->course->price - $commissionAmount;
                $instructor = Course::find($item->course->id)->instructor;
                $instructor->increment('wallet_balance', $amountAfterCommission);

            }
            DB::commit();
            $user->carts()->delete();

            $settings = cache()->get('setting');
            $marketingSettings = cache()->get('marketing_setting');
            if ($user && $settings->google_tagmanager_status == 'active' && $marketingSettings->order_success) {
                $order_success = [
                    'invoice_id'       => $order->invoice_id,
                    'transaction_id'   => $order->transaction_id,
                    'payment_method'   => $order->payment_method,
                    'payable_currency' => $order->payable_currency,
                    'paid_amount'      => $order->paid_amount,
                    'payment_status'   => $order->payment_status,
                    'order_items'      => $data_layer_order_items,
                    'student_info'     => [
                        'name'  => $user->name,
                        'email' => $user->email,
                    ],
                ];
                session()->put('enrollSuccess', $order_success);
            }
            // send mail
            $this->handleMailSending([
                'email'          => $user->email,
                'name'           => $user->name,
                'order_id'       => $order->invoice_id,
                'paid_amount'    => $order->paid_amount . ' ' . $order->payable_currency,
                'payment_method' => $order->payment_method,
            ]);

            $order_id = $order?->invoice_id;
            $newToken = $user->createToken('extra-token', ['extra'], now()->addWeek())->plainTextToken;

            return response()->json(['status' => 'success', 'url' => route('payment-api.payment', ['token' => $newToken, 'order_id' => $order_id])], 200);
        } catch (Exception $e) {
            DB::rollBack();
            $data_layer_order_items = [];
            foreach ($carts as $item) {
                $data_layer_order_items[] = [
                    'course_name' => $item->course->name,
                    'price'       => currency($item->course->price),
                    'url'         => route('course.show', $item->course->slug),
                ];
            }

            $settings = cache()->get('setting');
            $marketingSettings = cache()->get('marketing_setting');
            if ($settings->google_tagmanager_status == 'active' && $marketingSettings->order_failed) {
                $user = userAuth();
                $order_failed = [
                    'payable_currency' => session('payable_currency', getSessionCurrency()),
                    'paid_amount'      => session('paid_amount', null),
                    'payment_status'   => 'Failed',
                    'order_items'      => $data_layer_order_items,
                    'student_info'     => [
                        'name'  => $user->name,
                        'email' => $user->email,
                    ],
                ];
                session()->put('enrollFailed', $order_failed);
            }
            info($e->getMessage());
            return to_route('payment-api.webview-failed-payment');
        }
    }
    public function pay_via_free_gateway() {
        $user = auth()->user();
        if (!$user) {
            return response()->json(['status' => 'error', 'message' => 'UnAuthenticated'], 401);
        }
        $payable_amount = $user->cart_total;
        if ($payable_amount != 0) {
            return response()->json(['status' => 'error', 'message' => 'Payment failed, please try again'], 400);
        }
        try {
            DB::beginTransaction();
            $order = Order::create([
                'invoice_id'          => Str::random(10),
                'buyer_id'            => $user->id,
                'payment_method'      => 'Free',
                'status'              => 'completed',
                'payment_status'      => 'paid',
                'payable_amount'      => $payable_amount,
                'gateway_charge'      => 0,
                'payable_with_charge' => $payable_amount,
                'paid_amount'         => $payable_amount,
                'payable_currency'    => getSessionCurrency(),
                'transaction_id'      => Str::random(10),
            ]);
            $carts = $user->carts()->with('course:id,title,slug,price,discount')->get(['id', 'user_id', 'course_id']);

            foreach ($carts as $item) {
                OrderItem::create([
                    'order_id'  => $order->id,
                    'price'     => $item->course->price,
                    'course_id' => $item->course->id,
                ]);
                Enrollment::create([
                    'order_id'   => $order->id,
                    'user_id'    => $user->id,
                    'course_id'  => $item->course->id,
                    'has_access' => 1,
                ]);
            }

            DB::commit();
            $user->carts()->delete();

            return response()->json(['status' => 'success', 'message' => 'Your order has been placed'], 200);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => 'Your order has been fail'], 400);
        }
    }
    public function payment(Request $request) {
        $token = $request?->token ?? null;
        $request->headers->set('Authorization', 'Bearer ' . $token);
        $user = auth('sanctum')->user();
        if (!$user) {
            abort(401);
        }
        $order_id = $request?->order_id ?? null;
        $order = $user?->orders()->where('invoice_id', $order_id)->where('status', 'pending')->first();
        if (!$order) {
            abort(404);
        }
        $paymentMethod = $order->payment_method;
        if (!$this->paymentService->isActive($paymentMethod)) {
            return response()->json(['status' => 'error', 'message' => 'The selected payment method is now inactive.'], 400);
        }

        $calculatePayableCharge = $this->paymentService->getPayableAmount($paymentMethod, $order?->payable_amount, $order?->payable_currency);

        Session::put('order', $order);
        Session::put('payable_currency', $order?->payable_currency);
        Session::put('paid_amount', $calculatePayableCharge?->payable_with_charge);

        $paymentService = $this->paymentService;
        $view = $this->paymentService->getBladeView($paymentMethod);
        $viewData = compact('order', 'paymentService', 'paymentMethod', 'user', 'token', 'order_id');

        if ($paymentMethod === 'iyzico') {
            $viewData = array_merge(
                $viewData,
                $this->buildIyzicoCheckoutForm($order, $calculatePayableCharge, $user)
            );
        }

        return view($view, $viewData);
    }

    private function buildIyzicoCheckoutForm(Order $order, object $payableCharge, $user): array {
        $paymentSetting = $this->get_payment_gateway_info();
        $apiKey = $paymentSetting->iyzico_api_key ?? null;
        $secretKey = $paymentSetting->iyzico_secret_key ?? null;
        $accountMode = $paymentSetting->iyzico_account_mode ?? 'sandbox';

        if (!$apiKey || !$secretKey) {
            return ['iyzico_error' => __('Iyzico credentials not found')];
        }

        $options = $this->getIyzicoOptions($apiKey, $secretKey, $accountMode);

        $request = new \Iyzipay\Request\CreateCheckoutFormInitializeRequest();
        $request->setLocale(\Iyzipay\Model\Locale::TR);
        $request->setConversationId($order->invoice_id);
        $request->setPrice(number_format((float) $order->payable_amount, 2, '.', ''));
        $request->setPaidPrice(number_format((float) $payableCharge->payable_with_charge, 2, '.', ''));
        $request->setCurrency(\Iyzipay\Model\Currency::TL);
        $request->setBasketId($order->invoice_id);
        $request->setPaymentGroup(\Iyzipay\Model\PaymentGroup::PRODUCT);
        // Mark this callback as "app" so we can deep-link back to the mobile app.
        $request->setCallbackUrl(route('iyzico-callback', ['app' => 1]));
        $request->setEnabledInstallments([1, 2, 3, 6, 9, 12]);

        $buyer = new \Iyzipay\Model\Buyer();
        $name = trim((string) ($user?->name ?? ''));
        $nameParts = preg_split('/\s+/', $name, 2);
        $firstName = $nameParts[0] ?? 'Customer';
        $lastName = $nameParts[1] ?? $firstName;
        $buyer->setId((string) ($user?->id ?? $order->buyer_id));
        $buyer->setName($firstName);
        $buyer->setSurname($lastName);
        $buyer->setGsmNumber($user?->phone ?: '+905350000000');
        $buyer->setEmail((string) ($user?->email ?? ''));
        $buyer->setIdentityNumber('11111111111');
        $buyer->setRegistrationAddress($user?->address ?: 'N/A');
        $buyer->setIp(request()->ip());
        $buyer->setCity($user?->city ?: 'Istanbul');
        $buyer->setCountry('Turkey');
        $buyer->setZipCode('34000');
        $request->setBuyer($buyer);

        $address = new \Iyzipay\Model\Address();
        $address->setContactName($name !== '' ? $name : 'Customer');
        $address->setCity($user?->city ?: 'Istanbul');
        $address->setCountry('Turkey');
        $address->setAddress($user?->address ?: 'N/A');
        $address->setZipCode('34000');
        $request->setShippingAddress($address);
        $request->setBillingAddress($address);

        $basketItems = [];
        if (($order->order_type ?? null) === 'student_plan') {
            $title = $order->orderDetails?->title ?? 'Plan';
            $basketItem = new \Iyzipay\Model\BasketItem();
            $basketItem->setId((string) $order->id);
            $basketItem->setName((string) $title);
            $basketItem->setCategory1('Plan');
            $basketItem->setItemType(\Iyzipay\Model\BasketItemType::VIRTUAL);
            $basketItem->setPrice(number_format((float) $order->payable_amount, 2, '.', ''));
            $basketItems[] = $basketItem;
        } else {
            $orderItems = $order->orderItems()->with('course:id,title')->get();
            foreach ($orderItems as $item) {
                $basketItem = new \Iyzipay\Model\BasketItem();
                $basketItem->setId((string) $item->course_id);
                $basketItem->setName($item->course?->title ?? 'Course');
                $basketItem->setCategory1('Course');
                $basketItem->setItemType(\Iyzipay\Model\BasketItemType::VIRTUAL);
                $basketItem->setPrice(number_format((float) $item->price, 2, '.', ''));
                $basketItems[] = $basketItem;
            }
        }
        $request->setBasketItems($basketItems);

        $checkoutFormInitialize = \Iyzipay\Model\CheckoutFormInitialize::create($request, $options);
        if ($checkoutFormInitialize->getStatus() !== 'success') {
            return [
                'iyzico_error' => $checkoutFormInitialize->getErrorMessage() ?: __('Iyzico initialization failed'),
            ];
        }

        Session::put('iyzico_token', $checkoutFormInitialize->getToken());

        return [
            'iyzico_checkout_form' => $checkoutFormInitialize->getCheckoutFormContent(),
        ];
    }

    private function getIyzicoOptions(string $apiKey, string $secretKey, string $accountMode): \Iyzipay\Options {
        $options = new \Iyzipay\Options();
        $options->setApiKey($apiKey);
        $options->setSecretKey($secretKey);
        $options->setBaseUrl($accountMode === 'live' ? 'https://api.iyzipay.com' : 'https://sandbox-api.iyzipay.com');

        return $options;
    }
    public function payment_success() {
        $order = session()->get('order');
        if (!$order) {
            abort(404);
        }
        $invoiceId = (string) $order->invoice_id;

        // Idempotency: Some gateways (and browsers) can hit the success URL multiple times.
        // If the order is already completed, avoid re-processing enrollments / plan writes.
        if (($order->payment_status ?? null) === 'paid' && ($order->status ?? null) === 'completed') {
            $image = 'success.png';
            $title = __('Payment Success.');
            $sub_title = __('For check more details you can go to your dashboard');
            $result = 'success';
            return view('basicpayment::app_order_notification', compact('image', 'title', 'sub_title', 'invoiceId', 'result'));
        }
        $after_success_transaction = session()->get('after_success_transaction', null);
        $payment_details = session()->get('payment_details', null);

        $method_bank_or_offline = in_array($order->payment_method, [$this->paymentService::BANK_PAYMENT, $this->paymentService::OFFLINE_PAYMENT, $this->paymentService::MPESA]);

        try {
            $order->transaction_id = $after_success_transaction;
            $order->payment_status = $method_bank_or_offline ? 'pending' : 'paid';
            $order->status = 'completed';
            $order->payment_details = $method_bank_or_offline ? $payment_details : json_encode($payment_details);
            $order->save();
            if (!$method_bank_or_offline) {
                if (($order->order_type ?? null) === 'student_plan') {
                    $details = $order->orderDetails;
                    $durationMonths = (int) ($details?->duration_months ?? 0);
                    $lessonsTotal = (int) ($details?->lessons_total ?? 0);
                    $cancelTotal = (int) ($details?->cancel_total ?? 0);

                    if (\Illuminate\Support\Facades\Schema::hasTable('user_plans')) {
                        $planKey = trim((string) ($details?->plan_key ?? $details?->key ?? ''));
                        if ($planKey === '') {
                            $planKey = 'order_'.$order->id;
                        }

                        $planTitle = trim((string) ($details?->title ?? $details?->plan_title ?? ''));
                        if ($planTitle === '') {
                            $planTitle = 'Plan';
                        }

                        $existingPlan = DB::table('user_plans')->where('user_id', $order->buyer_id)->first();
                        $newLessons = max(0, $lessonsTotal);
                        $newCancels = max(0, $cancelTotal);
                        $currentLessonsTotal = (int) ($existingPlan?->lessons_total ?? 0);
                        $currentLessonsRemaining = (int) ($existingPlan?->lessons_remaining ?? 0);
                        $currentCancelTotal = (int) ($existingPlan?->cancel_total ?? 0);
                        $currentCancelRemaining = (int) ($existingPlan?->cancel_remaining ?? 0);

                        $payload = [
                            'plan_key' => $planKey,
                            'plan_title' => $planTitle,
                            // Keep existing credits (e.g. referral/free lessons) and add new package credits.
                            'lessons_total' => $currentLessonsTotal + $newLessons,
                            'lessons_remaining' => $currentLessonsRemaining + $newLessons,
                            'cancel_total' => $currentCancelTotal + $newCancels,
                            'cancel_remaining' => $currentCancelRemaining + $newCancels,
                            'starts_at' => now(),
                            'ends_at' => $durationMonths > 0 ? now()->addMonths($durationMonths) : null,
                            'last_order_id' => $order->id,
                            'updated_at' => now(),
                        ];

                        if ($existingPlan) {
                            DB::table('user_plans')->where('user_id', $order->buyer_id)->update($payload);
                        } else {
                            DB::table('user_plans')->insert(array_merge($payload, [
                                'user_id' => $order->buyer_id,
                                'created_at' => now(),
                            ]));
                        }
                    }

                    // Referral reward (Cowboy-like "Ücretsiz Ders Al" flow).
                    try {
                        app(\App\Services\Referral\ReferralService::class)->rewardReferrerForPaidPlanOrder($order);
                    } catch (\Throwable $e) {
                        report($e);
                    }
                } else {
                    foreach ($order->orderItems as $item) {
                        Enrollment::firstOrCreate([
                            'user_id'   => $order->buyer_id,
                            'course_id' => $item->course_id,
                        ], [
                            'order_id'   => $order->id,
                            'has_access' => 1,
                        ]);
                    }
                }
            }

            try {
                // Iyzipay callbacks are not authenticated (no session user). Fall back to the
                // order buyer so we don't fatal on null.
                $user = auth()->user() ?: auth('sanctum')->user();
                if (!$user) {
                    $user = \App\Models\User::find($order->buyer_id);
                }

                if ($user && !empty($user->email)) {
                    $this->sendingPaymentStatusMail([
                        'email'          => $user->email,
                        'name'           => $user->name ?? '',
                        'order_id'       => $order->invoice_id,
                        'paid_amount'    => $order->paid_amount . ' ' . $order->payable_currency,
                        'payment_status' => $order->payment_status,
                    ]);
                }
            } catch (\Throwable $e) {
                info($e->getMessage());
            }

            $this->paymentService->removeSessions();

            $image = 'success.png';
            $title = 'Your order has been placed';
            $sub_title = __('For check more details you can go to your dashboard');
            $result = 'success';
            return view('basicpayment::app_order_notification', compact('image', 'title', 'sub_title', 'invoiceId', 'result'));
        } catch (\Throwable $e) {
            info($e->getMessage());
            $image = 'fail.png';
            $title = 'Your order has been fail';
            $sub_title = __('Please try again for more details connect with us');
            $result = 'failed';
            return view('basicpayment::app_order_notification', compact('image', 'title', 'sub_title', 'invoiceId', 'result'));
        }
    }
    public function payment_failed() {
        $order = session()->get('order');
        $invoiceId = $order ? (string) $order->invoice_id : '';
        if ($order) {
            $order->payment_status = 'cancelled';
            $order->save();
        }

        try {
            if ($order) {
                $user = auth()->user() ?: auth('sanctum')->user();
                if (!$user) {
                    $user = \App\Models\User::find($order->buyer_id);
                }

                if ($user && !empty($user->email)) {
                    $this->sendingPaymentStatusMail([
                        'email'          => $user->email,
                        'name'           => $user->name ?? '',
                        'order_id'       => $order->invoice_id,
                        'paid_amount'    => $order->paid_amount . ' ' . $order->payable_currency,
                        'payment_status' => $order->payment_status,
                    ]);
                }
            }
        } catch (\Throwable $e) {
            info($e->getMessage());
        }

        $this->paymentService->removeSessions();
        $image = 'fail.png';
        $title = 'Your order has been fail';
        $sub_title = __('Please try again for more details connect with us');
        $result = 'failed';
        return view('basicpayment::app_order_notification', compact('image', 'title', 'sub_title', 'invoiceId', 'result'));
    }
    public function stripe_pay() {
        $basic_payment = $this->get_basic_payment_info();
        \Stripe\Stripe::setApiKey($basic_payment?->stripe_secret);

        $after_failed_url = route('payment-api.webview-failed-payment');
        session()->put('after_failed_url', $after_failed_url);

        $payable_currency = session()->get('payable_currency');
        $paid_amount = session()->get('paid_amount');

        $allCurrencyCodes = $this->paymentService->getSupportedCurrencies($this->paymentService::STRIPE);

        if (in_array(Str::upper($payable_currency), $allCurrencyCodes['non_zero_currency_codes'])) {
            $payable_with_charge = $paid_amount;
        } elseif (in_array(Str::upper($payable_currency), $allCurrencyCodes['three_digit_currency_codes'])) {
            $convertedCharge = (string) $paid_amount . '0';
            $payable_with_charge = (int) $convertedCharge;
        } else {
            $payable_with_charge = (int) ($paid_amount * 100);
        }

        // Create a checkout session
        $checkoutSession = \Stripe\Checkout\Session::create([
            'payment_method_types' => ['card'],
            'line_items'           => [[
                'price_data' => [
                    'currency'     => $payable_currency,
                    'unit_amount'  => $payable_with_charge,
                    'product_data' => [
                        'name' => cache()->get('setting')->app_name,
                    ],
                ],
                'quantity'   => 1,
            ]],
            'mode'                 => 'payment',
            'success_url'          => url('/webview-success-payment') . '?session_id={CHECKOUT_SESSION_ID}&bearer_token=' . request()->bearer_token,
            'cancel_url'           => $after_failed_url,
        ]);
        // Redirect to the checkout session URL
        return redirect()->away($checkoutSession->url);
    }
    public function stripe_success(Request $request) {
        $after_success_url = route('payment-api.webview-success-payment', ['bearer_token' => request()->bearer_token]);

        $basic_payment = $this->get_basic_payment_info();

        // Assuming the Checkout Session ID is passed as a query parameter
        $session_id = $request->query('session_id');
        if ($session_id) {
            \Stripe\Stripe::setApiKey($basic_payment->stripe_secret);

            $session = \Stripe\Checkout\Session::retrieve($session_id);

            $paymentDetails = [
                'transaction_id' => $session->payment_intent,
                'amount'         => $session->amount_total,
                'currency'       => $session->currency,
                'payment_status' => $session->payment_status,
                'created'        => $session->created,
            ];
            session()->put('after_success_url', $after_success_url);
            session()->put('after_success_transaction', $session->payment_intent);
            session()->put('payment_details', $paymentDetails);

            return redirect($after_success_url);
        }

        $after_failed_url = session()->get('after_failed_url');
        return redirect($after_failed_url);
    }

    public function pay_via_mollie() {
        $payment_setting = $this->get_payment_gateway_info();

        $mollie_credentials = (object) [
            'mollie_key' => $payment_setting->mollie_key,
        ];

        $after_success_url = route('payment-api.webview-success-payment', ['bearer_token' => request()->bearer_token]);
        $after_failed_url = route('payment-api.webview-failed-payment');

        session()->put('after_success_url', $after_success_url);
        session()->put('after_failed_url', $after_failed_url);

        $payable_currency = session()->get('payable_currency');
        $paid_amount = session()->get('paid_amount');

        try {
            Mollie::api()->setApiKey($mollie_credentials->mollie_key);
            $payment = Mollie::api()->payments()->create([
                'amount'      => [
                    'currency' => '' . strtoupper($payable_currency) . '',
                    'value'    => '' . $paid_amount . '',
                ],
                'description' => cache()->get('setting')->app_name,
                'redirectUrl' => route('payment-api.mollie-success', ['bearer_token' => request()->bearer_token]),
            ]);

            $payment = Mollie::api()->payments()->get($payment->id);

            session()->put('payment_id', $payment->id);
            session()->put('mollie_credentials', $mollie_credentials);

            return redirect($payment->getCheckoutUrl(), 303);

        } catch (Exception $ex) {
            info($ex->getMessage());
            $image = 'fail.png';
            $title = 'Your order has been fail';
            $sub_title = __('Please try again for more details connect with us');
            return view('basicpayment::app_order_notification', compact('image', 'title', 'sub_title'));
        }

    }
    public function mollie_success() {
        $mollie_credentials = Session::get('mollie_credentials');

        $mollie = new \Mollie\Api\MollieApiClient();
        $mollie->setApiKey($mollie_credentials->mollie_key);
        $payment = $mollie->payments->get(session()->get('payment_id'));

        if ($payment->isPaid()) {
            $paymentDetails = [
                'transaction_id' => $payment->id,
                'amount'         => $payment->amount->value,
                'currency'       => $payment->amount->currency,
                'fee'            => $payment->settlementAmount->value . ' ' . $payment->settlementAmount->currency,
                'description'    => $payment->description,
                'payment_method' => $payment->method,
                'status'         => $payment->status,
                'paid_at'        => $payment->paidAt,
            ];

            Session::put('payment_details', $paymentDetails);
            Session::put('after_success_transaction', session()->get('payment_id'));

            $after_success_url = Session::get('after_success_url');
            return redirect($after_success_url);

        } else {
            $after_failed_url = Session::get('after_failed_url');
            return redirect($after_failed_url);
        }
    }
    public function pay_via_razorpay(Request $request) {
        $payment_setting = $this->get_payment_gateway_info();

        $after_success_url = route('payment-api.webview-success-payment', ['bearer_token' => request()->bearer_token]);
        $after_failed_url = route('payment-api.webview-failed-payment');

        $razorpay_credentials = (object) [
            'razorpay_key'    => $payment_setting->razorpay_key,
            'razorpay_secret' => $payment_setting->razorpay_secret,
        ];

        return $this->pay_with_razorpay($request, $razorpay_credentials, $after_success_url, $after_failed_url);

    }
    public function pay_with_razorpay(Request $request, $razorpay_credentials, $after_success_url, $after_failed_url) {
        $input = $request->all();
        $api = new Api($razorpay_credentials->razorpay_key, $razorpay_credentials->razorpay_secret);
        $payment = $api->payment->fetch($input['razorpay_payment_id']);
        if (count($input) && !empty($input['razorpay_payment_id'])) {
            try {
                $response = $api->payment->fetch($input['razorpay_payment_id'])->capture(['amount' => $payment['amount']]);

                $paymentDetails = [
                    'transaction_id' => $response->id,
                    'amount'         => $response->amount,
                    'currency'       => $response->currency,
                    'fee'            => $response->fee,
                    'description'    => $response->description,
                    'payment_method' => $response->method,
                    'status'         => $response->status,
                ];

                Session::put('after_success_url', $after_success_url);
                Session::put('after_failed_url', $after_failed_url);
                Session::put('after_success_transaction', $response->id);
                Session::put('payment_details', $paymentDetails);

                return redirect($after_success_url);

            } catch (Exception $e) {
                info($e->getMessage());
                return redirect($after_failed_url);
            }
        } else {
            return redirect($after_failed_url);
        }

    }
    public function flutterwave_payment(Request $request) {
        $payment_setting = $this->get_payment_gateway_info();
        $curl = curl_init();
        $tnx_id = $request->tnx_id;
        $url = "https://api.flutterwave.com/v3/transactions/$tnx_id/verify";
        $token = $payment_setting?->flutterwave_secret_key;
        curl_setopt_array($curl, [
            CURLOPT_URL            => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING       => '',
            CURLOPT_MAXREDIRS      => 10,
            CURLOPT_TIMEOUT        => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST  => 'GET',
            CURLOPT_HTTPHEADER     => [
                'Content-Type: application/json',
                "Authorization: Bearer $token",
            ],
        ]);

        $response = curl_exec($curl);

        curl_close($curl);
        $response = json_decode($response);
        if ($response->status == 'success') {
            $paymentDetails = [
                'status'            => $response->status,
                'trx_id'            => $tnx_id,
                'amount'            => $response?->data?->amount,
                'amount_settled'    => $response?->data?->amount_settled,
                'currency'          => $response?->data?->currency,
                'charged_amount'    => $response?->data?->charged_amount,
                'app_fee'           => $response?->data?->app_fee,
                'merchant_fee'      => $response?->data?->merchant_fee,
                'card_last_4digits' => $response?->data?->card?->last_4digits,
            ];

            Session::put('payment_details', $paymentDetails);
            Session::put('after_success_transaction', $tnx_id);

            $image = 'success.png';
            $title = 'Your order has been placed';
            $sub_title = __('For check more details you can go to your dashboard');
            return view('basicpayment::app_order_notification', compact('image', 'title', 'sub_title'));

        } else {

            $image = 'fail.png';
            $title = 'Your order has been fail';
            $sub_title = __('Please try again for more details connect with us');
            return view('basicpayment::app_order_notification', compact('image', 'title', 'sub_title'));
        }

    }
    public function paystack_payment(Request $request) {
        $payment_setting = $this->get_payment_gateway_info();

        $reference = $request->reference;
        $transaction = $request->tnx_id;
        $secret_key = $payment_setting?->paystack_secret_key;
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL            => "https://api.paystack.co/transaction/verify/$reference",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING       => '',
            CURLOPT_MAXREDIRS      => 10,
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_TIMEOUT        => 30,
            CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST  => 'GET',
            CURLOPT_HTTPHEADER     => [
                "Authorization: Bearer $secret_key",
                'Cache-Control: no-cache',
            ],
        ]);
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
        $final_data = json_decode($response);
        if ($final_data->status == true) {
            $paymentDetails = [
                'status'             => $final_data?->data?->status,
                'transaction_id'     => $transaction,
                'requested_amount'   => $final_data?->data->requested_amount,
                'amount'             => $final_data?->data?->amount,
                'currency'           => $final_data?->data?->currency,
                'gateway_response'   => $final_data?->data?->gateway_response,
                'paid_at'            => $final_data?->data?->paid_at,
                'card_last_4_digits' => $final_data?->data->authorization?->last4,
            ];

            Session::put('payment_details', $paymentDetails);
            Session::put('after_success_transaction', $transaction);

            return response()->json(['message' => 'Payment Success.']);
        } else {
            info('here');
            $notification = 'Payment faild, please try again';
            return response()->json(['message' => $notification], 403);
        }
    }
    public function pay_via_instamojo() {
        $after_success_url = route('payment-api.webview-success-payment', ['bearer_token' => request()->bearer_token]);
        $after_failed_url = route('payment-api.webview-failed-payment');

        session()->put('after_success_url', $after_success_url);
        session()->put('after_failed_url', $after_failed_url);

        $payment_setting = $this->get_payment_gateway_info();

        $instamojo_credentials = (object) [
            'instamojo_api_key'    => $payment_setting->instamojo_api_key,
            'instamojo_auth_token' => $payment_setting->instamojo_auth_token,
            'account_mode'         => $payment_setting->instamojo_account_mode,
        ];

        return $this->pay_with_instamojo($instamojo_credentials);
    }
    public function pay_with_instamojo($instamojo_credentials) {
        $payable_currency = session()->get('payable_currency');
        $paid_amount = session()->get('paid_amount');

        $environment = $instamojo_credentials->account_mode;
        $api_key = $instamojo_credentials->instamojo_api_key;
        $auth_token = $instamojo_credentials->instamojo_auth_token;

        $environment = $instamojo_credentials->account_mode;
        $api_key = $instamojo_credentials->instamojo_api_key;
        $auth_token = $instamojo_credentials->instamojo_auth_token;

        if ($environment == 'Sandbox') {
            $url = 'https://test.instamojo.com/api/1.1/';
        } else {
            $url = 'https://www.instamojo.com/api/1.1/';
        }

        try {
            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, $url . 'payment-requests/');
            curl_setopt($ch, CURLOPT_HEADER, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER,
                ["X-Api-Key:$api_key",
                    "X-Auth-Token:$auth_token"]);
            $payload = [
                'purpose'                 => env('APP_NAME'),
                'amount'                  => $paid_amount,
                'phone'                   => '918160651749',
                'buyer_name'              => auth()->user()?->name,
                'redirect_url'            => route('payment-api.instamojo-success', ['bearer_token' => request()->bearer_token]),
                'send_email'              => true,
                'webhook'                 => 'http://www.example.com/webhook/',
                'send_sms'                => true,
                'email'                   => auth()->user()->email,
                'allow_repeated_payments' => false,
            ];
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($payload));
            $response = curl_exec($ch);
            curl_close($ch);
            $response = json_decode($response);
            session()->put('instamojo_credentials', $instamojo_credentials);

            if (!empty($response?->payment_request?->longurl)) {
                return redirect($response?->payment_request?->longurl);
            } else {
                $image = 'fail.png';
                $title = 'Your order has been fail';
                $sub_title = __('Please try again for more details connect with us');
                return view('basicpayment::app_order_notification', compact('image', 'title', 'sub_title'));
            }

        } catch (Exception $ex) {
            $after_failed_url = Session::get('after_failed_url');
            return redirect($after_failed_url);
        }

    }
    public function instamojo_success(Request $request) {

        $instamojo_credentials = Session::get('instamojo_credentials');

        $input = $request->all();
        $environment = $instamojo_credentials->account_mode;
        $api_key = $instamojo_credentials->instamojo_api_key;
        $auth_token = $instamojo_credentials->instamojo_auth_token;

        if ($environment == 'Sandbox') {
            $url = 'https://test.instamojo.com/api/1.1/';
        } else {
            $url = 'https://www.instamojo.com/api/1.1/';
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url . 'payments/' . $request->get('payment_id'));
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER,
            ["X-Api-Key:$api_key",
                "X-Auth-Token:$auth_token"]);
        $response = curl_exec($ch);
        $err = curl_error($ch);
        curl_close($ch);

        if ($err) {
            $after_failed_url = Session::get('after_failed_url');

            return redirect($after_failed_url);
        } else {
            $data = json_decode($response);
        }

        if ($data->success == true) {
            if ($data->payment->status == 'Credit') {
                Session::put('after_success_transaction', $request->get('payment_id'));
                Session::put('paid_amount', $data->payment->amount);
                $after_success_url = Session::get('after_success_url');

                return redirect($after_success_url);
            }
        } else {
            $after_failed_url = Session::get('after_failed_url');

            return redirect($after_failed_url);
        }
    }
    public function handleMailSending(array $mailData) {
        try {
            self::setMailConfig();

            // Get email template
            $template = EmailTemplate::where('name', 'order_completed')->firstOrFail();
            $mailData['subject'] = $template->subject;

            // Prepare email content
            $message = str_replace('{{name}}', $mailData['name'], $template->message);
            $message = str_replace('{{order_id}}', $mailData['order_id'], $message);
            $message = str_replace('{{paid_amount}}', $mailData['paid_amount'], $message);
            $message = str_replace('{{payment_method}}', $mailData['payment_method'], $message);

            if (self::isQueable()) {
                DefaultMailJob::dispatch($mailData['email'], $mailData, $message);
            } else {
                Mail::to($mailData['email'])->send(new DefaultMail($mailData, $message));
            }
        } catch (Exception $e) {
            info($e->getMessage());
        }
    }
    public function sendingPaymentStatusMail(array $mailData) {
        try {
            self::setMailConfig();

            // Get email template
            $template = EmailTemplate::where('name', 'payment_status')->firstOrFail();
            $mailData['subject'] = $template->subject;

            // Prepare email content
            $message = str_replace('{{name}}', $mailData['name'], $template->message);
            $message = str_replace('{{order_id}}', $mailData['order_id'], $message);
            $message = str_replace('{{paid_amount}}', $mailData['paid_amount'], $message);
            $message = str_replace('{{payment_status}}', $mailData['payment_status'], $message);

            if (self::isQueable()) {
                DefaultMailJob::dispatch($mailData['email'], $mailData, $message);
            } else {
                Mail::to($mailData['email'])->send(new DefaultMail($mailData, $message));
            }
        } catch (Exception $e) {
            info($e->getMessage());
        }
    }

}
