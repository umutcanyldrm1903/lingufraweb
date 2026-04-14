<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseReview;
use App\Models\InstructorAvailability;
use App\Models\StudentLiveLesson;
use App\Models\User;
use App\Models\UserPlan;
use App\Services\Zoom\ZoomOAuthService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Modules\Order\app\Models\Order;

class InstructorController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $search = trim((string) $request->query('search', ''));
        $rawTags = $request->query('tag', $request->query('tags', []));

        if (is_string($rawTags)) {
            $rawTags = str_contains($rawTags, ',') ? explode(',', $rawTags) : [$rawTags];
        }

        $tags = is_array($rawTags) ? $rawTags : [];
        $tags = array_values(array_filter(array_map('trim', $tags), function ($value) {
            return $value !== '';
        }));

        $tagMap = [
            // Stable keys
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

            // Legacy values
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
        $unknownTags = [];
        foreach ($tags as $tag) {
            $lookup = Str::lower((string) $tag);
            if (isset($tagMap[$lookup])) {
                $filterKeys[] = $tagMap[$lookup];
            } elseif (isset($tagMap[$tag])) {
                $filterKeys[] = $tagMap[$tag];
            } else {
                $unknownTags[] = $tag;
            }
        }
        $filterKeys = array_values(array_unique($filterKeys));

        $nationalityFilters = array_values(array_intersect($filterKeys, ['nationality_turkish', 'nationality_foreign']));
        $speaksFilters = array_values(array_intersect($filterKeys, ['speaks_turkish_yes', 'speaks_turkish_no']));
        $categoryFilters = array_values(array_intersect($filterKeys, ['category_general', 'category_speaking', 'category_kids', 'category_exam', 'category_business']));
        $availabilityFilters = array_values(array_intersect($filterKeys, ['availability_morning', 'availability_afternoon', 'availability_evening']));

        $courseCountSub = Course::query()
            ->selectRaw('count(*)')
            ->whereColumn('instructor_id', 'users.id')
            ->where(['status' => 'active', 'is_approved' => 'approved']);

        $ratingSub = CourseReview::query()
            ->selectRaw('coalesce(avg(course_reviews.rating),0)')
            ->join('courses', 'course_reviews.course_id', '=', 'courses.id')
            ->whereColumn('courses.instructor_id', 'users.id')
            ->where('course_reviews.status', 1)
            ->where(['courses.status' => 'active', 'courses.is_approved' => 'approved']);

        $query = User::query()
            ->select('users.id', 'users.name', 'users.image', 'users.job_title', 'users.short_bio', 'users.bio')
            ->addSelect([
                'course_count' => $courseCountSub,
                'avg_rating' => $ratingSub,
            ])
            ->where('status', 'active')
            ->where('role', 'instructor')
            ->where(function ($q) {
                // `users.is_banned` is string ('yes'/'no') in this project, but some installs may use 0/1.
                $q->where('is_banned', 'no')
                    ->orWhereNull('is_banned')
                    ->orWhere('is_banned', '0');
            });

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('job_title', 'like', '%' . $search . '%')
                    ->orWhere('short_bio', 'like', '%' . $search . '%')
                    ->orWhere('bio', 'like', '%' . $search . '%');
            });
        }

        if (!empty($unknownTags)) {
            $query->where(function ($q) use ($unknownTags) {
                foreach ($unknownTags as $tag) {
                    $q->orWhere('name', 'like', '%' . $tag . '%')
                        ->orWhere('job_title', 'like', '%' . $tag . '%')
                        ->orWhere('short_bio', 'like', '%' . $tag . '%')
                        ->orWhere('bio', 'like', '%' . $tag . '%');
                }
            });
        }

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

            $query->where(function ($builder) use ($nationalityFilters, $hasCountriesTable, $turkeyCondition) {
                foreach ($nationalityFilters as $filter) {
                    if ($filter === 'nationality_turkish') {
                        if ($hasCountriesTable) {
                            $builder->orWhereHas('country', $turkeyCondition);
                        } else {
                            $builder->orWhereNotNull('id');
                        }
                    }

                    if ($filter === 'nationality_foreign') {
                        if ($hasCountriesTable) {
                            $builder->orWhere(function ($foreignQuery) use ($turkeyCondition) {
                                $foreignQuery->whereNull('country_id')
                                    ->orWhereDoesntHave('country', $turkeyCondition);
                            });
                        } else {
                            $builder->orWhereNotNull('id');
                        }
                    }
                }
            });
        }

        if (!empty($speaksFilters) && Schema::hasColumn('users', 'instructor_profile')) {
            $query->where(function ($builder) use ($speaksFilters) {
                foreach ($speaksFilters as $filter) {
                    if ($filter === 'speaks_turkish_yes') {
                        $builder->orWhere(function ($yesQuery) {
                            $yesQuery->where('instructor_profile', 'like', '%"turkish_level":"beginner"%')
                                ->orWhere('instructor_profile', 'like', '%"turkish_level":"intermediate"%')
                                ->orWhere('instructor_profile', 'like', '%"turkish_level":"advanced"%')
                                ->orWhere('instructor_profile', 'like', '%"turkish_level":"native"%');
                        });
                    }

                    if ($filter === 'speaks_turkish_no') {
                        $builder->orWhere(function ($noQuery) {
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

            $query->where(function ($builder) use ($categoryFilters, $categoryPatterns) {
                foreach ($categoryFilters as $filter) {
                    $patterns = $categoryPatterns[$filter] ?? [];
                    if (empty($patterns)) {
                        continue;
                    }
                    $builder->orWhere(function ($categoryQuery) use ($patterns) {
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

            $query->where(function ($builder) use ($availabilityFilters, $availabilityRanges) {
                foreach ($availabilityFilters as $filter) {
                    [$start, $end] = $availabilityRanges[$filter] ?? [null, null];
                    if (!$start || !$end) {
                        continue;
                    }

                    $builder->orWhereExists(function ($subQuery) use ($start, $end) {
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

        $perPage = $request->filled('limit') && is_numeric($request->limit)
            ? (int) $request->limit
            : 18;

        $instructors = $query->orderByDesc('course_count')->paginate($perPage);

        $data = $instructors->getCollection()->map(function (User $instructor) {
            return $this->normalizeInstructor($instructor);
        })->values();

        return response()->json([
            'status' => 'success',
            'data' => $data,
            'pagination' => [
                'current_page' => $instructors->currentPage(),
                'per_page' => $instructors->perPage(),
                'total' => $instructors->total(),
                'last_page' => $instructors->lastPage(),
                'links' => [
                    'first' => $instructors->url(1),
                    'prev' => $instructors->previousPageUrl(),
                    'next' => $instructors->nextPageUrl(),
                    'last' => $instructors->url($instructors->lastPage()),
                ],
            ],
        ], 200);
    }

    public function show(User $instructor): JsonResponse
    {
        $isBanned = in_array(strtolower((string) ($instructor->is_banned ?? '')), ['yes', '1', 'true'], true);

        if ($instructor->role !== 'instructor' || $instructor->status !== 'active' || $isBanned) {
            return response()->json(['status' => 'error', 'message' => 'Instructor not found.'], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $this->normalizeInstructor($instructor),
        ], 200);
    }

    public function schedule(Request $request, User $instructor): JsonResponse
    {
        // Route is public, but we still want to detect the user when a valid
        // Sanctum token is provided (to resolve plan-based lesson duration).
        $user = $request->user('sanctum');
        if ($user instanceof User) {
            $this->syncUserPlanFromLatestPaidOrder($user);
        }

        $isBanned = in_array(strtolower((string) ($instructor->is_banned ?? '')), ['yes', '1', 'true'], true);

        if ($instructor->role !== 'instructor' || $instructor->status !== 'active' || $isBanned) {
            return response()->json(['status' => 'error', 'message' => 'Instructor not found.'], 404);
        }

        $start = trim((string) $request->query('start', ''));
        $weekStart = $start !== '' ? Carbon::parse($start) : now();
        $weekStart = $weekStart->startOfWeek(Carbon::MONDAY);
        $weekEnd = $weekStart->copy()->endOfWeek(Carbon::SUNDAY);

        $days = [];
        $cursor = $weekStart->copy();
        for ($i = 0; $i < 7; $i++) {
            $days[] = [
                'date' => $cursor->format('Y-m-d'),
                'day_of_week' => $cursor->dayOfWeekIso - 1,
            ];
            $cursor->addDay();
        }

        $prevStart = $weekStart->copy()->subWeek()->format('Y-m-d');
        $nextStart = $weekStart->copy()->addWeek()->format('Y-m-d');

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

        $lessons = $lessonsQuery->get(['start_time']);
        $bookedTimesByDate = [];
        foreach ($lessons as $lesson) {
            $dateKey = $lesson->start_time?->format('Y-m-d');
            $timeKey = $lesson->start_time?->format('H:i');
            if ($dateKey && $timeKey) {
                $bookedTimesByDate[$dateKey][] = $timeKey;
            }
        }

        $lessonDuration = $this->resolveLessonDuration($user);
        $timezone = config('app.timezone') ?: 'Europe/Istanbul';
        $now = now($timezone);

        $slots = [];
        foreach ($days as $day) {
            $date = $day['date'];
            $dayOfWeek = $day['day_of_week'];
            $slotsForDay = [];

            foreach ($availabilityByDay[$dayOfWeek] ?? [] as $availability) {
                $startTime = substr((string) $availability->start_time, 0, 5);
                $endTime = substr((string) $availability->end_time, 0, 5);

                $slotStart = Carbon::createFromFormat('Y-m-d H:i', $date . ' ' . $startTime, $timezone);
                $availabilityEnd = Carbon::createFromFormat('Y-m-d H:i', $date . ' ' . $endTime, $timezone);
                $slotEnd = $lessonDuration > 0 ? $slotStart->copy()->addMinutes($lessonDuration) : $availabilityEnd;

                $isPast = $slotStart->lessThan($now);
                $isTaken = in_array($startTime, $bookedTimesByDate[$date] ?? [], true);
                $fitsDuration = $slotEnd->lessThanOrEqualTo($availabilityEnd);

                $available = !$isPast && !$isTaken && $fitsDuration;

                $slotsForDay[] = [
                    'start_time' => $startTime,
                    'end_time' => $endTime,
                    'label' => $startTime . ' - ' . $endTime,
                    'available' => $available,
                    'value' => $date . '|' . $startTime,
                ];
            }

            $slots[$date] = $slotsForDay;
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'instructor' => $this->normalizeInstructor($instructor),
                'week_start' => $weekStart->format('Y-m-d'),
                'week_end' => $weekEnd->format('Y-m-d'),
                'prev_start' => $prevStart,
                'next_start' => $nextStart,
                'lesson_duration' => $lessonDuration,
                'timezone' => $timezone,
                'days' => $days,
                'slots' => $slots,
            ],
        ], 200);
    }

    public function book(Request $request, User $instructor): JsonResponse
    {
        $user = $request->user('sanctum');
        if ($user instanceof User) {
            $this->syncUserPlanFromLatestPaidOrder($user);
        }

        $isBanned = in_array(strtolower((string) ($instructor->is_banned ?? '')), ['yes', '1', 'true'], true);

        if ($instructor->role !== 'instructor' || $instructor->status !== 'active' || $isBanned) {
            return response()->json(['status' => 'error', 'message' => 'Instructor not found.'], 404);
        }

        $request->validate([
            'slot' => ['required', 'string'],
        ]);

        $parts = array_map('trim', explode('|', (string) $request->input('slot')));
        if (count($parts) !== 2 || $parts[0] === '' || $parts[1] === '') {
            return response()->json(['status' => 'error', 'message' => 'Please select a time slot.'], 422);
        }

        $dateValue = $parts[0];
        $timeValue = $parts[1];

        try {
            $startTime = Carbon::createFromFormat('Y-m-d H:i', $dateValue . ' ' . $timeValue);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'message' => 'Selected slot is invalid.'], 422);
        }

        if ($startTime->isPast()) {
            return response()->json(['status' => 'error', 'message' => 'Selected slot is no longer available.'], 422);
        }

        $lessonDuration = $this->resolveLessonDuration($user);
        $endedAt = $lessonDuration > 0 ? $startTime->copy()->addMinutes($lessonDuration) : null;

        if (Schema::hasTable('user_plans')) {
            $plan = UserPlan::query()->currentForUser((int) ($user?->id ?? 0))->first();
            if (!$plan || ($plan->lessons_remaining ?? 0) <= 0) {
                return response()->json([
                    'status' => 'error',
                    'error_code' => 'no_credits',
                    'message' => 'No credits remaining. Please purchase a package to book lessons.',
                ], 422);
            }
        }

        $dayOfWeek = $startTime->dayOfWeekIso - 1;
        $availability = InstructorAvailability::query()
            ->where('instructor_id', $instructor->id)
            ->where('day_of_week', $dayOfWeek)
            ->where('start_time', $timeValue)
            ->where('is_active', 1)
            ->first();

        if (!$availability) {
            return response()->json(['status' => 'error', 'message' => 'Selected slot is no longer available.'], 422);
        }

        if ($lessonDuration > 0) {
            $availabilityEnd = Carbon::createFromFormat('Y-m-d H:i', $dateValue . ' ' . substr((string) $availability->end_time, 0, 5));
            if ($endedAt && $availabilityEnd && $endedAt->greaterThan($availabilityEnd)) {
                return response()->json(['status' => 'error', 'message' => 'Selected slot is no longer available.'], 422);
            }
        }

        $hasStatusColumn = Schema::hasTable('student_live_lessons')
            && Schema::hasColumn('student_live_lessons', 'status');
        $hasEndedAtColumn = Schema::hasTable('student_live_lessons')
            && Schema::hasColumn('student_live_lessons', 'ended_at');

        $slotTaken = StudentLiveLesson::query()
            ->where('instructor_id', $instructor->id)
            ->whereDate('start_time', $dateValue)
            ->whereTime('start_time', $timeValue)
            ->when($hasStatusColumn, function ($query) {
                $query->whereNotIn('status', ['cancelled_teacher', 'cancelled_student']);
            })
            ->exists();

        if ($slotTaken) {
            return response()->json(['status' => 'error', 'message' => 'Selected slot is no longer available.'], 422);
        }

        $payload = [
            'instructor_id' => $instructor->id,
            'student_id' => $user?->id,
            'title' => __('Private Lesson'),
            'start_time' => $startTime,
            'meeting_id' => 'pending-' . Str::uuid()->toString(),
            'password' => null,
            'join_url' => null,
            'type' => 'zoom',
        ];

        if ($hasStatusColumn) {
            $payload['status'] = 'pending';
        }
        if ($hasEndedAtColumn) {
            $payload['ended_at'] = $endedAt;
        }

        $lesson = null;
        DB::transaction(function () use ($payload, &$lesson) {
            $lesson = StudentLiveLesson::create($payload);
        });

        try {
            $meeting = app(ZoomOAuthService::class)->getOrCreateDefaultRecurringMeeting($instructor, $payload['title']);

            $meetingId = (string) ($meeting['id'] ?? '');
            if ($meetingId === '') {
                throw new \RuntimeException('Zoom meeting id missing.');
            }

            $update = [
                'meeting_id' => $meetingId,
                'join_url' => $meeting['join_url'] ?? null,
                'password' => $meeting['password'] ?? null,
            ];

            if ($hasStatusColumn) {
                $update['status'] = 'scheduled';
            }

            $lesson->update($update);
        } catch (\Throwable $e) {
            report($e);
            if ($lesson) {
                $lesson->delete();
            }

            $errorMessage = str_contains($e->getMessage(), 'not connected')
                ? 'Zoom account is not connected.'
                : 'Zoom meeting could not be created. Please try again.';

            return response()->json([
                'status' => 'error',
                'error_code' => str_contains($e->getMessage(), 'not connected')
                    ? 'zoom_not_connected'
                    : 'zoom_meeting_create_failed',
                'message' => $errorMessage,
            ], 422);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Reservation request sent.',
            'data' => [
                'lesson_id' => $lesson->id,
                'start_time' => $lesson->start_time?->toDateTimeString(),
                'join_url' => $lesson->join_url,
                'meeting_id' => $lesson->meeting_id,
                'status' => $lesson->status ?? 'scheduled',
            ],
        ], 200);
    }

    private function normalizeInstructor(User $instructor): array
    {
        $teachKeys = (array) data_get($instructor->instructor_profile, 'can_teach', []);
        $teachMap = [
            'speaking_b1' => 'Speaking Lessons',
            'general_english_a1' => 'General English',
            'kids_6_12' => 'For Kids',
            'young_13_18' => 'For Kids',
            'business_english' => 'Business English',
            'exams' => 'IELTS & TOEFL',
        ];
        $tags = collect($teachKeys)
            ->map(function ($key) use ($teachMap) {
                return $teachMap[$key] ?? null;
            })
            ->filter()
            ->unique()
            ->values()
            ->all();

        return [
            'id' => $instructor->id,
            'name' => (string) $instructor->first_name,
            'image' => $instructor->image ? asset($instructor->image) : null,
            'job_title' => (string) ($instructor->job_title ?? ''),
            'short_bio' => (string) ($instructor->short_bio ?? ''),
            'bio' => (string) ($instructor->bio ?? ''),
            'course_count' => (int) ($instructor->course_count ?? 0),
            'avg_rating' => round((float) ($instructor->avg_rating ?? 0), 1),
            'tags' => $tags,
        ];
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
}
