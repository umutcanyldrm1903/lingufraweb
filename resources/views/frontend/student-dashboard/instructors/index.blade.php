@extends('frontend.student-dashboard.layouts.master')

@section('dashboard-contents')
    @php
        $activeTags = request('tag', []);
        if (!is_array($activeTags)) {
            $activeTags = [$activeTags];
        }
        $activeTags = array_values(array_filter($activeTags, function ($value) {
            return $value !== null && $value !== '';
        }));
        $searchValue = (string) request('search', '');
    @endphp
    <section class="cowboy-instructor-page" id="student-instructors">
        <div class="ci-frame">
            <div class="ci-headline">
                <div class="sp-panel__title">
                    <i class="fas fa-user-tie"></i>
                    {{ __('Instructors') }}
                </div>
            </div>
            <form method="get" action="{{ route('student.instructors') }}" class="ci-form">
                <div class="row g-4">
                    <div class="col-lg-4">
                        <div class="ci-filter">
                            <div class="ci-filter__actions">
                                <button type="submit" class="ci-filter__btn">{{ __('Search') }}</button>
                                <a href="{{ route('student.instructors') }}" class="ci-filter__btn ci-filter__btn--ghost">{{ __('View All') }}</a>
                            </div>
                            <div class="ci-filter-block">
                                <p class="label">{{ __('Nationality') }}</p>
                                <label class="ci-option ci-option--round">
                                    <input type="checkbox" name="tag[]" value="nationality_turkish" {{ in_array('nationality_turkish', $activeTags, true) || in_array(__('Turkish'), $activeTags, true) ? 'checked' : '' }}>
                                    <span>{{ __('Turkish') }}</span>
                                </label>
                                <label class="ci-option ci-option--round">
                                    <input type="checkbox" name="tag[]" value="nationality_foreign" {{ in_array('nationality_foreign', $activeTags, true) || in_array(__('Foreign'), $activeTags, true) ? 'checked' : '' }}>
                                    <span>{{ __('Foreign') }}</span>
                                </label>
                            </div>
                            <div class="ci-filter-block">
                                <p class="label">{{ __('Speaks Turkish') }}</p>
                                <label class="ci-option ci-option--round">
                                    <input type="checkbox" name="tag[]" value="speaks_turkish_yes" {{ in_array('speaks_turkish_yes', $activeTags, true) || in_array(__('Turkish Language'), $activeTags, true) ? 'checked' : '' }}>
                                    <span>{{ __('Yes') }}</span>
                                </label>
                                <label class="ci-option ci-option--round">
                                    <input type="checkbox" name="tag[]" value="speaks_turkish_no" {{ in_array('speaks_turkish_no', $activeTags, true) || in_array(__('English'), $activeTags, true) ? 'checked' : '' }}>
                                    <span>{{ __('No') }}</span>
                                </label>
                            </div>
                            <div class="ci-filter-block">
                                <p class="label">{{ __('Category') }}</p>
                                <label class="ci-option ci-option--square">
                                    <input type="checkbox" name="tag[]" value="category_general" {{ in_array('category_general', $activeTags, true) || in_array(__('General English'), $activeTags, true) ? 'checked' : '' }}>
                                    <span>{{ __('General English') }}</span>
                                </label>
                                <label class="ci-option ci-option--square">
                                    <input type="checkbox" name="tag[]" value="category_speaking" {{ in_array('category_speaking', $activeTags, true) || in_array(__('Speaking Lessons'), $activeTags, true) ? 'checked' : '' }}>
                                    <span>{{ __('Speaking Lessons') }}</span>
                                </label>
                                <label class="ci-option ci-option--square">
                                    <input type="checkbox" name="tag[]" value="category_kids" {{ in_array('category_kids', $activeTags, true) || in_array(__('For Kids'), $activeTags, true) ? 'checked' : '' }}>
                                    <span>{{ __('For Kids') }}</span>
                                </label>
                                <label class="ci-option ci-option--square">
                                    <input type="checkbox" name="tag[]" value="category_exam" {{ in_array('category_exam', $activeTags, true) || in_array(__('IELTS & TOEFL'), $activeTags, true) ? 'checked' : '' }}>
                                    <span>{{ __('IELTS & TOEFL') }}</span>
                                </label>
                                <label class="ci-option ci-option--square">
                                    <input type="checkbox" name="tag[]" value="category_business" {{ in_array('category_business', $activeTags, true) || in_array(__('Business English'), $activeTags, true) ? 'checked' : '' }}>
                                    <span>{{ __('Business English') }}</span>
                                </label>
                            </div>
                            <div class="ci-filter-block">
                                <p class="label">{{ __('Availability') }}</p>
                                <label class="ci-option ci-option--square">
                                    <input type="checkbox" name="tag[]" value="availability_morning" {{ in_array('availability_morning', $activeTags, true) || in_array('06:00', $activeTags, true) ? 'checked' : '' }}>
                                    <span>{{ __('Morning (06:00-12:00)') }}</span>
                                </label>
                                <label class="ci-option ci-option--square">
                                    <input type="checkbox" name="tag[]" value="availability_afternoon" {{ in_array('availability_afternoon', $activeTags, true) || in_array('12:00', $activeTags, true) ? 'checked' : '' }}>
                                    <span>{{ __('Afternoon (12:00-18:00)') }}</span>
                                </label>
                                <label class="ci-option ci-option--square">
                                    <input type="checkbox" name="tag[]" value="availability_evening" {{ in_array('availability_evening', $activeTags, true) || in_array('18:00', $activeTags, true) ? 'checked' : '' }}>
                                    <span>{{ __('Evening (18:00-24:00)') }}</span>
                                </label>
                            </div>
                            @if (!empty($activeTags) || $searchValue !== '')
                                <div class="mt-2">
                                    <a href="{{ route('student.instructors') }}" class="ci-filter__link">{{ __('Clear Filters') }}</a>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="col-lg-8">
                        <div class="ci-search">
                            <i class="fas fa-search" aria-hidden="true"></i>
                            <input type="search" name="search" placeholder="{{ __('Search here') }}" value="{{ $searchValue }}" aria-label="{{ __('Search') }}">
                        </div>
                        <div class="ci-list">
                            @forelse ($instructors as $instructor)
                                @php
                                    $displayName = \Illuminate\Support\Str::before($instructor->name, ' ') ?: $instructor->name;
                                    $profileUrl = route('instructor-details', ['id' => $instructor->id, 'slug' => \Illuminate\Support\Str::slug($instructor->name)]);
                                    $instructorImage = !empty($instructor->image) ? asset($instructor->image) : asset('frontend/img/instructor/instructor01.png');
                                    $avgRating = (float) ($instructor->avg_live_rating ?? 0);
                                    $teachMap = [
                                        'speaking_b1' => __('Speaking Lessons'),
                                        'general_english_a1' => __('General English'),
                                        'kids_6_12' => __('For Kids'),
                                        'young_13_18' => __('For Kids'),
                                        'business_english' => __('Business English'),
                                        'exams' => __('IELTS & TOEFL'),
                                    ];
                                    $teachKeys = collect((array) data_get($instructor->instructor_profile, 'can_teach', []));
                                    $dynamicTags = $teachKeys
                                        ->map(fn ($key) => $teachMap[$key] ?? null)
                                        ->filter()
                                        ->unique()
                                        ->take(3)
                                        ->values();
                                    if ($dynamicTags->isEmpty()) {
                                        $dynamicTags = collect([__('General English')]);
                                    }
                                @endphp
                                <div class="ci-card">
                                    <button class="ci-fav" type="button" aria-label="{{ __('Wishlist') }}">
                                        <i class="far fa-heart"></i>
                                    </button>
                                    <div class="ci-avatar">
                                        <a href="{{ $profileUrl }}" class="ci-link">
                                            <img src="{{ $instructorImage }}" alt="{{ $displayName }}">
                                        </a>
                                    </div>
                                    <div class="ci-body">
                                        <div class="ci-head">
                                            <div>
                                                <h4><a href="{{ $profileUrl }}" class="ci-link">{{ $displayName }}</a></h4>
                                                <p class="ci-role">{{ $instructor->job_title }}</p>
                                            </div>
                                        </div>
                                        <div class="ci-tags">
                                            @foreach ($dynamicTags as $tagLabel)
                                                <span>{{ $tagLabel }}</span>
                                            @endforeach
                                        </div>
                                        <p class="ci-text">
                                            {{ \Illuminate\Support\Str::limit(strip_tags($instructor->about ?? $instructor->job_title), 180) }}
                                        </p>
                                        <a class="ci-more" href="{{ $profileUrl }}">{{ __('View more') }}</a>
                                        <div class="ci-actions">
                                            <span class="ci-rating"><i class="fas fa-star"></i>
                                                {{ number_format($avgRating, 1) }} / 5
                                            </span>
                                            <div class="ci-buttons">
                                                <a class="ci-btn" href="{{ route('student.instructors.schedule', ['instructor' => $instructor->id]) }}">{{ __('Schedule Lesson') }}</a>
                                                <a class="ci-btn ci-btn--ghost" href="{{ $profileUrl }}">{{ __('Profile') }}</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="sp-empty">{{ __('No instructors found yet.') }}</div>
                            @endforelse
                        </div>
                        <nav class="pagination__wrap mt-25">
                            {{ $instructors->links() }}
                        </nav>
                    </div>
                </div>
            </form>
        </div>
    </section>
@endsection

@push('styles')
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Sora:wght@400;600;700;800&display=swap');

        .sp-panel__title{display:flex;align-items:center;gap:10px;font-weight:900;color:#0f172a;}
        .sp-panel__title i{color:var(--ci-accent);}
        .sp-empty{color:#64748b;font-weight:800;}

        .cowboy-instructor-page{
            --ci-ink:#0f172a;
            --ci-muted:#64748b;
            --ci-accent:#f59e0b;
            --ci-accent-strong:#f97316;
            --ci-blue:#0e5c93;
            --ci-cream:#fff7e6;
            --ci-card:#ffffff;
            font-family:"Sora", sans-serif;
            position:relative;
            padding:28px;
            border-radius:22px;
            background:radial-gradient(900px circle at 0% 0%, rgba(245,158,11,.18), transparent 55%),
                       radial-gradient(700px circle at 100% 12%, rgba(14,92,147,.12), transparent 60%),
                       linear-gradient(180deg, #fff7e6 0%, #ffffff 55%, #f8fafc 100%);
            overflow:hidden;
            box-shadow:0 30px 60px rgba(15,23,42,.08);
        }
        .cowboy-instructor-page::before{
            content:"";
            position:absolute;
            inset:0;
            background:repeating-linear-gradient(135deg, rgba(15,23,42,.04) 0 2px, transparent 2px 10px);
            opacity:.35;
            pointer-events:none;
        }
        .cowboy-instructor-page > *{position:relative;z-index:1;}

        .ci-frame{
            background:var(--ci-card);
            border-radius:20px;
            padding:24px;
            border:1px solid rgba(15,23,42,.08);
            box-shadow:0 20px 40px rgba(15,23,42,.08);
        }
        .ci-headline{display:flex;align-items:center;gap:10px;margin-bottom:18px;}
        .ci-headline .sp-panel__title{margin:0;}

        .ci-form{margin:0;}
        .ci-filter{
            background:linear-gradient(180deg, #ffffff 0%, #fff8ec 100%);
            border:1px solid rgba(15,23,42,.08);
            border-radius:18px;
            padding:18px;
            box-shadow:0 16px 32px rgba(15,23,42,.08);
        }
        .ci-filter__actions{
            display:grid;
            grid-template-columns:1fr 1fr;
            gap:10px;
            margin-bottom:16px;
        }
        .ci-filter__btn{
            border-radius:12px;
            padding:10px 12px;
            font-weight:800;
            border:1px solid var(--ci-accent);
            background:linear-gradient(135deg, var(--ci-accent), var(--ci-accent-strong));
            color:#fff;
            text-align:center;
            text-decoration:none;
            box-shadow:0 12px 22px rgba(245,158,11,.25);
            transition:transform .2s ease, box-shadow .2s ease;
        }
        .ci-filter__btn--ghost{
            background:#fff;
            color:var(--ci-accent);
            box-shadow:none;
        }
        .ci-filter__btn:hover{transform:translateY(-1px);box-shadow:0 16px 26px rgba(245,158,11,.28);color:#fff;}
        .ci-filter__btn--ghost:hover{background:#fff3d6;color:var(--ci-accent);box-shadow:none;}
        .ci-filter__link{display:inline-flex;align-items:center;gap:6px;color:var(--ci-accent);font-weight:800;text-decoration:none;}
        .ci-filter__link:hover{text-decoration:underline;}

        .ci-filter-block{margin-bottom:18px;}
        .ci-filter-block .label{font-weight:900;color:var(--ci-accent);margin-bottom:10px;font-size:12px;letter-spacing:.08em;text-transform:uppercase;}
        .ci-option{
            display:flex;
            align-items:center;
            gap:10px;
            font-weight:700;
            color:var(--ci-ink);
            margin-bottom:8px;
            padding:8px 10px;
            border:1px solid rgba(15,23,42,.08);
            border-radius:12px;
            background:#fff;
            transition:border-color .2s ease, box-shadow .2s ease;
        }
        .ci-option:hover{border-color:rgba(245,158,11,.5);box-shadow:0 8px 18px rgba(245,158,11,.12);}
        .ci-option input{
            appearance:none;
            width:16px;
            height:16px;
            border:2px solid #e2e8f0;
            border-radius:6px;
            display:grid;
            place-items:center;
            background:#fff;
        }
        .ci-option--round input{border-radius:999px;}
        .ci-option input:checked{
            background:linear-gradient(135deg, var(--ci-accent), var(--ci-accent-strong));
            border-color:transparent;
        }
        .ci-option input:checked::after{
            content:"";
            width:6px;
            height:10px;
            border-right:2px solid #fff;
            border-bottom:2px solid #fff;
            transform:rotate(45deg);
            margin-top:-2px;
        }

        .ci-search{
            display:flex;
            align-items:center;
            gap:10px;
            background:#fff;
            border:1px solid rgba(15,23,42,.1);
            border-radius:14px;
            padding:12px 14px;
            margin-bottom:18px;
            box-shadow:0 14px 30px rgba(15,23,42,.08);
        }
        .ci-search i{color:var(--ci-muted);}
        .ci-search input{
            border:0;
            outline:0;
            flex:1;
            font-weight:700;
            color:var(--ci-ink);
        }

        .ci-list{display:grid;gap:18px;}
        .ci-card{
            position:relative;
            display:grid;
            grid-template-columns:140px 1fr;
            gap:18px;
            background:linear-gradient(180deg, #ffffff 0%, #fffaf0 100%);
            border:1px solid rgba(15,23,42,.08);
            border-radius:20px;
            padding:20px;
            box-shadow:0 18px 36px rgba(15,23,42,.1);
            transition:transform .2s ease, box-shadow .2s ease;
            animation:ci-rise .45s ease both;
        }
        .ci-card:hover{transform:translateY(-3px);box-shadow:0 24px 44px rgba(15,23,42,.14);}
        .ci-fav{
            position:absolute;
            right:16px;
            top:16px;
            border:1px solid rgba(15,23,42,.2);
            border-radius:12px;
            background:#fff;
            width:32px;
            height:32px;
            display:grid;
            place-items:center;
            color:var(--ci-ink);
        }
        .ci-avatar{
            width:130px;
            height:130px;
            border-radius:18px;
            padding:4px;
            background:linear-gradient(135deg, rgba(245,158,11,.3), rgba(14,92,147,.2));
        }
        .ci-avatar img{width:100%;height:100%;object-fit:cover;border-radius:14px;}
        .ci-link{color:inherit;text-decoration:none;display:inline-block;}
        .ci-link:hover{text-decoration:none;}
        .ci-head h4{margin:0;font-weight:900;font-size:18px;}
        .ci-role{margin:6px 0 0;color:var(--ci-muted);font-weight:700;font-size:12px;}
        .ci-tags{display:flex;flex-wrap:wrap;gap:8px;margin:12px 0 8px;}
        .ci-tags span{
            background:rgba(245,158,11,.15);
            color:var(--ci-ink);
            font-weight:800;
            font-size:11px;
            padding:6px 10px;
            border-radius:999px;
            border:1px solid rgba(245,158,11,.3);
        }
        .ci-text{margin:0 0 8px;color:var(--ci-ink);font-weight:600;}
        .ci-more{color:var(--ci-ink);font-weight:800;text-decoration:none;border-bottom:1px solid rgba(15,23,42,.3);font-size:12px;}
        .ci-actions{display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap;margin-top:10px;}
        .ci-rating{font-weight:800;color:var(--ci-accent);}
        .ci-buttons{display:flex;gap:10px;flex-wrap:wrap;}
        .ci-btn{
            background:linear-gradient(135deg, var(--ci-accent), var(--ci-accent-strong));
            color:#fff;
            border-radius:12px;
            padding:9px 16px;
            font-weight:800;
            text-decoration:none;
            border:1px solid transparent;
            box-shadow:0 10px 20px rgba(245,158,11,.2);
        }
        .ci-btn--ghost{
            background:#fff;
            color:var(--ci-accent);
            border:1px solid rgba(245,158,11,.4);
            box-shadow:none;
        }

        @keyframes ci-rise{from{opacity:0;transform:translateY(12px);}to{opacity:1;transform:translateY(0);}}
        @media (prefers-reduced-motion: reduce){
            .ci-card{animation:none;}
            .ci-card:hover{transform:none;}
            .ci-filter__btn{transition:none;}
        }

        @media(max-width:991px){
            .cowboy-instructor-page{padding:18px;}
            .ci-filter{position:static;}
            .ci-filter__actions{grid-template-columns:1fr;}
            .ci-card{grid-template-columns:1fr;}
            .ci-avatar{width:110px;height:110px;}
            .ci-buttons{width:100%;}
            .ci-btn, .ci-btn--ghost{flex:1;text-align:center;}
        }
    </style>
@endpush
