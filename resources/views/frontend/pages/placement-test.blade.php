@extends('frontend.layouts.master')

@section('meta_title', __('2-Minute English Level Test') . ' || ' . ($setting->app_name ?? config('app.name')))

@section('contents')
    <section class="pt-page section-py-100">
        <div class="container">
            <div class="pt-wrap">
                <div class="pt-head">
                    <p class="pt-eyebrow">{{ __('Placement Test') }}</p>
                    <h1>{{ __('2-Minute English Level Test') }}</h1>
                    <p>{{ __('Answer 8 quick questions and see your level instantly.') }}</p>
                </div>

                @if ($result)
                    <div class="pt-result">
                        <div>
                            <p class="pt-result__label">{{ __('Your Level') }}</p>
                            <h2>{{ $result['level'] }}</h2>
                            <p class="pt-result__meta">
                                {{ __('Score') }}: {{ $result['score'] }} / {{ $result['max_score'] }}
                            </p>
                        </div>
                        <div>
                            <p class="pt-result__title">{{ __('Recommended Track') }}: {{ $result['recommended_track'] }}</p>
                            <p>{{ $result['summary'] }}</p>
                            <p>{{ $result['next_step'] }}</p>
                            <div class="pt-result__actions">
                                <a class="pt-btn" href="{{ $result['cta']['schedule_url'] }}">{{ __('Schedule Trial Lesson') }}</a>
                                @if (!empty($result['cta']['whatsapp_url']))
                                    <a class="pt-btn pt-btn--ghost" href="{{ $result['cta']['whatsapp_url'] }}" target="_blank"
                                        rel="noopener noreferrer">{{ __('Send via WhatsApp') }}</a>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif

                <form id="placement-test-form" method="POST" action="{{ route('placement-test.submit') }}">
                    @csrf
                    <div class="pt-progress">
                        <span id="pt-progress-text"></span>
                        <div class="pt-progress__bar">
                            <span id="pt-progress-fill"></span>
                        </div>
                    </div>

                    <div class="pt-steps">
                        @foreach ($questions as $index => $question)
                            <section class="pt-step" data-step="{{ $index }}" @if ($index !== 0) hidden @endif>
                                <p class="pt-step__count">{{ __('Question') }} {{ $index + 1 }} / {{ count($questions) }}</p>
                                <h3>{{ $question['prompt'] }}</h3>
                                <div class="pt-options">
                                    @foreach ($question['options'] as $option)
                                        <label class="pt-option">
                                            <input type="radio" name="answers[{{ $question['id'] }}]" value="{{ $option['id'] }}"
                                                @checked(old('answers.' . $question['id']) === $option['id'])>
                                            <span>{{ $option['label'] }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </section>
                        @endforeach

                        <section class="pt-step" data-step="{{ count($questions) }}" hidden>
                            <p class="pt-step__count">{{ __('Optional') }}</p>
                            <h3>{{ __('Leave contact info to get a matching trial lesson plan.') }}</h3>
                            <div class="pt-contact">
                                <label>
                                    <span>{{ __('Full name') }}</span>
                                    <input type="text" name="name" value="{{ old('name') }}" maxlength="255">
                                </label>
                                <label>
                                    <span>{{ __('Email') }}</span>
                                    <input type="email" name="email" value="{{ old('email') }}" maxlength="255">
                                </label>
                                <label>
                                    <span>{{ __('Phone (WhatsApp)') }}</span>
                                    <input type="text" name="phone" value="{{ old('phone') }}" maxlength="32">
                                </label>
                            </div>
                        </section>
                    </div>

                    @if ($errors->any())
                        <div class="pt-errors">
                            @foreach ($errors->all() as $error)
                                <p>{{ $error }}</p>
                            @endforeach
                        </div>
                    @endif

                    <div class="pt-actions">
                        <button type="button" class="pt-btn pt-btn--ghost" id="pt-prev">{{ __('Back') }}</button>
                        <button type="button" class="pt-btn" id="pt-next">{{ __('Next') }}</button>
                        <button type="submit" class="pt-btn" id="pt-submit" hidden>{{ __('Get My Result') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </section>
@endsection

@push('styles')
    <style>
        .pt-page {
            background: linear-gradient(180deg, #f4f8ff 0%, #eef4fb 100%);
            padding: 70px 0 100px;
        }

        .pt-wrap {
            max-width: 920px;
            margin: 0 auto;
            background: #fff;
            border: 1px solid #e4ebf6;
            border-radius: 24px;
            padding: 28px;
            box-shadow: 0 18px 45px rgba(12, 29, 63, 0.08);
        }

        .pt-head h1 {
            margin: 0 0 8px;
            font-size: 34px;
            line-height: 1.2;
            font-weight: 900;
            color: #0c2850;
        }

        .pt-head p {
            margin: 0;
            color: #4d5e76;
            font-weight: 700;
        }

        .pt-eyebrow {
            color: #0e5c93;
            text-transform: uppercase;
            letter-spacing: .08em;
            font-weight: 900;
            font-size: 12px;
            margin-bottom: 10px;
        }

        .pt-result {
            margin-top: 18px;
            margin-bottom: 24px;
            display: grid;
            grid-template-columns: 220px 1fr;
            gap: 16px;
            background: #f7fbff;
            border: 1px solid #d8e6fb;
            border-radius: 18px;
            padding: 16px;
        }

        .pt-result__label {
            margin: 0;
            font-size: 12px;
            color: #6b7a90;
            font-weight: 800;
            text-transform: uppercase;
        }

        .pt-result h2 {
            margin: 4px 0;
            font-size: 46px;
            line-height: 1;
            color: #0e5c93;
            font-weight: 1000;
        }

        .pt-result__meta {
            margin: 0;
            color: #6b7a90;
            font-weight: 700;
        }

        .pt-result__title {
            margin: 0 0 6px;
            color: #0c2850;
            font-weight: 900;
        }

        .pt-result__actions {
            margin-top: 10px;
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .pt-progress {
            margin-top: 24px;
            margin-bottom: 14px;
        }

        .pt-progress span {
            display: block;
            color: #6b7a90;
            font-weight: 700;
            margin-bottom: 6px;
            font-size: 13px;
        }

        .pt-progress__bar {
            height: 10px;
            background: #e8eef8;
            border-radius: 999px;
            overflow: hidden;
        }

        .pt-progress__bar span {
            display: block;
            height: 100%;
            width: 0;
            margin: 0;
            border-radius: inherit;
            background: linear-gradient(90deg, #0e5c93, #f6a105);
            transition: width .25s ease;
        }

        .pt-step h3 {
            margin: 0 0 16px;
            color: #0c2850;
            font-size: 24px;
            font-weight: 900;
            line-height: 1.35;
        }

        .pt-step__count {
            margin: 8px 0 8px;
            font-size: 12px;
            font-weight: 900;
            color: #0e5c93;
            text-transform: uppercase;
            letter-spacing: .05em;
        }

        .pt-options {
            display: grid;
            gap: 10px;
        }

        .pt-option {
            display: flex;
            gap: 10px;
            align-items: flex-start;
            border: 1px solid #d9e3f3;
            border-radius: 14px;
            padding: 12px;
            cursor: pointer;
            background: #fff;
        }

        .pt-option:hover {
            border-color: #0e5c93;
            background: #f8fbff;
        }

        .pt-option input {
            margin-top: 4px;
        }

        .pt-option span {
            color: #1f334f;
            font-weight: 700;
        }

        .pt-contact {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 12px;
        }

        .pt-contact label {
            display: grid;
            gap: 6px;
        }

        .pt-contact span {
            color: #425672;
            font-size: 12px;
            font-weight: 800;
            text-transform: uppercase;
        }

        .pt-contact input {
            border: 1px solid #cbd8ee;
            border-radius: 12px;
            padding: 10px 12px;
            font-weight: 700;
        }

        .pt-errors {
            margin-top: 10px;
            border: 1px solid #f6b4b4;
            border-radius: 12px;
            background: #fff5f5;
            padding: 10px 12px;
        }

        .pt-errors p {
            margin: 0;
            color: #a01d1d;
            font-weight: 700;
            font-size: 13px;
        }

        .pt-actions {
            margin-top: 18px;
            display: flex;
            justify-content: space-between;
            gap: 12px;
        }

        .pt-btn {
            border: 1px solid #f6a105;
            background: #f6a105;
            color: #fff;
            border-radius: 12px;
            padding: 10px 16px;
            font-weight: 900;
            text-decoration: none;
            cursor: pointer;
        }

        .pt-btn--ghost {
            background: #fff;
            color: #0e5c93;
            border-color: #0e5c93;
        }

        @media (max-width: 767.98px) {
            .pt-wrap {
                padding: 18px;
                border-radius: 18px;
            }

            .pt-head h1 {
                font-size: 28px;
            }

            .pt-result {
                grid-template-columns: 1fr;
            }

            .pt-contact {
                grid-template-columns: 1fr;
            }
        }
    </style>
@endpush

@push('scripts')
    <script>
        (() => {
            const form = document.getElementById('placement-test-form');
            if (!form) return;

            const steps = Array.from(form.querySelectorAll('.pt-step'));
            const prevBtn = document.getElementById('pt-prev');
            const nextBtn = document.getElementById('pt-next');
            const submitBtn = document.getElementById('pt-submit');
            const progressFill = document.getElementById('pt-progress-fill');
            const progressText = document.getElementById('pt-progress-text');

            let current = 0;
            const total = steps.length;
            const questionSteps = total - 1;

            const showStep = (index) => {
                current = Math.max(0, Math.min(index, total - 1));
                steps.forEach((step, i) => {
                    step.hidden = i !== current;
                });

                prevBtn.hidden = current === 0;
                const isContactStep = current === total - 1;
                nextBtn.hidden = isContactStep;
                submitBtn.hidden = !isContactStep;

                const visibleStep = Math.min(current + 1, total);
                const percent = (visibleStep / total) * 100;
                progressFill.style.width = percent + '%';
                progressText.textContent = `${visibleStep} / ${total}`;
            };

            const hasAnswer = (index) => {
                const step = steps[index];
                if (!step || index >= questionSteps) return true;
                return !!step.querySelector('input[type="radio"]:checked');
            };

            prevBtn?.addEventListener('click', () => showStep(current - 1));
            nextBtn?.addEventListener('click', () => {
                if (!hasAnswer(current)) return;
                showStep(current + 1);
            });

            showStep(0);
        })();
    </script>
@endpush

