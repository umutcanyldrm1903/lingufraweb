<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\StudentLiveLesson;
use App\Models\StudentLiveLessonAttendance;
use App\Models\UserPlan;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class InstructorReportController extends Controller
{
    public function index(): View
    {
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
            $status = $this->resolveStatus($lesson, $attendanceMap->get($lesson->id));
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

        $monthly = $lessons->groupBy(function (StudentLiveLesson $lesson) {
            return $lesson->start_time ? $lesson->start_time->format('Y-m') : 'unknown';
        })->map(function ($group) use ($attendanceMap) {
            $completed = 0;
            $noShow = 0;
            foreach ($group as $lesson) {
                $status = $this->resolveStatus($lesson, $attendanceMap->get($lesson->id));
                if ($status === 'completed' || $status === 'late') {
                    $completed++;
                } elseif ($status === 'no_show') {
                    $noShow++;
                }
            }
            return [
                'total' => $group->count(),
                'completed' => $completed,
                'no_show' => $noShow,
            ];
        })->sortKeysDesc();

        return view('frontend.instructor-dashboard.reports.index', [
            'metrics' => $metrics,
            'studentsCount' => $studentsCount,
            'monthly' => $monthly,
        ]);
    }

    private function resolveStatus(StudentLiveLesson $lesson, $attendanceGroup): string
    {
        if ($lesson->status && $lesson->status !== 'scheduled') {
            return $lesson->status;
        }

        if (!$lesson->start_time) {
            return 'scheduled';
        }

        if ($lesson->start_time->isFuture()) {
            return 'scheduled';
        }

        if ($attendanceGroup && $attendanceGroup->isNotEmpty()) {
            $joinedAt = $attendanceGroup->first()->joined_at ? Carbon::parse($attendanceGroup->first()->joined_at) : null;
            if ($joinedAt && $joinedAt->gt($lesson->start_time->copy()->addMinutes(10))) {
                return 'late';
            }
            return 'completed';
        }

        return 'no_show';
    }
}
