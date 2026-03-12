<?php

namespace App\Http\Controllers\Auth;

use App\Enums\SocialiteDriverType;
use App\Enums\UserStatus;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Traits\NewUserCreateTrait;
use App\Traits\SetConfigTrait;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class SocialiteController extends Controller {
    use NewUserCreateTrait, SetConfigTrait;

    public function __construct() {
        $driver = request('driver', null);
        if ($driver == SocialiteDriverType::FACEBOOK->value) {
            self::setFacebookLoginInfo();
        } elseif ($driver == SocialiteDriverType::GOOGLE->value) {
            self::setGoogleLoginInfo();
        }
    }

    public function redirectToDriver($driver) {
        if (in_array($driver, SocialiteDriverType::getAll())) {
            // When initiated from the mobile app we return an API token via deep-link.
            if (request()->boolean('app')) {
                session()->put('app_social_login', true);
            } else {
                session()->forget('app_social_login');
            }

            return Socialite::driver($driver)->redirect();
        }
        $notification = __('Invalid Social Login Type!');
        $notification = ['messege' => $notification, 'alert-type' => 'error'];

        return redirect()->back()->with($notification);
    }

    public function handleDriverCallback($driver) {
        $isApp = (bool) session()->pull('app_social_login', false);

        if (!in_array($driver, SocialiteDriverType::getAll())) {
            if ($isApp) {
                return $this->appAuthNotification('failed', __('Invalid Social Login Type!'));
            }

            $notification = __('Invalid Social Login Type!');
            $notification = ['messege' => $notification, 'alert-type' => 'error'];
            return redirect()->back()->with($notification);
        }
        try {
            $provider_name = SocialiteDriverType::from($driver)->value;
            $callbackUser = Socialite::driver($provider_name)->stateless()->user();
            $user = User::where('email', $callbackUser->getEmail())->first();
            if ($user) {
                $findDriver = $user
                    ->socialite()
                    ->where(['provider_name' => $provider_name, 'provider_id' => $callbackUser->getId()])
                    ->first();

                if ($findDriver) {
                    if ($user->status == UserStatus::ACTIVE->value) {
                        if ($user->is_banned == UserStatus::UNBANNED->value) {
                            if (app()->isProduction() && $user->email_verified_at == null) {
                                if ($isApp) {
                                    return $this->appAuthNotification('failed', __('Please verify your email'));
                                }

                                $notification = __('Please verify your email');
                                $notification = ['messege' => $notification, 'alert-type' => 'error'];
                                return redirect()->back()->with($notification);
                            }
                            if ($findDriver) {
                                if ($isApp) {
                                    return $this->appAuthNotification('success', __('Logged in successfully.'), $user);
                                }

                                Auth::guard('web')->login($user, true);
                                $notification = __('Logged in successfully.');
                                $notification = ['messege' => $notification, 'alert-type' => 'success'];
                                return redirect()->intended(route('student.dashboard'))->with($notification);
                            }
                        } else {
                            if ($isApp) {
                                return $this->appAuthNotification('failed', __('Inactive account'));
                            }

                            $notification = __('Inactive account');
                            $notification = ['messege' => $notification, 'alert-type' => 'error'];
                            return redirect()->back()->with($notification);
                        }
                    } else {
                        if ($isApp) {
                            return $this->appAuthNotification('failed', __('Inactive account'));
                        }

                        $notification = __('Inactive account');
                        $notification = ['messege' => $notification, 'alert-type' => 'error'];
                        return redirect()->back()->with($notification);
                    }
                } else {
                    $socialite = $this->createNewUser(callbackUser: $callbackUser, provider_name: $provider_name, user: $user);

                    if ($socialite) {
                        if ($isApp) {
                            return $this->appAuthNotification('success', __('Logged in successfully.'), $user);
                        }

                        Auth::guard('web')->login($user, true);
                        $notification = __('Logged in successfully.');
                        $notification = ['messege' => $notification, 'alert-type' => 'success'];
                        return redirect()->intended(route('user.dashboard'))->with($notification);
                    }

                    if ($isApp) {
                        return $this->appAuthNotification('failed', __('Login Failed'));
                    }

                    $notification = __('Login Failed');
                    $notification = ['messege' => $notification, 'alert-type' => 'error'];
                    return redirect()->back()->with($notification);
                }
            } else {
                if ($callbackUser) {
                    $socialite = $this->createNewUser(callbackUser: $callbackUser, provider_name: $provider_name, user: false);
                    if ($socialite) {
                        $user = User::find($socialite->user_id);
                        if ($isApp) {
                            return $this->appAuthNotification('success', __('Logged in successfully.'), $user);
                        }

                        Auth::guard('web')->login($user, true);
                        $notification = __('Logged in successfully.');
                        $notification = ['messege' => $notification, 'alert-type' => 'success'];
                        return redirect()->intended(route('student.dashboard'))->with($notification);
                    }

                    if ($isApp) {
                        return $this->appAuthNotification('failed', __('Login Failed'));
                    }

                    $notification = __('Login Failed');
                    $notification = ['messege' => $notification, 'alert-type' => 'error'];
                    return redirect()->back()->with($notification);
                }

                if ($isApp) {
                    return $this->appAuthNotification('failed', __('Login Failed'));
                }

                $notification = __('Login Failed');
                $notification = ['messege' => $notification, 'alert-type' => 'error'];
                return redirect()->back()->with($notification);
            }

        } catch (\Exception $e) {
            if ($isApp) {
                return $this->appAuthNotification('failed', __('Login Failed'));
            }
            return to_route('login');
        }
    }

    private function appAuthNotification(string $result, string $message, ?User $user = null)
    {
        $deeplink = null;
        if ($result === 'success' && $user) {
            $token = $user->createToken('mobile', ['*'])->plainTextToken;
            $deeplink = 'lingufranca://auth?' . http_build_query([
                'result' => 'success',
                'token' => $token,
                'role' => (string) ($user->role ?? 'student'),
                'user_id' => (string) $user->id,
                'message' => $message,
            ]);
        } else {
            $deeplink = 'lingufranca://auth?' . http_build_query([
                'result' => 'failed',
                'message' => $message,
            ]);
        }

        return view('app_auth_notification', [
            'title' => $result === 'success' ? __('Login Success') : __('Login Failed'),
            'sub_title' => $message,
            'deeplink' => $deeplink,
            'result' => $result,
        ]);
    }
}
