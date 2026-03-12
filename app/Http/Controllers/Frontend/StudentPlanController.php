<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class StudentPlanController extends Controller
{
    public function index(): RedirectResponse
    {
        return redirect()->to(route('student.dashboard') . '#student-plans');
    }

    public function purchase(Request $request): RedirectResponse
    {
        return app(StudentDashboardController::class)->purchasePlan($request);
    }

    public function purchasePlan(Request $request): RedirectResponse
    {
        return $this->purchase($request);
    }

    public function __invoke(Request $request): RedirectResponse
    {
        return $this->purchase($request);
    }

    public function __call(string $name, array $arguments): RedirectResponse
    {
        return redirect()->route('student.dashboard')->with([
            'messege' => __('Not Found!'),
            'alert-type' => 'error',
        ]);
    }
}
