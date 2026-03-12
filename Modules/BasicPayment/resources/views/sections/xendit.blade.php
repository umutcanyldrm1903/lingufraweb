<div class="tab-pane fade" id="xendit_tab" role="tabpanel">
    <form action="{{ route('admin.xendit-update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="row">
            <div class="form-group col-md-12">
                <label for="">{{ __('Gateway charge (%)') }}</label>
                <input type="text" class="form-control" name="xendit_charge"
                    value="{{ $payment_setting->xendit_charge }}">
            </div>
            <div class="form-group col-md-12">
                <label for="">{{ __('API key') }}</label>
                <input type="text" class="form-control" name="xendit_api_key"
                    value="{{ $payment_setting->xendit_api_key }}">
            </div>
            <div class="form-group col-md-12">
                <label>{{ __('New Image') }} <code>({{ __('Recommended') }}: 210X100 PX)</code></label>
                <div id="xendit_image_preview" class="image-preview">
                    <label for="xendit_image_upload" id="xendit_image_label">{{ __('Image') }}</label>
                    <input type="file" name="xendit_image" id="xendit_image_upload">
                </div>
            </div>
            <div class="form-group col-md-12">
                <label class="d-flex align-items-center">
                    <input type="hidden" value="inactive" name="xendit_status" class="custom-switch-input">
                    <input type="checkbox" value="active" name="xendit_status" class="custom-switch-input"
                        {{ $payment_setting?->xendit_status == 'active' ? 'checked' : '' }}>
                    <span class="custom-switch-indicator"></span>
                    <span class="custom-switch-description">{{ __('Status') }}</span>
                </label>
            </div>
        </div>

        <button class="btn btn-primary">{{ __('Update') }}</button>
    </form>
</div>
