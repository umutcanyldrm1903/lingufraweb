<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MessageController extends Controller
{
    public function threads(): JsonResponse
    {
        $auth = auth()->user();

        $allMessages = Message::with(['sender:id,name,image,role', 'receiver:id,name,image,role'])
            ->where(function ($q) use ($auth) {
                $q->where('sender_id', $auth->id)->orWhere('receiver_id', $auth->id);
            })
            ->latest()
            ->get();

        $threads = $allMessages->groupBy(function (Message $message) use ($auth) {
            return $message->sender_id === $auth->id ? $message->receiver_id : $message->sender_id;
        })->map(function ($messages) {
            return $messages->sortByDesc('created_at')->first();
        });

        $unreadCounts = Message::where('receiver_id', $auth->id)
            ->where('is_read', false)
            ->groupBy('sender_id')
            ->selectRaw('sender_id, COUNT(*) as total')
            ->pluck('total', 'sender_id');

        $data = $threads->values()->map(function (Message $message) use ($auth, $unreadCounts) {
            $partner = $message->sender_id === $auth->id ? $message->receiver : $message->sender;
            return [
                'partner' => [
                    'id' => (int) $partner->id,
                    'name' => (string) ($partner->first_name ?? $partner->name),
                    'image' => $partner->image ? asset($partner->image) : null,
                    'role' => (string) $partner->role,
                ],
                'last_message' => [
                    'id' => (int) $message->id,
                    'body' => (string) $message->body,
                    'sender_id' => (int) $message->sender_id,
                    'created_at' => optional($message->created_at)->toISOString(),
                ],
                'unread_count' => (int) ($unreadCounts[$partner->id] ?? 0),
                'moderation' => $this->moderationState($auth->id, (int) $partner->id),
            ];
        });

        return response()->json([
            'status' => 'success',
            'data' => $data,
        ], 200);
    }

    public function thread(User $user): JsonResponse
    {
        $auth = auth()->user();

        $messages = Message::between($auth->id, $user->id)
            ->with(['sender:id,name,image', 'receiver:id,name,image'])
            ->orderBy('created_at')
            ->get();

        Message::between($auth->id, $user->id)
            ->where('receiver_id', $auth->id)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        $data = $messages->map(function (Message $message) {
            return [
                'id' => (int) $message->id,
                'sender_id' => (int) $message->sender_id,
                'receiver_id' => (int) $message->receiver_id,
                'body' => (string) $message->body,
                'created_at' => optional($message->created_at)->toISOString(),
            ];
        });

        return response()->json([
            'status' => 'success',
            'data' => $data,
            'meta' => [
                'moderation' => $this->moderationState($auth->id, $user->id),
            ],
        ], 200);
    }

    public function moderation(User $user): JsonResponse
    {
        $auth = auth()->user();

        return response()->json([
            'status' => 'success',
            'data' => $this->moderationState($auth->id, $user->id),
        ], 200);
    }

    public function send(Request $request, User $user): JsonResponse
    {
        $auth = auth()->user();

        if ($auth->id === $user->id) {
            return response()->json([
                'status' => 'error',
                'message' => 'You cannot send a message to yourself.',
            ], 422);
        }

        $moderation = $this->moderationState($auth->id, $user->id);
        if ($moderation['blocked_by_me']) {
            return response()->json([
                'status' => 'error',
                'message' => 'You blocked this user. Unblock the user to send messages again.',
            ], 423);
        }

        if ($moderation['blocked_by_partner']) {
            return response()->json([
                'status' => 'error',
                'message' => 'This user is not accepting messages from you.',
            ], 423);
        }

        $data = $request->validate([
            'body' => 'required|string|max:2000',
        ]);

        $message = Message::create([
            'sender_id' => $auth->id,
            'receiver_id' => $user->id,
            'body' => $data['body'],
            'is_read' => false,
        ]);

        return response()->json([
            'status' => 'success',
            'data' => [
                'id' => (int) $message->id,
                'sender_id' => (int) $message->sender_id,
                'receiver_id' => (int) $message->receiver_id,
                'body' => (string) $message->body,
                'created_at' => optional($message->created_at)->toISOString(),
            ],
        ], 201);
    }

    public function block(Request $request, User $user): JsonResponse
    {
        $auth = auth()->user();

        if ($auth->id === $user->id) {
            return response()->json([
                'status' => 'error',
                'message' => 'You cannot block yourself.',
            ], 422);
        }

        $data = $request->validate([
            'reason' => 'nullable|string|max:500',
        ]);

        DB::table('message_user_blocks')->updateOrInsert(
            [
                'blocker_user_id' => $auth->id,
                'blocked_user_id' => $user->id,
            ],
            [
                'reason' => $data['reason'] ?? null,
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );

        return response()->json([
            'status' => 'success',
            'message' => 'User blocked successfully.',
            'data' => $this->moderationState($auth->id, $user->id),
        ], 200);
    }

    public function unblock(User $user): JsonResponse
    {
        $auth = auth()->user();

        DB::table('message_user_blocks')
            ->where('blocker_user_id', $auth->id)
            ->where('blocked_user_id', $user->id)
            ->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'User unblocked successfully.',
            'data' => $this->moderationState($auth->id, $user->id),
        ], 200);
    }

    public function report(Request $request, User $user): JsonResponse
    {
        $auth = auth()->user();

        if ($auth->id === $user->id) {
            return response()->json([
                'status' => 'error',
                'message' => 'You cannot report yourself.',
            ], 422);
        }

        $data = $request->validate([
            'reason' => 'required|string|max:1000',
            'message_id' => 'nullable|integer|exists:messages,id',
        ]);

        if (!empty($data['message_id'])) {
            $messageBelongsToThread = Message::query()
                ->whereKey($data['message_id'])
                ->where(function ($query) use ($auth, $user) {
                    $query->where(function ($inner) use ($auth, $user) {
                        $inner->where('sender_id', $auth->id)
                            ->where('receiver_id', $user->id);
                    })->orWhere(function ($inner) use ($auth, $user) {
                        $inner->where('sender_id', $user->id)
                            ->where('receiver_id', $auth->id);
                    });
                })
                ->exists();

            if (!$messageBelongsToThread) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'The selected message does not belong to this conversation.',
                ], 422);
            }
        }

        DB::table('message_reports')->insert([
            'reporter_user_id' => $auth->id,
            'reported_user_id' => $user->id,
            'message_id' => $data['message_id'] ?? null,
            'reason' => $data['reason'],
            'status' => 'pending',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Report submitted successfully.',
        ], 201);
    }

    private function moderationState(int $authId, int $partnerId): array
    {
        return [
            'blocked_by_me' => DB::table('message_user_blocks')
                ->where('blocker_user_id', $authId)
                ->where('blocked_user_id', $partnerId)
                ->exists(),
            'blocked_by_partner' => DB::table('message_user_blocks')
                ->where('blocker_user_id', $partnerId)
                ->where('blocked_user_id', $authId)
                ->exists(),
        ];
    }
}
