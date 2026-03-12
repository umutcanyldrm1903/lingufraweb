<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\StudentLibraryItem;
use App\Models\UserPlan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class InstructorLibraryController extends Controller
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

        $items = StudentLibraryItem::query()
            ->with('student:id,name')
            ->where('instructor_id', $instructorId)
            ->orderByDesc('id')
            ->get();

        return view('frontend.instructor-dashboard.library.index', [
            'students' => $students,
            'items' => $items,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'student_id' => ['required', 'integer', 'exists:users,id'],
            'category' => ['required', 'string', 'max:80'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'file' => ['required', 'file', 'max:20480'],
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

        $file = $request->file('file');
        $fileName = $file->getClientOriginalName();
        $filePath = file_upload($file, 'uploads/student-library/');
        $fileType = $file->getClientOriginalExtension();

        StudentLibraryItem::create([
            'instructor_id' => $instructorId,
            'student_id' => $validated['student_id'],
            'category' => $validated['category'],
            'title' => $validated['title'],
            'description' => $validated['description'] ?: null,
            'file_path' => $filePath,
            'file_name' => $fileName,
            'file_type' => $fileType,
        ]);

        return redirect()->back()->with([
            'messege' => __('Library item uploaded.'),
            'alert-type' => 'success',
        ]);
    }

    public function destroy(StudentLibraryItem $item): RedirectResponse
    {
        if ((int) $item->instructor_id !== (int) auth()->id()) {
            return redirect()->back()->with([
                'messege' => __('This item does not belong to you.'),
                'alert-type' => 'error',
            ]);
        }

        $item->delete();

        return redirect()->back()->with([
            'messege' => __('Library item removed.'),
            'alert-type' => 'success',
        ]);
    }
}
