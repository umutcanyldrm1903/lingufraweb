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
use App\Traits\MailSenderTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
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

        $brands = Brand::where('status', 1)->get();

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

        $testimonials = Testimonial::all();

        $featuredBlogs = Blog::with(['translation', 'author'])
            ->whereHas('category', function ($q) {$q->where('status', 1);})
            ->where(['show_homepage' => 1, 'status' => 1])->orderBy('created_at', 'desc')->limit(4)->get();
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
}
