@extends('frontend.student-dashboard.layouts.master')

@section('dashboard-contents')
    @php
        $instructorName = (string) ($lesson?->instructor?->first_name ?? $lesson?->instructor?->name ?? '');
        $startTime = $lesson?->start_time ? formattedDateTime($lesson->start_time) : '-';
        $alreadyRated = !empty($lesson?->student_rating);
    @endphp

    <div class="sp-rate">
        <div class="sp-rate__head">
            <h4 class="sp-rate__title">{{ __('Rate Lesson') }}</h4>
            <a href="{{ route('student.enrolled-courses') }}" class="sp-rate__back">{{ __('Back') }}</a>
        </div>

        <div class="sp-rate__card">
            <div class="sp-rate__meta">
                <div class="sp-rate__meta-item">
                    <span class="sp-rate__label">{{ __('Instructor') }}</span>
                    <span class="sp-rate__value">{{ $instructorName !== '' ? $instructorName : '-' }}</span>
                </div>
                <div class="sp-rate__meta-item">
                    <span class="sp-rate__label">{{ __('Date') }}</span>
                    <span class="sp-rate__value">{{ $startTime }}</span>
                </div>
                <div class="sp-rate__meta-item">
                    <span class="sp-rate__label">{{ __('Lesson') }}</span>
                    <span class="sp-rate__value">{{ $lesson?->title ?: __('Private Live Lesson') }}</span>
                </div>
            </div>

            @if ($alreadyRated)
                <div class="sp-rate__done">
                    <span class="sp-rate__done-pill">
                        <i class="fas fa-star"></i>
                        {{ (int) $lesson->student_rating }} / 5
                    </span>
                    <p class="sp-rate__done-text">{{ __('You have already rated this lesson.') }}</p>
                </div>
            @else
                <form method="POST" action="{{ route('student.live-lessons.rate.store', $lesson->id) }}" class="sp-rate__form">
                    @csrf

                    <div class="sp-rate__field">
                        <p class="sp-rate__field-label">{{ __('Rating') }}</p>
                        <div class="sp-rate__stars" role="radiogroup" aria-label="{{ __('Rating') }}">
                            @for ($i = 5; $i >= 1; $i--)
                                <input type="radio" id="rating-{{ $i }}" name="rating" value="{{ $i }}" {{ (int) old('rating') === $i ? 'checked' : '' }}>
                                <label for="rating-{{ $i }}" title="{{ $i }}">★</label>
                            @endfor
                        </div>
                        @error('rating')
                            <div class="sp-rate__error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="sp-rate__field">
                        <label class="sp-rate__field-label" for="review">{{ __('Review (optional)') }}</label>
                        <textarea id="review" name="review" rows="4" maxlength="1000" placeholder="{{ __('You can write a short review...') }}">{{ old('review') }}</textarea>
                        @error('review')
                            <div class="sp-rate__error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="sp-rate__actions">
                        <button type="submit" class="sp-rate__btn">{{ __('Send') }}</button>
                        <a href="{{ route('student.enrolled-courses') }}" class="sp-rate__btn sp-rate__btn--ghost">{{ __('Cancel') }}</a>
                    </div>
                </form>
            @endif
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .sp-rate{display:grid;gap:16px;}
        .sp-rate__head{display:flex;align-items:center;justify-content:space-between;gap:12px;}
        .sp-rate__title{margin:0;font-weight:1000;color:#111827;}
        .sp-rate__back{font-weight:900;color:#0e5c93;text-decoration:none;border:1px solid #e5e7eb;border-radius:12px;padding:8px 12px;background:#fff;}
        .sp-rate__back:hover{background:#f9fafb;}

        .sp-rate__card{background:#fff;border:1px solid #e5e7eb;border-radius:18px;padding:18px;box-shadow:0 18px 44px rgba(15,23,42,0.08);}
        .sp-rate__meta{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:12px;margin-bottom:16px;}
        .sp-rate__meta-item{background:#f9fafb;border:1px solid #eef2f7;border-radius:14px;padding:12px;}
        .sp-rate__label{display:block;font-size:12px;font-weight:1000;color:#6b7280;text-transform:uppercase;letter-spacing:.06em;margin-bottom:4px;}
        .sp-rate__value{display:block;font-weight:900;color:#111827;}

        .sp-rate__field{margin-top:14px;}
        .sp-rate__field-label{display:block;margin:0 0 8px;font-weight:1000;color:#111827;}
        .sp-rate textarea{
            width:100%;
            border-radius:14px;
            border:1px solid #e5e7eb;
            padding:12px 14px;
            font-weight:700;
            color:#111827;
            background:#fff;
            outline:none;
        }
        .sp-rate textarea:focus{border-color:#f6a105;box-shadow:0 0 0 3px rgba(246,161,5,0.18);}

        .sp-rate__stars{display:inline-flex;flex-direction:row-reverse;gap:6px;}
        .sp-rate__stars input{display:none;}
        .sp-rate__stars label{
            font-size:28px;
            line-height:1;
            cursor:pointer;
            color:#e5e7eb;
            transition:color .15s ease, transform .15s ease;
            user-select:none;
        }
        .sp-rate__stars label:hover{transform:translateY(-1px);}
        .sp-rate__stars input:checked ~ label,
        .sp-rate__stars label:hover,
        .sp-rate__stars label:hover ~ label{color:#f6a105;}

        .sp-rate__error{margin-top:6px;color:#dc2626;font-weight:900;}

        .sp-rate__actions{display:flex;gap:10px;flex-wrap:wrap;margin-top:16px;}
        .sp-rate__btn{
            border-radius:999px;
            padding:10px 16px;
            font-weight:1000;
            background:#0e5c93;
            border:1px solid #0e5c93;
            color:#fff;
            text-decoration:none;
            cursor:pointer;
        }
        .sp-rate__btn:hover{opacity:.92;color:#fff;}
        .sp-rate__btn--ghost{
            background:#fff;
            color:#0e5c93;
            border-color:#e5e7eb;
        }
        .sp-rate__btn--ghost:hover{background:#f9fafb;color:#0e5c93;}

        .sp-rate__done{display:grid;gap:8px;padding:12px 0;}
        .sp-rate__done-pill{display:inline-flex;align-items:center;gap:8px;width:fit-content;border-radius:999px;padding:10px 14px;font-weight:1000;background:#fff7e6;border:1px solid rgba(246,161,5,0.35);color:#111827;}
        .sp-rate__done-pill i{color:#f6a105;}
        .sp-rate__done-text{margin:0;color:#6b7280;font-weight:800;}

        @media(max-width:991px){
            .sp-rate__meta{grid-template-columns:1fr;}
        }
    </style>
@endpush
