<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OutreachCampaign;
use App\Models\OutreachLead;
use App\Models\OutreachMessage;
use App\Services\Outreach\ImapReplySyncService;
use App\Services\Outreach\LushaClient;
use App\Services\Outreach\OutreachAutomationService;
use App\Services\Outreach\OutreachLeadManager;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use RuntimeException;

class OutreachCampaignController extends Controller
{
    public function index(Request $request): View
    {
        $tableMissing = ! $this->tablesReady();

        if ($tableMissing) {
            $campaigns = $this->emptyPaginator($request, 15);

            return view('admin.outreach.campaigns.index', compact('campaigns', 'tableMissing'));
        }

        $campaigns = OutreachCampaign::query()
            ->withCount([
                'leads',
                'messages',
                'messages as sent_messages_count' => fn ($query) => $query->whereNotNull('sent_at'),
                'messages as replied_messages_count' => fn ($query) => $query->whereNotNull('replied_at'),
            ])
            ->when($request->filled('keyword'), function ($query) use ($request) {
                $keyword = trim((string) $request->input('keyword'));

                $query->where(function ($subQuery) use ($keyword) {
                    $subQuery->where('name', 'like', "%{$keyword}%")
                        ->orWhere('company_name', 'like', "%{$keyword}%")
                        ->orWhere('product_name', 'like', "%{$keyword}%");
                });
            })
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->input('status')))
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        return view('admin.outreach.campaigns.index', compact('campaigns', 'tableMissing'));
    }

    public function create(): View|RedirectResponse
    {
        if (! $this->tablesReady()) {
            return $this->redirectTableMissing();
        }

        return view('admin.outreach.campaigns.create');
    }

    public function store(Request $request): RedirectResponse
    {
        if (! $this->tablesReady()) {
            return $this->redirectTableMissing();
        }

        $campaign = OutreachCampaign::create($this->validateCampaign($request));

        return redirect()->route('admin.outreach-campaigns.show', $campaign)->with($this->notify(__('Outreach campaign created successfully.')));
    }

    public function show(Request $request, OutreachCampaign $outreachCampaign): View|RedirectResponse
    {
        if (! $this->tablesReady()) {
            return $this->redirectTableMissing();
        }

        $outreachCampaign->loadCount([
            'leads',
            'messages',
            'messages as sent_messages_count' => fn ($query) => $query->whereNotNull('sent_at'),
            'messages as replied_messages_count' => fn ($query) => $query->whereNotNull('replied_at'),
            'messages as failed_messages_count' => fn ($query) => $query->where('status', 'failed'),
            'messages as generated_messages_count' => fn ($query) => $query->where('status', 'generated'),
            'messages as approved_messages_count' => fn ($query) => $query->where('status', 'approved'),
        ]);

        $leads = $outreachCampaign->leads()
            ->when($request->filled('lead_status'), fn ($query) => $query->where('status', $request->input('lead_status')))
            ->when($request->filled('lead_keyword'), function ($query) use ($request) {
                $keyword = trim((string) $request->input('lead_keyword'));

                $query->where(function ($subQuery) use ($keyword) {
                    $subQuery->where('full_name', 'like', "%{$keyword}%")
                        ->orWhere('email', 'like', "%{$keyword}%")
                        ->orWhere('company_name', 'like', "%{$keyword}%")
                        ->orWhere('job_title', 'like', "%{$keyword}%");
                });
            })
            ->latest('id')
            ->paginate(12, ['*'], 'lead_page')
            ->withQueryString();

        $messages = $outreachCampaign->messages()
            ->with('lead')
            ->when($request->filled('message_status'), fn ($query) => $query->where('status', $request->input('message_status')))
            ->latest('id')
            ->paginate(12, ['*'], 'message_page')
            ->withQueryString();

        $stats = [
            'imported' => $outreachCampaign->leads()->where('status', 'imported')->count(),
            'ready' => $outreachCampaign->leads()->where('status', 'ready')->count(),
            'sent' => $outreachCampaign->messages()->whereNotNull('sent_at')->count(),
            'replied' => $outreachCampaign->messages()->whereNotNull('replied_at')->count(),
        ];

        return view('admin.outreach.campaigns.show', compact('outreachCampaign', 'leads', 'messages', 'stats'));
    }

    public function edit(OutreachCampaign $outreachCampaign): View|RedirectResponse
    {
        if (! $this->tablesReady()) {
            return $this->redirectTableMissing();
        }

        return view('admin.outreach.campaigns.edit', compact('outreachCampaign'));
    }

    public function update(Request $request, OutreachCampaign $outreachCampaign): RedirectResponse
    {
        if (! $this->tablesReady()) {
            return $this->redirectTableMissing();
        }

        $outreachCampaign->update($this->validateCampaign($request, $outreachCampaign));

        return redirect()->route('admin.outreach-campaigns.show', $outreachCampaign)->with($this->notify(__('Outreach campaign updated successfully.')));
    }

    public function destroy(OutreachCampaign $outreachCampaign): RedirectResponse
    {
        if (! $this->tablesReady()) {
            return $this->redirectTableMissing();
        }

        $outreachCampaign->delete();

        return redirect()->route('admin.outreach-campaigns.index')->with($this->notify(__('Outreach campaign deleted successfully.')));
    }

    public function importLusha(Request $request, OutreachCampaign $outreachCampaign, LushaClient $lushaClient, OutreachLeadManager $outreachLeadManager): RedirectResponse
    {
        if (! $this->tablesReady()) {
            return $this->redirectTableMissing();
        }

        $validated = $request->validate([
            'payload_json' => ['required', 'string'],
        ]);

        try {
            $payload = $this->decodePayload($validated['payload_json']);
            $result = $lushaClient->searchContacts($payload);
            $count = 0;

            foreach ($result['contacts'] as $contact) {
                $outreachLeadManager->upsertFromLusha($outreachCampaign, $contact, 'imported');
                $count++;
            }

            $outreachCampaign->forceFill([
                'status' => 'imported',
                'last_lusha_payload' => $payload,
            ])->save();

            return redirect()->route('admin.outreach-campaigns.show', $outreachCampaign)->with($this->notify(__("Imported {$count} lead(s) from Lusha.")));
        } catch (\Throwable $throwable) {
            return redirect()->route('admin.outreach-campaigns.show', $outreachCampaign)->with($this->notify($throwable->getMessage(), 'error'));
        }
    }

    public function enrichLusha(Request $request, OutreachCampaign $outreachCampaign, LushaClient $lushaClient, OutreachLeadManager $outreachLeadManager): RedirectResponse
    {
        if (! $this->tablesReady()) {
            return $this->redirectTableMissing();
        }

        $validated = $request->validate([
            'limit' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

        $limit = (int) ($validated['limit'] ?? 25);
        $success = 0;
        $failed = 0;

        $leads = OutreachLead::where('campaign_id', $outreachCampaign->id)
            ->whereNull('email')
            ->whereNotNull('request_id')
            ->whereNotNull('contact_id')
            ->limit($limit)
            ->get();

        foreach ($leads as $lead) {
            try {
                $contact = $lushaClient->enrichLead($lead);

                if ($contact === []) {
                    $failed++;
                    continue;
                }

                $outreachLeadManager->upsertFromLusha($outreachCampaign, $contact, 'enriched');
                $success++;
            } catch (\Throwable $throwable) {
                $lead->forceFill([
                    'status' => 'enrich_failed',
                    'enrichment_payload' => ['error' => $throwable->getMessage()],
                ])->save();
                $failed++;
            }
        }

        if ($success > 0) {
            $outreachCampaign->forceFill(['status' => 'enriched'])->save();
        }

        return redirect()->route('admin.outreach-campaigns.show', $outreachCampaign)->with($this->notify(__("Enriched {$success} lead(s), {$failed} failed."), $failed > 0 && $success === 0 ? 'error' : 'success'));
    }

    public function generate(Request $request, OutreachCampaign $outreachCampaign, OutreachAutomationService $outreachAutomationService): RedirectResponse
    {
        if (! $this->tablesReady()) {
            return $this->redirectTableMissing();
        }

        $validated = $request->validate([
            'limit' => ['nullable', 'integer', 'min:1', 'max:100'],
            'lead_id' => ['nullable', 'integer'],
            'refresh' => ['nullable', 'boolean'],
        ]);

        $refresh = $request->boolean('refresh');
        $processed = 0;
        $failed = 0;
        $leadQuery = OutreachLead::where('campaign_id', $outreachCampaign->id)->whereNotNull('email');

        if (! empty($validated['lead_id'])) {
            $leadQuery->where('id', $validated['lead_id']);
        } else {
            $leadQuery->limit((int) ($validated['limit'] ?? 20));
        }

        foreach ($leadQuery->get() as $lead) {
            try {
                $outreachAutomationService->generateMessageForLead($lead, $refresh);
                $processed++;
            } catch (\Throwable $throwable) {
                $failed++;
            }
        }

        if ($processed > 0) {
            $outreachCampaign->forceFill(['status' => 'generated'])->save();
        }

        return redirect()->route('admin.outreach-campaigns.show', $outreachCampaign)->with($this->notify(__("Generated {$processed} draft(s), {$failed} failed."), $failed > 0 && $processed === 0 ? 'error' : 'success'));
    }

    public function approve(Request $request, OutreachCampaign $outreachCampaign, OutreachAutomationService $outreachAutomationService): RedirectResponse
    {
        if (! $this->tablesReady()) {
            return $this->redirectTableMissing();
        }

        $validated = $request->validate([
            'limit' => ['nullable', 'integer', 'min:1', 'max:200'],
        ]);

        $messages = OutreachMessage::where('campaign_id', $outreachCampaign->id)
            ->where('status', 'generated')
            ->limit((int) ($validated['limit'] ?? 50))
            ->get();

        foreach ($messages as $message) {
            $outreachAutomationService->approveMessage($message);
        }

        if ($messages->count() > 0) {
            $outreachCampaign->forceFill(['status' => 'approved'])->save();
        }

        return redirect()->route('admin.outreach-campaigns.show', $outreachCampaign)->with($this->notify(__("Approved {$messages->count()} draft(s).")));
    }

    public function send(Request $request, OutreachCampaign $outreachCampaign, OutreachAutomationService $outreachAutomationService): RedirectResponse
    {
        if (! $this->tablesReady()) {
            return $this->redirectTableMissing();
        }

        $validated = $request->validate([
            'limit' => ['nullable', 'integer', 'min:1', 'max:100'],
            'force' => ['nullable', 'boolean'],
        ]);

        $limit = (int) ($validated['limit'] ?? 10);
        $force = $request->boolean('force');
        $statuses = $force ? ['generated', 'approved'] : ['approved'];

        $messages = OutreachMessage::where('campaign_id', $outreachCampaign->id)
            ->whereIn('status', $statuses)
            ->limit($limit)
            ->get();

        $sent = 0;
        $deferred = 0;
        $failed = 0;

        foreach ($messages as $message) {
            try {
                $updated = $outreachAutomationService->sendMessage($message, $force);

                if ($updated->status === 'sent') {
                    $sent++;
                } else {
                    $deferred++;
                }
            } catch (\Throwable $throwable) {
                $failed++;
            }
        }

        if ($sent > 0) {
            $outreachCampaign->forceFill(['status' => 'sent'])->save();
        }

        return redirect()->route('admin.outreach-campaigns.show', $outreachCampaign)->with($this->notify(__("Sent {$sent}, deferred {$deferred}, failed {$failed}."), $failed > 0 && $sent === 0 ? 'error' : 'success'));
    }

    public function syncReplies(OutreachCampaign $outreachCampaign, ImapReplySyncService $imapReplySyncService): RedirectResponse
    {
        if (! $this->tablesReady()) {
            return $this->redirectTableMissing();
        }

        try {
            $result = $imapReplySyncService->sync($outreachCampaign);

            return redirect()->route('admin.outreach-campaigns.show', $outreachCampaign)->with($this->notify(__("Searched {$result['searched']} mailbox message(s), matched {$result['matched']} replies.")));
        } catch (\Throwable $throwable) {
            return redirect()->route('admin.outreach-campaigns.show', $outreachCampaign)->with($this->notify($throwable->getMessage(), 'error'));
        }
    }

    public function editMessage(OutreachMessage $outreachMessage): View|RedirectResponse
    {
        if (! $this->tablesReady()) {
            return $this->redirectTableMissing();
        }

        $outreachMessage->loadMissing('campaign', 'lead');

        return view('admin.outreach.campaigns.edit-message', compact('outreachMessage'));
    }

    public function updateMessage(Request $request, OutreachMessage $outreachMessage): RedirectResponse
    {
        if (! $this->tablesReady()) {
            return $this->redirectTableMissing();
        }

        if ($outreachMessage->sent_at) {
            return redirect()->route('admin.outreach-campaigns.show', $outreachMessage->campaign_id)->with($this->notify(__('Sent messages cannot be edited.'), 'error'));
        }

        $validated = $request->validate([
            'subject' => ['required', 'string', 'max:190'],
            'body_text' => ['required', 'string'],
            'body_html' => ['nullable', 'string'],
            'status' => ['required', Rule::in(['draft', 'generated', 'approved'])],
        ]);

        $approveAfterSave = $request->boolean('approve_after_save');

        $outreachMessage->fill([
            'subject' => $validated['subject'],
            'body_text' => trim($validated['body_text']),
            'body_html' => trim((string) $validated['body_html']) !== '' ? $validated['body_html'] : $this->textToHtml($validated['body_text']),
            'status' => $approveAfterSave ? 'approved' : $validated['status'],
            'approved_at' => $approveAfterSave ? now() : ($validated['status'] === 'approved' ? ($outreachMessage->approved_at ?: now()) : null),
        ])->save();

        return redirect()->route('admin.outreach-campaigns.show', $outreachMessage->campaign_id)->with($this->notify(__('Message updated successfully.')));
    }

    public function approveMessage(OutreachMessage $outreachMessage, OutreachAutomationService $outreachAutomationService): RedirectResponse
    {
        if (! $this->tablesReady()) {
            return $this->redirectTableMissing();
        }

        $outreachAutomationService->approveMessage($outreachMessage);

        return redirect()->route('admin.outreach-campaigns.show', $outreachMessage->campaign_id)->with($this->notify(__('Message approved successfully.')));
    }

    public function sendMessage(OutreachMessage $outreachMessage, OutreachAutomationService $outreachAutomationService): RedirectResponse
    {
        if (! $this->tablesReady()) {
            return $this->redirectTableMissing();
        }

        try {
            $updated = $outreachAutomationService->sendMessage($outreachMessage);

            $message = $updated->status === 'sent'
                ? __('Message sent successfully.')
                : __('Message is scheduled for a later send window.');

            return redirect()->route('admin.outreach-campaigns.show', $outreachMessage->campaign_id)->with($this->notify($message));
        } catch (\Throwable $throwable) {
            return redirect()->route('admin.outreach-campaigns.show', $outreachMessage->campaign_id)->with($this->notify($throwable->getMessage(), 'error'));
        }
    }

    protected function validateCampaign(Request $request, ?OutreachCampaign $outreachCampaign = null): array
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'status' => ['nullable', Rule::in(['draft', 'imported', 'enriched', 'generated', 'approved', 'sent'])],
            'company_name' => ['nullable', 'string', 'max:255'],
            'company_website' => ['nullable', 'string', 'max:255'],
            'product_name' => ['nullable', 'string', 'max:255'],
            'language' => ['required', 'string', 'max:10'],
            'audience_summary' => ['nullable', 'string'],
            'offer_summary' => ['nullable', 'string'],
            'tone' => ['nullable', 'string', 'max:100'],
            'prompt_preamble' => ['nullable', 'string'],
            'signature_text' => ['nullable', 'string'],
            'signature_html' => ['nullable', 'string'],
            'unsubscribe_mailto' => ['nullable', 'email'],
            'timezone' => ['required', 'string', 'max:100'],
            'daily_send_limit' => ['required', 'integer', 'min:1', 'max:1000'],
            'hourly_send_limit' => ['required', 'integer', 'min:1', 'max:500'],
            'min_delay_seconds' => ['required', 'integer', 'min:0', 'max:86400'],
            'send_start_hour' => ['required', 'integer', 'min:0', 'max:23'],
            'send_end_hour' => ['required', 'integer', 'min:0', 'max:23'],
            'notes' => ['nullable', 'string'],
        ]);

        $validated['status'] = $validated['status'] ?? ($outreachCampaign?->status ?: 'draft');
        $validated['require_approval'] = $request->boolean('require_approval');
        $validated['timezone'] = 'Europe/Istanbul';

        return $validated;
    }

    protected function tablesReady(): bool
    {
        foreach (['outreach_campaigns', 'outreach_leads', 'outreach_messages', 'outreach_suppressions'] as $table) {
            if (! Schema::hasTable($table)) {
                return false;
            }
        }

        return true;
    }

    protected function redirectTableMissing(): RedirectResponse
    {
        return redirect()->route('admin.outreach-campaigns.index')->with($this->notify(__('Outreach tables are missing. Run migrations first.'), 'error'));
    }

    protected function decodePayload(string $payload): array
    {
        $decoded = json_decode($payload, true);

        if (json_last_error() !== JSON_ERROR_NONE || ! is_array($decoded)) {
            throw new RuntimeException('Payload must be valid JSON.');
        }

        return $decoded;
    }

    protected function textToHtml(string $bodyText): string
    {
        $paragraphs = preg_split("/\r\n\r\n|\n\n|\r\r/", trim($bodyText)) ?: [];

        return collect($paragraphs)
            ->map(fn ($paragraph) => trim($paragraph))
            ->filter()
            ->map(fn ($paragraph) => '<p>' . nl2br(e($paragraph)) . '</p>')
            ->implode("\n");
    }

    protected function notify(string $message, string $type = 'success'): array
    {
        return [
            'messege' => $message,
            'alert-type' => $type,
        ];
    }

    protected function emptyPaginator(Request $request, int $perPage, string $pageName = 'page'): LengthAwarePaginator
    {
        return new LengthAwarePaginator([], 0, $perPage, (int) $request->input($pageName, 1), [
            'path' => $request->url(),
            'pageName' => $pageName,
            'query' => $request->query(),
        ]);
    }
}
