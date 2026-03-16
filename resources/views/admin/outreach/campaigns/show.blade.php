@extends('admin.master_layout')
@section('title')
    <title>{{ $outreachCampaign->name }} - Outreach Bot</title>
@endsection

@section('admin-content')
    @php
        $campaignStatusLabels = [
            'draft' => 'Taslak',
            'imported' => 'Lead Yüklendi',
            'enriched' => 'Zenginleştirildi',
            'generated' => 'Mesaj Üretildi',
            'approved' => 'Onaylandı',
            'sent' => 'Gönderildi',
        ];
        $leadStatusLabels = [
            'imported' => 'Yüklendi',
            'enriched' => 'Zenginleştirildi',
            'ready' => 'Hazır',
            'sent' => 'Gönderildi',
            'replied' => 'Yanıtlandı',
            'suppressed' => 'Durduruldu',
            'invalid' => 'Geçersiz',
            'enrich_failed' => 'Enrich Hatası',
        ];
        $messageStatusLabels = [
            'draft' => 'Taslak',
            'generated' => 'Üretildi',
            'approved' => 'Onaylandı',
            'sending' => 'Gönderiliyor',
            'sent' => 'Gönderildi',
            'failed' => 'Hatalı',
            'replied' => 'Yanıtlandı',
            'suppressed' => 'Durduruldu',
        ];
    @endphp
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>{{ $outreachCampaign->name }}</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a></div>
                    <div class="breadcrumb-item active"><a href="{{ route('admin.outreach-campaigns.index') }}">{{ __('Outreach Bot') }}</a></div>
                    <div class="breadcrumb-item">{{ $outreachCampaign->name }}</div>
                </div>
            </div>

            <div class="section-body">
                <div class="row">
                    <div class="col-md-3">
                        <div class="card card-statistic-1">
                            <div class="card-icon bg-primary"><i class="fas fa-users"></i></div>
                            <div class="card-wrap">
                                <div class="card-header"><h4>Lead</h4></div>
                                <div class="card-body">{{ $outreachCampaign->leads_count }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card card-statistic-1">
                            <div class="card-icon bg-info"><i class="fas fa-file-alt"></i></div>
                            <div class="card-wrap">
                                <div class="card-header"><h4>Üretilen Taslak</h4></div>
                                <div class="card-body">{{ $outreachCampaign->generated_messages_count }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card card-statistic-1">
                            <div class="card-icon bg-success"><i class="fas fa-paper-plane"></i></div>
                            <div class="card-wrap">
                                <div class="card-header"><h4>Gönderilen</h4></div>
                                <div class="card-body">{{ $outreachCampaign->sent_messages_count }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card card-statistic-1">
                            <div class="card-icon bg-warning"><i class="fas fa-reply"></i></div>
                            <div class="card-wrap">
                                <div class="card-header"><h4>Yanıt</h4></div>
                                <div class="card-body">{{ $outreachCampaign->replied_messages_count }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-5">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between">
                                <h4>Kampanya Bilgileri</h4>
                                <a href="{{ route('admin.outreach-campaigns.edit', $outreachCampaign) }}" class="btn btn-sm btn-warning">Düzenle</a>
                            </div>
                            <div class="card-body">
                                <div class="mb-2"><strong>Durum:</strong> <span class="badge badge-info">{{ $campaignStatusLabels[$outreachCampaign->status] ?? $outreachCampaign->status }}</span></div>
                                <div class="mb-2"><strong>Şirket:</strong> {{ $outreachCampaign->company_name ?: '-' }}</div>
                                <div class="mb-2"><strong>Website:</strong> {{ $outreachCampaign->company_website ?: '-' }}</div>
                                <div class="mb-2"><strong>Ürün / Hizmet:</strong> {{ $outreachCampaign->product_name ?: '-' }}</div>
                                <div class="mb-2"><strong>Mesaj Dili:</strong> {{ strtoupper($outreachCampaign->language) }}</div>
                                <div class="mb-2"><strong>Saat Dilimi:</strong> {{ $outreachCampaign->timezone }} <span class="text-muted">(İstanbul saati)</span></div>
                                <div class="mb-2"><strong>Gönderim Limiti:</strong> Günlük {{ $outreachCampaign->daily_send_limit }}, saatlik {{ $outreachCampaign->hourly_send_limit }}</div>
                                <div class="mb-2"><strong>Gönderim Aralığı:</strong> {{ str_pad((string) $outreachCampaign->send_start_hour, 2, '0', STR_PAD_LEFT) }}:00 - {{ str_pad((string) $outreachCampaign->send_end_hour, 2, '0', STR_PAD_LEFT) }}:00 <span class="text-muted">(İstanbul)</span></div>
                                <div class="mb-2"><strong>Onay:</strong> {{ $outreachCampaign->require_approval ? 'Göndermeden önce onay gerekli' : 'Onaysız gönderime hazır' }}</div>
                                <hr>
                                <div class="mb-3">
                                    <strong>Hedef Kitle Özeti</strong>
                                    <div class="text-muted">{{ $outreachCampaign->audience_summary ?: '-' }}</div>
                                </div>
                                <div class="mb-3">
                                    <strong>Teklif Özeti</strong>
                                    <div class="text-muted">{{ $outreachCampaign->offer_summary ?: '-' }}</div>
                                </div>
                                <div class="mb-3">
                                    <strong>Yapay Zeka Ek Talimatı</strong>
                                    <div class="text-muted">{{ $outreachCampaign->prompt_preamble ?: '-' }}</div>
                                </div>
                                <div>
                                    <strong>Notlar</strong>
                                    <div class="text-muted">{{ $outreachCampaign->notes ?: '-' }}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-7">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between">
                                <h4>Hızlı İşlemler</h4>
                                <a href="{{ route('admin.crediential-setting') }}" class="btn btn-sm btn-light border">Sağlayıcı Ayarları</a>
                            </div>
                            <div class="card-body">
                                <div class="alert alert-light border">
                                    <div class="small text-muted">
                                        SMTP normal mail ayarlarından gelir. OpenAI, Lusha ve IMAP bilgilerini "Sağlayıcı Ayarları" ekranından yönetebilirsin.
                                    </div>
                                    <div class="small text-muted mt-1">
                                        Gönderim saatleri ve limitleri bu kampanyada İstanbul saatine göre uygulanır.
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <form action="{{ route('admin.outreach-campaigns.import-lusha', $outreachCampaign) }}" method="POST">
                                            @csrf
                                            <div class="form-group">
                                                <label>Lusha Arama JSON Verisi</label>
                                                <textarea name="payload_json" class="form-control" rows="8" placeholder='{"filters": {...}}'>{{ old('payload_json', $outreachCampaign->last_lusha_payload ? json_encode($outreachCampaign->last_lusha_payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : '') }}</textarea>
                                                <small class="text-muted">Lusha `contact/search` isteğinde kullandığın JSON gövdesini buraya yapıştır.</small>
                                            </div>
                                            <button type="submit" class="btn btn-primary">Lusha'dan Lead Çek</button>
                                        </form>
                                    </div>
                                </div>

                                <hr>

                                <div class="row">
                                    <div class="col-md-6">
                                        <form action="{{ route('admin.outreach-campaigns.enrich-lusha', $outreachCampaign) }}" method="POST" class="mb-3">
                                            @csrf
                                            <div class="form-group">
                                                <label>Eksik E-postaları Tamamla</label>
                                                <input type="number" min="1" max="100" name="limit" class="form-control" value="25">
                                                <small class="text-muted">Email olmayan lead'ler için Lusha enrich çalıştırır.</small>
                                            </div>
                                            <button type="submit" class="btn btn-info">Enrich Çalıştır</button>
                                        </form>
                                    </div>
                                    <div class="col-md-6">
                                        <form action="{{ route('admin.outreach-campaigns.generate', $outreachCampaign) }}" method="POST" class="mb-3">
                                            @csrf
                                            <div class="form-group">
                                                <label>Yapay Zeka ile Taslak Üret</label>
                                                <input type="number" min="1" max="100" name="limit" class="form-control" value="20">
                                                <small class="text-muted">Email adresi olan lead'ler için mesaj üretir.</small>
                                            </div>
                                            <div class="custom-control custom-checkbox mb-2">
                                                <input type="checkbox" class="custom-control-input" id="refresh_drafts" name="refresh" value="1">
                                                <label class="custom-control-label" for="refresh_drafts">Gönderilmemiş mevcut taslakları yeniden üret</label>
                                            </div>
                                            <button type="submit" class="btn btn-success">Yapay Zeka ile Üret</button>
                                        </form>
                                    </div>
                                    <div class="col-md-6">
                                        <form action="{{ route('admin.outreach-campaigns.approve', $outreachCampaign) }}" method="POST" class="mb-3">
                                            @csrf
                                            <div class="form-group">
                                                <label>Taslakları Onayla</label>
                                                <input type="number" min="1" max="200" name="limit" class="form-control" value="50">
                                                <small class="text-muted">Gönderim öncesi kaç mesajın onaylanacağını belirler.</small>
                                            </div>
                                            <button type="submit" class="btn btn-warning">Üretilenleri Onayla</button>
                                        </form>
                                    </div>
                                    <div class="col-md-6">
                                        <form action="{{ route('admin.outreach-campaigns.send', $outreachCampaign) }}" method="POST" class="mb-3">
                                            @csrf
                                            <div class="form-group">
                                                <label>Onaylı Mesajları Gönder</label>
                                                <input type="number" min="1" max="100" name="limit" class="form-control" value="10">
                                                <small class="text-muted">Bu işlem doğrudan kurumsal mailbox üzerinden gerçek mail gönderir.</small>
                                            </div>
                                            <div class="custom-control custom-checkbox mb-2">
                                                <input type="checkbox" class="custom-control-input" id="force_send" name="force" value="1">
                                                <label class="custom-control-label" for="force_send">Onaylanmamış ama üretilmiş taslakları da zorla gönder</label>
                                            </div>
                                            <button type="submit" class="btn btn-danger">E-postaları Gönder</button>
                                        </form>
                                    </div>
                                    <div class="col-md-12">
                                        <form action="{{ route('admin.outreach-campaigns.sync-replies', $outreachCampaign) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn btn-secondary">Gelen Yanıtları IMAP ile Çek</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h4>Lead Listesi</h4>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.outreach-campaigns.show', $outreachCampaign) }}" method="GET" class="row">
                            <div class="col-md-5 form-group">
                                <input type="text" name="lead_keyword" class="form-control" value="{{ request('lead_keyword') }}" placeholder="İsim, e-posta, şirket veya unvan ara">
                            </div>
                            <div class="col-md-3 form-group">
                                <select name="lead_status" class="form-control">
                                    <option value="">Tüm Lead Durumları</option>
                                    @foreach (['imported', 'enriched', 'ready', 'sent', 'replied', 'suppressed', 'invalid', 'enrich_failed'] as $status)
                                        <option value="{{ $status }}" @selected(request('lead_status') === $status)>{{ $leadStatusLabels[$status] }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4 form-group d-flex">
                                <button type="submit" class="btn btn-primary mr-2">Leadleri Filtrele</button>
                                <a href="{{ route('admin.outreach-campaigns.show', $outreachCampaign) }}" class="btn btn-light border">Sıfırla</a>
                            </div>
                        </form>

                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Lead</th>
                                        <th>Şirket</th>
                                        <th>Durum</th>
                                        <th>Güncelleme</th>
                                        <th>İşlem</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($leads as $index => $lead)
                                        <tr>
                                            <td>{{ $leads->firstItem() + $index }}</td>
                                            <td>
                                                <div class="font-weight-bold">{{ $lead->full_name ?: '-' }}</div>
                                                <div class="text-muted">{{ $lead->email ?: '-' }}</div>
                                                <div><small>{{ $lead->job_title ?: '-' }}</small></div>
                                            </td>
                                            <td>
                                                <div>{{ $lead->company_name ?: '-' }}</div>
                                                <div><small>{{ $lead->location ?: '-' }}</small></div>
                                            </td>
                                            <td><span class="badge badge-light">{{ $leadStatusLabels[$lead->status] ?? $lead->status }}</span></td>
                                            <td>{{ $lead->updated_at ? formatDate($lead->updated_at) : '-' }}</td>
                                            <td class="text-nowrap">
                                                <form action="{{ route('admin.outreach-campaigns.generate', $outreachCampaign) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <input type="hidden" name="lead_id" value="{{ $lead->id }}">
                                                    <button type="submit" class="btn btn-sm btn-success">Taslak Üret</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center text-muted">Henüz lead yok.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-3">
                            {{ $leads->links() }}
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h4>Mesajlar</h4>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.outreach-campaigns.show', $outreachCampaign) }}" method="GET" class="row">
                            <div class="col-md-4 form-group">
                                <input type="text" name="lead_keyword" class="form-control" value="{{ request('lead_keyword') }}" placeholder="Lead filtresi gerekiyorsa burada tut">
                            </div>
                            <div class="col-md-4 form-group">
                                <select name="message_status" class="form-control">
                                    <option value="">Tüm Mesaj Durumları</option>
                                    @foreach (['draft', 'generated', 'approved', 'sending', 'sent', 'failed', 'replied', 'suppressed'] as $status)
                                        <option value="{{ $status }}" @selected(request('message_status') === $status)>{{ $messageStatusLabels[$status] }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4 form-group d-flex">
                                <button type="submit" class="btn btn-primary mr-2">Mesajları Filtrele</button>
                                <a href="{{ route('admin.outreach-campaigns.show', $outreachCampaign) }}" class="btn btn-light border">Sıfırla</a>
                            </div>
                        </form>

                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Lead</th>
                                        <th>Konu</th>
                                        <th>Durum</th>
                                        <th>Riskler</th>
                                        <th>Gönderim</th>
                                        <th>İşlem</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($messages as $index => $message)
                                        <tr>
                                            <td>{{ $messages->firstItem() + $index }}</td>
                                            <td>
                                                <div class="font-weight-bold">{{ $message->lead?->full_name ?: '-' }}</div>
                                                <div class="text-muted">{{ $message->lead?->email ?: '-' }}</div>
                                            </td>
                                            <td>
                                                <div class="font-weight-bold">{{ truncate($message->subject, 70) }}</div>
                                                <div><small>{{ truncate($message->body_text, 90) }}</small></div>
                                            </td>
                                            <td>
                                                <span class="badge badge-{{ $message->status === 'sent' ? 'success' : ($message->status === 'failed' ? 'danger' : 'light') }}">
                                                    {{ $messageStatusLabels[$message->status] ?? $message->status }}
                                                </span>
                                            </td>
                                            <td>
                                                @forelse (($message->risk_flags ?? []) as $flag)
                                                    <span class="badge badge-warning mb-1">{{ $flag }}</span>
                                                @empty
                                                    <span class="text-muted">-</span>
                                                @endforelse
                                            </td>
                                            <td>{{ $message->sent_at ? formattedDateTime($message->sent_at) : '-' }}</td>
                                            <td class="text-nowrap">
                                                <a href="{{ route('admin.outreach-messages.edit', $message) }}" class="btn btn-sm btn-warning">Düzenle</a>
                                                @if (in_array($message->status, ['generated', 'draft']))
                                                    <form action="{{ route('admin.outreach-messages.approve', $message) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-info">Onayla</button>
                                                    </form>
                                                @endif
                                                @if (in_array($message->status, ['approved', 'generated']))
                                                    <form action="{{ route('admin.outreach-messages.send', $message) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-success">Gönder</button>
                                                    </form>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center text-muted">Henüz mesaj yok.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-3">
                            {{ $messages->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
