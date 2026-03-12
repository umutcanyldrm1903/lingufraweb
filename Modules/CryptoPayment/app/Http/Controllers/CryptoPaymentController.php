<?php

namespace Modules\CryptoPayment\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Modules\CryptoPayment\app\Models\CryptoPG;

class CryptoPaymentController extends Controller {
    public function __construct() {
        $this->middleware('auth:admin');
    }
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id) {
        checkAdminHasPermissionAndThrowException('basic.payment.update');

        $request->validate([
            'crypto_status'           => 'required|in:active,inactive',
            'crypto_sandbox'          => 'required|in:1,0',
            'crypto_token'            => 'required|string',
            'crypto_image'            => 'nullable|image|max:256',
            'crypto_charge'           => 'required|numeric',
            'crypto_receive_currency' => 'required|string',
        ]);

        $cryptoImage = CryptoPG::firstOrCreate(['key' => 'crypto_image'], ['value' => 'uploads/website-images/coingate.webp']);
        $oldImage = $cryptoImage->value;

        if ($request->hasFile('crypto_image')) {
            $image = file_upload(
                file: $request->file('crypto_image'),
                path: 'uploads/custom-images/crypto/',
                oldFile: $oldImage
            );
            $cryptoImage->update(['value' => $image]);
        }

        $cryptoKeys = [
            'crypto_status',
            'crypto_sandbox',
            'crypto_token',
            'crypto_charge',
            'crypto_receive_currency',
        ];

        foreach ($request->only($cryptoKeys) as $key => $value) {
            CryptoPG::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }

        Cache::forget('cryptoConfig');

        return redirect()->back()->with(['messege' => __('Updated Successfully'), 'alert-type' => 'success']);
    }
}
