@php
    $plans = [
        [
            'id' => 'plan_3m',
            'title' => 'CORE STARTER',
            'lessons' => __('24 Lessons'),
            'features' => [
                __('2 lessons in 4 weeks'),
                __('Free trial lesson'),
                __('Weekly cancellation right'),
            ],
        ],
        [
            'id' => 'plan_6m',
            'title' => 'PROGRESS BUILDER',
            'lessons' => __('48 Lessons'),
            'features' => [
                __('Online lessons'),
                __('Action plan'),
                __('Instructor change'),
                __('Personalized materials'),
            ],
        ],
        [
            'id' => 'plan_12m',
            'title' => 'PREMIUM PAKET',
            'lessons' => __('96 Lessons'),
            'features' => [
                __('8 lessons per week'),
                __('Personal coach matching'),
                __('Unlimited rescheduling'),
                __('Certificate'),
            ],
        ],
    ];

    $whatsappLeadPhone = preg_replace('/\\D+/', '', (string) config('app.whatsapp_lead_phone', ''));
@endphp

<section class="section-py-120" id="lang-packages">
    <div class="container">
        <div class="sp-plans sp-plans--galaxy">
            <div class="sp-plans__head">
                <div>
                    <h2 class="sp-plans__title">{{ __('Choose the plan that fits you best') }}</h2>
                    <p class="sp-plans__subtitle">{{ __('Pick a package, fill out a short form, and send your details via WhatsApp.') }}</p>
                </div>
            </div>

            @php
                $displayPlans = collect($plans)->values();
                $premiumPlan = $displayPlans->first(function ($plan) {
                    return str_contains(strtolower((string) $plan['title']), 'premium');
                });
                if ($premiumPlan) {
                    $others = $displayPlans->reject(function ($plan) use ($premiumPlan) {
                        return (string) $plan['id'] === (string) $premiumPlan['id'];
                    })->values();
                    if ($others->isNotEmpty()) {
                        $others = $others->sortByDesc(function ($plan) {
                            $lessons = (string) ($plan['lessons'] ?? '');
                            return (int) preg_replace('/\\D+/', '', $lessons);
                        })->values();
                        $displayPlans = collect();
                        $displayPlans->push($others->shift());
                        $displayPlans->push($premiumPlan);
                        foreach ($others as $other) {
                            $displayPlans->push($other);
                        }
                    }
                }
            @endphp

            <div class="sp-plans__grid">
                @foreach ($displayPlans as $plan)
                    @php
                        $isFeatured = isset($premiumPlan) && (string) $plan['id'] === (string) $premiumPlan['id'];
                        $toneClass = '';
                        if ($loop->first) {
                            $toneClass = 'sp-plan-card--warm';
                        } elseif ($loop->last) {
                            $toneClass = 'sp-plan-card--stone';
                        }
                    @endphp
                    <div class="sp-plan-card {{ $isFeatured ? 'is-featured' : '' }} {{ $toneClass }}">
                        <h4 class="sp-plan-card__title">{{ $plan['title'] }}</h4>
                        @if ($isFeatured)
                            <div class="sp-plan-card__chips">
                                <span class="sp-plan-card__chip sp-plan-card__chip--primary">{{ __('En Populer') }}</span>
                                <span class="sp-plan-card__chip">{{ __('En Avantajli') }}</span>
                            </div>
                        @endif

                        <div class="sp-plan-card__panel">
                            <div class="sp-plan-card__gem" aria-hidden="true"></div>
                            <div class="sp-plan-card__buy">
                                <button
                                    type="button"
                                    class="sp-plan-card__btn js-lang-plan-start"
                                    data-plan-id="{{ $plan['id'] }}"
                                    data-plan-title="{{ $plan['title'] }}"
                                    data-plan-lessons="{{ $plan['lessons'] }}"
                                >
                                    {{ __('Start') }}
                                </button>
                            </div>
                        </div>
                        <div class="sp-plan-card__lessons">{{ $plan['lessons'] }}</div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="lang-flow" id="lang-plan-flow" data-wa-phone="{{ $whatsappLeadPhone }}">
            <div class="lang-flow__card">
                <div class="lang-flow__top">
                    <p class="lang-flow__kicker">
                        {{ __('Selected package') }}:
                        <strong data-flow-plan>{{ __('(Not selected)') }}</strong>
                    </p>
                    <div class="lang-flow__progress" aria-hidden="true">
                        <span class="lang-flow__progress-bar" data-flow-progress></span>
                    </div>
                    @if (!$whatsappLeadPhone)
                        <p class="lang-flow__warning">
                            {{ __('WhatsApp number is not set. Add WHATSAPP_LEAD_PHONE=90xxxxxxxxxx to .env.') }}
                        </p>
                    @endif
                </div>

                <div class="lang-flow__empty" data-flow-empty>
                    <h3 class="lang-flow__empty-title">{{ __('Select a package to continue') }}</h3>
                    <p class="lang-flow__empty-text">{{ __('Choose a package above and click "Start" to continue.') }}</p>
                </div>

                <form id="lang-plan-form" class="lang-flow__form" hidden novalidate>
                    <input type="hidden" name="plan_id" id="lang_flow_plan_id" required>
                    <input type="hidden" name="plan_title" id="lang_flow_plan_title">
                    <input type="hidden" name="plan_lessons" id="lang_flow_plan_lessons">

                    <div class="lang-flow__step" data-step="0" hidden>
                        <h3 class="lang-flow__question">{{ __('Where should the lesson take place?') }}</h3>
                        <div class="lang-flow__options">
                            <label class="lang-option">
                                <input type="radio" name="lesson_place" value="Online (Zoom)" checked required>
                                <span>{{ __('Online (Zoom)') }}</span>
                            </label>
                        </div>
                    </div>

                    <div class="lang-flow__step" data-step="1" hidden>
                        <h3 class="lang-flow__question">{{ __('Does the learner attend school?') }}</h3>
                        <div class="lang-flow__options">
                            @foreach (['Adult', 'University', 'High school', 'Middle school', 'Primary school', 'Preschool'] as $opt)
                                <label class="lang-option">
                                    <input type="radio" name="student_type" value="{{ $opt }}" {{ $loop->first ? 'required' : '' }}>
                                    <span>{{ __($opt) }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <div class="lang-flow__step" data-step="2" hidden>
                        <h3 class="lang-flow__question">{{ __('What is your goal for taking lessons?') }}</h3>
                        <div class="lang-flow__options">
                            @foreach (['Business English', 'Speaking practice', 'School support', 'YDS', 'IELTS', 'TOEFL', 'PTE', 'Other'] as $opt)
                                <label class="lang-option">
                                    <input type="radio" name="goal" value="{{ $opt }}" {{ $loop->first ? 'required' : '' }}>
                                    <span>{{ __($opt) }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <div class="lang-flow__step" data-step="3" hidden>
                        <h3 class="lang-flow__question">{{ __('What level?') }}</h3>
                        <div class="lang-flow__options">
                            @foreach (['Beginner (A1)', 'Elementary (A2)', 'Intermediate (B1)', 'Upper-intermediate (B2)', 'Advanced (C1)', 'Proficient (C2)'] as $opt)
                                <label class="lang-option">
                                    <input type="radio" name="level" value="{{ $opt }}" {{ $loop->first ? 'required' : '' }}>
                                    <span>{{ __($opt) }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <div class="lang-flow__step" data-step="4" hidden>
                        <h3 class="lang-flow__question">{{ __('How often?') }}</h3>
                        <div class="lang-flow__options">
                            @foreach (['3 or more per week', 'Twice a week', 'Once a week', 'Other'] as $opt)
                                <label class="lang-option">
                                    <input type="radio" name="frequency" value="{{ $opt }}" {{ $loop->first ? 'required' : '' }}>
                                    <span>{{ __($opt) }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <div class="lang-flow__step" data-step="5" hidden>
                        <h3 class="lang-flow__question">{{ __('What should the instructor know or pay attention to?') }}</h3>
                        <label class="lang-field">
                            <span class="lang-field__label">{{ __('Write a short note (required)') }}</span>
                            <textarea name="details" rows="4" required placeholder="{{ __('Example: Weekday evenings work. Focused on speaking practice. Goal: YDS...') }}"></textarea>
                        </label>
                    </div>

                    <div class="lang-flow__step" data-step="6" hidden>
                        <h3 class="lang-flow__question">{{ __('When would you like to start?') }}</h3>
                        <div class="lang-flow__options">
                            @foreach (['At a specific time (within 3 weeks)', 'Within 2 months', 'Within 6 months', 'I just want information'] as $opt)
                                <label class="lang-option">
                                    <input type="radio" name="when" value="{{ $opt }}" {{ $loop->first ? 'required' : '' }}>
                                    <span>{{ __($opt) }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <div class="lang-flow__step" data-step="7" hidden>
                        <h3 class="lang-flow__question">{{ __('Your contact details') }}</h3>
                        <div class="lang-flow__grid">
                            <label class="lang-field">
                                <span class="lang-field__label">{{ __('Full name') }}</span>
                                <input type="text" name="full_name" autocomplete="name" required>
                            </label>
                            <label class="lang-field">
                                <span class="lang-field__label">{{ __('Phone (WhatsApp)') }}</span>
                                <input type="tel" name="phone" autocomplete="tel" required placeholder="05xx xxx xx xx">
                            </label>
                            <label class="lang-field lang-field--full">
                                <span class="lang-field__label">{{ __('Email (optional)') }}</span>
                                <input type="email" name="email" autocomplete="email" placeholder="example@mail.com">
                            </label>
                        </div>
                    </div>

                    <div class="lang-flow__step" data-step="8" hidden>
                        <h3 class="lang-flow__question">{{ __('All set!') }}</h3>
                        <p class="lang-flow__desc">{{ __('You can send your details to us via WhatsApp.') }}</p>
                        <pre class="lang-flow__summary" data-flow-summary></pre>
                    </div>

                    <div class="lang-flow__nav">
                        <button type="button" class="btn lang-flow__btn lang-flow__btn--ghost" data-flow-back>{{ __('Back') }}</button>
                        <button type="button" class="btn lang-flow__btn" data-flow-next>{{ __('Next') }}</button>
                        <a class="btn lang-flow__btn" data-flow-wa target="_blank" rel="noopener noreferrer" hidden>{{ __('Send via WhatsApp') }}</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

@push('styles')
<style>
    #lang-packages{
        --brand-primary: var(--tg-theme-primary);
        --brand-accent: var(--tg-theme-secondary);
        --brand-dark: var(--tg-common-color-dark);
    }
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
    .sp-plan-card.is-featured{transform:translateY(-12px);border-color:#93c5fd;box-shadow:0 30px 70px rgba(15,23,42,.25);background:linear-gradient(180deg,#f4fbff,#e7f2ff);}
    .sp-plan-card.is-featured::before{background:#38bdf8;}

    .sp-plan-card__chips{display:flex;flex-wrap:wrap;gap:8px;justify-content:center;margin:0 0 12px;}
    .sp-plan-card__chip{background:#f59e0b;color:#1f2937;padding:6px 10px;border-radius:999px;font-weight:900;font-size:11px;box-shadow:0 10px 24px rgba(245,158,11,.25);text-transform:uppercase;letter-spacing:.05em;}
    .sp-plan-card__chip--primary{background:#0ea5e9;color:#fff;box-shadow:0 10px 24px rgba(14,165,233,.3);}

    .sp-plan-card__title{margin:8px 0 10px;font-weight:1000;letter-spacing:.08em;text-transform:uppercase;font-size:18px;color:#0f172a;}

    .sp-plan-card__panel{position:relative;background:#fff;border:1px solid #dbe7f5;border-radius:20px;padding:18px 16px;box-shadow:inset 0 0 0 1px rgba(255,255,255,.6);background-image:repeating-linear-gradient(135deg, rgba(148,163,184,.18) 0 2px, transparent 2px 8px);text-align:left;}
    .sp-plan-card__panel::after{content:"";position:absolute;inset:10px;border-radius:14px;border:1px solid rgba(148,163,184,.2);pointer-events:none;}
    .sp-plan-card__gem{width:18px;height:18px;margin:0 auto 10px;border-radius:4px;background:#fff;border:3px solid #f59e0b;transform:rotate(45deg);}
    .sp-plan-card.is-featured .sp-plan-card__gem{border-color:#38bdf8;}
    .sp-plan-card--stone .sp-plan-card__gem{border-color:#cbd5e1;}

    .sp-plan-card__features{margin:0;padding:0;list-style:none;display:grid;gap:10px;font-weight:800;color:#1f2937;}
    .sp-plan-card__features li{display:flex;gap:10px;align-items:flex-start;}
    .sp-plan-card__features li::before{content:"";width:8px;height:8px;border-radius:50%;background:#f59e0b;margin-top:6px;flex:0 0 auto;}
    .sp-plan-card.is-featured .sp-plan-card__features li::before{background:#38bdf8;}
    .sp-plan-card--stone .sp-plan-card__features li::before{background:#cbd5e1;}

    .sp-plan-card__buy{margin:16px 0 0;}
    .sp-plan-card__btn{width:100%;border-radius:999px;padding:10px 12px;font-weight:1000;background:linear-gradient(180deg,#6cc3ff,#2b8ef1);border:1px solid #2b8ef1;color:#fff;box-shadow:0 12px 24px rgba(43,142,241,.35);}
    .sp-plan-card__btn:hover{opacity:.92;}
    .sp-plan-card__lessons{margin-top:16px;border-radius:16px;padding:12px 10px;font-weight:1000;font-size:22px;letter-spacing:.08em;text-transform:uppercase;background:linear-gradient(180deg,#ffcc8a,#f59e0b);color:#1f2937;box-shadow:inset 0 0 0 1px rgba(255,255,255,.4);}
    .sp-plan-card.is-featured .sp-plan-card__lessons{background:linear-gradient(180deg,#7fd0ff,#2f9cff);color:#fff;}
    .sp-plan-card--stone .sp-plan-card__lessons{background:linear-gradient(180deg,#d9dee6,#bfc7d2);color:#1f2937;}

    .lang-flow {
        margin-top: 34px;
    }

    .lang-flow__card {
        background: rgba(255, 255, 255, 0.92);
        border-radius: 18px;
        border: 2px solid rgba(14, 92, 147, 0.10);
        box-shadow: 0 18px 52px rgba(0, 0, 0, 0.10);
        overflow: hidden;
        backdrop-filter: blur(10px);
    }

    .lang-flow__top {
        padding: 18px 20px 14px;
        border-bottom: 1px solid var(--tg-border-2);
        display: grid;
        gap: 10px;
    }

    .lang-flow__kicker {
        margin: 0;
        font-weight: 900;
        color: var(--tg-heading-color);
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
        align-items: center;
    }

    .lang-flow__warning {
        margin: 0;
        font-weight: 800;
        color: #b42318;
        background: rgba(180, 35, 24, 0.08);
        border: 1px solid rgba(180, 35, 24, 0.18);
        padding: 10px 12px;
        border-radius: 12px;
    }

    .lang-flow__progress {
        height: 10px;
        background: rgba(14, 92, 147, 0.10);
        border-radius: 999px;
        overflow: hidden;
    }

    .lang-flow__progress-bar {
        display: block;
        height: 100%;
        width: 0%;
        background: linear-gradient(90deg, var(--brand-accent), var(--brand-primary));
        border-radius: 999px;
        transition: width .25s ease;
    }

    .lang-flow__empty {
        padding: 26px 20px;
        text-align: center;
        color: var(--tg-body-color);
    }

    .lang-flow__empty-title {
        margin: 0 0 8px;
        font-weight: 1000;
        color: var(--tg-heading-color);
        font-size: 22px;
    }

    .lang-flow__empty-text {
        margin: 0;
        font-weight: 700;
    }

    .lang-flow__form {
        padding: 22px 20px 20px;
        display: grid;
        gap: 18px;
    }

    .lang-flow__question {
        margin: 0 0 10px;
        font-weight: 1000;
        color: var(--tg-heading-color);
        font-size: 20px;
    }

    .lang-flow__desc {
        margin: 0 0 10px;
        font-weight: 700;
        color: var(--tg-body-color);
    }

    .lang-flow__options {
        display: grid;
        gap: 10px;
    }

    .lang-option {
        display: flex;
        gap: 12px;
        align-items: flex-start;
        padding: 12px 14px;
        border-radius: 14px;
        border: 1px solid rgba(14, 92, 147, 0.18);
        background: rgba(14, 92, 147, 0.04);
        cursor: pointer;
        user-select: none;
        transition: transform .12s ease, background .12s ease, border-color .12s ease;
    }

    .lang-option:hover {
        transform: translateY(-1px);
        border-color: rgba(14, 92, 147, 0.28);
        background: rgba(14, 92, 147, 0.06);
    }

    .lang-option input[type="radio"] {
        margin-top: 3px;
        accent-color: var(--brand-primary);
    }

    .lang-field {
        display: grid;
        gap: 8px;
    }

    .lang-field__label {
        font-weight: 900;
        color: var(--tg-heading-color);
    }

    .lang-field input,
    .lang-field textarea {
        width: 100%;
        border-radius: 14px;
        border: 1px solid rgba(14, 92, 147, 0.18);
        padding: 12px 14px;
        font-weight: 700;
        color: var(--tg-common-color-black-2);
        background: #fff;
    }

    .lang-field textarea {
        resize: vertical;
        min-height: 120px;
    }

    .lang-flow__grid {
        display: grid;
        gap: 14px;
        grid-template-columns: 1fr;
    }

    .lang-field--full {
        grid-column: 1 / -1;
    }

    .lang-flow__summary {
        margin: 0;
        padding: 14px;
        border-radius: 14px;
        background: rgba(14, 92, 147, 0.05);
        border: 1px solid rgba(14, 92, 147, 0.16);
        white-space: pre-wrap;
        font-weight: 700;
        color: var(--tg-common-color-black-2);
        min-height: 140px;
    }

    .lang-flow__nav {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
        align-items: center;
        justify-content: flex-end;
        padding-top: 6px;
        border-top: 1px solid var(--tg-border-2);
        margin-top: 6px;
    }

    .lang-flow__btn {
        border-radius: 14px;
        font-weight: 900;
        padding: 12px 16px;
        background: var(--brand-accent);
        border-color: var(--brand-accent);
        color: var(--tg-common-color-black-3);
        transition: background 0.2s ease, border-color 0.2s ease, color 0.2s ease, transform .18s ease;
    }

    .lang-flow__btn:hover {
        transform: translateY(-1px);
        background: var(--brand-primary);
        border-color: var(--brand-primary);
        color: var(--tg-common-color-white);
    }

    .lang-flow__btn--ghost {
        background: transparent;
        border-color: var(--tg-border-2);
        color: var(--tg-heading-color);
    }

    .lang-flow__btn--ghost:hover {
        transform: translateY(-1px);
        background: rgba(14, 92, 147, 0.10);
        border-color: rgba(14, 92, 147, 0.22);
        color: var(--brand-primary);
    }

    @media(max-width:1199.98px){
        .sp-plans__grid{grid-template-columns:repeat(2,minmax(0,1fr));}
        .sp-plan-card.is-featured{transform:translateY(-6px);}
    }
    @media (max-width: 991px) {
        .sp-plans__title{font-size:26px;}
        .sp-plan-card.is-featured{transform:none;}
    }
    @media(max-width:575.98px){
        .sp-plans{padding:28px 16px;border-radius:22px;}
        .sp-plans__grid{grid-template-columns:1fr;}
        .sp-plan-card{padding:20px 16px;}
        .sp-plan-card__lessons{font-size:18px;}
    }

    @media (min-width: 992px) {
        .lang-flow__grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }
</style>
@endpush

@push('scripts')
    <script>
        (() => {
            const flow = document.getElementById('lang-plan-flow');
            if (!flow) return;

            const form = document.getElementById('lang-plan-form');
            const empty = flow.querySelector('[data-flow-empty]');
            const steps = Array.from(flow.querySelectorAll('[data-step]'));
            const progress = flow.querySelector('[data-flow-progress]');
            const planLabel = flow.querySelector('[data-flow-plan]');
            const summaryEl = flow.querySelector('[data-flow-summary]');
            const backBtn = flow.querySelector('[data-flow-back]');
            const nextBtn = flow.querySelector('[data-flow-next]');
            const waBtn = flow.querySelector('[data-flow-wa]');
            const waPhone = (flow.dataset.waPhone || '').trim();

            const planIdInput = document.getElementById('lang_flow_plan_id');
            const planTitleInput = document.getElementById('lang_flow_plan_title');
            const planLessonsInput = document.getElementById('lang_flow_plan_lessons');

            const labels = {
                requestTitle: @json(__('New Language Course Request')),
                package: @json(__('Package')),
                lessonLocation: @json(__('Lesson location')),
                learner: @json(__('Learner')),
                goal: @json(__('Goal')),
                level: @json(__('Level')),
                frequency: @json(__('Frequency')),
                preferredStart: @json(__('Preferred start')),
                notes: @json(__('Notes')),
                fullName: @json(__('Full name')),
                phone: @json(__('Phone')),
                email: @json(__('Email')),
                page: @json(__('Page')),
            };

            let currentStep = 0;

            const updateProgress = () => {
                if (!progress) return;
                const percent = steps.length > 1 ? (currentStep / (steps.length - 1)) * 100 : 0;
                progress.style.width = `${percent}%`;
            };

            const toggleFieldsets = () => {
                steps.forEach((step, index) => {
                    step.hidden = index !== currentStep;
                });
                updateProgress();
                backBtn.hidden = currentStep === 0;
                nextBtn.hidden = currentStep === steps.length - 1;
                waBtn.hidden = currentStep !== steps.length - 1;
            };

            const focusFirstField = () => {
                const field = steps[currentStep].querySelector('input, textarea');
                if (field) field.focus({ preventScroll: true });
            };

            const validateStep = () => {
                const fields = steps[currentStep].querySelectorAll('input, textarea');
                for (const field of fields) {
                    if (!field.checkValidity()) {
                        field.reportValidity();
                        return false;
                    }
                }
                return true;
            };

            const getValue = (name) => {
                try {
                    const escaped = window.CSS && CSS.escape ? CSS.escape(name) : name.replace(/\"/g, '\\\\\"');
                    const checked = form.querySelector(`[name="${escaped}"]:checked`);
                    if (checked) return checked.value;
                    const field = form.querySelector(`[name="${escaped}"]`);
                    return field ? (field.value || '').trim() : '';
                } catch (e) {
                    return '';
                }
            };

            const buildMessage = () => {
                const plan = [planTitleInput.value, planLessonsInput.value].filter(Boolean).join(' - ');
                const lines = [
                    labels.requestTitle,
                    `${labels.package}: ${plan}`,
                    '',
                    `${labels.lessonLocation}: ${getValue('lesson_place')}`,
                    `${labels.learner}: ${getValue('student_type')}`,
                    `${labels.goal}: ${getValue('goal')}`,
                    `${labels.level}: ${getValue('level')}`,
                    `${labels.frequency}: ${getValue('frequency')}`,
                    `${labels.preferredStart}: ${getValue('when')}`,
                    `${labels.notes}: ${getValue('details')}`,
                    '',
                    `${labels.fullName}: ${getValue('full_name')}`,
                    `${labels.phone}: ${getValue('phone')}`,
                    `${labels.email}: ${getValue('email') || '-'}`,
                    '',
                    `${labels.page}: ${window.location.href.split('#')[0]}`,
                ];

                return lines.join('\n');
            };

            const buildWhatsAppUrl = (message) => {
                const encoded = encodeURIComponent(message);
                if (waPhone) return `https://wa.me/${waPhone}?text=${encoded}`;
                return `https://wa.me/?text=${encoded}`;
            };

            const goToStep = (index) => {
                currentStep = Math.max(0, Math.min(index, steps.length - 1));
                toggleFieldsets();
                focusFirstField();
            };

            const prepareFinalStep = () => {
                const message = buildMessage();
                summaryEl.textContent = message;
                waBtn.setAttribute('href', buildWhatsAppUrl(message));
            };

            const startFlow = (button) => {
                const planId = button.dataset.planId || '';
                const planTitle = button.dataset.planTitle || '';
                const planLessons = button.dataset.planLessons || '';

                if (!planId) return;

                planIdInput.value = planId;
                planTitleInput.value = planTitle;
                planLessonsInput.value = planLessons;

                if (planLabel) {
                    planLabel.textContent = [planTitle, planLessons].filter(Boolean).join(' - ');
                }

                empty.hidden = true;
                form.hidden = false;
                goToStep(0);

                flow.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start',
                });
            };

            document.querySelectorAll('.js-lang-plan-start').forEach((button) => {
                button.addEventListener('click', () => startFlow(button));
            });

            backBtn.addEventListener('click', () => {
                if (currentStep === 0) return;
                goToStep(currentStep - 1);
            });

            nextBtn.addEventListener('click', () => {
                if (!validateStep()) return;

                const nextIndex = currentStep + 1;
                if (nextIndex >= steps.length) return;

                if (nextIndex === steps.length - 1) {
                    prepareFinalStep();
                }

                goToStep(nextIndex);
            });

            waBtn.addEventListener('click', () => {
                prepareFinalStep();
            });

            form.addEventListener('submit', (event) => {
                event.preventDefault();
            });
        })();
    </script>
@endpush

