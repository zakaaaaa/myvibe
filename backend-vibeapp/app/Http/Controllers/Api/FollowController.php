<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class FollowController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
    public function show($id)
    {
        //
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

    public function follow(Request $request, User $user)
    {
        $currentUser = auth()->user();

        // Cek apakah pengguna mencoba mengikuti dirinya sendiri
        if ($currentUser->id === $user->id) {
            return response()->json(['message' => 'You cannot follow yourself.'], 400);
        }

        if (!$currentUser->isFollowing($user)) {
            $currentUser->follow($user);

            return response()->json(['message' => 'User followed successfully!']);
        }

        return response()->json(['message' => 'Already following this user.']);
    }

    public function unfollow(Request $request, User $user)
    {
        $currentUser = auth()->user();

        if ($currentUser->isFollowing($user)) {
            $currentUser->unfollow($user);

            return response()->json(['message' => 'User unfollowed successfully!']);
        }

        return response()->json(['message' => 'Not following this user.']);
    }

    public function followers(User $user)
    {
        $currentUser = auth()->user();
        // $followers = $user->followers()->get();
        $followers = $user->followers->map(function ($follower) use ($currentUser) {
            $follower->is_following = $currentUser->isfollowing($follower);
            return $follower;
        });
        return response()->json(['followers' => $followers]);
    }

    public function following(User $user)
    {
        $currentUser = auth()->user();
        $following = $user->following->map(function ($following) use ($currentUser) {
            $following->is_follower = $currentUser->isfollower($following);
            return $following;
        });
        return response()->json(['following' => $following]);
    }
}
