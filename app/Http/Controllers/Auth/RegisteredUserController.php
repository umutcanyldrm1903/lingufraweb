<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserOnboarding;
use App\Rules\CustomRecaptcha;
use App\Services\MailSenderService;
use App\Services\Referral\ReferralService;
use App\Traits\GetGlobalInformationTrait;
use Cache;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Modules\InstructorRequest\app\Models\InstructorRequest;
use Str;

class RegisteredUserController extends Controller
{
    use GetGlobalInformationTrait;

    public function create(): View
    {
        return view('auth.register');
    }

    public function store(Request $request): RedirectResponse
    {
        $setting = Cache::get('setting');
        $recaptchaActive = data_get($setting, 'recaptcha_status') === 'active';

        $request->validate([
            'role' => ['nullable', Rule::in(['student', 'instructor'])],
            'full_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:'.User::class],
            'phone' => ['nullable', 'string', 'max:50'],
            'referral_code' => ['nullable', 'string', 'max:50'],
            'password' => ['required', 'confirmed', 'min:4', 'max:100'],

            'accept_terms' => ['accepted'],
            'marketing_consent' => ['nullable', 'boolean'],
            'g-recaptcha-response' => $recaptchaActive ? ['required', new CustomRecaptcha()] : '',
        ], [
            'full_name.required' => __('Name is required'),
            'email.required' => __('Email is required'),
            'email.unique' => __('Email already exist'),
            'password.required' => __('Password is required'),
            'password.confirmed' => __('Confirm password does not match'),
            'password.min' => __('You have to provide minimum 4 character password'),
            'g-recaptcha-response.required' => __('Please complete the recaptcha to submit the form'),
            'accept_terms.accepted' => __('Lutfen kullanici sozlesmesini onaylayin.'),
        ]);

        $fullName = trim((string) $request->full_name);
        $role = strtolower(trim((string) $request->input('role', $request->query('role'))));
        \Log::info('register role check', [
            'role_input' => $role,
            'role_query' => $request->query('role'),
            'email' => $request->email,
        ]);
        if (!in_array($role, ['student', 'instructor'], true)) {
            $role = 'student';
        }
        $user = User::create([
            'role' => $role,
            'name' => $fullName,
            'email' => $request->email,
            'status' => 'active',
            'is_banned' => 'no',
            'password' => Hash::make($request->password),
            'verification_token' => Str::random(100),
        ]);

        if ($user) {
            $phone = trim((string) $request->input('phone', ''));
            $user->phone = $phone !== '' ? $phone : null;
            $user->save();

            UserOnboarding::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'marketing_consent' => (bool) $request->marketing_consent,
                    'terms_accepted_at' => now(),
                ]
            );

            // Cowboy-like referral flow:
            // - Every user gets a referral code.
            // - New registrants can enter a referral code to be linked to a referrer.
            try {
                $referral = app(ReferralService::class);
                $referral->ensureReferralCode($user);
                $referral->attachReferrerFromCode(
                    $user,
                    $role === 'student' ? $request->input('referral_code') : null
                );
            } catch (\Throwable $e) {
                report($e);
            }

            if ($role === 'instructor' && class_exists(InstructorRequest::class)) {
                InstructorRequest::firstOrCreate(
                    ['user_id' => $user->id],
                    ['status' => 'pending']
                );
            }
        }

        $settings = cache()->get('setting');
        $marketingSettings = cache()->get('marketing_setting');
        $googleTagEnabled = data_get($settings, 'google_tagmanager_status') === 'active';
        $registerEventEnabled = (bool) data_get($marketingSettings, 'register', false);

        if ($user && $googleTagEnabled && $registerEventEnabled) {
            $register_user = [
                'name' => $user->name,
                'email' => $user->email,
            ];
            session()->put('registerUser', $register_user);
        }

        $mailSent = (new MailSenderService)->sendVerifyMailToUserFromTrait('single_user', $user);
        if (!$mailSent) {
            $notification = __('We could not send the verification email. Please try again later or contact support.');
            $notification = ['messege' => $notification, 'alert-type' => 'error'];
            return redirect()->route('login')->with($notification);
        }

        $notification = __('A varification link has been send to your mail, please verify and enjoy our service');
        $notification = ['messege' => $notification, 'alert-type' => 'success'];

        return redirect()->route('login')->with($notification);

    }

    public function custom_user_verification($token)
    {

        $user = User::where('verification_token', $token)->first();
        if ($user) {

            if ($user->email_verified_at != null) {
                $notification = __('Email already verified');
                $notification = ['messege' => $notification, 'alert-type' => 'error'];

                return redirect()->route('login')->with($notification);
            }

            $user->email_verified_at = date('Y-m-d H:i:s');
            $user->verification_token = null;
            $user->save();

            $notification = __('Verification successful please try to login now');
            $notification = ['messege' => $notification, 'alert-type' => 'success'];
            return redirect()->route('login')->with($notification);
        } else {
            $notification = __('Invalid token');
            $notification = ['messege' => $notification, 'alert-type' => 'error'];

            return redirect()->route('register')->with($notification);
        }
    }
}
