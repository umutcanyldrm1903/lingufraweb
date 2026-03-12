<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\InstructorAvailability;
use App\Models\StudentLiveLesson;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class InstructorScheduleController extends Controller
{
    public function index(Request $request): View
    {
        $instructorId = auth()->id();

        $start = $request->query('start');
        $weekStart = $start ? Carbon::parse($start) : now();
        $weekStart = $weekStart->startOfWeek(Carbon::MONDAY);
        $weekEnd = $weekStart->copy()->endOfWeek(Carbon::SUNDAY);

        $lessons = StudentLiveLesson::query()
            ->with(['student:id,name,image'])
            ->where('instructor_id', $instructorId)
            ->whereBetween('start_time', [$weekStart, $weekEnd])
            ->orderBy('start_time')
            ->get();

        $lessonsByDate = $lessons->groupBy(function (StudentLiveLesson $lesson) {
            return $lesson->start_time->format('Y-m-d');
        });

        $availabilities = InstructorAvailability::query()
            ->where('instructor_id', $instructorId)
            ->orderBy('day_of_week')
            ->orderBy('start_time')
            ->get();

        $availabilityByDay = $availabilities->groupBy('day_of_week');

        return view('frontend.instructor-dashboard.schedule.index', [
            'weekStart' => $weekStart,
            'weekEnd' => $weekEnd,
            'lessonsByDate' => $lessonsByDate,
            'availabilityByDay' => $availabilityByDay,
            'availabilities' => $availabilities,
        ]);
    }

    public function storeAvailability(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'day_of_week' => ['required', 'integer', 'between:0,6'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        InstructorAvailability::create([
            'instructor_id' => auth()->id(),
            'day_of_week' => (int) $validated['day_of_week'],
            'start_time' => $validated['start_time'],
            'end_time' => $validated['end_time'],
            'is_active' => (bool) ($validated['is_active'] ?? true),
        ]);

        setFormTabStep('profile_tab', 'schedule');

        return redirect()->back()->with([
            'messege' => __('Schedule updated.'),
            'alert-type' => 'success',
        ]);
    }

    public function destroyAvailability(InstructorAvailability $availability): RedirectResponse
    {
        if ((int) $availability->instructor_id !== (int) auth()->id()) {
            return redirect()->back()->with([
                'messege' => __('This slot does not belong to you.'),
                'alert-type' => 'error',
            ]);
        }

        $availability->delete();

        setFormTabStep('profile_tab', 'schedule');

        return redirect()->back()->with([
            'messege' => __('Slot removed.'),
            'alert-type' => 'success',
        ]);
    }

    public function updateAvailability(Request $request): RedirectResponse
    {
        setFormTabStep('profile_tab', 'schedule');

        $rawSlots = $request->input('slots', []);
        $slots = is_array($rawSlots) ? $rawSlots : [];
        $normalized = [];

        foreach ($slots as $slot) {
            if (!is_string($slot)) {
                continue;
            }

            $parts = array_map('trim', explode('|', $slot));
            if (count($parts) !== 3) {
                continue;
            }

            [$day, $start, $end] = $parts;
            $day = (int) $day;

            if ($day < 0 || $day > 6) {
                continue;
            }

            if (!preg_match('/^\d{2}:\d{2}$/', $start) || !preg_match('/^\d{2}:\d{2}$/', $end)) {
                continue;
            }

            $startMinutes = ((int) substr($start, 0, 2) * 60) + (int) substr($start, 3, 2);
            $endMinutes = ((int) substr($end, 0, 2) * 60) + (int) substr($end, 3, 2);
            if ($endMinutes <= $startMinutes) {
                continue;
            }

            $key = $day . '|' . $start . '|' . $end;
            $normalized[$key] = [
                'instructor_id' => auth()->id(),
                'day_of_week' => $day,
                'start_time' => $start,
                'end_time' => $end,
                'is_active' => true,
            ];
        }

        DB::transaction(function () use ($normalized) {
            InstructorAvailability::where('instructor_id', auth()->id())->delete();

            foreach ($normalized as $slot) {
                InstructorAvailability::create($slot);
            }
        });

        return redirect()->back()->with([
            'messege' => __('Schedule updated.'),
            'alert-type' => 'success',
        ]);
    }
}
