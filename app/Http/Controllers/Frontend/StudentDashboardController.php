<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseChapterItem;
use App\Models\CourseLiveClass;
use App\Models\CourseProgress;
use App\Models\InstructorAvailability;
use App\Models\LiveLessonAttendance;
use App\Models\QuizResult;
use App\Models\StudentLiveLesson;
use App\Models\StudentLiveLessonAttendance;
use App\Models\User;
use App\Models\UserPlan;
use App\Services\Referral\ReferralService;
use App\Services\Zoom\ZoomOAuthService;
use Carbon\Carbon;
use Dompdf\Dompdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Str;
use Modules\BasicPayment\app\Services\PaymentMethodService;
use Modules\CertificateBuilder\app\Models\CertificateBuilder;
use Modules\CertificateBuilder\app\Models\CertificateBuilderItem;
use Modules\Order\app\Models\Enrollment;
use Modules\Order\app\Models\Order;
use Modules\Order\app\Models\OrderItem;

class StudentDashboardController extends Controller {
    public function index(Request $request): View {
        $user = userAuth();
        if ($user instanceof User) {
            $this->syncUserPlanFromLatestPaidOrder($user);
        }

        $plans = collect();
        if (Schema::hasTable('student_plans')) {
            $plans = DB::table('student_plans')
                ->where('is_active', 1)
                ->orderBy('sort_order')
                ->orderBy('id')
                ->get();
        } else {
            $plans = collect(config('student_plans.plans', []))->values()->map(function ($plan) {
                return (object) $plan;
            });
        }

        $upcomingLessonsPreview = $user instanceof User
            ? $this->buildUpcomingLessonPreview($user, 4)
            : collect();

        return view('frontend.student-dashboard.index', compact('plans', 'upcomingLessonsPreview'));
    }

    public function invite(Request $request): View
    {
        $user = userAuth();
        if ($user instanceof User) {
            $this->syncUserPlanFromLatestPaidOrder($user);
        }

        $referralCode = '';
        try {
            $referralCode = app(ReferralService::class)->ensureReferralCode($user);
        } catch (\Throwable $e) {
            report($e);
        }

        $inviteUrl = $referralCode !== '' ? url('/register?ref=' . $referralCode) : url('/register');

        return view('frontend.student-dashboard.invite.index', compact('referralCode', 'inviteUrl'));
    }

    public function reports(Request $request): View
    {
        $user = userAuth();
        if ($user instanceof User) {
            $this->syncUserPlanFromLatestPaidOrder($user);
        }

        $startDate = trim((string) $request->query('start_date', ''));
        $endDate = trim((string) $request->query('end_date', ''));
        $reports = collect();

        if ($user instanceof User && Schema::hasTable('student_live_lessons')) {
            $query = StudentLiveLesson::query()
                ->where('student_id', $user->id)
                ->whereNotNull('instructor_summary')
                ->where('instructor_summary', '!=', '')
                ->with(['instructor:id,name,image'])
                ->orderByDesc('start_time');

            if ($startDate !== '') {
                try {
                    $query->whereDate('start_time', '>=', Carbon::parse($startDate)->toDateString());
                } catch (\Throwable $e) {
                    // Ignore invalid date filter.
                }
            }

            if ($endDate !== '') {
                try {
                    $query->whereDate('start_time', '<=', Carbon::parse($endDate)->toDateString());
                } catch (\Throwable $e) {
                    // Ignore invalid date filter.
                }
            }

            $reports = $query->get()->map(function (StudentLiveLesson $lesson) {
                return (object) [
                    'id' => (int) $lesson->id,
                    'title' => $lesson->title ?: __('Private Live Lesson'),
                    'summary' => (string) $lesson->instructor_summary,
                    'status' => (string) ($lesson->status ?: 'scheduled'),
                    'instructor_name' => $lesson->instructor?->first_name ?: '-',
                    'start_time' => $lesson->start_time,
                    'date_label' => $lesson->start_time ? formattedDateTime($lesson->start_time) : '-',
                    'written_at' => $lesson->instructor_summary_written_at,
                ];
            })->values();
        }

        return view('frontend.student-dashboard.reports.index', compact('reports', 'startDate', 'endDate'));
    }

    public function instructors(Request $request): View
    {
        $user = userAuth();
        if ($user instanceof User) {
            $this->syncUserPlanFromLatestPaidOrder($user);
        }

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

        // Be defensive: don't 500 if DB migrations haven't been applied yet.
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

        return view('frontend.student-dashboard.instructors.index', compact('instructors'));
    }

    public function showLiveLessonRating(StudentLiveLesson $lesson): View
    {
        $user = userAuth();

        if (!Schema::hasTable('student_live_lessons') || !Schema::hasColumn('student_live_lessons', 'student_rating')) {
            abort(404);
        }

        if (!$user || !($user instanceof User) || $user->role !== 'student') {
            abort(403);
        }

        if ((int) $lesson->student_id !== (int) $user->id) {
            abort(404);
        }

        if (in_array((string) $lesson->status, ['cancelled_teacher', 'cancelled_student'], true)) {
            return redirect()->route('student.enrolled-courses')->with([
                'messege' => __('This lesson was cancelled, so it cannot be rated.'),
                'alert-type' => 'error',
            ]);
        }

        if ($lesson->start_time && $lesson->start_time->isFuture()) {
            return redirect()->route('student.enrolled-courses')->with([
                'messege' => __('You cannot rate the lesson before it ends.'),
                'alert-type' => 'error',
            ]);
        }

        $lesson->load(['instructor:id,name,image']);

        return view('frontend.student-dashboard.live-lessons.rate', compact('lesson'));
    }

    public function storeLiveLessonRating(Request $request, StudentLiveLesson $lesson): RedirectResponse
    {
        $user = userAuth();

        if (!Schema::hasTable('student_live_lessons') || !Schema::hasColumn('student_live_lessons', 'student_rating')) {
            return redirect()->route('student.enrolled-courses')->with([
                'messege' => __('Rating is not available yet. Please run the required migrations.'),
                'alert-type' => 'error',
            ]);
        }

        if (!$user || !($user instanceof User) || $user->role !== 'student') {
            abort(403);
        }

        if ((int) $lesson->student_id !== (int) $user->id) {
            abort(404);
        }

        if (in_array((string) $lesson->status, ['cancelled_teacher', 'cancelled_student'], true)) {
            return redirect()->route('student.enrolled-courses')->with([
                'messege' => __('This lesson was cancelled, so it cannot be rated.'),
                'alert-type' => 'error',
            ]);
        }

        if ($lesson->start_time && $lesson->start_time->isFuture()) {
            return redirect()->route('student.enrolled-courses')->with([
                'messege' => __('You cannot rate the lesson before it ends.'),
                'alert-type' => 'error',
            ]);
        }

        if (!empty($lesson->student_rating)) {
            return redirect()->route('student.enrolled-courses')->with([
                'messege' => __('You have already rated this lesson.'),
                'alert-type' => 'info',
            ]);
        }

        $validated = $request->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'review' => ['nullable', 'string', 'max:1000'],
        ]);

        $lesson->student_rating = (int) $validated['rating'];
        $lesson->student_review = trim((string) ($validated['review'] ?? '')) ?: null;
        $lesson->rated_at = now();
        $lesson->save();

        return redirect()->route('student.enrolled-courses')->with([
            'messege' => __('Your rating has been saved.'),
            'alert-type' => 'success',
        ]);
    }

    public function instructorSchedule(Request $request, User $instructor): View
    {
        $user = userAuth();
        if ($user instanceof User) {
            $this->syncUserPlanFromLatestPaidOrder($user);
        }

        $isBanned = in_array(strtolower((string) ($instructor->is_banned ?? '')), ['yes', '1', 'true'], true);

        if (
            $instructor->role !== 'instructor' ||
            $instructor->status !== 'active' ||
            $isBanned
        ) {
            abort(404);
        }

        $start = trim((string) $request->query('start', ''));
        if ($start !== '') {
            try {
                $weekStart = Carbon::parse($start);
            } catch (\Throwable $e) {
                $weekStart = now();
            }
        } else {
            $weekStart = now();
        }
        $weekStart = $weekStart->startOfWeek(Carbon::MONDAY);
        $weekEnd = $weekStart->copy()->endOfWeek(Carbon::SUNDAY);

        $days = [];
        $cursor = $weekStart->copy();
        for ($i = 0; $i < 7; $i++) {
            $days[] = $cursor->copy();
            $cursor->addDay();
        }

        $prevStart = $weekStart->copy()->subWeek()->format('Y-m-d');
        $nextStart = $weekStart->copy()->addWeek()->format('Y-m-d');
        $dayLabels = [__('Mon'), __('Tue'), __('Wed'), __('Thu'), __('Fri'), __('Sat'), __('Sun')];

        $availabilities = InstructorAvailability::query()
            ->where('instructor_id', $instructor->id)
            ->where('is_active', 1)
            ->orderBy('day_of_week')
            ->orderBy('start_time')
            ->get();

        $availabilityByDay = $availabilities->groupBy('day_of_week');

        $hasStatusColumn = Schema::hasTable('student_live_lessons')
            && Schema::hasColumn('student_live_lessons', 'status');

        $lessonsQuery = StudentLiveLesson::query()
            ->where('instructor_id', $instructor->id)
            ->whereBetween('start_time', [$weekStart, $weekEnd])
            ->orderBy('start_time');

        if ($hasStatusColumn) {
            $lessonsQuery->whereNotIn('status', ['cancelled_teacher', 'cancelled_student']);
        }

        $lessons = $lessonsQuery->get();

        $lessonsByDate = $lessons->groupBy(function (StudentLiveLesson $lesson) {
            return $lesson->start_time?->format('Y-m-d') ?: 'unknown';
        });

        $lessonDuration = $this->resolveLessonDuration($user);
        $currentPlan = Schema::hasTable('user_plans') ? UserPlan::query()->currentForUser((int) ($user?->id ?? 0))->first() : null;
        $weeklyLimit = $this->resolveWeeklyLessonLimit($currentPlan);
        $reservedThisWeek = StudentLiveLesson::query()
            ->where('student_id', $user?->id)
            ->whereBetween('start_time', [$weekStart, $weekEnd])
            ->when($hasStatusColumn, function ($query) {
                $query->whereNotIn('status', ['cancelled_teacher', 'cancelled_student']);
            })
            ->count();

        return view('frontend.student-dashboard.instructors.schedule', compact(
            'instructor',
            'weekStart',
            'weekEnd',
            'prevStart',
            'nextStart',
            'days',
            'dayLabels',
            'availabilityByDay',
            'lessonsByDate',
            'lessonDuration',
            'weeklyLimit',
            'reservedThisWeek'
        ));
    }

    public function storeInstructorSchedule(Request $request, User $instructor): RedirectResponse
    {
        $user = userAuth();
        if ($user instanceof User) {
            $this->syncUserPlanFromLatestPaidOrder($user);
        }

        $isBanned = in_array(strtolower((string) ($instructor->is_banned ?? '')), ['yes', '1', 'true'], true);

        if ($instructor->role !== 'instructor' || $instructor->status !== 'active' || $isBanned) {
            return redirect()->route('student.instructors')->with([
                'messege' => __('Instructor not found.'),
                'alert-type' => 'error',
            ]);
        }

        $validated = $request->validate([
            'slots' => ['required', 'array', 'min:1'],
            'slots.*' => ['required', 'string'],
            'reservation_mode' => ['nullable', 'in:temporary,weekly'],
        ]);

        $selectedSlots = collect($validated['slots'] ?? [])->map(fn ($slot) => trim((string) $slot))->filter()->unique()->values();
        if ($selectedSlots->isEmpty()) {
            return redirect()->back()->with([
                'messege' => __('Please select a time slot.'),
                'alert-type' => 'error',
            ]);
        }

        $lessonDuration = $this->resolveLessonDuration($user);
        $currentPlan = Schema::hasTable('user_plans') ? UserPlan::query()->currentForUser((int) ($user?->id ?? 0))->first() : null;

        if (Schema::hasTable('user_plans') && (!$currentPlan || ($currentPlan->lessons_remaining ?? 0) <= 0)) {
            return redirect()->back()->with([
                'messege' => __('You do not have enough credits left. Please purchase a package to continue.'),
                'alert-type' => 'error',
            ]);
        }

        $parsedSlots = [];
        foreach ($selectedSlots as $slotValue) {
            $parts = array_map('trim', explode('|', $slotValue));
            if (count($parts) !== 2 || $parts[0] === '' || $parts[1] === '') {
                return redirect()->back()->with([
                    'messege' => __('Selected slot is invalid.'),
                    'alert-type' => 'error',
                ]);
            }

            try {
                $startTime = Carbon::createFromFormat('Y-m-d H:i', $parts[0] . ' ' . $parts[1]);
            } catch (\Throwable $e) {
                return redirect()->back()->with([
                    'messege' => __('Selected slot is invalid.'),
                    'alert-type' => 'error',
                ]);
            }

            if ($startTime->lt(now()->addHours(24))) {
                return redirect()->back()->with([
                    'messege' => __('Bookings must be made at least 24 hours in advance.'),
                    'alert-type' => 'error',
                ]);
            }

            $parsedSlots[] = [
                'date' => $parts[0],
                'time' => $parts[1],
                'start' => $startTime,
                'end' => $lessonDuration > 0 ? $startTime->copy()->addMinutes($lessonDuration) : null,
            ];
        }

        $firstWeekStart = $parsedSlots[0]['start']->copy()->startOfWeek(Carbon::MONDAY);
        $firstWeekEnd = $parsedSlots[0]['start']->copy()->endOfWeek(Carbon::SUNDAY);
        $weeklyLimit = $this->resolveWeeklyLessonLimit($currentPlan);
        $reservedThisWeek = StudentLiveLesson::query()
            ->where('student_id', $user?->id)
            ->whereBetween('start_time', [$firstWeekStart, $firstWeekEnd])
            ->when(Schema::hasTable('student_live_lessons') && Schema::hasColumn('student_live_lessons', 'status'), function ($query) {
                $query->whereNotIn('status', ['cancelled_teacher', 'cancelled_student']);
            })
            ->count();

        if (($reservedThisWeek + count($parsedSlots)) > $weeklyLimit) {
            return redirect()->back()->with([
                'messege' => __('Weekly reservation limit reached for your package.'),
                'alert-type' => 'error',
            ]);
        }

        $hasStatusColumn = Schema::hasTable('student_live_lessons')
            && Schema::hasColumn('student_live_lessons', 'status');
        $hasEndedAtColumn = Schema::hasTable('student_live_lessons')
            && Schema::hasColumn('student_live_lessons', 'ended_at');

        $recurring = ($validated['reservation_mode'] ?? 'temporary') === 'weekly';
        $slotTargets = [];
        foreach ($parsedSlots as $parsedSlot) {
            $starts = [$parsedSlot['start']];
            if ($recurring && $currentPlan?->ends_at) {
                $cursor = $parsedSlot['start']->copy()->addWeek();
                while ($cursor->lte($currentPlan->ends_at) && count($slotTargets) < (int) ($currentPlan->lessons_remaining ?? 0)) {
                    $starts[] = $cursor->copy();
                    $cursor->addWeek();
                }
            }

            foreach ($starts as $startTime) {
                $dateValue = $startTime->format('Y-m-d');
                $timeValue = $startTime->format('H:i');
                $endedAt = $lessonDuration > 0 ? $startTime->copy()->addMinutes($lessonDuration) : null;
                $dayOfWeek = $startTime->dayOfWeekIso - 1;
                $availability = InstructorAvailability::query()
                    ->where('instructor_id', $instructor->id)
                    ->where('day_of_week', $dayOfWeek)
                    ->where('start_time', $timeValue)
                    ->where('is_active', 1)
                    ->first();

                if (!$availability) {
                    return redirect()->back()->with([
                        'messege' => __('Selected slot is no longer available.'),
                        'alert-type' => 'error',
                    ]);
                }

                if ($lessonDuration > 0) {
                    $availabilityEnd = Carbon::createFromFormat('Y-m-d H:i', $dateValue . ' ' . substr((string) $availability->end_time, 0, 5));
                    if ($endedAt && $availabilityEnd && $endedAt->greaterThan($availabilityEnd)) {
                        return redirect()->back()->with([
                            'messege' => __('Selected slot is no longer available.'),
                            'alert-type' => 'error',
                        ]);
                    }
                }

                $slotTaken = StudentLiveLesson::query()
                    ->where('instructor_id', $instructor->id)
                    ->whereDate('start_time', $dateValue)
                    ->whereTime('start_time', $timeValue)
                    ->when($hasStatusColumn, function ($query) {
                        $query->whereNotIn('status', ['cancelled_teacher', 'cancelled_student']);
                    })
                    ->exists();

                if ($slotTaken) {
                    return redirect()->back()->with([
                        'messege' => __('One of the selected slots is no longer available.'),
                        'alert-type' => 'error',
                    ]);
                }

                $slotTargets[] = [
                    'start' => $startTime,
                    'end' => $endedAt,
                    'date' => $dateValue,
                    'time' => $timeValue,
                    'availability_id' => $availability->id,
                ];
            }
        }

        if (empty($slotTargets)) {
            return redirect()->back()->with([
                'messege' => __('Please select a time slot.'),
                'alert-type' => 'error',
            ]);
        }

        $createdLessons = [];
        foreach ($slotTargets as $slotTarget) {
            $payload = [
                'instructor_id' => $instructor->id,
                'student_id' => $user?->id,
                'title' => __('Private Lesson'),
                'start_time' => $slotTarget['start'],
                'meeting_id' => 'pending-' . Str::uuid()->toString(),
                'password' => null,
                'join_url' => null,
                'type' => 'zoom',
            ];

            if ($hasStatusColumn) {
                $payload['status'] = 'pending';
            }
            if ($hasEndedAtColumn) {
                $payload['ended_at'] = $slotTarget['end'];
            }

            $lesson = DB::transaction(function () use ($payload, $slotTarget, $instructor, $hasStatusColumn) {
                $lockedAvailability = InstructorAvailability::query()
                    ->where('id', $slotTarget['availability_id'])
                    ->lockForUpdate()
                    ->first();

                if (!$lockedAvailability || !$lockedAvailability->is_active) {
                    return null;
                }

                $slotTakenNow = StudentLiveLesson::query()
                    ->where('instructor_id', $instructor->id)
                    ->whereDate('start_time', $slotTarget['date'])
                    ->whereTime('start_time', $slotTarget['time'])
                    ->when($hasStatusColumn, function ($query) {
                        $query->whereNotIn('status', ['cancelled_teacher', 'cancelled_student']);
                    })
                    ->lockForUpdate()
                    ->exists();

                if ($slotTakenNow) {
                    return null;
                }

                return StudentLiveLesson::create($payload);
            });

            if (!$lesson) {
                return redirect()->back()->with([
                    'messege' => __('Selected slot is no longer available.'),
                    'alert-type' => 'error',
                ]);
            }
            $createdLessons[] = $lesson;
        }

        try {
            $meeting = app(ZoomOAuthService::class)->getOrCreateDefaultRecurringMeeting($instructor, __('Private Lesson'));
            $meetingId = (string) ($meeting['id'] ?? '');
            if ($meetingId === '') {
                throw new \RuntimeException('Zoom meeting id missing.');
            }

            foreach ($createdLessons as $lesson) {
                $update = [
                    'meeting_id' => $meetingId,
                    'join_url' => $meeting['join_url'] ?? null,
                    'password' => $meeting['password'] ?? null,
                ];

                if ($hasStatusColumn) {
                    $update['status'] = 'scheduled';
                }

                $lesson->update($update);
            }
        } catch (\Throwable $e) {
            report($e);
            foreach ($createdLessons as $lesson) {
                $lesson->delete();
            }

            return redirect()->back()->with([
                'messege' => str_contains($e->getMessage(), 'not connected')
                    ? __('Zoom account is not connected.')
                    : __('Zoom meeting could not be created. Please try again.'),
                'alert-type' => 'error',
            ]);
        }

        return redirect()->route('student.instructors.schedule', [
            'instructor' => $instructor->id,
            'start' => $parsedSlots[0]['start']->format('Y-m-d'),
        ])->with([
            'messege' => __('Reservation confirmed.'),
            'alert-type' => 'success',
        ]);
    }

    public function plans(Request $request): View
    {
        $user = userAuth();
        if ($user instanceof User) {
            $this->syncUserPlanFromLatestPaidOrder($user);
        }

        $plans = collect();
        if (Schema::hasTable('student_plans')) {
            $plans = DB::table('student_plans')
                ->where('is_active', 1)
                ->orderBy('sort_order')
                ->orderBy('id')
                ->get();
        } else {
            $plans = collect(config('student_plans.plans', []))->values()->map(function ($plan) {
                return (object) $plan;
            });
        }

        $selectedPlanKey = (string) $request->query('plan', '');

        return view('frontend.student-dashboard.plans.index', compact('plans', 'selectedPlanKey'));
    }

    public function requestTrialLesson(Request $request): RedirectResponse
    {
        $user = userAuth();

        if (!$user || !($user instanceof User)) {
            return redirect()->route('login');
        }

        if (!Schema::hasTable('trial_lesson_requests')) {
            return redirect()->back()->with([
                'messege' => __('Trial request table not found. Please run migrations.'),
                'alert-type' => 'error',
            ]);
        }

        // Cowboy-like behavior: trial request is only for users who haven't purchased a plan yet.
        $hasPaidPlan = Order::query()
            ->where('buyer_id', $user->id)
            ->where('order_type', 'student_plan')
            ->where(function ($query) {
                $query->where('payment_status', 'paid')
                    ->orWhere('status', 'completed');
            })
            ->exists();

        if ($hasPaidPlan) {
            return redirect()->back()->with([
                'messege' => __('You already have an active plan.'),
                'alert-type' => 'info',
            ]);
        }

        $alreadyRequested = DB::table('trial_lesson_requests')
            ->where('user_id', $user->id)
            ->whereIn('status', ['pending', 'approved'])
            ->exists();

        if ($alreadyRequested) {
            return redirect()->back()->with([
                'messege' => __('Your trial lesson request has already been received.'),
                'alert-type' => 'info',
            ]);
        }

        DB::table('trial_lesson_requests')->insert([
            'user_id' => $user->id,
            'phone' => ($phone = trim((string) ($user->phone ?? ''))) !== '' ? $phone : null,
            'status' => 'pending',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->back()->with([
            'messege' => __('Your trial lesson request has been received.'),
            'alert-type' => 'success',
        ]);
    }

    function enrolledCourses() {
        $user = userAuth();
        $enrolls = Enrollment::where('user_id', userAuth()->id)
            ->where('course_id', '>', 0)
            ->whereHas('course', function ($q) {
                $q->withTrashed();
            })
            ->with(['course' => function ($q) {
                $q->withTrashed();
            }])
            ->orderByDesc('id')
            ->paginate(10);

        $courseIds = Enrollment::query()
            ->where('user_id', $user->id)
            ->where('has_access', 1)
            ->pluck('course_id')
            ->filter()
            ->unique()
            ->values();

        $courseUpcomingLiveClasses = collect();
        $coursePastLiveClasses = collect();

        if ($courseIds->isNotEmpty()) {
            $baseLiveQuery = CourseLiveClass::query()
                ->whereNotNull('start_time')
                ->whereHas('lesson', function ($query) use ($courseIds) {
                    $query->whereIn('course_id', $courseIds);
                })
                ->with([
                    'lesson:id,course_id,title,duration',
                    'lesson.course:id,slug,title,thumbnail,instructor_id',
                    'lesson.course.instructor:id,name,image',
                ]);

            $courseUpcomingLiveClasses = (clone $baseLiveQuery)
                ->where('start_time', '>=', now())
                ->orderBy('start_time')
                ->get();

            $coursePastLiveClasses = (clone $baseLiveQuery)
                ->where('start_time', '<', now())
                ->orderByDesc('start_time')
                ->get();
        }

        $attendedLessonIds = [];
        if (Schema::hasTable('live_lesson_attendances')) {
            $attendedLessonIds = LiveLessonAttendance::query()
                ->where('user_id', $user->id)
                ->pluck('lesson_id')
                ->toArray();
        }

        $studentUpcomingLiveClasses = collect();
        $studentPastLiveClasses = collect();
        $attendedStudentLessonIds = [];

        if (Schema::hasTable('student_live_lessons')) {
            $studentBaseQuery = StudentLiveLesson::query()
                ->where('student_id', $user->id)
                ->with(['instructor:id,name,image']);

            $studentUpcomingLiveClasses = (clone $studentBaseQuery)
                ->where('start_time', '>=', now())
                ->orderBy('start_time')
                ->get();

            $studentPastLiveClasses = (clone $studentBaseQuery)
                ->where('start_time', '<', now())
                ->orderByDesc('start_time')
                ->get();
        }

        if (Schema::hasTable('student_live_lesson_attendances')) {
            $attendedStudentLessonIds = StudentLiveLessonAttendance::query()
                ->where('student_id', $user->id)
                ->pluck('student_live_lesson_id')
                ->toArray();
        }

        $courseUpcomingItems = $courseUpcomingLiveClasses->map(function ($live) use ($attendedLessonIds) {
            $lesson = $live->lesson;
            $course = $lesson?->course;
            $instructor = $course?->instructor;

            return (object) [
                'kind' => 'course',
                'id' => $live->id,
                'lesson_id' => $lesson?->id,
                'title' => $lesson?->title ?: __('Live Lesson'),
                'course_title' => $course?->title,
                'instructor_name' => $instructor?->first_name,
                'thumbnail' => $course?->thumbnail ?: 'frontend/img/courses/course_thumb01.jpg',
                'start_time' => $live->start_time,
                'type' => $live->type,
                'join_route' => ($course && $lesson) ? route('student.learning.live', [$course->slug, $lesson->id]) : null,
                'attended' => in_array((int) ($lesson?->id ?? 0), $attendedLessonIds, true),
            ];
        })->toBase();

        $coursePastItems = $coursePastLiveClasses->map(function ($live) use ($attendedLessonIds) {
            $lesson = $live->lesson;
            $course = $lesson?->course;
            $instructor = $course?->instructor;

            return (object) [
                'kind' => 'course',
                'id' => $live->id,
                'lesson_id' => $lesson?->id,
                'title' => $lesson?->title ?: __('Live Lesson'),
                'course_title' => $course?->title,
                'instructor_name' => $instructor?->first_name,
                'thumbnail' => $course?->thumbnail ?: 'frontend/img/courses/course_thumb01.jpg',
                'start_time' => $live->start_time,
                'type' => $live->type,
                'join_route' => ($course && $lesson) ? route('student.learning.live', [$course->slug, $lesson->id]) : null,
                'attended' => in_array((int) ($lesson?->id ?? 0), $attendedLessonIds, true),
            ];
        })->toBase();

        $studentUpcomingItems = $studentUpcomingLiveClasses->map(function ($live) use ($attendedStudentLessonIds) {
            $status = $live->status ?: (Str::startsWith((string) $live->meeting_id, 'pending-') ? 'pending' : 'scheduled');
            return (object) [
                'kind' => 'student',
                'id' => $live->id,
                'lesson_id' => $live->id,
                'title' => $live->title ?: __('Private Live Lesson'),
                'course_title' => __('Private Lesson'),
                'instructor_name' => $live->instructor?->first_name,
                'thumbnail' => $live->instructor?->image ?: 'frontend/img/courses/course_thumb01.jpg',
                'start_time' => $live->start_time,
                'type' => $live->type ?: 'zoom',
                'join_route' => route('student.live-lessons.join', $live->id),
                'attended' => in_array((int) $live->id, $attendedStudentLessonIds, true),
                'status' => $status,
                'cancel_route' => route('student.live-lessons.cancel', $live->id),
                'student_rating' => $live->student_rating,
            ];
        })->toBase();

        $studentPastItems = $studentPastLiveClasses->map(function ($live) use ($attendedStudentLessonIds) {
            $status = $live->status ?: (Str::startsWith((string) $live->meeting_id, 'pending-') ? 'pending' : 'scheduled');
            return (object) [
                'kind' => 'student',
                'id' => $live->id,
                'lesson_id' => $live->id,
                'title' => $live->title ?: __('Private Live Lesson'),
                'course_title' => __('Private Lesson'),
                'instructor_name' => $live->instructor?->first_name,
                'thumbnail' => $live->instructor?->image ?: 'frontend/img/courses/course_thumb01.jpg',
                'start_time' => $live->start_time,
                'type' => $live->type ?: 'zoom',
                'join_route' => route('student.live-lessons.join', $live->id),
                'attended' => in_array((int) $live->id, $attendedStudentLessonIds, true),
                'status' => $status,
                'cancel_route' => route('student.live-lessons.cancel', $live->id),
                'student_rating' => $live->student_rating,
            ];
        })->toBase();

        $upcomingLiveClasses = $courseUpcomingItems
            ->merge($studentUpcomingItems)
            ->sortBy('start_time')
            ->values();

        $pastLiveClasses = $coursePastItems
            ->merge($studentPastItems)
            ->sortByDesc('start_time')
            ->values();

        $currentPlan = null;
        if (Schema::hasTable('user_plans')) {
            $currentPlan = UserPlan::query()->currentForUser($user->id)->first();
        }

        return view('frontend.student-dashboard.enrolled-courses.index', compact(
            'enrolls',
            'upcomingLiveClasses',
            'pastLiveClasses',
            'attendedLessonIds',
            'currentPlan'
        ));
    }

    public function cancelLiveLesson(Request $request, StudentLiveLesson $lesson): RedirectResponse
    {
        $user = userAuth();

        if ((int) $lesson->student_id !== (int) ($user?->id ?? 0)) {
            return redirect()->back()->with([
                'messege' => __('You do not have access to this lesson.'),
                'alert-type' => 'error',
            ]);
        }

        if ($lesson->status !== 'scheduled') {
            return redirect()->back()->with([
                'messege' => __('This lesson is already completed or cancelled.'),
                'alert-type' => 'error',
            ]);
        }

        if ($lesson->start_time && $lesson->start_time->isPast()) {
            return redirect()->back()->with([
                'messege' => __('You cannot cancel a lesson that has already started.'),
                'alert-type' => 'error',
            ]);
        }

        if (Schema::hasTable('user_plans')) {
            $error = null;
            DB::transaction(function () use ($user, $lesson, &$error, $request) {
                $plan = UserPlan::query()
                    ->where('user_id', $user->id)
                    ->lockForUpdate()
                    ->first();

                $lesson->status = 'cancelled_student';
                $lesson->cancelled_by = 'student';
                $lesson->cancelled_reason = trim((string) $request->input('reason', '')) ?: null;
                $lesson->cancelled_at = now();
                $lesson->save();

                if ($plan) {
                    if (($plan->cancel_remaining ?? 0) > 0) {
                        $plan->decrement('cancel_remaining');
                    } elseif (($plan->lessons_remaining ?? 0) > 0) {
                        $plan->decrement('lessons_remaining');
                    }
                }
            });
        } else {
            $lesson->status = 'cancelled_student';
            $lesson->cancelled_by = 'student';
            $lesson->cancelled_reason = trim((string) $request->input('reason', '')) ?: null;
            $lesson->cancelled_at = now();
            $lesson->save();
        }

        return redirect()->back()->with([
            'messege' => __('Lesson cancelled.'),
            'alert-type' => 'success',
        ]);
    }

    function quizAttempts() {
        Session::forget('course_slug');
        $quizAttempts = QuizResult::with(['quiz'])->where('user_id', userAuth()->id)->orderByDesc('id')->paginate(10);

        return view('frontend.student-dashboard.quiz-attempts.index', compact('quizAttempts'));
    }

    function downloadCertificate(string $id) {
        $certificate = CertificateBuilder::first();
        $certificateItems = CertificateBuilderItem::all();
        $course = Course::withTrashed()->find($id);

        $courseLectureCount = CourseChapterItem::whereHas('chapter', function ($q) use ($course) {
            $q->where('course_id', $course->id);
        })->count();

        $courseLectureCompletedByUser = CourseProgress::where('user_id', userAuth()->id)
            ->where('course_id', $course->id)->where('watched', 1)->latest();

        $completed_date = formatDate($courseLectureCompletedByUser->first()?->created_at);

        $courseLectureCompletedByUser = CourseProgress::where('user_id', userAuth()->id)
            ->where('course_id', $course->id)->where('watched', 1)->count();

        $courseCompletedPercent = $courseLectureCount > 0 ? ($courseLectureCompletedByUser / $courseLectureCount) * 100 : 0;

        if ($courseCompletedPercent != 100) {
            return abort(404);
        }

        $html = view('frontend.student-dashboard.certificate.index', compact('certificateItems', 'certificate'))->render();

        $html = str_replace('[student_name]', userAuth()->name, $html);
        $html = str_replace('[platform_name]', Cache::get('setting')->app_name, $html);
        $html = str_replace('[course]', $course->title, $html);
        $html = str_replace('[date]', formatDate($completed_date), $html);
        $html = str_replace('[instructor_name]', $course->instructor->first_name ?? $course->instructor->name, $html);

        // Initialize Dompdf
        $dompdf = new Dompdf(array('enable_remote' => true));

        // Load HTML content
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');

        $dompdf->render();
        $dompdf->stream("certificate.pdf");
        return redirect()->back();
    }

    public function purchasePlan(Request $request): RedirectResponse
    {
        $planKey = (string) $request->input('plan_key');
        if ($planKey === '') {
            return redirect()->back()->with([
                'messege' => __('Plan not found. Please try again.'),
                'alert-type' => 'error',
            ]);
        }

        $plan = null;
        if (Schema::hasTable('student_plans')) {
            $plan = DB::table('student_plans')
                ->where('key', $planKey)
                ->where('is_active', 1)
                ->first();
        } else {
            $plansConfig = (array) config('student_plans.plans', []);
            $plan = isset($plansConfig[$planKey]) ? (object) $plansConfig[$planKey] : null;
        }

        if (!$plan) {
            return redirect()->back()->with([
                'messege' => __('Plan not found. Please make sure the package is active and the plan key is correct in the admin panel.'),
                'alert-type' => 'error',
            ]);
        }

        $paymentMethod = 'iyzico';
        $paymentService = app(PaymentMethodService::class);

        if (!$paymentService->isActive($paymentMethod)) {
            return redirect()->back()->with([
                'messege' => __('The selected payment method is now inactive.'),
                'alert-type' => 'error',
            ]);
        }

        $currencyCode = (string) (config('student_plans.currency') ?: getSessionCurrency());
        if (!$paymentService->isCurrencySupported($paymentMethod, $currencyCode)) {
            return redirect()->back()->with([
                'messege' => __('You are trying to use unsupported currency'),
                'alert-type' => 'error',
            ]);
        }

        $payableAmount = (float) ($plan->price ?? 0);
        if ($payableAmount <= 0) {
            return redirect()->back()->with([
                'messege' => __('This package does not have a valid price. Please fill the package price field in the admin panel.'),
                'alert-type' => 'error',
            ]);
        }

        $payableCharge = $paymentService->getPayableAmount($paymentMethod, $payableAmount, $currencyCode);
        $paidAmount = $payableCharge?->payable_amount + $payableCharge?->gateway_charge;

        $user = userAuth();

        DB::beginTransaction();
        try {
            $order = Order::create([
                'invoice_id' => Str::random(10),
                'buyer_id' => $user->id,
                'has_coupon' => 0,
                'payment_method' => $paymentMethod,
                'payment_status' => 'pending',
                'status' => 'pending',
                'payable_amount' => $payableAmount,
                'gateway_charge' => $payableCharge?->gateway_charge,
                'payable_with_charge' => $payableCharge?->payable_with_charge,
                'paid_amount' => $paidAmount,
                'payable_currency' => $currencyCode,
                'conversion_rate' => $payableCharge?->currency_rate ?? 1,
                'commission_rate' => Cache::get('setting')->commission_rate,
                'order_type' => 'student_plan',
                'order_details' => [
                    'plan_key' => (string) ($plan->key ?? $planKey),
                    'title' => (string) ($plan->title ?? $planKey),
                    'duration_months' => (int) ($plan->duration_months ?? 0),
                    'lesson_duration' => (int) ($plan->lesson_duration ?? config('student_plans.default_lesson_duration', 40)),
                    'lessons_total' => (int) ($plan->lessons_total ?? 0),
                    'cancel_total' => (int) ($plan->cancel_total ?? 0),
                    'price' => (float) ($plan->price ?? 0),
                    'currency' => $currencyCode,
                ],
            ]);

            OrderItem::create([
                'order_id' => $order->id,
                'price' => $payableAmount,
                'course_id' => 0,
                'commission_rate' => 0,
            ]);

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            report($e);

            return redirect()->back()->with([
                'messege' => __('Payment failed, please try again'),
                'alert-type' => 'error',
            ]);
        }

        return redirect()->route('payment', ['invoice_id' => $order->invoice_id]);
    }

    private function buildUpcomingLessonPreview(User $user, int $limit = 4)
    {
        $courseIds = Enrollment::query()
            ->where('user_id', $user->id)
            ->where('has_access', 1)
            ->pluck('course_id')
            ->filter()
            ->unique()
            ->values();

        $courseUpcomingItems = collect();
        if ($courseIds->isNotEmpty()) {
            $courseUpcomingItems = CourseLiveClass::query()
                ->whereNotNull('start_time')
                ->where('start_time', '>=', now())
                ->whereHas('lesson', function ($query) use ($courseIds) {
                    $query->whereIn('course_id', $courseIds);
                })
                ->with([
                    'lesson:id,course_id,title,duration',
                    'lesson.course:id,slug,title,thumbnail,instructor_id',
                    'lesson.course.instructor:id,name,image',
                ])
                ->orderBy('start_time')
                ->get()
                ->map(function (CourseLiveClass $live) {
                    $lesson = $live->lesson;
                    $course = $lesson?->course;
                    $instructor = $course?->instructor;

                    return (object) [
                        'kind' => 'course',
                        'title' => $lesson?->title ?: __('Live Lesson'),
                        'course_title' => $course?->title ?: __('Course Lesson'),
                        'instructor_name' => $instructor?->first_name ?: '-',
                        'start_time' => $live->start_time,
                        'join_route' => ($course && $lesson) ? route('student.learning.live', [$course->slug, $lesson->id]) : null,
                    ];
                });
        }

        $studentUpcomingItems = collect();
        if (Schema::hasTable('student_live_lessons')) {
            $studentQuery = StudentLiveLesson::query()
                ->where('student_id', $user->id)
                ->where('start_time', '>=', now())
                ->with(['instructor:id,name,image'])
                ->orderBy('start_time');

            if (Schema::hasColumn('student_live_lessons', 'status')) {
                $studentQuery->whereNotIn('status', ['cancelled_teacher', 'cancelled_student']);
            }

            $studentUpcomingItems = $studentQuery
                ->get()
                ->map(function (StudentLiveLesson $lesson) {
                    return (object) [
                        'kind' => 'student',
                        'title' => $lesson->title ?: __('Private Live Lesson'),
                        'course_title' => __('Private Lesson'),
                        'instructor_name' => $lesson->instructor?->first_name ?: '-',
                        'start_time' => $lesson->start_time,
                        'join_route' => route('student.live-lessons.join', $lesson->id),
                    ];
                });
        }

        return $courseUpcomingItems
            ->merge($studentUpcomingItems)
            ->sortBy('start_time')
            ->take($limit)
            ->values();
    }

    private function syncUserPlanFromLatestPaidOrder(User $user): void
    {
        if (!Schema::hasTable('user_plans')) {
            return;
        }

        $latestPlanOrder = Order::query()
            ->where('buyer_id', $user->id)
            ->where('order_type', 'student_plan')
            ->where(function ($query) {
                $query->where('payment_status', 'paid')
                    ->orWhere('status', 'completed');
            })
            ->orderByDesc('id')
            ->first();

        if (!$latestPlanOrder) {
            return;
        }

        $existingPlan = DB::table('user_plans')
            ->where('user_id', $user->id)
            ->orderByDesc('last_order_id')
            ->orderByDesc('id')
            ->first();
        $existingLastOrderId = (int) ($existingPlan?->last_order_id ?? 0);

        if (
            $existingPlan &&
            $existingLastOrderId >= (int) $latestPlanOrder->id &&
            trim((string) ($existingPlan->plan_title ?? '')) !== ''
        ) {
            return;
        }

        $details = $latestPlanOrder->orderDetails;
        $now = now();

        $planKey = trim((string) ($details?->plan_key ?? $details?->key ?? ''));
        if ($planKey === '') {
            $planKey = 'order_'.$latestPlanOrder->id;
        }

        $planTitle = trim((string) ($details?->title ?? $details?->plan_title ?? ''));
        if ($planTitle === '') {
            $planTitle = 'Plan';
        }

        $durationMonths = (int) ($details?->duration_months ?? 0);
        $lessonDuration = (int) ($details?->lesson_duration ?? config('student_plans.default_lesson_duration', 40));
        $lessonsTotal = max(0, (int) ($details?->lessons_total ?? 0));
        $cancelTotal = max(0, (int) ($details?->cancel_total ?? 0));
        $hasLessonDurationColumn = Schema::hasColumn('user_plans', 'lesson_duration');

        $payload = [
            'plan_key' => $planKey,
            'plan_title' => $planTitle,
            'lessons_total' => $lessonsTotal,
            'lessons_remaining' => $lessonsTotal,
            'cancel_total' => $cancelTotal,
            'cancel_remaining' => $cancelTotal,
            'starts_at' => $now,
            'ends_at' => $durationMonths > 0 ? $now->copy()->addMonths($durationMonths) : null,
            'last_order_id' => $latestPlanOrder->id,
        ];

        if ($hasLessonDurationColumn) {
            $payload['lesson_duration'] = max(1, $lessonDuration);
        }

        if ($existingPlan) {
            DB::table('user_plans')
                ->where('user_id', $user->id)
                ->update(array_merge($payload, ['updated_at' => now()]));
            return;
        }

        DB::table('user_plans')->insert(array_merge(['user_id' => $user->id], $payload, [
            'created_at' => now(),
            'updated_at' => now(),
        ]));
    }

    private function resolveLessonDuration(?User $user): int
    {
        $defaultDuration = (int) config('student_plans.default_lesson_duration', 40);
        $defaultDuration = max(1, $defaultDuration);

        if (!$user) {
            return $defaultDuration;
        }

        if (Schema::hasTable('user_plans') && Schema::hasColumn('user_plans', 'lesson_duration')) {
            $plan = UserPlan::query()->currentForUser($user->id)->first();
            if ($plan && (int) $plan->lesson_duration > 0) {
                return (int) $plan->lesson_duration;
            }
        }

        return $defaultDuration;
    }

    private function resolveWeeklyLessonLimit(?UserPlan $plan): int
    {
        if (!$plan) {
            return 2;
        }

        $durationMonths = 0;
        if (Schema::hasTable('student_plans')) {
            $durationMonths = (int) DB::table('student_plans')
                ->where('key', $plan->plan_key)
                ->value('duration_months');
        }

        if ($durationMonths <= 0) {
            $configPlan = (array) data_get(config('student_plans.plans'), $plan->plan_key, []);
            $durationMonths = (int) ($configPlan['duration_months'] ?? 0);
        }

        $weeks = max(1, $durationMonths * 4);
        $lessonsTotal = max(1, (int) ($plan->lessons_total ?? 0));

        return max(1, (int) ceil($lessonsTotal / $weeks));
    }
}
