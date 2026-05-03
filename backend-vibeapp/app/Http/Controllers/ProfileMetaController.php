<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Vibe;
use App\Models\VibeOther; // sesuaikan kalau nama model lain
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProfileMetaController extends Controller
{
    /**
     * Render HTML profile dengan OG meta tags.
     * - Bots (Facebook, WhatsApp, Twitter, Telegram, dll) → render meta tags langsung
     * - User biasa → redirect ke Vue SPA (atau biarkan Nginx handle)
     *
     * Strategi: kita SELALU render HTML dengan meta tags + script redirect.
     * Bots ambil meta tags, browser jalankan redirect ke SPA.
     */
    public function show($username)
    {
        $user = User::where('username', $username)->first();

        if (!$user) {
            // Tetap render fallback supaya tidak 404 di crawler
            return $this->renderFallback($username);
        }

        // Hitung total post (vibes + vibe_others)
        $vibesCount = Vibe::where('user_id', $user->id)->count();

        // Kalau ada model VibeOther, tambah:
        $otherCount = 0;
        if (class_exists(VibeOther::class)) {
            $otherCount = VibeOther::where('user_id', $user->id)->count();
        }
        $totalPosts = $vibesCount + $otherCount;

        // Profile picture URL absolut
        $profilePic = $user->profile_picture
            ? rtrim(config('app.url_frontend', 'https://api.myvibeapp.co'), '/') . '/' . ltrim($user->profile_picture, '/')
            : 'https://myvibeapp.co/default-avatar.png';

        $title = '@' . $user->username . ' on MyVibe';
        $description = $user->name
            . ' — ' . $totalPosts . ' '
            . ($totalPosts === 1 ? 'vibe' : 'vibes')
            . ' shared on MyVibe. Rate Your Favorites.';

        $canonicalUrl = 'https://myvibeapp.co/' . $user->username;

        return response()
            ->view('profile-meta', [
                'title'        => $title,
                'description'  => $description,
                'image'        => $profilePic,
                'url'          => $canonicalUrl,
                'username'     => $user->username,
                'name'         => $user->name,
                'totalPosts'   => $totalPosts,
            ])
            ->header('Content-Type', 'text/html; charset=UTF-8')
            ->header('Cache-Control', 'public, max-age=300'); // cache 5 menit
    }

    private function renderFallback($username)
    {
        return response()->view('profile-meta', [
            'title'       => 'MyVibe — Rate Your Favorites',
            'description' => 'Discover and rate your favorite films, music, books, and more on MyVibe.',
            'image'       => 'https://myvibeapp.co/og-default.png',
            'url'         => 'https://myvibeapp.co/' . $username,
            'username'    => $username,
            'name'        => 'MyVibe',
            'totalPosts'  => 0,
        ])->header('Content-Type', 'text/html; charset=UTF-8');
    }
}