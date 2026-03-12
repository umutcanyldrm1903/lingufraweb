@extends('frontend.instructor-dashboard.layouts.master')

@section('dashboard-contents')
    @php
        $days = [];
        $cursor = $weekStart->copy();
        for ($i = 0; $i < 7; $i++) {
            $days[] = $cursor->copy();
            $cursor->addDay();
        }

        $prevStart = $weekStart->copy()->subWeek()->format('Y-m-d');
        $nextStart = $weekStart->copy()->addWeek()->format('Y-m-d');
        $dayLabels = [
            __('Mon'), __('Tue'), __('Wed'), __('Thu'), __('Fri'), __('Sat'), __('Sun'),
        ];
    @endphp

    <div class="sp-schedule">
        <div class="sp-schedule__head">
            <div>
                <h2 class="sp-schedule__title">{{ __('My Lessons') }}</h2>
                <p class="sp-schedule__subtitle">{{ __('Manage your weekly schedule and upcoming lessons.') }}</p>
            </div>
            <div class="sp-schedule__actions">
                <label class="sp-checkbox">
                    <input type="checkbox" checked disabled>
                    <span>{{ __('All Lessons') }}</span>
                </label>
                <a class="sp-btn sp-btn-outline" href="{{ route('instructor.setting.index', ['tab' => 'schedule']) }}">
                    {{ __('Edit Schedule') }}
                </a>
            </div>
        </div>

        <div class="sp-schedule__range">
            <a class="sp-range-btn" href="{{ route('instructor.schedule.index', ['start' => $prevStart]) }}" aria-label="{{ __('Previous week') }}">&larr;</a>
            <span>{{ $weekStart->format('d F') }} - {{ $weekEnd->format('d F Y') }}</span>
            <a class="sp-range-btn" href="{{ route('instructor.schedule.index', ['start' => $nextStart]) }}" aria-label="{{ __('Next week') }}">&rarr;</a>
        </div>

        <div class="sp-schedule__grid">
            @foreach ($days as $index => $day)
                @php
                    $dateKey = $day->format('Y-m-d');
                    $dayLessons = $lessonsByDate->get($dateKey, collect());
                    $slots = $availabilityByDay->get($day->dayOfWeekIso - 1, collect());
                @endphp
                <div class="sp-day">
                    <div class="sp-day__head">
                        <div class="sp-day__date">{{ $day->format('d') }}</div>
                        <div class="sp-day__meta">
                            <span class="sp-day__name">{{ $dayLabels[$index] ?? $day->format('D') }}</span>
                            <span class="sp-day__month">{{ $day->format('M') }}</span>
                        </div>
                    </div>

                    <div class="sp-day__lessons">
                        @forelse ($dayLessons as $lesson)
                            <div class="sp-lesson-chip">
                                <div class="sp-lesson-chip__name">{{ $lesson->student?->name ?? __('Student') }}</div>
                                <div class="sp-lesson-chip__time">{{ $lesson->start_time?->format('H:i') }}</div>
                            </div>
                        @empty
                            <div class="sp-day__empty">{{ __('No lesson') }}</div>
                        @endforelse
                    </div>

                    <div class="sp-day__slots">
                        @forelse ($slots as $slot)
                            <span>{{ substr($slot->start_time, 0, 5) }}-{{ substr($slot->end_time, 0, 5) }}</span>
                        @empty
                            <span class="sp-day__slot-empty">{{ __('No availability') }}</span>
                        @endforelse
                    </div>
                </div>
            @endforeach
        </div>
    </div>

@endsection

@push('styles')
    <style>
        .sp-schedule__head{display:flex;justify-content:space-between;gap:18px;align-items:flex-start;flex-wrap:wrap;margin-bottom:16px;}
        .sp-schedule__title{margin:0;font-weight:1000;color:#111827;}
        .sp-schedule__subtitle{margin:6px 0 0;color:#6b7280;font-weight:700;}
        .sp-schedule__actions{display:flex;align-items:center;gap:12px;}
        .sp-checkbox{display:flex;align-items:center;gap:8px;font-weight:800;color:#111827;}

        .sp-schedule__range{display:flex;align-items:center;justify-content:center;gap:12px;font-weight:900;margin-bottom:22px;}
        .sp-range-btn{width:32px;height:32px;border-radius:50%;border:1px solid #e5e7eb;display:grid;place-items:center;text-decoration:none;color:#111827;background:#fff;}

        .sp-schedule__grid{display:grid;grid-template-columns:repeat(7, minmax(0, 1fr));gap:12px;}
        .sp-day{background:#fff;border:1px solid #f3f4f6;border-radius:16px;padding:12px;min-height:220px;display:flex;flex-direction:column;gap:10px;}
        .sp-day__head{display:flex;align-items:center;gap:10px;border-bottom:1px solid #f3f4f6;padding-bottom:8px;}
        .sp-day__date{width:36px;height:36px;border-radius:12px;background:#fff2d0;color:#111827;font-weight:1000;display:grid;place-items:center;}
        .sp-day__meta{display:grid;gap:2px;}
        .sp-day__name{font-weight:900;color:#111827;}
        .sp-day__month{font-size:12px;color:#6b7280;font-weight:700;}

        .sp-day__lessons{display:grid;gap:8px;flex:1;}
        .sp-lesson-chip{border:1px solid #d1fae5;background:#ecfdf3;border-radius:12px;padding:8px 10px;font-weight:800;display:flex;align-items:center;justify-content:space-between;gap:8px;}
        .sp-lesson-chip__name{font-size:12px;color:#111827;}
        .sp-lesson-chip__time{font-size:11px;color:#16a34a;}
        .sp-day__empty{font-size:12px;color:#9ca3af;font-weight:800;}

        .sp-day__slots{display:flex;flex-wrap:wrap;gap:6px;border-top:1px dashed #e5e7eb;padding-top:8px;font-size:11px;color:#6b7280;font-weight:800;}
        .sp-day__slot-empty{color:#cbd5e1;}

        @media (max-width: 1199px){
            .sp-schedule__grid{grid-template-columns:repeat(4, minmax(0, 1fr));}
        }
        @media (max-width: 991px){
            .sp-schedule__grid{grid-template-columns:repeat(2, minmax(0, 1fr));}
        }
        @media (max-width: 575px){
            .sp-schedule__grid{grid-template-columns:1fr;}
        }
    </style>
@endpush
