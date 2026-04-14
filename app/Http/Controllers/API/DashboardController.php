<?php

namespace App\Http\Controllers\API;

use App\Enums\UserStatus;
use App\Http\Controllers\Controller;
use App\Http\Resources\API\AnnouncementResource;
use App\Http\Resources\API\CourseDetailsCollection;
use App\Http\Resources\API\CourseListResource;
use App\Http\Resources\API\EnrolledCourseResource;
use App\Http\Resources\API\LessonResource;
use App\Http\Resources\API\LiveResource;
use App\Http\Resources\API\OrderDetailsResource;
use App\Http\Resources\API\OrderResource;
use App\Http\Resources\API\QnaReplyResource;
use App\Http\Resources\API\QnaResource;
use App\Http\Resources\API\QuizAttemptsResource;
use App\Http\Resources\API\QuizResource;
use App\Http\Resources\API\ReviewsResource;
use App\Http\Resources\API\UserResource;
use App\Models\Announcement;
use App\Models\Course;
use App\Models\CourseChapterItem;
use App\Models\CourseChapterLesson;
use App\Models\CourseProgress;
use App\Models\CourseReview;
use App\Models\LessonQuestion;
use App\Models\LessonReply;
use App\Models\Quiz;
use App\Models\QuizQuestion;
use App\Models\QuizResult;
use App\Models\StudentLiveLesson;
use App\Models\StudentLiveLessonAttendance;
use App\Models\StudentHomework;
use App\Models\StudentHomeworkSubmission;
use App\Models\StudentLibraryItem;
use App\Models\UserEducation;
use App\Models\UserExperience;
use App\Models\User;
use App\Models\Message;
use App\Models\MobileNotificationRead;
use Carbon\Carbon;
use Dompdf\Dompdf;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Modules\CertificateBuilder\app\Models\CertificateBuilder;
use Modules\CertificateBuilder\app\Models\CertificateBuilderItem;
use Modules\ContactMessage\app\Jobs\ContactMessageSendJob;
use Modules\ContactMessage\app\Models\ContactMessage;
use Modules\Order\app\Models\Enrollment;
use Modules\Order\app\Models\Order;

class DashboardController extends Controller {
    public function enrolled_courses(Request $request): JsonResponse {
        $user_id = auth()->user()->id;
        $limit = $request->filled('limit') && is_numeric($request->limit) ? (int) $request->limit : 6;

        $enrolls = Enrollment::select('course_id')->where('user_id', $user_id)->with([
            'course' => function ($q) {
                $q->select('id', 'instructor_id', 'title', 'slug', 'thumbnail')->with('instructor:id,name,image')->withTrashed()->withCount('enrollments');
            },
        ])->orderByDesc('id')->paginate(perPage: $limit);

        //course percentage calculate
        $enrolls->getCollection()->transform(function ($enroll) use ($user_id) {
            $course = $enroll->course;
            $courseCompletedPercent = CourseChapterItem::whereHas('chapter', fn($q) => $q->where('course_id', $course->id))->count() > 0 ? CourseProgress::where('user_id', $user_id)->where('course_id', $course->id)->where('watched', 1)->count() / CourseChapterItem::whereHas('chapter', fn($q) => $q->where('course_id', $course->id))->count() * 100 : 0;

            $course->completed_percent = $courseCompletedPercent;
            return $enroll;
        });

        if ($enrolls->isNotEmpty()) {
            $data = EnrolledCourseResource::collection($enrolls);
            return response()->json(['status' => 'success',
                'data'                            => $data,
                'pagination'                      => [
                    'current_page' => $enrolls->currentPage(),
                    'per_page'     => $enrolls->perPage(),
                    'total'        => $enrolls->total(),
                    'last_page'    => $enrolls->lastPage(),
                    'links'        => [
                        'first' => $enrolls->url(1),
                        'prev'  => $enrolls->previousPageUrl(),
                        'next'  => $enrolls->nextPageUrl(),
                        'last'  => $enrolls->url($enrolls->lastPage()),
                    ],
                ],

            ], 200);
        }
        return response()->json(['status' => 'error', 'message' => 'Not Found!'], 404);
    }

    public function live_lessons(): JsonResponse
    {
        $user = auth()->user();
        $defaultDurationMinutes = (int) config('student_plans.default_lesson_duration', 40);
        $now = now();

        $studentLiveLessons = collect();
        $attendedStudentLessonIds = [];

        if (Schema::hasTable('student_live_lessons')) {
            $studentLiveLessons = StudentLiveLesson::query()
                ->where('student_id', $user->id)
                ->with(['instructor:id,name,image'])
                ->orderBy('start_time')
                ->get();
        }

        if (Schema::hasTable('student_live_lesson_attendances')) {
            $attendedStudentLessonIds = StudentLiveLessonAttendance::query()
                ->where('student_id', $user->id)
                ->pluck('student_live_lesson_id')
                ->toArray();
        }

        $studentUpcomingItems = collect();
        $studentPastItems = collect();

        foreach ($studentLiveLessons as $live) {
            $startTime = $live->start_time;
            $endTime = $startTime ? $startTime->copy()->addMinutes($defaultDurationMinutes) : null;

            $status = (string) ($live->status ?? '');
            if ($status === '') {
                $status = Str::startsWith((string) $live->meeting_id, 'pending-') ? 'pending' : 'scheduled';
            }

            $startWindow = $startTime ? $startTime->copy()->subMinutes(15) : null;
            $timeOk = $startWindow && $endTime ? $now->between($startWindow, $endTime) : false;

            $meetingId = (string) ($live->meeting_id ?? '');
            $canJoin = $status === 'started'
                && $timeOk
                && !empty($live->join_url)
                && $meetingId !== ''
                && !Str::startsWith($meetingId, 'pending-');

            $item = [
                'kind' => 'student',
                'id' => (int) $live->id,
                'lesson_id' => (int) $live->id,
                'title' => $live->title ?: 'Private Live Lesson',
                'course_title' => 'Private Lesson',
                'course_slug' => null,
                'instructor_name' => $live->instructor?->first_name,
                'thumbnail' => $live->instructor?->image,
                'start_time' => optional($startTime)->toIso8601String(),
                'end_time' => optional($endTime)->toIso8601String(),
                'duration_minutes' => $defaultDurationMinutes,
                'type' => $live->type ?: 'zoom',
                'meeting_id' => $canJoin ? $meetingId : null,
                'password' => $canJoin ? $live->password : null,
                'join_url' => $canJoin ? $live->join_url : null,
                'can_join' => $canJoin,
                'attended' => in_array((int) $live->id, $attendedStudentLessonIds, true),
                'status' => $status,
            ];

            $isUpcoming = false;
            if ($endTime) {
                $isUpcoming = $endTime->gt($now);
            } elseif ($startTime) {
                $isUpcoming = $startTime->greaterThanOrEqualTo($now);
            }

            if ($isUpcoming) {
                $studentUpcomingItems->push($item);
            } else {
                $studentPastItems->push($item);
            }
        }

        $upcomingLiveClasses = $studentUpcomingItems
            ->sortBy('start_time')
            ->values();

        $pastLiveClasses = $studentPastItems
            ->sortByDesc('start_time')
            ->values();

        return response()->json([
            'status' => 'success',
            'data' => [
                'upcoming' => $upcomingLiveClasses,
                'past' => $pastLiveClasses,
            ],
        ], 200);
    }
    public function wishlist_courses(Request $request): JsonResponse {
        $limit = $request->filled('limit') && is_numeric($request->limit) ? (int) $request->limit : 6;

        $courses = auth()->user()->favoriteCourses()
            ->whereHas('category.parentCategory', fn($q) => $q->where('status', 1))
            ->whereHas('category', fn($q) => $q->where('status', 1))
            ->withCount(['reviews as average_rating' => function ($q) {
                $q->select(DB::raw('coalesce(avg(rating), 0) as average_rating'))->where('status', 1);
            }, 'enrollments'])->paginate($limit);

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
    public function add_remove_wishlist(Course $course): JsonResponse {
        if (!$course) {
            return response()->json(['status' => 'error', 'message' => 'Not Found!'], 404);
        }
        $favorite = auth()->user()->favoriteCourses();
        $favorite->toggle($course);
        return response()->json(['status' => 'success', 'message' => 'Success'], 200);
    }
    public function course_learning(string $slug): JsonResponse {
        $user = auth()->user();
        if (!self::checkEnrollments($user, $slug)) {
            return response()->json(['status' => 'error', 'message' => 'Not Found!'], 404);
        }
        $user_id = $user->id;

        $course = Course::active()->where('slug', $slug)->select('id', 'instructor_id', 'thumbnail', 'title', 'description')->with([
            'instructor:id,name,image',
            'chapters'                           => function ($query) {
                $query->where('status', 'active')->select('id', 'course_id', 'title')->orderBy('order', 'asc')->with([
                    'chapterItems:id,chapter_id,type',
                    'chapterItems.quiz'   => fn($q)   => $q->select('id', 'chapter_item_id', 'title', 'time', 'attempt', 'pass_mark', 'total_mark')->where('status', 'active'),
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

        if (!$course) {
            return response()->json(['status' => 'error', 'message' => 'Not Found!'], 404);
        }
        $data = new CourseDetailsCollection($course);

        return response()->json(['status' => 'success', 'data' => $data], 200);
    }
    public function get_lesson_info(string $slug, string $type, int $lesson_id): JsonResponse {
        $user = auth()->user();
        if (!self::checkEnrollments($user, $slug)) {
            return response()->json(['status' => 'error', 'message' => 'Not Found!'], 404);
        }

        if (in_array($type, ['lesson', 'document'])) {
            // Fetch lesson details
            $lesson = CourseChapterLesson::select('id', 'course_id', 'chapter_id', 'chapter_item_id', 'title', 'description', 'downloadable', 'file_path', 'storage', 'file_type', 'duration')->findOrFail($lesson_id);

            if (!$lesson) {
                return response()->json(['status' => 'error', 'message' => 'Not Found!'], 404);
            }
            // Update course progress
            self::updateCourseProgress($user->id, $lesson->course_id, $lesson->chapter_id, $lesson_id, $type);

            $data = new LessonResource($lesson);
            return response()->json(['status' => 'success', 'data' => $data], 200);
        } elseif ($type == 'live') {
            $live = CourseChapterLesson::with(['live:id,lesson_id,start_time,type,meeting_id,password,join_url'])->select('id', 'course_id', 'chapter_id', 'chapter_item_id', 'title', 'description', 'duration', 'is_free')->findOrFail($lesson_id);

            if (!$live) {
                return response()->json(['status' => 'error', 'message' => 'Not Found!'], 404);
            }
            // Update course progress
            self::updateCourseProgress($user->id, $live->course_id, $live->chapter_id, $lesson_id, $type);

            $now = Carbon::now();
            $startTime = Carbon::parse($live['live']['start_time']);
            $endTime = $startTime->clone()->addMinutes($live['duration']);
            $live['start_time'] = formattedDateTime($startTime);
            $live['end_time'] = formattedDateTime($endTime);

            if ($now->between($startTime, $endTime)) {
                $live['is_live_now'] = 'started';
            } elseif ($now->lt($startTime)) {
                $live['is_live_now'] = 'not_started';
            } else {
                $live['is_live_now'] = 'ended';
            }

            $data = new LiveResource($live);
            return response()->json(['status' => 'success', 'data' => $data], 200);
        } else {
            $quiz = Quiz::withCount('questions')->findOrFail($lesson_id);
            if (!$quiz) {
                return response()->json(['status' => 'error', 'message' => 'Not Found!'], 404);
            }
            // Update course progress
            self::updateCourseProgress($user->id, $quiz->course_id, $quiz->chapter_id, $lesson_id, $type);

            $data = new QuizResource($quiz);
            return response()->json(['status' => 'success', 'data' => $data], 200);
        }

    }
    public function learning_progress(string $slug): JsonResponse {
        $user = auth()->user();
        $user_id = $user->id;

        $enroll = $user->enrollments()->select('id','course_id')->where('has_access', 1)->whereHas('course', fn($q) => $q->where('slug', $slug))->first();
        
        if (!$enroll) {
            return response()->json(['status' => 'error', 'message' => 'Not Found!'], 404);
        }

        $course_id = $enroll->course_id;

        $totalItems = CourseChapterItem::whereHas('chapter', function ($q) use ($course_id) {
            $q->where('course_id', $course_id);
        })->count();

        $completedItems = CourseProgress::where('user_id', $user_id)
            ->where('course_id', $course_id)
            ->where('watched', 1)
            ->count();

        $progress = ($totalItems > 0) ? ($completedItems / $totalItems) * 100 : 0;

        return response()->json(['status' => 'success', 'data' => $progress], 200);
    }
    public function make_lesson_complete(int $lesson_id): JsonResponse {
        $user = auth()->user();
        $progress = CourseProgress::where(['lesson_id' => $lesson_id, 'user_id' => $user->id])->first();
        if ($progress) {
            $progress->watched = !$progress->watched;
            $progress->save();
            return response()->json(['status' => 'success', 'message' => 'Updated successfully.'], 200);
        }
        return response()->json(['status' => 'error', 'message' => 'You didnt watched this lesson'], 400);
    }
    public function quiz_index(string $slug, int $id): JsonResponse {
        $user = auth()->user();
        if (!self::checkEnrollments($user, $slug)) {
            return response()->json(['status' => 'error', 'message' => 'Not Found!'], 404);
        }
        $quiz = Quiz::whereHas('course', fn($q) => $q->where('slug', $slug))->with([
            'questions:id,quiz_id,title,type' => ['answers:id,question_id,title,correct'],
        ])->withCount('questions')->find($id);
        $attempt = QuizResult::where('user_id', $user->id)->where('quiz_id', $id)->count();
        if ($attempt >= $quiz->attempt) {
            return response()->json(['status' => 'error', 'message' => 'You reached maximum attempt'], 400);
        }
        $quiz = new QuizResource($quiz);
        return response()->json(['status' => 'success', 'data' => ['quiz' => $quiz, 'attempt' => (int) $attempt]], 200);
    }
    public function quiz_store(Request $request, string $slug, int $id): JsonResponse {
        $user = auth()->user();
        if (!self::checkEnrollments($user, $slug)) {
            return response()->json(['status' => 'error', 'message' => 'Not Found!'], 404);
        }

        $validator = Validator::make($request->all(), [
            'answers'               => 'required|array',
            'answers.*.question_id' => 'required|exists:quiz_questions,id',
            'answers.*.answer_id'   => 'required|integer',
        ], [
            'answers.required'               => 'Answers are required.',
            'answers.array'                  => 'Answers must be an array.',
            'answers.*.question_id.required' => 'Each answer must have a question ID.',
            'answers.*.question_id.exists'   => 'The provided question ID does not exist.',
            'answers.*.answer_id.required'   => 'Each answer must have an answer ID.',
            'answers.*.answer_id.integer'    => 'Answer ID must be an integer.',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => $validator->errors()], 422);
        }
        $grad = 0;
        $result = [];

        // Fetch the quiz
        $quiz = Quiz::whereHas('course', fn($q) => $q->where('slug', $slug))->findOrFail($id);

        // Process each answer in the submitted data
        foreach ($request->answers as $key => $answerData) {
            $question = QuizQuestion::findOrFail($answerData['question_id']);
            $correctAnswers = $question->answers->where('correct', 1)->pluck('id')->toArray();

            // Check if the provided answer is correct
            $isCorrect = in_array($answerData['answer_id'], $correctAnswers);

            if ($isCorrect) {
                $grad += $question->grade;
            }

            $result[$answerData['question_id']] = [
                "answer"  => $answerData['answer_id'],
                "correct" => $isCorrect,
            ];
        }

        // Store the quiz result
        $quizResult = QuizResult::create([
            'user_id'    => $user->id,
            'quiz_id'    => $id,
            'result'     => json_encode($result),
            'user_grade' => $grad,
            'status'     => $grad >= $quiz->pass_mark ? 'pass' : 'failed',
        ]);
        return response()->json(['status' => 'success', 'data' => $quizResult], 200);
    }
    public function quiz_results(string $slug, int $id): JsonResponse {
        $user = auth()->user();
        if (!self::checkEnrollments($user, $slug)) {
            return response()->json(['status' => 'error', 'message' => 'Not Found!'], 404);
        }
        $quiz_result = QuizResult::where(['user_id' => $user->id, 'quiz_id' => $id])->select('id', 'user_id', 'quiz_id', 'result', 'user_grade', 'status', 'created_at')->with(['quiz:id,course_id,title,attempt,pass_mark,total_mark', 'quiz.course:id,title'])->first();

        if ($quiz_result) {
            $data = new QuizAttemptsResource($quiz_result);
            return response()->json(['status' => 'success', 'data' => $data], 200);
        }
        return response()->json(['status' => 'error', 'message' => 'Not Found!'], 404);
    }
    public function fetch_lesson_questions(Request $request, string $course_slug, int $lesson_id): JsonResponse {
        $limit = $request->filled('limit') && is_numeric($request->limit) ? (int) $request->limit : 6;
        $user = auth()->user();

        $query = LessonQuestion::query();

        $query->where(['user_id'=> $user->id,'lesson_id'=> $lesson_id])->whereHas('course', function ($q) use ($course_slug) {
            $q->where('slug', $course_slug);
        })->with('user','replies')->withCount('replies');

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('question_title', 'like', "%{$request->search}%")
                    ->orWhere('question_description', 'like', "%{$request->search}%");
            });
        }
        $questions = $query->oldest()->paginate($limit);

        if ($questions->isNotEmpty()) {
            $data = QnaResource::collection($questions);
            return response()->json(['status' => 'success',
                'data'                            => $data,
                'pagination'                      => [
                    'current_page' => $questions->currentPage(),
                    'per_page'     => $questions->perPage(),
                    'total'        => $questions->total(),
                    'last_page'    => $questions->lastPage(),
                    'links'        => [
                        'first' => $questions->url(1),
                        'prev'  => $questions->previousPageUrl(),
                        'next'  => $questions->nextPageUrl(),
                        'last'  => $questions->url($questions->lastPage()),
                    ],
                ],

            ], 200);
        }
        return response()->json(['status' => 'error', 'message' => 'Not Found!'], 404);
    }
    public function create_lesson_questions(Request $request, string $course_slug, int $lesson_id): JsonResponse {
        $user = auth()->user();
        $course = Course::select('id')->whereSlug($course_slug)->whereHas('enrollments', fn($q) => $q->where(['user_id' => $user->id, 'has_access' => 1]))->whereHas('lessons', fn($q) => $q->where('id', $lesson_id))->first();
        if (!$course) {
            return response()->json(['status' => 'error', 'message' => 'Not Found!'], 404);
        }

        $validator = Validator::make($request->all(), [
            'question'    => ['required', 'max:255'],
            'description' => ['required'],
        ], [
            'question.required'    => 'Question is required',
            'question.max'         => 'Question may not be greater than 255 characters',
            'description.required' => 'Description is required',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => $validator->errors()], 422);
        }

        $question = LessonQuestion::create([
            'user_id'              => $user->id,
            'lesson_id'            => $lesson_id,
            'course_id'            => $course->id,
            'question_title'       => $request->question,
            'question_description' => $request->description,
        ]);
        $data = new QnaResource($question);
        return response()->json(['status' => 'success', 'message' => 'Question created successfully','data' => $data], 201);
    }
    public function destroyQuestion(int $question_id): JsonResponse {

        $question = LessonQuestion::where(['user_id' => auth()->id(), 'id' => $question_id])->first();
        if ($question) {
            $question->replies()->delete();
            extractAndFilterImageSrc($question?->question_description);
            $question->delete();

            return response()->json(['status' => 'success', 'message' => 'Question deleted successfully'], 200);
        }
        return response()->json(['status' => 'error', 'message' => 'Not Found!'], 404);
    }
    public function create_replay_questions(Request $request, int $lesson_id, int $question_id): JsonResponse {
        $user = auth()->user();
        $question = LessonQuestion::select('id')->whereHas(
            'lesson', fn($q) => $q->where('id', $lesson_id)
        )->where(['user_id'=>$user->id,'id'=>$question_id])->first();
        if (!$question) {
            return response()->json(['status' => 'error', 'message' => 'Not Found!'], 404);
        }
        $validator = Validator::make($request->all(), [
            'reply' => 'required',
        ], [
            'reply.required' => 'Replay is required',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => $validator->errors()], 422);
        }

        $reply = LessonReply::create([
            'user_id'     => $user->id,
            'question_id' => $question->id,
            'reply'       => $request?->reply,
        ]);
        $data = new QnaReplyResource($reply);
        return response()->json(['status' => 'success', 'data' => $data, 'message' => 'Reply created successfully'], 201);
    }
    public function destroyReply(int $reply_id): JsonResponse {
        $reply = LessonReply::where(['user_id' => auth()->id(), 'id' => $reply_id])->first();
        if ($reply) {
            extractAndFilterImageSrc($reply?->reply);
            $reply->delete();
            return response()->json(['status' => 'success', 'message' => 'Reply deleted successfully'], 200);
        }
        return response()->json(['status' => 'error', 'message' => 'Not Found!'], 404);
    }
    public function course_announcements(string $slug): JsonResponse {
        $user = auth()->user();
        if (!self::checkEnrollments($user, $slug)) {
            return response()->json(['status' => 'error', 'message' => 'Not Found!'], 404);
        }

        $announcements = Announcement::select('instructor_id', 'title', 'announcement', 'created_at')->whereHas('course', fn($q) => $q->where('slug', $slug))->with('instructor:id,name,image')->orderBy('id', 'desc')->get();

        if ($announcements->isNotEmpty()) {
            $data = AnnouncementResource::collection($announcements);
            return response()->json(['status' => 'success', 'data' => $data], 200);
        }
        return response()->json(['status' => 'error', 'message' => 'Not Found!'], 404);
    }
    public function orders(Request $request): JsonResponse {
        $limit = $request->filled('limit') && is_numeric($request->limit) ? (int) $request->limit : 6;
        $orders = Order::select('invoice_id', 'payment_method', 'payable_currency', 'paid_amount', 'payment_status', 'status')->where('buyer_id', auth()->id())->latest()->paginate($limit);

        if ($orders->isNotEmpty()) {
            $data = OrderResource::collection($orders);
            return response()->json(['status' => 'success',
                'data'                            => $data,
                'pagination'                      => [
                    'current_page' => $orders->currentPage(),
                    'per_page'     => $orders->perPage(),
                    'total'        => $orders->total(),
                    'last_page'    => $orders->lastPage(),
                    'links'        => [
                        'first' => $orders->url(1),
                        'prev'  => $orders->previousPageUrl(),
                        'next'  => $orders->nextPageUrl(),
                        'last'  => $orders->url($orders->lastPage()),
                    ],
                ],
            ], 200);
        }
        return response()->json(['status' => 'error', 'message' => 'Not Found!'], 404);
    }
    public function show_order(string $invoice_id): JsonResponse {
        $order = Order::select('id', 'invoice_id', 'buyer_id', 'payment_method', 'payable_currency', 'paid_amount', 'coupon_discount_amount', 'gateway_charge', 'conversion_rate', 'payment_status', 'created_at', 'status')->where('invoice_id', $invoice_id)->where('buyer_id', auth()->id())
            ->with([
                'user:id,name,email,phone,address',
                'orderItems:id,order_id,course_id,price',
                'orderItems.course:id,instructor_id,title',
                'orderItems.course.instructor:id,name',
            ])->first();
        if ($order) {
            $data = new OrderDetailsResource($order);
            return response()->json(['status' => 'success', 'data' => $data], 200);
        }
        return response()->json(['status' => 'error', 'message' => 'Not Found!'], 404);
    }

    public function reviews(Request $request): JsonResponse {
        $limit = $request->filled('limit') && is_numeric($request->limit) ? (int) $request->limit : 6;
        $reviews = CourseReview::with('course:title,id')->where('user_id', auth()->id())->latest()->paginate($limit);

        if ($reviews->isNotEmpty()) {
            $data = ReviewsResource::collection($reviews);
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
    public function show_review(string $id): JsonResponse {
        $review = CourseReview::with('course:title,id')->where('user_id', auth()->id())->find($id);

        if ($review) {
            $data = new ReviewsResource($review);
            return response()->json(['status' => 'success', 'data' => $data], 200);
        }
        return response()->json(['status' => 'error', 'message' => 'Not Found!'], 404);
    }
    public function destroy_review(string $id): JsonResponse {
        $review = CourseReview::where('user_id', auth()->id())->find($id);
        if ($review) {
            $review->delete();
            return response()->json(['status' => 'success', 'message' => 'Review deleted successfully'], 200);
        }
        return response()->json(['status' => 'error', 'message' => 'Not Found!'], 404);
    }
    public function quiz_attempts(Request $request): JsonResponse {
        $limit = $request->filled('limit') && is_numeric($request->limit) ? (int) $request->limit : 6;
        $quizAttempts = QuizResult::select('id', 'user_id', 'quiz_id', 'result', 'user_grade', 'status', 'created_at')->with(['quiz:id,course_id,title', 'quiz.course:id,title'])->where('user_id', auth()->id())->latest()->paginate($limit);

        if ($quizAttempts->isNotEmpty()) {
            $data = QuizAttemptsResource::collection($quizAttempts);
            return response()->json(['status' => 'success',
                'data'                            => $data,
                'pagination'                      => [
                    'current_page' => $quizAttempts->currentPage(),
                    'per_page'     => $quizAttempts->perPage(),
                    'total'        => $quizAttempts->total(),
                    'last_page'    => $quizAttempts->lastPage(),
                    'links'        => [
                        'first' => $quizAttempts->url(1),
                        'prev'  => $quizAttempts->previousPageUrl(),
                        'next'  => $quizAttempts->nextPageUrl(),
                        'last'  => $quizAttempts->url($quizAttempts->lastPage()),
                    ],
                ],
            ], 200);
        }
        return response()->json(['status' => 'error', 'message' => 'Not Found!'], 404);
    }
    public function homeworks(): JsonResponse
    {
        $studentId = auth()->id();

        $homeworks = StudentHomework::query()
            ->with(['submission', 'instructor:id,name,image'])
            ->where('student_id', $studentId)
            ->orderByDesc('id')
            ->get();

        $map = function (StudentHomework $homework): array {
            $submissionMeta = $homework->submission
                ? StudentHomeworkSubmission::parseNotePayload($homework->submission->note)
                : null;

            return [
                'id' => (int) $homework->id,
                'title' => (string) ($homework->title ?? ''),
                'description' => (string) ($homework->description ?? ''),
                'status' => (string) ($homework->status ?? 'pending'),
                'due_at' => optional($homework->due_at)->toDateTimeString(),
                'attachment_name' => (string) ($homework->attachment_name ?? ''),
                'attachment_path' => (string) ($homework->attachment_path ?? ''),
                'instructor_name' => (string) ($homework->instructor?->first_name ?? $homework->instructor?->name ?? ''),
                'instructor_image' => (string) ($homework->instructor?->image ?? ''),
                'submission' => $homework->submission ? [
                    'status' => (string) ($homework->submission->status ?? ''),
                    'submission_name' => (string) ($homework->submission->submission_name ?? ''),
                    'submission_path' => (string) ($homework->submission->submission_path ?? ''),
                    'submitted_at' => optional($homework->submission->submitted_at)->toDateTimeString(),
                    'note' => (string) ($homework->submission->note ?? ''),
                    'student_note' => (string) ($submissionMeta['student_note'] ?? ''),
                    'instructor_note' => (string) ($submissionMeta['instructor_note'] ?? ''),
                    'reviewed_at' => optional($submissionMeta['reviewed_at'] ?? null)->toDateTimeString(),
                ] : null,
            ];
        };

        $active = $homeworks->where('status', '!=', 'archived')->values()->map($map);
        $archived = $homeworks->where('status', 'archived')->values()->map($map);

        return response()->json([
            'status' => 'success',
            'data' => [
                'active' => $active,
                'archived' => $archived,
            ],
        ], 200);
    }

    public function submit_homework(Request $request, StudentHomework $homework): JsonResponse
    {
        if ((int) $homework->student_id !== (int) auth()->id()) {
            return response()->json([
                'status' => 'error',
                'message' => __('You cannot submit this homework.'),
            ], 403);
        }

        $existingSubmission = $homework->submission()
            ->where('student_id', auth()->id())
            ->first();

        $validated = $request->validate([
            'submission' => [
                Rule::requiredIf($existingSubmission === null),
                'nullable',
                'file',
                'max:10240',
            ],
            'note' => ['nullable', 'string'],
        ]);

        $submissionPath = $existingSubmission?->submission_path;
        $submissionName = $existingSubmission?->submission_name;
        if ($request->hasFile('submission')) {
            $file = $request->file('submission');
            $submissionName = $file->getClientOriginalName();
            $submissionPath = file_upload($file, 'uploads/student-homeworks/submissions/');
        }

        $existingNotePayload = StudentHomeworkSubmission::parseNotePayload($existingSubmission?->note);
        $submission = StudentHomeworkSubmission::updateOrCreate(
            [
                'student_homework_id' => $homework->id,
                'student_id' => auth()->id(),
            ],
            [
                'submission_path' => $submissionPath,
                'submission_name' => $submissionName,
                'note' => StudentHomeworkSubmission::buildNotePayload(
                    $validated['note'] ?? $existingNotePayload['student_note'] ?? '',
                    $existingNotePayload['instructor_note'] ?? '',
                    $existingNotePayload['reviewed_at'] ?? null,
                ),
                'submitted_at' => now(),
                'status' => 'submitted',
            ]
        );

        $homework->status = 'submitted';
        $homework->save();

        $submissionMeta = StudentHomeworkSubmission::parseNotePayload($submission->note);

        return response()->json([
            'status' => 'success',
            'message' => __('Homework submitted.'),
            'data' => [
                'status' => (string) $submission->status,
                'submission_name' => (string) ($submission->submission_name ?? ''),
                'submission_path' => (string) ($submission->submission_path ?? ''),
                'submitted_at' => optional($submission->submitted_at)->toDateTimeString(),
                'student_note' => (string) ($submissionMeta['student_note'] ?? ''),
                'instructor_note' => (string) ($submissionMeta['instructor_note'] ?? ''),
                'reviewed_at' => optional($submissionMeta['reviewed_at'] ?? null)->toDateTimeString(),
            ],
        ], 200);
    }

    public function library(Request $request): JsonResponse
    {
        $studentId = auth()->id();
        $selectedCategory = trim((string) $request->query('category', ''));

        $items = StudentLibraryItem::query()
            ->with('instructor:id,name')
            ->where('student_id', $studentId)
            ->orderByDesc('id')
            ->get();

        $categories = $items
            ->pluck('category')
            ->filter()
            ->unique()
            ->values()
            ->map(fn ($category) => [
                'name' => (string) $category,
                'slug' => Str::slug((string) $category),
            ]);

        $filteredItems = $selectedCategory !== ''
            ? $items->filter(function (StudentLibraryItem $item) use ($selectedCategory) {
                $category = (string) ($item->category ?? '');
                return $category === $selectedCategory || Str::slug($category) === $selectedCategory;
            })->values()
            : $items;

        $mappedItems = $filteredItems->map(function (StudentLibraryItem $item) {
            return [
                'id' => (int) $item->id,
                'category' => (string) ($item->category ?? ''),
                'title' => (string) ($item->title ?? ''),
                'description' => (string) ($item->description ?? ''),
                'file_name' => (string) ($item->file_name ?? ''),
                'file_type' => (string) ($item->file_type ?? ''),
                'file_path' => (string) ($item->file_path ?? ''),
                'instructor_name' => (string) ($item->instructor?->first_name ?? $item->instructor?->name ?? ''),
                'created_at' => optional($item->created_at)->toDateTimeString(),
            ];
        })->values();

        return response()->json([
            'status' => 'success',
            'data' => [
                'categories' => $categories,
                'selected_category' => $selectedCategory,
                'items' => $mappedItems,
            ],
        ], 200);
    }

    public function guide(Request $request): JsonResponse
    {
        $language = strtolower((string) $request->query('language', app()->getLocale()));
        if (in_array($language, ['tr', 'en'], true)) {
            app()->setLocale($language);
        }

        $webUrl = static function (string $path): string {
            return url($path);
        };

        $items = [
            ['title' => __('How can I view notifications?'), 'url' => $webUrl('/student/dashboard#student-notifications')],
            ['title' => __('How can I start a lesson?'), 'url' => $webUrl('/student/enrolled-courses')],
            ['title' => __('How can I pause my training?'), 'url' => $webUrl('/student/support')],
            ['title' => __('How can I view my attendance?'), 'url' => $webUrl('/student/reports')],
            ['title' => __('How do I purchase a package?'), 'url' => $webUrl('/student/dashboard#student-plans')],
            ['title' => __('How do I track assignments?'), 'url' => $webUrl('/student/homeworks')],
            ['title' => __('How do I contact my instructor?'), 'url' => $webUrl('/student/messages')],
            ['title' => __('How do I use the library?'), 'url' => $webUrl('/student/library')],
            ['title' => __('Where can I view my reports?'), 'url' => $webUrl('/student/reports')],
            ['title' => __('How do I update my profile settings?'), 'url' => $webUrl('/student/setting')],
            ['title' => __('Steps to change your password'), 'url' => $webUrl('/student/setting#password')],
            ['title' => __('How do I create a support request?'), 'url' => $webUrl('/student/support')],
        ];

        return response()->json([
            'status' => 'success',
            'data' => [
                'title' => __('User Guide'),
                'subtitle' => __('Watch the user guide videos below to better understand the system and quickly find answers to your questions.'),
                'items' => $items,
            ],
        ], 200);
    }
    public function notifications(Request $request): JsonResponse
    {
        $user = auth()->user();
        $limit = $request->filled('limit') && is_numeric($request->limit) ? (int) $request->limit : 30;
        $limit = max(1, min($limit, 100));

        [$lessonNotifications, $messageNotifications, $paymentNotifications] = $this->buildNotificationCollections($user);
        $lessonNotifications = $this->applyPersistedNotificationReads($user->id, $lessonNotifications);
        $paymentNotifications = $this->applyPersistedNotificationReads($user->id, $paymentNotifications);

        $notifications = $lessonNotifications
            ->merge($messageNotifications)
            ->merge($paymentNotifications)
            ->sortByDesc('sort_time')
            ->take($limit)
            ->values()
            ->map(function ($item) {
                $item = (array) $item;
                unset($item['sort_time']);
                return $item;
            });

        return response()->json([
            'status' => 'success',
            'data' => $notifications,
        ], 200);
    }

    public function mark_all_notifications_as_read(): JsonResponse
    {
        $user = auth()->user();

        Message::query()
            ->where('receiver_id', $user->id)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        [$lessonNotifications, , $paymentNotifications] = $this->buildNotificationCollections($user);
        $this->storePersistedNotificationReads(
            $user->id,
            $lessonNotifications
                ->merge($paymentNotifications)
                ->filter(fn ($item) => is_array($item) && (($item['unread'] ?? false) === true))
        );

        return response()->json([
            'status' => 'success',
            'message' => __('Notifications marked as read.'),
        ], 200);
    }

    private function buildNotificationCollections(User $user): array
    {
        $lessonNotifications = collect();
        $liveResponse = $this->live_lessons()->getData(true);
        $upcomingLessons = collect(data_get($liveResponse, 'data.upcoming', []))
            ->filter(fn ($item) => is_array($item))
            ->take(8);

        foreach ($upcomingLessons as $lesson) {
            $startRaw = (string) ($lesson['start_time'] ?? '');
            $startAt = $startRaw !== '' ? Carbon::parse($startRaw) : null;
            $lessonTitle = trim((string) ($lesson['title'] ?? $lesson['course_title'] ?? ''));
            $instructorName = trim((string) ($lesson['instructor_name'] ?? ''));
            $subtitle = trim($lessonTitle . ($instructorName !== '' ? ' - ' . $instructorName : ''));

            $lessonNotifications->push([
                'id' => 'lesson-' . (string) ($lesson['id'] ?? ''),
                'type' => 'lesson',
                'title' => __('Upcoming Lessons'),
                'subtitle' => $subtitle !== '' ? $subtitle : __('Upcoming Lessons'),
                'time' => $this->notificationTimeLabel($startAt),
                'unread' => true,
                'lesson' => $lesson,
                'sort_time' => $startAt?->timestamp ?? 0,
            ]);
        }

        $messageNotifications = collect();
        $unreadThreads = Message::query()
            ->select('sender_id', DB::raw('MAX(id) as last_message_id'), DB::raw('COUNT(*) as unread_count'), DB::raw('MAX(created_at) as last_created_at'))
            ->where('receiver_id', $user->id)
            ->where('is_read', false)
            ->groupBy('sender_id')
            ->orderByRaw('MAX(created_at) DESC')
            ->take(15)
            ->get();

        if ($unreadThreads->isNotEmpty()) {
            $messageIds = $unreadThreads->pluck('last_message_id')->filter()->map(fn ($id) => (int) $id)->values();
            $senderIds = $unreadThreads->pluck('sender_id')->filter()->map(fn ($id) => (int) $id)->values();

            $lastMessages = Message::query()
                ->whereIn('id', $messageIds)
                ->get()
                ->keyBy('id');

            $senders = User::query()
                ->select(['id', 'name'])
                ->whereIn('id', $senderIds)
                ->get()
                ->keyBy('id');

            foreach ($unreadThreads as $thread) {
                $lastMessage = $lastMessages->get((int) $thread->last_message_id);
                $sender = $senders->get((int) $thread->sender_id);
                $createdAt = $lastMessage?->created_at
                    ? Carbon::parse((string) $lastMessage->created_at)
                    : ($thread->last_created_at ? Carbon::parse((string) $thread->last_created_at) : null);

                $body = trim((string) ($lastMessage?->body ?? ''));
                if ($body !== '') {
                    $body = Str::limit($body, 140);
                }

                $messageNotifications->push([
                    'id' => 'thread-' . (string) ($thread->sender_id ?? ''),
                    'type' => 'message',
                    'title' => __('Messages'),
                    'subtitle' => $body !== '' ? $body : __('Messages'),
                    'time' => $this->notificationTimeLabel($createdAt),
                    'unread' => ((int) ($thread->unread_count ?? 0)) > 0,
                    'thread' => [
                        'partner_id' => (int) ($thread->sender_id ?? 0),
                        'partner_name' => (string) ($sender?->first_name ?? $sender?->name ?? ''),
                    ],
                    'sort_time' => $createdAt?->timestamp ?? 0,
                ]);
            }
        }

        $paymentNotifications = Order::query()
            ->select(['id', 'invoice_id', 'payment_status', 'status', 'created_at'])
            ->where('buyer_id', $user->id)
            ->latest('id')
            ->take(8)
            ->get()
            ->map(function (Order $order) {
                $createdAt = $order->created_at ? Carbon::parse((string) $order->created_at) : null;
                $invoiceId = trim((string) ($order->invoice_id ?? ''));
                $paymentStatus = trim((string) ($order->payment_status ?? ''));
                $orderStatus = trim((string) ($order->status ?? ''));

                $subtitleParts = [];
                if ($invoiceId !== '') {
                    $subtitleParts[] = __('Order Id: ') . $invoiceId;
                }
                if ($paymentStatus !== '') {
                    $subtitleParts[] = __('Payment Status') . ': ' . ucfirst($paymentStatus);
                } elseif ($orderStatus !== '') {
                    $subtitleParts[] = ucfirst($orderStatus);
                }

                return [
                    'id' => 'payment-' . (string) ($order->id ?? ''),
                    'type' => 'payment',
                    'title' => __('Payment'),
                    'subtitle' => implode(' - ', $subtitleParts),
                    'time' => $this->notificationTimeLabel($createdAt),
                    'unread' => $createdAt ? $createdAt->gt(now()->subDay()) : false,
                    'sort_time' => $createdAt?->timestamp ?? 0,
                ];
            });

        return [$lessonNotifications, $messageNotifications, $paymentNotifications];
    }

    private function applyPersistedNotificationReads(int $userId, Collection $notifications): Collection
    {
        if (!$this->canPersistNotificationReads() || $notifications->isEmpty()) {
            return $notifications;
        }

        $keys = $notifications
            ->pluck('id')
            ->filter(fn ($id) => is_string($id) && $id !== '')
            ->values();

        if ($keys->isEmpty()) {
            return $notifications;
        }

        $readLookup = MobileNotificationRead::query()
            ->where('user_id', $userId)
            ->whereIn('notification_key', $keys)
            ->pluck('notification_key')
            ->flip();

        return $notifications->map(function ($item) use ($readLookup) {
            $item = (array) $item;
            $key = (string) ($item['id'] ?? '');
            if ($key !== '' && $readLookup->has($key)) {
                $item['unread'] = false;
            }
            return $item;
        });
    }

    private function storePersistedNotificationReads(int $userId, Collection $notifications): void
    {
        if (!$this->canPersistNotificationReads() || $notifications->isEmpty()) {
            return;
        }

        $now = now();
        $records = $notifications
            ->pluck('id')
            ->filter(fn ($id) => is_string($id) && $id !== '')
            ->unique()
            ->map(fn ($id) => [
                'user_id' => $userId,
                'notification_key' => $id,
                'read_at' => $now,
                'created_at' => $now,
                'updated_at' => $now,
            ])
            ->values()
            ->all();

        if ($records === []) {
            return;
        }

        MobileNotificationRead::query()->upsert(
            $records,
            ['user_id', 'notification_key'],
            ['read_at', 'updated_at']
        );
    }

    private function canPersistNotificationReads(): bool
    {
        return Schema::hasTable('mobile_notification_reads');
    }

    public function support_requests(Request $request): JsonResponse
    {
        if (!Schema::hasTable('contact_messages')) {
            return response()->json([
                'status' => 'error',
                'message' => 'contact_messages table not found. Please run migrations.',
            ], 500);
        }

        $limit = $request->filled('limit') && is_numeric($request->limit) ? (int) $request->limit : 20;
        $user = auth()->user();
        $email = trim((string) ($user?->email ?? ''));

        if ($email === '') {
            return response()->json([
                'status' => 'success',
                'data' => [],
                'pagination' => [
                    'current_page' => 1,
                    'per_page' => $limit,
                    'total' => 0,
                    'last_page' => 1,
                    'links' => [
                        'first' => null,
                        'prev' => null,
                        'next' => null,
                        'last' => null,
                    ],
                ],
            ], 200);
        }

        $tickets = ContactMessage::query()
            ->select(['id', 'name', 'email', 'phone', 'subject', 'message', 'created_at'])
            ->where('email', $email)
            ->where(function ($query) {
                $query->where('subject', 'like', 'Support:%')
                    ->orWhere('subject', 'like', 'Destek:%');
            })
            ->latest('id')
            ->paginate($limit);

        $items = collect($tickets->items())->map(function (ContactMessage $ticket) {
            $subject = trim((string) ($ticket->subject ?? ''));
            $category = trim((string) Str::after($subject, ':'));
            if ($category === '') {
                $category = $subject !== '' ? $subject : 'Support';
            }

            return [
                'id' => (int) $ticket->id,
                'category' => $category,
                'subject' => $subject,
                'message' => (string) ($ticket->message ?? ''),
                'name' => (string) ($ticket->name ?? ''),
                'email' => (string) ($ticket->email ?? ''),
                'phone' => (string) ($ticket->phone ?? ''),
                'created_at' => optional($ticket->created_at)->toDateTimeString(),
            ];
        })->values();

        return response()->json([
            'status' => 'success',
            'data' => $items,
            'pagination' => [
                'current_page' => $tickets->currentPage(),
                'per_page' => $tickets->perPage(),
                'total' => $tickets->total(),
                'last_page' => $tickets->lastPage(),
                'links' => [
                    'first' => $tickets->url(1),
                    'prev' => $tickets->previousPageUrl(),
                    'next' => $tickets->nextPageUrl(),
                    'last' => $tickets->url($tickets->lastPage()),
                ],
            ],
        ], 200);
    }
    public function create_support_request(Request $request): JsonResponse
    {
        if (!Schema::hasTable('contact_messages')) {
            return response()->json([
                'status' => 'error',
                'message' => 'contact_messages table not found. Please run migrations.',
            ], 500);
        }

        $validator = Validator::make($request->all(), [
            'subject' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string'],
            'phone' => ['nullable', 'string', 'max:50'],
        ], [
            'subject.required' => 'Subject is required',
            'message.required' => 'Message is required',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => $validator->errors()], 422);
        }

        $user = auth()->user();
        $inputSubject = trim((string) $request->input('subject'));
        $normalizedSubject = Str::startsWith(Str::lower($inputSubject), 'support:')
            ? $inputSubject
            : 'Support: ' . $inputSubject;

        $ticket = new ContactMessage();
        $ticket->name = (string) ($user?->name ?? '');
        $ticket->email = (string) ($user?->email ?? '');
        $ticket->phone = trim((string) ($request->input('phone') ?: ($user?->phone ?? '')));
        $ticket->subject = $normalizedSubject;
        $ticket->message = trim((string) $request->input('message'));
        $ticket->save();

        dispatch(new ContactMessageSendJob($ticket));

        return response()->json([
            'status' => 'success',
            'message' => 'Support request created successfully',
            'data' => [
                'id' => (int) $ticket->id,
                'subject' => (string) ($ticket->subject ?? ''),
                'message' => (string) ($ticket->message ?? ''),
                'created_at' => optional($ticket->created_at)->toDateTimeString(),
            ],
        ], 201);
    }
    public function show_quiz_attempt(string $id): JsonResponse {
        $quiz_result = QuizResult::select('id', 'user_id', 'quiz_id', 'result', 'user_grade', 'status', 'created_at')->with(['quiz:id,course_id,title', 'quiz.course:id,title'])->where('user_id', auth()->id())->find($id);

        if ($quiz_result) {
            $data = new QuizAttemptsResource($quiz_result);
            return response()->json(['status' => 'success', 'data' => $data], 200);
        }
        return response()->json(['status' => 'error', 'message' => 'Not Found!'], 404);
    }
    public function reports(): JsonResponse
    {
        $user = auth()->user();

        $progressQuery = CourseProgress::query()
            ->where('user_id', $user->id)
            ->where('watched', 1);

        $completedLessons = $progressQuery->count();

        $lessonIds = $progressQuery->pluck('lesson_id')->filter()->values();
        $totalMinutes = 0;
        if ($lessonIds->isNotEmpty()) {
            $durations = CourseChapterLesson::query()
                ->whereIn('id', $lessonIds)
                ->pluck('duration', 'id');
            foreach ($durations as $duration) {
                if (!$duration) {
                    continue;
                }
                if (is_numeric($duration)) {
                    $totalMinutes += (int) $duration;
                    continue;
                }
                if (preg_match('/\d+/', (string) $duration, $matches)) {
                    $totalMinutes += (int) $matches[0];
                }
            }
        }

        $reviewCount = CourseReview::query()
            ->where('user_id', $user->id)
            ->count();

        $quizAverage = QuizResult::query()
            ->where('user_id', $user->id)
            ->avg('user_grade');
        $quizAverage = $quizAverage ? round((float) $quizAverage, 2) : 0;

        return response()->json([
            'status' => 'success',
            'data' => [
                'total_minutes' => $totalMinutes,
                'completed_lessons' => $completedLessons,
                'quiz_average' => $quizAverage,
                'review_count' => $reviewCount,
            ],
        ], 200);
    }
    public function profile(): JsonResponse {
        $user = auth()->user();
        $data = new UserResource($user);
        return response()->json(['status' => 'success', 'data' => $data], 200);
    }

    public function request_trial_lesson(Request $request): JsonResponse
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json(['status' => 'error', 'message' => 'UnAuthenticated'], 401);
        }

        if (!Schema::hasTable('trial_lesson_requests')) {
            return response()->json([
                'status' => 'error',
                'message' => __('Trial request table not found. Please run migrations.'),
            ], 500);
        }

        $hasPaidPlan = Order::query()
            ->where('buyer_id', $user->id)
            ->where('order_type', 'student_plan')
            ->where(function ($query) {
                $query->where('payment_status', 'paid')
                    ->orWhere('status', 'completed');
            })
            ->exists();

        if ($hasPaidPlan) {
            return response()->json([
                'status' => 'error',
                'message' => __('You already have an active plan.'),
            ], 409);
        }

        $alreadyRequested = DB::table('trial_lesson_requests')
            ->where('user_id', $user->id)
            ->whereIn('status', ['pending', 'approved'])
            ->exists();

        if ($alreadyRequested) {
            return response()->json([
                'status' => 'error',
                'message' => __('Your trial lesson request has already been received.'),
            ], 409);
        }

        DB::table('trial_lesson_requests')->insert([
            'user_id' => $user->id,
            'phone' => ($phone = trim((string) ($user->phone ?? ''))) !== '' ? $phone : null,
            'status' => 'pending',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $whatsappLeadPhone = preg_replace('/\D+/', '', (string) config('app.whatsapp_lead_phone', ''));
        $trialMessage = "Hello, I would like to request a trial lesson.\n"
            . 'Name: ' . ($user->name ?? '') . "\n"
            . 'Phone: ' . (($user->phone ?? '') !== '' ? $user->phone : '-') . "\n"
            . 'Email: ' . ($user->email ?? '') . "\n"
            . 'User ID: ' . ($user->id ?? '');

        $trialWhatsAppUrl = $whatsappLeadPhone !== ''
            ? 'https://wa.me/' . $whatsappLeadPhone . '?text=' . rawurlencode($trialMessage)
            : null;

        return response()->json([
            'status' => 'success',
            'message' => __('Your trial lesson request has been received.'),
            'data' => [
                'whatsapp_url' => $trialWhatsAppUrl,
            ],
        ], 201);
    }

    public function update_profile(Request $request): JsonResponse {
        $introVideoMaxKb = (int) config('course.instructor_intro_video_max_kb', 204800);
        $introVideoMaxMb = (int) ceil($introVideoMaxKb / 1024);

        $validator = Validator::make($request->all(), [
            'name'  => ['required', 'string', 'max:50'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'age'   => ['nullable', 'integer', 'max:150'],
            'job_title' => ['nullable', 'string', 'max:255'],
            'short_bio' => ['nullable', 'string', 'max:2000'],
            'bio' => ['nullable', 'string', 'max:10000'],
            'country_id' => ['nullable', 'integer'],
            'first_name' => ['nullable', 'string', 'max:50'],
            'last_name' => ['nullable', 'string', 'max:50'],
            'education' => ['nullable', 'string', 'max:255'],
            'university' => ['nullable', 'string', 'max:255'],
            'turkish_level' => ['nullable', 'string', 'max:100'],
            'experience_years' => ['nullable', 'string', 'max:100'],
            'availability_per_month' => ['nullable', 'string', 'max:100'],
            'birth_date' => ['nullable', 'date'],
            'major' => ['nullable', 'string', 'max:255'],
            'identity_number' => ['nullable', 'string', 'max:255'],
            'account_holder_name' => ['nullable', 'string', 'max:255'],
            'bank_number' => ['nullable', 'string', 'max:255'],
            'agreement_address' => ['nullable', 'string', 'max:255'],
            'can_teach' => ['nullable', 'array'],
            'can_teach.*' => ['string', 'max:100'],
            'certificates' => ['nullable', 'array'],
            'certificates.*' => ['string', 'max:100'],
            'work_type' => ['nullable', 'string', 'max:100'],
            'teaching_materials' => ['nullable', 'array'],
            'teaching_materials.*' => ['string', 'max:100'],
            'intro_video' => ['nullable', 'file', 'mimes:mp4,webm,ogg,mov', 'max:' . $introVideoMaxKb],
        ], [
            'name.required'  => 'The name field is required',
            'name.string'    => 'The name must be a string',
            'name.max'       => 'The name may not be greater than 50 characters.',
            'email.required' => 'The email field is required',
            'email.email'    => 'The email must be a valid email address',
            'email.max'      => 'The email may not be greater than 255 characters',
            'intro_video.max' => "The intro video may not be greater than {$introVideoMaxMb} MB.",
            'phone.string'   => 'The phone must be a string',
            'phone.max'      => 'The phone may not be greater than 30 characters',
            'age.integer'    => 'The age must be an integer',
            'age.max'        => 'The age may not be greater than 150',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => $validator->errors()], 422);
        }

        $user = auth()->user();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->phone = $request->phone;
        $user->age = $request->age;
        $user->gender = $request->gender;

        if ($request->filled('job_title')) {
            $user->job_title = $request->input('job_title');
        } elseif ($request->filled('major')) {
            $user->job_title = $request->input('major');
        }

        if ($request->has('short_bio')) {
            $user->short_bio = (string) $request->input('short_bio', '');
        }
        if ($request->has('bio')) {
            $user->bio = (string) $request->input('bio', '');
        }
        if ($request->filled('country_id')) {
            $user->country_id = $request->input('country_id');
        }
        if ($request->filled('agreement_address')) {
            $user->address = (string) $request->input('agreement_address');
        }

        $profileData = (array) ($user->instructor_profile ?? []);
        $profileTextFields = [
            'first_name',
            'last_name',
            'education',
            'university',
            'turkish_level',
            'experience_years',
            'availability_per_month',
            'birth_date',
            'major',
            'identity_number',
            'account_holder_name',
            'bank_number',
            'agreement_address',
            'work_type',
        ];
        foreach ($profileTextFields as $field) {
            if ($request->has($field)) {
                $profileData[$field] = trim((string) $request->input($field, ''));
            }
        }

        if ($request->has('can_teach')) {
            $profileData['can_teach'] = array_values(array_filter((array) $request->input('can_teach', []), function ($value) {
                return $value !== null && $value !== '';
            }));
        }
        if ($request->has('certificates')) {
            $profileData['certificates'] = array_values(array_filter((array) $request->input('certificates', []), function ($value) {
                return $value !== null && $value !== '';
            }));
        }
        if ($request->has('teaching_materials')) {
            $profileData['teaching_materials'] = array_values(array_filter((array) $request->input('teaching_materials', []), function ($value) {
                return $value !== null && $value !== '';
            }));
        }

        if ($request->hasFile('intro_video')) {
            $videoPath = 'uploads/instructor-videos/';
            if (!File::exists(public_path($videoPath))) {
                File::makeDirectory(public_path($videoPath), 0755, true);
            }
            $profileData['intro_video'] = file_upload(
                file: $request->intro_video,
                path: $videoPath,
                oldFile: (string) ($profileData['intro_video'] ?? '')
            );
        }

        if ($request->filled('first_name') || $request->filled('last_name')) {
            $name = trim((string) $request->input('first_name', '') . ' ' . (string) $request->input('last_name', ''));
            if ($name !== '') {
                $user->name = $name;
            }
        }
        $user->instructor_profile = $profileData;
        $user->save();

        return response()->json(['status' => 'success', 'message' => 'Updated Successfully'], 200);
    }
    public function update_profile_picture(Request $request): JsonResponse {
        $validator = Validator::make($request->all(), [
            'image' => ['required', 'image', 'max:2000'],
        ], [
            'image.required' => 'The image is required.',
            'image.image'    => 'The image must be an image',
            'image.max'      => 'The image may not be greater than 2000 kilobytes',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => $validator->errors()], 422);
        }

        // handle image files
        if ($request->hasFile('image')) {
            $user = auth()->user();
            $imagePath = file_upload(file: $request->image, optimize: true);
            $user->image = $imagePath;
            $user->save();
        }

        return response()->json(['status' => 'success', 'message' => 'Updated Successfully'], 200);
    }
    public function update_bio(Request $request): JsonResponse {
        $validator = Validator::make($request->all(), [
            'job_title' => ['required', 'string', 'max:255'],
            'bio'       => ['required', 'string', 'max:10000'],
            'short_bio' => ['required', 'string', 'max:2000'],
        ], [
            'job_title.required' => 'The designation field is required',
            'job_title.string'   => 'The designation must be a string',
            'job_title.max'      => 'The designation may not be greater than 255 characters.',
            'bio.required'       => 'The bio field is required',
            'bio.string'         => 'The bio must be a string',
            'bio.max'            => 'The bio may not be greater than 10000 characters.',
            'short_bio.required' => 'The short bio field is required',
            'short_bio.string'   => 'The short bio must be a string',
            'short_bio.max'      => 'The short bio may not be greater than 2000 characters.',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => $validator->errors()], 422);
        }

        $user = auth()->user();
        $user->job_title = $request->job_title;
        $user->bio = $request->bio;
        $user->short_bio = $request->short_bio;
        $user->save();

        return response()->json(['status' => 'success', 'message' => 'Updated Successfully'], 200);
    }
    public function update_password(Request $request): JsonResponse {
        $validator = Validator::make($request->all(), [
            'current_password' => ['required', 'string', 'max:255', 'current_password'],
            'password'         => ['required', 'string', 'max:255', 'confirmed'],
        ], [
            'current_password.required'         => 'The current password field is required',
            'current_password.string'           => 'The current password must be a string',
            'current_password.max'              => 'The current password may not be greater than 255 characters.',
            'current_password.current_password' => 'The current password is incorrect.',
            'password.required'                 => 'The password field is required',
            'password.string'                   => 'The password must be a string',
            'password.max'                      => 'The password may not be greater than 255 characters.',
            'password.confirmed'                => 'The password confirmation does not match.',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => $validator->errors()], 422);
        }

        $user = auth()->user();
        $user->password = Hash::make($request->password);
        $user->save();

        return response()->json(['status' => 'success', 'message' => 'Updated Successfully'], 200);
    }
    public function delete_account(Request $request): JsonResponse {
        $validator = Validator::make($request->all(), [
            'current_password' => ['required', 'string', 'max:255', 'current_password'],
            'confirm'          => ['nullable', 'string', 'in:DELETE'],
        ], [
            'current_password.required'         => 'The current password field is required',
            'current_password.current_password' => 'The current password is incorrect.',
            'confirm.in'                        => 'The confirm field must be DELETE.',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => $validator->errors()], 422);
        }

        /** @var User $user */
        $user = auth()->user();

        try {
            DB::beginTransaction();

            $this->removeUserOwnedRows($user->id);

            $nowSuffix = now()->format('YmdHis');
            $user->name = 'Deleted User #' . $user->id;
            $user->email = "deleted+{$user->id}+{$nowSuffix}@lingufranca.invalid";
            $user->phone = null;
            $user->address = null;
            $user->state = null;
            $user->city = null;
            $user->facebook = null;
            $user->twitter = null;
            $user->linkedin = null;
            $user->website = null;
            $user->github = null;
            $user->bio = null;
            $user->short_bio = null;
            $user->job_title = null;
            $user->gender = null;
            $user->age = null;
            $user->country_id = null;
            $user->image = '/uploads/website-images/frontend-avatar.png';
            $user->verification_token = null;
            $user->forget_password_token = null;
            $user->email_verified_at = null;
            $user->remember_token = null;
            $user->instructor_profile = [];
            $user->status = UserStatus::DEACTIVE->value;
            $user->is_banned = UserStatus::BANNED->value;
            $user->password = Hash::make(Str::random(64));
            $user->save();

            $user->tokens()->delete();

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Account deleted successfully',
            ], 200);
        } catch (\Throwable $e) {
            DB::rollBack();
            logger()->error('Account delete failed', [
                'user_id' => $user->id ?? null,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong',
            ], 500);
        }
    }
    public function update_address(Request $request): JsonResponse {
        $validator = Validator::make($request->all(), [
            'country_id' => ['required', 'integer', 'exists:countries,id'],
            'state'      => ['nullable', 'max:255'],
            'city'       => ['nullable', 'max:255'],
            'address'    => ['nullable', 'string', 'max:255'],
        ], [
            'country_id.required' => 'You must select a country.',
            'country_id.integer'  => 'Country ID must be an integer.',
            'country_id.exists'   => 'The selected country is invalid.',
            'state.integer'       => 'State ID must be an integer.',
            'state.exists'        => 'The selected state is invalid.',
            'city.integer'        => 'City ID must be an integer.',
            'city.exists'         => 'The selected city is invalid.',
            'address.string'      => 'The address must be a string.',
            'address.max'         => 'The address may not be greater than 255 characters.',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => $validator->errors()], 422);
        }

        $user = auth()->user();
        $user->country_id = $request->country_id;
        $user->state = $request->state;
        $user->city = $request->city;
        $user->address = $request->address;
        $user->save();

        return response()->json(['status' => 'success', 'message' => 'Updated Successfully'], 200);
    }
    public function update_socials(Request $request): JsonResponse {
        $user = auth()->user();
        $user->facebook = $request->facebook;
        $user->twitter = $request->twitter;
        $user->website = $request->website;
        $user->linkedin = $request->linkedin;
        $user->github = $request->github;
        $user->save();

        return response()->json(['status' => 'success', 'message' => 'Updated Successfully'], 200);
    }

    public function educations(): JsonResponse
    {
        $items = UserEducation::query()
            ->where('user_id', auth()->id())
            ->orderByDesc('start_date')
            ->get()
            ->map(function (UserEducation $education) {
                return [
                    'id' => (int) $education->id,
                    'organization' => (string) ($education->organization ?? ''),
                    'degree' => (string) ($education->degree ?? ''),
                    'start_date' => optional($education->start_date)->format('Y-m-d'),
                    'end_date' => optional($education->end_date)->format('Y-m-d'),
                    'current' => (bool) ($education->current ?? false),
                ];
            });

        return response()->json(['status' => 'success', 'data' => $items], 200);
    }

    public function store_education(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'organization' => ['required', 'max:255', 'string'],
            'degree' => ['required', 'max:255', 'string'],
            'start_date' => ['required', 'date'],
            'end_date' => ['nullable', 'date'],
            'current' => ['nullable', 'boolean'],
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => $validator->errors()], 422);
        }

        $education = new UserEducation();
        $education->user_id = auth()->id();
        $education->organization = $request->organization;
        $education->degree = $request->degree;
        $education->start_date = $request->start_date;
        $education->end_date = $request->end_date;
        $education->current = (bool) ($request->current ?? false);
        $education->save();

        return response()->json(['status' => 'success', 'message' => 'Created successfully'], 201);
    }

    public function update_education(Request $request, UserEducation $education): JsonResponse
    {
        if ((int) $education->user_id !== (int) auth()->id()) {
            return response()->json(['status' => 'error', 'message' => 'Permission Denied!'], 403);
        }

        $validator = Validator::make($request->all(), [
            'organization' => ['required', 'max:255', 'string'],
            'degree' => ['required', 'max:255', 'string'],
            'start_date' => ['required', 'date'],
            'end_date' => ['nullable', 'date'],
            'current' => ['nullable', 'boolean'],
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => $validator->errors()], 422);
        }

        $education->organization = $request->organization;
        $education->degree = $request->degree;
        $education->start_date = $request->start_date;
        $education->end_date = $request->end_date;
        $education->current = (bool) ($request->current ?? false);
        $education->save();

        return response()->json(['status' => 'success', 'message' => 'Updated successfully'], 200);
    }

    public function destroy_education(UserEducation $education): JsonResponse
    {
        if ((int) $education->user_id !== (int) auth()->id()) {
            return response()->json(['status' => 'error', 'message' => 'Permission Denied!'], 403);
        }

        $education->delete();

        return response()->json(['status' => 'success', 'message' => 'Deleted successfully'], 200);
    }

    public function experiences(): JsonResponse
    {
        $items = UserExperience::query()
            ->where('user_id', auth()->id())
            ->orderByDesc('start_date')
            ->get()
            ->map(function (UserExperience $experience) {
                return [
                    'id' => (int) $experience->id,
                    'company' => (string) ($experience->company ?? ''),
                    'position' => (string) ($experience->position ?? ''),
                    'start_date' => optional($experience->start_date)->format('Y-m-d'),
                    'end_date' => optional($experience->end_date)->format('Y-m-d'),
                    'current' => (bool) ($experience->current ?? false),
                ];
            });

        return response()->json(['status' => 'success', 'data' => $items], 200);
    }

    public function store_experience(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'company' => ['required', 'string', 'max:255'],
            'position' => ['required', 'string', 'max:255'],
            'start_date' => ['required', 'date'],
            'end_date' => ['nullable', 'date'],
            'current' => ['nullable', 'boolean'],
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => $validator->errors()], 422);
        }

        $experience = new UserExperience();
        $experience->user_id = auth()->id();
        $experience->company = $request->company;
        $experience->position = $request->position;
        $experience->start_date = $request->start_date;
        $experience->end_date = $request->end_date;
        $experience->current = (bool) ($request->current ?? false);
        $experience->save();

        return response()->json(['status' => 'success', 'message' => 'Created successfully'], 201);
    }

    public function update_experience(Request $request, UserExperience $experience): JsonResponse
    {
        if ((int) $experience->user_id !== (int) auth()->id()) {
            return response()->json(['status' => 'error', 'message' => 'Permission Denied!'], 403);
        }

        $validator = Validator::make($request->all(), [
            'company' => ['required', 'string', 'max:255'],
            'position' => ['required', 'string', 'max:255'],
            'start_date' => ['required', 'date'],
            'end_date' => ['nullable', 'date'],
            'current' => ['nullable', 'boolean'],
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => $validator->errors()], 422);
        }

        $experience->company = $request->company;
        $experience->position = $request->position;
        $experience->start_date = $request->start_date;
        $experience->end_date = $request->end_date;
        $experience->current = (bool) ($request->current ?? false);
        $experience->save();

        return response()->json(['status' => 'success', 'message' => 'Updated successfully'], 200);
    }

    public function destroy_experience(UserExperience $experience): JsonResponse
    {
        if ((int) $experience->user_id !== (int) auth()->id()) {
            return response()->json(['status' => 'error', 'message' => 'Permission Denied!'], 403);
        }

        $experience->delete();

        return response()->json(['status' => 'success', 'message' => 'Deleted successfully'], 200);
    }
    /**
     * Check if a user is enrolled in a course with access.
     *
     * @param $user The user to check.
     * @param string $course_slug The course slug.
     * @return bool True if enrolled with access, false otherwise.
     */
    private function checkEnrollments($user, $course_slug) {
        return $user->enrollments()->where('has_access', 1)->whereHas('course', fn($q) => $q->where('slug', $course_slug))->exists();
    }

    private function removeUserOwnedRows(int $userId): void
    {
        $this->deleteWhere('favorite_course_user', 'user_id', $userId);
        $this->deleteWhere('carts', 'user_id', $userId);
        $this->deleteWhere('user_educations', 'user_id', $userId);
        $this->deleteWhere('user_experiences', 'user_id', $userId);
        $this->deleteWhere('user_onboardings', 'user_id', $userId);
        $this->deleteWhere('socialite_credentials', 'user_id', $userId);
        $this->deleteWhere('instructor_availabilities', 'instructor_id', $userId);
        $this->deleteWhere('student_live_lessons', 'student_id', $userId);
        $this->deleteWhere('student_live_lessons', 'instructor_id', $userId);
        $this->deleteWhere('student_homeworks', 'student_id', $userId);
        $this->deleteWhere('student_homeworks', 'instructor_id', $userId);
        $this->deleteWhere('student_homework_submissions', 'student_id', $userId);
        $this->deleteWhere('student_library_items', 'student_id', $userId);
        $this->deleteWhere('student_library_items', 'instructor_id', $userId);
        $this->deleteWhere('live_lesson_attendances', 'user_id', $userId);
        $this->deleteWhere('student_live_lesson_attendances', 'student_id', $userId);
        $this->deleteWhere('messages', 'sender_id', $userId);
        $this->deleteWhere('messages', 'receiver_id', $userId);
        $this->deleteWhere('referral_rewards', 'referrer_user_id', $userId);
        $this->deleteWhere('referral_rewards', 'referred_user_id', $userId);
        $this->deleteWhere('trial_lesson_requests', 'requested_by_user_id', $userId);
        $this->deleteWhere('user_plans', 'user_id', $userId);

        if (Schema::hasTable('user_plans') && Schema::hasColumn('user_plans', 'assigned_instructor_id')) {
            DB::table('user_plans')->where('assigned_instructor_id', $userId)->update(['assigned_instructor_id' => null]);
        }
    }

    private function deleteWhere(string $table, string $column, int $userId): void
    {
        if (!Schema::hasTable($table) || !Schema::hasColumn($table, $column)) {
            return;
        }

        DB::table($table)->where($column, $userId)->delete();
    }
    private function notificationTimeLabel(?Carbon $dateTime): string
    {
        if (!$dateTime) {
            return '';
        }

        $seconds = abs(now()->diffInSeconds($dateTime, false));
        if ($seconds < 60) {
            return '1m';
        }

        if ($seconds < 3600) {
            return (string) max(1, (int) floor($seconds / 60)) . 'm';
        }

        if ($seconds < 86400) {
            return (string) max(1, (int) floor($seconds / 3600)) . 'h';
        }

        return (string) max(1, (int) floor($seconds / 86400)) . 'd';
    }
    /**
     * Update course progress.
     *
     * @param $user_id user_id.
     * @param int $courseId The course ID.
     * @param int $chapterId The chapter ID.
     * @param int $lessonId The lesson or quiz ID.
     * @param string $type The type of content ('lesson' or 'quiz').
     */
    private function updateCourseProgress($user_id, int $course_id, int $chapter_id, int $lesson_id, string $type): void {
        // Reset current progress for the course
        CourseProgress::where('course_id', $course_id)->update(['current' => 0]);

        // Update or create progress for the current lesson or quiz
        CourseProgress::updateOrCreate(
            [
                'user_id'    => $user_id,
                'course_id'  => $course_id,
                'chapter_id' => $chapter_id,
                'lesson_id'  => $lesson_id,
                'type'       => $type,
            ],
            ['current' => 1]
        );
    }

    //pdf download routes
    public function downloadCertificate(string $slug) {
        $user = auth('sanctum')->user();
        if (!$user) {
            return response()->json(['status' => 'error', 'message' => 'UnAuthenticated'], 401);
        }
        $certificate = CertificateBuilder::first();
        $certificateItems = CertificateBuilderItem::all();
        $course = Course::withTrashed()->whereSlug($slug)->first();

        $courseLectureCount = CourseChapterItem::whereHas('chapter', function ($q) use ($course) {
            $q->where('course_id', $course->id);
        })->count();

        $courseLectureCompletedByUser = CourseProgress::where('user_id', $user->id)
            ->where('course_id', $course->id)->where('watched', 1)->latest();

        $completed_date = formatDate($courseLectureCompletedByUser->first()?->created_at);

        $courseLectureCompletedByUser = CourseProgress::where('user_id', $user->id)
            ->where('course_id', $course->id)->where('watched', 1)->count();

        $courseCompletedPercent = $courseLectureCount > 0 ? ($courseLectureCompletedByUser / $courseLectureCount) * 100 : 0;

        if ($courseCompletedPercent != 100) {
            return abort(404);
        }

        $html = view('frontend.student-dashboard.certificate.index', compact('certificateItems', 'certificate'))->render();

        $html = str_replace('[student_name]', $user->name, $html);
        $html = str_replace('[platform_name]', cache()->get('setting')->app_name, $html);
        $html = str_replace('[course]', $course->title, $html);
        $html = str_replace('[date]', formatDate($completed_date), $html);
        $html = str_replace('[instructor_name]', $course->instructor->first_name ?? $course->instructor->name, $html);

        // Initialize Dompdf
        $dompdf = new Dompdf(array('enable_remote' => true));

        // Load HTML content
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');

        $dompdf->render();
        return $dompdf->stream("certificate.pdf");
    }
    public function downloadInvoice(string $invoice_id) {
        $user = auth('sanctum')->user();
        if (!$user) {
            return response()->json(['status' => 'error', 'message' => 'UnAuthenticated'], 401);
        }
        $order = Order::where('invoice_id', $invoice_id)->where('buyer_id', $user->id)->first();
        if ($order) {
            $html = view('frontend.student-dashboard.order.invoice', compact('order'))->render();

            // Initialize Dompdf
            $dompdf = new Dompdf(array('enable_remote' => true));

            // Load HTML content
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'portrait');

            $dompdf->render();
            return $dompdf->stream("invoice-{$invoice_id}.pdf");

        } else {
            return response()->json(['status' => 'error', 'message' => 'Not Found!'], 404);
        }
    }
}
