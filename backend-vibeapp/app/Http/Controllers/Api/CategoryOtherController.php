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

class CategoryOtherController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        $categoryOther = CategoryOther::where('user_id', Auth::id());

        $searchTerm = $request->input('search', null);
        $perPage = $request->input('per_page', 10);
        $paginate = $request->input('pagination', false);
        $sortColumn = $request->input('sort_column', 'id'); // Kolom untuk pengurutan
        $sortOrder = $request->input('sort_order', 'DESC'); // ASC atau DESC

        $result = PaginationHelper::searchAndPaginate(
            $categoryOther,
            $searchTerm,
            ['title'],
            $perPage,
            $paginate,
            $sortColumn,
            $sortOrder
        );

        foreach ($result['data'] as $key => $value) {
            $vib = VibeOther::where(['category_other_id' => $value->id])->orderBy('rating','DESC')->inRandomOrder()->first();
            $value->image = $vib->image_url ?? null;
        }

        return response()->json($result);
    }

    public function category_other_user(Request $request, $username)
    {
        try {

            $user = User::where('username', $username)->firstOrFail();

            $categoryOther = CategoryOther::with('user')->where('user_id', $user->id);

            // $data = $dataCategory;

            // return response()->json($data);
            $searchTerm = $request->input('search', null);
            $perPage = $request->input('per_page', 10);
            $paginate = $request->input('pagination', false);
            $sortColumn = $request->input('sort_column', 'id'); // Kolom untuk pengurutan
            $sortOrder = $request->input('sort_order', 'DESC'); // ASC atau DESC

            $result = PaginationHelper::searchAndPaginate(
                $categoryOther,
                $searchTerm,
                ['title'],
                $perPage,
                $paginate,
                $sortColumn,
                $sortOrder
            );

            // foreach ($categoryOther as $key => $value) {
            //     $categoryOther[$key]['vibes'] =  'a'; //VibeOther::where('category_other_id', $value->id)->where('user_id', $user->id)->orderBy('created_at', 'DESC')->limit(3)->get();
            // }
            foreach ($result['data'] as $key => $value) {
                $result['data'][$key]['vibes'] =
                    VibeOther::with('user')->where('category_other_id', $value->id)->orderBy('rating','DESC')->where('user_id', $user->id)->orderBy('created_at', 'DESC')->limit(3)->get();
                // $vib = Vibe::where('category_id', $value->id)->inRandomOrder()->first();
            }

            return response()->json($result);
        } catch (\Exception $e) {
            // Handle case where user is not found
            return response()->json([
                'error' =>
                $e->getMessage(),
            ], 404);
        }
    }

    public function category_other_user_detail(Request $request, $username, $category_other_id)
    {
        try {

            $user = User::where('username', $username)->firstOrFail();

            // $categoryOther = CategoryOther::with('user')->where('user_id', $user->id);

            // $data = $dataCategory;

            // return response()->json($data);
            $searchTerm = $request->input('search', null);
            $perPage = $request->input('per_page', 10);
            $paginate = $request->input('pagination', false);
            $sortColumn = $request->input('sort_column', 'id'); // Kolom untuk pengurutan
            $sortOrder = $request->input('sort_order', 'DESC'); // ASC atau DESC

            $categoryOther = VibeOther::with(['category', 'user'])->where('category_other_id', $category_other_id)->where('user_id', $user->id)->orderBy('created_at', 'DESC');

            $result = PaginationHelper::searchAndPaginate(
                $categoryOther,
                $searchTerm,
                ['title', 'desc', 'comment'],
                $perPage,
                $paginate,
                $sortColumn,
                $sortOrder
            );

            // foreach ($categoryOther as $key => $value) {
            //     $categoryOther[$key]['vibes'] =  'a'; //VibeOther::where('category_other_id', $value->id)->where('user_id', $user->id)->orderBy('created_at', 'DESC')->limit(3)->get();
            // }
            // foreach ($result['data'] as $key => $value) {
            //     $result['data'][$key]['vibes'] =
            //         VibeOther::where('category_other_id', $value->id)->where('user_id', $user->id)->orderBy('created_at', 'DESC')->limit(3)->get();
            //     // $vib = Vibe::where('category_id', $value->id)->inRandomOrder()->first();
            // }

            return response()->json($result);
        } catch (\Exception $e) {
            // Handle case where user is not found
            return response()->json([
                'error' =>
                $e->getMessage(),
            ], 404);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $request->validate([
            'title' => 'required',
        ]);

        $userId = Auth::id();

        DB::beginTransaction();
        try {
            CategoryOther::create([
                'title' => $request->title,
                'user_id' => $userId,
            ]);
            DB::commit();
            return response()->json(['message' => 'Category Other add successfully'], 201);
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
    public function show(Request $request, $id)
    {
        $searchTerm = $request->input('search', null);
        $perPage = $request->input('per_page', 10);
        $paginate = $request->input('pagination', false);
        $sortColumn = $request->input('sort_column', 'id'); // Kolom untuk pengurutan
        $sortOrder = $request->input('sort_order', 'DESC'); // ASC atau DESC

        $vibeOther = VibeOther::with(['category','user'])->where('category_other_id', $id);

        $result = PaginationHelper::searchAndPaginate(
            $vibeOther,
            $searchTerm,
            ['title', 'desc', 'comment'],
            $perPage,
            $paginate,
            $sortColumn,
            $sortOrder
        );

        return response()->json($result);
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
        DB::beginTransaction();

        try {
            $categoryOther = CategoryOther::findOrFail($id);

            $validated = $request->validate([
                'title' => 'required|string|max:255',
            ]);

            $categoryOther->update($validated);

            DB::commit();
            return response()->json(['message' => 'Update successfully.'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to update.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {

        DB::beginTransaction();
        try {

            $categoryOther = CategoryOther::findOrFail($id);
            $categoryOther->delete();

            DB::commit();

            return response()->json([
                'message' => 'Deleted successfully.'
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to delete.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
