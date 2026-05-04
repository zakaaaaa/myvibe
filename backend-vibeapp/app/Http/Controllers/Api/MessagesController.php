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
    /**
     * Whitelist host GIPHY untuk validasi attachment GIF.
     * Mencegah user kirim URL random sebagai "gif".
     */
    private const GIPHY_ALLOWED_HOSTS = [
        'media.giphy.com',
        'media0.giphy.com',
        'media1.giphy.com',
        'media2.giphy.com',
        'media3.giphy.com',
        'media4.giphy.com',
        'i.giphy.com',
    ];

    /**
     * Tipe attachment yang didukung.
     * 'gif' = GIPHY, 'vibe' = share vibe (existing future), 
     * 'image'/'voice'/'video' = roadmap mid-term.
     */
    private const ALLOWED_ATTACHMENT_TYPES = ['gif', 'vibe', 'image', 'voice', 'video'];

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

    /**
     * Kirim pesan teks atau pesan dengan attachment (gif, vibe, dll).
     *
     * Backward-compatible: client lama yang cuma kirim `message` tetap jalan.
     * Client baru bisa kirim:
     *   - message: text (optional kalau ada attachment)
     *   - attachment_type: 'gif' | 'vibe' | 'image' | 'voice' | 'video'
     *   - attachment_payload: object (struktur tergantung tipe)
     *     untuk gif: { id, url, preview, width, height, title }
     */
    public function sendMessage(Request $request)
    {
        $request->validate([
            'receiver_id'        => 'required|exists:users,id',
            // 'message' jadi nullable karena bisa pesan attachment-only (GIF tanpa caption)
            'message'            => 'nullable|string|max:1000',
            'reply_to_id'        => 'nullable|integer',
            'reply_to_text'      => 'nullable|string|max:1000',
            'attachment_type'    => 'nullable|string|in:' . implode(',', self::ALLOWED_ATTACHMENT_TYPES),
            'attachment_payload' => 'nullable|array',

            // Validasi spesifik untuk GIF
            'attachment_payload.id'      => 'required_if:attachment_type,gif|string|max:64',
            'attachment_payload.url'     => 'required_if:attachment_type,gif|url|max:500',
            'attachment_payload.preview' => 'nullable|url|max:500',
            'attachment_payload.width'   => 'nullable|integer|min:1|max:4096',
            'attachment_payload.height'  => 'nullable|integer|min:1|max:4096',
            'attachment_payload.title'   => 'nullable|string|max:255',
        ]);

        // Pesan harus punya minimal salah satu: text atau attachment
        if (empty($request->message) && empty($request->attachment_type)) {
            return response()->json([
                'message' => 'Message must have either text or an attachment.'
            ], 422);
        }

        // Whitelist host GIPHY untuk attachment tipe gif
        if ($request->attachment_type === 'gif') {
            $url  = $request->input('attachment_payload.url');
            $host = parse_url($url, PHP_URL_HOST);
            if (!in_array($host, self::GIPHY_ALLOWED_HOSTS, true)) {
                return response()->json([
                    'message' => 'Invalid GIF source. Only GIPHY URLs are allowed.'
                ], 422);
            }
        }

        $userId     = Auth::id();
        $receiverId = $request->receiver_id;

        /** @var User $friend */
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
                'sender_id'          => $userId,
                'receiver_id'        => $receiverId,
                'message_text'       => $request->message,
                'reply_to_id'        => $request->reply_to_id,
                'reply_to_text'      => $request->reply_to_text,
                'attachment_type'    => $request->attachment_type,
                'attachment_payload' => $request->attachment_payload,
            ]);

            $receiver = User::find($receiverId);

            $deviceToken = $receiver->fcm_token;
            $title       = "New Message From " . $receiver->username;
            $body        = $this->buildNotificationPreview($message);

            if ($deviceToken != null) {
                $this->firebaseHelper->sendNotification($deviceToken, $title, $body);
            }

            DB::commit();
            return response()->json([
                'message' => 'Message sent successfully.',
                'data'    => $message
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()]);
        }
    }

    /**
     * Bangun preview text untuk push notification.
     * Kalau pesan punya text → pakai text-nya.
     * Kalau pesan attachment-only → pakai label per tipe.
     */
    private function buildNotificationPreview(Messages $message): string
    {
        if (!empty($message->message_text)) {
            return $message->message_text;
        }

        return match ($message->attachment_type) {
            'gif'   => '🎬 Sent a GIF',
            'vibe'  => '✨ Shared a vibe',
            'image' => '📷 Sent a photo',
            'voice' => '🎤 Sent a voice note',
            'video' => '🎥 Sent a video',
            default => 'Sent a message',
        };
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
                    // Tambahin kolom attachment biar preview di chat list akurat
                    $query->select(
                        'id',
                        'sender_id',
                        'receiver_id',
                        'message_text',
                        'attachment_type',
                        'created_at'
                    );
                },
                'friend:id,name,username,email,profile_picture'
            ])
            ->orderByDesc('last_message_id')
            ->get()
            ->map(function ($friend) {
                return [
                    'friend_id'       => $friend->friend_id,
                    'friend'          => $friend->friend,
                    'last_message'    => $this->buildConversationPreview($friend->lastMessage),
                    'last_message_at' => $friend->lastMessage->created_at ?? null,
                ];
            });

        return response()->json($friends, 200);
    }

    /**
     * Preview untuk conversation list (sidebar/chat list).
     * Kalau pesan terakhir attachment, tampilkan label-nya, bukan empty string.
     */
    private function buildConversationPreview($lastMessage): ?string
    {
        if (!$lastMessage) {
            return null;
        }

        if (!empty($lastMessage->message_text)) {
            return Str::limit($lastMessage->message_text, 27);
        }

        return match ($lastMessage->attachment_type) {
            'gif'   => '🎬 GIF',
            'vibe'  => '✨ Vibe',
            'image' => '📷 Photo',
            'voice' => '🎤 Voice note',
            'video' => '🎥 Video',
            default => null,
        };
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