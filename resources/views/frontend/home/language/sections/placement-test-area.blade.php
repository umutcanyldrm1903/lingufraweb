<section class="lang-placement section-py-100">
    <div class="container">
        <div class="lang-placement__box">
            <div>
                <p class="lang-placement__eyebrow">{{ __('Placement Test') }}</p>
                <h2>{{ __('Find Your English Level in 2 Minutes') }}</h2>
                <p>{{ __('Answer 8 quick questions and get an instant level result with a recommended plan.') }}</p>
            </div>
            <div class="lang-placement__actions">
                <a href="{{ route('placement-test.show') }}" class="btn lang-placement__btn">
                    {{ __('Start Level Test') }}
                </a>
                <a href="{{ route('all-instructors') }}" class="btn lang-placement__btn lang-placement__btn--ghost">
                    {{ __('Choose Your Instructor') }}
                </a>
            </div>
        </div>
    </div>
</section>

@push('styles')
    <style>
        .lang-placement {
            padding-top: 28px;
            padding-bottom: 28px;
        }

        .lang-placement__box {
            border: 1px solid #d8e4f6;
            border-radius: 22px;
            padding: 26px;
            background:
                radial-gradient(500px circle at 0% 0%, rgba(246, 161, 5, 0.12), transparent 65%),
                linear-gradient(145deg, #ffffff, #f5f9ff);
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 20px;
            box-shadow: 0 16px 38px rgba(11, 63, 108, 0.08);
        }

        .lang-placement__eyebrow {
            margin: 0 0 10px;
            font-size: 12px;
            font-weight: 900;
            letter-spacing: .08em;
            text-transform: uppercase;
            color: #0e5c93;
        }

        .lang-placement h2 {
            margin: 0 0 8px;
            font-size: 32px;
            font-weight: 1000;
            color: #0c2850;
        }

        .lang-placement p {
            margin: 0;
            color: #4f6078;
            font-weight: 700;
        }

        .lang-placement__actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            justify-content: flex-end;
        }

        .lang-placement__btn {
            border-radius: 12px;
            font-weight: 900;
            padding: 12px 18px;
            white-space: nowrap;
        }

        .lang-placement__btn--ghost {
            background: #fff;
            border: 1px solid #0e5c93;
            color: #0e5c93;
        }

        @media (max-width: 991.98px) {
            .lang-placement__box {
                flex-direction: column;
                align-items: flex-start;
            }

            .lang-placement__actions {
                justify-content: flex-start;
            }
        }
    </style>
@endpush
