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
            // Get the user information from Google
            $googleUser = Socialite::driver('google')->stateless()->user();

            // Check if the user already exists in the database
            $user = User::where('email', $googleUser->getEmail())->first();

            if (!$user) {
                // Register the user if they don't exist
                $user = User::create([
                    'name' => $googleUser->getName(),
                    'email' => $googleUser->getEmail(),
                    'password' => Hash::make(Str::random(24)),  // Random password (Google will handle login)
                ]);
                $user->markEmailAsVerified();
            }

            // Log the user in
            Auth::login($user);

            // Generate a token for the user (you can use Laravel Passport or Sanctum for API tokens)
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'token' => $token,
                'user' => $user,
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Unable to authenticate with Google'], 400);
        }
    }

    public function registerGoogle(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required_if:user_apple_id,null|string|max:255',
            'user_apple_id' => 'required_if:email,null|string',
            'email' => 'required_if:user_apple_id,null|string|email|max:255',
        ]);
        DB::beginTransaction();
        try {
            // Check if the user already exists in the database
            if (isset($validated['user_apple_id']) && $validated['user_apple_id'] != null) {
                // If user_apple_id exists, search for the user by apple id
                $user = User::where('user_apple_id', $validated['user_apple_id'])->first();
            } else {
                // If user_apple_id is null, search for the user by email
                if (isset($validated['email'])) {
                    $user = User::where('email', $validated['email'])->first();
                } else {
                    return response()->json(['error' => 'Email or Apple ID is required'], 400);
                }
            }

            if (!$user) {
                // Register the user if they don't exist
                // $user = User::create([
                //     'name' => $validated['name'],
                //     // 'username' => $validated['username'],
                //     'email' => $validated['email'],
                //     'user_apple_id' => $validated['user_apple_id'],
                //     'regis_from' => 'google',
                //     'password' => Hash::make(Str::random(24)),  // Random password (Google will handle login)
                // ]);

                $user = User::updateOrCreate(
                    [
                        'email' => $validated['email'] ?? null, // Use email if available
                    ],
                    [
                        'user_apple_id' => $validated['user_apple_id'] ?? null, // Use Apple ID if available
                        'name' => $validated['name'],
                        'regis_from' => 'google',  // Mark the registration source
                        'password' => Hash::make(Str::random(24)),  // Generate a random password for the user (Google handles the login)
                    ]
                );

                $user->markEmailAsVerified();
            }

            // Log the user in
            Auth::login($user);

            // Generate a token for the user (you can use Laravel Passport or Sanctum for API tokens)
            $token = $user->createToken('auth_token')->plainTextToken;
            DB::commit();
            return response()->json([
                'message' => 'Login successful',
                'token' => $token,
                'user' => $user,
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Unable to authenticate with Google','msg' => $e->getMessage()], 400);
        }
    }
}
