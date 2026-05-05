<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use App\Enums\UserStatus;
use Throwable;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Services\MailSenderService;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\PersonalAccessToken;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Firebase\JWT\JWK;
use Firebase\JWT\JWT;
use Modules\GlobalSetting\app\Models\Setting;
use Modules\GlobalSetting\app\Models\MarketingSetting;
use Modules\InstructorRequest\app\Models\InstructorRequest;

class AuthenticatedController extends Controller {
    private const SOCIAL_LOGIN_PROVIDERS = ['google', 'apple'];

    public function register(Request $request): JsonResponse {
        $validator = Validator::make($request->all(), [
            'role' => ['nullable', Rule::in(['student', 'instructor'])],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:'.User::class],
            'phone' => ['nullable', 'string', 'max:50'],
            'password' => ['required', 'confirmed', 'min:4', 'max:100'],
        ], [
            'name.required' => 'Name is required',
            'email.required' => 'Email is required',
            'email.unique' => 'Email already exist',
            'password.required' => 'Password is required',
            'password.confirmed' => 'Confirm password does not match',
            'password.min' => 'You have to provide minimum 4 character password',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => $validator->errors()], 422);
        }

        try {
            DB::beginTransaction();
            $role = strtolower(trim((string) $request->input('role', 'student')));
            if (!in_array($role, ['student', 'instructor'], true)) {
                $role = 'student';
            }

            // Mobile API has no verification flow. Verify immediately so the
            // account can be used right after registration.
            $user = User::create([
                'role'               => $role,
                'name'               => $request->name,
                'email'              => $request->email,
                'status'             => 'active',
                'is_banned'          => 'no',
                'password'           => Hash::make($request->password),
                'verification_token' => null,
                'email_verified_at'  => now(),
            ]);

            $phone = trim((string) $request->input('phone', ''));
            $user->phone = $phone !== '' ? $phone : null;
            $user->save();

            if ($role === 'instructor' && class_exists(InstructorRequest::class)) {
                InstructorRequest::firstOrCreate(
                    ['user_id' => $user->id],
                    ['status' => 'pending']
                );
            }

            DB::commit();

            $google_tagmanager_status = Setting::where('key', 'google_tagmanager_status')->value('value');
            $marketing_setting_register = MarketingSetting::where('key', 'register')->value('value');
            if ($google_tagmanager_status == 'active' && $marketing_setting_register) {
                session()->put('registerUser', [
                    'name'  => $user->name,
                    'email' => $user->email,
                ]);
            }

            try {
                (new MailSenderService)->sendVerifyMailToUserFromTrait('single_user', $user);
            } catch (Throwable $mailException) {
                Log::warning('Mobile register welcome mail failed', [
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'error' => $mailException->getMessage(),
                ]);
            }

            $bearer_token = $user->createToken('mobile-app', ['*'])->plainTextToken;

            return response()->json([
                'status'  => 'success',
                'message' => 'Registered successfully.',
                'bearer_token' => $bearer_token,
                'user_id' => $user->id,
                'role' => $user->role,
                'data' => [
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                ],
            ], 201);

        } catch (Throwable $e) {
            DB::rollBack();
            Log::error('Mobile registration failed', [
                'email' => $request->email,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'status'  => 'error',
                'message' => 'Registration failed. Please try again.',
            ], 500);

        }
    }
    public function socialLogin(Request $request): JsonResponse {
        $validator = Validator::make($request->all(), [
            'provider' => ['required', 'string', Rule::in(self::SOCIAL_LOGIN_PROVIDERS)],
            'id_token' => ['required', 'string'],
            'name' => ['nullable', 'string', 'max:255'],
        ], [
            'provider.required' => 'Provider is required',
            'id_token.required' => 'Social login token is required',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => $validator->errors()], 422);
        }

        $provider = strtolower((string) $request->input('provider'));

        try {
            $identity = match ($provider) {
                'google' => $this->verifyGoogleIdToken((string) $request->input('id_token')),
                'apple' => $this->verifyAppleIdentityToken((string) $request->input('id_token')),
            };

            $user = $this->resolveSocialUser(
                provider: $provider,
                providerId: $identity['provider_id'],
                email: $identity['email'],
                name: trim((string) $request->input('name', '')) ?: $identity['name']
            );

            if ($user->status !== UserStatus::ACTIVE->value) {
                return response()->json(['status' => 'error', 'message' => 'Inactive account'], 403);
            }

            if ($user->is_banned === UserStatus::BANNED->value) {
                return response()->json(['status' => 'error', 'message' => 'Your account has been banned'], 403);
            }

            PersonalAccessToken::where('tokenable_id', $user->id)
                ->where('tokenable_type', User::class)
                ->where('name', 'extra-token')
                ->delete();

            $bearerToken = $user->createToken('mobile-social-login', ['*'])->plainTextToken;

            return response()->json([
                'status' => 'success',
                'message' => 'Logged in successfully.',
                'bearer_token' => $bearerToken,
                'user_id' => $user->id,
                'role' => $user->role ?: 'student',
                'data' => [
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role ?: 'student',
                ],
            ], 200);
        } catch (Throwable $e) {
            Log::warning('Mobile social login failed', [
                'provider' => $provider,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage() ?: 'Social login failed',
            ], 401);
        }
    }

    private function verifyGoogleIdToken(string $idToken): array
    {
        $allowedClientIds = config('services.mobile_social_login.google_client_ids', []);
        if (empty($allowedClientIds)) {
            throw new \RuntimeException('Google login is not configured.');
        }

        $response = Http::timeout(8)->get('https://oauth2.googleapis.com/tokeninfo', [
            'id_token' => $idToken,
        ]);

        if (!$response->ok()) {
            throw new \RuntimeException('Invalid Google login token.');
        }

        $payload = $response->json();
        $audience = (string) ($payload['aud'] ?? '');
        if (!in_array($audience, $allowedClientIds, true)) {
            throw new \RuntimeException('Google login client is not allowed.');
        }

        $emailVerified = $payload['email_verified'] ?? false;
        if (!($emailVerified === true || $emailVerified === 'true' || $emailVerified === '1')) {
            throw new \RuntimeException('Google email is not verified.');
        }

        $email = strtolower(trim((string) ($payload['email'] ?? '')));
        $subject = trim((string) ($payload['sub'] ?? ''));
        if ($email === '' || $subject === '') {
            throw new \RuntimeException('Google login did not return a usable account.');
        }

        return [
            'provider_id' => $subject,
            'email' => $email,
            'name' => trim((string) ($payload['name'] ?? Str::before($email, '@'))),
        ];
    }

    private function verifyAppleIdentityToken(string $idToken): array
    {
        $allowedClientIds = config('services.mobile_social_login.apple_client_ids', []);
        if (empty($allowedClientIds)) {
            throw new \RuntimeException('Apple login is not configured.');
        }

        $decoded = JWT::decode($idToken, JWK::parseKeySet($this->appleJwkSet()));
        $payload = json_decode(json_encode($decoded), true) ?: [];

        if (($payload['iss'] ?? '') !== 'https://appleid.apple.com') {
            throw new \RuntimeException('Invalid Apple login issuer.');
        }

        $audience = $payload['aud'] ?? null;
        $audiences = is_array($audience) ? $audience : [$audience];
        if (empty(array_intersect($audiences, $allowedClientIds))) {
            throw new \RuntimeException('Apple login client is not allowed.');
        }

        $subject = trim((string) ($payload['sub'] ?? ''));
        $email = strtolower(trim((string) ($payload['email'] ?? '')));

        if ($subject === '') {
            throw new \RuntimeException('Apple login did not return a usable account.');
        }

        return [
            'provider_id' => $subject,
            'email' => $email,
            'name' => $email !== '' ? Str::before($email, '@') : 'Apple User',
        ];
    }

    private function appleJwkSet(): array
    {
        $cacheKey = 'mobile_social_login.apple_jwks';
        $cachedKeys = Cache::get($cacheKey);
        if (is_array($cachedKeys) && !empty($cachedKeys['keys'])) {
            return $cachedKeys;
        }

        try {
            $keysResponse = Http::timeout(8)->get('https://appleid.apple.com/auth/keys');
            if ($keysResponse->ok()) {
                $keys = $keysResponse->json();
                if (is_array($keys) && !empty($keys['keys'])) {
                    Cache::put($cacheKey, $keys, now()->addHours(12));
                    return $keys;
                }
            }
        } catch (Throwable $e) {
            Log::warning('Apple login keys request failed', [
                'error' => $e->getMessage(),
            ]);
        }

        $fallbackKeysJson = trim((string) config('services.mobile_social_login.apple_jwks_json', ''));
        if ($fallbackKeysJson !== '') {
            $fallbackKeys = json_decode($fallbackKeysJson, true);
            if (is_array($fallbackKeys) && !empty($fallbackKeys['keys'])) {
                return $fallbackKeys;
            }
        }

        throw new \RuntimeException('Apple login keys could not be loaded.');
    }

    private function resolveSocialUser(string $provider, string $providerId, string $email, string $name): User
    {
        $socialUser = User::whereHas('socialite', function ($query) use ($provider, $providerId) {
            $query->where('provider_name', $provider)
                ->where('provider_id', $providerId);
        })->first();

        if ($socialUser) {
            return $socialUser;
        }

        if ($email === '') {
            throw new \RuntimeException('Social login email is required for first login.');
        }

        return DB::transaction(function () use ($provider, $providerId, $email, $name) {
            $user = User::where('email', $email)->first();

            if (!$user) {
                $user = User::create([
                    'role' => 'student',
                    'name' => $name !== '' ? $name : Str::before($email, '@'),
                    'email' => $email,
                    'status' => UserStatus::ACTIVE->value,
                    'is_banned' => UserStatus::UNBANNED->value,
                    'password' => Hash::make(Str::random(40)),
                    'verification_token' => null,
                    'email_verified_at' => now(),
                ]);
            } elseif (!$user->email_verified_at) {
                $user->email_verified_at = now();
                $user->save();
            }

            $user->socialite()->firstOrCreate(
                [
                    'provider_name' => $provider,
                    'provider_id' => $providerId,
                ],
                [
                    'access_token' => null,
                    'refresh_token' => null,
                ]
            );

            return $user;
        });
    }

    public function forgetPassword(Request $request): JsonResponse {
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'email'],
        ], [
            'email.required' => 'Email is required',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => $validator->errors()], 422);
        }

        $user = User::where('email', $request->email)->first();
        if ($user) {
            $user->forget_password_token = Str::random(100);
            $user->save();
            (new MailSenderService)->sendUserForgetPasswordFromTrait($user);

            return response()->json([
                'status'  => 'success',
                'message' => 'A password reset link has been send to your mail',
            ], 200);

        } else {
            return response()->json(['status' => 'error', 'message' => 'Email does not exist'], 404);
        }
    }
    public function resetPassword(Request $request): JsonResponse {
        $validator = Validator::make($request->all(), [
            'forget_password_token' => ['required', 'string'],
            'email'                 => ['required', 'string', 'email'],
            'password'              => ['required', 'confirmed', 'min:4', 'max:100'],
        ], [
            'email.required' => 'Email is required',
            'password.required' => 'Password is required',
            'password.min' => 'Password must be 4 characters',
            'forget_password_token.required' => 'Forget password token is required',
            'password.confirmed'             => 'Confirm password does not match',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => $validator->errors()], 422);
        }

        // Find the user with the provided token and email
        $user = User::select('id', 'name', 'email', 'forget_password_token')->where('forget_password_token', $request->forget_password_token)->where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Invalid token, please try again',
            ], 400);
        }

        // Update the user's password
        $user->password = Hash::make($request->password);
        $user->forget_password_token = null;
        $user->save();

        return response()->json([
            'status'  => 'success',
            'message' => 'Password Reset successfully',
        ], 200);
    }
    public function login(Request $request): JsonResponse {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ], [
            'email.required' => 'Email is required',
            'password.required' => 'Password is required',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => $validator->errors()], 422);
        }

        // Find the user by email
        $user = User::where('email', $request->email)->first();

        // Check if user exists and password match
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['status' => 'error', 'message' => 'Invalid credentials please check your email and password'], 401);
        }
        // Check if user active
        if ($user->status != UserStatus::ACTIVE->value) {
            return response()->json(['status' => 'error', 'message' => 'Inactive account'], 403);
        }
        // Check if user is banned
        if ($user->is_banned == UserStatus::BANNED->value) {
            return response()->json(['status' => 'error', 'message' => 'Your account has been banned'], 403);
        }

        //delete all extra token
        PersonalAccessToken::where('tokenable_id', $user->id)->where('tokenable_type', 'App\Models\User')->where('name', 'extra-token')->delete();

        $bearer_token = $user->createToken('student', ['*'])->plainTextToken;
        return response()->json(['status' => 'success', 'message' => 'Logged in successfully.', 'bearer_token' => $bearer_token, 'user_id'=>$user->id], 200);
    }
    public function logout(): JsonResponse {
        $user = auth()->user();
        //delete all extra token
        PersonalAccessToken::where('tokenable_id', $user->id)->where('tokenable_type', 'App\Models\User')->where('name', 'extra-token')->delete();
        $user->currentAccessToken()->delete();
        return response()->json(['status' => 'success', 'message' => 'Logged out successfully.'],200);

    }
    public function logoutAllApp(): JsonResponse {
        auth()->user()->tokens()->delete();
        return response()->json(['status' => 'success', 'message' => 'Logged out successfully.'],200);
    }
    public function checkAccessToken(): JsonResponse {
        return response()->json(['status' => 'success'],200);
    }
}
