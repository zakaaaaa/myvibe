<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class GoogleAuthController extends Controller
{
    // Redirect to Google OAuth
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    // Handle Google callback
    public function handleGoogleCallback(Request $request)
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();

            $user = User::where('email', $googleUser->getEmail())->first();

            if (!$user) {
                $user = User::create([
                    'name' => $googleUser->getName(),
                    'email' => $googleUser->getEmail(),
                    'password' => Hash::make(Str::random(24)),
                ]);
                $user->markEmailAsVerified();
            }

            Auth::login($user);

            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'token' => $token,
                'user' => $user,
            ], 200);
        } catch (\Exception $e) {
            Log::error('handleGoogleCallback error: ' . $e->getMessage());
            return response()->json(['error' => 'Unable to authenticate with Google'], 400);
        }
    }

    public function registerGoogle(Request $request)
    {
        Log::info('registerGoogle called', [
            'request_data' => $request->all()
        ]);

        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'user_apple_id' => 'nullable|string',
            'email' => 'nullable|string|email|max:255',
        ]);

        // Must have either email or user_apple_id
        if (empty($validated['email']) && empty($validated['user_apple_id'])) {
            Log::warning('registerGoogle: no email or apple_id provided');
            return response()->json(['error' => 'Email or Apple ID is required'], 400);
        }

        DB::beginTransaction();
        try {
            $user = null;

            // Search by apple_id first, then by email
            if (!empty($validated['user_apple_id'])) {
                $user = User::where('user_apple_id', $validated['user_apple_id'])->first();
                Log::info('registerGoogle: searched by apple_id', ['found' => $user ? true : false]);
            }
            if (!$user && !empty($validated['email'])) {
                $user = User::where('email', $validated['email'])->first();
                Log::info('registerGoogle: searched by email', ['found' => $user ? true : false]);
            }

            if (!$user) {
                // New user — need at least name and email to create
                if (empty($validated['name']) || empty($validated['email'])) {
                    Log::warning('registerGoogle: new user but missing name or email');
                    return response()->json(['error' => 'Name and email are required for new users'], 400);
                }

                $user = User::create([
                    'name' => $validated['name'],
                    'email' => $validated['email'],
                    'user_apple_id' => $validated['user_apple_id'] ?? null,
                    'regis_from' => !empty($validated['user_apple_id']) ? 'apple' : 'google',
                    'password' => Hash::make(Str::random(24)),
                ]);
                $user->markEmailAsVerified();
                Log::info('registerGoogle: new user created', ['user_id' => $user->id]);
            } else {
                // Existing user — update apple_id if provided and not set yet
                if (!empty($validated['user_apple_id']) && empty($user->user_apple_id)) {
                    $user->user_apple_id = $validated['user_apple_id'];
                    $user->save();
                    Log::info('registerGoogle: updated apple_id for existing user', ['user_id' => $user->id]);
                }
            }

            Auth::login($user);
            $token = $user->createToken('auth_token')->plainTextToken;
            DB::commit();

            Log::info('registerGoogle: login successful', ['user_id' => $user->id]);

            return response()->json([
                'message' => 'Login successful',
                'token' => $token,
                'user' => $user,
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('registerGoogle error: ' . $e->getMessage(), [
                'request_data' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['error' => 'Unable to authenticate', 'msg' => $e->getMessage()], 400);
        }
    }
}