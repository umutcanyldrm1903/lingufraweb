@extends('frontend.layouts.master')
@php
    $courseDescription = $course->seo_description
        ?: \Illuminate\Support\Str::of(strip_tags((string) $course->description))->squish()->limit(160, '...')->toString();
    $courseKeywords = collect([
        $course?->title,
        $course?->category?->translation?->name,
        $course?->instructor?->first_name ?? $course?->instructor?->name,
        $setting->app_name,
        'online course',
        'english lesson',
    ])->filter()->implode(', ');
    $courseRatingValue = number_format((float) $course->reviews()->where('status', 1)->avg('rating'), 1, '.', '');
    $courseRatingCount = (int) $course->reviews()->where('status', 1)->count();
    $coursePrice = $course->discount ?: $course->price;
@endphp
@section('meta_title', $course?->title . ' || ' . $setting->app_name)
@section('meta_description', $courseDescription)
@section('meta_keywords', $courseKeywords)
@section('canonical_url', route('course.show', $course->slug))
@section('meta_image', $course->thumbnail)
@push('styles')
    <link rel="stylesheet" href="{{ asset('frontend/css/shareon.min.css') }}">
    <style>
        .courses__details-links {
            margin-top: 24px;
            padding: 24px;
            border-radius: 24px;
            background: #f7fbff;
            border: 1px solid rgba(14, 92, 147, 0.1);
        }

        .courses__details-links .title {
            margin-bottom: 14px;
        }

        .courses__details-links-list {
            display: grid;
            gap: 10px;
            margin: 0;
            padding: 0;
            list-style: none;
        }

        .courses__details-links-list a {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 14px 16px;
            border-radius: 16px;
            background: #fff;
            border: 1px solid rgba(14, 92, 147, 0.1);
            color: var(--tg-heading-color);
            font-weight: 800;
        }

        .courses__details-links-list a:hover {
            border-color: rgba(246, 161, 5, 0.48);
            color: var(--tg-theme-primary);
        }
    </style>
@endpush
@section('contents')
    <!-- breadcrumb-area -->
    <x-frontend.breadcrumb :title="__('Course Details')" :links="[
        ['url' => route('home'), 'text' => __('Home')],
        ['url' => route('courses'), 'text' => __('Courses')],
        ['url' => '', 'text' => $course?->title],
    ]" />
    <!-- breadcrumb-area-end -->

    <!-- courses-details-area -->
    <section class="courses__details-area section-py-120">
        <div class="container">
            <div class="row">
                <div class="col-xl-9 col-lg-8">
                    <div class="courses__details-thumb">
                        <img class="w-100" src="{{ asset($course->thumbnail) }}" alt="img">
                        @if ($course->demo_video_source)
                            <a href="{{ $course->demo_video_source }}" class="popup-video"
                                aria-label="{{ $course?->title }}"><i class="fas fa-play"></i></a>
                        @endif
                    </div>
                    <div class="courses__details-content">
                        <ul class="courses__item-meta list-wrap">
                            <li class="courses__item-tag">
                                <a
                                    href="{{ route('courses', ['category' => $course->category->id]) }}">{{ $course->category->translation->name }}</a>
                            </li>
                            <li class="avg-rating"><i class="fas fa-star"></i>
                                {{ number_format($course->reviews()->avg('rating'), 1) ?? 0 }} {{ __('Reviews') }}</li>
                            <li class="courses__wishlist">
                                <a href="javascript:;" class="wsus-wishlist-btn" aria-label="WishList"
                                    data-slug="{{ $course?->slug }}">
                                    <i class="{{ $course?->favorite_by_client ? 'fas' : 'far' }} fa-heart"></i>
                                </a>
                            </li>
                        </ul>
                        <h2 class="title">{{ $course?->title }}</h2>
                        <div class="courses__details-meta">
                            <ul class="list-wrap">
                                <li class="author-two">
                                    <img src="{{ asset($course->instructor->image) }}" alt="img"
                                        class="instructor-avatar">
                                    {{ __('By') }}
                                    <a
                                        href="{{ route('instructor-details', $course->instructor->id) }}">{{ $course->instructor->first_name }}</a>
                                </li>
                                <li class="date"><i
                                        class="flaticon-calendar"></i>{{ formatDate($course->created_at, 'd/M/Y') }}</li>
                                <li><i class="flaticon-mortarboard"></i>{{ $course->enrollments->count() }}
                                    {{ __('Students') }}</li>
                            </ul>
                        </div>
                        <ul class="nav nav-tabs" id="myTab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="overview-tab" data-bs-toggle="tab"
                                    data-bs-target="#overview-tab-pane" type="button" role="tab"
                                    aria-controls="overview-tab-pane" aria-selected="true">{{ __('Overview') }}</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="curriculum-tab" data-bs-toggle="tab"
                                    data-bs-target="#curriculum-tab-pane" type="button" role="tab"
                                    aria-controls="curriculum-tab-pane"
                                    aria-selected="false">{{ __('Curriculum') }}</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="instructors-tab" data-bs-toggle="tab"
                                    data-bs-target="#instructors-tab-pane" type="button" role="tab"
                                    aria-controls="instructors-tab-pane"
                                    aria-selected="false">{{ __('Instructors') }}</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="reviews-tab" data-bs-toggle="tab"
                                    data-bs-target="#reviews-tab-pane" type="button" role="tab"
                                    aria-controls="reviews-tab-pane" aria-selected="false">{{ __('reviews') }}</button>
                            </li>
                        </ul>
                        <div class="tab-content" id="myTabContent">
                            <div class="tab-pane fade show active" id="overview-tab-pane" role="tabpanel"
                                aria-labelledby="overview-tab" tabindex="0">
                                <div class="courses__overview-wrap">
                                    <h3 class="title">{{ __('Course Description') }}</h3>
                                    {!! clean($course->description) !!}

                                </div>
                            </div>
                            <div class="tab-pane fade" id="curriculum-tab-pane" role="tabpanel"
                                aria-labelledby="curriculum-tab" tabindex="0">
                                <div class="courses__curriculum-wrap">
                                    <h3 class="title">{{ __('Course Curriculum') }}</h3>
                                    <p></p>
                                    <div class="accordion" id="accordionExample">
                                        @foreach ($course->chapters as $chapter)
                                            <div class="accordion-item">
                                                <h2 class="accordion-header" id="heading{{ $chapter->id }}">
                                                    <button class="accordion-button collapsed" type="button"
                                                        data-bs-toggle="collapse"
                                                        data-bs-target="#collapse{{ $chapter->id }}"
                                                        aria-expanded="false"
                                                        aria-controls="collapse{{ $chapter->id }}">
                                                        {{ $loop->iteration }}. {{ $chapter?->title }}
                                                    </button>
                                                </h2>
                                                <div id="collapse{{ $chapter->id }}" class="accordion-collapse collapse"
                                                    aria-labelledby="heading{{ $chapter->id }}"
                                                    data-bs-parent="#accordionExample">
                                                    <div class="accordion-body">
                                                        <ul class="list-wrap">
                                                            @foreach ($chapter->chapterItems as $chapterItem)
                                                                @if ($chapterItem?->type == 'lesson')
                                                                    @if ($chapterItem?->lesson?->is_free == 1)
                                                                        @if ($chapterItem?->lesson?->file_type == 'video')
                                                                            @if ($chapterItem?->lesson->storage == 'google_drive')
                                                                                <li class="course-item open-item">
                                                                                    <a href="javascript:;"
                                                                                        data-bs-toggle="modal"
                                                                                        data-bs-target="#videoModal"
                                                                                        data-bs-video="https://drive.google.com/file/d/{{ extractGoogleDriveVideoId($chapterItem?->lesson->file_path) }}/preview"
                                                                                        class="course-item-link">
                                                                                        <span
                                                                                            class="item-name">{{ $chapterItem?->lesson?->title }}</span>
                                                                                        <div class="course-item-meta">
                                                                                            <span
                                                                                                class="item-meta duration">{{ minutesToHours($chapterItem?->lesson?->duration) }}</span>
                                                                                        </div>
                                                                                    </a>
                                                                                </li>
                                                                            @else
                                                                                <li class="course-item open-item">
                                                                                    <a href="@if(!in_array($chapterItem?->lesson->storage, ['wasabi', 'aws'])){{ $chapterItem?->lesson->file_path }}@else{{ Storage::disk($chapterItem?->lesson->storage)->temporaryUrl($chapterItem?->lesson->file_path, now()->addHours(1)) }}@endif"
                                                                                        class="course-item-link popup-video">
                                                                                        <span
                                                                                            class="item-name">{{ $chapterItem?->lesson?->title }}</span>
                                                                                        <div class="course-item-meta">
                                                                                            <span
                                                                                                class="item-meta duration">{{ minutesToHours($chapterItem?->lesson?->duration) }}</span>
                                                                                        </div>
                                                                                    </a>
                                                                                </li>
                                                                            @endif
                                                                        @else
                                                                            <li class="course-item">
                                                                                <a href="javascript:;"
                                                                                    class="course-item-link">
                                                                                    <span
                                                                                        class="item-name">{{ $chapterItem?->lesson?->title }}</span>
                                                                                    <div class="course-item-meta">
                                                                                        <span class="item-meta duration">
                                                                                            --.-- </span>
                                                                                        <span
                                                                                            class="item-meta course-item-status">
                                                                                            <img src="{{ asset('frontend/img/icons/lock.svg') }}"
                                                                                                alt="icon">
                                                                                        </span>
                                                                                    </div>
                                                                                </a>
                                                                            </li>
                                                                        @endif
                                                                    @else
                                                                        <li class="course-item">
                                                                            <a href="javascript:;"
                                                                                class="course-item-link">
                                                                                <span
                                                                                    class="item-name">{{ $chapterItem?->lesson?->title }}</span>
                                                                                <div class="course-item-meta">
                                                                                    <span
                                                                                        class="item-meta duration">{{ minutesToHours($chapterItem?->lesson?->duration) }}</span>
                                                                                    <span
                                                                                        class="item-meta course-item-status">
                                                                                        <img src="{{ asset('frontend/img/icons/lock.svg') }}"
                                                                                            alt="icon">
                                                                                    </span>
                                                                                </div>
                                                                            </a>
                                                                        </li>
                                                                    @endif
                                                                @elseif($chapterItem?->type == 'live')
                                                                    <li class="course-item">
                                                                        <a href="javascript:;" class="course-item-link">
                                                                            <span
                                                                                class="item-name">{{ $chapterItem?->lesson?->title }}</span>
                                                                            <div class="course-item-meta">
                                                                                <span
                                                                                    class="item-meta duration">{{ minutesToHours($chapterItem?->lesson?->duration) }}</span>
                                                                                <span class="item-meta course-item-status">
                                                                                    <img src="{{ asset('frontend/img/icons/lock.svg') }}"
                                                                                        alt="icon">
                                                                                </span>
                                                                            </div>
                                                                        </a>
                                                                    </li>
                                                                @elseif($chapterItem?->type == 'document')
                                                                    <li class="course-item">
                                                                        <a href="javascript:;" class="course-item-link">
                                                                            <span
                                                                                class="item-name">{{ $chapterItem?->lesson?->title }}</span>
                                                                            <div class="course-item-meta">
                                                                                <span
                                                                                    class="item-meta duration">{{ minutesToHours($chapterItem?->lesson?->duration) }}</span>
                                                                                <span class="item-meta course-item-status">
                                                                                    <img src="{{ asset('frontend/img/icons/lock.svg') }}"
                                                                                        alt="icon">
                                                                                </span>
                                                                            </div>
                                                                        </a>
                                                                    </li>
                                                                @elseif ($chapterItem->type == 'quiz')
                                                                    <li class="course-item">
                                                                        <a href="javascript:;" class="course-item-link">
                                                                            <span
                                                                                class="item-name">{{ $chapterItem?->quiz?->title }}</span>
                                                                            <div class="course-item-meta">
                                                                                <span
                                                                                    class="item-meta duration">{{ minutesToHours($chapterItem?->lesson?->duration) }}</span>
                                                                                <span class="item-meta course-item-status">
                                                                                    <img src="{{ asset('frontend/img/icons/lock.svg') }}"
                                                                                        alt="icon">
                                                                                </span>
                                                                            </div>
                                                                        </a>
                                                                    </li>
                                                                @endif
                                                            @endforeach
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="instructors-tab-pane" role="tabpanel"
                                aria-labelledby="instructors-tab" tabindex="0">

                                <div class="courses__instructors-wrap">
                                    <div class="courses__instructors-thumb">
                                        <img src="{{ asset($course->instructor->image) }}" alt="img"
                                            class="instructor-thumb">
                                    </div>
                                    <div class="courses__instructors-content">
                                        <h2 class="title">{{ $course->instructor->first_name }}</h2>
                                        <span class="designation">{{ $course->instructor->job_title }}</span>
                                        <p>{{ $course->instructor->short_bio }}</p>
                                        <div class="instructor__social">
                                            <ul class="list-wrap justify-content-start">
                                                @if ($course->instructor->facebook)
                                                    <li><a href="{{ $course->instructor->facebook }}"
                                                            aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                                                    </li>
                                                @endif
                                                @if ($course->instructor->twitter)
                                                    <li><a href="{{ $course->instructor->twitter }}"
                                                            aria-label="Twitter"><i class="fab fa-twitter"></i></a></li>
                                                @endif
                                                @if ($course->instructor->linkedin)
                                                    <li><a href="{{ $course->instructor->linkedin }}"
                                                            aria-label="Linkedin"><i class="fab fa-linkedin"></i></a></li>
                                                @endif
                                                @if ($course->instructor->github)
                                                    <li><a href="{{ $course->instructor->github }}"
                                                            aria-label="Github"><i class="fab fa-github"></i></a></li>
                                                @endif

                                                @if ($course->instructor->facebook)
                                                    <li><a href="{{ $course->instructor->facebook }}"
                                                            aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                                                    </li>
                                                @endif
                                                @if ($course->instructor->twitter)
                                                    <li><a href="{{ $course->instructor->twitter }}"
                                                            aria-label="Twitter"><i class="fab fa-twitter"></i></a></li>
                                                @endif
                                                @if ($course->instructor->website)
                                                    <li><a href="{{ $course->instructor->website }}"
                                                            aria-label="Website"><i class="fas fa-link"></i></a></li>
                                                @endif
                                                @if ($course->instructor->github)
                                                    <li><a href="{{ $course->instructor->github }}"
                                                            aria-label="Github"><i class="fab fa-github"></i></a></li>
                                                @endif
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                @if ($course->partnerInstructors->count() > 0)
                                    <h3 class="title mt-3">{{ __('Partner Instructors') }}</h3>
                                    @foreach ($course->partnerInstructors as $instructor)
                                        <div class="courses__instructors-wrap">
                                            <div class="courses__instructors-thumb">
                                                <img src="{{ asset($instructor->instructor->image) }}" alt="img">
                                            </div>
                                            <div class="courses__instructors-content">
                                                <h2 class="title">{{ $instructor->instructor->first_name }}</h2>
                                                <span class="designation">{{ $instructor->instructor->job_title }}</span>
                                                <p>{{ $instructor->instructor->short_bio }}</p>
                                                <div class="instructor__social">
                                                    <ul class="list-wrap justify-content-start">
                                                        @if ($instructor->instructor->facebook)
                                                            <li><a href="{{ $instructor->instructor->facebook }}"
                                                                    aria-label="Facebook"><i
                                                                        class="fab fa-facebook-f"></i></a></li>
                                                        @endif
                                                        @if ($instructor->instructor->twitter)
                                                            <li><a href="{{ $instructor->instructor->twitter }}"
                                                                    aria-label="Twitter"><i
                                                                        class="fab fa-twitter"></i></a></li>
                                                        @endif
                                                        @if ($instructor->instructor->website)
                                                            <li><a href="{{ $instructor->instructor->website }}"
                                                                    aria-label="Website"><i class="fas fa-link"></i></a>
                                                            </li>
                                                        @endif
                                                        @if ($instructor->instructor->github)
                                                            <li><a href="{{ $instructor->instructor->github }}"
                                                                    aria-label="Github"><i class="fab fa-github"></i></a>
                                                            </li>
                                                        @endif
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                            <div class="tab-pane fade" id="reviews-tab-pane" role="tabpanel"
                                aria-labelledby="reviews-tab" tabindex="0">
                                <div class="courses__rating-wrap">
                                    <h2 class="title">{{ __('Reviews') }}</h2>
                                    <div class="course-rate">
                                        <div class="course-rate__summary">
                                            <div class="course-rate__summary-value">
                                                {{ number_format($course->reviews()->whereHas('course')->whereHas('user')->avg('rating'), 1) ?? 0 }}
                                            </div>
                                            <div class="course-rate__summary-stars">
                                                <i class="fas fa-star"></i>
                                                <i class="fas fa-star"></i>
                                                <i class="fas fa-star"></i>
                                                <i class="fas fa-star"></i>
                                                <i class="fas fa-star"></i>
                                            </div>
                                            <div class="course-rate__summary-text">
                                                {{ $course->reviews()->whereHas('course')->whereHas('user')->where('status', 1)->count() }}
                                                {{ __('Ratings') }}
                                            </div>
                                        </div>
                                        @php
                                            $totalRating = $course->reviews_count;
                                            $fiveStar = $course
                                                ->reviews()
                                                ->where('rating', 5)
                                                ->where('status', 1)
                                                ->whereHas('course')
                                                ->whereHas('user')
                                                ->count();
                                            $fourStar = $course
                                                ->reviews()
                                                ->where('rating', 4)
                                                ->where('status', 1)
                                                ->whereHas('course')
                                                ->whereHas('user')
                                                ->count();
                                            $threeStar = $course
                                                ->reviews()
                                                ->where('rating', 3)
                                                ->where('status', 1)
                                                ->whereHas('course')
                                                ->whereHas('user')
                                                ->count();
                                            $twoStar = $course
                                                ->reviews()
                                                ->where('rating', 2)
                                                ->where('status', 1)
                                                ->whereHas('course')
                                                ->whereHas('user')
                                                ->count();
                                            $oneStar = $course
                                                ->reviews()
                                                ->where('rating', 1)
                                                ->where('status', 1)
                                                ->whereHas('course')
                                                ->whereHas('user')
                                                ->count();
                                            $totalPercentage = $totalRating > 0 ? ($fiveStar / $totalRating) * 100 : 0;
                                            $fourPercentage = $totalRating > 0 ? ($fourStar / $totalRating) * 100 : 0;
                                            $threePercentage = $totalRating > 0 ? ($threeStar / $totalRating) * 100 : 0;
                                            $twoPercentage = $totalRating > 0 ? ($twoStar / $totalRating) * 100 : 0;
                                            $onePercentage = $totalRating > 0 ? ($oneStar / $totalRating) * 100 : 0;
                                        @endphp
                                        <div class="course-rate__details">
                                            <div class="course-rate__details-row">
                                                <div class="course-rate__details-row-star">
                                                    5
                                                    <i class="fas fa-star"></i>
                                                </div>
                                                <div class="course-rate__details-row-value">
                                                    <div class="rating-gray"></div>
                                                    <div class="rating" style="width: {{ $totalPercentage }}%;"
                                                        title="{{ $totalPercentage }}%"></div>
                                                    <span class="rating-count">{{ $fiveStar }}</span>
                                                </div>
                                            </div>
                                            <div class="course-rate__details-row">
                                                <div class="course-rate__details-row-star">
                                                    4
                                                    <i class="fas fa-star"></i>
                                                </div>
                                                <div class="course-rate__details-row-value">
                                                    <div class="rating-gray"></div>
                                                    <div class="rating" style="width: {{ $fourPercentage }}%;"
                                                        title="{{ $fourPercentage }}%"></div>
                                                    <span class="rating-count">{{ $fourStar }}</span>
                                                </div>
                                            </div>
                                            <div class="course-rate__details-row">
                                                <div class="course-rate__details-row-star">
                                                    3
                                                    <i class="fas fa-star"></i>
                                                </div>
                                                <div class="course-rate__details-row-value">
                                                    <div class="rating-gray"></div>
                                                    <div class="rating" style="width: {{ $threePercentage }}%;"
                                                        title="{{ $threePercentage }}%"></div>
                                                    <span class="rating-count">{{ $threeStar }}</span>
                                                </div>
                                            </div>
                                            <div class="course-rate__details-row">
                                                <div class="course-rate__details-row-star">
                                                    2
                                                    <i class="fas fa-star"></i>
                                                </div>
                                                <div class="course-rate__details-row-value">
                                                    <div class="rating-gray"></div>
                                                    <div class="rating" style="width: {{ $twoPercentage }}%;"
                                                        title="{{ $twoPercentage }}%"></div>
                                                    <span class="rating-count">{{ $twoStar }}</span>
                                                </div>
                                            </div>
                                            <div class="course-rate__details-row">
                                                <div class="course-rate__details-row-star">
                                                    1
                                                    <i class="fas fa-star"></i>
                                                </div>
                                                <div class="course-rate__details-row-value">
                                                    <div class="rating-gray"></div>
                                                    <div class="rating" style="width: {{ $onePercentage }}%;"
                                                        title="{{ $onePercentage }}%"></div>
                                                    <span class="rating-count">{{ $oneStar }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @foreach ($reviews as $review)
                                        <div class="course-review-head">
                                            <div class="review-author-thumb">
                                                <img src="{{ asset($review?->user?->image) }}" alt="img">
                                            </div>
                                            <div class="review-author-content">
                                                <div class="author-name">
                                                    <h5 class="name">{{ $review?->user?->name }}
                                                        <span>{{ formatDate($review->created_at) }}</span>
                                                    </h5>
                                                    <div class="author-rating">
                                                        @for ($i = 1; $i <= $review->rating; $i++)
                                                            <i class="fas fa-star"></i>
                                                        @endfor
                                                    </div>
                                                </div>
                                                <p>{{ $review->review }}</p>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-4">
                    <div class="courses__details-sidebar">
                        <div class="courses__cost-wrap">
                            <span>{{ __('This Course Fee') }}:</span>
                            @if ($course->price == 0)
                                <h2 class="title">{{ __('Free') }}</h2>
                            @elseif ($course->discount)
                                <h2 class="title">{{ currency($course->discount) }}
                                    <del>{{ currency($course->price) }}</del>
                                </h2>
                            @else
                                <h2 class="title">{{ currency($course->price) }}</h2>
                            @endif

                        </div>
                        <div class="courses__information-wrap">
                            <h5 class="title">{{ __('Course includes') }}:</h5>
                            <ul class="list-wrap">
                                <li class="level-wrapper">
                                    <b>
                                        <img src="{{ asset('frontend/img/icons/course_icon01.svg') }}" alt="img"
                                            class="injectable">
                                        {{ __('Level') }}
                                    </b>
                                    <ul class="course-level-list">
                                        @foreach ($course->levels as $level)
                                            <span class="level">{{ @$level->level->translation->name }}</span>
                                        @endforeach
                                    </ul>
                                </li>
                                <li>
                                    <img src="{{ asset('frontend/img/icons/course_icon02.svg') }}" alt="img"
                                        class="injectable">
                                    {{ __('Duration') }}
                                    <span>{{ minutesToHours($course->duration) }}</span>
                                </li>
                                <li>
                                    <img src="{{ asset('frontend/img/icons/course_icon03.svg') }}" alt="img"
                                        class="injectable">
                                    {{ __('Lessons') }}
                                    <span>{{ $courseLessonCount }}</span>
                                </li>
                                <li>
                                    <img src="{{ asset('frontend/img/icons/course_icon04.svg') }}" alt="img"
                                        class="injectable">
                                    {{ __('Quizzes') }}
                                    <span>{{ $courseQuizCount }}</span>
                                </li>
                                <li>
                                    <img src="{{ asset('frontend/img/icons/course_icon05.svg') }}" alt="img"
                                        class="injectable">
                                    {{ __('Certifications') }}
                                    @if ($course->certificate)
                                        <span>{{ __('Yes') }}</span>
                                    @else
                                        <span>{{ __('No') }}</span>
                                    @endif
                                </li>
                                <li class="level-wrapper">
                                    <b>
                                        <img src="{{ asset('frontend/img/icons/course_icon06.svg') }}" alt="img"
                                            class="injectable">
                                        {{ __('Language') }}
                                    </b>

                                    <ul class="course-language-list">
                                        @foreach ($course->languages as $language)
                                            <span>{{ $language->language->name }}</span>
                                        @endforeach
                                    </ul>
                                </li>
                            </ul>
                        </div>
                        <div class="courses__details-social">
                            <h5 class="title">{{ __('Share this course') }}:</h5>
                            <div class="shareon">
                                <a class="facebook"></a>
                                <a class="linkedin"></a>
                                <a class="pinterest"></a>
                                <a class="telegram"></a>
                                <a class="twitter"></a>
                            </div>
                        </div>
                        <div class="courses__details-links">
                            <h5 class="title">{{ __('Explore More') }}</h5>
                            <ul class="courses__details-links-list">
                                <li>
                                    <a href="{{ route('courses', ['category' => $course->category->id]) }}">
                                        <span>{{ __('More courses in this category') }}</span>
                                        <i class="flaticon-arrow-right"></i>
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('all-instructors') }}">
                                        <span>{{ __('Compare instructors and teaching styles') }}</span>
                                        <i class="flaticon-arrow-right"></i>
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('placement-test.show') }}">
                                        <span>{{ __('Take the placement test before you enroll') }}</span>
                                        <i class="flaticon-arrow-right"></i>
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('blogs') }}">
                                        <span>{{ __('Read blog guides about language learning') }}</span>
                                        <i class="flaticon-arrow-right"></i>
                                    </a>
                                </li>
                            </ul>
                        </div>
                        <div class="courses__details-enroll">
                            <div class="tg-button-wrap">
                                @if (in_array($course->id, session('enrollments') ?? []))
                                    <a href="{{ route('student.enrolled-courses') }}"
                                        class="btn btn-two arrow-btn already-enrolled-btn" data-id="">
                                        <span class="text">{{ __('Enrolled') }}</span>
                                        <i class="flaticon-arrow-right"></i>
                                    </a>
                                @elseif ($course->enrollments->count() >= $course->capacity && $course->capacity != null)
                                    <a href="javascript:;" class="btn btn-two arrow-btn" data-id="{{ $course->id }}">
                                        <span class="text">{{ __('Booked') }}</span>
                                        <i class="flaticon-arrow-right"></i>
                                    </a>
                                @else
                                    <a href="javascript:;" class="btn btn-two arrow-btn add-to-cart"
                                        data-id="{{ $course->id }}">
                                        <span class="text">{{ __('Add To Cart') }}</span>
                                        <i class="flaticon-arrow-right"></i>
                                    </a>
                                @endif
                            </div>
                            @if (Module::has('GiftCourse') && Module::isEnabled('GiftCourse'))
                                <div class="d-block text-center mt-3">
                                    <a href="{{ route('gift-course', $course->slug) }}" class="btn btn-four arrow-btn">
                                        <i class="fas fa-gift"></i> {{ __('Gift This Course') }}
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Google Drive player modal Structure -->
    <div class="google_drive_modal">
        <div class="modal fade" id="videoModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"><i
                                class="fas fa-times"></i></button>
                    </div>
                    <div class="modal-body">
                        <div class="ratio ratio-16x9">
                            <iframe class="iframe-video" src="" width="640" height="680" allow="autoplay"
                                frameborder="0" allowfullscreen></iframe>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- courses-details-area-end -->
@endsection

@push('structured_data')
    @php
        $courseSchema = [
            '@context' => 'https://schema.org',
            '@type' => 'Course',
            'name' => $course?->title,
            'description' => $courseDescription,
            'url' => route('course.show', $course->slug),
            'image' => asset($course->thumbnail),
            'provider' => [
                '@type' => 'EducationalOrganization',
                'name' => $setting->app_name,
                'url' => route('home'),
            ],
            'instructor' => [
                '@type' => 'Person',
                'name' => $course->instructor->first_name ?? $course->instructor->name,
                'url' => route('instructor-details', $course->instructor->id),
            ],
            'courseMode' => 'online',
            'offers' => [
                '@type' => 'Offer',
                'url' => route('course.show', $course->slug),
                'priceCurrency' => session('currency_code') ?? 'TRY',
                'price' => number_format((float) $coursePrice, 2, '.', ''),
                'availability' => 'https://schema.org/InStock',
            ],
        ];

        if ($courseRatingCount > 0) {
            $courseSchema['aggregateRating'] = [
                '@type' => 'AggregateRating',
                'ratingValue' => $courseRatingValue,
                'reviewCount' => $courseRatingCount,
            ];
        }

        $courseBreadcrumbSchema = [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => [
                [
                    '@type' => 'ListItem',
                    'position' => 1,
                    'name' => 'Home',
                    'item' => route('home'),
                ],
                [
                    '@type' => 'ListItem',
                    'position' => 2,
                    'name' => 'Courses',
                    'item' => route('courses'),
                ],
                [
                    '@type' => 'ListItem',
                    'position' => 3,
                    'name' => $course?->title,
                    'item' => route('course.show', $course->slug),
                ],
            ],
        ];
    @endphp
    <script type="application/ld+json">{!! json_encode($courseSchema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}</script>
    <script type="application/ld+json">{!! json_encode($courseBreadcrumbSchema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}</script>
@endpush

@push('scripts')
    <script src="{{ asset('frontend/js/default/course-details.js') }}"></script>
    <script src="{{ asset('frontend/js/shareon.iife.js') }}"></script>
    <script>
        Shareon.init();
    </script>

    @if ($setting->google_tagmanager_status == 'active' && $marketing_setting?->course_details)
        <script>
            $(document).ready(function() {
                dataLayer.push({
                    'event': 'courseDetails',
                    'courses': {
                        'name': '{{ $course?->title }}',
                        'price': '{{ currency($course->price) }}',
                        'instructor': '{{ $course->instructor->first_name ?? $course->instructor->name }}',
                        'category': '{{ $course->category->translation->name }}',
                        'lessons': '{{ $courseLessonCount }}',
                        'duration': '{{ minutesToHours($course->duration) }}',
                        'url': "{{ route('course.show', $course->slug) }}",
                    }
                });
            });
        </script>
    @endif
@endpush
