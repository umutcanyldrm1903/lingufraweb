@extends('admin.master_layout')
@section('title')
    <title>{{ $outreachCampaign->name }} - {{ __('Outreach Bot') }}</title>
@endsection

@section('admin-content')
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
                                <div class="card-header"><h4>{{ __('Leads') }}</h4></div>
                                <div class="card-body">{{ $outreachCampaign->leads_count }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card card-statistic-1">
                            <div class="card-icon bg-info"><i class="fas fa-file-alt"></i></div>
                            <div class="card-wrap">
                                <div class="card-header"><h4>{{ __('Generated') }}</h4></div>
                                <div class="card-body">{{ $outreachCampaign->generated_messages_count }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card card-statistic-1">
                            <div class="card-icon bg-success"><i class="fas fa-paper-plane"></i></div>
                            <div class="card-wrap">
                                <div class="card-header"><h4>{{ __('Sent') }}</h4></div>
                                <div class="card-body">{{ $outreachCampaign->sent_messages_count }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card card-statistic-1">
                            <div class="card-icon bg-warning"><i class="fas fa-reply"></i></div>
                            <div class="card-wrap">
                                <div class="card-header"><h4>{{ __('Replies') }}</h4></div>
                                <div class="card-body">{{ $outreachCampaign->replied_messages_count }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-5">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between">
                                <h4>{{ __('Campaign Details') }}</h4>
                                <a href="{{ route('admin.outreach-campaigns.edit', $outreachCampaign) }}" class="btn btn-sm btn-warning">{{ __('Edit') }}</a>
                            </div>
                            <div class="card-body">
                                <div class="mb-2"><strong>{{ __('Status') }}:</strong> <span class="badge badge-info text-uppercase">{{ $outreachCampaign->status }}</span></div>
                                <div class="mb-2"><strong>{{ __('Company') }}:</strong> {{ $outreachCampaign->company_name ?: '-' }}</div>
                                <div class="mb-2"><strong>{{ __('Website') }}:</strong> {{ $outreachCampaign->company_website ?: '-' }}</div>
                                <div class="mb-2"><strong>{{ __('Product') }}:</strong> {{ $outreachCampaign->product_name ?: '-' }}</div>
                                <div class="mb-2"><strong>{{ __('Language') }}:</strong> {{ strtoupper($outreachCampaign->language) }}</div>
                                <div class="mb-2"><strong>{{ __('Timezone') }}:</strong> {{ $outreachCampaign->timezone }}</div>
                                <div class="mb-2"><strong>{{ __('Limits') }}:</strong> {{ $outreachCampaign->daily_send_limit }}/{{ __('day') }}, {{ $outreachCampaign->hourly_send_limit }}/{{ __('hour') }}</div>
                                <div class="mb-2"><strong>{{ __('Window') }}:</strong> {{ str_pad((string) $outreachCampaign->send_start_hour, 2, '0', STR_PAD_LEFT) }}:00 - {{ str_pad((string) $outreachCampaign->send_end_hour, 2, '0', STR_PAD_LEFT) }}:00</div>
                                <div class="mb-2"><strong>{{ __('Approval') }}:</strong> {{ $outreachCampaign->require_approval ? __('Required') : __('Auto send ready') }}</div>
                                <hr>
                                <div class="mb-3">
                                    <strong>{{ __('Audience Summary') }}</strong>
                                    <div class="text-muted">{{ $outreachCampaign->audience_summary ?: '-' }}</div>
                                </div>
                                <div class="mb-3">
                                    <strong>{{ __('Offer Summary') }}</strong>
                                    <div class="text-muted">{{ $outreachCampaign->offer_summary ?: '-' }}</div>
                                </div>
                                <div class="mb-3">
                                    <strong>{{ __('Prompt Preamble') }}</strong>
                                    <div class="text-muted">{{ $outreachCampaign->prompt_preamble ?: '-' }}</div>
                                </div>
                                <div>
                                    <strong>{{ __('Notes') }}</strong>
                                    <div class="text-muted">{{ $outreachCampaign->notes ?: '-' }}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-7">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between">
                                <h4>{{ __('Quick Actions') }}</h4>
                                <a href="{{ route('admin.crediential-setting') }}" class="btn btn-sm btn-light border">{{ __('Provider Settings') }}</a>
                            </div>
                            <div class="card-body">
                                <div class="alert alert-light border">
                                    <div class="small text-muted">
                                        {{ __('SMTP mevcut mail ayarlarindan gelir. OpenAI, Lusha ve IMAP bilgilerini Provider Settings ekranindan yonetebilirsin.') }}
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <form action="{{ route('admin.outreach-campaigns.import-lusha', $outreachCampaign) }}" method="POST">
                                            @csrf
                                            <div class="form-group">
                                                <label>{{ __('Lusha Search Payload JSON') }}</label>
                                                <textarea name="payload_json" class="form-control" rows="8" placeholder='{"filters": {...}}'>{{ old('payload_json', $outreachCampaign->last_lusha_payload ? json_encode($outreachCampaign->last_lusha_payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : '') }}</textarea>
                                            </div>
                                            <button type="submit" class="btn btn-primary">{{ __('Import From Lusha') }}</button>
                                        </form>
                                    </div>
                                </div>

                                <hr>

                                <div class="row">
                                    <div class="col-md-6">
                                        <form action="{{ route('admin.outreach-campaigns.enrich-lusha', $outreachCampaign) }}" method="POST" class="mb-3">
                                            @csrf
                                            <div class="form-group">
                                                <label>{{ __('Enrich Missing Emails') }}</label>
                                                <input type="number" min="1" max="100" name="limit" class="form-control" value="25">
                                            </div>
                                            <button type="submit" class="btn btn-info">{{ __('Run Enrich') }}</button>
                                        </form>
                                    </div>
                                    <div class="col-md-6">
                                        <form action="{{ route('admin.outreach-campaigns.generate', $outreachCampaign) }}" method="POST" class="mb-3">
                                            @csrf
                                            <div class="form-group">
                                                <label>{{ __('Generate Drafts') }}</label>
                                                <input type="number" min="1" max="100" name="limit" class="form-control" value="20">
                                            </div>
                                            <div class="custom-control custom-checkbox mb-2">
                                                <input type="checkbox" class="custom-control-input" id="refresh_drafts" name="refresh" value="1">
                                                <label class="custom-control-label" for="refresh_drafts">{{ __('Regenerate existing unsent drafts') }}</label>
                                            </div>
                                            <button type="submit" class="btn btn-success">{{ __('Generate With GPT') }}</button>
                                        </form>
                                    </div>
                                    <div class="col-md-6">
                                        <form action="{{ route('admin.outreach-campaigns.approve', $outreachCampaign) }}" method="POST" class="mb-3">
                                            @csrf
                                            <div class="form-group">
                                                <label>{{ __('Approve Drafts') }}</label>
                                                <input type="number" min="1" max="200" name="limit" class="form-control" value="50">
                                            </div>
                                            <button type="submit" class="btn btn-warning">{{ __('Approve Generated') }}</button>
                                        </form>
                                    </div>
                                    <div class="col-md-6">
                                        <form action="{{ route('admin.outreach-campaigns.send', $outreachCampaign) }}" method="POST" class="mb-3">
                                            @csrf
                                            <div class="form-group">
                                                <label>{{ __('Send Approved Messages') }}</label>
                                                <input type="number" min="1" max="100" name="limit" class="form-control" value="10">
                                            </div>
                                            <div class="custom-control custom-checkbox mb-2">
                                                <input type="checkbox" class="custom-control-input" id="force_send" name="force" value="1">
                                                <label class="custom-control-label" for="force_send">{{ __('Force send generated drafts too') }}</label>
                                            </div>
                                            <button type="submit" class="btn btn-danger">{{ __('Send Emails') }}</button>
                                        </form>
                                    </div>
                                    <div class="col-md-12">
                                        <form action="{{ route('admin.outreach-campaigns.sync-replies', $outreachCampaign) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn btn-secondary">{{ __('Sync IMAP Replies') }}</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h4>{{ __('Leads') }}</h4>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.outreach-campaigns.show', $outreachCampaign) }}" method="GET" class="row">
                            <div class="col-md-5 form-group">
                                <input type="text" name="lead_keyword" class="form-control" value="{{ request('lead_keyword') }}" placeholder="{{ __('Search leads') }}">
                            </div>
                            <div class="col-md-3 form-group">
                                <select name="lead_status" class="form-control">
                                    <option value="">{{ __('All Lead Statuses') }}</option>
                                    @foreach (['imported', 'enriched', 'ready', 'sent', 'replied', 'suppressed', 'invalid', 'enrich_failed'] as $status)
                                        <option value="{{ $status }}" @selected(request('lead_status') === $status)>{{ ucfirst(str_replace('_', ' ', $status)) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4 form-group d-flex">
                                <button type="submit" class="btn btn-primary mr-2">{{ __('Filter Leads') }}</button>
                                <a href="{{ route('admin.outreach-campaigns.show', $outreachCampaign) }}" class="btn btn-light border">{{ __('Reset') }}</a>
                            </div>
                        </form>

                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>{{ __('SN') }}</th>
                                        <th>{{ __('Lead') }}</th>
                                        <th>{{ __('Company') }}</th>
                                        <th>{{ __('Status') }}</th>
                                        <th>{{ __('Updated') }}</th>
                                        <th>{{ __('Action') }}</th>
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
                                            <td><span class="badge badge-light text-uppercase">{{ $lead->status }}</span></td>
                                            <td>{{ $lead->updated_at ? formatDate($lead->updated_at) : '-' }}</td>
                                            <td class="text-nowrap">
                                                <form action="{{ route('admin.outreach-campaigns.generate', $outreachCampaign) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <input type="hidden" name="lead_id" value="{{ $lead->id }}">
                                                    <button type="submit" class="btn btn-sm btn-success">{{ __('Generate') }}</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center text-muted">{{ __('No Data!') }}</td>
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
                        <h4>{{ __('Messages') }}</h4>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.outreach-campaigns.show', $outreachCampaign) }}" method="GET" class="row">
                            <div class="col-md-4 form-group">
                                <input type="text" name="lead_keyword" class="form-control" value="{{ request('lead_keyword') }}" placeholder="{{ __('Keep lead filter if needed') }}">
                            </div>
                            <div class="col-md-4 form-group">
                                <select name="message_status" class="form-control">
                                    <option value="">{{ __('All Message Statuses') }}</option>
                                    @foreach (['draft', 'generated', 'approved', 'sending', 'sent', 'failed', 'replied', 'suppressed'] as $status)
                                        <option value="{{ $status }}" @selected(request('message_status') === $status)>{{ ucfirst($status) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4 form-group d-flex">
                                <button type="submit" class="btn btn-primary mr-2">{{ __('Filter Messages') }}</button>
                                <a href="{{ route('admin.outreach-campaigns.show', $outreachCampaign) }}" class="btn btn-light border">{{ __('Reset') }}</a>
                            </div>
                        </form>

                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>{{ __('SN') }}</th>
                                        <th>{{ __('Lead') }}</th>
                                        <th>{{ __('Subject') }}</th>
                                        <th>{{ __('Status') }}</th>
                                        <th>{{ __('Risks') }}</th>
                                        <th>{{ __('Sent') }}</th>
                                        <th>{{ __('Action') }}</th>
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
                                                <span class="badge badge-{{ $message->status === 'sent' ? 'success' : ($message->status === 'failed' ? 'danger' : 'light') }} text-uppercase">
                                                    {{ $message->status }}
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
                                                <a href="{{ route('admin.outreach-messages.edit', $message) }}" class="btn btn-sm btn-warning">{{ __('Edit') }}</a>
                                                @if (in_array($message->status, ['generated', 'draft']))
                                                    <form action="{{ route('admin.outreach-messages.approve', $message) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-info">{{ __('Approve') }}</button>
                                                    </form>
                                                @endif
                                                @if (in_array($message->status, ['approved', 'generated']))
                                                    <form action="{{ route('admin.outreach-messages.send', $message) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-success">{{ __('Send') }}</button>
                                                    </form>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center text-muted">{{ __('No Data!') }}</td>
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
