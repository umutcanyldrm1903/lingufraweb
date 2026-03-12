<div class="sp-drawer__header">
    <h3>{{ $student->name }}</h3>
    <p>{{ $student->email }}</p>
</div>

<ul class="nav nav-tabs sp-drawer__tabs" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active" id="tab-homeworks" data-bs-toggle="tab" data-bs-target="#drawer-homeworks"
            type="button" role="tab">
            {{ __('Homeworks') }}
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="tab-attendance" data-bs-toggle="tab" data-bs-target="#drawer-attendance"
            type="button" role="tab">
            {{ __('Attendance') }}
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="tab-profile" data-bs-toggle="tab" data-bs-target="#drawer-profile"
            type="button" role="tab">
            {{ __('Profile') }}
        </button>
    </li>
</ul>

<div class="tab-content sp-drawer__content">
    <div class="tab-pane fade show active" id="drawer-homeworks" role="tabpanel">
        @forelse ($homeworks as $hw)
            <div class="sp-drawer-card">
                <div>
                    <strong>{{ $hw->title }}</strong>
                    <span>{{ $hw->due_at?->format('d M Y, H:i') ?? __('No deadline') }}</span>
                </div>
                @if ($hw->attachment_path)
                    <a href="{{ asset($hw->attachment_path) }}" target="_blank" class="sp-btn sp-btn-light sp-btn-sm">
                        {{ __('View') }}
                    </a>
                @endif
            </div>
        @empty
            <div class="sp-drawer-empty">{{ __('No homework found.') }}</div>
        @endforelse
    </div>

    <div class="tab-pane fade" id="drawer-attendance" role="tabpanel">
        @forelse ($lessons as $lesson)
            @php
                $status = $lessonStatuses[$lesson->id] ?? ['key' => 'scheduled', 'label' => __('Scheduled')];
            @endphp
            <div class="sp-drawer-card sp-drawer-card--{{ $status['key'] }}">
                <div>
                    <strong>{{ $lesson->title }}</strong>
                    <span>{{ $lesson->start_time?->format('d M Y, H:i') ?? '-' }}</span>
                </div>
                <span class="sp-drawer-status">{{ $status['label'] }}</span>
            </div>
        @empty
            <div class="sp-drawer-empty">{{ __('No lessons found.') }}</div>
        @endforelse
    </div>

    <div class="tab-pane fade" id="drawer-profile" role="tabpanel">
        <div class="sp-drawer-profile">
            <div class="sp-drawer-profile__row">
                <span>{{ __('Plan') }}</span>
                <strong>{{ $plan->plan_title ?: $plan->plan_key }}</strong>
            </div>
            <div class="sp-drawer-profile__row">
                <span>{{ __('Credits') }}</span>
                <strong>{{ $plan->lessons_remaining ?? 0 }}</strong>
            </div>
            <div class="sp-drawer-profile__row">
                <span>{{ __('Cancellation Right') }}</span>
                <strong>{{ $plan->cancel_remaining ?? 0 }}</strong>
            </div>
            <div class="sp-drawer-profile__row">
                <span>{{ __('Plan Ends') }}</span>
                <strong>{{ $plan->ends_at?->format('d M Y') ?? '-' }}</strong>
            </div>
        </div>
    </div>
</div>
