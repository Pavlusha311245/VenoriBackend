<?php

namespace App\Http\Controllers;

use App\Models\Place;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

/**
 * Controller used for add, delete, and show favorite places
 *
 * @package App\Http\Controllers
 */
class FavouriteController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/user/favourites",
     *     summary="Favourites info",
     *     description="Getting a list of all favourites",
     *     operationId="favouritesIndex",
     *     tags={"favourites"},
     *     security={ {"bearer": {} }},
     *     @OA\Response(
     *          response=200,
     *          description="Success getting a list of all favourites",
     *          @OA\JsonContent(
     *              @OA\Property(property="current_page", type="integer", example=1),
     *              @OA\Property(
     *                  property="data",
     *                  type="array",
     *                  @OA\Items(type="object", ref="#/components/schemas/Place")
     *              )
     *          )
     *     ),
     *     @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Unauthenticated.")
     *          )
     *     )
     * )
     */
    public function index()
    {
        return response()->json(auth()->user()->favoutirePlaces()->paginate(Config::get('constants.pagination.count')));
    }

    /**
     * @OA\Post(
     *     path="/api/user/favourites?place={id}",
     *     summary="Add a new favourite",
     *     description="Adding a new favourite",
     *     operationId="favouritesStore",
     *     tags={"favourites"},
     *     security={ {"bearer": {} }},
     *     @OA\Parameter(
     *          description="ID of place",
     *          in="path",
     *          name="id",
     *          required=true,
     *          example=1,
     *          @OA\Schema(type="integer", format="int64")
     *     ),
     *     @OA\Response(
     *          response=200,
     *          description="OK",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Place has already been added")
     *          )
     *     ),
     *     @OA\Response(
     *          response=201,
     *          description="Success storing a new favourite",
     *          @OA\JsonContent(type="object", ref="#/components/schemas/Place")
     *     ),
     *     @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Unauthenticated.")
     *          )
     *     ),
     *     @OA\Response(
     *          response=404,
     *          description="Place does not exist",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="message", type="string", example="No place found")
     *          )
     *     )
     * )
     */
    public function store(Request $request)
    {
        $request->validate(['place' => 'required|integer']);
        $userFavouritePlaces = auth()->user()->favoutirePlaces();

        if ($userFavouritePlaces->find($request->get('place')) !== null)
            return response()->json(['message' => 'Favourite has already been added']);

        Place::findOrFail($request->get('place'));
        $userFavouritePlaces->attach($request->get('place'));

        return response()->json(Place::findOrFail($request->get('place')), 201);
    }

    /**
     * @OA\Delete(
     *     path="/api/user/favourites?place={id}",
     *     summary="Delete favourite",
     *     description="Deleting favourite",
     *     operationId="favouritesDelete",
     *     tags={"favourites"},
     *     security={ {"bearer": {} }},
     *     @OA\Parameter(
     *          description="Place ID",
     *          in="path",
     *          name="place",
     *          required=true,
     *          example=1,
     *          @OA\Schema(type="integer", format="int64")
     *     ),
     *     @OA\Response(
     *          response=200,
     *          description="Success deleting favourite",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Favourite is deleted successfully")
     *          )
     *     ),
     *     @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *          @OA\JsonContent(
     *              @OA\Property(property="messerrorage", type="string", example="Unauthenticated.")
     *          )
     *     ),
     *     @OA\Response(
     *          response=404,
     *          description="Place does not exist in favourites",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Place does not exist in favourites")
     *          )
     *     )
     * )
     */
    public function destroy(Request $request)
    {
        $request->validate(['place' => 'required|integer']);

        $userFavouritePlaces = auth()->user()->favoutirePlaces();

        throw_if($userFavouritePlaces->find($request->get('place')) === null, new ModelNotFoundException('Place not found in favourites'));

        $userFavouritePlaces->detach($request->get('place'));

        return response()->json(['message' => 'Favourite is deleted successfully']);
    }
}
