@extends('frontend.instructor-dashboard.layouts.master')

@section('dashboard-contents')
    <div class="dashboard__content-wrap">
        <div class="dashboard__content-title">
            <h4 class="title">{{ __('Zoom live setting') }}</h4>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <div class="instructor__profile-form-wrap">
                    <div class="row g-4">
                        <div class="col-xl-6">
                            <div class="alert alert-info" role="alert">
                                <h4 class="alert-heading">{{ __('Zoom meeting setup') }}</h4>
                                <p class="mb-3">
                                    {{ __('Enter your fixed Zoom Meeting ID and Passcode once. The system will use the same meeting for all your reservations.') }}
                                </p>

                                @if ($isConfigured)
                                    <p class="mb-2">
                                        <strong>{{ __('Configured') }}:</strong>
                                        {{ __('Meeting ID') }}: {{ $credential?->default_meeting_id }}
                                    </p>
                                    <form action="{{ route('instructor.zoom-setting.disconnect') }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-danger">{{ __('Clear Zoom settings') }}</button>
                                    </form>
                                @else
                                    <p class="mb-0">{{ __('Not configured yet.') }}</p>
                                @endif
                            </div>
                        </div>

                        <form action="{{ route('instructor.zoom-setting.update') }}" method="POST"
                            class="col-xl-6 instructor__profile-form">
                            @csrf
                            @method('PUT')
                            <div class="form-grp">
                                <label for="default_meeting_id">{{ __('Meeting ID') }}</label>
                                <input id="default_meeting_id" name="default_meeting_id" type="text"
                                    value="{{ $credential?->default_meeting_id }}" placeholder="1234567890">
                            </div>
                            <div class="form-grp">
                                <label for="default_meeting_password">{{ __('Passcode') }}</label>
                                <input id="default_meeting_password" name="default_meeting_password" type="text"
                                    value="{{ $credential?->default_meeting_password }}" placeholder="******">
                                <small class="d-block mt-1 text-muted">
                                    {{ __('Use the meeting Passcode from Zoom meeting details. Do NOT use Host Key.') }}
                                </small>
                            </div>
                            <div class="form-grp">
                                <label for="default_join_url">{{ __('Join URL (optional)') }}</label>
                                <input id="default_join_url" name="default_join_url" type="text"
                                    value="{{ $credential?->default_join_url }}" placeholder="https://zoom.us/j/...">
                                <small class="d-block mt-1 text-muted">
                                    {{ __('Copy the full invitation link from Zoom (it usually contains ?pwd=...).') }}
                                </small>
                            </div>
                            <div class="submit-btn mt-25">
                                <button type="submit" class="btn">{{ __('Update') }}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
