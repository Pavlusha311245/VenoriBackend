<?php

namespace App\Http\Controllers;

use App\Models\Favourite;
use Illuminate\Http\Request;

/**
 * Controller used for add, delete, and show favorite places
 *
 * @package App\Http\Controllers
 */
class FavouriteController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/favourites",
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
     *                  @OA\Items(type="object", ref="#/components/schemas/Favourite")
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
        return Favourite::paginate(5);
    }

    /**
     * @OA\Post(
     *     path="/api/favourites",
     *     summary="Add a new favourite",
     *     description="Adding a new favourite",
     *     operationId="favouritesStore",
     *     tags={"favourites"},
     *     security={ {"bearer": {} }},
     *     @OA\RequestBody(
     *          required=true,
     *          description="Pass data to add a new favourite",
     *          @OA\JsonContent(
     *              required={"user_id","place_id"},
     *              @OA\Property(property="user_id", type="integer", example=1),
     *              @OA\Property(property="place_id", type="integer", example=1)
     *          )
     *     ),
     *     @OA\Response(
     *          response=201,
     *          description="Success storing a new favourite",
     *          @OA\JsonContent(type="object", ref="#/components/schemas/Favourite")
     *     ),
     *     @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Unauthenticated.")
     *          )
     *     ),
     *     @OA\Response(
     *          response=422,
     *          description="Validation error",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="The given data was invalid."),
     *              @OA\Property(
     *                  property="errors",
     *                  type="object",
     *                  @OA\Property(
     *                      property="user_id",
     *                      type="array",
     *                      @OA\Items(type="string", example="The review id field is required.")
     *                  )
     *              )
     *          )
     *     )
     * )
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|integer',
            'place_id' => 'required|integer',
        ]);

        $user = Favourite::create($request->all());

        return response()->json($user, 201);
    }

    /**
     * @OA\Get(
     *     path="/api/users/{id}/favourites",
     *     summary="Show user favourites by id",
     *     description="Showing favourites by user_id",
     *     operationId="favouritesShowForUserById",
     *     tags={"favourites"},
     *     security={ {"bearer": {} }},
     *     @OA\Parameter(
     *          description="ID of user",
     *          in="path",
     *          name="id",
     *          required=true,
     *          example=1,
     *          @OA\Schema(type="integer", format="int64")
     *     ),
     *     @OA\Response(
     *          response=200,
     *          description="Success showing user favourites",
     *          @OA\JsonContent(
     *             @OA\Items(type="object", ref="#/components/schemas/Favourite")
     *          )
     *     ),
     *     @OA\Response(response=400, description="Favourites not found"),
     *     @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Unauthenticated.")
     *          )
     *     )
     * )
     */
    public function show($id)
    {
        return Favourite::where('user_id', $id)->get();
    }

    /**
     * @OA\Get(
     *     path="/api/user/favourites",
     *     summary="Show favourites for auth user",
     *     description="Showing favourites for auth user",
     *     operationId="favouritesShowForAuthUser",
     *     tags={"favourites"},
     *     security={ {"bearer": {} }},
     *     @OA\Response(
     *          response=200,
     *          description="Success showing auth user favourites",
     *          @OA\JsonContent(
     *              @OA\Items(type="object", ref="#/components/schemas/Favourite")
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
    public function showUserFavourites()
    {
        return Favourite::where('user_id', auth()->user()->id)->get();
    }

    /**
     * @OA\Delete(
     *     path="/api/favourites/{id}",
     *     summary="Delete favourite",
     *     description="Deleting favourite",
     *     operationId="favouritesDelete",
     *     tags={"favourites"},
     *     security={ {"bearer": {} }},
     *     @OA\Parameter(
     *          description="ID of favourite",
     *          in="path",
     *          name="id",
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
     *          response=400,
     *          description="Favourite not found",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="message", type="string", example="ModelNotFoundException handled for API")
     *          )
     *     ),
     *     @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *          @OA\JsonContent(
     *              @OA\Property(property="messerrorage", type="string", example="Unauthenticated.")
     *          )
     *     )
     * )
     */
    public function destroy($id)
    {
        $favourite = Favourite::findOrFail($id);
        $favourite->delete();

        return response()->json(['message' => 'Favourite is deleted successfully']);
    }
}
