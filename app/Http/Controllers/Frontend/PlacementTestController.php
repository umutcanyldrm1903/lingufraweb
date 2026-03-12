<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\PlacementTestAttempt;
use App\Support\PlacementTestEngine;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PlacementTestController extends Controller
{
    public function show(Request $request, PlacementTestEngine $engine): View
    {
        $locale = app()->getLocale();
        $result = $request->session()->get('placement_test_result');

        return view('frontend.pages.placement-test', [
            'questions' => $engine->questions($locale),
            'result' => $result,
        ]);
    }

    public function submit(Request $request, PlacementTestEngine $engine): RedirectResponse
    {
        $validated = $request->validate([
            'answers' => ['required', 'array', 'min:1'],
            'answers.*' => ['nullable', 'string', 'max:10'],
            'name' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:32'],
        ]);

        $locale = app()->getLocale();
        $result = $engine->evaluate($validated['answers'], $locale);
        $isTurkish = strtolower(substr($locale, 0, 2)) === 'tr';

        $whatsAppPhone = preg_replace('/\D+/', '', (string) config('app.whatsapp_lead_phone', ''));
        $levelMessage = $isTurkish
            ? "Merhaba, seviye testinde {$result['level']} sonucunu aldım. Uygun deneme dersi için bilgi almak istiyorum."
            : "Hi, I got {$result['level']} in the placement test. I would like to book a trial lesson.";

        $whatsAppUrl = $whatsAppPhone !== ''
            ? 'https://wa.me/' . $whatsAppPhone . '?text=' . rawurlencode($levelMessage)
            : null;

        PlacementTestAttempt::create([
            'user_id' => auth()->id(),
            'source' => 'web',
            'locale' => $locale,
            'contact_name' => $validated['name'] ?? null,
            'contact_email' => $validated['email'] ?? null,
            'contact_phone' => $validated['phone'] ?? null,
            'score' => $result['score'],
            'max_score' => $result['max_score'],
            'answered_count' => $result['answered_count'],
            'level' => $result['level'],
            'recommended_track' => $result['recommended_track'],
            'answers' => $validated['answers'],
            'meta' => [
                'ip' => $request->ip(),
                'user_agent' => (string) $request->userAgent(),
            ],
        ]);

        $result['cta'] = [
            'schedule_url' => auth()->check() ? route('student.setting.index') : route('login'),
            'whatsapp_url' => $whatsAppUrl,
        ];

        return redirect()->route('placement-test.show')->with([
            'placement_test_result' => $result,
            'messege' => __('Placement test completed successfully.'),
            'alert-type' => 'success',
        ]);
    }
}
