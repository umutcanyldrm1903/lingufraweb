@extends('frontend.student-dashboard.layouts.master')

@section('dashboard-contents')
    @php
        $user = auth()->user();
        $plans = isset($plans) ? collect($plans) : collect();

        $currentPlan = null;
        $assignedInstructor = null;

        if ($user && Schema::hasTable('user_plans')) {
            $currentPlan = DB::table('user_plans')->where('user_id', $user->id)->first();

            if (!empty($currentPlan?->assigned_instructor_id)) {
                $assignedInstructorName = DB::table('users')->where('id', $currentPlan->assigned_instructor_id)->value('name');
                if ($assignedInstructorName) {
                    $assignedInstructor = (object) ['name' => $assignedInstructorName];
                }
            }
        }

        $formatTry = function ($value) {
            if ($value === null) {
                return null;
            }
            return number_format((float) $value, 0, ',', '.') . ' TL';
        };

        $studentPhoneDigits = preg_replace('/\D+/', '', (string) ($user?->phone ?? ''));
    @endphp

    <div class="sp-home">
        <h4 class="sp-welcome">{{ __('Nice to see you again, :name!', ['name' => $user?->name]) }}</h4>

        <div class="sp-panel sp-panel--library">
            <div class="sp-panel__head">
                <div class="sp-panel__title">
                    <i class="fas fa-book"></i>
                    {{ __('Library') }}
                </div>
                <a class="sp-panel__more" href="{{ route('student.library.index') }}">{{ __('See More') }}</a>
            </div>
            @php
                $libraryItems = [
                    ['title' => 'Vocabulary', 'color' => '#dbe7f5', 'icon' => 'fas fa-book-open'],
                    ['title' => 'Grammar', 'color' => '#f6e9e2', 'icon' => 'fas fa-chalkboard'],
                    ['title' => 'Listening', 'color' => '#f5cfd1', 'icon' => 'fas fa-headphones'],
                    ['title' => 'Reading & Writing', 'color' => '#f2d9ae', 'icon' => 'fas fa-pen-nib'],
                    ['title' => 'Practice', 'color' => '#e7f3dd', 'icon' => 'fas fa-dumbbell'],
                    ['title' => 'IELTS & TOEFL', 'color' => '#f5d6e4', 'icon' => 'fas fa-globe'],
                    ['title' => 'English for Kids', 'color' => '#f6d7d7', 'icon' => 'fas fa-child'],
                    ['title' => 'General English', 'color' => '#d5e8ed', 'icon' => 'fas fa-comments'],
                    ['title' => 'Story Books', 'color' => '#d7ead9', 'icon' => 'fas fa-umbrella'],
                ];
            @endphp
            <div class="sp-library__grid">
                @foreach ($libraryItems as $item)
                    <a class="sp-libcard" href="{{ route('student.library.index') }}" style="background: {{ $item['color'] }}">
                        <div class="sp-libcard__text">{{ __($item['title']) }}</div>
                        <div class="sp-libcard__icon"><i class="{{ $item['icon'] }}"></i></div>
                    </a>
                @endforeach
            </div>
        </div>

        <div class="sp-panel">
            <div class="sp-panel__head">
                <div class="sp-panel__title">
                    <i class="far fa-calendar-alt"></i>
                    {{ __('Upcoming Lessons') }}
                </div>
            </div>
            <div class="sp-note sp-note--action">
                <i class="fas fa-video"></i>
                <span>{{ __('Canli derslerinizi Derslerim sayfasindan takip edebilirsiniz.') }}</span>
                <a href="{{ route('student.enrolled-courses') }}" class="sp-primary-btn">{{ __('Derse Katil') }}</a>
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
                $hasPlanOrCredits =
                    $currentPlan &&
                    (trim((string) ($currentPlan->plan_key ?? '')) !== '' ||
                        (int) ($currentPlan->lessons_remaining ?? 0) > 0 ||
                        (int) ($currentPlan->cancel_remaining ?? 0) > 0);
            @endphp

            @if ($hasPlanOrCredits)
                <div class="sp-plan__active">
                    <div class="sp-plan__stats">
                        <div class="sp-plan__stat">
                            <span>{{ __('Plan') }}</span>
                            <strong>{{ $currentPlan->plan_title ?: __('Plan Yok') }}</strong>
                        </div>
                        <div class="sp-plan__stat">
                            <span>{{ __('Credits') }}</span>
                            <strong>{{ $currentPlan->lessons_remaining }}</strong> {{ __('Lessons') }}
                        </div>
                        <div class="sp-plan__stat">
                            <span>{{ __('Cancellation Right') }}</span>
                            <strong>{{ $currentPlan->cancel_remaining }}</strong> {{ __('Lessons') }}
                        </div>
                        <div class="sp-plan__stat">
                            <span>{{ __('Instructor') }}</span>
                            <strong>{{ $assignedInstructor?->name ?: __('Not Assigned') }}</strong>
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
        <div class="sp-plans sp-plans--galaxy" id="student-plans">
            <div class="sp-plans__head">
                <div>
                    <h4 class="sp-plans__title">{{ __('Ders Paketleri') }}</h4>
                    <p class="sp-plans__subtitle">{{ __('Premium Paket - En avantajli ders basi fiyat - Onerilen secim') }}</p>
                </div>
            </div>

            @php
                $displayPlans = collect($plans)->values();
                $premiumPlan = $displayPlans->first(function ($plan) {
                    $title = strtolower((string) ($plan->display_title ?? $plan->title ?? ''));
                    return str_contains($title, 'premium');
                });
                if ($premiumPlan) {
                    $others = $displayPlans->reject(function ($plan) use ($premiumPlan) {
                        return (string) ($plan->key ?? '') === (string) ($premiumPlan->key ?? '');
                    })->values();
                    if ($others->isNotEmpty()) {
                        $others = $others->sortByDesc(function ($plan) {
                            return (int) ($plan->lessons_total ?? 0);
                        })->values();
                        $displayPlans = collect();
                        $displayPlans->push($others->shift());
                        $displayPlans->push($premiumPlan);
                        foreach ($others as $other) {
                            $displayPlans->push($other);
                        }
                    } else {
                        $displayPlans = collect([$premiumPlan]);
                    }
                }
            @endphp

            <div class="sp-plans__grid">
                @foreach ($displayPlans as $plan)
                    @php
                        $price = (float) ($plan->price ?? 0);
                        $lessonsTotal = (int) ($plan->lessons_total ?? 0);
                        $pricePerLesson = $lessonsTotal > 0 ? $price / $lessonsTotal : 0;
                        $isPremium = $premiumPlan && ((string) ($plan->key ?? '') === (string) ($premiumPlan->key ?? ''));
                        $isFeatured = $isPremium || (!$premiumPlan && (bool) ($plan->featured ?? false));
                        $toneClass = '';
                        if ($loop->first) {
                            $toneClass = 'sp-plan-card--warm';
                        } elseif ($loop->last) {
                            $toneClass = 'sp-plan-card--stone';
                        }
                    @endphp

                    <div class="sp-plan-card {{ $isFeatured ? 'is-featured' : '' }} {{ $toneClass }}" id="plan-{{ $plan->key ?? '' }}">
                        <h5 class="sp-plan-card__title">{{ __($plan->display_title ?? $plan->title ?? '') }}</h5>
                        @if ($isFeatured)
                            <div class="sp-plan-card__chips">
                                <span class="sp-plan-card__chip sp-plan-card__chip--primary">{{ __('En Populer') }}</span>
                                <span class="sp-plan-card__chip">{{ __('En Avantajli') }}</span>
                            </div>
                        @endif

                        <div class="sp-plan-card__panel">
                            <div class="sp-plan-card__gem" aria-hidden="true"></div>
                            <div class="sp-plan-card__price-label">{{ __('Tek ders') }}:</div>
                            <div class="sp-plan-card__price">{{ $formatTry($pricePerLesson) }}</div>
                            <div class="sp-plan-card__total-label">{{ __('Toplam') }}:</div>
                            <div class="sp-plan-card__total">{{ $formatTry($price) }}</div>
                            <div class="sp-plan-card__tax">({{ __('KDV DAHIL') }})</div>

                            @if ($isPremium)
                                <div class="sp-plan-card__note">{{ __('Premium Paket') }} - {{ __('En avantajli ders basi fiyat') }} - {{ __('Onerilen secim') }}</div>
                            @elseif ($isFeatured && !empty($plan->tagline))
                                <div class="sp-plan-card__note">{!! nl2br(e(__($plan->tagline))) !!}</div>
                            @endif

                            <form method="POST" action="{{ route('student.plans.cart.add') }}" class="sp-plan-card__buy">
                                @csrf
                                <input type="hidden" name="plan_key" value="{{ $plan->key ?? '' }}">
                                <button type="submit" class="sp-plan-card__btn">{{ __('Hemen Basla') }}</button>
                            </form>
                        </div>

                        <div class="sp-plan-card__lessons">{{ $lessonsTotal }} {{ __('Ders') }}</div>
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

        .sp-teachers__list{display:flex;gap:14px;overflow-x:auto;padding-bottom:4px;}
        .sp-teacher{flex:0 0 auto;width:110px;background:#f6a105;border-radius:16px;padding:14px 10px;display:flex;flex-direction:column;align-items:center;gap:10px;text-decoration:none;box-shadow:0 10px 24px rgba(246,161,5,.22);}
        .sp-teacher__avatar{width:72px;height:72px;border-radius:50%;overflow:hidden;border:4px solid rgba(255,255,255,.9);box-shadow:0 10px 18px rgba(0,0,0,0.10);}
        .sp-teacher__avatar img{width:100%;height:100%;object-fit:cover;}
        .sp-teacher__name{font-weight:1000;color:#111827;text-align:center;font-size:12px;line-height:1.2;}

        .sp-library__grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:12px;}
        .sp-libcard{border-radius:16px;padding:16px;display:flex;justify-content:space-between;align-items:center;gap:14px;min-height:88px;text-decoration:none;color:#111827;border:1px solid rgba(17,24,39,.06);}
        .sp-libcard:hover{transform:translateY(-1px);box-shadow:0 14px 26px rgba(0,0,0,0.06);color:#111827;}
        .sp-libcard__text{font-weight:1000;}
        .sp-libcard__icon{width:56px;height:56px;border-radius:14px;background:rgba(255,255,255,.75);display:grid;place-items:center;font-size:24px;box-shadow:0 10px 18px rgba(0,0,0,0.06);}
        .sp-libcard__icon i{color:#111827;opacity:.9;}

        .sp-note{display:flex;align-items:flex-start;gap:10px;color:#6b7280;font-weight:900;}
        .sp-note i{color:#f6a105;margin-top:2px;}
        .sp-note--action{align-items:center;justify-content:space-between;flex-wrap:wrap;}
        .sp-note--action span{flex:1 1 auto;}
        .sp-note--action .sp-primary-btn{margin-left:auto;}

        .sp-primary-btn{display:inline-block;background:#f6a105;border:1px solid #f6a105;color:#111827;font-weight:1000;padding:10px 18px;border-radius:14px;text-decoration:none;min-width:160px;text-align:center;cursor:pointer;}
        .sp-primary-btn:hover{opacity:.92;color:#111827;}

        .sp-panel--plan{margin-bottom:24px;}
        .sp-plan__empty{display:flex;flex-direction:column;align-items:center;justify-content:center;text-align:center;padding:18px 10px;gap:10px;}
        .sp-plan__empty-actions{display:flex;gap:10px;flex-wrap:wrap;justify-content:center;}
        .ce-cow-btn{
            border-radius:12px;
            padding:10px 16px;
            font-weight:1000;
            border:1px solid #e5e7eb;
            background:#fff;
            color:#0f172a;
            text-decoration:none;
            display:inline-flex;
            align-items:center;
            justify-content:center;
            min-width:170px;
            cursor:pointer;
        }
        .ce-cow-btn:hover{background:#f9fafb;color:#0f172a;}
        .ce-cow-btn--accent{border:2px solid #f6a105;color:#f6a105;background:#fff;}
        .ce-cow-btn--accent:hover{background:#fff7e6;color:#f6a105;}

        .sp-plan__coin{width:96px;height:96px;border-radius:50%;border:2px dashed rgba(246,161,5,.85);background:#fff7e6;color:#f6a105;display:grid;place-items:center;font-size:40px;box-shadow:0 14px 28px rgba(0,0,0,0.08);}
        .sp-plan__text{font-weight:1000;color:#111827;}
        .sp-plan__active{display:flex;align-items:center;justify-content:space-between;gap:14px;flex-wrap:wrap;}
        .sp-plan__stats{display:flex;gap:12px;flex-wrap:wrap;}
        .sp-plan__stat{background:#f9fafb;border:1px solid #e5e7eb;border-radius:14px;padding:10px 12px;font-weight:900;color:#111827;}
        .sp-plan__stat span{display:block;color:#6b7280;font-weight:1000;font-size:12px;margin-bottom:2px;}
        .sp-plan__stat strong{font-weight:1000;}

        .sp-plans{position:relative;background:radial-gradient(900px 360px at 15% 0, #d8f2ff 0, transparent 60%),radial-gradient(900px 360px at 85% 5%, #f6e7ff 0, transparent 60%),#eef5ff;border-radius:28px;padding:40px 24px 50px;scroll-margin-top:120px;border:1px solid #dbe7f5;box-shadow:0 30px 80px rgba(15,23,42,0.18);overflow:hidden;color:#0f172a;}
        .sp-plans::before{content:"";position:absolute;inset:0;background:
            radial-gradient(2px 2px at 40px 60px, rgba(255,255,255,.7) 50%, transparent 55%),
            radial-gradient(2px 2px at 160px 120px, rgba(255,255,255,.55) 50%, transparent 55%),
            radial-gradient(2px 2px at 260px 40px, rgba(255,255,255,.6) 50%, transparent 55%),
            radial-gradient(2px 2px at 380px 150px, rgba(255,255,255,.5) 50%, transparent 55%),
            radial-gradient(2px 2px at 520px 80px, rgba(255,255,255,.6) 50%, transparent 55%),
            radial-gradient(2px 2px at 660px 160px, rgba(255,255,255,.45) 50%, transparent 55%),
            radial-gradient(2px 2px at 780px 30px, rgba(255,255,255,.6) 50%, transparent 55%);
            opacity:.6;}
        .sp-plans::after{content:"";position:absolute;inset:auto 0 0;height:180px;background:radial-gradient(600px 220px at 50% 0, rgba(255,255,255,.7), transparent 70%);opacity:.7;}
        .sp-plans > *{position:relative;z-index:1;}
        .sp-plans__head{display:flex;justify-content:center;align-items:center;text-align:center;gap:12px;flex-wrap:wrap;margin-bottom:28px;}
        .sp-plans__title{margin:0;font-weight:1000;color:#1f2937;font-size:34px;letter-spacing:.04em;text-shadow:0 10px 24px rgba(15,23,42,.18);}
        .sp-plans__subtitle{margin:4px 0 0;color:#475569;font-weight:800;}

        .sp-plans__grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:24px;align-items:end;}
        .sp-plan-card{position:relative;border:1px solid #dbe7f5;border-radius:26px;padding:20px 18px 18px;background:linear-gradient(180deg,#fdfdff,#f1f6ff);box-shadow:0 20px 50px rgba(15,23,42,.15);transition:transform .2s ease, box-shadow .2s ease, border-color .2s ease;color:#0f172a;text-align:center;}
        .sp-plan-card::before{content:"";position:absolute;top:0;left:18px;right:18px;height:6px;border-radius:999px;background:#f59e0b;}
        .sp-plan-card:hover{transform:translateY(-4px);box-shadow:0 26px 60px rgba(15,23,42,.2);border-color:#f59e0b;}
        .sp-plan-card--warm{background:linear-gradient(180deg,#fff4e6,#fef2d6);border-color:#f8d59a;}
        .sp-plan-card--stone{background:linear-gradient(180deg,#f6f7fb,#e6ebf3);border-color:#cbd5e1;}
        .sp-plan-card--stone::before{background:#cbd5e1;}
        .sp-plan-card.is-featured{transform:translateY(-14px);border-color:#38bdf8;box-shadow:0 36px 90px rgba(56,189,248,.35);background:linear-gradient(180deg,#f4fbff,#e7f2ff);overflow:hidden;}
        .sp-plan-card.is-featured::before{background:#38bdf8;}
        .sp-plan-card.is-featured::after{content:"";position:absolute;inset:-35% -15% auto;height:140%;background:radial-gradient(600px 260px at 50% 0, rgba(125,211,252,.45), transparent 65%);opacity:.9;pointer-events:none;animation:premiumGlow 5s ease-in-out infinite;}
        .sp-plan-card.is-featured > *{position:relative;z-index:1;}

        .sp-plan-card__chips{display:flex;flex-wrap:wrap;gap:8px;justify-content:center;margin:0 0 12px;}
        .sp-plan-card__chip{background:#f59e0b;color:#1f2937;padding:6px 10px;border-radius:999px;font-weight:900;font-size:11px;box-shadow:0 10px 24px rgba(245,158,11,.25);text-transform:uppercase;letter-spacing:.05em;}
        .sp-plan-card__chip--primary{background:#0ea5e9;color:#fff;box-shadow:0 10px 24px rgba(14,165,233,.3);}

        .sp-plan-card__title{margin:8px 0 10px;font-weight:1000;letter-spacing:.08em;text-transform:uppercase;font-size:18px;color:#0f172a;}

        .sp-plan-card__panel{position:relative;background:#fff;border:1px solid #dbe7f5;border-radius:20px;padding:18px 16px;box-shadow:inset 0 0 0 1px rgba(255,255,255,.6);background-image:repeating-linear-gradient(135deg, rgba(148,163,184,.18) 0 2px, transparent 2px 8px);}
        .sp-plan-card.is-featured .sp-plan-card__panel::before{content:"";position:absolute;inset:0;border-radius:20px;background:linear-gradient(120deg, transparent 0%, rgba(255,255,255,.75) 45%, transparent 60%);transform:translateX(-120%);animation:premiumShimmer 4s ease-in-out infinite;pointer-events:none;}
        .sp-plan-card__panel::after{content:"";position:absolute;inset:10px;border-radius:14px;border:1px solid rgba(148,163,184,.2);pointer-events:none;}
        .sp-plan-card__gem{width:18px;height:18px;margin:0 auto 10px;border-radius:4px;background:#fff;border:3px solid #f59e0b;transform:rotate(45deg);}
        .sp-plan-card.is-featured .sp-plan-card__gem{border-color:#38bdf8;}
        .sp-plan-card--stone .sp-plan-card__gem{border-color:#cbd5e1;}
        .sp-plan-card__price-label{font-size:12px;text-transform:uppercase;letter-spacing:.12em;color:#64748b;}
        .sp-plan-card__price{font-size:30px;font-weight:1000;letter-spacing:.04em;color:#0f172a;}
        .sp-plan-card__total-label{margin-top:12px;font-size:12px;text-transform:uppercase;letter-spacing:.12em;color:#64748b;}
        .sp-plan-card__total{font-size:26px;font-weight:1000;letter-spacing:.04em;color:#0f172a;}
        .sp-plan-card__tax{margin-top:6px;font-size:11px;letter-spacing:.16em;text-transform:uppercase;color:#94a3b8;}
        .sp-plan-card__note{margin-top:12px;color:#475569;font-weight:700;font-size:12px;line-height:1.35;}
        .sp-plan-card__buy{margin:14px 0 0;}
        .sp-plan-card__btn{width:100%;border-radius:999px;padding:10px 12px;font-weight:1000;background:linear-gradient(180deg,#6cc3ff,#2b8ef1);border:1px solid #2b8ef1;color:#fff;box-shadow:0 12px 24px rgba(43,142,241,.35);}
        .sp-plan-card__btn:hover{opacity:.92;}
        .sp-plan-card.is-featured .sp-plan-card__btn{background:linear-gradient(180deg,#7fe0ff,#1d8fff);border-color:#1d8fff;box-shadow:0 18px 38px rgba(29,143,255,.45),0 0 0 3px rgba(125,211,252,.35);}
        .sp-plan-card__lessons{margin-top:16px;border-radius:16px;padding:12px 10px;font-weight:1000;font-size:22px;letter-spacing:.08em;text-transform:uppercase;background:linear-gradient(180deg,#ffcc8a,#f59e0b);color:#1f2937;box-shadow:inset 0 0 0 1px rgba(255,255,255,.4);}
        .sp-plan-card.is-featured .sp-plan-card__lessons{background:linear-gradient(180deg,#9be3ff,#2f9cff);color:#fff;box-shadow:0 18px 36px rgba(47,156,255,.35);}
        .sp-plan-card--stone .sp-plan-card__lessons{background:linear-gradient(180deg,#d9dee6,#bfc7d2);color:#1f2937;}

        @media(max-width:1199.98px){
            .sp-library__grid{grid-template-columns:repeat(2,minmax(0,1fr));}
            .sp-plans__grid{grid-template-columns:repeat(2,minmax(0,1fr));}
            .sp-plan-card.is-featured{transform:translateY(-6px);}
        }
        @media(max-width:575.98px){
            .sp-library__grid{grid-template-columns:1fr;}
            .sp-plans{padding:28px 16px;border-radius:22px;}
            .sp-plans__grid{grid-template-columns:1fr;}
            .sp-plan-card{padding:20px 16px;}
            .sp-plan-card__lessons{font-size:18px;}
        }
        @keyframes premiumGlow{
            0%,100%{opacity:.75;transform:translateY(0);}
            50%{opacity:1;transform:translateY(6px);}
        }
        @keyframes premiumShimmer{
            0%{transform:translateX(-120%);}
            55%{transform:translateX(120%);}
            100%{transform:translateX(120%);}
        }
    </style>
@endpush
