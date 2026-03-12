<?php

namespace App\Http\Controllers\Frontend;

use App\Enums\RedirectType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Frontend\StudentPasswordUpdateRequest;
use App\Http\Requests\Frontend\StudentProfileUpdateRequest;
use App\Traits\RedirectHelperTrait;
use App\Models\InstructorAvailability;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use Modules\Location\app\Models\Country;

class InstructorProfileSettingController extends Controller
{
    use RedirectHelperTrait;

    function index(Request $request): View
    {
        // set profile tab
        if ($request->filled('tab')) {
            $allowedTabs = ['profile', 'schedule', 'email', 'password'];
            $tab = (string) $request->query('tab');
            if (in_array($tab, $allowedTabs, true)) {
                setFormTabStep('profile_tab', $tab);
            }
        }

        if (!session('profile_tab')) {
            setFormTabStep('profile_tab', 'profile');
        }

        $user = Auth::user();
        $countries = Country::where('status', 1)->get();
        $availabilities = InstructorAvailability::query()
            ->where('instructor_id', $user->id)
            ->orderBy('day_of_week')
            ->orderBy('start_time')
            ->get();

        return view('frontend.instructor-dashboard.profile.index', compact(
            'user',
            'availabilities',
            'countries'
        ));
    }

    function updateProfile(StudentProfileUpdateRequest $request): RedirectResponse
    {

        $user = Auth::user();
        // handle image files
        if ($request->hasFile('avatar')) {
            $imagePath = file_upload(file: $request->avatar, optimize: true);
            $user->image = $imagePath;
        }
        if ($request->hasFile('cover')) {
            $imagePath = file_upload(file: $request->cover, optimize: true);
            $user->cover = $imagePath;
        }

        $profileData = (array) ($user->instructor_profile ?? []);
        $profileData['first_name'] = trim((string) $request->input('first_name', ''));
        $profileData['last_name'] = trim((string) $request->input('last_name', ''));
        $profileData['education'] = trim((string) $request->input('education', ''));
        $profileData['university'] = trim((string) $request->input('university', ''));
        $profileData['turkish_level'] = trim((string) $request->input('turkish_level', ''));
        $profileData['experience_years'] = trim((string) $request->input('experience_years', ''));
        $profileData['availability_per_month'] = trim((string) $request->input('availability_per_month', ''));
        $profileData['birth_date'] = trim((string) $request->input('birth_date', ''));
        $profileData['major'] = trim((string) $request->input('major', ''));
        $profileData['identity_number'] = trim((string) $request->input('identity_number', ''));
        $profileData['account_holder_name'] = trim((string) $request->input('account_holder_name', ''));
        $profileData['bank_number'] = trim((string) $request->input('bank_number', ''));
        $profileData['agreement_address'] = trim((string) $request->input('agreement_address', ''));
        $profileData['can_teach'] = array_values(array_filter((array) $request->input('can_teach', []), function ($value) {
            return $value !== null && $value !== '';
        }));
        $profileData['certificates'] = array_values(array_filter((array) $request->input('certificates', []), function ($value) {
            return $value !== null && $value !== '';
        }));
        $profileData['work_type'] = trim((string) $request->input('work_type', ''));
        $profileData['teaching_materials'] = array_values(array_filter((array) $request->input('teaching_materials', []), function ($value) {
            return $value !== null && $value !== '';
        }));

        if ($request->hasFile('intro_video')) {
            $videoPath = 'uploads/instructor-videos/';
            if (!File::exists(public_path($videoPath))) {
                File::makeDirectory(public_path($videoPath), 0755, true);
            }
            $profileData['intro_video'] = file_upload(
                file: $request->intro_video,
                path: $videoPath,
                oldFile: $profileData['intro_video'] ?? ''
            );
        }

        $fullName = trim((string) $request->input('name', ''));
        if ($fullName === '') {
            $fullName = trim($profileData['first_name'] . ' ' . $profileData['last_name']);
        }
        $user->name = $fullName ?: $user->name;
        $user->email = $request->email;
        $user->phone = $request->phone;
        if ($request->has('age')) {
            $user->age = $request->age;
        }
        if ($request->has('gender')) {
            $user->gender = $request->gender;
        }
        $user->country_id = $request->input('country_id');
        $user->address = $request->input('agreement_address');
        $user->job_title = $request->input('major');
        $user->short_bio = $request->short_bio;
        $user->bio = $request->bio;
        $user->instructor_profile = $profileData;
        $user->save();

        return $this->redirectWithMessage(RedirectType::UPDATE->value, 'instructor.setting.index');
    }

    function updatePassword(StudentPasswordUpdateRequest $request): RedirectResponse
    {
        $user = Auth::user();
        $user->password = Hash::make($request->password);
        $user->save();

        return $this->redirectWithMessage(RedirectType::UPDATE->value, 'instructor.setting.index');
    }

    function updateEmail(Request $request): RedirectResponse
    {
        setFormTabStep('profile_tab', 'email');

        $user = Auth::user();
        $validated = $request->validate([
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'current_password' => ['required'],
        ]);

        if (!Hash::check($validated['current_password'], $user->password)) {
            return redirect()->back()->with([
                'messege' => __('Current password does not match'),
                'alert-type' => 'error',
            ]);
        }

        $user->email = $validated['email'];
        $user->save();

        return redirect()->back()->with([
            'messege' => __('Updated successfully.'),
            'alert-type' => 'success',
        ]);
    }

}
