@extends('frontend.student-dashboard.layouts.master')

@section('dashboard-contents')
    <section class="ci-booking" id="student-instructor-schedule">
        <div class="ci-booking__top">
            <a href="{{ route('student.instructors') }}" class="ci-booking__back">
                <i class="fas fa-arrow-left" aria-hidden="true"></i>
                {{ __('Back') }}
            </a>
            <div class="ci-booking__range">
                <a class="ci-booking__nav" href="{{ route('student.instructors.schedule', ['instructor' => $instructor->id, 'start' => $prevStart]) }}" aria-label="{{ __('Previous') }}">&larr;</a>
                <span>{{ $weekStart->format('d F') }} - {{ $weekEnd->format('d F Y') }}</span>
                <a class="ci-booking__nav" href="{{ route('student.instructors.schedule', ['instructor' => $instructor->id, 'start' => $nextStart]) }}" aria-label="{{ __('Next') }}">&rarr;</a>
            </div>
        </div>

        <div class="ci-booking__hero">
            <div class="ci-teacher">
                <div class="ci-teacher__avatar">
                    <img src="{{ asset($instructor->image ?: 'frontend/img/courses/course_thumb01.jpg') }}" alt="{{ $instructor->name }}">
                </div>
                <div>
                    <p class="ci-teacher__label">{{ __('Instructor') }}</p>
                    <h3 class="ci-teacher__name">{{ $instructor->name }}</h3>
                    <p class="ci-teacher__role">{{ $instructor->job_title }}</p>
                </div>
            </div>
            <div class="ci-booking__meta">
                <h2>{{ __('Reservation details') }}</h2>
                <p>{{ __('Pick your favorite instructor and schedule a trial lesson instantly') }}</p>
                <p class="ci-booking__duration">
                    {{ __('Lesson Duration') }}: <strong>{{ $lessonDuration }}</strong> {{ __('Minutes') }}
                </p>
            </div>
        </div>

        <form method="POST" action="{{ route('student.instructors.schedule.store', $instructor->id) }}" class="ci-booking__grid">
            @csrf
            @foreach ($days as $index => $day)
                @php
                    $dateKey = $day->format('Y-m-d');
                    $slots = $availabilityByDay->get($day->dayOfWeekIso - 1, collect());
                    $dayLessons = $lessonsByDate->get($dateKey, collect());
                    $bookedTimes = $dayLessons->map(fn ($lesson) => $lesson->start_time?->format('H:i'))->filter()->values()->all();
                    $myTimes = $dayLessons
                        ->filter(fn ($lesson) => (int) $lesson->student_id === (int) auth()->id())
                        ->map(fn ($lesson) => $lesson->start_time?->format('H:i'))
                        ->filter()
                        ->values()
                        ->all();
                @endphp
                <div class="ci-day">
                    <div class="ci-day__head">
                        <div class="ci-day__date">{{ $day->format('d') }}</div>
                        <div class="ci-day__meta">
                            <span class="ci-day__name">{{ $dayLabels[$index] ?? $day->format('D') }}</span>
                            <span class="ci-day__month">{{ $day->format('M') }}</span>
                        </div>
                    </div>

                    <div class="ci-day__slots">
                        @forelse ($slots as $slot)
                            @php
                                $startTime = substr($slot->start_time, 0, 5);
                                $endTime = substr($slot->end_time, 0, 5);
                                $slotStart = \Carbon\Carbon::createFromFormat('Y-m-d H:i', $dateKey . ' ' . $startTime);
                                $availabilityEnd = \Carbon\Carbon::createFromFormat('Y-m-d H:i', $dateKey . ' ' . $endTime);
                                $displayEnd = $lessonDuration > 0 ? $slotStart->copy()->addMinutes($lessonDuration)->format('H:i') : $endTime;
                                $fitsDuration = $lessonDuration > 0 ? $slotStart->copy()->addMinutes($lessonDuration)->lte($availabilityEnd) : true;
                                $isPast = $slotStart->isPast();
                                $isBooked = in_array($startTime, $bookedTimes, true);
                                $isMine = in_array($startTime, $myTimes, true);
                                $slotClasses = 'ci-slot';
                                if ($isMine) {
                                    $slotClasses .= ' ci-slot--mine';
                                } elseif ($isBooked || $isPast || !$fitsDuration) {
                                    $slotClasses .= ' ci-slot--booked';
                                }
                            @endphp

                            @if ($isMine)
                                <span class="{{ $slotClasses }}">{{ $startTime }} - {{ $displayEnd }} <em>{{ __('Booked') }}</em></span>
                            @elseif ($isBooked || $isPast || !$fitsDuration)
                                <span class="{{ $slotClasses }}">{{ $startTime }} - {{ $displayEnd }} <em>{{ __('Unavailable') }}</em></span>
                            @else
                                <button type="submit" name="slot" value="{{ $dateKey }}|{{ $startTime }}" class="{{ $slotClasses }}">
                                    {{ $startTime }} - {{ $displayEnd }}
                                </button>
                            @endif
                        @empty
                            <span class="ci-slot ci-slot--empty">{{ __('No availability') }}</span>
                        @endforelse
                    </div>
                </div>
            @endforeach
        </form>
    </section>
@endsection

@push('styles')
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Sora:wght@400;600;700;800&display=swap');

        .ci-booking{
            --bk-ink:#0f172a;
            --bk-muted:#64748b;
            --bk-accent:#f59e0b;
            --bk-deep:#0e5c93;
            --bk-card:#ffffff;
            font-family:"Sora", sans-serif;
            border-radius:24px;
            padding:28px;
            position:relative;
            overflow:hidden;
            background:
                radial-gradient(700px circle at 0% 0%, rgba(245,158,11,.18), transparent 60%),
                radial-gradient(700px circle at 100% 20%, rgba(14,92,147,.16), transparent 58%),
                linear-gradient(180deg, #fff7e6 0%, #fff 55%, #f8fafc 100%);
            box-shadow:0 30px 60px rgba(15,23,42,.08);
        }
        .ci-booking::before{
            content:"";
            position:absolute;
            inset:0;
            background:repeating-linear-gradient(135deg, rgba(15,23,42,.04) 0 2px, transparent 2px 12px);
            opacity:.35;
            pointer-events:none;
        }
        .ci-booking > *{position:relative;z-index:1;}

        .ci-booking__top{
            display:flex;
            justify-content:space-between;
            align-items:center;
            gap:16px;
            flex-wrap:wrap;
            margin-bottom:18px;
        }
        .ci-booking__back{
            display:inline-flex;
            align-items:center;
            gap:8px;
            padding:8px 14px;
            border-radius:999px;
            background:#fff;
            border:1px solid rgba(15,23,42,.1);
            color:var(--bk-ink);
            font-weight:800;
            text-decoration:none;
            box-shadow:0 10px 20px rgba(15,23,42,.08);
        }
        .ci-booking__range{
            display:flex;
            align-items:center;
            gap:12px;
            font-weight:900;
            color:var(--bk-ink);
            background:#fff;
            padding:10px 16px;
            border-radius:999px;
            border:1px solid rgba(15,23,42,.08);
            box-shadow:0 10px 20px rgba(15,23,42,.08);
        }
        .ci-booking__nav{
            width:32px;
            height:32px;
            border-radius:50%;
            border:1px solid rgba(15,23,42,.12);
            display:grid;
            place-items:center;
            text-decoration:none;
            color:var(--bk-ink);
            background:#fff;
        }

        .ci-booking__hero{
            display:flex;
            gap:24px;
            flex-wrap:wrap;
            align-items:center;
            background:var(--bk-card);
            border:1px solid rgba(15,23,42,.08);
            border-radius:20px;
            padding:20px 22px;
            box-shadow:0 16px 32px rgba(15,23,42,.08);
            margin-bottom:22px;
        }
        .ci-teacher{display:flex;align-items:center;gap:16px;}
        .ci-teacher__avatar{
            width:68px;
            height:68px;
            border-radius:18px;
            padding:4px;
            background:linear-gradient(135deg, rgba(245,158,11,.35), rgba(14,92,147,.2));
        }
        .ci-teacher__avatar img{width:100%;height:100%;object-fit:cover;border-radius:14px;}
        .ci-teacher__label{margin:0;color:var(--bk-muted);font-weight:800;font-size:12px;text-transform:uppercase;letter-spacing:.08em;}
        .ci-teacher__name{margin:4px 0 0;font-weight:900;color:var(--bk-ink);}
        .ci-teacher__role{margin:4px 0 0;color:var(--bk-muted);font-weight:700;font-size:12px;}

        .ci-booking__meta{flex:1;min-width:240px;}
        .ci-booking__meta h2{margin:0;font-weight:900;color:var(--bk-ink);}
        .ci-booking__meta p{margin:6px 0 0;color:var(--bk-muted);font-weight:700;}
        .ci-booking__duration{font-size:13px;}

        .ci-booking__grid{
            display:grid;
            grid-template-columns:repeat(7, minmax(0, 1fr));
            gap:14px;
        }
        .ci-day{
            background:var(--bk-card);
            border-radius:18px;
            border:1px solid rgba(15,23,42,.08);
            padding:12px;
            min-height:220px;
            display:flex;
            flex-direction:column;
            gap:12px;
            box-shadow:0 14px 24px rgba(15,23,42,.06);
        }
        .ci-day__head{
            display:flex;
            align-items:center;
            gap:10px;
            padding-bottom:8px;
            border-bottom:1px solid rgba(15,23,42,.06);
        }
        .ci-day__date{
            width:34px;
            height:34px;
            border-radius:12px;
            background:#fff4dc;
            display:grid;
            place-items:center;
            font-weight:900;
            color:var(--bk-ink);
        }
        .ci-day__meta{display:grid;gap:2px;}
        .ci-day__name{font-weight:900;color:var(--bk-ink);}
        .ci-day__month{font-size:12px;color:var(--bk-muted);font-weight:700;}

        .ci-day__slots{
            display:flex;
            flex-direction:column;
            gap:8px;
        }
        .ci-slot{
            border-radius:12px;
            padding:8px 10px;
            font-weight:800;
            font-size:12px;
            border:1px solid rgba(245,158,11,.3);
            background:linear-gradient(135deg, rgba(245,158,11,.9), rgba(249,115,22,.95));
            color:#fff;
            text-align:center;
            cursor:pointer;
            box-shadow:0 10px 18px rgba(245,158,11,.2);
            transition:transform .2s ease, box-shadow .2s ease;
        }
        .ci-slot em{display:block;font-size:11px;font-style:normal;opacity:.85;margin-top:2px;}
        .ci-slot:hover{transform:translateY(-1px);box-shadow:0 14px 22px rgba(245,158,11,.28);}
        .ci-slot--booked{
            background:#f3f4f6;
            border-color:#e2e8f0;
            color:#94a3b8;
            cursor:not-allowed;
            box-shadow:none;
        }
        .ci-slot--mine{
            background:linear-gradient(135deg, rgba(14,92,147,.92), rgba(30,64,175,.9));
            border-color:rgba(14,92,147,.4);
            box-shadow:0 12px 20px rgba(14,92,147,.2);
            cursor:default;
        }
        .ci-slot--empty{
            background:#fff;
            border:1px dashed rgba(15,23,42,.16);
            color:var(--bk-muted);
            cursor:default;
            box-shadow:none;
        }

        @media (max-width: 1199px){
            .ci-booking__grid{grid-template-columns:repeat(4, minmax(0, 1fr));}
        }
        @media (max-width: 991px){
            .ci-booking__grid{grid-template-columns:repeat(2, minmax(0, 1fr));}
            .ci-booking__hero{flex-direction:column;align-items:flex-start;}
        }
        @media (max-width: 575px){
            .ci-booking{padding:18px;}
            .ci-booking__range{width:100%;justify-content:center;}
            .ci-booking__grid{grid-template-columns:1fr;}
        }
    </style>
@endpush
