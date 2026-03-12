<div class="tab-pane fade" id="mpesa_tab" role="tabpanel">
    <form action="{{ route('admin.mpesa-update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="row">
            <div class="form-group col-md-6">
                <label for="mpesa_account_mode">{{ __('Account Mode') }}</label>
                <select name="mpesa_account_mode" id="mpesa_account_mode" class="form-control">
                    <option {{ $basic_payment?->mpesa_account_mode == 'sandbox' ? 'selected' : '' }} value="sandbox">
                        {{ __('Sandbox') }}</option>
                    <option {{ $basic_payment?->mpesa_account_mode == 'openapi' ? 'selected' : '' }} value="openapi">
                        {{ __('OpenApi') }}</option>
                </select>
            </div>
            <div class="form-group col-md-6">
                <label for="mpesa_charge">{{ __('Gateway charge (%)') }}</label>
                <input type="text" class="form-control" name="mpesa_charge" id="mpesa_charge"
                    value="{{ $basic_payment?->mpesa_charge }}">
            </div>
            <div class="form-group col-md-6">
                <label for="mpesa_market">{{ __('Market') }}</label>
                @php
                    $markets = ['vodafoneGHA', 'vodacomTZN', 'vodacomLES', 'vodacomDRC', 'vodacomMOZ'];
                @endphp
                <select name="mpesa_market" id="mpesa_market" class="form-control">
                    @foreach ($markets as $market)
                        <option {{ $basic_payment?->mpesa_market == $market ? 'selected' : '' }}
                            value="{{ $market }}">{{ $market }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group  col-md-6">
                <label for="mpesa_origin">{{ __('Origin') }}</label>
                <input type="text" class="form-control" name="mpesa_origin" id="mpesa_origin"
                    value="{{ $basic_payment?->mpesa_origin }}" placeholder="e.g. * (allow all) or 127.0.0.1">
            </div>
            <div class="form-group  col-md-6">
                <label for="mpesa_shortcode">{{ __('ShortCode') }}</label>
                <input type="text" class="form-control" name="mpesa_shortcode" id="mpesa_shortcode"
                    value="{{ $basic_payment?->mpesa_shortcode }}">
            </div>
            <div class="form-group col-md-6">
                <label for="mpesa_api_key">{{ __('API key') }}</label>
                <input type="text" class="form-control" name="mpesa_api_key" id="mpesa_api_key"
                    value="{{ $basic_payment?->mpesa_api_key }}">
            </div>
            <div class="form-group col-md-12">
                <label for="mpesa_public_key">{{ __('Public key') }}</label>
                <textarea name="mpesa_public_key" id="mpesa_public_key" cols="30" rows="10" class="text-area-5 form-control">{{ $basic_payment?->mpesa_public_key }}</textarea>
            </div>
            <div class="form-group col-md-12">
                <label>{{ __('New Image') }} <code>({{ __('Recommended') }}: 210X100 PX)</code></label>
                <div id="mpesa_image_preview" class="image-preview">
                    <label for="mpesa_image_upload" id="mpesa_image_label">{{ __('Image') }}</label>
                    <input type="file" name="mpesa_image" id="mpesa_image_upload">
                </div>
            </div>
            <div class="form-group col-md-12">
                <label class="d-flex align-items-center">
                    <input type="hidden" value="inactive" name="mpesa_status" class="custom-switch-input">
                    <input type="checkbox" value="active" name="mpesa_status" class="custom-switch-input"
                        {{ $basic_payment?->mpesa_status == 'active' ? 'checked' : '' }}>
                    <span class="custom-switch-indicator"></span>
                    <span class="custom-switch-description">{{ __('Status') }}</span>
                </label>
            </div>
        </div>

        <button class="btn btn-primary">{{ __('Update') }}</button>
    </form>
    <div class="form-group mt-3">
        <label>{{ __('Callback url ') }} <span data-toggle="tooltip" data-placement="top"
                class="fa fa-info-circle text--primary"
                title="{{ __('Copy the Response URL and paste it wherever you need to use it.') }}"></span></label>
        <div class="input-group">
            <input type="text" value="{{ route('mpesa.callback') }}" id="mpesa_response_url"
                class="form-control" readonly>
            <div class="input-group-append">
                <div class="input-group-text">
                    <span id="copyButton" data-toggle="tooltip"
                        title="{{ __('Copy the Gmail login URL and paste it wherever you need to use it.') }}"><i
                            class="fas fa-copy"></i></span>
                </div>
            </div>
        </div>
    </div>
</div>
