<?php

namespace App\Http\Controllers\Api;

use App\Helpers\FirebaseNotificationHelper;
use App\Helpers\PaginationHelper;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Friendship;
use App\Models\User;
use App\Models\Vibe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FriendshipController extends Controller
{
    protected $firebaseHelper;

    public function __construct(FirebaseNotificationHelper $firebaseHelper)
    {
        $this->firebaseHelper = $firebaseHelper;
    }

    public function
    sendSingleNotification(Request $request)
    {
        $deviceToken = $request->fcm_token;
        $title = "Hello!";
        $body = "This is a test notification.";

        $result = $this->firebaseHelper->sendNotification($deviceToken, $title, $body);
        return response()->json($result);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $userId = Auth::id();

        $friendships = Friendship::where('user_id_1', $userId)
            ->orWhere('user_id_2', $userId)
            ->with(['user1' => function ($query) {
                $query->where('deleted_at','=', null);
            }, 'user2' => function ($query) {
                $query->where('deleted_at','=', null);
            }]);

        $searchTerm = $request->input('search', null);
        $perPage = $request->input('per_page', 10);
        $paginate = $request->input('pagination', false);
        $sortColumn = $request->input('sort_column', 'id'); // Kolom untuk pengurutan
        $sortOrder = $request->input('sort_order', 'DESC'); // ASC atau DESC


        $result = PaginationHelper::searchAndPaginateFriendship(
            $friendships,
            $searchTerm,
            ['name'],
            $perPage,
            $paginate,
            $sortColumn,
            $sortOrder,
            ['user2', 'user1']
        );

        // foreach ($result['data'] as $friendship) {
        //     $friendship->user1->followers = $friendship->user1->followers()->count();
        //     $friendship->user1->following = $friendship->user1->following()->count();
        //     $friendship->user2->followers = $friendship->user2->followers()->count();
        //     $friendship->user2->following = $friendship->user2->following()->count();
        // }

        // $friendList = collect($result['data'])->map(function ($friendship) use ($userId) {
        //     $friend = $friendship->user_id_1 === $userId
        //         ? $friendship->user2 // Jika user_id_1 adalah user saat ini, maka user_id_2 adalah teman
        //         : $friendship->user1; // Jika user_id_2 adalah user saat ini, maka user_id_1 adalah teman
        //     $friend->status_friendship = $friendship->status;
        //     return $friend;
        // });

        // $result['data'] = $friendList;

        return response()->json($result);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'friend_id' => 'required|exists:users,id',
        ]);

        $friendId = $validated['friend_id'];
        $userId = Auth::id();

        // Validasi: Pastikan friend_id tidak sama dengan user_id
        if ($friendId == $userId) {
            return response()->json([
                'message' => 'You cannot send a friend request to yourself.'
            ], 400); // Bad Request
        }

        // Cek jika sudah ada hubungan
        $existing = Friendship::where(function ($query) use ($userId, $friendId) {
            $query->where('user_id_1', $userId)
                ->where('user_id_2', $friendId);
        })
            // ->where(function ($query) use ($userId, $friendId) {
            //     $query->where('user_id_1', $friendId)
            //         ->where('user_id_2', $userId);
            // })
            ->first();

        if ($existing) {
            return response()->json(['message' => 'Friend request already sent or existing'], 409);
        }

        // Cek exist user
        $friend = User::find($friendId);
        DB::beginTransaction();
        try {
            // Kirim permintaan pertemanan
            $friendship = Friendship::create([
                'user_id_1' => $userId,
                'user_id_2' => $friendId,
                'status' => 'accepted',
            ]);

            $deviceToken = $friend->fcm_token;
            $title = "Permintaan Pertemanan Baru";
            $body = $friend->username . "Membuat Pertemanan Baru";
            $notif = null;
            if ($deviceToken != null) {
                $notif = $this->firebaseHelper->sendNotification($deviceToken, $title, $body);
            }

            DB::commit();
            return response()->json(['message' => 'Friend request sent', 'notif' => $notif, 'friend' => $friendship], 201);
        } catch (\Exception $e) {
            // Rollback the transaction if anything goes wrong
            DB::rollBack();
            // Handle the exception
            return response()->json(['error' => $e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($username)
    {
        try {
            // Fetch the friend by username
            $friend = User::with(['mbti', 'zodiac', 'relationship'])->where('username', $username)->where('deleted_at','=',null)->firstOrFail();

            // Check if the authenticated user is friends with the requested user
            // $isFriend = Auth::user()->scopeHasFriendship()
            //     ->where('user_id_1', $friend->id)
            //     ->orWhere('user_id_2', $friend->id)
            //     ->wherePivot('status', 'accepted')
            //     ->exists();

            $friend->isFollowing = auth()->user()->isFollowing($friend);
            $friend->isFollower = auth()->user()->isFollower($friend);
            $friend->follower = $friend->followers()->count();
            $friend->following = $friend->following()->count();

            $data = array();
            $data['profile'] = $friend;

            $dataCategory = Category::get();
            foreach ($dataCategory as $key => $value) {
                $dataCategory[$key]['vibes'] =  Vibe::where('category_id', $value->id)->where('blocked', 0)->where('user_id', $friend->id)->orderBy('created_at', 'DESC')->limit(3)->get();
            }

            $data['vibe_category'] = $dataCategory;

            return response()->json($data);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            // Handle case where user is not found
            return response()->json([
                'error' => 'User not found',
            ], 404);
        } catch (\Exception $e) {
            // Handle any other exceptions
            return response()->json([
                'error' => 'An unexpected error occurred. Please try again.',
            ], 500);
        }
    }

    public function detail(Request $request, $userName, $categoryId)
    {
        try {
            $searchTerm = $request->input('search', null);
            $perPage = $request->input('per_page', 10);
            $paginate = $request->input('pagination', false);
            $sortColumn = $request->input('sort_column', 'id'); // Kolom untuk pengurutan
            $sortOrder = $request->input('sort_order', 'DESC'); // ASC atau DESC

            $userId = User::where('username', $userName)->firstOrFail('id');

            $vibe = Vibe::with(['category','user'])->where('category_id', $categoryId)->where('blocked', 0)->where('user_id', $userId->id);

            $result = PaginationHelper::searchAndPaginate(
                $vibe,
                $searchTerm,
                ['title', 'desc', 'comment'],
                $perPage,
                $paginate,
                $sortColumn,
                $sortOrder
            );

            return response()->json($result);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            // Handle case where user is not found
            return response()->json([
                'error' => 'User not found',
            ], 404);
        } catch (\Exception $e) {
            // Handle any other exceptions
            return response()->json([
                'error' => 'An unexpected error occurred. Please try again.',
            ], 500);
        }
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
        $friendship = Friendship::where(function ($query) use ($id) {
            $query->where('id', $id)
                ->where('user_id_1', Auth::id());
        })->orWhere(function ($query) use ($id) {
            $query->where('id', $id)
                ->where('user_id_2', Auth::id());
        })->first();

        if (!$friendship) {
            return response()->json(['message' => 'Friendship not found'], 404);
        }

        $friendship->delete();

        return response()->json(['message' => 'Friendship deleted'], 200);
    }

    public function acceptFriendRequest(Request $request, $friendId)
    {
        $userId = Auth::id();

        // Periksa apakah ada permintaan pertemanan dengan status 'pending'
        $friendship = Friendship::where(function ($q) use ($userId, $friendId) {
            $q->where('user_id_1', $friendId)
                ->where('user_id_2', $userId);
        })->where('status', 'pending')->first();

        // Jika tidak ditemukan, kembalikan respons gagal
        if (!$friendship) {
            return response()->json([
                'message' => 'No pending friend request found from this user.'
            ], 404);
        }

        // Ubah status menjadi 'accepted'
        $friendship->update(['status' => 'accepted']);

        return response()->json([
            'message' => 'Friend request accepted successfully.',
            'friendship' => $friendship
        ], 200);
    }

    public function searchFriends(Request $request)
    {

        $query = $request->query('search');
        $userId = Auth::id();

        // Ambil semua ID teman
        $friendIds = Friendship::where(function ($q) use ($userId) {
            $q->where('user_id_1', $userId)->orWhere('user_id_2', $userId);
        })
            ->pluck('user_id_1', 'user_id_2')
            ->flatten()
            ->unique()
            ->filter(fn($id) => $id !== $userId);

        // Tambahkan user_id sendiri ke daftar pengecualian
        $excludedIds = $friendIds->merge([$userId]);

        // // Cari pengguna yang bukan teman dan bukan diri sendiri
        $nonFriends = User::withCount(['followers as total_followers', 'following as total_following'])->when($query, function ($q) use ($query) {
            // $q->where('name', 'LIKE', "%$query%")
            // ->orWhere('email', 'LIKE', "%$query%");
        }); //->paginate(25)->withQueryString();

        $searchTerm = $request->input('search', null);
        $perPage = $request->input('per_page', 10);
        $paginate = $request->input('pagination', false);
        $sortColumn = $request->input('sort_column', 'id'); // Kolom untuk pengurutan
        $sortOrder = $request->input('sort_order', 'DESC'); // ASC atau DESC

        $result = PaginationHelper::searchAndPaginate(
            $nonFriends,
            $searchTerm,
            ['name', 'email'],
            $perPage,
            $paginate,
            $sortColumn,
            $sortOrder,
            []
        );

        return response()->json($result);
    }
}
