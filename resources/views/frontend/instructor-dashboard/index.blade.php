@extends('frontend.instructor-dashboard.layouts.master')

@section('dashboard-contents')
    <div class="sp-home">
        <h2 class="sp-home__greeting">
            {{ __('Good to see you') }}, {{ auth()->user()->name }}!
        </h2>

        <div class="sp-home__card sp-home__card--wide">
            <div class="sp-home__card-head">
                <h3>{{ __('Today Lessons') }}</h3>
            </div>
            @if ($todayLessons->isEmpty())
                <div class="sp-home__empty">
                    <i class="fas fa-calendar-times"></i>
                    <span>{{ __('No lesson today!') }}</span>
                </div>
            @else
                <div class="sp-home__list">
                    @foreach ($todayLessons as $lesson)
                        <div class="sp-home__item">
                            <div>
                                <strong>{{ $lesson->student?->name ?? __('Student') }}</strong>
                                <span>{{ $lesson->start_time?->format('H:i') }}</span>
                            </div>
                            <a href="{{ route('instructor.live-lessons.join', $lesson->id) }}" class="sp-btn sp-btn-dark sp-btn-sm">
                                {{ __('Join') }}
                            </a>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <div class="sp-home__grid">
            <div class="sp-home__card">
                <div class="sp-home__card-head">
                    <h3>{{ __('Notifications') }}</h3>
                </div>
                <div class="sp-home__list">
                    @forelse ($upcomingLessons as $lesson)
                        <div class="sp-home__note">
                            <i class="fas fa-bell"></i>
                            <div>
                                <strong>{{ $lesson->student?->name ?? __('Student') }}</strong>
                                <span>{{ $lesson->start_time?->format('d M, H:i') }}</span>
                            </div>
                        </div>
                    @empty
                        <div class="sp-home__empty">
                            <i class="fas fa-bell-slash"></i>
                            <span>{{ __('No notifications yet.') }}</span>
                        </div>
                    @endforelse
                </div>
            </div>

            <div class="sp-home__card">
                <div class="sp-home__card-head">
                    <h3>{{ __('Announcements') }}</h3>
                </div>
                <div class="sp-home__empty">
                    <i class="fas fa-bullhorn"></i>
                    <span>{{ __('Announcements will appear here.') }}</span>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .sp-home__greeting{margin:0 0 18px;font-weight:1000;color:#111827;}
        .sp-home__grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:18px;margin-top:18px;}
        .sp-home__card{background:#fff;border:1px solid #eef2f7;border-radius:18px;box-shadow:0 14px 30px rgba(0,0,0,0.08);padding:16px;}
        .sp-home__card--wide{margin-bottom:18px;}
        .sp-home__card-head{display:flex;align-items:center;justify-content:space-between;margin-bottom:12px;}
        .sp-home__card-head h3{margin:0;font-weight:1000;color:#111827;font-size:16px;}
        .sp-home__list{display:grid;gap:10px;}
        .sp-home__item{display:flex;align-items:center;justify-content:space-between;gap:10px;border:1px solid #f3f4f6;border-radius:12px;padding:10px 12px;font-weight:800;}
        .sp-home__item strong{display:block;color:#111827;}
        .sp-home__item span{font-size:12px;color:#6b7280;}
        .sp-home__note{display:flex;gap:10px;align-items:center;border:1px solid #f3f4f6;border-radius:12px;padding:10px 12px;}
        .sp-home__note i{width:32px;height:32px;border-radius:50%;background:#fff2d0;color:#111827;display:grid;place-items:center;}
        .sp-home__note strong{display:block;color:#111827;font-weight:900;}
        .sp-home__note span{font-size:12px;color:#6b7280;font-weight:700;}
        .sp-home__empty{display:flex;align-items:center;gap:10px;color:#6b7280;font-weight:800;}
        .sp-home__empty i{width:40px;height:40px;border-radius:50%;background:#fff2d0;color:#111827;display:grid;place-items:center;}
        .sp-btn-sm{padding:6px 10px;font-size:12px;}

        @media (max-width: 991px){
            .sp-home__grid{grid-template-columns:1fr;}
        }
    </style>
@endpush
