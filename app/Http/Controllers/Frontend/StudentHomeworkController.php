<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\StudentHomework;
use App\Models\StudentHomeworkSubmission;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StudentHomeworkController extends Controller
{
    public function index(): View
    {
        $studentId = auth()->id();

        $homeworks = StudentHomework::query()
            ->with('submission')
            ->where('student_id', $studentId)
            ->orderByDesc('id')
            ->get();

        $activeHomeworks = $homeworks->where('status', '!=', 'archived');
        $archivedHomeworks = $homeworks->where('status', 'archived');

        return view('frontend.student-dashboard.homeworks.index', [
            'homeworks' => $activeHomeworks,
            'archivedHomeworks' => $archivedHomeworks,
        ]);
    }

    public function submit(Request $request, StudentHomework $homework): RedirectResponse
    {
        if ((int) $homework->student_id !== (int) auth()->id()) {
            return redirect()->back()->with([
                'messege' => __('You cannot submit this homework.'),
                'alert-type' => 'error',
            ]);
        }

        $validated = $request->validate([
            'submission' => ['required', 'file', 'max:10240'],
            'note' => ['nullable', 'string'],
        ]);

        $file = $request->file('submission');
        $fileName = $file->getClientOriginalName();
        $filePath = file_upload($file, 'uploads/student-homeworks/submissions/');

        StudentHomeworkSubmission::updateOrCreate(
            [
                'student_homework_id' => $homework->id,
                'student_id' => auth()->id(),
            ],
            [
                'submission_path' => $filePath,
                'submission_name' => $fileName,
                'note' => $validated['note'] ?? null,
                'submitted_at' => now(),
                'status' => 'submitted',
            ]
        );

        $homework->status = 'submitted';
        $homework->save();

        return redirect()->back()->with([
            'messege' => __('Homework submitted.'),
            'alert-type' => 'success',
        ]);
    }
}
