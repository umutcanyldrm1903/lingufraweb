@extends('admin.master_layout')

@section('title')
    <title>{{ __('Mobile Push Campaigns') }}</title>
@endsection

@section('admin-content')
    <section class="section">
        <div class="section-header">
            <h1>{{ __('Mobile Push Campaigns') }}</h1>
        </div>
        <div class="section-body">
            <div class="card">
                <div class="card-header"><h4>{{ __('Create Campaign') }}</h4></div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.growth.push-campaigns.store') }}">
                        @csrf
                        <div class="row">
                            <div class="col-md-4 mb-2"><input class="form-control" name="name" placeholder="Name" required></div>
                            <div class="col-md-2 mb-2"><input class="form-control" name="segment" value="all" placeholder="Segment"></div>
                            <div class="col-md-3 mb-2"><input class="form-control" name="deep_link" value="/start-speaking" placeholder="Deep link"></div>
                            <div class="col-md-3 mb-2"><input class="form-control" type="datetime-local" name="scheduled_at"></div>
                            <div class="col-md-6 mb-2"><input class="form-control" name="title_tr" placeholder="Title TR" required></div>
                            <div class="col-md-6 mb-2"><input class="form-control" name="title_en" placeholder="Title EN" required></div>
                            <div class="col-md-6 mb-2"><input class="form-control" name="body_tr" placeholder="Body TR" required></div>
                            <div class="col-md-6 mb-2"><input class="form-control" name="body_en" placeholder="Body EN" required></div>
                            <div class="col-md-2 mb-2">
                                <label><input type="checkbox" name="is_active" value="1" checked> {{ __('Active') }}</label>
                            </div>
                            <div class="col-md-2 mb-2">
                                <button class="btn btn-primary">{{ __('Save') }}</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-header"><h4>{{ __('Campaigns') }}</h4></div>
                <div class="card-body table-responsive">
                    <table class="table table-striped table-sm">
                        <thead>
                        <tr>
                            <th>{{ __('Name') }}</th>
                            <th>{{ __('Segment') }}</th>
                            <th>{{ __('Deep Link') }}</th>
                            <th>{{ __('Scheduled') }}</th>
                            <th>{{ __('Active') }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($campaigns as $campaign)
                            <tr>
                                <td>{{ $campaign->name }}</td>
                                <td>{{ $campaign->segment }}</td>
                                <td>{{ $campaign->deep_link }}</td>
                                <td>{{ optional($campaign->scheduled_at)->format('Y-m-d H:i') }}</td>
                                <td>{{ $campaign->is_active ? __('Yes') : __('No') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-muted text-center">{{ __('No campaigns yet.') }}</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                    {{ $campaigns->links() }}
                </div>
            </div>
        </div>
    </section>
@endsection
