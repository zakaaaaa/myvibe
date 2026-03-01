<?php

namespace App\Http\Controllers\Api;

use App\Helpers\PaginationHelper;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Vibe;
use App\Models\VibeOther;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        // $dataCategory = Category::get();
        // foreach ($dataCategory as $key => $value) {
        //     $dataCategory[$key]['img'] =  Vibe::where('category_id', $value->id)->first();
        // }

        $searchTerm = $request->input('search', null);
        $perPage = $request->input('per_page', 10);
        $paginate = $request->input('pagination', false);
        $sortColumn = $request->input('sort_column', 'id'); // Kolom untuk pengurutan
        $sortOrder = $request->input('sort_order', 'DESC'); // ASC atau DESC

        $result = PaginationHelper::searchAndPaginate(
            Category::query(),
            $searchTerm,
            ['title'],
            $perPage,
            $paginate,
            $sortColumn,
            $sortOrder
        );

        foreach ($result['data'] as $key => $value) {
            if($value->id== 9 ){
                $vib1 = VibeOther::orderBy('rating','DESC')->inRandomOrder()->first();
                $value->image = $vib1->image_url ?? null;
            }else{
                $vib = Vibe::where(['category_id' => $value->id, 'blocked' => 0])->orderBy('rating','DESC')->inRandomOrder()->first();
                $value->image = $vib->image_url ?? null;
            }
            
        }

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
        $category = Category::findOrFail($id);
        return response()->json($category, 200);
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
