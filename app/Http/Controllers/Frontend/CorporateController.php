<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Traits\MailSenderTrait;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;
use Modules\ContactMessage\app\Emails\ContactMessageMail;
use Modules\ContactMessage\app\Jobs\ContactMessageSendJob;
use Modules\ContactMessage\app\Models\ContactMessage;
use Modules\GlobalSetting\app\Models\EmailTemplate;

class CorporateController extends Controller
{
    use MailSenderTrait;

    public function index(): View
    {
        return view('frontend.pages.corporate');
    }

    public function form(): View
    {
        return view('frontend.pages.corporate-form');
    }

    public function submit(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'company_contact_first_name' => ['required', 'string', 'max:255'],
            'company_contact_last_name'  => ['required', 'string', 'max:255'],
            'company_name'               => ['required', 'string', 'max:255'],
            'company_phone'              => ['required', 'string', 'max:50'],
            'company_email'              => ['required', 'email', 'max:255'],
            'people_count'               => ['required', 'integer', 'min:1', 'max:100000'],

            'your_first_name'            => ['required', 'string', 'max:255'],
            'your_last_name'             => ['required', 'string', 'max:255'],
            'your_email'                 => ['required', 'email', 'max:255'],
        ], [
            'company_contact_first_name.required' => __('Name is required'),
            'company_contact_last_name.required'  => __('Name is required'),
            'company_name.required'               => __('Company is required'),
            'company_phone.required'              => __('Phone is required'),
            'company_email.required'              => __('Email is required'),
            'people_count.required'               => __('This field is required'),
            'your_first_name.required'            => __('Name is required'),
            'your_last_name.required'             => __('Name is required'),
            'your_email.required'                 => __('Email is required'),
        ]);

        $messageLines = [
            'Corporate Form',
            '---',
            'Company contact: ' . $validated['company_contact_first_name'] . ' ' . $validated['company_contact_last_name'],
            'Company name: ' . $validated['company_name'],
            'Company phone: ' . $validated['company_phone'],
            'Company email: ' . $validated['company_email'],
            'People count: ' . $validated['people_count'],
            '---',
            'Submitted by: ' . $validated['your_first_name'] . ' ' . $validated['your_last_name'],
            'Email: ' . $validated['your_email'],
        ];

        $contact = new ContactMessage();
        $contact->name = trim($validated['your_first_name'] . ' ' . $validated['your_last_name']);
        $contact->email = $validated['your_email'];
        $contact->phone = $validated['company_phone'];
        $contact->subject = 'Corporate';
        $contact->message = implode("\n", $messageLines);
        $contact->save();

        // Corporate form should behave like contact form: save in panel + send email to receiver.
        self::setMailConfig();
        if (self::isQueable()) {
            ContactMessageSendJob::dispatch($contact);
        } else {
            $template = EmailTemplate::where('name', 'contact_mail')->first();
            $subject = (string) ($template?->subject ?: 'Corporate Form');
            $message = (string) ($template?->message ?: '{{message}}');

            $message = str_replace('{{name}}', $contact->name, $message);
            $message = str_replace('{{email}}', (string) $contact->email, $message);
            $message = str_replace('{{phone}}', (string) $contact->phone, $message);
            $message = str_replace('{{subject}}', (string) $contact->subject, $message);
            $message = str_replace('{{message}}', (string) $contact->message, $message);

            $receiver = (string) (Cache::get('setting')?->contact_message_receiver_mail ?? '');
            if ($receiver !== '') {
                Mail::to($receiver)->send(new ContactMessageMail($subject, $message));
            }
        }

        return redirect()
            ->route('corporate.index')
            ->with(['messege' => __('Message sent successfully'), 'alert-type' => 'success']);
    }
}
