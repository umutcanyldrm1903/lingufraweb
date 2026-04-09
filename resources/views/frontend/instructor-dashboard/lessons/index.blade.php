@extends('frontend.instructor-dashboard.layouts.master')

@section('dashboard-contents')
    <div class="sp-lessons">
        <div class="sp-lessons__head">
            <h2 class="sp-lessons__title">{{ __('Lessons') }}</h2>
            <form method="GET" action="{{ route('instructor.lessons.index') }}" class="sp-lessons__filter">
                <input type="month" name="month" class="form-control" value="{{ $selectedMonth }}">
                <button class="sp-btn sp-btn-dark" type="submit">{{ __('Filter') }}</button>
                <a href="{{ route('instructor.lessons.index') }}" class="sp-btn sp-btn-light">{{ __('Clear') }}</a>
            </form>
        </div>

        <div class="sp-lessons__list">
            @forelse ($lessons as $lesson)
                @php
                    $status = $statusMap[$lesson->id] ?? ['key' => 'scheduled', 'label' => __('Scheduled')];
                    $isUpcoming = $lesson->start_time && $lesson->start_time->isFuture() && $status['key'] === 'scheduled';
                @endphp
                <div class="sp-lesson-row sp-lesson-row--{{ $status['key'] }}">
                    <div class="sp-lesson-row__main">
                        <div class="sp-lesson-row__name">{{ $lesson->student?->name ?? __('Student') }}</div>
                        <div class="sp-lesson-row__meta">
                            {{ $lesson->start_time?->format('d M Y, H:i') ?? '-' }}
                        </div>
                        <form method="POST" action="{{ route('instructor.lessons.summary', $lesson) }}" class="sp-summary-form">
                            @csrf
                            <textarea name="instructor_summary" rows="2" class="form-control" placeholder="{{ __('Lesson summary / report') }}">{{ $lesson->instructor_summary }}</textarea>
                            <button type="submit" class="sp-btn sp-btn-light sp-btn-sm">{{ __('Save Report') }}</button>
                        </form>
                    </div>
                    <div class="sp-lesson-row__status">{{ $status['label'] }}</div>
                    <div class="sp-lesson-row__actions">
                        @if ($isUpcoming)
                            <a href="{{ route('instructor.live-lessons.join', $lesson->id) }}" class="sp-btn sp-btn-dark sp-btn-sm">
                                {{ __('Go to Lesson') }}
                            </a>
                            <form method="POST" action="{{ route('instructor.lessons.cancel', $lesson) }}" class="sp-inline-form">
                                @csrf
                                <button type="submit" class="sp-btn sp-btn-light sp-btn-sm">{{ __('Cancel') }}</button>
                            </form>
                        @else
                            <span class="sp-lesson-row__hint">{{ $status['label'] }}</span>
                        @endif
                    </div>
                </div>
            @empty
                <div class="sp-empty-state">
                    <div class="sp-empty-state__icon" aria-hidden="true">
                        <i class="fas fa-calendar-times"></i>
                    </div>
                    <div class="sp-empty-state__text">{{ __('No lessons found.') }}</div>
                </div>
            @endforelse
        </div>

        <div class="sp-pagination">
            {{ $lessons->links() }}
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .sp-lessons__head{display:flex;justify-content:space-between;align-items:center;gap:16px;flex-wrap:wrap;margin-bottom:16px;}
        .sp-lessons__title{margin:0;font-weight:1000;color:#111827;}
        .sp-lessons__filter{display:flex;gap:10px;align-items:center;flex-wrap:wrap;}
        .sp-lessons__filter .form-control{min-width:180px;}

        .sp-lessons__list{display:grid;gap:12px;}
        .sp-lesson-row{display:flex;align-items:center;justify-content:space-between;gap:14px;padding:14px 16px;border:1px solid #f3f4f6;border-radius:16px;background:#fff;box-shadow:0 12px 24px rgba(0,0,0,0.06);}
        .sp-lesson-row__main{display:grid;gap:4px;}
        .sp-lesson-row__name{font-weight:900;color:#111827;}
        .sp-lesson-row__meta{font-weight:700;color:#6b7280;font-size:13px;}
        .sp-summary-form{display:grid;gap:8px;margin-top:10px;max-width:420px;}
        .sp-summary-form textarea{min-height:74px;}
        .sp-lesson-row__status{font-weight:900;font-size:12px;text-transform:uppercase;letter-spacing:.04em;padding:6px 10px;border-radius:999px;background:#f9fafb;border:1px solid #e5e7eb;}
        .sp-lesson-row__actions{display:flex;gap:8px;align-items:center;flex-wrap:wrap;}
        .sp-inline-form{margin:0;}
        .sp-lesson-row__hint{font-weight:800;color:#6b7280;font-size:12px;}

        .sp-lesson-row--completed .sp-lesson-row__status{background:#ecfdf3;border-color:#d1fae5;color:#16a34a;}
        .sp-lesson-row--late .sp-lesson-row__status{background:#fef3c7;border-color:#fde68a;color:#d97706;}
        .sp-lesson-row--no_show .sp-lesson-row__status{background:#fee2e2;border-color:#fecaca;color:#dc2626;}
        .sp-lesson-row--cancelled_teacher .sp-lesson-row__status,
        .sp-lesson-row--cancelled_student .sp-lesson-row__status{background:#f3f4f6;border-color:#e5e7eb;color:#6b7280;}

        .sp-empty-state{display:flex;flex-direction:column;align-items:center;justify-content:center;text-align:center;gap:10px;min-height:240px;padding:18px 10px;}
        .sp-empty-state__icon{width:92px;height:92px;border-radius:50%;border:3px solid rgba(246,161,5,.55);display:grid;place-items:center;color:var(--student-brand);font-size:34px;box-shadow:0 14px 28px rgba(0,0,0,0.06);background:#fff;}
        .sp-empty-state__text{font-weight:1000;color:#111827;}
        .sp-btn-sm{padding:6px 10px;font-size:12px;}
        .sp-pagination{margin-top:16px;}

        @media (max-width: 767px){
            .sp-lesson-row{flex-direction:column;align-items:flex-start;}
            .sp-lesson-row__actions{width:100%;}
        }
    </style>
@endpush
