<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class StudentPlanController extends Controller
{
    public function index(): View
    {
        if (!Schema::hasTable('student_plans')) {
            $tableMissing = true;
            $plans = $this->emptyPaginator(20);

            return view('admin.student-plans.index', compact('plans', 'tableMissing'));
        }

        $tableMissing = false;
        $plans = DB::table('student_plans')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->paginate(20);

        return view('admin.student-plans.index', compact('plans', 'tableMissing'));
    }

    public function create(): View
    {
        if (!Schema::hasTable('student_plans')) {
            abort(404);
        }

        return view('admin.student-plans.create');
    }

    public function store(Request $request): RedirectResponse
    {
        if (!Schema::hasTable('student_plans')) {
            return redirect()->route('admin.student-plans.index')->with([
                'messege' => __('Please create the student_plans table first.'),
                'alert-type' => 'error',
            ]);
        }

        $validated = $this->validatePlan($request);

        $now = now();
        DB::table('student_plans')->insert(array_merge($validated, [
            'created_at' => $now,
            'updated_at' => $now,
        ]));

        return redirect()->route('admin.student-plans.index')->with([
            'messege' => __('Created Successfully'),
            'alert-type' => 'success',
        ]);
    }

    public function edit(string $student_plan): View
    {
        if (!Schema::hasTable('student_plans')) {
            abort(404);
        }

        $plan = DB::table('student_plans')->where('id', $student_plan)->first();
        abort_if(!$plan, 404);

        return view('admin.student-plans.create', [
            'plan' => $plan,
        ]);
    }

    public function update(Request $request, string $student_plan): RedirectResponse
    {
        if (!Schema::hasTable('student_plans')) {
            return redirect()->route('admin.student-plans.index')->with([
                'messege' => __('Please create the student_plans table first.'),
                'alert-type' => 'error',
            ]);
        }

        $plan = DB::table('student_plans')->where('id', $student_plan)->first();
        abort_if(!$plan, 404);

        $validated = $this->validatePlan($request, (int) $student_plan);

        DB::table('student_plans')->where('id', $student_plan)->update(array_merge($validated, [
            'updated_at' => now(),
        ]));

        return redirect()->route('admin.student-plans.index')->with([
            'messege' => __('Updated Successfully'),
            'alert-type' => 'success',
        ]);
    }

    public function destroy(string $student_plan): RedirectResponse
    {
        if (!Schema::hasTable('student_plans')) {
            return redirect()->route('admin.student-plans.index')->with([
                'messege' => __('Please create the student_plans table first.'),
                'alert-type' => 'error',
            ]);
        }

        DB::table('student_plans')->where('id', $student_plan)->delete();

        return redirect()->route('admin.student-plans.index')->with([
            'messege' => __('Deleted Successfully'),
            'alert-type' => 'success',
        ]);
    }

    private function validatePlan(Request $request, ?int $planId = null): array
    {
        $validated = $request->validate([
            'key' => ['required', 'string', 'max:100', Rule::unique('student_plans', 'key')->ignore($planId)],
            'title' => ['required', 'string', 'max:255'],
            'display_title' => ['nullable', 'string', 'max:255'],
            'label' => ['nullable', 'string', 'max:255'],
            'subtitle' => ['nullable', 'string', 'max:255'],
            'tagline' => ['nullable', 'string', 'max:4000'],

            'duration_months' => ['required', 'integer', 'min:0', 'max:1200'],
            'lesson_duration' => ['required', 'integer', 'min:1', 'max:480'],
            'lessons_total' => ['required', 'integer', 'min:0', 'max:1000000'],
            'cancel_total' => ['required', 'integer', 'min:0', 'max:1000000'],

            'old_price' => ['required', 'numeric', 'min:0'],
            'price' => ['required', 'numeric', 'min:0'],

            'sort_order' => ['nullable', 'integer', 'min:0', 'max:1000000'],
        ]);

        $validated['featured'] = $request->boolean('featured');
        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['sort_order'] = (int) ($validated['sort_order'] ?? 0);

        return $validated;
    }

    private function emptyPaginator(int $perPage): LengthAwarePaginator
    {
        $request = request();

        return new LengthAwarePaginator([], 0, $perPage, (int) ($request->input('page', 1)), [
            'path' => $request->url(),
            'query' => $request->query(),
        ]);
    }
}
