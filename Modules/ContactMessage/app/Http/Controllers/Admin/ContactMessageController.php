<?php

namespace Modules\ContactMessage\app\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Traits\MailSenderTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Modules\ContactMessage\app\Emails\ContactMessageMail;
use Modules\ContactMessage\app\Models\ContactMessage;

class ContactMessageController extends Controller
{
    use MailSenderTrait;

    public function index()
    {
        $messages = ContactMessage::orderBy('id', 'desc')->get();

        return view('contactmessage::index', ['messages' => $messages]);
    }

    public function show($id)
    {
        checkAdminHasPermissionAndThrowException('contect.message.view');

        $message = ContactMessage::findOrFail($id);

        return view('contactmessage::show', ['message' => $message]);
    }

    public function destroy($id)
    {
        checkAdminHasPermissionAndThrowException('contect.message.delete');
        $message = ContactMessage::findOrFail($id);
        $message->delete();

        $notification = __('Deleted successfully');
        $notification = ['messege' => $notification, 'alert-type' => 'success'];

        return redirect()->route('admin.contact-messages')->with($notification);
    }

    public function sendMail(Request $request, $id)
    {
        checkAdminHasPermissionAndThrowException('contect.message.view');

        $request->validate([
            'to_email' => ['nullable', 'email', 'max:255'],
            'subject' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
        ], [
            'to_email.email' => __('Email is not valid'),
            'subject.required' => __('Subject is required'),
            'description.required' => __('Description is required'),
        ]);

        $contactMessage = ContactMessage::findOrFail($id);
        $targetEmail = trim((string) ($request->to_email ?: $contactMessage->email));
        if ($targetEmail === '') {
            return redirect()->back()->with([
                'messege' => __('Email is required'),
                'alert-type' => 'error',
            ]);
        }

        if (!self::setMailConfig()) {
            return redirect()->back()->with([
                'messege' => __('Mail Can\'t be sent.'),
                'alert-type' => 'error',
            ]);
        }

        $emailBody = nl2br(e((string) $request->description));
        Mail::to($targetEmail)->send(new ContactMessageMail($request->subject, $emailBody));

        return redirect()->back()->with([
            'messege' => __('Mail send successfully'),
            'alert-type' => 'success',
        ]);
    }

    public function sendCustomMail(Request $request)
    {
        checkAdminHasPermissionAndThrowException('contect.message.view');

        $validated = $request->validate([
            'to_email' => ['required', 'email', 'max:255'],
            'subject' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
        ], [
            'to_email.required' => __('Email is required'),
            'to_email.email' => __('Email is not valid'),
            'subject.required' => __('Subject is required'),
            'description.required' => __('Description is required'),
        ]);

        if (!self::setMailConfig()) {
            return redirect()->back()->with([
                'messege' => __('Mail Can\'t be sent.'),
                'alert-type' => 'error',
            ]);
        }

        $emailBody = nl2br(e((string) $validated['description']));
        Mail::to($validated['to_email'])->send(new ContactMessageMail($validated['subject'], $emailBody));

        return redirect()->back()->with([
            'messege' => __('Mail send successfully'),
            'alert-type' => 'success',
        ]);
    }
}
