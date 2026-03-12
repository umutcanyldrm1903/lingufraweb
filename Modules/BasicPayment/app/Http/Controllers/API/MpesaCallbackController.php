<?php

namespace Modules\BasicPayment\app\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Exception;
use Illuminate\Http\Request;
use Modules\BasicPayment\app\Http\Controllers\PaymentController;
use Modules\Order\app\Models\Enrollment;
use Modules\Order\app\Models\Order;
use Modules\Order\app\Traits\GiftOrderTraits;

class MpesaCallbackController extends Controller {
    use GiftOrderTraits;
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request) {
        try {
            if ($request->input('input_ResultCode') !== 'INS-0') {
                info('M-Pesa Callback: Payment failed. Please try again.');
                return;
            }

            $input_ThirdPartyConversationID = $request->input('input_ThirdPartyConversationID');
            $transaction_id = $request->input('input_TransactionID');

            $paymentDetails = json_encode([
                'input_OriginalConversationID'   => $request->input('input_OriginalConversationID'),
                'input_ThirdPartyConversationID' => $input_ThirdPartyConversationID,
                'input_TransactionID'            => $transaction_id,
                'input_ResultCode'               => $request->input('input_ResultCode'),
                'input_ResultDesc'               => $request->input('input_ResultDesc'),
            ]);

            $order = Order::with(['orderItems', 'user'])
                ->where('transaction_id', $input_ThirdPartyConversationID)
                ->first();

            if (!$order) {
                info('M-Pesa Callback: Order not found.');
                return;
            }

            // Update order
            $order->update([
                'payment_status'  => 'paid',
                'transaction_id'  => $transaction_id,
                'payment_details' => $paymentDetails,
            ]);

            // Process order items and update instructor wallets
            foreach ($order->orderItems as $orderItem) {
                $commission = $orderItem->price * ($orderItem->commission_rate / 100);
                $instructorEarning = $orderItem->price - $commission;

                $instructor = Course::find($orderItem->course_id)?->instructor;
                if ($instructor) {
                    $instructor->increment('wallet_balance', $instructorEarning);
                }
            }

            // Handle gift or normal order enrollment
            if ($order->isGiftOrder()) {
                $this->giftOrderDetailsUpdate($order);
            } else {
                foreach ($order->orderItems as $item) {
                    Enrollment::firstOrCreate(
                        ['user_id' => $order->buyer_id, 'course_id' => $item->course_id],
                        ['order_id' => $order->id, 'has_access' => 1]
                    );
                }
            }

            // Send payment status email
            $user = $order->user;
            if ($user) {
                $paymentController = new PaymentController();
                $paymentController->sendingPaymentStatusMail([
                    'email'          => $user->email,
                    'name'           => $user->name,
                    'order_id'       => $order->invoice_id,
                    'paid_amount'    => "{$order->paid_amount} {$order->payable_currency}",
                    'payment_status' => $order->payment_status,
                ]);

                session()->forget(
                    [
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

            info('M-Pesa Callback: Payment Success.');
        } catch (Exception $e) {
            info('M-Pesa Callback: Payment Error', ['message' => $e->getMessage()]);
        }
    }

}
