<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\API\CourseCategoryResource;
use App\Http\Resources\API\CourseDetailsCollection;
use App\Http\Resources\API\CourseLanguageResource;
use App\Http\Resources\API\CourseLevelResource;
use App\Http\Resources\API\CourseListResource;
use App\Http\Resources\API\CourseReviewsResource;
use App\Http\Resources\API\CustomPageResource;
use App\Http\Resources\API\FaqResource;
use App\Http\Resources\API\LanguageResource;
use App\Http\Resources\API\LessonResource;
use App\Http\Resources\API\MultiCurrencyResource;
use App\Http\Resources\API\OnBoardingScreenResource;
use App\Http\Resources\API\SocialLinkResource;
use App\Models\Course;
use App\Models\CourseChapterLesson;
use App\Models\CourseReview;
use App\Services\MailSenderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Modules\ContactMessage\app\Jobs\ContactMessageSendJob;
use Modules\ContactMessage\app\Models\ContactMessage;
use Modules\Blog\app\Models\Blog;
use Modules\Brand\app\Models\Brand;
use Modules\Course\app\Models\CourseCategory;
use Modules\Course\app\Models\CourseLanguage;
use Modules\Course\app\Models\CourseLevel;
use Modules\Currency\app\Models\MultiCurrency;
use Modules\Faq\app\Models\Faq;
use Modules\Frontend\app\Models\ContactSection;
use Modules\Frontend\app\Models\FeaturedCourseSection;
use Modules\Frontend\app\Models\FeaturedInstructor;
use Modules\Frontend\app\Models\Section;
use Modules\GlobalSetting\app\Models\Setting;
use Modules\Language\app\Models\Language;
use Modules\Location\app\Models\Country;
use Modules\NewsLetter\app\Models\NewsLetter;
use Modules\PageBuilder\app\Models\CustomPage;
use Modules\SiteAppearance\app\Models\SectionSetting;
use Modules\SocialLink\app\Models\SocialLink;
use Modules\Testimonial\app\Models\Testimonial;
use App\Models\User;
use App\Support\CorporateBrandCatalog;

class FrontendController extends Controller {
    public function settings(): JsonResponse {
        $setting_list = ['app_name', 'logo', 'timezone', 'primary_color', 'secondary_color'];
        $settings = Setting::whereIn('key', $setting_list)->pluck('value', 'key');
        $whatsappLeadPhone = preg_replace('/\\D+/', '', (string) config('app.whatsapp_lead_phone', ''));

        $data = [
            'app_name'        => (string) $settings['app_name'],
            'logo'            => (string) $settings['logo'],
            'timezone'        => (string) $settings['timezone'],
            'primary_color'   => (string) $settings['primary_color'],
            'secondary_color' => (string) $settings['secondary_color'],
            'whatsapp_lead_phone' => $whatsappLeadPhone,
        ];
        return response()->json([
            'status' => 'success',
            'data'   => $data,
        ], 200);
    }

    public function allLanguages(): JsonResponse {
        $allLanguages = Language::select('code', 'name', 'direction', 'is_default', 'status')->get();
        if ($allLanguages->isNotEmpty()) {
            $data = LanguageResource::collection($allLanguages);
            return response()->json(['status' => 'success', 'data' => $data], 200);
        }
        return response()->json(['status' => 'error', 'message' => 'Not Found!'], 404);
    }
    public function allCurrency(): JsonResponse {
        $allCurrency = MultiCurrency::all();
        if ($allCurrency->isNotEmpty()) {
            $data = MultiCurrencyResource::collection($allCurrency);
            return response()->json(['status' => 'success', 'data' => $data], 200);
        }
        return response()->json(['status' => 'error', 'message' => 'Not Found!'], 404);
    }
    public function getLanguageFile($code = 'en'): JsonResponse {
        $filePath = base_path('lang/' . $code . '.json');
        if (File::exists($filePath)) {
            $data = json_decode(File::get($filePath), true);
            return response()->json(['status' => 'success', 'data' => $data], 200);
        }
        return response()->json(['status' => 'error', 'message' => 'Not Found!', 'code' => $code], 404);
    }
    public function course_languages(Request $request): JsonResponse {
        $limit = $request->filled('limit') && is_numeric($request->limit) ? (int) $request->limit : -1;
        $languages = CourseLanguage::select('id', 'name')->orderBy('name')->where('status', 1)->latest()->take($limit)->get();
        if ($languages->isNotEmpty()) {
            $data = CourseLanguageResource::collection($languages);
            return response()->json(['status' => 'success', 'data' => $data], 200);
        }
        return response()->json(['status' => 'error', 'message' => 'Not Found!'], 404);
    }
    public function course_levels(Request $request): JsonResponse {
        $code = strtolower(request()->query('language', 'en'));
        $limit = $request->filled('limit') && is_numeric($request->limit) ? (int) $request->limit : -1;

        $levels = CourseLevel::select('id', 'slug')->with(['translations' => function ($q) use ($code) {
            $q->where('lang_code', $code)->select('course_level_id', 'name');
        }])->orderBy('slug')->where('status', 1)->latest()->take($limit)->get();

        if ($levels->isNotEmpty()) {
            $data = CourseLevelResource::collection($levels);
            return response()->json(['status' => 'success', 'data' => $data], 200);
        }
        return response()->json(['status' => 'error', 'message' => 'Not Found!'], 404);
    }
    public function main_categories(Request $request): JsonResponse {
        $code = strtolower(request()->query('language', 'en'));
        $categories = CourseCategory::select('id', 'slug', 'icon', 'show_at_trending')->with(['translations' => function ($q) use ($code) {
            $q->where('lang_code', $code)->select('course_category_id', 'name');
        }])->where('parent_id', null)->orderBy('slug')->where('status', 1);

        $limit = $request->filled('limit') && is_numeric($request->limit) ? (int) $request->limit : -1;
        $categories = $categories->latest()->take($limit)->get();

        if ($categories->isNotEmpty()) {
            $data = CourseCategoryResource::collection($categories);
            return response()->json(['status' => 'success', 'data' => $data], 200);
        }
        return response()->json(['status' => 'error', 'message' => 'Not Found!'], 404);
    }
    public function sub_categories(Request $request, string $slug): JsonResponse {
        $code = strtolower(request()->query('language', 'en'));
        $categories = CourseCategory::select('id', 'slug')->with(['translations' => function ($q) use ($code) {
            $q->where('lang_code', $code)->select('course_category_id', 'name');
        }])->whereHas('parentCategory', function ($q) use ($slug) {
            $q->where('slug', $slug);
        })->whereNotNull('parent_id')->orderBy('slug')->where('status', 1);

        $limit = $request->filled('limit') && is_numeric($request->limit) ? (int) $request->limit : -1;
        $categories = $categories->latest()->take($limit)->get();

        if ($categories->isNotEmpty()) {
            $data = CourseCategoryResource::collection($categories);
            return response()->json(['status' => 'success', 'data' => $data], 200);
        }
        return response()->json(['status' => 'error', 'message' => 'Not Found!'], 404);
    }
    public function popular_courses(Request $request): JsonResponse {
        $limit = $request->filled('limit') && is_numeric($request->limit) ? (int) $request->limit : 2;

        $courses = Course::select('slug', 'title', 'instructor_id', 'thumbnail', 'price', 'discount')->active()->with(['instructor:id,name,image'])
            ->whereHas('category.parentCategory', fn($q) => $q->where('status', 1))
            ->whereHas('category', fn($q) => $q->where('status', 1))
            ->withCount(['reviews as average_rating' => function ($q) {
                $q->select(DB::raw('coalesce(avg(rating), 0) as average_rating'))->where('status', 1);
            }, 'enrollments'])->orderByDesc('enrollments_count')->orderByDesc('average_rating')->take($limit)->get();

        if ($courses->isNotEmpty()) {
            $data = CourseListResource::collection($courses);
            return response()->json(['status' => 'success', 'data' => $data], 200);
        }
        return response()->json(['status' => 'error', 'message' => 'Not Found!'], 404);
    }
    public function fresh_courses(Request $request): JsonResponse {
        $limit = $request->filled('limit') && is_numeric($request->limit) ? (int) $request->limit : 2;

        $courses = Course::select('slug', 'title', 'instructor_id', 'thumbnail', 'price', 'discount')->active()->with(['instructor:id,name,image'])
            ->whereHas('category.parentCategory', fn($q) => $q->where('status', 1))
            ->whereHas('category', fn($q) => $q->where('status', 1))
            ->withCount(['reviews as average_rating' => function ($q) {
                $q->select(DB::raw('coalesce(avg(rating), 0) as average_rating'))->where('status', 1);
            }, 'enrollments'])->latest()->take($limit)->get();

        if ($courses->isNotEmpty()) {
            $data = CourseListResource::collection($courses);
            return response()->json(['status' => 'success', 'data' => $data], 200);
        }
        return response()->json(['status' => 'error', 'message' => 'Not Found!'], 404);
    }
    public function search_courses(Request $request): JsonResponse {
        $limit = $request->filled('limit') && is_numeric($request->limit) ? (int) $request->limit : 6;

        $query = Course::select('id', 'slug', 'title', 'instructor_id', 'thumbnail', 'price', 'discount')
            ->active()->with(['instructor:id,name,image'])
            ->whereHas('category.parentCategory', fn($q) => $q->where('status', 1))
            ->whereHas('category', fn($q) => $q->where('status', 1))
            ->withCount(['reviews as average_rating' => fn($q) => $q->select(DB::raw('coalesce(avg(rating), 0)'))->where('status', 1), 'enrollments']);

        $query->when($request->filled('search'), fn($q) => $q->where(function ($q) use ($request) {
            $q->where('title', 'like', "%{$request->search}%")
                ->orWhere('slug', 'like', "%{$request->search}%")
                ->orWhere('type', 'like', "%{$request->search}%")
                ->orWhere('seo_description', 'like', "%{$request->search}%")
                ->orWhere('description', 'like', "%{$request->search}%")
                ->orWhere('price', 'like', "%{$request->search}%")
                ->orWhere('discount', 'like', "%{$request->search}%");
        }));

        $query->when($request->filled('main_category'), function ($q) use ($request) {
            $main_category_slugs = explode(',', $request->main_category);
            $q->whereHas('category.parentCategory', function ($q) use ($main_category_slugs) {
                $q->whereIn('slug', $main_category_slugs);
            });
        });

        $query->when($request->filled('sub_category'), function ($q) use ($request) {
            $sub_category_slugs = explode(',', $request->sub_category);
            $q->whereHas('category', function ($q) use ($sub_category_slugs) {
                $q->whereIn('slug', $sub_category_slugs);
            });
        });

        $query->when($request->filled('languages'), function ($q) use ($request) {
            $languages_names = explode(',', $request->languages);
            $q->whereHas('languages.language', function ($q) use ($languages_names) {
                $q->whereIn('name', $languages_names);
            });
        });

        $query->when($request->filled('levels'), function ($q) use ($request) {
            $levelSlugs = explode(',', $request->levels);
            $q->whereHas('levels.level', function ($q) use ($levelSlugs) {
                $q->whereIn('slug', $levelSlugs);
            });
        });

        $query->when($request->filled('price'), function ($q) use ($request) {
            $q->where(function ($q) use ($request) {
                if ($request->price == 'paid') {
                    $q->where('price', '>', 0);
                } elseif ($request->price == 'free') {
                    $q->where('price', 0)->orWhereNull('price');
                }
            });
        });

        $query->when($request->filled('rating'), function ($q) use ($request) {
            $rating = (int) $request->rating;
            $q->having('average_rating', '>=', $rating);
        });

        $courses = $query->latest()->paginate($limit);

        if ($courses->isNotEmpty()) {
            $data = CourseListResource::collection($courses);
            return response()->json(['status' => 'success',
                'data'                            => $data,
                'pagination'                      => [
                    'current_page' => $courses->currentPage(),
                    'per_page'     => $courses->perPage(),
                    'total'        => $courses->total(),
                    'last_page'    => $courses->lastPage(),
                    'links'        => [
                        'first' => $courses->url(1),
                        'prev'  => $courses->previousPageUrl(),
                        'next'  => $courses->nextPageUrl(),
                        'last'  => $courses->url($courses->lastPage()),
                    ],
                ],
            ], 200);
        }
        return response()->json(['status' => 'error', 'message' => 'Not Found!'], 404);
    }
    public function course_details(string $slug): JsonResponse {
        $user_id = request('user_id', 0);
        $course = Course::active()->where('slug', $slug)->select('id', 'instructor_id', 'demo_video_source', 'demo_video_storage', 'thumbnail', 'title', 'slug', 'price', 'discount', 'description', 'updated_at')->with([
            'instructor:id,name,image',
            'chapters'                           => function ($query) {
                $query->where('status', 'active')->select('id', 'course_id', 'title')->orderBy('order', 'asc')->with([
                    'chapterItems:id,chapter_id,type',
                    'chapterItems.quiz'   => fn($q)   => $q->select('id', 'chapter_item_id', 'title')->where('status', 'active'),
                    'chapterItems.lesson' => fn($q) => $q->select('id', 'chapter_item_id', 'title', 'file_path', 'storage', 'file_type', 'duration', 'is_free')->where('status', 'active'),
                ]);
            },
            'languages:id,course_id,language_id' => ['language:id,name'],
        ])->whereHas('category.parentCategory', fn($q) => $q->where('status', 1))
            ->whereHas('category', fn($q) => $q->where('status', 1))->withCount([
            'reviews as average_rating' => fn($q) => $q->select(DB::raw('coalesce(avg(rating), 0)'))->where('status', 1),
            'reviews'                   => fn($q)                   => $q->where('status', 1),
            'lessons', 'quizzes', 'enrollments',
            'favoriteBy as is_wishlist' => function ($query) use ($user_id) {
                $query->where('user_id', $user_id);
            },
        ])->first();

        if ($course) {
            $data = new CourseDetailsCollection($course);
            return response()->json(['status' => 'success', 'data' => $data], 200);
        }
        return response()->json(['status' => 'error', 'message' => 'Not Found!'], 404);
    }
    public function get_lesson_info(int $lesson_id): JsonResponse {
        // Fetch lesson details
        $lesson = CourseChapterLesson::where('is_free', 1)->select('id', 'course_id', 'chapter_id', 'chapter_item_id', 'title', 'description', 'downloadable', 'file_path', 'storage', 'file_type', 'duration')->find($lesson_id);

        if (!$lesson) {
            return response()->json(['status' => 'error', 'message' => 'Not Found!'], 404);
        }
        $data = new LessonResource($lesson);
        return response()->json(['status' => 'success', 'data' => $data], 200);

    }
    public function course_reviews(Request $request, string $slug): JsonResponse {
        $limit = $request->filled('limit') && is_numeric($request->limit) ? (int) $request->limit : 5;

        $reviews = CourseReview::whereHas('course', fn($q) => $q->where('slug', $slug))->where('status', 1)
            ->whereHas('user')
            ->with('user')->orderBy('created_at', 'desc')->paginate($limit);

        if ($reviews) {
            $data = CourseReviewsResource::collection($reviews);
            return response()->json(['status' => 'success',
                'data'                            => $data,
                'pagination'                      => [
                    'current_page' => $reviews->currentPage(),
                    'per_page'     => $reviews->perPage(),
                    'total'        => $reviews->total(),
                    'last_page'    => $reviews->lastPage(),
                    'links'        => [
                        'first' => $reviews->url(1),
                        'prev'  => $reviews->previousPageUrl(),
                        'next'  => $reviews->nextPageUrl(),
                        'last'  => $reviews->url($reviews->lastPage()),
                    ],
                ],

            ], 200);
        }
        return response()->json(['status' => 'error', 'message' => 'Not Found!'], 404);
    }

    public function privacy_policy(): JsonResponse {
        $code = strtolower(request()->query('language', 'en'));

        $page = CustomPage::select('id', 'slug')->whereSlug('privacy-policy')->with(['translations' => function ($q) use ($code) {
            $q->where('lang_code', $code)->select('custom_page_id', 'name', 'content');
        }])->first();

        if ($page) {
            $data = new CustomPageResource($page);
            return response()->json(['status' => 'success', 'data' => $data], 200);
        }
        return response()->json(['status' => 'error', 'message' => 'Not Found!'], 404);
    }
    public function terms_and_conditions(): JsonResponse {
        $code = strtolower(request()->query('language', 'en'));
        $page = CustomPage::select('id', 'slug')->whereSlug('terms-and-conditions')->with(['translations' => function ($q) use ($code) {
            $q->where('lang_code', $code)->select('custom_page_id', 'name', 'content');
        }])->first();
        if ($page) {
            $data = new CustomPageResource($page);
            return response()->json(['status' => 'success', 'data' => $data], 200);
        }
        return response()->json(['status' => 'error', 'message' => 'Not Found!'], 404);
    }
    public function faqs(Request $request): JsonResponse {
        $limit = $request->filled('limit') && is_numeric($request->limit) ? (int) $request->limit : 4;
        $code = strtolower(request()->query('language', 'en'));

        $faqs = Faq::select('id')->with(['translations' => function ($q) use ($code) {
            $q->where('lang_code', $code)->select('faq_id', 'question', 'answer');
        }])->latest()->paginate($limit);
        if ($faqs->isNotEmpty()) {
            $data = FaqResource::collection($faqs);
            return response()->json(['status' => 'success',
                'data'                            => $data,
                'pagination'                      => [
                    'current_page' => $faqs->currentPage(),
                    'per_page'     => $faqs->perPage(),
                    'total'        => $faqs->total(),
                    'last_page'    => $faqs->lastPage(),
                    'links'        => [
                        'first' => $faqs->url(1),
                        'prev'  => $faqs->previousPageUrl(),
                        'next'  => $faqs->nextPageUrl(),
                        'last'  => $faqs->url($faqs->lastPage()),
                    ],
                ],
            ], 200);
        }
        return response()->json(['status' => 'error', 'message' => 'Not Found!'], 404);
    }
    public function on_boarding_screen(): JsonResponse {
        $screens = [
            [
                'title'       => 'Welcome to Skillgro',
                'description' => 'Discover a world of knowledge and unlock your potential with our curated courses.',
            ],
            [
                'title'       => 'Learn at Your Pace',
                'description' => 'Access courses anytime, anywhere, and track your progress as you grow.',
            ],
            [
                'title'       => 'Showcase Your Skills',
                'description' => 'Complete courses to earn certificates and take your career to new heights.',
            ],
        ];
        $screensCollection = collect($screens);

        if ($screensCollection) {
            $data = OnBoardingScreenResource::collection($screensCollection);
            return response()->json(['status' => 'success', 'data' => $data], 200);
        }
        return response()->json(['status' => 'error', 'message' => 'Not Found!'], 404);
    }
    public function country_list(): JsonResponse {
        $country_list = Country::select('id','name')->where('status',1)->get();
        if ($country_list->isNotEmpty()) {
            return response()->json(['status' => 'success', 'data' => $country_list], 200);
        }
        return response()->json(['status' => 'error', 'message' => 'Not Found!'], 404);
    }

    public function contactSection(): JsonResponse {
        $contact = ContactSection::first();
        if (!$contact) {
            return response()->json(['status' => 'error', 'message' => 'Not Found!'], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'address' => (string) ($contact->address ?? ''),
                'phone_one' => (string) ($contact->phone_one ?? ''),
                'phone_two' => (string) ($contact->phone_two ?? ''),
                'email_one' => (string) ($contact->email_one ?? ''),
                'email_two' => (string) ($contact->email_two ?? ''),
                'map' => (string) ($contact->map ?? ''),
            ],
        ], 200);
    }

    public function aboutPage(Request $request): JsonResponse {
        $code = strtolower($request->query('language', 'en'));
        $theme = defined('DEFAULT_HOMEPAGE') ? DEFAULT_HOMEPAGE : 'language';

        $sections = Section::whereHas('home', function ($q) use ($theme) {
            $q->where('slug', $theme);
        })->with('translations')->get();

        $faqs = Faq::select('id')
            ->with(['translations' => function ($q) use ($code) {
                $q->where('lang_code', $code)->select('faq_id', 'question', 'answer');
            }])
            ->where('status', 1)
            ->latest()
            ->get()
            ->map(function ($faq) use ($code) {
                $translation = $faq->translations->firstWhere('lang_code', $code);
                return [
                    'question' => (string) ($translation?->question ?? ''),
                    'answer' => (string) ($translation?->answer ?? ''),
                ];
            })
            ->values();

        $brands = CorporateBrandCatalog::merge(
            Brand::where('status', 1)->get(['name', 'image', 'url']),
            true
        )->map(function ($brand) {
            return [
                'name' => (string) ($brand->name ?? ''),
                'image' => $brand->image ?: null,
                'url' => (string) ($brand->url ?? ''),
            ];
        })->values();

        $testimonials = Testimonial::active()
            ->with('translations')
            ->get()
            ->map(function ($testimonial) use ($code) {
                $translation = $testimonial->translations->firstWhere('lang_code', $code);
                return [
                    'name' => (string) ($translation?->name ?? ''),
                    'designation' => (string) ($translation?->designation ?? ''),
                    'comment' => (string) ($translation?->comment ?? ''),
                    'rating' => (float) ($testimonial->rating ?? 0),
                    'image' => $testimonial->image ? asset($testimonial->image) : null,
                ];
            })
            ->values();

        $data = [
            'hero' => $this->mapSection($sections->where('name', 'hero_section')->first(), $code),
            'about' => $this->mapSection($sections->where('name', 'about_section')->first(), $code),
            'our_features' => $this->mapSection($sections->where('name', 'our_features_section')->first(), $code),
            'newsletter' => $this->mapSection($sections->where('name', 'newsletter_section')->first(), $code),
            'faq_section' => $this->mapSection($sections->where('name', 'faq_section')->first(), $code),
            'brands' => $brands,
            'testimonials' => $testimonials,
            'faqs' => $faqs,
        ];

        return response()->json(['status' => 'success', 'data' => $data], 200);
    }

    public function homePage(Request $request): JsonResponse {
        $code = strtolower($request->query('language', 'en'));
        $theme = defined('DEFAULT_HOMEPAGE') ? DEFAULT_HOMEPAGE : 'language';

        $sections = Section::whereHas('home', function ($q) use ($theme) {
            $q->where('slug', $theme);
        })->with('translations')->get();

        $sectionData = [
            'hero' => $this->mapSection($sections->where('name', 'hero_section')->first(), $code),
            'slider' => $this->mapSection($sections->where('name', 'slider_section')->first(), $code),
            'about' => $this->mapSection($sections->where('name', 'about_section')->first(), $code),
            'newsletter' => $this->mapSection($sections->where('name', 'newsletter_section')->first(), $code),
            'counter' => $this->mapSection($sections->where('name', 'counter_section')->first(), $code),
            'our_features' => $this->mapSection($sections->where('name', 'our_features_section')->first(), $code),
            'banner' => $this->mapSection($sections->where('name', 'banner_section')->first(), $code),
            'faq_section' => $this->mapSection($sections->where('name', 'faq_section')->first(), $code),
        ];

        $trendingCategories = CourseCategory::with([
            'translation:id,name,course_category_id',
            'subCategories' => function ($query) {
                $query->with(['translation:id,name,course_category_id'])
                    ->withCount(['courses' => function ($query) {
                        $query->where('status', 'active');
                    }]);
            },
        ])
            ->withCount(['subCategories as active_sub_categories_count' => function ($query) {
                $query->whereHas('courses', function ($query) {
                    $query->where('status', 'active');
                });
            }])
            ->whereNull('parent_id')
            ->where('status', 1)
            ->where('show_at_trending', 1)
            ->get()
            ->map(function ($category) {
                $sub = $category->subCategories->map(function ($subCategory) {
                    return [
                        'id' => $subCategory->id,
                        'slug' => (string) ($subCategory->slug ?? ''),
                        'name' => (string) ($subCategory->translation?->name ?? ''),
                        'course_count' => (int) ($subCategory->courses_count ?? 0),
                    ];
                });
                return [
                    'id' => $category->id,
                    'slug' => (string) ($category->slug ?? ''),
                    'name' => (string) ($category->translation?->name ?? ''),
                    'icon' => $category->icon ? asset($category->icon) : null,
                    'active_sub_categories_count' => (int) ($category->active_sub_categories_count ?? 0),
                    'sub_categories' => $sub,
                ];
            })
            ->values();

        $brands = CorporateBrandCatalog::merge(
            Brand::where('status', 1)->get(['name', 'image', 'url']),
            true
        )->map(function ($brand) {
            return [
                'name' => (string) ($brand->name ?? ''),
                'image' => $brand->image ?: null,
                'url' => (string) ($brand->url ?? ''),
            ];
        })->values();

        $featuredCourse = FeaturedCourseSection::first();

        $featuredInstructorSection = FeaturedInstructor::with('translations')->first();
        $instructorIds = json_decode($featuredInstructorSection?->instructor_ids ?? '[]', true);
        $instructorIds = is_array($instructorIds) ? $instructorIds : [];

        $selectedInstructors = User::whereIn('id', $instructorIds)
            ->where(['status' => 'active', 'is_banned' => 0, 'role' => 'instructor'])
            ->with(['courses' => function ($query) {
                $query->withCount(['reviews as avg_rating' => function ($query) {
                    $query->select(DB::raw('coalesce(avg(rating),0)'));
                }]);
            }])
            ->get()
            ->map(function (User $instructor) {
                return [
                    'id' => $instructor->id,
                    'name' => (string) $instructor->first_name,
                    'image' => $instructor->image ? asset($instructor->image) : null,
                    'job_title' => (string) ($instructor->job_title ?? ''),
                    'short_bio' => (string) ($instructor->short_bio ?? ''),
                    'avg_rating' => round((float) ($instructor->courses->avg('avg_rating') ?? 0), 1),
                    'course_count' => (int) ($instructor->courses->count() ?? 0),
                ];
            })
            ->values();

        $testimonials = Testimonial::active()
            ->with('translations')
            ->get()
            ->map(function ($testimonial) use ($code) {
                $translation = $testimonial->translations->firstWhere('lang_code', $code);
                return [
                    'name' => (string) ($translation?->name ?? ''),
                    'designation' => (string) ($translation?->designation ?? ''),
                    'comment' => (string) ($translation?->comment ?? ''),
                    'rating' => (float) ($testimonial->rating ?? 0),
                    'image' => $testimonial->image ? asset($testimonial->image) : null,
                ];
            })
            ->values();

        $featuredBlogs = Blog::with(['translation', 'author'])
            ->whereHas('category', function ($q) {
                $q->where('status', 1);
            })
            ->where(['show_homepage' => 1, 'status' => 1])
            ->orderBy('created_at', 'desc')
            ->limit(4)
            ->get()
            ->map(function (Blog $blog) {
                return [
                    'id' => $blog->id,
                    'title' => (string) ($blog->title ?? ''),
                    'slug' => (string) ($blog->slug ?? ''),
                    'image' => $blog->image ? asset($blog->image) : null,
                    'excerpt' => Str::limit(strip_tags((string) $blog->description), 140),
                    'created_at' => optional($blog->created_at)->toDateTimeString(),
                    'author' => (string) ($blog->author?->name ?? ''),
                ];
            })
            ->values();

        $sectionSetting = SectionSetting::first();

        $data = array_merge($sectionData, [
            'trending_categories' => $trendingCategories,
            'brands' => $brands,
            'featured_course' => $featuredCourse,
            'featured_instructor_section' => $featuredInstructorSection,
            'selected_instructors' => $selectedInstructors,
            'testimonials' => $testimonials,
            'featured_blogs' => $featuredBlogs,
            'section_setting' => $sectionSetting,
        ]);

        return response()->json(['status' => 'success', 'data' => $data], 200);
    }

    //extra
    public function socialLinks(): JsonResponse {
        $socialLinks = SocialLink::select('icon', 'link')->get();
        if ($socialLinks->isNotEmpty()) {
            $data = SocialLinkResource::collection($socialLinks);
            return response()->json(['status' => 'success', 'data' => $data], 200);
        }
        return response()->json(['status' => 'error', 'message' => 'Not Found!'], 404);
    }
    public function contactUs(Request $request): JsonResponse {
        $validator = Validator::make($request->all(), [
            'name'    => 'required',
            'email'   => 'required',
            'subject' => 'required',
            'message' => 'required',
        ], [
            'name.required'    => 'Name is required',
            'email.required'   => 'Email is required',
            'subject.required' => 'Subject is required',
            'message.required' => 'Message is required',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => $validator->errors()], 422);
        }

        $new_message = new ContactMessage();
        $new_message->name = $request->name;
        $new_message->email = $request->email;
        $new_message->subject = $request->subject;
        $new_message->message = $request->message;
        $new_message->phone = $request->phone;
        $new_message->save();

        dispatch(new ContactMessageSendJob($new_message));

        return response()->json(['status' => 'success', 'message' => 'Message sent successfully'], 200);
    }

    private function mapSection(?Section $section, string $code): ?array {
        if (!$section) {
            return null;
        }

        $translation = $section->translations->firstWhere('lang_code', $code);
        $content = $translation?->content ?? $section->translation?->content;

        $global = $section->global_content;
        $globalData = [];
        if (is_object($global) || is_array($global)) {
            $globalData = (array) $global;
        }

        return [
            'name' => (string) $section->name,
            'content' => $content ? (array) $content : [],
            'global' => $globalData,
        ];
    }
    public function newsletter_request(Request $request): JsonResponse {
        $validator = Validator::make($request->all(), [
            'email' => 'required|unique:news_letters',
        ], [
            'email.required' => 'Email is required',
            'email.unique'   => 'Email already exist',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => $validator->errors()], 422);
        }

        $newsletter = new NewsLetter();
        $newsletter->email = $request->email;
        $newsletter->verify_token = Str::random(100);
        $newsletter->save();

        (new MailSenderService)->sendVerifyMailToNewsletterFromTrait($newsletter);

        return response()->json(['status' => 'success', 'message' => 'A verification link has been send to your email, please verify it and getting our newsletter'], 200);
    }

    public function blogPosts(): JsonResponse {
        if (!class_exists(Blog::class)) {
            return response()->json(['status' => 'error', 'message' => 'Not Found!'], 404);
        }

        $posts = Blog::query()
            ->with('translation')
            ->where('status', 1)
            ->orderByDesc('id')
            ->take(20)
            ->get()
            ->map(function (Blog $blog) {
                $description = strip_tags((string) ($blog->description ?? ''));
                return [
                    'id' => $blog->id,
                    'title' => (string) ($blog->title ?? ''),
                    'slug' => (string) ($blog->slug ?? ''),
                    'image' => $blog->image ? asset($blog->image) : null,
                    'excerpt' => Str::limit($description, 140),
                    'created_at' => optional($blog->created_at)->toDateTimeString(),
                ];
            })
            ->values();

        return response()->json(['status' => 'success', 'data' => $posts], 200);
    }

    public function blogDetail(Request $request, string $slug): JsonResponse {
        if (!class_exists(Blog::class)) {
            return response()->json(['status' => 'error', 'message' => 'Not Found!'], 404);
        }

        $code = strtolower($request->query('language', 'en'));

        $blog = Blog::query()
            ->with(['translations', 'author', 'category'])
            ->where('status', 1)
            ->where('slug', $slug)
            ->first();

        if (!$blog) {
            return response()->json(['status' => 'error', 'message' => 'Not Found!'], 404);
        }

        $translation = $blog->translations->firstWhere('lang_code', $code);
        $title = (string) ($translation?->title ?? $blog->title ?? '');
        $description = (string) ($translation?->description ?? $blog->description ?? '');

        $data = [
            'id' => $blog->id,
            'title' => $title,
            'slug' => (string) ($blog->slug ?? ''),
            'image' => $blog->image ? asset($blog->image) : null,
            'excerpt' => Str::limit(strip_tags($description), 160),
            'description' => $description,
            'created_at' => optional($blog->created_at)->toDateTimeString(),
            'author' => (string) (optional($blog->author)->name ?? ''),
            'category' => (string) (optional($blog->category?->translation)->name ?? ''),
        ];

        return response()->json(['status' => 'success', 'data' => $data], 200);
    }

    public function studentPlans(): JsonResponse {
        $currency = (string) (config('student_plans.currency') ?: 'USD');
        $listPrice = (float) (config('student_plans.list_price_per_lesson') ?? 0);
        $defaultDuration = (int) (config('student_plans.default_lesson_duration') ?? 40);

        $plans = collect();
        if (Schema::hasTable('student_plans')) {
            $hasDuration = Schema::hasColumn('student_plans', 'lesson_duration');
            $plans = DB::table('student_plans')
                ->where('is_active', 1)
                ->orderBy('sort_order')
                ->orderBy('id')
                ->get()
                ->map(function ($plan) use ($hasDuration, $defaultDuration) {
                    return [
                        'key' => (string) ($plan->key ?? ''),
                        'title' => (string) ($plan->title ?? ''),
                        'display_title' => (string) ($plan->display_title ?? ''),
                        'label' => (string) ($plan->label ?? ''),
                        'subtitle' => (string) ($plan->subtitle ?? ''),
                        'tagline' => (string) ($plan->tagline ?? ''),
                        'duration_months' => (int) ($plan->duration_months ?? 0),
                        'lesson_duration' => $hasDuration ? (int) ($plan->lesson_duration ?? 0) : $defaultDuration,
                        'lessons_total' => (int) ($plan->lessons_total ?? 0),
                        'cancel_total' => (int) ($plan->cancel_total ?? 0),
                        'old_price' => (float) ($plan->old_price ?? 0),
                        'price' => (float) ($plan->price ?? 0),
                        'featured' => (bool) ($plan->featured ?? false),
                    ];
                })
                ->values();
        } else {
            $plans = collect(config('student_plans.plans', []))
                ->values()
                ->map(function ($plan) use ($defaultDuration) {
                    return [
                        'key' => (string) ($plan['key'] ?? ''),
                        'title' => (string) ($plan['title'] ?? ''),
                        'display_title' => (string) ($plan['display_title'] ?? ''),
                        'label' => (string) ($plan['label'] ?? ''),
                        'subtitle' => (string) ($plan['subtitle'] ?? ''),
                        'tagline' => (string) ($plan['tagline'] ?? ''),
                        'duration_months' => (int) ($plan['duration_months'] ?? 0),
                        'lesson_duration' => (int) ($plan['lesson_duration'] ?? $defaultDuration),
                        'lessons_total' => (int) ($plan['lessons_total'] ?? 0),
                        'cancel_total' => (int) ($plan['cancel_total'] ?? 0),
                        'old_price' => (float) ($plan['old_price'] ?? 0),
                        'price' => (float) ($plan['price'] ?? 0),
                        'featured' => (bool) ($plan['featured'] ?? false),
                    ];
                })
                ->values();
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'currency' => $currency,
                'list_price_per_lesson' => $listPrice,
                'plans' => $plans,
            ],
        ], 200);
    }

    public function corporateLead(Request $request): JsonResponse {
        $validator = Validator::make($request->all(), [
            'company_name' => ['required', 'string', 'max:255'],
            'contact_first_name' => ['required', 'string', 'max:255'],
            'contact_last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['required', 'string', 'max:50'],
            'trainees' => ['nullable', 'integer', 'min:1', 'max:100000'],
        ], [
            'company_name.required' => 'Company is required',
            'contact_first_name.required' => 'Name is required',
            'contact_last_name.required' => 'Name is required',
            'email.required' => 'Email is required',
            'phone.required' => 'Phone is required',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => $validator->errors()], 422);
        }

        $messageLines = [
            'Corporate Lead',
            '---',
            'Company name: ' . $request->company_name,
            'Company phone: ' . $request->phone,
            'Company email: ' . $request->email,
            'Contact person: ' . $request->contact_first_name . ' ' . $request->contact_last_name,
            'Trainees: ' . ($request->trainees ?: 'N/A'),
        ];

        $contact = new ContactMessage();
        $contact->name = trim($request->contact_first_name . ' ' . $request->contact_last_name);
        $contact->email = $request->email;
        $contact->phone = $request->phone;
        $contact->subject = 'Corporate';
        $contact->message = implode("\n", $messageLines);
        $contact->save();
        dispatch(new ContactMessageSendJob($contact));

        return response()->json(['status' => 'success', 'message' => 'Message sent successfully'], 200);
    }
}
