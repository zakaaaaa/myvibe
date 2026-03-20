<?php

namespace App\Http\Controllers\Api;

use App\Helpers\FirebaseNotificationHelper;
use App\Http\Controllers\Controller;
use App\Models\Messages;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class MessagesController extends Controller
{

    protected $firebaseHelper;

    public function __construct(FirebaseNotificationHelper $firebaseHelper)
    {
        $this->firebaseHelper = $firebaseHelper;
    }

    public function index()
    {
        $currentUserId = Auth::id();

        $messages = Messages::where('sender_id', $currentUserId)
            ->orWhere('receiver_id', $currentUserId)
            ->with(['sender', 'receiver'])
            ->get()
            ->groupBy(function ($message) use ($currentUserId) {
                return $message->sender_id == $currentUserId ? $message->receiver_id : $message->sender_id;
            });

        return response()->json($messages, 200);
    }

    public function store(Request $request)
    {
        //
    }

    public function show($userId)
    {
        $currentUserId = Auth::id();

        $messages = Messages::where(function ($query) use ($currentUserId, $userId) {
            $query->where('sender_id', $currentUserId)
                ->where('receiver_id', $userId);
        })->orWhere(function ($query) use ($currentUserId, $userId) {
            $query->where('sender_id', $userId)
                ->where('receiver_id', $currentUserId);
        })->orderBy('created_at', 'asc')->get();

        return response()->json($messages, 200);
    }

    public function update(Request $request, $id)
    {
        //
    }

    public function destroy($id)
    {
        //
    }

    public function sendMessage(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'message' => 'required|string|max:1000',
            'reply_to_id' => 'nullable|integer',
            'reply_to_text' => 'nullable|string|max:1000',
        ]);
        $userId = Auth::id();
        $receiverId = $request->receiver_id;

        $friend = User::with(['mbti', 'zodiac', 'relationship'])->where('id', $receiverId)->firstOrFail();
        $isFriend = auth()->user()->isFollowing($friend) || $friend->isFollowing(auth()->user());

        if (!$isFriend) {
            return response()->json([
                'message' => 'You can only send messages to friends.'
            ], 403);
        }

        DB::beginTransaction();
        try {
            $message = Messages::create([
                'sender_id' => $userId,
                'receiver_id' => $receiverId,
                'message_text' => $request->message,
                'reply_to_id' => $request->reply_to_id,
                'reply_to_text' => $request->reply_to_text,
            ]);

            $receiver = User::find($receiverId);

            $deviceToken = $receiver->fcm_token;
            $title = "New Message From " . $receiver->username;
            $body = $request->message;

            if ($deviceToken != null) {
                $this->firebaseHelper->sendNotification($deviceToken, $title, $body);
            }

            DB::commit();
            return response()->json([
                'message' => 'Message sent successfully.',
                'data' => $message
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()]);
        }
    }

    public function getConversation($userId)
    {
        $currentUserId = Auth::id();

        $messages = Messages::where(function ($query) use ($currentUserId, $userId) {
            $query->where('sender_id', $currentUserId)
                ->where('receiver_id', $userId);
        })->orWhere(function ($query) use ($currentUserId, $userId) {
            $query->where('sender_id', $userId)
                ->where('receiver_id', $currentUserId);
        })->orderBy('created_at', 'asc')->get();

        return response()->json($messages, 200);
    }

    public function getConversations()
    {
        $userId = Auth::id();

        $friends = Messages::where('sender_id', $userId)
            ->orWhere('receiver_id', $userId)
            ->selectRaw('
            CASE 
                WHEN sender_id = ? THEN receiver_id
                ELSE sender_id
            END as friend_id,
            MAX(id) as last_message_id', [$userId])
            ->groupBy('friend_id')
            ->with([
                'lastMessage' => function ($query) {
                    $query->select('id', 'sender_id', 'receiver_id', 'message_text', 'created_at');
                },
                'friend:id,name,username,email,profile_picture'
            ])
            ->orderByDesc('last_message_id')
            ->get()
            ->map(function ($friend) {
                return [
                    'friend_id' => $friend->friend_id,
                    'friend' => $friend->friend,
                    'last_message' => Str::limit($friend->lastMessage->message_text, 27) ?? null,
                    'last_message_at' => $friend->lastMessage->created_at ?? null,
                ];
            });

        return response()->json($friends, 200);
    }

    public function deleteMessage($messageId)
    {
        $userId = Auth::id();

        $message = Messages::find($messageId);

        if (!$message) {
            return response()->json([
                'message' => 'Message not found.'
            ], 404);
        }

        if ($message->sender_id !== $userId && $message->receiver_id !== $userId) {
            return response()->json([
                'message' => 'You do not have permission to delete this message.'
            ], 403);
        }

        $message->delete();

        return response()->json([
            'message' => 'Message deleted successfully.'
        ], 200);
    }

    public function deleteConversation($userId)
    {
        $currentUserId = Auth::id();

        $messages = Messages::where(function ($query) use ($currentUserId, $userId) {
            $query->where('sender_id', $currentUserId)
                ->where('receiver_id', $userId);
        })->orWhere(function ($query) use ($currentUserId, $userId) {
            $query->where('sender_id', $userId)
                ->where('receiver_id', $currentUserId);
        })->get();

        if ($messages->isEmpty()) {
            return response()->json([
                'message' => 'No conversation found with the specified user.'
            ], 404);
        }

        foreach ($messages as $message) {
            $message->delete();
        }

        return response()->json([
            'message' => 'Conversation deleted successfully.'
        ], 200);
    }
}
