<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Follow;
use App\Models\Messages;
use App\Models\User;
use App\Models\Vibe;
use App\Models\VibeOther;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Image;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $data = User::with(['mbti', 'zodiac', 'relationship'])->where('id', $request->user()->id)->first();

        $data->follower = $data->followers()->count();
        $data->following = $data->following()->count();

        return response()->json($data);
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
    public function update(Request $request) {}

    public function updateProfile(Request $request)
    {
        // Get the authenticated user
        $user = Auth::user();

        // Validate the request data
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|max:255|unique:users,email,' . $user->id,
            'username' => 'sometimes|string|max:255|unique:users,username,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'profile_picture' => 'nullable|image|mimes:jpg,jpeg,png', // For profile picture
            'profile_picture_url' => 'sometimes|string', // For profile picture
            'mbti_id' => 'nullable|integer',
            'zodiac_id' => 'nullable|integer',
            'relationship_id' => 'nullable|integer',
            'enthusiast' => 'nullable|string|max:10',
            'fcm_token' => 'nullable|string',

        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Update user data
        if ($request->has('name')) {
            $user->name = $request->name;
        }

        if ($request->has('username')) {
            $user->username = $request->username;
        }

        // if ($request->has('mbti_id')) {
            $user->mbti_id = $request->mbti_id;
        // }

        // if ($request->has('zodiac_id')) {
            $user->zodiac_id = $request->zodiac_id;
        // }

        // if ($request->has('relationship_id')) {
            $user->relationship_id = $request->relationship_id;
        // }

        if ($request->has('enthusiast')) {
            $user->enthusiast = $request->enthusiast;
        }

        if ($request->has('profile_picture_url')) {
            $user->profile_picture = $request->profile_picture_url;
        }

        if ($request->has('password')) {
            $user->password = bcrypt($request->password);
        }

        if ($request->has('fcm_token')) {
            $user->fcm_token = $request->fcm_token;
        }

        // Handle profile picture upload (optional)
        if ($request->hasFile('profile_picture')) {
            $file = $request->file('profile_picture');

            // Delete old profile picture if exists
            if ($user->profile_picture && Storage::disk('public')->exists($user->profile_picture)) {
                Storage::disk('public')->delete($user->profile_picture);
            }

            $filename = time() . '.' . $file->getClientOriginalExtension();
            // Auto-Crop menggunakan Intervention Image
            $image = Image::make($file->getRealPath());
            $image->fit(300, 300); // Crop ke ukuran 300x300 (square)

            // Simpan gambar yang sudah dicrop ke direktori public/uploads
            $destinationPath = public_path('storage/profile_pictures');
            $image->save($destinationPath . '/' . $filename);

            // Simpan URL atau path ke database jika diperlukan
            $path = 'storage/profile_pictures/' . $filename;

            $user->profile_picture = $path; // Save the file path in the database
        }

        $user->save();

        // Return updated user data
        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully.',
            'user' => $user,
        ]);
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

    public function hapusAkun(Request $request)
    {
        // Get the authenticated user
        $user = Auth::user();
        $user->deleted_at = now();
        $user->save();

        //follow
        //delete vibe
        //delete vibe other
        //message
        
        Follow::where('follower_id',$user->id)->orWhere('following_id', $user->id)->delete();
        Vibe::where('user_id',$user->id)->delete();
        VibeOther::where('user_id',$user->id)->delete();
        Messages::where('sender_id',$user->id)->orWhere('receiver_id', $user->id)->delete();
        
        $request->user()->currentAccessToken()->delete();

        // Return updated user data
        return response()->json([
            'success' => true,
            'message' => 'Your account has been successfully deleted.',
            'user' => $user,
        ]);
    }
}
