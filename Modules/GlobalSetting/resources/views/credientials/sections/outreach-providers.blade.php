<div class="tab-pane fade" id="outreach_provider_tab" role="tabpanel">
    <form action="{{ route('admin.update-outreach-providers') }}" method="POST">
        @csrf
        @method('PUT')

        <div class="alert alert-light border">
            <div><strong>OpenAI / Lusha / IMAP Ayarları</strong></div>
            <div class="small text-muted">
                Bu alandaki bilgiler Outreach Bot tarafından kullanılır. Buradaki değer boşsa sistem `.env` içindeki değeri yedek olarak kullanır.
            </div>
            <div class="small text-muted mt-1">
                {{ function_exists('imap_open') ? 'Bu PHP ortamında IMAP eklentisi aktif.' : 'Bu PHP ortamında IMAP eklentisi aktif değil. Sistem socket tabanlı yedek bağlantıyı dener; yine de hosting tarafında IMAPS veya STARTTLS çıkışına izin verilmelidir.' }}
            </div>
        </div>

        <h6 class="mb-3">Yapay Zeka Sağlayıcısı</h6>
        <div class="form-group">
            <label>API Anahtarı</label>
            <input type="text" class="form-control" name="outreach_openai_api_key" value="{{ data_get($setting, 'outreach_openai_api_key') }}">
            <small class="text-muted">OpenAI ya da OpenAI uyumlu sağlayıcının anahtarını yaz. Örnek: OpenAI, Groq.</small>
        </div>
        <div class="row">
            <div class="col-md-6 form-group">
                <label>Model</label>
                <input type="text" class="form-control" name="outreach_openai_model" value="{{ data_get($setting, 'outreach_openai_model', 'gpt-5-mini') }}">
                <small class="text-muted">Örnek: `gpt-5-mini` veya kullandığın sağlayıcının model adı.</small>
            </div>
            <div class="col-md-6 form-group">
                <label>Zaman Aşımı</label>
                <input type="number" min="5" max="300" class="form-control" name="outreach_openai_timeout" value="{{ data_get($setting, 'outreach_openai_timeout', 60) }}">
                <small class="text-muted">İstek yanıtı için beklenecek saniye.</small>
            </div>
        </div>
        <div class="form-group">
            <label>Base URL</label>
            <input type="text" class="form-control" name="outreach_openai_base_url" value="{{ data_get($setting, 'outreach_openai_base_url', 'https://api.openai.com/v1') }}">
            <small class="text-muted">OpenAI için varsayılan `https://api.openai.com/v1`, Groq için `https://api.groq.com/openai/v1`</small>
        </div>

        <hr>

        <h6 class="mb-3">Lusha Ayarları</h6>
        <div class="form-group">
            <label>API Anahtarı</label>
            <input type="text" class="form-control" name="outreach_lusha_api_key" value="{{ data_get($setting, 'outreach_lusha_api_key') }}">
            <small class="text-muted">Lead arama ve enrich için kullanılan Lusha anahtarı.</small>
        </div>
        <div class="row">
            <div class="col-md-4 form-group">
                <label>Anahtar Ön Eki</label>
                <input type="text" class="form-control" name="outreach_lusha_api_key_prefix" value="{{ data_get($setting, 'outreach_lusha_api_key_prefix') }}" placeholder="İsteğe bağlı">
            </div>
            <div class="col-md-4 form-group">
                <label>Zaman Aşımı</label>
                <input type="number" min="5" max="300" class="form-control" name="outreach_lusha_timeout" value="{{ data_get($setting, 'outreach_lusha_timeout', 45) }}">
            </div>
            <div class="col-md-4 form-group d-flex align-items-center">
                <div class="custom-control custom-checkbox mt-4">
                    <input type="hidden" name="outreach_lusha_send_authorization_header" value="0">
                    <input type="checkbox" class="custom-control-input" id="outreach_lusha_send_authorization_header" name="outreach_lusha_send_authorization_header" value="1" @checked((string) data_get($setting, 'outreach_lusha_send_authorization_header', '0') === '1')>
                    <label class="custom-control-label" for="outreach_lusha_send_authorization_header">Authorization Header da gönder</label>
                </div>
            </div>
        </div>
        <div class="form-group">
            <label>Base URL</label>
            <input type="text" class="form-control" name="outreach_lusha_base_url" value="{{ data_get($setting, 'outreach_lusha_base_url', 'https://api.lusha.com') }}">
        </div>
        <div class="row">
            <div class="col-md-6 form-group">
                <label>Arama Endpoint Yolu</label>
                <input type="text" class="form-control" name="outreach_lusha_search_path" value="{{ data_get($setting, 'outreach_lusha_search_path', '/prospecting/contact/search') }}">
            </div>
            <div class="col-md-6 form-group">
                <label>Enrich Endpoint Yolu</label>
                <input type="text" class="form-control" name="outreach_lusha_enrich_path" value="{{ data_get($setting, 'outreach_lusha_enrich_path', '/prospecting/contact/enrich') }}">
            </div>
        </div>

        <hr>

        <h6 class="mb-3">IMAP Yanıt Takibi</h6>
        <div class="row">
            <div class="col-md-6 form-group">
                <label>Sunucu (Host)</label>
                <input type="text" class="form-control" name="outreach_imap_host" value="{{ data_get($setting, 'outreach_imap_host') }}">
            </div>
            <div class="col-md-3 form-group">
                <label>Port</label>
                <input type="number" min="1" max="65535" class="form-control" name="outreach_imap_port" value="{{ data_get($setting, 'outreach_imap_port', 993) }}">
            </div>
            <div class="col-md-3 form-group">
                <label>Şifreleme</label>
                <input type="text" class="form-control" name="outreach_imap_encryption" value="{{ data_get($setting, 'outreach_imap_encryption', 'ssl') }}">
                <small class="text-muted">Genelde `ssl` kullanılır.</small>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 form-group">
                <label>Kullanıcı Adı</label>
                <input type="text" class="form-control" name="outreach_imap_username" value="{{ data_get($setting, 'outreach_imap_username') }}">
            </div>
            <div class="col-md-6 form-group">
                <label>Şifre</label>
                <input type="text" class="form-control" name="outreach_imap_password" value="{{ data_get($setting, 'outreach_imap_password') }}">
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 form-group">
                <label>Klasör (Mailbox)</label>
                <input type="text" class="form-control" name="outreach_imap_mailbox" value="{{ data_get($setting, 'outreach_imap_mailbox', 'INBOX') }}">
            </div>
            <div class="col-md-6 form-group">
                <label>Arama Filtresi</label>
                <input type="text" class="form-control" name="outreach_imap_search" value="{{ data_get($setting, 'outreach_imap_search', 'UNSEEN') }}">
                <small class="text-muted">Yeni yanıtları çekmek için genelde `UNSEEN` bırakılır.</small>
            </div>
        </div>

        <button class="btn btn-primary">Ayarları Güncelle</button>
    </form>
</div>
