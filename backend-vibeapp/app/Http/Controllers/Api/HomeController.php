<?php

namespace App\Http\Controllers\Api;

use App\Helpers\PaginationHelper;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\User;
use App\Models\Vibe;
use App\Models\VibeOther;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $userId = Auth::id();
        $dataCategory = Category::get();
        foreach ($dataCategory as $key => $value) {
            $dataCategory[$key]['vibes'] =  Vibe::with('user')->where('category_id', $value->id)->where('blocked', 0)->where('user_id', $userId)->orderBy('created_at', 'DESC')->limit(3)->get();
        }
        $data = $dataCategory;

        return response()->json($data);
    }

    public function detail(Request $request)
    {
        $userId = Auth::id();
        $searchTerm = $request->input('search', null);
        $perPage = $request->input('per_page', 10);
        $categoryId = $request->input('category_id', 1);
        $paginate = $request->input('pagination', false);
        $sortColumn = $request->input('sort_column', 'id'); // Kolom untuk pengurutan
        $sortOrder = $request->input('sort_order', 'DESC'); // ASC atau DESC

        if ($categoryId == 8) {
            $vibe = VibeOther::with(['category','user'])->where('user_id', $userId)->orderBy('rating', 'DESC');
        } else {
            $vibe = Vibe::with(['category','user'])->where('blocked', 0)->where('category_id', $categoryId)->where('user_id', $userId)->orderBy('rating', 'DESC');
        }


        $result = PaginationHelper::searchAndPaginate(
            $vibe,
            $searchTerm,
            ['title', 'desc', 'comment'],
            $perPage,
            $paginate,
            $sortColumn,
            $sortOrder,
        );

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
}
