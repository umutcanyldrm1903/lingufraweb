@extends('admin.master_layout')
@section('title')
    <title>{{ __('Edit Outreach Message') }}</title>
@endsection

@section('admin-content')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>{{ __('Edit Outreach Message') }}</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a></div>
                    <div class="breadcrumb-item active"><a href="{{ route('admin.outreach-campaigns.index') }}">{{ __('Outreach Bot') }}</a></div>
                    <div class="breadcrumb-item active"><a href="{{ route('admin.outreach-campaigns.show', $outreachMessage->campaign) }}">{{ $outreachMessage->campaign->name }}</a></div>
                    <div class="breadcrumb-item">{{ __('Message') }}</div>
                </div>
            </div>

            <div class="section-body">
                <div class="row">
                    <div class="col-lg-8">
                        <div class="card">
                            <div class="card-header">
                                <h4>{{ __('Message Editor') }}</h4>
                            </div>
                            <div class="card-body">
                                <form action="{{ route('admin.outreach-messages.update', $outreachMessage) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="form-group">
                                        <label>{{ __('Subject') }}</label>
                                        <input type="text" name="subject" class="form-control" value="{{ old('subject', $outreachMessage->subject) }}" required>
                                    </div>
                                    <div class="form-group">
                                        <label>{{ __('Body Text') }}</label>
                                        <textarea name="body_text" class="form-control" rows="12" required>{{ old('body_text', $outreachMessage->body_text) }}</textarea>
                                    </div>
                                    <div class="form-group">
                                        <label>{{ __('Body HTML') }}</label>
                                        <textarea name="body_html" class="form-control" rows="12">{{ old('body_html', $outreachMessage->body_html) }}</textarea>
                                        <small class="text-muted">{{ __('Leave blank to regenerate simple HTML from Body Text.') }}</small>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4 form-group">
                                            <label>{{ __('Status') }}</label>
                                            <select name="status" class="form-control">
                                                @foreach (['draft', 'generated', 'approved'] as $status)
                                                    <option value="{{ $status }}" @selected(old('status', $outreachMessage->status) === $status)>{{ ucfirst($status) }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-8 d-flex align-items-center">
                                            <div class="custom-control custom-checkbox mt-4">
                                                <input type="checkbox" class="custom-control-input" id="approve_after_save" name="approve_after_save" value="1">
                                                <label class="custom-control-label" for="approve_after_save">{{ __('Approve after save') }}</label>
                                            </div>
                                        </div>
                                    </div>
                                    <button type="submit" class="btn btn-primary">{{ __('Save Message') }}</button>
                                    <a href="{{ route('admin.outreach-campaigns.show', $outreachMessage->campaign) }}" class="btn btn-light border">{{ __('Back') }}</a>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="card">
                            <div class="card-header">
                                <h4>{{ __('Lead Details') }}</h4>
                            </div>
                            <div class="card-body">
                                <div class="mb-2"><strong>{{ __('Name') }}:</strong> {{ $outreachMessage->lead?->full_name ?: '-' }}</div>
                                <div class="mb-2"><strong>{{ __('Email') }}:</strong> {{ $outreachMessage->lead?->email ?: '-' }}</div>
                                <div class="mb-2"><strong>{{ __('Company') }}:</strong> {{ $outreachMessage->lead?->company_name ?: '-' }}</div>
                                <div class="mb-2"><strong>{{ __('Title') }}:</strong> {{ $outreachMessage->lead?->job_title ?: '-' }}</div>
                                <div class="mb-2"><strong>{{ __('Message Status') }}:</strong> {{ $outreachMessage->status }}</div>
                                <div class="mb-2"><strong>{{ __('Model') }}:</strong> {{ $outreachMessage->ai_model ?: '-' }}</div>
                                <div class="mb-2"><strong>{{ __('Prompt Version') }}:</strong> {{ $outreachMessage->prompt_version ?: '-' }}</div>
                                <div class="mb-2">
                                    <strong>{{ __('Risk Flags') }}:</strong><br>
                                    @forelse (($outreachMessage->risk_flags ?? []) as $flag)
                                        <span class="badge badge-warning mb-1">{{ $flag }}</span>
                                    @empty
                                        <span class="text-muted">-</span>
                                    @endforelse
                                </div>
                            </div>
                        </div>

                        <div class="card">
                            <div class="card-header">
                                <h4>{{ __('Preview') }}</h4>
                            </div>
                            <div class="card-body">
                                <div class="border rounded p-3 bg-light">
                                    {!! $outreachMessage->body_html !!}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
