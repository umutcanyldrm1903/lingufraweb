<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\StudentHomework;
use App\Models\UserPlan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class InstructorHomeworkController extends Controller
{
    public function index(): View
    {
        $instructorId = auth()->id();

        $students = collect();
        if (Schema::hasTable('user_plans') && Schema::hasColumn('user_plans', 'assigned_instructor_id')) {
            $students = UserPlan::query()
                ->with('user:id,name,email')
                ->where('assigned_instructor_id', $instructorId)
                ->get();
        }

        $homeworks = StudentHomework::query()
            ->with(['student:id,name,image', 'submission'])
            ->where('instructor_id', $instructorId)
            ->orderByDesc('id')
            ->get();

        $activeHomeworks = $homeworks->where('status', '!=', 'archived');
        $archivedHomeworks = $homeworks->where('status', 'archived');

        return view('frontend.instructor-dashboard.homeworks.index', [
            'students' => $students,
            'activeHomeworks' => $activeHomeworks,
            'archivedHomeworks' => $archivedHomeworks,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'student_id' => ['required', 'integer', 'exists:users,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'due_at' => ['nullable', 'date'],
            'attachment' => ['nullable', 'file', 'max:10240'],
        ]);

        $instructorId = auth()->id();
        if (Schema::hasTable('user_plans') && Schema::hasColumn('user_plans', 'assigned_instructor_id')) {
            $assigned = UserPlan::query()
                ->where('user_id', $validated['student_id'])
                ->where('assigned_instructor_id', $instructorId)
                ->exists();
            if (!$assigned) {
                return redirect()->back()->with([
                    'messege' => __('This student is not assigned to you.'),
                    'alert-type' => 'error',
                ]);
            }
        }

        $attachmentPath = null;
        $attachmentName = null;
        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $attachmentName = $file->getClientOriginalName();
            $attachmentPath = file_upload($file, 'uploads/student-homeworks/');
        }

        StudentHomework::create([
            'instructor_id' => $instructorId,
            'student_id' => $validated['student_id'],
            'title' => $validated['title'],
            'description' => $validated['description'] ?: null,
            'due_at' => $validated['due_at'] ?? null,
            'attachment_path' => $attachmentPath,
            'attachment_name' => $attachmentName,
            'status' => 'open',
        ]);

        return redirect()->back()->with([
            'messege' => __('Homework created.'),
            'alert-type' => 'success',
        ]);
    }

    public function archive(StudentHomework $homework): RedirectResponse
    {
        if ((int) $homework->instructor_id !== (int) auth()->id()) {
            return redirect()->back()->with([
                'messege' => __('This homework does not belong to you.'),
                'alert-type' => 'error',
            ]);
        }

        $homework->status = 'archived';
        $homework->save();

        return redirect()->back()->with([
            'messege' => __('Homework archived.'),
            'alert-type' => 'success',
        ]);
    }
}
