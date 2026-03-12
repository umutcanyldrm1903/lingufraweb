<div class="tab-pane fade" id="azampay_tab" role="tabpanel">
    <form action="{{ route('admin.azampay-update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="row">

            <div class="form-group col-md-6">
                <label for="">{{ __('Account Mode') }}</label>
                <select name="azampay_account_mode" id="azampay_account_mode" class="form-control">
                    <option {{ $payment_setting->azampay_account_mode == 'live' ? 'selected' : '' }} value="live">
                        {{ __('Live') }}</option>
                    <option {{ $payment_setting->azampay_account_mode == 'sandbox' ? 'selected' : '' }} value="sandbox">
                        {{ __('Sandbox') }}</option>
                </select>
            </div>
            <div class="form-group col-md-6">
                <label for="">{{ __('Gateway charge (%)') }}</label>
                <input type="text" class="form-control" name="azampay_charge"
                    value="{{ $payment_setting->azampay_charge }}">
            </div>
            <div class="form-group col-md-6">
                <label for="">{{ __('App Name') }}</label>
                <input type="text" class="form-control" name="azampay_app_name"
                    value="{{ $payment_setting->azampay_app_name }}">
            </div>
            <div class="form-group col-md-6">
                <label for="">{{ __('Token') }}</label>
                <input type="text" class="form-control" name="azampay_token"
                    value="{{ $payment_setting->azampay_token }}">
            </div>
            <div class="form-group col-md-12">
                <label for="">{{ __('Client Id') }}</label>
                @if (env('APP_MODE') == 'DEMO')
                    <input type="text" class="form-control" name="azampay_client_id" value="DEMO-83493483-TEST-KEY">
                @else
                    <input type="text" class="form-control" name="azampay_client_id"
                        value="{{ $payment_setting->azampay_client_id }}">
                @endif

            </div>
            <div class="form-group col-md-12">
                <label for="">{{ __('Secret Key') }}</label>
                @if (env('APP_MODE') == 'DEMO')
                    <input type="text" class="form-control" name="azampay_client_secret"
                        value="TEST98384934-SECRET-KEY">
                @else
                    <input type="text" class="form-control" name="azampay_client_secret"
                        value="{{ $payment_setting->azampay_client_secret }}">
                @endif
            </div>

            <div class="form-group col-md-12">
                <label>{{ __('New Image') }} <code>({{ __('Recommended') }}: 210X100 PX)</code></label>
                <div id="azampay_image_preview" class="image-preview">
                    <label for="azampay_image_upload" id="azampay_image_label">{{ __('Image') }}</label>
                    <input type="file" name="azampay_image" id="azampay_image_upload">
                </div>
            </div>
            <div class="form-group col-md-12">
                <label class="d-flex align-items-center">
                    <input type="hidden" value="inactive" name="azampay_status" class="custom-switch-input">
                    <input type="checkbox" value="active" name="azampay_status" class="custom-switch-input"
                        {{ $payment_setting?->azampay_status == 'active' ? 'checked' : '' }}>
                    <span class="custom-switch-indicator"></span>
                    <span class="custom-switch-description">{{ __('Status') }}</span>
                </label>
            </div>
        </div>

        <button class="btn btn-primary">{{ __('Update') }}</button>
    </form>
</div>
