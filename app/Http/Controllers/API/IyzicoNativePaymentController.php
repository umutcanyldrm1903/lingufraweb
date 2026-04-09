<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Iyzipay\Model\Address;
use Iyzipay\Model\BasketItem;
use Iyzipay\Model\BasketItemType;
use Iyzipay\Model\Buyer;
use Iyzipay\Model\Currency;
use Iyzipay\Model\Locale;
use Iyzipay\Model\PaymentCard;
use Iyzipay\Model\PaymentChannel;
use Iyzipay\Model\PaymentGroup;
use Iyzipay\Model\ThreedsInitialize;
use Iyzipay\Model\ThreedsPayment;
use Iyzipay\Options;
use Iyzipay\Request\CreatePaymentRequest;
use Iyzipay\Request\CreateThreedsPaymentRequest;
use Modules\BasicPayment\app\Services\PaymentMethodService;
use Modules\Order\app\Models\Order;
use Modules\Order\app\Models\OrderItem;

class IyzicoNativePaymentController extends Controller
{
    public function initStudentPlan3ds(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'plan_key' => ['required', 'string'],
            'currency' => ['nullable', 'string'],
            'card_holder_name' => ['required', 'string'],
            'card_number' => ['required', 'string'],
            'expire_month' => ['required', 'string'],
            'expire_year' => ['required', 'string'],
            'cvc' => ['required', 'string'],
        ], [
            'plan_key.required' => 'Plan key is required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors(),
            ], 422);
        }

        /** @var User|null $user */
        $user = $request->user();
        if (!$user) {
            return response()->json(['status' => 'error', 'message' => 'UnAuthenticated'], 401);
        }

        $planKey = (string) $request->input('plan_key');
        $plan = $this->resolveStudentPlan($planKey);
        if (!$plan) {
            return response()->json([
                'status' => 'error',
                'message' => 'Plan not found',
            ], 404);
        }

        $paymentMethod = 'iyzico';
        /** @var PaymentMethodService $paymentService */
        $paymentService = app(PaymentMethodService::class);

        if (!$paymentService->isActive($paymentMethod)) {
            return response()->json([
                'status' => 'error',
                'message' => 'The selected payment method is now inactive.',
            ], 400);
        }

        $currencyCode = (string) ($request->input('currency') ?: (config('student_plans.currency') ?: 'TRY'));
        if (!$paymentService->isCurrencySupported($paymentMethod, $currencyCode)) {
            return response()->json([
                'status' => 'error',
                'message' => 'You are trying to use unsupported currency',
            ], 400);
        }

        $payableAmount = (float) ($plan->price ?? 0);
        if ($payableAmount <= 0) {
            return response()->json([
                'status' => 'error',
                'message' => 'Price is not defined for this plan.',
            ], 400);
        }

        $payableCharge = $paymentService->getPayableAmount($paymentMethod, $payableAmount, $currencyCode);
        $paidAmount = (float) (($payableCharge?->payable_amount ?? 0) + ($payableCharge?->gateway_charge ?? 0));

        DB::beginTransaction();
        try {
            $order = Order::create([
                'invoice_id' => Str::random(10),
                'buyer_id' => $user->id,
                'has_coupon' => 0,
                'payment_method' => $paymentMethod,
                'payment_status' => 'pending',
                'status' => 'pending',
                'payable_amount' => $payableAmount,
                'gateway_charge' => $payableCharge?->gateway_charge,
                'payable_with_charge' => $payableCharge?->payable_with_charge,
                'paid_amount' => $paidAmount,
                'payable_currency' => $currencyCode,
                'conversion_rate' => $payableCharge?->currency_rate ?? 1,
                'commission_rate' => cache()->get('setting')->commission_rate ?? 0,
                'order_type' => 'student_plan',
                'order_details' => [
                    'plan_key' => (string) ($plan->key ?? $planKey),
                    'title' => (string) ($plan->title ?? $planKey),
                    'duration_months' => (int) ($plan->duration_months ?? 0),
                    'lessons_total' => (int) ($plan->lessons_total ?? 0),
                    'cancel_total' => (int) ($plan->cancel_total ?? 0),
                    'price' => (float) ($plan->price ?? 0),
                    'currency' => $currencyCode,
                ],
            ]);

            OrderItem::create([
                'order_id' => $order->id,
                'price' => $payableAmount,
                'course_id' => 0,
                'commission_rate' => 0,
            ]);

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            report($e);
            return response()->json([
                'status' => 'error',
                'message' => 'Payment initialization failed.',
            ], 400);
        }

        $gateway = $paymentService->getGatewayDetails($paymentMethod);
        $apiKey = $gateway?->iyzico_api_key ?? null;
        $secretKey = $gateway?->iyzico_secret_key ?? null;
        $accountMode = $gateway?->iyzico_account_mode ?? 'sandbox';

        if (!$apiKey || !$secretKey) {
            $order->payment_status = 'cancelled';
            $order->save();
            return response()->json([
                'status' => 'error',
                'message' => 'Iyzico credentials not found',
            ], 400);
        }

        $options = new Options();
        $options->setApiKey($apiKey);
        $options->setSecretKey($secretKey);
        $options->setBaseUrl($accountMode === 'live' ? 'https://api.iyzipay.com' : 'https://sandbox-api.iyzipay.com');

        $paymentRequest = new CreatePaymentRequest();
        $paymentRequest->setLocale(Locale::TR);
        $paymentRequest->setConversationId($order->invoice_id);
        $paymentRequest->setPrice(number_format((float) $order->payable_amount, 2, '.', ''));
        $paymentRequest->setPaidPrice(number_format((float) ($order->payable_with_charge ?? $order->paid_amount), 2, '.', ''));
        $paymentRequest->setCurrency($this->normalizeCurrencyCode($currencyCode));
        $paymentRequest->setInstallment(1);
        $paymentRequest->setBasketId($order->invoice_id);
        $paymentRequest->setPaymentChannel(PaymentChannel::MOBILE);
        $paymentRequest->setPaymentGroup(PaymentGroup::PRODUCT);
        $paymentRequest->setCallbackUrl(route('iyzico-3ds-callback'));

        $paymentCard = new PaymentCard();
        $paymentCard->setCardHolderName(trim((string) $request->input('card_holder_name')));
        $paymentCard->setCardNumber($this->normalizeCardNumber((string) $request->input('card_number')));
        $paymentCard->setExpireMonth($this->normalizeExpireMonth((string) $request->input('expire_month')));
        $paymentCard->setExpireYear($this->normalizeExpireYear((string) $request->input('expire_year')));
        $paymentCard->setCvc($this->normalizeCvc((string) $request->input('cvc')));
        $paymentCard->setRegisterCard(0);
        $paymentRequest->setPaymentCard($paymentCard);

        $name = trim((string) ($user->name ?? ''));
        $nameParts = preg_split('/\s+/', $name, 2);
        $firstName = $nameParts[0] ?? 'Customer';
        $lastName = $nameParts[1] ?? $firstName;

        $buyer = new Buyer();
        $buyer->setId((string) $user->id);
        $buyer->setName($firstName);
        $buyer->setSurname($lastName);
        $buyer->setGsmNumber($user->phone ?: '+905350000000');
        $buyer->setEmail((string) ($user->email ?? ''));
        $buyer->setIdentityNumber('11111111111');
        $buyer->setRegistrationAddress($user->address ?: 'N/A');
        $buyer->setIp($request->ip());
        $buyer->setCity($user->city ?: 'Istanbul');
        $buyer->setCountry('Turkey');
        $buyer->setZipCode('34000');
        $paymentRequest->setBuyer($buyer);

        $address = new Address();
        $address->setContactName($name !== '' ? $name : 'Customer');
        $address->setCity($user->city ?: 'Istanbul');
        $address->setCountry('Turkey');
        $address->setAddress($user->address ?: 'N/A');
        $address->setZipCode('34000');
        $paymentRequest->setShippingAddress($address);
        $paymentRequest->setBillingAddress($address);

        $basketItem = new BasketItem();
        $basketItem->setId((string) $order->id);
        $basketItem->setName((string) ($order->orderDetails?->title ?? 'Plan'));
        $basketItem->setCategory1('Plan');
        $basketItem->setItemType(BasketItemType::VIRTUAL);
        $basketItem->setPrice(number_format((float) $order->payable_amount, 2, '.', ''));
        $paymentRequest->setBasketItems([$basketItem]);

        try {
            $threeDs = ThreedsInitialize::create($paymentRequest, $options);
        } catch (\Throwable $e) {
            report($e);
            $order->payment_status = 'cancelled';
            $order->save();
            return response()->json([
                'status' => 'error',
                'message' => 'Iyzico initialization failed',
            ], 400);
        }

        if ($threeDs->getStatus() !== 'success') {
            $order->payment_status = 'cancelled';
            $order->payment_details = json_encode([
                'status' => $threeDs->getStatus(),
                'error_code' => $threeDs->getErrorCode(),
                'error_message' => $threeDs->getErrorMessage(),
            ]);
            $order->save();

            return response()->json([
                'status' => 'error',
                'message' => $threeDs->getErrorMessage() ?: 'Iyzico initialization failed',
            ], 400);
        }

        // Keep it lightweight: htmlContent is base64-encoded and will be rendered in-app.
        return response()->json([
            'status' => 'success',
            'data' => [
                'invoice_id' => $order->invoice_id,
                'payment_id' => $threeDs->getPaymentId(),
                'html_content' => $threeDs->getHtmlContent(),
            ],
        ], 200);
    }

    public function threeDsCallback(Request $request)
    {
        $status = strtolower((string) $request->input('status', ''));
        $paymentId = trim((string) $request->input('paymentId', ''));
        $conversationId = trim((string) $request->input('conversationId', ''));
        $conversationData = (string) $request->input('conversationData', '');
        $mdStatus = trim((string) $request->input('mdStatus', ''));
        $signature = trim((string) $request->input('signature', ''));

        $invoiceId = $conversationId;
        $order = $invoiceId !== '' ? Order::where('invoice_id', $invoiceId)->first() : null;

        $image = 'fail.png';
        $title = 'Your order has been fail';
        $sub_title = __('Please try again for more details connect with us');
        $result = 'failed';

        if (!$order) {
            return view('basicpayment::app_order_notification', compact('image', 'title', 'sub_title', 'invoiceId', 'result'));
        }

        // Idempotency: callback may be hit multiple times.
        if (($order->payment_status ?? null) === 'paid' && ($order->status ?? null) === 'completed') {
            $image = 'success.png';
            $title = __('Payment Success.');
            $sub_title = __('For check more details you can go to your dashboard');
            $result = 'success';
            return view('basicpayment::app_order_notification', compact('image', 'title', 'sub_title', 'invoiceId', 'result'));
        }

        /** @var PaymentMethodService $paymentService */
        $paymentService = app(PaymentMethodService::class);
        $gateway = $paymentService->getGatewayDetails('iyzico');
        $apiKey = $gateway?->iyzico_api_key ?? null;
        $secretKey = $gateway?->iyzico_secret_key ?? null;
        $accountMode = $gateway?->iyzico_account_mode ?? 'sandbox';

        if (!$apiKey || !$secretKey) {
            $order->payment_status = 'cancelled';
            $order->save();
            return view('basicpayment::app_order_notification', compact('image', 'title', 'sub_title', 'invoiceId', 'result'));
        }

        // Optional callback signature validation.
        if ($signature !== '') {
            $dataToSign = $conversationData . ':' . $conversationId . ':' . $mdStatus . ':' . $paymentId . ':' . $status;
            $expected = bin2hex(hash_hmac('sha256', $dataToSign, $secretKey, true));
            if (!hash_equals($expected, $signature)) {
                $order->payment_status = 'cancelled';
                $order->payment_details = json_encode([
                    'status' => 'failed',
                    'reason' => 'invalid_signature',
                ]);
                $order->save();
                return view('basicpayment::app_order_notification', compact('image', 'title', 'sub_title', 'invoiceId', 'result'));
            }
        }

        if ($status !== 'success' || $mdStatus !== '1' || $paymentId === '') {
            $order->payment_status = 'cancelled';
            $order->payment_details = json_encode([
                'status' => 'failed',
                'md_status' => $mdStatus,
            ]);
            $order->save();
            return view('basicpayment::app_order_notification', compact('image', 'title', 'sub_title', 'invoiceId', 'result'));
        }

        $options = new Options();
        $options->setApiKey($apiKey);
        $options->setSecretKey($secretKey);
        $options->setBaseUrl($accountMode === 'live' ? 'https://api.iyzipay.com' : 'https://sandbox-api.iyzipay.com');

        $threeDsRequest = new CreateThreedsPaymentRequest();
        $threeDsRequest->setLocale(Locale::TR);
        $threeDsRequest->setConversationId($conversationId);
        $threeDsRequest->setPaymentId($paymentId);
        $threeDsRequest->setConversationData($conversationData);

        try {
            $threeDsPayment = ThreedsPayment::create($threeDsRequest, $options);
        } catch (\Throwable $e) {
            report($e);
            $order->payment_status = 'cancelled';
            $order->payment_details = json_encode([
                'status' => 'failed',
                'reason' => 'threeds_auth_exception',
            ]);
            $order->save();
            return view('basicpayment::app_order_notification', compact('image', 'title', 'sub_title', 'invoiceId', 'result'));
        }

        if ($threeDsPayment->getStatus() !== 'success' || $threeDsPayment->getPaymentStatus() !== 'SUCCESS') {
            $order->payment_status = 'cancelled';
            $order->payment_details = json_encode([
                'status' => $threeDsPayment->getStatus(),
                'payment_status' => $threeDsPayment->getPaymentStatus(),
                'error_code' => $threeDsPayment->getErrorCode(),
                'error_message' => $threeDsPayment->getErrorMessage(),
            ]);
            $order->save();
            return view('basicpayment::app_order_notification', compact('image', 'title', 'sub_title', 'invoiceId', 'result'));
        }

        DB::beginTransaction();
        try {
            $order->transaction_id = $paymentId;
            $order->payment_status = 'paid';
            $order->status = 'completed';
            $order->payment_details = json_encode([
                'provider' => 'iyzico',
                'type' => '3ds',
                'payment_id' => $paymentId,
                'conversation_id' => $conversationId,
                'md_status' => $mdStatus,
                'status' => $threeDsPayment->getStatus(),
                'payment_status' => $threeDsPayment->getPaymentStatus(),
                'paid_price' => $threeDsPayment->getPaidPrice(),
                'currency' => $threeDsPayment->getCurrency(),
            ]);
            $order->save();

            $this->finalizeOrder($order);

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            report($e);
            $order->payment_status = 'cancelled';
            $order->save();
            return view('basicpayment::app_order_notification', compact('image', 'title', 'sub_title', 'invoiceId', 'result'));
        }

        $image = 'success.png';
        $title = __('Payment Success.');
        $sub_title = __('For check more details you can go to your dashboard');
        $result = 'success';
        return view('basicpayment::app_order_notification', compact('image', 'title', 'sub_title', 'invoiceId', 'result'));
    }

    private function resolveStudentPlan(string $planKey): ?object
    {
        $planKey = trim($planKey);
        if ($planKey === '') {
            return null;
        }

        if (Schema::hasTable('student_plans')) {
            return DB::table('student_plans')
                ->where('key', $planKey)
                ->where('is_active', 1)
                ->first();
        }

        $plansConfig = (array) config('student_plans.plans', []);
        return isset($plansConfig[$planKey]) ? (object) $plansConfig[$planKey] : null;
    }

    private function normalizeCurrencyCode(string $code): string
    {
        $upper = strtoupper(trim($code));
        if ($upper === '') {
            return Currency::TL;
        }
        // Iyzipay constants are the ISO codes themselves.
        return $upper;
    }

    private function normalizeCardNumber(string $raw): string
    {
        return preg_replace('/\D+/', '', $raw) ?: '';
    }

    private function normalizeExpireMonth(string $raw): string
    {
        $digits = preg_replace('/\D+/', '', $raw) ?: '';
        $month = (int) $digits;
        if ($month < 1) $month = 1;
        if ($month > 12) $month = 12;
        return str_pad((string) $month, 2, '0', STR_PAD_LEFT);
    }

    private function normalizeExpireYear(string $raw): string
    {
        $digits = preg_replace('/\D+/', '', $raw) ?: '';
        if (strlen($digits) === 2) {
            $digits = '20' . $digits;
        }
        if (strlen($digits) < 4) {
            $digits = str_pad($digits, 4, '0', STR_PAD_LEFT);
        }
        return $digits;
    }

    private function normalizeCvc(string $raw): string
    {
        return preg_replace('/\D+/', '', $raw) ?: '';
    }

    private function finalizeOrder(Order $order): void
    {
        // Keep behavior aligned with existing payment_success handler.
        if (($order->order_type ?? null) !== 'student_plan') {
            return;
        }

        if (!Schema::hasTable('user_plans')) {
            return;
        }

        $details = $order->orderDetails;
        $durationMonths = (int) ($details?->duration_months ?? 0);
        $lessonsTotal = (int) ($details?->lessons_total ?? 0);
        $cancelTotal = (int) ($details?->cancel_total ?? 0);

        $planKey = trim((string) ($details?->plan_key ?? $details?->key ?? ''));
        if ($planKey === '') {
            $planKey = 'order_' . $order->id;
        }

        $planTitle = trim((string) ($details?->title ?? $details?->plan_title ?? ''));
        if ($planTitle === '') {
            $planTitle = 'Plan';
        }

        $existingPlan = DB::table('user_plans')
            ->where('user_id', $order->buyer_id)
            ->orderByDesc('last_order_id')
            ->orderByDesc('id')
            ->first();
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
            DB::table('user_plans')->where('id', $existingPlan->id)->update($payload);
        } else {
            DB::table('user_plans')->insert(array_merge($payload, [
                'user_id' => $order->buyer_id,
                'created_at' => now(),
            ]));
        }

        // Referral reward (Cowboy-like "Ücretsiz Ders Al" flow).
        try {
            app(\App\Services\Referral\ReferralService::class)->rewardReferrerForPaidPlanOrder($order);
        } catch (\Throwable $e) {
            report($e);
        }
    }
}
