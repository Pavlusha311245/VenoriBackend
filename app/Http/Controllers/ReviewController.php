<?php

namespace App\Http\Controllers;

use App\Models\Place;
use App\Models\Review;
use App\Services\Rating\PlaceRatingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * Controller for adding, removing, viewing and updating reviews
 *
 * @package App\Http\Controllers
 */
class ReviewController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/reviews",
     *     summary="Review info",
     *     description="Getting a list of all reviews",
     *     operationId="reviewsIndex",
     *     tags={"reviews"},
     *     security={ {"bearer": {} }},
     *     @OA\Response(
     *          response=200,
     *          description="Success getting a list of all reviews",
     *          @OA\JsonContent(
     *              @OA\Property(property="current_page", type="integer", example=1),
     *              @OA\Property(
     *                  property="data",
     *                  type="array",
     *                  @OA\Items(
     *                      type="object",
     *                      ref="#/components/schemas/Review"
     *                  ),
     *              ),
     *          ),
     *     ),
     *     @OA\Response(
     *          response=401,
     *          description="Validation error",
     *          @OA\JsonContent(
     *              @OA\Property(property="error", type="string", example="Unauthorized"),
     *          )
     *     ),
     * )
     */
    public function index()
    {
        return Review::paginate(5);
    }

    /**
     * @OA\Post(
     *     path="/api/reviews",
     *     summary="Add review",
     *     description="Adding a new review",
     *     operationId="reviewStore",
     *     tags={"reviews"},
     *     security={ {"bearer": {} }},
     *     @OA\RequestBody(
     *          required=true,
     *          description="Pass data to add a new review",
     *          @OA\JsonContent(
     *              @OA\Property(
     *                  property="data",
     *                  type="array",
     *                  @OA\Items(
     *                      type="object",
     *                      ref="#/components/schemas/Review"
     *                  ),
     *              ),
     *          )
     *     ),
     *     @OA\Response(
     *          response=201,
     *          description="Success storing a new review",
     *          @OA\JsonContent(
     *              @OA\Property(
     *                  property="data",
     *                  type="array",
     *                  @OA\Items(
     *                      type="object",
     *                      ref="#/components/schemas/Review"
     *                  ),
     *              ),
     *          ),
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
     *                      property="name",
     *                      type="array",
     *                      @OA\Items(
     *                          type="string",
     *                          example="The name field is required.",
     *                      )
     *                  )
     *              )
     *          )
     *      )
     * )
     */
    public function store(Request $request, PlaceRatingService $placeRatingService)
    {
        $request->validate([
            'title' => 'required|string',
            'rating' => 'required|numeric|min:1|max:5',
            'description' => 'required|string',
            'place_id' => 'required',
            'user_id' => 'required|unique:reviews'
        ]);

        $review = Review::create($request->all());

        $placeRatingService->countPlaceRating($review);

        return response()->json($review, 201);
    }

    /**
     * Display the specified resource.
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        return Review::findOrFail($id);
    }

    /**
     * @OA\Put(
     *     path="/api/reviews/{id}",
     *     summary="Update review",
     *     description="Updating review information",
     *     operationId="reviewUpdate",
     *     tags={"reviews"},
     *     security={ {"bearer": {} }},
     *     @OA\Parameter(
     *          description="ID of category",
     *          in="path",
     *          name="id",
     *          required=true,
     *          example=1,
     *          @OA\Schema(
     *              type="integer",
     *              format="int64"
     *          )
     *     ),
     *     @OA\RequestBody(
     *          required=true,
     *          description="Pass data to update review information",
     *          @OA\JsonContent(
     *              @OA\Property(
     *                  property="data",
     *                  type="array",
     *                  @OA\Items(
     *                      type="object",
     *                      ref="#/components/schemas/Review"
     *                  ),
     *              ),
     *          )
     *     ),
     *     @OA\Response(
     *          response=201,
     *          description="Success updating review information",
     *          @OA\JsonContent(
     *              @OA\Property(
     *                  property="data",
     *                  type="array",
     *                  @OA\Items(
     *                      type="object",
     *                      ref="#/components/schemas/Review"
     *                  ),
     *              ),
     *          ),
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
     *                      property="email",
     *                      type="array",
     *                      @OA\Items(
     *                          type="string",
     *                          example="The email has already been taken.",
     *                      )
     *                  )
     *              )
     *          )
     *      )
     * )
     */
    public function update(Request $request, Review $review, PlaceRatingService $placeRatingService)
    {
        $request->validate([
            'title' => 'string',
            'rating' => 'numeric|min:1|max:5',
            'description' => 'string',
        ]);

        $review->update($request->all());

        $placeRatingService->countPlaceRating($review);

        return response()->json($review, 200);
    }

    /**
     * @OA\Delete(
     *     path="/api/reviews/{id}",
     *     summary="Delete review",
     *     description="Deleting review",
     *     operationId="reviewsDelete",
     *     tags={"reviews"},
     *     security={ {"bearer": {} }},
     *     @OA\Parameter(
     *          description="ID of review",
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
     *          description="Success deleting review",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Review is deleted successfully")
     *          ),
     *     ),
     *     @OA\Response(
     *          response=400,
     *          description="Review not found",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="message", type="string", example="ModelNotFoundException handled for API")
     *          )
     *     )
     * ),
     */
    public function destroy($id, PlaceRatingService $placeRatingService)
    {
        $review = Review::findOrFail($id);
        $review->comments()->delete();
        $review->delete();

        $placeRatingService->countPlaceRating($review);

        return response()->json(['message' => 'Review is successfully deleted'], 200);
    }

    /**
     * @param $id
     * @return int
     */
    public function reviewsCount($id)
    {
        return count(Review::where('place_id',$id)->get());
    }
}
