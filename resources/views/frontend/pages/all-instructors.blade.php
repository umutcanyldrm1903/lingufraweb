@extends('frontend.layouts.master')
@section('meta_title', __('All Instructors') . ' || ' . $setting->app_name)
@section('contents')
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

    <section class="cowboy-instructor-page section-py-100">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-4 col-xl-3">
                    <div class="ci-filter">
                        <h4>{{ __('Filter') }}</h4>
                        <form method="get" action="{{ route('all-instructors') }}">
                            <div class="ci-filter-block">
                                <p class="label">{{ __('Search') }}</p>
                                <div class="ci-search">
                                    <i class="fas fa-search" aria-hidden="true"></i>
                                    <input type="search" name="search" placeholder="{{ __('Search here') }}" value="{{ $searchValue }}" aria-label="{{ __('Search') }}">
                                </div>
                            </div>
                            <div class="ci-filter-block">
                                <p class="label">{{ __('Nationality') }}</p>
                                <div class="chips">
                                    <label class="chip ci-chip-check {{ in_array('nationality_turkish', $activeTags, true) || in_array(__('Turkish'), $activeTags, true) ? 'is-active' : '' }}">
                                        <input type="checkbox" name="tag[]" value="nationality_turkish" {{ in_array('nationality_turkish', $activeTags, true) || in_array(__('Turkish'), $activeTags, true) ? 'checked' : '' }}>
                                        <span>{{ __('Turkish') }}</span>
                                    </label>
                                    <label class="chip ci-chip-check {{ in_array('nationality_foreign', $activeTags, true) || in_array(__('Foreign'), $activeTags, true) ? 'is-active' : '' }}">
                                        <input type="checkbox" name="tag[]" value="nationality_foreign" {{ in_array('nationality_foreign', $activeTags, true) || in_array(__('Foreign'), $activeTags, true) ? 'checked' : '' }}>
                                        <span>{{ __('Foreign') }}</span>
                                    </label>
                                </div>
                            </div>
                            <div class="ci-filter-block">
                                <p class="label">{{ __('Speaks Turkish') }}</p>
                                <div class="chips">
                                    <label class="chip ci-chip-check {{ in_array('speaks_turkish_yes', $activeTags, true) || in_array(__('Turkish Language'), $activeTags, true) ? 'is-active' : '' }}">
                                        <input type="checkbox" name="tag[]" value="speaks_turkish_yes" {{ in_array('speaks_turkish_yes', $activeTags, true) || in_array(__('Turkish Language'), $activeTags, true) ? 'checked' : '' }}>
                                        <span>{{ __('Yes') }}</span>
                                    </label>
                                    <label class="chip ci-chip-check {{ in_array('speaks_turkish_no', $activeTags, true) || in_array(__('English'), $activeTags, true) ? 'is-active' : '' }}">
                                        <input type="checkbox" name="tag[]" value="speaks_turkish_no" {{ in_array('speaks_turkish_no', $activeTags, true) || in_array(__('English'), $activeTags, true) ? 'checked' : '' }}>
                                        <span>{{ __('No') }}</span>
                                    </label>
                                </div>
                            </div>
                            <div class="ci-filter-block">
                                <p class="label">{{ __('Category') }}</p>
                                <div class="chips">
                                    <label class="chip ci-chip-check {{ in_array('category_general', $activeTags, true) || in_array(__('General English'), $activeTags, true) ? 'is-active' : '' }}">
                                        <input type="checkbox" name="tag[]" value="category_general" {{ in_array('category_general', $activeTags, true) || in_array(__('General English'), $activeTags, true) ? 'checked' : '' }}>
                                        <span>{{ __('General English') }}</span>
                                    </label>
                                    <label class="chip ci-chip-check {{ in_array('category_speaking', $activeTags, true) || in_array(__('Speaking Lessons'), $activeTags, true) ? 'is-active' : '' }}">
                                        <input type="checkbox" name="tag[]" value="category_speaking" {{ in_array('category_speaking', $activeTags, true) || in_array(__('Speaking Lessons'), $activeTags, true) ? 'checked' : '' }}>
                                        <span>{{ __('Speaking Lessons') }}</span>
                                    </label>
                                    <label class="chip ci-chip-check {{ in_array('category_kids', $activeTags, true) || in_array(__('For Kids'), $activeTags, true) ? 'is-active' : '' }}">
                                        <input type="checkbox" name="tag[]" value="category_kids" {{ in_array('category_kids', $activeTags, true) || in_array(__('For Kids'), $activeTags, true) ? 'checked' : '' }}>
                                        <span>{{ __('For Kids') }}</span>
                                    </label>
                                    <label class="chip ci-chip-check {{ in_array('category_exam', $activeTags, true) || in_array(__('IELTS & TOEFL'), $activeTags, true) ? 'is-active' : '' }}">
                                        <input type="checkbox" name="tag[]" value="category_exam" {{ in_array('category_exam', $activeTags, true) || in_array(__('IELTS & TOEFL'), $activeTags, true) ? 'checked' : '' }}>
                                        <span>{{ __('IELTS & TOEFL') }}</span>
                                    </label>
                                    <label class="chip ci-chip-check {{ in_array('category_business', $activeTags, true) || in_array(__('Business English'), $activeTags, true) ? 'is-active' : '' }}">
                                        <input type="checkbox" name="tag[]" value="category_business" {{ in_array('category_business', $activeTags, true) || in_array(__('Business English'), $activeTags, true) ? 'checked' : '' }}>
                                        <span>{{ __('Business English') }}</span>
                                    </label>
                                </div>
                            </div>
                            <div class="ci-filter-block">
                                <p class="label">{{ __('Availability') }}</p>
                                <div class="chips">
                                    <label class="chip ci-chip-check {{ in_array('availability_morning', $activeTags, true) || in_array('06:00', $activeTags, true) ? 'is-active' : '' }}">
                                        <input type="checkbox" name="tag[]" value="availability_morning" {{ in_array('availability_morning', $activeTags, true) || in_array('06:00', $activeTags, true) ? 'checked' : '' }}>
                                        <span>{{ __('Morning (06:00-12:00)') }}</span>
                                    </label>
                                    <label class="chip ci-chip-check {{ in_array('availability_afternoon', $activeTags, true) || in_array('12:00', $activeTags, true) ? 'is-active' : '' }}">
                                        <input type="checkbox" name="tag[]" value="availability_afternoon" {{ in_array('availability_afternoon', $activeTags, true) || in_array('12:00', $activeTags, true) ? 'checked' : '' }}>
                                        <span>{{ __('Afternoon (12:00-18:00)') }}</span>
                                    </label>
                                    <label class="chip ci-chip-check {{ in_array('availability_evening', $activeTags, true) || in_array('18:00', $activeTags, true) ? 'is-active' : '' }}">
                                        <input type="checkbox" name="tag[]" value="availability_evening" {{ in_array('availability_evening', $activeTags, true) || in_array('18:00', $activeTags, true) ? 'checked' : '' }}>
                                        <span>{{ __('Evening (18:00-24:00)') }}</span>
                                    </label>
                                </div>
                            </div>
                            <div class="ci-filter-actions">
                                <button type="submit" class="ci-apply">{{ __('Search') }}</button>
                                <a href="{{ route('all-instructors') }}" class="ci-clear">{{ __('View All') }}</a>
                            </div>
                            @if (!empty($activeTags) || $searchValue !== '')
                                <div class="mt-2">
                                    <a href="{{ route('all-instructors') }}" class="chip" style="background:#fff;border:1px solid #f39c12;">{{ __('Clear Filters') }}</a>
                                </div>
                            @endif
                        </form>
                    </div>
                </div>
                <div class="col-lg-8 col-xl-9">
                    <div class="ci-list">
                        @forelse ($instructors as $instructor)
                            @php
                                $profileUrl = route('instructor-details', ['id' => $instructor->id, 'slug' => Str::slug($instructor->name)]);
                                $instructorImage = !empty($instructor->image) ? asset($instructor->image) : asset('frontend/img/instructor/instructor01.png');
                                $displayName = $instructor->first_name ?: $instructor->name;
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
                                        <a class="ci-profile" href="{{ $profileUrl }}">{{ __('Profile') }}</a>
                                    </div>
                                    <div class="ci-tags">
                                        @foreach ($dynamicTags as $tagLabel)
                                            <span>{{ $tagLabel }}</span>
                                        @endforeach
                                    </div>
                                    <p class="ci-text">
                                        {{ Str::limit(strip_tags($instructor->about ?? $instructor->job_title), 180) }}
                                    </p>
                                    <div class="ci-actions">
                                        <span class="ci-rating"><i class="fas fa-star"></i>
                                            {{ number_format($avgRating, 1) }} / 5
                                        </span>
                                        <a class="ci-btn" href="{{ route('student.instructors.schedule', ['instructor' => $instructor->id]) }}">{{ __('Schedule Lesson') }}</a>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="ci-empty">{{ __('No instructors found yet.') }}</div>
                        @endforelse
                    </div>
                    <nav class="pagination__wrap mt-25">
                        {{ $instructors->links() }}
                    </nav>
                </div>
            </div>
        </div>
    </section>

@endsection

@push('styles')
<style>
    /* Make the public instructors page feel more spacious on wide screens */
    .cowboy-instructor-page {
        background: #f4f6fb;
        padding: 70px 0 120px;
    }

    .cowboy-instructor-page .container {
        max-width: 1480px;
    }

    .ci-filter {background:#fff;border:1px solid #e4e7f0;border-radius:22px;padding:20px;box-shadow:0 12px 30px rgba(0,0,0,0.06);}
    .ci-filter h4 {font-weight:900;font-size:18px;margin-bottom:12px;}
    .ci-filter-block {margin-bottom:16px;}
    .ci-filter-block .label {font-weight:900;color:#111827;margin-bottom:8px;}
    .chips {display:flex;flex-wrap:wrap;gap:8px;}
    .chip {background:#ffe3bf;color:#1c1c1c;font-weight:800;border-radius:12px;padding:6px 10px;font-size:12px;border:none;cursor:pointer;}
    .chip:focus {outline:2px solid #f39c12;}
    .ci-chip-check {display:inline-flex;align-items:center;gap:6px;}
    .ci-chip-check input {width:14px;height:14px;}
    .ci-chip-check.is-active {background:#f39c12;color:#fff;}
    .ci-search {display:flex;align-items:center;gap:8px;background:#fff;border:1px solid #e4e7f0;border-radius:12px;padding:10px 12px;}
    .ci-search i {color:#6b7280;}
    .ci-search input {border:0;outline:0;flex:1;font-weight:700;color:#111827;}
    .ci-filter-actions {display:flex;gap:8px;flex-wrap:wrap;margin-top:8px;}
    .ci-apply,.ci-clear {border-radius:12px;padding:8px 12px;font-weight:800;text-decoration:none;display:inline-flex;align-items:center;justify-content:center;}
    .ci-apply {border:1px solid #f39c12;background:#f39c12;color:#fff;}
    .ci-clear {border:1px solid #f39c12;background:#fff;color:#f39c12;}
    .ci-checks {list-style:none;padding:0;margin:0;display:grid;gap:6px;color:#333;font-weight:600;}
    .ci-list {display:grid;gap:16px;}
    .ci-card {display:grid;grid-template-columns:140px 1fr;gap:20px;background:#fff;border:1px solid #e4e7f0;border-radius:22px;padding:20px;box-shadow:0 14px 38px rgba(0,0,0,0.08);}
    .ci-avatar img {width:140px;height:140px;object-fit:cover;border-radius:20px;}
    .ci-link {color:inherit;text-decoration:none;display:inline-block;}
    .ci-link:hover {text-decoration:none;}
    .ci-head {display:flex;justify-content:space-between;align-items:flex-start;gap:10px;}
    .ci-head h4 {margin:0;font-weight:900;font-size:20px;}
    .ci-role {margin:2px 0 0;color:#6b7280;font-weight:800;font-size:13px;}
    .ci-profile {background:#fff;border:1px solid #f39c12;color:#f39c12;font-weight:800;border-radius:12px;padding:8px 12px;text-decoration:none;}
    .ci-tags {display:flex;flex-wrap:wrap;gap:8px;margin:8px 0;}
    .ci-tags span {background:#ffb84f;color:#1c1c1c;font-weight:800;font-size:12px;padding:6px 10px;border-radius:12px;}
    .ci-text {margin:0 0 12px;color:#333;}
    .ci-actions {display:flex;align-items:center;justify-content:space-between;gap:10px;flex-wrap:wrap;}
    .ci-rating {font-weight:800;color:#f39c12;}
    .ci-btn {background:#f39c12;color:#fff;border-radius:12px;padding:10px 14px;font-weight:800;text-decoration:none;}
    .ci-empty {background:#fff;border:1px dashed #d1d5db;border-radius:14px;padding:16px;color:#6b7280;font-weight:800;}
    @media(max-width:991px){.cowboy-instructor-page{padding:40px 0 70px;}.ci-card{grid-template-columns:1fr;grid-template-rows:auto;justify-items:start;}.ci-avatar img{width:110px;height:110px;}}
</style>
@endpush
