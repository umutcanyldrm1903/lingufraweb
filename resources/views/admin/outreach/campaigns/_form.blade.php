@php
    /** @var \App\Models\OutreachCampaign|null $outreachCampaign */
    $outreachCampaign = $outreachCampaign ?? null;
    $statusLabels = [
        'draft' => 'Taslak',
        'imported' => 'Lead Yüklendi',
        'enriched' => 'Zenginleştirildi',
        'generated' => 'Mesaj Üretildi',
        'approved' => 'Onaylandı',
        'sent' => 'Gönderildi',
    ];
@endphp

<div class="alert alert-light border">
    <div><strong>Bu kampanya ne yapar?</strong></div>
    <div class="small text-muted">Lusha'dan kişi çeker, yapay zeka ile kişiye özel e-posta taslağı üretir ve kurumsal mail hesabından gönderir.</div>
    <div class="small text-muted mt-1">Saat alanları İstanbul saatine göredir. Örneğin `9` değeri sabah `09:00`, `18` değeri akşam `18:00` anlamına gelir.</div>
</div>

<div class="row">
    <div class="col-md-6 form-group">
        <label>Kampanya Adı</label>
        <input type="text" name="name" class="form-control" value="{{ old('name', $outreachCampaign?->name) }}" required>
        <small class="text-muted">Panelde göreceğin iç isim. Örnek: `IK Yoneticileri Mart 2026`</small>
    </div>
    <div class="col-md-3 form-group">
        <label>Mesaj Dili</label>
        <select name="language" class="form-control">
            <option value="tr" @selected(old('language', $outreachCampaign?->language ?? 'tr') === 'tr')>TR</option>
            <option value="en" @selected(old('language', $outreachCampaign?->language) === 'en')>EN</option>
        </select>
    </div>
    <div class="col-md-3 form-group">
        <label>Kampanya Durumu</label>
        <select name="status" class="form-control">
            @foreach (['draft', 'imported', 'enriched', 'generated', 'approved', 'sent'] as $status)
                <option value="{{ $status }}" @selected(old('status', $outreachCampaign?->status ?? 'draft') === $status)>{{ $statusLabels[$status] }}</option>
            @endforeach
        </select>
    </div>
</div>

<div class="row">
    <div class="col-md-4 form-group">
        <label>Şirket Adı</label>
        <input type="text" name="company_name" class="form-control" value="{{ old('company_name', $outreachCampaign?->company_name) }}">
        <small class="text-muted">E-postayı atan şirket adı.</small>
    </div>
    <div class="col-md-4 form-group">
        <label>Şirket Websitesi</label>
        <input type="text" name="company_website" class="form-control" value="{{ old('company_website', $outreachCampaign?->company_website) }}">
        <small class="text-muted">Yapay zekanın bağlam kurması için kullanılır.</small>
    </div>
    <div class="col-md-4 form-group">
        <label>Ürün / Hizmet Adı</label>
        <input type="text" name="product_name" class="form-control" value="{{ old('product_name', $outreachCampaign?->product_name) }}">
        <small class="text-muted">Ne sattığını kısa ve net yaz.</small>
    </div>
</div>

<div class="row">
    <div class="col-md-4 form-group">
        <label>Yazım Tonu</label>
        <input type="text" name="tone" class="form-control" value="{{ old('tone', $outreachCampaign?->tone ?? 'consultative') }}">
        <small class="text-muted">Örnek: `consultative`, `professional`, `friendly`</small>
    </div>
    <div class="col-md-4 form-group">
        <label>Saat Dilimi</label>
        <input type="text" name="timezone" class="form-control" value="{{ old('timezone', $outreachCampaign?->timezone ?? 'Europe/Istanbul') }}" required readonly>
        <small class="text-muted">Sistem bu kampanyada İstanbul saatini kullanır.</small>
    </div>
    <div class="col-md-4 form-group">
        <label>Çıkış / İptal E-postası</label>
        <input type="email" name="unsubscribe_mailto" class="form-control" value="{{ old('unsubscribe_mailto', $outreachCampaign?->unsubscribe_mailto) }}">
        <small class="text-muted">“Bu mailleri almak istemiyorum” yanıtları için adres.</small>
    </div>
</div>

<div class="row">
    <div class="col-md-6 form-group">
        <label>Hedef Kitle Özeti</label>
        <textarea name="audience_summary" class="form-control" rows="4">{{ old('audience_summary', $outreachCampaign?->audience_summary) }}</textarea>
        <small class="text-muted">Kime yazdığını anlat. Örnek: `IK müdürleri, L&D yöneticileri, kurumsal eğitim satın almacıları`</small>
    </div>
    <div class="col-md-6 form-group">
        <label>Teklif Özeti</label>
        <textarea name="offer_summary" class="form-control" rows="4">{{ old('offer_summary', $outreachCampaign?->offer_summary) }}</textarea>
        <small class="text-muted">Tek cümlede değer önerin. Örnek: `Kurumsal İngilizce ve konuşma odaklı eğitim programı sunuyoruz.`</small>
    </div>
</div>

<div class="row">
    <div class="col-md-12 form-group">
        <label>Yapay Zeka Ek Talimatı</label>
        <textarea name="prompt_preamble" class="form-control" rows="4">{{ old('prompt_preamble', $outreachCampaign?->prompt_preamble) }}</textarea>
        <small class="text-muted">Ek kural yazmak istersen kullan. Örnek: `Mesaj kısa olsun, ilk cümlede satış baskısı olmasın.`</small>
    </div>
</div>

<div class="row">
    <div class="col-md-6 form-group">
        <label>İmza Metni</label>
        <textarea name="signature_text" class="form-control" rows="5">{{ old('signature_text', $outreachCampaign?->signature_text) }}</textarea>
        <small class="text-muted">Düz metin mail sonu imzası.</small>
    </div>
    <div class="col-md-6 form-group">
        <label>İmza HTML</label>
        <textarea name="signature_html" class="form-control" rows="5">{{ old('signature_html', $outreachCampaign?->signature_html) }}</textarea>
        <small class="text-muted">Özel tasarımlı imza kullanacaksan doldur. Boşsa üstteki düz metin kullanılır.</small>
    </div>
</div>

<div class="row">
    <div class="col-md-3 form-group">
        <label>Günlük Gönderim Limiti</label>
        <input type="number" min="1" name="daily_send_limit" class="form-control" value="{{ old('daily_send_limit', $outreachCampaign?->daily_send_limit ?? 40) }}" required>
        <small class="text-muted">Bir gün içinde en fazla kaç mail gitsin?</small>
    </div>
    <div class="col-md-3 form-group">
        <label>Saatlik Gönderim Limiti</label>
        <input type="number" min="1" name="hourly_send_limit" class="form-control" value="{{ old('hourly_send_limit', $outreachCampaign?->hourly_send_limit ?? 10) }}" required>
        <small class="text-muted">Bir saat içinde en fazla kaç mail gitsin?</small>
    </div>
    <div class="col-md-3 form-group">
        <label>Mailler Arası Bekleme</label>
        <input type="number" min="0" name="min_delay_seconds" class="form-control" value="{{ old('min_delay_seconds', $outreachCampaign?->min_delay_seconds ?? 180) }}" required>
        <small class="text-muted">Saniye cinsinden. `180` = 3 dakika bekleme.</small>
    </div>
    <div class="col-md-3 form-group d-flex align-items-center">
        <div class="custom-control custom-checkbox mt-4">
            <input type="hidden" name="require_approval" value="0">
            <input type="checkbox" class="custom-control-input" id="require_approval" name="require_approval" value="1" @checked(old('require_approval', $outreachCampaign?->require_approval ?? true))>
            <label class="custom-control-label" for="require_approval">Göndermeden Önce Onay İste</label>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-3 form-group">
        <label>Gönderim Başlangıç Saati</label>
        <input type="number" min="0" max="23" name="send_start_hour" class="form-control" value="{{ old('send_start_hour', $outreachCampaign?->send_start_hour ?? 9) }}" required>
        <small class="text-muted">İstanbul saati. `9` = 09:00</small>
    </div>
    <div class="col-md-3 form-group">
        <label>Gönderim Bitiş Saati</label>
        <input type="number" min="0" max="23" name="send_end_hour" class="form-control" value="{{ old('send_end_hour', $outreachCampaign?->send_end_hour ?? 18) }}" required>
        <small class="text-muted">İstanbul saati. `18` = 18:00</small>
    </div>
    <div class="col-md-6 form-group">
        <label>Notlar</label>
        <textarea name="notes" class="form-control" rows="3">{{ old('notes', $outreachCampaign?->notes) }}</textarea>
        <small class="text-muted">Sadece kendi ekibin için dahili not.</small>
    </div>
</div>
