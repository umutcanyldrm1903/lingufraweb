<div class="tab-pane fade" id="braintree_tab" role="tabpanel">
    <form action="{{ route('admin.update-braintree') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="">{{ __('Account Mode') }}</label>
                    <select name="braintree_account_mode" id="braintree_account_mode" class="form-control">
                        <option {{ $basic_payment->braintree_account_mode == 'live' ? 'selected' : '' }} value="live">
                            {{ __('Live') }}</option>
                        <option {{ $basic_payment->braintree_account_mode == 'sandbox' ? 'selected' : '' }}
                            value="sandbox">{{ __('Sandbox') }}</option>
                    </select>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="form-group">
                    <label for="">{{ __('Gateway charge (%)') }}</label>
                    <input type="text" class="form-control" name="braintree_charge"
                        value="{{ $basic_payment->braintree_charge }}">
                </div>
            </div>
             <div class="form-group col-md-6">
                <label for="">{{ __('Currency Name') }}</label>
                <select name="braintree_currency" id="" class="form-control">
                    <option value="">{{ __('Select Currency') }}</option>
                    @foreach (allCurrencies() as $currency)
                        <option @selected(strtolower($basic_payment?->braintree_currency) == strtolower($currency->currency_code))
                            value="{{ $currency->currency_code }}">{{ $currency->currency_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-6">
                <div class="form-group">
                    <label for="">{{ __('Merchant ID') }}</label>
                        <input type="text" class="form-control" name="braintree_merchant_id"
                            value="{{ $basic_payment->braintree_merchant_id }}">
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="">{{ __('Public key') }}</label>
                    @if (env('APP_MODE') == 'DEMO')
                        <input type="text" class="form-control" name="braintree_public_key"
                            value="PAYPAL-TEST-CLIENT98934343-343-ID">
                    @else
                        <input type="text" class="form-control" name="braintree_public_key"
                            value="{{ $basic_payment->braintree_public_key }}">
                    @endif

                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="">{{ __('Private Key') }}</label>
                    @if (env('APP_MODE') == 'DEMO')
                        <input type="text" class="form-control" name="braintree_private_key"
                            value="PAYPAL-TEST-398439483-SECRET-KEY">
                    @else
                        <input type="text" class="form-control" name="braintree_private_key"
                            value="{{ $basic_payment->braintree_private_key }}">
                    @endif
                </div>
            </div>
        </div>

        <div class="form-group">
            <label>{{ __('New Image') }} <code>({{ __('Recommended') }}: 210X100 PX)</code></label>
            <div id="image-preview-braintree" class="image-preview">
                <label for="image-upload-braintree"
                    id="image-label-braintree">{{ __('Image') }}</label>
                <input type="file" name="braintree_image" id="image-upload-braintree">
            </div>
        </div>
        <div class="form-group">
            <label class="d-flex align-items-center">
                <input type="hidden" value="inactive" name="braintree_status" class="custom-switch-input">
                <input type="checkbox" value="active" name="braintree_status" class="custom-switch-input"
                    {{ $basic_payment?->braintree_status == 'active' ? 'checked' : '' }}>
                <span class="custom-switch-indicator"></span>
                <span class="custom-switch-description">{{ __('Status') }}</span>
            </label>
        </div>

        <button class="btn btn-primary">{{ __('Update') }}</button>
    </form>
</div>
