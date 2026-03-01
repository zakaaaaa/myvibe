<?php

namespace App\Http\Controllers\Api;

use App\Helpers\PaginationHelper;
use App\Http\Controllers\Controller;
use App\Models\CategoryOther;
use App\Models\User;
use App\Models\VibeOther;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class VibeOtherController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        $userId = Auth::id();

        $vibes = VibeOther::with(['user'])->where('user_id', $userId);

        $searchTerm = $request->input('search', null);
        $perPage = $request->input('per_page', 10);
        $paginate = $request->input('pagination', false);
        $sortColumn = $request->input('sort_column', 'id'); // Kolom untuk pengurutan
        $sortOrder = $request->input('sort_order', 'DESC'); // ASC atau DESC

        $categoryOther = CategoryOther::where('user_id', Auth::id());

        $result = PaginationHelper::searchAndPaginate(
            // $vibes,
            $categoryOther,
            $searchTerm,
            ['title', 'desc', 'comment'],
            $perPage,
            $paginate,
            $sortColumn,
            $sortOrder,
        );

        foreach ($result['data'] as $key => $value) {
            $value->vibes = VibeOther::with(['category','user'])->where(['category_other_id' => $value->id])->orderBy('rating','DESC')->inRandomOrder()->limit(3)->get();
        }
        $data = $result['data'];
        $dataArry=[];
        
        foreach ($data as $value) {
            foreach ($value->vibes as $value1) {
                array_push($dataArry, $value1);
            }
        }
        $result['data']=$dataArry;

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
        DB::beginTransaction();
        try {
            $request->validate([
                'category_other_id' => 'required',
                'image_url' => 'required|string',
                'rating' => 'required',
                'title' => 'required',
                'desc' => 'required',

            ]);

            $user = Auth::user();
            $uuid = Str::uuid();

            // $vibe = VibeOther::create([
            //     'id' => $uuid,
            //     'user_id' => $user->id,
            //     'category_other_id' => $request->category_other_id,
            //     'image_url' => $request->image_url,
            //     'comment' => $request->comment,
            //     'rating' => $request->rating,
            //     'title' => $request->title,
            //     'desc' => $request->desc,
            //     'path' => $user->username . '/' . $uuid
            // ]);

            $vibe = VibeOther::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'category_other_id' => $request->category_other_id,
                    'image_url' => $request->image_url,
                ],  // Unique key
                [
                    'id' => $uuid,
                    'user_id' => $user->id,
                    'category_other_id' => $request->category_other_id,
                    'image_url' => $request->image_url,
                    'comment' => $request->comment,
                    'rating' => $request->rating,
                    'title' => $request->title,
                    'desc' => $request->desc,
                    'path' => $user->username . '/' . $uuid
                ]
            );

            DB::commit();
            return response()->json(['message' => 'Vibe Other add successfully.', 'data' => $vibe], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($username = null, $vibe_id = null)
    {

        $user = User::where('username', $username)->first();
        $vibe = VibeOther::with(['category', 'user'])->where('id', $vibe_id)->where('user_id', $user->id ?? null)->orderBy('category_other_id', 'asc')->first();

        if (!$vibe) {
            return response()->json([
                'message' => 'Vibe Other not found.',
            ], 404);
        }

        return response()->json($vibe);
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
}
