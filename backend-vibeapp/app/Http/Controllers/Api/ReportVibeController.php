<?php

namespace App\Http\Controllers\Api;

use App\Helpers\PaginationHelper;
use App\Http\Controllers\Controller;
use App\Models\Report_vibe;
use App\Models\ReportVibe;
use App\Models\Vibe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReportVibeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $userId = Auth::id();

        $vibes = ReportVibe::with(['user_report:id,name,email','vibe.user','vibe.category'])->where('user_id', $userId);

        $searchTerm = $request->input('search', null);
        $perPage = $request->input('per_page', 10);
        $paginate = $request->input('pagination', false);
        $sortColumn = $request->input('sort_column', 'id'); // Kolom untuk pengurutan
        $sortOrder = $request->input('sort_order', 'DESC'); // ASC atau DESC

        $result = PaginationHelper::searchAndPaginate(
            $vibes,
            $searchTerm,
            ['desc'],
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
            'vibe_id' => 'required',
            'desc' => 'required',
        ]);

        $userId = Auth::id();

        ReportVibe::create([
            'user_id' => $userId,
            'vibe_id' => $request->vibe_id,
            'desc' => $request->desc
        ]);

        return response()->json(['message' => 'Report Vibe successfully.'], 201);
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

    public function validateReport(Request $request, $id)
    {
        DB::beginTransaction();

        try {
            $report_vibe = ReportVibe::findOrFail($id);

            $validated = $request->validate([
                'status' => 'required',
            ]);

            $report_vibe->update($validated);
            if($request->status === "approved"){
                // block vibe
                Vibe::where('id',$report_vibe->vibe_id)->update(['blocked'=>1]);
            }
            
            DB::commit();
            return response()->json(['message' => 'Validate report successfully.'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to validate report.',
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
        $userId = Auth::id();
        $data = ReportVibe::find($id);

        if (!$data) {
            return response()->json([
                'message' => 'Report Vibe not found.'
            ], 404);
        }

        if ($data->user_id !== $userId) {
            return response()->json([
                'message' => 'You do not have permission to delete this.'
            ], 403);
        }

        $data->delete();

        return response()->json([
            'message' => 'Report Vibe deleted successfully.'
        ], 200);
    }
}
