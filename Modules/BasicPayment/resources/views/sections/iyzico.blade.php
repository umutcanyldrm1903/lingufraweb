<div class="tab-pane fade" id="iyzico_tab" role="tabpanel">
    <form action="{{ route('admin.iyzico-update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="row">
            <div class="form-group col-md-6">
                <label for="">{{ __('Account Mode') }}</label>
                <select name="iyzico_account_mode" id="iyzico_account_mode" class="form-control">
                    <option {{ ($payment_setting->iyzico_account_mode ?? 'sandbox') == 'live' ? 'selected' : '' }} value="live">
                        {{ __('Live') }}</option>
                    <option {{ ($payment_setting->iyzico_account_mode ?? 'sandbox') == 'sandbox' ? 'selected' : '' }} value="sandbox">
                        {{ __('Sandbox') }}</option>
                </select>
            </div>
            <div class="form-group col-md-6">
                <label for="">{{ __('Gateway charge (%)') }}</label>
                <input type="text" class="form-control" name="iyzico_charge" value="{{ $payment_setting->iyzico_charge ?? 0 }}">
            </div>
            <div class="form-group col-md-12">
                <label for="">{{ __('API Key') }}</label>
                @if (env('APP_MODE') == 'DEMO')
                    <input type="text" class="form-control" name="iyzico_api_key" value="DEMO-IYZICO-API-KEY">
                @else
                    <input type="text" class="form-control" name="iyzico_api_key" value="{{ $payment_setting->iyzico_api_key ?? '' }}">
                @endif
            </div>
            <div class="form-group col-md-12">
                <label for="">{{ __('Secret Key') }}</label>
                @if (env('APP_MODE') == 'DEMO')
                    <input type="text" class="form-control" name="iyzico_secret_key" value="DEMO-IYZICO-SECRET-KEY">
                @else
                    <input type="text" class="form-control" name="iyzico_secret_key" value="{{ $payment_setting->iyzico_secret_key ?? '' }}">
                @endif
            </div>
            <div class="form-group col-md-12">
                <label>{{ __('New Image') }} <code>({{ __('Recommended') }}: 210X100 PX)</code></label>
                <div id="iyzico_image_preview" class="image-preview">
                    <label for="iyzico_image_upload" id="iyzico_image_label">{{ __('Image') }}</label>
                    <input type="file" name="iyzico_image" id="iyzico_image_upload">
                </div>
            </div>
            <div class="form-group col-md-12">
                <label class="d-flex align-items-center">
                    <input type="hidden" value="inactive" name="iyzico_status" class="custom-switch-input">
                    <input type="checkbox" value="active" name="iyzico_status" class="custom-switch-input"
                        {{ ($payment_setting->iyzico_status ?? 'inactive') == 'active' ? 'checked' : '' }}>
                    <span class="custom-switch-indicator"></span>
                    <span class="custom-switch-description">{{ __('Status') }}</span>
                </label>
            </div>
        </div>

        <button class="btn btn-primary">{{ __('Update') }}</button>
    </form>
</div>
