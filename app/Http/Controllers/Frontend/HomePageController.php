<?php

namespace App\Http\Controllers\Frontend;

use App\Enums\ThemeList;
use App\Http\Controllers\Controller;
use App\Jobs\DefaultMailJob;
use App\Mail\DefaultMail;
use App\Models\Course;
use App\Models\User;
use App\Models\UserEducation;
use App\Models\UserExperience;
use App\Rules\CustomRecaptcha;
use App\Support\CorporateBrandCatalog;
use App\Support\SeoBlogLibrary;
use App\Traits\MailSenderTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Modules\Badges\app\Models\Badge;
use Modules\Blog\app\Models\Blog;
use Modules\Brand\app\Models\Brand;
use Modules\Course\app\Models\CourseCategory;
use Modules\Faq\app\Models\Faq;
use Modules\Frontend\app\Models\FeaturedCourseSection;
use Modules\Frontend\app\Models\FeaturedInstructor;
use Modules\Frontend\app\Models\Section;
use Modules\GlobalSetting\app\Models\EmailTemplate;
use Modules\Location\app\Models\City;
use Modules\Location\app\Models\Country;
use Modules\Location\app\Models\State;
use Modules\PageBuilder\app\Models\CustomPage;
use Modules\SiteAppearance\app\Models\SectionSetting;
use Modules\Testimonial\app\Models\Testimonial;

class HomePageController extends Controller {
    use MailSenderTrait;

    function index(): View {
        $theme_name = Session::has('demo_theme') ? Session::get('demo_theme') : DEFAULT_HOMEPAGE;

        $sections = Section::whereHas("home", function ($q) use ($theme_name) {
            $q->where('slug', $theme_name);
        })->get();

        $hero = $sections->where('name', 'hero_section')->first();
        $slider = $sections->where('name', 'slider_section')->first();
        $aboutSection = $sections->where('name', 'about_section')->first();
        $newsletterSection = $sections->where('name', 'newsletter_section')->first();
        $counter = $sections->where('name', 'counter_section')->first();
        $ourFeatures = $sections->where('name', 'our_features_section')->first();
        $bannerSection = $sections->where('name', 'banner_section')->first();
        $faqSection = $sections->where('name', 'faq_section')->first();

        $faqs = Faq::with('translation')->where('status', 1)->get();

        $trendingCategories = CourseCategory::with(['translation:id,name,course_category_id', 'subCategories' => function ($query) {
            $query->with(['translation:id,name,course_category_id'])->withCount(['courses' => function ($query) {
                $query->where('status', 'active');
            }]);
        }])->withCount(['subCategories as active_sub_categories_count' => function ($query) {
            $query->whereHas('courses', function ($query) {
                $query->where('status', 'active');
            });
        }])->whereNull('parent_id')
            ->where('status', 1)
            ->where('show_at_trending', 1)
            ->get();

        $brands = CorporateBrandCatalog::merge(Brand::where('status', 1)->get());

        $featuredCourse = FeaturedCourseSection::first();

        $featuredInstructorSection = FeaturedInstructor::first();
        $instructorIds = json_decode($featuredInstructorSection->instructor_ids ?? '[]');

        $selectedInstructors = User::whereIn('id', $instructorIds)
            ->with(['courses' => function ($query) {
                $query->withCount(['reviews as avg_rating' => function ($query) {
                    $query->select(DB::raw('coalesce(avg(rating),0)'));
                }]);
            }])
            ->get();

        $featuredInstructorVideos = $selectedInstructors
            ->filter(fn($instructor) => filled(data_get($instructor->instructor_profile, 'intro_video')))
            ->values();

        if (Schema::hasColumn('users', 'instructor_profile')) {
            $extraInstructorVideos = User::query()
                ->active()
                ->unbanned()
                ->instructor()
                ->whereNotIn('id', $featuredInstructorVideos->pluck('id')->all())
                ->where('instructor_profile', 'like', '%"intro_video":"%')
                ->orderBy('name')
                ->get()
                ->filter(fn($instructor) => filled(data_get($instructor->instructor_profile, 'intro_video')))
                ->values();

            $featuredInstructorVideos = $featuredInstructorVideos
                ->concat($extraInstructorVideos)
                ->unique('id')
                ->values();
        }

        $testimonials = Testimonial::all();

        $featuredBlogs = Blog::with(['translation', 'category.translation', 'author'])
            ->whereHas('category', function ($q) {$q->where('status', 1);})
            ->where(['show_homepage' => 1, 'status' => 1])->orderBy('created_at', 'desc')->limit(4)->get();
        $featuredBlogs = app(SeoBlogLibrary::class)->featured(
            app(SeoBlogLibrary::class)->mergeWithDatabase($featuredBlogs),
            4
        );
        $sectionSetting = SectionSetting::first();

        return view('frontend.home.' . $theme_name . '.index', compact(
            'hero',
            'slider',
            'trendingCategories',
            'brands',
            'aboutSection',
            'featuredCourse',
            'newsletterSection',
            'featuredInstructorSection',
            'selectedInstructors',
            'featuredInstructorVideos',
            'counter',
            'faqSection',
            'faqs',
            'testimonials',
            'ourFeatures',
            'bannerSection',
            'featuredBlogs',
            'sectionSetting'
        ));
    }

    public function sitemap(): Response {
        $staticUrls = collect([
            $this->sitemapEntry(route('home'), now(), 'daily', '1.0'),
            $this->sitemapEntry(route('courses'), now(), 'daily', '0.9'),
            $this->sitemapEntry(route('lingufranca-performance'), now(), 'weekly', '0.95'),
            $this->sitemapEntry(route('english-private-lessons'), now(), 'weekly', '0.95'),
            $this->sitemapEntry(route('english-private-lessons.online'), now(), 'weekly', '0.85'),
            $this->sitemapEntry(route('english-private-lessons.speaking'), now(), 'weekly', '0.85'),
            $this->sitemapEntry(route('english-private-lessons.business'), now(), 'weekly', '0.85'),
            $this->sitemapEntry(route('english-private-lessons.istanbul'), now(), 'weekly', '0.75'),
            $this->sitemapEntry(route('english-private-lessons.ankara'), now(), 'weekly', '0.75'),
            $this->sitemapEntry(route('english-private-lessons.izmir'), now(), 'weekly', '0.75'),
            $this->sitemapEntry(route('corporate.index'), now(), 'weekly', '0.9'),
            $this->sitemapEntry(route('corporate.form'), now(), 'weekly', '0.6'),
            $this->sitemapEntry(route('all-instructors'), now(), 'daily', '0.8'),
            $this->sitemapEntry(route('blogs'), now(), 'daily', '0.8'),
            $this->sitemapEntry(route('about-us'), now(), 'monthly', '0.7'),
            $this->sitemapEntry(route('contact.index'), now(), 'monthly', '0.7'),
            $this->sitemapEntry(route('placement-test.show'), now(), 'weekly', '0.8'),
            $this->sitemapEntry(route('mobile-app-privacy-policy'), now(), 'yearly', '0.3'),
            $this->sitemapEntry(route('delivery-return-terms'), now(), 'yearly', '0.3'),
            $this->sitemapEntry(route('distance-sales-contract'), now(), 'yearly', '0.3'),
        ]);

        $courseUrls = Course::query()
            ->where('status', 'active')
            ->select(['slug', 'updated_at', 'created_at'])
            ->get()
            ->map(fn($course) => $this->sitemapEntry(
                route('course.show', $course->slug),
                $course->updated_at ?? $course->created_at,
                'weekly',
                '0.8'
            ));

        $blogUrls = Blog::query()
            ->where('status', 1)
            ->select(['slug', 'updated_at', 'created_at'])
            ->get()
            ->map(fn($blog) => $this->sitemapEntry(
                route('blog.show', $blog->slug),
                $blog->updated_at ?? $blog->created_at,
                'weekly',
                '0.7'
            ));
        $staticBlogUrls = app(SeoBlogLibrary::class)
            ->mergeWithDatabase(collect())
            ->map(fn($blog) => $this->sitemapEntry(
                route('blog.show', $blog->slug),
                $blog->updated_at ?? $blog->created_at,
                'weekly',
                '0.7'
            ));

        $customPageUrls = CustomPage::query()
            ->select(['slug', 'updated_at', 'created_at'])
            ->get()
            ->map(fn($page) => $this->sitemapEntry(
                route('custom-page', $page->slug),
                $page->updated_at ?? $page->created_at,
                'monthly',
                '0.6'
            ));

        $instructorUrls = User::query()
            ->select(['id', 'name', 'status', 'role', 'is_banned', 'updated_at', 'created_at'])
            ->where('status', 'active')
            ->where('role', 'instructor')
            ->where(function ($query) {
                $query->where('is_banned', 'no')
                    ->orWhereNull('is_banned')
                    ->orWhere('is_banned', '0');
            })
            ->get()
            ->map(fn($instructor) => $this->sitemapEntry(
                route('instructor-details', [$instructor->id, Str::slug((string) $instructor->name) ?: 'instructor']),
                $instructor->updated_at ?? $instructor->created_at,
                'weekly',
                '0.6'
            ));

        $urls = $staticUrls
            ->merge($courseUrls)
            ->merge($blogUrls)
            ->merge($staticBlogUrls)
            ->merge($customPageUrls)
            ->merge($instructorUrls)
            ->unique('loc')
            ->values();

        return response()
            ->view('frontend.seo.sitemap', compact('urls'))
            ->header('Content-Type', 'application/xml; charset=UTF-8');
    }

    public function robots(): Response {
        $content = implode(PHP_EOL, [
            'User-agent: *',
            'Allow: /',
            'Disallow: /admin',
            'Disallow: /student/',
            'Disallow: /instructor/',
            'Disallow: /cart',
            'Disallow: /checkout',
            'Disallow: /wishlist',
            'Disallow: /learning/',
            'Sitemap: ' . route('sitemap'),
        ]);

        return response($content, 200)->header('Content-Type', 'text/plain; charset=UTF-8');
    }

    public function englishPrivateLesson(Request $request): View {
        $page = (string) $request->route('page', 'hub');
        $pages = $this->englishPrivateLessonPages();

        abort_unless(isset($pages[$page]), 404);

        $pageData = $pages[$page];
        $relatedPages = collect($pageData['related_pages'] ?? array_diff(array_keys($pages), [$page]))
            ->map(function (string $relatedPage) use ($pages) {
                $related = $pages[$relatedPage] ?? null;

                if (!$related) {
                    return null;
                }

                return [
                    'title' => $related['nav_title'] ?? $related['breadcrumb'],
                    'description' => $related['nav_description'] ?? $related['lead'],
                    'url' => route($related['route']),
                ];
            })
            ->filter()
            ->values();

        return view('frontend.pages.english-private-lessons', compact('pageData', 'relatedPages'));
    }

    public function linguFrancaPerformance(): View {
        $performanceConfig = $this->loadLinguFrancaPerformanceConfig();
        $pageData = (array) data_get($performanceConfig, 'page', []);

        $downloads = collect(data_get($performanceConfig, 'downloads', []))
            ->map(function (array $item) {
                $item['cover_url'] = $this->linguFrancaPerformanceAssetUrl($item['cover_asset'] ?? null);
                $item['file_url'] = $this->linguFrancaPerformanceAssetUrl($item['file_asset'] ?? null);
                return $item;
            })
            ->values()
            ->all();

        $mediaLibrary = collect(data_get($performanceConfig, 'media_library', []))
            ->map(function (array $item) {
                $item['file_url'] = $this->linguFrancaPerformanceAssetUrl($item['file_asset'] ?? null);
                $item['poster_url'] = $this->linguFrancaPerformanceAssetUrl($item['poster_asset'] ?? null);
                return $item;
            })
            ->filter(fn(array $item) => filled($item['file_url']))
            ->values()
            ->all();

        $pageData['hero_primary_visual'] = $this->linguFrancaPerformanceAssetUrl('general-cover');
        $pageData['hero_secondary_visual'] = $this->linguFrancaPerformanceAssetUrl('ielts-cover');
        $pageData['hero_tertiary_visual'] = $this->linguFrancaPerformanceAssetUrl('pte-cover');
        $pageData['meta_image_url'] = $pageData['hero_primary_visual'] ?: $pageData['hero_secondary_visual'];
        $deckGalleries = $this->linguFrancaPerformanceDeckGalleries($downloads);

        return view('frontend.pages.lingufranca-performance-v7', compact('pageData', 'downloads', 'mediaLibrary', 'deckGalleries'));
    }

    public function linguFrancaPerformanceAsset(string $asset) {
        $path = $this->resolveLinguFrancaPerformanceAssetPath($asset);
        abort_unless($path, 404);

        $extension = Str::lower(pathinfo($path, PATHINFO_EXTENSION));
        $mime = match ($extension) {
            'pdf' => 'application/pdf',
            'png' => 'image/png',
            'jpg', 'jpeg' => 'image/jpeg',
            'mp4' => 'video/mp4',
            default => mime_content_type($path) ?: 'application/octet-stream',
        };

        return response()->file($path, [
            'Content-Type' => $mime,
            'Cache-Control' => 'public, max-age=86400',
        ]);
    }

    private function linguFrancaPerformanceDeckGalleries(array $downloads): array {
        $galleryMap = [
            [
                'slug' => 'general-english',
                'eyebrow' => 'Genel İngilizce Akışı',
                'title' => data_get($downloads, '0.title', 'LinguFranca Genel İngilizce'),
                'lead' => 'Program davetinden süreç tasarımına kadar tüm akış burada.',
            ],
            [
                'slug' => 'ielts-exam',
                'eyebrow' => 'IELTS / TOEFL / YDS Akışı',
                'title' => data_get($downloads, '1.title', 'LinguFranca Sınav Programı'),
                'lead' => 'Sınav sisteminin tüm anlatımı sayfa sayfa burada.',
            ],
            [
                'slug' => 'pte-academic',
                'eyebrow' => 'PTE Academic Akışı',
                'title' => data_get($downloads, '2.title', 'PTE Academic Programı'),
                'lead' => 'PTE tarafındaki tam sunum akışı görselleriyle burada.',
            ],
        ];

        return collect($galleryMap)
            ->map(function (array $deck) {
                $files = glob(public_path('uploads/lingufranca-performance/decks/' . $deck['slug'] . '/page-*.png')) ?: [];
                sort($files, SORT_NATURAL);

                $publicBase = str_replace('\\', '/', public_path()) . '/';
                $pages = collect($files)
                    ->map(function (string $path) use ($publicBase) {
                        $normalized = str_replace('\\', '/', $path);
                        return asset(Str::after($normalized, $publicBase));
                    })
                    ->values()
                    ->all();

                $deck['page_count'] = count($pages);
                $deck['pages'] = $pages;

                return $deck;
            })
            ->filter(fn(array $deck) => ! empty($deck['pages']))
            ->values()
            ->all();
    }

    function countries(): JsonResponse {
        $countries = Country::where('status', 1)->get();
        return response()->json($countries);
    }

    function states(string $id): JsonResponse {
        $states = State::where(['country_id' => $id, 'status' => 1])->get();
        return response()->json($states);
    }

    function cities(string $id): JsonResponse {
        $cities = City::where(['state_id' => $id, 'status' => 1])->get();
        return response()->json($cities);
    }

    public function setCurrency() {
        $currency = allCurrencies()->where('currency_code', request('currency'))->first();
        if (session()->has('currency_code')) {
            session()->forget('currency_code');
            session()->forget('currency_position');
            session()->forget('currency_icon');
            session()->forget('currency_rate');
        }
        if ($currency) {
            session()->put('currency_code', $currency->currency_code);
            session()->put('currency_position', $currency->currency_position);
            session()->put('currency_icon', $currency->currency_icon);
            session()->put('currency_rate', $currency->currency_rate);

            $notification = __('Currency Changed Successfully');
            $notification = ['messege' => $notification, 'alert-type' => 'success'];

            return redirect()->back()->with($notification);
        }
        getSessionCurrency();
        $notification = __('Currency Changed Successfully');
        $notification = ['messege' => $notification, 'alert-type' => 'success'];

        return redirect()->back()->with($notification);
    }

    function instructorDetails(string $id) {
        $instructor = User::where(['status' => 'active', 'is_banned' => 0, 'id' => $id])->with(['courses' => function ($query) {
            $query->withCount('enrollments')->withCount(['reviews as avg_rating' => function ($query) {
                $query->select(DB::raw('coalesce(avg(rating),0)'));
            }]);
        }])
            ->firstOrFail();
        $experiences = UserExperience::where(['user_id' => $id])->get();
        $educations = UserEducation::where(['user_id' => $id])->get();
        $courses = Course::active()->where(['instructor_id' => $id])
            ->with(['category.translation', 'instructor'])
            ->withAvg('reviews', 'rating')
            ->orderByDesc('id')
            ->get();
        $badges = Badge::where(['status' => 1])->get()->groupBy('key');
        return view('frontend.pages.instructor-details', compact('instructor', 'experiences', 'educations', 'courses', 'badges'));
    }

    function allInstructors(Request $request) {
        $search = trim((string) $request->get('search', ''));
        $rawTags = $request->get('tag', []);
        $tags = is_array($rawTags) ? $rawTags : [$rawTags];
        $tags = array_values(array_filter(array_map('trim', $tags), function ($value) {
            return $value !== '';
        }));

        $tagMap = [
            // Stable keys (new UI values)
            'nationality_turkish' => 'nationality_turkish',
            'nationality_foreign' => 'nationality_foreign',
            'speaks_turkish_yes' => 'speaks_turkish_yes',
            'speaks_turkish_no' => 'speaks_turkish_no',
            'category_general' => 'category_general',
            'category_speaking' => 'category_speaking',
            'category_kids' => 'category_kids',
            'category_exam' => 'category_exam',
            'category_business' => 'category_business',
            'availability_morning' => 'availability_morning',
            'availability_afternoon' => 'availability_afternoon',
            'availability_evening' => 'availability_evening',

            // Backward compatibility (old translated values / legacy values)
            Str::lower(__('Turkish')) => 'nationality_turkish',
            Str::lower(__('Foreign')) => 'nationality_foreign',
            Str::lower(__('Turkish Language')) => 'speaks_turkish_yes',
            Str::lower(__('English')) => 'speaks_turkish_no',
            Str::lower(__('General English')) => 'category_general',
            Str::lower(__('Speaking Lessons')) => 'category_speaking',
            Str::lower(__('For Kids')) => 'category_kids',
            Str::lower(__('IELTS & TOEFL')) => 'category_exam',
            Str::lower(__('Business English')) => 'category_business',
            '06:00' => 'availability_morning',
            '12:00' => 'availability_afternoon',
            '18:00' => 'availability_evening',
        ];

        $filterKeys = [];
        foreach ($tags as $tag) {
            $lookup = Str::lower((string) $tag);
            if (isset($tagMap[$lookup])) {
                $filterKeys[] = $tagMap[$lookup];
            } elseif (isset($tagMap[$tag])) {
                $filterKeys[] = $tagMap[$tag];
            }
        }
        $filterKeys = array_values(array_unique($filterKeys));

        $nationalityFilters = array_values(array_intersect($filterKeys, ['nationality_turkish', 'nationality_foreign']));
        $speaksFilters = array_values(array_intersect($filterKeys, ['speaks_turkish_yes', 'speaks_turkish_no']));
        $categoryFilters = array_values(array_intersect($filterKeys, ['category_general', 'category_speaking', 'category_kids', 'category_exam', 'category_business']));
        $availabilityFilters = array_values(array_intersect($filterKeys, ['availability_morning', 'availability_afternoon', 'availability_evening']));

        $instructorsQuery = User::query()
            ->where('status', 'active')
            ->where('role', 'instructor')
            ->where(function ($q) {
                // `users.is_banned` is string ('yes'/'no') in this project, but some installs may use 0/1.
                $q->where('is_banned', 'no')
                    ->orWhereNull('is_banned')
                    ->orWhere('is_banned', '0');
            })
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%')
                        ->orWhere('job_title', 'like', '%' . $search . '%')
                        ->orWhere('short_bio', 'like', '%' . $search . '%')
                        ->orWhere('bio', 'like', '%' . $search . '%');
                });
            });

        if (!empty($nationalityFilters)) {
            $hasCountriesTable = Schema::hasTable('countries');
            $turkeyCondition = function ($countryQuery) {
                $countryQuery->where(function ($nameQuery) {
                    $nameQuery->where('name', 'like', '%Turkey%')
                        ->orWhere('name', 'like', '%Turkiye%')
                        ->orWhere('name', 'like', '%Türkiye%')
                        ->orWhere('name', 'like', '%Turk%')
                        ->orWhere('name', 'like', '%Türk%');
                });
            };

            $instructorsQuery->where(function ($query) use ($nationalityFilters, $hasCountriesTable, $turkeyCondition) {
                foreach ($nationalityFilters as $filter) {
                    if ($filter === 'nationality_turkish') {
                        if ($hasCountriesTable) {
                            $query->orWhereHas('country', $turkeyCondition);
                        } else {
                            $query->orWhere(function ($fallback) {
                                $fallback->where('name', 'like', '%Turk%')
                                    ->orWhere('short_bio', 'like', '%Turk%')
                                    ->orWhere('bio', 'like', '%Turk%');
                            });
                        }
                    }

                    if ($filter === 'nationality_foreign') {
                        if ($hasCountriesTable) {
                            $query->orWhere(function ($foreignQuery) use ($turkeyCondition) {
                                $foreignQuery->whereNull('country_id')
                                    ->orWhereDoesntHave('country', $turkeyCondition);
                            });
                        } else {
                            // Without countries table, keep broad result instead of hiding all instructors.
                            $query->orWhereNotNull('id');
                        }
                    }
                }
            });
        }

        if (!empty($speaksFilters) && Schema::hasColumn('users', 'instructor_profile')) {
            $instructorsQuery->where(function ($query) use ($speaksFilters) {
                foreach ($speaksFilters as $filter) {
                    if ($filter === 'speaks_turkish_yes') {
                        $query->orWhere(function ($yesQuery) {
                            $yesQuery->where('instructor_profile', 'like', '%"turkish_level":"beginner"%')
                                ->orWhere('instructor_profile', 'like', '%"turkish_level":"intermediate"%')
                                ->orWhere('instructor_profile', 'like', '%"turkish_level":"advanced"%')
                                ->orWhere('instructor_profile', 'like', '%"turkish_level":"native"%');
                        });
                    }

                    if ($filter === 'speaks_turkish_no') {
                        $query->orWhere(function ($noQuery) {
                            $noQuery->whereNull('instructor_profile')
                                ->orWhere('instructor_profile', '=', '')
                                ->orWhere('instructor_profile', 'not like', '%"turkish_level":"%');
                        });
                    }
                }
            });
        }

        if (!empty($categoryFilters) && Schema::hasColumn('users', 'instructor_profile')) {
            $categoryPatterns = [
                'category_general' => ['%"general_english_a1"%'],
                'category_speaking' => ['%"speaking_b1"%'],
                'category_kids' => ['%"kids_6_12"%', '%"young_13_18"%'],
                'category_exam' => ['%"exams"%'],
                'category_business' => ['%"business_english"%'],
            ];

            $instructorsQuery->where(function ($query) use ($categoryFilters, $categoryPatterns) {
                foreach ($categoryFilters as $filter) {
                    $patterns = $categoryPatterns[$filter] ?? [];
                    if (empty($patterns)) {
                        continue;
                    }

                    $query->orWhere(function ($categoryQuery) use ($patterns) {
                        foreach ($patterns as $pattern) {
                            $categoryQuery->orWhere('instructor_profile', 'like', $pattern);
                        }
                    });
                }
            });
        }

        if (!empty($availabilityFilters)
            && Schema::hasTable('instructor_availabilities')
            && Schema::hasColumn('instructor_availabilities', 'instructor_id')
            && Schema::hasColumn('instructor_availabilities', 'start_time')
            && Schema::hasColumn('instructor_availabilities', 'end_time')
        ) {
            $availabilityRanges = [
                'availability_morning' => ['06:00:00', '12:00:00'],
                'availability_afternoon' => ['12:00:00', '18:00:00'],
                'availability_evening' => ['18:00:00', '23:59:59'],
            ];

            $instructorsQuery->where(function ($query) use ($availabilityFilters, $availabilityRanges) {
                foreach ($availabilityFilters as $filter) {
                    [$start, $end] = $availabilityRanges[$filter] ?? [null, null];
                    if (!$start || !$end) {
                        continue;
                    }

                    $query->orWhereExists(function ($subQuery) use ($start, $end) {
                        $subQuery->selectRaw('1')
                            ->from('instructor_availabilities as ia')
                            ->whereColumn('ia.instructor_id', 'users.id')
                            ->where('ia.is_active', 1)
                            ->whereRaw('TIME(ia.start_time) < ?', [$end])
                            ->whereRaw('TIME(ia.end_time) > ?', [$start]);
                    });
                }
            });
        }

        $hasLiveRatingColumns = Schema::hasTable('student_live_lessons')
            && Schema::hasColumn('student_live_lessons', 'student_rating')
            && Schema::hasColumn('student_live_lessons', 'status')
            && Schema::hasColumn('student_live_lessons', 'instructor_id');

        if ($hasLiveRatingColumns) {
            $ratingScope = function ($query) {
                $query->whereNotNull('student_rating')
                    ->whereNotIn('status', ['cancelled_teacher', 'cancelled_student']);
            };

            $instructorsQuery
                ->withAvg(['liveLessonsAsInstructor as avg_live_rating' => $ratingScope], 'student_rating')
                ->withCount(['liveLessonsAsInstructor as rating_count' => $ratingScope])
                ->orderByDesc('avg_live_rating')
                ->orderByDesc('rating_count');
        } else {
            $instructorsQuery->orderBy('name');
        }

        $instructors = $instructorsQuery
            ->paginate(18)
            ->appends(['search' => $search, 'tag' => $tags]);

        return view('frontend.pages.all-instructors', compact('instructors'));
    }

    function quickConnect(Request $request, string $id) {
        $validated = $request->validate([
            'name'                 => ['required', 'string', 'max:255'],
            'email'                => ['required', 'string', 'email', 'max:255'],
            'subject'              => ['required', 'string', 'max:255'],
            'message'              => ['required', 'string', 'max:1000'],
            'g-recaptcha-response' => Cache::get('setting')->recaptcha_status == 'active' ? ['required', new CustomRecaptcha()] : 'nullable',
        ]);

        $settings = cache()->get('setting');
        $marketingSettings = cache()->get('marketing_setting');
        if ($settings->google_tagmanager_status == 'active' && $marketingSettings->instructor_contact) {
            $instructor_contact = [
                'name'    => $request->name,
                'email'   => $request->email,
                'subject' => $request->subject,
                'message' => $request->message,
            ];
            session()->put('instructorQuickContact', $instructor_contact);
        }

        $this->handleMailSending($validated);
        return redirect()->back()->with(['messege' => __('Message sent successfully'), 'alert-type' => 'success']);
    }

    function handleMailSending(array $mailData) {
        self::setMailConfig();

        // Get email template
        $template = EmailTemplate::where('name', 'instructor_quick_contact')->firstOrFail();

        // Prepare email content
        $message = str_replace('{{name}}', $mailData['name'], $template->message);
        $message = str_replace('{{email}}', $mailData['email'], $message);
        $message = str_replace('{{subject}}', $mailData['subject'], $message);
        $message = str_replace('{{message}}', $mailData['message'], $message);

        if (self::isQueable()) {
            DefaultMailJob::dispatch($mailData['email'], $mailData, $message);
        } else {
            Mail::to($mailData['email'])->send(new DefaultMail($mailData, $message));
        }
    }

    function customPage(string $slug) {
        $page = CustomPage::where('slug', $slug)->firstOrFail();
        return view('frontend.pages.custom-page', compact('page'));
    }

    function changeTheme(string $theme) {
        if (Cache::get('setting')?->show_all_homepage != 1) {
            abort(404);
        }

        foreach (ThemeList::cases() as $enumTheme) {
            if ($theme == $enumTheme->value) {
                Session::put('demo_theme', $enumTheme->value);
                break;
            }
        }
        return redirect('/');
    }

    private function englishPrivateLessonPages(): array {
        return (array) config('english_private_lessons.pages', []);
    }

    private function linguFrancaPerformanceAssetUrl(?string $asset): ?string {
        if (blank($asset)) {
            return null;
        }

        $path = $this->resolveLinguFrancaPerformanceAssetPath((string) $asset);
        if (!$path) {
            $fallbackPath = $this->linguFrancaPerformanceFallbackAssetPath((string) $asset);
            if ($fallbackPath) {
                return asset($fallbackPath);
            }

            return null;
        }

        $normalizedPath = str_replace('\\', '/', $path);
        $publicRoot = rtrim(str_replace('\\', '/', public_path()), '/') . '/';

        if (str_starts_with($normalizedPath, $publicRoot)) {
            return asset(Str::after($normalizedPath, $publicRoot));
        }

        return route('lingufranca-performance.asset', ['asset' => $asset]);
    }

    private function linguFrancaPerformanceFallbackAssetPath(string $asset): ?string {
        $map = [
            'general-pdf' => 'uploads/lingufranca-performance/pdfs/general-english.pdf',
            'ielts-pdf' => 'uploads/lingufranca-performance/pdfs/ielts-exam.pdf',
            'pte-pdf' => 'uploads/lingufranca-performance/pdfs/pte-exam.pdf',
            'general-cover' => 'uploads/lingufranca-performance/covers/general-cover.png',
            'ielts-cover' => 'uploads/lingufranca-performance/covers/ielts-cover.png',
            'pte-cover' => 'uploads/lingufranca-performance/covers/pte-cover.png',
            'beyaz-tv-video' => 'uploads/lingufranca-performance/videos/beyaz-tv-preview.mp4',
            'tv8-video' => 'uploads/lingufranca-performance/videos/tv8-preview.mp4',
            'cnn-video' => 'uploads/lingufranca-performance/videos/cnn-turk-preview.mp4',
            'ezgi-video' => 'uploads/lingufranca-performance/videos/ezgi-aze-preview.mp4',
            'furkan-video' => 'uploads/lingufranca-performance/videos/furkan-kanadmis-preview.mp4',
            'gizem-video' => 'uploads/lingufranca-performance/videos/gizem-preview.mp4',
            'beyaz-tv-poster' => 'uploads/lingufranca-performance/posters/beyaz-tv-poster.jpg',
            'tv8-poster' => 'uploads/lingufranca-performance/posters/tv8-poster.jpg',
            'cnn-turk-poster' => 'uploads/lingufranca-performance/posters/cnn-turk-poster.jpg',
            'ezgi-aze-poster' => 'uploads/lingufranca-performance/posters/ezgi-aze-poster.jpg',
            'furkan-kanadmis-poster' => 'uploads/lingufranca-performance/posters/furkan-kanadmis-poster.jpg',
            'gizem-poster' => 'uploads/lingufranca-performance/posters/gizem-poster.jpg',
        ];

        return $map[$asset] ?? null;
    }

    private function resolveLinguFrancaPerformanceAssetPath(string $asset): ?string {
        $definition = data_get($this->loadLinguFrancaPerformanceConfig(), "assets.{$asset}");
        if (!is_array($definition)) {
            return null;
        }

        $directory = base_path((string) ($definition['directory'] ?? ''));
        $extension = ltrim((string) ($definition['extension'] ?? ''), '.');
        $tokens = array_values(array_filter((array) ($definition['tokens'] ?? [])));

        if ($directory === '' || $extension === '' || empty($tokens) || !is_dir($directory)) {
            return null;
        }

        $pattern = $directory . DIRECTORY_SEPARATOR . '*.' . $extension;
        $files = glob($pattern) ?: [];

        foreach ($files as $file) {
            $normalizedName = Str::of(pathinfo($file, PATHINFO_FILENAME))
                ->ascii()
                ->lower()
                ->value();

            $matchesAllTokens = collect($tokens)->every(function (string $token) use ($normalizedName) {
                $normalizedToken = Str::of($token)->ascii()->lower()->value();
                return $normalizedToken !== '' && str_contains($normalizedName, $normalizedToken);
            });

            if ($matchesAllTokens) {
                return $file;
            }
        }

        return null;
    }

    private function loadLinguFrancaPerformanceConfig(): array {
        $configPath = config_path('lingufranca_performance.php');

        if (is_file($configPath)) {
            $config = require $configPath;
            if (is_array($config)) {
                return $config;
            }
        }

        return (array) config('lingufranca_performance', []);
    }

    private function sitemapEntry(string $loc, mixed $timestamp = null, string $changefreq = 'weekly', string $priority = '0.7'): array {
        return [
            'loc' => $loc,
            'lastmod' => $this->resolveSitemapTimestamp($timestamp),
            'changefreq' => $changefreq,
            'priority' => $priority,
        ];
    }

    private function resolveSitemapTimestamp(mixed $timestamp): string {
        if ($timestamp instanceof \DateTimeInterface) {
            return $timestamp->format(\DateTimeInterface::ATOM);
        }

        return now()->format(\DateTimeInterface::ATOM);
    }
}
