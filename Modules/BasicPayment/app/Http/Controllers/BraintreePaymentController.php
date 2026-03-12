<?php

namespace Modules\BasicPayment\app\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Modules\BasicPayment\app\Services\BraintreeService;

class BraintreePaymentController {
    protected $braintree;

    public function __construct(BraintreeService $braintree) {
        $this->braintree = $braintree->gateway();
    }

    public function token(): JsonResponse {
        try {
            $clientToken = $this->braintree->clientToken()->generate();
            return response()->json([
                'success' => true,
                'token'   => $clientToken,
            ]);
        } catch (\Exception $e) {
            info('Braintree Token Generation Failed', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function checkout(Request $request) {
        try {
            $validator = Validator::make($request->all(), [
                'payment_method_nonce' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json(['success' => false, 'message' => $validator->errors()->first()], 422);
            }
            $paid_amount = session()->get('paid_amount');
            // Attempt transaction
            $result = $this->braintree->transaction()->sale([
                'amount'             => $paid_amount,
                'paymentMethodNonce' => $request->payment_method_nonce,
                'options'            => [
                    'submitForSettlement' => true,
                ],
            ]);

            // Handle successful transaction
            if ($result->success) {
                $paymentDetails = [
                    'transaction_id' => $result->transaction->id,
                    'amount'         => $result->transaction->amount,
                    'currency'       => $result->transaction->currencyIsoCode,
                    'payment_status' => $result->transaction->status,
                    'created'        => $result->transaction->createdAt,
                ];
                Session::put('payment_details', $paymentDetails);
                Session::put('after_success_transaction', $result->transaction->id);

                return response()->json([
                    'success'        => true,
                    'transaction_id' => $result->transaction->id,
                ]);
            } else {
                // Handle failure from Braintree
                info('Braintree transaction failed', ['error' => $result->message]);

                return response()->json([
                    'success' => false,
                    'message' => $result->message,
                ], 500);
            }

        } catch (\Exception $e) {
            // Catch general exceptions
            info('Checkout exception', ['exception' => $e]);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred during payment processing. Please try again.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
}