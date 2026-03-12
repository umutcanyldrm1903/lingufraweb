<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Modules\BasicPayment\app\Services\PaymentMethodService;
use Modules\Order\app\Models\Order;
use Modules\Order\app\Models\OrderItem;

class PlanController extends Controller
{
    public function purchase(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'plan_key' => ['required', 'string'],
        ], [
            'plan_key.required' => 'Plan key is required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors(),
            ], 422);
        }

        $user = auth()->user();
        if (!$user) {
            return response()->json(['status' => 'error', 'message' => 'UnAuthenticated'], 401);
        }

        $planKey = (string) $request->input('plan_key');
        $plan = null;

        if (Schema::hasTable('student_plans')) {
            $plan = DB::table('student_plans')
                ->where('key', $planKey)
                ->where('is_active', 1)
                ->first();
        } else {
            $plansConfig = (array) config('student_plans.plans', []);
            $plan = isset($plansConfig[$planKey]) ? (object) $plansConfig[$planKey] : null;
        }

        if (!$plan) {
            return response()->json([
                'status' => 'error',
                'message' => 'Plan not found',
            ], 404);
        }

        $paymentMethod = 'iyzico';
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
        $paidAmount = $payableCharge?->payable_amount + $payableCharge?->gateway_charge;

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

        $token = $user->createToken('extra-token', ['extra'], now()->addWeek())->plainTextToken;
        $paymentUrl = route('payment-api.payment', [
            'token' => $token,
            'order_id' => $order->invoice_id,
        ]);

        return response()->json([
            'status' => 'success',
            'data' => [
                'invoice_id' => $order->invoice_id,
                'payment_url' => $paymentUrl,
            ],
        ], 200);
    }
}
