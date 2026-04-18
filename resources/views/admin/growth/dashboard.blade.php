@extends('admin.master_layout')

@section('title')
    <title>{{ __('Growth Dashboard') }}</title>
@endsection

@section('admin-content')
    <section class="section">
        <div class="section-header">
            <h1>{{ __('Growth Dashboard') }}</h1>
        </div>
        <div class="section-body">
            <div class="card">
                <div class="card-header">
                    <h4>{{ __('Speaking Coach Funnel (:days days)', ['days' => $days]) }}</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach ([
                            'Opened' => $totals['opened'],
                            'Started' => $totals['started'],
                            'Completed' => $totals['completed'],
                            'Trial Tapped' => $totals['trialTapped'],
                            'Trial Requested' => $totals['trialRequested'],
                            'Booking Started' => $totals['bookingStarted'],
                        ] as $label => $value)
                            <div class="col-md-2 col-6 mb-3">
                                <div class="p-3 border rounded text-center">
                                    <div class="h4 mb-1">{{ $value }}</div>
                                    <small>{{ __($label) }}</small>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header"><h4>{{ __('Segment Split') }}</h4></div>
                        <div class="card-body">
                            @forelse($segmentCounts as $segment => $count)
                                <div class="d-flex justify-content-between mb-2">
                                    <span>{{ $segment }}</span><strong>{{ $count }}</strong>
                                </div>
                            @empty
                                <p class="text-muted mb-0">{{ __('No segment data yet.') }}</p>
                            @endforelse
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header"><h4>{{ __('Experiment Split') }}</h4></div>
                        <div class="card-body">
                            @forelse($experimentCounts as $experiment => $count)
                                <div class="d-flex justify-content-between mb-2">
                                    <span>{{ $experiment }}</span><strong>{{ $count }}</strong>
                                </div>
                            @empty
                                <p class="text-muted mb-0">{{ __('No experiment data yet.') }}</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header"><h4>{{ __('Store Funnel Metrics') }}</h4></div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.growth.store-metric') }}" class="mb-4">
                        @csrf
                        <div class="row">
                            <div class="col-md-2"><input type="date" name="metric_date" class="form-control" required></div>
                            <div class="col-md-2"><input type="number" name="store_page_views" class="form-control" placeholder="Store views" required></div>
                            <div class="col-md-2"><input type="number" name="installs" class="form-control" placeholder="Installs" required></div>
                            <div class="col-md-2"><input type="number" name="trial_starts" class="form-control" placeholder="Trial starts" required></div>
                            <div class="col-md-2"><input type="number" name="trial_conversions" class="form-control" placeholder="Trial conv." required></div>
                            <div class="col-md-1"><input type="text" name="channel" class="form-control" value="organic"></div>
                            <div class="col-md-1"><button type="submit" class="btn btn-primary btn-block">{{ __('Save') }}</button></div>
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table class="table table-sm table-striped">
                            <thead>
                                <tr>
                                    <th>{{ __('Date') }}</th>
                                    <th>{{ __('Channel') }}</th>
                                    <th>{{ __('Store Views') }}</th>
                                    <th>{{ __('Installs') }}</th>
                                    <th>{{ __('Trial Starts') }}</th>
                                    <th>{{ __('Trial Conversions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($storeMetrics as $metric)
                                    <tr>
                                        <td>{{ $metric->metric_date?->format('Y-m-d') }}</td>
                                        <td>{{ $metric->channel }}</td>
                                        <td>{{ $metric->store_page_views }}</td>
                                        <td>{{ $metric->installs }}</td>
                                        <td>{{ $metric->trial_starts }}</td>
                                        <td>{{ $metric->trial_conversions }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="6" class="text-center text-muted">{{ __('No store metrics yet.') }}</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
