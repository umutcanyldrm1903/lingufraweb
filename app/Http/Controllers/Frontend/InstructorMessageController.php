<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\StudentLiveLesson;
use App\Models\User;
use App\Models\UserPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;

class InstructorMessageController extends Controller
{
    public function index(?User $user = null)
    {
        $auth = auth()->user();

        $studentIds = $this->getAllowedStudentIds((int) $auth->id);
        $students = User::query()
            ->whereIn('id', $studentIds)
            ->orderBy('name')
            ->get();

        $allMessages = Message::with(['sender', 'receiver'])
            ->where(function ($q) use ($auth) {
                $q->where('sender_id', $auth->id)->orWhere('receiver_id', $auth->id);
            })
            ->latest()
            ->get()
            ->filter(function (Message $message) use ($auth, $studentIds) {
                $partnerId = (int) ($message->sender_id === $auth->id ? $message->receiver_id : $message->sender_id);
                return $studentIds->contains($partnerId);
            });

        $threads = $allMessages->groupBy(function ($message) use ($auth) {
            return $message->sender_id === $auth->id ? $message->receiver_id : $message->sender_id;
        })->map(function ($messages) {
            return $messages->sortByDesc('created_at')->first();
        });

        $unreadCounts = Message::where('receiver_id', $auth->id)
            ->whereIn('sender_id', $studentIds)
            ->where('is_read', false)
            ->groupBy('sender_id')
            ->selectRaw('sender_id, COUNT(*) as total')
            ->pluck('total', 'sender_id');

        $partner = null;
        $threadMessages = collect();

        if ($user?->exists) {
            if (!$students->contains('id', $user->id)) {
                abort(403);
            }

            $partner = $user;
            $threadMessages = Message::between($auth->id, $partner->id)
                ->with(['sender', 'receiver'])
                ->orderBy('created_at')
                ->get();

            Message::between($auth->id, $partner->id)
                ->where('receiver_id', $auth->id)
                ->where('is_read', false)
                ->update(['is_read' => true]);
        }

        return view('frontend.instructor-dashboard.messages.index', [
            'students' => $students,
            'threads' => $threads,
            'partner' => $partner,
            'threadMessages' => $threadMessages,
            'unreadCounts' => $unreadCounts,
        ]);
    }

    public function store(Request $request, User $user)
    {
        $auth = auth()->user();

        $studentIds = $this->getAllowedStudentIds((int) $auth->id);
        if (!$studentIds->contains((int) $user->id)) {
            abort(403);
        }

        $data = $request->validate([
            'body' => 'required|string|max:2000',
        ]);

        Message::create([
            'sender_id' => $auth->id,
            'receiver_id' => $user->id,
            'body' => $data['body'],
            'is_read' => false,
        ]);

        return redirect()
            ->route('instructor.messages.index', $user->id)
            ->with('success', __('Mesaj gonderildi'));
    }

    private function getAllowedStudentIds(int $instructorId): Collection
    {
        $ids = collect();

        if (Schema::hasTable('user_plans') && Schema::hasColumn('user_plans', 'assigned_instructor_id')) {
            $ids = $ids->merge(
                UserPlan::query()
                    ->where('assigned_instructor_id', $instructorId)
                    ->pluck('user_id')
            );
        }

        if (Schema::hasTable('student_live_lessons')) {
            $ids = $ids->merge(
                StudentLiveLesson::query()
                    ->where('instructor_id', $instructorId)
                    ->pluck('student_id')
            );
        }

        $messagePartnerIds = Message::query()
            ->where(function ($q) use ($instructorId) {
                $q->where('sender_id', $instructorId)->orWhere('receiver_id', $instructorId);
            })
            ->get(['sender_id', 'receiver_id'])
            ->map(function (Message $message) use ($instructorId) {
                return (int) ($message->sender_id === $instructorId ? $message->receiver_id : $message->sender_id);
            });

        $ids = $ids->merge($messagePartnerIds);

        return $ids
            ->map(fn ($id) => (int) $id)
            ->filter(fn ($id) => $id > 0)
            ->unique()
            ->values();
    }
}
