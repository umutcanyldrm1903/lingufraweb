<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\StudentHomework;
use App\Models\StudentLiveLesson;
use App\Models\StudentLiveLessonAttendance;
use App\Models\User;
use App\Models\UserPlan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class InstructorStudentController extends Controller
{
    public function index(Request $request): View
    {
        $hasPlansTable = Schema::hasTable('user_plans');
        $myPlans = collect();
        $filters = [
            'name' => trim((string) $request->query('name', '')),
            'email' => trim((string) $request->query('email', '')),
            'package' => trim((string) $request->query('package', '')),
            'date_from' => trim((string) $request->query('date_from', '')),
            'date_to' => trim((string) $request->query('date_to', '')),
        ];

        if ($hasPlansTable) {
            $parseDate = function (string $value, bool $endOfDay) {
                if ($value === '') {
                    return null;
                }
                try {
                    $date = Carbon::createFromFormat('Y-m-d', $value);
                } catch (\Throwable $e) {
                    return null;
                }
                return $endOfDay ? $date->endOfDay() : $date->startOfDay();
            };

            $fromDate = $parseDate($filters['date_from'], false);
            $toDate = $parseDate($filters['date_to'], true);

            $studentIdQuery = StudentLiveLesson::query()
                ->where('instructor_id', auth()->id())
                ->select('student_id')
                ->distinct();

            if ($filters['name'] !== '') {
                $studentIdQuery->whereHas('student', function ($q) use ($filters) {
                    $q->where('name', 'like', '%' . $filters['name'] . '%');
                });
            }

            if ($filters['email'] !== '') {
                $studentIdQuery->whereHas('student', function ($q) use ($filters) {
                    $q->where('email', 'like', '%' . $filters['email'] . '%');
                });
            }

            $studentIds = $studentIdQuery->pluck('student_id');

            if ($studentIds->isNotEmpty()) {
                $myPlansQuery = UserPlan::query()
                    ->with('user')
                    ->whereIn('user_id', $studentIds)
                    ->whereNotNull('plan_key');

                if ($filters['package'] !== '') {
                    $myPlansQuery->where(function ($q) use ($filters) {
                        $q->where('plan_title', 'like', '%' . $filters['package'] . '%')
                            ->orWhere('plan_key', 'like', '%' . $filters['package'] . '%');
                    });
                }

                if ($fromDate || $toDate) {
                    $myPlansQuery->where(function ($q) use ($fromDate, $toDate) {
                        if ($fromDate && $toDate) {
                            $q->whereBetween('starts_at', [$fromDate, $toDate])
                                ->orWhere(function ($q) use ($fromDate, $toDate) {
                                    $q->whereNull('starts_at')
                                        ->whereBetween('created_at', [$fromDate, $toDate]);
                                });
                        } elseif ($fromDate) {
                            $q->where('starts_at', '>=', $fromDate)
                                ->orWhere(function ($q) use ($fromDate) {
                                    $q->whereNull('starts_at')
                                        ->where('created_at', '>=', $fromDate);
                                });
                        } else {
                            $q->where('starts_at', '<=', $toDate)
                                ->orWhere(function ($q) use ($toDate) {
                                    $q->whereNull('starts_at')
                                        ->where('created_at', '<=', $toDate);
                                });
                        }
                    });
                }

                $myPlans = $myPlansQuery->orderByDesc('id')->get()->unique('user_id')->values();
            }
        }

        return view('frontend.instructor-dashboard.students.index', compact('myPlans', 'hasPlansTable', 'filters'));
    }

    public function assign(Request $request): RedirectResponse
    {
        if (!Schema::hasTable('user_plans') || !Schema::hasColumn('user_plans', 'assigned_instructor_id')) {
            return redirect()->back()->with([
                'messege' => __('Paket tablosu veya assigned_instructor_id kolonu bulunamadi. Migrasyonu calistirin.'),
                'alert-type' => 'error',
            ]);
        }

        $validated = $request->validate([
            'plan_id' => ['required', 'integer', 'exists:user_plans,id'],
        ]);

        $updated = UserPlan::query()
            ->where('id', $validated['plan_id'])
            ->whereNull('assigned_instructor_id')
            ->whereNotNull('plan_key')
            ->update([
                'assigned_instructor_id' => auth()->id(),
                'updated_at' => now(),
            ]);

        if (!$updated) {
            return redirect()->back()->with([
                'messege' => __('Bu ogrenci zaten baska bir ogretmene atanmis olabilir.'),
                'alert-type' => 'error',
            ]);
        }

        return redirect()->back()->with([
            'messege' => __('Ogrenci basariyla atandi.'),
            'alert-type' => 'success',
        ]);
    }

    public function unassign(Request $request): RedirectResponse
    {
        if (!Schema::hasTable('user_plans') || !Schema::hasColumn('user_plans', 'assigned_instructor_id')) {
            return redirect()->back()->with([
                'messege' => __('Paket tablosu veya assigned_instructor_id kolonu bulunamadi. Migrasyonu calistirin.'),
                'alert-type' => 'error',
            ]);
        }

        $validated = $request->validate([
            'plan_id' => ['required', 'integer', 'exists:user_plans,id'],
        ]);

        $updated = UserPlan::query()
            ->where('id', $validated['plan_id'])
            ->where('assigned_instructor_id', auth()->id())
            ->whereNotNull('plan_key')
            ->update([
                'assigned_instructor_id' => null,
                'updated_at' => now(),
            ]);

        if (!$updated) {
            return redirect()->back()->with([
                'messege' => __('Bu ogrenci size ait degil ya da zaten bos.'),
                'alert-type' => 'error',
            ]);
        }

        return redirect()->back()->with([
            'messege' => __('Atama kaldirildi.'),
            'alert-type' => 'success',
        ]);
    }

    public function storeLiveLesson(Request $request): RedirectResponse
    {
        if (!Schema::hasTable('user_plans') || !Schema::hasColumn('user_plans', 'assigned_instructor_id')) {
            return redirect()->back()->with([
                'messege' => __('Paket tablosu veya assigned_instructor_id kolonu bulunamadi. Migrasyonu calistirin.'),
                'alert-type' => 'error',
            ]);
        }

        $validated = $request->validate([
            'student_id' => ['required', 'integer', 'exists:users,id'],
            'title' => ['required', 'string', 'max:255'],
            'start_time' => ['required', 'date'],
            'meeting_id' => ['required', 'string', 'max:255'],
            'password' => ['nullable', 'string', 'max:255'],
            'join_url' => ['nullable', 'url', 'max:2048'],
        ]);

        $plan = UserPlan::query()
            ->where('user_id', $validated['student_id'])
            ->where('assigned_instructor_id', auth()->id())
            ->whereNotNull('plan_key')
            ->first();

        if (!$plan) {
            return redirect()->back()->with([
                'messege' => __('Bu ogrenci size atanmis degil.'),
                'alert-type' => 'error',
            ]);
        }

        StudentLiveLesson::create([
            'instructor_id' => auth()->id(),
            'student_id' => $validated['student_id'],
            'title' => $validated['title'],
            'start_time' => Carbon::parse($validated['start_time']),
            'meeting_id' => $validated['meeting_id'],
            'password' => $validated['password'] ?: null,
            'join_url' => $validated['join_url'] ?: null,
            'type' => 'zoom',
            'status' => 'scheduled',
        ]);

        return redirect()->back()->with([
            'messege' => __('Canli ders olusturuldu.'),
            'alert-type' => 'success',
        ]);
    }

    public function panel(User $student): View
    {
        if (!Schema::hasTable('user_plans')) {
            abort(404);
        }

        $hasRelation = StudentLiveLesson::query()
            ->where('instructor_id', auth()->id())
            ->where('student_id', $student->id)
            ->exists();

        if (!$hasRelation) {
            abort(404);
        }

        $plan = UserPlan::query()
            ->where('user_id', $student->id)
            ->orderByDesc('id')
            ->first();

        $lessons = StudentLiveLesson::query()
            ->where('instructor_id', auth()->id())
            ->where('student_id', $student->id)
            ->orderByDesc('start_time')
            ->get();

        $attendanceMap = StudentLiveLessonAttendance::query()
            ->whereIn('student_live_lesson_id', $lessons->pluck('id'))
            ->get()
            ->keyBy('student_live_lesson_id');

        $lessonStatuses = $lessons->mapWithKeys(function (StudentLiveLesson $lesson) use ($attendanceMap) {
            $attendance = $attendanceMap->get($lesson->id);
            return [$lesson->id => $this->resolveStatus($lesson, $attendance?->joined_at)];
        });

        $homeworks = StudentHomework::query()
            ->where('instructor_id', auth()->id())
            ->where('student_id', $student->id)
            ->orderByDesc('id')
            ->limit(5)
            ->get();

        return view('frontend.instructor-dashboard.students.partials.panel', compact(
            'student',
            'plan',
            'lessons',
            'lessonStatuses',
            'homeworks'
        ));
    }

    private function resolveStatus(StudentLiveLesson $lesson, ?Carbon $joinedAt): array
    {
        if ($lesson->status && $lesson->status !== 'scheduled') {
            return [
                'key' => $lesson->status,
                'label' => $this->formatStatusLabel($lesson->status),
            ];
        }

        if ($lesson->start_time && $lesson->start_time->isFuture()) {
            return ['key' => 'scheduled', 'label' => __('Scheduled')];
        }

        if ($joinedAt) {
            $lateThreshold = $lesson->start_time->copy()->addMinutes(10);
            if (Carbon::parse($joinedAt)->gt($lateThreshold)) {
                return ['key' => 'late', 'label' => __('Late')];
            }
            return ['key' => 'completed', 'label' => __('Completed')];
        }

        return ['key' => 'no_show', 'label' => __('No Show')];
    }

    private function formatStatusLabel(string $status): string
    {
        return match ($status) {
            'cancelled_teacher' => __('Cancelled by Teacher'),
            'cancelled_student' => __('Cancelled by Student'),
            'pending' => __('Pending'),
            'no_show' => __('No Show'),
            'late' => __('Late'),
            'completed' => __('Completed'),
            default => __('Scheduled'),
        };
    }
}
