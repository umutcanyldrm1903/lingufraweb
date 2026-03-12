@extends('frontend.layouts.master')
@section('meta_title', $seo_setting['course_page']['seo_title'])
@section('meta_description', $seo_setting['course_page']['seo_description'])

@section('contents')
    <!-- cowboy hero -->
    <section class="cowboy-course-hero">
        <div class="container">
            <div class="cowboy-course-hero__content">
                <p class="eyebrow">{{ __('Courses') }}</p>
                <h1>{{ __('Choose Your Plan') }}</h1>
                <p class="lead">{{ __('Explore speaking, business English, exam prep, and kids courses on one page. Choose the training that fits you best.') }}</p>
            </div>
        </div>
    </section>

    <!-- plan comparison -->
    @php
        $compareCheck = '__CHECK__';
        $compareFeatures = [
            __('Program Duration'),
            __('Total Minutes'),
            __('1-on-1 Private Lessons'),
            __('Lesson Duration'),
            __('Weekly Lessons'),
            __('Cancellation Right'),
            __('Freeze Right'),
            __('Free Trial Lesson'),
            __('Installment Options'),
            __('Single Lesson'),
            __('Flexible Lesson Scheduling'),
            __('Personalized Lesson Content'),
            __('Approved Foreign / Turkish Instructors'),
            __('Access to All Instructors'),
            __('Choose / Change Instructor'),
            __('Direct Instructor Communication'),
            __('Certificate'),
            __('Corporate Invoice'),
            __('Homework System'),
            __('All Materials Included'),
            __('Dedicated Student Support Team'),
        ];
        $comparePlans = [
            [
                'title' => __('3 Months'),
                'lessons' => __('24 Lessons'),
                'price' => '8.999 TL',
                'notes' => [
                    __('3 Months'),
                    __('960 min'),
                    $compareCheck,
                    __('40 min'),
                    __('1-2 Lessons'),
                    __('3 Lessons'),
                    __('1 Month'),
                    $compareCheck,
                    __('3-9 Months'),
                    '375 TL',
                    $compareCheck,
                    $compareCheck,
                    $compareCheck,
                    $compareCheck,
                    $compareCheck,
                    $compareCheck,
                    $compareCheck,
                    $compareCheck,
                    $compareCheck,
                    $compareCheck,
                    $compareCheck,
                ],
            ],
            [
                'title' => __('6 Months'),
                'lessons' => __('48 Lessons'),
                'price' => '11.999 TL',
                'badge' => __('Most Popular'),
                'notes' => [
                    __('6 Months'),
                    __('1,920 min'),
                    $compareCheck,
                    __('40 min'),
                    __('1-3 Lessons'),
                    __('5 Lessons'),
                    __('1 Month'),
                    $compareCheck,
                    __('3-9 Months'),
                    '250 TL',
                    $compareCheck,
                    $compareCheck,
                    $compareCheck,
                    $compareCheck,
                    $compareCheck,
                    $compareCheck,
                    $compareCheck,
                    $compareCheck,
                    $compareCheck,
                    $compareCheck,
                    $compareCheck,
                ],
            ],
            [
                'title' => __('12 Months'),
                'lessons' => __('96 Lessons'),
                'price' => '18.999 TL',
                'badge' => __('Best Value'),
                'notes' => [
                    __('12 Months'),
                    __('3,840 min'),
                    $compareCheck,
                    __('40 min'),
                    __('2 Lessons'),
                    __('10 Lessons'),
                    __('1 Month'),
                    $compareCheck,
                    __('3-9 Months'),
                    '198 TL',
                    $compareCheck,
                    $compareCheck,
                    $compareCheck,
                    $compareCheck,
                    $compareCheck,
                    $compareCheck,
                    $compareCheck,
                    $compareCheck,
                    $compareCheck,
                    $compareCheck,
                    $compareCheck,
                ],
            ],
            [
                'title' => __('12 Months'),
                'lessons' => __('144 Lessons'),
                'price' => '26.999 TL',
                'badge' => __('Best Value'),
                'notes' => [
                    __('12 Months'),
                    __('5,760 min'),
                    $compareCheck,
                    __('40 min'),
                    __('3 Lessons'),
                    __('15 Lessons'),
                    __('1 Month'),
                    $compareCheck,
                    __('3-9 Months'),
                    '188 TL',
                    $compareCheck,
                    $compareCheck,
                    $compareCheck,
                    $compareCheck,
                    $compareCheck,
                    $compareCheck,
                    $compareCheck,
                    $compareCheck,
                    $compareCheck,
                    $compareCheck,
                    $compareCheck,
                ],
            ],
        ];
    @endphp
    <section class="plan-compare">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-4">
                    <h2 class="pc-title">{{ __('Choose Your Plan') }}</h2>
                    <p class="pc-sub">{{ __('Compare course duration, lesson count, and features to pick the package that fits you best.') }}</p>
                    <ul class="pc-features">
                        @foreach ($compareFeatures as $feature)
                            <li>{{ $feature }}</li>
                        @endforeach
                    </ul>
                </div>
                <div class="col-lg-8">
                    <div class="pc-table">
                        @foreach ($comparePlans as $plan)
                            <div class="pc-col">
                                @if (!empty($plan['badge']))
                                    <span class="pc-badge">{{ $plan['badge'] }}</span>
                                @endif
                                <h4 class="pc-name">{{ $plan['title'] }}</h4>
                                <p class="pc-lessons">{{ $plan['lessons'] }}</p>
                                <p class="pc-price">{{ $plan['price'] }}</p>
                                <ul class="pc-notes">
                                    @foreach ($plan['notes'] as $note)
                                        <li>{!! $note === $compareCheck ? '<span class="pc-dot"></span>' : e(__($note)) !!}</li>
                                    @endforeach
                                </ul>
                                <a href="{{ Route::has('login') ? route('login') : route('home') }}" class="btn pc-btn">{{ __('Start') }}</a>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- all-courses -->
    <section class="all-courses-area section-py-120 top-baseline cowboy-course-page">
        <div class="container position-relative">
            <div class="preloader-two d-none">
                <div class="loader-icon-two"><img src="{{ asset(Cache::get('setting')->preloader) }}" alt="Preloader"></div>
            </div>
            <div class="row">
                <div class="col-xl-3 col-lg-4">
                    <div class="courses__sidebar_area">
                        <div class="courses__sidebar_button d-lg-none">
                            <h4>{{ __('filter') }}</h4>
                        </div>
                        <aside class="courses__sidebar">
                            <div class="courses-widget">
                                <h4 class="widget-title">{{ __('Categories') }}</h4>
                                <div class="courses-cat-list">
                                    <ul class="list-wrap">
                                        @foreach ($categories->sortBy('translation.name') as $category)
                                            <li>
                                                <div class="form-check">
                                                    <input class="form-check-input main-category-checkbox" type="radio"
                                                        name="main_category" value="{{ $category->slug }}"
                                                        id="cat_{{ $category->id }}">
                                                    <label class="form-check-label"
                                                        for="cat_{{ $category->id }}">{{ $category->translation->name }}</label>
                                                </div>
                                            </li>
                                        @endforeach
                                    </ul>
                                    <div class="show-more">
                                    </div>
                                </div>
                            </div>

                            <div class="sub-category-holder "></div>
                            <div class="courses-widget">
                                <h4 class="widget-title">{{ __('Language') }}</h4>
                                <div class="courses-cat-list">
                                    <ul class="list-wrap">

                                        <li>
                                            <div class="form-check">
                                                <input class="form-check-input language-checkbox" type="checkbox"
                                                    value="" id="lang">
                                                <label class="form-check-label"
                                                    for="lang">{{ __('All Language') }}</label>
                                            </div>
                                        </li>
                                        @foreach ($languages as $language)
                                            <li>
                                                <div class="form-check">
                                                    <input class="form-check-input language-checkbox" type="checkbox"
                                                        value="{{ $language->id }}" id="lang_{{ $language->id }}">
                                                    <label class="form-check-label"
                                                        for="lang_{{ $language->id }}">{{ $language->name }}</label>
                                                </div>
                                            </li>
                                        @endforeach

                                    </ul>
                                </div>
                                <div class="show-more">
                                </div>
                            </div>
                            <div class="courses-widget">
                                <h4 class="widget-title">{{ __('Price') }}</h4>
                                <div class="courses-cat-list">
                                    <ul class="list-wrap">
                                        <li>
                                            <div class="form-check">
                                                <input class="form-check-input price-checkbox" type="checkbox"
                                                    value="" id="price_1">
                                                <label class="form-check-label"
                                                    for="price_1">{{ __('All Price') }}</label>
                                            </div>
                                        </li>
                                        <li>
                                            <div class="form-check">
                                                <input class="form-check-input price-checkbox" type="checkbox"
                                                    value="free" id="price_2">
                                                <label class="form-check-label" for="price_2">{{ __('Free') }}</label>
                                            </div>
                                        </li>
                                        <li>
                                            <div class="form-check">
                                                <input class="form-check-input price-checkbox" type="checkbox"
                                                    value="paid" id="price_3">
                                                <label class="form-check-label" for="price_3">{{ __('Paid') }}</label>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <div class="courses-widget">
                                <h4 class="widget-title">{{ __('Skill level') }}</h4>
                                <div class="courses-cat-list">
                                    <ul class="list-wrap">
                                        <li>
                                            <div class="form-check">
                                                <input class="form-check-input level-checkbox" type="checkbox"
                                                    value="" id="difficulty_1">
                                                <label class="form-check-label"
                                                    for="difficulty_1">{{ __('All Levels') }}</label>
                                            </div>
                                        </li>
                                        @foreach ($levels as $level)
                                            <li>
                                                <div class="form-check">
                                                    <input class="form-check-input level-checkbox" type="checkbox"
                                                        value="{{ $level->id }}" id="difficulty_{{ $level->id }}">
                                                    <label class="form-check-label"
                                                        for="difficulty_{{ $level->id }}">{{ $level->translation->name }}</label>
                                                </div>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </aside>
                    </div>
                </div>
                <div class="col-xl-9 col-lg-8">
                    <div class="courses-top-wrap courses-top-wrap">
                        <div class="row align-items-center">
                            <div class="col-md-5">
                                <div class="courses-top-left">
                                    <p>{{ __('Total') }} <span class="course-count">0</span> {{ __('courses found') }}
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-7">
                                <div class="d-flex justify-content-center align-items-center flex-wrap">
                                    <div class="courses-top-right m-0 ms-md-auto">
                                        <span class="sort-by">{{ __('Sort By') }}:</span>
                                        <div class="courses-top-right-select">
                                            <select name="orderby" class="orderby">
                                                <option value="desc">{{ __('Latest to Oldest') }}</option>
                                                <option value="asc">{{ __('Oldest to Latest') }}</option>
                                            </select>
                                        </div>
                                    </div>
                                    
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-content" id="myTabContent">
                        <div class="tab-pane fade show active" id="grid" role="tabpanel"
                            aria-labelledby="grid-tab">
                            <div
                                class="course-holder row courses__grid-wrap row-cols-1 row-cols-xl-3 row-cols-lg-2 row-cols-md-2 row-cols-sm-1">
                                {{-- dynamic content will go here via ajax --}}
                            </div>

                            <div class="pagination-wrap">
                                <div class="pagination">
                                    {{-- dynamic content will go here via ajax --}}
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- all-courses-end -->
@endsection

@push('styles')
<style>
    /* Cowboy courses page styling */
    :root{
        --brand-primary:#0e5c93;
        --brand-dark:#0b3f6c;
        --brand-accent:#f36f25;
    }
    .cowboy-course-hero{background:linear-gradient(135deg,var(--brand-primary) 0%,var(--brand-dark) 100%);padding:70px 0 60px;color:#fff;position:relative;overflow:hidden;}
    .cowboy-course-hero::after{content:"";position:absolute;right:-140px;top:-120px;width:320px;height:320px;border-radius:50%;background:rgba(255,255,255,0.12);}
    .cowboy-course-hero__content{max-width:620px;}
    .cowboy-course-hero__content .eyebrow{text-transform:uppercase;font-weight:800;letter-spacing:0.5px;margin-bottom:6px;}
    .cowboy-course-hero__content h1{font-weight:900;font-size:42px;margin:0 0 10px;}
    .cowboy-course-hero__content .lead{margin:0;font-size:18px;font-weight:600;max-width:560px;}

    .cowboy-course-page{background:#f7f7fb;padding-top:70px;}
    .cowboy-course-page .courses__sidebar_area{background:#fff;border:1px solid #e5e5ef;border-radius:14px;box-shadow:0 18px 40px rgba(0,0,0,0.08);padding:16px;}
    .cowboy-course-page .courses-widget{border-bottom:1px solid #eee;padding:12px 0;}
    .cowboy-course-page .courses-widget:last-child{border-bottom:none;}
    .cowboy-course-page .widget-title{font-weight:800;color:#1c1c1c;}
    .cowboy-course-page .courses-cat-list .form-check-input{border:1px solid #c7c7d9;width:18px;height:18px;}
    .cowboy-course-page .courses-cat-list .form-check-input:checked{background-color:#f6a105;border-color:#f6a105;}
    .cowboy-course-page .courses-cat-list .form-check-label{color:#1c1c1c;font-weight:700;}

    .cowboy-course-page .courses-top-wrap{background:#fff;border:1px solid #e5e5ef;border-radius:14px;box-shadow:0 12px 32px rgba(0,0,0,0.06);padding:12px 16px;margin-bottom:16px;}
    .cowboy-course-page .courses-top-left p{margin:0;font-weight:700;color:#1c1c1c;}
    .cowboy-course-page .courses-top-right select{border:1px solid #d1d1df;border-radius:10px;padding:8px 12px;font-weight:700;}

    .cowboy-course-page .course-holder .courses__item,
    .cowboy-course-page .course-holder .courses__item-two,
    .cowboy-course-page .course-holder .courses__item-three,
    .cowboy-course-page .course-holder .single-course{
        background:#fff;border:1px solid #e5e5ef;border-radius:16px;box-shadow:0 16px 36px rgba(0,0,0,0.08);overflow:hidden;transition:transform 0.2s ease, box-shadow 0.2s ease;
    }
    .cowboy-course-page .courses__item,
    .cowboy-course-page .courses__item-two,
    .cowboy-course-page .courses__item-three{padding:14px;display:flex;flex-direction:column;gap:12px;}
    .cowboy-course-page .courses__item-thumb,
    .cowboy-course-page .courses__item-thumb-two,
    .cowboy-course-page .courses__item-thumb-three{border-radius:14px;overflow:hidden;}
    .cowboy-course-page .courses__item-thumb img,
    .cowboy-course-page .courses__item-thumb-two img,
    .cowboy-course-page .courses__item-thumb-three img{width:100%;height:auto;display:block;}
    .cowboy-course-page .courses__item-meta{gap:8px;}
    .cowboy-course-page .courses__item-tag a{background:var(--brand-accent);color:#1c1c1c;border-radius:999px;padding:6px 12px;font-weight:800;display:inline-block;}
    .cowboy-course-page .courses__item-meta .avg-rating{background:#fff;border:1px solid #e5e5ef;border-radius:999px;padding:4px 10px;font-weight:800;color:var(--brand-accent);}
    .cowboy-course-page .courses__item-content .title a{color:#1c1c1c;font-weight:900;}
    .cowboy-course-page .courses__item-content .author{color:#3a3a3a;font-weight:700;margin-bottom:6px;}
    .cowboy-course-page .courses__item-bottom{border-top:1px solid #ececf5;padding-top:10px;display:flex;align-items:center;justify-content:space-between;gap:10px;}
    .cowboy-course-page .courses__item-bottom .button a{background:var(--brand-accent);color:#1c1c1c;border-radius:12px;padding:10px 14px;font-weight:800;display:inline-flex;align-items:center;gap:8px;}
    .cowboy-course-page .courses__item-bottom .button a:hover{background:var(--brand-primary);color:#fff;}
    .cowboy-course-page .courses__item-bottom .price{margin:0;font-weight:900;color:#1c1c1c;}
    .cowboy-course-page .courses__wishlist-two{background:rgba(255,255,255,0.86);color:#d44936;border-radius:50%;width:40px;height:40px;display:grid;place-items:center;box-shadow:0 8px 18px rgba(0,0,0,0.12);}
    .cowboy-course-page .course-holder .courses__item:hover,
    .cowboy-course-page .course-holder .courses__item-two:hover,
    .cowboy-course-page .course-holder .courses__item-three:hover,
    .cowboy-course-page .course-holder .single-course:hover{
        transform:translateY(-4px);box-shadow:0 22px 44px rgba(0,0,0,0.12);
    }
    .cowboy-course-page .pagination-wrap .pagination .page-link,
    .cowboy-course-page .pagination-wrap .pagination .page-item span{background:#fff;border:1px solid #e5e5ef;color:#1c1c1c;}
    .cowboy-course-page .pagination-wrap .pagination .page-link:hover,
    .cowboy-course-page .pagination-wrap .pagination li.active .page-link{background:var(--brand-accent);color:#1c1c1c;}

    /* Plan compare */
    .plan-compare{background:#f5f7fb;padding:60px 0;}
    .pc-title{font-weight:900;font-size:30px;color:#0e1a2c;}
    .pc-sub{color:#4a5b6d;font-weight:700;}
    .pc-features{list-style:none;padding-left:0;margin:14px 0;display:grid;gap:8px;color:#0e1a2c;font-weight:700;}
    .pc-table{display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:12px;}
    .pc-col{background:#fff;border:1px solid #d5e4f3;border-radius:14px;padding:12px;box-shadow:0 12px 28px rgba(0,0,0,0.06);position:relative;display:flex;flex-direction:column;gap:6px;}
    .pc-badge{position:absolute;top:-10px;left:50%;transform:translateX(-50%);background:var(--brand-accent);color:#1c1c1c;font-weight:900;padding:4px 10px;border-radius:10px;font-size:12px;}
    .pc-name{font-weight:900;font-size:18px;color:#0e1a2c;margin-bottom:0;}
    .pc-lessons{margin:0;font-weight:800;color:#4a5b6d;}
    .pc-price{margin:0;font-weight:900;color:#0e1a2c;}
    .pc-notes{list-style:none;padding:0;margin:6px 0;display:grid;gap:6px;font-weight:700;color:#22374a;}
    .pc-notes li{display:flex;align-items:center;gap:6px;}
    .pc-dot{width:10px;height:10px;border-radius:50%;display:inline-block;background:#0e1a2c;}
    .pc-btn{background:var(--brand-accent);color:#1c1c1c;font-weight:900;border-radius:10px;padding:10px 14px;border:1px solid var(--brand-accent);margin-top:auto;text-align:center;}
    .pc-btn:hover{background:var(--brand-primary);color:#fff;border-color:var(--brand-primary);}

    @media(max-width:991px){
        .cowboy-course-hero__content h1{font-size:32px;}
        .cowboy-course-page{padding-top:40px;}
    }
</style>
@endpush

@push('scripts')
    <script src="{{ asset('frontend/js/default/course-page.js') }}"></script>
@endpush
