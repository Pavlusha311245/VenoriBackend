<?php

namespace App\Http\Controllers;

use App\Models\Favourite;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

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
     * @return Response
     */
    public function index()
    {
        return Favourite::paginate(5);
    }

    /**
     * The method adds a new favourite place
     *
     * @param Request $request
     * @return JsonResponse|Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required',
            'place_id' => 'required',
        ]);

        $user = Favourite::create($request->all());

        return response()->json($user, 201);
    }

    /**
     * The method returns favourite places for current authorization user
     *
     * @return Response
     */
    public function show()
    {
        return Favourite::where('user_id', auth()->user()->id);
    }

    /**
     * The method removes favourite place by id
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy($id)
    {
        $favourite = Favourite::findOrFail($id);
        $favourite->delete();

        return response()->json(['message' => 'Favourite is deleted successfully'], 200);
    }
}
