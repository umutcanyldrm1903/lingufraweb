@extends('frontend.student-dashboard.layouts.master')

@section('dashboard-contents')
    @php
        $plans = isset($plans) ? collect($plans)->values() : collect();
        $selectedPlanKey = (string) ($selectedPlanKey ?? request()->query('plan', ''));

        $formatTry = function ($value) {
            if ($value === null) {
                return null;
            }
            return number_format((float) $value, 0, ',', '.') . ' TL';
        };

        $check = fn () => '<span class="sp-compare-check"><i class="fas fa-check"></i></span>';
        $dash = fn () => '<span class="sp-compare-na">&mdash;</span>';

        $getProgramDuration = function ($plan) use ($dash) {
            $months = (int) ($plan->duration_months ?? 0);
            return $months > 0 ? $months . ' ' . __('Months') : $dash();
        };

        $getTotalMinutes = function ($plan) use ($dash) {
            $lessons = (int) ($plan->lessons_total ?? 0);
            $minsPerLesson = 40;
            if ($lessons <= 0) {
                return $dash();
            }
            return number_format(max(0, $lessons) * $minsPerLesson, 0, ',', '.') . ' ' . __('min');
        };

        $getWeeklyLessons = function ($plan) use ($dash) {
            $months = (int) ($plan->duration_months ?? 0);
            $lessons = (int) ($plan->lessons_total ?? 0);
            if ($months <= 0 || $lessons <= 0) {
                return $dash();
            }

            if ($months <= 3) {
                return __('1-2 Lessons');
            }

            if ($months <= 6) {
                return __('1-3 Lessons');
            }

            $weeklyAvg = $lessons / ($months * 4);
            if ($weeklyAvg >= 3) {
                return __('3 Lessons');
            }
            return __('2 Lessons');
        };

        $getCancelRight = function ($plan) use ($dash) {
            $cancel = (int) ($plan->cancel_total ?? 0);
            return $cancel > 0 ? $cancel . ' ' . __('Lessons') : $dash();
        };

        $isPremium = function ($plan) {
            $title = (string) ($plan->title ?? '');
            $display = (string) ($plan->display_title ?? '');
            $hay = mb_strtoupper($title . ' ' . $display, 'UTF-8');
            return \Illuminate\Support\Str::contains($hay, 'PREMIUM');
        };

        $getFreezeRight = function ($plan) use ($dash, $isPremium) {
            return $isPremium($plan) ? '1 ' . __('Month') : $dash();
        };

        $getPerLesson = function ($plan) use ($formatTry, $dash) {
            $price = (float) ($plan->price ?? 0);
            $lessons = (int) ($plan->lessons_total ?? 0);
            if ($price <= 0 || $lessons <= 0) {
                return $dash();
            }
            return $formatTry($price / $lessons);
        };

        $features = [
            ['label' => 'Program Duration', 'value' => $getProgramDuration],
            ['label' => 'Total Minutes', 'value' => $getTotalMinutes],
            ['label' => '1-on-1 Private Lessons', 'value' => fn () => $check()],
            ['label' => 'Lesson Duration', 'value' => fn () => '40 ' . __('min')],
            ['label' => 'Weekly Lessons', 'value' => $getWeeklyLessons],
            ['label' => 'Cancellation Right', 'value' => $getCancelRight],
            ['label' => 'Freeze Right', 'value' => $getFreezeRight],
            // "Usage Period" removed
            ['label' => 'Free Trial Lesson', 'value' => fn () => $check()],
            ['label' => 'Installment Options', 'value' => fn () => __('3-9 Months')],
            ['label' => 'Single Lesson', 'value' => $getPerLesson],
            ['label' => 'Flexible Lesson Scheduling', 'value' => fn () => $check()],
            ['label' => 'Personalized Lesson Content', 'value' => fn () => $check()],
            ['label' => 'Approved Foreign/Turkish Instructors', 'value' => fn () => $check()],
            ['label' => 'Access to All Instructors', 'value' => fn () => $check()],
            ['label' => 'Choose / Change Instructor', 'value' => fn () => $check()],
            ['label' => 'Direct Instructor Communication', 'value' => fn () => $check()],
            ['label' => 'Certificate', 'value' => fn () => $check()],
            ['label' => 'Corporate Invoice', 'value' => fn () => $check()],
            ['label' => 'Homework System', 'value' => fn () => $check()],
            ['label' => 'All Materials Included', 'value' => fn () => $check()],
            ['label' => 'Dedicated Student Support Team', 'value' => fn () => $check()],
        ];
    @endphp

    <div class="sp-compare">
        <div class="sp-compare__top">
            <a href="{{ route('student.dashboard') }}#student-plans" class="sp-compare__back">
                <i class="fas fa-arrow-left"></i> {{ __('Back') }}
            </a>
            <h3 class="sp-compare__title">{{ __('Features') }}</h3>
        </div>

        <div class="sp-compare__wrap">
            <div class="sp-compare__grid" style="--plan-cols: {{ max(1, $plans->count()) }};">
                <div class="sp-compare__cell sp-compare__cell--feature sp-compare__cell--head"></div>

                @foreach ($plans as $plan)
                    @php
                        $duration = (int) ($plan->duration_months ?? 0);
                        $lessons = (int) ($plan->lessons_total ?? 0);
                        $price = (float) ($plan->price ?? 0);
                        $oldPrice = (float) ($plan->old_price ?? 0);
                        $isSelected = $selectedPlanKey !== '' && (string) ($plan->key ?? '') === $selectedPlanKey;
                        $hasLabel = !empty($plan->label);
                    @endphp
                    <div class="sp-compare__cell sp-compare__cell--planhead {{ $isSelected ? 'is-selected' : '' }}"
                        id="plan-{{ $plan->key ?? '' }}">
                        @if ($hasLabel)
                            <div class="sp-compare__badge">{{ __($plan->label) }}</div>
                        @endif
                        <div class="sp-compare__planbox">
                            <div class="sp-compare__planname">{{ $plan->display_title ?? $plan->title ?? '' }}</div>
                            <div class="sp-compare__plansub">
                                {{ $duration > 0 ? $duration . ' ' . __('Months') : '' }}
                                @if ($duration > 0 && $lessons > 0)
                                    &bull;
                                @endif
                                {{ $lessons > 0 ? $lessons . ' ' . __('Lessons') : '' }}
                            </div>
                            @if ($price > 0)
                                <div class="sp-compare__planprice">
                                    @if ($oldPrice > 0)
                                        <span class="sp-compare__old">{{ $formatTry($oldPrice) }}</span>
                                    @endif
                                    <span class="sp-compare__new">{{ $formatTry($price) }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach

                @foreach ($features as $feature)
                    <div class="sp-compare__cell sp-compare__cell--feature">{{ __($feature['label']) }}</div>
                    @foreach ($plans as $plan)
                        @php
                            $value = $feature['value']($plan);
                        @endphp
                        <div class="sp-compare__cell sp-compare__cell--value">{!! $value !!}</div>
                    @endforeach
                @endforeach

                <div class="sp-compare__cell sp-compare__cell--feature sp-compare__cell--actions"></div>
                @foreach ($plans as $plan)
                    <div class="sp-compare__cell sp-compare__cell--value sp-compare__cell--actions">
                        <form method="POST" action="{{ route('student.plans.cart.add') }}" class="m-0">
                            @csrf
                            <input type="hidden" name="plan_key" value="{{ $plan->key ?? '' }}">
                            <button type="submit" class="sp-compare__start">{{ __('Start') }}</button>
                        </form>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .sp-compare__top{display:flex;align-items:center;gap:14px;flex-wrap:wrap;margin-bottom:14px;}
        .sp-compare__back{display:inline-flex;align-items:center;gap:8px;border:1px solid rgba(246,161,5,.55);background:#fff7e6;color:#111827;font-weight:1000;padding:10px 12px;border-radius:12px;text-decoration:none;}
        .sp-compare__back:hover{opacity:.92;color:#111827;}
        .sp-compare__title{margin:0;font-weight:1100;color:#111827;letter-spacing:-.02em;}

        .sp-compare__wrap{overflow-x:auto;padding-bottom:6px;}
        .sp-compare__grid{
            --feature-col: 260px;
            display:grid;
            grid-template-columns: var(--feature-col) repeat(var(--plan-cols), minmax(220px, 1fr));
            gap:10px;
            min-width: calc(var(--feature-col) + (var(--plan-cols) * 220px));
            align-items:stretch;
        }

        .sp-compare__cell{background:#fff;border:1px solid #eef2f7;border-radius:14px;padding:12px;box-shadow:0 10px 22px rgba(0,0,0,0.05);color:#111827;font-weight:900;}
        .sp-compare__cell--feature{background:#fafafa;color:#111827;display:flex;align-items:center;}
        .sp-compare__cell--value{display:flex;align-items:center;justify-content:center;text-align:center;}

        .sp-compare__cell--planhead{position:relative;padding:14px;background:linear-gradient(180deg,#fff, #fff7e6);}
        .sp-compare__cell--planhead.is-selected{outline:3px solid rgba(14,92,147,.35);border-color:rgba(14,92,147,.55);}
        .sp-compare__badge{position:absolute;top:-12px;left:14px;background:#0e5c93;color:#fff;font-weight:1100;font-size:12px;padding:6px 10px;border-radius:999px;box-shadow:0 10px 20px rgba(14,92,147,.18);}
        .sp-compare__planbox{display:flex;flex-direction:column;gap:6px;}
        .sp-compare__planname{font-weight:1200;font-size:18px;letter-spacing:-.02em;line-height:1.1;}
        .sp-compare__plansub{color:#6b7280;font-weight:1000;font-size:12px;}
        .sp-compare__planprice{display:flex;align-items:baseline;gap:10px;flex-wrap:wrap;}
        .sp-compare__old{color:#ef4444;text-decoration:line-through;font-weight:1100;}
        .sp-compare__new{color:#111827;font-weight:1200;font-size:18px;}

        .sp-compare-check{width:28px;height:28px;border-radius:999px;background:rgba(246,161,5,.18);border:1px solid rgba(246,161,5,.45);display:grid;place-items:center;}
        .sp-compare-check i{color:#111827;font-size:12px;}
        .sp-compare-na{color:#9ca3af;font-weight:1100;}

        .sp-compare__cell--actions{background:transparent;border:none;box-shadow:none;padding:0;}
        .sp-compare__start{width:100%;border-radius:14px;padding:12px 14px;font-weight:1100;background:#f6a105;border:1px solid #f6a105;color:#111827;}
        .sp-compare__start:hover{opacity:.92;}

        @media(max-width:575.98px){
            .sp-compare__grid{--feature-col: 200px;min-width: calc(var(--feature-col) + (var(--plan-cols) * 200px));}
        }
    </style>
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var key = @json($selectedPlanKey);
            if (!key) return;

            var element = document.getElementById('plan-' + key);
            if (!element) return;

            element.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' });
        });
    </script>
@endpush
