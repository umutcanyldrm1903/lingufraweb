<div class="tab-pane fade" id="outreach_provider_tab" role="tabpanel">
    <form action="{{ route('admin.update-outreach-providers') }}" method="POST">
        @csrf
        @method('PUT')

        <div class="alert alert-light border">
            <div><strong>{{ __('OpenAI / Lusha / IMAP') }}</strong></div>
            <div class="small text-muted">
                {{ __('These values are used by the Outreach Bot admin panel. Env values remain as fallback.') }}
            </div>
            <div class="small text-muted mt-1">
                {{ function_exists('imap_open') ? __('IMAP extension is available on this PHP runtime.') : __('IMAP extension is not available on this PHP runtime. The outreach bot will use its socket-based IMAP fallback instead, but the hosting must still allow outbound IMAPS or STARTTLS connections.') }}
            </div>
        </div>

        <h6 class="mb-3">{{ __('OpenAI') }}</h6>
        <div class="form-group">
            <label>{{ __('API Key') }}</label>
            <input type="text" class="form-control" name="outreach_openai_api_key" value="{{ data_get($setting, 'outreach_openai_api_key') }}">
        </div>
        <div class="row">
            <div class="col-md-6 form-group">
                <label>{{ __('Model') }}</label>
                <input type="text" class="form-control" name="outreach_openai_model" value="{{ data_get($setting, 'outreach_openai_model', 'gpt-5-mini') }}">
            </div>
            <div class="col-md-6 form-group">
                <label>{{ __('Timeout') }}</label>
                <input type="number" min="5" max="300" class="form-control" name="outreach_openai_timeout" value="{{ data_get($setting, 'outreach_openai_timeout', 60) }}">
            </div>
        </div>
        <div class="form-group">
            <label>{{ __('Base URL') }}</label>
            <input type="text" class="form-control" name="outreach_openai_base_url" value="{{ data_get($setting, 'outreach_openai_base_url', 'https://api.openai.com/v1') }}">
        </div>

        <hr>

        <h6 class="mb-3">{{ __('Lusha') }}</h6>
        <div class="form-group">
            <label>{{ __('API Key') }}</label>
            <input type="text" class="form-control" name="outreach_lusha_api_key" value="{{ data_get($setting, 'outreach_lusha_api_key') }}">
        </div>
        <div class="row">
            <div class="col-md-4 form-group">
                <label>{{ __('API Key Prefix') }}</label>
                <input type="text" class="form-control" name="outreach_lusha_api_key_prefix" value="{{ data_get($setting, 'outreach_lusha_api_key_prefix') }}" placeholder="{{ __('Optional') }}">
            </div>
            <div class="col-md-4 form-group">
                <label>{{ __('Timeout') }}</label>
                <input type="number" min="5" max="300" class="form-control" name="outreach_lusha_timeout" value="{{ data_get($setting, 'outreach_lusha_timeout', 45) }}">
            </div>
            <div class="col-md-4 form-group d-flex align-items-center">
                <div class="custom-control custom-checkbox mt-4">
                    <input type="hidden" name="outreach_lusha_send_authorization_header" value="0">
                    <input type="checkbox" class="custom-control-input" id="outreach_lusha_send_authorization_header" name="outreach_lusha_send_authorization_header" value="1" @checked((string) data_get($setting, 'outreach_lusha_send_authorization_header', '0') === '1')>
                    <label class="custom-control-label" for="outreach_lusha_send_authorization_header">{{ __('Send Authorization Header') }}</label>
                </div>
            </div>
        </div>
        <div class="form-group">
            <label>{{ __('Base URL') }}</label>
            <input type="text" class="form-control" name="outreach_lusha_base_url" value="{{ data_get($setting, 'outreach_lusha_base_url', 'https://api.lusha.com') }}">
        </div>
        <div class="row">
            <div class="col-md-6 form-group">
                <label>{{ __('Search Path') }}</label>
                <input type="text" class="form-control" name="outreach_lusha_search_path" value="{{ data_get($setting, 'outreach_lusha_search_path', '/prospecting/contact/search') }}">
            </div>
            <div class="col-md-6 form-group">
                <label>{{ __('Enrich Path') }}</label>
                <input type="text" class="form-control" name="outreach_lusha_enrich_path" value="{{ data_get($setting, 'outreach_lusha_enrich_path', '/prospecting/contact/enrich') }}">
            </div>
        </div>

        <hr>

        <h6 class="mb-3">{{ __('IMAP') }}</h6>
        <div class="row">
            <div class="col-md-6 form-group">
                <label>{{ __('Host') }}</label>
                <input type="text" class="form-control" name="outreach_imap_host" value="{{ data_get($setting, 'outreach_imap_host') }}">
            </div>
            <div class="col-md-3 form-group">
                <label>{{ __('Port') }}</label>
                <input type="number" min="1" max="65535" class="form-control" name="outreach_imap_port" value="{{ data_get($setting, 'outreach_imap_port', 993) }}">
            </div>
            <div class="col-md-3 form-group">
                <label>{{ __('Encryption') }}</label>
                <input type="text" class="form-control" name="outreach_imap_encryption" value="{{ data_get($setting, 'outreach_imap_encryption', 'ssl') }}">
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 form-group">
                <label>{{ __('Username') }}</label>
                <input type="text" class="form-control" name="outreach_imap_username" value="{{ data_get($setting, 'outreach_imap_username') }}">
            </div>
            <div class="col-md-6 form-group">
                <label>{{ __('Password') }}</label>
                <input type="text" class="form-control" name="outreach_imap_password" value="{{ data_get($setting, 'outreach_imap_password') }}">
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 form-group">
                <label>{{ __('Mailbox') }}</label>
                <input type="text" class="form-control" name="outreach_imap_mailbox" value="{{ data_get($setting, 'outreach_imap_mailbox', 'INBOX') }}">
            </div>
            <div class="col-md-6 form-group">
                <label>{{ __('Search Filter') }}</label>
                <input type="text" class="form-control" name="outreach_imap_search" value="{{ data_get($setting, 'outreach_imap_search', 'UNSEEN') }}">
            </div>
        </div>

        <button class="btn btn-primary">{{ __('Update') }}</button>
    </form>
</div>
