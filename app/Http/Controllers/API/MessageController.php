<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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
                    'name' => (string) $partner->name,
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
        ], 200);
    }

    public function send(Request $request, User $user): JsonResponse
    {
        $auth = auth()->user();

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
}
