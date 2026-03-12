@php
    $onboarding = $user->onboarding;

    $goalLabels = [
        'speaking' => __('Speaking Lessons'),
        'kids' => __('For Kids'),
        'general' => __('General English'),
        'from_scratch' => __('English From Scratch'),
        'business' => __('Business English'),
        'ielts_toefl' => __('IELTS & TOEFL'),
    ];

    $instructorLabels = [
        'foreign' => __('Foreign Instructor'),
        'turkish' => __('Turkish Instructor'),
    ];

    $availabilityLabels = [
        'morning' => __('Morning (06:00-12:00)'),
        'noon' => __('Afternoon (12:00-18:00)'),
        'evening' => __('Evening (18:00-24:00)'),
        'night' => __('Night (00:00-06:00)'),
    ];

    $levelLabels = [
        'beginner' => __('Beginner'),
        'basics' => __('I know the basics'),
        'intermediate' => __('I can speak generally'),
        'fluent' => __('I can speak fluently'),
    ];

    $availabilityText = collect($onboarding?->availability ?? [])
        ->map(fn($key) => $availabilityLabels[$key] ?? $key)
        ->implode(', ');

    $hasLeadForm = (bool) ($onboarding?->lesson_place
        || $onboarding?->student_type
        || $onboarding?->goal
        || $onboarding?->level
        || $onboarding?->frequency
        || $onboarding?->details
        || $onboarding?->start_when);
@endphp

<div class="tab-pane fade show {{ session('profile_tab') == 'preferences' ? 'active': '' }}" id="itemPref-tab-pane"
    role="tabpanel" aria-labelledby="itemPref-tab" tabindex="0">
    <div class="instructor__profile-form-wrap">
        <div class="dashboard__content-title mb-3">
            <h4 class="title">{{ __('Preferences') }}</h4>
        </div>

        @if ($onboarding)
            <div class="row">
                @if ($hasLeadForm)
                    <div class="col-md-6">
                        <div class="form-grp">
                            <label>{{ __('Where should the lesson take place?') }}</label>
                            <input type="text" value="{{ $onboarding->lesson_place ?: '-' }}" readonly>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-grp">
                            <label>{{ __('Who will take the lesson?') }}</label>
                            <input type="text" value="{{ $onboarding->student_type ?: '-' }}" readonly>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-grp">
                            <label>{{ __('Goal') }}</label>
                            <input type="text" value="{{ $onboarding->goal ?: '-' }}" readonly>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-grp">
                            <label>{{ __('Level') }}</label>
                            <input type="text" value="{{ $onboarding->level ?: '-' }}" readonly>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-grp">
                            <label>{{ __('Frequency') }}</label>
                            <input type="text" value="{{ $onboarding->frequency ?: '-' }}" readonly>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-grp">
                            <label>{{ __('When') }}</label>
                            <input type="text" value="{{ $onboarding->start_when ?: '-' }}" readonly>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-grp">
                            <label>{{ __('Additional Info') }}</label>
                            <textarea class="form-control" rows="4" readonly>{{ $onboarding->details ?: '-' }}</textarea>
                        </div>
                    </div>
                @else
                    <div class="col-md-6">
                        <div class="form-grp">
                            <label>{{ __('Learning Goal') }}</label>
                            <input type="text" value="{{ $goalLabels[$onboarding->learning_goal] ?? $onboarding->learning_goal ?: '-' }}" readonly>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-grp">
                            <label>{{ __('Instructor Preference') }}</label>
                            <input
                                type="text"
                                value="{{ $instructorLabels[$onboarding->instructor_preference] ?? $onboarding->instructor_preference ?: '-' }}"
                                readonly
                            >
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-grp">
                            <label>{{ __('Lesson Times') }}</label>
                            <input type="text" value="{{ $availabilityText ?: '-' }}" readonly>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-grp">
                            <label>{{ __('English Level') }}</label>
                            <input type="text" value="{{ $levelLabels[$onboarding->english_level] ?? $onboarding->english_level ?: '-' }}" readonly>
                        </div>
                    </div>
                @endif

                <div class="col-md-6">
                    <div class="form-grp">
                        <label>{{ __('Birth Date') }}</label>
                        <input type="text" value="{{ $onboarding->birth_date ? $onboarding->birth_date->format('d.m.Y') : '-' }}" readonly>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-grp">
                        <label>{{ __('Referral Code') }}</label>
                        <input type="text" value="{{ $onboarding->referral_code ?: '-' }}" readonly>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-grp">
                        <label>{{ __('Where did you hear about us?') }}</label>
                        <input type="text" value="{{ $onboarding->heard_from ?: '-' }}" readonly>
                    </div>
                </div>

                <div class="col-md-12">
                    <div class="form-grp">
                        <label>{{ __('Marketing Consent') }}</label>
                        <input type="text" value="{{ $onboarding->marketing_consent ? __('Yes') : __('No') }}" readonly>
                    </div>
                </div>
            </div>
        @else
            <p class="mb-0 text-muted">{{ __('Preference details not found.') }}</p>
        @endif
    </div>
</div>
