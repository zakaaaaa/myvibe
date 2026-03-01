<?php

namespace App\Http\Controllers\Api;

use App\Helpers\PaginationHelper;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\User;
use App\Models\Vibe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class VibeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $userId = Auth::id();

        $vibes = Vibe::with(['category','user'])->where('blocked', 0)->where('user_id', $userId);//->orderBy('created_at', 'DESC');

        $searchTerm = $request->input('search', null);
        $perPage = $request->input('per_page', 10);
        $paginate = $request->input('pagination', false);
        $sortColumn = $request->input('sort_column', 'id'); // Kolom untuk pengurutan
        $sortOrder = $request->input('sort_order', 'DESC'); // ASC atau DESC

        $result = PaginationHelper::searchAndPaginate(
            $vibes,
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
        $request->validate([
            'category_id' => 'required',
            'image_url' => 'required|string',
            'rating' => 'required',
            'title' => 'required',
            'desc' => 'required',

        ]);

        $user = Auth::user();
        $userId = Auth::id();
        $uuid = Str::uuid();

        // $vibe = Vibe::upsert([
        //     [
        //         'id' => $uuid,
        //         'user_id' => $userId,
        //         'category_id' => $request->category_id,
        //         'image_url' => $request->image_url,
        //         'comment' => $request->comment,
        //         'rating' => $request->rating,
        //         'title' => $request->title,
        //         'desc' => $request->desc,
        //         'path' => $user->username . '/' . $uuid
        //     ]
        // ], ['user_id', 'category_id','image_url'], ['comment','rating','title','title','path']);
        
        $vibe = Vibe::updateOrCreate(
            [
                'user_id' => $userId,
                'category_id' => $request->category_id,
                'image_url' => $request->image_url,
                'title' => $request->title,
                'desc' => $request->desc,
            ],  // Unique key
            [
                'id' => $uuid,
                'user_id' => $userId,
                'category_id' => $request->category_id,
                'image_url' => $request->image_url,
                'comment' => $request->comment,
                'rating' => $request->rating,
                'title' => $request->title,
                'desc' => $request->desc,
                'path' => $user->username . '/' . $uuid
            ]
        );

        // $vibe = Vibe::create([
        //     'id' => $uuid,
        //     'user_id' => $userId,
        //     'category_id' => $request->category_id,
        //     'image_url' => $request->image_url,
        //     'comment' => $request->comment,
        //     'rating' => $request->rating,
        //     'title' => $request->title,
        //     'desc' => $request->desc,
        //     'path' => $user->username . '/' . $uuid
        // ]);

        return response()->json(['message' => 'Vibe add successfully.', 'data' => $vibe], 201);
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
        $vibe = Vibe::with(['category', 'user'])->where('id', $vibe_id)->where('user_id', $user->id ?? null)->orderBy('category_id', 'asc')->first();

        if (!$vibe) {
            return response()->json([
                'message' => 'Vibe not found.',
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

    public function explore(Request $request)
    {
        $userId = Auth::id();
        $search = $request->search;

        $searchTerm = $request->input('search', null);
        $perPage = $request->input('per_page', 10);
        $page = $request->input('page', 1);
        $paginate = $request->input('pagination', false);
        $sortColumn = $request->input('sort_column', 'id'); // Kolom untuk pengurutan
        $sortOrder = $request->input('sort_order', 'DESC'); // ASC atau DESC

        // if ($request->has('search')) {
        //     $data = Vibe::whereHas('category', function ($query) use ($search) {
        //         $query->where('title', 'like', '%' . $search . '%');
        //     })->with(['category', 'user'])->where('blocked',0)->orderBy('rating', 'DESC')->inRandomOrder()->take(10)->get()->map(function ($item) {
        //         $item['comment'] = Str::limit($item['comment'], 47);
        //         return $item;
        //     });
        // } else {
            // $data = Vibe::with(['category', 'user'])->where('blocked',0)->orderBy('rating', 'DESC')
            // ->inRandomOrder()->take(10)->get()->map(function ($item) {
            //     $item['comment'] = Str::limit($item['comment'], 47);
            //     return $item;
            // })
            // ;
        // }
        $seed = session('random_seed', rand()); // Generate or retrieve a seed

        $data = Vibe::with(['category', 'user'])->where('blocked',0)->inRandomOrder($seed);//->whereNotIn('id', $request->session()->get('vibe',[]));
        
        // if($paginate) {
        //     $request->session()->push('vibe', $data->pluck('id'));
        // } 
        // else {
        
        //     $request->session()->forget('vibe');
        // }

         $result = PaginationHelper::searchAndPaginate(
            $data,
            $searchTerm,
            ['title', 'desc', 'comment'],
            $perPage,
            $paginate,
            $sortColumn,
            $sortOrder,
        );
        session(['random_seed' => $seed]);
        // $result['data']= $result['data']->shuffle();
        $result['data1']  = $request->session()->get('vibe');
        foreach ($result['data'] as $key => $value) {
            $value->comment = Str::limit($value->comment, 47);
        }

        return response()->json($result);

    }
}
