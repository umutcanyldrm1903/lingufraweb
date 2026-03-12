<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\InstructorAvailability;
use App\Models\CourseReview;
use App\Models\Course;
use App\Models\StudentHomework;
use App\Models\StudentHomeworkSubmission;
use App\Models\StudentLibraryItem;
use App\Models\StudentLiveLesson;
use App\Models\StudentLiveLessonAttendance;
use App\Models\User;
use App\Models\UserPlan;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;

class InstructorDashboardController extends Controller
{
    public function instructions(Request $request): JsonResponse
    {
        $language = strtolower((string) $request->query('language', app()->getLocale()));
        if (in_array($language, ['tr', 'en'], true)) {
            app()->setLocale($language);
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'title' => __('Instructor Instructions Title'),
                'subtitle' => __('Instructor Instructions Subtitle'),
                'sections' => [
                    [
                        'title' => __('Instructor Instructions Section 1 Title'),
                        'items' => [
                            __('Instructor Instructions Section 1 Item 1'),
                            __('Instructor Instructions Section 1 Item 2'),
                            __('Instructor Instructions Section 1 Item 3'),
                        ],
                    ],
                    [
                        'title' => __('Instructor Instructions Section 2 Title'),
                        'items' => [
                            __('Instructor Instructions Section 2 Item 1'),
                            __('Instructor Instructions Section 2 Item 2'),
                            __('Instructor Instructions Section 2 Item 3'),
                        ],
                    ],
                    [
                        'title' => __('Instructor Instructions Section 3 Title'),
                        'items' => [
                            __('Instructor Instructions Section 3 Item 1'),
                            __('Instructor Instructions Section 3 Item 2'),
                        ],
                    ],
                    [
                        'title' => __('Instructor Instructions Section 4 Title'),
                        'items' => [
                            __('Instructor Instructions Section 4 Item 1'),
                            __('Instructor Instructions Section 4 Item 2'),
                        ],
                    ],
                ],
            ],
        ], 200);
    }

    public function guide(Request $request): JsonResponse
    {
        $language = strtolower((string) $request->query('language', app()->getLocale()));
        if (in_array($language, ['tr', 'en'], true)) {
            app()->setLocale($language);
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'title' => __('Instructor Guide Title'),
                'subtitle' => __('Instructor Guide Subtitle'),
                'sections' => [
                    [
                        'title' => __('Instructor Guide Section 1 Title'),
                        'type' => 'ordered',
                        'items' => [
                            __('Instructor Guide Section 1 Item 1'),
                            __('Instructor Guide Section 1 Item 2'),
                            __('Instructor Guide Section 1 Item 3'),
                            __('Instructor Guide Section 1 Item 4'),
                        ],
                    ],
                    [
                        'title' => __('Instructor Guide Section 2 Title'),
                        'type' => 'unordered',
                        'items' => [
                            __('Instructor Guide Section 2 Item 1'),
                            __('Instructor Guide Section 2 Item 2'),
                            __('Instructor Guide Section 2 Item 3'),
                            __('Instructor Guide Section 2 Item 4'),
                            __('Instructor Guide Section 2 Item 5'),
                            __('Instructor Guide Section 2 Item 6'),
                        ],
                    ],
                    [
                        'title' => __('Instructor Guide Section 3 Title'),
                        'type' => 'unordered',
                        'items' => [
                            __('Instructor Guide Section 3 Item 1'),
                            __('Instructor Guide Section 3 Item 2'),
                            __('Instructor Guide Section 3 Item 3'),
                        ],
                    ],
                ],
            ],
        ], 200);
    }

    public function availabilities(): JsonResponse
    {
        $items = InstructorAvailability::query()
            ->where('instructor_id', auth()->id())
            ->orderBy('day_of_week')
            ->orderBy('start_time')
            ->get();

        $data = $items->map(function (InstructorAvailability $availability) {
            return [
                'id' => (int) $availability->id,
                'day_of_week' => (int) $availability->day_of_week,
                'start_time' => substr((string) $availability->start_time, 0, 5),
                'end_time' => substr((string) $availability->end_time, 0, 5),
                'is_active' => (bool) $availability->is_active,
            ];
        });

        return response()->json([
            'status' => 'success',
            'data' => $data,
        ], 200);
    }

    public function storeAvailability(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'day_of_week' => ['required', 'integer', 'between:0,6'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $availability = InstructorAvailability::create([
            'instructor_id' => auth()->id(),
            'day_of_week' => (int) $validated['day_of_week'],
            'start_time' => $validated['start_time'],
            'end_time' => $validated['end_time'],
            'is_active' => (bool) ($validated['is_active'] ?? true),
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Schedule updated.',
            'data' => [
                'id' => (int) $availability->id,
            ],
        ], 201);
    }

    public function destroyAvailability(InstructorAvailability $availability): JsonResponse
    {
        if ((int) $availability->instructor_id !== (int) auth()->id()) {
            return response()->json([
                'status' => 'error',
                'message' => 'This slot does not belong to you.',
            ], 403);
        }

        $availability->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Slot removed.',
        ], 200);
    }

    public function students(): JsonResponse
    {
        $instructorId = auth()->id();

        $studentIdsFromLessons = StudentLiveLesson::query()
            ->where('instructor_id', $instructorId)
            ->select('student_id')
            ->distinct()
            ->pluck('student_id')
            ->filter()
            ->values();

        $studentIds = $studentIdsFromLessons;
        if (Schema::hasTable('user_plans') && Schema::hasColumn('user_plans', 'assigned_instructor_id')) {
            $assignedIds = UserPlan::query()
                ->where('assigned_instructor_id', $instructorId)
                ->pluck('user_id')
                ->filter()
                ->values();
            $studentIds = $studentIds
                ->merge($assignedIds)
                ->unique()
                ->values();
        }

        if ($studentIds->isEmpty()) {
            return response()->json([
                'status' => 'success',
                'data' => [],
            ], 200);
        }

        $students = User::query()
            ->select('id', 'name', 'email', 'image', 'phone')
            ->whereIn('id', $studentIds)
            ->get()
            ->map(function (User $student) {
                return [
                    'id' => (int) $student->id,
                    'name' => (string) $student->name,
                    'email' => (string) $student->email,
                    'phone' => (string) $student->phone,
                    'image' => $student->image ? asset($student->image) : null,
                ];
            });

        return response()->json([
            'status' => 'success',
            'data' => $students,
        ], 200);
    }

    public function homeworks(Request $request): JsonResponse
    {
        $language = strtolower((string) $request->query('language', app()->getLocale()));
        $isTr = $language === 'tr';

        $homeworks = StudentHomework::query()
            ->with(['student:id,name,image', 'submission'])
            ->where('instructor_id', auth()->id())
            ->orderByDesc('id')
            ->get();

        $label = function (string $status) use ($isTr): string {
            return match ($status) {
                'submitted' => $isTr ? 'G?nderildi' : 'Submitted',
                'reviewed' => $isTr ? '?ncelendi' : 'Reviewed',
                'needs_revision' => $isTr ? 'Revizyon ?stendi' : 'Revision Requested',
                'archived' => $isTr ? 'Ar?ivlendi' : 'Archived',
                default => $isTr ? 'Bekliyor' : 'Pending',
            };
        };

        $map = function (StudentHomework $homework) use ($label): array {
            $submissionMeta = $homework->submission
                ? StudentHomeworkSubmission::parseNotePayload($homework->submission->note)
                : null;
            $submissionStatus = (string) ($homework->submission?->status ?? '');
            $effectiveStatus = $submissionStatus !== '' ? $submissionStatus : (string) ($homework->status ?? 'open');

            return [
                'id' => (int) $homework->id,
                'title' => (string) ($homework->title ?? ''),
                'description' => (string) ($homework->description ?? ''),
                'status' => $effectiveStatus,
                'status_label' => $label($effectiveStatus),
                'due_at' => optional($homework->due_at)->toDateTimeString(),
                'attachment_name' => (string) ($homework->attachment_name ?? ''),
                'attachment_path' => (string) ($homework->attachment_path ?? ''),
                'student_name' => (string) ($homework->student?->name ?? ''),
                'student_image' => (string) ($homework->student?->image ?? ''),
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

        $active = $homeworks
            ->where('status', '!=', 'archived')
            ->values()
            ->map($map);

        $archived = $homeworks
            ->where('status', 'archived')
            ->values()
            ->map($map);

        return response()->json([
            'status' => 'success',
            'data' => [
                'active' => $active,
                'archived' => $archived,
            ],
        ], 200);
    }

    public function storeHomework(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'student_id' => [
                'required',
                'integer',
                Rule::exists('users', 'id')->where(function ($query) {
                    $query->where('role', 'student');
                }),
            ],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'due_at' => ['nullable', 'date'],
            'attachment' => ['nullable', 'file', 'max:10240'],
        ]);

        $instructorId = (int) auth()->id();
        $studentId = (int) $validated['student_id'];
        if (!$this->isStudentLinkedToInstructor($studentId, $instructorId)) {
            return response()->json([
                'status' => 'error',
                'message' => 'This student is not assigned to you.',
            ], 422);
        }

        $attachmentPath = null;
        $attachmentName = null;
        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $attachmentName = $file->getClientOriginalName();
            $attachmentPath = file_upload($file, 'uploads/student-homeworks/');
        }

        $homework = StudentHomework::create([
            'instructor_id' => $instructorId,
            'student_id' => $studentId,
            'title' => (string) $validated['title'],
            'description' => (string) ($validated['description'] ?? ''),
            'due_at' => $validated['due_at'] ?? null,
            'attachment_path' => $attachmentPath,
            'attachment_name' => $attachmentName,
            'status' => 'open',
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Homework created.',
            'data' => [
                'id' => (int) $homework->id,
            ],
        ], 201);
    }

    public function updateHomework(Request $request, StudentHomework $homework): JsonResponse
    {
        if ((int) $homework->instructor_id !== (int) auth()->id()) {
            return response()->json([
                'status' => 'error',
                'message' => 'This homework does not belong to you.',
            ], 403);
        }

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'due_at' => ['nullable', 'date'],
            'status' => ['nullable', Rule::in(['open', 'submitted', 'archived'])],
            'attachment' => ['nullable', 'file', 'max:10240'],
        ]);

        $homework->title = (string) $validated['title'];
        $homework->description = (string) ($validated['description'] ?? '');
        $homework->due_at = $validated['due_at'] ?? null;
        if (!empty($validated['status'])) {
            $homework->status = (string) $validated['status'];
        }

        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $homework->attachment_name = $file->getClientOriginalName();
            $homework->attachment_path = file_upload($file, 'uploads/student-homeworks/');
        }

        $homework->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Homework updated.',
        ], 200);
    }

    public function archiveHomework(StudentHomework $homework): JsonResponse
    {
        if ((int) $homework->instructor_id !== (int) auth()->id()) {
            return response()->json([
                'status' => 'error',
                'message' => 'This homework does not belong to you.',
            ], 403);
        }

        $homework->status = 'archived';
        $homework->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Homework archived.',
        ], 200);
    }

    public function reviewHomework(Request $request, StudentHomework $homework): JsonResponse
    {
        if ((int) $homework->instructor_id !== (int) auth()->id()) {
            return response()->json([
                'status' => 'error',
                'message' => 'This homework does not belong to you.',
            ], 403);
        }

        $submission = $homework->submission;
        if (!$submission) {
            return response()->json([
                'status' => 'error',
                'message' => 'No submission found for this homework.',
            ], 404);
        }

        $validated = $request->validate([
            'status' => ['required', Rule::in(['submitted', 'reviewed', 'needs_revision'])],
            'instructor_note' => ['nullable', 'string'],
        ]);

        $existingNotePayload = StudentHomeworkSubmission::parseNotePayload($submission->note);
        $reviewedAt = in_array($validated['status'], ['reviewed', 'needs_revision'], true)
            ? now()
            : null;

        $submission->status = (string) $validated['status'];
        $submission->note = StudentHomeworkSubmission::buildNotePayload(
            $existingNotePayload['student_note'] ?? '',
            $validated['instructor_note'] ?? $existingNotePayload['instructor_note'] ?? '',
            $reviewedAt,
        );
        $submission->save();

        $submissionMeta = StudentHomeworkSubmission::parseNotePayload($submission->note);

        return response()->json([
            'status' => 'success',
            'message' => 'Homework review updated.',
            'data' => [
                'status' => (string) $submission->status,
                'student_note' => (string) ($submissionMeta['student_note'] ?? ''),
                'instructor_note' => (string) ($submissionMeta['instructor_note'] ?? ''),
                'reviewed_at' => optional($submissionMeta['reviewed_at'] ?? null)->toDateTimeString(),
            ],
        ], 200);
    }

    public function library(): JsonResponse
    {
        $items = StudentLibraryItem::query()
            ->with('student:id,name')
            ->where('instructor_id', auth()->id())
            ->orderByDesc('id')
            ->get();

        $categories = $items
            ->pluck('category')
            ->filter()
            ->unique()
            ->values();

        $mappedItems = $items->map(function (StudentLibraryItem $item) {
            return [
                'id' => (int) $item->id,
                'category' => (string) ($item->category ?? ''),
                'title' => (string) ($item->title ?? ''),
                'description' => (string) ($item->description ?? ''),
                'file_name' => (string) ($item->file_name ?? ''),
                'file_type' => (string) ($item->file_type ?? ''),
                'file_path' => (string) ($item->file_path ?? ''),
                'file_url' => $item->file_path ? asset($item->file_path) : '',
                'student_name' => (string) ($item->student?->name ?? ''),
                'created_at' => optional($item->created_at)->toDateTimeString(),
            ];
        });

        return response()->json([
            'status' => 'success',
            'data' => [
                'categories' => $categories,
                'items' => $mappedItems,
            ],
        ], 200);
    }

    public function storeLibrary(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'student_id' => [
                'required',
                'integer',
                Rule::exists('users', 'id')->where(function ($query) {
                    $query->where('role', 'student');
                }),
            ],
            'category' => ['required', 'string', 'max:80'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'file' => ['nullable', 'file', 'max:20480'],
        ]);

        $instructorId = (int) auth()->id();
        $studentId = (int) $validated['student_id'];
        if (!$this->isStudentLinkedToInstructor($studentId, $instructorId)) {
            return response()->json([
                'status' => 'error',
                'message' => 'This student is not assigned to you.',
            ], 422);
        }

        $filePath = '';
        $fileName = '';
        $fileType = '';
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $fileName = (string) $file->getClientOriginalName();
            $filePath = (string) file_upload($file, 'uploads/student-library/');
            $fileType = strtolower((string) $file->getClientOriginalExtension());
        }

        $item = StudentLibraryItem::create([
            'instructor_id' => $instructorId,
            'student_id' => $studentId,
            'category' => (string) $validated['category'],
            'title' => (string) $validated['title'],
            'description' => (string) ($validated['description'] ?? ''),
            'file_path' => $filePath,
            'file_name' => $fileName,
            'file_type' => $fileType,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Library item uploaded.',
            'data' => [
                'id' => (int) $item->id,
            ],
        ], 201);
    }

    public function updateLibrary(Request $request, StudentLibraryItem $item): JsonResponse
    {
        if ((int) $item->instructor_id !== (int) auth()->id()) {
            return response()->json([
                'status' => 'error',
                'message' => 'This item does not belong to you.',
            ], 403);
        }

        $validated = $request->validate([
            'category' => ['required', 'string', 'max:80'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'file' => ['nullable', 'file', 'max:20480'],
        ]);

        $item->category = (string) $validated['category'];
        $item->title = (string) $validated['title'];
        $item->description = (string) ($validated['description'] ?? '');

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $item->file_name = (string) $file->getClientOriginalName();
            $item->file_path = (string) file_upload($file, 'uploads/student-library/');
            $item->file_type = strtolower((string) $file->getClientOriginalExtension());
        }

        $item->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Library item updated.',
        ], 200);
    }

    public function destroyLibrary(StudentLibraryItem $item): JsonResponse
    {
        if ((int) $item->instructor_id !== (int) auth()->id()) {
            return response()->json([
                'status' => 'error',
                'message' => 'This item does not belong to you.',
            ], 403);
        }

        $item->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Library item removed.',
        ], 200);
    }

    public function reports(Request $request): JsonResponse
    {
        $language = strtolower((string) $request->query('language', app()->getLocale()));
        $isTr = $language === 'tr';
        $instructorId = auth()->id();

        $lessons = StudentLiveLesson::query()
            ->where('instructor_id', $instructorId)
            ->orderByDesc('start_time')
            ->get();

        $attendanceMap = StudentLiveLessonAttendance::query()
            ->whereIn('student_live_lesson_id', $lessons->pluck('id'))
            ->get()
            ->groupBy('student_live_lesson_id');

        $metrics = [
            'total_lessons' => $lessons->count(),
            'upcoming_lessons' => $lessons->where('start_time', '>=', now())->count(),
            'cancelled_by_teacher' => $lessons->where('status', 'cancelled_teacher')->count(),
            'cancelled_by_student' => $lessons->where('status', 'cancelled_student')->count(),
            'completed' => 0,
            'no_show' => 0,
            'late' => 0,
        ];

        foreach ($lessons as $lesson) {
            $status = $this->resolveLessonStatus($lesson, $attendanceMap->get($lesson->id));
            if ($status === 'completed') {
                $metrics['completed']++;
            } elseif ($status === 'late') {
                $metrics['late']++;
            } elseif ($status === 'no_show') {
                $metrics['no_show']++;
            }
        }

        $studentsCount = 0;
        if (Schema::hasTable('user_plans') && Schema::hasColumn('user_plans', 'assigned_instructor_id')) {
            $studentsCount = UserPlan::query()
                ->where('assigned_instructor_id', $instructorId)
                ->count();
        }

        $monthly = $lessons
            ->groupBy(function (StudentLiveLesson $lesson) {
                return $lesson->start_time ? $lesson->start_time->format('Y-m') : 'unknown';
            })
            ->map(function ($group) use ($attendanceMap) {
                $completed = 0;
                $late = 0;
                $noShow = 0;

                foreach ($group as $lesson) {
                    $status = $this->resolveLessonStatus($lesson, $attendanceMap->get($lesson->id));
                    if ($status === 'completed') {
                        $completed++;
                    } elseif ($status === 'late') {
                        $late++;
                    } elseif ($status === 'no_show') {
                        $noShow++;
                    }
                }

                return [
                    'total' => $group->count(),
                    'completed' => $completed,
                    'late' => $late,
                    'no_show' => $noShow,
                ];
            })
            ->sortKeysDesc()
            ->map(function ($row, $month) {
                return [
                    'month' => (string) $month,
                    'total' => (int) ($row['total'] ?? 0),
                    'completed' => (int) ($row['completed'] ?? 0),
                    'late' => (int) ($row['late'] ?? 0),
                    'no_show' => (int) ($row['no_show'] ?? 0),
                ];
            })
            ->values();

        return response()->json([
            'status' => 'success',
            'data' => [
                'title' => $isTr ? 'Raporlar' : 'Reports',
                'subtitle' => $isTr
                    ? 'Ders performansini ve devam durumunu takip et.'
                    : 'Track your lesson performance and attendance.',
                'metrics' => $metrics,
                'students_count' => (int) $studentsCount,
                'monthly' => $monthly,
            ],
        ], 200);
    }

    public function dashboard(): JsonResponse
    {
        $instructorId = auth()->id();
        $defaultDurationMinutes = (int) config('student_plans.default_lesson_duration', 40);

        $totalLessons = StudentLiveLesson::query()
            ->where('instructor_id', $instructorId)
            ->count();

        $upcomingLessons = StudentLiveLesson::query()
            ->with(['student:id,name'])
            ->where('instructor_id', $instructorId)
            ->where('start_time', '>=', now())
            ->orderBy('start_time')
            ->limit(10)
            ->get();

        $upcomingCount = $upcomingLessons->count();

        $activeStudents = StudentLiveLesson::query()
            ->where('instructor_id', $instructorId)
            ->distinct('student_id')
            ->count('student_id');

        $avgRating = CourseReview::query()
            ->join('courses', 'course_reviews.course_id', '=', 'courses.id')
            ->where('courses.instructor_id', $instructorId)
            ->avg('course_reviews.rating');

        $data = [
            'name' => (string) auth()->user()->name,
            'stats' => [
                'total_lessons' => (int) $totalLessons,
                'upcoming_lessons' => (int) $upcomingCount,
                'active_students' => (int) $activeStudents,
                'avg_rating' => $avgRating ? round((float) $avgRating, 1) : 0.0,
            ],
            'upcoming' => $upcomingLessons->map(function (StudentLiveLesson $lesson) use ($defaultDurationMinutes) {
                $endTime = $lesson->start_time ? $lesson->start_time->copy()->addMinutes($defaultDurationMinutes) : null;
                return [
                    'id' => (int) $lesson->id,
                    'title' => (string) ($lesson->title ?? ''),
                    'student_name' => (string) ($lesson->student?->name ?? ''),
                    'start_time' => $lesson->start_time?->toIso8601String(),
                    'end_time' => $endTime?->toIso8601String(),
                    'duration_minutes' => $defaultDurationMinutes,
                    'join_url' => $lesson->join_url ? (string) $lesson->join_url : null,
                    'meeting_id' => (string) ($lesson->meeting_id ?? ''),
                    'password' => (string) ($lesson->password ?? ''),
                    'status' => (string) ($lesson->status ?? 'scheduled'),
                ];
            }),
        ];

        return response()->json([
            'status' => 'success',
            'data' => $data,
        ], 200);
    }

    public function lessons(): JsonResponse
    {
        $instructorId = auth()->id();
        $defaultDurationMinutes = (int) config('student_plans.default_lesson_duration', 40);

        $baseQuery = StudentLiveLesson::query()
            ->with(['student:id,name'])
            ->where('instructor_id', $instructorId)
            ->orderBy('start_time');

        $upcoming = (clone $baseQuery)
            ->where('start_time', '>=', now())
            ->get();

        $past = (clone $baseQuery)
            ->where('start_time', '<', now())
            ->orderByDesc('start_time')
            ->get();

        $map = function (StudentLiveLesson $lesson) use ($defaultDurationMinutes) {
            $endTime = $lesson->start_time ? $lesson->start_time->copy()->addMinutes($defaultDurationMinutes) : null;
            return [
                'id' => (int) $lesson->id,
                'title' => (string) ($lesson->title ?? ''),
                'student_name' => (string) ($lesson->student?->name ?? ''),
                'start_time' => $lesson->start_time?->toIso8601String(),
                'end_time' => $endTime?->toIso8601String(),
                'duration_minutes' => $defaultDurationMinutes,
                'join_url' => $lesson->join_url ? (string) $lesson->join_url : null,
                'meeting_id' => (string) ($lesson->meeting_id ?? ''),
                'password' => (string) ($lesson->password ?? ''),
                'status' => (string) ($lesson->status ?? 'scheduled'),
            ];
        };

        return response()->json([
            'status' => 'success',
            'data' => [
                'upcoming' => $upcoming->map($map),
                'past' => $past->map($map),
            ],
        ], 200);
    }

    private function resolveLessonStatus(StudentLiveLesson $lesson, $attendanceGroup): string
    {
        if ($lesson->status && $lesson->status !== 'scheduled') {
            return (string) $lesson->status;
        }

        if (!$lesson->start_time) {
            return 'scheduled';
        }

        if ($lesson->start_time->isFuture()) {
            return 'scheduled';
        }

        if ($attendanceGroup && $attendanceGroup->isNotEmpty()) {
            $joinedAt = $attendanceGroup->first()->joined_at
                ? Carbon::parse($attendanceGroup->first()->joined_at)
                : null;

            if ($joinedAt && $joinedAt->gt($lesson->start_time->copy()->addMinutes(10))) {
                return 'late';
            }

            return 'completed';
        }

        return 'no_show';
    }

    public function startLesson(StudentLiveLesson $lesson): JsonResponse
    {
        if ((int) $lesson->instructor_id !== (int) auth()->id()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Permission Denied!',
            ], 403);
        }

        $status = (string) ($lesson->status ?? 'scheduled');
        if (in_array($status, ['cancelled_teacher', 'cancelled_student', 'completed'], true)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Lesson is finished',
            ], 422);
        }

        if (empty($lesson->join_url)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Zoom settings are missing.',
            ], 422);
        }

        $startTime = $lesson->start_time;
        if ($startTime && now()->lessThan($startTime->copy()->subMinutes(15))) {
            return response()->json([
                'status' => 'error',
                'message' => 'Lesson is not started yet',
            ], 422);
        }

        if ($status !== 'started') {
            $lesson->update(['status' => 'started']);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Lesson is started',
            'data' => [
                'id' => (int) $lesson->id,
                'status' => (string) ($lesson->status ?? 'started'),
                'join_url' => (string) $lesson->join_url,
                'meeting_id' => (string) ($lesson->meeting_id ?? ''),
                'password' => (string) ($lesson->password ?? ''),
                'start_time' => $lesson->start_time?->toIso8601String(),
            ],
        ], 200);
    }

    private function isStudentLinkedToInstructor(int $studentId, int $instructorId): bool
    {
        if (Schema::hasTable('user_plans') && Schema::hasColumn('user_plans', 'assigned_instructor_id')) {
            return UserPlan::query()
                ->where('user_id', $studentId)
                ->where('assigned_instructor_id', $instructorId)
                ->exists();
        }

        return StudentLiveLesson::query()
            ->where('student_id', $studentId)
            ->where('instructor_id', $instructorId)
            ->exists();
    }
}
