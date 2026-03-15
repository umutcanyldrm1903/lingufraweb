@extends('admin.master_layout')
@section('title')
    <title>{{ __('Outreach Bot') }}</title>
@endsection

@section('admin-content')
    @php
        $tableMissing = $tableMissing ?? false;
    @endphp
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>{{ __('Outreach Bot') }}</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a></div>
                    <div class="breadcrumb-item">{{ __('Outreach Bot') }}</div>
                </div>
            </div>

            <div class="section-body">
                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('admin.outreach-campaigns.index') }}" method="GET" class="row">
                            <div class="col-md-5 form-group">
                                <input type="text" class="form-control" name="keyword" value="{{ request('keyword') }}" placeholder="{{ __('Search campaign, company, product') }}">
                            </div>
                            <div class="col-md-3 form-group">
                                <select name="status" class="form-control">
                                    <option value="">{{ __('All Statuses') }}</option>
                                    @foreach (['draft', 'imported', 'enriched', 'generated', 'approved', 'sent'] as $status)
                                        <option value="{{ $status }}" @selected(request('status') === $status)>{{ ucfirst($status) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4 form-group d-flex">
                                <button type="submit" class="btn btn-primary mr-2">{{ __('Search') }}</button>
                                <a href="{{ route('admin.outreach-campaigns.index') }}" class="btn btn-light border mr-2">{{ __('Clear') }}</a>
                                @if (!$tableMissing)
                                    <a href="{{ route('admin.outreach-campaigns.create') }}" class="btn btn-success">{{ __('Create Campaign') }}</a>
                                @endif
                            </div>
                        </form>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h4>{{ __('Campaigns') }}</h4>
                    </div>
                    <div class="card-body">
                        @if ($tableMissing)
                            <div class="alert alert-warning mb-0">
                                {{ __('Outreach tables are missing. Run migrations first.') }}
                            </div>
                        @else
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>{{ __('Campaign') }}</th>
                                            <th>{{ __('Status') }}</th>
                                            <th>{{ __('Leads') }}</th>
                                            <th>{{ __('Messages') }}</th>
                                            <th>{{ __('Sent') }}</th>
                                            <th>{{ __('Replies') }}</th>
                                            <th>{{ __('Updated') }}</th>
                                            <th>{{ __('Action') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($campaigns as $campaign)
                                            <tr>
                                                <td>
                                                    <div class="font-weight-bold">{{ $campaign->name }}</div>
                                                    <div class="text-muted">{{ $campaign->company_name ?: '-' }}</div>
                                                    @if ($campaign->product_name)
                                                        <div><small>{{ $campaign->product_name }}</small></div>
                                                    @endif
                                                </td>
                                                <td><span class="badge badge-info text-uppercase">{{ $campaign->status }}</span></td>
                                                <td>{{ $campaign->leads_count }}</td>
                                                <td>{{ $campaign->messages_count }}</td>
                                                <td>{{ $campaign->sent_messages_count }}</td>
                                                <td>{{ $campaign->replied_messages_count }}</td>
                                                <td>{{ $campaign->updated_at ? formatDate($campaign->updated_at) : '-' }}</td>
                                                <td class="text-nowrap">
                                                    <a href="{{ route('admin.outreach-campaigns.show', $campaign) }}" class="btn btn-sm btn-primary">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('admin.outreach-campaigns.edit', $campaign) }}" class="btn btn-sm btn-warning">
                                                        <i class="far fa-edit"></i>
                                                    </a>
                                                    <form action="{{ route('admin.outreach-campaigns.destroy', $campaign) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('Delete this campaign and all related leads/messages?') }}')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger">
                                                            <i class="fa fa-trash"></i>
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="8" class="text-center text-muted">{{ __('No Data!') }}</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            <div class="mt-3">
                                {{ $campaigns->links() }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
