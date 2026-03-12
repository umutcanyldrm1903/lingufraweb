<?php

namespace App\Http\Controllers\Frontend;

use Illuminate\View\View;
use App\Enums\RedirectType;
use App\Models\JitsiSetting;
use Illuminate\Http\Request;
use App\Models\ZoomCredential;
use App\Services\Zoom\ZoomOAuthService;
use App\Traits\RedirectHelperTrait;
use App\Http\Controllers\Controller;

class InstructorLiveCredentialController extends Controller {
    use RedirectHelperTrait;
    function index(): View {
        $credential = userAuth()->zoom_credential;
        $isConfigured = (bool) ($credential && $credential->default_meeting_id && ($credential->default_meeting_password || $credential->default_join_url));
        return view('frontend.instructor-dashboard.zoom.index', compact('credential', 'isConfigured'));
    }
    function update(Request $request) {
        $validated = $request->validate([
            'default_meeting_id' => ['required', 'string', 'max:64'],
            'default_meeting_password' => 'nullable|string|max:64|required_without:default_join_url',
            'default_join_url' => 'nullable|url|max:2048|required_without:default_meeting_password',
        ],[
            'default_meeting_id.required' => __('Meeting ID is required'),
            'default_meeting_password.required' => __('Passcode is required'),
            'default_meeting_password.required_without' => __('Passcode is required when Join URL is empty.'),
            'default_join_url.required_without' => __('Join URL is required when Passcode is empty.'),
        ]);

        $meetingId = preg_replace('/[^0-9]/', '', (string) $validated['default_meeting_id']);
        if ($meetingId === '') {
            return redirect()->back()->withErrors([
                'default_meeting_id' => __('Meeting ID is invalid.'),
            ])->withInput();
        }

        $payload = [
            'default_meeting_id' => $meetingId,
            'default_meeting_password' => trim((string) ($validated['default_meeting_password'] ?? '')),
            'default_join_url' => trim((string) ($validated['default_join_url'] ?? '')) ?: null,
            'default_meeting_created_at' => now(),
        ];

        ZoomCredential::updateOrCreate(['instructor_id' => userAuth()->id], $payload);
        return $this->redirectWithMessage(RedirectType::UPDATE->value);
    }

    public function connect()
    {
        try {
            $service = app(ZoomOAuthService::class);
            $url = $service->buildAuthorizationUrl(userAuth());
            return redirect()->away($url);
        } catch (\Throwable $e) {
            report($e);
            return redirect()->back()->with([
                'messege' => __('Zoom settings are missing.'),
                'alert-type' => 'error',
            ]);
        }
    }

    public function callback(Request $request)
    {
        $code = (string) $request->query('code', '');
        $state = $request->query('state');

        if ($code === '') {
            return redirect()->route('instructor.zoom-setting.index')->with([
                'messege' => __('Zoom account connection failed.'),
                'alert-type' => 'error',
            ]);
        }

        try {
            $service = app(ZoomOAuthService::class);
            $service->handleCallback(userAuth(), $code, $state);
        } catch (\Throwable $e) {
            report($e);
            return redirect()->route('instructor.zoom-setting.index')->with([
                'messege' => __('Zoom account connection failed.'),
                'alert-type' => 'error',
            ]);
        }

        return redirect()->route('instructor.zoom-setting.index')->with([
            'messege' => __('Zoom account connected.'),
            'alert-type' => 'success',
        ]);
    }

    public function disconnect()
    {
        try {
            $service = app(ZoomOAuthService::class);
            $service->disconnect(userAuth());
        } catch (\Throwable $e) {
            report($e);
        }

        return redirect()->route('instructor.zoom-setting.index')->with([
            'messege' => __('Zoom settings cleared.'),
            'alert-type' => 'success',
        ]);
    }
    function jitsi_index(): View {
        $credential = userAuth()->jitsi_credential;
        return view('frontend.instructor-dashboard.jitsi.index', compact('credential'));
    }
    function jitsi_update(Request $request) {
        $user_id = userAuth()->id;
        $rules = [
            'app_id' => 'required',
            'api_key' => 'required',
            'permissions' => 'sometimes',
        ];
        $extension = $request->hasFile('private_key') ? $request->file('private_key')->getClientOriginalExtension() : 'pk';
        // Define the storage path and file name
        $storage_path = storage_path("app/user_{$user_id}");
        $file_name = "rsb_private_key." . $extension;
        $full_file_path = "{$storage_path}/{$file_name}";
    
        if (!file_exists($full_file_path)) {
            $rules['private_key'] = 'required|file';
        }
        $messages = [
            'app_id.required' => __('App ID is required'),
            'api_key.required' => __('API Key is required'),
            'private_key.required' => __('RSA Private key file is required'),
            'private_key.file' => __('RSA Private key must be a valid file'),
        ];
        $validated = $request->validate($rules, $messages);
        if ($request->hasFile('private_key')) {
            if (!is_dir($storage_path)) {
                mkdir($storage_path, 0777, true);
            } else if (file_exists($full_file_path)) {
                unlink($full_file_path);
            }
            $request->file('private_key')->move($storage_path, $file_name);
        }
        JitsiSetting::updateOrCreate(['instructor_id' => $user_id], $validated);
        return $this->redirectWithMessage(RedirectType::UPDATE->value);
    }
    
}
