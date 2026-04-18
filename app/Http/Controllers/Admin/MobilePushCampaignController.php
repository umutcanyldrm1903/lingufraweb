<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MobilePushCampaign;
use Illuminate\Http\Request;

class MobilePushCampaignController extends Controller
{
    public function index()
    {
        $campaigns = MobilePushCampaign::query()->orderByDesc('created_at')->paginate(20);
        return view('admin.growth.push-campaigns', compact('campaigns'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'segment' => ['nullable', 'string', 'max:40'],
            'title_tr' => ['required', 'string', 'max:120'],
            'body_tr' => ['required', 'string', 'max:240'],
            'title_en' => ['required', 'string', 'max:120'],
            'body_en' => ['required', 'string', 'max:240'],
            'deep_link' => ['nullable', 'string', 'max:80'],
            'scheduled_at' => ['nullable', 'date'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        MobilePushCampaign::create([
            ...$validated,
            'segment' => $validated['segment'] ?? 'all',
            'deep_link' => $validated['deep_link'] ?? '/start-speaking',
            'is_active' => (bool) ($validated['is_active'] ?? true),
        ]);

        return redirect()->route('admin.growth.push-campaigns.index')->with([
            'messege' => __('Push campaign created.'),
            'alert-type' => 'success',
        ]);
    }
}
