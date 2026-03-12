@extends('admin.master_layout')
@section('title')
    <title>{{ __('Trial Lesson Requests') }}</title>
@endsection

@section('admin-content')
    @php
        $tableMissing = $tableMissing ?? false;
    @endphp
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>{{ __('Trial Lesson Requests') }}</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a>
                    </div>
                    <div class="breadcrumb-item">{{ __('Trial Lesson Requests') }}</div>
                </div>
            </div>

            <div class="section-body">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <form action="{{ route('admin.trial-lesson-requests.index') }}" method="GET" class="row">
                                    <div class="col-md-5 form-group">
                                        <input type="text" class="form-control" name="keyword"
                                            value="{{ request('keyword') }}"
                                            placeholder="{{ __('Search') }} ({{ __('Name') }}, {{ __('Email') }}, {{ __('Phone') }})">
                                    </div>
                                    <div class="col-md-3 form-group">
                                        <select class="form-control" name="status">
                                            <option value="">{{ __('Select Status') }}</option>
                                            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>
                                                {{ __('Pending') }}
                                            </option>
                                            <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>
                                                {{ __('Approved') }}
                                            </option>
                                            <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>
                                                {{ __('Rejected') }}
                                            </option>
                                        </select>
                                    </div>
                                    <div class="col-md-4 form-group d-flex">
                                        <button type="submit" class="btn btn-primary mr-2">{{ __('Search') }}</button>
                                        <a href="{{ route('admin.trial-lesson-requests.index') }}"
                                            class="btn btn-light border">{{ __('Clear') }}</a>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4>{{ __('Trial Lesson Requests') }}</h4>
                            </div>
                            <div class="card-body">
                                @if ($tableMissing)
                                    <div class="alert alert-warning mb-0">
                                        {{ __('trial_lesson_requests table not found. Please run migrations.') }}
                                    </div>
                                @else
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped">
                                            <thead>
                                                <tr>
                                                    <th>{{ __('SN') }}</th>
                                                    <th>{{ __('User') }}</th>
                                                    <th>{{ __('Phone') }}</th>
                                                    <th>{{ __('Status') }}</th>
                                                    <th>{{ __('Date') }}</th>
                                                    <th>{{ __('Action') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse ($requests as $index => $requestItem)
                                                    <tr>
                                                        <td>{{ $requests->firstItem() + $index }}</td>
                                                        <td>
                                                            <div class="font-weight-bold">
                                                                {{ $requestItem->user?->name ?? '-' }}
                                                            </div>
                                                            <div class="text-muted">
                                                                {{ $requestItem->user?->email ?? '-' }}
                                                            </div>
                                                        </td>
                                                        <td>{{ $requestItem->phone ?: ($requestItem->user?->phone ?? '-') }}</td>
                                                        <td>
                                                            @if ($requestItem->status === 'approved')
                                                                <span class="badge badge-success">{{ __('Approved') }}</span>
                                                            @elseif ($requestItem->status === 'rejected')
                                                                <span class="badge badge-danger">{{ __('Rejected') }}</span>
                                                            @else
                                                                <span class="badge badge-warning">{{ __('Pending') }}</span>
                                                            @endif
                                                        </td>
                                                        <td>{{ formatDate($requestItem->created_at) }}</td>
                                                        <td>
                                                            <form method="POST"
                                                                action="{{ route('admin.trial-lesson-requests.update-status', $requestItem->id) }}"
                                                                class="form-inline">
                                                                @csrf
                                                                @method('PUT')
                                                                <select name="status" class="form-control form-control-sm mr-2">
                                                                    <option value="pending"
                                                                        {{ $requestItem->status === 'pending' ? 'selected' : '' }}>
                                                                        {{ __('Pending') }}
                                                                    </option>
                                                                    <option value="approved"
                                                                        {{ $requestItem->status === 'approved' ? 'selected' : '' }}>
                                                                        {{ __('Approved') }}
                                                                    </option>
                                                                    <option value="rejected"
                                                                        {{ $requestItem->status === 'rejected' ? 'selected' : '' }}>
                                                                        {{ __('Rejected') }}
                                                                    </option>
                                                                </select>
                                                                <button type="submit" class="btn btn-sm btn-primary">
                                                                    {{ __('Update') }}
                                                                </button>
                                                            </form>
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="6" class="text-center text-muted">{{ __('No Data!') }}
                                                        </td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>

                                    <div class="mt-3">
                                        {{ $requests->links() }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection

