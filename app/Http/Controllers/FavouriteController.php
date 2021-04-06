<?php

namespace App\Http\Controllers;

use App\Models\Favourite;
use App\Models\Place;
use App\Models\User;
use http\Env\Response;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Controller used for add, delete, and show favorite places
 *
 * @package App\Http\Controllers
 */
class FavouriteController extends Controller
{
    /**
     * The method returns a list of all favorite places
     *
     * @return JsonResponse
     */
    public function index()
    {
        $favourites = Favourite::paginate(5);
        return response()->json($favourites,200);
    }

    /**
     * The method adds a new favourite place
     *
     * @param Request $request
     * @return Response
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
     * The method returns favourite places for current authorization user
     *
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
     * The method removes favourite place by id
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id)
    {
        $favourite = Favourite::findOrFail($id);
        $favourite->delete();

        return response()->json(['message' => 'Favourite is deleted successfully'], 200);
    }
}
