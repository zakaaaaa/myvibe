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

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
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

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
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

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function sendMessage(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'message' => 'required|string|max:1000',
        ]);
        $userId = Auth::id();
        $receiverId = $request->receiver_id;

        // Periksa apakah pengguna adalah teman
        // $isFriend = \App\Models\Friendship::where(function ($query) use ($userId, $receiverId) {
        //     $query->where('user_id_1', $userId)
        //         ->where('user_id_2', $receiverId)
        //         ->where('status', 'accepted');
        // })->orWhere(function ($query) use ($userId, $receiverId) {
        //     $query->where('user_id_1', $receiverId)
        //         ->where('user_id_2', $userId)
        //         ->where('status', 'accepted');
        // })->exists();

        $friend = User::with(['mbti', 'zodiac', 'relationship'])->where('id', $receiverId)->firstOrFail();
        $isFriend = auth()->user()->isFollowing($friend) || $friend->isFollowing(auth()->user());

        if (!$isFriend) {
            return response()->json([
                'message' => 'You can only send messages to friends.'
            ], 403); // Forbidden
        }

        DB::beginTransaction();
        try {
            // Kirim pesan jika validasi lolos
            $message = Messages::create([
                'sender_id' => $userId,
                'receiver_id' => $receiverId,
                'message_text' => $request->message,
            ]);

            $receiver  = User::find($receiverId);

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
            // Rollback the transaction if anything goes wrong
            DB::rollBack();
            // Handle the exception
            return response()->json(['error' => $e->getMessage()]);
        }
    }

    // Ambil semua pesan dalam percakapan antara dua pengguna
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

    // Ambil daftar semua percakapan pengguna
    public function getConversations()
    {
        $userId = Auth::id();

        // $messages = Messages::where('sender_id', $currentUserId)
        //     ->orWhere('receiver_id', $currentUserId)
        //     ->with(['sender', 'receiver'])
        //     ->get()
        //     ->groupBy(function ($message) use ($currentUserId) {
        //         return $message->sender_id == $currentUserId ? $message->receiver_id : $message->sender_id;
        //     });

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
                'friend:id,name,username,email,profile_picture' // Assuming `User` model has `name` and `email`
            ])
            ->orderByDesc('last_message_id') // Most recent messages first
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

        // Cari pesan berdasarkan ID
        $message = Messages::find($messageId);

        // Periksa apakah pesan ditemukan dan milik pengguna saat ini
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

        // Hapus pesan
        $message->delete();

        return response()->json([
            'message' => 'Message deleted successfully.'
        ], 200);
    }

    public function deleteConversation($userId)
    {
        $currentUserId = Auth::id();

        // Cari pesan yang terlibat dalam percakapan
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

        // Hapus semua pesan dalam percakapan
        foreach ($messages as $message) {
            $message->delete();
        }

        return response()->json([
            'message' => 'Conversation deleted successfully.'
        ], 200);
    }
}
