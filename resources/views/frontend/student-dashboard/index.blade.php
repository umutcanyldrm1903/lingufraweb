@extends('frontend.student-dashboard.layouts.master')

@section('dashboard-contents')
    @php
        $user = auth()->user();
        $plans = isset($plans) ? collect($plans) : collect();
        $upcomingLessonsPreview = isset($upcomingLessonsPreview) ? collect($upcomingLessonsPreview) : collect();

        $currentPlan = null;
        $assignedInstructor = null;

        if ($user && Schema::hasTable('user_plans')) {
            $currentPlan = \App\Models\UserPlan::query()->currentForUser((int) $user->id)->first();

            if (!empty($currentPlan?->assigned_instructor_id)) {
                $assignedInstructor = DB::table('users')
                    ->select('id', 'name')
                    ->where('id', $currentPlan->assigned_instructor_id)
                    ->first();
            }
        }

        $formatTry = function ($value) {
            if ($value === null) {
                return null;
            }

            return number_format((float) $value, 0, ',', '.') . ' TL';
        };

        $studentPhoneDigits = preg_replace('/\D+/', '', (string) ($user?->phone ?? ''));
        $displayPlans = $plans->values();
    @endphp

    <div class="sp-home">
        <h4 class="sp-welcome">{{ __('Nice to see you again, :name!', ['name' => $user?->first_name ?: $user?->name]) }}</h4>

        <div class="sp-panel">
            <div class="sp-panel__head">
                <div class="sp-panel__title">
                    <i class="far fa-calendar-check"></i>
                    {{ __('Upcoming Lessons') }}
                </div>
                <a class="sp-panel__more" href="{{ route('student.enrolled-courses') }}">{{ __('See More') }}</a>
            </div>

            @if ($upcomingLessonsPreview->isEmpty())
                <div class="sp-empty-state">
                    <i class="far fa-calendar-times"></i>
                    <div>
                        <strong>{{ __('No upcoming lessons yet') }}</strong>
                        <p>{{ __('Your confirmed lessons will appear here first.') }}</p>
                    </div>
                </div>
            @else
                <div class="sp-upcoming-grid">
                    @foreach ($upcomingLessonsPreview as $lesson)
                        <a class="sp-upcoming-card" href="{{ $lesson->join_route ?: route('student.enrolled-courses') }}">
                            <div class="sp-upcoming-card__top">
                                <span class="sp-upcoming-card__kind">{{ $lesson->kind === 'student' ? __('Private Lesson') : __('Course Lesson') }}</span>
                                <span class="sp-upcoming-card__date">{{ $lesson->start_time ? formattedDateTime($lesson->start_time) : '-' }}</span>
                            </div>
                            <div class="sp-upcoming-card__title">{{ $lesson->title }}</div>
                            <div class="sp-upcoming-card__meta">
                                <span>{{ $lesson->course_title }}</span>
                                <strong>{{ $lesson->instructor_name }}</strong>
                            </div>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>

        <div class="sp-panel">
            <div class="sp-panel__head">
                <div class="sp-panel__title">
                    <i class="fas fa-book"></i>
                    {{ __('Library') }}
                </div>
            </div>

            <div class="sp-note sp-note--action">
                <i class="fas fa-folder-open"></i>
                <span>{{ __('Use the library section for grammar, reading, IELTS and vocabulary resources.') }}</span>
                <a href="{{ route('student.library.index') }}" class="sp-primary-btn">{{ __('Open Library') }}</a>
            </div>
        </div>

        <div class="row g-3">
            <div class="col-lg-6">
                <div class="sp-panel">
                    <div class="sp-panel__head">
                        <div class="sp-panel__title">
                            <i class="far fa-bell"></i>
                            {{ __('Notifications') }}
                        </div>
                    </div>
                    <div class="sp-note">
                        <i class="fas fa-bell"></i>
                        <span>{{ __('Your notifications will appear here.') }}</span>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="sp-panel">
                    <div class="sp-panel__head">
                        <div class="sp-panel__title">
                            <i class="fas fa-bullhorn"></i>
                            {{ __('Announcements') }}
                        </div>
                    </div>
                    <div class="sp-note">
                        <i class="fas fa-bullhorn"></i>
                        <span>{{ __('Announcements will appear here.') }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="sp-panel sp-panel--plan mt-3">
            <div class="sp-panel__head">
                <div class="sp-panel__title">
                    <i class="fas fa-id-badge"></i>
                    {{ __('My Plan') }}
                </div>
            </div>

            @php
                $hasPlanOrCredits = $currentPlan
                    && (
                        trim((string) ($currentPlan->plan_key ?? '')) !== ''
                        || (int) ($currentPlan->lessons_remaining ?? 0) > 0
                        || (int) ($currentPlan->cancel_remaining ?? 0) > 0
                    );
            @endphp

            @if ($hasPlanOrCredits)
                <div class="sp-plan__active">
                    <div class="sp-plan__stats">
                        <div class="sp-plan__stat">
                            <span>{{ __('Plan') }}</span>
                            <strong>{{ $currentPlan->plan_title ?: __('No Plan') }}</strong>
                        </div>
                        <div class="sp-plan__stat">
                            <span>{{ __('Credits') }}</span>
                            <strong>{{ (int) ($currentPlan->lessons_remaining ?? 0) }}</strong> {{ __('Lessons') }}
                        </div>
                        <div class="sp-plan__stat">
                            <span>{{ __('Cancellation Right') }}</span>
                            <strong>{{ (int) ($currentPlan->cancel_remaining ?? 0) }}</strong> {{ __('Lessons') }}
                        </div>
                        <div class="sp-plan__stat">
                            <span>{{ __('Instructor') }}</span>
                            <strong>{{ $assignedInstructor?->name ? (\Illuminate\Support\Str::before($assignedInstructor->name, ' ') ?: $assignedInstructor->name) : __('Not Assigned') }}</strong>
                        </div>
                    </div>
                </div>
            @else
                <div class="sp-plan__empty">
                    <div class="sp-plan__coin" aria-hidden="true">
                        <i class="fas fa-coins"></i>
                    </div>
                    <div class="sp-plan__text">{{ __('Let us find a plan that suits you.') }}</div>
                    <div class="sp-plan__empty-actions">
                        <a href="{{ route('student.invite') }}" class="ce-cow-btn ce-cow-btn--accent">{{ __('Get Free Lessons') }}</a>
                        @if ($studentPhoneDigits === '')
                            <a href="{{ route('student.setting.index') }}" class="ce-cow-btn">{{ __('Schedule Trial Lesson') }}</a>
                        @else
                            <button type="button" class="ce-cow-btn" data-open-trial-modal>{{ __('Schedule Trial Lesson') }}</button>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>

    @if (!$currentPlan?->plan_key)
        <div class="sp-plans" id="student-plans">
            <div class="sp-plans__head">
                <div>
                    <h4 class="sp-plans__title">{{ __('Lesson Packages') }}</h4>
                    <p class="sp-plans__subtitle">{{ __('Choose the package that matches your weekly lesson plan.') }}</p>
                </div>
            </div>

            <div class="sp-plans__grid">
                @foreach ($displayPlans as $plan)
                    @php
                        $price = (float) ($plan->price ?? 0);
                        $lessonsTotal = (int) ($plan->lessons_total ?? 0);
                        $pricePerLesson = $lessonsTotal > 0 ? $price / $lessonsTotal : 0;
                    @endphp

                    <div class="sp-plan-card">
                        <h5 class="sp-plan-card__title">{{ __($plan->display_title ?? $plan->title ?? '') }}</h5>
                        <div class="sp-plan-card__price">{{ $formatTry($price) }}</div>
                        <div class="sp-plan-card__price-sub">{{ __('Per lesson') }}: {{ $formatTry($pricePerLesson) }}</div>
                        <div class="sp-plan-card__metric">{{ $lessonsTotal }} {{ __('Lessons') }}</div>

                        <form method="POST" action="{{ route('student.plans.cart.add') }}" class="sp-plan-card__buy">
                            @csrf
                            <input type="hidden" name="plan_key" value="{{ $plan->key ?? '' }}">
                            <button type="submit" class="sp-plan-card__btn">{{ __('Start Now') }}</button>
                        </form>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
@endsection

@push('styles')
    <style>
        .sp-welcome{margin:0 0 14px;font-weight:1000;color:#111827;}
        .sp-panel{background:#fff;border:1px solid #eef2f7;border-radius:18px;padding:16px;box-shadow:0 10px 24px rgba(0,0,0,0.05);margin-bottom:16px;}
        .sp-panel__head{display:flex;justify-content:space-between;align-items:center;gap:12px;margin-bottom:12px;}
        .sp-panel__title{display:flex;align-items:center;gap:10px;font-weight:1000;color:#111827;}
        .sp-panel__title i{color:#f6a105;}
        .sp-panel__more{background:#f6a105;border:1px solid #f6a105;color:#111827;font-weight:1000;padding:8px 14px;border-radius:999px;text-decoration:none;}
        .sp-panel__more:hover{opacity:.92;color:#111827;}

        .sp-upcoming-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:12px;}
        .sp-upcoming-card{display:grid;gap:10px;padding:16px;border-radius:16px;border:1px solid #e5e7eb;background:#f9fafb;text-decoration:none;color:#111827;box-shadow:0 10px 24px rgba(15,23,42,0.06);}
        .sp-upcoming-card:hover{transform:translateY(-1px);box-shadow:0 16px 30px rgba(15,23,42,0.08);color:#111827;}
        .sp-upcoming-card__top{display:flex;align-items:center;justify-content:space-between;gap:8px;flex-wrap:wrap;}
        .sp-upcoming-card__kind{display:inline-flex;align-items:center;padding:6px 10px;border-radius:999px;background:#fff7e6;border:1px solid rgba(246,161,5,.35);font-size:11px;font-weight:1000;color:#111827;text-transform:uppercase;letter-spacing:.05em;}
        .sp-upcoming-card__date{font-size:12px;font-weight:900;color:#6b7280;}
        .sp-upcoming-card__title{font-size:18px;font-weight:1000;line-height:1.2;}
        .sp-upcoming-card__meta{display:flex;justify-content:space-between;gap:10px;flex-wrap:wrap;color:#6b7280;font-weight:800;}
        .sp-upcoming-card__meta strong{color:#111827;font-weight:1000;}

        .sp-empty-state{display:flex;align-items:center;gap:12px;min-height:110px;padding:8px 4px;}
        .sp-empty-state i{width:52px;height:52px;border-radius:50%;display:grid;place-items:center;background:#fff7e6;color:#f6a105;font-size:20px;}
        .sp-empty-state strong{display:block;color:#111827;font-weight:1000;}
        .sp-empty-state p{margin:4px 0 0;color:#6b7280;font-weight:800;}

        .sp-note{display:flex;align-items:flex-start;gap:10px;color:#6b7280;font-weight:900;}
        .sp-note i{color:#f6a105;margin-top:2px;}
        .sp-note--action{align-items:center;justify-content:space-between;flex-wrap:wrap;}
        .sp-note--action span{flex:1 1 auto;}
        .sp-primary-btn{display:inline-block;background:#f6a105;border:1px solid #f6a105;color:#111827;font-weight:1000;padding:10px 18px;border-radius:14px;text-decoration:none;min-width:160px;text-align:center;cursor:pointer;}
        .sp-primary-btn:hover{opacity:.92;color:#111827;}

        .sp-panel--plan{margin-bottom:24px;}
        .sp-plan__active{display:flex;align-items:center;justify-content:space-between;gap:14px;flex-wrap:wrap;}
        .sp-plan__stats{display:flex;gap:12px;flex-wrap:wrap;}
        .sp-plan__stat{background:#f9fafb;border:1px solid #e5e7eb;border-radius:14px;padding:10px 12px;font-weight:900;color:#111827;}
        .sp-plan__stat span{display:block;color:#6b7280;font-weight:1000;font-size:12px;margin-bottom:2px;}
        .sp-plan__stat strong{font-weight:1000;}
        .sp-plan__empty{display:flex;flex-direction:column;align-items:center;justify-content:center;text-align:center;padding:18px 10px;gap:10px;}
        .sp-plan__empty-actions{display:flex;gap:10px;flex-wrap:wrap;justify-content:center;}
        .sp-plan__coin{width:96px;height:96px;border-radius:50%;border:2px dashed rgba(246,161,5,.85);background:#fff7e6;color:#f6a105;display:grid;place-items:center;font-size:40px;box-shadow:0 14px 28px rgba(0,0,0,0.08);}
        .sp-plan__text{font-weight:1000;color:#111827;}

        .ce-cow-btn{border-radius:12px;padding:10px 16px;font-weight:1000;border:1px solid #e5e7eb;background:#fff;color:#0f172a;text-decoration:none;display:inline-flex;align-items:center;justify-content:center;min-width:170px;cursor:pointer;}
        .ce-cow-btn:hover{background:#f9fafb;color:#0f172a;}
        .ce-cow-btn--accent{border:2px solid #f6a105;color:#f6a105;background:#fff;}
        .ce-cow-btn--accent:hover{background:#fff7e6;color:#f6a105;}

        .sp-plans{margin-top:18px;background:#eef5ff;border:1px solid #dbe7f5;border-radius:24px;padding:28px;box-shadow:0 22px 60px rgba(15,23,42,.12);}
        .sp-plans__head{text-align:center;margin-bottom:20px;}
        .sp-plans__title{margin:0;font-weight:1000;color:#111827;}
        .sp-plans__subtitle{margin:6px 0 0;color:#6b7280;font-weight:800;}
        .sp-plans__grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:16px;}
        .sp-plan-card{display:grid;gap:10px;padding:18px;border-radius:20px;background:#fff;border:1px solid #dbe7f5;box-shadow:0 14px 34px rgba(15,23,42,.08);text-align:center;}
        .sp-plan-card__title{margin:0;font-weight:1000;color:#111827;}
        .sp-plan-card__price{font-size:28px;font-weight:1000;color:#0e5c93;}
        .sp-plan-card__price-sub{color:#6b7280;font-weight:800;}
        .sp-plan-card__metric{display:inline-flex;justify-content:center;align-items:center;padding:8px 12px;border-radius:999px;background:#fff7e6;border:1px solid rgba(246,161,5,.35);font-weight:1000;color:#111827;}
        .sp-plan-card__buy{margin-top:4px;}
        .sp-plan-card__btn{width:100%;border-radius:999px;padding:10px 14px;font-weight:1000;background:#0e5c93;border:1px solid #0e5c93;color:#fff;}

        @media (max-width: 991.98px){
            .sp-upcoming-grid{grid-template-columns:1fr;}
            .sp-plans__grid{grid-template-columns:repeat(2,minmax(0,1fr));}
        }

        @media (max-width: 575.98px){
            .sp-plans{padding:18px;}
            .sp-plans__grid{grid-template-columns:1fr;}
            .sp-panel__head{align-items:flex-start;}
            .sp-panel__more{width:100%;text-align:center;}
        }
    </style>
@endpush
