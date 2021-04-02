<?php

namespace App\Http\Controllers;

use App\Models\Favourite;
use App\Models\Place;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FavouriteController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index()
    {
        $favourites = Favourite::paginate(5);
        return response()->json($favourites,200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Support\Facades\Request $request
     * @return Response|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required',
            'place_id' => 'required',
        ]);

        $user = Favourite::create($request->all());
        return response($user, 201);
    }

    /**
     * @return JsonResponse
     */
    public function show()
    {
        try {
            return Favourite::where('user_id', auth()->user()->id);
        } catch (ModelNotFoundException $exception) {
            return response()->json(['message' => 'Favourites Are Not Found'], 201);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $favourite = Favourite::findOrFail($id);
            $favourite->delete();
            return response()->json(['message' => 'Favourite is deleted successfully'], 200);
        } catch (ModelNotFoundException $ex) {
            return response()->json(['error' => 'Favorite Is Not Found'], 404);
        }
    }
}
