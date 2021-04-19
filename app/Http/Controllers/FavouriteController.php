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
     *                  @OA\Items(
     *                      type="object",
     *                      ref="#/components/schemas/Favourite"
     *                  ),
     *              ),
     *          ),
     *     ),
     *     @OA\Response(
     *          response=401,
     *          description="Unauthorized",
     *          @OA\JsonContent(
     *              @OA\Property(property="error", type="string", example="Unauthorized"),
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
     *          @OA\JsonContent(
     *              type="object",
     *              ref="#/components/schemas/Favourite"
     *          ),
     *     ),
     *     @OA\Response(
     *          response=401,
     *          description="Unauthorized",
     *          @OA\JsonContent(
     *              @OA\Property(property="error", type="string", example="Unauthorized"),
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
     *                      @OA\Items(
     *                          type="string",
     *                          example="The review id field is required.",
     *                      )
     *                  )
     *              )
     *          )
     *      )
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
     * The method returns favourite places for current authorization user
     *
     * @return Response
     */
    public function show()
    {
        return Favourite::where('user_id', auth()->user()->id);
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
     *          @OA\Schema(
     *              type="integer",
     *              format="int64"
     *          )
     *     ),
     *     @OA\Response(
     *          response=200,
     *          description="Success deleting favourite",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Favourite is deleted successfully")
     *          ),
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
     *          description="Unauthorized",
     *          @OA\JsonContent(
     *              @OA\Property(property="error", type="string", example="Unauthorized"),
     *          )
     *     )
     * )
     */
    public function destroy($id)
    {
        $favourite = Favourite::findOrFail($id);
        $favourite->delete();

        return response()->json(['message' => 'Favourite is deleted successfully'], 200);
    }
}
