<?php

namespace App\Http\Controllers\Frontend;

use Carbon\Carbon;
use App\Models\Quiz;
use Firebase\JWT\JWT;
use App\Models\Course;
use App\Models\QuizResult;
use App\Models\Announcement;
use App\Models\CourseReview;
use App\Models\JitsiSetting;
use App\Models\QuizQuestion;
use App\Models\LiveLessonAttendance;
use App\Models\StudentLiveLesson;
use App\Models\StudentLiveLessonAttendance;
use App\Models\UserPlan;
use Illuminate\Http\Request;
use App\Models\CourseProgress;
use App\Rules\CustomRecaptcha;
use App\Models\CourseChapterItem;
use App\Models\CourseChapterLesson;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Traits\GenerateSecureLinkTrait;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Modules\Order\app\Models\Enrollment;

class LearningController extends Controller {
    use GenerateSecureLinkTrait;
    function index(string $slug) {
        $user = userAuth();
        $course = Course::active()->with([
            'chapters',
            'chapters.chapterItems',
            'chapters.chapterItems.lesson',
            'chapters.chapterItems.quiz',
        ])->withTrashed()->where('slug', $slug)->whereHas('enrollments', fn($q) => $q->where('user_id', $user->id))->first();
        if(!$course){
            abort(404);
        }
        Session::put('course_slug', $slug);
        Session::put('course_title', $course->title);

        $currentProgress = CourseProgress::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->where('current', 1)
            ->orderBy('id', 'desc')
            ->first();

        $alreadyWatchedLectures = CourseProgress::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->where('type', 'lesson')
            ->where('watched', 1)
            ->pluck('lesson_id')
            ->toArray();

        $alreadyCompletedQuiz = CourseProgress::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->where('type', 'quiz')
            ->where('watched', 1)
            ->pluck('lesson_id')
            ->toArray();

        $announcements = Announcement::where('course_id', $course->id)->orderBy('id', 'desc')->get();

        $courseLectureCount = CourseChapterItem::whereHas('chapter', function ($q) use ($course) {
            $q->where('course_id', $course->id);
        })->count();

        $courseLectureCompletedByUser = CourseProgress::where('user_id', $user->id)
            ->where('course_id', $course->id)->where('watched', 1)->count();
        $courseCompletedPercent = $courseLectureCount > 0 ? ($courseLectureCompletedByUser / $courseLectureCount) * 100 : 0;

        if (!$currentProgress) {
            $lessonId = @$course->chapters?->first()?->chapterItems()?->first()?->lesson->id;
            if ($lessonId) {
                $currentProgress = CourseProgress::create([
                    'user_id'    => $user->id,
                    'course_id'  => $course->id,
                    'chapter_id' => $course->chapters->first()->id,
                    'lesson_id'  => $lessonId,
                    'current'    => 1,
                ]);
            }
        }
        return view('frontend.pages.learning-player.index', compact(
            'course',
            'currentProgress',
            'announcements',
            'courseCompletedPercent',
            'courseLectureCount',
            'courseLectureCompletedByUser',
            'alreadyWatchedLectures',
            'alreadyCompletedQuiz'
        ));
    }

    function getFileInfo(Request $request) {
        // set progress status
        CourseProgress::where('course_id', $request->courseId)->update(['current' => 0]);
        $progress = CourseProgress::updateOrCreate(
            [
                'user_id'    => userAuth()->id,
                'course_id'  => $request->courseId,
                'chapter_id' => $request->chapterId,
                'lesson_id'  => $request->lessonId,
                'type'       => $request->type,
            ],
            [
                'current' => 1,
            ]
        );

        if ($request->type == 'lesson') {
            $fileInfo = array_merge(CourseChapterLesson::select(['id', 'file_path', 'storage', 'file_type', 'downloadable', 'description'])->findOrFail($request->lessonId)->toArray(), ['type' => 'lesson']);
            if (in_array($fileInfo['storage'], ['wasabi', 'aws'])) {
                $fileInfo['file_path'] = Storage::disk($fileInfo['storage'])->temporaryUrl($fileInfo['file_path'], now()->addSeconds(30));
            }
            if($fileInfo['storage'] == 'upload'){
                $fileInfo['file_path'] = $this->generateSecureLink($fileInfo['file_path']);
            }
            return response()->json([
                'file_info' => $fileInfo,
            ]);
        } elseif ($request->type == 'live') {
            $fileInfo = array_merge(
                CourseChapterLesson::with([
                    'course:id,instructor_id,slug',
                    'course.instructor:id',
                    'course.instructor.zoom_credential:id,instructor_id,client_id,client_secret',
                    'course.instructor.jitsi_credential:id,instructor_id,app_id,api_key,permissions',
                    'live:id,lesson_id,start_time,type,meeting_id,password,join_url',
                ])->select([
                    'id', 'course_id', 'chapter_item_id', 'title', 'description',
                    'duration', 'file_path', 'storage', 'file_type', 'downloadable',
                ])->findOrFail($request->lessonId)->toArray(),
                ['type' => 'live']
            );

            $now = Carbon::now();
            $startTime = Carbon::parse($fileInfo['live']['start_time']);
            $endTime = $startTime->clone()->addMinutes($fileInfo['duration']);
            $fileInfo['start_time'] = formattedDateTime($startTime);
            $fileInfo['end_time'] = formattedDateTime($endTime);
            $fileInfo['is_live_now'] = $now->between($startTime, $endTime);

            if ($now->lt($startTime)) {
                $fileInfo['is_live_now'] = 'not_started';
            } elseif ($now->between($startTime, $endTime)) {
                $fileInfo['is_live_now'] = 'started';
            } else {
                $fileInfo['is_live_now'] = 'ended';
            }

            return response()->json([
                'file_info' => $fileInfo,
            ]);
        } elseif ($request->type == 'document') {
            $fileInfo = array_merge(CourseChapterLesson::select(['id', 'file_path', 'storage', 'file_type', 'downloadable', 'description'])->findOrFail($request->lessonId)->toArray(), ['type' => 'document']);
            if ('pdf' == $fileInfo['file_type']) {
                return response()->json([
                    'view'      => view('frontend.pages.learning-player.partials.pdf-viewer', ['file_path' => $fileInfo['file_path']])->render(),
                    'file_info' => $fileInfo,
                ]);
            } elseif ('docx' == $fileInfo['file_type']) {
                return response()->json([
                    'view'      => view('frontend.pages.learning-player.partials.docx-viewer', ['file_path' => $fileInfo['file_path']])->render(),
                    'file_info' => $fileInfo,
                ]);
            } else {
                return response()->json([
                    'file_info' => $fileInfo,
                ]);
            }
        } else {
            $fileInfo = array_merge(Quiz::findOrFail($request->lessonId)->toArray(), ['type' => 'quiz']);

            return response()->json([
                'file_info' => $fileInfo,
            ]);
        }
    }

    function makeLessonComplete(Request $request) {
        $progress = CourseProgress::where(['lesson_id' => $request->lessonId, 'user_id' => userAuth()->id, 'type' => $request->type])->first();
        if ($progress) {
            $progress->watched = $request->status;
            $progress->save();
            return response()->json(['status' => 'success', 'message' => __('Updated successfully.')]);
        } else {
            if ($request->status == 0) {
                return;
            }

            return response()->json(['status' => 'error', 'message' => __('You didnt watched this lesson')]);
        }
    }

    function downloadResource(string $lessonId) {
        $resource = CourseChapterLesson::findOrFail($lessonId);
        if (!\File::exists(public_path($resource->file_path))) {
            return redirect()->back()->with(['alert-type' => 'error', 'messege' => __('Links is broke or some thing went wrong')]);
        }
        return response()->download(public_path($resource->file_path));
    }

    function quizIndex(string $id) {
        $attempt = QuizResult::where('user_id', userAuth()->id)->where('quiz_id', $id)->count();
        $quiz = Quiz::withCount('questions')->findOrFail($id);
        if ($attempt >= $quiz->attempt) {
            return redirect()->route('student.learning.index', Session::get('course_slug'))->with(['alert-type' => 'error', 'messege' => __('You reached maximum attempt')]);
        }

        return view('frontend.pages.learning-player.quiz-index', compact('quiz', 'attempt'));
    }

    function quizStore(Request $request, string $id) {
        $grad = 0;
        $result = [];
        $quiz = Quiz::findOrFail($id);
        foreach ($request->question ?? [] as $key => $questionAns) {
            $question = QuizQuestion::findOrFail($key);
            $answer = $question->answers->where('correct', 1)->pluck('id')->toArray();

            if (in_array($questionAns, $answer)) {
                $grad += $question->grade;
            }
            $result[$key] = [
                "answer"  => $questionAns,
                "correct" => in_array($questionAns, $answer),
            ];
        }

        $quizResult = QuizResult::create([
            'user_id'    => userAuth()->id,
            'quiz_id'    => $id,
            'result'     => json_encode($result),
            'user_grade' => $grad,
            'status'     => $grad >= $quiz->pass_mark ? 'pass' : 'failed',
        ]);
        return redirect()->route('student.quiz.result', ['id' => $id, 'result_id' => $quizResult->id]);
    }

    function quizResult(string $id, string $resultId) {
        $attempt = QuizResult::where('user_id', userAuth()->id)->where('quiz_id', $id)->count();
        $quiz = Quiz::withCount('questions')->findOrFail($id);
        $quizResult = QuizResult::findOrFail($resultId);

        return view('frontend.pages.learning-player.quiz-result', compact('quiz', 'attempt', 'quizResult'));
    }

    function addReview(Request $request) {
        $request->validate([
            'course_id'            => ['required', 'exists:courses,id'],
            'rating'               => ['required', 'integer', 'min:1', 'max:5'],
            'review'               => ['required', 'max: 1000', 'string'],
            'g-recaptcha-response' => Cache::get('setting')->recaptcha_status === 'active' ? ['required', new CustomRecaptcha()] : 'nullable',
        ], [
            'rating.required'               => __('rating filed is required'),
            'rating.integer'                => __('rating have to be an integer'),
            'review.required'               => __('review filed is required'),
            'g-recaptcha-response.required' => __('Please complete the recaptcha to submit the form'),
        ]);

        $review = CourseReview::where(['course_id' => $request->course_id, 'user_id' => userAuth()->id])->first();
        if ($review) {
            return redirect()->back()->with(['alert-type' => 'error', 'messege' => __('Already added review')]);
        }

        CourseReview::create([
            'course_id' => $request->course_id,
            'user_id'   => userAuth()->id,
            'rating'    => $request->rating,
            'review'    => $request->review,
        ]);

        return redirect()->back()->with(['alert-type' => 'success', 'messege' => __('Review added successfully')]);

    }

    function fetchReviews(Request $request, string $courseId) {
        $reviews = CourseReview::where(['course_id' => $courseId, 'status' => 1])->whereHas('course')->whereHas('user')->orderBy('id', 'desc')->paginate(8, ['*'], 'page', $request->page ?? 1);
        return response()->json([
            'view'       => view('frontend.pages.learning-player.partials.review-card', compact('reviews'))->render(),
            'page'       => $request->page,
            'last_page'  => $reviews->lastPage(),
            'data_count' => $reviews->count(),
        ]);
    }

    function liveSession(Request $request, string $slug, string $lesson_id) {
        $user = userAuth();
        $lesson = CourseChapterLesson::select('id', 'course_id', 'chapter_item_id', 'title')->with(['course' => function ($q) {
            $q->select('id', 'instructor_id', 'slug');
        }, 'course.instructor' => function ($q) {
            $q->select('id');
        }, 'course.instructor.zoom_credential' => function ($q) {
            $q->select('id', 'instructor_id', 'client_id', 'client_secret');
        }, 'chapterItem' => function ($q) {
            $q->select('id', 'type');
        }, 'live' => function ($q) {
            $q->select('id', 'lesson_id', 'start_time', 'type', 'meeting_id', 'password', 'join_url');
        }])->findOrFail($lesson_id);

        if (!$lesson->live) {
            return redirect()->back()->with([
                'alert-type' => 'error',
                'messege' => __('Live lesson information is missing.'),
            ]);
        }

        if ($user?->role === 'student') {
            $isEnrolled = Enrollment::query()
                ->where('user_id', $user->id)
                ->where('course_id', $lesson->course_id)
                ->where('has_access', 1)
                ->exists();

            if (!$isEnrolled) {
                return redirect()->route('student.enrolled-courses')->with([
                    'alert-type' => 'error',
                    'messege' => __('You must be enrolled before joining this lesson.'),
                ]);
            }

            if (Schema::hasTable('user_plans')) {
                $creditError = null;

                try {
                    DB::transaction(function () use ($user, $lesson, &$creditError) {
                        $attendance = LiveLessonAttendance::query()
                            ->where('user_id', $user->id)
                            ->where('lesson_id', $lesson->id)
                            ->first();

                        if ($attendance) {
                            return;
                        }

                        $plan = UserPlan::query()
                            ->where('user_id', $user->id)
                            ->lockForUpdate()
                            ->first();

                        if (!$plan || $plan->lessons_remaining <= 0) {
                            $creditError = 'no_credits';
                            return;
                        }

                        LiveLessonAttendance::create([
                            'user_id' => $user->id,
                            'lesson_id' => $lesson->id,
                            'joined_at' => now(),
                        ]);

                        $plan->decrement('lessons_remaining');
                    });
                } catch (\Throwable $e) {
                    report($e);

                    return redirect()->route('student.enrolled-courses')->with([
                        'alert-type' => 'error',
                        'messege' => __('Something went wrong while joining the lesson. Please try again.'),
                    ]);
                }

                if ($creditError === 'no_credits') {
                    return redirect()->route('student.enrolled-courses')->with([
                        'alert-type' => 'error',
                        'messege' => __('No credits remaining. Please purchase a package to join lessons.'),
                    ]);
                }
            }
        }

        if ($lesson->live->type == 'zoom') {
            [$sdkKey, $sdkSecret] = $this->resolveZoomSdkCredentials($lesson->course?->instructor);
            if ($sdkKey === '' || $sdkSecret === '') {
                return redirect()->back()->with([
                    'alert-type' => 'error',
                    'messege' => __('Zoom settings are missing.'),
                ]);
            }

            // Allow forcing attendee mode when host can't start (e.g. already hosting another meeting).
            $role = $request->boolean('as_attendee') ? 0 : ($user?->role === 'student' ? 0 : 1);
            $signature = $this->generateZoomMeetingSdkSignature($sdkKey, $sdkSecret, (string) $lesson->live->meeting_id, $role);
            if ($signature === '') {
                return redirect()->back()->with([
                    'alert-type' => 'error',
                    'messege' => __('Zoom settings are missing.'),
                ]);
            }

            return view('frontend.pages.learning-player.partials.live.zoom', compact('lesson', 'sdkKey', 'signature', 'role'));
        } else {
            $jitsi_credential = JitsiSetting::where('instructor_id',$lesson->course->instructor_id)->first();
            if($jitsi_credential){
                $jwt = $this->generateJwtToken($jitsi_credential);
                $roomName = "{$jitsi_credential->app_id}/{$lesson->live->meeting_id}";
                return view('frontend.pages.learning-player.partials.live.jitsi', [
                    'title' => $lesson->title,
                    'jwt' => trim($jwt),
                    'roomName' => $roomName
                ]);
            }
            return back();
        }
    }

    public function studentLiveSession(Request $request, string $lesson_id)
    {
        $user = userAuth();
        $fallbackRoute = $user?->role === 'instructor' ? 'instructor.lessons.index' : 'student.enrolled-courses';
        $liveLesson = StudentLiveLesson::query()
            ->with([
                'instructor:id,name,image',
                'instructor.zoom_credential:id,instructor_id,client_id,client_secret,default_meeting_id,default_meeting_password,default_join_url',
            ])
            ->findOrFail($lesson_id);

        if ($user?->role === 'student' && (int) $liveLesson->student_id !== (int) $user->id) {
            return redirect()->route('student.enrolled-courses')->with([
                'alert-type' => 'error',
                'messege' => __('You do not have access to this lesson.'),
            ]);
        }

        if ($user?->role === 'instructor' && (int) $liveLesson->instructor_id !== (int) $user->id) {
            return redirect()->route('instructor.students.index')->with([
                'alert-type' => 'error',
                'messege' => __('You do not have access to this lesson.'),
            ]);
        }

        if (in_array((string) $liveLesson->status, ['cancelled_teacher', 'cancelled_student'], true)) {
            return redirect()->route($fallbackRoute)->with([
                'alert-type' => 'error',
                'messege' => __('This lesson has been cancelled.'),
            ]);
        }

        if ((string) $liveLesson->status === 'pending' || str_starts_with((string) $liveLesson->meeting_id, 'pending-')) {
            return redirect()->route($fallbackRoute)->with([
                'alert-type' => 'error',
                'messege' => __('Reservation is pending.'),
            ]);
        }

        if ($user?->role === 'student' && Schema::hasTable('user_plans')) {
            if (!Schema::hasTable('student_live_lesson_attendances')) {
                return redirect()->route('student.enrolled-courses')->with([
                    'alert-type' => 'error',
                    'messege' => __('Live lesson attendance table was not found. Please run the migration.'),
                ]);
            }

            $creditError = null;

            try {
                DB::transaction(function () use ($user, $liveLesson, &$creditError) {
                    $attendance = StudentLiveLessonAttendance::query()
                        ->where('student_id', $user->id)
                        ->where('student_live_lesson_id', $liveLesson->id)
                        ->first();

                    if ($attendance) {
                        return;
                    }

                    $plan = UserPlan::query()
                        ->where('user_id', $user->id)
                        ->lockForUpdate()
                        ->first();

                    if (!$plan || $plan->lessons_remaining <= 0) {
                        $creditError = 'no_credits';
                        return;
                    }

                    StudentLiveLessonAttendance::create([
                        'student_id' => $user->id,
                        'student_live_lesson_id' => $liveLesson->id,
                        'joined_at' => now(),
                    ]);

                    $plan->decrement('lessons_remaining');
                });
            } catch (\Throwable $e) {
                report($e);

                return redirect()->route('student.enrolled-courses')->with([
                    'alert-type' => 'error',
                    'messege' => __('Something went wrong while joining the lesson. Please try again.'),
                ]);
            }

            if ($creditError === 'no_credits') {
                return redirect()->route('student.enrolled-courses')->with([
                    'alert-type' => 'error',
                    'messege' => __('No credits remaining. Please purchase a package to join lessons.'),
                ]);
            }
        }

        // If Zoom Meeting SDK keys are configured, we can render the in-page Zoom view.
        // Otherwise, fall back to redirecting users to the Zoom join URL.
        [$sdkKey, $sdkSecret] = $this->resolveZoomSdkCredentials($liveLesson->instructor);
        if ($sdkKey === '' || $sdkSecret === '') {
            $joinUrl = (string) ($liveLesson->join_url ?? '');
            if ($joinUrl === '') {
                $joinUrl = (string) ($liveLesson->instructor?->zoom_credential?->default_join_url ?? '');
            }

            if ($joinUrl === '' && $liveLesson->instructor?->zoom_credential?->default_meeting_id && $liveLesson->instructor?->zoom_credential?->default_meeting_password) {
                $joinUrl = $this->buildZoomJoinUrl(
                    (string) $liveLesson->instructor->zoom_credential->default_meeting_id,
                    (string) $liveLesson->instructor->zoom_credential->default_meeting_password
                );
            }

            if ($joinUrl === '' && $liveLesson->meeting_id && $liveLesson->password) {
                $joinUrl = $this->buildZoomJoinUrl((string) $liveLesson->meeting_id, (string) $liveLesson->password);

                if ($joinUrl !== '') {
                    try {
                        $liveLesson->update(['join_url' => $joinUrl]);
                    } catch (\Throwable $e) {
                        report($e);
                    }
                }
            }

        if ($joinUrl !== '') {
            return redirect()->away($joinUrl);
        }

        return redirect()->route($fallbackRoute)->with([
            'alert-type' => 'error',
            'messege' => __('Zoom settings are missing.'),
        ]);
    }

        // Allow forcing attendee mode when host can't start (e.g. already hosting another meeting).
        $role = $request->boolean('as_attendee') ? 0 : ($user?->role === 'student' ? 0 : 1);
        $signature = $this->generateZoomMeetingSdkSignature($sdkKey, $sdkSecret, (string) $liveLesson->meeting_id, $role);
        if ($signature === '') {
            return redirect()->route($fallbackRoute)->with([
                'alert-type' => 'error',
                'messege' => __('Zoom settings are missing.'),
            ]);
        }

        return view('frontend.pages.learning-player.partials.live.student-zoom', compact('liveLesson', 'sdkKey', 'signature', 'role'));
    }

    /**
     * Generate a JaaS JWT token.
     *
     * @return string
     */
    protected function generateJwtToken($jitsi_credential) {
        $user = userAuth();
        $instructor = $jitsi_credential->instructor_id == $user->id;

        $api_key = $jitsi_credential->api_key;
        $app_id =  $jitsi_credential->app_id; // Your AppID (previously tenant)
        $user_email = $user->name;
        $user_name = $user->name;
        $user_is_moderator = $instructor;
        $user_avatar_url = !empty($user->image) ? asset($user->image) : "";
        $user_id = $user->id;
        $live_streaming_enabled = $instructor;
        $recording_enabled = $instructor;
        $outbound_enabled = false;
        $transcription_enabled = false;
        $exp_delay = 7200;
        $nbf_delay = 0;

        // Read your private key from file
        $private_key = file_get_contents(storage_path("app/user_{$jitsi_credential->instructor_id}/rsb_private_key.pk"));

        $payload = [
            'iss'     => 'chat',
            'aud'     => 'jitsi',
            'exp'     => time() + $exp_delay,
            'nbf'     => time() - $nbf_delay,
            'room'    => '*',
            'sub'     => $app_id,
            'context' => [
                'user'     => [
                    'moderator' => $user_is_moderator ? "true" : "false",
                    'email'     => $user_email,
                    'name'      => $user_name,
                    'avatar'    => $user_avatar_url,
                    'id'        => $user_id,
                ],
                'features' => [
                    'recording'     => $recording_enabled ? "true" : "false",
                    'livestreaming' => $live_streaming_enabled ? "true" : "false",
                    'transcription' => $transcription_enabled ? "true" : "false",
                    'outbound-call' => $outbound_enabled ? "true" : "false",
                ],
            ],
        ];

        return JWT::encode($payload, $private_key, "RS256", $api_key);
    }

    private function resolveZoomSdkCredentials($instructor): array
    {
        // Meeting SDK credentials are application-level (not per instructor).
        $sdkKey = trim((string) config('services.zoom_meeting_sdk.key'));
        $sdkSecret = trim((string) config('services.zoom_meeting_sdk.secret'));

        return [$sdkKey, $sdkSecret];
    }

    private function buildZoomJoinUrl(string $meetingId, string $passcode): string
    {
        $meetingId = preg_replace('/[^0-9]/', '', $meetingId);

        if ($meetingId === '') {
            return '';
        }

        // We can't reliably generate Zoom's `pwd` token without the Zoom API.
        // This URL will prompt for passcode if the meeting is protected.
        return 'https://zoom.us/j/'.$meetingId;
    }

    private function generateZoomMeetingSdkSignature(string $sdkKey, string $sdkSecret, string $meetingNumber, int $role): string
    {
        $meetingNumber = preg_replace('/[^0-9]/', '', trim($meetingNumber));
        if ($meetingNumber === '') {
            return '';
        }

        $sdkKey = trim($sdkKey);
        $sdkSecret = trim($sdkSecret);
        if ($sdkKey === '' || $sdkSecret === '') {
            return '';
        }

        $iat = time() - 30; // allow some clock drift
        $exp = $iat + (2 * 60 * 60);

        $payload = [
            'sdkKey' => $sdkKey,
            // Zoom expects mn/role as numbers (not strings) in the Meeting SDK JWT payload.
            'mn' => (int) $meetingNumber,
            'role' => (int) $role,
            'iat' => (int) $iat,
            'exp' => (int) $exp,
            'appKey' => $sdkKey,
            'tokenExp' => (int) $exp,
        ];

        try {
            // HS256 signature (Meeting SDK signature format)
            return JWT::encode($payload, $sdkSecret, 'HS256');
        } catch (\Throwable $e) {
            report($e);
            return '';
        }
    }
}
