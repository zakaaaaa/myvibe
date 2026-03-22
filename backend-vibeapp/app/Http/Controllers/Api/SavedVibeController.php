<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SavedVibe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SavedVibeController extends Controller
{
    public function toggle(Request $request)
    {
        $request->validate([
            'vibe_id' => 'required|string|exists:vibes,id',
        ]);

        $userId = Auth::id();
        $vibeId = $request->vibe_id;

        $existing = SavedVibe::where('user_id', $userId)->where('vibe_id', $vibeId)->first();

        if ($existing) {
            $existing->delete();
            return response()->json(['message' => 'Vibe unsaved', 'saved' => false], 200);
        }

        SavedVibe::create(['user_id' => $userId, 'vibe_id' => $vibeId]);
        return response()->json(['message' => 'Vibe saved', 'saved' => true], 201);
    }

    public function index()
    {
        $userId = Auth::id();

        $saved = SavedVibe::where('user_id', $userId)
            ->with(['vibe', 'vibe.user', 'vibe.category'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($item) {
                return $item->vibe;
            })
            ->filter();

        return response()->json($saved->values(), 200);
    }

    public function check($vibeId)
    {
        $userId = Auth::id();
        $exists = SavedVibe::where('user_id', $userId)->where('vibe_id', $vibeId)->exists();
        return response()->json(['saved' => $exists], 200);
    }
}
