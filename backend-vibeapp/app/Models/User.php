<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Notifications\ResetPasswordNotification;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'mbti_id',
        'zodiac_id',
        'relationship_id',
        'enthusiast',
        'profile_picture',
        'regis_from',
        'fcm_token',
        'deleted_at',
        'user_apple_id'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    // public function sendPasswordResetNotification($token)
    // {
    //     $this->notify(new ResetPasswordNotification($token));
    // }

    public function mbti()
    {
        return $this->belongsTo(Mbti::class, 'mbti_id');
    }

    public function zodiac()
    {
        return $this->belongsTo(Zodiac::class, 'zodiac_id');
    }

    public function relationship()
    {
        return $this->belongsTo(Relationship::class, 'relationship_id');
    }

    public function vibes()
    {
        return $this->hasMany(Vibe::class);
    }

    // Users this user is following
    public function following()
    {
        return $this->belongsToMany(User::class, 'follows', 'follower_id', 'following_id');
    }

    // Users following this user
    public function followers()
    {
        return $this->belongsToMany(User::class, 'follows', 'following_id', 'follower_id');
    }

    public function follow(User $user)
    {
        return $this->following()->attach($user->id);
    }

    public function unfollow(User $user)
    {
        return $this->following()->detach($user->id);
    }

    public function isFollowing(User $user)
    {
        return $this->following()->where('following_id', $user->id)->exists();
    }

    public function isFollower(User $user)
    {
        return $this->followers()->where('follower_id', $user->id)->exists();
    }

    public function isFriend($friendId)
    {
        return DB::table('friendships')
            ->where('status', 'accepted')
            // ->where(function ($query) use ($friendId) {
            //     // $query->where('user_id_1', $this->id);
            //         // ->where('user_id_2', $friendId);
            // })
            ->where(function ($query) use ($friendId) {
                $query->where('user_id_1', $friendId)
                    ->where('user_id_2', $this->id);
            })->exists();
    }
}
