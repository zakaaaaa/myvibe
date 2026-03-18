<?php

use App\Http\Controllers\Api\Auth\GoogleAuthController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\CategoryOtherController;
use App\Http\Controllers\Api\FollowController;
use App\Http\Controllers\Api\FriendshipController;
use App\Http\Controllers\Api\HomeController;
use App\Http\Controllers\Api\MbtiController;
use App\Http\Controllers\Api\MessagesController;
use App\Http\Controllers\Api\RelationshipController;
use App\Http\Controllers\Api\ReportVibeController;
use App\Http\Controllers\Api\SpotifyController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\VibeController;
use App\Http\Controllers\Api\VibeOtherController;
use App\Http\Controllers\Api\ZodiacController;
use App\Models\Mbti;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::get('/email/verify/{id}/{hash}', [AuthController::class, 'verify'])->name('verification.verify');
Route::post('/email/resend', [AuthController::class, 'resend']);

Route::post('/forgot-password', [AuthController::class, 'sendResetLinkEmail']);
Route::post('/reset-password', [AuthController::class, 'reset']);

Route::prefix('auth')->group(function () {
    // Route::get('login/google', [GoogleAuthController::class, 'redirectToGoogle'])->middleware('web');
    Route::get('login/google/callback', [GoogleAuthController::class, 'handleGoogleCallback']);
    Route::post('login/google', [GoogleAuthController::class, 'registerGoogle']);
});
Route::get('/proxy', [\App\Http\Controllers\Api\ProxyController::class, 'proxy']);
Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/profile', function (Request $request) {
        return $request->user();
    });

    Route::apiResource('/category', CategoryController::class);
    Route::apiResource('/user', UserController::class);
    Route::apiResource('/zodiac', ZodiacController::class);
    Route::apiResource('/mbti', MbtiController::class);
    Route::apiResource('/relationship', RelationshipController::class);

    Route::get('/cors', function(){
        return DB::table('settings')->where('id',1)->first('value as url');
    });

   

    Route::apiResource('/friendship', FriendshipController::class);
    Route::get('/friendship/{username}', [FriendshipController::class, 'show'])->name('friend.profile');
    Route::get('/friendship/{username}/{category}', [FriendshipController::class, 'detail']);

    Route::apiResource('/home', HomeController::class);

    Route::get('/home_detail', [HomeController::class, 'detail']);

    Route::apiResource('/vibe', VibeController::class);
    Route::get('/vibe_explore', [VibeController::class, 'explore']);
    Route::get('/vibe/{username}/{vibe_id}', [VibeController::class, 'show']);

    Route::get('/friendship_search', [FriendshipController::class, 'searchFriends']);

    Route::POST('/send_notification', [FriendshipController::class, 'sendSingleNotification']);

    Route::get('/generate_token_spotify', [SpotifyController::class, 'getToken']);
    Route::post('/friendship/{friendId}/accept', [FriendshipController::class, 'acceptFriendRequest']);

    Route::put('/user', [UserController::class, 'updateProfile']);
    Route::POST('/hapus_akun', [UserController::class, 'hapusAkun']);

    // Route::apiResource('/messages', MessagesController::class);
    Route::post('/messages', [MessagesController::class, 'sendMessage']);
    Route::get('/messages/conversation/{userId}', [MessagesController::class, 'getConversation']);
    Route::get('/messages/conversations', [MessagesController::class, 'getConversations']);
    Route::delete('/messages/{messageId}', [MessagesController::class, 'deleteMessage']);
    Route::delete('/messages/conversation/{userId}', [MessagesController::class, 'deleteConversation']);

    Route::post('/follow/{user}', [FollowController::class, 'follow']);
    Route::post('/unfollow/{user}', [FollowController::class, 'unfollow']);
    Route::get('/followers/{user}', [FollowController::class, 'followers']);
    Route::get('/following/{user}', [FollowController::class, 'following']);

    Route::apiResource('/category_other', CategoryOtherController::class);
    Route::get('/category_other_user/{username}', [CategoryOtherController::class, 'category_other_user']);
    Route::get('/category_other_user_detail/{username}/{category_other_id}', [CategoryOtherController::class, 'category_other_user_detail']);

    Route::apiResource('/vibe_other', VibeOtherController::class);
    Route::get('/vibe_other_explore', [VibeOtherController::class, 'explore']);
    Route::get('/vibe_other/{username}/{vibe_id}', [VibeOtherController::class, 'show']);

    Route::post('/validate_report/{report_id}', [ReportVibeController::class, 'validateReport']);
    Route::apiResource('/report_vibe', ReportVibeController::class);

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});