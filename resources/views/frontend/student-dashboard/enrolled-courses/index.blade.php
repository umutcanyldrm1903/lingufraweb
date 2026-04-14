@extends('frontend.student-dashboard.layouts.master')

@section('dashboard-contents')
    @php
        $lessonsRemaining = (int) ($currentPlan->lessons_remaining ?? 0);
        $cancelRemaining = (int) ($currentPlan->cancel_remaining ?? 0);
        $upcomingLiveClasses = $upcomingLiveClasses ?? collect();
        $pastLiveClasses = $pastLiveClasses ?? collect();
    @endphp
    <div class="dashboard__content-wrap">
        <div class="dashboard__content-title d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h4 class="title mb-0">{{ __('My Lessons') }}</h4>
            <div class="sp-lessons__count">{{ __('Active Upcoming Lessons') }}: <strong>{{ $upcomingLiveClasses->count() }}</strong></div>
        </div>

        <div class="sp-lessons__tabs" role="tablist" aria-label="{{ __('My Lessons') }}">
            <button type="button" class="sp-lessons__tab is-active" data-live-tab="upcoming" aria-selected="true">{{ __('Upcoming') }}</button>
            <button type="button" class="sp-lessons__tab" data-live-tab="past" aria-selected="false">{{ __('Past') }}</button>
        </div>

        <div class="sp-live" id="student-live-lessons">
            <div class="sp-live__pane is-active" data-live-pane="upcoming">
                @if ($upcomingLiveClasses->count())
                    <div class="sp-live__grid">
                        @foreach ($upcomingLiveClasses as $live)
                            @php
                                $startTime = $live->start_time ? formattedDateTime($live->start_time) : '-';
                                $alreadyJoined = (bool) ($live->attended ?? false);
                                $statusKey = (string) ($live->status ?? 'scheduled');
                                $isCancelled = in_array($statusKey, ['cancelled_teacher', 'cancelled_student'], true);
                                $isPending = $statusKey === 'pending';
                                $canJoin = !$isCancelled && !$isPending && ($alreadyJoined || $lessonsRemaining > 0);
                                $canCancel = $live->kind === 'student' && in_array($statusKey, ['scheduled', 'pending'], true);
                                $thumbnail = asset($live->thumbnail ?: 'frontend/img/courses/course_thumb01.jpg');
                            @endphp
                            <div class="sp-live-card">
                                <div class="sp-live-card__media">
                                    <img src="{{ $thumbnail }}" alt="{{ $live->course_title ?: $live->title }}">
                                </div>
                                <div class="sp-live-card__body">
                                    <div class="sp-live-card__meta">
                                        <span class="sp-live-card__badge">{{ strtoupper($live->type ?? 'LIVE') }}</span>
                                        <span class="sp-live-card__time">{{ $startTime }}</span>
                                    </div>
                                    <h5 class="sp-live-card__lesson">{{ $live->title }}</h5>
                                    <p class="sp-live-card__course">{{ $live->course_title ?: __('Private Lesson') }}</p>
                                    <p class="sp-live-card__teacher">{{ \Illuminate\Support\Str::before($live->instructor_name ?? '', ' ') ?: $live->instructor_name }}</p>
                                    @if ($isCancelled)
                                        <p class="sp-live-card__status">{{ __('Lesson Cancelled') }}</p>
                                    @elseif ($isPending)
                                        <p class="sp-live-card__status">{{ __('Pending') }}</p>
                                    @endif
                                </div>
                                <div class="sp-live-card__actions">
                                    @if ($isPending)
                                        <span class="sp-live-card__btn sp-live-card__btn--disabled">{{ __('Pending') }}</span>
                                    @elseif ($canJoin && $live->join_route)
                                        <a href="{{ $live->join_route }}" class="sp-live-card__btn">
                                            {{ __('Join Lesson') }}
                                        </a>
                                    @elseif ($isCancelled)
                                        <span class="sp-live-card__btn sp-live-card__btn--disabled">{{ __('Cancelled') }}</span>
                                    @else
                                        <span class="sp-live-card__btn sp-live-card__btn--disabled">{{ __('No Credits Left') }}</span>
                                    @endif
                                    @if ($canCancel)
                                        <form method="POST" action="{{ $live->cancel_route }}" class="sp-live-card__cancel">
                                            @csrf
                                            <button type="submit" class="sp-live-card__cancel-btn">{{ $cancelRemaining > 0 ? __('Cancel Lesson') : __('Cancel And Lose Credit') }}</button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="sp-live-empty">
                        <h5 class="sp-live-empty__title">{{ __('No upcoming live lessons') }}</h5>
                        <p class="sp-live-empty__text">{{ __('Your teacher will schedule lessons and they will appear here.') }}</p>
                    </div>
                @endif
            </div>

            <div class="sp-live__pane" data-live-pane="past">
                @if ($pastLiveClasses->count())
                    <div class="sp-live__grid">
                        @foreach ($pastLiveClasses as $live)
                            @php
                                $startTime = $live->start_time ? formattedDateTime($live->start_time) : '-';
                                $statusKey = (string) ($live->status ?? 'scheduled');
                                $thumbnail = asset($live->thumbnail ?: 'frontend/img/courses/course_thumb01.jpg');
                            @endphp
                            <div class="sp-live-card sp-live-card--past">
                                <div class="sp-live-card__media">
                                    <img src="{{ $thumbnail }}" alt="{{ $live->course_title ?: $live->title }}">
                                </div>
                                <div class="sp-live-card__body">
                                    <div class="sp-live-card__meta">
                                        <span class="sp-live-card__badge">{{ strtoupper($live->type ?? 'LIVE') }}</span>
                                        <span class="sp-live-card__time">{{ $startTime }}</span>
                                    </div>
                                    <h5 class="sp-live-card__lesson">{{ $live->title }}</h5>
                                    <p class="sp-live-card__course">{{ $live->course_title ?: __('Private Lesson') }}</p>
                                    <p class="sp-live-card__teacher">{{ \Illuminate\Support\Str::before($live->instructor_name ?? '', ' ') ?: $live->instructor_name }}</p>
                                    @if (in_array($statusKey, ['cancelled_teacher', 'cancelled_student'], true))
                                        <p class="sp-live-card__status">{{ __('Lesson Cancelled') }}</p>
                                    @endif
                                </div>
                                <div class="sp-live-card__actions">
                                    @php
                                        $isCancelled = in_array($statusKey, ['cancelled_teacher', 'cancelled_student'], true);
                                        $isStudentLesson = (string) ($live->kind ?? '') === 'student';
                                        $studentRating = (int) ($live->student_rating ?? 0);
                                    @endphp

                                    @if ($isCancelled)
                                        <span class="sp-live-card__btn sp-live-card__btn--disabled">{{ __('Cancelled') }}</span>
                                    @elseif ($isStudentLesson && $studentRating > 0)
                                        <span class="sp-live-card__btn sp-live-card__btn--disabled">
                                            <i class="fas fa-star"></i> {{ $studentRating }} / 5
                                        </span>
                                    @elseif ($isStudentLesson)
                                        <a class="sp-live-card__btn sp-live-card__btn--outline" href="{{ route('student.live-lessons.rate', $live->id) }}">
                                            {{ __('Rate Lesson') }}
                                        </a>
                                    @else
                                        <span class="sp-live-card__btn sp-live-card__btn--disabled">{{ __('Lesson Finished') }}</span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="sp-live-empty">
                        <h5 class="sp-live-empty__title">{{ __('No past live lessons') }}</h5>
                        <p class="sp-live-empty__text">{{ __('Completed lessons will appear here.') }}</p>
                    </div>
                @endif
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="row">
                    <div class="col-12">
                        <div class="tab-content" id="courseTabContent">
                            @forelse ($enrolls as $enroll)
                                <div class="tab-pane fade show active" id="all-tab-pane" role="tabpanel"
                                    aria-labelledby="all-tab" tabindex="0">
                                    <div class="dashboard-courses-active dashboard_courses">
                                        <div class="courses__item courses__item-two shine__animate-item">
                                            <div class="row align-items-center">
                                                <div class="col-xl-5">
                                                    <div class="courses__item-thumb courses__item-thumb-two">
                                                        <a href="{{ route('student.learning.index', $enroll->course->slug) }}"
                                                            class="shine__animate-link">
                                                            <img src="{{ asset($enroll->course->thumbnail) }}"
                                                                alt="img">
                                                        </a>
                                                    </div>
                                                </div>
                                                <div class="col-xl-7">
                                                    <div class="courses__item-content courses__item-content-two">
                                                        <ul class="courses__item-meta list-wrap">
                                                            <li class="courses__item-tag">
                                                                <a
                                                                    href="javascript:;">{{ $enroll->course->category->translation->name }}</a>
                                                            </li>
                                                        </ul>

                                                        <h5 class="title"><a
                                                                href="{{ route('student.learning.index', $enroll->course->slug) }}">{{ $enroll->course->title }}</a>
                                                        </h5>
                                                        <div class="courses__item-content-bottom">
                                                            <div class="author-two">
                                                                <a href="javascript:;"><img
                                                                        src="{{ asset($enroll->course->instructor->image) }}"
                                                                        alt="img">{{ $enroll->course->instructor->first_name }}</a>
                                                            </div>
                                                            <div class="avg-rating">
                                                                <i class="fas fa-star"></i>
                                                                {{ number_format($enroll->course->reviews()->avg('rating') ?? 0, 1) }}
                                                            </div>
                                                        </div>
                                                        @php
                                                            $courseLectureCount = App\Models\CourseChapterItem::whereHas(
                                                                'chapter',
                                                                function ($q) use ($enroll) {
                                                                    $q->where('course_id', $enroll->course->id);
                                                                },
                                                            )->count();

                                                            $courseLectureCompletedByUser = App\Models\CourseProgress::where(
                                                                'user_id',
                                                                userAuth()->id,
                                                            )
                                                                ->where('course_id', $enroll->course->id)
                                                                ->where('watched', 1)
                                                                ->count();
                                                            $courseCompletedPercent =
                                                                $courseLectureCount > 0
                                                                    ? ($courseLectureCompletedByUser /
                                                                            $courseLectureCount) *
                                                                        100
                                                                    : 0;
                                                        @endphp
                                                        <div class="progress-item progress-item-two">
                                                            <h6 class="title">
                                                                {{ __('Complete') }}<span>{{ number_format($courseCompletedPercent, 1) }}%</span>
                                                            </h6>
                                                            <div class="progress" role="progressbar"
                                                                aria-label="Example with label" aria-valuenow="25"
                                                                aria-valuemin="0" aria-valuemax="100">
                                                                <div class="progress-bar"
                                                                    style="width: {{ number_format($courseCompletedPercent, 1) }}%">
                                                                </div>
                                                            </div>
                                                        </div>

                                                    </div>
                                                    <div class="courses__item-bottom-two">
                                                        <ul class="list-wrap">
                                                            <li><i class="flaticon-book"></i>{{ $courseLectureCount }}
                                                            </li>
                                                            <li><i
                                                                    class="flaticon-clock"></i>{{ minutesToHours($enroll->course->duration) }}
                                                            </li>
                                                            <li><i
                                                                    class="flaticon-mortarboard"></i>{{ $enroll->course->enrollments()->count() }}
                                                            </li>
                                                            @if ($courseCompletedPercent == 100)
                                                                <li class="ms-auto">
                                                                    <a class="basic-button"
                                                                        href="{{ route('student.download-certificate', $enroll->course->id) }}"><i
                                                                            class="certificate fas fa-download"></i>
                                                                        {{ __('Certificate') }}</a>
                                                                </li>
                                                            @endif
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="sp-lessons-empty">
                                    <div class="sp-lessons-empty__icon" aria-hidden="true">
                                        <i class="fas fa-frown"></i>
                                    </div>
                                    <h5 class="sp-lessons-empty__title">{{ __('No lessons found!') }}</h5>
                                    <p class="sp-lessons-empty__text">{{ __('Let us find a plan for you.') }}</p>
                                    <a href="{{ route('student.dashboard') }}#student-plans" class="sp-lessons-empty__btn">
                                        {{ __('Plans') }}
                                    </a>
                                </div>
                            @endforelse
                        </div>
                        @if ($enrolls->hasPages())
                            <div class="enroll-courses pagination__wrap mt-25">
                                {{ $enrolls->links() }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .sp-lessons__count{font-weight:900;color:#111827;background:#f9fafb;border:1px solid #e5e7eb;border-radius:999px;padding:8px 10px;}
        .sp-lessons__count strong{font-weight:1000;}

        .sp-lessons__tabs{display:flex;gap:18px;margin:8px 0 18px;padding-bottom:8px;border-bottom:1px solid #eef2f7;}
        .sp-lessons__tab{background:transparent;border:0;padding:10px 0;font-weight:900;color:#6b7280;position:relative;}
        .sp-lessons__tab.is-active{color:#f6a105;}
        .sp-lessons__tab.is-active::after{content:"";position:absolute;left:0;right:0;bottom:-9px;height:3px;background:#f6a105;border-radius:999px;}

        .sp-live{display:grid;gap:16px;margin-bottom:24px;}
        .sp-live__pane{display:none;}
        .sp-live__pane.is-active{display:block;}
        .sp-live__grid{display:grid;gap:16px;grid-template-columns:repeat(2,minmax(0,1fr));}
        .sp-live-card{display:grid;grid-template-columns:140px 1fr auto;gap:16px;align-items:center;background:#fff;border:1px solid #e5e7eb;border-radius:18px;padding:14px;box-shadow:0 10px 24px rgba(15,23,42,0.08);}
        .sp-live-card--past{opacity:0.75;}
        .sp-live-card__media{width:140px;height:90px;border-radius:14px;overflow:hidden;background:#f3f4f6;}
        .sp-live-card__media img{width:100%;height:100%;object-fit:cover;}
        .sp-live-card__meta{display:flex;gap:10px;align-items:center;margin-bottom:6px;font-weight:800;font-size:12px;color:#475569;}
        .sp-live-card__badge{background:#f6a105;color:#111827;padding:4px 8px;border-radius:999px;font-weight:900;}
        .sp-live-card__lesson{margin:0;font-weight:1000;color:#111827;font-size:18px;}
        .sp-live-card__course,.sp-live-card__teacher{margin:2px 0 0;color:#6b7280;font-weight:800;font-size:13px;}
        .sp-live-card__status{margin:4px 0 0;color:#dc2626;font-weight:900;font-size:12px;text-transform:uppercase;}
        .sp-live-card__actions{display:flex;flex-direction:column;align-items:flex-start;gap:8px;}
        .sp-live-card__btn{border-radius:999px;padding:10px 16px;font-weight:1000;background:#0e5c93;border:1px solid #0e5c93;color:#fff;text-decoration:none;white-space:nowrap;}
        .sp-live-card__btn:hover{opacity:.92;color:#fff;}
        .sp-live-card__btn--outline{background:#fff;color:#0e5c93;}
        .sp-live-card__btn--outline:hover{background:#f1f7fd;color:#0e5c93;opacity:1;}
        .sp-live-card__btn--disabled{background:#e5e7eb;border-color:#e5e7eb;color:#6b7280;cursor:not-allowed;pointer-events:none;}
        .sp-live-card__btn--disabled i{color:#f6a105;}
        .sp-live-card__cancel{margin-top:8px;}
        .sp-live-card__cancel-btn{border-radius:999px;padding:8px 12px;font-weight:900;background:#fff;border:1px solid #f6a105;color:#f6a105;}
        .sp-live-card__cancel-btn:hover{background:#fef3c7;}

        .sp-live-empty{text-align:center;padding:24px;background:#f9fafb;border:1px dashed #e5e7eb;border-radius:16px;}
        .sp-live-empty__title{margin:0 0 6px;font-weight:1000;color:#111827;}
        .sp-live-empty__text{margin:0;color:#6b7280;font-weight:800;}

        .sp-lessons-empty{padding:56px 16px;display:flex;flex-direction:column;align-items:center;justify-content:center;text-align:center;gap:8px;}
        .sp-lessons-empty__icon{width:82px;height:82px;border-radius:50%;border:4px solid #f6a105;color:#f6a105;display:grid;place-items:center;font-size:36px;margin-bottom:8px;}
        .sp-lessons-empty__title{margin:0;font-weight:1000;color:#111827;}
        .sp-lessons-empty__text{margin:0;color:#6b7280;font-weight:800;}
        .sp-lessons-empty__btn{margin-top:10px;border-radius:14px;padding:12px 18px;font-weight:1000;background:#f6a105;border:1px solid #f6a105;color:#111827;text-decoration:none;min-width:160px;}
        .sp-lessons-empty__btn:hover{opacity:.92;color:#111827;}

        @media (max-width: 991px){
            .sp-live__grid{grid-template-columns:1fr;}
            .sp-live-card{grid-template-columns:1fr;align-items:flex-start;}
            .sp-live-card__media{width:100%;}
            .sp-live-card__actions{justify-content:flex-start;}
        }
    </style>
@endpush

@push('scripts')
    <script>
        (() => {
            const tabs = document.querySelectorAll('.sp-lessons__tab[data-live-tab]');
            const panes = document.querySelectorAll('.sp-live__pane[data-live-pane]');

            if (!tabs.length || !panes.length) return;

            const activate = (target) => {
                tabs.forEach((tab) => {
                    const isActive = tab.dataset.liveTab === target;
                    tab.classList.toggle('is-active', isActive);
                    tab.setAttribute('aria-selected', isActive ? 'true' : 'false');
                });
                panes.forEach((pane) => {
                    pane.classList.toggle('is-active', pane.dataset.livePane === target);
                });
            };

            tabs.forEach((tab) => {
                tab.addEventListener('click', () => activate(tab.dataset.liveTab));
            });
        })();
    </script>
@endpush
