@php
    /** @var \App\Models\OutreachCampaign|null $outreachCampaign */
    $outreachCampaign = $outreachCampaign ?? null;
@endphp

<div class="row">
    <div class="col-md-6 form-group">
        <label>{{ __('Campaign Name') }}</label>
        <input type="text" name="name" class="form-control" value="{{ old('name', $outreachCampaign?->name) }}" required>
    </div>
    <div class="col-md-3 form-group">
        <label>{{ __('Language') }}</label>
        <select name="language" class="form-control">
            <option value="tr" @selected(old('language', $outreachCampaign?->language ?? 'tr') === 'tr')>TR</option>
            <option value="en" @selected(old('language', $outreachCampaign?->language) === 'en')>EN</option>
        </select>
    </div>
    <div class="col-md-3 form-group">
        <label>{{ __('Status') }}</label>
        <select name="status" class="form-control">
            @foreach (['draft', 'imported', 'enriched', 'generated', 'approved', 'sent'] as $status)
                <option value="{{ $status }}" @selected(old('status', $outreachCampaign?->status ?? 'draft') === $status)>{{ ucfirst($status) }}</option>
            @endforeach
        </select>
    </div>
</div>

<div class="row">
    <div class="col-md-4 form-group">
        <label>{{ __('Company Name') }}</label>
        <input type="text" name="company_name" class="form-control" value="{{ old('company_name', $outreachCampaign?->company_name) }}">
    </div>
    <div class="col-md-4 form-group">
        <label>{{ __('Company Website') }}</label>
        <input type="text" name="company_website" class="form-control" value="{{ old('company_website', $outreachCampaign?->company_website) }}">
    </div>
    <div class="col-md-4 form-group">
        <label>{{ __('Product Name') }}</label>
        <input type="text" name="product_name" class="form-control" value="{{ old('product_name', $outreachCampaign?->product_name) }}">
    </div>
</div>

<div class="row">
    <div class="col-md-4 form-group">
        <label>{{ __('Tone') }}</label>
        <input type="text" name="tone" class="form-control" value="{{ old('tone', $outreachCampaign?->tone ?? 'consultative') }}">
    </div>
    <div class="col-md-4 form-group">
        <label>{{ __('Timezone') }}</label>
        <input type="text" name="timezone" class="form-control" value="{{ old('timezone', $outreachCampaign?->timezone ?? 'Europe/Istanbul') }}" required>
    </div>
    <div class="col-md-4 form-group">
        <label>{{ __('Unsubscribe Mail') }}</label>
        <input type="email" name="unsubscribe_mailto" class="form-control" value="{{ old('unsubscribe_mailto', $outreachCampaign?->unsubscribe_mailto) }}">
    </div>
</div>

<div class="row">
    <div class="col-md-6 form-group">
        <label>{{ __('Audience Summary') }}</label>
        <textarea name="audience_summary" class="form-control" rows="4">{{ old('audience_summary', $outreachCampaign?->audience_summary) }}</textarea>
    </div>
    <div class="col-md-6 form-group">
        <label>{{ __('Offer Summary') }}</label>
        <textarea name="offer_summary" class="form-control" rows="4">{{ old('offer_summary', $outreachCampaign?->offer_summary) }}</textarea>
    </div>
</div>

<div class="row">
    <div class="col-md-12 form-group">
        <label>{{ __('GPT Prompt Preamble') }}</label>
        <textarea name="prompt_preamble" class="form-control" rows="4">{{ old('prompt_preamble', $outreachCampaign?->prompt_preamble) }}</textarea>
    </div>
</div>

<div class="row">
    <div class="col-md-6 form-group">
        <label>{{ __('Signature Text') }}</label>
        <textarea name="signature_text" class="form-control" rows="5">{{ old('signature_text', $outreachCampaign?->signature_text) }}</textarea>
    </div>
    <div class="col-md-6 form-group">
        <label>{{ __('Signature HTML') }}</label>
        <textarea name="signature_html" class="form-control" rows="5">{{ old('signature_html', $outreachCampaign?->signature_html) }}</textarea>
    </div>
</div>

<div class="row">
    <div class="col-md-3 form-group">
        <label>{{ __('Daily Limit') }}</label>
        <input type="number" min="1" name="daily_send_limit" class="form-control" value="{{ old('daily_send_limit', $outreachCampaign?->daily_send_limit ?? 40) }}" required>
    </div>
    <div class="col-md-3 form-group">
        <label>{{ __('Hourly Limit') }}</label>
        <input type="number" min="1" name="hourly_send_limit" class="form-control" value="{{ old('hourly_send_limit', $outreachCampaign?->hourly_send_limit ?? 10) }}" required>
    </div>
    <div class="col-md-3 form-group">
        <label>{{ __('Min Delay Seconds') }}</label>
        <input type="number" min="0" name="min_delay_seconds" class="form-control" value="{{ old('min_delay_seconds', $outreachCampaign?->min_delay_seconds ?? 180) }}" required>
    </div>
    <div class="col-md-3 form-group d-flex align-items-center">
        <div class="custom-control custom-checkbox mt-4">
            <input type="hidden" name="require_approval" value="0">
            <input type="checkbox" class="custom-control-input" id="require_approval" name="require_approval" value="1" @checked(old('require_approval', $outreachCampaign?->require_approval ?? true))>
            <label class="custom-control-label" for="require_approval">{{ __('Require Approval') }}</label>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-3 form-group">
        <label>{{ __('Send Start Hour') }}</label>
        <input type="number" min="0" max="23" name="send_start_hour" class="form-control" value="{{ old('send_start_hour', $outreachCampaign?->send_start_hour ?? 9) }}" required>
    </div>
    <div class="col-md-3 form-group">
        <label>{{ __('Send End Hour') }}</label>
        <input type="number" min="0" max="23" name="send_end_hour" class="form-control" value="{{ old('send_end_hour', $outreachCampaign?->send_end_hour ?? 18) }}" required>
    </div>
    <div class="col-md-6 form-group">
        <label>{{ __('Notes') }}</label>
        <textarea name="notes" class="form-control" rows="3">{{ old('notes', $outreachCampaign?->notes) }}</textarea>
    </div>
</div>
