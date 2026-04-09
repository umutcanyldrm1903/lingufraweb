<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\StudentLiveLesson;
use App\Models\StudentLiveLessonAttendance;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InstructorLessonController extends Controller
{
    public function index(Request $request): View
    {
        $instructorId = auth()->id();
        $month = trim((string) $request->query('month', ''));

        $lessonQuery = StudentLiveLesson::query()
            ->with(['student:id,name,image'])
            ->where('instructor_id', $instructorId);

        if ($month !== '') {
            try {
                $monthStart = Carbon::createFromFormat('Y-m', $month)->startOfMonth();
                $monthEnd = $monthStart->copy()->endOfMonth();
                $lessonQuery->whereBetween('start_time', [$monthStart, $monthEnd]);
            } catch (\Throwable $e) {
                // ignore invalid month filter
            }
        }

        $lessons = $lessonQuery
            ->orderBy('start_time')
            ->paginate(20);

        $attendanceMap = StudentLiveLessonAttendance::query()
            ->whereIn('student_live_lesson_id', $lessons->pluck('id'))
            ->get()
            ->keyBy('student_live_lesson_id');

        $statusMap = $lessons->mapWithKeys(function (StudentLiveLesson $lesson) use ($attendanceMap) {
            $attendance = $attendanceMap->get($lesson->id);
            return [$lesson->id => $this->resolveStatus($lesson, $attendance?->joined_at)];
        });

        return view('frontend.instructor-dashboard.lessons.index', [
            'lessons' => $lessons,
            'statusMap' => $statusMap,
            'attendanceMap' => $attendanceMap,
            'selectedMonth' => $month,
        ]);
    }

    public function cancel(Request $request, StudentLiveLesson $lesson): RedirectResponse
    {
        if ((int) $lesson->instructor_id !== (int) auth()->id()) {
            return redirect()->back()->with([
                'messege' => __('You cannot cancel this lesson.'),
                'alert-type' => 'error',
            ]);
        }

        if ($lesson->status !== 'scheduled') {
            return redirect()->back()->with([
                'messege' => __('Lesson is already finalized.'),
                'alert-type' => 'error',
            ]);
        }

        $lesson->status = 'cancelled_teacher';
        $lesson->cancelled_by = 'instructor';
        $lesson->cancelled_reason = trim((string) $request->input('reason', '')) ?: null;
        $lesson->cancelled_at = now();
        $lesson->save();

        $teacherCancelLimit = (int) config('student_plans.teacher_cancel_limit', 2);
        $cancelCountThisMonth = StudentLiveLesson::query()
            ->where('instructor_id', auth()->id())
            ->where('status', 'cancelled_teacher')
            ->whereBetween('cancelled_at', [now()->startOfMonth(), now()->endOfMonth()])
            ->count();

        $penaltyApplied = false;
        if ($cancelCountThisMonth > $teacherCancelLimit) {
            $studentPlan = \App\Models\UserPlan::query()->currentForUser((int) $lesson->student_id)->first();
            if ($studentPlan) {
                $studentPlan->increment('lessons_remaining');
                $penaltyApplied = true;
            }
        }

        return redirect()->back()->with([
            'messege' => $penaltyApplied
                ? __('Lesson cancelled. Student compensation credit was added because the monthly teacher cancellation limit was exceeded.')
                : __('Lesson cancelled.'),
            'alert-type' => 'success',
        ]);
    }

    public function updateSummary(Request $request, StudentLiveLesson $lesson): RedirectResponse
    {
        if ((int) $lesson->instructor_id !== (int) auth()->id()) {
            return redirect()->back()->with([
                'messege' => __('You cannot update this lesson.'),
                'alert-type' => 'error',
            ]);
        }

        $validated = $request->validate([
            'instructor_summary' => ['nullable', 'string', 'max:3000'],
        ]);

        $lesson->update([
            'instructor_summary' => trim((string) ($validated['instructor_summary'] ?? '')) ?: null,
            'instructor_summary_written_at' => now(),
        ]);

        return redirect()->back()->with([
            'messege' => __('Lesson summary saved.'),
            'alert-type' => 'success',
        ]);
    }

    private function resolveStatus(StudentLiveLesson $lesson, ?Carbon $joinedAt): array
    {
        if ($lesson->status && $lesson->status !== 'scheduled') {
            return [
                'key' => $lesson->status,
                'label' => $this->formatStatusLabel($lesson->status),
            ];
        }

        $now = now();
        $start = $lesson->start_time instanceof Carbon ? $lesson->start_time : Carbon::parse($lesson->start_time);

        if ($start->isFuture()) {
            return ['key' => 'scheduled', 'label' => __('Scheduled')];
        }

        if ($joinedAt) {
            $lateThreshold = $start->copy()->addMinutes(10);
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
