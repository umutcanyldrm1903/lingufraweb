<?php

namespace App\Http\Controllers\Frontend;

use App\Enums\UserStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Frontend\BecomeInstructorStoreRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Modules\InstructorRequest\app\Models\InstructorRequest;
use Modules\InstructorRequest\app\Models\InstructorRequestSetting;
use Modules\PaymentWithdraw\app\Models\WithdrawMethod;

class BecomeInstructorController extends Controller
{

    function index(): View|RedirectResponse
    {
        if ($this->checkIfApproveInstructor()) return to_route('instructor.dashboard');

        $instructorRequestSetting = InstructorRequestSetting::first();
        $withdrawMethods = WithdrawMethod::where('status', 'active')->get();
        return view('frontend.pages.become-instructor', compact('withdrawMethods', 'instructorRequestSetting'));
    }

    function store(BecomeInstructorStoreRequest $request): RedirectResponse
    {
        $user = auth('web')->user();
        $currentStatus = $user->instructorInfo?->status;
        $status = $currentStatus === UserStatus::APPROVED->value
            ? UserStatus::APPROVED->value
            : UserStatus::PENDING->value;
        $applicationLines = [
            'Uzmanlik Alani: '.trim((string) $request->expertise),
            'Deneyim: '.trim((string) $request->experience_years),
            'Ders Dili: '.trim((string) $request->lesson_languages),
            'Musaitlik: '.trim((string) $request->availability),
            'Kendini Tanit: '.trim((string) $request->bio),
        ];
        if ($request->filled('linkedin')) {
            $applicationLines[] = 'LinkedIn/Website: '.trim((string) $request->linkedin);
        }
        if ($request->filled('extra_information')) {
            $applicationLines[] = 'Ek Not: '.trim((string) $request->extra_information);
        }
        $applicationText = implode("\n", $applicationLines);

        $instructorRequest = InstructorRequest::updateOrCreate(
            ['user_id' => $user->id],
            [
                'status' => $status,
                'payout_account' => null,
                'payout_information' => null,
                'extra_information' => $applicationText,
            ]
        );

        if ($request->hasFile('certificate')) {
            $filePath = file_upload($request->certificate);
            $instructorRequest->certificate = $filePath;
            $instructorRequest->save();
        }

        if ($request->hasFile('identity_scan')) {
            $filePath = file_upload($request->identity_scan);
            $instructorRequest->identity_scan = $filePath;
            $instructorRequest->save();
        }

        return redirect()->route('student.dashboard')->with([
            'success' => __('Instructor request submitted successfully we will let you know when your account is approved'),
            'alert-type' => 'success'
        ]);
    }

    function checkIfApproveInstructor(): bool
    {
        return instructorStatus() == UserStatus::APPROVED->value;
    }
}
